
<?php
$peticionAjax = true;
require_once "../config/APP.php";

/** Instancia al controlador */
require_once "../controladores/recepcionservicioControlador.php";
$inst_recep = new recepcionservicioControlador();

if (isset($_POST['buscar_cliente'])) {
    echo $inst_recep->buscar_cliente_controlador();
}
if (isset($_POST['buscar_vehiculo'], $_POST['id_cliente'])) {
    echo $inst_recep->buscar_vehiculo_controlador();
}
if (isset($_POST['accion']) && $_POST['accion'] === 'guardar_recepcion') {
    echo $inst_recep->guardar_recepcion_controlador();
}
if (isset($_POST['recepcion_id_del'])) {
    echo $inst_recep->anular_recepcion_controlador();
}