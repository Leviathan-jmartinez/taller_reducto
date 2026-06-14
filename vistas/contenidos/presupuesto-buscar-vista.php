<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('compra.presupuesto.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
} ?>

<!-- Page header -->
<?php
$fecha_inicio = $_SESSION['fecha_inicio_presupuesto'] ?? '';
$fecha_final  = $_SESSION['fecha_final_presupuesto'] ?? '';
$fecha_inicio_dt = $fecha_inicio ? $fecha_inicio . ' 00:00:00' : '';
$fecha_final_dt  = $fecha_final  ? $fecha_final  . ' 23:59:59' : '';
$nro_presupuesto = $_SESSION['nro_presupuesto'] ?? '';
$proveedor_presupuesto = $_SESSION['proveedor_presupuesto'] ?? '';
$estado_presupuesto_compra = $_SESSION['estado_presupuesto_compra'] ?? '';

if (isset($_GET['estado_presupuesto_compra']) && in_array((string)$_GET['estado_presupuesto_compra'], ['0', '1', '2'], true)) {
    $_SESSION['estado_presupuesto_compra'] = (string)$_GET['estado_presupuesto_compra'];
    $estado_presupuesto_compra = (string)$_GET['estado_presupuesto_compra'];
}

$ordenPresupuesto = mainModel::cargar_ordenamiento_sesion('presupuesto', ['fecha', 'estado'], 'fecha', 'DESC');

$estadosPresupuesto = [
    '' => 'Todos',
    '0' => 'Anulado',
    '1' => 'Pendiente',
    '2' => 'Procesado'
];

if (!isset($pagina) || !is_array($pagina)) {
    $url = $_GET['views'] ?? 'presupuesto-buscar/1';
    $pagina = explode('/', $url);
    $pagina = [$pagina[0] ?? 'presupuesto-buscar', $pagina[1] ?? 1];
}
?>

<?php if (!$fecha_inicio && !$fecha_final && !$nro_presupuesto && !$proveedor_presupuesto && !isset($_SESSION['estado_presupuesto_compra'])) { ?>
    <div class="container-fluid form-neon app-view">
        <h3 class="text-left">
            <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR PRESUPUESTOS
        </h3>
        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a href="<?php echo SERVERURL; ?>presupuesto-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PRESUPUESTO</a>
            </li>
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>presupuesto-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR PRESUPUESTOS</a>
            </li>
        </ul>
        <form class="form-neon FormularioAjax app-form" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="search" autocomplete="off">
            <input type="hidden" name="modulo" value="presupuesto">
            <input type="hidden" name="fecha_inicio_dt" value="">
            <input type="hidden" name="fecha_final_dt" value="">

            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha inicial</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" maxlength="30">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="fecha_final">Fecha final</label>
                            <input type="date" class="form-control" name="fecha_final" id="fecha_final" maxlength="30">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="nro_presupuesto">Nro. Presupuesto</label>
                            <input type="text" class="form-control" name="nro_presupuesto" id="nro_presupuesto">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="proveedor_presupuesto">Proveedor</label>
                            <input type="text" class="form-control" name="proveedor_presupuesto" id="proveedor_presupuesto">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="estado_presupuesto_compra">Estado</label>
                            <select class="form-control" name="estado_presupuesto_compra" id="estado_presupuesto_compra">
                                <option value="">Todos</option>
                                <option value="1">Pendiente</option>
                                <option value="2">Procesado</option>
                                <option value="0">Anulado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 text-center" style="margin-top: 40px;">
                        <button type="submit" class="btn btn-raised btn-info"><i class="fas fa-search"></i> &nbsp; BUSCAR</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php } else { ?>
    <div class="container-fluid form-neon app-view">
        <h3 class="text-left">
            <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; PRESUPUESTO DE SERVICIOS
        </h3>
        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a href="<?php echo SERVERURL; ?>presupuesto-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PRESUPUESTO</a>
            </li>
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>presupuesto-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA</a>
            </li>
        </ul>
        <form class="FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="search" autocomplete="off">
            <input type="hidden" name="modulo" value="presupuesto">
            <input type="hidden" name="eliminar_busqueda" value="eliminar">
            <input type="hidden" name="fecha_inicio_dt" value="<?php echo $fecha_inicio_dt; ?>">
            <input type="hidden" name="fecha_final_dt" value="<?php echo $fecha_final_dt; ?>">

            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-6">
                        <p class="text-center" style="font-size: 20px;">
                            Busqueda:
                            <strong>
                                <?php
                                $criterios = [];
                                if ($fecha_inicio || $fecha_final) {
                                    $criterios[] = 'Fecha: ' . ($fecha_inicio ?: 'inicio') . ' a ' . ($fecha_final ?: 'final');
                                }
                                if ($nro_presupuesto) {
                                    $criterios[] = 'Nro. Presupuesto: ' . $nro_presupuesto;
                                }
                                if ($proveedor_presupuesto) {
                                    $criterios[] = 'Proveedor: ' . $proveedor_presupuesto;
                                }
                                if (isset($_SESSION['estado_presupuesto_compra'])) {
                                    $criterios[] = 'Estado: ' . ($estadosPresupuesto[(string)$estado_presupuesto_compra] ?? $estado_presupuesto_compra);
                                }
                                echo htmlspecialchars(implode(' | ', $criterios), ENT_QUOTES, 'UTF-8');
                                ?>
                            </strong>
                        </p>
                    </div>
                    <div class="col-12 text-center" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-raised btn-danger"><i class="fas fa-times"></i> &nbsp; Limpiar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="container-fluid">
        <?php
        require_once "./controladores/presupuestoControlador.php";
        $ins_presupuesto = new presupuestoControlador();
        $ins_presupuesto->paginador_presupuestos_controlador(
            $pagina[1],
            15,
            $pagina[0],
            $_SESSION['fecha_inicio_presupuesto'] ?? '',
            $_SESSION['fecha_final_presupuesto'] ?? '',
            $_SESSION['nro_presupuesto'] ?? '',
            $_SESSION['proveedor_presupuesto'] ?? '',
            $_SESSION['estado_presupuesto_compra'] ?? '',
            $ordenPresupuesto['orden'],
            $ordenPresupuesto['direccion']
        );
        ?>
    </div>
<?php
} ?>

<div class="modal fade" id="modalDetallePresupuestoCompra" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye"></i> &nbsp; Detalle de presupuesto de compra
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenidoDetallePresupuestoCompra">
                <div class="text-center text-muted py-4">Seleccione un presupuesto para ver el detalle.</div>
            </div>
        </div>
    </div>
</div>

<script>
    function verDetallePresupuestoCompra(idPresupuesto) {
        const contenedor = document.getElementById('contenidoDetallePresupuestoCompra');
        if (!contenedor) return;

        contenedor.innerHTML = '<div class="text-center text-muted py-4">Cargando detalle...</div>';
        $('#modalDetallePresupuestoCompra').modal('show');

        fetch("<?php echo SERVERURL; ?>ajax/presupuestoAjax.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    detalle_presupuesto_compra: idPresupuesto
                })
            })
            .then(resp => resp.json())
            .then(data => {
                contenedor.innerHTML = data.html || '<div class="alert alert-warning mb-0">No se pudo cargar el detalle.</div>';
            })
            .catch(() => {
                contenedor.innerHTML = '<div class="alert alert-danger mb-0">Error al cargar el detalle.</div>';
            });
    }
</script>
