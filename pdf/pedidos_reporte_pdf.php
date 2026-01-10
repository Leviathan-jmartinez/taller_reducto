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

    <?php
    function estadoPedido($estado)
    {
        return match ((int)$estado) {
            0 => 'Anulado',
            1 => 'Pendiente',
            2 => 'Procesado',
            default => 'Desconocido',
        };
    }
    ?>

    <div class="header">
        <h2>REPORTE DE PEDIDOS</h2>
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
                <th>Pedido</th>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Usuario</th>
                <th>Ítems</th>
                <th>Estado</th>
                <th>Sucursal</th>
            </tr>
        </thead>

        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($datos as $row): ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td class="text-center"><?= $row['idpedido_cabecera'] ?></td>
                    <td class="text-center"><?= date('d-m-Y', strtotime($row['fecha'])) ?></td>
                    <td><?= $row['proveedor'] ?: '-' ?></td>
                    <td><?= $row['usuario'] ?></td>
                    <td class="text-center"><?= $row['cantidad_items'] ?></td>
                    <td class="text-center"><?= estadoPedido($row['estado']) ?></td>
                    <td><?= $row['sucursal'] ?: '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>