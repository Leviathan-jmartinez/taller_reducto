<?php
$pagina = require __DIR__ . '/../inc/pagina.php';

$vistaPartes = explode('/', trim($_GET['vista'] ?? '', '/'));
$vistaActual = $vistaPartes[0] ?? 'vehiculo-nuevo';
$id = ($vistaActual === 'vehiculo-actualizar') ? ($vistaPartes[1] ?? null) : null;
$permisoNecesario = ($vistaActual === 'vehiculo-actualizar') ? 'vehiculo.editar' : 'vehiculo.ver';

if (!mainModel::tienePermiso($permisoNecesario)) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$editando = false;

require_once "./controladores/vehiculoControlador.php";
$ins_vehiculo = new vehiculoControlador();

if ($id != null) {
    $dat = $ins_vehiculo->datos_vehiculo_controlador("Unico", $id);

    if ($dat->rowCount() == 1) {
        $campos = $dat->fetch();
        $editando = true;
    }
}

$busqueda = $_SESSION['busqueda_vehiculo'] ?? "";


/* LISTAS */

$modelos  = $ins_vehiculo->listar_modelos_controlador();
?>

<div class="full-box page-header">
    <h3>
        <?php echo $editando ? "ACTUALIZAR VEHICULO" : "AGREGAR VEHICULO"; ?>
    </h3>
</div>

<div class="container-fluid">

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/vehiculoAjax.php"  
        method="POST"
        data-form="<?php echo $editando ? 'update' : 'save'; ?>">

        <?php if ($editando) { ?>
            <input type="hidden" name="vehiculo_id_up" value="<?php echo $id; ?>">
        <?php
} ?>

        <div class="row">
            <legend><i class="far fa-car"></i> &nbsp; Datos del vehículo</legend>
            <!-- CLIENTE -->
            <div class="col-md-4">
                <div class="form-group">
                    <select class="form-control select2-clientes"
                        name="<?php echo $editando ? 'cliente_up' : 'cliente_reg'; ?>">

                        <?php if ($editando) { ?>
                            <option value="<?php echo $campos['id_cliente']; ?>" selected>
                                <?php echo $campos['cliente']; ?>
                            </option>
                        <?php
} ?>

                    </select>
                </div>
            </div>

            <!-- MODELO -->
            <div class="col-md-4">
                <div class="form-group">
                    <select class="form-control select2"
                        name="<?php echo $editando ? 'modelo_up' : 'modelo_reg'; ?>">
                        <option value="" disabled selected>Seleccione modelo</option>
                        <?php
foreach ($modelos as $m) { ?>
                            <option value="<?php echo $m['id_modeloauto']; ?>"
                                <?php if ($editando && $campos['id_modeloauto'] == $m['id_modeloauto']) echo "selected"; ?>>
                                <?php echo $m['mod_descri']; ?>
                            </option>
                        <?php
} ?>
                    </select>
                </div>
            </div>

            <!-- COLOR -->
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text"
                        class="form-control"
                        placeholder="Color"
                        name="<?php echo $editando ? 'color_up' : 'color_reg'; ?>"
                        value="<?php echo $editando ? $campos['color'] : ''; ?>">
                </div>
            </div>

            <!-- PLACA -->
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text"
                        class="form-control"
                        placeholder="Placa *"
                        name="<?php echo $editando ? 'placa_up' : 'placa_reg'; ?>"
                        value="<?php echo $editando ? $campos['placa'] : ''; ?>">
                </div>
            </div>

            <!-- AÑO -->
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text"
                        class="form-control"
                        placeholder="Año"
                        name="<?php echo $editando ? 'anho_up' : 'anho_reg'; ?>"
                        value="<?php echo $editando ? $campos['anho'] : ''; ?>">
                </div>
            </div>

            <!-- SERIE -->
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text"
                        class="form-control"
                        placeholder="Nro Serie"
                        name="<?php echo $editando ? 'serie_up' : 'serie_reg'; ?>"
                        value="<?php echo $editando ? $campos['nro_serie'] : ''; ?>">
                </div>
            </div>

            <!-- ESTADO -->
            <div class="col-md-4">
                <div class="form-group">
                    <select class="form-control select2"
                        name="<?php echo $editando ? 'estado_up' : 'estado_reg'; ?>">
                        <option value="" disabled selected>Seleccione estado</option>
                        <option value="1" <?php if ($editando && $campos['estado'] == 1) echo "selected"; ?>>Activo</option>
                        <option value="0" <?php if ($editando && $campos['estado'] == 0) echo "selected"; ?>>Inactivo</option>
                    </select>
                </div>
            </div>

        </div>

        <p class="text-center mt-4">
            <button type="submit"
                class="btn btn-raised <?php echo $editando ? 'btn-success' : 'btn-info'; ?>">
                <?php echo $editando ? 'GUARDAR' : 'GUARDAR'; ?>
            </button>

            <?php if ($editando) { ?>
                <a href="<?php echo SERVERURL; ?>vehiculo-nuevo/"
                    class="btn btn-raised btn-secondary">
                    CANCELAR
                </a>
            <?php
} ?>
        </p>

    </form>
</div>

<!-- BUSCADOR -->
<div class="container-fluid mb-3">

    <form class="FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="search"
        autocomplete="off">

        <input type="hidden" name="modulo" value="vehiculo">

        <div class="row">
            <div class="col-12 col-md-6">
                <input type="text"
                    class="form-control"
                    name="busqueda_inicial"
                    placeholder="Buscar vehículo..."
                    value="<?php echo $_SESSION['busqueda_vehiculo'] ?? ''; ?>">
            </div>

            <div class="col-12 col-md-6">
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-search"></i> Buscar
                </button>

                <?php if (isset($_SESSION['busqueda_vehiculo'])) { ?>
                    <form class="FormularioAjax d-inline"
                        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
                        method="POST">

                        <input type="hidden" name="modulo" value="vehiculo">
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

    echo $ins_vehiculo->paginador_vehiculos_controlador(
        $pag_actual,
        10,
        $pagina[0],
        $busqueda
    );
    ?>
</div>
