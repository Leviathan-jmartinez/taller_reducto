
<?php
$peticionAjax = true;
require_once "../config/APP.php";
if (
    isset($_POST['idpresupuesto']) || isset($_POST['ordencompra_id_del']) || isset($_POST['buscar_proveedorOC']) || isset($_POST['id_agregar_proveedorOC'])
    || isset($_POST['buscar_articuloOC']) || isset($_POST['id_agregar_articuloOC']) || isset($_POST['agregar_ordencompra']) || isset($_POST['limpiar_ordencompra'])
) {
    /** Instancia al controlador */
    require_once "../controladores/ordencompraControlador.php";
    $inst_ocompra = new ordencompraControlador();

    if (isset($_POST['idpresupuesto'])) {
        echo $inst_ocompra->generar_oc_controlador();
    }
    if (isset($_POST['ordencompra_id_del'])) {
        echo $inst_ocompra->anular_ordencompra_controlador();
    }
    if (isset($_POST['buscar_proveedorOC'])) {
        echo $inst_ocompra->buscar_proveedor_controlador();
    }
    if (isset($_POST['id_agregar_proveedorOC'])) {
        echo $inst_ocompra->agregar_proveedor_controlador();
    }
    if (isset($_POST['buscar_articuloOC'])) {
        echo $inst_ocompra->buscar_articulo_controlador();
    }
    if (isset($_POST['id_agregar_articuloOC'])) {
        echo $inst_ocompra->articulo_controlador();
    }
    if (isset($_POST['agregar_ordencompra'])) {
        echo $inst_ocompra->agregar_oc_controlador();
    }
    if (isset($_POST['limpiar_ordencompra'])) {
        session_start(['name' => 'STR']);
        unset($_SESSION['tipo_ordencompra']);
        unset($_SESSION['Sdatos_proveedorOC']);
        unset($_SESSION['Sdatos_articuloOC']);
        unset($_SESSION['presupuesto_articulo']);
        unset($_SESSION['total_pre']);
        // Redirigir a la página de nuevo pedido
        header("Location: " . SERVERURL . "oc-nuevo/");
        exit();
    }
} else {
    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}
