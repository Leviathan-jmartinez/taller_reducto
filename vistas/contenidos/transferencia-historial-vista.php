<?php

require_once "./controladores/transferenciaControlador.php";
require_once "./modelos/mainModel.php";
$mainModel = new mainModel();

$transferencia = new transferenciaControlador();
$transferencias = $transferencia->listar_transferencias_controlador();
$mainModel = new mainModel();


?>


<div class="container-fluid">
    <h3 class="mb-3">
        <i class="fas fa-exchange-alt"></i> Historial de Transferencias
    </h3>

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
                function estadoTransferencia($estado, $origen, $destino, $miSucursal)
                {
                    if ($estado === 'anulado') {
                        return '<span class="badge badge-danger">Anulada</span>';
                    }

                    if ($estado === 'recibido') {
                        return '<span class="badge badge-success">Recibida</span>';
                    }

                    // en_transito
                    if ($miSucursal == $origen) {
                        return '<span class="badge badge-primary">Enviado</span>';
                    }

                    if ($miSucursal == $destino) {
                        return '<span class="badge badge-warning">Pendiente de recibir</span>';
                    }

                    return '<span class="badge badge-secondary">En tránsito</span>';
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
            </tbody>
        </table>
    </div>
</div>

<?php include "./vistas/inc/transferenciaRecibirJS.php"; ?>