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
        <h2>REPORTE DE CLIENTES</h2>
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
                <th>Documento</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Ciudad</th>
                <th>Dirección</th>
                <th>Celular</th>
                <th>Email</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($datos as $row): ?>
                <?php
                $doc = trim(($row['doc_type'] ?? '') . ' ' . ($row['doc_number'] ?? '') . ' ' . ($row['digito_v'] ?? ''));
                ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td><?= $doc ?: '-' ?></td>
                    <td><?= $row['nombre_cliente'] ?: '-' ?></td>
                    <td><?= $row['apellido_cliente'] ?: '-' ?></td>
                    <td><?= $row['ciudad'] ?: '-' ?></td>
                    <td><?= $row['direccion_cliente'] ?: '-' ?></td>
                    <td><?= $row['celular_cliente'] ?: '-' ?></td>
                    <td><?= $row['email_cliente'] ?: '-' ?></td>
                    <td class="text-center"><?= $row['estado_cliente'] == 1 ? 'Activo' : 'Inactivo' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>