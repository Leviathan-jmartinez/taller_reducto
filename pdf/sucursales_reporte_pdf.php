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
        <h2>REPORTE DE SUCURSALES</h2>
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
                <th>Sucursal</th>
                <th>Empresa</th>
                <th>Direccion</th>
                <th>Telefono</th>
                <th>Establecimiento</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($datos as $row): ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td><?= $row['suc_descri'] ?: '-' ?></td>
                    <td><?= $row['empresa'] ?: '-' ?></td>
                    <td><?= $row['suc_direccion'] ?: '-' ?></td>
                    <td><?= $row['suc_telefono'] ?: '-' ?></td>
                    <td class="text-center"><?= $row['nro_establecimiento'] ?: '-' ?></td>
                    <td class="text-center"><?= $row['estado'] == 1 ? 'Activo' : 'Inactivo' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>
