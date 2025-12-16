<?php
$peticionAjax = true;
require_once "../config/APP.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start(['name' => 'STR']);
}

require_once "../controladores/remisionControlador.php";
$inst_remision = new remisionControlador();

/* ===============================
   BUSCAR FACTURA
================================ */
if (isset($_POST['buscar_factura'])) {
    echo $inst_remision->buscar_factura_controlador();
    exit();
}
/* ===============================
    CARGAR FACTURA SELECCIONADA
================================ */

if (isset($_POST['idfacturaseleccionado'])) {
    echo $inst_remision->cargar_factura_controlador();
    exit();
}

/* ===============================
   GUARDAR REMISIÓN
================================ */
if (isset($_POST['nro_remision'])) {
    echo $inst_remision->guardar_remision_controlador();
    exit();
}