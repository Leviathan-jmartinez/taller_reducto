<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('servicio.registro.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<?php
$fecha_inicio = $_SESSION['fecha_inicio_registro_servicio'] ?? '';
$fecha_final  = $_SESSION['fecha_final_registro_servicio'] ?? '';
$estado = $_SESSION['estado_regSer'] ?? '';
$ordenRegistroServicio = mainModel::cargar_ordenamiento_sesion('registro_servicio', ['fecha', 'estado'], 'fecha', 'DESC');
$busqueda_activa = isset($_SESSION['filtro_registro_servicio_activo']);
?>

<!-- 🔎 FORMULARIO SIEMPRE VISIBLE -->
<div class="container-fluid form-neon app-view">
    <h3>
        <i class="fas fa-clipboard-check"></i>
        &nbsp; REGISTRO DE SERVICIO
    </h3>

    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>/registro-servicio-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>registro-servicio-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR
            </a>
        </li>
    </ul>
    <form class="form-neon FormularioAjax app-form"
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
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado_regSer" class="form-control">
                        <option value="">Todos</option>
                        <option value="1" <?php if ($estado == "1") echo "selected"; ?>>Registrado</option>
                        <option value="2" <?php if ($estado == "2") echo "selected"; ?>>Facturado</option>
                        <option value="3" <?php if ($estado == "3") echo "selected"; ?>>Con Reclamo</option>
                        <option value="0" <?php if ($estado === "0") echo "selected"; ?>>Anulada</option>
                    </select>
                </div>
            </div>
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


<div class="container-fluid mt-3">
    <?php
    if ($busqueda_activa) {
        require_once "./controladores/registroServicioControlador.php";

        $reg = new registroServicioControlador();

        echo $reg->listar_registro_servicio_controlador(
            $pagina[1],
            15,
            $pagina[0],
            $fecha_inicio,
            $fecha_final,
            $ordenRegistroServicio['orden'],
            $ordenRegistroServicio['direccion']
        );
    } else {
        echo '<div class="alert alert-info text-center">Ingrese filtros y presione Buscar para ver registros.</div>';
    }
    ?>
</div>

<div class="modal fade" id="modalDetalleRegistroServicio" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-clipboard-list"></i>
                    &nbsp; Detalle del servicio realizado
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenidoDetalleRegistroServicio">
                <div class="text-center text-muted py-4">Seleccione un registro</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>


<?php
include_once "./vistas/inc/registroServicioJS.php"; ?>
