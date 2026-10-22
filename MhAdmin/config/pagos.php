<?php
/**
 * ============================================================
 *  CONFIGURACIÓN DE PASARELAS DE PAGO - MH Globos y Detalles
 * ============================================================
 *
 *  Este es el ÚNICO archivo que debes editar para activar pagos.
 *  Cada pasarela se activa sola cuando le pones sus llaves.
 *  Si una pasarela no tiene llaves, su botón NO aparece en el carrito.
 *
 *  Pasarelas incluidas:
 *    1. WOMPI (Bancolombia) -> PSE, Nequi, Botón Bancolombia,
 *                              Daviplata, tarjetas, efectivo.
 *    2. MERCADO PAGO        -> Tarjetas, PSE, Efecty, saldo Mercado Pago.
 *    3. PAYPAL              -> Cuenta PayPal y tarjetas internacionales
 *                              (cobra en USD, ver tasa abajo).
 *    4. TRANSFERENCIA       -> Pago manual a cuenta bancaria + WhatsApp.
 *
 *  IMPORTANTE: los secretos de este archivo NUNCA se envían al navegador.
 *  No subas este archivo a un repositorio público.
 */

/* ------------------------------------------------------------
 *  DATOS GENERALES DE LA TIENDA
 * ------------------------------------------------------------ */
define('TIENDA_NOMBRE', 'MH Globos y Detalles');
define('TIENDA_WHATSAPP', '573053342258');          // Con indicativo 57, sin "+"
define('TIENDA_CUENTA_BANCO', 'Bancolombia Ahorros 51100001218');

/**
 * URL pública de la carpeta del sitio, SIN "/" al final.
 * Déjala vacía ('') para que se calcule sola (sirve en localhost).
 * En producción escríbela, por ejemplo: 'https://www.mhglobdetalle.com'
 */
define('TIENDA_URL_BASE', '');

// Límites de seguridad del carrito
define('PAGO_MAX_POR_PRODUCTO', 50);
define('PAGO_MAX_ITEMS', 200);
define('PAGO_MONTO_MINIMO_COP', 1500);

/* ------------------------------------------------------------
 *  1. WOMPI   (https://comercios.wompi.co -> Desarrolladores)
 * ------------------------------------------------------------
 *  - Llave pública:          pub_test_...      / pub_prod_...
 *  - Secreto de integridad:  test_integrity_...  / prod_integrity_...
 *  - Secreto de eventos:     test_events_...     / prod_events_...
 *  - URL de eventos (webhook): https://TU-DOMINIO/webhook_wompi.php
 *  El ambiente (pruebas o real) lo decide el prefijo de la llave.
 */
define('WOMPI_PUBLIC_KEY', 'pub_test_TQR1DorzutQjI1dFU3CJRKSE5OZWZ8XM');
define('WOMPI_INTEGRITY_SECRET', 'test_integrity_Qcyv80yk3jGockhmvo1F19G9THbC8lDJ');
define('WOMPI_EVENTS_SECRET', 'test_events_RM2yTzWOuIV0efDeE22OMn0vwm8MRUIT');

/* ------------------------------------------------------------
 *  2. MERCADO PAGO  (https://www.mercadopago.com.co/developers
 *                    -> Tus integraciones -> Credenciales)
 * ------------------------------------------------------------
 *  - Access Token: TEST-... (pruebas) o APP_USR-... (producción)
 *  - Clave secreta de webhooks (opcional, recomendada):
 *    Tus integraciones -> Webhooks -> "Clave secreta"
 *  - URL de webhook: https://TU-DOMINIO/webhook_mercadopago.php
 *    (se envía automáticamente con cada pago si el sitio no es localhost)
 */
define('MP_ACCESS_TOKEN', 'REEMPLAZA_CON_TU_ACCESS_TOKEN');
define('MP_WEBHOOK_SECRET', '');

/* ------------------------------------------------------------
 *  3. PAYPAL  (https://developer.paypal.com -> Apps & Credentials)
 * ------------------------------------------------------------
 *  - Client ID y Secret de tu app REST.
 *  - PAYPAL_MODO: 'sandbox' (pruebas) o 'live' (cobros reales).
 *  - PayPal NO acepta pesos colombianos: el total se convierte a
 *    dólares con la tasa de abajo. Actualízala cuando cambie el dólar.
 */
define('PAYPAL_MODO', 'sandbox');
define('PAYPAL_CLIENT_ID', 'REEMPLAZA_CON_TU_CLIENT_ID');
define('PAYPAL_SECRET', 'REEMPLAZA_CON_TU_SECRET');
define('PAYPAL_TASA_COP_POR_USD', 4000);   // 1 USD = 4.000 COP (ajústala)

/* ------------------------------------------------------------
 *  4. TRANSFERENCIA BANCARIA + WHATSAPP (siempre disponible)
 * ------------------------------------------------------------ */
define('TRANSFERENCIA_ACTIVA', true);


/* ============================================================
 *  NO NECESITAS EDITAR NADA DE AQUÍ HACIA ABAJO
 * ============================================================ */

define('WOMPI_CHECKOUT_URL', 'https://checkout.wompi.co/p/');
define('WOMPI_API_URL', strpos(WOMPI_PUBLIC_KEY, 'pub_prod_') === 0
    ? 'https://production.wompi.co/v1'
    : 'https://sandbox.wompi.co/v1');

define('MP_API_URL', 'https://api.mercadopago.com');

define('PAYPAL_API_URL', PAYPAL_MODO === 'live'
    ? 'https://api-m.paypal.com'
    : 'https://api-m.sandbox.paypal.com');

/** Una llave está configurada si no está vacía ni tiene el texto REEMPLAZA. */
function pago_llave_ok($valor)
{
    return $valor !== '' && strpos($valor, 'REEMPLAZA') === false;
}

/** Lista de pasarelas activas (las que tienen llaves). */
function pasarelas_activas()
{
    return array(
        'wompi' => pago_llave_ok(WOMPI_PUBLIC_KEY) && pago_llave_ok(WOMPI_INTEGRITY_SECRET),
        'mercadopago' => pago_llave_ok(MP_ACCESS_TOKEN),
        'paypal' => pago_llave_ok(PAYPAL_CLIENT_ID) && pago_llave_ok(PAYPAL_SECRET),
        'transferencia' => TRANSFERENCIA_ACTIVA,
    );
}
