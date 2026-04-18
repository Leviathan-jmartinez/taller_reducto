<?php
$peticionAjax = true;
require_once "../config/APP.php";

require_once "../controladores/diagnosticoControlador.php";
$inst_diag = new diagnosticoControlador();

if (isset($_POST['buscar_recepcion'])) {
    echo $inst_diag->buscar_recepcion_controlador();
    exit();
}

if (isset($_POST['accion'])) {

    if ($_POST['accion'] == "listar_equipos") {
        echo json_encode($inst_diag->listar_equipos_controlador());
        exit();
    }

    if ($_POST['accion'] == "guardar_diagnostico") {
        echo $inst_diag->guardar_diagnostico_controlador();
        exit();
    }
}
