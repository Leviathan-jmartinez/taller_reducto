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
            <a class="active" href="<?php echo SERVERURL; ?>empleado-equipo/">
                <i class="fas fa-users-cog fa-fw"></i> &nbsp; EQUIPOS
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>empleado-equipo-asignar/">
                <i class="fas fa-user-plus fa-fw"></i> &nbsp; ASIGNAR EMPLEADOS
            </a>
        </li>
    </ul>
</div>

<!-- ================== CREAR EQUIPO ================== -->
<div class="container-fluid">
    <?php
    require_once "./controladores/equipoControlador.php";
    $ins_equipo = new equipoControlador();
    ?>

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/equipoAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="crear_equipo">

        <fieldset>
            <legend><i class="fas fa-plus"></i> &nbsp; Crear nuevo equipo</legend>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Sucursal</label>
                        <select class="form-control" name="sucursal" required>
                            <option value="">Seleccione</option>
                            <?php
                            require_once "./controladores/empleadoControlador.php";
                            $empCtrl = new empleadoControlador();
                            $sucursales = $empCtrl->listar_sucursales_controlador();
                            foreach ($sucursales as $s) {
                                echo "<option value='{$s['id_sucursal']}'>{$s['suc_descri']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nombre del equipo</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" class="form-control" name="descripcion">
                    </div>
                </div>
            </div>
        </fieldset>

        <p class="text-center mt-3">
            <button type="submit" class="btn btn-raised btn-info">
                <i class="fas fa-save"></i> &nbsp; GUARDAR
            </button>
        </p>
    </form>
</div>

<!-- ================== LISTA EQUIPOS ================== -->
<div class="container-fluid mt-4">
    <?php
    $equipos = $ins_equipo->listar_equipos_controlador();
    ?>

    <div class="table-responsive">
        <table class="table table-dark table-sm">
            <thead>
                <tr class="text-center">
                    <th>#</th>
                    <th>Equipo</th>
                    <th>Sucursal</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $cont = 1;
                foreach ($equipos as $eq):
                ?>
                    <tr class="text-center">
                        <td><?= $cont++; ?></td>
                        <td><?= $eq['nombre']; ?></td>
                        <td><?= $eq['suc_descri']; ?></td>
                        <td>
                            <?= ($eq['estado'] == 1)
                                ? '<span class="badge badge-success">Activo</span>'
                                : '<span class="badge badge-danger">Inactivo</span>'; ?>
                        </td>
                        <td>
                            <a href="<?php echo SERVERURL; ?>empleado-equipo-miembros/<?php echo $lc->encryption($eq['id_equipo']); ?>/"
                                class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if (mainModel::tienePermisoVista('empleado.eliminar')): ?>
                                <form class="FormularioAjax d-inline"
                                    action="<?php echo SERVERURL; ?>ajax/equipoAjax.php"
                                    method="POST"
                                    data-form="delete"
                                    autocomplete="off">

                                    <input type="hidden" name="accion" value="eliminar_equipo">
                                    <input type="hidden" name="equipo_id_del" value="<?php echo $lc->encryption($eq['id_equipo']); ?>">

                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>