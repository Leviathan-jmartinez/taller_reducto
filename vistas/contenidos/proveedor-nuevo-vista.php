<?php
$pagina = require __DIR__ . '/../inc/pagina.php';

$vistaPartes = explode('/', trim($_GET['vista'] ?? '', '/'));
$vistaActual = $vistaPartes[0] ?? 'proveedor-nuevo';
$id = ($vistaActual === 'proveedor-actualizar') ? ($vistaPartes[1] ?? null) : null;
$permisoNecesario = ($vistaActual === 'proveedor-actualizar') ? 'proveedor.editar' : 'proveedor.crear';

if (!mainModel::tienePermiso($permisoNecesario)) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$editando = false;

require_once "./controladores/proveedorControlador.php";
$ins_proveedor = new proveedorControlador();

if ($id != null) {
    $dat = $ins_proveedor->datos_proveedor_controlador("Unico", $id);
    if ($dat->rowCount() == 1) {
        $campos = $dat->fetch();
        $editando = true;
    }
}

$busqueda = $_SESSION['busqueda_proveedor'] ?? "";
$ciudades = $ins_proveedor->listar_ciudades_controlador();
?>

<!-- HEADER -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-truck fa-fw"></i> &nbsp;
        <?php echo $editando ? "ACTUALIZAR PROVEEDOR" : "AGREGAR PROVEEDOR"; ?>
    </h3>
</div>

<!-- FORM -->
<div class="container-fluid">
    <form class="form-neon FormularioAjax app-form"
        action="<?php echo SERVERURL; ?>ajax/proveedorAjax.php"
        method="POST"
        data-form="<?php echo $editando ? 'update' : 'save'; ?>"
        autocomplete="off">

        <?php if ($editando) { ?>
            <input type="hidden" name="proveedor_id_up" value="<?php echo $id; ?>">
        <?php
} ?>

        <fieldset>
            <legend>Información del proveedor</legend>

            <div class="row">

                <div class="col-12 col-md-6">
                    <input type="text"
                        class="form-control"
                        name="<?php echo $editando ? 'razon_social_up' : 'razon_social_reg'; ?>"
                        value="<?php echo $editando ? $campos['razon_social'] : ''; ?>"
                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,&-]{3,70}"
                        maxlength="70"
                        title="Use de 3 a 70 caracteres: letras, numeros, espacios, punto, coma, ampersand o guion"
                        placeholder="Razón Social">
                </div>

                <div class="col-12 col-md-6">
                    <input type="text"
                        class="form-control"
                        name="<?php echo $editando ? 'ruc_up' : 'ruc_reg'; ?>"
                        value="<?php echo $editando ? $campos['ruc'] : ''; ?>"
                        pattern="[0-9-]{6,15}"
                        maxlength="15"
                        inputmode="numeric"
                        title="Ingrese de 6 a 15 caracteres, solo numeros y guion"
                        placeholder="RUC">
                </div>

                <div class="w-100 mt-2"></div>

                <div class="col-12 col-md-4">
                    <input type="text"
                        class="form-control"
                        name="<?php echo $editando ? 'telefono_up' : 'telefono_reg'; ?>"
                        value="<?php echo $editando ? $campos['telefono'] : ''; ?>"
                        pattern="[0-9()+ -]{6,30}"
                        maxlength="30"
                        inputmode="tel"
                        title="Use de 6 a 30 caracteres: numeros, espacios, parentesis, mas o guion"
                        placeholder="Teléfono">
                </div>

                <div class="col-12 col-md-4">
                    <input type="email"
                        class="form-control"
                        name="<?php echo $editando ? 'correo_up' : 'correo_reg'; ?>"
                        value="<?php echo $editando ? $campos['correo'] : ''; ?>"
                        maxlength="100"
                        placeholder="Correo">
                </div>

                <div class="col-12 col-md-4">
                    <select class="form-control select2"
                        name="<?php echo $editando ? 'ciudad_up' : 'ciudad_reg'; ?>">

                        <option value=""></option>

                        <?php
foreach ($ciudades as $c) { ?>
                            <option value="<?php echo $c['id_ciudad']; ?>"
                                <?php if ($editando && $campos['id_ciudad'] == $c['id_ciudad']) echo "selected"; ?>>
                                <?php echo $c['ciu_descri']; ?>
                            </option>
                        <?php
} ?>
                    </select>
                </div>

                <div class="w-100 mt-2"></div>

                <div class="col-12">
                    <input type="text"
                        class="form-control"
                        name="<?php echo $editando ? 'direccion_up' : 'direccion_reg'; ?>"
                        value="<?php echo $editando ? $campos['direccion'] : ''; ?>"
                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,#°\/-]{3,120}"
                        maxlength="120"
                        title="Use de 3 a 120 caracteres validos para direccion"
                        placeholder="Dirección">
                </div>

                <div class="w-100 mt-2"></div>

                <div class="col-12 col-md-4">
                    <select class="form-control"
                        name="<?php echo $editando ? 'estado_up' : 'estado_reg'; ?>">

                        <option value="">Estado</option>
                        <option value="1" <?php if ($editando && $campos['estado'] == 1) echo "selected"; ?>>Activo</option>
                        <option value="0" <?php if ($editando && $campos['estado'] == 0) echo "selected"; ?>>Inactivo</option>

                    </select>
                </div>

            </div>
        </fieldset>

        <br>

        <button class="btn btn-info">
            <?php echo $editando ? "Guardar" : "Guardar"; ?>
        </button>

        <?php if ($editando) { ?>
            <a href="<?php echo SERVERURL; ?>proveedor-nuevo/" class="btn btn-secondary">
                Cancelar
            </a>
        <?php } else { ?>
            <button type="reset" class="btn btn-secondary">
                Cancelar
            </button>
        <?php
} ?>

    </form>
</div>

<!-- BUSCADOR -->
<div class="container-fluid mb-3">

    <form class="form-neon FormularioAjax app-form"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="search"
        autocomplete="off">

        <input type="hidden" name="modulo" value="proveedor">

        <div class="row">
            <div class="col-12 col-md-6">
                <input type="text"
                    class="form-control"
                    name="busqueda_inicial"
                    placeholder="Buscar proveedor..."
                    value="<?php echo $_SESSION['busqueda_proveedor'] ?? ''; ?>">
            </div>

            <div class="col-12 col-md-6">
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-search"></i> Buscar
                </button>

                <?php if (isset($_SESSION['busqueda_proveedor'])) { ?>
                    <button type="submit" name="eliminar_busqueda" value="1" class="btn btn-danger">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
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

    $ins_proveedor->paginador_proveedores_controlador(
        $pag_actual,
        10,
        $pagina[0],
        $busqueda
    );

    ?>
</div>
