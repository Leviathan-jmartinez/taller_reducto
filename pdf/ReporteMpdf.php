<?php

use Mpdf\Mpdf;

class ReporteMpdf
{
    public static function desdeHtml(string $html, array $opciones = []): void
    {
        $orientacion = strtoupper($opciones['orientacion'] ?? 'P');
        $formato = $opciones['formato'] ?? 'A4';
        $archivo = $opciones['archivo'] ?? 'reporte.pdf';
        $salida = $opciones['salida'] ?? 'D';

        $mpdf = new Mpdf([
            'format' => $formato . '-' . $orientacion,
            'margin_top' => $opciones['margin_top'] ?? 10,
            'margin_bottom' => $opciones['margin_bottom'] ?? 10,
            'margin_left' => $opciones['margin_left'] ?? 10,
            'margin_right' => $opciones['margin_right'] ?? 10,
            'default_font' => 'dejavusans'
        ]);

        $mpdf->SetTitle($opciones['titulo'] ?? $archivo);
        if (($opciones['estilo_reporte'] ?? false) === true) {
            $html = self::aplicarFormatoReporte($html, $opciones);
        }
        $mpdf->WriteHTML($html);

        if (($opciones['limpiar_buffers'] ?? true) === true) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
        }

        $mpdf->Output($archivo, $salida);
        exit();
    }

    public static function generar(array $opciones): void
    {
        $orientacion = strtoupper($opciones['orientacion'] ?? 'P');
        $formato = $opciones['formato'] ?? 'A4';
        $archivo = $opciones['archivo'] ?? 'reporte.pdf';
        $salida = $opciones['salida'] ?? 'D';

        $mpdf = new Mpdf([
            'format' => $formato . '-' . $orientacion,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'margin_left' => 10,
            'margin_right' => 10,
            'default_font' => 'dejavusans'
        ]);

        $mpdf->SetTitle($opciones['titulo'] ?? 'Reporte');
        $mpdf->SetHTMLFooter(self::footer());
        $mpdf->WriteHTML(self::html($opciones));

        if (($opciones['limpiar_buffers'] ?? true) === true) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
        }

        $mpdf->Output($archivo, $salida);
        exit();
    }

    private static function html(array $opciones): string
    {
        $titulo = self::e($opciones['titulo'] ?? 'Reporte');
        $subtitulo = self::e($opciones['subtitulo'] ?? '');
        $empresa = self::e($opciones['empresa'] ?? 'Empresa');
        $usuario = self::e($opciones['usuario'] ?? '');
        $datos = $opciones['datos'] ?? [];
        $columnas = $opciones['columnas'] ?? [];
        $filtros = $opciones['filtros'] ?? [];
        $resumen = $opciones['resumen'] ?? [];
        $logo = $opciones['logo'] ?? __DIR__ . '/assets/logo.png';

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    color: #263238;
                    font-family: dejavusans, sans-serif;
                    font-size: 9px;
                }

                .header {
                    background: #2f6f6f;
                    color: #fff;
                    margin-bottom: 10px;
                    width: 100%;
                }

                .logo {
                    width: 72px;
                }

                .header td {
                    border: 0;
                    color: #fff;
                    padding: 8px;
                }

                .title {
                    color: #fff;
                    font-size: 18px;
                    font-weight: bold;
                    margin: 0;
                    text-transform: uppercase;
                }

                .subtitle {
                    color: #fff;
                    font-size: 10px;
                    margin-top: 2px;
                }

                .meta {
                    color: #fff;
                    font-size: 8.5px;
                    text-align: right;
                }

                .panel {
                    background: #f4f7f7;
                    border: 1px solid #d6e0e0;
                    margin-bottom: 10px;
                    padding: 6px;
                }

                .panel-title {
                    color: #245f63;
                    font-size: 9px;
                    font-weight: bold;
                    margin-bottom: 4px;
                    text-transform: uppercase;
                }

                .filters td,
                .summary td {
                    border: 0;
                    padding: 2px 6px 2px 0;
                }

                .label {
                    color: #607d8b;
                    font-weight: bold;
                }

                table.data {
                    border-collapse: collapse;
                    width: 100%;
                }

                table.data th {
                    background: #2f6f6f;
                    border: 1px solid #2f6f6f;
                    color: #fff;
                    font-size: 8.5px;
                    padding: 5px 4px;
                    text-align: left;
                }

                table.data td {
                    border: 1px solid #d8e0e0;
                    padding: 4px;
                    vertical-align: top;
                }

                table.data tbody tr:nth-child(even) td {
                    background: #f8fbfb;
                }

                .center {
                    text-align: center;
                }

                .right {
                    text-align: right;
                }

                .empty {
                    color: #607d8b;
                    margin-top: 20px;
                    text-align: center;
                }
            </style>
        </head>

        <body>
            <table class="header">
                <tr>
                    <td style="width: 90px;">
                        <?php if (is_file($logo)) { ?>
                            <img src="<?= self::e($logo) ?>" class="logo">
                        <?php } ?>
                    </td>
                    <td>
                        <div class="title"><?= $titulo ?></div>
                        <?php if ($subtitulo !== '') { ?>
                            <div class="subtitle"><?= $subtitulo ?></div>
                        <?php } ?>
                        <div class="subtitle"><?= $empresa ?></div>
                    </td>
                    <td class="meta" style="width: 210px;">
                        <strong>Emitido por:</strong> <?= $usuario ?><br>
                        <strong>Fecha:</strong> <?= date('d/m/Y H:i') ?>
                    </td>
                </tr>
            </table>

            <?php if (!empty($filtros)) { ?>
                <div class="panel">
                    <div class="panel-title">Filtros aplicados</div>
                    <table class="filters">
                        <tr>
                            <?php foreach ($filtros as $label => $valor) { ?>
                                <td><span class="label"><?= self::e($label) ?>:</span> <?= self::e(self::texto($valor)) ?></td>
                            <?php } ?>
                        </tr>
                    </table>
                </div>
            <?php } ?>

            <?php if (!empty($resumen)) { ?>
                <div class="panel">
                    <div class="panel-title">Resumen</div>
                    <table class="summary">
                        <tr>
                            <?php foreach ($resumen as $label => $valor) { ?>
                                <td><span class="label"><?= self::e($label) ?>:</span> <?= self::e(self::texto($valor)) ?></td>
                            <?php } ?>
                        </tr>
                    </table>
                </div>
            <?php } ?>

            <?php if (empty($datos)) { ?>
                <div class="empty">No existen registros para los filtros seleccionados.</div>
            <?php } else { ?>
                <table class="data">
                    <thead>
                        <tr>
                            <?php foreach ($columnas as $columna) { ?>
                                <th class="<?= self::e($columna['align'] ?? '') ?>"><?= self::e($columna['label'] ?? '') ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datos as $index => $fila) { ?>
                            <tr>
                                <?php foreach ($columnas as $columna) { ?>
                                    <td class="<?= self::e($columna['align'] ?? '') ?>">
                                        <?= self::e(self::valorColumna($fila, $columna, $index)) ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </body>

        </html>
        <?php
        return ob_get_clean();
    }

    private static function valorColumna(array $fila, array $columna, int $index): string
    {
        if (isset($columna['valor']) && is_callable($columna['valor'])) {
            return (string)$columna['valor']($fila, $index);
        }

        $key = $columna['key'] ?? '';
        return self::texto($fila[$key] ?? '');
    }

    private static function texto($valor): string
    {
        if ($valor === null || $valor === '') {
            return '-';
        }

        return (string)$valor;
    }

    private static function footer(): string
    {
        return '<table width="100%" style="border-top:1px solid #d6e0e0; color:#607d8b; font-size:8px; padding-top:4px;">
            <tr>
                <td>Documento generado por el sistema</td>
                <td style="text-align:right;">Pagina {PAGENO} de {nbpg}</td>
            </tr>
        </table>';
    }

    private static function e($valor): string
    {
        return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
    }

    private static function aplicarFormatoReporte(string $html, array $opciones): string
    {
        $titulo = self::extraerTituloReporte($html);
        $empresa = self::e($opciones['empresa'] ?? 'Empresa');
        $usuario = self::e($opciones['usuario'] ?? '');
        $logo = $opciones['logo'] ?? __DIR__ . '/assets/logo.png';

        $html = preg_replace('/<div\s+class=["\']header["\'][\s\S]*?<\/div>\s*/i', '', $html, 1);
        $html = preg_replace('/<div\s+class=["\']info["\'][\s\S]*?<\/div>\s*/i', '', $html, 1);

        $cabecera = self::cabeceraReporteHtml($titulo, $empresa, $usuario, $logo);
        $css = self::cssReportePedido();

        if (stripos($html, '</head>') !== false) {
            $html = str_ireplace('</head>', $css . '</head>', $html);
        } else {
            $html = $css . $html;
        }

        if (stripos($html, '<body>') !== false) {
            $html = str_ireplace('<body>', '<body>' . $cabecera, $html);
        } else {
            $html = $cabecera . $html;
        }

        return $html;
    }

    private static function extraerTituloReporte(string $html): string
    {
        if (preg_match('/<div\s+class=["\']header["\'][\s\S]*?<h2[^>]*>([\s\S]*?)<\/h2>/i', $html, $m)) {
            return trim(strip_tags($m[1]));
        }

        if (preg_match('/<h2[^>]*>([\s\S]*?)<\/h2>/i', $html, $m)) {
            return trim(strip_tags($m[1]));
        }

        return 'REPORTE';
    }

    private static function cabeceraReporteHtml(string $titulo, string $empresa, string $usuario, string $logo): string
    {
        $logoHtml = is_file($logo)
            ? '<img src="' . self::e($logo) . '" height="50">'
            : '';

        return '
            <table class="reporte-header">
                <tr>
                    <td width="20%" align="left" style="padding:8px;">' . $logoHtml . '</td>
                    <td width="50%" align="center">
                        <h2 style="margin:0;">' . self::e($titulo) . '</h2>
                        <div class="reporte-empresa">' . $empresa . '</div>
                    </td>
                    <td width="30%" align="right" style="padding:8px; font-size:9px;">
                        <strong>Emitido por:</strong> ' . $usuario . '<br>
                        <strong>Fecha:</strong> ' . date('d/m/Y H:i') . '
                    </td>
                </tr>
            </table>
        ';
    }

    private static function cssReportePedido(): string
    {
        return '
            <style>
                body {
                    font-family: DejaVu Sans, sans-serif;
                    font-size: 10px;
                    color: #333;
                }

                .reporte-header {
                    background: #2f6f6f;
                    color: #fff;
                    margin-bottom: 10px;
                    width: 100%;
                    border-collapse: collapse;
                }

                .reporte-header td {
                    border: 0;
                    color: #fff;
                }

                .reporte-header h2 {
                    color: #fff;
                    font-size: 17px;
                    letter-spacing: 0;
                    text-transform: uppercase;
                }

                .reporte-empresa {
                    font-size: 9px;
                    margin-top: 2px;
                }

                table {
                    border-collapse: collapse;
                    width: 100%;
                }

                th,
                td {
                    border: 1px solid #ddd;
                    padding: 5px;
                }

                th {
                    background: #2f6f6f;
                    color: #fff;
                    font-weight: bold;
                }

                tbody tr:nth-child(even) td {
                    background: #f7fafa;
                }

                .text-center,
                .center {
                    text-align: center;
                }

                .text-right,
                .right {
                    text-align: right;
                }
            </style>
        ';
    }
}
