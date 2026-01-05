<?php
$peticionAjax = true;
require_once "../config/APP.php";

if (
    isset($_POST['placa_reg']) ||
    isset($_POST['vehiculo_id_up']) ||
    isset($_POST['vehiculo_id_del'])
) {
    require_once "../controladores/vehiculoControlador.php";
    $inst = new vehiculoControlador();

    if (isset($_POST['placa_reg'])) {
        echo $inst->agregar_vehiculo_controlador();
        exit();
    }

    if (isset($_POST['vehiculo_id_up'])) {
        echo $inst->actualizar_vehiculo_controlador();
        exit();
    }

    if (isset($_POST['vehiculo_id_del'])) {
        echo $inst->eliminar_vehiculo_controlador();
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
