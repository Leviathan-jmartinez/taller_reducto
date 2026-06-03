<?php
if (!isset($pagina) || !is_array($pagina)) {
    $pagina = explode('/', trim($_GET['vista'] ?? '', '/'));
}

require_once "./controladores/ordenTrabajoControlador.php";

if (!mainModel::tienePermiso('servicio.ot.asignar_tecnico')) {
    echo '<div class="alert alert-danger">No posee permisos para completar OT por reclamo.</div>';
    return;
}

$insOT = new ordenTrabajoControlador();
$id = $_GET['id'] ?? ($pagina[1] ?? 0);
$ot = $insOT->obtener_ot_controlador($id);

if (!$ot) {
    echo '<div class="alert alert-danger">OT no encontrada</div>';
    return;
}

if (($ot['origen'] ?? '') !== 'RECLAMO' || (int)$ot['estado'] !== 3) {
    echo '<div class="alert alert-warning">Solo las OT pendientes por reclamo se completan desde esta pantalla.</div>';
    return;
}

$detalleDiagnostico = !empty($ot['id_diagnostico_reclamo'])
    ? $insOT->obtener_detalle_diagnostico_ot_controlador($ot['id_diagnostico_reclamo'])
    : [];
?>

<div class="card mb-3 shadow-sm">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-tools"></i> OT #<?php echo $ot['idorden_trabajo']; ?>
            </h4>
            <small class="text-muted">
                <?php echo !empty($ot['fecha_inicio'])
                    ? date("d/m/Y H:i", strtotime($ot['fecha_inicio']))
                    : '-'; ?>
            </small>
        </div>
        <div>
            <span class="badge bg-info">Pendiente completar</span>
            <span class="badge bg-danger">Reclamo</span>
            <a href="<?php echo SERVERURL; ?>ordenTrabajo-buscar/"
                class="btn btn-secondary btn-sm ml-2">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        <i class="fas fa-car"></i> Informacion del vehiculo
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Cliente:</strong><br>
                <?php echo $ot['nombre_cliente'] . ' ' . $ot['apellido_cliente']; ?>
            </div>
            <div class="col-md-4">
                <strong>Vehiculo:</strong><br>
                <?php
                $vehiculo = trim(($ot['marca'] ?? '') . ' ' . ($ot['modelo'] ?? '') . ' ' . ($ot['placa'] ?? ''));
                echo $vehiculo !== '' ? $vehiculo : '-';
                ?>
            </div>
            <div class="col-md-4">
                <strong>Kilometraje:</strong><br>
                <?php echo $ot['kilometraje'] ?? '-'; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        <i class="fas fa-stethoscope"></i> Diagnostico del reclamo
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Diagnostico:</strong><br>
                <?php echo !empty($ot['id_diagnostico_reclamo']) ? '#' . $ot['id_diagnostico_reclamo'] : '-'; ?>
            </div>
            <div class="col-md-3">
                <strong>Fecha:</strong><br>
                <?php echo !empty($ot['fecha_diagnostico'])
                    ? date("d/m/Y H:i", strtotime($ot['fecha_diagnostico']))
                    : '-'; ?>
            </div>
            <div class="col-md-2">
                <strong>Garantia:</strong><br>
                <?php echo !empty($ot['es_garantia']) ? 'Si' : 'No'; ?>
            </div>
            <div class="col-md-2">
                <strong>Reclamo valido:</strong><br>
                <?php echo !empty($ot['es_reclamo_valido']) ? 'Si' : 'No'; ?>
            </div>
            <div class="col-md-2">
                <strong>Requiere cobro:</strong><br>
                <?php echo !empty($ot['requiere_cobro']) ? 'Si' : 'No'; ?>
            </div>
            <div class="col-12 mt-2">
                <strong>Observacion:</strong><br>
                <?php
                $diagnosticoTexto = $ot['diagnostico_general']
                    ?: ($ot['diagnostico_observaciones'] ?: ($ot['diagnostico_descripcion_cliente'] ?? ''));
                echo $diagnosticoTexto ?: '-';
                ?>
            </div>
            <div class="col-12 mt-3">
                <strong>Detalle tecnico:</strong>
                <div class="table-responsive mt-2">
                    <table class="table table-dark table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Problema</th>
                                <th>Gravedad</th>
                                <th>Repuesto</th>
                                <th>Cantidad</th>
                                <th>Origen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($detalleDiagnostico)) { ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Sin detalle tecnico</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($detalleDiagnostico as $det) { ?>
                                    <tr>
                                        <td><?php echo $det['servicio'] ?: '-'; ?></td>
                                        <td><?php echo $det['problema'] ?: '-'; ?></td>
                                        <td><?php echo $det['gravedad'] ?: '-'; ?></td>
                                        <td><?php echo $det['repuesto'] ?: '-'; ?></td>
                                        <td><?php echo $det['cantidad_repuesto'] ?: '-'; ?></td>
                                        <td><?php echo $det['repuesto_origen'] ?: '-'; ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-dark text-white">
        <i class="fas fa-edit"></i> Completar OT por reclamo
    </div>
    <div class="card-body">
        <form class="FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/ordenTrabajoAjax.php"
            method="POST"
            data-form="save">

            <input type="hidden" name="accion" value="completar_ot">
            <input type="hidden" name="idorden_trabajo" value="<?php echo $ot['idorden_trabajo']; ?>">
            <input type="hidden" name="trabajos_json" id="trabajos_json">
            <input type="hidden" name="repuestos_json" id="repuestos_json">

            <div class="row">
                <div class="col-md-4">
                    <label>Equipo</label>
                    <select name="idtrabajos" class="form-control" required>
                        <?php echo $insOT->listar_equipos_select(); ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Tecnico</label>
                    <select name="tecnico_responsable" class="form-control" required disabled>
                        <option value="">Seleccione un equipo primero</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Observacion</label>
                    <input type="text" name="observacion" class="form-control"
                        value="<?php echo $ot['observacion'] ?? ''; ?>">
                </div>
            </div>

            <hr>

            <h5><i class="fas fa-wrench"></i> Trabajos</h5>
            <div class="row mb-2">
                <div class="col-md-8">
                    <input type="text" id="buscar_servicio" class="form-control" placeholder="Buscar servicio...">
                    <div id="resultado_servicios" class="list-group"></div>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-primary w-100" onclick="agregarServicio()">
                        Agregar
                    </button>
                </div>
            </div>
            <table class="table table-dark table-sm">
                <thead>
                    <tr>
                        <th>Servicio realizado</th>
                        <th width="80">Accion</th>
                    </tr>
                </thead>
                <tbody id="lista_trabajos"></tbody>
            </table>

            <h5><i class="fas fa-cogs"></i> Repuestos</h5>
            <div class="row mb-2">
                <div class="col-md-5">
                    <input type="text" id="buscar_articulo" class="form-control" placeholder="Buscar articulo...">
                    <div id="resultado_articulos" class="list-group"></div>
                </div>
                <div class="col-md-3">
                    <input type="number" id="rep_cantidad" class="form-control" placeholder="Cantidad">
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-primary w-100" onclick="agregarRepuesto()">
                        Agregar
                    </button>
                </div>
            </div>
            <table class="table table-dark table-sm">
                <thead>
                    <tr>
                        <th>Repuesto</th>
                        <th>Cant</th>
                        <th width="80">Accion</th>
                    </tr>
                </thead>
                <tbody id="lista_repuestos"></tbody>
            </table>

            <button class="btn btn-success">
                <i class="fas fa-save"></i> Guardar OT
            </button>
        </form>
    </div>
</div>


<?php
$trabajosIniciales = [];
$repuestosIniciales = [];

foreach ($detalleDiagnostico as $det) {

    if (!empty($det['id_articulo_servicio'])) {

        $trabajosIniciales[] = [
            'id_articulo' => (int)$det['id_articulo_servicio'],
            'descripcion' => $det['servicio'] ?? ''
        ];
    }

    if (!empty($det['id_articulo_repuesto'])) {

        $repuestosIniciales[] = [
            'id_articulo' => (int)$det['id_articulo_repuesto'],
            'descripcion' => $det['repuesto'] ?? '',
            'cantidad' => (float)$det['cantidad_repuesto']
        ];
    }
}
?>

<script>
    window.TRABAJOS_DIAGNOSTICO =
        <?php echo json_encode($trabajosIniciales, JSON_UNESCAPED_UNICODE); ?>;

    window.REPUESTOS_DIAGNOSTICO =
        <?php echo json_encode($repuestosIniciales, JSON_UNESCAPED_UNICODE); ?>;
</script>

<?php include_once "./vistas/inc/ordenTrabajoJS.php"; ?>
