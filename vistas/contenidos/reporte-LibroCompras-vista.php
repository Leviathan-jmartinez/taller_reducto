<?php
if (!mainModel::tienePermisoVista('compra.reportes.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/sucursalControlador.php";
$insSucursal = new sucursalControlador();
$sucursales = $insSucursal->listar_sucursales_controlador();

require_once "./controladores/proveedorControlador.php";
$insProveedor = new proveedorControlador();
$proveedores = $insProveedor->listar_proveedores_controlador();
?>

<div class="container-fluid">
    <div class="form-neon">

        <h4 class="text-center mb-3">
            <i class="fas fa-book"></i>
            &nbsp; Libro de Compras
        </h4>

        <form action="<?= SERVERURL ?>ajax/reportesAjax.php"
            method="POST"
            target="_blank"
            autocomplete="off">

            <input type="hidden" name="accion" value="imprimir_reporte_libro_compras">

            <div class="row">

                <div class="col-md-2">
                    <label>Desde</label>
                    <input type="date" name="desde" class="form-control">
                </div>

                <div class="col-md-2">
                    <label>Hasta</label>
                    <input type="date" name="hasta" class="form-control">
                </div>

                <div class="col-md-3">
                    <label>Proveedor</label>
                    <select name="proveedor" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($proveedores as $p): ?>
                            <option value="<?= $p['idproveedores'] ?>">
                                <?= $p['razon_social'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Anulado</option>
                    </select>
                </div>

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
                    <i class="fas fa-print"></i> &nbsp; Generar PDF
                </button>
            </div>

        </form>

    </div>
</div>