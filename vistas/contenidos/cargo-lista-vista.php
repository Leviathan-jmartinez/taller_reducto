<?php
if (!mainModel::tienePermiso('cargo.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

if ($peticionAjax) {
    require_once "../controladores/cargosControlador.php";
} else {
    require_once "./controladores/cargosControlador.php";
}
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE CARGOS
    </h3>
    <p class="text-justify"></p>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>cargo-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR CARGO
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>cargo-lista/">
                <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE CARGOS
            </a>
        </li>
    </ul>
</div>


<!-- CONTENT -->
<div class="container-fluid">
    <?php
    require_once "./controladores/cargosControlador.php";
    $ins_cargo = new cargosControlador();
    $busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : "";
    echo $ins_cargo->paginador_cargos_controlador($pagina[1], 10, $pagina[0], $busqueda);
    ?>
</div>