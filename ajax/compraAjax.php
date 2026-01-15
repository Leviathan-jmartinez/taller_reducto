<?php
$peticionAjax = true;
require_once "../config/APP.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start(['name' => 'STR']);
}

require_once "../controladores/compraControlador.php";
$inst_compra = new compraControlador();

/* ===============================
   BUSCAR ORDEN DE COMPRA
================================ */
if (isset($_POST['buscar_oc'])) {
    echo $inst_compra->buscar_oc_controlador();
    exit();
}

/* ===============================
   CARGAR ORDEN SELECCIONADA
================================ */
if (isset($_POST['id_oc_seleccionado'])) {
    echo $inst_compra->cargar_oc_controlador();
    exit();
}

/* ===============================
   LIMPIAR SESIÓN
================================ */
if (isset($_POST['limpiar_presupuesto'])) {
    unset($_SESSION['tipo_presupuesto']);
    unset($_SESSION['Sdatos_proveedorPre']);
    unset($_SESSION['Cdatos_proveedorPre']);
    unset($_SESSION['Sdatos_articuloPre']);
    unset($_SESSION['Cdatos_articuloPre']);
    unset($_SESSION['presupuesto_articulo']);
    unset($_SESSION['total_pre']);
    header("Location: " . SERVERURL . "presupuesto-nuevo/");
    exit();
}
/* ===============================
   Buscar PROVEEDOR
================================ */
if (isset($_POST['buscar_proveedorCO'])) {
    echo $inst_compra->buscar_proveedor_controlador();
    exit();
}

/* ===============================
   AGREGAR PROVEEDOR
================================ */
if (isset($_POST['id_agregar_proveedorCO'])) {
    echo $inst_compra->agregar_proveedor_controlador();
    exit();
}
/* ===============================
   Buscar ARTÍCULO
================================ */
if (isset($_POST['buscar_articuloCO'])) {
    echo $inst_compra->buscar_articulo_controlador();
    exit();
}

/* ===============================
   AGREGAR ARTÍCULO
================================ */
if (isset($_POST['id_agregar_articuloCO'])) {
    echo $inst_compra->articulo_controlador();
    exit();
}

/* ===============================
   Anular COMPRA
================================ */
if (isset($_POST['compra_id_del'])) {
    echo $inst_compra->anular_compra_controlador();
    exit();
}

if (isset($_POST['cancelar'])) {
    // Limpiar solo las variables de la compra
    unset($_SESSION['Cdatos_articuloCO']);
    unset($_SESSION['datos_proveedorCO']);
    unset($_SESSION['id_oc_seleccionado']);
    if($_SESSION['factura_tipo'] == "sin_oc"){
        $_SESSION['factura_tipo'] = "con_oc";
    }
    // Retornar respuesta JSON
    echo json_encode([
        "Alerta" => "recargar",
        "Titulo" => "Operación cancelada",
        "Texto" => "Se han limpiado las variables de sesión.",
        "Tipo"   => "success"
    ]);
    exit(); // importantísimo: salir antes de procesar cualquier otro código
}

/* ===============================
   ACTUALIZAR DETALLES EN SESIÓN
================================ */
if (isset($_POST['index'])) {
    $i = intval($_POST['index']);

    if (isset($_SESSION['Cdatos_articuloCO'][$i])) {

        $cantidad = floatval($_POST['cantidad']);
        $precio   = floatval($_POST['precio']);


        // ✔️ ACTUALIZAR SESIÓN SOLO SI LOS DATOS SON VÁLIDOS
        $_SESSION['Cdatos_articuloCO'][$i]['cantidad'] = $cantidad;
        $_SESSION['Cdatos_articuloCO'][$i]['precio']   = $precio;
        $_SESSION['Cdatos_articuloCO'][$i]['subtotal'] = floatval($_POST['subtotal']);
        $_SESSION['Cdatos_articuloCO'][$i]['iva']      = floatval($_POST['iva']);

        echo json_encode(["status" => "ok"]);
        exit();
    }

    echo json_encode(["status" => "error", "msg" => "Índice no existe"]);
    exit();
}


/* ===============================
   GUARDAR COMPRA
================================ */
if (isset($_POST['accion']) && $_POST['accion'] == "guardar_compra") {
    echo json_encode($inst_compra->guardar_compra_controlador());
    exit();
}

/* ===============================
   ACCIÓN NO RECONOCIDA
================================ */
echo json_encode(["status" => "error", "msg" => "Acción no definida"]);
exit();
