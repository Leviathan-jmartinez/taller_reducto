<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; PRESUPUESTO DE SERVICIOS
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>presupuesto-servicio-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PRESUPUESTO</a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>presupuesto-servicio-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTADOS DE PRESUPUESTOS</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>presupuesto-servicio-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA</a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <?php
    require_once "./controladores/presupuestoservicioControlador.php";
    $ins_presupuestoservi = new presupuestoservicioControlador();
    echo $ins_presupuestoservi->paginador_presupuestoservi_controlador($pagina[1], 15, $_SESSION['nivel_str'], $pagina[0], "", "");
    ?>
</div>