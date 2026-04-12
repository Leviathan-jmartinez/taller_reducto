<?php
if (!mainModel::tienePermiso('cargo.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
} ?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-briefcase fa-fw"></i> &nbsp; AGREGAR CARGO
    </h3>
    <p class="text-justify"></p>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>cargo-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR CARGO
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>cargo-lista/">
                <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE CARGOS
            </a>
        </li>
    </ul>
</div>

<!-- CONTENT -->
<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/cargoAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <fieldset>
            <legend>
                <i class="far fa-address-card"></i> &nbsp; Información del cargo
            </legend>

            <div class="container-fluid">
                <div class="row">

                    <!-- Descripción -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="bmd-label-floating">Descripción</label>
                            <input type="text"
                                class="form-control"
                                name="descripcion_reg"
                                maxlength="60"
                                required>
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
            <button type="button" id="btnCancelar" class="btn btn-raised btn-secondary btn-sm">
                <i class="fas fa-times"></i> &nbsp; CANCELAR
            </button>
            &nbsp;&nbsp;
            <button type="submit"
                class="btn btn-raised btn-info btn-sm">
                <i class="far fa-save"></i> &nbsp; GUARDAR
            </button>
        </p>

    </form>
</div>