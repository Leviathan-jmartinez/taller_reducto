<?php
require_once "./controladores/sucursalControlador.php";
$insSucursal = new sucursalControlador();
$sucursales = $insSucursal->listar_sucursales_controlador();
$empleados = $insSucursal->listar_empleados_controlador();
?>

<div class="container-fluid">
    <div class="form-neon">

        <h4 class="text-center mb-3">
            <i class="fas fa-cogs fa-fw"></i>
            &nbsp; Informe de Registro de Servicios
        </h4>

        <form action="<?= SERVERURL ?>ajax/reportesAjax.php"
            method="POST"
            target="_blank"
            autocomplete="off">

            <input type="hidden" name="accion" value="imprimir_reporte_registro_servicio">

            <div class="row">

                <div class="col-md-3">
                    <label>Desde</label>
                    <input type="date" name="desde" class="form-control">
                </div>

                <div class="col-md-3">
                    <label>Hasta</label>
                    <input type="date" name="hasta" class="form-control">
                </div>

                <div class="col-md-2">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="1">Registrado</option>
                        <option value="2">Facturado</option>
                        <option value="0">Anulado</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Tecnico Encargado</label>
                    <select name="empleado" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($empleados as $s): ?>
                            <option value="<?= $s['idempleados'] ?>">
                                <?= $s['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Sucursal</label>
                    <select name="sucursal" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($sucursales as $s): ?>
                            <option value="<?= $s['id_sucursal'] ?>">
                                <?= $s['suc_descri'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-print"></i> &nbsp; Generar PDF
                </button>
            </div>

        </form>

    </div>
</div>