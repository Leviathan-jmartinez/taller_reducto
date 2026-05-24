<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('servicio.reclamo.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$busqueda = $_SESSION['busqueda_reclamo_servicio'] ?? '';
$estado   = $_SESSION['estado_reclamo_servicio'] ?? '';
$ordenReclamoServicio = mainModel::cargar_ordenamiento_sesion('reclamo_servicio', ['fecha', 'estado'], 'fecha', 'DESC');

if (isset($_GET['estado_reclamo_servicio']) && in_array((string)$_GET['estado_reclamo_servicio'], ['0', '1', '2', '3'], true)) {
    $_SESSION['estado_reclamo_servicio'] = (string)$_GET['estado_reclamo_servicio'];
    $estado = (string)$_GET['estado_reclamo_servicio'];
}

$hayFiltro = $busqueda !== '' || $estado !== '';
?>
<style>
    /* Contenedor principal SOLO registro de servicio */
    .reclamo-servicio {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 6px;
    }

    /* Encabezados */
    .reclamo-servicio h3 {
        margin-bottom: 15px;
    }
</style>
<div class="container-fluid reclamo-servicio">
    <h3>
        <i class="fas fa-exclamation-circle"></i>
        &nbsp; RECLAMOS DE SERVICIO
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>/reclamo-servicio-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>/reclamo-servicio-lista/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR
            </a>
        </li>
    </ul>
</div>

<!-- 🔎 FORMULARIO SIEMPRE VISIBLE -->
<div class="container-fluid form-neon reclamo-servicio">

    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="search"
        autocomplete="off">

        <input type="hidden" name="modulo" value="reclamo_servicio">

        <div class="row justify-content-md-center">

            <!-- 🔍 BUSQUEDA -->
            <div class="col-12 col-md-4">
                <label>Buscar</label>
                <input type="text"
                    class="form-control"
                    name="busqueda_inicial"
                    value="<?php echo $busqueda; ?>"
                    placeholder="Cliente, placa, OT...">
            </div>

            <!-- 📊 ESTADO -->
            <div class="col-12 col-md-4">
                <label>Estado</label>
                <select name="estado_reclamo_servicio" class="form-control">
                    <option value="">Todos</option>
                    <option value="1" <?php if ($estado == "1") echo "selected"; ?>>Activo</option>
                    <option value="2" <?php if ($estado == "2") echo "selected"; ?>>En proceso</option>
                    <option value="3" <?php if ($estado == "3") echo "selected"; ?>>Resuelto</option>
                    <option value="0" <?php if ($estado === "0") echo "selected"; ?>>Anulado</option>
                </select>
            </div>

            <!-- 🔘 BOTONES -->
            <div class="col-12 text-center mt-4">

                <button type="submit" class="btn btn-raised btn-info">
                    <i class="fas fa-search"></i> &nbsp; BUSCAR
                </button>

                <button type="button"
                    class="btn btn-raised btn-danger btn-limpiar-busqueda">
                    <i class="fas fa-times"></i> &nbsp; Cancelar
                </button>

            </div>

        </div>
    </form>
</div>

<!-- 🧾 INDICADOR DE FILTRO -->
<?php if ($hayFiltro) { ?>
    <div class="container-fluid mt-2">
        <p class="text-center" style="font-size:14px;">
            Filtro:
            <strong><?php echo $busqueda ?: '---'; ?></strong>
            |
            Estado:
            <strong>
                <?php
                switch ($estado) {
                    case "1":
                        echo "Activo";
                        break;
                    case "2":
                        echo "En proceso";
                        break;
                    case "3":
                        echo "Resuelto";
                        break;
                    case "0":
                        echo "Anulado";
                        break;
                    default:
                        echo "Todos";
                }
                ?>
            </strong>
        </p>
    </div>
<?php
} ?>

<!-- 📋 LISTADO -->
<div class="container-fluid mt-3">
    <?php
    if ($hayFiltro) {
        require_once "./controladores/reclamoServicioControlador.php";

        $reclamo = new reclamoServicioControlador();

        echo $reclamo->listar_reclamo_controlador(
            $pagina[1],
            15,
            $pagina[0],
            $busqueda,
            $ordenReclamoServicio['orden'],
            $ordenReclamoServicio['direccion']
        );
    } else {
        echo '<div class="alert alert-info text-center">
            Ingrese un criterio de busqueda para mostrar reclamos.
        </div>';
    }
    ?>
</div>

<!-- JS LIMPIAR -->
<script>
    document.addEventListener('click', function(e) {

        const btn = e.target.closest('.btn-limpiar-busqueda');
        if (!btn) return;

        fetch("<?php echo SERVERURL; ?>ajax/buscadorAjax.php", {
                method: "POST",
                body: new URLSearchParams({
                    modulo: "reclamo_servicio",
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
