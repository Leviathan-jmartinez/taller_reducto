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
        <h2>REPORTE DE ARTÍCULOS</h2>
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
                <th>Código</th>
                <th>Artículo</th>
                <th>Categoría</th>
                <th>Proveedor</th>
                <th>Marca</th>
                <th>Unidad</th>
                <th>IVA</th>
                <th>Compra</th>
                <th>Venta</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($datos as $row): ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td><?= $row['codigo'] ?></td>
                    <td><?= $row['desc_articulo'] ?></td>
                    <td><?= $row['categoria'] ?></td>
                    <td><?= $row['proveedor'] ?></td>
                    <td><?= $row['marca'] ?></td>
                    <td><?= $row['unidad'] ?></td>
                    <td><?= $row['iva'] ?></td>
                    <td class="text-right"><?= number_format($row['precio_compra'], 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($row['precio_venta'], 0, ',', '.') ?></td>
                    <td class="text-center"><?= $row['estado'] == 1 ? 'Activo' : 'Inactivo' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>