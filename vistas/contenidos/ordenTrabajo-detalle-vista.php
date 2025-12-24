<?php
if (!isset($insOT)) {
    require_once "./controladores/ordenTrabajoControlador.php";
    $insOT = new ordenTrabajoControlador();
}

$paginaActual = isset($pagina[1]) ? $pagina[1] : null;
if (!$paginaActual) {
    echo '<div class="alert alert-danger">OT no válida</div>';
    exit;
}

$datos = $insOT->detalle_ot_controlador($paginaActual);
$ot = $datos['ot'];
$detalle = $datos['detalle'];
?>


<h3>ORDEN DE TRABAJO #<?= $ot['idorden_trabajo'] ?></h3>

<p><strong>Cliente:</strong> <?= $ot['nombre_cliente'] . ' ' . $ot['apellido_cliente'] ?></p>
<p><strong>Vehículo:</strong> <?= $ot['modelo'] . ' ' . $ot['placa'] ?></p>
<p><strong>Técnico:</strong>
    <?= $ot['tecnico_nombre']
        ? $ot['tecnico_nombre'] . ' ' . $ot['tecnico_apellido']
        : '<span class="badge badge-secondary">Sin asignar</span>' ?>
</p>

<?php if (!$ot['idtrabajos']): ?>
    <form class="FormularioAjax"
        action="<?= SERVERURL ?>ajax/ordenTrabajoAjax.php"
        method="POST"
        data-form="update">

        <input type="hidden" name="accion" value="asignar_tecnico">
        <input type="hidden" name="id_ot" value="<?= $pagina[1] ?>">

        <select name="idtrabajos" class="form-control" required>
            <option value="">Seleccione técnico</option>
            <?php foreach ($tecnicos as $t): ?>
                <option value="<?= $t['idtrabajos'] ?>">
                    <?= $t['nombre'] . ' ' . $t['apellido'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="btn btn-primary mt-2">Asignar</button>
    </form>
<?php endif; ?>