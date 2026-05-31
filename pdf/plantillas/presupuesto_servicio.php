<?php
$pdfVars = get_defined_vars();
$datos = isset($pdfVars['datos']) && is_array($pdfVars['datos']) ? $pdfVars['datos'] : [];
$filtros = isset($pdfVars['filtros']) && is_array($pdfVars['filtros']) ? $pdfVars['filtros'] : [];
$resumen = isset($pdfVars['resumen']) && is_array($pdfVars['resumen']) ? $pdfVars['resumen'] : [];
$cabecera = isset($pdfVars['cabecera']) && is_array($pdfVars['cabecera']) ? $pdfVars['cabecera'] : [];
$detalle = isset($pdfVars['detalle']) && is_array($pdfVars['detalle']) ? $pdfVars['detalle'] : [];
$promociones = isset($pdfVars['promociones']) && is_array($pdfVars['promociones']) ? $pdfVars['promociones'] : [];
$empresa = isset($pdfVars['empresa']) ? (string)$pdfVars['empresa'] : '';
$usuario = isset($pdfVars['usuario']) ? (string)$pdfVars['usuario'] : '';
$desde = isset($pdfVars['desde']) ? (string)$pdfVars['desde'] : '';
$hasta = isset($pdfVars['hasta']) ? (string)$pdfVars['hasta'] : '';
$proveedor = isset($pdfVars['proveedor']) ? (string)$pdfVars['proveedor'] : '';
$estado = isset($pdfVars['estado']) ? (string)$pdfVars['estado'] : '';
$sucursal = isset($pdfVars['sucursal']) ? (string)$pdfVars['sucursal'] : '';
$totalPromociones = 0;
$promocionesPorDetalle = [];
foreach ($promociones as $promo) {
    $totalPromociones += (float)($promo['monto_aplicado'] ?? 0);
    $idDetallePromo = (int)($promo['id_detalle_presupuesto'] ?? 0);
    if ($idDetallePromo > 0) {
        $promocionesPorDetalle[$idDetallePromo][] = $promo;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Presupuesto de Servicio</title>
    <style>
        body {
            color: #25313b;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #dbe3ea;
            padding: 7px;
        }

        th {
            background: #245f63;
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .muted {
            color: #66717c;
        }

        .header {
            background: #245f63;
            color: #fff;
            margin-bottom: 14px;
        }

        .header td {
            border: none;
            padding: 10px;
        }

        .doc-title {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: .5px;
            margin: 0;
            text-transform: uppercase;
        }

        .doc-meta {
            font-size: 10px;
            line-height: 1.7;
            text-align: right;
        }

        .box-table {
            margin-bottom: 12px;
        }

        .box-table td {
            background: #f8fafc;
            border: 1px solid #dbe3ea;
            vertical-align: top;
        }

        .box-title {
            color: #245f63;
            display: block;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .items td {
            border-left: none;
            border-right: none;
        }

        .items tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .promo-line td {
            background: #eef7f3 !important;
            border-top: none;
            color: #2f6f4e;
            font-size: 9.5px;
            padding-top: 4px;
        }

        .promo-label {
            color: #245f63;
            font-weight: bold;
        }

        .totales {
            margin-top: 12px;
            width: 42%;
        }

        .totales td {
            border: 1px solid #dbe3ea;
        }

        .total-final td {
            background: #245f63;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
        }

        .condiciones {
            background: #f8fafc;
            border: 1px solid #dbe3ea;
            margin-top: 24px;
            padding: 9px 10px;
        }

        .condiciones h4 {
            color: #245f63;
            font-size: 11px;
            margin: 0 0 6px 0;
            text-transform: uppercase;
        }

        .condiciones p {
            color: #46535f;
            font-size: 9.5px;
            line-height: 1.45;
            margin: 0 0 5px 0;
            text-align: justify;
        }

        .firmas td {
            border: none;
            padding-top: 55px;
        }

        .firma-box {
            text-align: center;
        }

        .firma-linea {
            border-top: 1px solid #25313b;
            margin: 0 auto 8px auto;
            width: 75%;
        }

        .firma-box strong {
            font-size: 10px;
            color: #25313b;
        }

        .firma-box small {
            font-size: 8px;
        }
    </style>
</head>

<body>
    <table class="header">
        <tr>
            <td width="20%" align="left">
                <img src="<?= __DIR__ . '/../assets/logo.png' ?>" height="50">
            </td>
            <td width="50%" align="center">
                <p class="doc-title">Presupuesto de Servicio</p>
                <span style="color:#dce8ea;">Taller de reparacion y mantenimiento</span>
            </td>
            <td class="doc-meta" width="30%">
                <strong>Nro.</strong> <?= str_pad($cabecera['idpresupuesto_servicio'], 6, '0', STR_PAD_LEFT) ?><br>
                <strong>Fecha:</strong> <?= date('d/m/Y', strtotime($cabecera['fecha'])) ?><br>
                <strong>Valido hasta:</strong> <?= date('d/m/Y', strtotime($cabecera['fecha_venc'])) ?>
            </td>
        </tr>
    </table>

    <table class="box-table">
        <tr>
            <td width="50%">
                <span class="box-title">Cliente</span>
                <strong><?= $cabecera['nombre_cliente'] . ' ' . $cabecera['apellido_cliente'] ?></strong><br>
                <span class="muted">Telefono:</span> <?= $cabecera['celular_cliente'] ?: '-' ?><br>
                <span class="muted">Direccion:</span> <?= $cabecera['direccion_cliente'] ?: '-' ?>
            </td>
            <td width="50%">
                <span class="box-title">Vehiculo</span>
                <strong><?= $cabecera['marca'] ?: '-' ?> <?= $cabecera['modelo'] ?: '-' ?></strong><br>
                <span class="muted">Placa:</span> <?= $cabecera['placa'] ?: '-' ?><br>
                <span class="muted">Referencia:</span>
                <?= (($cabecera['origen'] ?? 'DIAGNOSTICO') === 'PRELIMINAR') ? 'Cotizacion preliminar sujeta a diagnostico' : 'Servicio tecnico segun diagnostico' ?>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Descripcion</th>
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
                <?php foreach (($promocionesPorDetalle[(int)($d['id_detalle_presupuesto'] ?? 0)] ?? []) as $promo): ?>
                    <tr class="promo-line">
                        <td colspan="3">
                            <span class="promo-label">Promocion:</span>
                            <?= $promo['nombre'] ?>
                            <span class="muted">
                                | <?= $promo['cantidad'] ?> x -<?= number_format($promo['monto_unitario'], 0, ',', '.') ?>
                            </span>
                        </td>
                        <td class="right">- <?= number_format($promo['monto_aplicado'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="totales" align="right">
        <tr>
            <td>Subtotal</td>
            <td class="right"><?= number_format($cabecera['subtotal'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td>Promociones</td>
            <td class="right">- <?= number_format($totalPromociones, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td>Descuento</td>
            <td class="right">- <?= number_format($cabecera['total_descuento'], 0, ',', '.') ?></td>
        </tr>
        <tr class="total-final">
            <td>Total</td>
            <td class="right"><?= number_format($cabecera['total_final'], 0, ',', '.') ?></td>
        </tr>
    </table>

    <br><br><br><br>

    <div class="condiciones">
        <h4>Condiciones y garantia</h4>
        <p>
            Este presupuesto tiene validez hasta la fecha indicada. La garantia cubre la mano de obra realizada por el taller
            y los repuestos instalados, siempre que la falla este relacionada directamente con el servicio efectuado.
            <?php if (($cabecera['origen'] ?? 'DIAGNOSTICO') === 'PRELIMINAR'): ?>
                Al ser una cotizacion preliminar, los importes y trabajos quedan sujetos a recepcion y diagnostico tecnico.
            <?php endif; ?>
        </p>
        <p>
            La garantia no cubre desgaste natural, mal uso, modificaciones externas, reparaciones realizadas por terceros
            ni fallas ajenas al diagnostico inicial. Si durante el trabajo se detectan danos adicionales, se informara al
            cliente antes de realizar nuevos trabajos o generar costos extra.
        </p>
    </div>
    <br><br><br><br><br>
    <table class="firmas">
        <tr>
            <td width="50%" class="center">
                <div class="firma-box">
                    <div class="firma-linea"></div>
                    <strong>Firma del Cliente</strong><br>
                    <span class="muted">
                        <?= $cabecera['nombre_cliente'] . ' ' . $cabecera['apellido_cliente'] ?>
                    </span>
                </div>
            </td>

            <td width="50%" class="center">
                <div class="firma-box">
                    <div class="firma-linea"></div>
                    <strong>Firma Autorizada</strong><br>
                    <span class="muted">
                        <?= $cabecera['usu_nombre'] . ' ' . $cabecera['usu_apellido'] ?>
                    </span><br>
                    <small class="muted">Asesor de Servicio</small>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
