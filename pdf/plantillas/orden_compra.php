<?php
$pdfVars = get_defined_vars();
$datos = isset($pdfVars['datos']) && is_array($pdfVars['datos']) ? $pdfVars['datos'] : [];
$filtros = isset($pdfVars['filtros']) && is_array($pdfVars['filtros']) ? $pdfVars['filtros'] : [];
$resumen = isset($pdfVars['resumen']) && is_array($pdfVars['resumen']) ? $pdfVars['resumen'] : [];
$cabecera = isset($pdfVars['cabecera']) && is_array($pdfVars['cabecera']) ? $pdfVars['cabecera'] : [];
$detalle = isset($pdfVars['detalle']) && is_array($pdfVars['detalle']) ? $pdfVars['detalle'] : [];
$empresa = isset($pdfVars['empresa']) ? (string)$pdfVars['empresa'] : '';
$usuario = isset($pdfVars['usuario']) ? (string)$pdfVars['usuario'] : '';
$desde = isset($pdfVars['desde']) ? (string)$pdfVars['desde'] : '';
$hasta = isset($pdfVars['hasta']) ? (string)$pdfVars['hasta'] : '';
$proveedor = isset($pdfVars['proveedor']) ? (string)$pdfVars['proveedor'] : '';
$estado = isset($pdfVars['estado']) ? (string)$pdfVars['estado'] : '';
$sucursal = isset($pdfVars['sucursal']) ? (string)$pdfVars['sucursal'] : '';
?><!DOCTYPE html>
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
            background: #2f6f6f;
            color: #fff;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .info-title {
            color: #2f6f6f;
            font-weight: bold;
            margin-bottom: 4px;
        }
    </style>
</head>

<body>

    <table width="100%" style="background:#2f6f6f; color:#fff; margin-bottom:10px;">
        <tr>
            <td width="20%" align="left" style="padding:8px;">
                <img src="<?= __DIR__ . '/../assets/logo.png' ?>" height="50">
            </td>
            <td><strong>ORDEN DE COMPRA</strong></td>
            <td align="right">
                OC Nro <?= str_pad($cabecera['idorden_compra'], 6, '0', STR_PAD_LEFT) ?><br>
                Fecha: <?= date('d/m/Y', strtotime($cabecera['fecha'])) ?><br>
                Entrega: <?= $cabecera['fecha_entrega'] ? date('d/m/Y', strtotime($cabecera['fecha_entrega'])) : '-' ?>
            </td>
        </tr>
    </table>

    <table style="margin-bottom:10px;">
        <tr>
            <td width="50%" valign="top">
                <div class="info-title">Proveedor</div>
                <?= htmlspecialchars($cabecera['razon_social'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
                RUC: <?= htmlspecialchars($cabecera['ruc'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
                Tel: <?= htmlspecialchars($cabecera['telefono'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
                Dir: <?= htmlspecialchars($cabecera['direccion'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
            </td>
            <td width="50%" valign="top">
                <div class="info-title">Sucursal destino / lugar de entrega</div>
                <?= htmlspecialchars($cabecera['sucursal_destino'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
                Dir: <?= htmlspecialchars($cabecera['sucursal_destino_direccion'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
                Tel: <?= htmlspecialchars($cabecera['sucursal_destino_telefono'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
                Creado por: <?= htmlspecialchars(trim(($cabecera['usu_nombre'] ?? '') . ' ' . ($cabecera['usu_apellido'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
            </td>
        </tr>
    </table>

    <table style="margin-top:10px;">
        <thead>
            <tr>
                <th>Codigo</th>
                <th>Descripcion</th>
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
                    <td><?= htmlspecialchars($d['codigo'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($d['desc_articulo'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
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
