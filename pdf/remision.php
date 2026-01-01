<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/SERVER.php';
require_once __DIR__ . '/../modelos/mainModel.php';

use Mpdf\Mpdf;

if (!isset($_GET['id'])) {
    die("Remisión no especificada");
}

$id = (int) $_GET['id'];
$pdo = mainModel::conectar();

/* ================= CABECERA ================= */
$qCab = $pdo->prepare("
    SELECT 
        nr.*,
        s.suc_descri,
        s.nro_establecimiento,
        st.timbrado,
        st.fecha_vencimiento
    FROM nota_remision nr
    INNER JOIN sucursales s ON s.id_sucursal = nr.id_sucursal
    INNER JOIN sucursal_timbrado st 
        ON st.id_sucursal = nr.id_sucursal AND st.activo = 1
    WHERE nr.idnota_remision = :id
");
$qCab->execute([':id' => $id]);
$cab = $qCab->fetch(PDO::FETCH_ASSOC);

if (!$cab) {
    die("Remisión no encontrada");
}

/* ================= DETALLE ================= */
$qDet = $pdo->prepare("
    SELECT 
        d.cantidad,
        a.desc_articulo,
        d.costo,
        d.subtotal
    FROM nota_remision_detalle d
    INNER JOIN articulos a ON a.id_articulo = d.id_articulo
    WHERE d.idnota_remision = :id
");
$qDet->execute([':id' => $id]);
$detalle = $qDet->fetchAll(PDO::FETCH_ASSOC);

/* ================= MPDF ================= */
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_top' => 10,
    'margin_bottom' => 10
]);

ob_start();
?>

<style>
    body {
        font-family: sans-serif;
        font-size: 10pt;
    }

    h3 {
        margin: 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 4px;
    }

    .no-border td {
        border: none;
    }
</style>

<h3>NOTA DE REMISIÓN</h3>

<table class="no-border">
    <tr>
        <td><b>Sucursal:</b> <?= $cab['suc_descri'] ?></td>
        <td><b>Establecimiento:</b> <?= $cab['nro_establecimiento'] ?></td>
    </tr>
    <tr>
        <td><b>Timbrado:</b> <?= $cab['timbrado'] ?></td>
        <td><b>Válido hasta:</b> <?= $cab['fecha_vencimiento'] ?></td>
    </tr>
    <tr>
        <td><b>Nº Remisión:</b> <?= $cab['nro_remision'] ?></td>
        <td><b>Fecha:</b> <?= $cab['fecha_emision'] ?></td>
    </tr>
</table>

<hr>

<b>Datos del Transporte</b>
<table>
    <tr>
        <td>Transportista</td>
        <td><?= $cab['transportista'] ?></td>
    </tr>
    <tr>
        <td>RUC</td>
        <td><?= $cab['ruc_transport'] ?></td>
    </tr>
    <tr>
        <td>Chofer</td>
        <td><?= $cab['nombre_transpo'] ?></td>
    </tr>
    <tr>
        <td>CI</td>
        <td><?= $cab['ci_transpo'] ?></td>
    </tr>
    <tr>
        <td>Chapa</td>
        <td><?= $cab['vehichapa'] ?></td>
    </tr>
    <tr>
        <td>Vehículo</td>
        <td><?= $cab['vehimarca'] ?> <?= $cab['vehimodelo'] ?></td>
    </tr>
</table>

<br>

<b>Detalle de Mercaderías</b>
<table>
    <thead>
        <tr>
            <th>Cant.</th>
            <th>Descripción</th>
            <th>Costo</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total = 0;
        foreach ($detalle as $d):
            $total += $d['subtotal'];
        ?>
            <tr>
                <td><?= number_format($d['cantidad'], 2) ?></td>
                <td><?= $d['desc_articulo'] ?></td>
                <td><?= number_format($d['costo'], 2) ?></td>
                <td><?= number_format($d['subtotal'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><b>Total</b></td>
            <td><b><?= number_format($total, 2) ?></b></td>
        </tr>
    </tbody>
</table>

<?php
$html = ob_get_clean();
$mpdf->WriteHTML($html);
$mpdf->Output("remision_$id.pdf", "I");
