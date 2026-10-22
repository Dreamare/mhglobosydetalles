<?php
/**
 * ajax.php - Devuelve los productos del carrito con precios de la BD,
 * agrupados por producto y con su cantidad.
 */
require_once __DIR__ . '/lib/pagos.php';
header('Content-Type: application/json; charset=utf-8');

if (isset($_POST['action']) && $_POST['action'] === 'buscar') {
    try {
        $carrito = calcular_carrito($conexion, isset($_POST['data']) ? $_POST['data'] : array());
        echo json_encode(array('datos' => $carrito['detalle'], 'total' => $carrito['total']));
    } catch (Exception $ex) {
        echo json_encode(array('datos' => array(), 'total' => 0, 'mensaje' => $ex->getMessage()));
    }
    exit;
}
echo json_encode(array('datos' => array(), 'total' => 0));
