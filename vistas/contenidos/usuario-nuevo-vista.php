<?php
$pagina = require __DIR__ . '/../inc/pagina.php';

$vistaPartes = explode('/', trim($_GET['vista'] ?? '', '/'));
$vistaActual = $vistaPartes[0] ?? 'usuario-nuevo';
$idUsuario = ($vistaActual === 'usuario-actualizar') ? ($vistaPartes[1] ?? null) : null;
$editando = false;
$campos = [];
$cuentaPropia = false;
$puedeCrear = mainModel::tienePermiso('usuarios.crear');

if ($vistaActual === 'usuario-actualizar' && $idUsuario === null) {
	echo '<div class="alert alert-danger">No se pudo cargar el usuario seleccionado</div>';
	return;
}

if ($vistaActual !== 'usuario-actualizar' && !mainModel::tienePermiso('usuarios.ver')) {
	echo '<div class="alert alert-danger">Acceso no autorizado</div>';
	return;
}

require_once "./controladores/usuarioControlador.php";
$ins = new usuarioControlador();

if ($idUsuario !== null) {
	$datos_usuario = $ins->datos_usuario_controlador("Unico", $idUsuario);
	if ($datos_usuario->rowCount() == 1) {
		$campos = $datos_usuario->fetch();
		$editando = true;
		$cuentaPropia = ($lc->encryption($_SESSION['id_str']) == $idUsuario);
		if (!$cuentaPropia && !mainModel::tienePermiso('usuarios.editar')) {
			echo '<div class="alert alert-danger">Acceso no autorizado</div>';
			return;
		}
	} else {
		echo '<div class="alert alert-danger">No se pudo cargar el usuario seleccionado</div>';
		return;
	}
}

$busqueda = $_SESSION['busqueda_usuario'] ?? "";
?>

<!-- Page header -->
<div class="full-box page-header">
	<h3 class="text-left">
		<i class="fas <?php echo $editando ? 'fa-sync-alt' : 'fa-clipboard-list'; ?> fa-fw"></i> &nbsp;
		<?php echo $editando ? 'ACTUALIZAR USUARIO' : ($puedeCrear ? 'USUARIOS' : 'LISTADO DE USUARIOS'); ?>
	</h3>
	<p class="text-justify">

	</p>
</div>

