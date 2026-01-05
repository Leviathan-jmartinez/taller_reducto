<?php
$peticionAjax = true;
require_once "../config/APP.php";

if (
    isset($_POST['razon_social_reg']) ||
    isset($_POST['proveedor_id_del']) ||
    isset($_POST['proveedor_id_up'])
) {
    require_once "../controladores/proveedorControlador.php";
    $inst = new proveedorControlador();

    if (isset($_POST['razon_social_reg'])) {
        echo $inst->agregar_proveedor_controlador();
    }

    if (isset($_POST['proveedor_id_del'])) {
        echo $inst->eliminar_proveedor_controlador();
    }

    if (isset($_POST['proveedor_id_up'])) {
        echo $inst->actualizar_proveedor_controlador();
    }
} else {
    
    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();
    echo json_encode([
        "Alerta" => "simple",
        "Titulo" => "Sesión",
        "Texto" => "Petición no válida",
        "Tipo" => "error"
    ]);
    exit();
}
