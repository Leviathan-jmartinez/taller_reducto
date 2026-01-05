<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-car fa-fw"></i> &nbsp; AGREGAR VEHÍCULO
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>vehiculo-nuevo/">
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

<div class="container-fluid">
    <?php
    require_once "./controladores/vehiculoControlador.php";
    $ins = new vehiculoControlador();

    $clientes = $ins->listar_clientes_controlador();
    $modelos  = $ins->listar_modelos_controlador();
    $colores  = $ins->listar_colores_controlador();
    ?>
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/vehiculoAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <fieldset>
            <legend><i class="far fa-car"></i> &nbsp; Datos del vehículo</legend>

            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Cliente</label>
                        <select class="form-control" name="cliente_reg">
                            <option value="" selected>Seleccione</option>
                            <?php foreach ($clientes as $c) {
                                echo '<option value="' . $c['id_cliente'] . '">' . $c['cliente'] . '</option>';
                            } ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Modelo</label>
                        <select class="form-control" name="modelo_reg">
                            <option value="" selected>Seleccione</option>
                            <?php foreach ($modelos as $m) {
                                echo '<option value="' . $m['id_modeloauto'] . '">' . $m['mod_descri'] . '</option>';
                            } ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Color</label>
                        <select class="form-control" name="color_reg">
                            <option value="" selected>Seleccione</option>
                            <?php foreach ($colores as $c) {
                                echo '<option value="' . $c['id_color'] . '">' . $c['col_descripcion'] . '</option>';
                            } ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="bmd-label-floating">Placa</label>
                        <input type="text" class="form-control" name="placa_reg" maxlength="20">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="bmd-label-floating">Año</label>
                        <input type="text" class="form-control" name="anho_reg" maxlength="4">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="bmd-label-floating">Nro Serie</label>
                        <input type="text" class="form-control" name="serie_reg" maxlength="50">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="bmd-label-floating">Estado</label>
                        <select class="form-control" name="estado_reg">
                            <option value="" selected>Seleccione</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

            </div>
        </fieldset>

        <p class="text-center mt-4">
            <button type="submit" class="btn btn-raised btn-info btn-sm">
                <i class="far fa-save"></i> &nbsp; GUARDAR
            </button>
        </p>
    </form>
</div>