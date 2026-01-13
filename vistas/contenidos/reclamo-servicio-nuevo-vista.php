<?php
if (!mainModel::tienePermisoVista('servicio.reclamo.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
} ?>

<style>
    /* Contenedor principal SOLO registro de servicio */
    .reclamo-servicio {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 6px;
    }

    /* Bloques internos */
    .reclamo-servicio fieldset {
        background-color: #ffffff;
        border-radius: 4px;
    }

    /* Tablas */
    .reclamo-servicio .table,
    .reclamo-servicio .table-responsive {
        background-color: #ffffff;
    }

    /* Encabezados */
    .reclamo-servicio h3 {
        margin-bottom: 15px;
    }
</style>

<div class="container-fluid reclamo-servicio">
    <h3>
        <i class="fas fa-exclamation-circle"></i>
        &nbsp; RECLAMO DE SERVICIO
    </h3>
</div>

<div class="container-fluid reclamo-servicio">

    <form class="FormularioAjax"
        action="<?= SERVERURL ?>ajax/reclamoServicioAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="registrar_reclamo">
        <input type="hidden" name="idregistro_servicio" id="idregistro_servicio">

        <!-- ================= BUSCAR REGISTRO ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Servicio realizado</legend>

            <div class="row">
                <div class="col-md-4">
                    <label>Registro Nº</label>
                    <input type="text" id="registro_numero"
                        class="form-control" readonly>
                </div>

                <div class="col-md-4">
                    <label>Cliente</label>
                    <input type="text" id="cliente"
                        class="form-control" readonly>
                </div>

                <div class="col-md-4">
                    <label>Vehículo</label>
                    <input type="text" id="vehiculo"
                        class="form-control" readonly>
                </div>
            </div>

            <div class="text-right mt-2">
                <button type="button"
                    class="btn btn-info"
                    onclick="abrirModalRegistro()">
                    Buscar servicio
                </button>
            </div>
        </fieldset>

        <!-- ================= RECLAMO ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Detalle del reclamo</legend>

            <div class="form-group">
                <label>Descripción del reclamo</label>
                <textarea name="descripcion"
                    class="form-control"
                    rows="4"
                    required></textarea>
            </div>
        </fieldset>

        <div class="text-center">
            <button class="btn btn-info btn-raised">
                 <i class="fas fa-save"></i> &nbsp; Registrar reclamo
            </button>
        </div>

    </form>
</div>

<!-- ================= MODAL BUSCAR REGISTRO ================= -->
<div class="modal fade" id="modalRegistro">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Seleccionar servicio</h5>
                <button type="button" class="close"
                    data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="text"
                    class="form-control mb-2"
                    placeholder="Buscar por cliente o vehículo"
                    onkeyup="buscarRegistro(this.value)">

                <div id="resultado_registro"></div>
            </div>

        </div>
    </div>
</div>

<?php include_once "./vistas/inc/reclamoServicioJS.php"; ?>