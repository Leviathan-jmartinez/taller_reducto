
<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-sync-alt fa-fw"></i> &nbsp; ACTUALIZAR VEHÍCULO
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>vehiculo-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR VEHÍCULO
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>vehiculo-lista/">
                <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE VEHÍCULOS
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>vehiculo-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR VEHÍCULO
            </a>
        </li>
    </ul>
</div>

<!-- CONTENT -->
<div class="container-fluid">
    <?php
    require_once "./controladores/vehiculoControlador.php";
    $ins = new vehiculoControlador();

    $datos = $ins->datos_vehiculo_controlador("Unico", $pagina[1]);

    if ($datos->rowCount() == 1) {
        $campos = $datos->fetch();

        $clientes = $ins->listar_clientes_controlador();
        $modelos  = $ins->listar_modelos_controlador();
        $colores  = $ins->listar_colores_controlador();
    ?>
        <form class="form-neon FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/vehiculoAjax.php"
            method="POST"
            data-form="update"
            autocomplete="off">

            <input type="hidden" name="vehiculo_id_up" value="<?php echo $pagina[1]; ?>">

            <fieldset>
                <legend><i class="far fa-car"></i> &nbsp; Datos del vehículo</legend>

                <div class="row">

                    <!-- CLIENTE -->
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="bmd-label-floating">Cliente</label>
                            <select class="form-control" name="cliente_up">
                                <option value="" disabled>Seleccione</option>
                                <?php
                                foreach ($clientes as $cli) {
                                    $selected = ($cli['id_cliente'] == $campos['id_cliente']) ? 'selected' : '';
                                    $texto = ($selected)
                                        ? $cli['cliente'] . ' (Actual)'
                                        : $cli['cliente'];
                                    echo '<option value="' . $cli['id_cliente'] . '" ' . $selected . '>' . $texto . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- MODELO -->
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="bmd-label-floating">Modelo</label>
                            <select class="form-control" name="modelo_up">
                                <option value="" disabled>Seleccione</option>
                                <?php
                                foreach ($modelos as $mod) {
                                    $selected = ($mod['id_modeloauto'] == $campos['id_modeloauto']) ? 'selected' : '';
                                    $texto = ($selected)
                                        ? $mod['mod_descri'] . ' (Actual)'
                                        : $mod['mod_descri'];
                                    echo '<option value="' . $mod['id_modeloauto'] . '" ' . $selected . '>' . $texto . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- COLOR -->
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="bmd-label-floating">Color</label>
                            <select class="form-control" name="color_up">
                                <option value="" disabled>Seleccione</option>
                                <?php
                                foreach ($colores as $col) {
                                    $selected = ($col['id_color'] == $campos['id_color']) ? 'selected' : '';
                                    $texto = ($selected)
                                        ? $col['col_descripcion'] . ' (Actual)'
                                        : $col['col_descripcion'];
                                    echo '<option value="' . $col['id_color'] . '" ' . $selected . '>' . $texto . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- PLACA -->
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="bmd-label-floating">Placa</label>
                            <input type="text"
                                class="form-control"
                                name="placa_up"
                                maxlength="20"
                                value="<?php echo $campos['placa']; ?>">
                        </div>
                    </div>

                    <!-- AÑO -->
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="bmd-label-floating">Año</label>
                            <input type="text"
                                class="form-control"
                                name="anho_up"
                                maxlength="4"
                                value="<?php echo $campos['anho']; ?>">
                        </div>
                    </div>

                    <!-- NRO SERIE -->
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="bmd-label-floating">Nro. Serie</label>
                            <input type="text"
                                class="form-control"
                                name="serie_up"
                                maxlength="50"
                                value="<?php echo $campos['nro_serie']; ?>">
                        </div>
                    </div>

                    <!-- ESTADO -->
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="bmd-label-floating">
                                Estado
                                <?php
                                echo ($campos['estado'] == 1)
                                    ? '<span class="badge badge-success">Activo</span>'
                                    : '<span class="badge badge-danger">Inactivo</span>';
                                ?>
                            </label>
                            <select class="form-control" name="estado_up">
                                <option value="" disabled>Seleccione</option>
                                <option value="1" <?php if ($campos['estado'] == 1) echo 'selected'; ?>>Activo</option>
                                <option value="0" <?php if ($campos['estado'] == 0) echo 'selected'; ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>

                </div>
            </fieldset>

            <p class="text-center mt-4">
                <button type="submit" class="btn btn-raised btn-success btn-sm">
                    <i class="fas fa-sync-alt"></i> &nbsp; ACTUALIZAR
                </button>
            </p>
        </form>

    <?php } else { ?>

        <div class="alert alert-danger text-center">
            <p><i class="fas fa-exclamation-triangle fa-4x"></i></p>
            <h4>Error al cargar el vehículo</h4>
            <p>No se pudo obtener la información solicitada.</p>
        </div>

    <?php } ?>
</div>