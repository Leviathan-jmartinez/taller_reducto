<?php
if (!mainModel::tienePermiso('usuarios.ver')) {
	echo '<div class="alert alert-danger">Acceso no autorizado</div>';
	return;
}
?>

<!-- Page header -->
<div class="full-box page-header">
	<h3 class="text-left">
		<i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE USUARIOS
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
				<a class="active" href="<?php echo SERVERURL; ?>usuario-lista/">
					<i  class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE USUARIOS
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
				<a href="<?php echo SERVERURL; ?>usuario-rol/">
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

<!-- Content -->
<div class="container-fluid">
	<?php
	require_once "./controladores/usuarioControlador.php";
	$ins_usuario = new usuarioControlador();
	$ins_usuario->paginador_usuario_controlador($pagina[1], 10, $_SESSION['id_str'], $pagina[0], "");
	?>
</div>