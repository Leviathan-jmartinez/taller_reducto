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
        <h2>REPORTE DE REGISTRO DE SERVICIOS</h2>
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
                <th>Registro</th>
                <th>OT</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Ejecución</th>
                <th>Ítems</th>
                <th>Tecnico Encargado</th>
                <th>Estado</th>
                <th>Sucursal</th>
            </tr>
        </thead>

        <tbody>
            <?php

            function estadoRegistroServicio($estado)
            {
                return match ((int)$estado) {
                    1 => 'Registrado',
                    0 => 'Anulado',
                    2 => 'Facturado',
                    default => 'Desconocido',
                };
            }


            $i = 1; ?>
            <?php foreach ($datos as $row): ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td class="text-center"><?= $row['idregistro_servicio'] ?></td>
                    <td class="text-center"><?= $row['idorden_trabajo'] ?></td>
                    <td><?= $row['cliente'] ?></td>
                    <td><?= $row['vehiculo'] ?></td>
                    <td class="text-center"><?= $row['fecha_ejecucion'] ?></td>
                    <td class="text-center"><?= $row['cantidad_items'] ?></td>
                    <td class="text-center"><?= $row['tecnico'] ?></td>
                    <td class="text-center"><?= estadoRegistroServicio($row['estado']) ?></td>
                    <td><?= $row['sucursal'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>