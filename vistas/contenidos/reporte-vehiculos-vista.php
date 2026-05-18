<?php
if (!mainModel::tienePermiso('reportes.vehiculos.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/reportesControlador.php";
$rep = new reporteControlador();
$modelos = $rep->listar_modelos_controlador();
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-car"></i> &nbsp; REPORTE DE VEHICULOS
    </h3>
</div>

<div class="container-fluid">
    <form id="formPreview" class="form-neon" autocomplete="off">
        <input type="hidden" name="modulo" value="vehiculos">

        <div class="row">
            <div class="col-md-3">
                <label>Modelo</label>
                <select name="modelo" class="form-control">
                    <option value="0">Todos</option>
                    <?php foreach ($modelos as $m): ?>
                        <option value="<?= $m['id_modeloauto'] ?>"><?= $m['mod_descri'] ?></option>
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

            <div class="col-md-7">
                <label>Buscar</label>
                <input type="text" name="buscar" class="form-control" placeholder="Placa, serie, color, documento o cliente">
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

<form id="formPdf"
    action="<?= SERVERURL ?>ajax/reportesAjax.php"
    method="POST"
    target="_blank"
    class="d-none">
    <input type="hidden" name="accion" value="imprimir_reporte_vehiculos">
    <input type="hidden" name="modelo">
    <input type="hidden" name="estado">
    <input type="hidden" name="buscar">
</form>

<div class="container-fluid mt-3" id="resumenVeh" style="display:none;">
    <div class="row text-center">
        <div class="col">Total<br><strong id="r_total">0</strong></div>
        <div class="col">Activos<br><strong id="r_activos">0</strong></div>
        <div class="col">Inactivos<br><strong id="r_inactivos">0</strong></div>
    </div>
</div>

<div class="container-fluid mt-3">
    <div class="table-responsive">
        <table class="table table-dark table-sm" id="tablaVeh">
            <thead class="text-center">
                <tr>
                    <th>Placa</th>
                    <th>Modelo</th>
                    <th>Cliente</th>
                    <th>Documento</th>
                    <th>Serie</th>
                    <th>Anho</th>
                    <th>Color</th>
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

        fetch("<?php echo SERVERURL; ?>ajax/reportesAjax.php", {
                method: "POST",
                body: fd
            })
            .then(r => r.json())
            .then(json => {
                document.getElementById("resumenVeh").style.display = "block";
                document.getElementById("r_total").innerText = json.resumen.total;
                document.getElementById("r_activos").innerText = json.resumen.activos;
                document.getElementById("r_inactivos").innerText = json.resumen.inactivos;

                let tbody = document.querySelector("#tablaVeh tbody");
                tbody.innerHTML = "";

                json.data.forEach(it => {
                    let tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${it.placa ?? '-'}</td>
                        <td>${it.modelo ?? '-'}</td>
                        <td>${it.cliente ?? '-'}</td>
                        <td>${it.doc_number ?? '-'}</td>
                        <td>${it.nro_serie ?? '-'}</td>
                        <td class="text-center">${it.anho ?? '-'}</td>
                        <td>${it.color ?? '-'}</td>
                        <td class="text-center">${it.estado == 1 ? 'Activo' : 'Inactivo'}</td>
                    `;
                    tbody.appendChild(tr);
                });

                document.getElementById("btnPdf").classList.remove("d-none");

                const pdf = document.getElementById("formPdf");
                ["modelo", "estado", "buscar"].forEach(k => {
                    pdf.querySelector(`[name="${k}"]`).value = fd.get(k);
                });
            });
    });

    document.getElementById("btnPdf").addEventListener("click", function() {
        document.getElementById("formPdf").submit();
    });
</script>
