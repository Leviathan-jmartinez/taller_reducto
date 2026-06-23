<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('roles.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
$puedeCrear = mainModel::tienePermiso('roles.crear');

$vistaPartes = explode('/', trim($_GET['vista'] ?? '', '/'));
$vistaActual = $vistaPartes[0] ?? 'rol-nuevo';
$id = ($vistaActual === 'rol-actualizar') ? ($vistaPartes[1] ?? null) : null;

$editando = false;

require_once "./controladores/rolesControlador.php";
$ins = new rolesControlador();

if ($id != null) {
    if (!mainModel::tienePermiso('roles.editar')) {
        echo '<div class="alert alert-danger">Acceso no autorizado</div>';
        return;
    }

    $dat = $ins->datos_roles_controlador("Unico", $id);

    if ($dat->rowCount() == 1) {
        $campos = $dat->fetch();
        $editando = true;
    }
}

$busqueda = $_SESSION['busqueda_roles'] ?? "";
?>

<div class="full-box page-header">
    <h3>
        <?php echo $editando ? "ACTUALIZAR ROL" : ($puedeCrear ? "AGREGAR ROL" : "LISTADO DE ROLES"); ?>
    </h3>
</div>

<div class="container-fluid form-neon app-view">


    <ul class="full-box list-unstyled page-nav-tabs">

        <?php if (mainModel::tienePermiso('roles.ver')) { ?>
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>rol-nuevo/">
                    <i class="fas fa-key fa-fw"></i> &nbsp; Roles
                </a>
            </li>
        <?php
        } ?>

        <?php if (mainModel::tienePermiso('permisos.asignar_permisos')) { ?>
            <li>
                <a href="<?php echo SERVERURL; ?>rol-permisos/">
                    <i class="fas fa-key fa-fw"></i> &nbsp; Asignar permisos
                </a>
            </li>
        <?php
        } ?>

    </ul>

    <?php if ($editando || $puedeCrear) { ?>

    <form class="form-neon FormularioAjax app-form"
        action="<?php echo SERVERURL; ?>ajax/rolesAjax.php"
        method="POST"
        data-form="<?php echo $editando ? 'update' : 'save'; ?>"
        autocomplete="off">

        <?php if ($editando) { ?>
            <input type="hidden" name="rol_id_up" value="<?php echo $id; ?>">
        <?php
        } ?>

        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <label for="rol_nombre">Nombre del rol *</label>
                    <input type="text" class="form-control"
                        id="rol_nombre"
                        placeholder="Nombre del rol"
                        name="<?php echo $editando ? 'rol_nombre_up' : 'rol_nombre_reg'; ?>"
                        value="<?php echo $editando ? $campos['nombre'] : ''; ?>"
                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 _.-]{3,50}"
                        maxlength="50">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="rol_descripcion">Descripción del rol</label>
                    <input type="text" class="form-control"
                        id="rol_descripcion"
                        placeholder="Descripción del rol"
                        name="<?php echo $editando ? 'rol_descripcion_up' : 'rol_descripcion_reg'; ?>"
                        value="<?php echo $editando ? $campos['descripcion'] : ''; ?>"
                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,#\/_-]{3,150}"
                        maxlength="150">
                </div>
            </div>

            <!-- ESTADO SOLO EN UPDATE -->
            <?php if ($editando) { ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="rol_estado">Estado *</label>
                        <select class="form-control select2"
                            id="rol_estado"
                            name="rol_estado_up"
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
                <?php echo $editando ? 'ACTUALIZAR' : 'GUARDAR'; ?>
            </button>

            <?php if ($editando) { ?>
                <a href="<?php echo SERVERURL; ?>rol-nuevo/"
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
    <?php } ?>
</div>

<!-- ================= BUSCADOR ================= -->
<div class="container-fluid form-neon app-view mb-3">

    <form class="FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="search">

        <input type="hidden" name="modulo" value="roles">

        <div class="row">
            <div class="col-md-6">
                <input type="text"
                    class="form-control"
                    name="busqueda_inicial"
                    placeholder="Buscar rol..."
                    value="<?php echo $busqueda; ?>">
            </div>

            <div class="col-md-6">
                <button type="submit" class="btn btn-info">
                    Buscar
                </button>

                <?php if (isset($_SESSION['busqueda_roles'])) { ?>
                    <button type="submit" name="eliminar_busqueda" value="1" class="btn btn-danger">
                        Limpiar
                    </button>
                <?php
                } ?>

            </div>
        </div>

    </form>


    <!-- ================= LISTA ================= -->

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

    echo $ins->listar_roles_controlador(
        $pag_actual,
        10,
        $pagina[0],
        $busqueda
    );
    ?>
</div>
