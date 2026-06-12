<?php
$permisosReferenciales = [
    'reportes.proveedores.ver',
    'reportes.clientes.ver',
    'reportes.vehiculos.ver',
    'reportes.sucursales.ver',
    'reportes.articulos.ver',
    'reportes.empleados.ver',
    'usuarios.ver'
];

$tieneAccesoReferenciales = false;
foreach ($permisosReferenciales as $permisoReferencial) {
    if (mainModel::tienePermiso($permisoReferencial)) {
        $tieneAccesoReferenciales = true;
        break;
    }
}

if (!$tieneAccesoReferenciales) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/reportesControlador.php";
$rep = new reporteControlador();
$categorias = $rep->listar_categorias_controlador();

$tiposReferenciales = [
    'proveedores' => ['titulo' => 'Proveedores', 'icono' => 'fas fa-truck', 'permiso' => 'reportes.proveedores.ver', 'estado' => true, 'categoria' => false],
    'clientes' => ['titulo' => 'Clientes', 'icono' => 'fas fa-users', 'permiso' => 'reportes.clientes.ver', 'estado' => true, 'categoria' => false],
    'vehiculos' => ['titulo' => 'Vehiculos', 'icono' => 'fas fa-car', 'permiso' => 'reportes.vehiculos.ver', 'estado' => true, 'categoria' => false],
    'sucursales' => ['titulo' => 'Sucursales', 'icono' => 'fas fa-city', 'permiso' => 'reportes.sucursales.ver', 'estado' => true, 'categoria' => false],
    'articulos' => ['titulo' => 'Articulos', 'icono' => 'fas fa-boxes', 'permiso' => 'reportes.articulos.ver', 'estado' => true, 'categoria' => true],
    'empleados' => ['titulo' => 'Empleados', 'icono' => 'fas fa-user-tie', 'permiso' => 'reportes.empleados.ver', 'estado' => true, 'categoria' => false],
    'marcas' => ['titulo' => 'Marcas', 'icono' => 'fas fa-tags', 'permiso' => 'reportes.articulos.ver', 'estado' => false, 'categoria' => false],
    'categorias' => ['titulo' => 'Categorias', 'icono' => 'fas fa-layer-group', 'permiso' => 'reportes.articulos.ver', 'estado' => false, 'categoria' => false],
    'usuarios' => ['titulo' => 'Usuarios', 'icono' => 'fas fa-user-shield', 'permiso' => 'usuarios.ver', 'estado' => true, 'categoria' => false]
];

$tiposVisibles = [];
foreach ($tiposReferenciales as $clave => $tipo) {
    if (mainModel::tienePermiso($tipo['permiso'])) {
        $tiposVisibles[$clave] = $tipo;
    }
}

$primerTipo = array_key_first($tiposVisibles);
?>

