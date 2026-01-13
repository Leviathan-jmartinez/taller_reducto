<?php
if (!mainModel::tienePermisoVista('sucursal.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR SUCURSAL
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>sucursal-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR SUCURSAL
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>sucursal-lista/">
                <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE SUCURSALES
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>sucursal-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR SUCURSAL
            </a>
        </li>
    </ul>
</div>

<?php if (!isset($_SESSION['busqueda_sucursal']) || empty($_SESSION['busqueda_sucursal'])) { ?>

    <!-- FORM BUSQUEDA -->
    <div class="container-fluid">
        <form class="form-neon FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="default"
            autocomplete="off">

            <input type="hidden" name="modulo" value="sucursal">

            <div class="row justify-content-md-center">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="bmd-label-floating">
                            ¿Qué sucursal estás buscando?
                        </label>
                        <input type="text"
                            class="form-control"
                            name="busqueda_inicial"
                            maxlength="40">
                    </div>
                </div>

                <div class="col-12">
                    <p class="text-center mt-4">
                        <button type="submit" class="btn btn-raised btn-info">
                            <i class="fas fa-search"></i> &nbsp; BUSCAR
                        </button>
                    </p>
                </div>
            </div>
        </form>
    </div>

<?php } else { ?>

    <!-- ELIMINAR BUSQUEDA -->
    <div class="container-fluid">
        <form class="FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="search"
            autocomplete="off">

            <input type="hidden" name="modulo" value="sucursal">
            <input type="hidden" name="eliminar_busqueda" value="eliminar">

            <div class="row justify-content-md-center">
                <div class="col-12 col-md-6">
                    <p class="text-center" style="font-size:20px;">
                        Resultados de la búsqueda
                        <strong>“<?php echo $_SESSION['busqueda_sucursal']; ?>”</strong>
                    </p>
                </div>

                <div class="col-12">
                    <p class="text-center mt-3">
                        <button type="submit" class="btn btn-raised btn-danger">
                            <i class="far fa-trash-alt"></i> &nbsp; ELIMINAR BÚSQUEDA
                        </button>
                    </p>
                </div>
            </div>
        </form>
    </div>

    <!-- RESULTADOS -->
    <div class="container-fluid">
        <?php
        require_once "./controladores/sucursalControlador.php";
        $ins = new sucursalControlador();

        echo $ins->paginador_sucursales_controlador(
            $pagina[1],
            15,
            $_SESSION['nivel_str'],
            $pagina[0],
            $_SESSION['busqueda_sucursal']
        );
        ?>
    </div>

<?php } ?>