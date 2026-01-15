<?php
$peticionAjax = true;

require_once "../config/APP.php";
ob_start();
session_start(['name' => 'STR']);

require_once "../controladores/reportesControlador.php";

$inst_reporte = new reporteControlador();

/* =========================================
   REPORTE DE ARTICULOS (PREVIEW)
========================================= */

if (isset($_POST['modulo']) && $_POST['modulo'] == "articulos") {
    echo $inst_reporte->reporte_articulos_controlador();
    exit();
}

/* =========================================
   REPORTE DE ARTICULOS (PDF)
========================================= */

if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_articulos') {
    $inst_reporte->imprimir_reporte_articulos_controlador();
    exit();
}
/* =========================================
   REPORTE DE PROVEEDORES (PREVIEW)
========================================= */
if (isset($_POST['modulo']) && $_POST['modulo'] == "proveedores") {
    echo $inst_reporte->reporte_proveedores_controlador();
    exit();
}

/* =========================================
   REPORTE DE PROVEEDORES (PDF)
========================================= */
if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_proveedores') {
    $inst_reporte->imprimir_reporte_proveedores_controlador();
    exit();
}

/* =========================================
   REPORTE DE CLIENTES (PREVIEW)
========================================= */
if (isset($_POST['modulo']) && $_POST['modulo'] == "clientes") {
    echo $inst_reporte->reporte_clientes_controlador();
    exit();
}

/* =========================================
   REPORTE DE CLIENTES (PDF)
========================================= */
if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_clientes') {
    $inst_reporte->imprimir_reporte_clientes_controlador();
    exit();
}

/* =========================================
   REPORTE DE EMPLEADOS (PREVIEW)
========================================= */
if (isset($_POST['modulo']) && $_POST['modulo'] == "empleados") {
    echo $inst_reporte->reporte_empleados_controlador();
    exit();
}

/* =========================================
   REPORTE DE EMPLEADOS (PDF)
========================================= */
if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_empleados') {
    $inst_reporte->imprimir_reporte_empleados_controlador();
    exit();
}

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
if (isset($_POST['modulo']) && $_POST['modulo'] == "movimientos_stock") {
    echo $inst_reporte->reporte_movimientos_stock_controlador();
    exit();
}

if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_movimientos_stock') {
    $inst_reporte->imprimir_reporte_movimientos_stock_controlador();
    exit();
}


/* =========================================
   REPORTE DE TRANSFERENCIAS (PREVIEW)
========================================= */
if (isset($_POST['modulo']) && $_POST['modulo'] == "transferencias") {
    echo $inst_reporte->reporte_transferencias_controlador();
    exit();
}

/* =========================================
   REPORTE DE TRANSFERENCIAS (PDF)
========================================= */
if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_transferencias') {
    $inst_reporte->imprimir_reporte_transferencias_controlador();
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
