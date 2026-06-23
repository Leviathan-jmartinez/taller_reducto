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
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
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

        .right {
            text-align: right;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .info {
            font-size: 10px;
            margin-bottom: 10px;
        }

        .summary {
            width: 100%;
            margin: 8px 0 12px;
        }

        .summary td {
            border: 1px solid #777;
            padding: 6px;
            text-align: center;
        }

        .summary .label {
            display: block;
            font-size: 9px;
            color: #555;
            text-transform: uppercase;
        }

        .summary .value {
            display: block;
            font-size: 13px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>REPORTE DE REGISTRO DE SERVICIOS</h2>
        <div><?= htmlspecialchars($empresa, ENT_QUOTES, 'UTF-8') ?></div>
    </div>

    <div class="info">
        <b>Emitido por:</b> <?= htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?> |
        <b>Fecha:</b> <?= date('d-m-Y H:i') ?>
    </div>

    <?php if (empty($datos)): ?>
        <p style="text-align:center;">No existen registros para los filtros seleccionados.</p>
        <?php return; ?>
    <?php endif; ?>

    <?php
    function estadoRegistroServicio($estado)
    {
        return match ((int)$estado) {
            1 => 'Registrado',
            2 => 'Facturado',
            3 => 'Con Reclamo',
            0 => 'Anulado',
            default => 'Desconocido',
        };
    }

    function h($value)
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    $resumen = $resumen ?? [];
    ?>

    <table class="summary">
        <tr>
            <td><span class="label">Servicios</span><span class="value"><?= number_format($resumen['total'] ?? count($datos), 0, ',', '.') ?></span></td>
            <td><span class="label">Facturados</span><span class="value"><?= number_format($resumen['facturados'] ?? 0, 0, ',', '.') ?></span></td>
            <td><span class="label">Con reclamo</span><span class="value"><?= number_format($resumen['con_reclamo'] ?? 0, 0, ',', '.') ?></span></td>
            <td><span class="label">Repuestos</span><span class="value"><?= number_format((float)($resumen['cantidad_repuestos'] ?? 0), 2, ',', '.') ?></span></td>
            <td><span class="label">Insumos</span><span class="value"><?= number_format((float)($resumen['cantidad_insumos'] ?? 0), 2, ',', '.') ?></span></td>
            <td><span class="label">Importe total</span><span class="value"><?= number_format($resumen['total_importe'] ?? 0, 0, ',', '.') ?></span></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Registro</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Cliente</th>
                <th>Veh&iacute;culo</th>
                <th>Usuario</th>
                <th>Tecnico Encargado</th>
                <th>Repuestos</th>
                <th>Insumos</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($datos as $row): ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td class="text-center"><?= h($row['idregistro_servicio']) ?></td>
                    <td class="text-center"><?= h($row['fecha_servicio']) ?></td>
                    <td class="text-center"><?= estadoRegistroServicio($row['estado']) ?></td>
                    <td><?= h($row['cliente']) ?></td>
                    <td><?= h($row['vehiculo']) ?></td>
                    <td><?= h($row['usuario_registra']) ?></td>
                    <td class="text-center"><?= h($row['tecnico']) ?></td>
                    <td class="text-center"><?= number_format((float)($row['cantidad_repuestos'] ?? 0), 2, ',', '.') ?></td>
                    <td class="text-center"><?= number_format((float)($row['cantidad_insumos'] ?? 0), 2, ',', '.') ?></td>
                    <td class="right"><?= number_format($row['total'] ?? 0, 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>
