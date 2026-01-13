<?php
if (!mainModel::tienePermisoVista('compra.transferencia.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}


require_once "./controladores/transferenciaControlador.php";
require_once "./modelos/mainModel.php";
$mainModel = new mainModel();

$ctrl = new transferenciaControlador();

$resultado = $ctrl->listar_transferencias_controlador();

$transferencias = $resultado['datos'];
$totalPaginas   = $resultado['paginas'];
$paginaActual   = $resultado['pagina_actual'];

$filtroEstado = $resultado['filtroEstado'];
$filtroFecha  = $resultado['filtroFecha'];
$filtroId     = $resultado['filtroId'];

?>


<div class="container-fluid">
    <div class="card-header">
        <h3 class="mb-0">
            <i class="fas fa-exchange-alt"></i>
            Historial de Transferencias entre Sucursales
        </h3>

        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a href="<?php echo SERVERURL; ?>/transferencia-nuevo/">
                    <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVA TRANSFERENCIA
                </a>
            </li>
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>/transferencia-historial/">
                    <i class="fas fa-search fa-fw"></i> &nbsp; HISTORIAL DE TRANSFERENCIAS
                </a>
            </li>
        </ul>
    </div>
    <div class="card-header">
        <form class="form-inline mb-3" onsubmit="aplicarFiltros(event)">

            <!-- Estado -->
            <select id="filtroEstado" class="form-control mr-2">
                <option value="-">Todos</option>
                <option value="en_transito">Pendientes</option>
                <option value="recibido">Recibidas</option>
                <option value="recibido_parcial">Recibidas parciales</option>
                <option value="devolucion">Devoluciones</option>
            </select>

            <!-- Fecha -->
            <input type="date"
                id="filtroFecha"
                class="form-control mr-2">

            <!-- ID transferencia -->
            <input type="number"
                id="filtroId"
                class="form-control mr-2"
                placeholder="ID">

            <!-- Botones -->
            <button type="submit" class="btn btn-primary mr-2">
                Filtrar
            </button>

            <button type="button" class="btn btn-secondary"
                onclick="limpiarFiltros()">
                Limpiar
            </button>

        </form>




        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Estado</th>
                        <th width="220">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    function estadoTransferencia(
                        $estado,
                        $origen,
                        $destino,
                        $miSucursal,
                        $idTransferenciaOrigen = null
                    ) {
                        // Anulada
                        if ($estado === 'anulado') {
                            return '<span class="badge badge-danger">Anulada</span>';
                        }

                        // Recibida completa
                        if ($estado === 'recibido') {
                            return '<span class="badge badge-success">Recibida</span>';
                        }

                        // Recibida parcial (NO es pendiente)
                        if ($estado === 'recibido_parcial') {
                            return '<span class="badge badge-info">Recibida parcial</span>';
                        }

                        // 🔁 Devolución por faltantes (en tránsito)
                        if (
                            $estado === 'en_transito' &&
                            !empty($idTransferenciaOrigen)
                        ) {
                            if ($miSucursal == $destino) {
                                return '<span class="badge badge-warning">Devolución pendiente</span>';
                            }

                            if ($miSucursal == $origen) {
                                return '<span class="badge badge-primary">Devolución enviada</span>';
                            }

                            return '<span class="badge badge-secondary">Devolución en tránsito</span>';
                        }

                        // 🚚 Transferencia normal en tránsito
                        if ($estado === 'en_transito') {

                            if ($miSucursal == $origen) {
                                return '<span class="badge badge-primary">Enviado</span>';
                            }

                            if ($miSucursal == $destino) {
                                return '<span class="badge badge-warning">Pendiente de recibir</span>';
                            }

                            return '<span class="badge badge-secondary">En tránsito</span>';
                        }

                        // Fallback
                        return '<span class="badge badge-secondary">Desconocido</span>';
                    }
                    ?>

                    <?php foreach ($transferencias as $t): ?>


                        <tr>
                            <td><?= $t['idtransferencia'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($t['fecha'])) ?></td>
                            <td><?= $t['suc_origen'] ?></td>
                            <td><?= $t['suc_destino'] ?></td>
                            <td>
                                <?= estadoTransferencia(
                                    $t['estado'],
                                    $t['sucursal_origen'],
                                    $t['sucursal_destino'],
                                    $_SESSION['nick_sucursal']
                                ); ?>
                            </td>

                            <td>

                                <?php if (
                                    $t['estado'] === 'en_transito' &&
                                    $_SESSION['nick_sucursal'] == $t['sucursal_destino']
                                ): ?>
                                    <a href="<?= SERVERURL ?>transferencia-recibir/<?= $mainModel->encryption($t['idtransferencia']) ?>/"
                                        class="btn btn-sm btn-success">
                                        Recibir
                                    </a>

                                <?php endif; ?>



                                <?php if (!empty($t['idnota_remision'])): ?>
                                    <button class="btn btn-info btn-sm"
                                        onclick="window.open(
                                    '<?= SERVERURL ?>pdf/remision.php?id=<?= $t['idnota_remision'] ?>',
                                    '_blank'
                                )">
                                        Reimprimir
                                    </button>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transferencias)): ?>
                        <tr>
                            <td colspan="100%" class="text-center text-muted">
                                No hay transferencias para el filtro seleccionado
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if ($totalPaginas > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">

                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>

                            <?php
                            // URL base
                            $url = SERVERURL . "transferencia-historial/";

                            // Si hay filtros activos, reconstruirlos
                            if (
                                $filtroEstado !== '-' ||
                                $filtroFecha  !== '-' ||
                                $filtroId     !== '-'
                            ) {
                                $url .= "filtro/{$filtroEstado}/{$filtroFecha}/{$filtroId}/";
                            }

                            // Página
                            $url .= "pagina/{$i}/";
                            ?>

                            <li class="page-item <?= ($i == $paginaActual) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $url ?>">
                                    <?= $i ?>
                                </a>
                            </li>

                        <?php endfor; ?>

                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include "./vistas/inc/transferenciaRecibirJS.php"; ?>