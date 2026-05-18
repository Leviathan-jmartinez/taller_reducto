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

if ($modulo === "articulos") {
    echo $inst_reporte->reporte_articulos_controlador();
    exit();
}

if ($modulo === "stock") {
    echo $inst_reporte->reporte_stock_controlador();
    exit();
}

if ($modulo === "clientes") {
    echo $inst_reporte->reporte_clientes_controlador();
    exit();
}

if ($modulo === "sucursales") {
    echo $inst_reporte->reporte_sucursales_controlador();
    exit();
}

if ($modulo === "vehiculos") {
    echo $inst_reporte->reporte_vehiculos_controlador();
    exit();
}

if ($modulo === "empleados") {
    echo $inst_reporte->reporte_empleados_controlador();
    exit();
}

if ($modulo === "movimientos_stock") {
    echo $inst_reporte->reporte_movimientos_stock_controlador();
    exit();
}

if ($modulo === "transferencias") {
    echo $inst_reporte->reporte_transferencias_controlador();
    exit();
}

/* =========================================
   ACCIONES (PDF Y OTROS)
========================================= */

switch ($accion) {

    /* ===== ARTICULOS ===== */
    case 'reporte_articulos_simple':
        echo $inst_reporte->reporte_articulos_simple_controlador();
        break;

    case 'imprimir_reporte_articulos_simple':
        $inst_reporte->imprimir_reporte_articulos_simple_controlador();
        exit();

    case 'imprimir_reporte_articulos':
        $inst_reporte->imprimir_reporte_articulos_controlador();
        exit();

    case 'imprimir_reporte_stock':
        $inst_reporte->imprimir_reporte_stock_controlador();
        exit();


    /* ===== PROVEEDORES ===== */
    case 'reporte_proveedores':
        echo $inst_reporte->reporte_proveedores_controlador();
        break;

    case 'imprimir_reporte_proveedores':
        $inst_reporte->imprimir_reporte_proveedores_controlador();
        exit();


    /* ===== CLIENTES ===== */
    case 'imprimir_reporte_clientes':
        $inst_reporte->imprimir_reporte_clientes_controlador();
        exit();

    /* ===== SUCURSALES ===== */
    case 'imprimir_reporte_sucursales':
        $inst_reporte->imprimir_reporte_sucursales_controlador();
        exit();

    /* ===== VEHICULOS ===== */
    case 'imprimir_reporte_vehiculos':
        $inst_reporte->imprimir_reporte_vehiculos_controlador();
        exit();


    /* ===== EMPLEADOS ===== */
    case 'imprimir_reporte_empleados':
        $inst_reporte->imprimir_reporte_empleados_controlador();
        exit();


    /* ===== COMPRAS ===== */
    case 'imprimir_reporte_pedidos':
        $inst_reporte->imprimir_reporte_pedidos_controlador();
        exit();

    case 'imprimir_reporte_presupuestos':
        $inst_reporte->imprimir_reporte_presupuestos_controlador();
        exit();

    case 'imprimir_reporte_ordenes_compra':
        $inst_reporte->imprimir_reporte_ordenes_compra_controlador();
        exit();

    case 'imprimir_reporte_compras':
        $inst_reporte->imprimir_reporte_compras_controlador();
        exit();

    case 'imprimir_reporte_libro_compras':
        $inst_reporte->imprimir_reporte_libro_compras_controlador();
        exit();


    /* ===== MOVIMIENTOS STOCK ===== */
    case 'imprimir_reporte_movimientos_stock':
        $inst_reporte->imprimir_reporte_movimientos_stock_controlador();
        exit();


    /* ===== TRANSFERENCIAS ===== */
    case 'imprimir_reporte_transferencias':
        $inst_reporte->imprimir_reporte_transferencias_controlador();
        exit();


    /* ===== SERVICIOS ===== */
    case 'imprimir_reporte_recepcion_servicios':
        $inst_reporte->imprimir_reporte_recepcion_servicio_controlador();
        exit();

    case 'imprimir_reporte_presupuesto_servicio':
        $inst_reporte->imprimir_reporte_presupuesto_servicio_controlador();
        exit();

    case 'imprimir_reporte_orden_trabajo':
        $inst_reporte->imprimir_reporte_orden_trabajo_controlador();
        exit();

    case 'imprimir_reporte_registro_servicio':
        $inst_reporte->imprimir_reporte_registro_servicio_controlador();
        exit();
}

/* =========================================
   FALLBACK (OPCIONAL)
========================================= */
// echo json_encode(["error" => "Acción no válida"]);
