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
    <div class="container-fluid">
        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a href="<?php echo SERVERURL; ?>promocion-nuevo/">
                    <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVA PROMOCIÓN
                </a>
            </li>
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>promocion-lista/">
                    <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE PROMOCIONES
                </a>
            </li>
        </ul>
    </div>

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
                    <th>Editar</th>
                    <th>Estado</th>
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
                            </td>
                            <td>


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

                                    <button type="submit"
                                        class="btn <?= $p['estado'] == 1 ? 'btn-success' : 'btn-secondary' ?> btn-sm"
                                        title="<?= $p['estado'] == 1 ? 'Desactivar' : 'Activar' ?>">
                                        <i class="fas <?= $p['estado'] == 1 ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                        <?= $p['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
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