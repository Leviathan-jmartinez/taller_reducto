<?php
$pagina = require __DIR__ . '/../inc/pagina.php';

$vistaPartes = explode('/', trim($_GET['vista'] ?? '', '/'));
$vistaActual = $vistaPartes[0] ?? 'empleado-nuevo';
$id = ($vistaActual === 'empleado-actualizar') ? ($vistaPartes[1] ?? null) : null;
$permisoNecesario = ($vistaActual === 'empleado-actualizar') ? 'empleado.editar' : 'empleado.ver';

if (!mainModel::tienePermiso($permisoNecesario)) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$editando = false;

require_once "./controladores/empleadoControlador.php";
$ins = new empleadoControlador();

if ($id != null) {
    $dat = $ins->datos_empleado_controlador("Unico", $id);

    if ($dat->rowCount() == 1) {
        $campos = $dat->fetch();
        $editando = true;
    }
}

$cargos = $ins->listar_cargos_controlador();
$sucursales = $ins->listar_sucursales_controlador();
$busqueda = $_SESSION['busqueda_empleado'] ?? "";
?>

<div class="full-box page-header">
    <h3>
        <?php echo $editando ? "ACTUALIZAR EMPLEADO" : "AGREGAR EMPLEADO"; ?>
    </h3>
</div>

<div class="container-fluid">

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/empleadoAjax.php"
        method="POST"
        data-form="<?php echo $editando ? 'update' : 'save'; ?>"
        autocomplete="off">

        <?php if ($editando) { ?>
            <input type="hidden" name="empleado_id_up" value="<?php echo $id; ?>">
        <?php
} ?>
        <legend><i class="fas fa-info-circle"></i> &nbsp; Información básica</legend>
        <div class="row">

            <!-- CARGO -->
            <div class="col-md-4">
                <div class="form-group">
                    <select class="form-control select2"
                        name="<?php echo $editando ? 'cargo_up' : 'cargo_reg'; ?>"
                        data-placeholder="Seleccione cargo">

                        <option value=""></option>

                        <?php
foreach ($cargos as $c) { ?>
                            <option value="<?= $c['idcargos']; ?>"
                                <?php if ($editando && $campos['idcargos'] == $c['idcargos']) echo "selected"; ?>>
                                <?= $c['descripcion']; ?>
                            </option>
                        <?php
} ?>

                    </select>
                </div>
            </div>

            <!-- SUCURSAL -->
            <div class="col-md-4">
                <div class="form-group">
                    <select class="form-control select2"
                        name="<?php echo $editando ? 'sucursal_up' : 'sucursal_reg'; ?>"
                        data-placeholder="Seleccione sucursal">

                        <option value=""></option>

                        <?php
foreach ($sucursales as $s) { ?>
                            <option value="<?= $s['id_sucursal']; ?>"
                                <?php if ($editando && $campos['id_sucursal'] == $s['id_sucursal']) echo "selected"; ?>>
                                <?= $s['suc_descri']; ?>
                            </option>
                        <?php
} ?>

                    </select>
                </div>
            </div>

            <!-- CEDULA -->
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text" class="form-control"
                        placeholder="Cédula"
                        name="<?php echo $editando ? 'cedula_up' : 'cedula_reg'; ?>"
                        value="<?php echo $editando ? $campos['nro_cedula'] : ''; ?>"
                        pattern="[0-9]{5,10}"
                        maxlength="10"
                        inputmode="numeric">
                </div>
            </div>

            <!-- NOMBRE -->
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text" class="form-control"
                        placeholder="Nombre"
                        name="<?php echo $editando ? 'nombre_up' : 'nombre_reg'; ?>"
                        value="<?php echo $editando ? $campos['nombre'] : ''; ?>"
                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{2,70}"
                        maxlength="70">
                </div>
            </div>

            <!-- APELLIDO -->
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text" class="form-control"
                        placeholder="Apellido"
                        name="<?php echo $editando ? 'apellido_up' : 'apellido_reg'; ?>"
                        value="<?php echo $editando ? $campos['apellido'] : ''; ?>"
                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{2,70}"
                        maxlength="70">
                </div>
            </div>

            <!-- CELULAR -->
            <div class="col-md-4">
                <div class="form-group">
                    <input type="text" class="form-control"
                        placeholder="Celular"
                        name="<?php echo $editando ? 'celular_up' : 'celular_reg'; ?>"
                        value="<?php echo $editando ? $campos['celular'] : ''; ?>"
                        pattern="[0-9()+ -]{6,30}"
                        maxlength="30"
                        inputmode="tel">
                </div>
            </div>

            <!-- DIRECCION -->
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" class="form-control"
                        placeholder="Dirección"
                        name="<?php echo $editando ? 'direccion_up' : 'direccion_reg'; ?>"
                        value="<?php echo $editando ? $campos['direccion'] : ''; ?>"
                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,#°\/-]{3,120}"
                        maxlength="120">
                </div>
            </div>

            <!-- ESTADO CIVIL -->
            <div class="col-md-3">
                <div class="form-group">
                    <select class="form-control select2"
                        name="<?php echo $editando ? 'estado_civil_up' : 'estado_civil_reg'; ?>"
                        data-placeholder="Estado civil">

                        <option value=""></option>

                        <?php
