<?php

if (!mainModel::tienePermiso('compra.nota.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
if (session_status() == PHP_SESSION_NONE) {
    session_start(['name' => 'STR']);
}

$facturaNC = $_SESSION['NC_FACTURA'] ?? null;
$detalleNC = $_SESSION['NC_DETALLE'] ?? [];
$alcanceNota = $facturaNC['alcance_nota'] ?? 'regularizar_diferencia';
$fmtCantidadNota = function ($valor) {
    $texto = number_format((float)$valor, 2, '.', '');
    return rtrim(rtrim($texto, '0'), '.');
};
?>

<div class="container-fluid form-neon app-view">
    <h3 class="text-left">
        <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; INGRESO DE NOTA (CREDITO/DEBITO)
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>notasCreDe-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>notasCreDe-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR</a>
        </li>
    </ul>

    <form class="form-neon FormularioAjax app-form"
        action="<?php echo SERVERURL; ?>ajax/notasCreDeAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">
        <?php if (empty($facturaNC)): ?>
            <div class="col-md-6 mt-3">
                <label>Factura asociada</label>

                <div class="input-group">
                    <input type="hidden" name="idcompra_cabecera"
                        value="<?= $facturaNC['idcompra_cabecera'] ?? '' ?>">

                    <input type="text" class="form-control"
                        value="<?= $facturaNC['nro_factura'] ?? '' ?>"
                        placeholder="Seleccione factura"
                        readonly>

                    <div class="input-group-append">
                        <button type="button" class="btn btn-info"
                            onclick="abrirModalFactura()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <input type="hidden" name="accion" value="guardar_nota_compra">

        <!-- ================= CABECERA ================= -->
        <fieldset class="border p-3 mb-4">
            <legend class="w-auto px-2">Datos de la Nota</legend>

            <div class="row">

                <div class="col-md-3">
                    <label>Tipo</label>
                    <select name="tipo" class="form-control" required>
                        <option value="">Seleccione</option>
                        <option value="credito">Nota de Crédito</option>
                        <option value="debito">Nota de Débito</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Movimiento de Stock</label>
                    <select name="movimiento_stock" class="form-control" required>
                        <option value="NINGUNO">Sin movimiento</option>
                        <option value="DEVOLUCION">Devolución física</option>
                    </select>
                    <small class="text-muted">
                        Use “Devolución física” solo si la mercadería vuelve al proveedor.
                    </small>
                </div>

                <div class="col-md-3">
                    <label>Alcance NC</label>
                    <select name="alcance_nota" class="form-control" <?= empty($facturaNC) ? 'disabled' : '' ?>>
                        <option value="regularizar_diferencia" <?= $alcanceNota === 'regularizar_diferencia' ? 'selected' : '' ?>>Regularizar diferencia</option>
                        <option value="anulacion_total" <?= $alcanceNota === 'anulacion_total' ? 'selected' : '' ?>>Anular factura completa</option>
                    </select>
                    <small class="text-muted">Use anulacion total cuando la factura queda sin efecto.</small>
                </div>

                <div class="col-md-3">
                    <label>Numero de Nota</label>
                    <input type="text" name="nro_nota" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label>Fecha</label>
                    <input type="date" name="fecha" class="form-control" required>
                </div>

                <div class="col-md-4 mt-3">
                    <label>Timbrado</label>
                    <input type="text" name="timbrado" class="form-control">
                </div>

                <div class="col-md-4 mt-3">
                    <label>Factura asociada</label>
                    <input type="text" name="factura_asociada" class="form-control" value="<?= $facturaNC['nro_factura'] ?? '' ?>" readonly>
                </div>

                <div class="col-md-4 mt-3">
                    <label>Proveedor</label>
                    <input type="text" name="proveedor" class="form-control" value="<?= $facturaNC['proveedor'] ?? '' ?>" readonly>
                </div>

                <div class="col-md-12 mt-3">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2"></textarea>
                </div>

            </div>
        </fieldset>

        <!-- ================= DETALLE ================= -->
        <fieldset class="border p-3 mb-4">
            <legend class="w-auto px-2">Detalle de la Nota</legend>

            <div class="table-responsive">
                <table class="table table-dark table-sm">
                    <thead>
                        <tr>
                            <th>Artículo</th>
                            <th>Descripción</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Precio Unit.</th>
                            <th>IVA</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="detalle_nota">
                        <?php
                        $subtotal = 0;
                        $total_iva5 = 0;
                        $total_iva10 = 0;

                        foreach ($detalleNC as $d) {
                            $subtotal += $d['cantidad'] * $d['precio'];
                            $total_iva5 += $d['iva_5'];
                            $total_iva10 += $d['iva_10'];
                            $total = $subtotal;
                        }
                        $total_iva = $total_iva5 + $total_iva10;

                        foreach ($detalleNC as $i => $d):
                            $total_item = round($d['cantidad'] * $d['precio']); ?>
                            <tr>
                                <td><?= $d['id_articulo'] ?></td>
                                <td><?= $d['descripcion'] ?></td>

                                <td>
                                    <input type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control form-control-sm text-center"
                                        value="<?= htmlspecialchars($fmtCantidadNota($d['cantidad']), ENT_QUOTES, 'UTF-8') ?>"
                                        onchange="actualizarItem(<?= $i ?>)">
                                </td>

                                <td>
                                    <input type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control form-control-sm text-center"
                                        value="<?= $d['precio'] ?>"
                                        onchange="actualizarItem(<?= $i ?>)">
                                </td>
                                <td><?= $d['iva_tipo'] ?></td>
                                <td class="text-right" id="total_item_<?= $i ?>">
                                    <?= number_format($total_item, 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </fieldset>

        <!-- ================= TOTALES ================= -->
        <fieldset class="border p-3 mb-4">
            <legend class="w-auto px-2">Totales</legend>

            <div class="row">
                <div class="col-md-3">
                    <label>Total</label>
                    <input type="text" name="subtotal" id="subtotal"
                        class="form-control"
                        value="<?= number_format($subtotal, 0, ',', '.') ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label>IVA 5%</label>
                    <input type="text" name="iva_5" id="iva_5"
                        class="form-control"
                        value="<?= number_format($total_iva5, 0, ',', '.') ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label>IVA 10%</label>
                    <input type="text" name="iva_10" id="iva_10"
                        class="form-control"
                        value="<?= number_format($total_iva10, 0, ',', '.') ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label>Total IVA</label>
                    <input type="text" name="total" id="total"
                        class="form-control"
                        value="<?= number_format($total_iva, 0, ',', '.') ?>" readonly>
                </div>
            </div>
        </fieldset>

        <div class="text-center">
            <button type="submit" class="btn btn-info btn-raised">
                <i class="fas fa-save"></i> &nbsp; Guardar
            </button>
            <button type="button" class="btn btn-raised btn-secondary btn-sm"
                onclick="cancelarNota()">
                <i class="fas fa-times"></i> &nbsp; Cancelar
            </button>
        </div>

    </form>
</div>



<div class="modal fade" id="modalFactura" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Buscar Factura</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <input type="text" id="buscarFactura"
                    class="form-control mb-3"
                    placeholder="Nro factura">

                <div id="resultadoFacturas"></div>

            </div>

        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const tipoSelect = document.querySelector('[name="tipo"]');
        const movStock = document.querySelector('[name="movimiento_stock"]');
        const alcanceNota = document.querySelector('[name="alcance_nota"]');

        function actualizar() {
            const tipo = tipoSelect.value;

            if (tipo === 'debito') {
                movStock.value = 'NINGUNO';
                movStock.style.pointerEvents = 'none';
                movStock.style.backgroundColor = '#e9ecef';
                if (alcanceNota) {
                    alcanceNota.value = 'regularizar_diferencia';
                    alcanceNota.style.pointerEvents = 'none';
                    alcanceNota.style.backgroundColor = '#e9ecef';
                }
            } else {
                movStock.style.pointerEvents = 'auto';
                movStock.style.backgroundColor = '';
                if (alcanceNota && !alcanceNota.disabled) {
                    alcanceNota.style.pointerEvents = 'auto';
                    alcanceNota.style.backgroundColor = '';
                }
            }
        }

        tipoSelect.addEventListener('change', actualizar);
        if (alcanceNota) {
            alcanceNota.addEventListener('change', function() {
                const data = new FormData();
                data.append('accion', 'cambiar_alcance_nc');
                data.append('alcance_nota', alcanceNota.value);

                fetch('<?php echo SERVERURL ?>ajax/notasCreDeAjax.php', {
                        method: 'POST',
                        body: data
                    })
                    .then(r => r.json())
                    .then(resp => {
                        if (resp.status === 'ok') {
                            location.reload();
                            return;
                        }

                        Swal.fire('Error', resp.msg || 'No se pudo cambiar el alcance', 'error');
                    })
                    .catch(() => Swal.fire('Error', 'Error de comunicacion con el servidor', 'error'));
            });
        }

        // Ejecutar al cargar
        actualizar();
    });
</script>

<?php require_once "./vistas/inc/notasCreDe.php"; ?>
