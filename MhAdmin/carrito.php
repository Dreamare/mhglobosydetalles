<?php
require_once __DIR__ . '/lib/pagos.php';
$activas = pasarelas_activas();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Carrito de Compras - <?php echo e(TIENDA_NOMBRE); ?></title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link href="assets/css/estilos.css" rel="stylesheet" />
    <style>
        .btn-pago { display: flex; flex-direction: column; align-items: center; padding: .7rem 1rem; font-weight: 700; border: none; }
        .btn-pago small { font-weight: 400; font-size: .8rem; opacity: .9; }
        .btn-wompi { background: #2c2a29; color: #b0f2ae; }
        .btn-wompi:hover { background: #000; color: #b0f2ae; }
        .btn-mercadopago { background: #009ee3; color: #fff; }
        .btn-mercadopago:hover { background: #007eb5; color: #fff; }
        .btn-paypal { background: #ffc439; color: #003087; }
        .btn-paypal:hover { background: #f2b52e; color: #003087; }
        .btn-transferencia { background: #25d366; color: #fff; }
        .btn-transferencia:hover { background: #1da851; color: #fff; }
        .btn-pago:disabled { opacity: .6; }
        .cant-btn { width: 2rem; padding: 0; }
    </style>
</head>

<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="./">&larr; Seguir comprando</a>
            </div>
        </nav>
    </div>

    <header class="bg-dark py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Carrito</h1>
                <p class="lead fw-normal text-white-50 mb-0">Tus productos agregados.</p>
            </div>
        </div>
    </header>

    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th class="text-center">Cantidad</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="tblCarrito">
                                <tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6 col-lg-5 ms-auto">
                    <h4>Total a pagar: <span id="total_pagar">$0</span> COP</h4>
                    <p class="text-muted small mb-3">Elige cómo quieres pagar:</p>

                    <div class="d-grid gap-2" id="botonesPago">
                        <?php if ($activas['wompi']) { ?>
                            <button class="btn btn-pago btn-wompi" type="button" data-pasarela="wompi">
                                PSE · Nequi · Bancolombia · Tarjeta
                                <small>Pago seguro con Wompi (también Daviplata y efectivo)</small>
                            </button>
                        <?php } ?>
                        <?php if ($activas['mercadopago']) { ?>
                            <button class="btn btn-pago btn-mercadopago" type="button" data-pasarela="mercadopago">
                                Pagar con Mercado Pago
                                <small>Tarjetas, PSE, Efecty o saldo Mercado Pago</small>
                            </button>
                        <?php } ?>
                        <?php if ($activas['paypal']) { ?>
                            <button class="btn btn-pago btn-paypal" type="button" data-pasarela="paypal">
                                Pagar con PayPal
                                <small>Aprox. US$ <span id="totalUsd">0.00</span> · tarjetas internacionales</small>
                            </button>
                        <?php } ?>
                        <?php if ($activas['transferencia']) { ?>
                            <button class="btn btn-pago btn-transferencia" type="button" data-pasarela="transferencia">
                                Transferencia + WhatsApp
                                <small><?php echo e(TIENDA_CUENTA_BANCO); ?> · envía tu comprobante</small>
                            </button>
                        <?php } ?>
                        <?php if (!$activas['wompi'] && !$activas['mercadopago'] && !$activas['paypal']) { ?>
                            <div class="alert alert-warning small mb-0">
                                Los pagos en línea aún no están configurados. Agrega tus llaves en <code>config/pagos.php</code>.
                            </div>
                        <?php } ?>

                        <div class="alert alert-danger mb-0" id="mensajePago" role="alert" style="display:none;"></div>

                        <hr>
                        <button class="btn btn-outline-success" type="button" id="btnDescargarFactura">Descargar resumen del pedido</button>
                        <button class="btn btn-outline-warning" type="button" id="btnVaciar">Vaciar carrito</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">&copy; <?php echo date('Y'); ?> Mhglobdetalle.com Teléfono +57 305 3342258. Instagram: @mhglobosudetalles.es. E-mail: Mhglobos@gmail.com.</p>
        </div>
    </footer>

    <script>
        window.TIENDA = {
            tasaUsd: <?php echo json_encode((float) PAYPAL_TASA_COP_POR_USD); ?>,
            cuentaBanco: <?php echo json_encode(TIENDA_CUENTA_BANCO); ?>,
            whatsapp: <?php echo json_encode(TIENDA_WHATSAPP); ?>
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/scripts.js"></script>
    <script src="assets/js/pagos.js"></script>
</body>

</html>
