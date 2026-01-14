<?php
if (!mainModel::tienePermisoVista('cliente.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-user-friends"></i> &nbsp; REPORTE DE CLIENTES
    </h3>
</div>

<div class="container-fluid">
    <!-- FORM PREVIEW -->
    <form id="formPreview" class="form-neon" autocomplete="off">
        <input type="hidden" name="modulo" value="clientes">

        <div class="row">
            <div class="col-md-3">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="T">Todos</option>
                    <option value="A">Activos</option>
                    <option value="I">Inactivos</option>
                </select>
            </div>

            <div class="col-md-6">
                <label>Buscar (Nombre / Apellido / Documento / Email)</label>
                <input type="text" name="buscar" class="form-control" placeholder="Escriba para filtrar">
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
    <input type="hidden" name="accion" value="imprimir_reporte_clientes">
    <input type="hidden" name="estado">
    <input type="hidden" name="buscar">
</form>

<!-- RESUMEN -->
<div class="container-fluid mt-3" id="resumenCli" style="display:none;">
    <div class="row text-center">
        <div class="col">Total<br><strong id="r_total">0</strong></div>
        <div class="col">Activos<br><strong id="r_activos">0</strong></div>
        <div class="col">Inactivos<br><strong id="r_inactivos">0</strong></div>
    </div>
</div>

<!-- TABLA -->
<div class="container-fluid mt-3">
    <div class="table-responsive">
        <table class="table table-dark table-sm" id="tablaCli">
            <thead class="text-center">
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Ciudad</th>
                    <th>Dirección</th>
                    <th>Celular</th>
                    <th>Email</th>
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
                // Resumen
                document.getElementById("resumenCli").style.display = "block";
                document.getElementById("r_total").innerText = json.resumen.total;
                document.getElementById("r_activos").innerText = json.resumen.activos;
                document.getElementById("r_inactivos").innerText = json.resumen.inactivos;

                let tbody = document.querySelector("#tablaCli tbody");
                tbody.innerHTML = "";

                json.data.forEach(it => {
                    let doc = [it.doc_type, it.doc_number, it.digito_v].filter(Boolean).join(' ');
                    let tr = document.createElement("tr");
                    tr.innerHTML = `
                <td>${doc || '-'}</td>
                <td>${it.nombre_cliente ?? '-'}</td>
                <td>${it.apellido_cliente ?? '-'}</td>
                <td>${it.ciudad ?? '-'}</td>
                <td>${it.direccion_cliente ?? '-'}</td>
                <td>${it.celular_cliente ?? '-'}</td>
                <td>${it.email_cliente ?? '-'}</td>
                <td class="text-center">${it.estado_cliente == 1 ? 'Activo' : 'Inactivo'}</td>
            `;
                    tbody.appendChild(tr);
                });

                // Habilitar PDF
                document.getElementById("btnPdf").classList.remove("d-none");

                // Copiar filtros al form PDF
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