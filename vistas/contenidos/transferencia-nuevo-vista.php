<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$idSucursalOrigen = $_SESSION['nick_sucursal'] ?? null;
$idUsuario        = $_SESSION['id_str'] ?? null;

if (!$idSucursalOrigen) {
    echo '<div class="alert alert-danger">Sucursal no definida en sesión</div>';
    exit;
}

/*
  Estos datos deben venir del controlador:
  - $sucursalesDestino
  - $datosFiscal (timbrado, vencimiento, establecimiento, punto, proximo_nro)
  - $productosStock (productos de la sucursal origen)
*/
?>

<div class="container-fluid">

    <h3 class="text-left mb-3">
        <i class="fas fa-exchange-alt fa-fw"></i>
        &nbsp; Transferencia entre sucursales
    </h3>

    <form class="FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/transferenciaAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="crear_transferencia">
        <input type="hidden" name="id_sucursal_origen" value="<?= $idSucursalOrigen ?>">
        <input type="hidden" name="id_usuario" value="<?= $idUsuario ?>">

        <!-- ================= DATOS GENERALES ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos de la transferencia</legend>

            <div class="row">
                <div class="col-md-4">
                    <label>Sucursal origen</label>
                    <input type="text" class="form-control" value="<?= $_SESSION['sucursal_nombre'] ?? 'Sucursal' ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label>Sucursal destino</label>
                    <select name="sucursal_destino" class="form-control" required>
                        <option value="">Seleccione</option>
                        <?php foreach ($sucursalesDestino as $s): ?>
                            <option value="<?= $s['id_sucursal'] ?>">
                                <?= $s['suc_descri'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Motivo</label>
                    <input type="text"
                        name="motivo"
                        class="form-control"
                        value="Transferencia entre sucursales"
                        readonly>
                </div>
            </div>
        </fieldset>

        <!-- ================= DATOS FISCALES ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos fiscales (informativo)</legend>

            <div class="row">
                <div class="col-md-3">
                    <label>Timbrado</label>
                    <input class="form-control" value="<?= $datosFiscal['timbrado'] ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label>Vencimiento</label>
                    <input class="form-control" value="<?= $datosFiscal['vencimiento'] ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label>Estab. / Punto</label>
                    <input class="form-control"
                        value="<?= $datosFiscal['establecimiento'] . '-' . $datosFiscal['punto'] ?>"
                        readonly>
                </div>
                <div class="col-md-3">
                    <label>Próxima remisión</label>
                    <input class="form-control" value="<?= $datosFiscal['proximo_nro'] ?>" readonly>
                </div>
            </div>
        </fieldset>

        <!-- ================= PRODUCTOS ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Productos (stock sucursal origen)</legend>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Producto</th>
                            <th>Stock</th>
                            <th width="120">Cantidad</th>
                            <th width="80">Agregar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productosStock as $p): ?>
                            <tr>
                                <td><?= $p['descripcion'] ?></td>
                                <td><?= number_format($p['stock'], 2) ?></td>
                                <td>
                                    <input type="number"
                                        min="0.01"
                                        step="0.01"
                                        class="form-control cantidad"
                                        data-id="<?= $p['id_producto'] ?>"
                                        data-max="<?= $p['stock'] ?>">
                                </td>
                                <td class="text-center">
                                    <button type="button"
                                        class="btn btn-sm btn-primary agregar-producto"
                                        data-id="<?= $p['id_producto'] ?>"
                                        data-desc="<?= $p['descripcion'] ?>">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <hr>

            <h6>Detalle de transferencia</h6>

            <table class="table table-bordered table-sm" id="detalle_transferencia">
                <thead class="thead-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th width="60">Quitar</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </fieldset>

        <!-- ================= TRANSPORTE ================= -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos de transporte</legend>

            <div class="row">
                <div class="col-md-4">
                    <label>Transportista</label>
                    <input name="transportista" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>RUC transportista</label>
                    <input name="ruc_transport" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Chofer</label>
                    <input name="nombre_transpo" class="form-control" required>
                </div>

                <div class="col-md-3 mt-2">
                    <label>CI Chofer</label>
                    <input name="ci_transpo" class="form-control">
                </div>
                <div class="col-md-3 mt-2">
                    <label>Celular</label>
                    <input name="cel_transpo" class="form-control">
                </div>
                <div class="col-md-3 mt-2">
                    <label>Vehículo</label>
                    <input name="vehimarca" class="form-control" required>
                </div>
                <div class="col-md-3 mt-2">
                    <label>Chapa</label>
                    <input name="vehichapa" class="form-control" required>
                </div>

                <div class="col-md-6 mt-2">
                    <label>Fecha envío</label>
                    <input type="date" name="fechaenvio" class="form-control" required>
                </div>
                <div class="col-md-6 mt-2">
                    <label>Fecha llegada</label>
                    <input type="date" name="fechallegada" class="form-control" required>
                </div>
            </div>
        </fieldset>

        <!-- ================= ACCIÓN ================= -->
        <div class="text-right mb-4">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check"></i> Generar transferencia y remisión
            </button>
        </div>

    </form>
</div>

<!-- ================= JS ================= -->
<script>
    const detalle = document.querySelector('#detalle_transferencia tbody');

    document.querySelectorAll('.agregar-producto').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const desc = btn.dataset.desc;
            const input = document.querySelector('.cantidad[data-id="' + id + '"]');
            const cant = parseFloat(input.value);

            if (!cant || cant <= 0) {
                alert('Cantidad inválida');
                return;
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td>${desc}</td>
            <td>
                ${cant}
                <input type="hidden" name="productos[${id}]" value="${cant}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger quitar">X</button>
            </td>
        `;
            detalle.appendChild(tr);
            input.value = '';
        });
    });

    detalle.addEventListener('click', e => {
        if (e.target.classList.contains('quitar')) {
            e.target.closest('tr').remove();
        }
    });
</script>