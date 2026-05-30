<?php

$peticionAjax = true;
require_once "../config/APP.php";
require_once "../modelos/mainModel.php";
require_once "../controladores/salidaInsumoControlador.php";

$insSalida = new salidaInsumoControlador();

/* ================= REGISTRAR ================= */
if (
    isset($_POST['accion']) &&
    $_POST['accion'] === 'registrar_salida_consumible'
) {
    echo $insSalida->registrar_salida_consumible_controlador();
    exit();
}

/* ================= BUSCAR CONSUMIBLE ================= */
if (
    isset($_POST['accion']) &&
    $_POST['accion'] === 'buscar_consumible'
) {
    echo $insSalida->buscar_consumible_controlador();
    exit();
}

/* ================= ANULAR ================= */
if (
    isset($_POST['accion']) &&
    $_POST['accion'] === 'anular'
) {
    echo $insSalida->anular_salida_consumible_controlador();
    exit();
}

if (
    isset($_POST['accion']) &&
    $_POST['accion'] === 'buscar_empleado'
) {
    echo $insSalida->buscar_empleado_controlador();
    exit();
}

/* ================= BLINDAJE ================= */
echo json_encode([
    'Alerta' => 'simple',
    'Titulo' => 'Error',
    'Texto'  => 'Acción no reconocida',
    'Tipo'   => 'error'
]);

exit();
