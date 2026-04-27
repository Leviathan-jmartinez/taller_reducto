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
    if ($_POST['accion'] === 'roles_por_usuario') {
        echo $inst_usuario->roles_por_usuario_controlador();
        exit();
    }

    if ($_POST['accion'] === 'guardar_roles_usuario') {
        echo $inst_usuario->guardar_roles_usuario_controlador();
        exit();
    }
    if ($_POST['accion'] === 'sucursal_por_usuario') {
        echo $inst_usuario->sucursal_por_usuario_controlador();
        exit();
    }

    if ($_POST['accion'] === 'asignar_sucursal') {
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
