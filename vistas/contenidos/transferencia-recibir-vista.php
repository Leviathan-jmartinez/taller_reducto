<?php
if (!mainModel::tienePermisoVista('compra.transferencia.recibir')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
require_once "./controladores/transferenciaControlador.php";

$ctrl = new transferenciaControlador();

/* 🔥 OBTENER ID DESDE LA RUTA */
if (!isset($_GET['vista'])) {
    echo "Transferencia no encontrada o no disponible";
    exit;
}

$ruta = explode("/", $_GET['vista']);
$id = $ruta[1] ?? null;

$data = $ctrl->cargar_recibir_vista_controlador($id);

if (!$data) {
    echo "Transferencia no encontrada o no disponible";
    exit;
}

$transferencia = $data['cabecera'];
$detalle = $data['detalle'];

?>



<h4 class="mb-3">
    Recibir transferencia #<?= $transferencia['idtransferencia'] ?>
    <small class="text-muted">
        (<?= $transferencia['suc_origen'] ?> → <?= $transferencia['suc_destino'] ?>)
    </small>
</h4>


<form class="FormularioAjax"
    data-form="save"
    data-modulo="recibir"
    action="<?= SERVERURL ?>ajax/transferenciaAjax.php"
    method="POST">

    <input type="hidden" name="accion" value="recibir_transferencia">
    <input type="hidden" name="idtransferencia"
        value="<?= $transferencia['idtransferencia'] ?>">
    <input type="text" id="buscarProducto"
        class="form-control mb-2"
        placeholder="Buscar producto...">

    <table class="table table-bordered table-sm">
        <thead class="thead-light">
            <tr>
                <th>Producto</th>
                <th class="text-center">Enviado</th>
                <th class="text-center">Recibido</th>
                <th class="text-center">Diferencia</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalle as $d): ?>
                <tr>
                    <td><?= $d['desc_articulo'] ?></td>
                    <td class="text-center enviado"><?= number_format($d['cantidad'], 2) ?></td>
                    <td>
                        <input type="number"
                            name="recibidos[<?= $d['id_articulo'] ?>]"
                            value="<?= $d['cantidad'] ?>"
                            min="0"
                            max="<?= $d['cantidad'] ?>"
                            step="0.01"
                            class="form-control form-control-sm recibido">
                    </td>
                    <td class="text-center diferencia">
                        0.00
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    <div class="text-right">
        <button type="submit" class="btn btn-success">
            Confirmar recepción
        </button>

        <a href="<?= SERVERURL ?>transferencia-historial/"
            class="btn btn-secondary">
            Cancelar
        </a>
    </div>
</form>


<!-- 🔥 EL JS SIEMPRE VA DESPUÉS DEL FORM -->
<?php include "./vistas/inc/transferenciaRecibirJS.php"; ?>