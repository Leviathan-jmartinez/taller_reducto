<?php

// Iniciar sesión solo si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Valor por defecto
if (!isset($_SESSION['tipo_ordencompra'])) {
    $_SESSION['tipo_ordencompra'] = "con_presupuesto";
}

$tipo = $_SESSION['tipo_ordencompra'];

// Si se envió un nuevo valor por POST, sobrescribe
if (isset($_POST['tipo_ordencompra'])) {
    $_SESSION['tipo_ordencompra'] = $_POST['tipo_ordencompra'];
    $tipo = $_SESSION['tipo_ordencompra'];
}


?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-plus fa-fw"></i> &nbsp; ORDENES DE COMPRA
        <?php if ($tipo == 'con_pedido') echo "(a partir de pedido)"; ?>
    </h3>
</div>

<!-- Menú superior -->
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>oc-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; GENERAR ORDEN DE COMPRA</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>oc-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTADOS DE ORDENES DE COMPRA</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>oc-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA</a>
        </li>
    </ul>
</div>


<!-- Contenido según tipo de presupuesto -->
<?php if ($tipo === 'sin_presupuesto') { ?>
    <!-- SIN PEDIDO: agregar proveedor y artículos manualmente -->
    <div class="text-center mb-3">
        <?php if (empty($_SESSION['Sdatos_proveedorPre'])) { ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalproveedorPre">
                <i class="fas fa-user-plus"></i> &nbsp; Agregar Proveedor
            </button>
        <?php } ?>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalArticuloPre">
            <i class="fas fa-box-open"></i> &nbsp; Agregar artículo
        </button>
    </div>
<?php } ?>



<style>
    .oc-header {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: space-between;
        align-items: center;
    }
</style>

<div class="container-fluid oc-wrapper">

    <!-- HEADER / BARRA SUPERIOR -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="oc-header">

                <!-- BUSCADOR -->
                <div style="flex: 1; min-width: 250px;">
                    <label class="form-label fw-bold">Buscar Presupuesto</label>
                    <input type="text" class="form-control" placeholder="Ej: Ejemplo SRL…">
                </div>

                <!-- ACCIONES -->
                <div class="oc-actions">
                    <button class="btn btn-dark">
                        Filtrar
                    </button>

                    <button class="btn btn-primary btn-lg">
                        + OC sin presupuesto
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- TABLA -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover text-center align-middle oc-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">N°</th>
                            <th>Proveedor</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($_SESSION['Sdatos_proveedorPre'])) { ?>
                            <tr>
                                <td colspan="6" class="text-muted py-4">
                                    Sin presupuestos disponibles
                                </td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td>00125</td>
                                <td>Ejemplo SRL</td>
                                <td>2025-11-28</td>
                                <td>1.250.000 Gs</td>
                                <td>
                                    <span class="badge bg-success">
                                        Aprobado
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm">
                                        Generar OC
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>





<!-- Modal de selección inicial -->
<div class="modal fade" id="ModalTipoPresupuesto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title">Seleccione tipo de presupuesto</h5>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <button type="submit" name="tipo_presupuesto" value="sin_pedido" class="btn btn-primary m-2">Sin pedido</button>
                    <button type="submit" name="tipo_presupuesto" value="con_pedido" class="btn btn-success m-2">Con pedido</button>
                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-raised btn-danger btn-sm" onclick="window.location.href='<?php echo SERVERURL; ?>presupuesto-lista/'">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL proveedor -->
<div class="modal fade" id="ModalproveedorPre" tabindex="-1" role="dialog" aria-labelledby="ModalproveedorPre" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalproveedorPre">Agregar Proovedor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <label for="input_proveedor" class="bmd-label-floating">RUC, RAZON SOCIAL</label>
                        <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" name="input_proveedor" id="input_proveedor" maxlength="30">
                    </div>
                </div>
                <br>
                <div class="container-fluid" id="tabla_proveedorPre">

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="buscar_proveedorPre()"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<!-- MODAL ITEM -->
<div class="modal fade" id="ModalArticuloPre" tabindex="-1" role="dialog" aria-labelledby="ModalArticuloPre" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalArticuloPre">Agregar Articulo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <label for="input_item" class="bmd-label-floating">Código, descripción</label>
                        <input type="text" pattern="[a-zA-z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" name="input_articulo" id="input_articulo" maxlength="30">

                    </div>
                </div>
                <br>
                <div class="container-fluid" id="tabla_articulosPre">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="buscar_articuloPre()"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<!-- MODAL ITEM -->
<div class="modal fade" id="ModalBuscarPedido" tabindex="-1" role="dialog" aria-labelledby="ModalBuscarPedido" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalBuscarPedido">Agregar Pedido</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <label for="input_item" class="bmd-label-floating">Código, Proveedor</label>
                        <input type="text" pattern="[a-zA-z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" name="input_pedido" id="input_pedido" maxlength="30">
                    </div>
                </div>
                <br>
                <div class="container-fluid" id="tabla_pedidosPre">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="buscar_pedidoPre()"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>