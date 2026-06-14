<?php
$vistaActual = $_GET['vista'] ?? '';
$pagina = explode('/', trim($vistaActual, '/'));
$idDescuento = $pagina[1] ?? '';
$esEditar = $idDescuento !== '';

$puedeCrear = mainModel::tienePermiso('servicio.descuento.crear');
$puedeEditar = mainModel::tienePermiso('servicio.descuento.editar');
$puedeAsignar = mainModel::tienePermiso('servicio.descuento.asignarClientes');

if (!$esEditar && !$puedeCrear) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

if ($esEditar && !$puedeEditar && !$puedeAsignar) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/descuentoControlador.php";

$insDescuento = new descuentoControlador();
$descuento = [];
$clientesAsignados = [];

if ($esEditar) {
    $descuento = $insDescuento->datos_descuento_controlador($idDescuento);

    if (!$descuento) {
        echo '<div class="alert alert-danger">Descuento no encontrado</div>';
        return;
    }

    $clientesAsignados = $insDescuento->clientes_asignados_descuento_controlador($idDescuento);
}

$sucursales = mainModel::conectar()
    ->query("SELECT id_sucursal, suc_descri FROM sucursales WHERE estado = 1 ORDER BY suc_descri")
    ->fetchAll(PDO::FETCH_ASSOC);

$accion = $esEditar ? 'editar_descuento' : 'guardar_descuento';
$titulo = $esEditar ? 'EDITAR DESCUENTO' : 'REGISTRAR DESCUENTO';
$dataForm = $esEditar ? 'update' : 'save';
$botonTexto = $esEditar ? 'Guardar cambios' : 'Guardar';
$botonClase = $esEditar ? 'btn-primary' : 'btn-info btn-raised';
?>

