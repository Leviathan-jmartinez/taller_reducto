<?php
$pagina = require __DIR__ . '/../inc/pagina.php';

$vistaPartes = explode('/', trim($_GET['vista'] ?? '', '/'));
$vistaActual = $vistaPartes[0] ?? 'cliente-nuevo';
$id = ($vistaActual === 'cliente-actualizar') ? ($vistaPartes[1] ?? null) : null;
$permisoNecesario = ($vistaActual === 'cliente-actualizar') ? 'cliente.editar' : 'cliente.ver';

if (!mainModel::tienePermiso($permisoNecesario)) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$editando = false;

require_once "./controladores/clienteControlador.php";
$ins_cliente = new clienteControlador();

if ($id != null) {
    $dat = $ins_cliente->datos_cliente_controlador("Unico", $id);
    if ($dat->rowCount() == 1) {
        $campos = $dat->fetch();
        $editando = true;
    }
}

$busqueda = $_SESSION['busqueda_cliente'] ?? "";
?>

<div class="full-box page-header">
    <h3>
        <?php echo $editando ? "ACTUALIZAR CLIENTE" : "AGREGAR CLIENTE"; ?>
    </h3>
</div>

<div class="container-fluid">

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/clienteAjax.php"
        method="POST"
        data-form="<?php echo $editando ? 'update' : 'save'; ?>">

        <?php if ($editando) { ?>
            <input type="hidden" name="cliente_id_up" value="<?php echo $id; ?>">
        <?php
} ?>
        <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
        <div class="row">

            <div class="col-md-4">
                <select class="form-control select2"
                    name="<?php echo $editando ? 'tipo_documento_up' : 'tipo_documento_reg'; ?>">

                    <option value="">Tipo de documento</option>
                    <option value="CI" <?php echo ($editando && $campos['doc_type'] == "CI") ? 'selected' : ''; ?>>CI</option>
                    <option value="RUC" <?php echo ($editando && $campos['doc_type'] == "RUC") ? 'selected' : ''; ?>>RUC</option>
                    <option value="PASAPORTE" <?php echo ($editando && $campos['doc_type'] == "PASAPORTE") ? 'selected' : ''; ?>>Pasaporte</option>
                    <option value="CC" <?php echo ($editando && $campos['doc_type'] == "CC") ? 'selected' : ''; ?>>CC</option>
                    <option value="CD" <?php echo ($editando && $campos['doc_type'] == "CD") ? 'selected' : ''; ?>>CD</option>
                    <option value="OF" <?php echo ($editando && $campos['doc_type'] == "OF") ? 'selected' : ''; ?>>OF</option>
                </select>
            </div>
            <br><br>
            <div class="col-md-4">
                <input type="text" class="form-control"
                    placeholder="Documento (Ej: 1234567)"
                    name="<?php echo $editando ? 'cliente_doc_up' : 'cliente_doc_reg'; ?>"
                    value="<?php echo $editando ? $campos['doc_number'] : ''; ?>">
            </div>
            <br><br>
            <div class="col-md-4">
                <input type="text" class="form-control"
                    placeholder="DV (Ej: 5)"
                    name="<?php echo $editando ? 'cliente_dv_up' : 'cliente_dv_reg'; ?>"
                    value="<?php echo $editando ? $campos['digito_v'] : ''; ?>">
            </div>
            <br><br>
            <div class="col-md-4">
                <input type="text" class="form-control"
                    placeholder="Nombre del cliente"
                    name="<?php echo $editando ? 'cliente_nombre_up' : 'cliente_nombre_reg'; ?>"
                    value="<?php echo $editando ? $campos['nombre_cliente'] : ''; ?>">
            </div>
            <br><br>
            <div class="col-md-4">
                <input type="text" class="form-control"
                    placeholder="Apellido del cliente"
                    name="<?php echo $editando ? 'cliente_apellido_up' : 'cliente_apellido_reg'; ?>"
                    value="<?php echo $editando ? $campos['apellido_cliente'] : ''; ?>">
            </div>
            <br><br>
            <div class="col-md-4">
                <input type="text" class="form-control"
                    placeholder="Teléfono (Ej: 0981...)"
                    name="<?php echo $editando ? 'cliente_telefono_up' : 'cliente_telefono_reg'; ?>"
                    value="<?php echo $editando ? $campos['celular_cliente'] : ''; ?>">
            </div>
            <br><br>
            <div class="col-md-4">
                <input type="email" class="form-control"
                    placeholder="Email (Ej: correo@mail.com)"
                    name="<?php echo $editando ? 'cliente_email_up' : 'cliente_email_reg'; ?>"
                    value="<?php echo $editando ? $campos['email_cliente'] : ''; ?>">
            </div>
            <br><br>
            <div class="col-md-4">
                <input type="text" class="form-control"
                    placeholder="Dirección del cliente"
                    name="<?php echo $editando ? 'cliente_direccion_up' : 'cliente_direccion_reg'; ?>"
                    value="<?php echo $editando ? $campos['direccion_cliente'] : ''; ?>">
            </div>
            <br><br>
            <div class="col-md-4">

                <?php
