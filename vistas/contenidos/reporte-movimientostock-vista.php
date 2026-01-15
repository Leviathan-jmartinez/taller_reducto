<?php
if (!mainModel::tienePermisoVista('stock.movimiento.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/reportesControlador.php";
$rep = new reporteControlador();

$sucursales = $rep->listar_sucursales_controlador();
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-exchange-alt"></i> &nbsp; MOVIMIENTOS DE STOCK
    </h3>
</div>

<div class="container-fluid">
    <form id="formPreview" class="form-neon" autocomplete="off">
        <input type="hidden" name="modulo" value="movimientos_stock">

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
                <label>Tipo de movimiento</label>
                <select name="tipo" class="form-control">
                    <option value="T">Todos</option>
                    <option value="RECEPCION COMPRA">Recepción Compra</option>
                    <option value="ANULACION COMPRA">Anulación Compra</option>
                    <option value="AJUSTE_INV">Ajuste Inventario</option>
                    <option value="ANULACION_AJUSTE_INV">Anulación Ajuste</option>
                    <option value="REG. SERVICIO">Registro Servicio</option>
                    <option value="ANULACION REG. SERVICIO">Anulación Reg. Servicio</option>
                    <option value="TRANSFERENCIA_SALIDA">Transferencia Salida</option>
                    <option value="TRANSFERENCIA_ENTRADA">Transferencia Entrada</option>
                    <option value="NC_COMPRA_DEV">NC Compra Dev.</option>
                    <option value="ANULA_NC_COMPRA">Anula NC Compra</option>
                </select>
            </div>

            <div class="col-md-2">
                <label>Signo</label>
                <select name="signo" class="form-control">
                    <option value="T">Todos</option>
                    <option value="P">Entradas (+)</option>
                    <option value="N">Salidas (-)</option>
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

<form id="formPdf"
    action="<?= SERVERURL ?>ajax/reportesAjax.php"
    method="POST"
    target="_blank"
    class="d-none">
    <input type="hidden" name="accion" value="imprimir_reporte_movimientos_stock">
    <input type="hidden" name="sucursal">
    <input type="hidden" name="tipo">
    <input type="hidden" name="signo">
    <input type="hidden" name="desde">
    <input type="hidden" name="hasta">
</form>

<div class="container-fluid mt-3" id="resumenMov" style="display:none;">
    <div class="row text-center">
        <div class="col">Total<br><strong id="r_total">0</strong></div>
        <div class="col">Entradas<br><strong id="r_ent">0</strong></div>
        <div class="col">Salidas<br><strong id="r_sal">0</strong></div>
    </div>
</div>

<div class="container-fluid mt-3">
    <div class="table-responsive">
        <table class="table table-dark table-sm" id="tablaMov">
            <thead class="text-center">
                <tr>
                    <th>Fecha</th>
                    <th>Sucursal</th>
                    <th>Tipo</th>
                    <th>Artículo</th>
                    <th>Cant.</th>
                    <th>Signo</th>
                    <th>Referencia</th>
                    <th>Usuario</th>
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

        fetch("<?= SERVERURL ?>ajax/reportesAjax.php", {
                method: "POST",
                body: fd
            })
            .then(r => r.json())
            .then(json => {

                document.getElementById("resumenMov").style.display = "block";
                document.getElementById("r_total").innerText = json.resumen.total;
                document.getElementById("r_ent").innerText = json.resumen.entradas;
                document.getElementById("r_sal").innerText = json.resumen.salidas;

                let tbody = document.querySelector("#tablaMov tbody");
                tbody.innerHTML = "";

                json.data.forEach(it => {
                    let tr = document.createElement("tr");
                    tr.innerHTML = `
                <td>${it.MovStockFechaHora}</td>
                <td>${it.sucursal}</td>
                <td>${it.TipoMovStockId}</td>
                <td>${it.desc_articulo}</td>
                <td class="text-right">${it.MovStockCantidad}</td>
                <td class="text-center">${it.MovStockSigno == 1 ? '+' : '-'}</td>
                <td>${it.MovStockReferencia ?? ''}</td>
                <td>${it.usuario}</td>
            `;
                    tbody.appendChild(tr);
                });

                document.getElementById("btnPdf").classList.remove("d-none");

                const pdf = document.getElementById("formPdf");
                ["sucursal", "tipo", "signo", "desde", "hasta"].forEach(k => {
                    pdf.querySelector(`[name="${k}"]`).value = fd.get(k);
                });
            });
    });

    document.getElementById("btnPdf").addEventListener("click", function() {
        document.getElementById("formPdf").submit();
    });
</script>