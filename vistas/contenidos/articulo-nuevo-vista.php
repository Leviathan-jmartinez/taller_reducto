<?php
if (!mainModel::tienePermiso('articulo.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$pagina = explode("/", $_GET['vista']);
$id = $pagina[1] ?? null;

$editando = false;

require_once "./controladores/articuloControlador.php";
$ins_articulo = new articuloControlador();

if ($id != null) {
    $dat = $ins_articulo->datos_articulos_controlador("Unico", $id);
    if ($dat->rowCount() == 1) {
        $campos = $dat->fetch();
        $editando = true;
    }
}

$busqueda = $_SESSION['busqueda_articulo'] ?? "";

/* LISTAS */
$articlesIVA = $ins_articulo->listar_iva_controlador();
$articlesPro = $ins_articulo->listar_proveedores_controlador();
$articlesUM  = $ins_articulo->listar_um_controlador();
$articlesCAT = $ins_articulo->listar_cate_controlador();
$articlesMAR = $ins_articulo->listar_marca_controlador();
?>

<div class="full-box page-header">
    <h3>
        <?php echo $editando ? "ACTUALIZAR ARTICULO" : "AGREGAR ARTICULO"; ?>
    </h3>
</div>

<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/articuloAjax.php"
        method="POST"
        data-form="<?php echo $editando ? 'update' : 'save'; ?>">

        <?php if ($editando) { ?>
            <input type="hidden" name="articulo_id_up" value="<?php echo $id; ?>">
        <?php } ?>

        <div class="row">

            <!-- CODIGO -->
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text"
                        class="form-control"
                        placeholder="Código *"
                        name="<?php echo $editando ? 'articulo_codigo_up' : 'articulo_codigo_reg'; ?>"
                        value="<?php echo $editando ? $campos['codigo'] : ''; ?>">
                </div>
            </div>

            <!-- DESCRIPCION -->
            <div class="col-md-8">
                <div class="form-group">
                    <input type="text"
                        class="form-control"
                        placeholder="Descripción del artículo"
                        name="<?php echo $editando ? 'articulo_nombre_up' : 'articulo_nombre_reg'; ?>"
                        value="<?php echo $editando ? $campos['desc_articulo'] : ''; ?>">
                </div>
            </div>

            <!-- PRECIOS -->
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text"
                        class="form-control"
                        placeholder="Precio Compra"
                        name="<?php echo $editando ? 'articulo_priceC_up' : 'articulo_priceC_reg'; ?>"
                        value="<?php echo $editando ? $campos['precio_compra'] : ''; ?>">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <input type="text"
                        class="form-control"
                        placeholder="Precio Venta"
                        name="<?php echo $editando ? 'articulo_priceV_up' : 'articulo_priceV_reg'; ?>"
                        value="<?php echo $editando ? $campos['precio_venta'] : ''; ?>">
                </div>
            </div>

            <!-- IVA -->
            <div class="col-md-4">
                <div class="form-group">
                    <select class="form-control select2"
                        name="<?php echo $editando ? 'tipo_iva_up' : 'tipo_iva_reg'; ?>">
                        <option value="" disabled selected>Seleccione IVA</option>
                        <?php foreach ($articlesIVA as $iva) { ?>
                            <option value="<?php echo $iva['idiva']; ?>"
                                <?php if ($editando && $campos['idiva'] == $iva['idiva']) echo "selected"; ?>>
                                <?php echo $iva['tipo_impuesto_descri']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- PROVEEDOR -->
            <div class="col-md-4">
                <div class="form-group">
                    <select class="form-control select2" 
                        name="<?php echo $editando ? 'proveedor_up' : 'proveedor_reg'; ?>">
                        <option value="" disabled selected>Seleccione proveedor</option>
                        <?php foreach ($articlesPro as $prov) { ?>
                            <option value="<?php echo $prov['idproveedores']; ?>"
                                <?php if ($editando && $campos['idproveedores'] == $prov['idproveedores']) echo "selected"; ?>>
                                <?php echo $prov['razon_social']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- UNIDAD -->
            <div class="col-md-4">
                <div class="form-group">
                    <select class="form-control select2"
                        name="<?php echo $editando ? 'um_up' : 'um_reg'; ?>">
                        <option value="" disabled selected>Seleccione unidad</option>
                        <?php foreach ($articlesUM as $um) { ?>
                            <option value="<?php echo $um['idunidad_medida']; ?>"
                                <?php if ($editando && $campos['idunidad_medida'] == $um['idunidad_medida']) echo "selected"; ?>>
                                <?php echo $um['medida']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- CATEGORIA -->
            <div class="col-md-4">
                <div class="form-group">
                    <select class="form-control select2"
                        name="<?php echo $editando ? 'categoria_up' : 'categoria_reg'; ?>">
                        <option value="" disabled selected>Seleccione categoría</option>
                        <?php foreach ($articlesCAT as $cat) { ?>
                            <option value="<?php echo $cat['id_categoria']; ?>"
                                <?php if ($editando && $campos['id_categoria'] == $cat['id_categoria']) echo "selected"; ?>>
                                <?php echo $cat['cat_descri']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- MARCA -->
            <div class="col-md-4">
                <div class="form-group">
                    <select class="form-control select2"
                        name="<?php echo $editando ? 'marca_up' : 'marca_reg'; ?>">
                        <option value="" disabled selected>Seleccione marca</option>
                        <?php foreach ($articlesMAR as $marca) { ?>
                            <option value="<?php echo $marca['id_marcas']; ?>"
                                <?php if ($editando && $campos['id_marcas'] == $marca['id_marcas']) echo "selected"; ?>>
                                <?php echo $marca['mar_descri']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

        </div>

        <p class="text-center mt-4">
            <button type="submit"
                class="btn btn-raised <?php echo $editando ? 'btn-success' : 'btn-info'; ?>">
                <?php echo $editando ? 'ACTUALIZAR' : 'GUARDAR'; ?>
            </button>

            <?php if ($editando) { ?>
                <a href="<?php echo SERVERURL; ?>articulo-nuevo/"
                    class="btn btn-raised btn-secondary">
                    CANCELAR
                </a>
            <?php } ?>
        </p>

    </form>
</div>

<!-- BUSCADOR -->
<div class="container-fluid mb-3">

    <form class="FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="search"
        autocomplete="off">

        <input type="hidden" name="modulo" value="articulo">

        <div class="row">
            <div class="col-12 col-md-6">
                <input type="text"
                    class="form-control"
                    name="busqueda_inicial"
                    placeholder="Buscar articulo..."
                    value="<?php echo $_SESSION['busqueda_articulo'] ?? ''; ?>">
            </div>

            <div class="col-12 col-md-6">
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-search"></i> Buscar
                </button>

                <?php if (isset($_SESSION['busqueda_articulo'])) { ?>
                    <form class="FormularioAjax d-inline"
                        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
                        method="POST">

                        <input type="hidden" name="modulo" value="articulo">
                        <input type="hidden" name="eliminar_busqueda" value="1">

                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </form>
                <?php } ?>
            </div>
        </div>

    </form>

</div>



<div class="container-fluid mt-4">
    <?php

    $pag_actual = 1;
    if (isset($pagina[1]) && is_numeric($pagina[1])) {
        $pag_actual = (int)$pagina[1];
    }
    if (isset($pagina[2]) && is_numeric($pagina[2])) {
        $pag_actual = (int)$pagina[2];
    }

    echo $ins_articulo->listar_articulos_controlador(
        $pag_actual,
        15,
        $pagina[0],
        $busqueda
    );

    ?>
</div>