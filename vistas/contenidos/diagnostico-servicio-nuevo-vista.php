<?php
if (!mainModel::tienePermisoVista('servicio.diagnostico.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<div class="container-fluid">

    <h3 class="text-left">
        <i class="fas fa-stethoscope fa-fw"></i> &nbsp; NUEVO DIAGNÓSTICO
    </h3>

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/diagnosticoAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="guardar_diagnostico">

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

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Tipo</th>
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
                <i class="fas fa-save"></i> Guardar Diagnóstico
            </button>

            <button type="button"
                class="btn btn-secondary btn-raised"
                onclick="limpiarDiagnostico()">
                <i class="fas fa-times"></i> Limpiar
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