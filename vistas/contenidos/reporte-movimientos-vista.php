<?php
$permisosMovimientos = [
    'reportes.pedidos.ver',
    'reportes.presupuestos_compra.ver',
    'reportes.ordenes_compra.ver',
    'reportes.compras.ver',
    'reportes.libro_compras.ver',
    'reportes.stock.ver',
    'reportes.transferencias.ver',
    'reportes.movimientos_stock.ver',
    'reportes.recepcion_servicio.ver',
    'reportes.presupuesto_servicio.ver',
    'reportes.orden_trabajo.ver',
    'reportes.registro_servicio.ver'
];

$tieneAccesoMovimientos = false;
foreach ($permisosMovimientos as $permisoMovimiento) {
    if (mainModel::tienePermiso($permisoMovimiento)) {
        $tieneAccesoMovimientos = true;
        break;
    }
}

if (!$tieneAccesoMovimientos) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/reportesControlador.php";
$repMov = new reporteControlador();
$sucursalesMov = $repMov->listar_sucursales_controlador();
$proveedoresMov = $repMov->listar_proveedores_controlador();

$tiposMovimientos = [
    'pedidos' => ['titulo' => 'Pedidos de Compra', 'permiso' => 'reportes.pedidos.ver', 'proveedor' => false, 'cliente' => false, 'articulo' => true, 'empleado' => false],
    'presupuestos_compra' => ['titulo' => 'Presupuestos de Compra', 'permiso' => 'reportes.presupuestos_compra.ver', 'proveedor' => true, 'cliente' => false, 'articulo' => true, 'empleado' => false],
    'ordenes_compra' => ['titulo' => 'Ordenes de Compra', 'permiso' => 'reportes.ordenes_compra.ver', 'proveedor' => true, 'cliente' => false, 'articulo' => true, 'empleado' => false],
    'compras' => ['titulo' => 'Compras', 'permiso' => 'reportes.compras.ver', 'proveedor' => true, 'cliente' => false, 'articulo' => true, 'empleado' => false],
    'libro_compras' => ['titulo' => 'Libro de Compras', 'permiso' => 'reportes.libro_compras.ver', 'proveedor' => true, 'cliente' => false, 'articulo' => false, 'empleado' => false],
    'stock' => ['titulo' => 'Stock', 'permiso' => 'reportes.stock.ver', 'proveedor' => false, 'cliente' => false, 'articulo' => true, 'empleado' => false],
    'transferencias' => ['titulo' => 'Transferencias', 'permiso' => 'reportes.transferencias.ver', 'proveedor' => false, 'cliente' => false, 'articulo' => true, 'empleado' => false],
    'movimientos_stock' => ['titulo' => 'Movimientos de Stock', 'permiso' => 'reportes.movimientos_stock.ver', 'proveedor' => false, 'cliente' => false, 'articulo' => true, 'empleado' => false, 'stock_filtros' => true],
    'kardex_articulo' => ['titulo' => 'Kardex de Articulo', 'permiso' => 'reportes.movimientos_stock.ver', 'proveedor' => false, 'cliente' => false, 'articulo' => true, 'empleado' => false, 'stock_filtros' => true, 'requiere_sucursal' => true, 'requiere_articulo' => true],
    'recepcion_servicio' => ['titulo' => 'Recepcion de Servicios', 'permiso' => 'reportes.recepcion_servicio.ver', 'proveedor' => false, 'cliente' => true, 'articulo' => false, 'empleado' => false],
    'presupuesto_servicio' => ['titulo' => 'Presupuestos de Servicios', 'permiso' => 'reportes.presupuesto_servicio.ver', 'proveedor' => false, 'cliente' => true, 'articulo' => true, 'empleado' => false],
    'orden_trabajo' => ['titulo' => 'Ordenes de Trabajo', 'permiso' => 'reportes.orden_trabajo.ver', 'proveedor' => false, 'cliente' => true, 'articulo' => true, 'empleado' => false],
    'registro_servicio' => ['titulo' => 'Registro de Servicios', 'permiso' => 'reportes.registro_servicio.ver', 'proveedor' => false, 'cliente' => true, 'articulo' => true, 'empleado' => true]
];

