<?php
if (!mainModel::tienePermiso('servicio.diagnostico.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
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
                <label>Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-control"
                    value="<?php echo $_SESSION['fecha_inicio_diag'] ?? ''; ?>">
            </div>

            <div class="col-md-3">
                <label>Fecha fin</label>
                <input type="date" name="fecha_final" class="form-control"
                    value="<?php echo $_SESSION['fecha_final_diag'] ?? ''; ?>">
            </div>

            <div class="col-md-3">
                <label>Cliente</label>
                <input type="text" name="cliente" class="form-control"
                    value="<?php echo $_SESSION['cliente_diag'] ?? ''; ?>">
            </div>

            <div class="col-md-3">
                <label>Placa</label>
                <input type="text" name="placa" class="form-control"
                    value="<?php echo $_SESSION['placa_diag'] ?? ''; ?>">
            </div>

            <div class="col-12 text-center mt-3">
                <button class="btn btn-info mr-2">
                    <i class="fas fa-search"></i> Buscar
                </button>

                <button type="button"
                    class="btn btn-secondary btn-limpiar-busqueda">
                    <i class="fas fa-eraser"></i> Limpiar
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
        $_SESSION['placa_diag'] ?? ''
    );
    ?>
</div>

<?php include "./vistas/inc/diagnosticoJS.php"; ?>