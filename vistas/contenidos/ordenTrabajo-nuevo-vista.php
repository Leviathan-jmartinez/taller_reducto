<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$usuario_nombre = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];
$id_usuario     = $_SESSION['id_str'];
?>

<div class="container-fluid">

    <h3 class="text-left">
        <i class="fas fa-tools fa-fw"></i> &nbsp; GENERAR ORDEN DE TRABAJO
    </h3>

    <!-- ================= FORM OT ================= -->
    <form class="form-neon FormularioAjax"
        action="<?= SERVERURL; ?>ajax/ordenTrabajoAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="generar_ot2" value="1">
        <input type="hidden" name="idpresupuesto_servicio" id="idpresupuesto_servicio">
        <input type="hidden" name="idrecepcion" id="idrecepcion">
        <input type="hidden" name="id_usuario" value="<?= $id_usuario; ?>">

        <!-- ================= PRESUPUESTO ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Presupuesto</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Cliente</label>
                    <input type="text" id="cliente"
                        class="form-control" readonly>
                </div>

                <div class="col-md-6">
                    <label>Vehículo</label>
                    <input type="text" id="vehiculo"
                        class="form-control" readonly>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4">
                    <label>Presupuesto Nº</label>
                    <input type="text" id="nro_presupuesto"
                        class="form-control" readonly>
                </div>

                <div class="col-md-8 text-right align-self-end">
                    <button type="button" class="btn btn-info"
                        onclick="abrirModalPresupuesto()">
                        <i class="fas fa-search"></i> Buscar presupuesto
                    </button>
                </div>
            </div>
        </fieldset>

        <!-- ================= TECNICO ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Asignación</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Técnico encargado</label>
                    <select name="idtrabajos" id="idtrabajos" class="form-control" required>
                        <option value="">Seleccione técnico</option>
                        <!-- AJAX: equipo_trabajo -->
                    </select>
                </div>

                <div class="col-md-6">
                    <label>Usuario</label>
                    <input type="text"
                        class="form-control"
                        value="<?= $usuario_nombre; ?>"
                        readonly>
                </div>
            </div>

            <div class="mt-2">
                <label>Observación</label>
                <textarea name="observacion"
                    class="form-control"
                    rows="2"></textarea>
            </div>
        </fieldset>

        <!-- ================= DETALLE ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Servicios presupuestados</legend>

            <div class="table-responsive">
                <table class="table table-dark table-sm">
                    <thead class="text-center">
                        <tr>
                            <th>Servicio</th>
                            <th>Cant.</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="detalle_presupuesto">
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Seleccione un presupuesto
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>

        <!-- ================= BOTONES ================= -->
        <div class="text-center">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-tools"></i> Generar Orden de Trabajo
            </button>

            <a href="<?= SERVERURL; ?>orden-trabajo-lista/"
                class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

    </form>
</div>

<!-- ================= MODAL PRESUPUESTO ================= -->
<div class="modal fade" id="modalPresupuesto">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Presupuestos aprobados</h5>
                <button type="button" class="close"
                    data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="text" class="form-control mb-3"
                    placeholder="Buscar por cliente o vehículo"
                    onkeyup="buscarPresupuesto(this.value)">

                <div id="resultado_presupuesto"></div>
            </div>

        </div>
    </div>
</div>


<?php include_once "./vistas/inc/ordenTrabajoJS.php";?>