<?php
$peticionAjax = true;
require_once "../config/SERVER.php";
require_once "../controladores/promocionControlador.php";

$promo = new promocionControlador();

/* GUARDAR PROMOCIÓN */
if (isset($_POST['accion']) && $_POST['accion'] === 'guardar_promocion') {
    echo $promo->guardar_promocion_controlador();
}

/* BUSCAR ARTÍCULOS (SELECT2 / MODAL) */
if (isset($_POST['buscar_articulo'])) {
    echo $promo->buscar_articulos_controlador();
}
