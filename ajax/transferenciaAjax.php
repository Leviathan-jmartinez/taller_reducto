<?php
$peticionAjax = true;

require_once "../config/SERVER.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start(['name' => 'STR']);
}

require_once "../controladores/transferenciaControlador.php";

$transferencia = new transferenciaControlador();

/* ========= BUSCAR PRODUCTOS ========= */
if (isset($_POST['accion']) && $_POST['accion'] === 'buscar_producto') {
    $transferencia->buscar_producto_controlador();
}

/* ========= CREAR TRANSFERENCIA ========= */
if (isset($_POST['accion']) && $_POST['accion'] === 'crear_transferencia') {
    echo $transferencia->crear_transferencia_controlador();
}


/* ========= BUSCAR SUCURSAL DESTINO ========= */
if (isset($_POST['accion']) && $_POST['accion'] === 'buscar_sucursal_destino') {
    $transferencia->buscar_sucursal_destino_controlador();
}
