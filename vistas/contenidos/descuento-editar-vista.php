<?php
require_once __DIR__ . "/../../controladores/descuentoControlador.php";

$insDescuento = new descuentoControlador();
$descuento = $insDescuento->datos_descuento_controlador($pagina[1]);

if (!$descuento) {
    echo '<div class="alert alert-danger">Descuento no encontrado</div>';
    return;
}
?>

<div class="container-fluid">

    <!-- VOLVER -->
    <div class="mb-3">
        <a href="<?= SERVERURL; ?>descuento-lista/" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>

    <h3 class="text-left">
        <i class="fas fa-percent"></i> &nbsp; EDITAR DESCUENTO
    </h3>

    <form class="form-neon FormularioAjax"
        action="<?= SERVERURL; ?>ajax/descuentoAjax.php"
        method="POST"
        data-form="update"
        autocomplete="off">

        <input type="hidden" name="accion" value="editar_descuento">
        <input type="hidden" name="id_descuento"
            value="<?= $insDescuento->encryption($descuento['id_descuento']); ?>">

        <!-- DATOS -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos del descuento</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control"
                        value="<?= $descuento['nombre']; ?>" required>
                </div>

                <div class="col-md-6">
                    <label>Tipo</label>
                    <select name="tipo" class="form-control" required>
                        <option value="PORCENTAJE"
                            <?= $descuento['tipo'] == 'PORCENTAJE' ? 'selected' : '' ?>>
                            Porcentaje (%)
                        </option>
                        <option value="MONTO_FIJO"
                            <?= $descuento['tipo'] == 'MONTO_FIJO' ? 'selected' : '' ?>>
                            Monto fijo
                        </option>
                    </select>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Valor</label>
                    <input type="number" step="0.01" name="valor"
                        class="form-control"
                        value="<?= $descuento['valor']; ?>" required>
                </div>

                <div class="col-md-6">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control"
                        rows="2"><?= $descuento['descripcion']; ?></textarea>
                </div>
            </div>
        </fieldset>

        <!-- ESTADO -->
        <fieldset class="border p-3 mb-4">
            <legend class="w-auto px-2">Estado</legend>

            <div class="form-check">
                <input class="form-check-input"
                    type="checkbox"
                    name="estado"
                    value="1"
                    <?= $descuento['estado'] == 1 ? 'checked' : '' ?>>
                <label class="form-check-label">
                    Descuento activo
                </label>
            </div>
        </fieldset>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar cambios
            </button>
        </div>

    </form>
</div>