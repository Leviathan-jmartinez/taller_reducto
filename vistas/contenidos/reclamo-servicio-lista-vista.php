<?php
if (!mainModel::tienePermisoVista('servicio.reclamo.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$busqueda = $_SESSION['busqueda_reclamo_servicio'] ?? '';
?>

<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-exclamation-circle fa-fw"></i> &nbsp; RECLAMOS DE SERVICIO
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>reclamo-servicio-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>reclamo-servicio-lista/">
                <i class="fas fa-search fa-fw"></i> &nbsp; LISTADO DE RECLAMOS
            </a>
        </li>
    </ul>
</div>

<?php if (!$busqueda) { ?>

    <!-- FORMULARIO DE BÚSQUEDA -->
    <div class="container-fluid">
        <form class="form-neon FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="default"
            autocomplete="off">

            <input type="hidden" name="modulo" value="reclamo_servicio">

            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label>Buscar por cliente o placa</label>
                            <input type="text"
                                class="form-control"
                                name="busqueda_inicial"
                                placeholder="Ej: Juan, ABC123">
                        </div>
                    </div>

                    <div class="col-12 text-center" style="margin-top: 30px;">
                        <button type="submit" class="btn btn-raised btn-info">
                            <i class="fas fa-search"></i> &nbsp; BUSCAR
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

<?php } else { ?>

    <!-- MOSTRAR FILTRO ACTIVO -->
    <div class="container-fluid">
        <form class="FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="search"
            autocomplete="off">

            <input type="hidden" name="modulo" value="reclamo_servicio">
            <input type="hidden" name="eliminar_busqueda" value="eliminar">

            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-6">
                        <p class="text-center" style="font-size: 20px;">
                            Búsqueda por:
                            <strong><?php echo $busqueda; ?></strong>
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

<?php } ?>

<!-- LISTADO (SIEMPRE SE MUESTRA) -->
<div class="container-fluid">
    <?php
    require_once "./controladores/reclamoServicioControlador.php";
    $reclamo = new reclamoServicioControlador();

    echo $reclamo->paginador_reclamo_controlador(
        $pagina[1],
        15,
        $_SESSION['nivel_str'],
        $pagina[0],
        $_SESSION['busqueda_reclamo_servicio'] ?? ''
    );
    ?>
</div>