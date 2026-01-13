<?php
if (!mainModel::tienePermisoVista('proveedor.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
} ?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-truck fa-fw"></i> &nbsp; AGREGAR PROVEEDOR
    </h3>
    <p class="text-justify"></p>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>proveedor-nuevo/">
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
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/proveedorAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <fieldset>
            <legend>
                <i class="far fa-address-card"></i> &nbsp; Información del proveedor
            </legend>

            <div class="container-fluid">
                <div class="row">

                    <?php
                    require_once "./controladores/proveedorControlador.php";
                    $provCtrl = new proveedorControlador();
                    $ciudades = $provCtrl->listar_ciudades_controlador();
                    ?>

                    <!-- Razón Social -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="bmd-label-floating">Razón Social</label>
                            <input type="text"
                                class="form-control"
                                name="razon_social_reg"
                                maxlength="70"
                                required>
                        </div>
                    </div>

                    <!-- RUC -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="bmd-label-floating">RUC</label>
                            <input type="text"
                                class="form-control"
                                name="ruc_reg"
                                maxlength="15">
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="bmd-label-floating">Teléfono</label>
                            <input type="text"
                                class="form-control"
                                name="telefono_reg"
                                maxlength="30">
                        </div>
                    </div>

                    <!-- Correo -->
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="bmd-label-floating">Correo</label>
                            <input type="email"
                                class="form-control"
                                name="correo_reg"
                                maxlength="100">
                        </div>
                    </div>

                    <!-- Ciudad -->
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="bmd-label-floating">Ciudad</label>
                            <select class="form-control"
                                name="ciudad_reg"
                                required>
                                <option value="" selected>Seleccione una opción</option>
                                <?php
                                foreach ($ciudades as $c) {
                                    echo '<option value="' . $c['id_ciudad'] . '">' . $c['ciu_descri'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="bmd-label-floating">Dirección</label>
                            <input type="text"
                                class="form-control"
                                name="direccion_reg"
                                maxlength="120">
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="bmd-label-floating">Estado</label>
                            <select class="form-control"
                                name="estado_reg"
                                required>
                                <option value="" selected>Seleccione una opción</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </fieldset>

        <br><br>

        <p class="text-center">
            <button type="reset"
                class="btn btn-raised btn-secondary btn-sm">
                <i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR
            </button>
            &nbsp;&nbsp;
            <button type="submit"
                class="btn btn-raised btn-info btn-sm">
                <i class="far fa-save"></i> &nbsp; GUARDAR
            </button>
        </p>

    </form>
</div>