$ciudades = $ins_cliente->listar_ciudades_controlador_up();
                ?>

                <select class="form-control select2"
                    name="<?php echo $editando ? 'ciudad_up' : 'ciudad_reg'; ?>">

                    <option value="">Seleccione ciudad</option>

                    <?php
foreach ($ciudades as $ciu) { ?>

                        <option value="<?php echo $ciu['id_ciudad']; ?>"
                            <?php if ($editando && $campos['id_ciudad'] == $ciu['id_ciudad']) {
                                echo "selected";
                            }
                            ?>>
                            <?php echo $ciu['ciu_descri']; ?>
                        </option>

                    <?php
} ?>

                </select>

            </div>
            <br><br>
            <div class="col-md-4">
                <?php
$estadoCivil = $editando ? trim($campos['estado_civil']) : ""; ?>

                <select class="form-control select2"
                    name="<?php echo $editando ? 'cliente_estadoC_up' : 'cliente_estadoC_reg'; ?>">

                    <option value="">Estado civil</option>

                    <option value="Soltero" <?php echo ($estadoCivil == "Soltero") ? 'selected' : ''; ?>>
                        Soltero/a
                    </option>

                    <option value="Casado" <?php echo ($estadoCivil == "Casado") ? 'selected' : ''; ?>>
                        Casado/a
                    </option>

                    <option value="Viudo" <?php echo ($estadoCivil == "Viudo") ? 'selected' : ''; ?>>
                        Viudo/a
                    </option>

                    <option value="Divorciado" <?php echo ($estadoCivil == "Divorciado") ? 'selected' : ''; ?>>
                        Divorciado/a
                    </option>

                </select>
            </div>
        </div>
        <br><br>
        <p class="text-center mt-4">
            <button type="submit"
                class="btn btn-raised <?php echo $editando ? 'btn-success' : 'btn-info'; ?>">
                <?php echo $editando ? 'ACTUALIZAR' : 'GUARDAR'; ?>
            </button>

            <?php if ($editando) { ?>
                <a href="<?php echo SERVERURL; ?>cliente-nuevo/"
                    class="btn btn-raised btn-secondary">
                    CANCELAR
                </a>
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

        <input type="hidden" name="modulo" value="cliente">

        <div class="row">
            <div class="col-12 col-md-6">
                <input type="text"
                    class="form-control"
                    name="busqueda_inicial"
                    placeholder="Buscar cliente..."
                    value="<?php echo $_SESSION['busqueda_cliente'] ?? ''; ?>">
            </div>

            <div class="col-12 col-md-6">
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-search"></i> Buscar
                </button>

                <?php if (isset($_SESSION['busqueda_cliente'])) { ?>
                    <form class="FormularioAjax d-inline"
                        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
                        method="POST">

                        <input type="hidden" name="modulo" value="cliente">
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

    echo $ins_cliente->listar_cliente_controlador(
        $pag_actual,
        10,
        $pagina[0],
        $busqueda
    );

    ?>
</div>
