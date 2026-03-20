<?php
$peticionAjax = true;
require_once "../config/APP.php";

require_once "../controladores/diagnosticoControlador.php";
$inst_diag = new diagnosticoControlador();

if (isset($_POST['accion']) && $_POST['accion'] === 'guardar_diagnostico') {
    echo $inst_diag->guardar_diagnostico_controlador();
}

if (isset($_POST['buscar_recepcion'])) {
    echo $inst_diag->buscar_recepcion_controlador();
}