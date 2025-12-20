<?php
// ajax/notasCreDeAjax.php
$peticionAjax = true;

require_once "../config/APP.php";
require_once "../controladores/notasCreDeControlador.php";

if (isset($_POST['buscar_factura'])) {
    echo notasCreDeControlador::buscarFacturas(
        trim($_POST['buscar_factura'])
    );
    exit;
}

if (isset($_POST['seleccionar_factura'])) {
    echo notasCreDeControlador::seleccionarFactura(
        intval($_POST['seleccionar_factura'])
    ) ? 'OK' : 'ERROR';
    exit;
}
if (isset($_POST['accion']) && $_POST['accion'] === 'actualizar_item_nc') {
    echo json_encode(
        notasCreDeControlador::actualizarItemNC($_POST)
    );
    exit;
}
