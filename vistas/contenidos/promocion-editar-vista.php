<?php
require_once "./controladores/promocionControlador.php";
$insPromocion = new promocionControlador();

if (!isset($pagina[1])) {
    echo '<div class="alert alert-danger">ID de promoción inválido</div>';
    exit;
}

$id = $pagina[1];

$promo     = $insPromocion->datos_promocion_controlador($id);
$articulos = $insPromocion->articulos_promocion_controlador($id);

if (!$promo) {
    echo '<div class="alert alert-danger">Promoción no encontrada</div>';
    exit;
}
?>

<div class="container-fluid">

    <h3 class="text-left">
        <i class="fas fa-tags"></i> &nbsp; EDITAR PROMOCIÓN
    </h3>

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/promocionAjax.php"
        method="POST"
        data-form="update"
        autocomplete="off">

        <input type="hidden" name="accion" value="editar_promocion">
        <input type="hidden" name="id_promocion" value="<?= $id ?>">

        <!-- DATOS GENERALES -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos de la promoción</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Nombre</label>
                    <input type="text"
                        name="nombre"
                        class="form-control"
                        value="<?= $promo['nombre'] ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label>Tipo</label>
                    <select name="tipo" id="tipoPromo" class="form-control" required>
                        <option value="">Seleccione</option>
                        <option value="PORCENTAJE" <?= $promo['tipo'] == 'PORCENTAJE' ? 'selected' : '' ?>>
                            Porcentaje (%)
                        </option>
                        <option value="MONTO_FIJO" <?= $promo['tipo'] == 'MONTO_FIJO' ? 'selected' : '' ?>>
                            Monto fijo
                        </option>
                        <option value="PRECIO_FIJO" <?= $promo['tipo'] == 'PRECIO_FIJO' ? 'selected' : '' ?>>
                            Precio fijo
                        </option>
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
                        value="<?= $promo['valor'] ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label>Descripción</label>
                    <textarea name="descripcion"
                        class="form-control"
                        rows="2"><?= $promo['descripcion'] ?></textarea>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="form-check">
                        <input class="form-check-input"
                            type="checkbox"
                            id="estadoPromo"
                            name="estado"
                            value="1"
                            <?= $promo['estado'] == 1 ? 'checked' : '' ?>>

                        <label class="form-check-label" for="estadoPromo">
                            Promoción activa
                        </label>
                    </div>
                </div>
            </div>

        </fieldset>

        <!-- VIGENCIA -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Vigencia</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Fecha inicio</label>
                    <input type="date"
                        name="fecha_inicio"
                        class="form-control"
                        value="<?= $promo['fecha_inicio'] ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label>Fecha fin</label>
                    <input type="date"
                        name="fecha_fin"
                        class="form-control"
                        value="<?= $promo['fecha_fin'] ?>"
                        required>
                </div>
            </div>
        </fieldset>

        <!-- APLICACIÓN -->
        <fieldset class="border p-3 mb-4">
            <legend class="w-auto px-2">Aplicar a productos</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Buscar artículo</label>
                    <input type="search "
                        id="buscar_articulo"
                        class="form-control"
                        placeholder="Buscar por descripción"
                        onkeyup="buscarArticuloPromo()">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <h6>Resultados</h6>
                    <div id="resultado_articulos"></div>
                </div>

                <div class="col-md-6">
                    <h6>Artículos seleccionados</h6>
                    <ul class="list-group" id="articulos_seleccionados">
                        <?php foreach ($articulos as $a): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center"
                                id="articulo_<?= $a['id_articulo'] ?>">
                                <?= $a['desc_articulo'] ?>
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

        <!-- BOTONES -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Actualizar
            </button>

            <a href="<?php echo SERVERURL; ?>promocion-lista/"
                class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

    </form>
</div>
<?php include_once "./vistas/inc/promocion.php"; ?>