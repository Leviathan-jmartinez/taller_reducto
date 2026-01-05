<?php
$peticionAjax = true;
require_once "../config/APP.php";
require_once "../controladores/equipoControlador.php";

$ins_equipo = new equipoControlador();

/* CREAR EQUIPO */
if (isset($_POST['accion']) && $_POST['accion'] == "crear_equipo") {
    echo $ins_equipo->crear_equipo_controlador();
    exit();
}

/* ASIGNAR EMPLEADOS */
if (isset($_POST['id_equipo']) && isset($_POST['empleados'])) {
    echo $ins_equipo->asignar_empleados_controlador();
    exit();
}

/* PETICIÓN INVÁLIDA */
echo json_encode([
    "Alerta" => "simple",
    "Titulo" => "Error",
    "Texto" => "Petición no válida",
    "Tipo" => "error"
]);
