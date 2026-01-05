<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR SUCURSAL
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>sucursal-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR SUCURSAL
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>sucursal-lista/">
                <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE SUCURSALES
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>sucursal-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR SUCURSAL
            </a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <?php
    require_once "./controladores/sucursalControlador.php";
    $ins = new sucursalControlador();
    $empresas = $ins->listar_empresas_controlador();
    ?>
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/sucursalAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <fieldset>
            <legend><i class="far fa-building"></i> &nbsp; Datos de la sucursal</legend>

            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Empresa</label>
                        <select class="form-control" name="empresa_reg">
                            <option value="" selected>Seleccione una opción</option>
                            <?php foreach ($empresas as $e) {
                                echo '<option value="' . $e['id_empresa'] . '">' . $e['razon_social'] . '</option>';
                            } ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Descripción</label>
                        <input type="text" class="form-control" name="sucursal_descri_reg" maxlength="50">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="bmd-label-floating">Nro Establecimiento</label>
                        <input type="number" class="form-control" name="nro_establecimiento_reg">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="bmd-label-floating">Dirección</label>
                        <input type="text" class="form-control" name="sucursal_direccion_reg" maxlength="120">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="bmd-label-floating">Teléfono</label>
                        <input type="text" class="form-control" name="sucursal_telefono_reg" maxlength="50">
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