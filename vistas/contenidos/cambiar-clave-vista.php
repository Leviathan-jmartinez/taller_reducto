<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(['name' => 'STR']);
}

if (!isset($_SESSION['id_str'])) {
    echo '<div class="alert alert-danger">Sesion no valida</div>';
    return;
}
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-key fa-fw"></i> &nbsp; CAMBIAR CONTRASEÑA
    </h3>
</div>

<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/loginAjax.php"
        method="POST"
        data-form="update"
        autocomplete="off">

        <input type="hidden" name="accion" value="cambiar_clave_obligatoria">

        <fieldset>
            <legend><i class="fas fa-user-lock"></i> &nbsp; Cambio requerido</legend>
            <p class="text-muted">
                Debe cambiar su contraseña antes de continuar usando el sistema.
            </p>

            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="clave_actual" class="bmd-label-floating">Contraseña actual</label>
                            <input type="password"
                                class="form-control"
                                name="clave_actual"
                                id="clave_actual"
                                pattern="[a-zA-Z0-9$@._-]{7,100}"
                                maxlength="100">
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="clave_nueva_1" class="bmd-label-floating">Nueva contraseña</label>
                            <input type="password"
                                class="form-control"
                                name="clave_nueva_1"
                                id="clave_nueva_1"
                                pattern="[a-zA-Z0-9$@._-]{7,18}"
                                maxlength="18">
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="clave_nueva_2" class="bmd-label-floating">Repetir nueva contraseña</label>
                            <input type="password"
                                class="form-control"
                                name="clave_nueva_2"
                                id="clave_nueva_2"
                                pattern="[a-zA-Z0-9$@._-]{7,18}"
                                maxlength="18">
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <p class="text-center" style="margin-top: 30px;">
            <button type="submit" class="btn btn-raised btn-info btn-sm">
                <i class="fas fa-save"></i> &nbsp; Guardar contraseña
            </button>
        </p>
    </form>
</div>
