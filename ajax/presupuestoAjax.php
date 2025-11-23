
<?php
$peticionAjax = true;
require_once "../config/APP.php";
if (
    isset($_POST['buscar_proveedorPre']) || isset($_POST['id_agregar_proveedorPre']) || isset($_POST['id_eliminar_proveedorPre']) || isset($_POST['buscar_articuloPre'])
    || isset($_POST['id_agregar_articuloPre']) || isset($_POST['id_eliminar_articuloPre']) || isset($_POST['agregar_pedido']) || isset($_POST['limpiar_presupuesto']) || isset($_POST['pedido_id_del'])
) {
    /** Instancia al controlador */
    require_once "../controladores/presupuestoControlador.php";
    $inst_presu = new presupuestoControlador();

    if (isset($_POST['buscar_proveedorPre'])) {
        echo $inst_presu->buscar_proveedor_controlador();
    }
    if (isset($_POST['id_agregar_proveedorPre'])) {
        echo $inst_presu->agregar_proveedor_controlador();
    }
    if (isset($_POST['id_eliminar_proveedorPre'])) {
        echo $inst_presu->eliminar_proveedor_controlador();
    }
    if (isset($_POST['buscar_articuloPre'])) {
        echo $inst_presu->buscar_articulo_controlador();
    }
    if (isset($_POST['id_agregar_articuloPre'])) {
        echo $inst_presu->articulo_controlador();
    }
    if (isset($_POST['limpiar_presupuesto'])) {
        session_start(['name' => 'STR']);
        unset($_SESSION['tipo_presupuesto']);
        unset($_SESSION['datos_proveedorPre']);
        unset($_SESSION['datos_articuloPre']);
        unset($_SESSION['presupuesto_articulo']);
        unset($_SESSION['total_pre']);
        // Redirigir a la página de nuevo pedido
        header("Location: " . SERVERURL . "presupuesto-nuevo/");
        exit();
    }
    if (isset($_POST['pedido_id_del'])) {
        echo $inst_pedido->anular_pedido_controlador();
    }
} else {
    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}
