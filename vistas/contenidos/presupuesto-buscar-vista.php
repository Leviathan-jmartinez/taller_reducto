            <!-- Page header -->
            <div class="full-box page-header">
                <h3 class="text-left">
                    <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; BUSCADOR DE PRESUPUESTOS DE COMPRA
                </h3>
            </div>

            <div class="container-fluid">
                <ul class="full-box list-unstyled page-nav-tabs">
                    <li>
                        <a href="<?php echo SERVERURL; ?>presupuesto-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PRESUPUESTO</a>
                    </li>
                    <li>
                        <a href="<?php echo SERVERURL; ?>presupuesto-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTADOS DE PRESUPUESTOS</a>
                    </li>
                    <li>
                        <a class="active" href="<?php echo SERVERURL; ?>presupuesto-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA</a>
                    </li>
                </ul>
            </div>
            <?php
            // Preparar datetime completos para enviar
            $fecha_inicio = $_SESSION['fecha_inicio_presupuesto'] ?? '';
            $fecha_final  = $_SESSION['fecha_final_presupuesto'] ?? '';
            $fecha_inicio_dt = $fecha_inicio ? $fecha_inicio . ' 00:00:00' : '';
            $fecha_final_dt  = $fecha_final  ? $fecha_final  . ' 23:59:59' : '';
            ?>

            <?php if (!$fecha_inicio && !$fecha_final) { ?>
                <div class="container-fluid">
                    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="default" autocomplete="off">
                        <input type="hidden" name="modulo" value="presupuesto">

                        <!-- Inputs ocultos para enviar datetime completo -->
                        <input type="hidden" name="fecha_inicio_dt" value="">
                        <input type="hidden" name="fecha_final_dt" value="">

                        <div class="container-fluid">
                            <div class="row justify-content-md-center">
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="busqueda_inicio_presupuesto">Fecha inicial (día/mes/año)</label>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" maxlength="30">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="busqueda_final_presupuesto">Fecha final (día/mes/año)</label>
                                        <input type="date" class="form-control" name="fecha_final" id="fecha_final" maxlength="30">
                                    </div>
                                </div>
                                <div class="col-12 text-center" style="margin-top: 40px;">
                                    <button type="submit" class="btn btn-raised btn-info"><i class="fas fa-search"></i> &nbsp; BUSCAR</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            <?php } else { ?>
                <div class="container-fluid">
                    <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="search" autocomplete="off">
                        <input type="hidden" name="modulo" value="presupuesto">
                        <input type="hidden" name="eliminar_busqueda" value="eliminar">

                        <!-- Inputs ocultos con datetime completo -->
                        <input type="hidden" name="fecha_inicio_dt" value="<?php echo $fecha_inicio_dt; ?>">
                        <input type="hidden" name="fecha_final_dt" value="<?php echo $fecha_final_dt; ?>">

                        <div class="container-fluid">
                            <div class="row justify-content-md-center">
                                <div class="col-12 col-md-6">
                                    <p class="text-center" style="font-size: 20px;">
                                        Fecha de búsqueda: <strong><?php echo $fecha_inicio ?> &nbsp; a &nbsp; <?php echo $fecha_final ?></strong>
                                    </p>
                                </div>
                                <div class="col-12 text-center" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-raised btn-danger"><i class="far fa-trash-alt"></i> &nbsp; ELIMINAR BÚSQUEDA</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>



                <div class="container-fluid">
                    <?php
                    require_once "./controladores/presupuestoControlador.php";
                    $ins_presupuesto = new presupuestoControlador();
                    echo $ins_presupuesto->paginador_presupuestos_controlador($pagina[1], 15, $_SESSION['nivel_str'], $pagina[0], $_SESSION['fecha_inicio_presupuesto'], $_SESSION['fecha_final_presupuesto']);
                    ?>
                </div>

            <?php
            }
            ?>