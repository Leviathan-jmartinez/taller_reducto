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
            <a href="<?php echo SERVERURL; ?>/ordenTrabajo-lista/">
                <i class="fas fa-search fa-fw"></i> &nbsp; HISTORIAL DE ORDENES DE TRABAJO
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


<?php if (!$fecha_inicio && !$fecha_final) { ?>

    <div class="container-fluid">
        <form class="form-neon FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="default"
            autocomplete="off">

            <input type="hidden" name="modulo" value="orden_trabajo">

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

            <input type="hidden" name="modulo" value="orden_trabajo">
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
        require_once "./controladores/ordenTrabajoControlador.php";

        $ot = new ordenTrabajoControlador();

        echo $ot->paginador_ot_controlador(
            $pagina[1],
            15,
            $_SESSION['nivel_str'],
            $pagina[0],
            $_SESSION['fecha_inicio_orden_trabajo'] ?? '',
            $_SESSION['fecha_final_orden_trabajo'] ?? ''
        );
        ?>
    </div>

<?php } ?>