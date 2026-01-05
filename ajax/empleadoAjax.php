<?php
$peticionAjax = true;
require_once "../config/APP.php";

if (
    isset($_POST['cedula_reg']) ||
    isset($_POST['empleado_id_up']) ||
    isset($_POST['empleado_id_del'])
) {
    require_once "../controladores/empleadoControlador.php";
    $inst = new empleadoControlador();

    if (isset($_POST['cedula_reg'])) {
        echo $inst->agregar_empleado_controlador();
        exit();
    }

    if (isset($_POST['empleado_id_up'])) {
        echo $inst->actualizar_empleado_controlador();
        exit();
    }

    if (isset($_POST['empleado_id_del'])) {
        echo $inst->eliminar_empleado_controlador();
        exit();
    }
} else {
    echo json_encode([
        "Alerta" => "simple",
        "Titulo" => "Petición inválida",
        "Texto" => "No se pudo procesar la solicitud",
        "Tipo" => "error"
    ]);
}
