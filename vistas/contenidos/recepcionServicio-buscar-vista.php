			<div class="container-fluid">
				<h3 class="text-left">
					<i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR RECEPCIONES
				</h3>
				<ul class="full-box list-unstyled page-nav-tabs">
					<li>
						<a href="<?php echo SERVERURL; ?>recepcionServicio-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVA RECEPCION</a>
					</li>
					<li>
						<a class="active" href="<?php echo SERVERURL; ?>recepcionServicio-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR RECEPCION</a>
					</li>
				</ul>
			</div>
			<?php if (!isset($_SESSION['busqueda_recepcion']) && empty($_SESSION['busqueda_recepcion'])) { ?>
				<!-- Content here-->
				<div class="container-fluid">
					<form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="default" autocomplete="off">
						<input type="hidden" name="modulo" value="recepcion">
						<div class="container-fluid">
							<div class="row justify-content-md-center">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for="inputSearch" class="bmd-label-floating">¿Qué recepcion estas buscando?</label>
										<input type="text" class="form-control" name="busqueda_inicial" id="inputSearch" maxlength="30">
									</div>
								</div>
								<div class="col-12">
									<p class="text-center" style="margin-top: 40px;">
										<button type="submit" class="btn btn-raised btn-info"><i class="fas fa-search"></i> &nbsp; BUSCAR</button>
									</p>
								</div>
							</div>
						</div>
					</form>
				</div>
			<?php } else { ?>
				<div class="container-fluid">
					<form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="search" autocomplete="off">
						<input type="hidden" name="modulo" value="recepcion">
						<input type="hidden" name="eliminar_busqueda" value="eliminar">
						<div class="container-fluid">
							<div class="row justify-content-md-center">
								<div class="col-12 col-md-6">
									<p class="text-center" style="font-size: 20px;">
										Resultados de la busqueda <strong>“<?php echo $_SESSION['busqueda_recepcion'] ?>”</strong>
									</p>
								</div>
								<div class="col-12">
									<p class="text-center" style="margin-top: 20px;">
										<button type="submit" class="btn btn-raised btn-danger"><i class="far fa-trash-alt"></i> &nbsp; ELIMINAR BÚSQUEDA</button>
									</p>
								</div>
							</div>
						</div>
					</form>
				</div>

				<div class="container-fluid">
					<?php
					require_once "./controladores/recepcionservicioControlador.php";
					$ins_recepcion = new recepcionservicioControlador();
					echo $ins_recepcion->paginador_recepcion_servicio_controlador($pagina[1], 15, $_SESSION['nivel_str'], $pagina[0], $_SESSION['busqueda_recepcion']);
					?>
				</div>
			<?php
			}
			?>