<?php
function estadoCompra($estado)
{
    return match ((int)$estado) {
        1 => 'Activo',
        2 => 'Procesado',
        0 => 'Anulado',
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