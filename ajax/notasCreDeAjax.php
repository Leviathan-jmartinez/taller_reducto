<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$peticionAjax = true;
require_once "../modelos/notasCreDeModelo.php";

header('Content-Type: application/json');

$response = ['success' => false, 'data' => []];

// 1️⃣ Buscar facturas
if (!empty($_POST['buscar_factura'])) {
    $buscar = trim($_POST['buscar_factura']);
    $response['data'] = notasCreDeModelo::buscarFacturas($buscar);
    $response['success'] = true;
}

// 2️⃣ Seleccionar factura
if (!empty($_POST['id_factura'])) {
    $idFactura = $_POST['id_factura'];
    $response['success'] = notasCreDeModelo::setFacturaSeleccionada($idFactura);
}

echo json_encode($response);
