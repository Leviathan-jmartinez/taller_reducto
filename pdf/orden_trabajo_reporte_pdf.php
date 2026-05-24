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

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .info {
            font-size: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>REPORTE DE ÓRDENES DE TRABAJO</h2>
        <div><?= $empresa ?></div>
    </div>

    <div class="info">
        <b>Emitido por:</b> <?= $usuario ?> |
        <b>Fecha:</b> <?= date('d-m-Y H:i') ?>
    </div>

    <?php if (empty($datos)): ?>
        <p style="text-align:center;">No existen registros para los filtros seleccionados.</p>
        <?php return; ?>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>OT</th>
                <th>Presupuesto</th>
                <th>Recepción</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Equipo</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Ítems</th>
                <th>Estado</th>
                <th>Sucursal</th>
            </tr>
        </thead>

        <tbody>
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
             $i = 1; ?>
            <?php foreach ($datos as $row): ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td class="text-center"><?= $row['idorden_trabajo'] ?></td>
                    <td class="text-center"><?= $row['idpresupuesto_servicio'] ?></td>
                    <td class="text-center"><?= $row['idrecepcion'] ?></td>
                    <td><?= $row['cliente'] ?></td>
                    <td><?= $row['vehiculo'] ?></td>
                    <td><?= $row['equipo'] ?: '-' ?></td>
                    <td class="text-center"><?= date('d-m-Y H:i', strtotime($row['fecha_inicio'])) ?></td>
                    <td class="text-center"><?= $row['fecha_fin'] ? date('d-m-Y H:i', strtotime($row['fecha_fin'])) : '-' ?></td>
                    <td class="text-center"><?= $row['cantidad_items'] ?></td>
                    <td class="text-center"><?= estadoOT($row['estado']) ?></td>
                    <td><?= $row['sucursal'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>
