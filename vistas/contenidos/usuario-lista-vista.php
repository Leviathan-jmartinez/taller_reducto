<?php if ($_SESSION['nivel_str'] != 1) {
	echo $lc->forzarCierre_sesion_controlador();
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
		<li>
			<a href="<?php echo SERVERURL; ?>usuario-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO USUARIO</a>
		</li>
		<li>
			<a class="active" href="<?php echo SERVERURL; ?>usuario-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE USUARIOS</a>
		</li>
		<li>
			<a href="<?php echo SERVERURL; ?>usuario-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR USUARIO</a>
		</li>
	</ul>
</div>

<!-- Content -->
<div class="container-fluid">
	<?php
	require_once "./controladores/usuarioControlador.php";
	$ins_usuario = new usuarioControlador();
	$ins_usuario->paginador_usuario_controlador($pagina[1], 10, $_SESSION['nivel_str'], $_SESSION['id_str'], $pagina[0], "");
	?>
</div>