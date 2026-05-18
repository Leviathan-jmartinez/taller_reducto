<?php
if (!mainModel::tienePermiso('reportes.sucursales.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-city"></i> &nbsp; REPORTE DE SUCURSALES
    </h3>
</div>

<div class="container-fluid">
    <form id="formPreview" class="form-neon" autocomplete="off">
        <input type="hidden" name="modulo" value="sucursales">

        <div class="row">
            <div class="col-md-3">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="T">Todos</option>
                    <option value="A">Activas</option>
                    <option value="I">Inactivas</option>
                </select>
            </div>

            <div class="col-md-6">
                <label>Buscar</label>
                <input type="text" name="buscar" class="form-control" placeholder="Sucursal, direccion, telefono o empresa">
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
    <input type="hidden" name="accion" value="imprimir_reporte_sucursales">
    <input type="hidden" name="estado">
    <input type="hidden" name="buscar">
</form>

<div class="container-fluid mt-3" id="resumenSuc" style="display:none;">
    <div class="row text-center">
        <div class="col">Total<br><strong id="r_total">0</strong></div>
        <div class="col">Activas<br><strong id="r_activos">0</strong></div>
        <div class="col">Inactivas<br><strong id="r_inactivos">0</strong></div>
    </div>
</div>

<div class="container-fluid mt-3">
    <div class="table-responsive">
        <table class="table table-dark table-sm" id="tablaSuc">
            <thead class="text-center">
                <tr>
                    <th>Sucursal</th>
                    <th>Empresa</th>
                    <th>Direccion</th>
                    <th>Telefono</th>
                    <th>Establecimiento</th>
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
                document.getElementById("resumenSuc").style.display = "block";
                document.getElementById("r_total").innerText = json.resumen.total;
                document.getElementById("r_activos").innerText = json.resumen.activos;
                document.getElementById("r_inactivos").innerText = json.resumen.inactivos;

                let tbody = document.querySelector("#tablaSuc tbody");
                tbody.innerHTML = "";

                json.data.forEach(it => {
                    let tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${it.suc_descri ?? '-'}</td>
                        <td>${it.empresa ?? '-'}</td>
                        <td>${it.suc_direccion ?? '-'}</td>
                        <td>${it.suc_telefono ?? '-'}</td>
                        <td class="text-center">${it.nro_establecimiento ?? '-'}</td>
                        <td class="text-center">${it.estado == 1 ? 'Activa' : 'Inactiva'}</td>
                    `;
                    tbody.appendChild(tr);
                });

                document.getElementById("btnPdf").classList.remove("d-none");

                const pdf = document.getElementById("formPdf");
                ["estado", "buscar"].forEach(k => {
                    pdf.querySelector(`[name="${k}"]`).value = fd.get(k);
                });
            });
    });

    document.getElementById("btnPdf").addEventListener("click", function() {
        document.getElementById("formPdf").submit();
    });
</script>
