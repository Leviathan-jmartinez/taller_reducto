<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$usuario_nombre = $_SESSION['nombre_str'] .' '. $_SESSION['apellido_str'] ;
?>

<div class="container-fluid">

    <h3 class="text-left mb-3">
        <i class="fas fa-file-invoice-dollar"></i>
        &nbsp; PRESUPUESTO DE SERVICIO
    </h3>

    <form class="form-neon FormularioAjax"
        action="<?= SERVERURL; ?>ajax/presupuestoServicioAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="guardar_presupuesto_servicio">
        <input type="hidden" name="id_usuario" value="<?= $id_usuario; ?>">

        <!-- ================= RECEPCIÓN ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Recepción</legend>

            <input type="hidden" name="idrecepcion" id="idrecepcion">
            <input type="hidden" name="id_cliente" id="id_cliente">
            <input type="hidden" name="id_vehiculo" id="id_vehiculo">

            <div class="row">
                <div class="col-md-6">
                    <label>Cliente</label>
                    <input type="text" id="cliente_txt"
                        class="form-control" readonly
                        placeholder="Seleccione una recepción">
                </div>

                <div class="col-md-6">
                    <label>Vehículo</label>
                    <input type="text" id="vehiculo_txt"
                        class="form-control" readonly>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4">
                    <label>Kilometraje</label>
                    <input type="text" id="kilometraje_txt"
                        class="form-control" readonly>
                </div>

                <div class="col-md-8">
                    <label>Observación</label>
                    <input type="text" id="observacion_txt"
                        class="form-control" readonly>
                </div>
            </div>

            <div class="mt-3 text-right">
                <button type="button" class="btn btn-info"
                    onclick="abrirModalRecepcion()">
                    <i class="fas fa-search"></i> Buscar recepción
                </button>

                <button type="button" class="btn btn-warning"
                    onclick="limpiarRecepcion()">
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
                <table class="table table-bordered table-sm" id="tabla_detalle">
                    <thead class="text-center">
                        <tr>
                            <th>Servicio</th>
                            <th width="10%">Cant.</th>
                            <th width="15%">Precio</th>
                            <th width="15%">Subtotal</th>
                            <th width="5%"></th>
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
                <span id="subtotal_servicios">Gs. 0</span>
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
                    <strong id="total_descuento">Gs. 0</strong>
                </p>
                <h4>
                    TOTAL FINAL:
                    <strong id="total_final">Gs. 0</strong>
                </h4>
            </div>
        </fieldset>

        <!-- ================= BOTONES ================= -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar presupuesto
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