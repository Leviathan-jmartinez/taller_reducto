<?php
if (!mainModel::tienePermiso('permisos.asignar_permisos')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/rolesControlador.php";
$insRoles = new rolesControlador();
$roles = $insRoles->listar_rolesSelect_controlador();
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; PERMISOS
    </h3>
    <p class="text-justify">

    </p>
</div>

<div class="container-fluid form-neon app-view">


    <ul class="full-box list-unstyled page-nav-tabs">

        <?php if (mainModel::tienePermiso('roles.ver')) { ?>
            <li>
                <a href="<?php echo SERVERURL; ?>rol-nuevo/">
                    <i class="fas fa-key fa-fw"></i> &nbsp; Roles
                </a>
            </li>
        <?php } ?>

        <?php if (mainModel::tienePermiso('permisos.asignar_permisos')) { ?>
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>rol-permisos/">
                    <i class="fas fa-key fa-fw"></i> &nbsp; Asignar permisos
                </a>
            </li>
        <?php } ?>

    </ul>

    <div class="form-neon app-view">
        <form class="FormularioAjax"
            action="<?= SERVERURL ?>ajax/rolesAjax.php"
            method="POST"
            data-form="update">

            <input type="hidden" name="accion" value="guardar_permisos_rol">

            <div class="form-group">
                <label>Rol</label>
                <select name="id_rol" id="id_rol" class="form-control" required>
                    <option value="">Seleccione rol</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id_rol'] ?>">
                            <?= $r['nombre'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="contenedor_permisos" class="mt-3">
                <div class="alert alert-info">
                    Seleccione un rol para ver los permisos
                </div>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-primary">
                    Guardar permisos
                </button>
            </div>

        </form>
    </div>
</div>
<?php include_once "./vistas/inc/usuarioPermisosJS.php"; ?>