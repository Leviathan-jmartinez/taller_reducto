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
        <h2>REPORTE DE ÓRDENES DE COMPRA</h2>
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
                <th>Orden</th>
                <th>Proveedor</th>
                <th>Fecha</th>
                <th>Entrega</th>
                <th>Creado por</th>
                <th>Actualizado por</th>
                <th>Estado</th>
                <th>Ítems</th>
                <th>Cant.</th>
                <th>Pendiente</th>
                <th>Total</th>
                <th>Sucursal</th>
            </tr>
        </thead>

        <tbody>
            <?php
            function estadoOrdenCompra($estado)
            {
                return match ((int)$estado) {
                    1 => 'Pendiente',
                    2 => 'Procesado',
                    0 => 'Anulado',
                    default => 'Desconocido',
                };
            }
            $i = 1;
            $totalGeneral = 0; ?>
            <?php foreach ($datos as $row):
                $totalGeneral += $row['total'];
            ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td class="text-center"><?= $row['idorden_compra'] ?></td>
                    <td><?= $row['proveedor'] ?></td>
                    <td class="text-center"><?= date('d-m-Y', strtotime($row['fecha'])) ?></td>
                    <td class="text-center"><?= $row['fecha_entrega'] ?: '-' ?></td>
                    <td><?= $row['usuario_crea'] ?></td>
                    <td><?= $row['usuario_actualiza'] ?: '-' ?></td>
                    <td class="text-center"><?= estadoOrdenCompra($row['estado']) ?></td>
                    <td class="text-center"><?= $row['cantidad_items'] ?></td>
                    <td class="text-center"><?= $row['cantidad_total'] ?></td>
                    <td class="text-center"><?= $row['cantidad_pendiente'] ?></td>
                    <td class="text-right"><?= number_format($row['total'], 0, ',', '.') ?></td>
                    <td><?= $row['sucursal'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr>
                <th colspan="11" class="text-right">TOTAL GENERAL</th>
                <th class="text-right"><?= number_format($totalGeneral, 0, ',', '.') ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>

</body>

</html>