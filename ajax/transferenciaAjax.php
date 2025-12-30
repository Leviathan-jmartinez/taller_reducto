<?php
$peticionAjax = true;

require_once "../config/SERVER.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../controladores/transferenciaControlador.php";

$transferencia = new transferenciaControlador();

if (isset($_POST['accion']) && $_POST['accion'] === 'crear_transferencia') {
    $transferencia->crear_transferencia_controlador();
}
