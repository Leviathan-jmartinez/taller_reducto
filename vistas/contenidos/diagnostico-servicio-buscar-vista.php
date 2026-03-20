<?php
if (!mainModel::tienePermisoVista('servicio.diagnostico.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<div class="container-fluid">
    <h3><i class="fas fa-search"></i> BUSCAR DIAGNÓSTICO</h3>
</div>

<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="default">

        <input type="hidden" name="modulo" value="diagnostico">

        <div class="row">

            <div class="col-md-3">
                <label>Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-control">
            </div>

            <div class="col-md-3">
                <label>Fecha fin</label>
                <input type="date" name="fecha_final" class="form-control">
            </div>

            <div class="col-md-3">
                <label>Cliente</label>
                <input type="text" name="cliente" class="form-control">
            </div>

            <div class="col-md-3">
                <label>Placa</label>
                <input type="text" name="placa" class="form-control">
            </div>

            <div class="col-12 text-center mt-3">
                <button class="btn btn-info">
                    <i class="fas fa-search"></i> Buscar
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
    3,
    $_SESSION['nivel_str'],
    $pagina[0],
    $_SESSION['fecha_inicio_diag'] ?? '',
    $_SESSION['fecha_final_diag'] ?? '',
    $_SESSION['cliente_diag'] ?? '',
    $_SESSION['placa_diag'] ?? ''
);
?>
</div>