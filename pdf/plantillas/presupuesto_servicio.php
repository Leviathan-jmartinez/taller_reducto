<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Presupuesto de Servicio</title>
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
            background: #0d6efd;
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
            <td><strong>PRESUPUESTO DE SERVICIO</strong></td>
            <td align="right">
                N° <?= str_pad($cabecera['idpresupuesto_servicio'], 6, '0', STR_PAD_LEFT) ?><br>
                Fecha: <?= date('d/m/Y', strtotime($cabecera['fecha'])) ?><br>
                Vence: <?= date('d/m/Y', strtotime($cabecera['fecha_venc'])) ?>
            </td>
        </tr>
    </table>

    <strong>Cliente</strong><br>
    <?= $cabecera['nombre_cliente'] . ' ' . $cabecera['apellido_cliente'] ?><br>
    Tel: <?= $cabecera['celular_cliente'] ?><br>
    Dirección: <?= $cabecera['direccion_cliente'] ?><br><br>

    <strong>Vehículo</strong><br>
    <?= $cabecera['modelo'] ?> — <?= $cabecera['placa'] ?><br><br>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th class="center">Cant.</th>
                <th class="right">Precio</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalle as $d): ?>
                <tr>
                    <td><?= $d['desc_articulo'] ?></td>
                    <td class="center"><?= $d['cantidad'] ?></td>
                    <td class="right"><?= number_format($d['preciouni'], 0, ',', '.') ?></td>
                    <td class="right"><?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <table width="40%" align="right">
        <tr>
            <td>Subtotal</td>
            <td class="right"><?= number_format($cabecera['subtotal'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td>Descuento</td>
            <td class="right">- <?= number_format($cabecera['total_descuento'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td><strong>TOTAL</strong></td>
            <td class="right"><strong><?= number_format($cabecera['total_final'], 0, ',', '.') ?></strong></td>
        </tr>
    </table>

    <br><br><br>

</body>

</html>