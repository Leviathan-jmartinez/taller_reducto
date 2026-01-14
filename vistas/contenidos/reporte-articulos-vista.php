<?php
if (!mainModel::tienePermisoVista('articulo.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/reportesControlador.php";
$rep = new reporteControlador();

$sucursales = $rep->listar_sucursales_controlador();
$categorias = $rep->listar_categorias_controlador();
$proveedores = $rep->listar_proveedores_controlador();
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-boxes"></i> &nbsp; REPORTE DE ARTÍCULOS
    </h3>
</div>

<div class="container-fluid">
    <!-- FORM PREVIEW -->
    <form id="formPreview" class="form-neon" autocomplete="off">
        <input type="hidden" name="modulo" value="articulos">

        <div class="row">
            <div class="col-md-2">
                <label>Sucursal</label>
                <select name="sucursal" class="form-control">
                    <option value="0">Todas</option>
                    <?php foreach ($sucursales as $s): ?>
                        <option value="<?= $s['id_sucursal'] ?>"><?= $s['suc_descri'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label>Categoría</label>
                <select name="categoria" class="form-control">
                    <option value="0">Todas</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id_categoria'] ?>"><?= $c['cat_descri'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label>Proveedor</label>
                <select name="proveedor" class="form-control">
                    <option value="0">Todos</option>
                    <?php foreach ($proveedores as $p): ?>
                        <option value="<?= $p['idproveedores'] ?>"><?= $p['razon_social'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="T">Todos</option>
                    <option value="A">Activos</option>
                    <option value="I">Inactivos</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Código de barra</label>
                <input type="text" name="codigo" class="form-control" placeholder="Escanear o escribir">
            </div>

            <div class="col-md-1">
                <label>Stock</label>
                <select name="stock" class="form-control">
                    <option value="T">Todos</option>
                    <option value="C">Con</option>
                    <option value="S">Sin</option>
                    <option value="B">Bajo</option>
                </select>
            </div>
        </div>

        <p class="text-center mt-3">
            <button type="submit" class="btn btn-info">
                <i class="fas fa-search"></i> &nbsp; Previsualizar
            </button>

            <button type="button" id="btnPdf" class="btn btn-secondary d-none">
                <i class="fas fa-print"></i> &nbsp; Generar PDF
            </button>
        </p>
    </form>
</div>

<!-- FORM PDF (oculto) -->
<form id="formPdf"
    action="<?= SERVERURL ?>ajax/reportesAjax.php"
    method="POST"
    target="_blank"
    class="d-none">
    <input type="hidden" name="accion" value="imprimir_reporte_articulos">
    <input type="hidden" name="sucursal">
    <input type="hidden" name="categoria">
    <input type="hidden" name="proveedor">
    <input type="hidden" name="estado">
    <input type="hidden" name="stock">
    <input type="hidden" name="codigo">
</form>

<!-- RESUMEN -->
<div class="container-fluid mt-3" id="resumenArticulos" style="display:none;">
    <div class="row text-center">
        <div class="col">Total<br><strong id="r_total">0</strong></div>
        <div class="col">Activos<br><strong id="r_activos">0</strong></div>
        <div class="col">Inactivos<br><strong id="r_inactivos">0</strong></div>
        <div class="col">Con stock<br><strong id="r_constock">0</strong></div>
        <div class="col">Sin stock<br><strong id="r_sinstock">0</strong></div>
        <div class="col">Bajo mínimo<br><strong id="r_bajomin">0</strong></div>
    </div>
</div>

<!-- TABLA -->
<div class="container-fluid mt-3">
    <div class="table-responsive">
        <table class="table table-dark table-sm" id="tablaArticulos">
            <thead class="text-center">
                <tr>
                    <th>Código</th>
                    <th>Artículo</th>
                    <th>Categoría</th>
                    <th>Proveedor</th>
                    <th>Sucursal</th>
                    <th>Stock</th>
                    <th>Mín</th>
                    <th>Máx</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
    function formatoStock(val) {
        let n = parseFloat(val);
        if (Number.isInteger(n)) return n.toString();
        return n.toFixed(2).replace(/\.00$/, '');
    }

    document.getElementById("formPreview").addEventListener("submit", function(e) {
        e.preventDefault();

        let fd = new FormData(this);

        fetch("<?php echo SERVERURL; ?>ajax/reportesAjax.php", {
                method: "POST",
                body: fd
            })
            .then(r => r.json())
            .then(json => {
                // Resumen
                document.getElementById("resumenArticulos").style.display = "block";
                document.getElementById("r_total").innerText = json.resumen.total;
                document.getElementById("r_activos").innerText = json.resumen.activos;
                document.getElementById("r_inactivos").innerText = json.resumen.inactivos;
                document.getElementById("r_constock").innerText = json.resumen.con_stock;
                document.getElementById("r_sinstock").innerText = json.resumen.sin_stock;
                document.getElementById("r_bajomin").innerText = json.resumen.bajo_minimo;

                let tbody = document.querySelector("#tablaArticulos tbody");
                tbody.innerHTML = "";

                json.data.forEach(it => {
                    let tr = document.createElement("tr");
                    tr.innerHTML = `
                <td>${it.codigo ?? ''}</td>
                <td>${it.desc_articulo}</td>
                <td>${it.categoria}</td>
                <td>${it.proveedor}</td>
                <td>${it.sucursal ?? '-'}</td>
                <td class="text-right">${formatoStock(it.stock)}</td>
                <td class="text-right">${it.stockcant_min ?? '-'}</td>
                <td class="text-right">${it.stockcant_max ?? '-'}</td>
                <td class="text-center">${it.estado == 1 ? 'Activo' : 'Inactivo'}</td>
            `;
                    tbody.appendChild(tr);
                });

                // Habilitar PDF
                document.getElementById("btnPdf").classList.remove("d-none");

                // Copiar filtros al form PDF
                const pdf = document.getElementById("formPdf");
                ["sucursal", "categoria", "proveedor", "estado", "stock", "codigo"].forEach(k => {
                    pdf.querySelector(`[name="${k}"]`).value = fd.get(k);
                });
            });
    });

    document.getElementById("btnPdf").addEventListener("click", function() {
        document.getElementById("formPdf").submit();
    });
</script>