$tiposMovVisibles = [];
foreach ($tiposMovimientos as $clave => $tipo) {
    if (mainModel::tienePermiso($tipo['permiso'])) {
        $tiposMovVisibles[$clave] = $tipo;
    }
}
?>

<div class="container-fluid">
    <div class="form-neon">
        <h4 class="text-center mb-3">
            <i class="fas fa-chart-line"></i>
            &nbsp; Informes de Movimientos
        </h4>

        <form id="formMovimientos" autocomplete="off">
            <input type="hidden" name="modulo" value="movimientos_unificado">
            <input type="hidden" name="pagina" value="1">

            <div class="row align-items-end">
                <div class="col-md-3">
                    <label>Tipo de informe</label>
                    <select name="tipo_movimiento" id="tipoMovimiento" class="form-control">
                        <?php foreach ($tiposMovVisibles as $clave => $tipo): ?>
                            <option value="<?= htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($tipo['titulo'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Desde</label>
                    <input type="date" name="desde" class="form-control">
                </div>

                <div class="col-md-2">
                    <label>Hasta</label>
                    <input type="date" name="hasta" class="form-control">
                </div>

                <div class="col-md-2">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="1">Activo / Pendiente</option>
                        <option value="2">Procesado</option>
                        <option value="3">Finalizado / Reclamo</option>
                        <option value="0">Anulado</option>
                        <option value="en_transito">En transito</option>
                        <option value="recibido">Recibido</option>
                        <option value="recibido_parcial">Recibido parcial</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Sucursal</label>
                    <select name="sucursal" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($sucursalesMov as $sucursal): ?>
                            <option value="<?= (int)$sucursal['id_sucursal'] ?>">
                                <?= htmlspecialchars($sucursal['suc_descri'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 mt-3 d-none" id="grupoProveedorMovimiento">
                    <label>Proveedor</label>
                    <select name="proveedor" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($proveedoresMov as $proveedor): ?>
                            <option value="<?= (int)$proveedor['idproveedores'] ?>">
                                <?= htmlspecialchars($proveedor['razon_social'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 mt-3 d-none" id="grupoClienteMovimiento">
                    <label>Cliente</label>
                    <input type="text" name="cliente" class="form-control" placeholder="ID o documento exacto">
                </div>

                <div class="col-md-3 mt-3 d-none" id="grupoArticuloMovimiento">
                    <label>Articulo</label>
                    <input type="text" name="articulo" class="form-control" placeholder="ID o codigo exacto">
                </div>

                <div class="col-md-3 mt-3 d-none" id="grupoEmpleadoMovimiento">
                    <label>Tecnico</label>
                    <input type="text" name="empleado" class="form-control" placeholder="ID o cedula exacta">
                </div>

                <div class="col-md-3 mt-3 d-none" id="grupoNaturalezaStock">
                    <label>Naturaleza</label>
                    <select name="naturaleza_stock" id="naturaleza_stock" class="form-control">
                        <option value="">Todos</option>
                        <option value="entrada">Entradas</option>
                        <option value="salida">Salidas</option>
                        <option value="ajuste">Ajustes</option>
                        <option value="compra">Compras</option>
                        <option value="transferencia">Transferencias</option>
                        <option value="servicio">Servicios</option>
                        <option value="insumo">Insumos</option>
                    </select>
                </div>

                <div class="col-md-3 mt-3 d-none" id="grupoTipoStock">
                    <label>Tipo stock</label>
                    <select name="tipo_movimiento_stock" class="form-control">
                        <option value="">Todos</option>
                        <option value="RECEPCION COMPRA">Recepcion compra</option>
                        <option value="ANULACION COMPRA">Anulacion compra</option>
                        <option value="NC_COMPRA_DEV">NC compra devolucion</option>
                        <option value="ANULA_NC_COMPRA">Anula NC compra</option>
                        <option value="TRANSFERENCIA_SALIDA">Transferencia salida</option>
                        <option value="TRANSFERENCIA_ENTRADA">Transferencia entrada</option>
                        <option value="AJUSTE_INV">Ajuste inventario</option>
                        <option value="ANULACION_AJUSTE_INV">Anulacion ajuste</option>
                        <option value="REG. SERVICIO">Registro servicio</option>
                        <option value="ANULACION REG. SERVICIO">Anulacion servicio</option>
                        <option value="SALIDA INSUMO">Salida insumo</option>
                        <option value="ANUL SALIDA INSUMO">Anulacion salida insumo</option>
                    </select>
                </div>

                <div class="col-md-2 mt-3">
                    <label>Registros</label>
                    <select name="por_pagina" class="form-control">
                        <option value="25">25</option>
                        <option value="50" selected>50</option>
                        <option value="100">100</option>
                        <option value="250">250</option>
                        <option value="500">500</option>
                    </select>
                </div>

                <div class="col-12 mt-3">
                    <div class="acciones-movimientos">
                        <button type="submit" class="btn btn-info accion-movimiento">
                            <i class="fas fa-search"></i> &nbsp; Previsualizar
                        </button>
                        <button type="button" id="btnLimpiarMovimientos" class="btn btn-warning accion-movimiento">
                            <i class="fas fa-eraser"></i> &nbsp; Limpiar
                        </button>
                        <button type="button" id="btnPdfMovimientos" class="btn btn-secondary d-none accion-movimiento">
                            <i class="fas fa-print"></i> &nbsp; PDF
                        </button>
                        <button type="button" id="btnCsvMovimientos" class="btn btn-success d-none accion-movimiento">
                            <i class="fas fa-file-csv"></i> &nbsp; CSV
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="resumenMovimientos" class="row mt-3 d-none"></div>

    <div id="panelGraficosMovimientos" class="row mt-3 d-none">
        <div class="col-xl-4 col-lg-6 col-12 mb-3">
            <div class="card movimiento-grafico-card">
                <div class="card-header"><strong>Movimientos por periodo</strong></div>
                <div class="card-body"><canvas id="graficoFechaMovimientos"></canvas></div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-12 mb-3">
            <div class="card movimiento-grafico-card">
                <div class="card-header"><strong>Distribucion por estado</strong></div>
                <div class="card-body"><canvas id="graficoEstadoMovimientos"></canvas></div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12 col-12 mb-3">
            <div class="card movimiento-grafico-card">
                <div class="card-header"><strong>Top relacionado</strong></div>
                <div class="card-body"><canvas id="graficoTopMovimientos"></canvas></div>
            </div>
        </div>
    </div>

    <div class="card mt-3 d-none" id="panelTablaMovimientos">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong id="tituloTablaMovimientos">Resultados</strong>
            <span class="badge badge-info" id="contadorMovimientos">0 registros</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-dark table-sm" id="tablaMovimientos">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted" id="infoPaginacionMovimientos">Sin registros</small>
                <div>
                    <button type="button" class="btn btn-sm btn-secondary" id="btnMovAnterior" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="mx-2" id="paginaMovimientos">1 / 1</span>
                    <button type="button" class="btn btn-sm btn-secondary" id="btnMovSiguiente" disabled>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="formPdfMovimientos" action="<?= SERVERURL ?>ajax/reportesAjax.php" method="POST" target="_blank" class="d-none">
    <input type="hidden" name="accion" value="imprimir_reporte_movimientos_unificado">
    <input type="hidden" name="tipo_movimiento">
    <input type="hidden" name="desde">
    <input type="hidden" name="hasta">
    <input type="hidden" name="estado">
    <input type="hidden" name="sucursal">
    <input type="hidden" name="proveedor">
    <input type="hidden" name="cliente">
    <input type="hidden" name="articulo">
    <input type="hidden" name="empleado">
    <input type="hidden" name="naturaleza_stock">
    <input type="hidden" name="tipo_movimiento_stock">
</form>

<form id="formCsvMovimientos" action="<?= SERVERURL ?>ajax/reportesAjax.php" method="POST" target="_blank" class="d-none">
    <input type="hidden" name="accion" value="exportar_reporte_movimientos_csv">
    <input type="hidden" name="tipo_movimiento">
    <input type="hidden" name="desde">
    <input type="hidden" name="hasta">
    <input type="hidden" name="estado">
    <input type="hidden" name="sucursal">
    <input type="hidden" name="proveedor">
    <input type="hidden" name="cliente">
    <input type="hidden" name="articulo">
    <input type="hidden" name="empleado">
    <input type="hidden" name="naturaleza_stock">
    <input type="hidden" name="tipo_movimiento_stock">
</form>

<style>
    .acciones-movimientos {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }

    .acciones-movimientos .accion-movimiento {
        margin: 0;
        min-width: 132px;
        white-space: nowrap;
    }

    .movimiento-grafico-card .card-body {
        height: clamp(290px, 34vh, 370px);
        min-height: 290px;
        position: relative;
    }

    .movimiento-grafico-card canvas {
        display: block;
        height: 100% !important;
        max-width: 100%;
        width: 100% !important;
    }

    @media (max-width: 767.98px) {
        .movimiento-grafico-card .card-body {
            height: 320px;
            min-height: 320px;
        }
    }

    @media (max-width: 575.98px) {
        .acciones-movimientos {
            align-items: stretch;
            flex-direction: column;
        }

        .acciones-movimientos .accion-movimiento {
            width: 100%;
        }
    }
</style>
<script src="<?= SERVERURL ?>vistas/js/chart.js"></script>
<script>
    (() => {
        const tipos = <?= json_encode($tiposMovVisibles, JSON_UNESCAPED_UNICODE) ?>;
        const form = document.getElementById('formMovimientos');
        const tipo = document.getElementById('tipoMovimiento');
        const grupoProveedor = document.getElementById('grupoProveedorMovimiento');
        const grupoCliente = document.getElementById('grupoClienteMovimiento');
        const grupoArticulo = document.getElementById('grupoArticuloMovimiento');
        const grupoEmpleado = document.getElementById('grupoEmpleadoMovimiento');
        const grupoNaturalezaStock = document.getElementById('grupoNaturalezaStock');
        const grupoTipoStock = document.getElementById('grupoTipoStock');
        const resumen = document.getElementById('resumenMovimientos');
        const panelGraficos = document.getElementById('panelGraficosMovimientos');
        const panelTabla = document.getElementById('panelTablaMovimientos');
        const tabla = document.getElementById('tablaMovimientos');
        const tituloTabla = document.getElementById('tituloTablaMovimientos');
        const contador = document.getElementById('contadorMovimientos');
        const infoPag = document.getElementById('infoPaginacionMovimientos');
        const paginaTexto = document.getElementById('paginaMovimientos');
        const btnAnt = document.getElementById('btnMovAnterior');
        const btnSig = document.getElementById('btnMovSiguiente');
        const btnLimpiar = document.getElementById('btnLimpiarMovimientos');
        const btnPdf = document.getElementById('btnPdfMovimientos');
        const btnCsv = document.getElementById('btnCsvMovimientos');
        const formPdf = document.getElementById('formPdfMovimientos');
        const formCsv = document.getElementById('formCsvMovimientos');
        let columnas = [];
        let paginacion = {
            pagina: 1,
            total_paginas: 1,
            total: 0,
            desde: 0,
            hasta: 0
        };
        let estadoLabels = {};
        let graficosInstancias = {};

        const texto = valor => (valor === null || valor === undefined || valor === '') ? '-' : String(valor);
        const escapeHtml = valor => texto(valor).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
        const numero = valor => new Intl.NumberFormat('es-PY').format(Number(valor || 0));

        function configurarFiltros() {
            const cfg = tipos[tipo.value] || {};
            grupoProveedor.classList.toggle('d-none', !cfg.proveedor);
            grupoCliente.classList.toggle('d-none', !cfg.cliente);
            grupoArticulo.classList.toggle('d-none', !cfg.articulo);
            grupoEmpleado.classList.toggle('d-none', !cfg.empleado);
            grupoNaturalezaStock.classList.toggle('d-none', !cfg.stock_filtros);
            grupoTipoStock.classList.toggle('d-none', !cfg.stock_filtros);
            form.elements.pagina.value = 1;
        }

        function valorCelda(row, col) {
            const valor = row[col.key];
            if (col.tipo === 'estado') return estadoLabels[String(valor)] || estadoLabels[parseInt(valor, 10)] || texto(valor);
            if (col.tipo === 'moneda' || col.tipo === 'numero') return numero(valor);
            if (col.tipo === 'fecha' && valor) return new Date(valor).toLocaleDateString('es-PY');
            return texto(valor);
        }

        function renderResumen(data) {
            const tarjetas = data.tarjetas || {};
            const items = [
                ['TOTAL', tarjetas.total || 0],
                ['IMPORTE TOTAL', numero(tarjetas.importe_total || 0)],
                ['PROMEDIO', numero(tarjetas.promedio || 0)],
                ['ITEMS', numero(tarjetas.items || 0)]
            ];

            resumen.innerHTML = items.map(item => `
            <div class="col-md-3 mb-2">
                <div class="card">
                    <div class="card-body py-3">
                        <small class="text-muted">${item[0]}</small>
                        <h5 class="mb-0">${item[1]}</h5>
                    </div>
                </div>
            </div>
        `).join('');
            resumen.classList.remove('d-none');
        }

        function dibujarFallback(canvas, datos) {
            const ctx = canvas.getContext('2d');
            const width = canvas.width = canvas.clientWidth || 320;
            const height = canvas.height = 180;
            ctx.clearRect(0, 0, width, height);
            const entries = Object.entries(datos || {});
            if (!entries.length) {
                ctx.fillStyle = '#777';
                ctx.fillText('Sin datos', 12, 24);
                return;
            }
            const max = Math.max(...entries.map(([, value]) => Number(value) || 0), 1);
            const barWidth = Math.max(18, (width - 40) / entries.length - 8);
            ctx.font = '11px Arial';
            entries.forEach(([label, value], idx) => {
                const x = 25 + idx * (barWidth + 8);
                const h = Math.round((Number(value) / max) * 110);
                ctx.fillStyle = '#17a2b8';
                ctx.fillRect(x, 130 - h, barWidth, h);
                ctx.fillStyle = '#333';
                ctx.fillText(numero(value), x, 128 - h);
                ctx.save();
                ctx.translate(x, 150);
                ctx.rotate(-0.45);
                ctx.fillText(label.substring(0, 12), 0, 0);
                ctx.restore();
            });
        }

        function destruirGrafico(id) {
            if (graficosInstancias[id]) {
                graficosInstancias[id].destroy();
                delete graficosInstancias[id];
            }
        }

        function limpiarGraficos() {
            ['graficoFechaMovimientos', 'graficoEstadoMovimientos', 'graficoTopMovimientos'].forEach(id => {
                destruirGrafico(id);
                const canvas = document.getElementById(id);
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width || canvas.clientWidth || 320, canvas.height || canvas.clientHeight || 180);
            });
            panelGraficos.classList.add('d-none');
        }

        function datosGrafico(datos) {
            const entries = Object.entries(datos || {});
            return {
                labels: entries.map(([label]) => label),
                values: entries.map(([, value]) => Number(value) || 0)
            };
        }

        function crearGrafico(id, tipo, datos, opciones = {}) {
            const canvas = document.getElementById(id);
            destruirGrafico(id);

            if (typeof Chart === 'undefined') {
                dibujarFallback(canvas, datos);
                return;
            }

            const {
                labels,
                values
            } = datosGrafico(datos);
            if (!labels.length) {
                dibujarFallback(canvas, datos);
                return;
            }

            const paleta = ['#17a2b8', '#28a745', '#ffc107', '#6f42c1', '#fd7e14', '#20c997', '#dc3545'];
            const colores = labels.map((_, idx) => paleta[idx % paleta.length]);
            const esLinea = tipo === 'line';
            const escalaNumerica = {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,.06)'
                },
                ticks: {
                    callback: value => numero(value)
                }
            };
            const escalaCategorias = {
                grid: {
                    display: false
                },
                ticks: {
                    callback: function(value) {
                        return texto(this.getLabelForValue(value)).substring(0, 16);
                    }
                }
            };
            const etiquetasValores = {
                id: `etiquetasValores${id}`,
                afterDatasetsDraw(chart) {
                    const {
                        ctx
                    } = chart;
                    const meta = chart.getDatasetMeta(0);
                    const dataset = chart.data.datasets[0];
                    ctx.save();
                    const area = chart.chartArea;
                    ctx.font = 'bold 11px Arial';
                    ctx.textBaseline = 'middle';

                    meta.data.forEach((element, index) => {
                        const value = dataset.data[index];
                        if (!value) return;

                        const label = numero(value);
                        const pos = element.tooltipPosition();
                        const labelWidth = ctx.measureText(label).width;
                        ctx.fillStyle = '#343a40';

                        if (tipo === 'doughnut') {
                            ctx.textAlign = 'center';
                            ctx.fillText(label, pos.x, pos.y);
                            return;
                        }

                        if (opciones.indexAxis === 'y') {
                            if (pos.x + labelWidth + 12 > area.right) {
                                ctx.textAlign = 'right';
                                ctx.fillStyle = '#fff';
                                ctx.fillText(label, Math.max(area.left + labelWidth + 4, pos.x - 8), pos.y);
                            } else {
                                ctx.textAlign = 'left';
                                ctx.fillText(label, pos.x + 8, pos.y);
                            }
                            return;
                        }

                        ctx.textAlign = 'center';
                        const x = Math.min(Math.max(pos.x, area.left + labelWidth / 2), area.right - labelWidth / 2);
                        const y = Math.max(area.top + 10, pos.y - 14);
                        ctx.fillText(label, x, y);
                    });

                    ctx.restore();
                }
            };

            graficosInstancias[id] = new Chart(canvas, {
                type: tipo,
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        label: opciones.label || 'Total',
                        backgroundColor: esLinea ? 'rgba(23, 162, 184, .18)' : colores,
                        borderColor: esLinea ? '#17a2b8' : colores,
                        borderWidth: esLinea ? 2 : 1,
                        fill: esLinea,
                        tension: .35,
                        pointRadius: esLinea ? 4 : 0,
                        pointHoverRadius: esLinea ? 6 : 0,
                        borderRadius: tipo === 'bar' ? 8 : 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    events: [],
                    indexAxis: opciones.indexAxis || 'x',
                    layout: {
                        padding: {
                            top: tipo === 'doughnut' ? 8 : 26,
                            right: opciones.indexAxis === 'y' ? 34 : 12,
                            bottom: 8,
                            left: 4
                        }
                    },
                    plugins: {
                        legend: {
                            display: tipo === 'doughnut',
                            position: 'bottom',
                            onClick: null,
                            labels: {
                                boxWidth: 12,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            enabled: false,
                            callbacks: {
                                label: context => `${context.label}: ${numero(context.raw)}`
                            }
                        }
                    },
                    scales: tipo === 'doughnut' ? {} : {
                        x: opciones.indexAxis === 'y' ? escalaNumerica : escalaCategorias,
                        y: opciones.indexAxis === 'y' ? escalaCategorias : escalaNumerica
                    }
                },
                plugins: [etiquetasValores]
            });
        }

        function renderGraficos(data) {
            panelGraficos.classList.remove('d-none');
            crearGrafico('graficoFechaMovimientos', 'line', data.por_fecha || {}, {
                label: 'Movimientos'
            });
            crearGrafico('graficoEstadoMovimientos', 'doughnut', data.por_estado || {});
            crearGrafico('graficoTopMovimientos', 'bar', data.top_entidad || {}, {
                label: 'Total',
                indexAxis: 'y'
            });
        }

        function renderTabla(resp) {
            const data = Array.isArray(resp.data) ? resp.data : [];
            columnas = Array.isArray(resp.columnas) ? resp.columnas : [];
            estadoLabels = resp.estado_labels || {};
            paginacion = resp.paginacion || paginacion;
            tituloTabla.textContent = resp.titulo || 'Resultados';
            contador.textContent = `${paginacion.total || 0} registros`;

            tabla.querySelector('thead').innerHTML = `
            <tr>
                <th>#</th>
                ${columnas.map(col => `<th>${escapeHtml(col.label)}</th>`).join('')}
            </tr>`;

            tabla.querySelector('tbody').innerHTML = data.length ?
                data.map((row, idx) => `
                <tr>
                    <td class="text-center">${(paginacion.desde || 1) + idx}</td>
                    ${columnas.map(col => `<td>${escapeHtml(valorCelda(row, col))}</td>`).join('')}
                </tr>
            `).join('') :
                `<tr><td colspan="${columnas.length + 1}" class="text-center">Sin registros</td></tr>`;

            infoPag.textContent = paginacion.total ? `Mostrando ${paginacion.desde}-${paginacion.hasta} de ${paginacion.total}` : 'Sin registros';
            paginaTexto.textContent = `${paginacion.pagina || 1} / ${paginacion.total_paginas || 1}`;
            btnAnt.disabled = (paginacion.pagina || 1) <= 1;
            btnSig.disabled = (paginacion.pagina || 1) >= (paginacion.total_paginas || 1);
            panelTabla.classList.remove('d-none');
            btnPdf.classList.toggle('d-none', !(paginacion.total > 0));
            btnCsv.classList.toggle('d-none', !(paginacion.total > 0));
        }

        function sincronizar(formExportacion) {
            const fd = new FormData(form);
            ['tipo_movimiento', 'desde', 'hasta', 'estado', 'sucursal', 'proveedor', 'cliente', 'articulo', 'empleado', 'naturaleza_stock', 'tipo_movimiento_stock'].forEach(name => {
                formExportacion.elements[name].value = fd.get(name) || '';
            });
        }

        function limpiarResultados() {
            columnas = [];
            paginacion = {
                pagina: 1,
                total_paginas: 1,
                total: 0,
                desde: 0,
                hasta: 0
            };
            estadoLabels = {};
            resumen.innerHTML = '';
            resumen.classList.add('d-none');
            limpiarGraficos();
            tabla.querySelector('thead').innerHTML = '';
            tabla.querySelector('tbody').innerHTML = '';
            tituloTabla.textContent = 'Resultados';
            contador.textContent = '0 registros';
            infoPag.textContent = 'Sin registros';
            paginaTexto.textContent = '1 / 1';
            btnAnt.disabled = true;
            btnSig.disabled = true;
            panelTabla.classList.add('d-none');
            btnPdf.classList.add('d-none');
            btnCsv.classList.add('d-none');
        }

        function limpiarFiltros() {
            ['desde', 'hasta', 'estado', 'sucursal', 'proveedor', 'cliente', 'articulo', 'empleado', 'naturaleza_stock', 'tipo_movimiento_stock'].forEach(name => {
                if (form.elements[name]) form.elements[name].value = '';
            });
            form.elements.por_pagina.value = '50';
            form.elements.pagina.value = 1;
            configurarFiltros();
            limpiarResultados();
        }

        function consultar(pagina = 1) {
            form.elements.pagina.value = pagina;
            form.dispatchEvent(new Event('submit'));
        }

        form.addEventListener('submit', event => {
            event.preventDefault();
            const cfg = tipos[tipo.value] || {};
            if (cfg.requiere_sucursal && !form.elements.sucursal.value) {
                Swal.fire('Atencion', 'Para el Kardex debe seleccionar una sucursal.', 'warning');
                return;
            }
            if (cfg.requiere_articulo && !form.elements.articulo.value.trim()) {
                Swal.fire('Atencion', 'Para el Kardex debe ingresar el ID o codigo exacto del articulo.', 'warning');
                return;
            }
            fetch("<?= SERVERURL ?>ajax/reportesAjax.php", {
                    method: 'POST',
                    body: new FormData(form)
                })
                .then(resp => resp.json())
                .then(resp => {
                    if (resp.error) {
                        Swal.fire('Atencion', resp.error, 'warning');
                        return;
                    }
                    renderResumen(resp.resumen || {});
                    renderGraficos(resp.graficos || {});
                    renderTabla(resp);
                    sincronizar(formPdf);
                    sincronizar(formCsv);
                })
                .catch(() => Swal.fire('Error', 'No se pudo generar el informe.', 'error'));
        });

        tipo.addEventListener('change', configurarFiltros);
        btnLimpiar.addEventListener('click', limpiarFiltros);
        btnAnt.addEventListener('click', () => consultar((paginacion.pagina || 1) - 1));
        btnSig.addEventListener('click', () => consultar((paginacion.pagina || 1) + 1));
        btnPdf.addEventListener('click', () => {
            sincronizar(formPdf);
            formPdf.submit();
        });
        btnCsv.addEventListener('click', () => {
            sincronizar(formCsv);
            formCsv.submit();
        });

        ['desde', 'hasta', 'estado', 'sucursal', 'proveedor', 'cliente', 'articulo', 'empleado', 'naturaleza_stock', 'tipo_movimiento_stock', 'por_pagina'].forEach(name => {
            const control = form.elements[name];
            if (control) control.addEventListener('change', () => {
                form.elements.pagina.value = 1;
            });
        });

        window.addEventListener('resize', () => {
            Object.values(graficosInstancias).forEach(chart => chart.resize());
        });

        configurarFiltros();
    })();
</script>
