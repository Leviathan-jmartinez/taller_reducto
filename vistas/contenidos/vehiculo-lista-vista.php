<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE VEHÍCULOS
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li><a href="<?php echo SERVERURL; ?>vehiculo-nuevo/">AGREGAR</a></li>
        <li><a class="active" href="<?php echo SERVERURL; ?>vehiculo-lista/">LISTA</a></li>
        <li><a href="<?php echo SERVERURL; ?>vehiculo-buscar/">BUSCAR</a></li>
    </ul>
</div>

<div class="container-fluid">
    <?php
    require_once "./controladores/vehiculoControlador.php";
    $ins = new vehiculoControlador();

    $paginaActual = explode("/", $_GET['views'] ?? "");
    $paginaActual = isset($paginaActual[1]) ? $paginaActual[1] : 1;

    echo $ins->paginador_vehiculos_controlador(
        $paginaActual,
        10,
        $_SESSION['nivel_str'],
        "vehiculo-lista",
        ""
    );
    ?>
</div>