<?php

if (!mainModel::tienePermiso('servicio.ot.generar')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$usuario_nombre = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];
$id_usuario     = $_SESSION['id_str'];
?>

<div class="container-fluid form-neon">

    <h3>
        <i class="fas fa-tools"></i> &nbsp; ORDEN DE TRABAJO
    </h3>

    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>ordenTrabajo-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>ordenTrabajo-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR
            </a>
        </li>
    </ul>

    <form class="form-neon FormularioAjax"
        action="<?= SERVERURL; ?>ajax/ordenTrabajoAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="generar_ot2" value="1">
        <input type="hidden" name="idpresupuesto_servicio" id="idpresupuesto_servicio">
        <input type="hidden" name="idrecepcion" id="idrecepcion">
        <input type="hidden" name="id_usuario" value="<?= $id_usuario; ?>">

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Origen de la orden</legend>

            <div class="row">
                <div class="col-md-5">
                    <label>Cliente</label>
                    <input type="text" id="cliente" class="form-control" readonly>
                </div>

                <div class="col-md-4">
                    <label>Vehiculo</label>
                    <input type="text" id="vehiculo" class="form-control" readonly>
                </div>

                <div class="col-md-3">
                    <label>Presupuesto Nro.</label>
                    <input type="text" id="nro_presupuesto" class="form-control" readonly>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4">
                    <label>Fecha presupuesto</label>
                    <input type="text" id="fecha_presupuesto" class="form-control" readonly>
                </div>
                <div class="col-md-8 text-right align-self-end">
                    <button type="button" class="btn btn-success" onclick="abrirModalPresupuesto()">
                        <i class="fas fa-search"></i> Buscar presupuesto
                    </button>
                </div>
            </div>
        </fieldset>

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Asignacion</legend>

            <div class="row">
                <div class="col-md-4">
                    <label>Equipo encargado</label>
                    <select name="idtrabajos" id="idtrabajos" class="form-control" required>
                        <option value="">Seleccione equipo de trabajo</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Tecnico responsable</label>
                    <select name="tecnico_responsable" id="tecnico_responsable" class="form-control" required>
                        <option value="">Seleccione un tecnico</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Usuario</label>
                    <input type="text" class="form-control" value="<?= $usuario_nombre; ?>" readonly>
                </div>
            </div>

            <div class="mt-2">
                <label>Instrucciones internas</label>
                <textarea name="observacion"
                    class="form-control"
                    rows="2"
                    placeholder="Indicaciones para el equipo de taller"></textarea>
            </div>
        </fieldset>

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Trabajos y repuestos autorizados</legend>

            <div class="table-responsive">
                <table class="table table-dark table-sm">
                    <thead class="text-center">
                        <tr>
                            <th>Detalle</th>
                            <th>Tipo</th>
                            <th>Cant.</th>
                            <th>Estado inicial</th>
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

        <div class="text-center">
            <button type="submit" class="btn btn-info btn-raised">
                <i class="fas fa-tools"></i> &nbsp;Generar Orden de Trabajo
            </button>
            &nbsp;
            <button type="button"
                class="btn btn-secondary btn-raised"
                onclick="limpiarOrdenTrabajo()">
                <i class="fas fa-times"></i> &nbsp; Cancelar
            </button>
        </div>

    </form>
</div>

<div class="modal fade" id="modalPresupuesto">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Presupuestos aprobados</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="text"
                    class="form-control mb-3"
                    placeholder="Buscar por cliente o vehiculo"
                    onkeyup="buscarPresupuesto(this.value)">

                <div id="resultado_presupuesto"></div>
            </div>

        </div>
    </div>
</div>

<?php include_once "./vistas/inc/ordenTrabajoJS.php"; ?>
