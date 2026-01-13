<?php
if (!mainModel::tienePermisoVista('proveedor.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
} 

if ($peticionAjax) {
    require_once "../controladores/proveedorControlador.php";
} else {
    require_once "./controladores/proveedorControlador.php";
}
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE PROVEEDORES
    </h3>
    <p class="text-justify"></p>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>proveedor-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR PROVEEDOR
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>proveedor-lista/">
                <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE PROVEEDORES
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>proveedor-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR PROVEEDOR
            </a>
        </li>
    </ul>
</div>

<!-- CONTENT -->
<div class="container-fluid">
    <?php
    $pagina = explode("/", $_GET['views'] ?? "");
    $pagina = isset($pagina[1]) && is_numeric($pagina[1]) ? $pagina[1] : 1;

    $registros = 10;
    $busqueda = "";

    $lc = new proveedorControlador();
    echo $lc->paginador_proveedores_controlador(
        $pagina,
        $registros,
        $_SESSION['nivel_str'],
        "proveedor-lista",
        $busqueda
    );
    ?>
</div>