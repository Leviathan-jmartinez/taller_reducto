<?php
if (!mainModel::tienePermisoVista('sucursal.editar')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-sync-alt fa-fw"></i> &nbsp; ACTUALIZAR SUCURSAL
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>sucursal-nuevo/">
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

<!-- CONTENT -->
<div class="container-fluid">
    <?php
    require_once "./controladores/sucursalControlador.php";
    $ins = new sucursalControlador();

    $datos = $ins->datos_sucursal_controlador("Unico", $pagina[1]);
    if ($datos->rowCount() == 1) {
        $campos = $datos->fetch();
        $empresas = $ins->listar_empresas_controlador();
    ?>
        <form class="form-neon FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/sucursalAjax.php"
            method="POST"
            data-form="update"
            autocomplete="off">

            <input type="hidden" name="sucursal_id_up" value="<?php echo $pagina[1]; ?>">

            <fieldset>
                <legend><i class="far fa-edit"></i> &nbsp; Datos de la sucursal</legend>

                <div class="row">

                    <!-- EMPRESA -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="bmd-label-floating">Empresa</label>
                            <select class="form-control" name="empresa_up">
                                <option value="" disabled>Seleccione</option>
                                <?php
                                foreach ($empresas as $emp) {
                                    $selected = ($emp['id_empresa'] == $campos['id_empresa']) ? 'selected' : '';
                                    $texto = ($selected)
                                        ? $emp['razon_social'] . ' (Actual)'
                                        : $emp['razon_social'];
                                    echo "<option value='{$emp['id_empresa']}' $selected>$texto</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- DESCRIPCION -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="bmd-label-floating">Descripción</label>
                            <input type="text" class="form-control"
                                name="sucursal_descri_up"
                                maxlength="50"
                                value="<?php echo $campos['suc_descri']; ?>">
                        </div>
                    </div>

                    <!-- NRO ESTABLECIMIENTO -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="bmd-label-floating">Nro Establecimiento</label>
                            <input type="number" class="form-control"
                                name="nro_establecimiento_up"
                                value="<?php echo $campos['nro_establecimiento']; ?>">
                        </div>
                    </div>

                    <!-- DIRECCION -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="bmd-label-floating">Dirección</label>
                            <input type="text" class="form-control"
                                name="sucursal_direccion_up"
                                maxlength="120"
                                value="<?php echo $campos['suc_direccion']; ?>">
                        </div>
                    </div>

                    <!-- TELEFONO -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="bmd-label-floating">Teléfono</label>
                            <input type="text" class="form-control"
                                name="sucursal_telefono_up"
                                maxlength="50"
                                value="<?php echo $campos['suc_telefono']; ?>">
                        </div>
                    </div>

                    <!-- ESTADO -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="bmd-label-floating">
                                Estado
                                <?php
                                echo ($campos['estado'] == 1)
                                    ? '<span class="badge badge-success">Activo</span>'
                                    : '<span class="badge badge-danger">Inactivo</span>';
                                ?>
                            </label>
                            <select class="form-control" name="estado_up">
                                <option value="" disabled>Seleccione</option>
                                <option value="1" <?php if ($campos['estado'] == 1) echo 'selected'; ?>>Activo</option>
                                <option value="0" <?php if ($campos['estado'] == 0) echo 'selected'; ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>

                </div>
            </fieldset>

            <p class="text-center mt-4">
                <button type="submit" class="btn btn-raised btn-success btn-sm">
                    <i class="fas fa-sync-alt"></i> &nbsp; ACTUALIZAR
                </button>
            </p>
        </form>

    <?php } else { ?>

        <div class="alert alert-danger text-center">
            <p><i class="fas fa-exclamation-triangle fa-4x"></i></p>
            <h4>No se pudo cargar la sucursal</h4>
        </div>

    <?php } ?>
</div>