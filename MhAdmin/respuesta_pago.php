<?php
/**
 * ============================================================
 *  respuesta_pago.php  -  A donde vuelve el cliente tras pagar
 * ============================================================
 *  Detecta la pasarela por los parámetros que cada una agrega:
 *    Wompi        -> ?id=TRANSACCION
 *    Mercado Pago -> ?pasarela=mercadopago&payment_id=...
 *    PayPal       -> ?pasarela=paypal&token=ORDEN&PayerID=...
 *  Siempre se consulta el estado real en la API de la pasarela;
 *  nunca se confía en lo que diga la URL.
 */
session_start();
require_once __DIR__ . '/lib/pagos.php';

$estado = 'PENDIENTE';
$referencia = '';
$total = 0;
$metodo = '';
$idTransaccion = '';
$consultado = false;
$limpiar = function ($v) { return preg_replace('/[^A-Za-z0-9\-_]/', '', (string) $v); };

$pasarela = isset($_GET['pasarela']) ? $_GET['pasarela'] : (isset($_GET['id']) ? 'wompi' : '');

if ($pasarela === 'wompi' && !empty($_GET['id'])) {
    $idTransaccion = $limpiar($_GET['id']);
    $t = wompi_consultar($idTransaccion);
    if ($t) {
        $consultado = true;
        $estado = wompi_estado($t['status']);
        $referencia = $t['reference'];
        $total = $t['amount_in_cents'] / 100;
        $metodo = isset($t['payment_method_type']) ? $t['payment_method_type'] : 'wompi';
        $pedido = buscar_pedido($conexion, $referencia);
        if ($pedido && (int) $pedido['monto_centavos'] === (int) $t['amount_in_cents']) {
            actualizar_pedido($conexion, $referencia, $estado, $idTransaccion, $metodo);
        } elseif ($pedido) {
            $estado = 'ERROR'; // el monto pagado no coincide con el pedido
        }
    }
} elseif ($pasarela === 'mercadopago') {
    $idPago = !empty($_GET['payment_id']) ? $_GET['payment_id'] : (isset($_GET['collection_id']) ? $_GET['collection_id'] : '');
    $idPago = $limpiar($idPago);
    if ($idPago !== '' && $idPago !== 'null') {
        $r = mp_procesar_pago($conexion, $idPago);
        if ($r) {
            $consultado = true;
            list($estado, $referencia, $total, $metodo, $idTransaccion) = $r;
        }
    } elseif (isset($_GET['status']) && $_GET['status'] === 'null') {
        $estado = 'CANCELADO';
    }
} elseif ($pasarela === 'paypal') {
    if (!empty($_GET['cancelado'])) {
        $estado = 'CANCELADO';
        if (!empty($_GET['token'])) {
            $orden = $limpiar($_GET['token']);
            $stmt = mysqli_prepare($conexion, "SELECT referencia FROM pedidos WHERE transaccion_id = ? AND pasarela = 'paypal' AND estado = 'PENDIENTE'");
            mysqli_stmt_bind_param($stmt, "s", $orden);
            mysqli_stmt_execute($stmt);
            $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            if ($fila) {
                actualizar_pedido($conexion, $fila['referencia'], 'ANULADO', $orden, 'paypal');
            }
        }
    } elseif (!empty($_GET['token'])) {
        $r = paypal_capturar($conexion, $limpiar($_GET['token']));
        if ($r) {
            $consultado = true;
            list($estado, $referencia, $total, $metodo, $idTransaccion) = $r;
        }
    }
}

$mensajes = array(
    'APROBADO'  => array('success', '¡Pago aprobado!', 'Gracias por tu compra. Pronto nos pondremos en contacto contigo para coordinar la entrega de tu detalle.'),
    'RECHAZADO' => array('danger', 'Pago rechazado', 'Tu pago no fue aprobado. Puedes intentarlo nuevamente con otro medio de pago.'),
    'ANULADO'   => array('secondary', 'Pago anulado', 'La transacción fue anulada.'),
    'CANCELADO' => array('secondary', 'Pago cancelado', 'Cancelaste el pago. Tus productos siguen en el carrito.'),
    'ERROR'     => array('danger', 'Ocurrió un error', 'Hubo un problema verificando tu pago. Escríbenos por WhatsApp con tu referencia.'),
    'PENDIENTE' => array('warning', 'Pago pendiente', 'Tu pago está siendo procesado (es común con PSE o pagos en efectivo). Te confirmaremos cuando sea aprobado.'),
);
list($color, $titulo, $descripcion) = $mensajes[$estado];
$whatsapp = 'https://wa.me/' . TIENDA_WHATSAPP . '?text=' . rawurlencode('Hola, tengo una consulta sobre mi pedido ' . $referencia);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Resultado del Pago - <?php echo e(TIENDA_NOMBRE); ?></title>
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link href="assets/css/estilos.css" rel="stylesheet" />
</head>

<body>
    <header class="bg-dark py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Resultado del Pago</h1>
                <p class="lead fw-normal text-white-50 mb-0"><?php echo e(TIENDA_NOMBRE); ?></p>
            </div>
        </div>
    </header>

    <main class="py-5">
        <div class="container px-4 px-lg-5" style="max-width: 720px;">
            <div class="alert alert-<?php echo $color; ?> text-center" role="alert">
                <h2 class="h4 fw-bolder"><?php echo e($titulo); ?></h2>
                <p class="mb-0"><?php echo e($descripcion); ?></p>
            </div>

            <?php if ($consultado) { ?>
                <table class="table">
                    <tbody>
                        <tr><th scope="row">Referencia</th><td><?php echo e($referencia); ?></td></tr>
                        <tr><th scope="row">ID Transacción</th><td><?php echo e($idTransaccion); ?></td></tr>
                        <tr><th scope="row">Pasarela</th><td><?php echo e(ucfirst($pasarela)); ?></td></tr>
                        <tr><th scope="row">Medio de pago</th><td><?php echo e($metodo); ?></td></tr>
                        <tr><th scope="row">Total</th><td>$<?php echo number_format($total, 0, ',', '.'); ?> COP</td></tr>
                        <tr><th scope="row">Estado</th><td><strong><?php echo e($estado); ?></strong></td></tr>
                    </tbody>
                </table>
            <?php } elseif ($estado !== 'CANCELADO') { ?>
                <p class="text-center text-muted">No pudimos consultar la transacción. Si realizaste el pago, escríbenos por WhatsApp con tu comprobante.</p>
            <?php } ?>

            <div class="d-grid gap-2 mt-4">
                <a href="index.php" class="btn btn-dark">Volver a la tienda</a>
                <?php if ($estado !== 'APROBADO') { ?>
                    <a href="carrito.php" class="btn btn-outline-dark">Volver al carrito</a>
                <?php } ?>
                <a href="<?php echo e($whatsapp); ?>" class="btn btn-success" target="_blank" rel="noopener">Escribir por WhatsApp</a>
            </div>
        </div>
    </main>

    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">&copy; <?php echo date('Y'); ?> Mhglobdetalle.com Teléfono +57 305 3342258. Instagram: @mhglobosudetalles.es. E-mail: Mhglobos@gmail.com.</p>
        </div>
    </footer>

    <?php if ($estado === 'APROBADO') { ?>
        <script>localStorage.removeItem('productos');</script>
    <?php } ?>
</body>

</html>
