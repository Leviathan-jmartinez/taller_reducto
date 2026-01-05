<?php
$peticionAjax = true;
require_once "../modelos/mainModel.php";
require_once "../controladores/ordenTrabajoControlador.php";

$insOT = new ordenTrabajoControlador();

/* ================= GENERAR OT ================= */
if (isset($_POST['accion']) && $_POST['accion'] === 'generar_ot') {
    echo $insOT->generar_ot_controlador();
    exit();
}

if (isset($_POST['accion']) && $_POST['accion'] === 'listar_equipos') {
    echo json_encode($insOT->listar_equipos_controlador());
    exit();
}

if (isset($_POST['accion']) && $_POST['accion'] === 'asignar_equipo') {
    echo $insOT->asignar_equipo_controlador();
    exit();
}



/* ================= BUSCAR PRESUPUESTO ================= */
if (isset($_POST['buscar_presupuesto'])) {
    echo $insOT->buscar_presupuesto_aprobado_controlador();
    exit();
}

if (isset($_POST['accion']) && $_POST['accion'] === 'detalle_presupuesto') {
    echo $insOT->obtener_detalle_presupuesto_controlador();
    exit();
}

if (isset($_POST['generar_ot2'])) {
    echo $insOT->generar_ot_controlador2();
    exit();
}

/* ===== ANULAR OT ===== */
if (isset($_POST['accion']) && $_POST['accion'] === 'anular') {
    echo $insOT->anular_ot_controlador();
    exit();
}

echo json_encode([
    'Alerta' => 'simple',
    'Titulo' => 'Error',
    'Texto'  => 'Acción no reconocida',
    'Tipo'   => 'error'
]);
exit();


file_put_contents(
    __DIR__ . '/debug_ot.txt',
    print_r($_POST, true),
    FILE_APPEND
);
