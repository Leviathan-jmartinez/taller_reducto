<?php
$peticionAjax = true;
require_once "../config/APP.php";
ob_start();
session_start(['name' => 'STR']);

require_once "../controladores/inventarioControlador.php";
$inst_inventario = new inventarioControlador();

// Categorías
if (isset($_POST['cargar_categorias'])) {
    echo $inst_inventario->cargar_categorias_controlador();
    exit();
}

// Proveedores
if (isset($_POST['cargar_proveedores'])) {
    echo $inst_inventario->cargar_proveedores_controlador();
    exit();
}

// Productos para Select2
if (isset($_POST['buscar_producto'])) {
    $inst_inventario->cargarArticulosControlador($_POST['buscar_producto']);
    exit();
}

// Guardar inventario
if (isset($_POST['tipo_inventario'])) {
    $resp = $inst_inventario->guardarInventarioControlador();
    echo json_encode($resp);
    exit();
}
// Buscar inventario
if (isset($_POST['buscar_inv'])) {
    echo $inst_inventario->buscar_inv_controlador();
    exit();
}
// Buscar inventario
if (isset($_POST['id_inv_seleccionado'])) {
    echo $inst_inventario->cargar_inv_controlador();
    exit();
}
// Actualizar cantidad física
if (isset($_POST['index'])) {

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start(['name' => 'STR']);
    }

    $i = intval($_POST['index']);

    if (!isset($_SESSION['Cdatos_articuloINV'][$i])) {
        echo json_encode([
            "status" => "error",
            "msg" => "Índice no existe"
        ]);
        exit();
    }

    $cantidad_fisica = intval($_POST['cantidad_fisica']);

    if ($cantidad_fisica < 0) {
        echo json_encode([
            "status" => "error",
            "msg" => "La cantidad física no puede ser negativa"
        ]);
        exit();
    }

    $cantidad_teorica = $_SESSION['Cdatos_articuloINV'][$i]['cantidad_teorica'];
    $diferencia = $cantidad_fisica - $cantidad_teorica;

    // ✔️ Actualizar sesión
    $_SESSION['Cdatos_articuloINV'][$i]['cantidad_fisica'] = $cantidad_fisica;
    $_SESSION['Cdatos_articuloINV'][$i]['diferencia']      = $diferencia;

    echo json_encode([
        "status" => "ok",
        "diferencia" => $diferencia
    ]);
    exit();
}

// Guardar ajuste de inventario en BD
if (isset($_POST['guardar_ajuste'])) {
    $resp = $inst_inventario->guardar_ajuste_inv_controlador();
    echo json_encode($resp);
    exit();
}
// Aplicar ajuste de stock
if (isset($_POST['aplicar_stock'])) {
    $idsucursal = $_SESSION['nick_sucursal'];
    $resp = $inst_inventario->aplicar_ajuste_stock_controlador($idsucursal);
    echo json_encode($resp);
    exit();
}

// Limpiar ajuste y variables de sesión
if (isset($_POST['limpiar_ajuste'])) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start(['name' => 'STR']);
    }

    unset($_SESSION['Cdatos_articuloINV']);
    unset($_SESSION['id_ajuste_seleccionado']);
    unset($_SESSION['datos_ajuste_inv']);
    unset($_SESSION['alerta_inv']);

    echo json_encode(["status" => "ok", "msg" => "Variables de ajuste limpiadas correctamente"]);
    exit();
}
