<?php
if (!mainModel::tienePermiso('servicio.recepcion.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "./controladores/recepcionservicioControlador.php";
$ins_recepcion_rapida = new recepcionservicioControlador();
$ciudades_recepcion = $ins_recepcion_rapida->listar_ciudades_controlador();
$ciudades_recepcion = is_array($ciudades_recepcion) ? $ciudades_recepcion : [];
$modelos_recepcion = $ins_recepcion_rapida->listar_modelos_controlador();
$modelos_recepcion = is_array($modelos_recepcion) ? $modelos_recepcion : [];
?>

<style>
    .recepcion-autocomplete-wrap {
        position: relative;
    }

    .recepcion-autocomplete {
        background: #fff;
        border: 1px solid #dfe3e8;
        border-radius: 6px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, .14);
        display: none;
        left: 15px;
        max-height: 310px;
        overflow-y: auto;
        position: absolute;
        right: 15px;
        top: calc(100% + 4px);
        z-index: 1050;
    }

    .recepcion-autocomplete-item {
        align-items: center;
        background: #fff;
        border: 0;
        border-bottom: 1px solid #edf0f2;
        color: #263238;
        cursor: pointer;
        display: flex;
        gap: 12px;
        padding: 10px 12px;
        text-align: left;
        width: 100%;
    }

    .recepcion-autocomplete-item:hover,
    .recepcion-autocomplete-item:focus {
        background: #f4f8fb;
        outline: none;
    }

    .recepcion-autocomplete-main {
        flex: 1;
        min-width: 0;
    }

    .recepcion-autocomplete-title {
        display: block;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .recepcion-autocomplete-meta {
        color: #6c757d;
        display: block;
        font-size: 12px;
        margin-top: 2px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .recepcion-autocomplete-badge {
        background: #e8f4f8;
        border-radius: 999px;
        color: #0b7189;
        flex: 0 0 auto;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 9px;
    }

    .recepcion-autocomplete-empty {
        color: #6c757d;
        padding: 12px;
        text-align: center;
    }

    .recepcion-reclamo-detalle {
        display: none;
    }
</style>

<div class="container-fluid form-neon">

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
    <div id="alerta_reclamo" class="alert alert-warning" style="display:none;">
        <i class="fas fa-exclamation-triangle"></i>
        Recepción generada desde un reclamo
    </div>
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/recepcionservicioAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off"
        enctype="multipart/form-data">

        <!-- ACCIÓN -->
        <input type="hidden" name="accion" value="guardar_recepcion">
        <div class="text-right mb-3">
            <button type="button"
                id="btnServicioReclamo"
                class="btn btn-warning"
                onclick="activarServicioDesdeReclamo()">
                <i class="fas fa-exclamation-circle"></i> Servicio proveniente de reclamo
            </button>
        </div>

        <fieldset id="bloque_reclamo_recepcion" class="border p-3 mb-3" style="display:none;">
            <legend class="w-auto px-2">Reclamo de origen</legend>

            <div class="row">
                <div class="col-md-8">
                    <label>Buscar reclamo</label>
                    <input type="text"
                        id="buscar_reclamo_recepcion"
                        class="form-control"
                        placeholder="Buscar por cliente, apellido, placa o nro. de reclamo">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary btn-block" onclick="limpiarReclamoRecepcion()">
                        <i class="fas fa-times"></i> Quitar reclamo
                    </button>
                </div>
            </div>

            <div id="tabla_reclamos" class="mt-3"></div>

            <div id="detalle_reclamo_recepcion" class="recepcion-reclamo-detalle alert alert-light border mt-3 mb-0">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong><i class="fas fa-clipboard-list"></i> Detalle del reclamo</strong>
                    <span id="rec_badge_prioridad" class="badge badge-secondary">-</span>
                </div>
                <div class="row">
                    <div class="col-md-3"><strong>Nro. reclamo:</strong><br><span id="rec_id">-</span></div>
                    <div class="col-md-3"><strong>Fecha:</strong><br><span id="rec_fecha">-</span></div>
                    <div class="col-md-3"><strong>Tipo:</strong><br><span id="rec_tipo">-</span></div>
                    <div class="col-md-3"><strong>Garantia:</strong><br><span id="rec_garantia">-</span></div>
                    <div class="col-md-6 mt-2"><strong>Cliente:</strong><br><span id="rec_cliente">-</span></div>
                    <div class="col-md-6 mt-2"><strong>Vehiculo:</strong><br><span id="rec_vehiculo">-</span></div>
                    <div class="col-md-12 mt-2"><strong>Descripcion:</strong><br><span id="rec_descripcion">-</span></div>
                </div>
            </div>
        </fieldset>
        <!-- CLIENTE -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Cliente</legend>

            <div class="row">
                <div class="col-md-10 recepcion-autocomplete-wrap">
                    <input type="hidden" name="origen" id="origen" value="NORMAL">
                    <input type="hidden" name="idreclamo_servicio" id="idreclamo_servicio">
                    <input type="hidden" name="id_cliente" id="id_cliente">
                    <input type="text" class="form-control" id="cliente_nombre"
                        placeholder="Escriba al menos 4 caracteres: nombre, apellido o documento">
                    <div id="resultado_clientes_autocomplete"
                        class="recepcion-autocomplete"></div>
                </div>

                <?php if (mainModel::tienePermiso('cliente.crear')) { ?>
                    <div class="col-md-2">
                        <button type="button"
                            id="btnNuevoCliente"
                            class="btn btn-success btn-block"
                            onclick="abrirNuevoClienteRecepcion()">
                            <i class="fas fa-user-plus"></i>
                        </button>
                    </div>
                <?php } ?>
            </div>

        </fieldset>

        <!-- VEHÍCULO -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Vehículo</legend>

            <div class="row">
                <div class="col-md-10 recepcion-autocomplete-wrap">
                    <input type="hidden" name="id_vehiculo" id="id_vehiculo">
                    <input type="text" class="form-control" id="vehiculo_desc"
                        placeholder="Seleccione un cliente y escriba al menos 4 caracteres: placa o modelo">
                    <div id="resultado_vehiculos_autocomplete"
                        class="recepcion-autocomplete"></div>
                </div>

                <?php if (mainModel::tienePermiso('vehiculo.crear')) { ?>
                    <div class="col-md-2">
                        <button type="button"
                            id="btnNuevoVehiculo"
                            class="btn btn-success btn-block"
                            onclick="abrirNuevoVehiculoRecepcion()">
                            <i class="fas fa-car-side"></i>
                        </button>
                    </div>
                <?php } ?>
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


<!-- MODAL NUEVO CLIENTE RAPIDO -->
<div class="modal fade" id="modalNuevoClienteRecepcion" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus"></i> Nuevo Cliente
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    &times;
                </button>
            </div>

            <form id="formNuevoClienteRecepcion" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="guardar_cliente_rapido">

                    <div class="row">
                        <div class="col-md-4">
                            <label>Tipo de documento</label>
                            <select class="form-control" name="tipo_documento_reg">
                                <option value="CI">CI</option>
                                <option value="RUC">RUC</option>
                                <option value="PASAPORTE">Pasaporte</option>
                                <option value="CC">CC</option>
                                <option value="CD">CD</option>
                                <option value="OF">OF</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Documento</label>
                            <input type="text" class="form-control" name="cliente_doc_reg" required>
                        </div>
                        <div class="col-md-4">
                            <label>DV</label>
                            <input type="text" class="form-control" name="cliente_dv_reg">
                        </div>
                        <div class="col-md-4">
                            <label>Nombre</label>
                            <input type="text" class="form-control" name="cliente_nombre_reg" required>
                        </div>
                        <div class="col-md-4">
                            <label>Apellido</label>
                            <input type="text" class="form-control" name="cliente_apellido_reg">
                        </div>
                        <div class="col-md-4">
                            <label>Telefono</label>
                            <input type="text" class="form-control" name="cliente_telefono_reg">
                        </div>
                        <div class="col-md-4">
                            <label>Email</label>
                            <input type="email" class="form-control" name="cliente_email_reg">
                        </div>
                        <div class="col-md-4">
                            <label>Direccion</label>
                            <input type="text" class="form-control" name="cliente_direccion_reg" required>
                        </div>
                        <div class="col-md-4">
                            <label>Ciudad</label>
                            <select class="form-control" name="ciudad_reg" required>
                                <option value="">Seleccione ciudad</option>
                                <?php foreach ($ciudades_recepcion as $ciudad) { ?>
                                    <option value="<?php echo $ciudad['id_ciudad']; ?>">
                                        <?php echo htmlspecialchars($ciudad['ciu_descri'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar cliente
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- MODAL NUEVO VEHICULO RAPIDO -->
<div class="modal fade" id="modalNuevoVehiculoRecepcion" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-car-side"></i> Nuevo Vehiculo
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    &times;
                </button>
            </div>

            <form id="formNuevoVehiculoRecepcion" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="guardar_vehiculo_rapido">
                    <input type="hidden" name="cliente_reg" id="vehiculo_rapido_cliente">

                    <div class="alert alert-info" id="vehiculo_rapido_cliente_nombre"></div>

                    <div class="row">
                        <div class="col-md-4">
                            <label>Modelo</label>
                            <select class="form-control" name="modelo_reg" required>
                                <option value="">Seleccione modelo</option>
                                <?php foreach ($modelos_recepcion as $modelo) { ?>
                                    <option value="<?php echo $modelo['id_modeloauto']; ?>">
                                        <?php echo htmlspecialchars($modelo['mod_descri'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Color</label>
                            <input type="text" class="form-control" name="color_reg" required>
                        </div>
                        <div class="col-md-4">
                            <label>Placa</label>
                            <input type="text" class="form-control" name="placa_reg" required>
                        </div>
                        <div class="col-md-4">
                            <label>Año</label>
                            <input type="text" class="form-control" name="anho_reg">
                        </div>
                        <div class="col-md-4">
                            <label>Nro Serie</label>
                            <input type="text" class="form-control" name="serie_reg">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar vehiculo
                    </button>
                </div>
            </form>

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

<div class="modal fade" id="modalReclamo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Buscar reclamo</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <input type="text"
                    class="form-control mb-2"
                    placeholder="Buscar por cliente o vehículo"
                    onkeyup="buscarReclamoAjax(this.value)">

                <div id="tabla_reclamos_modal"></div>

            </div>

        </div>
    </div>
</div>

<?php include_once "./vistas/inc/recepcionservicio.php"; ?>
