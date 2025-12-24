<?php
$peticionAjax = true;
require_once "../controladores/ordenTrabajoControlador.php";

$insOT = new ordenTrabajoControlador();

if (isset($_POST['accion']) && $_POST['accion'] === 'generar_ot') {
    echo $insOT->generar_ot_controlador();
}
    