$estados = ["Soltero/a", "Casado/a", "Viudo/a", "Divorciado/a"];
                        foreach ($estados as $e) { ?>
                            <option value="<?= $e ?>"
                                <?php if ($editando && $campos['estado_civil'] == $e) echo "selected"; ?>>
                                <?= $e ?>
                            </option>
                        <?php
} ?>

                    </select>
                </div>
            </div>

            <?php if ($editando) { ?>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select2"
                            name="estado_up"
                            data-placeholder="Estado">

                            <option value=""></option>

                            <option value="1" <?php if ($campos['estado'] == 1) echo "selected"; ?>>
                                Activo
                            </option>

                            <option value="0" <?php if ($campos['estado'] == 0) echo "selected"; ?>>
                                Inactivo
                            </option>

                        </select>
                    </div>
                </div>
            <?php
} ?>

        </div>

        <p class="text-center mt-4">
            <button type="submit"
                class="btn btn-raised <?php echo $editando ? 'btn-success' : 'btn-info'; ?>">
                <?php echo $editando ? 'GUARDAR' : 'GUARDAR'; ?>
            </button>

            <?php if ($editando) { ?>
                <a href="<?php echo SERVERURL; ?>empleado-nuevo/"
                    class="btn btn-raised btn-secondary">
                    CANCELAR
                </a>
            <?php
} else { ?>
                <button type="reset" class="btn btn-raised btn-secondary">
                    CANCELAR
                </button>
            <?php
} ?>
        </p>

    </form>
</div>

<!-- ================= BUSCADOR ================= -->
<div class="container-fluid mb-3">

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="search"
        autocomplete="off">

        <input type="hidden" name="modulo" value="empleado">

        <div class="row">
            <div class="col-md-6">
                <input type="text"
                    class="form-control"
                    name="busqueda_inicial"
                    placeholder="Buscar empleado..."
                    value="<?php echo $_SESSION['busqueda_empleado'] ?? ''; ?>">
            </div>

            <div class="col-md-6">
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-search"></i> Buscar
                </button>

                <?php if (isset($_SESSION['busqueda_empleado'])) { ?>
                    <form class="FormularioAjax d-inline"
                        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
                        method="POST">

                        <input type="hidden" name="modulo" value="empleado">
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

<!-- ================= LISTA ================= -->
<div class="container-fluid mt-4">
    <?php
$pag_actual = 1;

    if (isset($pagina[1]) && is_numeric($pagina[1])) {
        $pag_actual = (int)$pagina[1];
    }

    if (isset($pagina[2]) && is_numeric($pagina[2])) {
        $pag_actual = (int)$pagina[2];
    }
    if ($pag_actual <= 0) {
        $pag_actual = 1;
    }

    echo $ins->listar_empleados_controlador(
        $pag_actual,
        10,
        $pagina[0],
        $busqueda
    );
    ?>
</div>
