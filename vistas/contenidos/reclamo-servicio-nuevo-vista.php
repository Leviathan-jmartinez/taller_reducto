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

<div class="container-fluid reclamo-servicio app-view">
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
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR
            </a>
        </li>
    </ul>
</div>

<div class="container-fluid reclamo-servicio app-view">

    <form class="FormularioAjax app-form"
        action="<?= SERVERURL ?>ajax/reclamoServicioAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="registrar_reclamo">
        <input type="hidden" name="idregistro_servicio" id="idregistro_servicio">
        <input type="hidden" name="detalles_reclamo_json" id="detalles_reclamo_json">

        <!-- ================= BUSCAR REGISTRO ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Seleccionar servicio realizado</legend>

            <div class="row mb-3">
                <div class="col-md-8">
                    <input type="text"
                        id="buscar_registro"
                        class="form-control"
                        placeholder="Buscar por cliente, vehículo, registro u OT"
                        onkeyup="buscarRegistro(this.value)">
                </div>
            </div>

            <div id="resultado_registro" class="mb-3"></div>

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
                    <label>Detalle reclamado</label>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th></th>
                                    <th>Item</th>
                                    <th>Tipo</th>
                                    <th>Cant.</th>
                                    <th>Origen</th>
                                    <th>Motivo especifico</th>
                                </tr>
                            </thead>
                            <tbody id="detalle_reclamo_items">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        Seleccione un servicio realizado
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12 mt-2">
                    <div id="garantia_estado" class="alert alert-secondary mb-0">
                        Seleccione un servicio para validar la garantia.
                    </div>
                </div>
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
                    <select name="tipo_reclamo" id="tipo_reclamo" class="form-control" onchange="actualizarTipoReclamo()">
                        <option value="SERVICIO">Servicio</option>
                        <option value="REPUESTO">Repuesto</option>
                        <option value="ATENCION">Atención</option>
                        <option value="GENERAL">General</option>
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
                    <select name="requiere_garantia" id="requiere_garantia" class="form-control" disabled>
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>

            </div>
        </fieldset>

        <div class="text-center app-actions">
            <button class="btn btn-info btn-raised" id="btnRegistrarReclamo" disabled>
                <i class="fas fa-save"></i> &nbsp; Registrar reclamo
            </button>
            <button type="button" class="btn btn-secondary btn-raised"
                onclick="limpiarReclamoServicio()">
                <i class="fas fa-times"></i> &nbsp; Cancelar
            </button>
        </div>

    </form>
</div>



<?php include_once "./vistas/inc/reclamoServicioJS.php"; ?>
