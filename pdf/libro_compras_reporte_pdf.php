<?php
// Acumuladores para el resumen
$totExenta    = 0;
$totGrav5     = 0;
$totIva5      = 0;
$totGrav10    = 0;
$totIva10     = 0;
$totGeneral   = 0;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
        }

        h2,
        h4 {
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .meta {
            width: 100%;
            margin-bottom: 10px;
        }

        .meta td {
            padding: 3px;
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

        th {
            background: #f0f0f0;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .totales {
            margin-top: 10px;
            width: 40%;
            float: right;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2><?= htmlspecialchars($empresa) ?></h2>
        <h4>LIBRO DE COMPRAS</h4>
    </div>

    <table class="meta">
        <tr>
            <td><strong>Usuario:</strong> <?= htmlspecialchars($usuario) ?></td>
            <td class="right"><strong>Fecha impresión:</strong> <?= date('d/m/Y H:i') ?></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr class="center">
                <th>Fecha</th>
                <th>Comprobante</th>
                <th>Proveedor</th>
                <th>RUC</th>
                <th>Estado</th>
                <th>Exenta</th>
                <th>Grav. 5%</th>
                <th>IVA 5%</th>
                <th>Grav. 10%</th>
                <th>IVA 10%</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($datos)):
                function estadoLC($estado)
                {
                    return match ((int)$estado) {
                        1 => 'Activo',
                        0 => 'Anulado',
                        default => 'Desconocido',
                    };
                } ?>
                <?php foreach ($datos as $row): ?>
                    <?php
                    $totExenta  += (float)$row['exenta'];
                    $totGrav5   += (float)$row['gravada_5'];
                    $totIva5    += (float)$row['iva_5'];
                    $totGrav10  += (float)$row['gravada_10'];
                    $totIva10   += (float)$row['iva_10'];
                    $totGeneral += (float)$row['total'];
                    ?>
                    <tr>
                        <td class="center"><?= date('d/m/Y', strtotime($row['fecha'])) ?></td>
                        <td class="center"><?= $row['nro_comprobante'] ?></td>
                        <td><?= htmlspecialchars($row['proveedor_nombre']) ?></td>
                        <td class="center"><?= htmlspecialchars($row['proveedor_ruc']) ?></td>
                        <td class="center"><?= estadoLC($row['estado']) ?></td>
                        <td class="right"><?= number_format($row['exenta'], 2, ',', '.') ?></td>
                        <td class="right"><?= number_format($row['gravada_5'], 2, ',', '.') ?></td>
                        <td class="right"><?= number_format($row['iva_5'], 2, ',', '.') ?></td>
                        <td class="right"><?= number_format($row['gravada_10'], 2, ',', '.') ?></td>
                        <td class="right"><?= number_format($row['iva_10'], 2, ',', '.') ?></td>
                        <td class="right"><?= number_format($row['total'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="center">No hay registros para el período seleccionado</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="totales">
        <tr>
            <th>Exenta</th>
            <td class="right"><?= number_format($totExenta, 2, ',', '.') ?></td>
        </tr>
        <tr>
            <th>Gravada 5%</th>
            <td class="right"><?= number_format($totGrav5, 2, ',', '.') ?></td>
        </tr>
        <tr>
            <th>IVA 5%</th>
            <td class="right"><?= number_format($totIva5, 2, ',', '.') ?></td>
        </tr>
        <tr>
            <th>Gravada 10%</th>
            <td class="right"><?= number_format($totGrav10, 2, ',', '.') ?></td>
        </tr>
        <tr>
            <th>IVA 10%</th>
            <td class="right"><?= number_format($totIva10, 2, ',', '.') ?></td>
        </tr>
        <tr>
            <th>Total General</th>
            <td class="right"><strong><?= number_format($totGeneral, 2, ',', '.') ?></strong></td>
        </tr>
    </table>

</body>

</html>