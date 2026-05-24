<?php
if (!mainModel::tienePermiso('compra.presupuesto.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>

<div class="container-fluid form-neon">

    <h3 class="text-left">
        <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PRESUPUESTO
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>presupuesto-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PRESUPUESTO</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>presupuesto-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR PRESUPUESTOS</a>
        </li>
    </ul>

    <?php if (empty($_SESSION['Cdatos_articuloPre'])) { ?>
        <div class="text-center mb-3">
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#ModalBuscarPedido">
                <i class="fas fa-search"></i> &nbsp; Buscar Pedido
            </button>
        </div>
    <?php } ?>

    <div class="col-12 col-md-6">
        <span class="roboto-medium">PROVEEDOR:</span>
        <?php if (empty($_SESSION['Cdatos_proveedorPre'])) { ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalproveedorPre">
                <i class="fas fa-user-plus"></i> &nbsp; Agregar Proveedor
            </button>
        <?php } else { ?>
            <form class="FormularioAjax d-inline-block" action="<?php echo SERVERURL ?>ajax/presupuestoAjax.php" method="POST" data-form="loans">
                <input type="hidden" name="id_eliminar_proveedorPre" value="<?php echo $_SESSION['Cdatos_proveedorPre']['ID']; ?>">
                <?php echo $_SESSION['Cdatos_proveedorPre']['RAZON'] . " (" . $_SESSION['Cdatos_proveedorPre']['RUC'] . ")"; ?>
                <button type="submit" class="btn btn-danger"><i class="fas fa-user-times"></i></button>
            </form>
        <?php } ?>
    </div>


    <div class="table-responsive mt-3">
        <table class="table table-dark table-sm">
            <thead>
                <tr class="text-center roboto-medium">
                    <th>#</th>
                    <th>CODIGO</th>
                    <th>DESCRIPCION</th>
                    <th>CANTIDAD</th>
                    <th>PRECIO UNITARIO</th>
                    <th>SUBTOTAL</th>
                    <th>ELIMINAR</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_SESSION['Cdatos_articuloPre']) && count($_SESSION['Cdatos_articuloPre']) >= 1) {
                    $_SESSION['presupuesto_articulo'] = 0;
                    $_SESSION['total_pre'] = 0;
                    $contador = 1;
                    foreach ($_SESSION['Cdatos_articuloPre'] as $article):
                        $_SESSION['presupuesto_articulo'] += $article['cantidad'];
                        $_SESSION['total_pre'] += $article['subtotal'];
                ?>
                        <tr class="text-center">
                            <td><?php echo $contador++; ?></td>
                            <td><?php echo $article['codigo']; ?></td>
                            <td><?php echo $article['descripcion']; ?></td>
                            <td><?php echo $article['cantidad']; ?></td>
                            <td>
                                <input type="text" class="form-control precio-articulo text-center"
                                    data-id="<?php echo $article['ID']; ?>"
                                    value="<?php echo number_format($article['precio'], 0, ',', '.'); ?>">
                            </td>
                            <td class="subtotal-articulo" data-id="<?php echo $article['ID']; ?>">
                                <?php echo number_format($article['subtotal'], 0, ',', '.'); ?>
                            </td>
                            <td>
                                <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/presupuestoAjax.php" method="POST" data-form="loans">
                                    <input type="hidden" name="id_eliminar_articuloPre" value="<?php echo $article['ID']; ?>">
                                    <button type="submit" class="btn btn-warning"><i class="far fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="text-center bg-light total-fila">
                        <td><strong>TOTAL</strong></td>
                        <td colspan="2"></td>
                        <td id="total-unidades"><strong><?php echo $_SESSION['presupuesto_articulo']; ?> unidades</strong></td>
                        <td></td>
                        <td id="total-general"><strong><?php echo number_format($_SESSION['total_pre'], 0, ',', '.'); ?></strong></td>
                        <td></td>
                    </tr>
                <?php
                } else {
                    $_SESSION['presupuesto_articulo'] = 0;
                ?>
                    <tr class="text-center bg-light">
                        <td colspan="7">No has seleccionado articulos</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="container-fluid mt-3">
        <form class="FormularioAjax" action="<?php echo SERVERURL ?>ajax/presupuestoAjax.php"
            method="POST" data-form="save" autocomplete="off">

            <div class="row">
                <div class="col-12 text-md-left mb-3">
                    <label for="fecha_vencimientoPre">Fecha Vencimiento</label>
                    <input type="date" class="form-control d-inline-block"
                        name="fecha_vencimientoPre" id="fecha_vencimientoPre"
                        min="<?php echo date('Y-m-d'); ?>"
                        style="width: 180px;" required>
                </div>
            </div>

            <input type="hidden" name="agregar_presupuesto" value="1">

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-raised btn-info btn-sm">
                    <i class="far fa-save"></i> &nbsp; GUARDAR
                </button>
            </div>
        </form>

        <div class="text-center mt-3">
            <form action="<?php echo SERVERURL ?>ajax/presupuestoAjax.php" method="POST"
                data-form="loans" autocomplete="off">
                <input type="hidden" name="limpiar_presupuesto" value="1">
                <button type="submit" class="btn btn-raised btn-secondary btn-sm">
                    <i class="fas fa-times"></i> &nbsp; Cancelar
                </button>
            </form>
        </div>
    </div>
</div>

<!-- MODAL proveedor -->
<div class="modal fade" id="ModalproveedorPre" tabindex="-1" role="dialog" aria-labelledby="ModalproveedorPre" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalproveedorPre">Agregar Proveedor</h5>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" placeholder="Ingrese RUC o Razon Social" class="form-control" name="input_proveedor" id="input_proveedor" maxlength="30">
                    </div>
                </div>
                <br>
                <div class="container-fluid" id="tabla_proveedorPre"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="buscar_proveedorPre()"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BUSCAR PEDIDO -->
<div class="modal fade" id="ModalBuscarPedido" tabindex="-1" role="dialog" aria-labelledby="ModalBuscarPedido" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalBuscarPedido">Agregar Pedido</h5>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="form-group">
                        <input type="text" pattern="[a-zA-z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" placeholder="Ingrese codigo de pedido" class="form-control" name="input_pedido" id="input_pedido" maxlength="30">
                    </div>
                </div>
                <br>
                <div class="container-fluid" id="tabla_pedidosPre"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="buscar_pedidoPre()"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php include_once "./vistas/inc/presupuestoCompra.php"; ?>