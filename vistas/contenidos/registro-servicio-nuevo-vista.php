<?php
if (!mainModel::tienePermisoVista('servicio.registro.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
} ?>

<style>
    /* Contenedor principal SOLO registro de servicio */
    .registro-servicio-bg {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 6px;
    }

    /* Bloques internos */
    .registro-servicio-bg fieldset {
        background-color: #ffffff;
        border-radius: 4px;
    }

    /* Tablas */
    .registro-servicio-bg .table,
    .registro-servicio-bg .table-responsive {
        background-color: #ffffff;
    }

    /* Encabezados */
    .registro-servicio-bg h3 {
        margin-bottom: 15px;
    }
</style>

<div class="container-fluid registro-servicio-bg">

    <!-- ================= TÍTULO ================= -->
    <div class="container-fluid mb-3">
        <h3>
            <i class="fas fa-clipboard-check"></i>
            &nbsp; REGISTRO DE SERVICIO
        </h3>

        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>/registro-servicio-nuevo/">
                    <i class="fas fa-plus fa-fw"></i> &nbsp; REGISTRO DE SERVICIO
                </a>
            </li>
            <li>
                <a href="<?php echo SERVERURL; ?>/registro-servicio-lista/">
                    <i class="fas fa-search fa-fw"></i> &nbsp; HISTORIAL DE SERVICIOS
                </a>
            </li>
            <li>
                <a href="<?php echo SERVERURL; ?>registro-servicio-buscar/">
                    <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR
                </a>
            </li>
        </ul>
    </div>

    <!-- ================= BUSCAR OT ================= -->
    <fieldset class="border p-3 mb-3">
        <legend class="w-auto px-2">Seleccionar Orden de Trabajo</legend>

        <div class="row">
            <div class="col-md-8">
                <input type="text"
                    class="form-control"
                    placeholder="Buscar por cliente, vehículo o Nº OT"
                    onkeyup="buscarOT(this.value)">
            </div>
        </div>

        <div id="resultado_ot" class="mt-3"></div>
    </fieldset>

    <!-- ================= DATOS OT ================= -->
    <fieldset class="border p-3 mb-3">
        <legend class="w-auto px-2">Datos de la Orden</legend>

        <div class="row">
            <div class="col-md-3">
                <label>OT Nº</label>
                <input type="text" id="ot_numero" class="form-control" readonly>
            </div>
            <div class="col-md-5">
                <label>Cliente</label>
                <input type="text" id="ot_cliente" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label>Vehículo</label>
                <input type="text" id="ot_vehiculo" class="form-control" readonly>
            </div>
        </div>
    </fieldset>

    <!-- ================= DETALLE ================= -->
    <fieldset class="border p-3 mb-3">
        <legend class="w-auto px-2">Detalle aprobado</legend>

        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="thead-light text-center">
                    <tr>
                        <th>Artículo / Servicio</th>
                        <th>Cant.</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="detalle_ot">
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Seleccione una orden de trabajo
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </fieldset>

    <!-- ================= CONFIRMACIÓN ================= -->
    <form class="FormularioAjax"
        action="<?= SERVERURL ?>ajax/registroServicioAjax.php"
        method="POST"
        data-form="save">

        <input type="hidden" name="accion" value="registrar_servicio">
        <input type="hidden" name="idorden_trabajo" id="idorden_trabajo">

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Confirmación</legend>

            <div class="row">
                <div class="col-md-4">
                    <label>Fecha de ejecución</label>
                    <input type="date"
                        name="fecha_ejecucion"
                        class="form-control"
                        value="<?= date('Y-m-d') ?>"
                        disabled
                        required>
                </div>
                <div class="col-md-8">
                    <label>Observación</label>
                    <input type="text"
                        name="observacion"
                        class="form-control"
                        disabled>
                </div>
            </div>

            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle"></i>
                Al registrar el servicio, la orden quedará finalizada.
            </div>
        </fieldset>

        <div class="text-center">
            <button class="btn btn-info btn-raised " id="btnRegistrar">
                <i class="fas fa-save"></i> &nbsp; Registrar Servicio
            </button>
        </div>

    </form>

</div>

<?php include_once "./vistas/inc/registroServicioJS.php"; ?>