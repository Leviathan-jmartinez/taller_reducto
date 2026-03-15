<?php
if (!mainModel::tienePermisoVista('servicio.diagnostico.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$id_recepcion = $_GET['id'] ?? 0;
?>

<div class="container-fluid">

    <div class="container-fluid">
        <h3 class="text-left">
            <i class="fas fa-stethoscope fa-fw"></i> &nbsp; DIAGNÓSTICO DEL VEHÍCULO
        </h3>

        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a class="active" href="#">
                    <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO DIAGNÓSTICO
                </a>
            </li>
            <li>
                <a href="<?php echo SERVERURL; ?>recepcionServicio-buscar/">
                    <i class="fas fa-search fa-fw"></i> &nbsp; RECEPCIONES
                </a>
            </li>
        </ul>
    </div>

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/diagnosticoAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="guardar_diagnostico">
        <input type="hidden" name="id_recepcion" value="<?php echo $id_recepcion; ?>">

        <!-- DATOS GENERALES -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos del Diagnóstico</legend>

            <div class="row">

                <div class="col-md-4">
                    <label>Fecha diagnóstico</label>
                    <input type="datetime-local"
                        name="fecha_diagnostico"
                        class="form-control"
                        value="<?php echo date('Y-m-d\TH:i'); ?>"
                        required>
                </div>

                <div class="col-md-4">
                    <label>Mecánico responsable</label>
                    <select name="id_usuario" class="form-control" required>
                        <option value="">Seleccione mecánico</option>

                        <?php
                        $sql = mainModel::conectar()->query("
                            select CONCAT(e.nombre ,' ',e.apellido) as mecanico from equipo_empleado ee
                    inner join empleados e on e.idempleados = ee.idempleados 
                    where e.empleado_estado ='1'
                        ");

                        while ($row = $sql->fetch()) {
                            echo '<option value="' . $row['idempleados'] . '">' . $row['mecanico'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Costo estimado</label>
                    <input type="number"
                        name="costo_estimado"
                        class="form-control"
                        step="0.01"
                        min="0">
                </div>

            </div>

        </fieldset>

        <!-- FALLA DETECTADA -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Falla Detectada</legend>

            <textarea class="form-control"
                name="descripcion_falla"
                rows="4"
                placeholder="Describa la falla detectada"></textarea>

        </fieldset>

        <!-- PRUEBAS REALIZADAS -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Pruebas Realizadas</legend>

            <textarea class="form-control"
                name="pruebas_realizadas"
                rows="4"
                placeholder="Describa las pruebas realizadas para detectar la falla"></textarea>

        </fieldset>

        <!-- CONCLUSIÓN -->
        <fieldset class="border p-3 mb-4">
            <legend class="w-auto px-2">Conclusión del Diagnóstico</legend>

            <textarea class="form-control"
                name="conclusion"
                rows="4"
                placeholder="Conclusión y recomendación de reparación"></textarea>

        </fieldset>

        <!-- BOTONES -->
        <div class="text-center">

            <button type="submit" class="btn btn-info btn-raised">
                <i class="fas fa-save"></i> &nbsp; Guardar Diagnóstico
            </button>

            <a href="<?php echo SERVERURL; ?>recepcionServicio-buscar/"
                class="btn btn-secondary btn-raised">
                <i class="fas fa-arrow-left"></i> &nbsp; Volver
            </a>

        </div>

    </form>

</div>