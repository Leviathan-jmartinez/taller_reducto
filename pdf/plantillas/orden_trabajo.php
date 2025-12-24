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
                <?= date('d/m/Y', strtotime($cabecera['fecha_inicio'])) ?>
            </td>
        </tr>
    </table>



    <!-- INFO -->
    <div class="grid">
        <div class="box">
            <h3>Cliente</h3>
            <?= $cabecera['nombre_cliente'] . ' ' . $cabecera['apellido_cliente'] ?><br>
            <strong>Tel:</strong> <?= $cabecera['celular_cliente'] ?? '-' ?>
        </div>

        <div class="box">
            <h3>Vehículo</h3>
            <?= $cabecera['modelo'] ?><br>
            <strong>Placa:</strong> <?= $cabecera['placa'] ?><br>
            <strong>Km:</strong> <?= number_format($cabecera['kilometraje'], 0, ',', '.') ?>
        </div>

        <div class="box">
            <h3>Orden</h3>
            <strong>Estado:</strong>
            <?= ['', 'Abierta', 'En proceso', 'Terminada', 'Facturada'][$cabecera['estado']] ?><br>
            <strong>Técnico:</strong>
            <?= $cabecera['tecnico_nombre']
                ? $cabecera['tecnico_nombre'] . ' ' . $cabecera['tecnico_apellido']
                : 'No asignado' ?>
        </div>
    </div>

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
            <?php foreach ($detalle as $d): ?>
                <tr>
                    <td><?= $d['desc_articulo'] ?></td>
                    <td class="center"><?= $d['cantidad'] ?></td>
                    <td class="right"><?= number_format($d['precio_unitario'], 0, ',', '.') ?></td>
                    <td class="right"><?= number_format($d['subtotal'], 0, ',', '.') ?></td>
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
            <span>Gs. <?= number_format($cabecera['subtotal'], 0, ',', '.') ?></span>
        </div>

        <div class="totales-linea">
            <span>Descuentos</span>
            <span>- Gs. <?= number_format($cabecera['total_descuento'], 0, ',', '.') ?></span>
        </div>

        <div class="totales-final">
            TOTAL ESTIMADO: Gs. <?= number_format($cabecera['total_final'], 0, ',', '.') ?>
        </div>
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