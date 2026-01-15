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
        nr.idnota_remision,
        nr.nro_remision,
        nr.fecha_emision,

        -- Datos del traslado
        nr.fechaenvio,
        nr.fechallegada,
        nr.motivo_remision,
        nr.tipo,

        -- Transportista / Conductor
        nr.nombre_transpo,
        nr.ci_transpo,
        nr.cel_transpo,
        nr.transportista,
        nr.ruc_transport,

        -- Vehículo
        nr.vehimarca,
        nr.vehimodelo,
        nr.vehichapa,

        -- Empresa / Sucursal
        s.suc_descri,
        s.nro_establecimiento,

        e.razon_social,
        e.direccion,
        e.telefono_empresa,
        e.ruc,

        -- Timbrado
        st.fecha_inicio,
        st.timbrado,
        st.fecha_vencimiento

    FROM nota_remision nr
    INNER JOIN sucursales s 
        ON s.id_sucursal = nr.id_sucursal
    INNER JOIN empresa e 
        ON e.id_empresa = s.id_empresa
    LEFT JOIN sucursal_timbrado st 
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

<table width="100%" style="border:2px solid #000; padding:6px; margin-bottom:10px;">
    <tr>
        <!-- Caja izquierda -->
        <td width="65%" style="padding:8px;">
            <table width="100%" style="border:2px solid #000; border-radius:8px;">
                <tr>
                    <td style="text-align:center; padding:10px;">
                        <div style="font-size:20px; font-weight:bold;"><?= $cab['razon_social'] ?></div>
                        <div style="font-size:12px; margin-top:4px;">
                            <?= $cab['direccion'] ?><br>
                            Taller de mantenimiento y Venta de Repuestos<br>
                            Teléfono <?= $cab['telefono_empresa'] ?>
                        </div>
                    </td>
                </tr>
            </table>
        </td>

        <!-- Caja derecha -->
        <td width="35%" style="padding:8px;">
            <table width="100%" style="border:2px solid #000; border-radius:8px;">
                <tr>
                    <td style="text-align:center; padding:10px; font-size:11px;">
                        <div><strong>TIMBRADO Nº</strong> <?= $cab['timbrado'] ?></div>
                        <div>Fecha Inicio Vigencia: <?= $cab['fecha_inicio'] ?></div>
                        <div>Fecha Fin Vigencia: <?= $cab['fecha_vencimiento'] ?></div>
                        <div style="margin-top:6px;"><strong>RUC:</strong> <?= $cab['ruc'] ?></div>

                        <div style="margin-top:8px; font-size:14px; font-weight:bold;">
                            NOTA DE REMISIÓN
                        </div>
                        <div style="font-size:13px; font-weight:bold;">
                            <?= $cab['nro_remision'] ?>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>



<hr>

<!-- 2. DATOS DEL TRASLADO -->
<table width="100%" style="border:1px solid #000; border-collapse:collapse; margin-bottom:6px;">
    <tr>
        <td colspan="4" style="padding:4px; font-weight:bold;">
            2. DATOS DEL TRASLADO
        </td>
    </tr>
    <tr>
        <td style="width:25%; padding:4px;">Motivo del traslado</td>
        <td style="width:25%; padding:4px;"><?= $cab['motivo_remision'] ?? '' ?></td>
        <td style="width:25%; padding:4px;">Tipo</td>
        <td style="width:25%; padding:4px;"><?= $cab['tipo'] ?? '' ?></td>
    </tr>
    <tr>
        <td style="padding:4px;">Fecha de inicio</td>
        <td style="padding:4px;"><?= $cab['fechaenvio'] ?? '' ?></td>
        <td style="padding:4px;">Fecha estimada de llegada</td>
        <td style="padding:4px;"><?= $cab['fechallegada'] ?? '' ?></td>
    </tr>
</table>

