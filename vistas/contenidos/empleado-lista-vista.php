<?php

if (!mainModel::tienePermisoVista('empleado.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-users fa-fw"></i> &nbsp; LISTA DE EMPLEADOS
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>empleado-nuevo/">
                <i class="fas fa-user-plus fa-fw"></i> &nbsp; AGREGAR EMPLEADO
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>empleado-lista/">
                <i class="fas fa-users fa-fw"></i> &nbsp; LISTA DE EMPLEADOS
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>empleado-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR EMPLEADO
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>empleado-equipo-asignar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; ASIGNAR EMPLEADO A EQUIPO
            </a>
        </li>
    </ul>
</div>
<div class="container-fluid">
    <?php
    require_once "./controladores/empleadoControlador.php";
    $ins = new empleadoControlador();

    echo $ins->paginador_empleados_controlador(
        $pagina[1] ?? 1,
        10,
        $_SESSION['nivel_str'],
        "empleado-lista",
        ""
    );
    ?>
</div>