<?php
$pedidoPlantillaVars = get_defined_vars();
$cabecera = isset($pedidoPlantillaVars['cabecera']) && is_array($pedidoPlantillaVars['cabecera']) ? $pedidoPlantillaVars['cabecera'] : [];
$detalle = isset($pedidoPlantillaVars['detalle']) && is_array($pedidoPlantillaVars['detalle']) ? $pedidoPlantillaVars['detalle'] : [];

$pedidoNumero = str_pad($cabecera['idpedido_cabecera'] ?? 0, 6, '0', STR_PAD_LEFT);
$fechaPedido = !empty($cabecera['fecha']) ? date('d/m/Y', strtotime($cabecera['fecha'])) : '';
$creadoPor = trim(($cabecera['usu_nombre'] ?? '') . ' ' . ($cabecera['usu_apellido'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pedido</title>
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
            background: #2f6f6f;
            color: #fff;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <table width="100%" style="background:#2f6f6f; color:#fff; margin-bottom:10px;">
        <tr>
            <td width="20%" align="left" style="padding:8px;">
                <img src="<?= __DIR__ . '/../assets/logo.png' ?>" height="50">
            </td>
            <td width="50%" align="center">
                <h2 style="margin:0;">PEDIDO DE COMPRA</h2>
            </td>
            <td align="right">
                Pedido N° <?= $pedidoNumero ?><br>
                <?= $fechaPedido ?>
            </td>
        </tr>
    </table>


    <strong>Creado por:</strong>
    <?= $creadoPor ?>

    <table style="margin-top:10px;">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th class="center">Stock actual</th>
                <th class="center">Cantidad solicitada</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalle as $d): ?>
                <tr>
                    <td><?= $d['codigo'] ?></td>
                    <td><?= $d['desc_articulo'] ?></td>
                    <td class="center"><?= number_format((float)($d['stock_actual'] ?? 0), 2, ',', '.') ?></td>
                    <td class="center"><?= number_format((float)($d['cantidad'] ?? 0), 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br><br>
</body>

</html>
