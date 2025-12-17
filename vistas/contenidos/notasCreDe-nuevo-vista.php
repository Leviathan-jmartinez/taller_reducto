<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Valor por defecto
if (!isset($_SESSION['nota_tipo'])) {
    $_SESSION['nota_tipo'] = "credito";
}

// Cambiar sesión si viene por POST desde el botón
if (isset($_POST['nota_tipo']) && in_array($_POST['nota_tipo'], ['credito', 'debito'])) {
    $_SESSION['nota_tipo'] = $_POST['nota_tipo'];
    exit; // detiene la vista y responde al AJAX
}

$tipo = $_SESSION['nota_tipo'];
?>

<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; INGRESO DE NOTA DE COMPRA
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="<?= $tipo === 'credito' ? 'active' : ''; ?>" href="#" onclick="cambiarTipoNota('credito')">
                <i class="fas fa-arrow-down fa-fw"></i> &nbsp; Nota Crédito
            </a>
        </li>
        <li>
            <a class="<?= $tipo === 'debito' ? 'active' : ''; ?>" href="#" onclick="cambiarTipoNota('debito')">
                <i class="fas fa-arrow-up fa-fw"></i> &nbsp; Nota Débito
            </a>
        </li>
    </ul>
</div>

<script>
    function cambiarTipoNota(tipo) {
        let formData = new FormData();
        formData.append('nota_tipo', tipo);

        fetch('', {
            method: 'POST',
            body: formData
        }).then(res => location.reload());
    }
</script>

<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/notaCompraAjax.php"
        method="POST"
        autocomplete="off"
        enctype="multipart/form-data">

        <input type="hidden" name="accion" value="guardar_nota_compra">

        <div class="container-fluid form-neon" style="margin-top: 30px;">

            <!-- BOTONES -->
            <div style="display: flex; justify-content: flex-end; margin-bottom: 15px; gap: 10px;">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalFactura">
                    <i class="fas fa-user-plus"></i> &nbsp; Agregar Factura Proveedor
                </button>
            </div>

            <legend><i class="fas fa-file-invoice-dollar"></i> &nbsp; Registrar nueva nota de compra</legend>
            <p class="text-muted mb-4">Complete los campos para cargar una nueva nota.</p>

            <!-- DATOS GENERALES -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Serie</label>
                        <input class="form-control" name="serie" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Número de documento</label>
                        <input type="text" class="form-control" name="nro_documento" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Fecha de emisión</label>
                        <input type="date" class="form-control" name="fecha" required>
                    </div>
                </div>
            </div>

            <!-- PROVEEDOR -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Proveedor</label>
                        <?php if (empty($_SESSION['datos_proveedorNC'])): ?>
                            <div class="d-flex align-items-center text-danger mt-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Seleccione un proveedor
                            </div>
                        <?php else: ?>
                            <div class="d-flex align-items-center mt-3">
                                <?php echo $_SESSION['datos_proveedorNC']['RAZON'] . " (" . $_SESSION['datos_proveedorNC']['RUC'] . ")"; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Timbrado</label>
                        <input class="form-control" name="Timbrado" required>
                    </div>
                </div>
            </div>

            <!-- DETALLE DE PRODUCTOS -->
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
                            <?php if (!empty($_SESSION['Cdatos_articuloNC'])): ?>
                                <?php foreach ($_SESSION['Cdatos_articuloNC'] as $i => $item): ?>
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
                                                required>
                                        </td>
                                        <td class="text-center">
                                            <input type="number"
                                                name="precios[]"
                                                class="form-control text-center precio"
                                                value="<?= number_format($item['precio'], 2, '.', ''); ?>"
                                                step="0.01" min="0" required>
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
                            <small class="text-muted">Total Nota</small><br>
                            <span id="total-factura">0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden inputs -->
            <input type="hidden" name="subtotal_general" id="input-subtotal-general" value="0.00">
            <input type="hidden" name="iva_total" id="input-iva-total" value="0.00">
            <input type="hidden" name="total_nota" id="input-total-factura" value="0.00">
            <input type="hidden" name="base10" id="input-base10" value="0.00">
            <input type="hidden" name="iva10" id="input-iva10" value="0.00">
            <input type="hidden" name="base5" id="input-base5" value="0.00">
            <input type="hidden" name="iva5" id="input-iva5" value="0.00">
            <input type="hidden" name="base0" id="input-base0" value="0.00">

            <div class="text-center" style="margin-top: 40px; display: flex; justify-content: center; gap: 15px;">
                <button type="submit" class="btn btn-info btn-raised btn-lg">
                    <i class="fas fa-save"></i> &nbsp; Guardar nota
                </button>
                <button type="button" id="btnCancelarNota" class="btn btn-danger btn-raised btn-lg">
                    <i class="fas fa-times"></i> &nbsp; Cancelar nota
                </button>
            </div>

        </div>
    </form>
</div>

<!-- MODAL BUSCAR FACTURA PROVEEDOR -->
<div class="modal fade" id="ModalFactura" tabindex="-1" role="dialog" aria-labelledby="ModalFacturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="ModalFacturaLabel"><i class="fas fa-search"></i> &nbsp; Buscar Factura</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Input de búsqueda -->
                <div class="form-group">
                    <label for="buscar_factura_input">Buscar por serie o número:</label>
                    <input type="text" class="form-control" id="buscar_factura_input" placeholder="Ingrese serie o número de factura">
                </div>

                <!-- Resultados -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tabla-facturas">
                        <thead>
                            <tr>
                                <th>Serie</th>
                                <th>Número</th>
                                <th>Proveedor</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aquí se llenarán los resultados vía AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Buscar mientras se escribe
    document.getElementById('buscar_factura_input').addEventListener('keyup', function() {
        let query = this.value.trim();
        let tbody = document.querySelector('#tabla-facturas tbody');

        if (query.length < 2) {
            tbody.innerHTML = '';
            return;
        }

        let formData = new FormData();
        formData.append('buscar_factura', query);

        fetch('<?php echo SERVERURL; ?>ajax/notasCreDeAjax.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                tbody.innerHTML = '';
                if (res.success && res.data.length > 0) {
                    res.data.forEach(factura => {
                        let tr = document.createElement('tr');
                        tr.innerHTML = `
                    <td>${factura.nro_factura}</td>
                    <td>${factura.nro_timbrado}</td>
                    <td>${factura.proveedor}</td>
                    <td>${factura.fecha_factura}</td>
                    <td>${parseFloat(factura.total_compra).toLocaleString('es-PY', {minimumFractionDigits: 2})}</td>
                    <td>
                        <button type="button" class="btn btn-success btn-sm" onclick="seleccionarFactura(${factura.idcompra_cabecera})">
                            <i class="fas fa-check"></i> Seleccionar
                        </button>
                    </td>
                `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No se encontraron resultados</td></tr>';
                }
            });
    });

    // Seleccionar factura
    function seleccionarFactura(idFactura) {
        let formData = new FormData();
        formData.append('id_factura', idFactura);

        fetch('<?php echo SERVERURL; ?>ajax/notasCreDeAjax.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    // Recargamos la vista para que se vean los datos del proveedor
                    location.reload();
                } else {
                    alert('Error al seleccionar la factura');
                }
            });
    }
</script>

