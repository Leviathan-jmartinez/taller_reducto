<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Valor por defecto para vista inventario
if (!isset($_SESSION['inventario_tipo'])) {
    $_SESSION['inventario_tipo'] = "todos"; // todos, críticos, etc.
}

$tipo = $_SESSION['inventario_tipo'];
if (!isset($_SESSION['id_inv_seleccionado'])) {
    $_SESSION['id_inv_seleccionado'] = '';
}
?>

<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-boxes fa-fw"></i> &nbsp; MÓDULO DE INVENTARIO
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="#"><i class="fas fa-list fa-fw"></i> &nbsp; Listado de productos</a>
        </li>
        <li>
            <a href="#"><i class="fas fa-chart-bar fa-fw"></i> &nbsp; Estadísticas</a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <!-- Botones de acción -->

    <div class="container-fluid">
        <!-- Botones de acción -->
        <?php var_dump($_SESSION['id_inv_seleccionado']); ?>
        <div style="display: flex; justify-content: flex-end; margin-bottom: 15px; gap: 10px;">
            <?php if ($_SESSION['id_inv_seleccionado'] != ''): ?>
                <button class="btn btn-success" data-toggle="modal" data-target="#modalInventario">
                    <i class="fas fa-plus"></i> &nbsp; Generar Inventario
                </button>
            <?php endif; ?>
            <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#ModalBuscarINV">
                <i class="fas fa-search"></i> &nbsp; Cargar Inventario
            </button>
        </div>
    </div>


    <!-- Buscador -->
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text"
                id="filtro-productos"
                class="form-control"
                placeholder="🔎 Buscar producto...">
        </div>
    </div>

    <!-- TABLA DETALLE DE PRODUCTOS -->
    <div class="row" style="margin-top:20px;">
        <div class="col-12">
            <h5>Detalle de productos</h5>
            <table class="table table-dark table-sm" id="tabla-detalle">
                <thead>
                    <tr>
                        <th style="width:20%;">Código</th>
                        <th style="width:20%;">Producto</th>
                        <th class="text-center" style="width:10%;">Costo</th>
                        <th class="text-center" style="width:20%;">Cantidad en stock</th>
                        <th class="text-center" style="width:20%;">Cantidad inventariada</th>
                        <th class="text-center" style="width:20%;">Diferencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($_SESSION['Cdatos_articuloINV'])): ?>
                        <?php foreach ($_SESSION['Cdatos_articuloINV'] as $i => $item): ?>
                            <tr data-index="<?= $i ?>">
                                <td><?= htmlspecialchars($item['codigo']); ?></td>
                                <td><?= htmlspecialchars($item['descripcion']); ?></td>

                                <td class="text-center">
                                    <input type="number"
                                        class="form-control text-center"
                                        value="<?= $item['costo']; ?>"
                                        readonly>
                                </td>

                                <td class="text-center">
                                    <input type="number"
                                        class="form-control text-center teorica"
                                        value="<?= $item['cantidad_teorica']; ?>"
                                        readonly>
                                </td>

                                <td class="text-center">
                                    <input type="number"
                                        name="cantidades_fisicas[]"
                                        class="form-control text-center cantidad"
                                        value="<?= $item['cantidad_fisica']; ?>"
                                        min="0"
                                        required>
                                </td>

                                <td class="text-center">
                                    <span class="diferencia">
                                        <?= $item['diferencia']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-12 text-center">
        <button id="guardar-ajuste" class="btn btn-success">
            <i class="fas fa-save"></i> Guardar cambios
        </button>
        <button id="btn-ajustar-stock" class="btn btn-danger">
            <i class="fas fa-sync-alt"></i> Ajustar Stock
        </button>
        <button id="btn-limpiar-todo" class="btn btn-default">
            <i class="fas fa-trash-alt"></i> Limpiar Todo
        </button>
    </div>
</div>




<!-- MODAL NUEVO INVENTARIO -->
<div class="modal fade" id="modalInventario" tabindex="-1" aria-labelledby="modalInventarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formInventario" method="POST">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalInventarioLabel">
                        <i class="fas fa-boxes"></i> Nuevo Inventario
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <!-- Tipo de inventario -->
                    <div class="form-group">
                        <label for="tipo_inventario">Tipo de Inventario</label>
                        <select name="tipo_inventario" id="tipo_inventario" class="form-control" required>
                            <option value="">Seleccione...</option>
                            <option value="General">Inventario General</option>
                            <option value="Categoria">Inventario por Categoría</option>
                            <option value="Proveedor">Inventario por Proveedor</option>
                            <option value="Producto">Inventario por Producto</option>
                        </select>
                    </div>

                    <!-- Subtipo Categoría -->
                    <div class="form-group" id="grupo_categoria" style="display:none;">
                        <label for="subtipo_categoria">Seleccione Categoría</label>
                        <select name="subtipo_categoria" id="subtipo_categoria" class="form-control">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>

                    <!-- Subtipo Proveedor -->
                    <div class="form-group" id="grupo_proveedor" style="display:none;">
                        <label for="subtipo_proveedor">Seleccione Proveedor</label>
                        <select name="subtipo_proveedor" id="subtipo_proveedor" class="form-control">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>

                    <!-- Subtipo Producto -->
                    <div class="form-group" id="grupo_producto" style="display:none;">
                        <label for="subtipo_producto">Seleccione Productos</label>
                        <select name="subtipo_producto[]" id="subtipo_producto" class="form-control" multiple="multiple"></select>
                    </div>

                    <!-- Fecha creación -->
                    <div class="form-group">
                        <label>Fecha de Creación</label>
                        <input type="text" name="fecha_creacion" class="form-control" value="<?= date('Y-m-d H:i:s'); ?>" readonly>
                    </div>

                    <!-- Observación -->
                    <div class="form-group">
                        <label for="observacion">Observación</label>
                        <textarea name="observacion" id="observacion" class="form-control" rows="3" placeholder="Observación del inventario..."></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>

            </div>
        </form>
    </div>
</div>


<!-- MODAL BUSCAR OC -->
<div class="modal fade" id="ModalBuscarINV" tabindex="-1" role="dialog" aria-labelledby="ModalBuscarINV" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalBuscarINV">Agregar Inventario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <label for="input_item" class="bmd-label-floating">Código de Inventario, Obseracion</label>
                        <input type="text" pattern="[a-zA-z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" name="input_inv" id="input_inv" maxlength="30">
                    </div>
                </div>
                <br>
                <div class="container-fluid" id="tabla_INV">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="buscar_INV()"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= SERVERURL ?>vistas/js/jquery-3.6.0.min.js"></script>
<script src="<?= SERVERURL ?>vistas/js/popper.min.js"></script>
<script src="<?= SERVERURL ?>vistas/js/bootstrap.min.js"></script>

<?php include "./vistas/inc/inventario.php"; ?>