<?php
$peticionAjax = true;
require_once "../config/APP.php";
ob_start();
session_start(['name' => 'STR']);

require_once "../controladores/inventarioControlador.php";
$inst_inventario = new inventarioControlador();

// Categorías
if (isset($_POST['cargar_categorias'])) {
    echo $inst_inventario->cargar_categorias_controlador();
    exit();
}

// Proveedores
if (isset($_POST['cargar_proveedores'])) {
    echo $inst_inventario->cargar_proveedores_controlador();
    exit();
}

// Productos para Select2
if (isset($_POST['buscar_producto'])) {
    $inst_inventario->cargarArticulosControlador($_POST['buscar_producto']);
    exit();
}

// Guardar inventario
if (isset($_POST['tipo_inventario'])) {
    $resp = $inst_inventario->guardarInventarioControlador();
    echo json_encode($resp);
    exit();
}
