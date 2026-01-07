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
        <h2>REPORTE DE RECEPCIÓN DE SERVICIOS</h2>
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
                <th>Recepción</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Ingreso</th>
                <th>Salida</th>
                <th>Kilometraje</th>
                <th>Usuario</th>
                <th>Estado</th>
                <th>Sucursal</th>
            </tr>
        </thead>

        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($datos as $row): ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td class="text-center"><?= $row['idrecepcion'] ?></td>
                    <td><?= $row['cliente'] ?></td>
                    <td><?= $row['vehiculo'] ?></td>
                    <td class="text-center"><?= date('d-m-Y H:i', strtotime($row['fecha_ingreso'])) ?></td>
                    <td class="text-center"><?= $row['fecha_salida'] ? date('d-m-Y H:i', strtotime($row['fecha_salida'])) : '-' ?></td>
                    <td class="text-center"><?= number_format($row['kilometraje'], 0, ',', '.') ?></td>
                    <td><?= $row['usuario'] ?></td>
                    <td class="text-center"><?= $row['estado'] ?></td>
                    <td><?= $row['sucursal'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>