<?php

// Iniciar sesión solo si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Valor por defecto
if (!isset($_SESSION['factura_tipo'])) {
    $_SESSION['factura_tipo'] = "con_oc";
}

$tipo = $_SESSION['factura_tipo'];

// Si se envió un nuevo valor por POST, sobrescribe
if (isset($_POST['factura_tipo'])) {
    $_SESSION['factura_tipo'] = $_POST['factura_tipo'];
    $tipo = $_SESSION['factura_tipo'];
}


?>


<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/facturaAjax.php"
        method="POST"
        autocomplete="off"
        enctype="multipart/form-data">

        <input type="hidden" name="modulo" value="factura">

        <div class="container-fluid form-neon" style="margin-top: 30px;">

            <!-- 🔹 BOTONES A LA DERECHA -->
            <div style="display: flex; justify-content: flex-end; margin-bottom: 15px; gap: 10px;">
                <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#ModalBuscarOC">
                    <i class="fas fa-search"></i> &nbsp; Cargar con Orden de Compra
                </button>
                <a href="<?php echo SERVERURL; ?>factura/nueva" class="btn btn-secondary">
                    <i class="fas fa-file"></i> Sin Orden de Compra
                </a>
            </div>

            <?php if ($tipo === 'sin_oc') { ?>
                <div class="text-center mb-3">
                    <?php if (empty($_SESSION['Sdatos_proveedorOC'])) { ?>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalproveedorOC">
                            <i class="fas fa-user-plus"></i> &nbsp; Agregar Proveedor
                        </button>
                    <?php } ?>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalArticuloOC">
                        <i class="fas fa-box-open"></i> &nbsp; Agregar artículo
                    </button>
                </div>
            <?php } ?>

            <legend><i class="fas fa-file-invoice-dollar"></i> &nbsp; Registrar nueva factura</legend>
            <p class="text-muted mb-4">Complete los campos para cargar una nueva factura.</p>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Timbrado</label>
                        <input class="form-control" name="fecha_emision" required>
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
                        <input type="date" class="form-control" name="fecha_emision" required>
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Proveedor</label>
                        <?php if (empty($_SESSION['datos_proveedorOC'])) { ?>
                            <div class="d-flex align-items-center text-danger mt-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span>Seleccione un proveedor</span>
                            </div>
                        <?php } else { ?>
                            <div class="d-flex align-items-center mt-3">
                                <form class="FormularioAjax d-inline-block" action="<?php echo SERVERURL ?>ajax/presupuestoAjax.php" method="POST" data-form="loans">
                                    <input type="hidden" name="id_eliminar_proveedorOC" value="<?php echo $_SESSION['datos_proveedorOC']['ID']; ?>">
                                    <?php echo $_SESSION['datos_proveedorOC']['RAZON'] . " (" . $_SESSION['datos_proveedorOC']['RUC'] . ")"; ?>
                                    <?php if ($tipo === 'sin_oc') { ?>
                                        <button type="submit" class="btn btn-danger"><i class="fas fa-user-times"></i></button>
                                    <?php } ?>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="proveedor" class="bmd-label-floating">Condicion de Venta</label>
                        <select class="form-control" name="proveedor" id="proveedor" required>
                            <option value="" selected disabled>Seleccione un proveedor</option>
                            <option value="" selected >Contado</option>
                            <option value="" selected >Crédito</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TABLA DETALLE DE PRODUCTOS -->
            <div class="row" style="margin-top:20px;">
                <div class="col-12">
                    <h5>Detalle de productos</h5>
                    <table class="table table-dark table-sm" id="tabla-detalle">
                        <thead>
                            <tr>
                                <th style="width:35%;">Producto</th>
                                <th class="text-center" style="width:10%;">Cantidad</th>
                                <th class="text-center" style="width:15%;">Precio unit.</th>
                                <th class="text-center" style="width:12%;">Subtotal</th>
                                <th class="text-center" style="width:10%;">IVA</th>
                                <th class="text-center" style="width:12%;">IVA monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Ejemplo de fila: duplicar o generar dinámicamente según convenga -->
                            <?php if (!empty($_SESSION['Cdatos_articuloOC'])): ?>
                                <?php foreach ($_SESSION['Cdatos_articuloOC'] as $item): ?>
                                    <tr>
                                        <!-- Producto -->
                                        <td class="text-left">
                                            <?= htmlspecialchars($item['descripcion']); ?>
                                        </td>


                                        <!-- Cantidad -->
                                        <td class="text-center">
                                            <input type="number" min="0" step="1"
                                                name="cantidades[]"
                                                class="form-control text-center cantidad"
                                                value="<?= $item['cantidad']; ?>" required>
                                        </td>

                                        <!-- Precio -->
                                        <td class="text-center">
                                            <input type="number"
                                                name="precios[]"
                                                class="form-control text-center precio"
                                                value="<?= number_format($item['precio'], 0, '.', ''); ?>"
                                                step="0.01"
                                                required>
                                        </td>

                                        <!-- Subtotal -->
                                        <td class="text-center subtotal">
                                            <?= number_format($item['subtotal'], 0, '.', ''); ?>
                                        </td>

                                        <!-- IVA selector -->
                                        <td class="text-center">
                                            <?= htmlspecialchars($item['iva_descri']); ?>
                                        </td>

                                        <!-- IVA Monto -->
                                        <td class="text-center iva-monto">
                                            <?= number_format($item['iva'], 0, '.', ''); ?>
                                        </td>
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

            <!-- Hidden para enviar totales al backend -->
            <input type="hidden" name="subtotal_general" id="input-subtotal-general" value="0.00">
            <input type="hidden" name="iva_total" id="input-iva-total" value="0.00">
            <input type="hidden" name="total_factura" id="input-total-factura" value="0.00">
            <input type="hidden" name="base10" id="input-base10" value="0.00">
            <input type="hidden" name="iva10" id="input-iva10" value="0.00">
            <input type="hidden" name="base5" id="input-base5" value="0.00">
            <input type="hidden" name="iva5" id="input-iva5" value="0.00">
            <input type="hidden" name="base0" id="input-base0" value="0.00">

            <p class="text-center" style="margin-top: 40px;">
                <button type="submit" class="btn btn-info btn-raised btn-lg">
                    <i class="fas fa-save"></i> &nbsp; Guardar factura
                </button>
            </p>

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

<?php include "./vistas/inc/compra.php"; ?>