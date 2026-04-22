<?php
$peticionAjax = true;
require_once "../controladores/reclamoServicioControlador.php";

$ins = new reclamoServicioControlador();

if ($_POST['accion'] === 'registrar_reclamo') {
    echo $ins->registrar_reclamo_controlador();
    exit();
}

if ($_POST['accion'] === 'buscar_registro') {
    echo $ins->buscar_registro_controlador();
    exit();
}

if ($_POST['accion'] === 'anular_reclamo') {
    echo $ins->anular_reclamo_controlador();
    exit();
}
if ($_POST['accion'] == "obtener_reclamo") {
    echo json_encode(
        $ins->obtener_reclamo_para_recepcion_controlador()
    );
    exit();
}
if ($_POST['accion'] == "buscar_reclamo_recepcion") {
    echo $ins->buscar_reclamo_recepcion_controlador();
}
