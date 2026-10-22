<?php
/**
 * ============================================================
 *  webhook_wompi.php  -  Notificaciones automáticas de Wompi
 * ============================================================
 *  Regístrala en Wompi -> Desarrolladores -> "URL de Eventos":
 *      https://TU-DOMINIO/webhook_wompi.php
 *  Se valida el checksum con el secreto de eventos.
 */
require_once __DIR__ . '/lib/pagos.php';

$evento = json_decode(file_get_contents('php://input'), true);

if (!$evento || !isset($evento['signature']['properties'], $evento['signature']['checksum'], $evento['timestamp'])) {
    http_response_code(400);
    exit('Evento no válido');
}

// checksum = SHA256( valores de "properties" + timestamp + secreto de eventos )
$cadena = '';
foreach ($evento['signature']['properties'] as $propiedad) {
    $valor = $evento['data'];
    foreach (explode('.', $propiedad) as $clave) {
        $valor = isset($valor[$clave]) ? $valor[$clave] : '';
    }
    $cadena .= $valor;
}
$cadena .= $evento['timestamp'] . WOMPI_EVENTS_SECRET;

if (!hash_equals(strtoupper(hash('sha256', $cadena)), strtoupper($evento['signature']['checksum']))) {
    http_response_code(401);
    exit('Firma inválida');
}

if ($evento['event'] === 'transaction.updated' && isset($evento['data']['transaction'])) {
    $t = $evento['data']['transaction'];
    $pedido = buscar_pedido($conexion, $t['reference']);
    if ($pedido && (int) $pedido['monto_centavos'] === (int) $t['amount_in_cents']) {
        $metodo = isset($t['payment_method_type']) ? $t['payment_method_type'] : 'wompi';
        actualizar_pedido($conexion, $t['reference'], wompi_estado($t['status']), $t['id'], $metodo);
    }
}

http_response_code(200); // Wompi necesita un 200 para dar el evento por entregado
echo 'OK';
