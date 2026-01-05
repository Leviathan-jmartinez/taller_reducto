<?php
$peticionAjax = false;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/SERVER.php";
require_once __DIR__ . "/../controladores/ordenCompraControlador.php";

$insOC = new ordenCompraControlador();

/* ID encriptado */
$idEnc = $_GET['id'] ?? '';
$idOC  = $insOC->decrypt($idEnc);

$data = $insOC->datos_orden_compra_controlador($idOC);

$cabecera = $data['cabecera'];
$detalle  = $data['detalle'];

if (!$cabecera) {
    exit('Orden de compra no encontrada');
}

ob_start();
require "plantillas/orden_compra.php";
$html = ob_get_clean();

$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
$mpdf->WriteHTML($html);
$mpdf->Output("ORDEN_COMPRA_$idOC.pdf", "I");
