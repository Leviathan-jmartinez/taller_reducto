<?php
$peticionAjax = true;
require_once "../config/SERVER.php";
require_once "../controladores/presupuestoservicioControlador.php";

$insPresupuesto = new presupuestoservicioControlador();

if (isset($_POST['buscar_recepcion'])) {
    echo $insPresupuesto->buscar_recepciones_controlador();
    exit;
}

if (isset($_POST['buscar_servicio'])) {
    echo $insPresupuesto->buscar_servicios_controlador();
    exit;
}
if (isset($_POST['promo_articulo'])) {
    echo $insPresupuesto->promo_articulo_controlador();
    exit;
}
if (isset($_POST['descuentos_cliente'])) {
    echo $insPresupuesto->descuentos_cliente_controlador();
    exit;
}

if (isset($_POST['guardar_presupuesto'])) {
    echo $insPresupuesto->guardar_presupuesto_controlador();
    exit;
}

if (isset($_POST['accion']) && $_POST['accion'] === 'aprobar') {
    echo $insPresupuesto->aprobar_presupuesto_controlador();
}
if (isset($_POST['accion']) && $_POST['accion'] === 'anular') {
    echo $insPresupuesto->anular_presupuesto_controlador();
}
