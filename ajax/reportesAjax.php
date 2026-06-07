<?php
$peticionAjax = true;

require_once "../config/APP.php";
ob_start();
session_start(['name' => 'STR']);

/* ===== VARIABLES SEGURAS ===== */
$accion = $_POST['accion'] ?? null;
$modulo = $_POST['modulo'] ?? null;

require_once "../controladores/reportesControlador.php";

$inst_reporte = new reporteControlador();


/* =========================================
   PREVIEWS (MODULO)
========================================= */

if ($modulo === "referenciales") {
    echo $inst_reporte->reporte_referenciales_controlador();
    exit();
}

if ($modulo === "movimientos_unificado") {
    echo $inst_reporte->reporte_movimientos_unificado_controlador();
    exit();
}

/* =========================================
   ACCIONES (PDF Y OTROS)
========================================= */

switch ($accion) {

    case 'imprimir_reporte_referenciales':
        $inst_reporte->imprimir_reporte_referenciales_controlador();
        exit();

    case 'exportar_reporte_referenciales_csv':
        $inst_reporte->exportar_reporte_referenciales_csv_controlador();
        exit();

    case 'imprimir_reporte_movimientos_unificado':
        $inst_reporte->imprimir_reporte_movimientos_unificado_controlador();
        exit();

    case 'exportar_reporte_movimientos_csv':
        $inst_reporte->exportar_reporte_movimientos_csv_controlador();
        exit();
}

/* =========================================
   FALLBACK (OPCIONAL)
========================================= */
// echo json_encode(["error" => "Acción no válida"]);
