<?php
$peticionAjax = true;
require_once "../config/APP.php";
if (isset($_POST['empresa_nombre_reg'])) {
    /** Instancia al controlador */
    require_once "../controladores/articuloControlador.php.php";
    $inst_article = new empresaControlador();
    /** Agregar un empresa */
    if (isset($_POST['empresa_nombre_reg'])) {
        echo $inst_article->agregar_empresa_controlador();
    }
    
} else {
    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}
