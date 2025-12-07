
<?php
$peticionAjax = true;
require_once "../config/APP.php";
if (
    isset($_POST['buscar_oc']) || isset($_POST['id_oc_seleccionado'])
) {
    /** Instancia al controlador */
    require_once "../controladores/compraControlador.php";
    $inst_compra = new compraControlador();

    if (isset($_POST['buscar_oc'])) {
        echo $inst_compra->buscar_oc_controlador();
    }
    if (isset($_POST['id_oc_seleccionado'])) {
        echo $inst_compra->cargar_oc_controlador();
    }
    

    if (isset($_POST['limpiar_presupuesto'])) {
        session_start(['name' => 'STR']);
        unset($_SESSION['tipo_presupuesto']);
        unset($_SESSION['Sdatos_proveedorPre']);
        unset($_SESSION['Cdatos_proveedorPre']);
        unset($_SESSION['Sdatos_articuloPre']);
        unset($_SESSION['Cdatos_articuloPre']);
        unset($_SESSION['presupuesto_articulo']);
        unset($_SESSION['total_pre']);
        // Redirigir a la página de nuevo pedido
        header("Location: " . SERVERURL . "presupuesto-nuevo/");
        exit();
    }
} else {
    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}
