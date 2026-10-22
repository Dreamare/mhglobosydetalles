<?php
/**
 * ============================================================
 *  CONFIGURACIÓN DE PASARELAS DE PAGO (PLANTILLA DE EJEMPLO)
 * ============================================================
 *  Copia este archivo como pagos.php y reemplaza las credenciales.
 */

define('TIENDA_NOMBRE', 'MH Globos y Detalles');
define('TIENDA_WHATSAPP', '573053342258');
define('TIENDA_CUENTA_BANCO', 'Bancolombia Ahorros 51100001218');
define('TIENDA_URL_BASE', '');

define('PAGO_MAX_POR_PRODUCTO', 50);
define('PAGO_MAX_ITEMS', 200);
define('PAGO_MONTO_MINIMO_COP', 1500);

// Wompi
define('WOMPI_PUBLIC_KEY', 'pub_test_REEMPLAZA_CON_TU_LLAVE_PUBLICA');
define('WOMPI_INTEGRITY_SECRET', 'test_integrity_REEMPLAZA_CON_TU_SECRETO');
define('WOMPI_EVENTS_SECRET', 'test_events_REEMPLAZA_CON_TU_SECRETO');

// Mercado Pago
define('MP_ACCESS_TOKEN', 'REEMPLAZA_CON_TU_ACCESS_TOKEN');
define('MP_WEBHOOK_SECRET', '');

// PayPal
define('PAYPAL_MODO', 'sandbox');
define('PAYPAL_CLIENT_ID', 'REEMPLAZA_CON_TU_CLIENT_ID');
define('PAYPAL_SECRET', 'REEMPLAZA_CON_TU_SECRET');
define('PAYPAL_TASA_COP_POR_USD', 4000);

// Transferencia
define('TRANSFERENCIA_ACTIVA', true);

define('WOMPI_CHECKOUT_URL', 'https://checkout.wompi.co/p/');
define('WOMPI_API_URL', strpos(WOMPI_PUBLIC_KEY, 'pub_prod_') === 0 ? 'https://production.wompi.co/v1' : 'https://sandbox.wompi.co/v1');
define('MP_API_URL', 'https://api.mercadopago.com');
define('PAYPAL_API_URL', PAYPAL_MODO === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com');

function pago_llave_ok($valor)
{
    return $valor !== '' && strpos($valor, 'REEMPLAZA') === false;
}

function pasarelas_activas()
{
    return array(
        'wompi' => pago_llave_ok(WOMPI_PUBLIC_KEY) && pago_llave_ok(WOMPI_INTEGRITY_SECRET),
        'mercadopago' => pago_llave_ok(MP_ACCESS_TOKEN),
        'paypal' => pago_llave_ok(PAYPAL_CLIENT_ID) && pago_llave_ok(PAYPAL_SECRET),
        'transferencia' => TRANSFERENCIA_ACTIVA,
    );
}
