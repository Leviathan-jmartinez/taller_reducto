<?php
$vistaActual = $_GET['vista'] ?? '';
$pagina = explode('/', trim($vistaActual, '/'));
$idPromocion = $pagina[1] ?? '';
$esEditar = $idPromocion !== '';

if ($esEditar) {
    if (!mainModel::tienePermiso('servicio.promocion.editar')) {
        echo '<div class="alert alert-danger">Acceso no autorizado</div>';
        return;
    }
} elseif (!mainModel::tienePermiso('servicio.promocion.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/promocionControlador.php";

$insPromocion = new promocionControlador();
$promo = [];
$articulos = [];

if ($esEditar) {
    $promo = $insPromocion->datos_promocion_controlador($idPromocion);

    if (!$promo) {
        echo '<div class="alert alert-danger">Promocion no encontrada</div>';
        return;
    }

    $articulos = $insPromocion->articulos_promocion_controlador($idPromocion);
}

$sucursales = mainModel::conectar()
    ->query("SELECT id_sucursal, suc_descri FROM sucursales WHERE estado = 1 ORDER BY suc_descri")
    ->fetchAll(PDO::FETCH_ASSOC);

$accion = $esEditar ? 'editar_promocion' : 'guardar_promocion';
$titulo = $esEditar ? 'EDITAR PROMOCION' : 'REGISTRAR PROMOCION';
$dataForm = $esEditar ? 'update' : 'save';
$botonTexto = $esEditar ? 'Actualizar' : 'Guardar';
$botonClase = $esEditar ? 'btn-primary' : 'btn-info btn-raised';
?>

<div class="container-fluid">

    <h3 class="text-left">
        <i class="fas fa-tags"></i> &nbsp; <?= $titulo ?>
    </h3>

    <div class="container-fluid">
        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a class="<?= !$esEditar ? 'active' : '' ?>" href="<?php echo SERVERURL; ?>promocion-nuevo/">
                    <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVA PROMOCION
                </a>
            </li>
            <li>
                <a href="<?php echo SERVERURL; ?>promocion-lista/">
                    <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE PROMOCIONES
                </a>
            </li>
        </ul>
    </div>

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/promocionAjax.php"
        method="POST"
        data-modulo="promociones"
        data-form="<?= $dataForm ?>"
        autocomplete="off">

        <input type="hidden" name="accion" value="<?= $accion ?>">
        <?php if ($esEditar): ?>
            <input type="hidden" name="id_promocion" value="<?= htmlspecialchars($idPromocion, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos de la promocion</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Nombre</label>
                    <input type="text"
                        name="nombre"
                        class="form-control"
                        value="<?= htmlspecialchars($promo['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label>Tipo</label>
                    <select name="tipo" id="tipoPromo" class="form-control" required>
                        <option value="">Seleccione</option>
                        <?php foreach (['PORCENTAJE' => 'Porcentaje (%)', 'MONTO_FIJO' => 'Monto fijo', 'PRECIO_FIJO' => 'Precio fijo'] as $valor => $texto): ?>
                            <option value="<?= $valor ?>" <?= (($promo['tipo'] ?? '') === $valor) ? 'selected' : '' ?>>
                                <?= $texto ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Valor</label>
                    <input type="number"
                        step="0.01"
                        name="valor"
                        class="form-control"
                        value="<?= htmlspecialchars($promo['valor'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label>Descripcion</label>
                    <textarea name="descripcion"
                        class="form-control"
                        rows="2"><?= htmlspecialchars($promo['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

            <?php if ($esEditar): ?>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input"
                                type="checkbox"
                                id="estadoPromo"
                                name="estado"
                                value="1"
                                <?= (($promo['estado'] ?? 1) == 1) ? 'checked' : '' ?>>

                            <label class="form-check-label" for="estadoPromo">
                                Promocion activa
                            </label>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </fieldset>

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Vigencia</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Fecha inicio</label>
                    <input type="date"
                        name="fecha_inicio"
                        class="form-control"
                        value="<?= htmlspecialchars($promo['fecha_inicio'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label>Fecha fin</label>
                    <input type="date"
                        name="fecha_fin"
                        class="form-control"
                        value="<?= htmlspecialchars($promo['fecha_fin'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Sucursal</label>
                    <select name="id_sucursal" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($sucursales as $s): ?>
                            <option value="<?= $s['id_sucursal'] ?>" <?= (($promo['id_sucursal'] ?? '') == $s['id_sucursal']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['suc_descri'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </fieldset>

        <fieldset class="border p-3 mb-4">
            <legend class="w-auto px-2">Aplicar a productos</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Buscar articulo</label>
                    <input type="text"
                        id="buscar_articulo"
                        class="form-control"
                        placeholder="Buscar por descripcion"
                        onkeyup="buscarArticuloPromo()">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <h6>Resultados</h6>
                    <div id="resultado_articulos"></div>
                </div>

                <div class="col-md-6">
                    <h6>Articulos seleccionados</h6>
                    <ul class="list-group" id="articulos_seleccionados">
                        <?php foreach ($articulos as $a): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center"
                                id="articulo_<?= $a['id_articulo'] ?>">
                                <?= htmlspecialchars($a['desc_articulo'], ENT_QUOTES, 'UTF-8') ?>
                                <input type="hidden" name="articulos[]" value="<?= $a['id_articulo'] ?>">
                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="quitarArticuloPromo(<?= $a['id_articulo'] ?>)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </fieldset>

        <div class="text-center">
            <button type="submit" class="btn <?= $botonClase ?>">
                <i class="fas fa-save"></i> &nbsp; <?= $botonTexto ?>
            </button>

            <?php if ($esEditar): ?>
                <a href="<?php echo SERVERURL; ?>promocion-lista/" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            <?php else: ?>
                <button type="reset" class="btn btn-secondary btn-raised">
                    <i class="fas fa-times"></i> &nbsp; Cancelar
                </button>
            <?php endif; ?>
        </div>

    </form>
</div>

<script>
    window.PROMOCION_FORM_MODO = "<?= $esEditar ? 'editar' : 'crear' ?>";
</script>
<?php include_once "./vistas/inc/promocion.php"; ?>
