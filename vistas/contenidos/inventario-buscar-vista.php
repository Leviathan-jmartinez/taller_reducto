<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('inventario.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$fecha_inicio = $_SESSION['fecha_inicio_inventario'] ?? '';
$fecha_final  = $_SESSION['fecha_final_inventario'] ?? '';
$nro_inventario = $_SESSION['nro_inventario'] ?? '';
$tipo_inv = $_SESSION['tipo_inv'] ?? '';
$estado_inv = $_SESSION['estado_inv'] ?? '';
$observacion = $_SESSION['observacion_inv'] ?? '';
$usuario = $_SESSION['usuario_inv'] ?? '';
$filtro_activo = $_SESSION['filtro_inventario_activo'] ?? '';

$hayFiltros = $filtro_activo || $fecha_inicio || $fecha_final || $nro_inventario || $tipo_inv || $estado_inv !== '' || $observacion || $usuario;

if (!isset($pagina)) {
    $url = $_GET['views'] ?? "inventario-buscar/1";
    $url = explode("/", $url);
    $pagina = [$url[0], $url[1] ?? 1];
}
?>

<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-boxes fa-fw"></i> &nbsp; MODULO DE INVENTARIO
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>inventario/"><i class="fas fa-list fa-fw"></i> &nbsp; Inventario</a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>inventario-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar inventario</a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="search" autocomplete="off">
        <input type="hidden" name="modulo" value="inventario">

        <div class="row">
            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha desde</label>
                    <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="fecha_final">Fecha hasta</label>
                    <input type="date" class="form-control" name="fecha_final" id="fecha_final" value="<?php echo htmlspecialchars($fecha_final, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="nro_inventario">Numero</label>
                    <input type="number" class="form-control" name="nro_inventario" id="nro_inventario" value="<?php echo htmlspecialchars($nro_inventario, ENT_QUOTES, 'UTF-8'); ?>" min="1">
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="tipo_inv">Tipo</label>
                    <select name="tipo_inv" id="tipo_inv" class="form-control">
                        <option value="">Todos</option>
                        <option value="General" <?php echo $tipo_inv === 'General' ? 'selected' : ''; ?>>General</option>
                        <option value="Categoria" <?php echo $tipo_inv === 'Categoria' ? 'selected' : ''; ?>>Categoria</option>
                        <option value="Proveedor" <?php echo $tipo_inv === 'Proveedor' ? 'selected' : ''; ?>>Proveedor</option>
                        <option value="Producto" <?php echo $tipo_inv === 'Producto' ? 'selected' : ''; ?>>Articulo</option>
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="estado_inv">Estado</label>
                    <select name="estado_inv" id="estado_inv" class="form-control">
                        <option value="">Todos</option>
                        <option value="1" <?php echo $estado_inv === '1' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="2" <?php echo $estado_inv === '2' ? 'selected' : ''; ?>>Modificado</option>
                        <option value="3" <?php echo $estado_inv === '3' ? 'selected' : ''; ?>>Ajustado</option>
                        <option value="0" <?php echo $estado_inv === '0' ? 'selected' : ''; ?>>Anulado</option>
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="observacion">Observacion</label>
                    <input type="text" class="form-control" name="observacion" id="observacion" value="<?php echo htmlspecialchars($observacion, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" class="form-control" name="usuario" id="usuario" value="<?php echo htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="col-12 col-md-3 d-flex align-items-end">
                <div class="form-group w-100">
                    <button type="submit" class="btn btn-raised btn-info btn-block">
                        <i class="fas fa-search"></i> &nbsp; Buscar
                    </button>
                </div>
            </div>
        </div>
    </form>

    <?php if ($hayFiltros) { ?>
        <form class="FormularioAjax text-center mb-3" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="search" autocomplete="off">
            <input type="hidden" name="modulo" value="inventario">
            <input type="hidden" name="eliminar_busqueda" value="eliminar">
            <button type="submit" class="btn btn-raised btn-danger">
                <i class="far fa-trash-alt"></i> &nbsp; Limpiar filtros
            </button>
        </form>
    <?php } ?>
</div>

<div class="container-fluid">
    <?php
    if ($hayFiltros) {
        require_once "./controladores/inventarioControlador.php";
        $ins_inventario = new inventarioControlador();
        $ins_inventario->paginador_inv_controlador(
            $pagina[1],
            10,
            $pagina[0],
            $fecha_inicio,
            $fecha_final,
            $nro_inventario,
            $tipo_inv,
            $estado_inv,
            $observacion,
            $usuario
        );
    } else {
        echo '<div class="alert alert-info text-center" role="alert">
                Use al menos un filtro para ver el detalle de inventario.
              </div>';
    }
    ?>
</div>

<?php if ($hayFiltros) { ?>
<div class="modal fade" id="modalDetalleInventario" tabindex="-1" role="dialog" aria-labelledby="modalDetalleInventarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalDetalleInventarioLabel">
                    <i class="fas fa-eye"></i> &nbsp; Detalle de inventario
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="detalleInventarioCabecera"></div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="detalleInventarioBuscar" placeholder="Buscar por codigo o articulo">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="detalleInventarioFiltro">
                            <option value="todos">Todos</option>
                            <option value="diferencias">Con diferencia</option>
                            <option value="sobrantes">Sobrantes</option>
                            <option value="faltantes">Faltantes</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="detalleInventarioRegistros">
                            <option value="50">50 por pagina</option>
                            <option value="100" selected>100 por pagina</option>
                            <option value="200">200 por pagina</option>
                        </select>
                    </div>
                </div>

                <div id="detalleInventarioBody"></div>
                <div id="detalleInventarioPaginacion"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php include "./vistas/inc/inventario.php"; ?>
