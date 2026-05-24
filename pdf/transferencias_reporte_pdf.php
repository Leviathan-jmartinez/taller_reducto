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
use Dompdf\Dompdf;
?>

<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 10px;
    }

    h2, h3 {
        margin: 0;
        padding: 0;
    }

    .header {
        width: 100%;
        margin-bottom: 10px;
    }

    .header td {
        border: none;
        padding: 4px;
    }

    .info {
        margin-bottom: 8px;
        font-size: 9px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid #000;
        padding: 4px;
    }

    th {
        background: #eaeaea;
        text-align: center;
    }

    .right { text-align: right; }
    .center { text-align: center; }
</style>

<table class="header">
    <tr>
        <td width="70%">
            <h2><?= htmlspecialchars($empresa) ?></h2>
            <div class="info">
                Reporte de Transferencias<br>
                Generado por: <?= htmlspecialchars($usuario) ?><br>
                Fecha: <?= date('d/m/Y H:i') ?>
            </div>
        </td>
        <td width="30%" class="right">
            <h3>TRANSFERENCIAS</h3>
        </td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Origen</th>
            <th>Destino</th>
            <th>Estado</th>
            <th>N° Remisión</th>
            <th>Motivo</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($datos)): ?>
            <?php foreach ($datos as $r): ?>
                <tr>
                    <td class="center">
                        <?= !empty($r['fecha']) ? date('d/m/Y H:i', strtotime($r['fecha'])) : '' ?>
                    </td>
                    <td><?= $r['suc_origen'] ?? '' ?></td>
                    <td><?= $r['suc_destino'] ?? '' ?></td>
                    <td class="center"><?= $r['estado'] ?? '' ?></td>
                    <td class="center"><?= $r['nro_remision'] ?? '-' ?></td>
                    <td><?= $r['motivo_remision'] ?? '' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="center">No hay datos para mostrar</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
