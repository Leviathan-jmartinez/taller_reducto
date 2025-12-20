<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(['name' => 'STR']);
}

$id_usuario = $_SESSION['id_str'];
$facturaNC = $_SESSION['NC_FACTURA'] ?? null;
$detalleNC = $_SESSION['NC_DETALLE'] ?? [];
?>

<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; INGRESO DE NOTA DE COMPRA
    </h3>
</div>
<div class="col-md-4 mt-3">
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
<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/notaCompraAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="guardar_nota_compra">
        <input type="hidden" name="idusuario" value="<?= $id_usuario ?>">

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
                    <label>Serie</label>
                    <input type="text" name="serie" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label>N° Documento</label>
                    <input type="text" name="nro_documento" class="form-control" required>
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
                    <input type="number" name="idcompra_cabecera" class="form-control" required>
                </div>

                <div class="col-md-4 mt-3">
                    <label>Proveedor</label>
                    <input type="number" name="idproveedor" class="form-control">
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
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
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

                        $total = $subtotal + $total_iva5 + $total_iva10;


                        foreach ($detalleNC as $i => $d):
                            $total_item = $d['cantidad'] * $d['precio']; ?>
                            <tr>
                                <td><?= $d['id_articulo'] ?></td>
                                <td><?= $d['descripcion'] ?></td>

                                <td>
                                    <input type="number"
                                        class="form-control form-control-sm text-right"
                                        value="<?= $d['cantidad'] ?>"
                                        min="0.01" step="0.01"
                                        onchange="actualizarItem(<?= $i ?>)">
                                </td>

                                <td>
                                    <input type="number"
                                        class="form-control form-control-sm text-right"
                                        value="<?= $d['precio'] ?>"
                                        min="0.01" step="0.01"
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

            <button type="button" class="btn btn-primary btn-sm" onclick="agregarFila()">
                <i class="fas fa-plus"></i> Agregar ítem
            </button>
        </fieldset>

        <!-- ================= TOTALES ================= -->
        <fieldset class="border p-3 mb-4">
            <legend class="w-auto px-2">Totales</legend>

            <div class="row">
                <div class="col-md-3">
                    <label>Subtotal</label>
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
                    <label>Total</label>
                    <input type="text" name="total" id="total"
                        class="form-control"
                        value="<?= number_format($total, 0, ',', '.') ?>" readonly>
                </div>
            </div>
        </fieldset>

        <div class="text-center">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Guardar Nota
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
                    placeholder="Nro factura o proveedor">

                <div id="resultadoFacturas"></div>

            </div>

        </div>
    </div>
</div>

<?php require_once "./vistas/inc/notasCreDe.php"; ?>