<?php
if (!mainModel::tienePermiso('compra.oc.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($pagina)) {
    $url = $_GET['views'] ?? "oc-buscar/1";
    $url = explode("/", $url);
    $pagina = [$url[0], $url[1] ?? 1];
}

$fecha_inicio = $_SESSION['fecha_inicio_ordencompra2'] ?? null;
$fecha_final  = $_SESSION['fecha_final_ordencompra2'] ?? null;
$proveedor    = $_SESSION['proveedor_oc'] ?? '';
$estado_oc    = $_SESSION['estado_oc'] ?? '';
$busqueda_activa = !empty($fecha_inicio) || !empty($fecha_final) || !empty($proveedor) || isset($_SESSION['estado_oc']);
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; LISTADO DE ORDENES DE COMPRA
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?= SERVERURL; ?>oc-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; GENERAR ORDEN DE COMPRA
            </a>
        </li>
        <li>
            <a href="<?= SERVERURL; ?>oc-lista/">
                <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTADOS DE ORDENES
            </a>
        </li>
        <li>
            <a class="active" href="<?= SERVERURL; ?>oc-buscar/">
                <i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA
            </a>
        </li>
    </ul>
</div>

<?php if (!$busqueda_activa) { ?>

    <div class="container-fluid">
        <form class="form-neon FormularioAjax"
            action="<?= SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="search"
            autocomplete="off">

            <input type="hidden" name="modulo" value="ordencompra2">

            <div class="container-fluid">
                <div class="row justify-content-md-center">

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label>Fecha inicial</label>
                            <input type="date"
                                class="form-control"
                                name="fecha_inicio">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label>Fecha final</label>
                            <input type="date"
                                class="form-control"
                                name="fecha_final">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label>Proveedor</label>
                            <input type="text"
                                class="form-control"
                                name="proveedor"
                                placeholder="Razon social">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label>Estado</label>
                            <select class="form-control" name="estado_oc">
                                <option value="">Todos</option>
                                <option value="1">Pendiente</option>
                                <option value="2">Procesado</option>
                                <option value="0">Anulado</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <p class="text-center mt-4">
                            <button type="submit" class="btn btn-raised btn-info">
                                <i class="fas fa-search"></i> &nbsp; BUSCAR
                            </button>
                        </p>
                    </div>

                </div>
            </div>
        </form>
    </div>

<?php } else { ?>

    <div class="container-fluid">
        <form class="FormularioAjax"
            action="<?= SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="search"
            autocomplete="off">

            <input type="hidden" name="modulo" value="ordencompra2">
            <input type="hidden" name="eliminar_busqueda" value="eliminar">

            <div class="container-fluid">
                <div class="row justify-content-md-center">

                    <div class="col-12 col-md-8">
                        <p class="text-center" style="font-size: 20px;">
                            Busqueda:
                            <strong>
                                <?php
                                $criterios = [];

                                if (!empty($fecha_inicio) || !empty($fecha_final)) {
                                    $criterios[] = "Fecha: " . ($fecha_inicio ?: 'inicio') . " a " . ($fecha_final ?: 'final');
                                }

                                if (!empty($proveedor)) {
                                    $criterios[] = "Proveedor: " . htmlspecialchars($proveedor, ENT_QUOTES, 'UTF-8');
                                }

                                if (isset($_SESSION['estado_oc'])) {
                                    $estados = [
                                        ""  => "Todos",
                                        "0" => "Anulado",
                                        "1" => "Pendiente",
                                        "2" => "Procesado"
                                    ];
                                    $criterios[] = "Estado: " . ($estados[(string)$estado_oc] ?? $estado_oc);
                                }

                                echo implode(" | ", $criterios);
                                ?>
                            </strong>
                        </p>
                    </div>

                    <div class="col-12">
                        <p class="text-center mt-3">
                            <button type="submit" class="btn btn-raised btn-danger">
                                <i class="far fa-trash-alt"></i> &nbsp; ELIMINAR BUSQUEDA
                            </button>
                        </p>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <div class="container-fluid">
        <?php
        require_once "./controladores/ordencompraControlador.php";

        $ins_ordencompra = new ordencompraControlador();

        echo $ins_ordencompra->paginador_ordencompra_controlador(
            $pagina[1],
            15,
            $pagina[0],
            $fecha_inicio,
            $fecha_final
        );
        ?>
    </div>

<?php } ?>
