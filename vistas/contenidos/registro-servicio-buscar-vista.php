<?php
if (!mainModel::tienePermisoVista('servicio.registro.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
} ?>

<div class="container-fluid mb-3">
    <h3>
        <i class="fas fa-clipboard-check"></i>
        &nbsp; BUSCAR REGISTRO DE SERVICIO
    </h3>

    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>/registro-servicio-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; REGISTRO DE SERVICIO
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>/registro-servicio-lista/">
                <i class="fas fa-search fa-fw"></i> &nbsp; HISTORIAL DE SERVICIOS
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>registro-servicio-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR
            </a>
        </li>
    </ul>
</div>

<?php
$fecha_inicio = $_SESSION['fecha_inicio_registro_servicio'] ?? '';
$fecha_final  = $_SESSION['fecha_final_registro_servicio'] ?? '';
?>

<?php if (!$fecha_inicio && !$fecha_final) { ?>

    <div class="container-fluid">
        <form class="form-neon FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="default"
            autocomplete="off">

            <input type="hidden" name="modulo" value="registro_servicio">

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

            <input type="hidden" name="modulo" value="registro_servicio">
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
        require_once "./controladores/registroServicioControlador.php";

        $reg = new registroServicioControlador();

        echo $reg->paginador_registro_servicio_controlador(
            $pagina[1],
            15,
            $_SESSION['nivel_str'],
            $pagina[0],
            $_SESSION['fecha_inicio_registro_servicio'],
            $_SESSION['fecha_final_registro_servicio']
        );
        ?>
    </div>

<?php } ?>