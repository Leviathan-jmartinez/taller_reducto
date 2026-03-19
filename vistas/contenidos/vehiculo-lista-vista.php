<?php
if (!mainModel::tienePermisoVista('vehiculo.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE VEHÍCULOS
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>vehiculo-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR VEHÍCULO
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>vehiculo-lista/">
                <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE VEHÍCULOS
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>vehiculo-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR VEHÍCULO
            </a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <?php
    require_once "./controladores/vehiculoControlador.php";
    $ins_vehiculo = new vehiculoControlador();
    echo $ins_vehiculo->paginador_vehiculos_controlador($pagina[1], 10, $_SESSION['nivel_str'], $pagina[0], "");
    ?>
</div>