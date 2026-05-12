<?php
$pagina = require __DIR__ . '/../inc/pagina.php';

if (!mainModel::tienePermiso('servicio.regla_comercial.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}

require_once "./controladores/reglaComercialControlador.php";
$insRegla = new reglaComercialControlador();
$sucursales = mainModel::conectar()->query("SELECT id_sucursal, suc_descri FROM sucursales WHERE estado = 1 ORDER BY suc_descri")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-project-diagram"></i> &nbsp; LISTADO DE REGLAS COMERCIALES
    </h3>

    <div class="container-fluid">
        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a href="<?= SERVERURL; ?>regla-comercial-nuevo/">
                    <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVA REGLA
                </a>
            </li>
            <li>
                <a class="active" href="<?= SERVERURL; ?>regla-comercial-lista/">
                    <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE REGLAS
                </a>
            </li>
        </ul>
    </div>

    <form method="GET" class="form-neon mb-3">
        <div class="row">
            <div class="col-md-3">
                <label>Buscar</label>
                <input type="text" name="buscar" class="form-control"
                    value="<?= htmlspecialchars($_GET['buscar'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Nombre o descripcion">
            </div>
            <div class="col-md-2">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="1" <?= (($_GET['estado'] ?? '') === '1') ? 'selected' : '' ?>>Activas</option>
                    <option value="0" <?= (($_GET['estado'] ?? '') === '0') ? 'selected' : '' ?>>Inactivas</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Sucursal</label>
                <select name="id_sucursal" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach ($sucursales as $s): ?>
                        <option value="<?= $s['id_sucursal'] ?>" <?= (($_GET['id_sucursal'] ?? '') == $s['id_sucursal']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['suc_descri'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="vigente" name="vigente" value="1"
                        <?= !empty($_GET['vigente']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="vigente">Solo vigentes</label>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm mr-1">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="<?= SERVERURL; ?>regla-comercial-lista/" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>
    </form>

    <?= $insRegla->listar_reglas_controlador($pagina[1], 15, $pagina[0]); ?>
</div>
