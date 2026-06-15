<?php
$pdfVars = get_defined_vars();
$datos = isset($pdfVars['datos']) && is_array($pdfVars['datos']) ? $pdfVars['datos'] : [];
$filtros = isset($pdfVars['filtros']) && is_array($pdfVars['filtros']) ? $pdfVars['filtros'] : [];
$resumen = isset($pdfVars['resumen']) && is_array($pdfVars['resumen']) ? $pdfVars['resumen'] : [];
$cabecera = isset($pdfVars['cabecera']) && is_array($pdfVars['cabecera']) ? $pdfVars['cabecera'] : [];
$detalle = isset($pdfVars['detalle']) && is_array($pdfVars['detalle']) ? $pdfVars['detalle'] : [];
$empresa = isset($pdfVars['empresa']) ? (string)$pdfVars['empresa'] : '';
$usuario = isset($pdfVars['usuario']) ? (string)$pdfVars['usuario'] : '';
$desde = isset($pdfVars['desde']) ? (string)$pdfVars['desde'] : '';
$hasta = isset($pdfVars['hasta']) ? (string)$pdfVars['hasta'] : '';
$proveedor = isset($pdfVars['proveedor']) ? (string)$pdfVars['proveedor'] : '';
$estado = isset($pdfVars['estado']) ? (string)$pdfVars['estado'] : '';
$sucursal = isset($pdfVars['sucursal']) ? (string)$pdfVars['sucursal'] : '';
function estadoCompra($estado)
{
    return match ((int)$estado) {
        0 => 'Anulado',
        1 => 'Activo',
        2 => 'Procesado',
        3 => 'Con diferencia',
        4 => 'Regularizada con NC',
        default => 'Desconocido',
    };
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
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
            background: #eaeaea;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .info {
            font-size: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>REPORTE DE COMPRAS</h2>
        <div><?= $empresa ?></div>
    </div>

    <div class="info">
        <b>Emitido por:</b> <?= $usuario ?> |
        <b>Fecha:</b> <?= date('d-m-Y H:i') ?>
    </div>

    <?php if (empty($datos)): ?>
        <p style="text-align:center;">No existen registros para los filtros seleccionados.</p>
        <?php return; ?>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Compra</th>
                <th>Proveedor</th>
                <th>Factura</th>
                <th>Fecha Fact.</th>
                <th>Estado</th>
                <th>Condición</th>
                <th>Ítems</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Sucursal</th>
            </tr>
        </thead>

        <tbody>
            <?php $i = 1;
            $totalGeneral = 0; ?>
            <?php foreach ($datos as $row):
                $totalGeneral += $row['total_compra'];
            ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td class="text-center"><?= $row['idcompra_cabecera'] ?></td>
                    <td><?= $row['proveedor'] ?></td>
                    <td><?= $row['nro_factura'] ?></td>
                    <td class="text-center"><?= $row['fecha_factura'] ?></td>
                    <td class="text-center"><?= estadoCompra($row['estado']) ?></td>
                    <td><?= $row['condicion'] ?></td>
                    <td class="text-center"><?= $row['cantidad_items'] ?></td>
                    <td class="text-center"><?= $row['cantidad_total'] ?></td>
                    <td class="text-right"><?= number_format($row['total_compra'], 0, ',', '.') ?></td>
                    <td><?= $row['sucursal'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr>
                <th colspan="9" class="text-right">TOTAL GENERAL</th>
                <th class="text-right"><?= number_format($totalGeneral, 0, ',', '.') ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>

</body>

</html>
