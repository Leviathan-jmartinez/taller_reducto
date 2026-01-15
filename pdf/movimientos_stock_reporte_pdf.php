<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte Movimientos de Stock</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h2 {
            margin: 0;
        }

        .encabezado {
            text-align: center;
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
            background: #e9ecef;
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .meta {
            margin-bottom: 8px;
            font-size: 10px;
        }
    </style>
</head>

<body>

    <div class="encabezado">
        <h2><?= $empresa ?></h2>
        <div>REPORTE DE MOVIMIENTOS DE STOCK</div>
    </div>

    <div class="meta">
        <strong>Usuario:</strong> <?= $usuario ?><br>
        <strong>Fecha de impresión:</strong> <?= date('d/m/Y H:i') ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Sucursal</th>
                <th>Tipo</th>
                <th>Artículo</th>
                <th>Cant.</th>
                <th>Signo</th>
                <th>Referencia</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalEntradas = 0;
            $totalSalidas  = 0;

            foreach ($datos as $d):
                if ($d['MovStockSigno'] == 1) {
                    $totalEntradas += $d['MovStockCantidad'];
                } else {
                    $totalSalidas += $d['MovStockCantidad'];
                }
            ?>
                <tr>
                    <td class="center"><?= date('d/m/Y H:i', strtotime($d['MovStockFechaHora'])) ?></td>
                    <td><?= $d['sucursal'] ?></td>
                    <td><?= $d['TipoMovStockId'] ?></td>
                    <td><?= $d['desc_articulo'] ?></td>
                    <td class="right"><?= number_format($d['MovStockCantidad'], 2, ',', '.') ?></td>
                    <td class="center"><?= $d['MovStockSigno'] == 1 ? '+' : '-' ?></td>
                    <td><?= $d['MovStockReferencia'] ?? '' ?></td>
                    <td><?= $d['usuario'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>

    <table width="40%" align="right">
        <tr>
            <td><strong>Total Entradas</strong></td>
            <td class="right"><?= number_format($totalEntradas, 2, ',', '.') ?></td>
        </tr>
        <tr>
            <td><strong>Total Salidas</strong></td>
            <td class="right"><?= number_format($totalSalidas, 2, ',', '.') ?></td>
        </tr>
    </table>

</body>

</html>