<?php
if (!mainModel::tienePermisoVista('compra.remision.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Usuario de sesión
$id_usuario = $_SESSION['id_usuario'] ?? null;
?>

<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-search fa-fw"></i> &nbsp; REMISIÓN
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>remision-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVA REMISIÓN
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>remision-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR
            </a>
        </li>
    </ul>
</div>


<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/remisionAjax.php"
        method="POST"
        autocomplete="off">

        <input type="hidden" name="accion" value="guardar_remision">
        <input type="hidden" name="idcompra_cabecera" value="<?= $_SESSION['datos_dactura']['ID'] ?? '' ?>">
        <input type="hidden" name="id_usuario" value="<?= $id_usuario; ?>">
        <input type="hidden" name="estado" value="1">
        <div style="display: flex; justify-content: flex-end; margin-bottom: 15px; gap: 10px;">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalBuscarFactura">
                <i class="fas fa-search"></i> &nbsp; Cargar factura
            </button>
        </div>
        <div class="container-fluid form-neon" style="margin-top: 30px;">

            <!-- SECCIÓN 1: DATOS DE LA REMISIÓN -->
            <h6 class="mb-3"><i class="fas fa-file-alt"></i> Datos de la remisión</h6>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>N° Remisión</label>
                        <input type="text" class="form-control" name="nro_remision" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Motivo de remisión</label>
                        <input type="text" class="form-control" name="motivo_remision" required>
                    </div>
                </div>
                <!-- SECCIÓN 5: FECHA DE EMISIÓN -->

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Fecha y hora de emisión</label>
                        <input type="date" class="form-control" name="fecha_emision" required>
                    </div>
                </div>

            </div>

            <!-- SECCIÓN 2: DATOS DEL TRASLADO -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Fecha de envío</label>
                        <input type="date" class="form-control" name="fechaenvio" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Fecha de llegada</label>
                        <input type="date" class="form-control" name="fechallegada" required>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 3: DATOS DEL TRANSPORTISTA -->
            <h6 class="mt-4 mb-3"><i class="fas fa-id-card"></i> Datos del transportista</h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>RUC transportista</label>
                        <input type="text" class="form-control" name="ruc_transport">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Razon Social transportista</label>
                        <input type="text" class="form-control" name="nombre_transpo" required>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 4: DATOS DEL VEHÍCULO -->
            <h6 class="mt-4 mb-3"><i class="fas fa-car"></i> Datos del vehículo</h6>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Marca</label>
                        <input type="text" class="form-control" name="vehimarca">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Modelo</label>
                        <input type="text" class="form-control" name="vehimodelo">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Chapa</label>
                        <input type="text" class="form-control" name="vehichapa">
                    </div>
                </div>
            </div>
            <h6 class="mt-4 mb-3"><i class="fas fa-id-card"></i> Datos del transportista</h6>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nombre transportista</label>
                        <input type="text" class="form-control" name="nombre_transpo" required>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>CI / RUC</label>
                        <input type="text" class="form-control" name="ci_transpo">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Celular</label>
                        <input type="text" class="form-control" name="cel_transpo">
                    </div>
                </div>
            </div>


            <!-- SECCIÓN 6: DETALLE DE PRODUCTOS -->
            <h6 class="mt-4 mb-3"><i class="fas fa-box-open"></i> Detalle de productos</h6>
            <div class="row">
                <div class="col-12">
                    <table class="table table-dark table-sm" id="tabla-detalle-remision">
                        <thead>
                            <tr>
                                <th style="width:40%;">Producto</th>
                                <th class="text-center" style="width:15%;">Cantidad</th>
                                <th class="text-center" style="width:20%;">Costo unit.</th>
                                <th class="text-center" style="width:25%;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($_SESSION['datos_articulofactura'])): ?>
                                <?php foreach ($_SESSION['datos_articulofactura'] as $i => $item): ?>
                                    <tr data-index="<?= $i; ?>">
                                        <td class="text-left"><?= htmlspecialchars($item['descripcion']); ?></td>
                                        <td class="text-center">
                                            <input type="number" min="0" step="1"
                                                name="cantidades[]"
                                                class="form-control text-center cantidad"
                                                value="<?= $item['cantidad']; ?>"
                                                required>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" step="0.01"
                                                name="costos[]"
                                                class="form-control text-center costo"
                                                value="<?= number_format($item['precio'], 2, '.', ''); ?>"
                                                required>
                                        </td>
                                        <td class="text-center subtotal"><?= number_format($item['subtotal'], 2, '.', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Hidden totals -->
            <input type="hidden" name="total_remision" id="input-total-remision" value="0.00">

            <!-- BOTONES -->
            <div class="text-center" style="margin-top: 40px; display: flex; justify-content: center; gap: 15px;">
                <button type="submit" class="btn btn-raised btn-info btn-sm">
                    <i class="fas fa-save"></i> &nbsp; Guardar
                </button>
                <button type="button" id="btnCancelarRemision" class="btn btn-raised btn-secondary btn-sm">
                    <i class="fas fa-times"></i> &nbsp; Cancelar
                </button>
            </div>

        </div>
    </form>
</div>



<!-- MODAL BUSCAR OC -->
<div class="modal fade" id="ModalBuscarFactura" tabindex="-1" role="dialog" aria-labelledby="ModalBuscarFactura" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalBuscarFactura">Agregar OC</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <label for="input_factura" class="bmd-label-floating">Código, Proveedor, Número OC</label>
                        <input type="text" class="form-control" name="input_factura" id="input_factura" maxlength="30">
                    </div>
                </div>
                <br>
                <div class="container-fluid" id="tabla_factura">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="buscar_factura()"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?php include "./vistas/inc/remisiones.php";
?>