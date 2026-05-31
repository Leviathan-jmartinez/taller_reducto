<?php
$peticionAjax = true;
require_once "../controladores/reclamoServicioControlador.php";

$ins = new reclamoServicioControlador();

$accion = $_POST['accion'] ?? '';

if ($accion === 'registrar_reclamo') {
    echo $ins->registrar_reclamo_controlador();
    exit();
}

if ($accion === 'buscar_registro') {
    echo $ins->buscar_registro_controlador();
    exit();
}

if ($accion === 'cargar_registro_reclamo') {
    echo $ins->cargar_registro_reclamo_controlador();
    exit();
}

if ($accion === 'anular_reclamo') {
    echo $ins->anular_reclamo_controlador();
    exit();
}
if ($accion == "obtener_reclamo") {
    echo json_encode(
        $ins->obtener_reclamo_para_recepcion_controlador()
    );
    exit();
}
if ($accion == "buscar_reclamo_recepcion") {
    echo $ins->buscar_reclamo_recepcion_controlador();
}
