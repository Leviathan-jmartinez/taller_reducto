<?php
require_once "./controladores/ordenTrabajoControlador.php";
$insOT = new ordenTrabajoControlador();

$id = $pagina[1] ?? 0;

$ot = $insOT->obtener_ot_controlador($id);

if (!$ot) {
    echo '<div class="alert alert-danger">OT no encontrada</div>';
    return;
}
?>

<!-- ================= CABECERA ================= -->
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
            <span class="badge bg-warning">Pendiente</span>

            <?php if ($ot['origen'] == 'RECLAMO'): ?>
                <span class="badge bg-danger">Reclamo</span>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- ================= VEHICULO ================= -->
<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        <i class="fas fa-car"></i> Información del vehículo
    </div>

    <div class="card-body">
        <div class="row">

            <div class="col-md-4">
                <strong>Cliente:</strong><br>
                <?php echo $ot['nombre_cliente'] . ' ' . $ot['apellido_cliente']; ?>
            </div>

            <div class="col-md-4">
                <strong>Vehículo:</strong><br>
                <?php echo $ot['placa'] ?? '-'; ?>
            </div>

            <div class="col-md-4">
                <strong>Kilometraje:</strong><br>
                <?php echo $ot['kilometraje'] ?? '-'; ?>
            </div>

        </div>
    </div>
</div>

<!-- ================= RECLAMO ================= -->
<?php if ($ot['origen'] == 'RECLAMO'): ?>
    <div class="card mb-3 border-warning">
        <div class="card-header bg-dark">
            <i class="fas fa-exclamation-triangle"></i> Detalle del Reclamo
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">
                    <strong>Tipo:</strong><br>
                    <?php echo $ot['tipo_reclamo'] ?? '-'; ?>
                </div>

                <div class="col-md-4">
                    <strong>Prioridad:</strong><br>
                    <?php
                    echo $ot['prioridad'] == 1 ? 'Alta' : ($ot['prioridad'] == 2 ? 'Media' : 'Baja');
                    ?>
                </div>

                <div class="col-md-4">
                    <strong>Fecha:</strong><br>
                    <?php echo !empty($ot['fecha_reclamo'])
                        ? date("d/m/Y H:i", strtotime($ot['fecha_reclamo']))
                        : '-'; ?>
                </div>

                <div class="col-12 mt-2">
                    <strong>Descripción:</strong><br>
                    <?php echo $ot['descripcion'] ?? '-'; ?>
                </div>

            </div>

        </div>
    </div>
<?php endif; ?>

<!-- ================= FORM OT ================= -->
<div class="card">
    <div class="card-header bg-dark text-white">
        <i class="fas fa-edit"></i> Completar Orden de Trabajo
    </div>

    <div class="card-body">

        <form class="FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/ordenTrabajoAjax.php"
            method="POST"
            data-form="save">

            <input type="hidden" name="accion" value="completar_ot">
            <input type="hidden" name="idorden_trabajo" value="<?php echo $ot['idorden_trabajo']; ?>">

            <div class="row">

                <!-- TECNICO -->
                <div class="col-md-4">
                    <label>Técnico</label>
                    <select name="tecnico_responsable" class="form-control" required>
                        <?php echo $insOT->listar_tecnicos_select(); ?>
                    </select>
                </div>

                <!-- EQUIPO -->
                <div class="col-md-4">
                    <label>Equipo</label>
                    <select name="idtrabajos" class="form-control" required>
                        <?php echo $insOT->listar_equipos_select(); ?>
                    </select>
                </div>

                <!-- OBS -->
                <div class="col-md-4">
                    <label>Observación</label>
                    <input type="text" name="observacion" class="form-control"
                        value="<?php echo $ot['observacion'] ?? ''; ?>">
                </div>

            </div>

            <hr>

            <!-- TRABAJOS -->
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

            <table class="table  table-dark table-sm">
                <thead>
                    <tr>
                        <th>Servicio realizado</th>
                        <th width="80">Acción</th>
                    </tr>
                </thead>
                <tbody id="lista_trabajos"></tbody>
            </table>

            <!-- REPUESTOS -->
            <h5><i class="fas fa-cogs"></i> Repuestos</h5>

            <div class="row mb-2">
                <div class="col-md-5">
                    <input type="text" id="buscar_articulo" class="form-control" placeholder="Buscar artículo...">
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

            <table class="table  table-dark table-sm">
                <thead>
                    <tr>
                        <th>Repuesto</th>
                        <th>Cant</th>
                        <th width="80">Acción</th>
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

<?php include_once "./vistas/inc/ordenTrabajoJS.php"; ?>