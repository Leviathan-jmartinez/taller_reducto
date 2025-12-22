<?php
$peticionAjax = true;
require_once "../config/SERVER.php";
require_once "../controladores/presupuestoservicioControlador.php";

$insPresupuesto = new presupuestoservicioControlador();

if (isset($_POST['buscar_recepcion'])) {
    echo $insPresupuesto->buscar_recepciones_controlador();
    exit;
}