<!-- 3. DATOS DEL TRANSPORTISTA -->
<table width="100%" style="border:1px solid #000; border-collapse:collapse; margin-bottom:6px;">
    <tr>
        <td colspan="2" style="padding:4px; font-weight:bold;">
            3. DATOS DEL TRANSPORTISTA
        </td>
    </tr>
    <tr>
        <td style="width:30%; padding:4px;">Razón Social</td>
        <td style="padding:4px;"><?= $cab['transportista'] ?? '' ?></td>
    </tr>
    <tr>
        <td style="padding:4px;">RUC</td>
        <td style="padding:4px;"><?= $cab['ruc_transport'] ?? '' ?></td>
    </tr>
</table>

<!-- 4. DATOS DEL VEHÍCULO -->
<table width="100%" style="border:1px solid #000; border-collapse:collapse; margin-bottom:6px;">
    <tr>
        <td colspan="2" style="padding:4px; font-weight:bold;">
            4. DATOS DEL VEHÍCULO DE TRANSPORTE
        </td>
    </tr>
    <tr>
        <td style="width:30%; padding:4px;">Marca / Modelo</td>
        <td style="padding:4px;">
            <?= ($cab['vehimarca'] ?? '') . ' ' . ($cab['vehimodelo'] ?? '') ?>
        </td>
    </tr>
    <tr>
        <td style="padding:4px;">Chapa</td>
        <td style="padding:4px;"><?= $cab['vehichapa'] ?? '' ?></td>
    </tr>
</table>

<!-- 5. DATOS DEL CONDUCTOR -->
<table width="100%" style="border:1px solid #000; border-collapse:collapse; margin-bottom:6px;">
    <tr>
        <td colspan="2" style="padding:4px; font-weight:bold;">
            5. DATOS DEL CONDUCTOR DEL VEHÍCULO
        </td>
    </tr>
    <tr>
        <td style="width:30%; padding:4px;">Nombre y Apellido</td>
        <td style="padding:4px;"><?= $cab['nombre_transpo'] ?? '' ?></td>
    </tr>
    <tr>
        <td style="padding:4px;">C.I.</td>
        <td style="padding:4px;"><?= $cab['ci_transpo'] ?? '' ?></td>
    </tr>
</table>

<!-- 6. DATOS DE LA MERCADERÍA -->
<table width="100%" style="border:1px solid #000; border-collapse:collapse; margin-bottom:6px;">
    <tr>
        <td colspan="4" style="padding:4px; font-weight:bold;">
            6. DATOS DE LA MERCADERÍA
        </td>
    </tr>
    <tr>
        <th style="border:1px solid #000; padding:4px;">Cantidad</th>
        <th style="border:1px solid #000; padding:4px;">Unidad</th>
        <th style="border:1px solid #000; padding:4px;">Descripción</th>
        <th style="border:1px solid #000; padding:4px;">Subtotal</th>
    </tr>

    <?php
    $total = 0;
    foreach ($detalle as $d):
        $total += $d['subtotal'];
    ?>
        <tr>
            <td style="border:1px solid #000; padding:4px; text-align:right;">
                <?= number_format($d['cantidad'], 2) ?>
            </td>
            <td style="border:1px solid #000; padding:4px; text-align:center;">
                <?= $d['unidad'] ?? 'UND' ?>
            </td>
            <td style="border:1px solid #000; padding:4px;">
                <?= $d['desc_articulo'] ?>
            </td>
            <td style="border:1px solid #000; padding:4px; text-align:right;">
                <?= number_format($d['subtotal'], 2) ?>
            </td>
        </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="3" style="border:1px solid #000; padding:4px; text-align:right;">
            <strong>Total</strong>
        </td>
        <td style="border:1px solid #000; padding:4px; text-align:right;">
            <strong><?= number_format($total, 2) ?></strong>
        </td>
    </tr>
</table>



<?php
$html = ob_get_clean();
$mpdf->WriteHTML($html);
$mpdf->Output("remision_$id.pdf", "I");
