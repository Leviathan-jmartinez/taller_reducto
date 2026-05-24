<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('compra.pedido.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$ordenPedido = mainModel::cargar_ordenamiento_sesion('pedido', ['fecha', 'estado'], 'fecha', 'DESC');
$pedido_orden = $ordenPedido['orden'];
$pedido_direccion = $ordenPedido['direccion'];
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTADO DE PEDIDOS
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>pedido-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PEDIDO</a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>pedido-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTADOS DE PEDIDOS</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>pedido-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA</a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <?php
require_once "./controladores/pedidoControlador.php";
    $ins_pedido = new pedidoControlador();
    $ins_pedido->paginador_pedidos_controlador($pagina[1], 15, $pagina[0], "", "", '', $pedido_orden, $pedido_direccion);
    ?>
</div>
