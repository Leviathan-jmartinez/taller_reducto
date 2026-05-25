<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(['name' => 'STR']);
}

$mostrarCambioClave = (
    isset($_SESSION['id_str'], $_SESSION['cambiar_clave_str']) &&
    (int)$_SESSION['cambiar_clave_str'] === 1
);

if (
    isset($_SESSION['id_str']) &&
    !$mostrarCambioClave
) {
    echo "<script> window.location.href='" . SERVERURL . "home/'; </script>";
    return;
}
?>

<div class="login-container">
    <div class="login-content">
        <p class="text-center">
            <i class="fas <?php echo $mostrarCambioClave ? 'fa-user-lock' : 'fa-user-circle'; ?> fa-5x"></i>
        </p>
        <p class="text-center">
            <?php echo $mostrarCambioClave ? 'Debe cambiar su contrasena antes de continuar' : 'Inicia sesion con tu cuenta'; ?>
        </p>

        <?php if ($mostrarCambioClave) { ?>
            <form class="FormularioAjax"
                action="<?php echo SERVERURL; ?>ajax/loginAjax.php"
                method="POST"
                data-form="update"
                autocomplete="off">

                <input type="hidden" name="accion" value="cambiar_clave_obligatoria">

                <div class="form-group">
                    <label for="clave_actual" class="bmd-label-floating"><i class="fas fa-key"></i> &nbsp; Contrasena actual</label>
                    <input type="password"
                        class="form-control"
                        id="clave_actual"
                        name="clave_actual"
                        pattern="[a-zA-Z0-9$@._-]{7,100}"
                        maxlength="100"
                        required>
                </div>

                <div class="form-group">
                    <label for="clave_nueva_1" class="bmd-label-floating"><i class="fas fa-lock"></i> &nbsp; Nueva contrasena</label>
                    <input type="password"
                        class="form-control"
                        id="clave_nueva_1"
                        name="clave_nueva_1"
                        pattern="[a-zA-Z0-9$@._-]{7,18}"
                        maxlength="18"
                        required>
                </div>

                <div class="form-group">
                    <label for="clave_nueva_2" class="bmd-label-floating"><i class="fas fa-lock"></i> &nbsp; Repetir nueva contrasena</label>
                    <input type="password"
                        class="form-control"
                        id="clave_nueva_2"
                        name="clave_nueva_2"
                        pattern="[a-zA-Z0-9$@._-]{7,18}"
                        maxlength="18"
                        required>
                </div>

                <button type="submit" class="btn-login text-center">GUARDAR CONTRASENA</button>
            </form>
        <?php } else { ?>
            <form action="" method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="UserName" class="bmd-label-floating"><i class="fas fa-user-secret"></i> &nbsp; Usuario</label>
                    <input type="text" class="form-control" id="UserName" name="usuario_login" pattern="[a-zA-Z0-9]{1,35}" maxlength="35" required>
                </div>
                <div class="form-group">
                    <label for="UserPassword" class="bmd-label-floating"><i class="fas fa-key"></i> &nbsp; Contrasena</label>
                    <input type="password" class="form-control" id="UserPassword" name="clave_login" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required>
                </div>
                <button type="submit" class="btn-login text-center">LOG IN</button>
            </form>
        <?php } ?>
    </div>
</div>
<?php
if (!$mostrarCambioClave && isset($_POST['usuario_login']) && isset($_POST['clave_login'])) {
    require_once "./controladores/loginControlador.php";
    $insta_login = new loginControlador();
    $insta_login->iniciar_sesion_controlador();
}
?>
