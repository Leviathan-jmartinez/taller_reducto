<?php
$peticionAjax = true;
require_once "../config/APP.php";
if (isset($_POST['empresa_nombre_reg']) || isset($_POST['empresa_nombre_up'])) {
    /** Instancia al controlador */
    require_once "../controladores/empresaControlador.php";
    $inst_empresa = new empresaControlador();
    /** Agregar un empresa */
    if (isset($_POST['empresa_nombre_reg'])) {
        echo $inst_empresa->agregar_empresa_controlador();
    }
} else {
    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}
