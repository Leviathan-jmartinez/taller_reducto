<?php
if (!mainModel::tienePermisoVista('compra.factura.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Valor por defecto
if (!isset($_SESSION['factura_tipo'])) {
    $_SESSION['factura_tipo'] = "con_oc";
}

// Cambiar sesión si viene por POST desde el botón
if (isset($_POST['factura_tipo']) && $_POST['factura_tipo'] === "sin_oc") {
    $_SESSION['factura_tipo'] = "sin_oc";
    exit; // detiene la vista y responde al AJAX
}

$tipo = $_SESSION['factura_tipo'];
?>

<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-search fa-fw"></i> &nbsp; INGRESO DE FACTURA
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>factura-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; INGRESO DE FACTURA</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>factura-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR</a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/compraAjax.php"
        method="POST"
        autocomplete="off"
        enctype="multipart/form-data">

        <input type="hidden" name="accion" value="guardar_compra">

        <div class="container-fluid form-neon" style="margin-top: 30px;">

            <!-- 🔹 BOTONES A LA DERECHA -->

            <div style="display: flex; justify-content: flex-end; margin-bottom: 15px; gap: 10px;">
                <?php if ($tipo === 'con_oc') { ?>
                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#ModalBuscarOC">
                        <i class="fas fa-search"></i> &nbsp; Cargar con Orden de Compra
                    </button>
                <?php } ?>
                <a href="#" id="btnSinOC" class="btn btn-secondary">
                    <i class="fas fa-file"></i> Sin Orden de Compra
                </a>

            </div>

            <?php if ($tipo === 'sin_oc') { ?>
                <div class="text-center mb-3">
                    <?php if (empty($_SESSION['datos_proveedorCO'])) { ?>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalproveedorCO">
                            <i class="fas fa-user-plus"></i> &nbsp; Agregar Proveedor
                        </button>
                    <?php } ?>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalArticuloCO">
                        <i class="fas fa-box-open"></i> &nbsp; Agregar artículo
                    </button>
                </div>
            <?php } ?>

            <legend><i class="fas fa-file-invoice-dollar"></i> &nbsp; Registrar nueva factura</legend>
            <p class="text-muted mb-4">Complete los campos para cargar una nueva factura.</p>

            <!-- FECHAS Y DATOS DE PROVEEDOR -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Timbrado</label>
                        <input class="form-control" name="timbrado" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="factura_numero" class="bmd-label-floating">Número de factura</label>
                        <input type="text" class="form-control" name="factura_numero" id="factura_numero" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Vencimiento Timbrado</label>
                        <input type="date" class="form-control" name="vencimiento_timbrado" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Fecha de emisión</label>
                        <input type="date" class="form-control" name="fecha_emision" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="bmd-label-floating">Proveedor</label>
                        <?php if (empty($_SESSION['datos_proveedorCO'])) { ?>
                            <div class="d-flex align-items-center text-danger mt-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span>Seleccione un proveedor</span>
                            </div>
                        <?php } else { ?>
                            <div class="d-flex align-items-center mt-3">
                                <form class="FormularioAjax d-inline-block" action="<?php echo SERVERURL ?>ajax/presupuestoAjax.php" method="POST" data-form="loans">
                                    <input type="hidden" name="id_eliminar_proveedorOC" value="<?php echo $_SESSION['datos_proveedorCO']['ID']; ?>">
                                    <?php echo $_SESSION['datos_proveedorCO']['RAZON'] . " (" . $_SESSION['datos_proveedorCO']['RUC'] . ")"; ?>
                                    <?php if ($tipo === 'sin_oc') { ?>
                                        <button type="submit" class="btn btn-danger"><i class="fas fa-user-times"></i></button>
                                    <?php } ?>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="condicion" class="bmd-label-floating">Condición de Venta</label>
                        <select class="form-control" name="condicion" id="condicion" required>
                            <option value="" selected disabled>Seleccione condición</option>
                            <option value="contado" selected>Contado</option>
                            <option value="credito" selected>Crédito</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="intervalo" class="bmd-label-floating">Intervalo</label>
                        <input type="text" class="form-control" name="intervalo" id="intervalo" required>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="cuotas" class="bmd-label-floating">Cuotas</label>
                        <input type="number" class="form-control" name="cuotas" id="cuotas" required>
                    </div>
                </div>
            </div>

            <!-- TABLA DETALLE DE ARTICULOS -->
            <div class="row" style="margin-top:20px;">
                <div class="col-12">
                    <h5>Detalle de articulos</h5>
                    <table class="table table-dark table-sm" id="tabla-detalle">
                        <thead>
                            <tr>
                                <th style="width:35%;">Articulo</th>
                                <th class="text-center" style="width:10%;">Cantidad</th>
                                <th class="text-center" style="width:15%;">Precio unit.</th>
                                <th class="text-center" style="width:12%;">Subtotal</th>
                                <th class="text-center" style="width:10%;">IVA</th>
                                <th class="text-center" style="width:12%;">IVA monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($_SESSION['Cdatos_articuloCO'])): ?>
                                <?php foreach ($_SESSION['Cdatos_articuloCO'] as $i => $item): ?>
                                    <tr
                                        data-index="<?= $i; ?>"
                                        data-rate="<?= $item['ratevalueiva']; ?>"
                                        data-divisor="<?= $item['divisor']; ?>">
                                        <td class="text-left"><?= htmlspecialchars($item['descripcion']); ?></td>
                                        <td class="text-center">
                                            <input type="number" min="0" step="1"
                                                name="cantidades[]"
                                                class="form-control text-center cantidad"
                                                value="<?= $item['cantidad']; ?>"
                                                <?= $tipo === 'sin_oc' ? 'readonly' : 'required'; ?>>
                                        </td>
                                        <td class="text-center">
                                            <input type="number"
                                                name="precios[]"
                                                class="form-control text-center precio"
                                                value="<?= number_format($item['precio'], 2, '.', ''); ?>"
                                                step="0.01"
                                                min="0"
                                                <?= $tipo === 'sin_oc' ? 'readonly' : 'required'; ?>>
                                        </td>
                                        <td class="text-center subtotal"><?= number_format($item['subtotal'], 0, '.', '.'); ?></td>
                                        <td class="text-center"><?= htmlspecialchars($item['iva_descri']); ?></td>
                                        <td class="text-center iva-monto"><?= number_format($item['iva'], 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RESUMEN DE IVA -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex align-items-start justify-content-between p-3 border rounded">
                        <div class="text-center mx-2">
                            <small class="text-muted">IVA 5%</small><br>
                            <span id="iva5">0.00</span>
                        </div>
                        <div class="text-center mx-2">
                            <small class="text-muted">IVA 10%</small><br>
                            <span id="iva10">0.00</span>
                        </div>
                        <div class="text-center mx-2 font-weight-bold">
                            <small class="text-muted">Total IVA</small><br>
                            <span id="total-iva">0.00</span>
                        </div>
                        <div class="text-center mx-2 font-weight-bold">
                            <small class="text-muted">Subtotal</small><br>
                            <span id="subtotal-general">0.00</span>
                        </div>
                        <div class="text-center mx-2 font-weight-bold">
                            <small class="text-muted">Total Factura</small><br>
                            <span id="total-factura">0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden inputs -->
            <input type="hidden" name="subtotal_general" id="input-subtotal-general" value="0.00">
            <input type="hidden" name="iva_total" id="input-iva-total" value="0.00">
            <input type="hidden" name="total_factura" id="input-total-factura" value="0.00">
            <input type="hidden" name="base10" id="input-base10" value="0.00">
            <input type="hidden" name="iva10" id="input-iva10" value="0.00">
            <input type="hidden" name="base5" id="input-base5" value="0.00">
            <input type="hidden" name="iva5" id="input-iva5" value="0.00">
            <input type="hidden" name="base0" id="input-base0" value="0.00">

            <div class="text-center" style="margin-top: 40px; display: flex; justify-content: center; gap: 15px;">
                <button type="submit" class="btn btn-info btn-raised">
                    <i class="fas fa-save"></i> &nbsp; Guardar
                </button>
                <button type="button" id="btnCancelarCompra" class="btn btn-secondary btn-raised">
                    <i class="fas fa-times"></i> &nbsp; Cancelar
                </button>
            </div>

        </div>
    </form>
</div>

<!-- MODAL BUSCAR OC -->
<div class="modal fade" id="ModalBuscarOC" tabindex="-1" role="dialog" aria-labelledby="ModalBuscarOC" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalBuscarOC">Agregar OC</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <label for="input_item" class="bmd-label-floating">Código, Proveedor, Número OC</label>
                        <input type="text" pattern="[a-zA-z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" name="input_oc" id="input_oc" maxlength="30">
                    </div>
                </div>
                <br>
                <div class="container-fluid" id="tabla_OC">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="buscar_OC()"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL proveedor -->
<div class="modal fade" id="ModalproveedorCO" tabindex="-1" role="dialog" aria-labelledby="ModalproveedorCO" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalproveedorCO">Agregar Proovedor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <label for="input_proveedor" class="bmd-label-floating">RUC, RAZON SOCIAL</label>
                        <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" name="input_proveedor" id="input_proveedor" maxlength="30">
                    </div>
                </div>
                <br>
                <div class="container-fluid" id="tabla_proveedorCO">

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="buscar_proveedorCO()"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<!-- MODAL ITEM -->
<div class="modal fade" id="ModalArticuloCO" tabindex="-1" role="dialog" aria-labelledby="ModalArticuloCO" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalArticuloCO">Agregar Articulo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <label for="input_item" class="bmd-label-floating">Código, descripción</label>
                        <input type="text" pattern="[a-zA-z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" name="input_articulo" id="input_articulo" maxlength="30">

                    </div>
                </div>
                <br>
                <div class="container-fluid" id="tabla_articuloCO">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="buscar_articuloCO()"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php include "./vistas/inc/compra.php";
?>