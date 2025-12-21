<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>

<div class="container-fluid">

    <div class="container-fluid">
        <h3 class="text-left">
            <i class="fas fa-search fa-fw"></i> &nbsp; NUEVA RECEPCION
        </h3>
        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>recepcionServicio-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVA RECEPCION</a>
            </li>
            <li>
                <a href="<?php echo SERVERURL; ?>recepcionServicio-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR RECEPCION</a>
            </li>
        </ul>
    </div>

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/recepcionservicioAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <!-- ACCIÓN -->
        <input type="hidden" name="accion" value="guardar_recepcion">

        <!-- CLIENTE -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Cliente</legend>

            <div class="row">
                <div class="col-md-10">
                    <input type="hidden" name="id_cliente" id="id_cliente">
                    <input type="text" class="form-control" id="cliente_nombre"
                        placeholder="Seleccione un cliente" readonly>
                </div>

                <div class="col-md-2">
                    <button type="button"
                        class="btn btn-info btn-block"
                        data-toggle="modal"
                        data-target="#modalCliente">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </fieldset>

        <!-- VEHÍCULO -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Vehículo</legend>

            <div class="row">
                <div class="col-md-10">
                    <input type="hidden" name="id_vehiculo" id="id_vehiculo">
                    <input type="text" class="form-control" id="vehiculo_desc"
                        placeholder="Seleccione un vehículo" readonly>
                </div>

                <div class="col-md-2">
                    <button type="button"
                        class="btn btn-info btn-block"
                        onclick="validarClienteVehiculo()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </fieldset>

        <!-- DATOS DE RECEPCIÓN -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos de Recepción</legend>

            <div class="row">
                <div class="col-md-4">
                    <label>Fecha y hora de ingreso</label>
                    <input type="datetime-local" class="form-control"
                        name="fecha_ingreso" required>
                </div>

                <div class="col-md-4">
                    <label>Kilometraje</label>
                    <input type="number" class="form-control"
                        name="kilometraje" min="0" required>
                </div>

                <div class="col-md-4">
                    <label>Estado</label>
                    <select name="estado" class="form-control" required>
                        <option value="1" selected>Recepcionado</option>
                    </select>
                </div>
            </div>
        </fieldset>

        <!-- OBSERVACIÓN -->
        <fieldset class="border p-3 mb-4">
            <legend class="w-auto px-2">Observación / Reclamo del Cliente</legend>

            <textarea class="form-control" name="observacion"
                rows="4" required
                placeholder="Describa el problema informado por el cliente"></textarea>
        </fieldset>

        <!-- BOTONES -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> &nbsp; Guardar Recepción
            </button>

            <button type="button"
                class="btn btn-secondary"
                onclick="limpiarFormularioRecepcion()">
                <i class="fas fa-undo"></i> &nbsp; Limpiar
            </button>
        </div>

    </form>
</div>


<!-- MODAL BUSCAR CLIENTE -->
<div class="modal fade" id="modalCliente" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user"></i> Buscar Cliente
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    &times;
                </button>
            </div>

            <div class="modal-body">

                <input type="text" id="buscar_cliente"
                    class="form-control mb-3"
                    placeholder="Buscar por nombre, RUC o CI"
                    onkeyup="buscarClienteAjax()">

                <div id="tabla_clientes">
                    <!-- AJAX -->
                </div>

            </div>

        </div>
    </div>
</div>


<!-- MODAL BUSCAR VEHÍCULO -->
<div class="modal fade" id="modalVehiculo" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-car"></i> Buscar Vehículo
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    &times;
                </button>
            </div>

            <div class="modal-body">

                <input type="text"
                    id="buscar_vehiculo"
                    class="form-control mb-3"
                    placeholder="Buscar por placa o modelo"
                    onkeyup="buscarVehiculoAjax()">
                <div id="tabla_vehiculos">
                    <!-- AJAX -->
                </div>

            </div>

        </div>
    </div>
</div>

<?php include_once "./vistas/inc/recepcionservicio.php"; ?>