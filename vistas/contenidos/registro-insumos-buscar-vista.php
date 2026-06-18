<?php
$pagina = require __DIR__ . '/../inc/pagina.php';

if (!mainModel::tienePermiso('servicio.insumo.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$fecha_inicio = $_SESSION['fecha_inicio_salida_insumo'] ?? '';
$fecha_final  = $_SESSION['fecha_final_salida_insumo'] ?? '';
$nro_salida   = $_SESSION['nro_salida_insumo'] ?? '';
$empleado     = $_SESSION['empleado_salida_insumo'] ?? '';
$estado       = $_SESSION['estado_salida_insumo'] ?? '';

$ordenSalida = mainModel::cargar_ordenamiento_sesion(
    'salida_insumo',
    ['fecha', 'estado'],
    'fecha',
    'DESC'
);
?>

<div class="container-fluid form-neon app-view">
    <form class="form-neon FormularioAjax app-form"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="search">

        <input type="hidden" name="modulo" value="salida_insumo">

        <h3>
            <i class="fas fa-box-open"></i> &nbsp; REGISTRO DE INSUMOS UTILIZADOS
        </h3>

        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a href="<?php echo SERVERURL; ?>registro-insumos/">
                    <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO
                </a>
            </li>
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>registro-insumos-buscar/">
                    <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR
                </a>
            </li>
        </ul>

        <div class="row">

            <div class="col-md-2">
                <label>Nro. salida</label>
                <input type="number" min="1" name="nro_salida" class="form-control"
                    value="<?php echo htmlspecialchars($nro_salida, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="col-md-3">
                <label>Empleado</label>
                <input type="text" name="empleado" class="form-control"
                    value="<?php echo htmlspecialchars($empleado, ENT_QUOTES, 'UTF-8'); ?>">
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
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="1" <?php echo $estado === '1' ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?php echo $estado === '0' ? 'selected' : ''; ?>>Anulado</option>
                </select>
            </div>

            <div class="col-12 text-center mt-3">
                <button class="btn btn-info mr-2">
                    <i class="fas fa-search"></i> Buscar
                </button>

                <button type="button" class="btn btn-secondary btn-limpiar-busqueda">
                    <i class="fas fa-times"></i> Limpiar
                </button>
            </div>
        </div>
    </form>



    <?php
    if (isset($_SESSION['filtro_salida_insumo_activo'])) {
        require_once "./controladores/salidaInsumoControlador.php";
        $salida = new salidaInsumoControlador();

        echo $salida->paginador_salida_insumo_controlador(
            $pagina[1],
            10,
            $pagina[0],
            $_SESSION['fecha_inicio_salida_insumo'] ?? '',
            $_SESSION['fecha_final_salida_insumo'] ?? '',
            $_SESSION['nro_salida_insumo'] ?? '',
            $_SESSION['empleado_salida_insumo'] ?? '',
            $_SESSION['estado_salida_insumo'] ?? '',
            $ordenSalida['orden'],
            $ordenSalida['direccion']
        );
    } else {
        echo '
        <div class="alert alert-info text-center">
            Ingrese parámetros de búsqueda para visualizar resultados
        </div>';
    }
    ?>
</div>

<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-limpiar-busqueda');
        if (!btn) return;

        fetch("<?php echo SERVERURL; ?>ajax/buscadorAjax.php", {
                method: "POST",
                body: new URLSearchParams({
                    modulo: "salida_insumo",
                    eliminar_busqueda: 1
                })
            })
            .then(r => r.json())
            .then(res => {
                if (res.Alerta === "redireccionar") {
                    window.location.href = res.URL;
                } else {
                    alert(res.Texto);
                }
            });
    });
</script>