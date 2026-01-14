<?php

if (!mainModel::tienePermisoVista('empleado.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-users fa-fw"></i> &nbsp; MIEMBROS DEL EQUIPO
    </h3>
</div>

<div class="container-fluid">
    <a href="<?php echo SERVERURL; ?>empleado-equipo/"
        class="btn btn-secondary btn-sm mb-3">
        <i class="fas fa-arrow-left"></i> &nbsp; VOLVER
    </a>
</div>

<div class="container-fluid">
    <?php
    require_once "./controladores/equipoControlador.php";
    $ins_equipo = new equipoControlador();

    $miembros = $ins_equipo->miembros_equipo_controlador($pagina[1]);

    ?>

    <div class="table-responsive">
        <table class="table table-dark table-sm">
            <thead>
                <tr class="text-center">
                    <th>#</th>
                    <th>Empleado</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $cont = 1;
                if (count($miembros) > 0):
                    foreach ($miembros as $m):
                ?>
                        <tr class="text-center">
                            <td><?= $cont++; ?></td>
                            <td><?= $m['apellido'] . ' ' . $m['nombre']; ?></td>
                            <td><?= $m['rol'] ?? 'Miembro'; ?></td>
                            <td>
                                <?= ($m['estado'] == 1)
                                    ? '<span class="badge badge-success">Activo</span>'
                                    : '<span class="badge badge-danger">Inactivo</span>'; ?>
                            </td>
                            <td>
                                <?php if (mainModel::tienePermisoVista('empleado.eliminar')): ?>
                                    <form class="FormularioAjax d-inline"
                                        action="<?php echo SERVERURL; ?>ajax/equipoAjax.php"
                                        method="POST"
                                        data-form="delete"
                                        autocomplete="off">

                                        <input type="hidden" name="accion" value="quitar_miembro">
                                        <input type="hidden" name="equipo_id" value="<?php echo $pagina[1]; ?>">
                                        <input type="hidden" name="empleado_id" value="<?php echo $lc->encryption($m['idempleados']); ?>">

                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr class="text-center">
                        <td colspan="4">No hay empleados asignados</td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>