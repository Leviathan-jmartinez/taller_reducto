<?php
$peticionAjax = true;
require_once "../config/APP.php";

require_once "../controladores/rolesControlador.php";
$ins = new rolesControlador();

/* ================= ACCIONES (PERMISOS) ================= */
if (isset($_POST['accion'])) {

    switch ($_POST['accion']) {

        case 'guardar_permisos_rol':
            echo $ins->guardar_permisos_rol_controlador();
            exit();

        case 'permisos_por_rol':
            echo $ins->permisos_por_rol_controlador();
            exit();
    }
}

/* ================= CRUD NORMAL ================= */
if (isset($_POST['rol_nombre_reg'])) {
    echo $ins->agregar_roles_controlador();
    exit();
}

if (isset($_POST['rol_id_up'])) {
    echo $ins->actualizar_roles_controlador();
    exit();
}

if (isset($_POST['rol_id_del'])) {
    echo $ins->eliminar_roles_controlador();
    exit();
}

/* ================= ERROR ================= */
echo json_encode([
    "Alerta" => "simple",
    "Titulo" => "Petición inválida",
    "Texto" => "No se pudo procesar la solicitud",
    "Tipo" => "error"
]);
