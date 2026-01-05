<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-sync-alt fa-fw"></i> &nbsp; ACTUALIZAR PROVEEDOR
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>proveedor-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR PROVEEDOR
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>proveedor-lista/">
                <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE PROVEEDORES
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>proveedor-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR PROVEEDOR
            </a>
        </li>
    </ul>
</div>

<!-- CONTENT -->
<div class="container-fluid">
    <?php
    require_once "./controladores/proveedorControlador.php";
    $ins_proveedor = new proveedorControlador();

    $dat_prov = $ins_proveedor->datos_proveedor_controlador("Unico", $pagina[1]);
    if ($dat_prov->rowCount() == 1) {
        $campos = $dat_prov->fetch();

        $ciudades = $ins_proveedor->listar_ciudades_controlador();
        $id_ciudad = $campos['id_ciudad'];
    ?>
        <form class="form-neon FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/proveedorAjax.php"
            method="POST"
            data-form="update"
            autocomplete="off">

            <input type="hidden" name="proveedor_id_up" value="<?php echo $pagina[1]; ?>">

            <fieldset>
                <legend><i class="far fa-edit"></i> &nbsp; Información del proveedor</legend>

                <div class="container-fluid">
                    <div class="row">

                        <!-- RAZON SOCIAL -->
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="bmd-label-floating">Razón Social</label>
                                <input type="text"
                                    class="form-control"
                                    name="razon_social_up"
                                    maxlength="70"
                                    value="<?php echo $campos['razon_social']; ?>">
                            </div>
                        </div>

                        <!-- RUC -->
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="bmd-label-floating">RUC</label>
                                <input type="text"
                                    class="form-control"
                                    name="ruc_up"
                                    maxlength="15"
                                    value="<?php echo $campos['ruc']; ?>">
                            </div>
                        </div>

                        <!-- TELEFONO -->
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="bmd-label-floating">Teléfono</label>
                                <input type="text"
                                    class="form-control"
                                    name="telefono_up"
                                    maxlength="30"
                                    value="<?php echo $campos['telefono']; ?>">
                            </div>
                        </div>

                        <!-- CORREO -->
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="bmd-label-floating">Correo</label>
                                <input type="email"
                                    class="form-control"
                                    name="correo_up"
                                    maxlength="100"
                                    value="<?php echo $campos['correo']; ?>">
                            </div>
                        </div>

                        <!-- CIUDAD -->
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="bmd-label-floating">Ciudad</label>
                                <select class="form-control" name="ciudad_up">
                                    <option value="" disabled>Seleccione una opción</option>
                                    <?php
                                    foreach ($ciudades as $ciu) {
                                        $selected = ($ciu['id_ciudad'] == $id_ciudad) ? 'selected' : '';
                                        $desc = ($ciu['id_ciudad'] == $id_ciudad)
                                            ? $ciu['ciu_descri'] . " (Actual)"
                                            : $ciu['ciu_descri'];
                                        echo '<option value="' . $ciu['id_ciudad'] . '" ' . $selected . '>' . $desc . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- DIRECCION -->
                        <div class="col-12">
                            <div class="form-group">
                                <label class="bmd-label-floating">Dirección</label>
                                <input type="text"
                                    class="form-control"
                                    name="direccion_up"
                                    maxlength="120"
                                    value="<?php echo $campos['direccion']; ?>">
                            </div>
                        </div>

                        <!-- ESTADO -->
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="bmd-label-floating">
                                    Estado
                                    <?php
                                    if ($campos['estado'] == 1) {
                                        echo '<span class="badge badge-success">Activo</span>';
                                    } else {
                                        echo '<span class="badge badge-danger">Inactivo</span>';
                                    }
                                    ?>
                                </label>
                                <select class="form-control" name="estado_up">
                                    <option value="" disabled>Seleccione una opción</option>
                                    <option value="1" <?php if ($campos['estado'] == 1) echo 'selected'; ?>>Activo</option>
                                    <option value="0" <?php if ($campos['estado'] == 0) echo 'selected'; ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
            </fieldset>

            <br><br>

            <p class="text-center" style="margin-top: 40px;">
                <button type="submit" class="btn btn-raised btn-success btn-sm">
                    <i class="fas fa-sync-alt"></i> &nbsp; ACTUALIZAR
                </button>
            </p>
        </form>

    <?php } else { ?>

        <div class="alert alert-danger text-center" role="alert">
            <p><i class="fas fa-exclamation-triangle fa-5x"></i></p>
            <h4 class="alert-heading">¡Ocurrió un error inesperado!</h4>
            <p class="mb-0">
                Lo sentimos, no podemos mostrar la información solicitada.
            </p>
        </div>

    <?php } ?>
</div>