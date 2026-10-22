<?php
/**
 * ============================================================
 *  pago_iniciar.php  -  Inicia el pago con la pasarela elegida
 * ============================================================
 *  Recibe (POST): pasarela = wompi | mercadopago | paypal | transferencia
 *                 data     = [{id:1},{id:1},{id:3}]  (carrito del navegador)
 *  Devuelve JSON: { ok: true, url: "https://..." }  -> el navegador redirige
 *
 *  El total SIEMPRE se calcula con los precios de la base de datos.
 */
session_start();
require_once __DIR__ . '/lib/pagos.php';

header('Content-Type: application/json; charset=utf-8');

function responder($datos, $codigo = 200)
{
    http_response_code($codigo);
    echo json_encode($datos);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(array('ok' => false, 'mensaje' => 'Solicitud no válida.'), 405);
}

$pasarela = isset($_POST['pasarela']) ? $_POST['pasarela'] : '';
$activas = pasarelas_activas();
if (!isset($activas[$pasarela]) || !$activas[$pasarela]) {
    responder(array('ok' => false, 'mensaje' => 'Ese medio de pago no está disponible.'), 400);
}

$pedido = null;
try {
    $carrito = calcular_carrito($conexion, isset($_POST['data']) ? $_POST['data'] : array());

    if ($pasarela !== 'transferencia' && $carrito['total'] < PAGO_MONTO_MINIMO_COP) {
        throw new Exception('El monto mínimo para pagar en línea es $' . number_format(PAGO_MONTO_MINIMO_COP, 0, ',', '.') . ' COP.');
    }

    $pedido = crear_pedido($conexion, $pasarela, $carrito);

    switch ($pasarela) {
        case 'wompi':
            $url = wompi_crear_url($pedido);
            break;

        case 'mercadopago':
            $url = mp_crear_url($pedido, $carrito);
            break;

        case 'paypal':
            $url = paypal_crear_url($conexion, $pedido);
            break;

        case 'transferencia':
            // Abre WhatsApp con el resumen del pedido para enviar el comprobante
            $texto = "Hola, quiero pagar por transferencia mi pedido " . $pedido['referencia'] . ":\n";
            foreach ($carrito['detalle'] as $p) {
                $texto .= "- " . $p['cantidad'] . " x " . $p['nombre'] . " ($" . number_format($p['subtotal'], 0, ',', '.') . ")\n";
            }
            $texto .= "Total: $" . number_format($carrito['total'], 0, ',', '.') . " COP\n";
            $texto .= "Pagaré a la cuenta " . TIENDA_CUENTA_BANCO . " y envío el comprobante.";
            $url = 'https://wa.me/' . TIENDA_WHATSAPP . '?text=' . rawurlencode($texto);
            break;
    }

    responder(array('ok' => true, 'url' => $url, 'referencia' => $pedido['referencia']));
} catch (Exception $ex) {
    if ($pedido) { // la pasarela falló: el pedido no queda como pendiente
        actualizar_pedido($conexion, $pedido['referencia'], 'ERROR', null, $pasarela);
    }
    responder(array('ok' => false, 'mensaje' => $ex->getMessage()), 400);
}
