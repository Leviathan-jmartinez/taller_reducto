<?php
require_once __DIR__ . "/../modelos/reportesModelo.php";

class reporteControlador extends reportesModelo
{
    private function acceso_denegado_json()
    {
        return json_encode([
            "error" => "Acceso no autorizado",
            "data" => [],
            "resumen" => []
        ]);
    }

    private function imprimir_mpdf_html($html, $archivo, $orientacion = 'P')
    {
        require_once __DIR__ . "/../vendor/autoload.php";
        require_once __DIR__ . "/../pdf/ReporteMpdf.php";

        ReporteMpdf::desdeHtml($html, [
            'archivo' => $archivo,
            'orientacion' => strtoupper(substr($orientacion, 0, 1)) === 'L' ? 'L' : 'P',
            'salida' => 'D',
            'estilo_reporte' => true,
            'margin_bottom' => 16,
            'empresa' => $_SESSION['empresa_nombre'] ?? 'Empresa',
            'usuario' => trim(($_SESSION['nombre_str'] ?? '') . ' ' . ($_SESSION['apellido_str'] ?? ''))
        ]);
    }

    private function convertir_utf16le_excel($texto)
    {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($texto, 'UTF-16LE', 'UTF-8');
        }

        if (function_exists('iconv')) {
            $convertido = iconv('UTF-8', 'UTF-16LE//IGNORE', $texto);
            return $convertido !== false ? $convertido : $texto;
        }

