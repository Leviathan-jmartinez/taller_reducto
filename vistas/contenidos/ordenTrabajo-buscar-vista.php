<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('servicio.ot.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
} ?>

<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-tools fa-fw"></i> &nbsp; ÓRDENES DE TRABAJO
    </h3>

    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>/ordenTrabajo-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVA ORDEN DE TRABAJO
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>/ordenTrabajo-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR ORDENES DE TRABAJO POR FECHA
            </a>
        </li>
    </ul>
</div>

<?php
$fecha_inicio = $_SESSION['fecha_inicio_orden_trabajo'] ?? '';
$fecha_final  = $_SESSION['fecha_final_orden_trabajo'] ?? '';

?>


<?php
$estado = $_SESSION['estado_ot'] ?? ''; ?>

<!-- SIEMPRE MOSTRAR FORMULARIO -->
<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="search"
        autocomplete="off">

        <input type="hidden" name="modulo" value="orden_trabajo">

        <div class="row justify-content-md-center">
            <div class="col-12 col-md-4">
                <label>Fecha inicial</label>
                <input type="date" class="form-control" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>">
            </div>

            <div class="col-12 col-md-4">
                <label>Fecha final</label>
                <input type="date" class="form-control" name="fecha_final" value="<?php echo $fecha_final; ?>">
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado_ot" class="form-control">
                        <option value="">Todos</option>
                        <option value="1" <?php if ($estado == "1") echo "selected"; ?>>En proceso</option>
                        <option value="2" <?php if ($estado == "2") echo "selected"; ?>>Finalizado</option>
                        <option value="0" <?php if ($estado === "0") echo "selected"; ?>>Anulada</option>
                    </select>
                </div>
            </div>

            <div class="col-12 text-center mt-4">

                <button type="submit" class="btn btn-raised btn-info">
                    <i class="fas fa-search"></i> &nbsp; BUSCAR
                </button>

                <button type="button"
                    class="btn btn-raised btn-danger btn-limpiar-busqueda">
                    <i class="fas fa-times"></i> &nbsp; Cancelar
                </button>

            </div>
        </div>
    </form>
</div>


<div class="container-fluid">
    <?php
require_once "./controladores/ordenTrabajoControlador.php";

    $ot = new ordenTrabajoControlador();

    echo $ot->listar_ot_controlador(
        $pagina[1],
        15,
        $pagina[0],
        $_SESSION['fecha_inicio_orden_trabajo'] ?? '',
        $_SESSION['fecha_final_orden_trabajo'] ?? ''
    );
    ?>
</div>

<?php
include_once "./vistas/inc/ordenTrabajoJS.php"; ?>
