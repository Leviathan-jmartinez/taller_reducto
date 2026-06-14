<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('servicio.ot.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
} ?>


<?php
$fecha_inicio = $_SESSION['fecha_inicio_orden_trabajo'] ?? '';
$fecha_final  = $_SESSION['fecha_final_orden_trabajo'] ?? '';
$estado = $_SESSION['estado_ot'] ?? '';
$ordenOT = mainModel::cargar_ordenamiento_sesion('ot', ['fecha', 'estado'], 'fecha', 'DESC');

if (isset($_GET['estado_ot']) && in_array((string)$_GET['estado_ot'], ['0', '1', '2', '3'], true)) {
    $_SESSION['estado_ot'] = (string)$_GET['estado_ot'];
    $_SESSION['filtro_orden_trabajo_activo'] = '1';
    $estado = (string)$_GET['estado_ot'];
}

$busqueda_activa = isset($_SESSION['filtro_orden_trabajo_activo']);
?>

<!-- SIEMPRE MOSTRAR FORMULARIO -->
<div class="container-fluid form-neon app-view">
    <h3 class="text-left">
        <i class="fas fa-tools fa-fw"></i> &nbsp; ORDEN DE TRABAJO
    </h3>

    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>ordenTrabajo-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>ordenTrabajo-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR
            </a>
        </li>
    </ul>
    <form class="form-neon FormularioAjax app-form"
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
                        <option value="1" <?php if ($estado == "1") echo "selected"; ?>>Activa</option>
                        <option value="2" <?php if ($estado == "2") echo "selected"; ?>>Servicio registrado</option>
                        <option value="3" <?php if ($estado == "3") echo "selected"; ?>>Pendiente completar</option>
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

<?php if ($busqueda_activa) { ?>
    <div class="container-fluid mt-3">
        <p class="text-center" style="font-size: 18px;">
            Mostrando resultados
            <?php if ($fecha_inicio) { ?>
                desde <strong><?php echo $fecha_inicio; ?></strong>
            <?php
            } ?>
            <?php if ($fecha_final) { ?>
                hasta <strong><?php echo $fecha_final; ?></strong>
            <?php
            } ?>
            <?php
            $estados = [
                '1' => 'Activa',
                '2' => 'Servicio registrado',
                '3' => 'Pendiente completar',
                '0' => 'Anulada'
            ];

            if ($estado !== '') { ?>
                estado <strong><?php echo $estados[$estado] ?? $estado; ?></strong>
            <?php
            } else { ?>
                estado <strong>Todos</strong>
            <?php
            } ?>
        </p>
    </div>
<?php
} ?>

<div class="container-fluid">
    <?php
    if ($busqueda_activa) {
        require_once "./controladores/ordenTrabajoControlador.php";

        $ot = new ordenTrabajoControlador();

        echo $ot->listar_ot_controlador(
            $pagina[1],
            15,
            $pagina[0],
            $fecha_inicio,
            $fecha_final,
            $ordenOT['orden'],
            $ordenOT['direccion']
        );
    } else {
        echo '<div class="alert alert-info text-center">Ingrese un criterio de busqueda para ver ordenes de trabajo.</div>';
    }
    ?>
</div>

<?php
include_once "./vistas/inc/ordenTrabajoJS.php"; ?>
