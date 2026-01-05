<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE SUCURSALES
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
            <a class="active" href="<?php echo SERVERURL; ?>sucursal-lista/">
                <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE SUCURSALES
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>sucursal-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR SUCURSAL
            </a>
        </li>
    </ul>
</div>

<!-- CONTENT -->
<div class="container-fluid">
    <?php
    require_once "./controladores/sucursalControlador.php";
    $ins = new sucursalControlador();

    /* Página actual */
    $paginaActual = explode("/", $_GET['views'] ?? "");
    $paginaActual = isset($paginaActual[1]) && is_numeric($paginaActual[1])
        ? $paginaActual[1]
        : 1;

    /* Cantidad de registros */
    $registros = 10;

    /* Sin búsqueda en lista */
    $busqueda = "";

    echo $ins->paginador_sucursales_controlador(
        $paginaActual,
        $registros,
        $_SESSION['nivel_str'],
        "sucursal-lista",
        $busqueda
    );
    ?>
</div>