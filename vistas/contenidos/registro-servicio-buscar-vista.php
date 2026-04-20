<?php
if (!mainModel::tienePermiso('servicio.registro.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<div class="container-fluid mb-3">
    <h3>
        <i class="fas fa-clipboard-check"></i>
        &nbsp; REGISTROS DE SERVICIO
    </h3>

    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>/registro-servicio-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO REGISTRO
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>registro-servicio-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR REGISTROS
            </a>
        </li>
    </ul>
</div>

<?php
$fecha_inicio = $_SESSION['fecha_inicio_registro_servicio'] ?? '';
$fecha_final  = $_SESSION['fecha_final_registro_servicio'] ?? '';
?>

<!-- 🔎 FORMULARIO SIEMPRE VISIBLE -->
<div class="container-fluid">
    <form class="form-neon FormularioAjax"
        action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
        method="POST"
        data-form="search"
        autocomplete="off">

        <input type="hidden" name="modulo" value="registro_servicio">

        <div class="row justify-content-md-center">

            <div class="col-12 col-md-4">
                <label>Fecha inicial</label>
                <input type="date"
                    class="form-control"
                    name="fecha_inicio"
                    value="<?php echo $fecha_inicio; ?>">
            </div>

            <div class="col-12 col-md-4">
                <label>Fecha final</label>
                <input type="date"
                    class="form-control"
                    name="fecha_final"
                    value="<?php echo $fecha_final; ?>">
            </div>
            <div class="col-12 col-md-4">
                <select name="estado_registro" class="form-control">
                    <option value="">Todos</option>
                    <option value="1">Registrado</option>
                    <option value="2">Facturado</option>
                    <option value="0">Anulado</option>
                </select>
            </div>
            <div class="col-12 text-center mt-4">

                <button type="submit" class="btn btn-raised btn-info">
                    <i class="fas fa-search"></i> &nbsp; BUSCAR
                </button>

                <button type="button"
                    class="btn btn-raised btn-danger btn-limpiar-busqueda">
                    <i class="far fa-trash-alt"></i> &nbsp; LIMPIAR
                </button>

            </div>
        </div>
    </form>
</div>

<!-- 📊 RESULTADOS SIEMPRE -->
<div class="container-fluid mt-3">
    <?php
    require_once "./controladores/registroServicioControlador.php";

    $reg = new registroServicioControlador();

    echo $reg->listar_registro_servicio_controlador(
        $pagina[1],
        15,
        $pagina[0],
        $fecha_inicio,
        $fecha_final
    );
    ?>
</div>

<!-- 🧹 JS LIMPIAR -->
<script>
    document.addEventListener('click', function(e) {

        const btn = e.target.closest('.btn-limpiar-busqueda');
        if (!btn) return;

        fetch("<?php echo SERVERURL; ?>ajax/buscadorAjax.php", {
                method: "POST",
                body: new URLSearchParams({
                    modulo: "registro_servicio",
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