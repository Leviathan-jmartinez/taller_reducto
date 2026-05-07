<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('servicio.presupuesto.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$fecha_inicio = $_SESSION['fecha_inicio_presupuesto_servicio'] ?? '';
$fecha_final  = $_SESSION['fecha_final_presupuesto_servicio'] ?? '';
$estado = $_SESSION['estado_presupuesto'] ?? '';
$busqueda_activa = isset($_SESSION['filtro_presupuesto_servicio_activo']);
?>

<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR PRESUPUESTO DE SERVICIO
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>presupuesto-servicio-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PRESUPUESTO
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>presupuesto-servicio-buscar/">
                <i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA
            </a>
        </li>
    </ul>
</div>

<!-- ================= FILTRO ================= -->

<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="search"
        autocomplete="off">

        <input type="hidden" name="modulo" value="presupuesto_servicio">

        <div class="row justify-content-md-center">

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label>Fecha inicial</label>
                    <input type="date"
                        class="form-control"
                        name="fecha_inicio"
                        value="<?php echo $fecha_inicio; ?>">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label>Fecha final</label>
                    <input type="date"
                        class="form-control"
                        name="fecha_final"
                        value="<?php echo $fecha_final; ?>">
                </div>
            </div>
            <select name="estado_presupuesto" class="form-control">
                <option value="">Todos</option>
                <option value="1" <?php if ($estado == "1") echo "selected"; ?>>Pendiente</option>
                <option value="2" <?php if ($estado == "2") echo "selected"; ?>>Aprobado</option>
                <option value="3" <?php if ($estado == "3") echo "selected"; ?>>OT generada</option>
                <option value="4" <?php if ($estado == "4") echo "selected"; ?>>Facturado</option>
                <option value="0" <?php if ($estado === "0") echo "selected"; ?>>Anulado</option>
            </select>
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

<!-- ================= INFO FILTRO ================= -->

<?php if ($busqueda_activa) { ?>

    <div class="container-fluid mt-3">
        <p class="text-center" style="font-size: 18px;">
            Mostrando resultados
            <?php if ($fecha_inicio) { ?>
                desde <strong><?php echo $fecha_inicio; ?></strong>
            <?php
} ?>
            <?php if ($fecha_final) { ?>
                hasta <strong><?php echo $fecha_final; ?></strong>
            <?php
} ?>
            <?php if ($estado !== '') {
                $estados = [
                    '1' => 'Pendiente',
                    '2' => 'Aprobado',
                    '3' => 'OT generada',
                    '4' => 'Facturado',
                    '0' => 'Anulado'
                ]; ?>
                estado <strong><?php echo $estados[$estado] ?? $estado; ?></strong>
            <?php
} ?>
        </p>
    </div>

<?php
} ?>

<!-- ================= RESULTADOS ================= -->

<div class="container-fluid mt-3">
    <?php
    if ($busqueda_activa) {
        require_once "./controladores/presupuestoServicioControlador.php";
        $presupuesto = new presupuestoServicioControlador();

        echo $presupuesto->paginador_presupuestoservi_controlador(
            $pagina[1],
            15,
            $pagina[0],
            $fecha_inicio,
            $fecha_final
        );
    } else {
        echo '<div class="alert alert-info text-center">Ingrese un criterio de busqueda para ver presupuestos.</div>';
    }
    ?>
</div>

<!-- ================= JS LIMPIAR ================= -->

<script>
    document.addEventListener('click', function(e) {

        const btn = e.target.closest('.btn-limpiar-busqueda');
        if (!btn) return;

        fetch("<?php echo SERVERURL; ?>ajax/buscadorAjax.php", {
                method: "POST",
                body: new URLSearchParams({
                    modulo: "presupuesto_servicio",
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
