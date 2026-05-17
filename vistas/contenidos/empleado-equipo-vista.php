<?php

$vistaPartes = explode('/', trim($_GET['vista'] ?? '', '/'));
$vistaActual = $vistaPartes[0] ?? 'empleado-equipo';
$idEquipo = ($vistaActual === 'empleado-equipo-actualizar') ? ($vistaPartes[1] ?? null) : null;
$editando = false;
$camposEquipo = [];

$puedeCrearEquipo = mainModel::tienePermiso('equipo.crear');
$puedeEditarEquipo = mainModel::tienePermiso('equipo.editar');
$puedeEliminarEquipo = mainModel::tienePermiso('equipo.eliminar');
$permisoNecesario = ($vistaActual === 'empleado-equipo-actualizar') ? $puedeEditarEquipo : ($puedeCrearEquipo || $puedeEditarEquipo);

if (!$permisoNecesario) {
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
        <?php if ($puedeEditarEquipo): ?>
            <li>
                <a href="<?php echo SERVERURL; ?>empleado-equipo-asignar/">
                    <i class="fas fa-user-plus fa-fw"></i> &nbsp; ASIGNAR EMPLEADOS
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>

<!-- ================== CREAR EQUIPO ================== -->
<div class="container-fluid">
    <?php
    require_once "./controladores/equipoControlador.php";
    $ins_equipo = new equipoControlador();

    if ($idEquipo !== null) {
        $camposEquipo = $ins_equipo->datos_equipo_controlador($idEquipo);
        if (!empty($camposEquipo)) {
            $editando = true;
        }
    }
    ?>

    <?php if ($editando || $puedeCrearEquipo): ?>
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/equipoAjax.php"
        method="POST"
        data-form="<?php echo $editando ? 'update' : 'save'; ?>"
        autocomplete="off">

        <input type="hidden" name="accion" value="<?php echo $editando ? 'actualizar_equipo' : 'crear_equipo'; ?>">
        <?php if ($editando): ?>
            <input type="hidden" name="equipo_id_up" value="<?php echo $idEquipo; ?>">
        <?php endif; ?>

        <fieldset>
            <legend><i class="fas <?php echo $editando ? 'fa-sync-alt' : 'fa-plus'; ?>"></i> &nbsp; <?php echo $editando ? 'Actualizar equipo' : 'Crear nuevo equipo'; ?></legend>

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
                                $selected = ($editando && (int)$camposEquipo['id_sucursal'] === (int)$s['id_sucursal']) ? 'selected' : '';
                                echo "<option value='{$s['id_sucursal']}' {$selected}>{$s['suc_descri']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nombre del equipo</label>
                        <input type="text" class="form-control" name="nombre" value="<?php echo $editando ? htmlspecialchars($camposEquipo['nombre'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" class="form-control" name="descripcion" value="<?php echo $editando ? htmlspecialchars($camposEquipo['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>
                </div>
            </div>
        </fieldset>

        <p class="text-center mt-3">
            <button type="submit" class="btn btn-raised <?php echo $editando ? 'btn-success' : 'btn-info'; ?>">
                <i class="fas <?php echo $editando ? 'fa-sync-alt' : 'fa-save'; ?>"></i> &nbsp; <?php echo $editando ? 'ACTUALIZAR' : 'GUARDAR'; ?>
            </button>
            <?php if ($editando): ?>
                <a href="<?php echo SERVERURL; ?>empleado-equipo/" class="btn btn-raised btn-secondary">CANCELAR</a>
            <?php else: ?>
                <button type="reset" class="btn btn-raised btn-secondary">CANCELAR</button>
            <?php endif; ?>
        </p>
    </form>
    <?php endif; ?>
</div>

<!-- ================== LISTA EQUIPOS ================== -->
<?php if ($puedeEditarEquipo): ?>
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
                            <?php if ($puedeEditarEquipo): ?>
                                <a href="<?php echo SERVERURL; ?>empleado-equipo-actualizar/<?php echo $lc->encryption($eq['id_equipo']); ?>/"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($puedeEliminarEquipo): ?>
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
<?php endif; ?>
