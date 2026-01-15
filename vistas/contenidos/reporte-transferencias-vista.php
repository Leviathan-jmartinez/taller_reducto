<?php
if (!mainModel::tienePermisoVista('compra.reportes.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/reportesControlador.php";
$rep = new reporteControlador();

$sucursales = $rep->listar_sucursales_controlador();
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-truck-moving"></i> &nbsp; REPORTE DE TRANSFERENCIAS
    </h3>
</div>

<div class="container-fluid">
    <!-- FORM PREVIEW -->
    <form id="formPreview" class="form-neon" autocomplete="off">
        <input type="hidden" name="modulo" value="transferencias">

        <div class="row">
            <div class="col-md-3">
                <label>Sucursal</label>
                <select name="sucursal" class="form-control">
                    <option value="0">Todas</option>
                    <?php foreach ($sucursales as $s): ?>
                        <option value="<?= $s['id_sucursal'] ?>"><?= $s['suc_descri'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label>Tipo</label>
                <select name="tipo" class="form-control">
                    <option value="T">Todos</option>
                    <option value="E">Envíos</option>
                    <option value="R">Recepciones</option>
                </select>
            </div>


            <div class="col-md-2">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="T">Todos</option>
                    <option value="en_transito">En tránsito</option>
                    <option value="recibido">Recibido</option>
                    <option value="recibido_parcial">Parcial</option>
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
    <input type="hidden" name="accion" value="imprimir_reporte_transferencias">
    <input type="hidden" name="sucursal">
    <input type="hidden" name="estado">
    <input type="hidden" name="desde">
    <input type="hidden" name="hasta">
    <input type="hidden" name="tipo">

</form>

<!-- RESUMEN -->
<div class="container-fluid mt-3" id="resumenTransferencias" style="display:none;">
    <div class="row text-center">
        <div class="col">Total<br><strong id="r_total">0</strong></div>
        <div class="col">En tránsito<br><strong id="r_transito">0</strong></div>
        <div class="col">Recibidos<br><strong id="r_recibidos">0</strong></div>
        <div class="col">Parciales<br><strong id="r_parciales">0</strong></div>
    </div>
</div>

<!-- TABLA -->
<div class="container-fluid mt-3">
    <div class="table-responsive">
        <table class="table table-dark table-sm" id="tablaTransferencias">
            <thead class="text-center">
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Estado</th>
                    <th>Remisión</th>
                    <th>Motivo</th>
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

                // Resumen
                document.getElementById("resumenTransferencias").style.display = "block";
                document.getElementById("r_total").innerText = json.resumen.total;
                document.getElementById("r_transito").innerText = json.resumen.en_transito;
                document.getElementById("r_recibidos").innerText = json.resumen.recibidos;
                document.getElementById("r_parciales").innerText = json.resumen.parciales;

                let tbody = document.querySelector("#tablaTransferencias tbody");
                tbody.innerHTML = "";

                json.data.forEach(it => {
                    let tr = document.createElement("tr");
                    tr.innerHTML = `
                <td>${it.idtransferencia}</td>
                <td>${it.fecha}</td>
                <td>${it.suc_origen}</td>
                <td>${it.suc_destino}</td>
                <td class="text-center">${it.estado}</td>
                <td class="text-center">${it.nro_remision ?? '-'}</td>
                <td>${it.motivo_remision ?? ''}</td>
            `;
                    tbody.appendChild(tr);
                });

                // Habilitar PDF
                document.getElementById("btnPdf").classList.remove("d-none");

                // Copiar filtros al form PDF
                const pdf = document.getElementById("formPdf");
                ["sucursal", "estado", "desde", "hasta", "tipo"].forEach(k => {
                    pdf.querySelector(`[name="${k}"]`).value = fd.get(k);
                });

            });
    });

    document.getElementById("btnPdf").addEventListener("click", function() {
        document.getElementById("formPdf").submit();
    });
</script>