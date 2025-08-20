<?php
$peticionAjax = true;
require_once "../config/APP.php";
if (isset($_POST['buscar_proveedor']) || isset($_POST['id_agregar_proveedor']) || isset($_POST['id_eliminar_proveedor']) || isset($_POST['buscar_articulo'])
    || isset($_POST['id_agregar_articulo']) || isset($_POST['id_eliminar_articulo'])) {
    /** Instancia al controlador */
    require_once "../controladores/pedidoControlador.php";
    $inst_pedido = new pedidoControlador();
    
    if (isset($_POST['buscar_proveedor'])) {
        echo $inst_pedido->buscar_proveedor_controlador();
    }
    if (isset($_POST['id_agregar_proveedor'])) {
        echo $inst_pedido->agregar_proveedor_controlador();
    }
    if (isset($_POST['id_eliminar_proveedor'])) {
        echo $inst_pedido->eliminar_proveedor_controlador();
    }
    if (isset($_POST['buscar_articulo'])) {
        echo $inst_pedido->articulo_controlador();
    }
    if (isset($_POST['id_agregar_articulo'])) {
        echo $inst_pedido->articulo_controlador();
    }
    if (isset($_POST['id_eliminar_articulo'])) {
        echo $inst_pedido->eliminar_articulo_controlador();
    }
} else {
    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}
