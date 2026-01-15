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
