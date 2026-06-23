
<?php
$peticionAjax = true;
require_once "../config/APP.php";
if (
    isset($_POST['buscar_proveedorPre']) || isset($_POST['id_agregar_proveedorPre']) || isset($_POST['id_eliminar_proveedorPre'])
    || isset($_POST['id_eliminar_articuloPre']) || isset($_POST['agregar_presupuesto']) || isset($_POST['limpiar_presupuesto'])
    || isset($_POST['buscar_pedidoPre']) || isset($_POST['id_pedido_seleccionado']) || isset($_POST['id_actualizar_precio']) || isset($_POST['presupuesto_id_del'])
    || isset($_POST['detalle_presupuesto_compra'])
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
    if (isset($_POST['id_eliminar_articuloPre'])) {
        echo $inst_presu->eliminar_articulo_controlador();
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

        session_start(['name' => 'STR']);
        header('Content-Type: application/json; charset=utf-8');

        $idArticulo = (int)$_POST['id_actualizar_precio'];
        $precio     = (float)$_POST['precio'];

        if ($idArticulo <= 0 || $precio <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Precio invalido",
                "Texto" => "El precio del articulo debe ser mayor a cero",
                "Tipo" => "error"
            ]);
            exit;
        }

        $key = 'Cdatos_articuloPre';

        if (isset($_SESSION[$key][$idArticulo])) {
            $_SESSION[$key][$idArticulo]['precio'] = $precio;
            $_SESSION[$key][$idArticulo]['subtotal'] =
                $_SESSION[$key][$idArticulo]['cantidad'] * $precio;
        }

        // recalcular total
        $_SESSION['total_pre'] = 0;
        foreach ($_SESSION['Cdatos_articuloPre'] ?? [] as $art) {
            $_SESSION['total_pre'] += $art['subtotal'];
        }

        echo json_encode([
            "Alerta" => "simple",
            "Titulo" => "Precio actualizado",
            "Texto" => "El precio fue actualizado correctamente",
            "Tipo" => "success"
        ]);
        exit;
    }

    if (isset($_POST['presupuesto_id_del'])) {
        echo $inst_presu->anular_presupuesto_controlador();
    }
    if (isset($_POST['detalle_presupuesto_compra'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo $inst_presu->detalle_presupuesto_compra_controlador();
    }
    if (isset($_POST['limpiar_presupuesto'])) {
        session_start(['name' => 'STR']);
        unset($_SESSION['Cdatos_proveedorPre']);
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
