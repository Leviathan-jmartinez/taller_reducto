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

    <?php if ($tipo === 'con_presupuesto') { ?>
        <!-- <form id="formSearch" autocomplete="off">-->
        <?php if (!isset($_SESSION['busqueda_ordencompra']) && empty($_SESSION['busqueda_ordencompra'])) { ?>
            <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="default" autocomplete="off">
                <input type="hidden" name="modulo" value="ordencompra">
                <!--<input type="hidden" name="modulo" value="presupuesto_compra">-->

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="oc-header">

                            <div style="flex: 1; min-width: 250px;">
                                <label for="inputSearch" class="bmd-label-floating">
                                    ¿Qué cliente estás buscando?
                                </label>
                                <input type="text"
                                    class="form-control"
                                    name="busqueda_inicial"
                                    id="inputSearch"
                                    placeholder="Ej: Ejemplo SRL…">
                            </div>

                            <div class="oc-actions">
                                <button type="submit" class="btn btn-dark">Filtrar</button>
            </form>
            <form action="" method="POST" style="display:inline;">
                <input type="hidden" name="tipo_ordencompra" value="sin_presupuesto">
                <button class="btn btn-primary btn-lg" type="submit">
                    + OC sin presupuesto
                </button>
            </form>
</div>
</div>

</div>
</div>



<?php } else { ?>
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="default" autocomplete="off">
        <input type="hidden" name="modulo" value="ordencompra">
        <input type="hidden" name="eliminar_busqueda" value="eliminar">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="oc-header">

                    <div style="flex: 1; min-width: 250px;">
                        <label for="inputSearch" class="bmd-label-floating">
                            Resultado de Búsqueda &nbsp;
                        </label>
                        <input
                            class="form-control"
                            placeholder="<?php echo $_SESSION['busqueda_ordencompra'] ?>">
                    </div>

                    <div class="oc-actions">
                        <button type="submit" class="btn btn-raised btn-danger"><i class="far fa-trash-alt"></i> &nbsp; ELIMINAR BÚSQUEDA</button>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <div class="container-fluid">
        <?php
            require_once "./controladores/ordencompraControlador.php";
            $ins_ordencompra = new ordencompraControlador();
            echo $ins_ordencompra->paginador_presupuestos_controlador($pagina[1], 15, $_SESSION['nivel_str'], $pagina[0], $_SESSION['busqueda_ordencompra']);
        ?>
    </div>
<?php
        }
?>
<?php } else { ?>
    <!-- Contenedor donde se cargará la tabla 
    <div id="tablaPresupuestos"></div>-->

<?php
    }
?>
</div>





<!-- Modal de selección inicial -->
<!-- Modal Detalle de Presupuesto -->
<div class="modal fade" id="modalDetallePresupuesto" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">ORDEN DE COMPRA</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">


                <form id="formOcProductos">
                    <div class="row align-items-end">
                        <div class="col-md-8 mb-3">
                            <input type="text" id="filtroProductos" class="form-control"
                                placeholder="Filtrar por código o descripción…">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="fecha_entrega" class="form-label">Fecha Entrega</label>
                            <input type="date" class="form-control"
                                name="fecha_entrega" id="fecha_entrega"
                                value="<?php echo date('Y-m-d'); ?>"
                                required>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-dark table-sm text-center">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th>Precio</th>
                                    <th>Cargar Cantidad</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyDetallePresupuesto">
                                <!-- AJAX carga aquí -->
                            </tbody>
                        </table>
                    </div>
                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnGuardarOC">Generar Orden de Compra</button>
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

<script>
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("generar-oc-btn")) {
            let id = e.target.getAttribute("data-id");

            // Guardar el ID global para usarlo en btnGuardarOC
            window.idPresupuestoActual = id;

            fetch("<?php echo SERVERURL; ?>ajax/presupuestoDetalleAjax.php", {
                    method: "POST",
                    body: new URLSearchParams({
                        idpresupuesto: id
                    })
                })
                .then(res => res.text())
                .then(html => {
                    document.querySelector("#tbodyDetallePresupuesto").innerHTML = html;
                    new bootstrap.Modal(document.getElementById("modalDetallePresupuesto")).show();
                });
        }
    });

    document.getElementById("filtroProductos").addEventListener("keyup", function() {
        let filtro = this.value.toLowerCase();
        document.querySelectorAll("#tbodyDetallePresupuesto tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filtro) ? "" : "none";
        });
    });

    document.getElementById("btnGuardarOC").addEventListener("click", function() {

        let form = document.getElementById("formOcProductos");
        let datos = new FormData(form);

        // Aquí le pasamos el ID del presupuesto seleccionado
        datos.append("idpresupuesto", window.idPresupuestoActual);

        // Si quieres, también puedes pasar módulo u otros datos
        datos.append("modulo", "ordencompra");

        fetch("<?php echo SERVERURL; ?>ajax/ordencompraAjax.php", {
                method: "POST",
                body: datos
            })
            .then(r => r.text())
            .then(r => {

                if (r.includes("ok:")) {
                    let idOC = r.replace("ok:", "");
                    Swal.fire("OC generada", "N° " + idOC, "success").then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire("Error", r, "error");
                }
            });
    });
</script>