<?php
if (!mainModel::tienePermiso('usuarios.asignarrol')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start(['name' => 'STR']);
}

require_once "./controladores/usuarioControlador.php";
$insUsuario = new usuarioControlador();

/* ================= VALIDAR ACCESO ================= */
if (
    !mainModel::tienePermiso('seguridad.roles.editar')
) {
    echo '
    <div class="container-fluid">
        <div class="alert alert-danger text-center">
            <strong>Acceso denegado</strong><br>
            No tiene permisos para acceder a esta sección
        </div>
    </div>';
    return;
}

/* ================= OBTENER DATOS ================= */
$usuarios = $insUsuario->listar_usuarios_controlador();

?>
<div class="full-box page-header">
	<h3 class="text-left">
		<i class="fas fa-clipboard-list fa-fw"></i> &nbsp; ASIGNAR ROL A USUARIO
	</h3>
	<p class="text-justify">

	</p>
</div>


<div class="container-fluid">

    <ul class="full-box list-unstyled page-nav-tabs">

        <?php if (mainModel::tienePermiso('usuarios.crear')) { ?>
            <li>
                <a href="<?php echo SERVERURL; ?>usuario-nuevo/">
                    <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO USUARIO
                </a>
            </li>
        <?php } ?>

        <?php if (mainModel::tienePermiso('usuarios.ver')) { ?>
            <li>
                <a href="<?php echo SERVERURL; ?>usuario-lista/">
                    <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE USUARIOS
                </a>
            </li>

            <li>
                <a href="<?php echo SERVERURL; ?>usuario-buscar/">
                    <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR USUARIO
                </a>
            </li>
        <?php } ?>

        <?php if (mainModel::tienePermiso('usuarios.asignarrol')) { ?>
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>usuario-rol/">
                    <i class="fas fa-user-tag fa-fw"></i> &nbsp; ASIGNAR ROL
                </a>
            </li>
        <?php } ?>

        <?php if (mainModel::tienePermiso('usuarios.asignarlocal')) { ?>
            <li>
                <a href="<?php echo SERVERURL; ?>usuario-sucursal/">
                    <i class="fas fa-store-alt fa-fw"></i> &nbsp; ASIGNAR SUCURSAL
                </a>
            </li>
        <?php } ?>

        <?php if (mainModel::tienePermiso('usuarios.permisos_por_roles')) { ?>
            <li>
                <a href="<?php echo SERVERURL; ?>rol-permisos/">
                    <i class="fas fa-key fa-fw"></i> &nbsp; PERMISOS POR ROL
                </a>
            </li>
        <?php } ?>

    </ul>
</div>


<div class="container-fluid">
    <div class="form-neon">
        <form class="FormularioAjax"
            action="<?= SERVERURL ?>ajax/usuarioAjax.php"
            method="POST"
            data-form="update"
            autocomplete="off">

            <input type="hidden" name="accion" value="asignar_rol">

            <div class="row">

                <!-- ================= USUARIO ================= -->
                <div class="col-md-6">
                    <label>Usuario</label>
                    <select name="id_usuario" class="form-control" required>
                        <option value="">Seleccione usuario</option>
                        <?php foreach ($usuarios as $u): ?>
                            <option value="<?= $u['id_usuario'] ?>">
                                <?= $u['usu_nombre'] ?>
                                <?= $u['usu_apellido'] ?>
                                (<?= $u['usu_nick'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ================= ROL ================= -->
                <div class="col-md-6">
                    <label>Rol</label>
                    <select name="id_rol" class="form-control" required>
                        <option value="">Seleccione rol</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id_rol'] ?>">
                                <?= $r['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <div class="text-center mt-4">
                <button class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Asignar rol
                </button>
            </div>

        </form>
    </div>
</div>