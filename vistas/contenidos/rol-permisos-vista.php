<?php
if (!mainModel::tienePermisoVista('usuarios.permisos_por_roles')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/usuarioControlador.php";
$insUsuario = new usuarioControlador();
$roles = $insUsuario->listar_roles_controlador();
?>

<div class="container-fluid">
    <h3>
        <i class="fas fa-key"></i>
        &nbsp; Permisos por Rol
    </h3>
</div>

<div class="container-fluid">
    <div class="form-neon">
        <form class="FormularioAjax"
            action="<?= SERVERURL ?>ajax/usuarioAjax.php"
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