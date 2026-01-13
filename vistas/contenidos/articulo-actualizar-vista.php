<?php
if (!mainModel::tienePermisoVista('articulo.editar')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}   
?>
<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-sync-alt fa-fw"></i> &nbsp; ACTUALIZAR ARTICULO
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>articulo-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ARTICULO</a>
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
    <?php
    require_once "./controladores/articuloControlador.php";
    $ins_artilcle = new articuloControlador();

    $dat_article = $ins_artilcle->datos_articulos_controlador("Unico", $pagina[1]);
    if ($dat_article->rowCount() == 1) {
        $campos = $dat_article->fetch();
    ?>
        <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/articuloAjax.php" method="POST" data-form="update" autocomplete="off">
            <input type="hidden" name="articulo_id_up" value="<?php echo $pagina[1] ?>">
            <fieldset>
                <legend><i class="far fa-plus-square"></i> &nbsp; Información del item</legend>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="item_codigo" class="bmd-label-floating">Códido</label>
                                <input type="text" pattern="[0-9]{1,15}" class="form-control" name="articulo_codigo_up" id="articulo_codigo" maxlength="45" value="<?php echo $campos['codigo']; ?>">
                            </div>
                        </div>

                        <div class="col-12 col-md-8">
                            <div class="form-group">
                                <label for="articulo_nombre" class="bmd-label-floating">Descripción</label>
                                <input type="text" pattern="[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}" class="form-control" name="articulo_nombre_up" id="articulo_nombre" maxlength="140" value="<?php echo $campos['desc_articulo']; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="articulo_stock" class="bmd-label-floating">Precio Compra</label>
                                <input type="num" pattern="[0-9]{1,15}" class="form-control" name="articulo_priceC_up" id="articulo_priceC" maxlength="9" value="<?php echo $campos['precio_compra']; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="articulo_stock" class="bmd-label-floating">Precio Venta</label>
                                <input type="num" pattern="[0-9]{1,15}" class="form-control" name="articulo_priceV_up" id="articulo_priceV" maxlength="9" value="<?php echo $campos['precio_venta']; ?>">
                            </div>
                        </div>
                        <?php
                        require_once "./controladores/articuloControlador.php";
                        $articleController = new articuloControlador();
                        $articlesIVA = $articleController->listar_IVA_controlador();
                        $id_iva = $campos['idiva'];
                        $articlesPro = $articleController->listar_proveedores_controlador();
                        $id_proveedor = $campos['idproveedores'];
                        $articlesUM = $articleController->listar_UM_controlador();
                        $id_um = $campos['idunidad_medida'];
                        $articlesCAT = $articleController->listar_cate_controlador();
                        $id_cat = $campos['id_categoria'];
                        $articlesMAR = $articleController->listar_marca_controlador();
                        $id_marca = $campos['id_marcas'];
                        ?>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="tipo_iva_up" class="bmd-label-floating">Tipo Impuesto</label>
                                <select class="form-control" name="tipo_iva_up" id="tipo_iva_up">
                                    <option value="" disabled>Seleccione una opción</option>
                                    <?php
                                    foreach ($articlesIVA as $iva) {
                                        $selected = ($iva['idiva'] == $id_iva) ? 'selected' : '';
                                        $iva_desc = ($iva['idiva'] == $id_iva) ? $iva['tipo_impuesto_descri'] . " (Actual)" : $iva['tipo_impuesto_descri'];
                                        echo '<option value="' . $iva['idiva'] . '" ' . $selected . '>' . $iva_desc . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="proveedor_up" class="bmd-label-floating">Proveedor</label>
                                <select class="form-control" name="proveedor_up" id="proveedor_up">
                                    <option value="" disabled>Seleccione una opción</option>
                                    <?php
                                    foreach ($articlesPro as $provee) {
                                        $selected = ($provee['idproveedores'] == $id_proveedor) ? 'selected' : '';
                                        $provee_desc = ($provee['idproveedores'] == $id_proveedor) ? $provee['razon_social'] . " (Actual)" : $provee['razon_social'];
                                        echo '<option value="' . $provee['idproveedores'] . '" ' . $selected . '>' . $provee_desc . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="proveedor_up" class="bmd-label-floating">Unidad de Medida</label>
                                <select class="form-control" name="um_up" id="um_up">
                                    <option value="" disabled>Seleccione una opción</option>
                                    <?php
                                    foreach ($articlesUM as $um) {
                                        $selected = ($um['idunidad_medida'] == $id_um) ? 'selected' : '';
                                        $um_desc = ($um['idunidad_medida'] == $id_um) ? $um['medida'] . " (Actual)" : $um['medida'];
                                        echo '<option value="' . $um['idunidad_medida'] . '" ' . $selected . '>' . $um_desc . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="categoria_up" class="bmd-label-floating">Categoria</label>
                                <select class="form-control" name="categoria_up" id="categoria_up">
                                    <option value="" disabled>Seleccione una opción</option>
                                    <?php
                                    foreach ($articlesCAT as $cat) {
                                        $selected = ($cat['id_categoria'] == $id_cat) ? 'selected' : '';
                                        $cat_desc = ($cat['id_categoria'] == $id_cat) ? $cat['cat_descri'] . " (Actual)" : $cat['cat_descri'];
                                        echo '<option value="' . $cat['id_categoria'] . '" ' . $selected . '>' . $cat_desc . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="marca_up" class="bmd-label-floating">Marcas</label>
                                <select class="form-control" name="marca_up" id="marca_up">
                                    <option value="" disabled>Seleccione una opción</option>
                                    <?php
                                    foreach ($articlesMAR as $cat) {
                                        $selected = ($cat['id_marcas'] == $id_marca) ? 'selected' : '';
                                        $cat_desc = ($cat['id_marcas'] == $id_marca) ? $cat['mar_descri'] . " (Actual)" : $cat['mar_descri'];
                                        echo '<option value="' . $cat['id_marcas'] . '" ' . $selected . '>' . $cat_desc . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="articulo_Estado_up" class="bmd-label-floating">Estado <span><?php
                                                                if ($campos['estado'] == 1) {
                                                                    echo '<span class="badge badge-success">Activo</span>';
                                                                } else {
                                                                    echo '<span class="badge badge-danger">Inactivo</span>';
                                                                } ?></span></label>
                                <select class="form-control" name="articulo_Estado_up" id="articulo_Estado_up">
                                    <option value="" selected disabled>Seleccione una opción</option>
                                    <option value="1" <?php if ($campos['estado'] == 1) {echo 'selected=""';} ?>>Activo</option>
                                    <option value="0" <?php if ($campos['estado'] == 0) {echo 'selected=""';} ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="articulo_Tipo_up" class="bmd-label-floating">Tipo Producto</label>
                                <select class="form-control" name="articulo_Tipo_up" id="articulo_Tipo_up">
                                    <option value="" selected disabled>Seleccione una opción</option>
                                    <option value="servicio" <?php if ($campos['tipo'] == 'servicio') {echo 'selected=""';} ?>>Servicio</option>
                                    <option value="producto" <?php if ($campos['tipo'] == 'producto') {echo 'selected=""';} ?>>Producto</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <br><br><br>
            <p class="text-center" style="margin-top: 40px;">
                <button type="submit" class="btn btn-raised btn-success btn-sm"><i class="fas fa-sync-alt"></i> &nbsp; ACTUALIZAR</button>
            </p>
        </form>
    <?php } else { ?>
        <div class="alert alert-danger text-center" role="alert">
            <p><i class="fas fa-exclamation-triangle fa-5x"></i></p>
            <h4 class="alert-heading">¡Ocurrió un error inesperado!</h4>
            <p class="mb-0">Lo sentimos, no podemos mostrar la información solicitada debido a un error.</p>
        </div>
    <?php } ?>
</div>