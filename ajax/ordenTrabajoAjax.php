<?php
$peticionAjax = true;
require_once "../controladores/ordenTrabajoControlador.php";

$insOT = new ordenTrabajoControlador();

if (isset($_POST['accion']) && $_POST['accion'] === 'generar_ot') {
    echo $insOT->generar_ot_controlador();
}
;

if ($_POST['accion'] === 'listar_tecnicos') {
    echo json_encode($insOT->listar_tecnicos_controlador());
}

if ($_POST['accion'] === 'asignar_tecnico') {
    echo $insOT->asignar_tecnico_controlador();
}
