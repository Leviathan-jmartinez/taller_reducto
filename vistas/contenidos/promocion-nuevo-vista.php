<div class="container-fluid">

    <h3 class="text-left">
        <i class="fas fa-tags"></i> &nbsp; REGISTRAR PROMOCIÓN
    </h3>

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/promocionAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="guardar_promocion">

        <!-- DATOS GENERALES -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos de la promoción</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label>Tipo</label>
                    <select name="tipo" id="tipoPromo" class="form-control" required>
                        <option value="">Seleccione</option>
                        <option value="PORCENTAJE">Porcentaje (%)</option>
                        <option value="MONTO_FIJO">Monto fijo</option>
                        <option value="PRECIO_FIJO">Precio fijo</option>
                    </select>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Valor</label>
                    <input type="number" step="0.01" name="valor" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </fieldset>

        <!-- VIGENCIA -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Vigencia</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Fecha inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label>Fecha fin</label>
                    <input type="date" name="fecha_fin" class="form-control" required>
                </div>
            </div>
        </fieldset>

        <!-- APLICACIÓN -->
        <fieldset class="border p-3 mb-4">
            <legend class="w-auto px-2">Aplicar a productos</legend>

            <div class="row">
                <div class="col-md-6">
                    <label>Buscar artículo</label>
                    <input type="text"
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
                    <ul class="list-group" id="articulos_seleccionados"></ul>
                </div>
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
<?php include_once "./vistas/inc/promocion.php"; ?>