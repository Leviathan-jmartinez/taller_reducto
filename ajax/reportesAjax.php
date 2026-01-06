<?php
$peticionAjax = true;

require_once "../config/APP.php";
ob_start();
session_start(['name' => 'STR']);

require_once "../controladores/reportesControlador.php";

$inst_reporte = new reporteControlador();

/* =========================================
   IMPRIMIR REPORTE DE PEDIDOS
========================================= */
if (isset($_POST['accion']) && $_POST['accion'] === 'imprimir_reporte_pedidos') {
    $inst_reporte->imprimir_reporte_pedidos_controlador();
    exit();
}
