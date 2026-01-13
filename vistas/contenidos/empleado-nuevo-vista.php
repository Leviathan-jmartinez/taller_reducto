<?php

if (!mainModel::tienePermisoVista('empleado.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-user-plus fa-fw"></i> &nbsp; AGREGAR EMPLEADO
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>empleado-nuevo/">
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
        <li>
            <a href="<?php echo SERVERURL; ?>empleado-equipo-asignar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; ASIGNAR EMPLEADO A EQUIPO
            </a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <?php
    require_once "./controladores/empleadoControlador.php";
    $ins = new empleadoControlador();
    $cargos = $ins->listar_cargos_controlador();
    $sucursales = $ins->listar_sucursales_controlador();
    ?>

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/empleadoAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <fieldset>
            <legend>Datos del empleado</legend>

            <div class="row">

                <div class="col-md-4">
                    <select class="form-control" name="cargo_reg">
                        <option value="">Cargo</option>
                        <?php foreach ($cargos as $c) echo "<option value='{$c['idcargos']}'>{$c['descripcion']}</option>"; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <select class="form-control" name="sucursal_reg">
                        <option value="">Sucursal</option>
                        <?php foreach ($sucursales as $s) echo "<option value='{$s['id_sucursal']}'>{$s['suc_descri']}</option>"; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <input type="text" class="form-control" name="cedula_reg" placeholder="Cédula">
                </div>

                <div class="col-md-4">
                    <input type="text" class="form-control" name="nombre_reg" placeholder="Nombre">
                </div>

                <div class="col-md-4">
                    <input type="text" class="form-control" name="apellido_reg" placeholder="Apellido">
                </div>

                <div class="col-md-4">
                    <input type="text" class="form-control" name="celular_reg" placeholder="Celular">
                </div>

                <div class="col-md-6">
                    <input type="text" class="form-control" name="direccion_reg" placeholder="Dirección">
                </div>

                <div class="col-md-3">
                    <input type="text" class="form-control" name="estado_civil_reg" placeholder="Estado civil">
                </div>

                <div class="col-md-3">
                    <select class="form-control" name="empleado_estado_reg">
                        <option value="">Estado laboral</option>
                        <option value="1">Disponible</option>
                        <option value="2">Vacaciones</option>
                        <option value="3">Suspendido</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select class="form-control" name="estado_reg">
                        <option value="">Estado</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>

            </div>
        </fieldset>

        <p class="text-center mt-4">
            <button type="submit" class="btn btn-info">GUARDAR</button>
        </p>

    </form>
</div>