
<?php
$peticionAjax = true;
require_once "../config/APP.php";
if (
    isset($_POST['idpresupuesto'])
) {
    /** Instancia al controlador */
    require_once "../controladores/ordencompraControlador.php";
    $inst_ocompra = new ordencompraControlador();
    
    if (isset($_POST['idpresupuesto'])) {
        echo $inst_ocompra->generar_oc_controlador();
    }
   
} else {
    session_start(['name' => 'STR']);
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}
