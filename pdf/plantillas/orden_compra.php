<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Orden de Compra</title>
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
            border: 1px solid #ddd;
            padding: 6px;
        }

        th {
            background: #1f3b5b;
            color: #fff;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>

    <table width="100%" style="margin-bottom:10px;">
        <tr>
            <td><strong>ORDEN DE COMPRA</strong></td>
            <td align="right">
                OC Nº <?= str_pad($cabecera['idorden_compra'], 6, '0', STR_PAD_LEFT) ?><br>
                Fecha: <?= date('d/m/Y', strtotime($cabecera['fecha'])) ?><br>
                Entrega: <?= $cabecera['fecha_entrega'] ? date('d/m/Y', strtotime($cabecera['fecha_entrega'])) : '-' ?>
            </td>
        </tr>
    </table>

    <strong>Proveedor</strong><br>
    <?= $cabecera['razon_social'] ?><br>
    RUC: <?= $cabecera['ruc'] ?><br>
    Tel: <?= $cabecera['telefono'] ?><br>
    Dir: <?= $cabecera['direccion'] ?><br><br>

    <strong>Creado por:</strong>
    <?= $cabecera['usu_nombre'] . ' ' . $cabecera['usu_apellido'] ?>

    <table style="margin-top:10px;">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th class="center">Cant.</th>
                <th class="right">Precio</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($detalle as $d):
                $total += $d['subtotal'];
            ?>
                <tr>
                    <td><?= $d['codigo'] ?></td>
                    <td><?= $d['desc_articulo'] ?></td>
                    <td class="center"><?= number_format($d['cantidad'], 0, ',', '.') ?></td>
                    <td class="right"><?= number_format($d['precio_unitario'], 0, ',', '.') ?></td>
                    <td class="right"><?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <table width="40%" align="right">
        <tr>
            <td><strong>TOTAL</strong></td>
            <td class="right"><strong>Gs. <?= number_format($total, 0, ',', '.') ?></strong></td>
        </tr>
    </table>

    <br><br><br>

</body>

</html>