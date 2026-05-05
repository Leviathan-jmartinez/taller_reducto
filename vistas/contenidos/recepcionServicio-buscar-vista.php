<?php
$pagina = require __DIR__ . '/../inc/pagina.php';
if (!mainModel::tienePermiso('servicio.recepcion.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

$fecha_inicio = $_SESSION['fecha_inicio_recepcion'] ?? '';
$fecha_final = $_SESSION['fecha_final_recepcion'] ?? '';
$nro_recepcion = $_SESSION['nro_recepcion'] ?? '';
$cliente = $_SESSION['cliente_recepcion'] ?? '';
$documento = $_SESSION['documento_recepcion'] ?? '';
$placa = $_SESSION['placa_recepcion'] ?? '';
$estado_recepcion = $_SESSION['estado_recepcion'] ?? '';
$origen_recepcion = $_SESSION['origen_recepcion'] ?? '';
$usuario = $_SESSION['usuario_recepcion'] ?? '';
$tipo_servicio = $_SESSION['tipo_servicio_recepcion'] ?? '';
$prioridad = $_SESSION['prioridad_recepcion'] ?? '';
$filtro_activo = $_SESSION['filtro_recepcion_activo'] ?? '';
$busqueda_general = $_SESSION['busqueda_recepcion'] ?? '';

$hayFiltros = $filtro_activo || $fecha_inicio || $fecha_final || $nro_recepcion || $cliente || $documento || $placa || $estado_recepcion !== '' || $origen_recepcion || $usuario || $tipo_servicio || $prioridad || $busqueda_general;

if (!isset($pagina)) {
    $url = $_GET['views'] ?? "recepcionServicio-buscar/1";
    $url = explode("/", $url);
    $pagina = [$url[0], $url[1] ?? 1];
}
?>

<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR RECEPCIONES
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>recepcionServicio-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVA RECEPCION</a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>recepcionServicio-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR RECEPCION</a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="search" autocomplete="off">
        <input type="hidden" name="modulo" value="recepcion">

        <div class="row">
            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha desde</label>
                    <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="fecha_final">Fecha hasta</label>
                    <input type="date" class="form-control" name="fecha_final" id="fecha_final" value="<?php echo htmlspecialchars($fecha_final, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="col-12 col-md-2">
                <div class="form-group">
                    <label for="nro_recepcion">Numero</label>
                    <input type="number" class="form-control" name="nro_recepcion" id="nro_recepcion" min="1" value="<?php echo htmlspecialchars($nro_recepcion, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="cliente">Cliente</label>
                    <input type="text" class="form-control" name="cliente" id="cliente" maxlength="80" value="<?php echo htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="documento">CI/RUC</label>
                    <input type="text" class="form-control" name="documento" id="documento" maxlength="30" value="<?php echo htmlspecialchars($documento, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="placa">Placa</label>
                    <input type="text" class="form-control" name="placa" id="placa" maxlength="20" value="<?php echo htmlspecialchars($placa, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="estado_recepcion">Estado</label>
                    <select name="estado_recepcion" id="estado_recepcion" class="form-control">
                        <option value="">Todos</option>
                        <option value="1" <?php echo $estado_recepcion === '1' ? 'selected' : ''; ?>>Recepcionado</option>
                        <option value="2" <?php echo $estado_recepcion === '2' ? 'selected' : ''; ?>>En proceso</option>
                        <option value="3" <?php echo $estado_recepcion === '3' ? 'selected' : ''; ?>>Finalizado</option>
                        <option value="0" <?php echo $estado_recepcion === '0' ? 'selected' : ''; ?>>Anulado</option>
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="origen_recepcion">Origen</label>
                    <select name="origen_recepcion" id="origen_recepcion" class="form-control">
                        <option value="">Todos</option>
                        <option value="NORMAL" <?php echo $origen_recepcion === 'NORMAL' ? 'selected' : ''; ?>>Normal</option>
                        <option value="RECLAMO" <?php echo $origen_recepcion === 'RECLAMO' ? 'selected' : ''; ?>>Reclamo</option>
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="tipo_servicio">Tipo de servicio</label>
                    <select name="tipo_servicio" id="tipo_servicio" class="form-control">
                        <option value="">Todos</option>
                        <option value="diagnostico" <?php echo $tipo_servicio === 'diagnostico' ? 'selected' : ''; ?>>Diagnostico</option>
                        <option value="mantenimiento" <?php echo $tipo_servicio === 'mantenimiento' ? 'selected' : ''; ?>>Mantenimiento</option>
                        <option value="reparacion" <?php echo $tipo_servicio === 'reparacion' ? 'selected' : ''; ?>>Reparacion</option>
                        <option value="garantia" <?php echo $tipo_servicio === 'garantia' ? 'selected' : ''; ?>>Garantia</option>
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="prioridad">Prioridad</label>
                    <select name="prioridad" id="prioridad" class="form-control">
                        <option value="">Todas</option>
                        <option value="normal" <?php echo $prioridad === 'normal' ? 'selected' : ''; ?>>Normal</option>
                        <option value="urgente" <?php echo $prioridad === 'urgente' ? 'selected' : ''; ?>>Urgente</option>
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" class="form-control" name="usuario" id="usuario" maxlength="80" value="<?php echo htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="col-12 col-md-3 d-flex align-items-end">
                <div class="form-group w-100">
                    <button type="submit" class="btn btn-raised btn-info btn-block">
                        <i class="fas fa-search"></i> &nbsp; Buscar
                    </button>
                </div>
            </div>
        </div>
    </form>

    <?php if ($hayFiltros) { ?>
        <form class="FormularioAjax text-center mb-3" action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php" method="POST" data-form="search" autocomplete="off">
            <input type="hidden" name="modulo" value="recepcion">
            <input type="hidden" name="eliminar_busqueda" value="eliminar">
            <button type="submit" class="btn btn-raised btn-danger">
                <i class="far fa-trash-alt"></i> &nbsp; Limpiar filtros
            </button>
        </form>
    <?php } ?>
</div>

<div class="container-fluid">
    <?php
    if ($hayFiltros) {
        require_once "./controladores/recepcionservicioControlador.php";
        $ins_recepcion = new recepcionservicioControlador();
        $ins_recepcion->listar_recepcion_controlador(
            $pagina[1],
            15,
            $pagina[0],
            $busqueda_general,
            $fecha_inicio,
            $fecha_final,
            $nro_recepcion,
            $cliente,
            $documento,
            $placa,
            $estado_recepcion,
            $origen_recepcion,
            $usuario,
            $tipo_servicio,
            $prioridad
        );
    } else {
        echo '<div class="alert alert-info text-center" role="alert">
                Use al menos un filtro para ver las recepciones.
              </div>';
    }
    ?>
</div>

<div class="modal fade" id="modalFotosRecepcion" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="far fa-images"></i> Fotos de la recepcion
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="galeria_fotos_recepcion" class="row"></div>
            </div>
        </div>
    </div>
</div>

<script>
    function escaparTextoRecepcion(valor) {
        const div = document.createElement('div');
        div.textContent = valor || '';
        return div.innerHTML;
    }

    function verFotosRecepcion(idRecepcion) {
        const contenedor = document.getElementById('galeria_fotos_recepcion');
        contenedor.innerHTML = '<div class="col-12 text-center text-muted py-4">Cargando fotos...</div>';

        const datos = new FormData();
        datos.append('accion', 'fotos_recepcion');
        datos.append('recepcion_id_fotos', idRecepcion);

        fetch('<?php echo SERVERURL; ?>ajax/recepcionservicioAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success || !Array.isArray(data.fotos) || data.fotos.length === 0) {
                    contenedor.innerHTML = '<div class="col-12 text-center text-muted py-4">Esta recepcion no tiene fotos cargadas.</div>';
                    return;
                }

                contenedor.innerHTML = data.fotos.map((foto, index) => {
                    const ruta = escaparTextoRecepcion(foto.ruta_foto);
                    const url = '<?php echo SERVERURL; ?>' + ruta;

                    return `
                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                            <a href="${url}" target="_blank" class="d-block border rounded bg-light p-2">
                                <img src="${url}"
                                    alt="Foto ${index + 1}"
                                    loading="lazy"
                                    class="img-fluid rounded"
                                    style="width:100%; height:220px; object-fit:cover;">
                            </a>
                        </div>
                    `;
                }).join('');
            })
            .catch(() => {
                contenedor.innerHTML = '<div class="col-12 text-center text-danger py-4">No se pudieron cargar las fotos.</div>';
            });

        $('#modalFotosRecepcion').modal('show');
    }
</script>
