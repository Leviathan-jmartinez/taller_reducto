<?php
require_once "../config/APP.php";
require_once "../modelos/mainModel.php";
require_once "../modelos/remisionModelo.php";
require_once "../vendor/autoload.php";

use Mpdf\Mpdf;

// 🔹 Obtener ID encriptado
$encrypted_id = $_GET['id'] ?? '';

if (empty($encrypted_id)) {
    die("ID no proporcionado");
}

// 🔹 Desencriptar
$mainModel = new mainModel();



// 🔹 Validar que sea numérico
if (!is_numeric($id) || $id <= 0) {
    die("ID inválido tras desencriptar");
}

// 🔹 Consultar la remisión
$remision = remisionModelo::obtener_remision_modelo($id);
$detalle  = remisionModelo::obtener_remision_detalle_modelo($id);

if (!$remision) {
    die("Remisión no encontrada");
}

// 🔹 Configuración MPDF
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_top' => 15,
    'margin_bottom' => 15,
    'margin_left' => 10,
    'margin_right' => 10
]);

// 🔹 HTML PDF
$html = '
<style>
body { font-family: Arial, sans-serif; font-size: 11px; }
h2 { text-align: center; }
table { width:100%; border-collapse: collapse; margin-top:5px; }
th, td { border:1px solid #000; padding:5px; }
th { background:#eee; }
.text-right { text-align:right; }
.text-center { text-align:center; }
</style>

<h2>NOTA DE REMISIÓN</h2>

<table>
<tr>
<td><strong>N° Remisión:</strong> ' . $remision['nro_remision'] . '</td>
<td><strong>Fecha Emisión:</strong> ' . date("d/m/Y", strtotime($remision['fecha_emision'])) . '</td>
</tr>
<tr>
<td colspan="2"><strong>Motivo:</strong> ' . $remision['motivo_remision'] . '</td>
</tr>
</table>

<br>

<table>
<tr>
<td><strong>Transportista:</strong> ' . $remision['nombre_transpo'] . '</td>
<td><strong>CI:</strong> ' . $remision['ci_transpo'] . '</td>
</tr>
<tr>
<td><strong>Vehículo:</strong> ' . $remision['vehimarca'] . ' ' . $remision['vehimodelo'] . '</td>
<td><strong>Chapa:</strong> ' . $remision['vehichapa'] . '</td>
</tr>
</table>

<br>

<table>
<thead>
<tr>
<th>#</th>
<th>Artículo</th>
<th>Cantidad</th>
</tr>
</thead>
<tbody>';

$cont = 1;
foreach ($detalle as $d) {
    $html .= '
    <tr>
        <td class="text-center">' . $cont . '</td>
        <td>' . $d['nombre_articulo'] . '</td>
        <td class="text-center">' . $d['cantidad'] . '</td>
    </tr>';
    $cont++;
}

$html .= '
</tbody>
</table>

<br><br>
<table>
<tr>
<td style="text-align:center">______________________<br>Entregado por</td>
<td style="text-align:center">______________________<br>Recibido por</td>
</tr>
</table>
';

// 🔹 Generar PDF
$mpdf->WriteHTML($html);
$mpdf->Output("remision_" . $remision['nro_remision'] . ".pdf", "I");
