
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
if (isset($_POST['accion']) && $_POST['accion'] === 'buscar_cliente_autocomplete') {
    echo $inst_recep->buscar_cliente_autocomplete_controlador();
}
if (isset($_POST['accion']) && $_POST['accion'] === 'buscar_vehiculo_autocomplete') {
    echo $inst_recep->buscar_vehiculo_autocomplete_controlador();
}
if (isset($_POST['accion']) && $_POST['accion'] === 'buscar_ciudad_autocomplete') {
    echo $inst_recep->buscar_ciudad_autocomplete_controlador();
}
if (isset($_POST['accion']) && $_POST['accion'] === 'buscar_modelo_autocomplete') {
    echo $inst_recep->buscar_modelo_autocomplete_controlador();
}
if (isset($_POST['accion']) && $_POST['accion'] === 'guardar_cliente_rapido') {
    echo $inst_recep->guardar_cliente_rapido_controlador();
}
if (isset($_POST['accion']) && $_POST['accion'] === 'guardar_vehiculo_rapido') {
    echo $inst_recep->guardar_vehiculo_rapido_controlador();
}
if (isset($_POST['recepcion_id_del'])) {
    echo $inst_recep->anular_recepcion_controlador();
}
if (isset($_POST['accion']) && $_POST['accion'] === 'fotos_recepcion') {
    echo $inst_recep->fotos_recepcion_controlador();
}
