<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('compra.pedido.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

if (isset($_GET['estado_pedido']) && in_array((string)$_GET['estado_pedido'], ['0', '1', '2'], true)) {
    $_SESSION['estado_pedido'] = (string)$_GET['estado_pedido'];
}

$ordenPedido = mainModel::cargar_ordenamiento_sesion('pedido', ['fecha', 'estado'], 'fecha', 'DESC');

$fecha_inicio = $_SESSION['fecha_inicio_pedido'] ?? '';
$fecha_final = $_SESSION['fecha_final_pedido'] ?? '';
$estado_pedido = $_SESSION['estado_pedido'] ?? '';
$pedido_orden = $ordenPedido['orden'];
$pedido_direccion = $ordenPedido['direccion'];
$busqueda_activa = $fecha_inicio !== '' || $fecha_final !== '' || isset($_SESSION['estado_pedido']);
$estadosPedido = [
    '' => 'Todos',
    '0' => 'Anulado',
    '1' => 'Pendiente',
    '2' => 'Procesado'
];
?>

<?php if (!$busqueda_activa) { ?>
    <div class="container-fluid form-neon">
        <h3 class="text-left">
            <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR PEDIDOS
        </h3>
        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a href="<?php echo SERVERURL; ?>pedido-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PEDIDO</a>
            </li>
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>pedido-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR PEDIDOS</a>
            </li>
        </ul>

        <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="search" autocomplete="off">
            <input type="hidden" name="modulo" value="pedido">
            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha inicial</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="fecha_final">Fecha final</label>
                            <input type="date" class="form-control" name="fecha_final" id="fecha_final">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="estado_pedido">Estado</label>
                            <select class="form-control" name="estado_pedido" id="estado_pedido">
                                <option value="">Todos</option>
                                <option value="1">Pendiente</option>
                                <option value="2">Procesado</option>
                                <option value="0">Anulado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <p class="text-center" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-raised btn-info"><i class="fas fa-search"></i> &nbsp; BUSCAR</button>
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php } else { ?>
    <div class="container-fluid form-neon">
        <h3 class="text-left">
            <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR PEDIDOS
        </h3>
        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a href="<?php echo SERVERURL; ?>pedido-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PEDIDO</a>
            </li>
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>pedido-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR PEDIDOS</a>
            </li>
        </ul>

        <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="search" autocomplete="off">
            <input type="hidden" name="modulo" value="pedido">
            <input type="hidden" name="eliminar_busqueda" value="eliminar">
            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-8">
                        <p class="text-center" style="font-size: 20px;">
                            Busqueda:
                            <strong>
                                <?php
                                $criterios = [];
                                if ($fecha_inicio !== '' || $fecha_final !== '') {
                                    $criterios[] = 'Fecha: ' . ($fecha_inicio ?: 'inicio') . ' a ' . ($fecha_final ?: 'final');
                                }
                                if (isset($_SESSION['estado_pedido'])) {
                                    $criterios[] = 'Estado: ' . ($estadosPedido[(string)$estado_pedido] ?? $estado_pedido);
                                }
                                echo htmlspecialchars(implode(' | ', $criterios), ENT_QUOTES, 'UTF-8');
                                ?>
                            </strong>
                        </p>
                    </div>
                    <div class="col-12">
                        <p class="text-center" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-raised btn-danger"><i class="far fa-trash-alt"></i> &nbsp; ELIMINAR BUSQUEDA</button>
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="container-fluid">
        <?php
        require_once "./controladores/pedidoControlador.php";
        $ins_pedido = new pedidoControlador();
        $ins_pedido->paginador_pedidos_controlador($pagina[1], 15, $pagina[0], $fecha_inicio, $fecha_final, $estado_pedido, $pedido_orden, $pedido_direccion);
        ?>
    </div>
<?php
}
?>
