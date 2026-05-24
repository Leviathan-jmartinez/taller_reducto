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
        data-form="<?php echo $editando ? 'update' : 'save'; ?>"
        autocomplete="off">

        <?php if ($editando) { ?>
            <input type="hidden" name="cliente_id_up" value="<?php echo $id; ?>">
        <?php
} ?>
        <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
        <div class="row">

            <div class="col-md-4">
                <select class="form-control select2"
                    id="cliente_tipo_documento"
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
                    id="cliente_documento"
                    placeholder="Documento (Ej: 1234567)"
                    name="<?php echo $editando ? 'cliente_doc_up' : 'cliente_doc_reg'; ?>"
                    value="<?php echo $editando ? $campos['doc_number'] : ''; ?>"
                    pattern="[a-zA-Z0-9-]{3,20}"
                    maxlength="20"
                    title="Use de 3 a 20 caracteres: letras, numeros o guion">
            </div>
            <br><br>
            <div class="col-md-4">
                <input type="text" class="form-control"
                    id="cliente_dv"
                    placeholder="DV (Ej: 5)"
                    name="<?php echo $editando ? 'cliente_dv_up' : 'cliente_dv_reg'; ?>"
                    value="<?php echo $editando ? $campos['digito_v'] : ''; ?>"
                    pattern="[0-9]{1}"
                    maxlength="1"
                    inputmode="numeric"
                    title="Ingrese un solo digito">
            </div>
            <br><br>
            <div class="col-md-4">
                <input type="text" class="form-control"
                    id="cliente_nombre"
                    placeholder="Nombre del cliente"
                    name="<?php echo $editando ? 'cliente_nombre_up' : 'cliente_nombre_reg'; ?>"
                    value="<?php echo $editando ? $campos['nombre_cliente'] : ''; ?>"
                    pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{2,30}"
                    maxlength="30"
                    title="Use de 2 a 30 letras">
            </div>
            <br><br>
            <div class="col-md-4">
                <input type="text" class="form-control"
                    id="cliente_apellido"
                    placeholder="Apellido del cliente"
                    name="<?php echo $editando ? 'cliente_apellido_up' : 'cliente_apellido_reg'; ?>"
                    value="<?php echo $editando ? $campos['apellido_cliente'] : ''; ?>"
                    pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{2,30}"
                    maxlength="30"
                    title="Use de 2 a 30 letras">
            </div>
            <br><br>
            <div class="col-md-4">
                <input type="text" class="form-control"
                    placeholder="Teléfono (Ej: 0981...)"
                    name="<?php echo $editando ? 'cliente_telefono_up' : 'cliente_telefono_reg'; ?>"
                    value="<?php echo $editando ? $campos['celular_cliente'] : ''; ?>"
                    pattern="[0-9()+ -]{6,15}"
                    maxlength="15"
                    inputmode="tel"
                    title="Use de 6 a 15 caracteres: numeros, espacios, parentesis, mas o guion">
            </div>
            <br><br>
            <div class="col-md-4">
                <input type="email" class="form-control"
                    placeholder="Email (Ej: correo@mail.com)"
                    name="<?php echo $editando ? 'cliente_email_up' : 'cliente_email_reg'; ?>"
                    value="<?php echo $editando ? $campos['email_cliente'] : ''; ?>"
                    maxlength="50">
            </div>
            <br><br>
            <div class="col-md-4">
                <input type="text" class="form-control"
                    placeholder="Dirección del cliente"
                    name="<?php echo $editando ? 'cliente_direccion_up' : 'cliente_direccion_reg'; ?>"
                    value="<?php echo $editando ? $campos['direccion_cliente'] : ''; ?>"
                    pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,#°\/-]{3,50}"
                    maxlength="50"
                    title="Use de 3 a 50 caracteres validos para direccion">
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
            <?php if ($editando) { ?>
                <br><br>
                <div class="col-md-4">
                    <select class="form-control" name="cliente_estado_up">
                        <option value="">Estado</option>
                        <option value="1" <?php if (($campos['estado_cliente'] ?? 1) == 1) echo 'selected'; ?>>Activo</option>
                        <option value="0" <?php if (($campos['estado_cliente'] ?? 1) == 0) echo 'selected'; ?>>Inactivo</option>
                    </select>
                </div>
            <?php } ?>
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
            <?php } else { ?>
                <button type="reset" class="btn btn-raised btn-secondary">
                    CANCELAR
                </button>
            <?php
} ?>
        </p>

    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tipo = document.getElementById('cliente_tipo_documento');
        var documento = document.getElementById('cliente_documento');
        var dv = document.getElementById('cliente_dv');
        var nombre = document.getElementById('cliente_nombre');
        var apellido = document.getElementById('cliente_apellido');

        function actualizarDocumentoCliente() {
            if (!tipo || !documento || !dv) {
                return;
            }
            if (apellido) {
                apellido.placeholder = 'Apellido del cliente';
            }
            if (nombre) {
                nombre.pattern = '[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{2,30}';
                nombre.title = 'Use de 2 a 30 letras';
                nombre.placeholder = 'Nombre del cliente';
            }

            if (tipo.value === 'CI') {
                documento.pattern = '[0-9]{5,10}';
                documento.maxLength = 10;
                documento.title = 'Ingrese de 5 a 10 digitos';
                documento.placeholder = 'Documento (Ej: 1234567)';
            } else if (tipo.value === 'RUC') {
                documento.pattern = '[0-9]{6,12}';
                documento.maxLength = 12;
                documento.title = 'Ingrese el RUC sin digito verificador';
                documento.placeholder = 'RUC sin DV (Ej: 80012345)';
                if (apellido) {
                    apellido.placeholder = 'Apellido (opcional para RUC)';
                }
                if (nombre) {
                    nombre.pattern = '[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,&-]{2,30}';
                    nombre.title = 'Use hasta 30 caracteres para la razon social';
                    nombre.placeholder = 'Razon social';
                }
            } else if (tipo.value === 'PASAPORTE') {
                documento.pattern = '[a-zA-Z0-9]{3,20}';
                documento.maxLength = 20;
                documento.title = 'Ingrese de 3 a 20 letras o numeros';
                documento.placeholder = 'Pasaporte';
            } else {
                documento.pattern = '[a-zA-Z0-9-]{3,20}';
                documento.maxLength = 20;
                documento.title = 'Use de 3 a 20 caracteres: letras, numeros o guion';
                documento.placeholder = 'Documento';
            }
        }

        if (tipo) {
            tipo.addEventListener('change', actualizarDocumentoCliente);
            actualizarDocumentoCliente();
        }
    });
</script>

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
                    <button type="submit" name="eliminar_busqueda" value="1" class="btn btn-danger">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
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
