<?php
if (!mainModel::tienePermiso('servicio.diagnostico.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<style>
    .diagnostico-recepcion-wrap {
        position: relative;
    }

    .diagnostico-autocomplete {
        background: #fff;
        border: 1px solid #dfe3e8;
        border-radius: 6px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, .14);
        display: none;
        left: 15px;
        max-height: 320px;
        overflow-y: auto;
        position: absolute;
        right: 15px;
        top: calc(100% + 4px);
        z-index: 1050;
    }

    .diagnostico-autocomplete-item {
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

    .diagnostico-autocomplete-item:hover,
    .diagnostico-autocomplete-item:focus {
        background: #f4f8fb;
        outline: none;
    }

    .diagnostico-autocomplete-main {
        flex: 1;
        min-width: 0;
    }

    .diagnostico-autocomplete-title {
        display: block;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .diagnostico-autocomplete-meta {
        color: #6c757d;
        display: block;
        font-size: 12px;
        margin-top: 2px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .diagnostico-autocomplete-badge {
        background: #e8f4f8;
        border-radius: 999px;
        color: #0b7189;
        flex: 0 0 auto;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 9px;
    }

    .diagnostico-autocomplete-empty {
        color: #6c757d;
        padding: 12px;
        text-align: center;
    }

    .diagnostico-recepcion-resumen {
        background: #f8fafc;
        border: 1px solid #e3e8ef;
        border-radius: 6px;
        display: none;
        margin-top: 14px;
        padding: 14px;
    }

    .diagnostico-recepcion-resumen dt {
        color: #6c757d;
        font-size: 12px;
        margin-bottom: 2px;
        text-transform: uppercase;
    }

    .diagnostico-recepcion-resumen dd {
        color: #263238;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .diagnostico-recepcion-observacion {
        background: #fff;
        border-left: 3px solid #17a2b8;
        color: #263238;
        margin: 0;
        min-height: 44px;
        padding: 10px 12px;
    }
</style>

<div class="container-fluid">
    <h3>
        <i class="fas fa-tools"></i> &nbsp; DIAGNOSTICO DE SERVICIO
    </h3>

    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>/diagnostico-servicio-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO DIAGNOSTICO
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>/diagnostico-servicio-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR DIAGNOSTICOS
            </a>
        </li>
    </ul>
</div>

<div id="alerta_reclamo" class="alert alert-warning" style="display:none;">
    <i class="fas fa-exclamation-triangle"></i>
    Recepcion generada desde reclamo
</div>

<div id="card_reclamo" style="display:none;">
    <div class="card border-warning mb-3">
        <div class="card-header bg-warning text-dark">
            <i class="fas fa-exclamation-circle"></i> Detalle del Reclamo
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Descripcion:</strong><br>
                    <span id="rec_desc"></span>
                </div>

                <div class="col-md-2">
                    <strong>Tipo:</strong><br>
                    <span id="rec_tipo"></span>
                </div>

                <div class="col-md-2">
                    <strong>Prioridad:</strong><br>
                    <span id="rec_prioridad"></span>
                </div>

                <div class="col-md-2">
                    <strong>Fecha:</strong><br>
                    <span id="rec_fecha"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/diagnosticoAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="guardar_diagnostico">
        <input type="hidden" name="id_sucursal" id="id_sucursal">

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Recepcion</legend>

            <div class="row">
                <div class="col-md-12 diagnostico-recepcion-wrap">
                    <input type="hidden" name="idrecepcion" id="idrecepcion">
                    <div class="input-group">
                        <input type="text"
                            class="form-control"
                            id="buscar_recepcion"
                            placeholder="Escriba al menos 3 caracteres: cliente, documento o placa"
                            onkeyup="buscarRecepcionAjax()">
                        <div class="input-group-append">
                            <button type="button"
                                class="btn btn-secondary"
                                title="Limpiar recepcion"
                                onclick="limpiarRecepcionDiagnostico()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" id="recepcion_info">
                    <div id="resultado_recepciones" class="diagnostico-autocomplete"></div>
                </div>
            </div>

            <div id="detalle_recepcion_seleccionada" class="diagnostico-recepcion-resumen">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">
                        <i class="fas fa-clipboard-list"></i> Detalle de la recepcion
                    </h6>
                    <span id="det_recepcion_origen" class="badge badge-secondary">NORMAL</span>
                </div>
                <dl class="row mb-0">
                    <div class="col-md-4">
                        <dt>Cliente</dt>
                        <dd id="det_recepcion_cliente">-</dd>
                    </div>
                    <div class="col-md-4">
                        <dt>Vehiculo</dt>
                        <dd id="det_recepcion_vehiculo">-</dd>
                    </div>
                    <div class="col-md-4">
                        <dt>Ingreso</dt>
                        <dd id="det_recepcion_fecha">-</dd>
                    </div>
                    <div class="col-md-3">
                        <dt>Kilometraje</dt>
                        <dd id="det_recepcion_km">-</dd>
                    </div>
                    <div class="col-md-3">
                        <dt>Combustible</dt>
                        <dd id="det_recepcion_combustible">-</dd>
                    </div>
                    <div class="col-md-3">
                        <dt>Servicio solicitado</dt>
                        <dd id="det_recepcion_servicio">-</dd>
                    </div>
                    <div class="col-md-3">
                        <dt>Prioridad</dt>
                        <dd id="det_recepcion_prioridad">-</dd>
                    </div>
                    <div class="col-md-4">
                        <dt>Area del problema</dt>
                        <dd id="det_recepcion_area">-</dd>
                    </div>
                    <div class="col-md-4">
                        <dt>Estado exterior</dt>
                        <dd id="det_recepcion_exterior">-</dd>
                    </div>
                    <div class="col-md-4">
                        <dt>Accesorios</dt>
                        <dd id="det_recepcion_accesorios">-</dd>
                    </div>
                    <div class="col-md-12">
                        <dt>Lo solicitado / observacion del cliente</dt>
                        <dd class="diagnostico-recepcion-observacion" id="det_recepcion_observacion">-</dd>
                    </div>
                </dl>
            </div>
        </fieldset>

        <fieldset id="bloque_reclamo_resultado" class="border p-3 mb-3" style="display:none;">
            <legend>Resultado del Reclamo</legend>

            <div class="row">
                <div class="col-md-4">
                    <label>Es reclamo valido?</label>
                    <select name="es_reclamo_valido" class="form-control">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Es garantia?</label>
                    <select name="es_garantia" class="form-control">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Requiere cobro?</label>
                    <select name="requiere_cobro" class="form-control">
                        <option value="0">No</option>
                        <option value="1">Si</option>
                    </select>
                </div>
            </div>
        </fieldset>

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos del Diagnostico</legend>

            <div class="row">
                <div class="col-md-4">
                    <label>Fecha</label>
                    <input type="datetime-local" name="fecha"
                        class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Equipo de trabajo</label>
                    <select name="id_equipo" id="id_equipo" class="form-control" required>
                        <option value="">Seleccione equipo</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="0">Pendiente</option>
                        <option value="1" selected>En proceso</option>
                        <option value="2">Finalizado</option>
                    </select>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <label>Observacion general</label>
                    <textarea name="observacion"
                        class="form-control"
                        rows="3"></textarea>
                </div>
            </div>
        </fieldset>

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Detalle del Diagnostico</legend>

            <table class="table table-dark table-sm">
                <thead>
                    <tr>
                        <th>Sistema</th>
                        <th>Problema</th>
                        <th>Gravedad</th>
                        <th>Solucion</th>
                        <th>Repuesto</th>
                        <th>Mano de obra</th>
                        <th width="50">Accion</th>
                    </tr>
                </thead>

                <tbody id="detalleDiagnostico"></tbody>
            </table>

            <button type="button" class="btn btn-success"
                onclick="agregarDetalleDiagnostico()">
                <i class="fas fa-plus"></i> Agregar
            </button>
        </fieldset>

        <div class="text-center">
            <button type="submit" class="btn btn-info btn-raised">
                <i class="fas fa-save"></i> Guardar
            </button>

            <button type="button"
                class="btn btn-secondary btn-raised"
                onclick="limpiarDiagnostico()">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </div>
    </form>
</div>

<?php include "./vistas/inc/diagnosticoJS.php"; ?>
