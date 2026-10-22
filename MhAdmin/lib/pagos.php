<?php
/**
 * ============================================================
 *  lib/pagos.php  -  Lógica compartida de todas las pasarelas
 * ============================================================
 *  - Cálculo del carrito con precios de la BASE DE DATOS
 *  - Registro y actualización de pedidos
 *  - Funciones de Wompi, Mercado Pago y PayPal
 */
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/pagos.php';

/* ============================================================
 *  UTILIDADES
 * ============================================================ */

/** URL base del sitio (carpeta donde está carrito.php), sin "/" final. */
function url_base()
{
    if (TIENDA_URL_BASE !== '') {
        return rtrim(TIENDA_URL_BASE, '/');
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $carpeta = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    return ($https ? 'https' : 'http') . '://' . $host . $carpeta;
}

/** true si el sitio corre en localhost (algunas pasarelas no aceptan esas URLs). */
function es_localhost()
{
    $host = parse_url(url_base(), PHP_URL_HOST);
    return in_array($host, array('localhost', '127.0.0.1', '::1'), true)
        || preg_match('/^(192\.168|10\.)\./', (string) $host);
}

/**
 * Petición HTTP que devuelve array(codigo_http, json_decodificado).
 * $auth puede ser 'Bearer xxx' o 'Basic xxx'.
 */
function http_json($metodo, $url, $auth, $cuerpo = null, $extraHeaders = array())
{
    $headers = array_merge(array('Authorization: ' . $auth, 'Accept: application/json'), $extraHeaders);
    $esForm = is_string($cuerpo);
    if ($cuerpo !== null && !$esForm) {
        $headers[] = 'Content-Type: application/json';
        $cuerpo = json_encode($cuerpo);
    }

    if (!function_exists('curl_init')) {
        error_log('[Pagos] La extensión cURL de PHP no está activa.');
        return array(0, null);
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_CUSTOMREQUEST => $metodo,
        CURLOPT_HTTPHEADER => $headers,
    ));
    if ($cuerpo !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $cuerpo);
    }
    $respuesta = curl_exec($ch);
    if ($respuesta === false) {
        error_log('[Pagos] Error cURL: ' . curl_error($ch));
    }
    $codigo = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return array($codigo, $respuesta ? json_decode($respuesta, true) : null);
}

function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

/* ============================================================
 *  CARRITO Y PEDIDOS
 * ============================================================ */

/**
 * Recibe los items del navegador ([{id:1},{id:1},{id:3}]) y devuelve
 * el detalle con precios reales de la BD. Lanza Exception si algo falla.
 */
function calcular_carrito($conexion, $items)
{
    if (!is_array($items) || empty($items)) {
        throw new Exception('Tu carrito está vacío.');
    }

    $cantidades = array();
    $totalItems = 0;
    foreach ($items as $item) {
        $id = isset($item['id']) ? filter_var($item['id'], FILTER_VALIDATE_INT) : false;
        if ($id === false || $id <= 0) {
            continue;
        }
        $cantidades[$id] = isset($cantidades[$id]) ? $cantidades[$id] + 1 : 1;
        $totalItems++;
    }
    if (empty($cantidades)) {
        throw new Exception('No hay productos válidos en el carrito.');
    }
    if ($totalItems > PAGO_MAX_ITEMS) {
        throw new Exception('El carrito supera el máximo permitido de productos.');
    }

    $detalle = array();
    $total = 0;
    $stmt = mysqli_prepare($conexion, "SELECT id, nombre, precio_rebajado FROM productos WHERE id = ?");
    foreach ($cantidades as $id => $cantidad) {
        if ($cantidad > PAGO_MAX_POR_PRODUCTO) {
            throw new Exception('Cantidad máxima por producto: ' . PAGO_MAX_POR_PRODUCTO);
        }
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $producto = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if (!$producto) {
            continue; // el producto ya no existe
        }
        $precio = (float) $producto['precio_rebajado'];
        $subtotal = $precio * $cantidad;
        $total += $subtotal;
        $detalle[] = array(
            'id' => (int) $producto['id'],
            'nombre' => $producto['nombre'],
            'precio' => $precio,
            'cantidad' => $cantidad,
            'subtotal' => $subtotal,
        );
    }
    mysqli_stmt_close($stmt);

    if ($total <= 0 || empty($detalle)) {
        throw new Exception('No fue posible calcular el total del pedido.');
    }
    return array('detalle' => $detalle, 'total' => $total);
}

