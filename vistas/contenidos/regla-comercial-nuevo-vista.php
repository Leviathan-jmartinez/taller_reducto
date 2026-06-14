<?php
$vistaActual = $_GET['vista'] ?? '';
$pagina = explode('/', trim($vistaActual, '/'));
$idRegla = $pagina[1] ?? '';
$esEditar = $idRegla !== '';

if ($esEditar) {
    if (!mainModel::tienePermiso('servicio.regla_comercial.editar')) {
        echo '<div class="alert alert-danger">Acceso no autorizado</div>';
        return;
    }
} elseif (!mainModel::tienePermiso('servicio.regla_comercial.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/reglaComercialControlador.php";

$insRegla = new reglaComercialControlador();
$regla = [];
$condiciones = [];
$descuentos = [];

if ($esEditar) {
    $regla = $insRegla->datos_regla_controlador($idRegla);

    if (!$regla) {
        echo '<div class="alert alert-danger">Regla comercial no encontrada</div>';
        return;
    }

    $condiciones = $insRegla->condiciones_regla_controlador($idRegla);
    $descuentos = $insRegla->descuentos_regla_controlador($idRegla);
}

$sucursales = mainModel::conectar()
    ->query("SELECT id_sucursal, suc_descri FROM sucursales WHERE estado = 1 ORDER BY suc_descri")
    ->fetchAll(PDO::FETCH_ASSOC);

$accion = $esEditar ? 'editar_regla' : 'guardar_regla';
$titulo = $esEditar ? 'EDITAR REGLA COMERCIAL' : 'REGISTRAR REGLA COMERCIAL';
$dataForm = $esEditar ? 'update' : 'save';
$botonTexto = $esEditar ? 'Actualizar' : 'Guardar';
?>

<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-project-diagram"></i> &nbsp; <?= $titulo ?>
    </h3>

    <div class="container-fluid">
        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a class="<?= !$esEditar ? 'active' : '' ?>" href="<?= SERVERURL; ?>regla-comercial-nuevo/">
                    <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVA REGLA
                </a>
            </li>
            <li>
                <a href="<?= SERVERURL; ?>regla-comercial-lista/">
                    <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE REGLAS
                </a>
            </li>
        </ul>
    </div>

    <form class="form-neon FormularioAjax app-form"
        action="<?= SERVERURL; ?>ajax/reglaComercialAjax.php"
        method="POST"
        data-modulo="reglas_comerciales"
        data-form="<?= $dataForm ?>"
        autocomplete="off">

        <input type="hidden" name="accion" value="<?= $accion ?>">
        <input type="hidden" name="condiciones_json" id="condiciones_json">
        <input type="hidden" name="descuentos_json" id="descuentos_json">
        <?php if ($esEditar): ?>
            <input type="hidden" name="id_regla" value="<?= htmlspecialchars($idRegla, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos de la regla</legend>
            <div class="row">
                <div class="col-md-6">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control"
                        value="<?= htmlspecialchars($regla['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-3">
                    <label>Fecha inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control"
                        value="<?= htmlspecialchars($regla['fecha_inicio'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-3">
                    <label>Fecha fin</label>
                    <input type="date" name="fecha_fin" class="form-control"
                        value="<?= htmlspecialchars($regla['fecha_fin'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-5">
                    <label>Descripcion</label>
                    <textarea name="descripcion" class="form-control" rows="2"><?= htmlspecialchars($regla['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="col-md-3">
                    <label>Sucursal</label>
                    <select name="id_sucursal" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($sucursales as $s): ?>
                            <option value="<?= $s['id_sucursal'] ?>" <?= (($regla['id_sucursal'] ?? '') == $s['id_sucursal']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['suc_descri'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Prioridad</label>
                    <input type="number" name="prioridad" class="form-control"
                        value="<?= htmlspecialchars($regla['prioridad'] ?? '0', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-2">
                    <label>Competencia</label>
                    <select name="modo_competencia" class="form-control">
                        <?php
                        $modoActual = $regla['modo_competencia'] ?? 'COMPITE_MISMO_ALCANCE';
                        $modosCompetencia = [
                            'COMPITE_MISMO_ALCANCE' => 'Compite mismo alcance',
                            'NO_COMPITE' => 'No compite',
                            'EXCLUSIVA' => 'Exclusiva'
                        ];
                        foreach ($modosCompetencia as $valor => $texto):
                        ?>
                            <option value="<?= $valor ?>" <?= $modoActual === $valor ? 'selected' : '' ?>>
                                <?= $texto ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Estado</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="estadoRegla" name="estado" value="1"
                            <?= (($regla['estado'] ?? 1) == 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="estadoRegla">Activa</label>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Condiciones</legend>
            <div class="row">
                <div class="col-md-3">
                    <label>Tipo</label>
                    <select id="cond_tipo" class="form-control">
                        <option value="CLIENTE">Cliente</option>
                        <option value="ARTICULO">Articulo</option>
                        <option value="CATEGORIA">Categoria</option>
                        <option value="TOTAL_OPERACION">Total operacion</option>
                        <option value="CANTIDAD_ITEMS">Cantidad items</option>
                        <option value="SUCURSAL">Sucursal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Operador</label>
                    <select id="cond_operador" class="form-control">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value=">=">>=</option>
                        <option value="&lt;=">&lt;=</option>
                        <option value=">">></option>
                        <option value="&lt;">&lt;</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>ID referencia</label>
                    <input type="number" id="cond_valor_ref" class="form-control" placeholder="Ej: id_cliente, id_articulo">
                </div>
                <div class="col-md-3">
                    <label>Valor/nota</label>
                    <input type="text" id="cond_valor_texto" class="form-control" placeholder="Ej: 500000">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-success btn-sm" onclick="agregarCondicionRegla()">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-sm">
                    <thead>
                        <tr class="text-center">
                            <th>Tipo</th>
                            <th>Operador</th>
                            <th>ID ref.</th>
                            <th>Valor</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tabla_condiciones"></tbody>
                </table>
            </div>
        </fieldset>

        <fieldset class="border p-3 mb-4">
            <legend class="w-auto px-2">Descuentos aplicables</legend>
            <div class="row">
                <div class="col-md-3">
                    <label>Nombre</label>
                    <input type="text" id="desc_nombre" class="form-control" placeholder="Ej: 10% por campana">
                </div>
                <div class="col-md-2">
                    <label>Tipo</label>
                    <select id="desc_tipo" class="form-control">
                        <option value="PORCENTAJE">Porcentaje</option>
                        <option value="MONTO_FIJO">Monto fijo</option>
                        <option value="PRECIO_FIJO">Precio fijo</option>
                        <option value="NXM">Lleva N paga M</option>
                        <option value="GRATIS">Gratis</option>
                    </select>
                </div>
                <div class="col-md-2" id="grupo_desc_valor">
                    <label>Valor</label>
                    <input type="number" step="0.01" id="desc_valor" class="form-control">
                </div>
                <div class="col-md-1 d-none" id="grupo_desc_requerida">
                    <label>Lleva</label>
                    <input type="number" step="1" min="1" id="desc_cantidad_requerida" class="form-control" placeholder="2">
                </div>
                <div class="col-md-1 d-none" id="grupo_desc_cobrada">
                    <label>Paga</label>
                    <input type="number" step="1" min="1" id="desc_cantidad_cobrada" class="form-control" placeholder="1">
                </div>
                <div class="col-md-2">
                    <label>Aplica a</label>
                    <select id="desc_aplica_a" class="form-control">
                        <option value="TOTAL">Total</option>
                        <option value="LINEA">Linea</option>
                        <option value="ARTICULO">Articulo</option>
                        <option value="CATEGORIA">Categoria</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Alcance ref.</label>
                    <input type="number" id="desc_alcance_ref" class="form-control" placeholder="Opcional">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-success btn-sm" onclick="agregarDescuentoRegla()">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-sm">
                    <thead>
                        <tr class="text-center">
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>N x M</th>
                            <th>Aplica a</th>
                            <th>Alcance</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tabla_descuentos_regla"></tbody>
                </table>
            </div>
        </fieldset>

        <div class="text-center">
            <button type="submit" class="btn btn-info btn-raised">
                <i class="fas fa-save"></i> &nbsp; <?= $botonTexto ?>
            </button>
            <a href="<?= SERVERURL; ?>regla-comercial-lista/" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </form>
</div>

<script>
    window.REGLA_CONDICIONES_INICIALES = <?= json_encode($condiciones, JSON_UNESCAPED_UNICODE) ?>;
    window.REGLA_DESCUENTOS_INICIALES = <?= json_encode($descuentos, JSON_UNESCAPED_UNICODE) ?>;
</script>
<?php include_once "./vistas/inc/reglaComercial.php"; ?>
