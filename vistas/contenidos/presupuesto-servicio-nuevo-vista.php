<?php
if (!mainModel::tienePermisoVista('servicio.presupuesto.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$usuario_nombre = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];
?>

<div class="container-fluid">

    <div class="container-fluid">
        <h3 class="text-left">
            <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; PRESUPUESTO DE SERVICIOS
        </h3>
        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>presupuesto-servicio-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PRESUPUESTO</a>
            </li>
            <li>
                <a href="<?php echo SERVERURL; ?>presupuesto-servicio-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTADOS DE PRESUPUESTOS</a>
            </li>
            <li>
                <a href="<?php echo SERVERURL; ?>presupuesto-servicio-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA</a>
            </li>
        </ul>
    </div>

    <form class="form-neon FormularioAjax"
        action="<?= SERVERURL; ?>ajax/presupuestoServicioAjax.php"
        method="POST"
        data-modulo="presupuesto"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="guardar_presupuesto" value="1">
        <input type="hidden" name="idrecepcion" id="idrecepcion">
        <input type="hidden" name="detalle_json" id="detalle_json">
        <input type="hidden" name="descuentos_json" id="descuentos_json">
        <input type="hidden" name="subtotal_servicios" id="inp_subtotal_servicios">
        <input type="hidden" name="total_descuento" id="inp_total_descuento">
        <input type="hidden" name="total_final" id="inp_total_final">


        <!-- ================= RECEPCIÓN ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Recepción</legend>

            <input type="hidden" name="id_cliente" id="id_cliente">
            <input type="hidden" name="id_vehiculo" id="id_vehiculo">

            <div class="row">
                <div class="col-md-6">
                    <label>Cliente</label>
                    <input type="text" id="cliente"
                        class="form-control" readonly
                        placeholder="Seleccione una recepción">
                </div>

                <div class="col-md-6">
                    <label>Vehículo</label>
                    <input type="text" id="vehiculo"
                        class="form-control" readonly>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4">
                    <label>Kilometraje</label>
                    <input type="text" id="kilometraje"
                        class="form-control" readonly>
                </div>

                <div class="col-md-8">
                    <label>Observación</label>
                    <input type="text" id="observacion"
                        class="form-control" readonly>
                </div>
            </div>

            <div class="mt-3 text-right">
                <button type="button" class="btn btn-info"
                    onclick="abrirModalRecepcion()">
                    <i class="fas fa-search"></i> Buscar recepción
                </button>

                <button type="button" class="btn btn-warning"
                    onclick="limpiarFormularioPresupuesto()">
                    <i class="fas fa-undo"></i> Limpiar
                </button>
            </div>
        </fieldset>

        <!-- ================= DATOS GENERALES ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos generales</legend>

            <div class="row">
                <div class="col-md-4">
                    <label>Fecha</label>
                    <input type="date" class="form-control"
                        value="<?= date('Y-m-d'); ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label>Usuario</label>
                    <input type="text" class="form-control"
                        value="<?= $usuario_nombre; ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label>Fecha vencimiento</label>
                    <input type="date" name="fecha_venc"
                        class="form-control">
                </div>
            </div>
        </fieldset>

        <!-- ================= DETALLE DE SERVICIOS ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Detalle de servicios</legend>

            <div class="row mb-2">
                <div class="col-md-6">
                    <input type="text"
                        id="buscar_servicio"
                        class="form-control"
                        placeholder="Buscar servicio o artículo"
                        onkeyup="buscarServicio()">
                </div>
            </div>

            <div id="resultado_servicios"></div>

            <div class="table-responsive mt-3">
                <table class="table table-dark table-sm" id="tabla_detalle">
                    <thead class="text-center">
                        <tr>
                            <th>Servicio</th>
                            <th width="10%">Cant.</th>
                            <th width="15%">Precio Unitario</th>
                            <th width="15%">Subtotal</th>
                            <th width="15%">Promoción</th>
                            <th width="15%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- JS agrega filas -->
                    </tbody>
                </table>
            </div>
        </fieldset>

        <!-- ================= SUBTOTAL ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Subtotal</legend>

            <div class="text-right">
                <strong>Subtotal servicios:</strong>
                <span id="txt_subtotal_servicios">Gs. 0</span>
            </div>
        </fieldset>

        <!-- ================= DESCUENTOS ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Descuentos disponibles</legend>

            <div id="descuentos_cliente">
                <span class="text-muted">
                    Seleccione una recepción para ver descuentos
                </span>
            </div>
        </fieldset>

        <!-- ================= TOTALES ================= -->
        <fieldset class="border p-3 mb-4">
            <legend class="w-auto px-2">Totales</legend>

            <div class="text-right">
                <p>Subtotal:
                    <strong id="total_subtotal">Gs. 0</strong>
                </p>
                <p>Descuentos:
                    <strong id="txt_total_descuento">Gs. 0</strong>
                </p>
                <h4>
                    TOTAL FINAL:
                    <strong id="txt_total_final">Gs. 0</strong>
                </h4>
            </div>
        </fieldset>

        <!-- ================= BOTONES ================= -->
        <div class="text-center">
            <button type="submit" class="btn btn-info btn-raised ">
                <i class="fas fa-save"></i> &nbsp; Guardar
            </button>

            <a href="<?= SERVERURL; ?>presupuesto-servicio-lista/"
                class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

    </form>
</div>

<!-- ================= MODAL RECEPCIÓN ================= -->
<div class="modal fade" id="modalRecepcion">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Seleccionar recepción</h5>
                <button type="button" class="close"
                    data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="text" class="form-control mb-3"
                    placeholder="Buscar por cliente o vehículo"
                    onkeyup="buscarRecepcion(this.value)">

                <div id="resultado_recepcion"></div>
            </div>

        </div>
    </div>
</div>

<?php include_once "./vistas/inc/presupuestoServicio.php"; ?>