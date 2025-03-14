<?php
if ($_SESSION['nivel_str'] > 2 || $_SESSION['nivel_str'] < 1) {
    echo $lc->forzarCierre_sesion_controlador();
    exit();
}
?>
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-sync-alt fa-fw"></i> &nbsp; ACTUALIZAR CLIENTE
    </h3>
    <p class="text-justify">
    </p>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>cliente-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR CLIENTE</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>cliente-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE CLIENTES</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>cliente-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR CLIENTE</a>
        </li>
    </ul>
</div>

<!-- Content here-->
<div class="container-fluid">
    <?php
    require_once "./controladores/clienteControlador.php";
    $ins_client = new clienteControlador();

    $datos_cliente = $ins_client->datos_cliente_controlador("Unico", $pagina[1]);
    if ($datos_cliente->rowCount() == 1) {
        $campos = $datos_cliente->fetch();
    ?>
        <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/clienteAjax.php" method="POST" data-form="update" autocomplete="off">
            <input type="hidden" name="cliente_id_up" value="<?php echo $pagina[1] ?>">
            <fieldset>
                <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="tipo_documento" class="bmd-label-floating">Tipo de Documento</label>
                                <select class="form-control" name="tipo_documento" id="cliente_tipo">
                                    <option value="" disabled selected>Seleccione una opción</option>
                                    <option value="CI">C.I</option>
                                    <option value="CC">CC</option>
                                    <option value="CD">CD</option>
                                    <option value="OF">OF</option>
                                    <option value="RUC">RUC</option>
                                    <option value="PASAPORTE">PASAPORTE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_doc" class="bmd-label-floating">CI o RUC</label>
                                <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{1,27}" class="form-control" name="cliente_doc_up" id="cliente_dni" maxlength="27" value="<?php echo $campos['doc_number']; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_dni" class="bmd-label-floating">Informacion Adicional</label>
                                <input type="text" pattern="[0-9()\+]{1,2}" class="form-control" name="cliente_dv_up" id="cliente_dv" maxlength="27" value="<?php echo $campos['digito_v']; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_nombre" class="bmd-label-floating">Nombre</label>
                                <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="cliente_nombre_up" id="cliente_nombre" maxlength="40" value="<?php echo $campos['nombre_cliente']; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_apellido" class="bmd-label-floating">Apellido</label>
                                <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="cliente_apellido_up" id="cliente_apellido" maxlength="40" value="<?php echo $campos['apellido_cliente']; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_telefono" class="bmd-label-floating">Teléfono</label>
                                <input type="text" pattern="[0-9()\+]{8,20}" class="form-control" name="cliente_telefono_up" id="cliente_telefono" maxlength="20" value="<?php echo $campos['celular_cliente']; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="cliente_direccion" class="bmd-label-floating">Dirección</label>
                                <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\-\ ]{1,150}" class="form-control" name="cliente_direccion_up" id="cliente_direccion" maxlength="150" value="<?php echo $campos['direccion_cliente']; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="cliente_email_up" class="bmd-label-floating">Email</label>
                                <input type="text" class="form-control" name="cliente_email_up" id="cliente_email_up" maxlength="150" value="<?php echo $campos['email_cliente']; ?>">
                            </div>
                        </div>
                        <?php
                        require_once "./controladores/clienteControlador.php";
                        $ciudadController = new clienteControlador();
                        $ciudades = $ciudadController->listar_ciudades_controlador_up();

                        // Suponiendo que $campos['id_ciudad'] tiene el ID de la ciudad del cliente
                        $id_ciudad_cliente = $campos['id_ciudad'];
                        ?>

                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="ciudad_up" class="bmd-label-floating">Ciudades</label>
                                <select class="form-control" name="ciudad_up" id="cliente_tipo">
                                    <option value="" disabled>Seleccione una opción</option>
                                    <?php
                                    foreach ($ciudades as $ciudad) {
                                        $selected = ($ciudad['id_ciudad'] == $id_ciudad_cliente) ? 'selected' : '';
                                        $nombre_ciudad = ($ciudad['id_ciudad'] == $id_ciudad_cliente) ? $ciudad['ciu_descri'] . " (Actual)" : $ciudad['ciu_descri'];
                                        echo '<option value="' . $ciudad['id_ciudad'] . '" ' . $selected . '>' . $nombre_ciudad . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>


                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_estadoC" class="bmd-label-floating">Estado Civil</label>
                                <select class="form-control" name="cliente_estadoC" id="cliente_estadoC">
                                    <option value="" selected>Seleccione una opción</option>
                                    <option value="Soltero/a">Soltero/a</option>
                                    <option value="Casado/a">Casado/a</option>
                                    <option value="Viudo/a">Viudo/a</option>
                                    <option value="Divorciado/a">Divorciado/a</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4 col-md-4">
                            <span>Estado de la cuenta &nbsp; <?php
                                                                if ($campos['estado_cliente'] == 1) {
                                                                    echo '<span class="badge badge-success">Activa</span>';
                                                                } else {
                                                                    echo '<span class="badge badge-danger">Inactiva</span>';
                                                                } ?></span>
                            <select class="form-control" name="usuario_estado_up">
                                <option value="1" <?php
                                                    if ($campos['estado_cliente'] == 1) {
                                                        echo 'selected=""';
                                                    } ?>>Activa</option>
                                <option value="0" <?php
                                                    if ($campos['estado_cliente'] == 0) {
                                                        echo 'selected=""';
                                                    } ?>>Inactiva</option>
                            </select>

                        </div>
                    </div>
            </fieldset>
            <br><br><br>
            <p class="text-center" style="margin-top: 40px;">
                <button type="submit" class="btn btn-raised btn-success btn-sm"><i class="fas fa-sync-alt"></i> &nbsp; ACTUALIZAR</button>
            </p>
        </form>
    <?php } else { ?>
        <div class="alert alert-danger text-center" role="alert">
            <p><i class="fas fa-exclamation-triangle fa-5x"></i></p>
            <h4 class="alert-heading">¡Ocurrió un error inesperado!</h4>
            <p class="mb-0">Lo sentimos, no podemos mostrar la información solicitada debido a un error.</p>
        </div>
    <?php } ?>
</div>