<div class="container-fluid">
    <div class="form-neon">
        <h4 class="text-center mb-3">
            <i class="fas fa-chart-bar"></i>
            &nbsp; Informes Referenciales
        </h4>

        <form id="formReferenciales" autocomplete="off">
            <input type="hidden" name="modulo" value="referenciales">
            <input type="hidden" name="pagina" value="1">

            <div class="row align-items-end">
                <div class="col-md-3">
                    <label>Tipo de informe</label>
                    <select name="tipo_referencial" id="tipoReferencial" class="form-control">
                        <?php foreach ($tiposVisibles as $clave => $tipo): ?>
                            <option value="<?= htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($tipo['titulo'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3" id="grupoEstado">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="T">Todos</option>
                        <option value="A">Activos</option>
                        <option value="I">Inactivos</option>
                    </select>
                </div>

                <div class="col-md-3 d-none" id="grupoCategoria">
                    <label>Categoria</label>
                    <select name="categoria" class="form-control">
                        <option value="0">Todas</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= (int)$categoria['id_categoria'] ?>">
                                <?= htmlspecialchars($categoria['cat_descri'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Buscar</label>
                    <input type="text" name="buscar" class="form-control" placeholder="Texto, codigo o documento">
                </div>

                <div class="col-md-2 mt-3">
                    <label>Registros</label>
                    <select name="por_pagina" id="porPaginaReferencial" class="form-control">
                        <option value="25">25</option>
                        <option value="50" selected>50</option>
                        <option value="100">100</option>
                        <option value="250">250</option>
                        <option value="500">500</option>
                    </select>
                </div>

                <div class="col-12 mt-3">
                    <div class="acciones-referenciales">
                        <button type="submit" class="btn btn-info accion-referencial">
                            <i class="fas fa-search"></i> &nbsp; Previsualizar
                        </button>
                        <button type="button" id="btnLimpiarReferencial" class="btn btn-warning accion-referencial">
                            <i class="fas fa-eraser"></i> &nbsp; Limpiar
                        </button>
                        <button type="button" id="btnPdfReferencial" class="btn btn-secondary d-none accion-referencial">
                            <i class="fas fa-print"></i> &nbsp; PDF
                        </button>
                        <button type="button" id="btnCsvReferencial" class="btn btn-success d-none accion-referencial">
                            <i class="fas fa-file-csv"></i> &nbsp; CSV
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="resumenReferenciales" class="row mt-3 d-none"></div>

    <div class="card mt-3 d-none" id="panelTablaReferenciales">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong id="tituloTablaReferenciales">Resultados</strong>
            <span class="badge badge-info" id="contadorReferenciales">0 registros</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-dark table-sm" id="tablaReferenciales">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted" id="infoPaginacionReferenciales">Sin registros</small>
                <div>
                    <button type="button" class="btn btn-sm btn-secondary" id="btnPaginaAnterior" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="mx-2" id="paginaActualReferenciales">1 / 1</span>
                    <button type="button" class="btn btn-sm btn-secondary" id="btnPaginaSiguiente" disabled>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="formPdfReferencial" action="<?= SERVERURL ?>ajax/reportesAjax.php" method="POST" target="_blank" class="d-none">
    <input type="hidden" name="accion" value="imprimir_reporte_referenciales">
    <input type="hidden" name="tipo_referencial">
    <input type="hidden" name="estado">
    <input type="hidden" name="categoria">
    <input type="hidden" name="buscar">
</form>

<form id="formCsvReferencial" action="<?= SERVERURL ?>ajax/reportesAjax.php" method="POST" target="_blank" class="d-none">
    <input type="hidden" name="accion" value="exportar_reporte_referenciales_csv">
    <input type="hidden" name="tipo_referencial">
    <input type="hidden" name="estado">
    <input type="hidden" name="categoria">
    <input type="hidden" name="buscar">
</form>

<style>
    .acciones-referenciales {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }

    .acciones-referenciales .accion-referencial {
        margin: 0;
        min-width: 132px;
        white-space: nowrap;
    }

    @media (max-width: 575.98px) {
        .acciones-referenciales {
            align-items: stretch;
            flex-direction: column;
        }

        .acciones-referenciales .accion-referencial {
            width: 100%;
        }
    }
</style>

<script>
(() => {
    const tipos = <?= json_encode($tiposVisibles, JSON_UNESCAPED_UNICODE) ?>;
    const form = document.getElementById('formReferenciales');
    const tipo = document.getElementById('tipoReferencial');
    const grupoEstado = document.getElementById('grupoEstado');
    const grupoCategoria = document.getElementById('grupoCategoria');
    const resumen = document.getElementById('resumenReferenciales');
    const panelTabla = document.getElementById('panelTablaReferenciales');
    const tituloTabla = document.getElementById('tituloTablaReferenciales');
    const contador = document.getElementById('contadorReferenciales');
    const tabla = document.getElementById('tablaReferenciales');
    const btnLimpiar = document.getElementById('btnLimpiarReferencial');
    const btnPdf = document.getElementById('btnPdfReferencial');
    const btnCsv = document.getElementById('btnCsvReferencial');
    const formPdf = document.getElementById('formPdfReferencial');
    const formCsv = document.getElementById('formCsvReferencial');
    const btnPaginaAnterior = document.getElementById('btnPaginaAnterior');
    const btnPaginaSiguiente = document.getElementById('btnPaginaSiguiente');
    const paginaActual = document.getElementById('paginaActualReferenciales');
    const infoPaginacion = document.getElementById('infoPaginacionReferenciales');
    let ultimaData = [];
    let ultimasColumnas = [];
    let ultimaPaginacion = { pagina: 1, total_paginas: 1, total: 0, desde: 0, hasta: 0 };

    const escapeHtml = valor => texto(valor)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
    const estadoTexto = valor => parseInt(valor, 10) === 1 ? 'Activo' : 'Inactivo';
    const numero = valor => new Intl.NumberFormat('es-PY').format(Number(valor || 0));
    const texto = valor => (valor === null || valor === undefined || valor === '') ? '-' : String(valor);

    function configurarFiltros() {
        const cfg = tipos[tipo.value] || {};
        grupoEstado.classList.toggle('d-none', !cfg.estado);
        grupoCategoria.classList.toggle('d-none', !cfg.categoria);
    }

    function limpiarResultados() {
        ultimaData = [];
        ultimasColumnas = [];
        ultimaPaginacion = { pagina: 1, total_paginas: 1, total: 0, desde: 0, hasta: 0 };
        resumen.innerHTML = '';
        resumen.classList.add('d-none');
        tabla.querySelector('thead').innerHTML = '';
        tabla.querySelector('tbody').innerHTML = '';
        tituloTabla.textContent = 'Resultados';
        contador.textContent = '0 registros';
        infoPaginacion.textContent = 'Sin registros';
        paginaActual.textContent = '1 / 1';
        btnPaginaAnterior.disabled = true;
        btnPaginaSiguiente.disabled = true;
        panelTabla.classList.add('d-none');
        btnPdf.classList.add('d-none');
        btnCsv.classList.add('d-none');
    }

    function limpiarFiltros() {
        form.elements.estado.value = 'T';
        form.elements.categoria.value = '0';
        form.elements.buscar.value = '';
        form.elements.por_pagina.value = '50';
        form.elements.pagina.value = 1;
        configurarFiltros();
        limpiarResultados();
    }

    function valorCelda(row, columna) {
        const valor = row[columna.key];
        if (columna.tipo === 'estado') return estadoTexto(valor);
        if (columna.tipo === 'numero') return numero(valor);
        return texto(valor);
    }

    function renderResumen(datos) {
        const items = [];
        if (datos && typeof datos === 'object') {
            Object.keys(datos).forEach(key => {
                const label = key.replaceAll('_', ' ').toUpperCase();
                items.push({ label, value: datos[key] ?? 0 });
            });
        }

        resumen.innerHTML = items.map(item => `
            <div class="col-md-3 mb-2">
                <div class="card">
                    <div class="card-body py-3">
                        <small class="text-muted">${item.label}</small>
                        <h5 class="mb-0">${numero(item.value)}</h5>
                    </div>
                </div>
            </div>
        `).join('');
        resumen.classList.toggle('d-none', items.length === 0);
    }

    function renderTabla(data, columnas, titulo, paginacion) {
        ultimaData = Array.isArray(data) ? data : [];
        ultimasColumnas = Array.isArray(columnas) ? columnas : [];
        ultimaPaginacion = paginacion || { pagina: 1, total_paginas: 1, total: ultimaData.length, desde: ultimaData.length ? 1 : 0, hasta: ultimaData.length };

        tituloTabla.textContent = titulo || 'Resultados';
        contador.textContent = `${ultimaPaginacion.total || 0} registros`;

        tabla.querySelector('thead').innerHTML = `
            <tr>
                <th>#</th>
                ${ultimasColumnas.map(col => `<th>${escapeHtml(col.label)}</th>`).join('')}
            </tr>
        `;

        tabla.querySelector('tbody').innerHTML = ultimaData.length
            ? ultimaData.map((row, idx) => `
                <tr>
                    <td class="text-center">${(ultimaPaginacion.desde || 1) + idx}</td>
                    ${ultimasColumnas.map(col => `<td>${escapeHtml(valorCelda(row, col))}</td>`).join('')}
                </tr>
            `).join('')
            : `<tr><td colspan="${ultimasColumnas.length + 1}" class="text-center">Sin registros</td></tr>`;

        infoPaginacion.textContent = ultimaPaginacion.total
            ? `Mostrando ${ultimaPaginacion.desde}-${ultimaPaginacion.hasta} de ${ultimaPaginacion.total}`
            : 'Sin registros';
        paginaActual.textContent = `${ultimaPaginacion.pagina || 1} / ${ultimaPaginacion.total_paginas || 1}`;
        btnPaginaAnterior.disabled = (ultimaPaginacion.pagina || 1) <= 1;
        btnPaginaSiguiente.disabled = (ultimaPaginacion.pagina || 1) >= (ultimaPaginacion.total_paginas || 1);

        panelTabla.classList.remove('d-none');
        btnPdf.classList.remove('d-none');
        btnCsv.classList.toggle('d-none', (ultimaPaginacion.total || 0) === 0);
    }

    function sincronizarExportacion(formExportacion) {
        const fd = new FormData(form);
        ['tipo_referencial', 'estado', 'categoria', 'buscar'].forEach(name => {
            formExportacion.elements[name].value = fd.get(name) || '';
        });
    }

    function consultar(pagina = 1) {
        form.elements.pagina.value = pagina;
        form.dispatchEvent(new Event('submit'));
    }

    form.addEventListener('submit', event => {
        event.preventDefault();
        const fd = new FormData(form);

        fetch("<?= SERVERURL ?>ajax/reportesAjax.php", {
            method: 'POST',
            body: fd
        })
            .then(resp => resp.json())
            .then(resp => {
                if (resp.error) {
                    Swal.fire('Atencion', resp.error, 'warning');
                    return;
                }
                renderResumen(resp.resumen || {});
                renderTabla(resp.data || [], resp.columnas || [], resp.titulo || 'Resultados', resp.paginacion || null);
                sincronizarExportacion(formPdf);
                sincronizarExportacion(formCsv);
            })
            .catch(() => Swal.fire('Error', 'No se pudo generar el informe.', 'error'));
    });

    tipo.addEventListener('change', () => {
        configurarFiltros();
        form.elements.pagina.value = 1;
    });
    btnLimpiar.addEventListener('click', limpiarFiltros);

    btnPdf.addEventListener('click', () => {
        sincronizarExportacion(formPdf);
        formPdf.submit();
    });

    btnCsv.addEventListener('click', () => {
        sincronizarExportacion(formCsv);
        formCsv.submit();
    });

    btnPaginaAnterior.addEventListener('click', () => {
        if ((ultimaPaginacion.pagina || 1) > 1) {
            consultar((ultimaPaginacion.pagina || 1) - 1);
        }
    });

    btnPaginaSiguiente.addEventListener('click', () => {
        if ((ultimaPaginacion.pagina || 1) < (ultimaPaginacion.total_paginas || 1)) {
            consultar((ultimaPaginacion.pagina || 1) + 1);
        }
    });

    ['estado', 'categoria', 'buscar', 'por_pagina'].forEach(name => {
        const control = form.elements[name];
        if (!control) return;
        control.addEventListener('change', () => {
            form.elements.pagina.value = 1;
        });
    });

    configurarFiltros();
})();
</script>
