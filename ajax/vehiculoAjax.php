<?php
$peticionAjax = true;
require_once "../config/APP.php";

if (
    isset($_POST['placa_reg']) ||
    isset($_POST['vehiculo_id_up']) ||
    isset($_POST['vehiculo_id_del']) ||
    (isset($_POST['accion']) && $_POST['accion'] == "buscar_cliente")
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
    if (isset($_POST['accion']) && $_POST['accion'] == "buscar_cliente") {
        echo $inst->buscar_cliente_controlador();
    }
} else {
    echo json_encode([
        "Alerta" => "simple",
        "Titulo" => "Petición inválida",
        "Texto" => "No se pudo procesar la solicitud",
        "Tipo" => "error"
    ]);
}
