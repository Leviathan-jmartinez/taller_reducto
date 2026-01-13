<?php

if (!mainModel::tienePermisoVista('empleado.editar')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-sync-alt fa-fw"></i> &nbsp; ACTUALIZAR EMPLEADO
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>empleado-nuevo/">
                <i class="fas fa-user-plus fa-fw"></i> &nbsp; AGREGAR EMPLEADO
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>empleado-lista/">
                <i class="fas fa-users fa-fw"></i> &nbsp; LISTA DE EMPLEADOS
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>empleado-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR EMPLEADO
            </a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <?php
    require_once "./controladores/empleadoControlador.php";
    $ins_empleado = new empleadoControlador();

    $datos = $ins_empleado->datos_empleado_controlador("Unico", $pagina[1]);

    if ($datos->rowCount() == 1) {
        $campos = $datos->fetch();

        $cargos = $ins_empleado->listar_cargos_controlador();
        $sucursales = $ins_empleado->listar_sucursales_controlador();
    ?>

        <form class="form-neon FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/empleadoAjax.php"
            method="POST"
            data-form="update"
            autocomplete="off">

            <input type="hidden" name="empleado_id_up" value="<?php echo $pagina[1]; ?>">

            <fieldset>
                <legend>Datos del empleado</legend>

                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cargo</label>
                            <select class="form-control" name="cargo_up">
                                <?php
                                foreach ($cargos as $c) {
                                    $sel = ($c['idcargos'] == $campos['idcargos']) ? 'selected' : '';
                                    $txt = ($sel) ? $c['descripcion'] . " (Actual)" : $c['descripcion'];
                                    echo "<option value='{$c['idcargos']}' $sel>$txt</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Sucursal</label>
                            <select class="form-control" name="sucursal_up">
                                <?php
                                foreach ($sucursales as $s) {
                                    $sel = ($s['id_sucursal'] == $campos['id_sucursal']) ? 'selected' : '';
                                    $txt = ($sel) ? $s['suc_descri'] . " (Actual)" : $s['suc_descri'];
                                    echo "<option value='{$s['id_sucursal']}' $sel>$txt</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cédula</label>
                            <input type="text" class="form-control" name="cedula_up"
                                value="<?php echo $campos['nro_cedula']; ?>">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" class="form-control" name="nombre_up"
                                value="<?php echo $campos['nombre']; ?>">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Apellido</label>
                            <input type="text" class="form-control" name="apellido_up"
                                value="<?php echo $campos['apellido']; ?>">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Celular</label>
                            <input type="text" class="form-control" name="celular_up"
                                value="<?php echo $campos['celular']; ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" class="form-control" name="direccion_up"
                                value="<?php echo $campos['direccion']; ?>">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Estado civil</label>
                            <input type="text" class="form-control" name="estado_civil_up"
                                value="<?php echo $campos['estado_civil']; ?>">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Estado laboral</label>
                            <select class="form-control" name="empleado_estado_up">
                                <option value="1" <?php if ($campos['empleado_estado'] == 1) echo 'selected'; ?>>Disponible</option>
                                <option value="2" <?php if ($campos['empleado_estado'] == 2) echo 'selected'; ?>>Vacaciones</option>
                                <option value="3" <?php if ($campos['empleado_estado'] == 3) echo 'selected'; ?>>Suspendido</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Estado</label>
                            <select class="form-control" name="estado_up">
                                <option value="1" <?php if ($campos['estado'] == 1) echo 'selected'; ?>>Activo</option>
                                <option value="0" <?php if ($campos['estado'] == 0) echo 'selected'; ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>

                </div>
            </fieldset>

            <p class="text-center mt-4">
                <button type="submit" class="btn btn-raised btn-success">
                    <i class="fas fa-sync-alt"></i> &nbsp; ACTUALIZAR
                </button>
            </p>

        </form>

    <?php } else { ?>

        <div class="alert alert-danger text-center">
            No se pudo cargar el empleado.
        </div>

    <?php } ?>
</div>