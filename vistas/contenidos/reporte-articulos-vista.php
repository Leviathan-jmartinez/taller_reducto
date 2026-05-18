<?php
if (!mainModel::tienePermiso('reportes.articulos.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/reportesControlador.php";
$rep = new reporteControlador();

$categorias = $rep->listar_categorias_controlador();
$proveedores = $rep->listar_proveedores_controlador();
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-box"></i> &nbsp; REPORTE DE ARTÍCULOS
    </h3>
</div>

<div class="container-fluid">
    <form id="formPreview" class="form-neon" autocomplete="off">

        <div class="row">
            <div class="col-md-3">
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

            <div class="col-md-4">
                <label>Código</label>
                <input type="text" name="codigo" class="form-control" placeholder="Buscar código">
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

<!-- 🔥 FORM PDF -->
<form id="formPdf"
    action="<?= SERVERURL ?>ajax/reportesAjax.php"
    method="POST"
    target="_blank"
    class="d-none">

    <input type="hidden" name="accion" value="imprimir_reporte_articulos_simple">
    <input type="hidden" name="categoria">
    <input type="hidden" name="proveedor">
    <input type="hidden" name="estado">
    <input type="hidden" name="codigo">
</form>

<!-- RESUMEN -->
<div class="container-fluid mt-3" id="resumenArticulos" style="display:none;">
    <div class="row text-center">
        <div class="col">Total<br><strong id="r_total">0</strong></div>
        <div class="col">Activos<br><strong id="r_activos">0</strong></div>
        <div class="col">Inactivos<br><strong id="r_inactivos">0</strong></div>
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
                    <th>Marca</th>
                    <th>Unidad</th>
                    <th>IVA</th>
                    <th>Compra</th>
                    <th>Venta</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById("formPreview").addEventListener("submit", function(e) {
        e.preventDefault();

        let fd = new FormData(this);
        fd.append("accion", "reporte_articulos_simple");

        fetch("<?php echo SERVERURL; ?>ajax/reportesAjax.php", {
                method: "POST",
                body: fd
            })
            .then(r => r.json())
            .then(json => {

                // 🔥 RESUMEN
                document.getElementById("resumenArticulos").style.display = "block";
                document.getElementById("r_total").innerText = json.resumen.total;
                document.getElementById("r_activos").innerText = json.resumen.activos;
                document.getElementById("r_inactivos").innerText = json.resumen.inactivos;

                // 🔥 TABLA
                let tbody = document.querySelector("#tablaArticulos tbody");
                tbody.innerHTML = "";

                json.data.forEach(it => {
                    let tr = document.createElement("tr");

                    tr.innerHTML = `
                <td>${it.codigo}</td>
                <td>${it.desc_articulo}</td>
                <td>${it.categoria}</td>
                <td>${it.proveedor}</td>
                <td>${it.marca}</td>
                <td>${it.unidad}</td>
                <td>${it.iva}</td>
                <td class="text-right">${it.precio_compra ?? '-'}</td>
                <td class="text-right">${it.precio_venta ?? '-'}</td>
                <td class="text-center">${it.estado == 1 ? 'Activo' : 'Inactivo'}</td>
            `;

                    tbody.appendChild(tr);
                });

                // 🔥 ACTIVAR PDF
                document.getElementById("btnPdf").classList.remove("d-none");

                // 🔥 PASAR FILTROS AL PDF
                const pdf = document.getElementById("formPdf");
                ["categoria", "proveedor", "estado", "codigo"].forEach(k => {
                    pdf.querySelector(`[name="${k}"]`).value = fd.get(k);
                });

            });
    });

    // 🔥 BOTÓN PDF
    document.getElementById("btnPdf").addEventListener("click", function() {
        document.getElementById("formPdf").submit();
    });
</script>
