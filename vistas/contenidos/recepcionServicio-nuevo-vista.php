<?php
if (!mainModel::tienePermisoVista('servicio.recepcion.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
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
        autocomplete="off"
        enctype="multipart/form-data">

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

       <!-- ESTADO DEL VEHÍCULO -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Estado del Vehículo al Ingreso</legend>

            <div class="row">
                <div class="col-md-4">
                    <label>Kilometraje</label>
                    <input type="number" class="form-control"
                        name="kilometraje" min="0" required>
                </div>
                <div class="col-md-4">
                    <label>Nivel de combustible</label>
                    <select name="nivel_combustible" class="form-control">
                        <option value="">Seleccione</option>
                        <option value="vacio">Vacío</option>
                        <option value="1/4">1/4</option>
                        <option value="1/2">1/2</option>
                        <option value="3/4">3/4</option>
                        <option value="lleno">Lleno</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Estado exterior</label>
                    <select name="estado_exterior" class="form-control">
                        <option value="sin_danos">Sin daños visibles</option>
                        <option value="rayones">Rayones</option>
                        <option value="golpes">Golpes</option>
                        <option value="varios">Varios daños</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Objetos dentro del vehículo</label>
                    <input type="text" name="objetos_vehiculo" class="form-control"
                        placeholder="Ej: herramientas, documentos">
                </div>

            </div>
        </fieldset>

        <!-- DATOS DEL SERVICIO -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos del Servicio</legend>

            <div class="row">

                <div class="col-md-4">
                    <label>Tipo de servicio</label>
                    <select name="tipo_servicio" class="form-control">
                        <option value="diagnostico">Diagnóstico</option>
                        <option value="mantenimiento">Mantenimiento</option>
                        <option value="reparacion">Reparación</option>
                        <option value="garantia">Garantía</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Área del problema</label>
                    <select name="area_problema" class="form-control">
                        <option value="motor">Motor</option>
                        <option value="chasis">Chasis</option>
                        <option value="transmision">Transmisión</option>
                        <option value="frenos">Frenos</option>
                        <option value="suspension">Suspensión</option>
                        <option value="direccion">Dirección</option>
                        <option value="electricidad">Sistema Eléctrico</option>
                        <option value="aire_acondicionado">Aire acondicionado</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Prioridad</label>
                    <select name="prioridad" class="form-control">
                        <option value="normal">Normal</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>

            </div>

        </fieldset>

        <!-- ACCESORIOS -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Accesorios Entregados</legend>

            <div class="row">

                <div class="col-md-3">
                    <input type="checkbox" name="accesorios[]" value="llave">
                    Llave
                </div>

                <div class="col-md-3">
                    <input type="checkbox" name="accesorios[]" value="llave_repuesto">
                    Llave de repuesto
                </div>

                <div class="col-md-3">
                    <input type="checkbox" name="accesorios[]" value="herramientas">
                    Herramientas
                </div>

                <div class="col-md-3">
                    <input type="checkbox" name="accesorios[]" value="rueda_auxilio">
                    Rueda de auxilio
                </div>

                <div class="col-md-3">
                    <input type="checkbox" name="accesorios[]" value="baliza">
                    Baliza
                </div>

            </div>
        </fieldset>

         <fieldset class="border p-3 mb-4">
            <legend class="w-auto px-2">Adjuntar imagen del Vehiculo</legend>
            <input type="file" name="fotos_vehiculo[]" multiple class="form-control">
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
            <button type="submit" class="btn btn-info btn-raised">
                <i class="fas fa-save"></i> &nbsp; Guardar
            </button>

            <button type="button"
                class="btn btn-secondary btn-raised"
                onclick="limpiarFormularioRecepcion()">
                <i class="fas fa-times"></i> &nbsp; Cancelar
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