<?php
$pagina = require __DIR__ . '/../inc/pagina.php';

$vistaPartes = explode('/', trim($_GET['vista'] ?? '', '/'));
$vistaActual = $vistaPartes[0] ?? 'sucursal-nuevo';
$id = ($vistaActual === 'sucursal-actualizar') ? ($vistaPartes[1] ?? null) : null;

$permisoNecesario = ($vistaActual === 'sucursal-actualizar') ? 'sucursal.editar' : 'sucursal.crear';

if (!mainModel::tienePermiso($permisoNecesario)) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$editando = false;

require_once "./controladores/sucursalControlador.php";
$ins = new sucursalControlador();

if ($id != null) {
    $dat = $ins->datos_sucursal_controlador("Unico", $id);
    if ($dat->rowCount() == 1) {
        $campos = $dat->fetch();
        $editando = true;
    }
}

$busqueda = $_SESSION['busqueda_sucursal'] ?? "";
$empresas = $ins->listar_empresas_controlador();
?>

<!-- HEADER -->
<div class="full-box page-header">
    <h3>
        <?php echo $editando ? "ACTUALIZAR SUCURSAL" : "AGREGAR SUCURSAL"; ?>
    </h3>
</div>

<!-- FORM -->
<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/sucursalAjax.php"
        method="POST"
        data-form="<?php echo $editando ? 'update' : 'save'; ?>">

        <?php if ($editando) { ?>
            <input type="hidden" name="sucursal_id_up" value="<?php echo $id; ?>">
        <?php
} ?>
        <legend><i class="fas fa-info-circle"></i> &nbsp; Información básica</legend>   
        <div class="row">

            <div class="col-md-4">
                <select class="form-control select2"
                    name="<?php echo $editando ? 'empresa_up' : 'empresa_reg'; ?>">
                    <option value="">Seleccione empresa</option>
                    <?php
foreach ($empresas as $e) { ?>
                        <option value="<?php echo $e['id_empresa']; ?>"
                            <?php if ($editando && $campos['id_empresa'] == $e['id_empresa']) echo "selected"; ?>>
                            <?php echo $e['razon_social']; ?>
                        </option>
                    <?php
} ?>
                </select>
            </div>

            <div class="col-md-4">
                <input class="form-control"
                    name="<?php echo $editando ? 'sucursal_descri_up' : 'sucursal_descri_reg'; ?>"
                    value="<?php echo $editando ? $campos['suc_descri'] : ''; ?>"
                    placeholder="Descripción">
            </div>

            <div class="col-md-4">
                <input class="form-control"
                    name="<?php echo $editando ? 'nro_establecimiento_up' : 'nro_establecimiento_reg'; ?>"
                    value="<?php echo $editando ? $campos['nro_establecimiento'] : ''; ?>"
                    placeholder="Nro Establecimiento">
            </div>

            <div class="w-100 mt-2"></div>

            <div class="col-md-6">
                <input class="form-control"
                    name="<?php echo $editando ? 'sucursal_direccion_up' : 'sucursal_direccion_reg'; ?>"
                    value="<?php echo $editando ? $campos['suc_direccion'] : ''; ?>"
                    placeholder="Dirección">
            </div>

            <div class="col-md-3">
                <input class="form-control"
                    name="<?php echo $editando ? 'sucursal_telefono_up' : 'sucursal_telefono_reg'; ?>"
                    value="<?php echo $editando ? $campos['suc_telefono'] : ''; ?>"
                    placeholder="Teléfono">
            </div>

            <div class="col-md-3">
                <select class="form-control"
                    name="<?php echo $editando ? 'estado_up' : 'estado_reg'; ?>">
                    <option value="">Estado</option>
                    <option value="1" <?php if ($editando && $campos['estado'] == 1) echo "selected"; ?>>Activo</option>
                    <option value="0" <?php if ($editando && $campos['estado'] == 0) echo "selected"; ?>>Inactivo</option>
                </select>
            </div>

        </div>

        <br>

        <button class="btn btn-info">
            <?php echo $editando ? "Guardar" : "Guardar"; ?>
        </button>

        <?php if ($editando) { ?>
            <a href="<?php echo SERVERURL; ?>sucursal-nuevo/" class="btn btn-secondary">
                Cancelar
            </a>
        <?php
} ?>

    </form>
</div>

<!-- BUSCADOR -->
<div class="container-fluid mb-3">

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="search"
        autocomplete="off">

        <input type="hidden" name="modulo" value="sucursal">

        <div class="row">
            <div class="col-12 col-md-6">
                <input type="text"
                    class="form-control"
                    name="busqueda_inicial"
                    placeholder="Buscar sucursal..."
                    value="<?php echo $_SESSION['busqueda_sucursal'] ?? ''; ?>">
            </div>

            <div class="col-12 col-md-6">
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-search"></i> Buscar
                </button>

                <?php if (isset($_SESSION['busqueda_sucursal'])) { ?>
                    <form class="FormularioAjax d-inline"
                        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
                        method="POST">

                        <input type="hidden" name="modulo" value="sucursal">
                        <input type="hidden" name="eliminar_busqueda" value="1">

                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </form>
                <?php
} ?>
            </div>
        </div>

    </form>

</div>

<!-- LISTA -->
<div class="container-fluid mt-4">
    <?php
$pag_actual = 1;
    if (isset($pagina[1]) && is_numeric($pagina[1])) {
        $pag_actual = (int)$pagina[1];
    }
    if (isset($pagina[2]) && is_numeric($pagina[2])) {
        $pag_actual = (int)$pagina[2];
    }

    echo $ins->listar_sucursales_controlador(
        $pag_actual,
        3,
        $pagina[0],
        $busqueda
    );

    ?>
</div>
