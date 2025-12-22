<?php
if (!isset($insPromocion)) {
    require_once "./controladores/promocionControlador.php";
    $insPromocion = new promocionControlador();
}

$promos = $insPromocion->listar_promociones_controlador();


?>

<div class="container-fluid">

    <h3 class="text-left">
        <i class="fas fa-tags"></i> &nbsp; LISTADO DE PROMOCIONES
    </h3>

    <div class="table-responsive">
        <table class="table table-dark table-sm">
            <thead>
                <tr class="text-center">
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Vigencia</th>
                    <th>Estado</th>
                    <th>Creado por</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                <?php if ($promos): $i = 1;
                    foreach ($promos as $p): ?>
                        <tr class="text-center">
                            <td><?= $i++ ?></td>
                            <td><?= $p['nombre'] ?></td>
                            <td><?= $p['tipo'] ?></td>
                            <td><?= number_format($p['valor'], 0, ',', '.') ?></td>
                            <td><?= $p['fecha_inicio'] ?> → <?= $p['fecha_fin'] ?></td>
                            <td>
                                <?= $p['estado'] == 1
                                    ? '<span class="badge badge-success">Activa</span>'
                                    : '<span class="badge badge-danger">Inactiva</span>' ?>
                            </td>
                            <td><?= $p['creado_por'] ?></td>
                            <td>

                                <!-- EDITAR -->
                                <a href="<?= SERVERURL ?>promocion-editar/<?= $insPromocion->encryption($p['id_promocion']) ?>/">
                                    <i class="fas fa-edit"></i>
                                </a>


                                <!-- ACTIVAR / DESACTIVAR -->
                                <form class="FormularioAjax d-inline"
                                    action="<?= SERVERURL ?>ajax/promocionAjax.php"
                                    method="POST"
                                    data-form="update">

                                    <input type="hidden" name="accion" value="cambiar_estado">
                                    <input type="hidden" name="id"
                                        value="<?= $insPromocion->encryption($p['id_promocion']) ?>">
                                    <input type="hidden" name="estado"
                                        value="<?= $p['estado'] == 1 ? 0 : 1 ?>">

                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No hay promociones</td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>