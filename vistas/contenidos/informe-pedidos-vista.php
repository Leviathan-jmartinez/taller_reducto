<div class="container-fluid">
    <h4 class="mb-3">
        <i class="fas fa-file-alt"></i> Informe de Pedidos
    </h4>

    <form action="<?= SERVERURL ?>ajax/reportesAjax.php"
        method="POST"
        target="_blank">

        <input type="hidden" name="accion" value="imprimir_reporte_pedidos">

        <div class="row">
            <div class="col-md-3">
                <label>Desde</label>
                <input type="date" name="desde" class="form-control">
            </div>

            <div class="col-md-3">
                <label>Hasta</label>
                <input type="date" name="hasta" class="form-control">
            </div>

            <div class="col-md-3">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="2">Procesado</option>
                    <option value="1">Pendiente</option>
                    <option value="0">Anulado</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-danger w-100">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </form>

</div>