
<?php
$peticionAjax = true;
require_once "../config/APP.php";
if (
    isset($_POST['buscar_proveedorPre']) || isset($_POST['id_agregar_proveedorPre']) || isset($_POST['id_eliminar_proveedorPre']) || isset($_POST['buscar_articuloPre'])
    || isset($_POST['id_agregar_articuloPre']) || isset($_POST['id_eliminar_articuloPre']) || isset($_POST['agregar_presupuesto']) || isset($_POST['limpiar_presupuesto'])
    || isset($_POST['buscar_pedidoPre']) || isset($_POST['id_pedido_seleccionado']) || isset($_POST['id_actualizar_precio'])
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
    if (isset($_POST['agregar_presupuesto'])) {
        echo $inst_presu->agregar_presupuesto_controlador();
    }
    if (isset($_POST['buscar_pedidoPre'])) {
        echo $inst_presu->buscar_pedido_controlador();
    }
    if (isset($_POST['id_pedido_seleccionado'])) {
        echo $inst_presu->cargar_pedido_controlador();
    }
    if (isset($_POST['id_actualizar_precio'])) {
        $idArticulo = $_POST['id_actualizar_precio'];
        $precio = floatval($_POST['precio']);
        session_start(['name' => 'STR']);

        if (isset($_SESSION['Cdatos_articuloPre'])) {
            foreach ($_SESSION['Cdatos_articuloPre'] as &$art) {
                if ($art['ID'] == $idArticulo) {
                    $art['precio'] = $precio;
                    $art['subtotal'] = $art['cantidad'] * $precio;
                    break;
                }
            }
            $_SESSION['total_pre'] = 0;
            foreach ($_SESSION['Cdatos_articuloPre'] as $art) {
                $_SESSION['total_pre'] += $art['subtotal'];
            }
        }
        exit();
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
