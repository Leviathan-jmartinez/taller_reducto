<?php
if (!mainModel::tienePermisoVista('compra.articulo.buscar')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}   
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR ARTICULO
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>articulo-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ARTICULO</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>articulo-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE ARTICULOS</a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>articulo-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR ARTICULO</a>
        </li>
    </ul>
</div>
<?php if (!isset($_SESSION['busqueda_articulo']) && empty($_SESSION['busqueda_articulo'])) { ?>
    <!--CONTENT-->
    <div class="container-fluid">
        <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="default" autocomplete="off">
            <input type="hidden" name="modulo" value="articulo">
            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="inputSearch" class="bmd-label-floating">¿Qué articulo estas buscando?</label>
                            <input type="text" class="form-control" name="busqueda_inicial" id="inputSearch" maxlength="30">
                        </div>
                    </div>
                    <div class="col-12">
                        <p class="text-center" style="margin-top: 40px;">
                            <button type="submit" class="btn btn-raised btn-info"><i class="fas fa-search"></i> &nbsp; BUSCAR</button>
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php } else { ?>
    <div class="container-fluid">
        <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="search" autocomplete="off">
            <input type="hidden" name="modulo" value="articulo">
            <input type="hidden" name="eliminar_busqueda" value="eliminar">
            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-6">
                        <p class="text-center" style="font-size: 20px;">
                            Resultados de la busqueda <strong>“<?php echo $_SESSION['busqueda_articulo'] ?>”</strong>
                        </p>
                    </div>
                    <div class="col-12">
                        <p class="text-center" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-raised btn-danger"><i class="far fa-trash-alt"></i> &nbsp; ELIMINAR BÚSQUEDA</button>
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="container-fluid">
        <?php
        require_once "./controladores/articuloControlador.php";
        $ins_articulo = new articuloControlador();
        echo $ins_articulo->paginador_articulos_controlador($pagina[1], 15, $_SESSION['nivel_str'], $pagina[0], $_SESSION['busqueda_articulo']);
        ?>
    </div>
<?php
}
?>