<?php
$peticionAjax = true;

require_once "../config/APP.php";
ob_start();
session_start(['name' => 'STR']);

require_once "../controladores/reportesControlador.php";

$inst_reporte = new reporteControlador();

/* =========================================
   REPORTE DE COMPRAS
========================================= */
if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_pedidos') {
    $inst_reporte->imprimir_reporte_pedidos_controlador();
    exit();
}

if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_presupuestos') {
    $inst_reporte->imprimir_reporte_presupuestos_controlador();
    exit();
}

if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_ordenes_compra') {
    $inst_reporte->imprimir_reporte_ordenes_compra_controlador();
    exit();
}

if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_compras') {
    $inst_reporte->imprimir_reporte_compras_controlador();
    exit();
}
if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_libro_compras') {
    $inst_reporte->imprimir_reporte_libro_compras_controlador();
    exit();
}


/* =========================================
    FIN REPORTE DE COMPRAS
========================================= */

/* =========================================
    REPORTE DE SERVICIOS
========================================= */

if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_recepcion_servicios') {
    $inst_reporte->imprimir_reporte_recepcion_servicio_controlador();
    exit();
}

if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_presupuesto_servicio') {
    $inst_reporte->imprimir_reporte_presupuesto_servicio_controlador();
    exit();
}
if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_orden_trabajo') {
    $inst_reporte->imprimir_reporte_orden_trabajo_controlador();
    exit();
}
if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_registro_servicio') {
    $inst_reporte->imprimir_reporte_registro_servicio_controlador();
    exit();
}
