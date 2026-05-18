<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
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
        <h2>REPORTE DE VEHICULOS</h2>
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
                <th>Placa</th>
                <th>Modelo</th>
                <th>Cliente</th>
                <th>Documento</th>
                <th>Serie</th>
                <th>Anho</th>
                <th>Color</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($datos as $row): ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td><?= $row['placa'] ?: '-' ?></td>
                    <td><?= $row['modelo'] ?: '-' ?></td>
                    <td><?= $row['cliente'] ?: '-' ?></td>
                    <td><?= $row['doc_number'] ?: '-' ?></td>
                    <td><?= $row['nro_serie'] ?: '-' ?></td>
                    <td class="text-center"><?= $row['anho'] ?: '-' ?></td>
                    <td><?= $row['color'] ?: '-' ?></td>
                    <td class="text-center"><?= $row['estado'] == 1 ? 'Activo' : 'Inactivo' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>
