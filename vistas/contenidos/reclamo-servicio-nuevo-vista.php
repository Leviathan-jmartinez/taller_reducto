<?php
if (!mainModel::tienePermiso('servicio.reclamo.crear')) {
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
        &nbsp; RECLAMOS DE SERVICIO
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>/reclamo-servicio-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>/reclamo-servicio-lista/">
                <i class="fas fa-search fa-fw"></i> &nbsp; LISTADO DE RECLAMOS
            </a>
        </li>
    </ul>
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
                <div class="col-md-12 mt-2">
                    <label>Trabajos realizados</label>
                    <div id="trabajos_realizados"
                        class="form-control"
                        style="height:auto; min-height:60px; background:#f8f9fa;">
                    </div>
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

            <div class="row">

                <div class="col-md-3">
                    <label>Tipo</label>
                    <select name="tipo_reclamo" class="form-control">
                        <option value="SERVICIO">Servicio</option>
                        <option value="REPUESTO">Repuesto</option>
                        <option value="ATENCION">Atención</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Origen</label>
                    <select name="origen" class="form-control">
                        <option value="CLIENTE">Cliente</option>
                        <option value="INTERNO">Interno</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Prioridad</label>
                    <select name="prioridad" class="form-control">
                        <option value="1">Baja</option>
                        <option value="2" selected>Media</option>
                        <option value="3">Alta</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Garantía</label>
                    <select name="requiere_garantia" class="form-control">
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>

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