<?php
include_once "./vistas/inc/pedido.php";
include_once "./vistas/inc/scripts.php";
// Iniciar sesión solo si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Capturar selección de tipo de presupuesto
if (isset($_POST['tipo_presupuesto'])) {
    $_SESSION['tipo_presupuesto'] = $_POST['tipo_presupuesto'];
}

$tipo = $_SESSION['tipo_presupuesto'] ?? null;
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-plus fa-fw"></i> &nbsp; CARGAR PRESUPUESTO
        <?php if ($tipo == 'con_pedido') echo "(a partir de pedido)"; ?>
    </h3>
</div>

<!-- Menú superior -->
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>presupuesto-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; CARGAR PRESUPUESTO</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>presupuesto-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTADOS DE PRESUPUESTOS</a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>presupuesto-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA</a>
        </li>
    </ul>
</div>

<div class="container-fluid form-neon">

    <!-- Contenido según tipo de presupuesto -->
    <?php if ($tipo === 'sin_pedido') { ?>
        <!-- SIN PEDIDO: agregar proveedor y artículos manualmente -->
        <div class="text-center mb-3">
            <?php if (empty($_SESSION['datos_proveedor'])) { ?>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#Modalproveedor">
                    <i class="fas fa-user-plus"></i> &nbsp; Agregar Proveedor
                </button>
            <?php } ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalArticulo">
                <i class="fas fa-box-open"></i> &nbsp; Agregar artículo
            </button>
        </div>
    <?php } elseif ($tipo === 'con_pedido') { ?>
        <!-- CON PEDIDO: buscar pedido en BD -->
        <div class="text-center mb-3">
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#ModalBuscarPedido">
                <i class="fas fa-search"></i> &nbsp; Buscar Pedido
            </button>
        </div>
    <?php } ?>

    <!-- Mostrar proveedor seleccionado -->
    <div>
        <span class="roboto-medium">PROVEEDOR:</span>
        <?php if (empty($_SESSION['datos_proveedor'])) { ?>
            <span class="text-danger">&nbsp; <i class="fas fa-exclamation-triangle"></i> Seleccione un Proveedor</span>
        <?php } else { ?>
            <form class="FormularioAjax" action="<?php echo SERVERURL ?>ajax/pedidoAjax.php" method="POST" data-form="loans" style="display:inline-block;">
                <input type="hidden" name="id_eliminar_proveedor" value="<?php echo $_SESSION['datos_proveedor']['ID']; ?>">
                <?php echo $_SESSION['datos_proveedor']['RAZON'] . " (" . $_SESSION['datos_proveedor']['RUC'] . ")"; ?>
                <button type="submit" class="btn btn-danger"><i class="fas fa-user-times"></i></button>
            </form>
        <?php } ?>
    </div>

    <!-- Tabla de artículos -->
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
                if (isset($_SESSION['datos_articulo']) && count($_SESSION['datos_articulo']) >= 1) {
                    $_SESSION['pedido_articulo'] = 0;
                    $contador = 1;
                    foreach ($_SESSION['datos_articulo'] as $article):
                        $_SESSION['pedido_articulo'] += $article['cantidad'];
                ?>
                        <tr class="text-center">
                            <td><?php echo $contador++; ?></td>
                            <td><?php echo $article['codigo']; ?></td>
                            <td><?php echo $article['descripcion']; ?></td>
                            <td><?php echo $article['cantidad']; ?></td>
                            <td><?php echo $article['cantidad']; ?></td>
                            <td><?php echo $article['cantidad']; ?></td>
                            <td>
                                <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/pedidoAjax.php" method="POST" data-form="loans">
                                    <input type="hidden" name="id_eliminar_articulo" value="<?php echo $article['ID']; ?>">
                                    <button type="submit" class="btn btn-warning"><i class="far fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="text-center bg-light">
                        <td colspan="2"></td>
                        <td><strong>TOTAL</strong></td>
                        <td><strong><?php echo $_SESSION['pedido_articulo']; ?> articulos</strong></td>
                        <td></td>
                    </tr>
                <?php
                } else {
                    $_SESSION['pedido_articulo'] = 0;
                ?>
                    <tr class="text-center bg-light">
                        <td colspan="8">No has seleccionado articulos</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Botones GUARDAR y LIMPIAR -->
    <div style="display: flex; justify-content: center; gap: 25px; margin-top: 30px;">
        <form class="FormularioAjax" action="<?php echo SERVERURL ?>ajax/presupuestoAjax.php" method="POST" data-form="save" autocomplete="off" style="margin:0;">
            <input type="hidden" name="agregar_presupuesto" value="1">
            <button type="submit" class="btn btn-raised btn-info btn-sm">
                <i class="far fa-save"></i> &nbsp; GUARDAR
            </button>
        </form>
        <form action="<?php echo SERVERURL ?>ajax/presupuestoAjax.php" method="POST" data-form="loans" autocomplete="off" style="margin:0;">
            <input type="hidden" name="limpiar_presupuesto" value="1">
            <button type="submit" class="btn btn-raised btn-secondary btn-sm">
                <i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR
            </button>
        </form>
    </div>
</div>

<!-- Mostrar modal si no hay selección -->
<script>
    $(document).ready(function() {
        <?php if (empty($_SESSION['tipo_presupuesto'])): ?>
            $('#ModalTipoPresupuesto').modal({
                backdrop: 'static',
                keyboard: false
            });
        <?php endif; ?>
    });
</script>

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
<div class="modal fade" id="Modalproveedor" tabindex="-1" role="dialog" aria-labelledby="Modalproveedor" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Modalproveedor">Agregar Proovedor</h5>
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
                <div class="container-fluid" id="tabla_proveedor">

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="buscar_proveedor()"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<!-- MODAL ITEM -->
<div class="modal fade" id="ModalArticulo" tabindex="-1" role="dialog" aria-labelledby="ModalArticulo" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalArticulo">Agregar Articulo</h5>
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
                <div class="container-fluid" id="tabla_articulos">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="buscar_articulo()"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<!-- MODAL AGREGAR ITEM -->
<div class="modal fade" id="ModalAgregarArticulo" tabindex="-1" role="dialog" aria-labelledby="ModalAgregarArticulo" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content FormularioAjax" action="<?php echo SERVERURL; ?>ajax/pedidoAjax.php" method="POST" data-form="save" autocomplete="off">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalAgregararticulo">Selecciona la cantidad de articulos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_agregar_articulo" id="id_agregar_articulo">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="detalle_cantidad" class="bmd-label-floating">Cantidad de items</label>
                                <input type="num" pattern="[0-9]{1,7}" class="form-control" name="detalle_cantidad" id="detalle_cantidad" maxlength="7" required="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Agregar</button>
                &nbsp; &nbsp;
                <button type="button" class="btn btn-secondary" onclick="modal_buscar_articulo()">Cancelar</button>
            </div>
        </form>
    </div>
</div>