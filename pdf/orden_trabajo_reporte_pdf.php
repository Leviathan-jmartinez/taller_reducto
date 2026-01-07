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
        <h2>REPORTE DE ÓRDENES DE TRABAJO</h2>
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
                <th>OT</th>
                <th>Presupuesto</th>
                <th>Recepción</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Equipo</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Ítems</th>
                <th>Estado</th>
                <th>Sucursal</th>
            </tr>
        </thead>

        <tbody>
            <?php
            function estadoOT($estado)
            {
                return match ((int)$estado) {
                    1 => 'Abierta',
                    2 => 'En proceso',
                    3 => 'Finalizado',
                    4 => 'Facturado',
                    0 => 'Anulado',
                    default => 'Desconocido',
                };
            }
             $i = 1; ?>
            <?php foreach ($datos as $row): ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td class="text-center"><?= $row['idorden_trabajo'] ?></td>
                    <td class="text-center"><?= $row['idpresupuesto_servicio'] ?></td>
                    <td class="text-center"><?= $row['idrecepcion'] ?></td>
                    <td><?= $row['cliente'] ?></td>
                    <td><?= $row['vehiculo'] ?></td>
                    <td><?= $row['equipo'] ?: '-' ?></td>
                    <td class="text-center"><?= date('d-m-Y H:i', strtotime($row['fecha_inicio'])) ?></td>
                    <td class="text-center"><?= $row['fecha_fin'] ? date('d-m-Y H:i', strtotime($row['fecha_fin'])) : '-' ?></td>
                    <td class="text-center"><?= $row['cantidad_items'] ?></td>
                    <td class="text-center"><?= estadoOT($row['estado']) ?></td>
                    <td><?= $row['sucursal'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>