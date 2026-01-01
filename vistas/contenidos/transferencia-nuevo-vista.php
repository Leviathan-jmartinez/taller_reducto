<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sucursalesDestino = $sucursalesDestino ?? [];
$productosStock    = $productosStock ?? [];
?>

<div class="container-fluid">
    <div class="card shadow-sm">

        <!-- HEADER -->
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-exchange-alt"></i>
                Traspaso Directo entre Sucursales
            </h5>
        </div>

        <form class="FormularioAjax"
            action="<?= SERVERURL ?>ajax/transferenciaAjax.php"
            method="POST"
            data-form="save">

            <input type="hidden" name="accion" value="crear_transferencia">

            <div class="card-body">

                <!-- ================= DATOS DE SALIDA ================= -->
                <div class="border rounded p-3 mb-4">
                    <h6 class="text-info mb-3">
                        <i class="fas fa-truck"></i> Datos de la Salida
                    </h6>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Observación</label>
                            <textarea name="observacion" class="form-control" required
                                placeholder="Motivo del traslado"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label>Local destino</label>

                            <input type="hidden" name="sucursal_destino" id="sucursal_destino">

                            <input type="text"
                                id="buscar_sucursal"
                                class="form-control"
                                placeholder="Buscar local destino">

                            <div id="resultado_sucursal" class="mt-1"></div>
                        </div>

                    </div>



                </div>
                <div class="border rounded p-3 mb-4">
                    <h6 class="text-info mb-3">
                        <i class="fas fa-truck"></i> Datos del Transporte
                    </h6>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Transportista</label>
                            <input type="text" name="transportista"
                                class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label>RUC Transportista</label>
                            <input type="text" name="ruc_transport"
                                class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Chofer</label>
                            <input type="text" name="nombre_transpo"
                                class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>CI Chofer</label>
                            <input type="text" name="ci_transpo"
                                class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Celular Chofer</label>
                            <input type="text" name="cel_transpo"
                                class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Chapa</label>
                            <input type="text" name="vehichapa"
                                class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Marca del vehículo</label>
                            <input type="text" name="vehimarca"
                                class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>Modelo del vehículo</label>
                            <input type="text" name="vehimodelo"
                                class="form-control">
                        </div>
                    </div>
                </div>
                <div class="border rounded p-3 mb-4">
                    <h6 class="text-info mb-3">
                        <i class="fas fa-calendar-alt"></i> Fechas de Traslado
                    </h6>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Fecha de envío</label>
                            <input type="date"
                                name="fechaenvio"
                                class="form-control"
                                value="<?= date('Y-m-d') ?>"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label>Fecha estimada de llegada</label>
                            <input type="date"
                                name="fechallegada"
                                class="form-control"
                                value="<?= date('Y-m-d') ?>"
                                required>
                        </div>
                    </div>
                </div>


                <!-- ================= PRODUCTOS ================= -->
                <div class="border rounded p-3 mb-4">
                    <h6 class="text-info mb-3">
                        <i class="fas fa-boxes"></i> Productos a Transferir
                    </h6>
                    <div class="border rounded p-3 mb-4">
                        <h6 class="text-info mb-3">
                            <i class="fas fa-search"></i> Buscar productos
                        </h6>

                        <!-- BUSCADOR -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <input type="text"
                                    id="buscar_producto"
                                    class="form-control"
                                    placeholder="Código o descripción del producto">
                            </div>
                            <div class="col-md-4">
                                <button type="button"
                                    class="btn btn-primary btn-block"
                                    onclick="buscarProducto()">
                                    Buscar
                                </button>
                            </div>
                        </div>

                        <!-- RESULTADOS -->
                        <div id="resultado_busqueda"></div>

                        <hr>

                        <!-- DETALLE -->
                        <h6>Detalle de transferencia</h6>
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Producto</th>
                                    <th width="120">Cantidad</th>
                                    <th width="60"></th>
                                </tr>
                            </thead>
                            <tbody id="detalle_productos"></tbody>
                        </table>
                    </div>


                    <small class="text-muted">
                        Solo se transferirán los productos con cantidad mayor a 0.
                    </small>
                </div>

                <!-- ================= CONFIRMACIÓN ================= -->
                <div class="border rounded p-3">
                    <h6 class="text-info mb-3">
                        <i class="fas fa-check-circle"></i> Confirmación
                    </h6>

                    <div class="d-flex justify-content-between">
                        <a href="<?= SERVERURL ?>dashboard/" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i>
                            Confirmar Envío
                        </button>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

<?php include "./vistas/inc/transferenciaJS.php"; ?>