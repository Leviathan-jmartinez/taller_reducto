<?php
if (!mainModel::tienePermisoVista('compra.presupuesto.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
} ?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; LISTADO DE PRESUPUESTOS DE COMPRA
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>presupuesto-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; CARGAR PRESUPUESTO</a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>presupuesto-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTADOS DE PRESUPUESTOS</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>presupuesto-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA</a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <?php
    require_once "./controladores/presupuestoControlador.php";
    $ins_presupuesto = new presupuestoControlador();
    echo $ins_presupuesto->paginador_presupuestos_controlador($pagina[1], 15, $_SESSION['nivel_str'], $pagina[0], "", "");
    ?>
</div>