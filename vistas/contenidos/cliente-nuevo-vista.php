<?php
// Capturar el valor seleccionado después de enviar el formulario
$tipo_documento = isset($_POST['cliente_tipo']) ? $_POST['cliente_tipo'] : '';
?>
<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR CLIENTE
    </h3>
    <p class="text-justify">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem odit amet asperiores quis minus, dolorem repellendus optio doloremque error a omnis soluta quae magnam dignissimos, ipsam, temporibus sequi, commodi accusantium!
    </p>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>cliente-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR CLIENTE</a>
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
    <form action="" class="form-neon" autocomplete="off">
        <fieldset>
            <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
            <div class="container-fluid">

                <form action="" method="POST">
                    <!-- Selección de Tipo de Documento -->
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_tipo" class="bmd-label-floating">Tipo de Documento</label>
                                <select class="form-control" name="cliente_tipo" id="cliente_tipo" onchange="this.form.submit()">
                                    <option value="" disabled selected>Seleccione una opción</option>
                                    <option value="ci" <?php if ($tipo_documento == "ci") echo "selected"; ?>>C.I</option>
                                    <option value="cc" <?php if ($tipo_documento == "cc") echo "selected"; ?>>CC</option>
                                    <option value="cd" <?php if ($tipo_documento == "cd") echo "selected"; ?>>CD</option>
                                    <option value="of" <?php if ($tipo_documento == "of") echo "selected"; ?>>OF</option>
                                    <option value="ruc" <?php if ($tipo_documento == "ruc") echo "selected"; ?>>RUC</option>
                                    <option value="pasaporte" <?php if ($tipo_documento == "pasaporte") echo "selected"; ?>>PASAPORTE</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- Mostrar campos según la selección -->
                <?php if ($tipo_documento == "ruc") { ?>
                    <!-- Campos para RUC -->
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_dni" class="bmd-label-floating">CI</label>
                                <input type="text" pattern="[0-9-]{1,27}" class="form-control" name="cliente_ci_reg" id="cliente_dni" maxlength="27">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_dni" class="bmd-label-floating">RUC</label>
                                <input type="text" pattern="[0-9-]{1,27}" class="form-control" name="cliente_ci_reg" id="cliente_dni" maxlength="27">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="cliente_nombre" class="bmd-label-floating">Nombre</label>
                                <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="cliente_nombre_reg" id="cliente_nombre" maxlength="40">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_apellido" class="bmd-label-floating">Apellido</label>
                                <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="cliente_apellido_reg" id="cliente_apellido" maxlength="40">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_telefono" class="bmd-label-floating">Teléfono</label>
                                <input type="text" pattern="[0-9()+]{8,20}" class="form-control" name="cliente_telefono_reg" id="cliente_telefono" maxlength="20">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_direccion" class="bmd-label-floating">Dirección</label>
                                <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,150}" class="form-control" name="cliente_direccion_reg" id="cliente_direccion" maxlength="150">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_tipo" class="bmd-label-floating">Ciudades</label>
                                <select class="form-control" name="ciudad_reg" id="cliente_tipo">
                                    <option value="" disabled selected>Seleccione una opción</option>
                                    <option value="regular">Regular</option>
                                    <option value="vip">VIP</option>
                                    <option value="nuevo">Nuevo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_dni" class="bmd-label-floating">RUC</label>
                                <input type="text" pattern="[0-9-]{1,27}" class="form-control" name="cliente_ci_reg" id="cliente_dni" maxlength="27">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="cliente_nombre" class="bmd-label-floating">Nombre</label>
                                <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="cliente_nombre_reg" id="cliente_nombre" maxlength="40">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_apellido" class="bmd-label-floating">Razon Social</label>
                                <input type="text" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}" class="form-control" name="cliente_apellido_reg" id="cliente_apellido" maxlength="40">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_telefono" class="bmd-label-floating">Email</label>
                                <input type="text" pattern="[0-9()+]{8,20}" class="form-control" name="cliente_telefono_reg" id="cliente_telefono" maxlength="20">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cliente_tipo" class="bmd-label-floating">Ciudades</label>
                                <select class="form-control" name="ciudad_reg" id="cliente_tipo">
                                    <option value="" disabled selected>Seleccione una opción</option>
                                    <option value="regular">Regular</option>
                                    <option value="vip">VIP</option>
                                    <option value="nuevo">Nuevo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </fieldset>
        <br><br><br>
        <p class="text-center" style="margin-top: 40px;">
            <button type="reset" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR</button>
            &nbsp; &nbsp;
            <button type="submit" class="btn btn-raised btn-info btn-sm"><i class="far fa-save"></i> &nbsp; GUARDAR</button>
        </p>
    </form>

</div>