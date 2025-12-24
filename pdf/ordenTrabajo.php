<?php
$peticionAjax = false;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/SERVER.php";
require_once __DIR__ . "/../controladores/ordenTrabajoControlador.php";


$insOT = new ordenTrabajoControlador();

// ID encriptado por GET
$idEnc = $_GET['id'] ?? '';
$idOT  = $insOT->decrypt($idEnc);           

// traer todos los datos
$data = $insOT->datos_ot_controlador($idOT);

// variables para la plantilla
$cabecera = $data['cabecera'];  
$detalle  = $data['detalle'];


ob_start();
require "plantillas/orden_trabajo.php";
$html = ob_get_clean();

// mPDF
$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
$mpdf->WriteHTML($html);
$mpdf->Output("OT_$idOT.pdf", "I");