        return $texto;
    }

    private function escribir_csv_excel($salida, $fila)
    {
        $tmp = fopen('php://temp', 'w+');
        fputcsv($tmp, $fila, ';', '"', "\\");
        rewind($tmp);
        $linea = stream_get_contents($tmp);
        fclose($tmp);

        fwrite($salida, $this->convertir_utf16le_excel($linea));
    }

    public function listar_sucursales_controlador()
    {
        $sql = mainModel::conectar()->query("SELECT id_sucursal, suc_descri FROM sucursales");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar_categorias_controlador()
    {
        $sql = mainModel::conectar()->query("SELECT id_categoria, cat_descri FROM categorias");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar_proveedores_controlador()
    {
        $sql = mainModel::conectar()->query("SELECT idproveedores, razon_social FROM proveedores ORDER BY razon_social ASC");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar_proveedores_json_controlador()
    {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($this->listar_proveedores_controlador(), JSON_UNESCAPED_UNICODE);
    }

    public function listar_clientes_controlador()
    {
        $sql = mainModel::conectar()->query("
            SELECT id_cliente, doc_number, nombre_cliente, apellido_cliente
            FROM clientes
            ORDER BY apellido_cliente, nombre_cliente
        ");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar_articulos_controlador()
    {
        $sql = mainModel::conectar()->query("
            SELECT id_articulo, codigo, desc_articulo
            FROM articulos
            ORDER BY desc_articulo ASC
        ");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listar_cargos_controlador()
    {
        $sql = mainModel::conectar()->query("SELECT idcargos, descripcion FROM cargos");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar_empleados_controlador()
    {
        $sql = mainModel::conectar()->query("SELECT idempleados, nombre, apellido FROM empleados WHERE estado = 1 ORDER BY apellido, nombre");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar_modelos_controlador()
    {
        $sql = mainModel::conectar()->query("SELECT id_modeloauto, mod_descri FROM modelo_auto WHERE estado = 1 ORDER BY mod_descri ASC");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    private function config_referenciales()
    {
        return [
            "proveedores" => [
                "titulo" => "Proveedores",
                "permiso" => "reportes.proveedores.ver",
                "modelo" => "reporte_proveedores_modelo",
                "resumen" => "resumen_proveedores_modelo",
                "orientacion" => "P",
                "estado_key" => "estado",
                "columnas" => [
                    ["key" => "razon_social", "label" => "Razon Social"],
                    ["key" => "ruc", "label" => "RUC"],
                    ["key" => "telefono", "label" => "Telefono"],
                    ["key" => "correo", "label" => "Correo"],
                    ["key" => "direccion", "label" => "Direccion"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "clientes" => [
                "titulo" => "Clientes",
                "permiso" => "reportes.clientes.ver",
                "modelo" => "reporte_clientes_modelo",
                "resumen" => "resumen_clientes_modelo",
                "orientacion" => "P",
                "estado_key" => "estado_cliente",
                "columnas" => [
                    ["key" => "doc_number", "label" => "Documento"],
                    ["key" => "nombre_cliente", "label" => "Nombre"],
                    ["key" => "apellido_cliente", "label" => "Apellido"],
                    ["key" => "ciudad", "label" => "Ciudad"],
                    ["key" => "celular_cliente", "label" => "Celular"],
                    ["key" => "estado_cliente", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "vehiculos" => [
                "titulo" => "Vehiculos",
                "permiso" => "reportes.vehiculos.ver",
                "modelo" => "reporte_vehiculos_modelo",
                "resumen" => "resumen_vehiculos_modelo",
                "orientacion" => "L",
                "estado_key" => "estado",
                "columnas" => [
                    ["key" => "placa", "label" => "Placa"],
                    ["key" => "modelo", "label" => "Modelo"],
                    ["key" => "cliente", "label" => "Cliente"],
                    ["key" => "doc_number", "label" => "Documento"],
                    ["key" => "version", "label" => "Version"],
                    ["key" => "anho", "label" => "Anho"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "sucursales" => [
                "titulo" => "Sucursales",
                "permiso" => "reportes.sucursales.ver",
                "modelo" => "reporte_sucursales_modelo",
                "resumen" => "resumen_sucursales_modelo",
                "orientacion" => "P",
                "estado_key" => "estado",
                "columnas" => [
                    ["key" => "suc_descri", "label" => "Sucursal"],
                    ["key" => "empresa", "label" => "Empresa"],
                    ["key" => "suc_direccion", "label" => "Direccion"],
                    ["key" => "suc_telefono", "label" => "Telefono"],
                    ["key" => "nro_establecimiento", "label" => "Est."],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "articulos" => [
                "titulo" => "Articulos",
                "permiso" => "reportes.articulos.ver",
                "modelo" => "reporte_articulos_simple_modelo",
                "resumen" => "resumen_articulos_simple_modelo",
                "orientacion" => "L",
                "estado_key" => "estado",
                "columnas" => [
                    ["key" => "codigo", "label" => "Codigo"],
                    ["key" => "desc_articulo", "label" => "Articulo"],
                    ["key" => "categoria", "label" => "Categoria"],
                    ["key" => "marca", "label" => "Marca"],
                    ["key" => "proveedor", "label" => "Proveedor"],
                    ["key" => "precio_venta", "label" => "Precio Venta", "tipo" => "numero"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "empleados" => [
                "titulo" => "Empleados",
                "permiso" => "reportes.empleados.ver",
                "modelo" => "reporte_empleados_modelo",
                "resumen" => "resumen_empleados_modelo",
                "orientacion" => "L",
                "estado_key" => "estado",
                "columnas" => [
                    ["key" => "nro_cedula", "label" => "Cedula"],
                    ["key" => "nombre", "label" => "Nombre"],
                    ["key" => "apellido", "label" => "Apellido"],
                    ["key" => "cargo", "label" => "Cargo"],
                    ["key" => "sucursal", "label" => "Sucursal"],
                    ["key" => "celular", "label" => "Celular"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "marcas" => [
                "titulo" => "Marcas",
                "permiso" => "reportes.marcas.ver",
                "modelo" => "reporte_marcas_modelo",
                "resumen" => "resumen_marcas_modelo",
                "orientacion" => "P",
                "columnas" => [
                    ["key" => "id_marcas", "label" => "ID"],
                    ["key" => "mar_descri", "label" => "Marca"]
                ]
            ],
            "categorias" => [
                "titulo" => "Categorias",
                "permiso" => "reportes.categorias.ver",
                "modelo" => "reporte_categorias_modelo",
                "resumen" => "resumen_categorias_modelo",
                "orientacion" => "P",
                "columnas" => [
                    ["key" => "id_categoria", "label" => "ID"],
                    ["key" => "cat_descri", "label" => "Categoria"]
                ]
            ],
            "usuarios" => [
                "titulo" => "Usuarios",
                "permiso" => "reportes.usuarios.ver",
                "modelo" => "reporte_usuarios_modelo",
                "resumen" => "resumen_usuarios_modelo",
                "orientacion" => "L",
                "estado_key" => "usu_estado",
                "columnas" => [
                    ["key" => "usu_nombre", "label" => "Nombre"],
                    ["key" => "usu_apellido", "label" => "Apellido"],
                    ["key" => "usu_nick", "label" => "Usuario"],
                    ["key" => "usu_email", "label" => "Correo"],
                    ["key" => "usu_telefono", "label" => "Telefono"],
                    ["key" => "sucursal", "label" => "Sucursal"],
                    ["key" => "usu_estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ]
        ];
    }

    private function filtros_referenciales()
    {
        $buscar = trim($_POST['buscar'] ?? '');

        return [
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => mainModel::limpiar_string($buscar),
            "codigo" => '',
            "categoria" => mainModel::limpiar_string($_POST['categoria'] ?? 0),
            "proveedor" => 0,
            "modelo" => 0,
            "sucursal" => mainModel::limpiar_string($_POST['sucursal'] ?? 0),
            "cargo" => 0,
            "pagina" => max(1, (int)($_POST['pagina'] ?? 1)),
            "por_pagina" => min(500, max(25, (int)($_POST['por_pagina'] ?? 50)))
        ];
    }

    private function preparar_datos_referenciales($tipo, $config, $filtros)
    {
        $datos = reportesModelo::{$config[$tipo]['modelo']}($filtros);

        return [
            "datos" => $datos
        ];
    }

    private function resumen_referencial_desde_datos($datos, $configTipo)
    {
        $resumen = [
            "total" => count($datos)
        ];

        if (!empty($configTipo['estado_key'])) {
            $resumen['activos'] = 0;
            $resumen['inactivos'] = 0;

            foreach ($datos as $row) {
                if ((int)($row[$configTipo['estado_key']] ?? 0) === 1) {
                    $resumen['activos']++;
                } else {
                    $resumen['inactivos']++;
                }
            }
        }

        return $resumen;
    }

    private function valor_reporte_referencial($row, $columna)
    {
        $valor = $row[$columna['key']] ?? '';

        if (($columna['tipo'] ?? '') === 'estado') {
            return ((int)$valor === 1) ? 'Activo' : 'Inactivo';
        }

        if (($columna['tipo'] ?? '') === 'numero') {
            return number_format((float)$valor, 0, ',', '.');
        }

        return ($valor !== null && $valor !== '') ? (string)$valor : '-';
    }

    public function reporte_referenciales_controlador()
    {
        $tipo = mainModel::limpiar_string($_POST['tipo_referencial'] ?? '');
        $config = $this->config_referenciales();

        if (!isset($config[$tipo])) {
            return json_encode(["error" => "Informe referencial no valido", "data" => [], "resumen" => []]);
        }

        if (!mainModel::tienePermiso($config[$tipo]['permiso'])) {
            return $this->acceso_denegado_json();
        }

        $filtros = $this->filtros_referenciales();
        $preparado = $this->preparar_datos_referenciales($tipo, $config, $filtros);
        $data = $preparado['datos'];
        $total = count($data);
        $porPagina = $filtros['por_pagina'];
        $totalPaginas = max(1, (int)ceil($total / $porPagina));
        $pagina = min($filtros['pagina'], $totalPaginas);
        $inicio = ($pagina - 1) * $porPagina;
        $dataPagina = array_slice($data, $inicio, $porPagina);
        $resumen = $this->resumen_referencial_desde_datos($data, $config[$tipo]);

        return json_encode([
            "data" => $dataPagina,
            "resumen" => $resumen,
            "columnas" => $config[$tipo]['columnas'],
            "titulo" => $config[$tipo]['titulo'],
            "paginacion" => [
                "pagina" => $pagina,
                "por_pagina" => $porPagina,
                "total" => $total,
                "total_paginas" => $totalPaginas,
                "desde" => $total > 0 ? $inicio + 1 : 0,
                "hasta" => min($inicio + $porPagina, $total)
            ]
        ]);
    }

    public function imprimir_reporte_referenciales_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        $tipo = mainModel::limpiar_string($_POST['tipo_referencial'] ?? '');
        $config = $this->config_referenciales();

        if (!isset($config[$tipo]) || !mainModel::tienePermiso($config[$tipo]['permiso'])) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = $this->filtros_referenciales();
        $preparado = $this->preparar_datos_referenciales($tipo, $config, $filtros);
        $datos = array_slice($preparado['datos'], 0, 500);
        $resumen = $this->resumen_referencial_desde_datos($preparado['datos'], $config[$tipo]);
        $columnas = $config[$tipo]['columnas'];
        $titulo = "Informe Referencial - " . $config[$tipo]['titulo'];
        $anchosColumnas = [
            "articulos" => ["4%", "9%", "30%", "12%", "10%", "20%", "10%", "5%"]
        ];
        $anchos = $anchosColumnas[$tipo] ?? [];
        $claseTabla = "referencial-table" . ($tipo === "articulos" ? " referencial-table-compact" : "");

        ob_start();
        ?>
        <style>
            .referencial-table {
                border-collapse: collapse;
                font-size: 8.4px;
                line-height: 1.25;
                width: 100%;
            }

            .referencial-table-compact {
                font-size: 7.8px;
            }

            .referencial-table thead {
                display: table-header-group;
            }

            .referencial-table tr {
                page-break-inside: avoid;
            }

            .referencial-table th {
                background: #eaf0f6;
                border: 1px solid #cfd8e3;
                color: #001d4a;
                font-weight: bold;
                padding: 4px 5px;
                text-align: center;
            }

            .referencial-table td {
                border: 1px solid #d9dee5;
                color: #172033;
                padding: 3.5px 5px;
                vertical-align: top;
            }

            .referencial-table tbody tr:nth-child(even) td {
                background: #f8fafc;
            }

            .report-bottom-space {
                height: 7mm;
            }
        </style>
        <h3><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h3>
        <table class="<?= $claseTabla ?>" cellspacing="0">
            <?php if (!empty($anchos)): ?>
                <colgroup>
                    <?php foreach ($anchos as $ancho): ?>
                        <col style="width: <?= $ancho ?>;">
                    <?php endforeach; ?>
                </colgroup>
            <?php endif; ?>
            <thead>
                <tr>
                    <th>#</th>
                    <?php foreach ($columnas as $columna): ?>
                        <th><?= htmlspecialchars($columna['label'], ENT_QUOTES, 'UTF-8') ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datos)): ?>
                    <tr>
                        <td colspan="<?= count($columnas) + 1 ?>" style="text-align:center;">Sin registros</td>
                    </tr>
                <?php else: ?>
                    <?php $i = 1;
                    foreach ($datos as $row): ?>
                        <tr>
                            <td style="text-align:center;"><?= $i++ ?></td>
                            <?php foreach ($columnas as $columna): ?>
                                <td<?= (($columna['tipo'] ?? '') === 'numero') ? ' style="text-align:right;"' : '' ?>><?= htmlspecialchars($this->valor_reporte_referencial($row, $columna), ENT_QUOTES, 'UTF-8') ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="report-bottom-space"></div>
        <?php
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_referenciales_" . $tipo . ".pdf", $config[$tipo]['orientacion']);
        exit();
    }

    public function exportar_reporte_referenciales_csv_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        $tipo = mainModel::limpiar_string($_POST['tipo_referencial'] ?? '');
        $config = $this->config_referenciales();

        if (!isset($config[$tipo]) || !mainModel::tienePermiso($config[$tipo]['permiso'])) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = $this->filtros_referenciales();
        $preparado = $this->preparar_datos_referenciales($tipo, $config, $filtros);
        $columnas = $config[$tipo]['columnas'];

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: text/csv; charset=UTF-16LE');
        header('Content-Disposition: attachment; filename="reporte_referenciales_' . $tipo . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xFF\xFE";
        echo $this->convertir_utf16le_excel("sep=;\r\n");

        $salida = fopen('php://output', 'w');
        $this->escribir_csv_excel($salida, array_merge(['#'], array_column($columnas, 'label')));

        $i = 1;
        foreach ($preparado['datos'] as $row) {
            $fila = [$i++];
            foreach ($columnas as $columna) {
                $fila[] = $this->valor_reporte_referencial($row, $columna);
            }
            $this->escribir_csv_excel($salida, $fila);
        }

        fclose($salida);
        exit();
    }

    private function config_movimientos()
    {
        return [
            "pedidos" => [
                "titulo" => "Pedidos de Compra",
                "permiso" => "reportes.pedidos.ver",
                "modelo" => "reporte_pedidos_modelo",
                "detalle_modelo" => "reporte_pedidos_detalle_modelo",
                "args" => ["desde", "hasta", "estado_int", "sucursal_int"],
                "detalle_args" => ["desde", "hasta", "estado_int", "sucursal_int"],
                "fecha" => "fecha",
                "estado" => "estado",
                "estado_labels" => [0 => "Anulado", 1 => "Pendiente", 2 => "Procesado"],
                "importe" => null,
                "detalle_importe" => null,
                "entidad" => "usuario",
                "articulo" => true,
                "columnas" => [
                    ["key" => "idpedido_cabecera", "label" => "Nro"],
                    ["key" => "fecha", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "usuario", "label" => "Usuario"],
                    ["key" => "sucursal", "label" => "Sucursal"],
                    ["key" => "cantidad_items", "label" => "Items", "tipo" => "numero"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ],
                "detalle_columnas" => [
                    ["key" => "idpedido_cabecera", "label" => "Pedido"],
                    ["key" => "fecha", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "sucursal", "label" => "Sucursal"],
                    ["key" => "codigo", "label" => "Codigo"],
                    ["key" => "articulo", "label" => "Articulo"],
                    ["key" => "cantidad", "label" => "Cantidad", "tipo" => "numero"],
                    ["key" => "stock_actual", "label" => "Stock", "tipo" => "numero"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "presupuestos_compra" => [
                "titulo" => "Presupuestos de Compra",
                "permiso" => "reportes.presupuestos_compra.ver",
                "modelo" => "reporte_presupuestos_modelo",
                "detalle_modelo" => "reporte_presupuestos_detalle_modelo",
                "args" => ["desde", "hasta", "estado_int", "sucursal_int"],
                "detalle_args" => ["desde", "hasta", "estado_int", "sucursal_int"],
                "fecha" => "fecha",
                "estado" => "estado",
                "estado_labels" => [0 => "Anulado", 1 => "Pendiente", 2 => "Procesado"],
                "importe" => "total",
                "detalle_importe_unico" => "total",
                "detalle_importe_unico_id" => "idpresupuesto_compra",
                "entidad" => "proveedor",
                "proveedor" => true,
                "articulo" => true,
                "columnas" => [
                    ["key" => "idpresupuesto_compra", "label" => "Nro"],
                    ["key" => "fecha", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "proveedor", "label" => "Proveedor"],
                    ["key" => "sucursal", "label" => "Sucursal"],
                    ["key" => "cantidad_items", "label" => "Items", "tipo" => "numero"],
                    ["key" => "total", "label" => "Total", "tipo" => "moneda"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ],
                "detalle_columnas" => [
                    ["key" => "idpresupuesto_compra", "label" => "Presupuesto"],
                    ["key" => "fecha", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "proveedor", "label" => "Proveedor"],
                    ["key" => "codigo", "label" => "Codigo"],
                    ["key" => "articulo", "label" => "Articulo"],
                    ["key" => "cantidad", "label" => "Cantidad", "tipo" => "numero"],
                    ["key" => "precio", "label" => "Precio", "tipo" => "moneda"],
                    ["key" => "subtotal", "label" => "Subtotal", "tipo" => "moneda"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "ordenes_compra" => [
                "titulo" => "Ordenes de Compra",
                "permiso" => "reportes.ordenes_compra.ver",
                "modelo" => "reporte_ordenes_compra_modelo",
                "detalle_modelo" => "reporte_ordenes_compra_detalle_modelo",
                "args" => ["desde", "hasta", "estado_int", "sucursal_int"],
                "detalle_args" => ["desde", "hasta", "estado_int", "sucursal_int"],
                "fecha" => "fecha",
                "estado" => "estado",
                "estado_labels" => [0 => "Anulado", 1 => "Pendiente", 2 => "Procesado"],
                "importe" => "total",
                "entidad" => "proveedor",
                "proveedor" => true,
                "articulo" => true,
                "columnas" => [
                    ["key" => "idorden_compra", "label" => "Nro"],
                    ["key" => "fecha", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "proveedor", "label" => "Proveedor"],
                    ["key" => "sucursal", "label" => "Sucursal"],
                    ["key" => "cantidad_items", "label" => "Items", "tipo" => "numero"],
                    ["key" => "cantidad_pendiente", "label" => "Pendiente", "tipo" => "numero"],
                    ["key" => "total", "label" => "Total", "tipo" => "moneda"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ],
                "detalle_columnas" => [
                    ["key" => "idorden_compra", "label" => "Orden"],
                    ["key" => "fecha", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "proveedor", "label" => "Proveedor"],
                    ["key" => "codigo", "label" => "Codigo"],
                    ["key" => "articulo", "label" => "Articulo"],
                    ["key" => "cantidad", "label" => "Cantidad", "tipo" => "numero"],
                    ["key" => "cantidad_pendiente", "label" => "Pendiente", "tipo" => "numero"],
                    ["key" => "precio_unitario", "label" => "Precio", "tipo" => "moneda"],
                    ["key" => "subtotal", "label" => "Subtotal", "tipo" => "moneda"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "compras" => [
                "titulo" => "Compras",
                "permiso" => "reportes.compras.ver",
                "modelo" => "reporte_compras_modelo",
                "detalle_modelo" => "reporte_compras_detalle_modelo",
                "args" => ["desde", "hasta", "estado_int", "sucursal_int"],
                "detalle_args" => ["desde", "hasta", "estado_int", "sucursal_int"],
                "fecha" => "fecha_creacion",
                "estado" => "estado",
                "estado_labels" => [0 => "Anulado", 1 => "Activo", 2 => "Procesado", 3 => "Con diferencia", 4 => "Regularizada con NC"],
                "importe" => "total_compra",
                "detalle_importe_unico" => "total_compra",
                "detalle_importe_unico_id" => "idcompra_cabecera",
                "entidad" => "proveedor",
                "proveedor" => true,
                "articulo" => true,
                "columnas" => [
                    ["key" => "idcompra_cabecera", "label" => "Nro"],
                    ["key" => "fecha_creacion", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "nro_factura", "label" => "Factura"],
                    ["key" => "proveedor", "label" => "Proveedor"],
                    ["key" => "condicion", "label" => "Condicion"],
                    ["key" => "sucursal", "label" => "Sucursal"],
                    ["key" => "cantidad_facturada_total", "label" => "Facturado", "tipo" => "numero"],
                    ["key" => "cantidad_total", "label" => "Recibido", "tipo" => "numero"],
                    ["key" => "cantidad_diferencia_total", "label" => "Dif.", "tipo" => "numero"],
                    ["key" => "total_compra", "label" => "Total", "tipo" => "moneda"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ],
                "detalle_columnas" => [
                    ["key" => "idcompra_cabecera", "label" => "Compra"],
                    ["key" => "fecha_factura", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "nro_factura", "label" => "Factura"],
                    ["key" => "proveedor", "label" => "Proveedor"],
                    ["key" => "codigo", "label" => "Codigo"],
                    ["key" => "articulo", "label" => "Articulo"],
                    ["key" => "cantidad_facturada", "label" => "Facturada", "tipo" => "numero"],
                    ["key" => "cantidad_recibida", "label" => "Recibida", "tipo" => "numero"],
                    ["key" => "cantidad_diferencia", "label" => "Dif.", "tipo" => "numero"],
                    ["key" => "precio_unitario", "label" => "Precio", "tipo" => "moneda"],
                    ["key" => "subtotal", "label" => "Subtotal", "tipo" => "moneda"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "libro_compras" => [
                "titulo" => "Libro de Compras",
                "permiso" => "reportes.libro_compras.ver",
                "modelo" => "reporte_libro_compras_modelo",
                "args" => ["desde", "hasta", "proveedor_int", "estado_int", "sucursal_int"],
                "fecha" => "fecha",
                "estado" => "estado",
                "estado_labels" => [0 => "Anulado", 1 => "Activo"],
                "importe" => "total",
                "entidad" => "proveedor_nombre",
                "proveedor" => true,
                "columnas" => [
                    ["key" => "fecha", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "tipo_comprobante", "label" => "Tipo"],
                    ["key" => "serie", "label" => "Serie"],
                    ["key" => "nro_comprobante", "label" => "Comprobante"],
                    ["key" => "proveedor_nombre", "label" => "Proveedor"],
                    ["key" => "proveedor_ruc", "label" => "RUC"],
                    ["key" => "exenta", "label" => "Exenta", "tipo" => "moneda"],
                    ["key" => "gravada_5", "label" => "Gravada 5%", "tipo" => "moneda"],
                    ["key" => "iva_5", "label" => "IVA 5%", "tipo" => "moneda"],
                    ["key" => "gravada_10", "label" => "Gravada 10%", "tipo" => "moneda"],
                    ["key" => "iva_10", "label" => "IVA 10%", "tipo" => "moneda"],
                    ["key" => "total", "label" => "Total", "tipo" => "moneda"],
                    ["key" => "sucursal", "label" => "Sucursal"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "transferencias" => [
                "titulo" => "Transferencias",
                "permiso" => "reportes.transferencias.ver",
                "modelo" => "reporte_transferencias_modelo",
                "args" => ["filtros_array"],
                "fecha" => "fecha",
                "estado" => "estado",
                "estado_labels" => [
                    "en_transito" => "Pendiente de recibir",
                    "recibido" => "Recibido",
                    "recibido_parcial" => "Recibido parcial",
                    "anulado" => "Anulado"
                ],
                "importe" => null,
                "entidad" => "suc_origen",
                "articulo" => true,
                "columnas" => [
                    ["key" => "idtransferencia", "label" => "Nro"],
                    ["key" => "fecha", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "suc_origen", "label" => "Origen"],
                    ["key" => "suc_destino", "label" => "Destino"],
                    ["key" => "nro_remision", "label" => "Remision"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "movimientos_stock" => [
                "titulo" => "Movimientos de Stock",
                "permiso" => "reportes.movimientos_stock.ver",
                "modelo" => "reporte_movimientos_stock_modelo",
                "args" => ["filtros_array"],
                "fecha" => "MovStockFechaHora",
                "estado" => "TipoMovStockId",
                "importe" => "importe_costo",
                "entidad" => "desc_articulo",
                "articulo" => true,
                "columnas" => [
                    ["key" => "MovStockFechaHora", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "sucursal", "label" => "Sucursal"],
                    ["key" => "TipoMovStockId", "label" => "Tipo"],
                    ["key" => "naturaleza_movimiento", "label" => "Naturaleza"],
                    ["key" => "desc_articulo", "label" => "Articulo"],
                    ["key" => "MovStockCantidad", "label" => "Cantidad", "tipo" => "numero"],
                    ["key" => "MovStockCosto", "label" => "Costo Unit.", "tipo" => "moneda"],
                    ["key" => "MovStockPrecioVenta", "label" => "Precio Venta Unit.", "tipo" => "moneda"],
                    ["key" => "importe_costo", "label" => "Importe Costo", "tipo" => "moneda"],
                    ["key" => "usuario", "label" => "Usuario"]
                ]
            ],
            "kardex_articulo" => [
                "titulo" => "Kardex de Articulo",
                "permiso" => "reportes.movimientos_stock.ver",
                "modelo" => "reporte_kardex_articulo_modelo",
                "args" => ["filtros_array"],
                "fecha" => "MovStockFechaHora",
                "estado" => "grupo",
                "importe" => null,
                "entidad" => "TipoMovStockId",
                "articulo" => true,
                "requiere_sucursal" => true,
                "requiere_articulo" => true,
                "columnas" => [
                    ["key" => "MovStockFechaHora", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "TipoMovStockId", "label" => "Tipo"],
                    ["key" => "MovStockReferencia", "label" => "Referencia"],
                    ["key" => "entrada", "label" => "Entrada", "tipo" => "numero"],
                    ["key" => "salida", "label" => "Salida", "tipo" => "numero"],
                    ["key" => "MovStockCosto", "label" => "Costo", "tipo" => "moneda"],
                    ["key" => "saldo_anterior", "label" => "Saldo Ant.", "tipo" => "numero"],
                    ["key" => "saldo_actual", "label" => "Saldo", "tipo" => "numero"],
                    ["key" => "usuario", "label" => "Usuario"]
                ]
            ],
            "stock" => [
                "titulo" => "Stock",
                "permiso" => "reportes.stock.ver",
                "modelo" => "reporte_articulos_modelo",
                "args" => ["stock_array"],
                "fecha" => null,
                "estado" => "estado",
                "estado_labels" => [0 => "Inactivo", 1 => "Activo"],
                "importe" => "valor_stock",
                "entidad" => "desc_articulo",
                "articulo" => true,
                "columnas" => [
                    ["key" => "codigo", "label" => "Codigo"],
                    ["key" => "desc_articulo", "label" => "Articulo"],
                    ["key" => "categoria", "label" => "Categoria"],
                    ["key" => "marca", "label" => "Marca"],
                    ["key" => "sucursal", "label" => "Sucursal"],
                    ["key" => "stock", "label" => "Stock", "tipo" => "numero"],
                    ["key" => "stockcant_min", "label" => "Min.", "tipo" => "numero"],
                    ["key" => "precio_venta", "label" => "Precio", "tipo" => "moneda"],
                    ["key" => "valor_stock", "label" => "Valor Stock", "tipo" => "moneda"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "recepcion_servicio" => [
                "titulo" => "Recepcion de Servicios",
                "permiso" => "reportes.recepcion_servicio.ver",
                "modelo" => "reporte_recepcion_servicio_modelo",
                "args" => ["desde", "hasta", "estado_int", "sucursal_int"],
                "fecha" => "fecha_ingreso",
                "estado" => "estado",
                "estado_labels" => [0 => "Anulado", 1 => "Recepcionado", 2 => "En proceso", 3 => "Finalizado"],
                "importe" => null,
                "entidad" => "cliente",
                "cliente" => true,
                "columnas" => [
                    ["key" => "idrecepcion", "label" => "Nro"],
                    ["key" => "fecha_ingreso", "label" => "Ingreso", "tipo" => "fecha"],
                    ["key" => "cliente", "label" => "Cliente"],
                    ["key" => "vehiculo", "label" => "Vehiculo"],
                    ["key" => "placa", "label" => "Placa"],
                    ["key" => "tipo_servicio", "label" => "Tipo"],
                    ["key" => "area_problema", "label" => "Area"],
                    ["key" => "observacion", "label" => "Solicitud"],
                    ["key" => "usuario", "label" => "Usuario"],
                    ["key" => "sucursal", "label" => "Sucursal"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "presupuesto_servicio" => [
                "titulo" => "Presupuestos de Servicios",
                "permiso" => "reportes.presupuesto_servicio.ver",
                "modelo" => "reporte_presupuesto_servicio_modelo",
                "detalle_modelo" => "reporte_presupuesto_servicio_detalle_modelo",
                "args" => ["desde", "hasta", "estado_int", "sucursal_int"],
                "detalle_args" => ["desde", "hasta", "estado_int", "sucursal_int"],
                "fecha" => "fecha",
                "estado" => "estado",
                "estado_labels" => [0 => "Anulado", 1 => "Pendiente", 2 => "Aprobado", 3 => "Rechazado", 4 => "Facturado"],
                "importe" => "total_final",
                "detalle_importe_unico" => "total_final",
                "detalle_importe_unico_id" => "idpresupuesto_servicio",
                "entidad" => "cliente",
                "cliente" => true,
                "articulo" => true,
                "columnas" => [
                    ["key" => "idpresupuesto_servicio", "label" => "Nro"],
                    ["key" => "fecha", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "cliente", "label" => "Cliente"],
                    ["key" => "vehiculo", "label" => "Vehiculo"],
                    ["key" => "cantidad_items", "label" => "Items", "tipo" => "numero"],
                    ["key" => "subtotal", "label" => "Subtotal", "tipo" => "moneda"],
                    ["key" => "total_promocion", "label" => "Promociones", "tipo" => "moneda"],
                    ["key" => "total_descuento", "label" => "Descuentos", "tipo" => "moneda"],
                    ["key" => "total_final", "label" => "Total Final", "tipo" => "moneda"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ],
                "detalle_columnas" => [
                    ["key" => "idpresupuesto_servicio", "label" => "Presupuesto"],
                    ["key" => "fecha", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "cliente", "label" => "Cliente"],
                    ["key" => "vehiculo", "label" => "Vehiculo"],
                    ["key" => "codigo", "label" => "Codigo"],
                    ["key" => "articulo", "label" => "Articulo"],
                    ["key" => "cantidad", "label" => "Cantidad", "tipo" => "numero"],
                    ["key" => "preciouni", "label" => "Precio", "tipo" => "moneda"],
                    ["key" => "subtotal", "label" => "Subtotal", "tipo" => "moneda"],
                    ["key" => "promocion", "label" => "Promocion"],
                    ["key" => "monto_promocion", "label" => "Monto Promo", "tipo" => "moneda"],
                    ["key" => "neto_linea", "label" => "Neto Linea", "tipo" => "moneda"],
                    ["key" => "descuentos_aplicados", "label" => "Descuento"],
                    ["key" => "total_descuento", "label" => "Desc. Presup.", "tipo" => "moneda"],
                    ["key" => "total_final", "label" => "Total Final", "tipo" => "moneda"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "orden_trabajo" => [
                "titulo" => "Ordenes de Trabajo",
                "permiso" => "reportes.orden_trabajo.ver",
                "modelo" => "reporte_orden_trabajo_modelo",
                "detalle_modelo" => "reporte_orden_trabajo_detalle_modelo",
                "args" => ["desde", "hasta", "estado_int", "sucursal_int"],
                "detalle_args" => ["desde", "hasta", "estado_int", "sucursal_int"],
                "fecha" => "fecha_inicio",
                "estado" => "estado",
                "estado_labels" => [0 => "Anulado", 1 => "Pendiente", 2 => "En proceso", 3 => "Pendiente completar"],
                "importe" => null,
                "entidad" => "cliente",
                "cliente" => true,
                "articulo" => true,
                "columnas" => [
                    ["key" => "idorden_trabajo", "label" => "Nro"],
                    ["key" => "fecha_inicio", "label" => "Inicio", "tipo" => "fecha"],
                    ["key" => "cliente", "label" => "Cliente"],
                    ["key" => "vehiculo", "label" => "Vehiculo"],
                    ["key" => "equipo", "label" => "Equipo"],
                    ["key" => "sucursal", "label" => "Sucursal"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ],
                "detalle_columnas" => [
                    ["key" => "idorden_trabajo", "label" => "Orden"],
                    ["key" => "fecha_inicio", "label" => "Inicio", "tipo" => "fecha"],
                    ["key" => "cliente", "label" => "Cliente"],
                    ["key" => "vehiculo", "label" => "Vehiculo"],
                    ["key" => "codigo", "label" => "Codigo"],
                    ["key" => "articulo", "label" => "Articulo"],
                    ["key" => "cantidad", "label" => "Cantidad", "tipo" => "numero"],
                    ["key" => "precio_unitario", "label" => "Precio", "tipo" => "moneda"],
                    ["key" => "subtotal", "label" => "Subtotal", "tipo" => "moneda"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ],
            "registro_servicio" => [
                "titulo" => "Registro de Servicios",
                "permiso" => "reportes.registro_servicio.ver",
                "modelo" => "reporte_registro_servicio_modelo",
                "detalle_modelo" => "reporte_registro_servicio_detalle_modelo",
                "args" => ["desde", "hasta", "estado_int", "empleado_int", "sucursal_int"],
                "detalle_args" => ["desde", "hasta", "estado_int", "empleado_int", "sucursal_int"],
                "fecha" => "fecha_servicio",
                "estado" => "estado",
                "estado_labels" => [0 => "Anulado", 1 => "Registrado", 2 => "Facturado", 3 => "Con Reclamo"],
                "importe" => "total",
                "entidad" => "cliente",
                "cliente" => true,
                "articulo" => true,
                "columnas" => [
                    ["key" => "idregistro_servicio", "label" => "Nro"],
                    ["key" => "fecha_servicio", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "cliente", "label" => "Cliente"],
                    ["key" => "tecnico", "label" => "Tecnico"],
                    ["key" => "sucursal", "label" => "Sucursal"],
                    ["key" => "cantidad_items", "label" => "Items", "tipo" => "numero"],
                    ["key" => "total", "label" => "Total", "tipo" => "moneda"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ],
                "detalle_columnas" => [
                    ["key" => "idregistro_servicio", "label" => "Registro"],
                    ["key" => "fecha_servicio", "label" => "Fecha", "tipo" => "fecha"],
                    ["key" => "cliente", "label" => "Cliente"],
                    ["key" => "tecnico", "label" => "Tecnico"],
                    ["key" => "codigo", "label" => "Codigo"],
                    ["key" => "articulo", "label" => "Articulo"],
                    ["key" => "origen", "label" => "Origen"],
                    ["key" => "cantidad", "label" => "Cantidad", "tipo" => "numero"],
                    ["key" => "precio_unitario", "label" => "Precio", "tipo" => "moneda"],
                    ["key" => "subtotal", "label" => "Subtotal", "tipo" => "moneda"],
                    ["key" => "estado", "label" => "Estado", "tipo" => "estado"]
                ]
            ]
        ];
    }

    private function filtros_movimientos()
    {
        return [
            "desde" => mainModel::limpiar_string($_POST['desde'] ?? ''),
            "hasta" => mainModel::limpiar_string($_POST['hasta'] ?? ''),
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? ''),
            "sucursal" => mainModel::limpiar_string($_POST['sucursal'] ?? ''),
            "proveedor" => mainModel::limpiar_string($_POST['proveedor'] ?? ''),
            "cliente" => mainModel::limpiar_string($_POST['cliente'] ?? ''),
            "articulo" => mainModel::limpiar_string($_POST['articulo'] ?? ''),
            "empleado" => mainModel::limpiar_string($_POST['empleado'] ?? ''),
            "naturaleza" => mainModel::limpiar_string($_POST['naturaleza_stock'] ?? ''),
            "tipo_stock" => mainModel::limpiar_string($_POST['tipo_movimiento_stock'] ?? ''),
            "vista" => ($_POST['vista_movimiento'] ?? '') === 'detalle' ? 'detalle' : 'resumen',
            "pagina" => max(1, (int)($_POST['pagina'] ?? 1)),
            "por_pagina" => min(500, max(25, (int)($_POST['por_pagina'] ?? 50)))
        ];
    }

    private function config_movimiento_aplicable($tipo, $config, $filtros)
    {
        $cfg = $config[$tipo];

        if (($filtros['vista'] ?? 'resumen') === 'detalle' && !empty($cfg['detalle_modelo'])) {
            $cfg['titulo'] .= ' - Detalle';
            $cfg['modelo'] = $cfg['detalle_modelo'];
            $cfg['args'] = $cfg['detalle_args'] ?? $cfg['args'];
            $cfg['columnas'] = $cfg['detalle_columnas'];
            $cfg['importe'] = array_key_exists('detalle_importe', $cfg) ? $cfg['detalle_importe'] : 'subtotal';
            $cfg['importe_unico'] = $cfg['detalle_importe_unico'] ?? null;
            $cfg['importe_unico_id'] = $cfg['detalle_importe_unico_id'] ?? null;
            $cfg['entidad'] = $cfg['detalle_entidad'] ?? $cfg['entidad'];
        }

        return $cfg;
    }

    private function filtro_int_nullable($valor)
    {
        return ($valor === '' || $valor === null || $valor === 'T') ? null : (int)$valor;
    }

    private function resolver_id_exacto($tabla, $idCampo, $codigoCampo, $valor)
    {
        $valor = trim((string)$valor);
        if ($valor === '') {
            return null;
        }

        $sql = mainModel::conectar()->prepare("
            SELECT {$idCampo}
            FROM {$tabla}
            WHERE {$idCampo} = :id OR {$codigoCampo} = :codigo
            LIMIT 1
        ");
        $sql->bindValue(':id', ctype_digit($valor) ? (int)$valor : 0, PDO::PARAM_INT);
        $sql->bindValue(':codigo', $valor);
        $sql->execute();
        $id = $sql->fetchColumn();

        return $id === false ? -1 : (int)$id;
    }

    private function resolver_articulo_movimiento($valor)
    {
        return $this->resolver_id_exacto('articulos', 'id_articulo', 'codigo', $valor);
    }

    private function resolver_cliente_movimiento($valor)
    {
        return $this->resolver_id_exacto('clientes', 'id_cliente', 'doc_number', $valor);
    }

    private function resolver_empleado_movimiento($valor)
    {
        return $this->resolver_id_exacto('empleados', 'idempleados', 'nro_cedula', $valor);
    }

    private function obtener_datos_movimientos($tipo, $config, $filtros, $cfg = null)
    {
        $cfg = $cfg ?? $this->config_movimiento_aplicable($tipo, $config, $filtros);
        if ($cfg['articulo'] ?? false) {
            $filtros['articulo'] = $this->resolver_articulo_movimiento($filtros['articulo']);
        }
        if ($cfg['cliente'] ?? false) {
            $filtros['cliente'] = $this->resolver_cliente_movimiento($filtros['cliente']);
        }
        if ($cfg['empleado'] ?? false) {
            $filtros['empleado'] = $this->resolver_empleado_movimiento($filtros['empleado']);
        }

        if (($cfg['requiere_sucursal'] ?? false) && (int)($filtros['sucursal'] ?? 0) <= 0) {
            return [];
        }

        if (($cfg['requiere_articulo'] ?? false) && (int)($filtros['articulo'] ?? 0) <= 0) {
            return [];
        }

        $args = [];

        foreach ($cfg['args'] as $arg) {
            if ($arg === 'desde') {
                $args[] = $filtros['desde'];
            } elseif ($arg === 'hasta') {
                $args[] = $filtros['hasta'];
            } elseif ($arg === 'estado_int') {
                $args[] = $this->filtro_int_nullable($filtros['estado']);
            } elseif ($arg === 'sucursal_int') {
                $args[] = $this->filtro_int_nullable($filtros['sucursal']);
            } elseif ($arg === 'proveedor_int') {
                $args[] = $this->filtro_int_nullable($filtros['proveedor']);
            } elseif ($arg === 'empleado_int') {
                $args[] = $this->filtro_int_nullable($filtros['empleado']);
            } elseif ($arg === 'filtros_array') {
                $args[] = [
                    "desde" => $filtros['desde'],
                    "hasta" => $filtros['hasta'],
                    "estado" => $filtros['estado'],
                    "sucursal" => $filtros['sucursal'],
                    "tipo" => $filtros['tipo_stock'],
                    "tipo_stock" => $filtros['tipo_stock'],
                    "naturaleza" => $filtros['naturaleza'],
                    "articulo" => $filtros['articulo']
                ];
            } elseif ($arg === 'stock_array') {
                $args[] = [
                    "desde" => $filtros['desde'],
                    "hasta" => $filtros['hasta'],
                    "estado" => $filtros['estado'],
                    "sucursal" => $filtros['sucursal'],
                    "tipo" => $filtros['tipo_stock'],
                    "tipo_stock" => $filtros['tipo_stock'],
                    "naturaleza" => $filtros['naturaleza'],
                    "articulo" => $filtros['articulo']
                ];
            }
        }

        $datos = reportesModelo::{$cfg['modelo']}(...$args);
        $datos = $this->filtrar_relaciones_movimientos($datos, $cfg, $filtros);

        return $datos;
    }

    private function filtrar_relaciones_movimientos($datos, $cfg, $filtros)
    {
        $proveedor = (int)($filtros['proveedor'] ?? 0);
        $cliente = (int)($filtros['cliente'] ?? 0);
        $articulo = (int)($filtros['articulo'] ?? 0);

        if (!($cfg['proveedor'] ?? false)) {
            $proveedor = 0;
        }
        if (!($cfg['cliente'] ?? false)) {
            $cliente = 0;
        }
        if (!($cfg['articulo'] ?? false)) {
            $articulo = 0;
        }

        if ($cliente === -1 || $articulo === -1) {
            return [];
        }

        if ($proveedor <= 0 && $cliente <= 0 && $articulo <= 0) {
            return $datos;
        }

        return array_values(array_filter($datos, function ($row) use ($proveedor, $cliente, $articulo) {
            if ($proveedor > 0 && (int)($row['idproveedores'] ?? 0) !== $proveedor) {
                return false;
            }

            if ($cliente > 0 && (int)($row['id_cliente'] ?? 0) !== $cliente) {
                return false;
            }

            if ($articulo > 0 && !$this->fila_contiene_articulo($row, $articulo)) {
                return false;
            }

            return true;
        }));
    }

    private function fila_contiene_articulo($row, $articulo)
    {
        if ((int)($row['id_articulo'] ?? 0) === $articulo) {
            return true;
        }

        $ids = array_filter(array_map('trim', explode(',', (string)($row['articulos_ids'] ?? ''))));
        return in_array((string)$articulo, $ids, true);
    }

    private function texto_estado_movimiento($valor, $cfg)
    {
        $labels = $cfg['estado_labels'] ?? [];
        $clave = is_numeric($valor) ? (int)$valor : (string)$valor;

        return $labels[$clave] ?? (string)$valor;
    }

    private function valor_movimiento($row, $columna, $cfg = [])
    {
        $valor = $row[$columna['key']] ?? '';

        if (($columna['tipo'] ?? '') === 'estado') {
            return $this->texto_estado_movimiento($valor, $cfg);
        }

        if (($columna['tipo'] ?? '') === 'moneda') {
            return number_format((float)$valor, 0, ',', '.');
        }

        if (($columna['tipo'] ?? '') === 'numero') {
            return number_format((float)$valor, 0, ',', '.');
        }

        if (($columna['tipo'] ?? '') === 'fecha' && $valor !== '') {
            return date('d/m/Y', strtotime((string)$valor));
        }

        return ($valor !== null && $valor !== '') ? (string)$valor : '-';
    }

    private function resumen_movimientos_desde_datos($datos, $cfg)
    {
        $resumen = [
            "total" => count($datos),
            "importe_total" => 0,
            "promedio" => 0,
            "items" => 0
        ];
        $estados = [];
        $importeKey = $cfg['importe'] ?? null;
        $importeUnicoKey = $cfg['importe_unico'] ?? null;
        $importeUnicoId = $cfg['importe_unico_id'] ?? null;
        $importesUnicos = [];

        foreach ($datos as $row) {
            if (
                $importeUnicoKey &&
                $importeUnicoId &&
                array_key_exists($importeUnicoKey, $row) &&
                array_key_exists($importeUnicoId, $row)
            ) {
                $idUnico = (string)$row[$importeUnicoId];
                if (!isset($importesUnicos[$idUnico])) {
                    $importesUnicos[$idUnico] = true;
                    $resumen['importe_total'] += (float)$row[$importeUnicoKey];
                }
            } elseif ($importeKey && isset($row[$importeKey])) {
                $resumen['importe_total'] += (float)$row[$importeKey];
            }
            if (isset($row['cantidad_items'])) {
                $resumen['items'] += (float)$row['cantidad_items'];
            } elseif (isset($row['cantidad'])) {
                $resumen['items'] += (float)$row['cantidad'];
            }
            $estadoKey = $cfg['estado'] ?? null;
            if ($estadoKey && isset($row[$estadoKey])) {
                $estado = $this->texto_estado_movimiento($row[$estadoKey], $cfg);
                $estados[$estado] = ($estados[$estado] ?? 0) + 1;
            }
        }

        $cantidadPromedio = !empty($importesUnicos) ? count($importesUnicos) : $resumen['total'];
        $resumen['promedio'] = $cantidadPromedio > 0 ? ($resumen['importe_total'] / $cantidadPromedio) : 0;

        if (($cfg['titulo'] ?? '') === 'Libro de Compras') {
            $totalesLibro = [
                'exenta_total' => 0,
                'gravada_5_total' => 0,
                'iva_5_total' => 0,
                'gravada_10_total' => 0,
                'iva_10_total' => 0
            ];

            foreach ($datos as $row) {
                $totalesLibro['exenta_total'] += (float)($row['exenta'] ?? 0);
                $totalesLibro['gravada_5_total'] += (float)($row['gravada_5'] ?? 0);
                $totalesLibro['iva_5_total'] += (float)($row['iva_5'] ?? 0);
                $totalesLibro['gravada_10_total'] += (float)($row['gravada_10'] ?? 0);
                $totalesLibro['iva_10_total'] += (float)($row['iva_10'] ?? 0);
            }

            $resumen = array_merge($resumen, $totalesLibro);
        }

        return [
            "tarjetas" => $resumen,
            "estados" => $estados
        ];
    }

    private function grafico_movimientos_desde_datos($datos, $cfg)
    {
        $porFecha = [];
        $porEstado = [];
        $topEntidad = [];
        $fechaKey = $cfg['fecha'] ?? null;
        $estadoKey = $cfg['estado'] ?? null;
        $entidadKey = $cfg['entidad'] ?? null;
        $importeKey = $cfg['importe'] ?? null;
        $importeUnicoKey = $cfg['importe_unico'] ?? null;
        $importeUnicoId = $cfg['importe_unico_id'] ?? null;
        $topImportesUnicos = [];

        foreach ($datos as $row) {
            if ($fechaKey && !empty($row[$fechaKey])) {
                $periodo = date('Y-m', strtotime((string)$row[$fechaKey]));
                $porFecha[$periodo] = ($porFecha[$periodo] ?? 0) + 1;
            }
            if ($estadoKey && isset($row[$estadoKey])) {
                $estado = $this->texto_estado_movimiento($row[$estadoKey], $cfg);
                $porEstado[$estado] = ($porEstado[$estado] ?? 0) + 1;
            }
            if ($entidadKey && !empty($row[$entidadKey])) {
                $entidad = (string)$row[$entidadKey];
                $valor = 1;

                if (
                    $importeUnicoKey &&
                    $importeUnicoId &&
                    array_key_exists($importeUnicoKey, $row) &&
                    array_key_exists($importeUnicoId, $row)
                ) {
                    $idUnico = (string)$row[$importeUnicoId];
                    if (isset($topImportesUnicos[$idUnico])) {
                        continue;
                    }
                    $topImportesUnicos[$idUnico] = true;
                    $valor = (float)$row[$importeUnicoKey];
                } elseif ($importeKey && isset($row[$importeKey])) {
                    $valor = (float)$row[$importeKey];
                }

                $topEntidad[$entidad] = ($topEntidad[$entidad] ?? 0) + $valor;
            }
        }

        ksort($porFecha);
        arsort($topEntidad);

        return [
            "por_fecha" => array_slice($porFecha, -12, 12, true),
            "por_estado" => $porEstado,
            "top_entidad" => array_slice($topEntidad, 0, 5, true)
        ];
    }

    private function meta_kardex_desde_datos($datos, $filtros)
    {
        if (empty($datos)) {
            return null;
        }

        $primera = reset($datos);
        $ultima = end($datos);
        $entradas = 0;
        $salidas = 0;

        foreach ($datos as $row) {
            $entradas += (float)($row['entrada'] ?? 0);
            $salidas += (float)($row['salida'] ?? 0);
        }

        return [
            "codigo" => $primera['codigo'] ?? '',
            "articulo" => $primera['desc_articulo'] ?? '',
            "sucursal" => $primera['sucursal'] ?? '',
            "desde" => $filtros['desde'] ?? '',
            "hasta" => $filtros['hasta'] ?? '',
            "saldo_inicial" => (float)($primera['saldo_anterior'] ?? 0),
            "entradas" => $entradas,
            "salidas" => $salidas,
            "saldo_final" => (float)($ultima['saldo_actual'] ?? 0)
        ];
    }

    private function columnas_tienen_claves($columnas, $claves)
    {
        foreach ($columnas as $columna) {
            if (in_array($columna['key'] ?? '', $claves, true)) {
                return true;
            }
        }

        return false;
    }

    public function reporte_movimientos_unificado_controlador()
    {
        $tipo = mainModel::limpiar_string($_POST['tipo_movimiento'] ?? '');
        $config = $this->config_movimientos();

        if (!isset($config[$tipo])) {
            return json_encode(["error" => "Informe de movimiento no valido", "data" => []]);
        }

        if (!mainModel::tienePermiso($config[$tipo]['permiso'])) {
            return $this->acceso_denegado_json();
        }

        $filtros = $this->filtros_movimientos();
        $cfg = $this->config_movimiento_aplicable($tipo, $config, $filtros);
        $datos = $this->obtener_datos_movimientos($tipo, $config, $filtros, $cfg);
        $total = count($datos);
        $porPagina = $filtros['por_pagina'];
        $totalPaginas = max(1, (int)ceil($total / $porPagina));
        $pagina = min($filtros['pagina'], $totalPaginas);
        $inicio = ($pagina - 1) * $porPagina;

        return json_encode([
            "data" => array_slice($datos, $inicio, $porPagina),
            "columnas" => $cfg['columnas'],
            "estado_labels" => $cfg['estado_labels'] ?? [],
            "titulo" => $cfg['titulo'],
            "resumen" => $this->resumen_movimientos_desde_datos($datos, $cfg),
            "graficos" => $this->grafico_movimientos_desde_datos($datos, $cfg),
            "metricas" => [
                "usa_importe" => !empty($cfg['importe']) || !empty($cfg['importe_unico']),
                "usa_items" => $this->columnas_tienen_claves($cfg['columnas'], ['cantidad_items', 'cantidad'])
            ],
            "kardex" => $tipo === 'kardex_articulo' ? $this->meta_kardex_desde_datos($datos, $filtros) : null,
            "paginacion" => [
                "pagina" => $pagina,
                "por_pagina" => $porPagina,
                "total" => $total,
                "total_paginas" => $totalPaginas,
                "desde" => $total > 0 ? $inicio + 1 : 0,
                "hasta" => min($inicio + $porPagina, $total)
            ]
        ]);
    }

    public function imprimir_reporte_movimientos_unificado_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        $tipo = mainModel::limpiar_string($_POST['tipo_movimiento'] ?? '');
        $config = $this->config_movimientos();

        if (!isset($config[$tipo]) || !mainModel::tienePermiso($config[$tipo]['permiso'])) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = $this->filtros_movimientos();
        $cfg = $this->config_movimiento_aplicable($tipo, $config, $filtros);
        $datosCompletos = $this->obtener_datos_movimientos($tipo, $config, $filtros, $cfg);
        $datos = array_slice($datosCompletos, 0, 500);
        $columnas = $cfg['columnas'];
        $titulo = "Informe de Movimientos - " . $cfg['titulo'];

        ob_start();
        ?>
        <h3><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h3>
        <table width="100%" cellspacing="0" cellpadding="5" border="1">
            <thead>
                <tr>
                    <th>#</th>
                    <?php foreach ($columnas as $columna): ?>
                        <th><?= htmlspecialchars($columna['label'], ENT_QUOTES, 'UTF-8') ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datos)): ?>
                    <tr>
                        <td colspan="<?= count($columnas) + 1 ?>" style="text-align:center;">Sin registros</td>
                    </tr>
                <?php else: ?>
                    <?php $i = 1;
                    foreach ($datos as $row): ?>
                        <tr>
                            <td style="text-align:center;"><?= $i++ ?></td>
                            <?php foreach ($columnas as $columna): ?>
                                <td><?= htmlspecialchars($this->valor_movimiento($row, $columna, $cfg), ENT_QUOTES, 'UTF-8') ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
        $html = ob_get_clean();
        $this->imprimir_mpdf_html($html, "reporte_movimientos_" . $tipo . ".pdf", 'L');
        exit();
    }

    public function exportar_reporte_movimientos_csv_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        $tipo = mainModel::limpiar_string($_POST['tipo_movimiento'] ?? '');
        $config = $this->config_movimientos();

        if (!isset($config[$tipo]) || !mainModel::tienePermiso($config[$tipo]['permiso'])) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = $this->filtros_movimientos();
        $cfg = $this->config_movimiento_aplicable($tipo, $config, $filtros);
        $datos = $this->obtener_datos_movimientos($tipo, $config, $filtros, $cfg);
        $columnas = $cfg['columnas'];

        header('Content-Type: text/csv; charset=UTF-16LE');
        header('Content-Disposition: attachment; filename="reporte_movimientos_' . $tipo . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xFF\xFE";
        echo $this->convertir_utf16le_excel("sep=;\r\n");

        $salida = fopen('php://output', 'w');
        $this->escribir_csv_excel($salida, array_merge(['#'], array_column($columnas, 'label')));

        $i = 1;
        foreach ($datos as $row) {
            $fila = [$i++];
            foreach ($columnas as $columna) {
                $fila[] = $this->valor_movimiento($row, $columna, $cfg);
            }
            $this->escribir_csv_excel($salida, $fila);
        }

        fclose($salida);
        exit();
    }


    /* ==================================================
        REPORTE ARTICULOS
    ================================================== */

    public function reporte_articulos_simple_controlador()
    {
        if (!mainModel::tienePermiso('reportes.articulos.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "categoria" => mainModel::limpiar_string($_POST['categoria']) ?? 0,
            "proveedor" => mainModel::limpiar_string($_POST['proveedor']) ?? 0,
            "estado"    => mainModel::limpiar_string($_POST['estado']) ?? 'T',
            "codigo"    => trim($_POST['codigo']) ?? ''
        ];

        $data = reportesModelo::reporte_articulos_simple_modelo($filtros);
        $resumen = reportesModelo::resumen_articulos_simple_modelo($filtros);

        return json_encode([
            "data" => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_articulos_simple_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.articulos.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "categoria" => mainModel::limpiar_string($_POST['categoria'] ?? 0),
            "proveedor" => mainModel::limpiar_string($_POST['proveedor'] ?? 0),
            "estado"    => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "codigo"    => trim($_POST['codigo'] ?? '')
        ];

        $datos = reportesModelo::reporte_articulos_simple_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/articulos_reportesimple_pdf.php";
        $html = ob_get_clean();
        $this->imprimir_mpdf_html($html, "reporte_articulos.pdf", 'L');
    }

    public function reporte_articulos_controlador()
    {
        if (!mainModel::tienePermiso('reportes.stock.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "sucursal"  => mainModel::limpiar_string($_POST['sucursal']) ?? 0,
            "categoria" => mainModel::limpiar_string($_POST['categoria']) ?? 0,
            "proveedor" => mainModel::limpiar_string($_POST['proveedor']) ?? 0,
            "estado"    => mainModel::limpiar_string($_POST['estado']) ?? 'T',
            "codigo"    => trim($_POST['codigo']) ?? '',
            "stock"     => mainModel::limpiar_string($_POST['stock']) ?? 'T'
        ];


        $data = reportesModelo::reporte_articulos_modelo($filtros);
        $resumen = reportesModelo::resumen_articulos_modelo($filtros);

        return json_encode([
            "data"    => $data,
            "resumen" => $resumen
        ]);
    }
    public function imprimir_reporte_articulos_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.stock.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "sucursal"  => ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : 0,
            "categoria" => ($_POST['categoria'] !== '') ? mainModel::limpiar_string($_POST['categoria']) : 0,
            "proveedor" => ($_POST['proveedor'] !== '') ? mainModel::limpiar_string($_POST['proveedor']) : 0,
            "estado"    => ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : 'T',
            "stock"     => ($_POST['stock'] !== '') ? mainModel::limpiar_string($_POST['stock']) : 'T',
            "codigo"    => trim($_POST['codigo'] ?? '')
        ];

        $datos = reportesModelo::reporte_articulos_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/articulos_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_articulos.pdf", 'L');
        exit();
    }

    public function reporte_stock_controlador()
    {
        return $this->reporte_articulos_controlador();
    }

    public function imprimir_reporte_stock_controlador()
    {
        $this->imprimir_reporte_articulos_controlador();
    }

    /* ==================================================
        REPORTE SUCURSALES
    ================================================== */
    public function reporte_sucursales_controlador()
    {
        if (!mainModel::tienePermiso('reportes.sucursales.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $data = reportesModelo::reporte_sucursales_modelo($filtros);
        $resumen = reportesModelo::resumen_sucursales_modelo($filtros);

        return json_encode([
            "data" => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_sucursales_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.sucursales.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $datos = reportesModelo::reporte_sucursales_modelo($filtros);
        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/sucursales_reporte_pdf.php";
        $html = ob_get_clean();
        $this->imprimir_mpdf_html($html, "reporte_sucursales.pdf", 'P');
    }

    /* ==================================================
        REPORTE VEHICULOS
    ================================================== */
    public function reporte_vehiculos_controlador()
    {
        if (!mainModel::tienePermiso('reportes.vehiculos.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "modelo" => mainModel::limpiar_string($_POST['modelo'] ?? 0),
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $data = reportesModelo::reporte_vehiculos_modelo($filtros);
        $resumen = reportesModelo::resumen_vehiculos_modelo($filtros);

        return json_encode([
            "data" => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_vehiculos_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.vehiculos.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "modelo" => mainModel::limpiar_string($_POST['modelo'] ?? 0),
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $datos = reportesModelo::reporte_vehiculos_modelo($filtros);
        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/vehiculos_reporte_pdf.php";
        $html = ob_get_clean();
        $this->imprimir_mpdf_html($html, "reporte_vehiculos.pdf", 'L');
    }

    public function reporte_proveedores_controlador()
    {
        if (!mainModel::tienePermiso('reportes.proveedores.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $data = reportesModelo::reporte_proveedores_modelo($filtros);
        $resumen = reportesModelo::resumen_proveedores_modelo($filtros);

        return json_encode([
            "data"    => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_proveedores_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.proveedores.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $datos = reportesModelo::reporte_proveedores_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/proveedores_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_proveedores.pdf", 'P');
        exit();
    }

    /* ==================================================
        REPORTE CLIENTES (PREVISUALIZACIÓN)
    ================================================== */
    public function reporte_clientes_controlador()
    {
        if (!mainModel::tienePermiso('reportes.clientes.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $data = reportesModelo::reporte_clientes_modelo($filtros);
        $resumen = reportesModelo::resumen_clientes_modelo($filtros);

        return json_encode([
            "data"    => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_clientes_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.clientes.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $datos = reportesModelo::reporte_clientes_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/clientes_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_clientes.pdf", 'P');
        exit();
    }

    /* ==================================================
        REPORTE EMPLEADOS (PREVISUALIZACIÓN)
    ================================================== */
    public function reporte_empleados_controlador()
    {
        if (!mainModel::tienePermiso('reportes.empleados.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "sucursal" => mainModel::limpiar_string($_POST['sucursal'] ?? 0),
            "cargo"    => mainModel::limpiar_string($_POST['cargo'] ?? 0),
            "estado"   => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar"   => trim($_POST['buscar'] ?? '')
        ];

        $data = reportesModelo::reporte_empleados_modelo($filtros);
        $resumen = reportesModelo::resumen_empleados_modelo($filtros);

        return json_encode([
            "data"    => $data,
            "resumen" => $resumen
        ]);
    }

    /* ==================================================
        REPORTE EMPLEADOS (PDF)
    ================================================== */
    public function imprimir_reporte_empleados_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.empleados.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "sucursal" => mainModel::limpiar_string($_POST['sucursal'] ?? 0),
            "cargo"    => mainModel::limpiar_string($_POST['cargo'] ?? 0),
            "estado"   => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar"   => trim($_POST['buscar'] ?? '')
        ];

        $datos = reportesModelo::reporte_empleados_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/empleados_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_empleados.pdf", 'P');
        exit();
    }


    /* =========================================
        INFORMES DE COMPRAS 
    ========================================= */
    private function filtros_movimiento_basico()
    {
        return [
            ($_POST['desde'] ?? '') !== '' ? mainModel::limpiar_string($_POST['desde']) : null,
            ($_POST['hasta'] ?? '') !== '' ? mainModel::limpiar_string($_POST['hasta']) : null,
            ($_POST['estado'] ?? '') !== '' ? mainModel::limpiar_string($_POST['estado']) : null,
            ($_POST['sucursal'] ?? '') !== '' ? mainModel::limpiar_string($_POST['sucursal']) : null
        ];
    }

    private function texto_estado_pedido($estado)
    {
        return match ((int)$estado) {
            0 => 'Anulado',
            1 => 'Pendiente',
            2 => 'Procesado',
            default => 'Desconocido',
        };
    }

    public function reporte_pedidos_controlador()
    {
        if (!mainModel::tienePermiso('reportes.pedidos.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_pedidos_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_pedidos_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.pedidos.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_pedidos_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ReporteMpdf::generar([
            'titulo' => 'Informe de Pedidos de Compra',
            'subtitulo' => 'Listado de pedidos segun filtros seleccionados',
            'empresa' => $empresa,
            'usuario' => $usuario,
            'archivo' => 'reporte_pedidos.pdf',
            'orientacion' => 'L',
            'salida' => 'D',
            'datos' => $datos,
            'columnas' => [
                [
                    'label' => '#',
                    'align' => 'center',
                    'valor' => fn($row, $index) => $index + 1
                ],
                [
                    'label' => 'Pedido',
                    'key' => 'idpedido_cabecera',
                    'align' => 'center'
                ],
                [
                    'label' => 'Fecha',
                    'align' => 'center',
                    'valor' => fn($row) => !empty($row['fecha']) ? date('d/m/Y', strtotime($row['fecha'])) : '-'
                ],
                [
                    'label' => 'Usuario',
                    'key' => 'usuario'
                ],
                [
                    'label' => 'Items',
                    'key' => 'cantidad_items',
                    'align' => 'center'
                ],
                [
                    'label' => 'Estado',
                    'align' => 'center',
                    'valor' => fn($row) => $this->texto_estado_pedido($row['estado'] ?? null)
                ],
                [
                    'label' => 'Sucursal',
                    'key' => 'sucursal'
                ]
            ]
        ]);
    }


    public function reporte_presupuestos_controlador()
    {
        if (!mainModel::tienePermiso('reportes.presupuestos_compra.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_presupuestos_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_presupuestos_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.presupuestos_compra.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_presupuestos_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa  = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario  = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];
        $filtros  = compact('desde', 'hasta', 'estado', 'sucursal');

        ob_start();
        require_once __DIR__ . "/../pdf/presupuestos_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_presupuestos_compra.pdf", 'L');
        exit();
    }

    public function reporte_ordenes_compra_controlador()
    {
        if (!mainModel::tienePermiso('reportes.ordenes_compra.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_ordenes_compra_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_ordenes_compra_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.ordenes_compra.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_ordenes_compra_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/ordenescompras_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_ordenes_compra.pdf", 'L');
        exit();
    }

    public function reporte_compras_controlador()
    {
        if (!mainModel::tienePermiso('reportes.compras.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_compras_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_compras_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.compras.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_compras_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/compras_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_compras.pdf", 'L');
        exit();
    }

    public function reporte_libro_compras_controlador()
    {
        if (!mainModel::tienePermiso('reportes.libro_compras.ver')) {
            return $this->acceso_denegado_json();
        }

        $desde     = ($_POST['desde'] ?? '') !== '' ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta     = ($_POST['hasta'] ?? '') !== '' ? mainModel::limpiar_string($_POST['hasta']) : null;
        $proveedor = ($_POST['proveedor'] ?? '') !== '' ? mainModel::limpiar_string($_POST['proveedor']) : null;
        $estado    = ($_POST['estado'] ?? '') !== '' ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal  = ($_POST['sucursal'] ?? '') !== '' ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $data = reportesModelo::reporte_libro_compras_modelo($desde, $hasta, $proveedor, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_libro_compras_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.libro_compras.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde     = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta     = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $proveedor = ($_POST['proveedor'] !== '') ? mainModel::limpiar_string($_POST['proveedor']) : null;
        $sucursal  = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;
        $estado  = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;

        $datos = reportesModelo::reporte_libro_compras_modelo(
            $desde,
            $hasta,
            $proveedor,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/libro_compras_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_libro_compras.pdf", 'L');
        exit();
    }

    public function reporte_transferencias_controlador()
    {
        if (!mainModel::tienePermiso('reportes.transferencias.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "sucursal" => mainModel::limpiar_string($_POST['sucursal']) ?? 0,
            "estado"   => mainModel::limpiar_string($_POST['estado']) ?? 'T',
            "desde"    => trim($_POST['desde'] ?? ''),
            "hasta"    => trim($_POST['hasta'] ?? ''),
            "tipo"     => mainModel::limpiar_string($_POST['tipo']) ?? 'T'
        ];


        $data = reportesModelo::reporte_transferencias_modelo($filtros);
        $resumen = reportesModelo::resumen_transferencias_modelo($filtros);

        return json_encode([
            "data"    => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_transferencias_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.transferencias.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "sucursal" => ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : 0,
            "estado"   => ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : 'T',
            "desde"    => trim($_POST['desde'] ?? ''),
            "hasta"    => trim($_POST['hasta'] ?? '')
        ];

        $datos = reportesModelo::reporte_transferencias_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/transferencias_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_transferencias.pdf", 'L');
        exit();
    }


    /* ==================================================
        REPORTE MOVIMIENTOS DE STOCK - PREVIEW
    ================================================== */
    public function reporte_movimientos_stock_controlador()
    {
        if (!mainModel::tienePermiso('reportes.movimientos_stock.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "sucursal" => mainModel::limpiar_string($_POST['sucursal']) ?? 0,
            "tipo"     => mainModel::limpiar_string($_POST['tipo']) ?? 'T',
            "signo"    => mainModel::limpiar_string($_POST['signo']) ?? 'T',
            "desde"    => trim($_POST['desde'] ?? ''),
            "hasta"    => trim($_POST['hasta'] ?? '')
        ];

        $data = reportesModelo::reporte_movimientos_stock_modelo($filtros);
        $resumen = reportesModelo::resumen_movimientos_stock_modelo($filtros);

        return json_encode([
            "data"    => $data,
            "resumen" => $resumen
        ]);
    }

    /* ==================================================
        REPORTE MOVIMIENTOS DE STOCK - PDF
    ================================================== */
    public function imprimir_reporte_movimientos_stock_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.movimientos_stock.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "sucursal" => ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : 0,
            "tipo"     => ($_POST['tipo'] !== '') ? mainModel::limpiar_string($_POST['tipo']) : 'T',
            "signo"    => ($_POST['signo'] !== '') ? mainModel::limpiar_string($_POST['signo']) : 'T',
            "desde"    => trim($_POST['desde'] ?? ''),
            "hasta"    => trim($_POST['hasta'] ?? '')
        ];

        $datos = reportesModelo::reporte_movimientos_stock_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/movimientos_stock_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_movimientos_stock.pdf", 'L');
        exit();
    }

    /* =========================================
        FIN INFORMES DE COMPRAS
    ========================================= */

    /* =========================================
        INFORMES DE SERVICIOS 
    ========================================= */
    public function reporte_recepcion_servicio_controlador()
    {
        if (!mainModel::tienePermiso('reportes.recepcion_servicio.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_recepcion_servicio_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_recepcion_servicio_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.recepcion_servicio.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_recepcion_servicio_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/recepcion_servicio_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_recepcion_servicio.pdf", 'L');
        exit();
    }

    public function reporte_presupuesto_servicio_controlador()
    {
        if (!mainModel::tienePermiso('reportes.presupuesto_servicio.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_presupuesto_servicio_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_presupuesto_servicio_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.presupuesto_servicio.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_presupuesto_servicio_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/presupuesto_servicio_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_presupuesto_servicio.pdf", 'L');
        exit();
    }

    public function reporte_orden_trabajo_controlador()
    {
        if (!mainModel::tienePermiso('reportes.orden_trabajo.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_orden_trabajo_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_orden_trabajo_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.orden_trabajo.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_orden_trabajo_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/orden_trabajo_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_orden_trabajo.pdf", 'L');
        exit();
    }

    public function reporte_registro_servicio_controlador()
    {
        if (!mainModel::tienePermiso('reportes.registro_servicio.ver')) {
            return $this->acceso_denegado_json();
        }

        $desde    = ($_POST['desde'] ?? '') !== '' ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] ?? '') !== '' ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] ?? '') !== '' ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] ?? '') !== '' ? mainModel::limpiar_string($_POST['sucursal']) : null;
        $empleado = ($_POST['empleado'] ?? '') !== '' ? mainModel::limpiar_string($_POST['empleado']) : null;

        $data = reportesModelo::reporte_registro_servicio_modelo($desde, $hasta, $estado, $empleado, $sucursal);
        $resumen = reportesModelo::resumen_registro_servicio_modelo($desde, $hasta, $estado, $empleado, $sucursal);

        return json_encode([
            "data" => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_registro_servicio_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.registro_servicio.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;
        $empleado = ($_POST['empleado'] !== '') ? mainModel::limpiar_string($_POST['empleado']) : null;

        $datos = reportesModelo::reporte_registro_servicio_modelo(
            $desde,
            $hasta,
            $estado,
            $empleado,
            $sucursal
        );
        $resumen = reportesModelo::resumen_registro_servicio_modelo(
            $desde,
            $hasta,
            $estado,
            $empleado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/registro_servicio_reporte_pdf.php";
        $html = ob_get_clean();

        $this->imprimir_mpdf_html($html, "reporte_registro_servicio.pdf", 'L');
        exit();
    }


    /* =========================================
        FIN INFORMES DE SERVICIOS   
    ========================================= */
}