<!-- Content -->
<?php if ($editando || $puedeCrear): ?>
<div class="container-fluid">
	<form class="form-neon FormularioAjax app-form" action="<?php echo SERVERURL; ?>ajax/usuarioAjax.php" method="POST" data-form="<?php echo $editando ? 'update' : 'save'; ?>" autocomplete="off">
		<?php if ($editando): ?>
			<input type="hidden" name="usuario_id_up" value="<?php echo $idUsuario; ?>">
		<?php endif; ?>
		<fieldset>
			<legend><i class="far fa-address-card"></i> &nbsp; Información personal</legend>
			<div class="container-fluid">
				<div class="row">
					<div class="col-12 col-md-4">
						<div class="form-group">
							<label for="usuario_dni" class="bmd-label-floating">C.I. *</label>
							<input type="text" pattern="[0-9]{5,10}" class="form-control" name="<?php echo $editando ? 'usuario_ci_up' : 'usuario_dni_reg'; ?>" id="usuario_dni" maxlength="10" inputmode="numeric" value="<?php echo $editando ? htmlspecialchars($campos['usu_ci'], ENT_QUOTES, 'UTF-8') : ''; ?>" <?php echo $cuentaPropia ? 'readonly' : ''; ?>>
						</div>
					</div>

					<div class="col-12 col-md-4">
						<div class="form-group">
							<label for="usuario_nombre" class="bmd-label-floating">Nombres *</label>
							<input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{2,50}" class="form-control" name="<?php echo $editando ? 'usuario_nombre_up' : 'usuario_nombre_reg'; ?>" id="usuario_nombre" maxlength="50" value="<?php echo $editando ? htmlspecialchars($campos['usu_nombre'], ENT_QUOTES, 'UTF-8') : ''; ?>" <?php echo $cuentaPropia ? 'readonly' : ''; ?>>
						</div>
					</div>
					<div class="col-12 col-md-4">
						<div class="form-group">
							<label for="usuario_apellido" class="bmd-label-floating">Apellidos *</label>
							<input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{2,50}" class="form-control" name="<?php echo $editando ? 'usuario_apellido_up' : 'usuario_apellido_reg'; ?>" id="usuario_apellido" maxlength="50" value="<?php echo $editando ? htmlspecialchars($campos['usu_apellido'], ENT_QUOTES, 'UTF-8') : ''; ?>" <?php echo $cuentaPropia ? 'readonly' : ''; ?>>
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label for="usuario_telefono" class="bmd-label-floating">Teléfono</label>
							<input type="text" pattern="[0-9()+ -]{6,50}" class="form-control" name="<?php echo $editando ? 'usuario_telefono_up' : 'usuario_telefono_reg'; ?>" id="usuario_telefono" maxlength="50" inputmode="tel" value="<?php echo $editando ? htmlspecialchars($campos['usu_telefono'], ENT_QUOTES, 'UTF-8') : ''; ?>">
						</div>
					</div>
				</div>
			</div>
		</fieldset>
		<br>
		<fieldset>
			<legend><i class="fas fa-user-lock"></i> &nbsp; Información de la cuenta</legend>
			<div class="container-fluid">
				<div class="row">
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label for="usuario_usuario" class="bmd-label-floating">Nombre de usuario *</label>
							<input type="text" pattern="[a-zA-Z0-9]{3,20}" class="form-control" name="<?php echo $editando ? 'usuario_usuario_up' : 'usuario_usuario_reg'; ?>" id="usuario_usuario" maxlength="20" value="<?php echo $editando ? htmlspecialchars($campos['usu_nick'], ENT_QUOTES, 'UTF-8') : ''; ?>" <?php echo $cuentaPropia ? 'readonly' : ''; ?>>
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label for="usuario_email" class="bmd-label-floating">Email</label>
							<input type="email" class="form-control" name="<?php echo $editando ? 'usuario_email_up' : 'usuario_email_reg'; ?>" id="usuario_email" maxlength="50" value="<?php echo $editando ? htmlspecialchars($campos['usu_email'], ENT_QUOTES, 'UTF-8') : ''; ?>">
						</div>
					</div>
                    <?php if ($editando): ?>
                        <div class="col-12">
                            <div class="form-group">
                                <span>Estado de la cuenta &nbsp;
                                    <?php echo ((int)$campos['usu_estado'] === 1) ? '<span class="badge badge-success">Activa</span>' : '<span class="badge badge-danger">Inactiva</span>'; ?>
                                </span>
                                <?php if ($cuentaPropia): ?>
                                    <input type="hidden" name="usuario_estado_up" value="<?php echo (int)$campos['usu_estado']; ?>">
                                <?php else: ?>
                                    <label for="usuario_estado">Cambiar estado *</label>
                                    <select class="form-control" id="usuario_estado" name="usuario_estado_up">
                                        <option value="1" <?php echo ((int)$campos['usu_estado'] === 1) ? 'selected' : ''; ?>>Activa</option>
                                        <option value="0" <?php echo ((int)$campos['usu_estado'] === 0) ? 'selected' : ''; ?>>Inactiva</option>
                                    </select>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <?php
                                $intentosFallidos = isset($campos['usu_intentos_fallidos']) ? (int)$campos['usu_intentos_fallidos'] : 0;
                                $bloqueado = isset($campos['usu_bloqueado']) ? (int)$campos['usu_bloqueado'] : 0;
                                ?>
                                <span>Intentos fallidos &nbsp;
                                    <span class="badge badge-<?php echo ($intentosFallidos >= 3 ? 'danger' : ($intentosFallidos > 0 ? 'warning' : 'success')); ?>">
                                        <?php echo $intentosFallidos; ?>/3
                                    </span>
                                </span>
                                <br>
                                <span>Bloqueo por intentos &nbsp;
                                    <?php echo ($bloqueado === 1) ? '<span class="badge badge-danger">Bloqueada</span>' : '<span class="badge badge-success">Libre</span>'; ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label for="usuario_clave_1" class="bmd-label-floating"><?php echo $editando ? 'Nueva contraseña' : 'Contraseña *'; ?></label>
							<input type="password" class="form-control" name="<?php echo $editando ? 'usuario_clave_nueva_1' : 'usuario_clave_1_reg'; ?>" id="usuario_clave_1" pattern="[a-zA-Z0-9$@._-]{7,18}" maxlength="18">
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label for="usuario_clave_2" class="bmd-label-floating"><?php echo $editando ? 'Repetir contraseña' : 'Repetir contraseña *'; ?></label>
							<input type="password" class="form-control" name="<?php echo $editando ? 'usuario_clave_nueva_2' : 'usuario_clave_2_reg'; ?>" id="usuario_clave_2" pattern="[a-zA-Z0-9$@._-]{7,18}" maxlength="18">
						</div>
					</div>
				</div>
			</div>
		</fieldset>
		<br>
		<?php if ($editando): ?>
			<fieldset>
				<p class="text-center">Para guardar los cambios debe ingresar su nombre de usuario y contraseña.</p>
				<div class="container-fluid">
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="form-group">
								<label for="usuario_admin" class="bmd-label-floating">Nombre de usuario *</label>
								<input type="text" pattern="[a-zA-Z0-9]{3,20}" class="form-control" name="usuario_admin" id="usuario_admin" maxlength="20">
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-group">
								<label for="clave_admin" class="bmd-label-floating">Contraseña *</label>
								<input type="password" class="form-control" name="clave_admin" id="clave_admin" pattern="[a-zA-Z0-9$@._-]{7,100}" maxlength="100">
							</div>
						</div>
					</div>
				</div>
			</fieldset>
			<?php if ($cuentaPropia): ?>
				<input type="hidden" name="tipo_cuenta" value="propia">
			<?php else: ?>
				<input type="hidden" name="tipo_cuenta" value="impropia">
			<?php endif; ?>
			<br>
		<?php endif; ?>
		<p class="text-center" style="margin-top: 40px;">
			<?php if ($editando): ?>
				<a href="<?php echo SERVERURL; ?>usuario-nuevo/" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-times"></i> &nbsp; Cancelar</a>
			<?php else: ?>
				<button type="reset" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-times"></i> &nbsp; Cancelar</button>
			<?php endif; ?>
			&nbsp; &nbsp;
			<button type="submit" class="btn btn-raised <?php echo $editando ? 'btn-success' : 'btn-info'; ?> btn-sm">
				<i class="fas <?php echo $editando ? 'fa-sync-alt' : 'fa-save'; ?>"></i> &nbsp; <?php echo $editando ? 'ACTUALIZAR' : 'GUARDAR'; ?>
			</button>
		</p>
	</form>
