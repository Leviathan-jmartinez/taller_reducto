<?php
$peticionAjax = false;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/SERVER.php";
require_once __DIR__ . "/../controladores/presupuestoServicioControlador.php";

$insPres = new presupuestoServicioControlador();

$idEnc = $_GET['id'] ?? '';
$id    = $insPres->decrypt($idEnc);

$data = $insPres->datos_presupuesto_controlador($id);

$cabecera = $data['cabecera'];
$detalle  = $data['detalle'];

if (!$cabecera) {
    exit('Presupuesto no encontrado');
}

ob_start();
require "plantillas/presupuesto_servicio.php";
$html = ob_get_clean();

$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
$mpdf->WriteHTML($html);
$mpdf->Output("PRESUPUESTO_$id.pdf", "I");
