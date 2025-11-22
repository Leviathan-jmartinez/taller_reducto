
<?php
$peticionAjax = true;
require_once "../config/APP.php";
if (
    isset($_POST['buscar_proveedor']) || isset($_POST['id_agregar_proveedor']) || isset($_POST['id_eliminar_proveedor']) || isset($_POST['buscar_articulo'])
    || isset($_POST['id_agregar_articulo']) || isset($_POST['id_eliminar_articulo']) || isset($_POST['agregar_pedido']) || isset($_POST['limpiar_presupuesto']) || isset($_POST['pedido_id_del'])
) {
    /** Instancia al controlador */
    require_once "../controladores/presupuestoControlador.php";
    $inst_presu = new presupuestoControlador();

    if (isset($_POST['buscar_proveedor'])) {
        
    }
    if (isset($_POST['limpiar_presupuesto'])) {
        session_start(['name' => 'STR']);
        unset($_SESSION['tipo_presupuesto']);

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