</div>
<?php endif; ?>

<?php if (!$cuentaPropia): ?>
<!-- ================= BUSCADOR ================= -->
<div class="container-fluid mb-3">

	<form class="form-neon FormularioAjax app-form"
		action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
		method="POST"
		data-form="search"
		autocomplete="off">

		<input type="hidden" name="modulo" value="usuario">

		<div class="row">
			<div class="col-md-6">
				<input type="text"
					class="form-control"
					name="busqueda_inicial"
					placeholder="Buscar usuario..."
					value="<?php echo $busqueda; ?>">
			</div>

			<div class="col-md-6">
				<button type="submit" class="btn btn-info">
					Buscar
				</button>

				<?php if (isset($_SESSION['busqueda_usuario'])) { ?>
					<button type="submit" name="eliminar_busqueda" value="1" class="btn btn-danger">
							Cancelar
						</button>
				<?php
} ?>

			</div>
		</div>

	</form>
</div>

<div class="modal fade" id="modalRolesUsuario" tabindex="-1">
	<div class="modal-dialog modal-md">
		<div class="modal-content">

			<div class="modal-header bg-info text-white">
				<h5 class="modal-title">
					<i class="fas fa-user-tag"></i> Asignar roles
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal">
					&times;
				</button>
			</div>

			<div class="modal-body">

				<!-- 🔥 FORMULARIO AJAX (CLAVE) -->
				<form class="FormularioAjax"
					action="<?php echo SERVERURL; ?>ajax/usuarioAjax.php"
					method="POST"
					data-form="update">

					<input type="hidden" name="accion" value="guardar_roles_usuario">
					<input type="hidden" name="id_usuario" id="input_id_usuario">

					<!-- CONTENEDOR DINÁMICO -->
					<div id="contenedor_roles_usuario">
						<div class="text-center">Cargando...</div>
					</div>

					<div class="text-center mt-3">
						<button class="btn btn-primary">
							Guardar cambios
						</button>
					</div>

				</form>

			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="modalSucursalUsuario" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header bg-primary text-white">
				<h5><i class="fas fa-store"></i> Asignar sucursal</h5>
				<button class="close text-white" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body">

				<form class="FormularioAjax"
					action="<?php echo SERVERURL; ?>ajax/usuarioAjax.php"
					method="POST"
					data-form="update">

					<input type="hidden" name="accion" value="asignar_sucursal">
					<input type="hidden" name="id_usuario" id="input_id_usuario_sucursal">

					<div id="contenedor_sucursal_usuario">
						Cargando...
					</div>

					<div class="text-center mt-3">
						<button class="btn btn-primary">Guardar</button>
					</div>

				</form>

			</div>

		</div>
	</div>
</div>

<!-- ================= LISTA ================= -->
<div class="container-fluid mt-4">
	<?php
$pag_actual = 1;

	if (isset($pagina[1]) && is_numeric($pagina[1])) {
		$pag_actual = (int)$pagina[1];
	}

	if ($pag_actual <= 0) {
		$pag_actual = 1;
	}

	echo $ins->paginador_usuario_controlador(
		$pag_actual,
		10,
		$_SESSION['id_str'],
		$pagina[0],
		$busqueda
	);
	?>
</div>


<?php
include_once "./vistas/inc/usuarioJS.php"; ?>
<?php endif; ?>
