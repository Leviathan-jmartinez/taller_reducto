<?php
if (!mainModel::tienePermiso('reportes.pedidos.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/reportesControlador.php";
$rep = new reporteControlador();
$sucursales = $rep->listar_sucursales_controlador();
?>

<div class="container-fluid">
    <div class="form-neon">

        <h4 class="text-center mb-3">
            <i class="fas fa-clipboard-list fa-fw"></i>
            &nbsp; Informe de Pedidos de Compra
        </h4>

        <form id="formPreview"
            class="form-neon"
            data-pdf-action="imprimir_reporte_pedidos"
            autocomplete="off">

            <input type="hidden" name="modulo" value="pedidos">

            <div class="row">

                <!-- Desde -->
                <div class="col-md-3">
                    <label>Desde</label>
                    <input type="date" name="desde" class="form-control">
                </div>

                <!-- Hasta -->
                <div class="col-md-3">
                    <label>Hasta</label>
                    <input type="date" name="hasta" class="form-control">
                </div>

                <!-- Estado -->
                <div class="col-md-3">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="1">Pendiente</option>
                        <option value="2">Procesado</option>
                        <option value="0">Anulado</option>
                    </select>
                </div>

                <!-- Sucursal -->
                <div class="col-md-3">
                    <label>Sucursal</label>
                    <select name="sucursal" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($sucursales as $s): ?>
                            <option value="<?= $s['id_sucursal'] ?>">
                                <?= $s['suc_descri'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-search"></i> &nbsp; Previsualizar
                </button>

                <button type="button" id="btnPdf" class="btn btn-secondary d-none">
                    <i class="fas fa-print"></i> &nbsp; Generar PDF
                </button>
            </div>

        </form>

    </div>
</div>

<?php include_once "./vistas/inc/reportePreviewTabla.php"; ?>
