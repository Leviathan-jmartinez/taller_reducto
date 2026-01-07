<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h2 {
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .info {
            font-size: 10px;
            margin-bottom: 10px;
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
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            font-size: 10px;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>REPORTE DE PRESUPUESTOS DE COMPRA</h2>
        <div><?= $empresa ?></div>
    </div>

    <div class="info">
        <b>Desde:</b> <?= $desde ?: 'Sin filtro' ?> |
        <b>Hasta:</b> <?= $hasta ?: 'Sin filtro' ?> |
        <b>Estado:</b> <?= $estado !== null ? $estado : 'Todos' ?> |
        <b>Sucursal:</b> <?= $sucursal ?: 'Todas' ?><br>
        <b>Emitido por:</b> <?= $usuario ?> |
        <b>Fecha:</b> <?= date('d-m-Y H:i') ?>
    </div>
    <?php if (empty($datos)): ?>
        <div style="text-align:center; margin-top:40px;">
            <strong>No existen presupuestos para la sucursal seleccionada.</strong>
        </div>
        <?php return; ?>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Presupuesto</th>
                <th>Proveedor</th>
                <th>Fecha</th>
                <th>Vencimiento</th>
                <th>Creado por</th>
                <th>Actualizado por</th>
                <th>Estado</th>
                <th>Ítems</th>
                <th>Unidades</th>
                <th>Total (Gs)</th>
                <th>Sucursal</th>
            </tr>
        </thead>

        <tbody>


            <?php
            function estadoPresupuestoCompra($estado)
            {
                return match ((int)$estado) {
                    1 => 'Pendiente',
                    2 => 'Procesado',
                    0 => 'Anulado',
                    default => 'Desconocido',
                };
            }
            $i = 1;
            $totalGeneral = 0;
            ?>

            <?php foreach ($datos as $row):
                $totalGeneral += $row['total'];
            ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td class="text-center"><?= $row['idpresupuesto_compra'] ?></td>
                    <td><?= $row['proveedor'] ?: '-' ?></td>
                    <td class="text-center"><?= date('d-m-Y', strtotime($row['fecha'])) ?></td>
                    <td class="text-center"><?= $row['fecha_venc'] ?: '-' ?></td>
                    <td><?= $row['usuario_crea'] ?></td>
                    <td><?= $row['usuario_actualiza'] ?: '-' ?></td>
                    <td class="text-center"><?= estadoPresupuestoCompra($row['estado']) ?></td>
                    <td class="text-center"><?= $row['cantidad_items'] ?></td>
                    <td class="text-center"><?= $row['cantidad_unidades'] ?></td>
                    <td class="text-right"><?= number_format($row['total'], 0, ',', '.') ?></td>
                    <td><?= $row['sucursal'] ?: '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr>
                <th colspan="10" class="text-right">TOTAL GENERAL</th>
                <th class="text-right"><?= number_format($totalGeneral, 0, ',', '.') ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <b>Total de presupuestos:</b> <?= count($datos) ?>
    </div>

</body>

</html>