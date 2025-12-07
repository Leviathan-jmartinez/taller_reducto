<?php
$peticionAjax = true;
require_once "../config/APP.php";

// Iniciar sesión de forma única
if (session_status() == PHP_SESSION_NONE) {
    session_start(['name' => 'STR']);
}

require_once "../controladores/compraControlador.php";
$inst_compra = new compraControlador();

if (isset($_POST['buscar_oc'])) {
    echo $inst_compra->buscar_oc_controlador();
    exit();
}

if (isset($_POST['id_oc_seleccionado'])) {
    echo $inst_compra->cargar_oc_controlador();
    exit();
}

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

// Actualización de cantidades y precios desde JS
if (isset($_POST['index'])) {
    $idx = intval($_POST['index']);
    if (isset($_SESSION['Cdatos_articuloOC'][$idx])) {
        $_SESSION['Cdatos_articuloOC'][$idx]['cantidad'] = floatval($_POST['cantidad']);
        $_SESSION['Cdatos_articuloOC'][$idx]['precio']   = floatval($_POST['precio']);
        $_SESSION['Cdatos_articuloOC'][$idx]['subtotal'] = floatval($_POST['subtotal']);
        $_SESSION['Cdatos_articuloOC'][$idx]['iva']      = floatval($_POST['iva']);
        echo json_encode(["status" => "ok"]);
        exit();
    }
    echo json_encode(["status" => "error", "msg" => "Índice no existe"]);
    exit();
}

// Si el POST no coincide con nada, simplemente devuelve error sin cerrar sesión
echo json_encode(["status" => "error", "msg" => "Acción no definida"]);
exit();
