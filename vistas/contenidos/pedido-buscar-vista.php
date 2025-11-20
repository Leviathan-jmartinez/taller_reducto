            <!-- Page header -->
            <div class="full-box page-header">
                <h3 class="text-left">
                    <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR PEDIDOS POR FECHA
                </h3>
            </div>

            <div class="container-fluid">
                <ul class="full-box list-unstyled page-nav-tabs">
                    <li>
                        <a href="<?php echo SERVERURL; ?>pedido-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PEDIDO</a>
                    </li>
                    <li>
                        <a href="<?php echo SERVERURL; ?>pedido-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTADOS DE PEDIDOS</a>
                    </li>
                    <li>
                        <a class="active" href="<?php echo SERVERURL; ?>pedido-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA</a>
                    </li>
                </ul>
            </div>
            <?php if (!isset($_SESSION['fecha_inicio_pedido']) && !isset($_SESSION['fecha_final_pedido'])) { ?>
                <div class="container-fluid">
                    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="default" autocomplete="off">
                        <input type="hidden" name="modulo" value="pedido">
                        <div class="container-fluid">
                            <div class="row justify-content-md-center">
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="busqueda_inicio_pedido">Fecha inicial (día/mes/año)</label>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" maxlength="30">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="busqueda_final_pedido">Fecha final (día/mes/año)</label>
                                        <input type="date" class="form-control" name="fecha_final" id="fecha_final" maxlength="30">
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
                        <input type="hidden" name="modulo" value="pedido">
                        <input type="hidden" name="eliminar_busqueda" value="eliminar">
                        <div class="container-fluid">
                            <div class="row justify-content-md-center">
                                <div class="col-12 col-md-6">
                                    <p class="text-center" style="font-size: 20px;">
                                        Fecha de busqueda: <strong><?php echo $_SESSION['fecha_inicio_pedido'] ?> &nbsp; a &nbsp; <?php echo $_SESSION['fecha_final_pedido'] ?></strong>
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
                    require_once "./controladores/pedidoControlador.php";
                    $ins_pedido = new pedidoControlador();
                    echo $ins_pedido->paginador_pedidos_controlador($pagina[1], 15, $_SESSION['nivel_str'], $pagina[0], $_SESSION['fecha_inicio_pedido'],$_SESSION['fecha_final_pedido']);
                    ?>
                </div>

            <?php
            }
            ?>