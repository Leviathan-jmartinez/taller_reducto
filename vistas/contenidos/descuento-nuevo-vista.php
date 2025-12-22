<div class="container-fluid">

    <h3 class="text-left">
        <i class="fas fa-percent"></i> &nbsp; REGISTRAR DESCUENTO
    </h3>

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/descuentoAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="guardar_descuento">
        <input type="hidden" name="es_reutilizable" value="1">

        <!-- DATOS DEL DESCUENTO -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos del descuento</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Nombre</label>
                    <input type="text"
                        name="nombre"
                        class="form-control"
                        required
                        placeholder="Ej: Cliente VIP">
                </div>

                <div class="col-md-6">
                    <label>Tipo</label>
                    <select name="tipo" class="form-control" required>
                        <option value="">Seleccione</option>
                        <option value="PORCENTAJE">Porcentaje (%)</option>
                        <option value="MONTO_FIJO">Monto fijo</option>
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
                        required>
                </div>

                <div class="col-md-6">
                    <label>Descripción</label>
                    <textarea name="descripcion"
                        class="form-control"
                        rows="2"
                        placeholder="Motivo o condición del descuento"></textarea>
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
                    checked>
                <label class="form-check-label">
                    Descuento activo
                </label>
            </div>
        </fieldset>

        <!-- BOTONES -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar
            </button>

            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-undo"></i> Limpiar
            </button>
        </div>

    </form>
</div>

<?php include_once "./vistas/inc/descuentos.php"; ?>