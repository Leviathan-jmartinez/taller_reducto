<?php
$peticionAjax = true;
require_once "../config/SERVER.php";
require_once "../controladores/descuentoControlador.php";

$insDescuento = new descuentoControlador();

/* ================= BUSCAR CLIENTES ================= */
if (isset($_POST['buscar_cliente'])) {
    echo $insDescuento->buscar_clientes_controlador();
    exit;
}

/* ================= ACCIONES ================= */
/* ================= ACCIONES ================= */
if (isset($_POST['accion'])) {

    /* GUARDAR DESCUENTO */
    if ($_POST['accion'] === 'guardar_descuento') {
        echo $insDescuento->guardar_descuento_controlador();
        exit;
    }

    /* EDITAR DESCUENTO */
    if ($_POST['accion'] === 'editar_descuento') {
        echo $insDescuento->editar_descuento_controlador();
        exit;
    }

    /* ASIGNAR DESCUENTO A CLIENTES */
    if ($_POST['accion'] === 'asignar_descuento_cliente') {
        echo $insDescuento->asignar_descuento_cliente_controlador();
        exit;
    }
}

/* ========= DESCUENTOS POR CLIENTE ========= */
if (isset($_POST['id_cliente'])) {
    echo $insDescuento->descuentos_por_cliente_controlador();
    exit;
}

/* ========= EDITAR DESCUENTO ========= */
if (isset($_POST['id_cliente'])) {
    echo $insDescuento->editar_descuento_controlador();
    exit;
}

/* ================= FALLBACK ================= */
exit;
