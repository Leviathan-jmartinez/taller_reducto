<?php
$peticionAjax = true;
require_once "../config/APP.php";

if (isset($_POST['usuario_nombre_reg']) || isset($_POST['usuario_id_del']) || isset($_POST['usuario_id_up'])) {
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
} else {
    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}
