<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('cargo.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
$id = $pagina[1] ?? null;

$editando = false;

if ($id != null) {
    require_once "./controladores/cargosControlador.php";
    $ins_cargo = new cargosControlador();

    $dat = $ins_cargo->datos_cargo_controlador("Unico", $id);

    if ($dat->rowCount() == 1) {
        $campos = $dat->fetch();
        $editando = true;
    }
}
?>
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-briefcase fa-fw"></i> &nbsp; AGREGAR CARGO
    </h3>
    <p class="text-justify"></p>
</div>
<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/cargoAjax.php"
        method="POST"
        data-form="<?php echo $editando ? 'update' : 'save'; ?>"
        autocomplete="off">

        <!-- ID OCULTO -->
        <?php if ($editando) { ?>
            <input type="hidden" name="cargo_id_up" value="<?php echo $id; ?>">
        <?php
} ?>

        <fieldset>
            <legend>
                <i class="far fa-address-card"></i> &nbsp;
                <?php echo $editando ? "Actualizar cargo" : "Información del cargo"; ?>
            </legend>
            <legend><i class="fas fa-info-circle"></i> &nbsp; Información básica</legend>
            <div class="container-fluid">
                <div class="row">

                    <!-- DESCRIPCIÓN -->
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="bmd-label-floating">Descripción</label>
                            <input type="text"
                                class="form-control"
                                name="<?php echo $editando ? 'descripcion_up' : 'descripcion_reg'; ?>"
                                maxlength="60"
                                value="<?php echo $editando ? $campos['descripcion'] : ''; ?>"
                                required>
                        </div>
                    </div>

                    <!-- ESTADO -->
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="bmd-label-floating">
                                Estado
                                <?php if ($editando) {
                                    echo $campos['estado'] == 1
                                        ? '<span class="badge badge-success">Activo</span>'
                                        : '<span class="badge badge-danger">Inactivo</span>';
                                } ?>
                            </label>

                            <select class="form-control"
                                name="<?php echo $editando ? 'estado_up' : 'estado_reg'; ?>"
                                required>

                                <option value="" <?php if (!$editando) echo "selected"; ?>>
                                    Seleccione una opción
                                </option>

                                <option value="1"
                                    <?php if ($editando && $campos['estado'] == 1) echo "selected"; ?>>
                                    Activo
                                </option>

                                <option value="0"
                                    <?php if ($editando && $campos['estado'] == 0) echo "selected"; ?>>
                                    Inactivo
                                </option>

                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </fieldset>

        <br><br>

        <p class="text-center">

            <!-- CANCELAR -->
            <?php if ($editando) { ?>
                <a href="<?php echo SERVERURL; ?>cargo-nuevo/"
                    class="btn btn-raised btn-secondary btn-sm">
                    <i class="fas fa-times"></i> &nbsp; CANCELAR
                </a>
            <?php } else { ?>
                <button type="button"
                    id="btnCancelar"
                    class="btn btn-raised btn-secondary btn-sm">
                    <i class="fas fa-times"></i> &nbsp; CANCELAR
                </button>
            <?php
} ?>

            &nbsp;&nbsp;

            <!-- GUARDAR / ACTUALIZAR -->
            <button type="submit"
                class="btn btn-raised <?php echo $editando ? 'btn-success' : 'btn-info'; ?> btn-sm">

                <i class="far <?php echo $editando ? 'fa-sync-alt' : 'fa-save'; ?>"></i>
                &nbsp;

                <?php echo $editando ? "GUARDAR" : "GUARDAR"; ?>
            </button>

        </p>

    </form>
</div>
<div class="container-fluid mb-3">

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="search"
        autocomplete="off">

        <input type="hidden" name="modulo" value="cargo">

        <div class="row">
            <div class="col-12 col-md-6">
                <input type="text"
                    class="form-control"
                    name="busqueda_inicial"
                    placeholder="Buscar cargo..."
                    value="<?php echo $_SESSION['busqueda_cargo'] ?? ''; ?>">
            </div>

            <div class="col-12 col-md-6">
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-search"></i> Buscar
                </button>

                <?php if (isset($_SESSION['busqueda_cargo'])) { ?>
                    <form class="FormularioAjax d-inline"
                        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
                        method="POST">

                        <input type="hidden" name="modulo" value="cargo">
                        <input type="hidden" name="eliminar_busqueda" value="1">

                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </form>
                <?php
} ?>
            </div>
        </div>

    </form>

</div>
<div class="container-fluid">
    <?php
require_once "./controladores/cargosControlador.php";
    $ins_cargo = new cargosControlador();
    $busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : "";
    echo $ins_cargo->listar_cargos_controlador($pagina[1], 10, $pagina[0]);
    ?>
</div>
