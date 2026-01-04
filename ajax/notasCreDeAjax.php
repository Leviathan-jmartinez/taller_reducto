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
    $ok = notasCreDeControlador::seleccionarFactura(
        intval($_POST['seleccionar_factura'])
    );

    echo json_encode([
        'status' => $ok ? 'ok' : 'error',
        'msg'    => $ok ? 'Factura seleccionada' : 'No se pudo seleccionar la factura'
    ]);
    exit;
}

if (isset($_POST['accion']) && $_POST['accion'] === 'actualizar_item_nc') {
    echo json_encode(
        notasCreDeControlador::actualizarItemNC($_POST)
    );
    exit;
}
// ajax/notasCreDeAjax.php

if (isset($_POST['accion']) && $_POST['accion'] === 'guardar_nota_compra') {
    $resp = notasCreDeControlador::guardarNotaCompraControlador();
    echo json_encode($resp);
    exit;
}
if (isset($_POST['accion']) && $_POST['accion'] === 'limpiar_nc') {
    session_start(['name' => 'STR']);
    unset($_SESSION['NC_DETALLE'], $_SESSION['NC_FACTURA']);
    echo json_encode(['status' => 'ok']);
    exit;
}
if (isset($_POST['notaCreDe_id_del'])) {
    echo json_encode(
        notasCreDeControlador::anularNotaCompraControlador()
    );
    exit;
}
