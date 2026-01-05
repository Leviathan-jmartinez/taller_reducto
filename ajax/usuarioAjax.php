<?php
$peticionAjax = true;
require_once "../config/APP.php";

if (isset($_POST['usuario_nombre_reg']) || isset($_POST['usuario_id_del']) || isset($_POST['usuario_id_up']) || isset($_POST['accion'])) {
    /** Instancia al controlador */
    require_once "../controladores/usuarioControlador.php";
    $inst_usuario = new usuarioControlador();
    /** Agregar un usuario */
    if (isset($_POST['usuario_nombre_reg']) && isset($_POST['usuario_apellido_reg'])) {
        echo $inst_usuario->agregar_usuario_controlador();
    }
    /** Eliminar usuario */
    if (isset($_POST['usuario_id_del'])) {
        echo $inst_usuario->eliminar_usuario_controlador();
    }
    /** Actualizar usuario */
    if (isset($_POST['usuario_id_up'])) {
        echo $inst_usuario->actualizar_usuario_controlador();
    }
    if (isset($_POST['accion']) && $_POST['accion'] === 'asignar_rol') {
        echo $inst_usuario->asignar_rol_controlador();
        exit();
    }

    if (isset($_POST['accion']) && $_POST['accion'] === 'guardar_permisos_rol') {
        echo $inst_usuario->guardar_permisos_rol_controlador();
        exit();
    }

    if (isset($_POST['accion']) && $_POST['accion'] === 'permisos_por_rol') {
        echo $inst_usuario->permisos_por_rol_controlador();
        exit();
    }

    /* ================= ASIGNAR SUCURSAL ================= */
    if (isset($_POST['accion']) && $_POST['accion'] === 'asignar_sucursal') {
        echo $inst_usuario->asignar_sucursal_controlador();
        exit();
    }
    } else {
    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}
