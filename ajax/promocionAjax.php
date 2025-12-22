<?php
$peticionAjax = true;
require_once "../config/SERVER.php";
require_once "../controladores/promocionControlador.php";

$promo = new promocionControlador();

/* ===== BUSCAR ARTÍCULOS ===== */
if (isset($_POST['buscar_articulo'])) {
    echo $promo->buscar_articulos_controlador();
    exit;
}

/* ===== ACCIONES ===== */
if (isset($_POST['accion'])) {

    if ($_POST['accion'] === 'guardar_promocion') {
        echo $promo->guardar_promocion_controlador();
        exit;
    }

    if ($_POST['accion'] === 'editar_promocion') {
        echo $promo->editar_promocion_controlador();
        exit;
    }

    if ($_POST['accion'] === 'cambiar_estado') {
        echo $promo->cambiar_estado_promocion_controlador();
        exit;
    }
}

/* ===== NO HACER NADA ===== */
exit;
