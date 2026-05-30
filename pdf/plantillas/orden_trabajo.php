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
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Orden de Trabajo</title>
    <style>
        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #1f3b5b;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo {
            width: 20%;
        }

        .titulo {
            width: 50%;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #1f3b5b;
        }

        .datos-ot {
            width: 30%;
            text-align: right;
            font-size: 12px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        /* COLORES */
        .azul {
            color: #0d6efd;
        }

        .bg-azul {
            background: #0d6efd;
            color: #fff;
        }

        /* HEADER */
        .header {
            border-bottom: 3px solid #0d6efd;
            margin-bottom: 15px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo img {
            max-height: 60px;
        }

        .titulo {
            text-align: right;
        }

        .titulo h1 {
            margin: 0;
            font-size: 22px;
        }

        .titulo small {
            font-size: 12px;
        }

        /* CAJAS */
        .grid {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
        }

        .box {
            flex: 1;
            border: 1px solid #ddd;
            padding: 8px;
            border-radius: 4px;
            background: #f9f9f9;
        }

        .box h3 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #0d6efd;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }

        /* TABLA */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background: #0d6efd;
            color: #fff;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        th {
            font-size: 11px;
        }

        td {
            font-size: 11px;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        /* FOOTER */
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .firma {
            width: 40%;
            text-align: center;
        }

        .firma hr {
            border: none;
            border-top: 1px solid #000;
            margin-bottom: 4px;
        }

        .totales {
            margin-top: 20px;
            border-top: 2px solid #2f4f4f;
            padding-top: 10px;
        }

        .totales-linea {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .totales-final {
            margin-top: 8px;
            background: #2f6f6f;
            color: #fff;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            text-align: right;
        }

        .condiciones {
            margin-top: 18px;
            padding: 10px;
            border: 1px solid #dcdcdc;
            background: #f8f9fa;
            font-size: 10px;
            line-height: 1.5;
            color: #444;
        }
    </style>
</head>

<body>

    <!-- HEADER -->

    <table width="100%" style="background:#2f6f6f; color:#fff; margin-bottom:10px;">
        <tr>
            <td width="20%" align="left" style="padding:8px;">
                <img src="<?= __DIR__ . '/../assets/logo.png' ?>" height="50">
            </td>
            <td width="50%" align="center">
                <h2 style="margin:0;">ORDEN DE TRABAJO</h2>
            </td>
            <td width="30%" align="right" style="padding-right:10px; font-size:11px;">
                <strong>OT N°:</strong>
                <?= str_pad($cabecera['idorden_trabajo'], 6, '0', STR_PAD_LEFT) ?><br>
                <?= date('d/m/Y H:i', strtotime($cabecera['fecha_inicio'])) ?>
            </td>
        </tr>
    </table>

    <?php
    function estadoOT($estado)
    {
        return match ((int)$estado) {
            1 => 'Activa',
            2 => 'Servicio registrado',
            3 => 'Pendiente completar',
            0 => 'Anulada',
            default => 'Desconocido',
        };
    }
    ?>

    <!-- INFO -->
    <div class="grid">
        <div class="box">
            <h3>Cliente</h3>
            <?= trim(($cabecera['nombre_cliente'] ?? '') . ' ' . ($cabecera['apellido_cliente'] ?? '')) ?><br>
            <strong>Tel:</strong> <?= $cabecera['celular_cliente'] ?? '-' ?>
        </div>

        <div class="box">
            <h3>Vehículo</h3>
            <strong><?= $cabecera['marca'] ?: '-' ?> <?= $cabecera['modelo'] ?: '-' ?></strong><br>
            <strong>Placa:</strong> <?= $cabecera['placa'] ?? '-' ?><br>
            <strong>Km:</strong> <?= number_format((float)($cabecera['kilometraje'] ?? 0), 0, ',', '.') ?>
        </div>

        <div class="box">
            <h3>Orden</h3>

            <strong>Estado:</strong>
            <?= estadoOT($cabecera['estado']) ?><br>

            <strong>Equipo:</strong>
            <?= $cabecera['nombre_equipo'] ?? 'No asignado' ?><br>

            <strong>Integrantes:</strong><br>
            <?= !empty($cabecera['miembros_equipo'])
                ? $cabecera['miembros_equipo']
                : 'No asignados'; ?>
        </div>
    </div>

    <?php if (($cabecera['origen'] ?? '') === 'RECLAMO'): ?>
        <div class="box" style="margin-bottom:10px;">
            <h3>Detalle del reclamo</h3>
            <strong>Tipo:</strong> <?= $cabecera['tipo_reclamo'] ?? '-' ?><br>
            <strong>Prioridad:</strong>
            <?php
            echo ($cabecera['prioridad'] ?? null) == 1
                ? 'Alta'
                : (($cabecera['prioridad'] ?? null) == 2 ? 'Media' : 'Baja');
            ?><br>
            <strong>Fecha:</strong>
            <?= !empty($cabecera['fecha_reclamo']) ? date('d/m/Y H:i', strtotime($cabecera['fecha_reclamo'])) : '-' ?><br>
            <strong>Descripcion:</strong><br>
            <?= nl2br($cabecera['descripcion_reclamo'] ?? '-') ?>
        </div>
    <?php endif; ?>

    <!-- DETALLE -->
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
            <?php if (empty($detalle)): ?>
                <tr>
                    <td colspan="4" class="center">Sin detalle cargado</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($detalle as $d): ?>
                <tr>
                    <td><?= $d['desc_articulo'] ?></td>
                    <td class="center"><?= $d['cantidad'] ?></td>
                    <td class="right"><?= number_format((float)$d['precio_unitario'], 0, ',', '.') ?></td>
                    <td class="right"><?= number_format((float)$d['subtotal'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- OBS -->
    <?php if (!empty($cabecera['observacion'])): ?>
        <div class="box" style="margin-top:10px;">
            <h3>Observaciones</h3>
            <?= nl2br($cabecera['observacion']) ?>
        </div>
    <?php endif; ?>
    <div class="totales">
        <div class="totales-linea">
            <span>Subtotal servicios</span>
            <span>Gs. <?= number_format((float)($cabecera['subtotal'] ?? 0), 0, ',', '.') ?></span>
        </div>

        <div class="totales-linea">
            <span>Descuentos</span>
            <span>- Gs. <?= number_format((float)($cabecera['total_descuento'] ?? 0), 0, ',', '.') ?></span>
        </div>

        <div class="totales-final">
            TOTAL ESTIMADO: Gs. <?= number_format((float)($cabecera['total_final'] ?? 0), 0, ',', '.') ?>
        </div>
    </div>

    <div class="condiciones">
        <strong>Condiciones:</strong><br>

        La presente OT autoriza al taller a realizar los trabajos detallados.
        Todo trabajo adicional será informado previamente al cliente.
    </div>


    <!-- FIRMAS -->
    <table class="firmas">
        <tr>
            <td>
                <div class="firma-linea"></div>
                <strong>Firma del Cliente</strong><br>

            </td>
            <td>
                <div class="firma-linea"></div>
                <strong>Firma del Técnico</strong><br>
            </td>
        </tr>
    </table>


</body>

</html>