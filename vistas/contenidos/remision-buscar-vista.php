<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-search fa-fw"></i> &nbsp; REMISIÓN
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>remision-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVA REMISIÓN
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>remision-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR
            </a>
        </li>
    </ul>
</div>

<?php
/* 🔹 Fechas desde sesión (REMISIÓN) */
$fecha_inicio = $_SESSION['fecha_inicio_remision'] ?? '';
$fecha_final  = $_SESSION['fecha_final_remision'] ?? '';

$fecha_inicio_dt = $fecha_inicio ? $fecha_inicio . ' 00:00:00' : '';
$fecha_final_dt  = $fecha_final  ? $fecha_final  . ' 23:59:59' : '';
?>

<?php if (!$fecha_inicio && !$fecha_final) { ?>

    <!-- 🔹 FORMULARIO DE BÚSQUEDA -->
    <div class="container-fluid">
        <form class="form-neon FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="default"
            autocomplete="off">

            <input type="hidden" name="modulo" value="remision">

            <!-- Fechas completas -->
            <input type="hidden" name="fecha_inicio_dt" value="">
            <input type="hidden" name="fecha_final_dt" value="">

            <div class="container-fluid">
                <div class="row justify-content-md-center">

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label>Fecha inicial</label>
                            <input type="date"
                                class="form-control"
                                name="fecha_inicio"
                                id="fecha_inicio">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label>Fecha final</label>
                            <input type="date"
                                class="form-control"
                                name="fecha_final"
                                id="fecha_final">
                        </div>
                    </div>

                    <div class="col-12 text-center" style="margin-top: 40px;">
                        <button type="submit" class="btn btn-raised btn-info">
                            <i class="fas fa-search"></i> &nbsp; BUSCAR
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>

<?php } else { ?>

    <!-- 🔹 FORMULARIO ELIMINAR BÚSQUEDA -->
    <div class="container-fluid">
        <form class="FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="search"
            autocomplete="off">

            <input type="hidden" name="modulo" value="remision">
            <input type="hidden" name="eliminar_busqueda" value="eliminar">

            <input type="hidden" name="fecha_inicio_dt" value="<?php echo $fecha_inicio_dt; ?>">
            <input type="hidden" name="fecha_final_dt" value="<?php echo $fecha_final_dt; ?>">

            <div class="container-fluid">
                <div class="row justify-content-md-center">

                    <div class="col-12 col-md-6">
                        <p class="text-center" style="font-size: 20px;">
                            Fecha de búsqueda:
                            <strong>
                                <?php echo $fecha_inicio; ?> &nbsp; a &nbsp; <?php echo $fecha_final; ?>
                            </strong>
                        </p>
                    </div>

                    <div class="col-12 text-center" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-raised btn-danger">
                            <i class="far fa-trash-alt"></i> &nbsp; ELIMINAR BÚSQUEDA
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <!-- 🔹 RESULTADOS -->
    <div class="container-fluid">
        <?php
        require_once "./controladores/remisionControlador.php";
        $remision = new remisionControlador();

        echo $remision->paginador_remision_controlador(
            $pagina[1],
            15,
            $_SESSION['nivel_str'],
            $pagina[0],
            $_SESSION['fecha_inicio_remision'],
            $_SESSION['fecha_final_remision']
        );
        ?>
    </div>

<?php } ?>