<?php
if (!mainModel::tienePermiso('servicio.diagnostico.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<div class="container-fluid">
    <h3>
        <i class="fas fa-tools"></i> &nbsp; DIAGNÓSTICO DE SERVICIO
    </h3>

    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>/diagnostico-servicio-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO DIAGNÓSTICO
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>/diagnostico-servicio-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR DIAGNÓSTICOS
            </a>
        </li>
    </ul>
</div>

<div id="alerta_reclamo" class="alert alert-warning" style="display:none;">
    <i class="fas fa-exclamation-triangle"></i>
    Recepción generada desde reclamo
</div>

<div id="card_reclamo" style="display:none;">
    <div class="card border-warning mb-3">
        <div class="card-header bg-warning text-dark">
            <i class="fas fa-exclamation-circle"></i> Detalle del Reclamo
        </div>

        <div class="card-body">

            <div class="row">
                <div class="col-md-6">
                    <strong>Descripción:</strong><br>
                    <span id="rec_desc"></span>
                </div>

                <div class="col-md-2">
                    <strong>Tipo:</strong><br>
                    <span id="rec_tipo"></span>
                </div>

                <div class="col-md-2">
                    <strong>Prioridad:</strong><br>
                    <span id="rec_prioridad"></span>
                </div>

                <div class="col-md-2">
                    <strong>Fecha:</strong><br>
                    <span id="rec_fecha"></span>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="container-fluid">




    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/diagnosticoAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="guardar_diagnostico">
        <input type="hidden" name="id_sucursal" id="id_sucursal">
        <!-- RECEPCIÓN -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Recepción</legend>

            <div class="row">
                <div class="col-md-10">
                    <input type="hidden" name="idrecepcion" id="idrecepcion">
                    <input type="text" class="form-control" id="recepcion_info"
                        placeholder="Seleccione una recepción" readonly>
                </div>

                <div class="col-md-2">
                    <button type="button"
                        class="btn btn-info btn-block"
                        onclick="abrirModalRecepcion()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </fieldset>

        <fieldset id="bloque_reclamo_resultado" class="border p-3 mb-3" style="display:none;">
            <legend>Resultado del Reclamo</legend>

            <div class="row">

                <div class="col-md-4">
                    <label>¿Es reclamo válido?</label>
                    <select name="es_reclamo_valido" class="form-control">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>¿Es garantía?</label>
                    <select name="es_garantia" class="form-control">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>¿Requiere cobro?</label>
                    <select name="requiere_cobro" class="form-control">
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>

            </div>
        </fieldset>

        <!-- DATOS DIAGNÓSTICO -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos del Diagnóstico</legend>

            <div class="row">

                <div class="col-md-4">
                    <label>Fecha</label>
                    <input type="datetime-local" name="fecha"
                        class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Equipo de trabajo</label>
                    <select name="id_equipo" id="id_equipo" class="form-control" required>
                        <option value="">Seleccione equipo</option>
                        <!-- cargar vía PHP o AJAX -->
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="0">Pendiente</option>
                        <option value="1" selected>En proceso</option>
                        <option value="2">Finalizado</option>
                    </select>
                </div>

            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <label>Observación general</label>
                    <textarea name="observacion"
                        class="form-control"
                        rows="3"></textarea>
                </div>
            </div>


        </fieldset>

        <!-- DETALLES -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Detalle del Diagnóstico</legend>

            <table class="table table-dark table-sm">
                <thead>
                    <tr>
                        <th>Sistema</th>
                        <th>Problema</th>
                        <th>Gravedad</th>
                        <th>Solución</th>
                        <th>Repuesto</th>
                        <th>Mano de obra</th>
                        <th width="50">Acción</th>
                    </tr>
                </thead>

                <tbody id="detalleDiagnostico"></tbody>
            </table>

            <button type="button" class="btn btn-success"
                onclick="agregarDetalleDiagnostico()">
                <i class="fas fa-plus"></i> Agregar
            </button>

        </fieldset>

        <!-- BOTONES -->
        <div class="text-center">
            <button type="submit" class="btn btn-info btn-raised">
                <i class="fas fa-save"></i> Guardar
            </button>

            <button type="button"
                class="btn btn-secondary btn-raised"
                onclick="limpiarDiagnostico()">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </div>

    </form>
    <div class="modal fade" id="modalRecepcion" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-search"></i> Buscar Recepción
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        &times;
                    </button>
                </div>

                <div class="modal-body">

                    <input type="text"
                        id="buscar_recepcion"
                        class="form-control mb-3"
                        placeholder="Buscar cliente o placa"
                        onkeyup="buscarRecepcionAjax()">

                    <div id="tabla_recepciones"></div>

                </div>

            </div>
        </div>
    </div>
</div>

<?php include "./vistas/inc/diagnosticoJS.php"; ?>