<div class="container-fluid app-view">



    <?php if (!$esEditar || $puedeEditar): ?>
        <form class="form-neon FormularioAjax app-form"
            action="<?php echo SERVERURL; ?>ajax/descuentoAjax.php"
            method="POST"
            data-form="<?= $dataForm ?>"
            autocomplete="off">

            <h3 class="text-left">
                <i class="fas fa-percent"></i> &nbsp; <?= $titulo ?>
            </h3>

            <ul class="full-box list-unstyled page-nav-tabs">
                <li>
                    <a class="<?= !$esEditar ? 'active' : '' ?>" href="<?php echo SERVERURL; ?>descuento-nuevo/">
                        <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO DESCUENTO
                    </a>
                </li>
                <li>
                    <a href="<?php echo SERVERURL; ?>descuento-lista/">
                        <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE DESCUENTOS
                    </a>
                </li>
            </ul>
            <input type="hidden" name="accion" value="<?= $accion ?>">
            <input type="hidden" name="es_reutilizable" value="<?= htmlspecialchars($descuento['es_reutilizable'] ?? '1', ENT_QUOTES, 'UTF-8') ?>">
            <?php if ($esEditar): ?>
                <input type="hidden" name="id_descuento" value="<?= htmlspecialchars($idDescuento, ENT_QUOTES, 'UTF-8') ?>">
            <?php endif; ?>

            <fieldset class="border p-3 mb-3">
                <legend class="w-auto px-2">Datos del descuento</legend>

                <div class="row">
                    <div class="col-md-6">
                        <label>Nombre</label>
                        <input type="text"
                            name="nombre"
                            class="form-control"
                            value="<?= htmlspecialchars($descuento['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            required
                            placeholder="Ej: Cliente VIP">
                    </div>

                    <div class="col-md-6">
                        <label>Tipo</label>
                        <select name="tipo" class="form-control" required>
                            <option value="">Seleccione</option>
                            <?php foreach (['PORCENTAJE' => 'Porcentaje (%)', 'MONTO_FIJO' => 'Monto fijo'] as $valor => $texto): ?>
                                <option value="<?= $valor ?>" <?= (($descuento['tipo'] ?? '') === $valor) ? 'selected' : '' ?>>
                                    <?= $texto ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Valor</label>
                        <input type="number"
                            step="0.01"
                            name="valor"
                            class="form-control"
                            value="<?= htmlspecialchars($descuento['valor'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label>Descripcion</label>
                        <textarea name="descripcion"
                            class="form-control"
                            rows="2"
                            placeholder="Motivo o condicion del descuento"><?= htmlspecialchars($descuento['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-4">
                        <label>Aplica a</label>
                        <select name="aplica_a" class="form-control" required>
                            <?php foreach (['TOTAL' => 'Total', 'PRODUCTO' => 'Producto', 'SERVICIO' => 'Servicio'] as $valor => $texto): ?>
                                <option value="<?= $valor ?>" <?= (($descuento['aplica_a'] ?? 'TOTAL') === $valor) ? 'selected' : '' ?>>
                                    <?= $texto ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Fecha inicio</label>
                        <input type="date"
                            name="fecha_inicio"
                            class="form-control"
                            value="<?= htmlspecialchars($descuento['fecha_inicio'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Fecha fin</label>
                        <input type="date"
                            name="fecha_fin"
                            class="form-control"
                            value="<?= htmlspecialchars($descuento['fecha_fin'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Sucursal</label>
                        <select name="id_sucursal" class="form-control">
                            <option value="">Todas</option>
                            <?php foreach ($sucursales as $s): ?>
                                <option value="<?= $s['id_sucursal'] ?>" <?= (($descuento['id_sucursal'] ?? '') == $s['id_sucursal']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['suc_descri'], ENT_QUOTES, 'UTF-8') ?>
                                    <?= (($descuento['id_sucursal'] ?? '') == $s['id_sucursal']) ? ' (Actual)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </fieldset>

            <fieldset class="border p-3 mb-4">
                <legend class="w-auto px-2">Estado</legend>

                <div class="form-check">
                    <input class="form-check-input"
                        type="checkbox"
                        name="estado"
                        value="1"
                        <?= (($descuento['estado'] ?? 1) == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label">
                        Descuento activo
                    </label>
                </div>
            </fieldset>

            <?php if ($puedeAsignar): ?>
                <fieldset class="border p-3 mb-4" id="asignar-clientes">
                    <legend class="w-auto px-2">Clientes con descuento</legend>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Buscar cliente</label>
                            <input type="text"
                                id="buscar_cliente"
                                class="form-control"
                                placeholder="Buscar por CI o nombre"
                                onkeyup="buscarClienteDescuento()">

                            <div class="mt-3" id="resultado_clientes"></div>
                        </div>

                        <div class="col-md-6">
                            <label>Clientes seleccionados</label>
                            <ul class="list-group" id="clientes_asignados">
                                <?php if (!empty($clientesAsignados)): ?>
                                    <?php foreach ($clientesAsignados as $cli): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center"
                                            id="cli_existente_<?= $cli['id_cliente'] ?>">
                                            <div>
                                                <?= htmlspecialchars($cli['nombre_cliente'] . ' ' . $cli['apellido_cliente'], ENT_QUOTES, 'UTF-8'); ?>
                                                <span class="badge badge-info ml-2">
                                                    CI: <?= htmlspecialchars($cli['doc_number'], ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            </div>

                                            <button type="button"
                                                class="btn btn-danger btn-sm"
                                                title="Quitar cliente"
                                                onclick="eliminarClienteDescuento(
                                                    '<?= htmlspecialchars($idDescuento, ENT_QUOTES, 'UTF-8'); ?>',
                                                    '<?= $insDescuento->encryption($cli['id_cliente']); ?>'
                                                )">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-muted" id="clientes_vacio">
                                        No hay clientes seleccionados
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </fieldset>
            <?php endif; ?>

            <div class="text-center">
                <button type="submit" class="btn <?= $botonClase ?>">
                    <i class="fas fa-save"></i> &nbsp; <?= $botonTexto ?>
                </button>

                <?php if ($esEditar): ?>
                    <a href="<?= SERVERURL; ?>descuento-lista/" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                <?php else: ?>
                    <button type="reset" class="btn btn-secondary btn-raised">
                        <i class="fas fa-times"></i> &nbsp; Cancelar
                    </button>
                <?php endif; ?>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include_once "./vistas/inc/descuentos.php"; ?>
