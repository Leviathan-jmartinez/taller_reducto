<?php
$peticionAjax = true;
require_once "../config/APP.php";
if (isset($_POST['cliente_doc_reg']) || isset($_POST['cliente_id_del'])) {
    /** Instancia al controlador */
    require_once "../controladores/clienteControlador.php";
    $inst_cliente = new clienteControlador();
    /** Agregar un usuario */
    if (isset($_POST['cliente_doc_reg']) && isset($_POST['cliente_nombre_reg'])) {
        echo $inst_cliente->agregar_cliente_controlador();
    }
    /** Eliminar usuario */
    if (isset($_POST['cliente_id_del'])) {
        echo $inst_cliente->eliminar_cliente_controlador();
    }
} else {
    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}
