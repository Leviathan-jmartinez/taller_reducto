<?php

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
            <?php if (empty($_SESSION['datos_proveedorPre'])) { ?>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalproveedorPre">
                    <i class="fas fa-user-plus"></i> &nbsp; Agregar Proveedor
                </button>
            <?php } ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalArticuloPre">
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

    <!-- IZQUIERDA -->
    <div class="col-12 col-md-6">
        <span class="roboto-medium">PROVEEDOR:</span>
        <?php if (empty($_SESSION['datos_proveedorPre'])) { ?>
            <span class="text-danger">&nbsp;
                <i class="fas fa-exclamation-triangle"></i> Seleccione un Proveedor
            </span>
        <?php } else { ?>
            <form class="FormularioAjax d-inline-block" action="<?php echo SERVERURL ?>ajax/presupuestoAjax.php" method="POST" data-form="loans">
                <input type="hidden" name="id_eliminar_proveedorPre" value="<?php echo $_SESSION['datos_proveedorPre']['ID']; ?>">
                <?php echo $_SESSION['datos_proveedorPre']['RAZON'] . " (" . $_SESSION['datos_proveedorPre']['RUC'] . ")"; ?>
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
                if (isset($_SESSION['datos_articuloPre']) && count($_SESSION['datos_articuloPre']) >= 1) {
                    $_SESSION['presupuesto_articulo'] = 0;
                    $_SESSION['total_pre'] = 0;
                    $contador = 1;
                    foreach ($_SESSION['datos_articuloPre'] as $article):
                        $_SESSION['presupuesto_articulo'] += $article['cantidad'];
                        $_SESSION['total_pre'] += $article['subtotal'];
                ?>
                        <tr class="text-center">
                            <td><?php echo $contador++; ?></td>
                            <td><?php echo $article['codigo']; ?></td>
                            <td><?php echo $article['descripcion']; ?></td>
                            <td><?php echo $article['cantidad']; ?></td>
                            <td><?php echo number_format($article['precio'], 0, ',', '.'); ?></td>
                            <td><?php echo number_format($article['subtotal'], 0, ',', '.'); ?></td>
                            <td>
                                <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/presupuestoAjax.php" method="POST" data-form="loans">
                                    <input type="hidden" name="id_eliminar_articuloPre" value="<?php echo $article['ID']; ?>">
                                    <button type="submit" class="btn btn-warning"><i class="far fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="text-center bg-light">
                        <td><strong>TOTAL</strong></td>
                        <td colspan="2"></td>
                        <td><strong><?php echo $_SESSION['presupuesto_articulo']; ?> unidades</strong></td>
                        <td></td>
                        <td><strong><?php echo number_format($_SESSION['total_pre'], 0, ',', '.'); ?> </strong></td>
                        <td></td>
                    </tr>
                <?php
                } else {
                    $_SESSION['presupuesto_articulo'] = 0;
                ?>
                    <tr class="text-center bg-light">
                        <td colspan="8">No has seleccionado articulos</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Botones GUARDAR y LIMPIAR 
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
    -->

    <!-- CONTENEDOR GENERAL -->
    <div class="container-fluid mt-3">

        <!-- FECHA (dentro del form GUARDAR) -->
        <form class="FormularioAjax" action="<?php echo SERVERURL ?>ajax/presupuestoAjax.php"
            method="POST" data-form="save" autocomplete="off">

            <div class="row">
                <div class="col-12 text-md-left mb-3">
                    <label for="fecha_vencimientoPre">Fecha Vencimiento</label>
                    <input type="date" class="form-control d-inline-block"
                        name="fecha_vencimientoPre" id="fecha_vencimientoPre"
                        style="width: 180px;" required>
                </div>
            </div>

            <input type="hidden" name="agregar_presupuesto" value="1">

            <!-- BOTONES ABAJO CENTRADOS -->
            <div class="text-center mt-3">
                <button type="submit" class="btn btn-raised btn-info btn-sm">
                    <i class="far fa-save"></i> &nbsp; GUARDAR
                </button>
            </div>

        </form>

        <!-- BOTÓN LIMPIAR (separado, como en tu versión original) -->
        <div class="text-center mt-3">
            <form action="<?php echo SERVERURL ?>ajax/presupuestoAjax.php" method="POST"
                data-form="loans" autocomplete="off">
                <input type="hidden" name="limpiar_presupuesto" value="1">
                <button type="submit" class="btn btn-raised btn-secondary btn-sm">
                    <i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR
                </button>
            </form>
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



<?php include_once "./vistas/inc/presupuestoCompra.php";
include_once "./vistas/inc/scripts.php"; ?>

<!-- Mostrar modal si no hay selección -->
<script>
    $(document).ready(function() {
        <?php if (empty($_SESSION['tipo_presupuesto'])): ?>
            $('#ModalTipoPresupuesto').modal({});
        <?php endif; ?>
    });
</script>