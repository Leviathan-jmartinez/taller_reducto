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
        <h2>REPORTE DE PRESUPUESTO DE SERVICIOS</h2>
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
                <th>Presupuesto</th>
                <th>Fecha</th>
                <th>Venc.</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Ítems</th>
                <th>Subtotal</th>
                <th>Desc.</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Sucursal</th>
            </tr>
        </thead>

        <tbody>
            <?php
            function estadoPreSer($estado)
            {
                return match ((int)$estado) {
                    1 => 'Pendiente',
                    2 => 'Aprobado',
                    3 => 'Finalizado',
                    0 => 'Anulado',
                    default => 'Desconocido',
                };
            }
            $i = 1; ?>
            <?php foreach ($datos as $row): ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td class="text-center"><?= $row['idpresupuesto_servicio'] ?></td>
                    <td class="text-center"><?= $row['fecha'] ?></td>
                    <td class="text-center"><?= $row['fecha_venc'] ?: '-' ?></td>
                    <td><?= $row['cliente'] ?: '-' ?></td>
                    <td><?= $row['vehiculo'] ?: '-' ?></td>
                    <td class="text-center"><?= $row['cantidad_items'] ?></td>
                    <td class="text-right"><?= number_format($row['subtotal'], 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($row['total_descuento'], 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($row['total_final'], 0, ',', '.') ?></td>
                    <td class="text-center"><?= estadoPreSer($row['estado']) ?></td>
                    <td><?= $row['sucursal'] ?: '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>