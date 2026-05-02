<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('usuarios.crear')) {
	echo '<div class="alert alert-danger">Acceso no autorizado</div>';
	return;
}

require_once "./controladores/usuarioControlador.php";
$ins = new usuarioControlador();

$busqueda = $_SESSION['busqueda_usuario'] ?? "";
?>

<!-- Page header -->
<div class="full-box page-header">
	<h3 class="text-left">
		<i class="fas fa-clipboard-list fa-fw"></i> &nbsp; USUARIOS
	</h3>
	<p class="text-justify">

	</p>
</div>

<!-- Content -->
<div class="container-fluid">
	<form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/usuarioAjax.php" method="POST" data-form="save" autocomplete="off">
		<fieldset>
			<legend><i class="far fa-address-card"></i> &nbsp; Información personal</legend>
			<div class="container-fluid">
				<div class="row">
					<div class="col-12 col-md-4">
						<div class="form-group">
							<label for="usuario_dni" class="bmd-label-floating">C.I.</label>
							<input type="text" pattern="[0-9]{7,20}" class="form-control" name="usuario_dni_reg" id="usuario_dni" maxlength="20">
						</div>
					</div>

					<div class="col-12 col-md-4">
						<div class="form-group">
							<label for="usuario_nombre" class="bmd-label-floating">Nombres</label>
							<input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}" class="form-control" name="usuario_nombre_reg" id="usuario_nombre" maxlength="35">
						</div>
					</div>
					<div class="col-12 col-md-4">
						<div class="form-group">
							<label for="usuario_apellido" class="bmd-label-floating">Apellidos</label>
							<input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}" class="form-control" name="usuario_apellido_reg" id="usuario_apellido" maxlength="35">
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label for="usuario_telefono" class="bmd-label-floating">Teléfono</label>
							<input type="text" pattern="[0-9()+]{8,20}" class="form-control" name="usuario_telefono_reg" id="usuario_telefono" maxlength="20">
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
							<label for="usuario_usuario" class="bmd-label-floating">Nombre de usuario</label>
							<input type="text" pattern="[a-zA-Z0-9]{1,35}" class="form-control" name="usuario_usuario_reg" id="usuario_usuario" maxlength="35">
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label for="usuario_email" class="bmd-label-floating">Email</label>
							<input type="email" class="form-control" name="usuario_email_reg" id="usuario_email" maxlength="70">
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label for="usuario_clave_1" class="bmd-label-floating">Contraseña</label>
							<input type="password" class="form-control" name="usuario_clave_1_reg" id="usuario_clave_1" pattern="[a-zA-Z0-9$@._-]{7,18}" maxlength="18" required="">
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label for="usuario_clave_2" class="bmd-label-floating">Repetir contraseña</label>
							<input type="password" class="form-control" name="usuario_clave_2_reg" id="usuario_clave_2" pattern="[a-zA-Z0-9$@._-]{7,18}" maxlength="18" required="">
						</div>
					</div>
				</div>
			</div>
		</fieldset>
		<br>
		<p class="text-center" style="margin-top: 40px;">
			<button type="reset" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-times"></i> &nbsp; Cancelar</button>
			&nbsp; &nbsp;
			<button type="submit" class="btn btn-raised btn-info btn-sm"><i class="far fa-save"></i> &nbsp; GUARDAR</button>
		</p>
	</form>
</div>

<!-- ================= BUSCADOR ================= -->
<div class="container-fluid mb-3">

	<form class="form-neon FormularioAjax"
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
					<form class="FormularioAjax d-inline"
						action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
						method="POST">

						<input type="hidden" name="modulo" value="usuario">
						<input type="hidden" name="eliminar_busqueda" value="1">

						<button type="submit" class="btn btn-danger">
							Cancelar
						</button>
					</form>
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
