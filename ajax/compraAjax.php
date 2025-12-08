<?php
$peticionAjax = true;
require_once "../config/APP.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start(['name' => 'STR']);
}

require_once "../controladores/compraControlador.php";
$inst_compra = new compraControlador();

/* ===============================
   BUSCAR ORDEN DE COMPRA
================================ */
if (isset($_POST['buscar_oc'])) {
    echo $inst_compra->buscar_oc_controlador();
    exit();
}

/* ===============================
   CARGAR ORDEN SELECCIONADA
================================ */
if (isset($_POST['id_oc_seleccionado'])) {
    echo $inst_compra->cargar_oc_controlador();
    exit();
}

/* ===============================
   LIMPIAR SESIÓN
================================ */
if (isset($_POST['limpiar_presupuesto'])) {
    unset($_SESSION['tipo_presupuesto']);
    unset($_SESSION['Sdatos_proveedorPre']);
    unset($_SESSION['Cdatos_proveedorPre']);
    unset($_SESSION['Sdatos_articuloPre']);
    unset($_SESSION['Cdatos_articuloPre']);
    unset($_SESSION['presupuesto_articulo']);
    unset($_SESSION['total_pre']);
    header("Location: " . SERVERURL . "presupuesto-nuevo/");
    exit();
}

/* ===============================
   ACTUALIZAR DETALLES EN SESIÓN
================================ */
if (isset($_POST['index'])) {
    $i = intval($_POST['index']);

    if (isset($_SESSION['Cdatos_articuloOC'][$i])) {

        $cantidad = floatval($_POST['cantidad']);
        $precio   = floatval($_POST['precio']);

        // ❌ BLOQUEAR CANTIDADES O PRECIOS EN CERO
        if ($cantidad <= 0) {
            echo json_encode([
                "status" => "error",
                "msg" => "La cantidad no puede ser cero"
            ]);
            exit();
        }

        if ($precio <= 0) {
            echo json_encode([
                "status" => "error",
                "msg" => "El precio no puede ser cero"
            ]);
            exit();
        }

        // ✔️ ACTUALIZAR SESIÓN SOLO SI LOS DATOS SON VÁLIDOS
        $_SESSION['Cdatos_articuloOC'][$i]['cantidad'] = $cantidad;
        $_SESSION['Cdatos_articuloOC'][$i]['precio']   = $precio;
        $_SESSION['Cdatos_articuloOC'][$i]['subtotal'] = floatval($_POST['subtotal']);
        $_SESSION['Cdatos_articuloOC'][$i]['iva']      = floatval($_POST['iva']);

        echo json_encode(["status" => "ok"]);
        exit();
    }

    echo json_encode(["status" => "error", "msg" => "Índice no existe"]);
    exit();
}


/* ===============================
   GUARDAR COMPRA
================================ */
if (isset($_POST['accion']) && $_POST['accion'] == "guardar_compra") {
    echo json_encode($inst_compra->guardar_compra_controlador());
    exit();
}

/* ===============================
   ACCIÓN NO RECONOCIDA
================================ */
echo json_encode(["status" => "error", "msg" => "Acción no definida"]);
exit();
