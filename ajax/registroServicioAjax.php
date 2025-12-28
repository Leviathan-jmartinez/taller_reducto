<?php
$peticionAjax = true;

require_once "../modelos/mainModel.php";
require_once "../controladores/registroServicioControlador.php";

$insRS = new registroServicioControlador();

if (isset($_POST['accion']) && $_POST['accion'] === 'registrar_servicio') {
    echo $insRS->registrar_servicio_controlador();
    exit();
}

if (isset($_POST['accion']) && $_POST['accion'] === 'buscar_ot') {
    echo $insRS->buscar_ot_para_registro_controlador();
    exit();
}

if (isset($_POST['accion']) && $_POST['accion'] === 'cargar_ot') {
    echo $insRS->cargar_ot_para_registro_controlador();
    exit();
}

if (isset($_POST['accion']) && $_POST['accion'] === 'anular_registro') {
    echo $insRS->anular_registro_servicio_controlador();
    exit();
}



/* BLINDAJE */
echo json_encode([
    'Alerta' => 'simple',
    'Titulo' => 'Error',
    'Texto'  => 'Acción no reconocida',
    'Tipo'   => 'error'
]);
exit();
