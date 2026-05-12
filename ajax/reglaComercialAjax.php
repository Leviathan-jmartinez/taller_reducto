<?php
$peticionAjax = true;
require_once "../config/SERVER.php";
require_once "../controladores/reglaComercialControlador.php";

$regla = new reglaComercialControlador();

if (isset($_POST['accion'])) {
    if ($_POST['accion'] === 'guardar_regla') {
        echo $regla->guardar_regla_controlador();
        exit;
    }

    if ($_POST['accion'] === 'editar_regla') {
        echo $regla->editar_regla_controlador();
        exit;
    }
}

exit;
