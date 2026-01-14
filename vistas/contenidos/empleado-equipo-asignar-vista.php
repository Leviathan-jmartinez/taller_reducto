<?php

if (!mainModel::tienePermisoVista('empleado.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-users-cog fa-fw"></i> &nbsp; EQUIPOS DE TRABAJO
    </h3>
</div>
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>empleado-equipo/">
                <i class="fas fa-users-cog fa-fw"></i> &nbsp; EQUIPOS
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>empleado-equipo-asignar/">
                <i class="fas fa-user-plus fa-fw"></i> &nbsp; ASIGNAR EMPLEADOS
            </a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <div class="form-neon">
        <?php
        require_once "./controladores/equipoControlador.php";
        $eq = new equipoControlador();

        $equipos = $eq->listar_equipos_controlador();
        ?>

        <form class="FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/equipoAjax.php"
            method="POST"
            data-form="save">

            <label>Equipo</label>
            <select name="id_equipo" class="form-control">
                <?php foreach ($equipos as $e): ?>
                    <option value="<?= $e['id_equipo']; ?>">
                        <?= $e['nombre']; ?> (<?= $e['suc_descri']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <hr>

            <h5>Empleados</h5>
            <?php

            $empleados = $eq->empleados_con_equipo_controlador($_SESSION['nick_sucursal']);
            ?>

            <?php foreach ($empleados as $emp): ?>
                <div class="form-check mb-1">
                    <input class="form-check-input"
                        type="checkbox"
                        name="empleados[]"
                        value="<?= $emp['idempleados']; ?>">

                    <label class="form-check-label">
                        <?= $emp['apellido'] . ' ' . $emp['nombre']; ?>

                        <?php if (!empty($emp['equipos'])): ?>
                            <span class="badge badge-info ml-2">
                                <?= $emp['equipos']; ?>
                            </span>
                        <?php else: ?>
                            <span class="badge badge-secondary ml-2">
                                Sin equipo
                            </span>
                        <?php endif; ?>
                    </label>
                </div>
            <?php endforeach; ?>



            <button class="btn btn-info mt-3">ASIGNAR</button>
        </form>
    </div>
</div>