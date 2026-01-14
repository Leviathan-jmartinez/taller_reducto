<?php
if (!mainModel::tienePermisoVista('empleado.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/sucursalControlador.php";
require_once "./controladores/reportesControlador.php";

$insSuc = new sucursalControlador();
$insCar = new reporteControlador();

$sucursales = $insSuc->listar_sucursales_controlador();
$cargos = $insCar->listar_cargos_controlador();
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-id-badge"></i> &nbsp; REPORTE DE EMPLEADOS
    </h3>
</div>

<div class="container-fluid">
    <!-- FORM PREVIEW -->
    <form id="formPreview" class="form-neon" autocomplete="off">
        <input type="hidden" name="modulo" value="empleados">

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

            <div class="col-md-3">
                <label>Cargo</label>
                <select name="cargo" class="form-control">
                    <option value="0">Todos</option>
                    <?php foreach ($cargos as $c): ?>
                        <option value="<?= $c['idcargos'] ?>"><?= $c['descripcion'] ?></option>
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
                <label>Buscar (Nombre / Apellido / Cédula)</label>
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
    <input type="hidden" name="accion" value="imprimir_reporte_empleados">
    <input type="hidden" name="sucursal">
    <input type="hidden" name="cargo">
    <input type="hidden" name="estado">
    <input type="hidden" name="buscar">
</form>

<!-- RESUMEN -->
<div class="container-fluid mt-3" id="resumenEmp" style="display:none;">
    <div class="row text-center">
        <div class="col">Total<br><strong id="r_total">0</strong></div>
        <div class="col">Activos<br><strong id="r_activos">0</strong></div>
        <div class="col">Inactivos<br><strong id="r_inactivos">0</strong></div>
    </div>
</div>

<!-- TABLA -->
<div class="container-fluid mt-3">
    <div class="table-responsive">
        <table class="table table-bordered table-sm" id="tablaEmp">
            <thead class="text-center">
                <tr>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Cargo</th>
                    <th>Sucursal</th>
                    <th>Celular</th>
                    <th>Dirección</th>
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
                document.getElementById("resumenEmp").style.display = "block";
                document.getElementById("r_total").innerText = json.resumen.total;
                document.getElementById("r_activos").innerText = json.resumen.activos;
                document.getElementById("r_inactivos").innerText = json.resumen.inactivos;

                let tbody = document.querySelector("#tablaEmp tbody");
                tbody.innerHTML = "";

                json.data.forEach(it => {
                    let tr = document.createElement("tr");
                    tr.innerHTML = `
                <td>${it.nro_cedula ?? '-'}</td>
                <td>${it.nombre ?? '-'}</td>
                <td>${it.apellido ?? '-'}</td>
                <td>${it.cargo}</td>
                <td>${it.sucursal}</td>
                <td>${it.celular ?? '-'}</td>
                <td>${it.direccion ?? '-'}</td>
                <td class="text-center">${it.estado == 1 ? 'Activo' : 'Inactivo'}</td>
            `;
                    tbody.appendChild(tr);
                });

                // Habilitar PDF
                document.getElementById("btnPdf").classList.remove("d-none");

                // Copiar filtros al form PDF
                const pdf = document.getElementById("formPdf");
                ["sucursal", "cargo", "estado", "buscar"].forEach(k => {
                    pdf.querySelector(`[name="${k}"]`).value = fd.get(k);
                });
            });
    });

    document.getElementById("btnPdf").addEventListener("click", function() {
        document.getElementById("formPdf").submit();
    });
</script>