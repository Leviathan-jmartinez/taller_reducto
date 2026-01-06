<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
        }

        .info {
            margin-bottom: 10px;
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
            background-color: #eaeaea;
            text-align: center;
        }

        td {
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            padding: 2px 5px;
            font-size: 10px;
            border-radius: 3px;
        }

        .estado-procesado {
            background: #2ecc71;
            color: #000;
        }

        .estado-pendiente {
            background: #f1c40f;
            color: #000;
        }

        .footer {
            margin-top: 10px;
            font-size: 10px;
        }
    </style>
</head>

<body>

    <!-- ================= CABECERA ================= -->
    <div class="header">
        <h2>REPORTE DE PEDIDOS</h2>
        <div><?= $empresa ?></div>
    </div>

    <div class="info">
        <b>Sucursal:</b> <?= $sucursal ?><br>
        <b>Desde:</b> <?= $filtros['desde'] ?: '-' ?> |
        <b>Hasta:</b> <?= $filtros['hasta'] ?: '-' ?> |
        <b>Estado:</b> <?= $filtros['estado'] ?><br>
        <b>Emitido por:</b> <?= $usuario ?> |
        <b>Fecha:</b> <?= date('d-m-Y H:i') ?>
    </div>

    <!-- ================= TABLA ================= -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Pedido</th>
                <th>Proveedor</th>
                <th>Fecha</th>
                <th>Creado por</th>
                <th>Estado</th>
                <th>Procesado Por</th>
                <th>Ítems</th>
                <th>Unidades</th>
            </tr>
        </thead>
        <tbody>

            <?php
            function estadoPedido($estado)
            {
                return match ((int)$estado) {
                    1 => 'Pendiente',
                    2 => 'Procesado',
                    0 => 'Anulado',
                    default => 'Desconocido',
                };
            }

            $i = 1;
            $totalitems = 0;
            $totalunidades = 0;
            $conteoEstado = [];
            ?>

            <?php foreach ($datos as $row): 
                $totalitems += $row['cantidad_items'];
                $totalunidades += $row['cantidad_unidades'];                
                $conteoEstado[$row['estado']] = ($conteoEstado[$row['estado']] ?? 0) + 1;?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td class="text-center"><?= $row['idpedido_cabecera'] ?></td>
                    <td><?= $row['proveedor'] ?></td>
                    <td class="text-center"><?= date('d-m-Y', strtotime($row['fecha'])) ?></td>
                    <td><?= $row['usuario_crea'] ?></td>
                    <td class="text-center"><?= estadoPedido($row['estado']) ?></td>
                    <td><?= $row['usuario_actualiza'] ?: '-' ?></td>
                    <td class="text-center"><?= $row['cantidad_items'] ?></td>
                    <td class="text-center"><?= $row['cantidad_unidades'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <!-- ================= TOTALES ================= -->
        <tfoot>
            <tr>
                <th colspan="7" class="text-right">TOTAL GENERAL</th>
                <th class="text-center"><?= number_format($totalitems, 0, ',', '.') ?></th>
                <th class="text-center"><?= number_format($totalunidades, 0, ',', '.') ?></th>
            </tr>
        </tfoot>
    </table>

    <!-- ================= PIE ================= -->
    <div class="footer">
        <b>Total de pedidos:</b> <?= count($datos) ?><br>
    </div>

</body>

</html>