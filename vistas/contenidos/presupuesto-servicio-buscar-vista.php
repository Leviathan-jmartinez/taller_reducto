<?php
if (!mainModel::tienePermisoVista('servicio.presupuesto.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
} ?>

<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR PRESUPUESTO DE SERVICIO
    </h3>
</div>
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>presupuesto-servicio-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; CARGAR PRESUPUESTO</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>presupuesto-servicio-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTADOS DE PRESUPUESTOS</a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>presupuesto-servicio-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA</a>
        </li>
    </ul>
</div>

<?php
$fecha_inicio = $_SESSION['fecha_inicio_presupuesto_servicio'] ?? '';
$fecha_final  = $_SESSION['fecha_final_presupuesto_servicio'] ?? '';
?>

<?php if (!$fecha_inicio && !$fecha_final) { ?>

    <div class="container-fluid">
        <form class="form-neon FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="default"
            autocomplete="off">

            <input type="hidden" name="modulo" value="presupuesto_servicio">

            <div class="row justify-content-md-center">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label>Fecha inicial</label>
                        <input type="date" class="form-control" name="fecha_inicio">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label>Fecha final</label>
                        <input type="date" class="form-control" name="fecha_final">
                    </div>
                </div>

                <div class="col-12 text-center" style="margin-top: 40px;">
                    <button type="submit" class="btn btn-raised btn-info">
                        <i class="fas fa-search"></i> &nbsp; BUSCAR
                    </button>
                </div>
            </div>
        </form>
    </div>

<?php } else { ?>

    <div class="container-fluid">
        <form class="FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="search"
            autocomplete="off">

            <input type="hidden" name="modulo" value="presupuesto_servicio">
            <input type="hidden" name="eliminar_busqueda" value="eliminar">

            <div class="row justify-content-md-center">
                <div class="col-12 col-md-6">
                    <p class="text-center" style="font-size: 20px;">
                        Fecha de búsqueda:
                        <strong><?php echo $fecha_inicio ?> &nbsp; a &nbsp; <?php echo $fecha_final ?></strong>
                    </p>
                </div>

                <div class="col-12 text-center" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-raised btn-danger">
                        <i class="far fa-trash-alt"></i> &nbsp; ELIMINAR BÚSQUEDA
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="container-fluid">
        <?php
        require_once "./controladores/presupuestoServicioControlador.php";
        $presupuesto = new presupuestoServicioControlador();

        echo $presupuesto->paginador_presupuestoservi_controlador(
            $pagina[1],
            15,
            $_SESSION['nivel_str'],
            $pagina[0],
            $_SESSION['fecha_inicio_presupuesto_servicio'],
            $_SESSION['fecha_final_presupuesto_servicio']
        );
        ?>
    </div>

<?php } ?>