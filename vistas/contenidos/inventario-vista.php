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
        <button class="btn btn-success" data-toggle="modal" data-target="#ModalProducto">
            <i class="fas fa-plus"></i> &nbsp; Agregar producto
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

<!-- MODAL AGREGAR/EDITAR PRODUCTO -->
<div class="modal fade" id="ModalProducto" tabindex="-1" role="dialog" aria-labelledby="ModalProducto" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="form-neon FormularioAjax" method="POST" action="">
            <input type="hidden" name="accion" value="guardar_producto">
            <input type="hidden" name="id" id="productoId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Producto</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Código</label>
                        <input type="text" class="form-control" name="codigo" id="codigo" required>
                    </div>
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" class="form-control" name="stock" id="stock" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Precio</label>
                        <input type="number" class="form-control" name="precio" id="precio" step="0.01" min="0" required>
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

<script>
function editarProducto(producto) {
    document.getElementById('productoId').value = producto.id;
    document.getElementById('codigo').value = producto.codigo;
    document.getElementById('nombre').value = producto.nombre;
    document.getElementById('stock').value = producto.stock;
    document.getElementById('precio').value = producto.precio;
}
</script>
