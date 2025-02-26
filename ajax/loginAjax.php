<?php
$peticionAjax = true;
require_once "../config/APP.php";

if (isset($_POST['token']) && isset($_POST['usuario'])) {
    require_once "../controladores/loginControlador.php";
    $inst_login = new loginControlador();
    echo $inst_login->cierre_sesion_controlador();
} else {
    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}
