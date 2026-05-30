<?php
$peticionAjax = true;
require_once "../config/APP.php";
if (isset($_POST['articulo_codigo_reg']) || isset($_POST['articulo_id_del']) || isset($_POST['articulo_id_up'])) {
    /** Instancia al controlador */
    require_once "../controladores/articuloControlador.php";
    $inst_article = new articuloControlador();
    /** Agregar un articulo */
    if (isset($_POST['articulo_codigo_reg'])) {
        $inst_article->agregar_articulo_controlador();
    }
    if (isset($_POST['articulo_id_del'])) {
        $inst_article->eliminar_articulo_controlador();
    }
    if (isset($_POST['articulo_id_up'])) {
        $inst_article->actualizar_articulo_controlador();
    }
} else {
    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}
