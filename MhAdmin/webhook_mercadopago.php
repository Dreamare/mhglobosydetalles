<?php
/**
 * ============================================================
 *  webhook_mercadopago.php  -  Notificaciones de Mercado Pago
 * ============================================================
 *  La URL se envía sola con cada pago (si el sitio no es localhost).
 *  También puedes registrarla en Tus integraciones -> Webhooks
 *  marcando el evento "Pagos".
 *
 *  Seguridad: nunca se confía en el contenido de la notificación;
 *  se vuelve a consultar el pago en la API de Mercado Pago con tu
 *  Access Token. Si configuras MP_WEBHOOK_SECRET, además se valida
 *  la firma x-signature.
 */
require_once __DIR__ . '/lib/pagos.php';

$cuerpo = json_decode(file_get_contents('php://input'), true);

// El ID del pago puede llegar en el cuerpo o en la URL (según el tipo de aviso)
$tipo = isset($cuerpo['type']) ? $cuerpo['type'] : (isset($_GET['type']) ? $_GET['type'] : (isset($_GET['topic']) ? $_GET['topic'] : ''));
$idPago = isset($cuerpo['data']['id']) ? $cuerpo['data']['id'] : (isset($_GET['data_id']) ? $_GET['data_id'] : (isset($_GET['id']) ? $_GET['id'] : ''));
$idPago = preg_replace('/[^0-9]/', '', (string) $idPago);

if ($tipo !== 'payment' || $idPago === '') {
    http_response_code(200); // otros eventos se ignoran
    exit('Ignorado');
}

// Validación opcional de firma: ts=...,v1=...
if (MP_WEBHOOK_SECRET !== '') {
    $firma = isset($_SERVER['HTTP_X_SIGNATURE']) ? $_SERVER['HTTP_X_SIGNATURE'] : '';
    $requestId = isset($_SERVER['HTTP_X_REQUEST_ID']) ? $_SERVER['HTTP_X_REQUEST_ID'] : '';
    $partes = array();
    foreach (explode(',', $firma) as $par) {
        $kv = explode('=', trim($par), 2);
        if (count($kv) === 2) {
            $partes[$kv[0]] = $kv[1];
        }
    }
    $dataIdUrl = isset($_GET['data_id']) ? strtolower($_GET['data_id']) : $idPago;
    $manifiesto = 'id:' . $dataIdUrl . ';request-id:' . $requestId . ';ts:' . (isset($partes['ts']) ? $partes['ts'] : '') . ';';
    $calculada = hash_hmac('sha256', $manifiesto, MP_WEBHOOK_SECRET);
    if (!isset($partes['v1']) || !hash_equals($calculada, $partes['v1'])) {
        http_response_code(401);
        exit('Firma inválida');
    }
}

mp_procesar_pago($conexion, $idPago);

http_response_code(200);
echo 'OK';
