<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Valor por defecto para vista inventario
if (!isset($_SESSION['inventario_tipo'])) {
    $_SESSION['inventario_tipo'] = "todos"; // todos, críticos, etc.
}

$tipo = $_SESSION['inventario_tipo'];
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
    <div style="display: flex; justify-content: flex-end; margin-bottom: 15px; gap: 10px;">
        <button class="btn btn-success" data-toggle="modal" data-target="#modalInventario">
            <i class="fas fa-plus"></i> &nbsp; Generar Inventario
        </button>
    </div>

    <!-- Buscador -->
    <form class="form-neon mb-3" method="GET" action="">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Buscar por código o nombre" name="search">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
        </div>
    </form>

    <!-- Tabla productos (ejemplo estático, reemplazar por PHP/AJAX) -->
    <div class="table-responsive">
        <table class="table table-dark table-sm">
            <thead>
                <tr class="text-center">
                    <th>ID</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-center">
                    <td>1</td>
                    <td>P001</td>
                    <td class="text-left">Producto de ejemplo 1</td>
                    <td>50</td>
                    <td>150.00</td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#ModalProducto" onclick='editarProducto({id:1,codigo:"P001",nombre:"Producto de ejemplo 1",stock:50,precio:150.00})'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <tr class="text-center">
                    <td>2</td>
                    <td>P002</td>
                    <td class="text-left">Producto de ejemplo 2</td>
                    <td>20</td>
                    <td>75.50</td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#ModalProducto" onclick='editarProducto({id:2,codigo:"P002",nombre:"Producto de ejemplo 2",stock:20,precio:75.50})'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <!-- Agregar más filas según necesidad -->
            </tbody>
        </table>
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
                            <option value="general">Inventario General</option>
                            <option value="categoria">Inventario por Categoría</option>
                            <option value="proveedor">Inventario por Proveedor</option>
                            <option value="producto">Inventario por Producto</option>
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


<script src="<?= SERVERURL ?>vistas/js/jquery-3.6.0.min.js"></script>
<script src="<?= SERVERURL ?>vistas/js/popper.min.js"></script>
<script src="<?= SERVERURL ?>vistas/js/bootstrap.min.js"></script>

<?php include "./vistas/inc/inventario.php"; ?>

<script>
    function editarProducto(producto) {
        document.getElementById('productoId').value = producto.id;
        document.getElementById('codigo').value = producto.codigo;
        document.getElementById('nombre').value = producto.nombre;
        document.getElementById('stock').value = producto.stock;
        document.getElementById('precio').value = producto.precio;
    }
</script>