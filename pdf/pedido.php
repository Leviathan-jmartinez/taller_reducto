<?php
$peticionAjax = false;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/SERVER.php";
require_once __DIR__ . "/../controladores/pedidoControlador.php";

$insPedido = new pedidoControlador();

/* ID encriptado */
$idEnc = $_GET['id'] ?? '';
$idPedido = $insPedido->decrypt($idEnc);

/* Datos */
$data = $insPedido->datos_pedido_controladorPDF($idPedido);

$cabecera = $data['cabecera'];
$detalle  = $data['detalle'];

if (!$cabecera) {
    exit('Pedido no encontrado');
}

ob_start();
require "plantillas/pedido.php";
$html = ob_get_clean();

/* mPDF */
$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
$mpdf->WriteHTML($html);
$mpdf->Output("PEDIDO_$idPedido.pdf", "I");
