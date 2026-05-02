<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('compra.presupuesto.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
} ?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; BUSCADOR DE PRESUPUESTOS DE COMPRA
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>presupuesto-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PRESUPUESTO</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>presupuesto-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTADOS DE PRESUPUESTOS</a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>presupuesto-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR</a>
        </li>
    </ul>
</div>

<?php
$fecha_inicio = $_SESSION['fecha_inicio_presupuesto'] ?? '';
$fecha_final  = $_SESSION['fecha_final_presupuesto'] ?? '';
$fecha_inicio_dt = $fecha_inicio ? $fecha_inicio . ' 00:00:00' : '';
$fecha_final_dt  = $fecha_final  ? $fecha_final  . ' 23:59:59' : '';
$nro_presupuesto = $_SESSION['nro_presupuesto'] ?? '';
$proveedor_presupuesto = $_SESSION['proveedor_presupuesto'] ?? '';

if (!isset($pagina) || !is_array($pagina)) {
    $url = $_GET['views'] ?? 'presupuesto-buscar/1';
    $pagina = explode('/', $url);
    $pagina = [$pagina[0] ?? 'presupuesto-buscar', $pagina[1] ?? 1];
}
?>

<?php if (!$fecha_inicio && !$fecha_final && !$nro_presupuesto && !$proveedor_presupuesto) { ?>
    <div class="container-fluid">
        <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="search" autocomplete="off">
            <input type="hidden" name="modulo" value="presupuesto">
            <input type="hidden" name="fecha_inicio_dt" value="">
            <input type="hidden" name="fecha_final_dt" value="">

            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha inicial</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" maxlength="30">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="fecha_final">Fecha final</label>
                            <input type="date" class="form-control" name="fecha_final" id="fecha_final" maxlength="30">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="nro_presupuesto">Nro. Presupuesto</label>
                            <input type="text" class="form-control" name="nro_presupuesto" id="nro_presupuesto">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="proveedor_presupuesto">Proveedor</label>
                            <input type="text" class="form-control" name="proveedor_presupuesto" id="proveedor_presupuesto">
                        </div>
                    </div>
                    <div class="col-12 text-center" style="margin-top: 40px;">
                        <button type="submit" class="btn btn-raised btn-info"><i class="fas fa-search"></i> &nbsp; BUSCAR</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php } else { ?>
    <div class="container-fluid">
        <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="search" autocomplete="off">
            <input type="hidden" name="modulo" value="presupuesto">
            <input type="hidden" name="eliminar_busqueda" value="eliminar">
            <input type="hidden" name="fecha_inicio_dt" value="<?php echo $fecha_inicio_dt; ?>">
            <input type="hidden" name="fecha_final_dt" value="<?php echo $fecha_final_dt; ?>">

            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-6">
                        <p class="text-center" style="font-size: 20px;">
                            <?php if ($fecha_inicio && $fecha_final) { ?>
                                Fecha de busqueda:
                                <strong><?php echo $fecha_inicio ?> &nbsp; a &nbsp; <?php echo $fecha_final ?></strong>
                            <?php
} elseif ($nro_presupuesto) { ?>
                                Busqueda por Nro. Presupuesto:
                                <strong><?php echo $nro_presupuesto; ?></strong>
                            <?php
} elseif ($proveedor_presupuesto) { ?>
                                Busqueda por Proveedor:
                                <strong><?php echo $proveedor_presupuesto; ?></strong>
                            <?php
} ?>
                        </p>
                    </div>
                    <div class="col-12 text-center" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-raised btn-danger"><i class="fas fa-times"></i> &nbsp; Limpiar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="container-fluid">
        <?php
require_once "./controladores/presupuestoControlador.php";
        $ins_presupuesto = new presupuestoControlador();
        $ins_presupuesto->paginador_presupuestos_controlador(
            $pagina[1],
            15,
            $pagina[0],
            $_SESSION['fecha_inicio_presupuesto'] ?? '',
            $_SESSION['fecha_final_presupuesto'] ?? '',
            $_SESSION['nro_presupuesto'] ?? '',
            $_SESSION['proveedor_presupuesto'] ?? ''
        );
        ?>
    </div>
<?php
} ?>
