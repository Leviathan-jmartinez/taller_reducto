<?php
if (!mainModel::tienePermisoVista('articulo.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}   
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE ARTICULOS
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>articulo-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ARTICULO</a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>articulo-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE ARTICULOS</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>articulo-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR ARTICULO</a>
        </li>
    </ul>
</div>

<!--CONTENT-->
<div class="container-fluid">
    <?php
    require_once "./controladores/articuloControlador.php";
    $ins_articulo = new articuloControlador();
    echo $ins_articulo->paginador_articulos_controlador($pagina[1], 15, $_SESSION['nivel_str'], $pagina[0], "");
    ?>
</div>