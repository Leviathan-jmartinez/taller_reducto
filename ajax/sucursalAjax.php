<?php
$peticionAjax = true;
require_once "../config/APP.php";

/* ========= VALIDAR PETICIÓN ========= */
if (
    isset($_POST['sucursal_descri_reg']) ||   // agregar
    isset($_POST['sucursal_id_up']) ||         // actualizar
    isset($_POST['sucursal_id_del'])            // eliminar
) {

    require_once "../controladores/sucursalControlador.php";
    $inst = new sucursalControlador();

    /* ===== AGREGAR ===== */
    if (isset($_POST['sucursal_descri_reg'])) {
        echo $inst->agregar_sucursal_controlador();
        exit();
    }

    /* ===== ACTUALIZAR ===== */
    if (isset($_POST['sucursal_id_up'])) {
        echo $inst->actualizar_sucursal_controlador();
        exit();
    }

    /* ===== ELIMINAR ===== */
    if (isset($_POST['sucursal_id_del'])) {
        echo $inst->eliminar_sucursal_controlador();
        exit();
    }
} else {
    /* ⚠️ NUNCA REDIRECCIONAR: DEVOLVER JSON */
    echo json_encode([
        "Alerta" => "simple",
        "Titulo" => "Petición inválida",
        "Texto"  => "No se pudo procesar la solicitud",
        "Tipo"   => "error"
    ]);
    exit();
}
