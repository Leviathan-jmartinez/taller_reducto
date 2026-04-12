<?php
if (!mainModel::tienePermiso('cargo.editar')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
} ?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-sync-alt fa-fw"></i> &nbsp; ACTUALIZAR CARGO
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>cargo-nuevo/">
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
    <?php
    require_once "./controladores/cargosControlador.php";
    $ins_cargo = new cargosControlador();

    $dat_cargo = $ins_cargo->datos_cargo_controlador("Unico", $pagina[1]);
    if ($dat_cargo->rowCount() == 1) {
        $campos = $dat_cargo->fetch();
    ?>
        <form class="form-neon FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/cargoAjax.php"
            method="POST"
            data-form="update"
            autocomplete="off">

            <input type="hidden" name="cargo_id_up" value="<?php echo $pagina[1]; ?>">

            <fieldset>
                <legend><i class="far fa-edit"></i> &nbsp; Información del cargo</legend>

                <div class="container-fluid">
                    <div class="row">

                        <!-- DESCRIPCION -->
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="bmd-label-floating">Descripción</label>
                                <input type="text"
                                    class="form-control"
                                    name="descripcion_up"
                                    maxlength="60"
                                    value="<?php echo $campos['descripcion']; ?>">
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

                <a href="<?php echo SERVERURL; ?>cargo-lista/"
                    class="btn btn-raised btn-secondary btn-sm">
                    <i class="fas fa-times"></i> &nbsp; CANCELAR
                </a>

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