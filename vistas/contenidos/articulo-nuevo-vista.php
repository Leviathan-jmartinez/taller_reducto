            <!-- Page header -->
            <div class="full-box page-header">
                <h3 class="text-left">
                    <i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ARTICULO
                </h3>
                <p class="text-justify">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eaque laudantium necessitatibus eius iure adipisci modi distinctio. Earum repellat iste et aut, ullam, animi similique sed soluta tempore cum quis corporis!
                </p>
            </div>

            <div class="container-fluid">
                <ul class="full-box list-unstyled page-nav-tabs">
                    <li>
                        <a class="active" href="<?php echo SERVERURL; ?>articulo-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ARTICULO</a>
                    </li>
                    <li>
                        <a href="<?php echo SERVERURL; ?>articulo-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE ARTICULOS</a>
                    </li>
                    <li>
                        <a href="<?php echo SERVERURL; ?>articulo-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR ARTICULO</a>
                    </li>
                </ul>
            </div>

            <!--CONTENT-->
            <div class="container-fluid">
                <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/articuloAjax.php" method="POST" data-form="save" autocomplete="off">
                    <fieldset>
                        <legend><i class="far fa-plus-square"></i> &nbsp; Información del item</legend>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="item_codigo" class="bmd-label-floating">Códido</label>
                                        <input type="text" pattern="[a-zA-Z0-9-]{1,45}" class="form-control" name="articulo_codigo_reg" id="articulo_codigo" maxlength="45">
                                    </div>
                                </div>

                                <div class="col-12 col-md-8">
                                    <div class="form-group">
                                        <label for="articulo_nombre" class="bmd-label-floating">Descripción</label>
                                        <input type="text" pattern="[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}" class="form-control" name="articulo_nombre_reg" id="articulo_nombre" maxlength="140">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="articulo_stock" class="bmd-label-floating">Precio Compra</label>
                                        <input type="num" pattern="[0-9]{1,9}" class="form-control" name="articulo_priceC_reg" id="articulo_priceC" maxlength="9">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="articulo_stock" class="bmd-label-floating">Precio Venta</label>
                                        <input type="num" pattern="[0-9]{1,9}" class="form-control" name="articulo_priceV_reg" id="articulo_priceV" maxlength="9">
                                    </div>
                                </div>
                                <?php
                                require_once "./controladores/articuloControlador.php";
                                $articleController = new articuloControlador();
                                $articlesIVA = $articleController->listar_iva_controlador();
                                $articlesPro = $articleController->listar_proveedores_controlador();
                                $articlesUM = $articleController->listar_UM_controlador();
                                $articlesCAT = $articleController->listar_cate_controlador();
                                $articlesMAR = $articleController->listar_marca_controlador();
                                ?>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="tipo_inv" class="bmd-label-floating">Tipo Impuesto</label>
                                        <select class="form-control" name="tipo_inv_reg" id="tipo_inv_reg">
                                            <?php echo $articlesIVA; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="proveedor" class="bmd-label-floating">Proveedor</label>
                                        <select class="form-control" name="proveedor_reg" id="proveedor_reg">
                                            <?php echo $articlesPro; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="um" class="bmd-label-floating">Unidad de Medida</label>
                                        <select class="form-control" name="um_reg" id="um_reg">
                                            <?php echo $articlesUM; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="categoria" class="bmd-label-floating">Categoria</label>
                                        <select class="form-control" name="categoria_reg" id="categoria_reg">
                                            <?php echo $articlesCAT; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="marca" class="bmd-label-floating">Marcas</label>
                                        <select class="form-control" name="marca_reg" id="marca_reg">
                                            <?php echo $articlesMAR; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <br><br><br>
                    <p class="text-center" style="margin-top: 40px;">
                        <button type="reset" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR</button>
                        &nbsp; &nbsp;
                        <button type="submit" class="btn btn-raised btn-info btn-sm"><i class="far fa-save"></i> &nbsp; GUARDAR</button>
                    </p>
                </form>
            </div>