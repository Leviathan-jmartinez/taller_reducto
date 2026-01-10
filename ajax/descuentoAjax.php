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
if (!isset($_POST['accion'])) {
    echo json_encode([
        "Alerta" => "simple",
        "Titulo" => "Error",
        "Texto"  => "Acción no definida",
        "Tipo"   => "error"
    ]);
    exit;
}

switch ($_POST['accion']) {

    case 'guardar_descuento':
        echo $insDescuento->guardar_descuento_controlador();
        exit;

    case 'editar_descuento':
        echo $insDescuento->editar_descuento_controlador();
        exit;

    case 'asignar_descuento_cliente':
        echo $insDescuento->asignar_descuento_cliente_controlador();
        exit;

    case 'eliminar_cliente_descuento':
        echo $insDescuento->eliminar_cliente_descuento_controlador();
        exit;

    case 'descuentos_por_cliente':
        echo $insDescuento->descuentos_por_cliente_controlador();
        exit;

    default:
        echo json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto"  => "Acción inválida",
            "Tipo"   => "error"
        ]);
        exit;
}
