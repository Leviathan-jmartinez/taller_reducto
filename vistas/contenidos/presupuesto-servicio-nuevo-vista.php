<?php
if (!mainModel::tienePermiso('servicio.presupuesto.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$usuario_nombre = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];
?>

<div class="container-fluid form-neon">

    <div class="container-fluid">
        <h3 class="text-left">
            <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; PRESUPUESTO DE SERVICIOS
        </h3>
        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>presupuesto-servicio-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO</a>
            </li>
            <li>
                <a href="<?php echo SERVERURL; ?>presupuesto-servicio-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR</a>
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
        <input type="hidden" name="origen_presupuesto" id="origen_presupuesto" value="DIAGNOSTICO">
        <input type="hidden" name="convertido_desde" id="convertido_desde">
        <input type="hidden" name="id_diagnostico" id="id_diagnostico">
        <input type="hidden" id="id_sucursal" name="id_sucursal">
        <input type="hidden" id="id_cliente" name="id_cliente">
        <input type="hidden" id="id_vehiculo" name="id_vehiculo">
        <input type="hidden" id="diagnostico_info" name="diagnostico_info">           
        <input type="hidden" name="detalle_json" id="detalle_json">
        <input type="hidden" name="descuentos_json" id="descuentos_json">
        <input type="hidden" name="subtotal_servicios" id="inp_subtotal_servicios">
        <input type="hidden" name="total_descuento" id="inp_total_descuento">
        <input type="hidden" name="total_final" id="inp_total_final">

        <!-- ================= TIPO DE PRESUPUESTO ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Tipo de presupuesto</legend>

            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="modo_diagnostico" name="modo_presupuesto" class="custom-control-input" value="DIAGNOSTICO" checked onchange="cambiarModoPresupuesto(this.value)">
                <label class="custom-control-label" for="modo_diagnostico">Con diagnostico</label>
            </div>

            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="modo_preliminar" name="modo_presupuesto" class="custom-control-input" value="PRELIMINAR" onchange="cambiarModoPresupuesto(this.value)">
                <label class="custom-control-label" for="modo_preliminar">Preliminar</label>
            </div>
        </fieldset>


        <!-- ================= DIAGNÓSTICO ================= -->
        <fieldset class="border p-3 mb-3" id="bloque_diagnostico">
            <legend class="w-auto px-2">Datos del Diagnóstico</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Cliente</label>
                    <input type="text" id="cliente"
                        class="form-control" readonly
                        placeholder="Seleccione un diagnóstico">
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
                <div class="col-md-6">
                    <label>Observación</label>
                    <input type="text" id="observacion"
                        class="form-control" readonly>
                </div>
            </div>

            <div class="table-responsive mt-2">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Servicio</th>
                            <th>Origen repuesto</th>
                            <th>Repuesto</th>
                            <th>Cant.</th>
                            <th>Gravedad</th>
                            <th>Observacion</th>
                        </tr>
                    </thead>
                    <tbody id="lista_diagnostico"></tbody>
                </table>
            </div>
            <div class="mt-3 text-right">
                <button type="button" class="btn btn-info"
                    onclick="abrirModalDiagnostico()">
                    <i class="fas fa-search"></i> Buscar diagnóstico
                </button>

                <button type="button" class="btn btn-warning"
                    onclick="limpiarFormularioPresupuesto()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>

            <div id="presupuestos_preliminares" class="mt-3"></div>
        </fieldset>

        <!-- ================= PRELIMINAR ================= -->
        <fieldset class="border p-3 mb-3" id="bloque_preliminar" style="display:none;">
            <legend class="w-auto px-2">Datos preliminares</legend>

            <div class="alert alert-info">
                Cotizacion referencial sujeta a recepcion y diagnostico tecnico.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label>Cliente</label>
                    <div class="input-group">
                        <input type="text" id="cliente_preliminar" class="form-control" readonly placeholder="Seleccione un cliente">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-info" onclick="abrirModalClientePresupuesto()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label>Vehiculo</label>
                    <div class="input-group">
                        <input type="text" id="vehiculo_preliminar" class="form-control" readonly placeholder="Seleccione un vehiculo">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-info" onclick="abrirModalVehiculoPresupuesto()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
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
                        class="form-control"
                        min="<?= date('Y-m-d'); ?>"
                        required>
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
                        onkeyup="buscarServicio()"
                        disabled>
                </div>
            </div>

            <div id="resultado_servicios"></div>

            <div id="aviso_detalle_preliminar" class="mt-3" style="display:none;"></div>

            <div class="table-responsive mt-3">
                <table class="table table-dark table-sm" id="tabla_detalle">
                    <thead class="text-center">
                        <tr>
                            <th>Servicio</th>
                            <th width="10%">Cant.</th>
                            <th width="14%">Precio Unitario</th>
                            <th width="13%">Subtotal</th>
                            <th width="13%">Promoción</th>
                            <th width="15%">Subtotal Final</th>
                            <th width="10%"></th>
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
                <br>
                <strong>Promociones:</strong>
                <span id="txt_total_promociones">Gs. 0</span>
            </div>
        </fieldset>

        <!-- ================= DESCUENTOS ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Descuentos disponibles</legend>

            <div id="descuentos_cliente">
                <span class="text-muted">
                    Seleccione un diagnóstico para ver descuentos
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
                <p>Promociones:
                    <strong id="total_promociones">Gs. 0</strong>
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
            <button type="submit" id="btn_guardar_presupuesto_servicio" class="btn btn-info btn-raised " disabled>
                <i class="fas fa-save"></i> &nbsp; Guardar
            </button>

            <button type="button" class="btn btn-secondary btn-raised"
                onclick="limpiarFormularioPresupuesto()">
                <i class="fas fa-times"></i> &nbsp; Cancelar
            </button>
        </div>

    </form>
</div>

<!-- ================= MODAL DIAGNÓSTICO ================= -->
<div class="modal fade" id="modalDiagnostico">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Seleccionar diagnostico</h5>
                <button type="button" class="close"
                    data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="text" id="buscar_diagnostico"
                    class="form-control mb-3"
                    placeholder="Buscar por cliente o vehículo"
                    onkeyup="buscarDiagnostico()">

                <div id="tabla_diagnostico"></div>
            </div>

        </div>
    </div>
</div>

<!-- ================= MODAL CLIENTE ================= -->
<div class="modal fade" id="modalClientePresupuesto">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Seleccionar cliente</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="text" id="buscar_cliente"
                    class="form-control mb-3"
                    placeholder="Buscar cliente"
                    onkeyup="buscarClientePresupuesto()">

                <div id="tabla_clientes"></div>
            </div>

        </div>
    </div>
</div>

<!-- ================= MODAL VEHICULO ================= -->
<div class="modal fade" id="modalVehiculoPresupuesto">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Seleccionar vehiculo</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="text" id="buscar_vehiculo"
                    class="form-control mb-3"
                    placeholder="Buscar vehiculo"
                    onkeyup="buscarVehiculoPresupuesto()">

                <div id="tabla_vehiculos"></div>
            </div>

        </div>
    </div>
</div>

<?php include_once "./vistas/inc/presupuestoServicio.php"; ?>