/** Crea la tabla `pedidos` o le agrega las columnas que falten. */
function asegurar_tabla_pedidos($conexion)
{
    mysqli_query($conexion, "CREATE TABLE IF NOT EXISTS `pedidos` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `referencia` varchar(60) NOT NULL,
        `usuario` varchar(255) DEFAULT NULL,
        `pasarela` varchar(20) NOT NULL DEFAULT 'wompi',
        `total` decimal(12,2) NOT NULL,
        `monto_centavos` bigint(20) NOT NULL,
        `estado` varchar(20) NOT NULL DEFAULT 'PENDIENTE',
        `transaccion_id` varchar(80) DEFAULT NULL,
        `metodo_pago` varchar(40) DEFAULT NULL,
        `detalle` text,
        `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `actualizado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `referencia` (`referencia`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Si la tabla venía de la versión anterior (solo Wompi), le falta `pasarela`
    $col = mysqli_query($conexion, "SHOW COLUMNS FROM `pedidos` LIKE 'pasarela'");
    if ($col && mysqli_num_rows($col) === 0) {
        mysqli_query($conexion, "ALTER TABLE `pedidos` ADD `pasarela` varchar(20) NOT NULL DEFAULT 'wompi' AFTER `usuario`");
    }
}

function crear_pedido($conexion, $pasarela, $carrito)
{
    asegurar_tabla_pedidos($conexion);

    $referencia = 'MH-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(4)));
    $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
    $total = $carrito['total'];
    $centavos = (int) round($total * 100);
    $estado = 'PENDIENTE';
    $detalleJson = json_encode($carrito['detalle'], JSON_UNESCAPED_UNICODE);

    $stmt = mysqli_prepare(
        $conexion,
        "INSERT INTO pedidos (referencia, usuario, pasarela, total, monto_centavos, estado, detalle) VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "sssdiss", $referencia, $usuario, $pasarela, $total, $centavos, $estado, $detalleJson);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('No se pudo registrar el pedido.');
    }
    mysqli_stmt_close($stmt);

    return array('referencia' => $referencia, 'total' => $total, 'centavos' => $centavos);
}

function buscar_pedido($conexion, $referencia)
{
    asegurar_tabla_pedidos($conexion);
    $stmt = mysqli_prepare($conexion, "SELECT * FROM pedidos WHERE referencia = ?");
    mysqli_stmt_bind_param($stmt, "s", $referencia);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $fila ?: null;
}

/**
 * Actualiza estado del pedido. Un pedido APROBADO no vuelve a PENDIENTE
 * (las notificaciones pueden llegar desordenadas).
 */
function actualizar_pedido($conexion, $referencia, $estado, $transaccionId, $metodo)
{
    asegurar_tabla_pedidos($conexion);
    $stmt = mysqli_prepare(
        $conexion,
        "UPDATE pedidos SET estado = ?, transaccion_id = ?, metodo_pago = ?
         WHERE referencia = ? AND (estado <> 'APROBADO' OR ? = 'APROBADO')"
    );
    mysqli_stmt_bind_param($stmt, "sssss", $estado, $transaccionId, $metodo, $referencia, $estado);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/* ============================================================
 *  WOMPI  (PSE, Nequi, Bancolombia, Daviplata, tarjetas)
 * ============================================================ */

function wompi_crear_url($pedido)
{
    $firma = hash('sha256', $pedido['referencia'] . $pedido['centavos'] . 'COP' . WOMPI_INTEGRITY_SECRET);
    $params = array(
        'public-key' => WOMPI_PUBLIC_KEY,
        'currency' => 'COP',
        'amount-in-cents' => $pedido['centavos'],
        'reference' => $pedido['referencia'],
        'signature:integrity' => $firma,
    );
    if (!es_localhost()) {
        $params['redirect-url'] = url_base() . '/respuesta_pago.php';
    }
    return WOMPI_CHECKOUT_URL . '?' . http_build_query($params);
}

function wompi_consultar($idTransaccion)
{
    list($codigo, $json) = http_json(
        'GET',
        WOMPI_API_URL . '/transactions/' . rawurlencode($idTransaccion),
        'Bearer ' . WOMPI_PUBLIC_KEY
    );
    return ($codigo === 200 && isset($json['data'])) ? $json['data'] : null;
}

function wompi_estado($estado)
{
    $mapa = array('APPROVED' => 'APROBADO', 'DECLINED' => 'RECHAZADO', 'VOIDED' => 'ANULADO', 'ERROR' => 'ERROR');
    return isset($mapa[$estado]) ? $mapa[$estado] : 'PENDIENTE';
}

/* ============================================================
 *  MERCADO PAGO  (Checkout Pro)
 * ============================================================ */

function mp_crear_url($pedido, $carrito)
{
    $items = array();
    foreach ($carrito['detalle'] as $p) {
        $items[] = array(
            'id' => (string) $p['id'],
            'title' => function_exists('mb_substr') ? mb_substr($p['nombre'], 0, 250) : substr($p['nombre'], 0, 250),
            'quantity' => (int) $p['cantidad'],
            'currency_id' => 'COP',
            'unit_price' => (float) $p['precio'],
        );
    }

    $retorno = url_base() . '/respuesta_pago.php?pasarela=mercadopago';
    $preferencia = array(
        'items' => $items,
        'external_reference' => $pedido['referencia'],
        'statement_descriptor' => 'MHGLOBOS',
        'back_urls' => array('success' => $retorno, 'failure' => $retorno, 'pending' => $retorno),
    );
    // Mercado Pago no acepta auto_return ni webhooks hacia localhost
    if (!es_localhost()) {
        $preferencia['auto_return'] = 'approved';
        $preferencia['notification_url'] = url_base() . '/webhook_mercadopago.php';
    }

    list($codigo, $json) = http_json('POST', MP_API_URL . '/checkout/preferences', 'Bearer ' . MP_ACCESS_TOKEN, $preferencia);
    if (($codigo !== 201 && $codigo !== 200) || empty($json['init_point'])) {
        error_log('[MercadoPago] Error creando preferencia: ' . json_encode($json));
        throw new Exception('No se pudo conectar con Mercado Pago. Revisa el Access Token en config/pagos.php.');
    }
    return $json['init_point'];
}

function mp_consultar_pago($idPago)
{
    list($codigo, $json) = http_json(
        'GET',
        MP_API_URL . '/v1/payments/' . rawurlencode($idPago),
        'Bearer ' . MP_ACCESS_TOKEN
    );
    return ($codigo === 200 && isset($json['id'])) ? $json : null;
}

function mp_estado($estado)
{
    $mapa = array(
        'approved' => 'APROBADO', 'rejected' => 'RECHAZADO', 'cancelled' => 'ANULADO',
        'refunded' => 'ANULADO', 'charged_back' => 'ANULADO',
    );
    return isset($mapa[$estado]) ? $mapa[$estado] : 'PENDIENTE';
}

/**
 * Valida un pago de Mercado Pago contra el pedido y lo actualiza.
 * Devuelve array(estado, referencia, totalCop, metodo) o null.
 */
function mp_procesar_pago($conexion, $idPago)
{
    $pago = mp_consultar_pago($idPago);
    if (!$pago || empty($pago['external_reference'])) {
        return null;
    }
    $referencia = $pago['external_reference'];
    $pedido = buscar_pedido($conexion, $referencia);
    if (!$pedido) {
        return null;
    }

    $estado = mp_estado($pago['status']);
    // Protección: el monto pagado debe coincidir con el del pedido
    if ($estado === 'APROBADO' && round((float) $pago['transaction_amount']) < round((float) $pedido['total'])) {
        error_log("[MercadoPago] Monto no coincide en $referencia");
        $estado = 'ERROR';
    }
    $metodo = isset($pago['payment_method_id']) ? $pago['payment_method_id'] : 'mercadopago';
    actualizar_pedido($conexion, $referencia, $estado, (string) $pago['id'], $metodo);

    return array($estado, $referencia, (float) $pedido['total'], $metodo, (string) $pago['id']);
}

/* ============================================================
 *  PAYPAL  (Orders API v2, cobra en USD)
 * ============================================================ */

function paypal_token()
{
    list($codigo, $json) = http_json(
        'POST',
        PAYPAL_API_URL . '/v1/oauth2/token',
        'Basic ' . base64_encode(PAYPAL_CLIENT_ID . ':' . PAYPAL_SECRET),
        'grant_type=client_credentials',
        array('Content-Type: application/x-www-form-urlencoded')
    );
    if ($codigo !== 200 || empty($json['access_token'])) {
        error_log('[PayPal] Error de autenticación: ' . json_encode($json));
        throw new Exception('No se pudo conectar con PayPal. Revisa las credenciales en config/pagos.php.');
    }
    return $json['access_token'];
}

function paypal_usd($totalCop)
{
    return number_format(max(1, $totalCop / PAYPAL_TASA_COP_POR_USD), 2, '.', '');
}

function paypal_crear_url($conexion, $pedido)
{
    $token = paypal_token();
    $retorno = url_base() . '/respuesta_pago.php?pasarela=paypal';
    $orden = array(
        'intent' => 'CAPTURE',
        'purchase_units' => array(array(
            'reference_id' => $pedido['referencia'],
            'custom_id' => $pedido['referencia'],
            'description' => 'Pedido ' . $pedido['referencia'] . ' - ' . TIENDA_NOMBRE,
            'amount' => array('currency_code' => 'USD', 'value' => paypal_usd($pedido['total'])),
        )),
        'application_context' => array(
            'brand_name' => TIENDA_NOMBRE,
            'locale' => 'es-CO',
            'shipping_preference' => 'NO_SHIPPING',
            'user_action' => 'PAY_NOW',
            'return_url' => $retorno,
            'cancel_url' => $retorno . '&cancelado=1',
        ),
    );

    list($codigo, $json) = http_json('POST', PAYPAL_API_URL . '/v2/checkout/orders', 'Bearer ' . $token, $orden);
    if ($codigo !== 201 || empty($json['id'])) {
        error_log('[PayPal] Error creando orden: ' . json_encode($json));
        throw new Exception('PayPal no pudo crear la orden.');
    }
    // Guardamos el ID de la orden PayPal para encontrar el pedido al volver
    actualizar_pedido($conexion, $pedido['referencia'], 'PENDIENTE', $json['id'], 'paypal');

    foreach ($json['links'] as $link) {
        if ($link['rel'] === 'approve' || $link['rel'] === 'payer-action') {
            return $link['href'];
        }
    }
    throw new Exception('PayPal no devolvió el enlace de pago.');
}

/**
 * Captura (cobra) la orden cuando el cliente vuelve de PayPal.
 * Devuelve array(estado, referencia, totalCop, metodo, idTransaccion) o null.
 */
function paypal_capturar($conexion, $ordenId)
{
    // Buscar el pedido por el ID de orden guardado
    asegurar_tabla_pedidos($conexion);
    $stmt = mysqli_prepare($conexion, "SELECT * FROM pedidos WHERE transaccion_id = ? AND pasarela = 'paypal'");
    mysqli_stmt_bind_param($stmt, "s", $ordenId);
    mysqli_stmt_execute($stmt);
    $pedido = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$pedido) {
        return null;
    }
    if ($pedido['estado'] === 'APROBADO') {   // recarga de página: ya se cobró
        return array('APROBADO', $pedido['referencia'], (float) $pedido['total'], 'paypal', $ordenId);
    }

    $token = paypal_token();
    list($codigo, $json) = http_json(
        'POST',
        PAYPAL_API_URL . '/v2/checkout/orders/' . rawurlencode($ordenId) . '/capture',
        'Bearer ' . $token,
        new stdClass()
    );

    $estado = 'RECHAZADO';
    $idCaptura = $ordenId;
    if (($codigo === 201 || $codigo === 200) && isset($json['status']) && $json['status'] === 'COMPLETED') {
        $captura = $json['purchase_units'][0]['payments']['captures'][0];
        $idCaptura = $captura['id'];
        $esperado = paypal_usd((float) $pedido['total']);
        $ok = $captura['status'] === 'COMPLETED'
            && $captura['amount']['currency_code'] === 'USD'
            && (float) $captura['amount']['value'] >= (float) $esperado;
        $estado = $ok ? 'APROBADO' : ($captura['status'] === 'PENDING' ? 'PENDIENTE' : 'ERROR');
    } else {
        error_log('[PayPal] Captura fallida: ' . json_encode($json));
    }

    // Se conserva el ID de la orden en transaccion_id para poder reconocer recargas
    actualizar_pedido($conexion, $pedido['referencia'], $estado, $ordenId, 'paypal');
    return array($estado, $pedido['referencia'], (float) $pedido['total'], 'paypal', $idCaptura);
}
