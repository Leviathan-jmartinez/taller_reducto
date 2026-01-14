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
/* ELIMINAR EQUIPO */
if (isset($_POST['accion']) && $_POST['accion'] == "eliminar_equipo") {
    echo $ins_equipo->eliminar_equipo_controlador();
    exit();
}

/* QUITAR MIEMBRO */
if (isset($_POST['accion']) && $_POST['accion'] == "quitar_miembro") {
    echo $ins_equipo->quitar_miembro_controlador();
    exit();
}

/* PETICIÓN INVÁLIDA */
echo json_encode([
    "Alerta" => "simple",
    "Titulo" => "Error",
    "Texto" => "Petición no válida",
    "Tipo" => "error"
]);
