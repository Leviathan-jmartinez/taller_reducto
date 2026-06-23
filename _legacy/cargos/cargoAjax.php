<?php
$peticionAjax = true;
require_once "../config/APP.php";

if (
    isset($_POST['descripcion_reg']) ||
    isset($_POST['cargo_id_del']) ||
    isset($_POST['cargo_id_up'])
) {

    require_once "../controladores/cargosControlador.php";
    $inst = new cargosControlador();

    // ✅ REGISTRAR
    if (isset($_POST['descripcion_reg'])) {
        echo $inst->agregar_cargo_controlador();
        exit();
    }

    // ✅ ELIMINAR
    if (isset($_POST['cargo_id_del'])) {
        echo $inst->eliminar_cargo_controlador();
        exit();
    }

    // ✅ ACTUALIZAR
    if (isset($_POST['cargo_id_up'])) {
        echo $inst->actualizar_cargo_controlador();
        exit();
    }
} else {

    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();

    echo json_encode([
        "Alerta" => "simple",
        "Titulo" => "Error",
        "Texto" => "Petición no válida",
        "Tipo" => "error"
    ]);
    exit();
}
