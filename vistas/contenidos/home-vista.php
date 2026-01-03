<div class="full-box page-header">
	<h3 class="text-left">
		<i class="fab fa-dashcube fa-fw"></i> &nbsp; Panel Principal
	</h3>
</div>

<!-- Content -->
<div class="full-box tile-container">
	<?php require_once "./controladores/clienteControlador.php";
	$ins_cliente = new clienteControlador;
	$total_client = $ins_cliente->datos_cliente_controlador("Conteo", 0);
	?>
	<a href="<?php echo SERVERURL; ?>cliente-lista/" class="tile">
		<div class="tile-tittle">Clientes</div>
		<div class="tile-icon">
			<i class="fas fa-users fa-fw"></i>
			<p><?php echo $total_client->rowCount(); ?> Registrados</p>
		</div>
	</a>
	<?php require_once "./controladores/articuloControlador.php";
	$ins_arti = new articuloControlador;
	$total_arti = $ins_arti->datos_articulos_controlador("Conteo", 0);
	?>
	<a href="<?php echo SERVERURL; ?>articulo-lista/" class="tile">
		<div class="tile-tittle">Articulos</div>
		<div class="tile-icon">
			<i class="fas fa-pallet fa-fw"></i>
			<p><?php echo $total_arti->rowCount(); ?> Registrados</p>
		</div>
	</a>
	<?php require_once "./controladores/pedidoControlador.php";
	$ins_pedi = new pedidoControlador;
	$total_pedi = $ins_pedi->datos_pedido_controlador("conteoActivos", 0);
	?>
	<a href="<?php echo SERVERURL; ?>pedido-lista/" class="tile">
		<div class="tile-tittle">Pedidos de Compra</div>
		<div class="tile-icon">
			<i class="fas fa-shopping-cart"></i>
			<p><?php echo $total_pedi->rowCount(); ?> Registradas</p>
		</div>
	</a>
	<?php
	require_once "./controladores/presupuestoControlador.php";

	$ins_pres = new presupuestoControlador;

	// Ejecuta la consulta
	$total_pre = $ins_pres->datos_presupuesto_controlador("conteoActivos");

	// Obtener el valor del COUNT(*)
	$total = ($total_pre) ? $total_pre->fetch(PDO::FETCH_ASSOC)['total'] : 0;
	?>

	<a href="<?php echo SERVERURL; ?>presupuesto-lista/" class="tile">
		<div class="tile-tittle">Presupuestos Compra</div>
		<div class="tile-icon">
			<i class="fas fa-hand-holding-usd fa-fw"></i>
			<p><?php echo $total; ?> Registrados</p>
		</div>
	</a>


	<a href="<?php echo SERVERURL; ?>oc-nuevo/" class="tile">
		<div class="tile-tittle">Ordenes de Compra</div>
		<div class="tile-icon">
			<i class="fas fa-clipboard-list fa-fw"></i>
			<p>700 Registrados</p>
		</div>
	</a>
	<?php
	if ($_SESSION['nivel_str'] == 1) {
		require_once "./controladores/usuarioControlador.php";
		$insHome = new usuarioControlador();
		$total_user = $insHome->datos_usuario_controlador("Conteo", 0)
	?>
		<a href="<?php echo SERVERURL; ?>usuario-lista/" class="tile">
			<div class="tile-tittle">Usuarios</div>
			<div class="tile-icon">
				<i class="fas fa-user-secret fa-fw"></i>
				<p><?php echo $total_user->rowCount(); ?> Registrados</p>
			</div>
		</a>
	<?php } ?>
	<a href="<?php echo SERVERURL; ?>company/" class="tile">
		<div class="tile-tittle">Empresa</div>
		<div class="tile-icon">
			<i class="fas fa-store-alt fa-fw"></i>
			<p>1 Registrada</p>
		</div>
	</a>
</div>