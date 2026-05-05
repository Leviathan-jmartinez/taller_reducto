<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('servicio.diagnostico.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$fecha_inicio = $_SESSION['fecha_inicio_diag'] ?? '';
$fecha_final = $_SESSION['fecha_final_diag'] ?? '';
$nro_diagnostico = $_SESSION['nro_diagnostico_diag'] ?? '';
$nro_recepcion = $_SESSION['nro_recepcion_diag'] ?? '';
$cliente = $_SESSION['cliente_diag'] ?? '';
$placa = $_SESSION['placa_diag'] ?? '';
$estado = $_SESSION['estado_diag'] ?? '';
$origen = $_SESSION['origen_diag'] ?? '';
$busqueda_general = $_SESSION['busqueda_general_diag'] ?? '';
?>

<div class="container-fluid">
    <h3>
        <i class="fas fa-tools"></i> &nbsp; DIAGNÓSTICO DE SERVICIO
    </h3>

    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>/diagnostico-servicio-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO DIAGNÓSTICO
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>/diagnostico-servicio-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR DIAGNÓSTICOS
            </a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="search">

        <input type="hidden" name="modulo" value="diagnostico">

        <div class="row">

            <div class="col-md-3">
                <label>Busqueda general</label>
                <input type="text" name="busqueda_general" class="form-control"
                    placeholder="Cliente, documento, placa, observacion..."
                    value="<?php echo htmlspecialchars($busqueda_general, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="col-md-2">
                <label>Nro. diagnostico</label>
                <input type="number" min="1" name="nro_diagnostico" class="form-control"
                    value="<?php echo htmlspecialchars($nro_diagnostico, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="col-md-2">
                <label>Nro. recepcion</label>
                <input type="number" min="1" name="nro_recepcion" class="form-control"
                    value="<?php echo htmlspecialchars($nro_recepcion, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="col-md-2">
                <label>Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-control"
                    value="<?php echo htmlspecialchars($fecha_inicio, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="col-md-2">
                <label>Fecha fin</label>
                <input type="date" name="fecha_final" class="form-control"
                    value="<?php echo htmlspecialchars($fecha_final, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="col-md-3">
                <label>Cliente</label>
                <input type="text" name="cliente" class="form-control"
                    value="<?php echo htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="col-md-3">
                <label>Placa</label>
                <input type="text" name="placa" class="form-control"
                    value="<?php echo htmlspecialchars($placa, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="col-md-3">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="1" <?php echo $estado === '1' ? 'selected' : ''; ?>>En proceso</option>
                    <option value="2" <?php echo $estado === '2' ? 'selected' : ''; ?>>Presupuestado</option>
                    <option value="3" <?php echo $estado === '3' ? 'selected' : ''; ?>>Finalizado</option>
                    <option value="0" <?php echo $estado === '0' ? 'selected' : ''; ?>>Anulado</option>
                </select>
            </div>

            <div class="col-md-3">
                <label>Origen</label>
                <select name="origen" class="form-control">
                    <option value="">Todos</option>
                    <option value="NORMAL" <?php echo $origen === 'NORMAL' ? 'selected' : ''; ?>>Normal</option>
                    <option value="RECLAMO" <?php echo $origen === 'RECLAMO' ? 'selected' : ''; ?>>Reclamo</option>
                </select>
            </div>

            <div class="col-12 text-center mt-3">
                <button class="btn btn-info mr-2">
                    <i class="fas fa-search"></i> Buscar
                </button>

                <button type="button"
                    class="btn btn-secondary btn-limpiar-busqueda">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </div>
    </form>
</div>

<div class="container-fluid mt-4">
    <?php
require_once "./controladores/diagnosticoControlador.php";
    $diag = new diagnosticoControlador();

    echo $diag->paginador_diagnostico_controlador(
        $pagina[1],
        10,
        $pagina[0],
        $_SESSION['fecha_inicio_diag'] ?? '',
        $_SESSION['fecha_final_diag'] ?? '',
        $_SESSION['cliente_diag'] ?? '',
        $_SESSION['placa_diag'] ?? '',
        $_SESSION['nro_diagnostico_diag'] ?? '',
        $_SESSION['nro_recepcion_diag'] ?? '',
        $_SESSION['estado_diag'] ?? '',
        $_SESSION['origen_diag'] ?? '',
        $_SESSION['busqueda_general_diag'] ?? ''
    );
    ?>
</div>

<div class="modal fade" id="modalDetalleDiagnostico" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye"></i> Detalle del diagnostico
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    &times;
                </button>
            </div>
            <div class="modal-body" id="contenidoDetalleDiagnostico">
                <div class="text-center text-muted py-4">Seleccione un diagnostico.</div>
            </div>
        </div>
    </div>
</div>

<?php
include "./vistas/inc/diagnosticoJS.php"; ?>
