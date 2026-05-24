<?php
$homeNombre = trim(($_SESSION['nombre_str'] ?? '') . ' ' . ($_SESSION['apellido_str'] ?? ''));
$homeSucursal = $_SESSION['nick_sucursal'] ?? null;
$homeConexion = mainModel::conectar();

$contarHome = function ($consulta, $parametros = []) use ($homeConexion) {
    $sql = $homeConexion->prepare($consulta);
    $sql->execute($parametros);
    $fila = $sql->fetch(PDO::FETCH_ASSOC);
    return (int)($fila['total'] ?? 0);
};

$metricas = [];
$metricasServicios = [];

if ($homeSucursal !== null && mainModel::tienePermiso('compra.pedido.ver')) {
    $metricas[] = [
        'titulo' => 'Pedidos pendientes',
        'valor' => $contarHome(
            'SELECT COUNT(*) AS total FROM pedido_cabecera WHERE estado = 1 AND id_sucursal = :sucursal',
            [':sucursal' => $homeSucursal]
        ),
        'icono' => 'fas fa-file-alt',
        'url' => SERVERURL . 'pedido-buscar/?estado_pedido=1'
    ];
}

if ($homeSucursal !== null && mainModel::tienePermiso('compra.presupuesto.ver')) {
    $metricas[] = [
        'titulo' => 'Presupuestos activos',
        'valor' => $contarHome(
            'SELECT COUNT(*) AS total FROM presupuesto_compra WHERE estado = 1 AND id_sucursal = :sucursal',
            [':sucursal' => $homeSucursal]
        ),
        'icono' => 'fas fa-file-invoice-dollar',
        'url' => SERVERURL . 'presupuesto-buscar/?estado_presupuesto_compra=1'
    ];
}

if ($homeSucursal !== null && mainModel::tienePermiso('compra.oc.ver')) {
    $metricas[] = [
        'titulo' => 'Ordenes abiertas',
        'valor' => $contarHome(
            'SELECT COUNT(*) AS total FROM orden_compra WHERE estado = 1 AND id_sucursal = :sucursal',
            [':sucursal' => $homeSucursal]
        ),
        'icono' => 'fas fa-file-invoice',
        'url' => SERVERURL . 'oc-buscar/?estado_oc=1'
    ];
}

if ($homeSucursal !== null && mainModel::tienePermiso('compra.factura.ver')) {
    $metricas[] = [
        'titulo' => 'Compras de hoy',
        'valor' => $contarHome(
            'SELECT COUNT(*) AS total FROM compra_cabecera WHERE DATE(fecha_creacion) = CURDATE() AND id_sucursal = :sucursal AND estado <> 0',
            [':sucursal' => $homeSucursal]
        ),
        'icono' => 'fas fa-shopping-cart',
        'url' => SERVERURL . 'factura-buscar/?hoy=1'
    ];
}

if ($homeSucursal !== null && mainModel::tienePermiso('servicio.recepcion.ver')) {
    $metricasServicios[] = [
        'titulo' => 'Recepciones pendientes',
        'valor' => $contarHome(
            'SELECT COUNT(*) AS total FROM recepcion_servicio WHERE estado = 1 AND id_sucursal = :sucursal',
            [':sucursal' => $homeSucursal]
        ),
        'icono' => 'fas fa-file-signature',
        'url' => SERVERURL . 'recepcionServicio-buscar/?estado_recepcion=1'
    ];
}

if ($homeSucursal !== null && mainModel::tienePermiso('servicio.diagnostico.ver')) {
    $metricasServicios[] = [
        'titulo' => 'Diagnosticos en proceso',
        'valor' => $contarHome(
            'SELECT COUNT(*) AS total FROM diagnostico_servicio WHERE estado = 1 AND id_sucursal = :sucursal',
            [':sucursal' => $homeSucursal]
        ),
        'icono' => 'fas fa-stethoscope',
        'url' => SERVERURL . 'diagnostico-servicio-buscar/?estado_diag=1'
    ];
}

if ($homeSucursal !== null && mainModel::tienePermiso('servicio.ot.ver')) {
    $metricasServicios[] = [
        'titulo' => 'Ordenes activas',
        'valor' => $contarHome(
            'SELECT COUNT(*) AS total FROM orden_trabajo WHERE estado = 1 AND id_sucursal = :sucursal',
            [':sucursal' => $homeSucursal]
        ),
        'icono' => 'fas fa-clipboard-check',
        'url' => SERVERURL . 'ordenTrabajo-buscar/?estado_ot=1'
    ];
}

if ($homeSucursal !== null && mainModel::tienePermiso('servicio.reclamo.ver')) {
    $metricasServicios[] = [
        'titulo' => 'Reclamos activos',
        'valor' => $contarHome(
            'SELECT COUNT(*) AS total FROM reclamo_servicio WHERE estado = 1 AND id_sucursal = :sucursal',
            [':sucursal' => $homeSucursal]
        ),
        'icono' => 'fas fa-exclamation-circle',
        'url' => SERVERURL . 'reclamo-servicio-lista/?estado_reclamo_servicio=1'
    ];
}

$acciones = [
    ['permiso' => 'compra.pedido.crear', 'titulo' => 'Nuevo pedido', 'detalle' => 'Solicitar articulos', 'icono' => 'fas fa-file-alt', 'url' => SERVERURL . 'pedido-nuevo/'],
    ['permiso' => 'compra.presupuesto.crear', 'titulo' => 'Nuevo presupuesto', 'detalle' => 'Cotizacion de compra', 'icono' => 'fas fa-file-invoice-dollar', 'url' => SERVERURL . 'presupuesto-nuevo/'],
    ['permiso' => 'compra.oc.crear', 'titulo' => 'Nueva orden', 'detalle' => 'Orden de compra', 'icono' => 'fas fa-file-invoice', 'url' => SERVERURL . 'oc-nuevo/'],
    ['permiso' => 'compra.crear', 'titulo' => 'Registrar compra', 'detalle' => 'Factura proveedor', 'icono' => 'fas fa-shopping-cart', 'url' => SERVERURL . 'factura-nuevo/'],
    ['permiso' => 'servicio.recepcion.crear', 'titulo' => 'Recepcion servicio', 'detalle' => 'Nueva solicitud', 'icono' => 'fas fa-file-signature', 'url' => SERVERURL . 'recepcionServicio-nuevo/'],
    ['permiso' => 'servicio.diagnostico.crear', 'titulo' => 'Diagnostico', 'detalle' => 'Evaluar servicio', 'icono' => 'fas fa-stethoscope', 'url' => SERVERURL . 'diagnostico-servicio-nuevo/'],
    ['permiso' => 'servicio.ot.generar', 'titulo' => 'Orden de trabajo', 'detalle' => 'Generar OT', 'icono' => 'fas fa-clipboard-check', 'url' => SERVERURL . 'ordenTrabajo-nuevo/'],
    ['permiso' => 'servicio.registro.crear', 'titulo' => 'Registro servicio', 'detalle' => 'Cerrar trabajo', 'icono' => 'fas fa-cogs', 'url' => SERVERURL . 'registro-servicio-nuevo/'],
    ['permiso' => 'servicio.reclamo.crear', 'titulo' => 'Nuevo reclamo', 'detalle' => 'Seguimiento postservicio', 'icono' => 'fas fa-exclamation-circle', 'url' => SERVERURL . 'reclamo-servicio-nuevo/'],
    ['permiso' => 'inventario.ver', 'titulo' => 'Inventario', 'detalle' => 'Conteo y ajuste fisico', 'icono' => 'fas fa-clipboard-list', 'url' => SERVERURL . 'inventario/']
];

$accionesPermitidas = array_filter($acciones, function ($accion) {
    return mainModel::tienePermiso($accion['permiso']);
});

$informes = [
    ['permiso' => 'reportes.pedidos.ver', 'titulo' => 'Pedidos', 'url' => SERVERURL . 'reporte-pedidos/'],
    ['permiso' => 'reportes.presupuestos_compra.ver', 'titulo' => 'Presupuestos', 'url' => SERVERURL . 'reporte-presupuestos/'],
    ['permiso' => 'reportes.ordenes_compra.ver', 'titulo' => 'Ordenes de compra', 'url' => SERVERURL . 'reporte-ordenes-compra/'],
    ['permiso' => 'reportes.compras.ver', 'titulo' => 'Compras', 'url' => SERVERURL . 'reporte-compras/'],
    ['permiso' => 'reportes.stock.ver', 'titulo' => 'Stock', 'url' => SERVERURL . 'reporte-stock/'],
    ['permiso' => 'reportes.registro_servicio.ver', 'titulo' => 'Servicios', 'url' => SERVERURL . 'reporte-registro-servicio/']
];

$informesPermitidos = array_filter($informes, function ($informe) {
    return mainModel::tienePermiso($informe['permiso']);
});
?>

<style>
    .home-dashboard {
        padding-bottom: 35px;
    }

    .home-welcome {
        background: rgba(255, 255, 255, .95);
        border-left: 4px solid #17a2b8;
        border-radius: 6px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .08);
        margin-bottom: 22px;
        padding: 20px 24px;
    }

    .home-welcome h3 {
        color: #22313f;
        font-size: 1.45rem;
        margin-bottom: 6px;
    }

    .home-welcome p {
        color: #6c757d;
        margin-bottom: 0;
    }

    .home-section-title {
        color: #22313f;
        font-size: 1.05rem;
        font-weight: 600;
        margin: 8px 0 14px;
    }

    .home-metric-grid,
    .home-action-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        margin-bottom: 24px;
    }

    .home-metric,
    .home-action,
    .home-report {
        background: rgba(255, 255, 255, .96);
        border: 1px solid rgba(0, 0, 0, .06);
        border-radius: 6px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .07);
        color: #22313f;
        display: block;
        text-decoration: none;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .home-metric:hover,
    .home-action:hover,
    .home-report:hover {
        box-shadow: 0 6px 16px rgba(0, 0, 0, .12);
        color: #17a2b8;
        text-decoration: none;
        transform: translateY(-2px);
    }

    .home-metric {
        align-items: center;
        display: flex;
        gap: 16px;
        padding: 18px;
    }

    .home-metric i,
    .home-action i {
        align-items: center;
        background: #eef9fb;
        border-radius: 6px;
        color: #17a2b8;
        display: inline-flex;
        height: 42px;
        justify-content: center;
        width: 42px;
    }

    .home-metric strong {
        display: block;
        font-size: 1.7rem;
        line-height: 1;
    }

    .home-metric span,
    .home-action span {
        color: #6c757d;
        display: block;
        font-size: .9rem;
        margin-top: 4px;
    }

    .home-action {
        min-height: 118px;
        padding: 18px;
    }

    .home-action strong {
        display: block;
        font-size: 1rem;
        margin-top: 12px;
    }

    .home-report-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }

    .home-report {
        padding: 10px 14px;
    }
</style>

<div class="container-fluid home-dashboard">
    <div class="home-welcome">
        <h3><i class="fab fa-dashcube fa-fw"></i> &nbsp; Panel principal</h3>
        <p>
            <?php if ($homeNombre !== '') { ?>
                Bienvenido, <?php echo htmlspecialchars($homeNombre, ENT_QUOTES, 'UTF-8'); ?>. Accesos y pendientes principales del sistema.
            <?php } else { ?>
                Accesos y pendientes principales del sistema.
            <?php } ?>
        </p>
    </div>

    <?php if (!empty($metricas)) { ?>
        <h4 class="home-section-title">Resumen de compras</h4>
        <div class="home-metric-grid">
            <?php foreach ($metricas as $metrica) { ?>
                <a href="<?php echo $metrica['url']; ?>" class="home-metric">
                    <i class="<?php echo $metrica['icono']; ?> fa-fw"></i>
                    <div>
                        <strong><?php echo number_format((int)$metrica['valor'], 0, ',', '.'); ?></strong>
                        <span><?php echo htmlspecialchars($metrica['titulo'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

    <?php if (!empty($metricasServicios)) { ?>
        <h4 class="home-section-title">Resumen de servicios</h4>
        <div class="home-metric-grid">
            <?php foreach ($metricasServicios as $metrica) { ?>
                <a href="<?php echo $metrica['url']; ?>" class="home-metric">
                    <i class="<?php echo $metrica['icono']; ?> fa-fw"></i>
                    <div>
                        <strong><?php echo number_format((int)$metrica['valor'], 0, ',', '.'); ?></strong>
                        <span><?php echo htmlspecialchars($metrica['titulo'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

    <?php if (!empty($accionesPermitidas)) { ?>
        <h4 class="home-section-title">Accesos rapidos</h4>
        <div class="home-action-grid">
            <?php foreach ($accionesPermitidas as $accion) { ?>
                <a href="<?php echo $accion['url']; ?>" class="home-action">
                    <i class="<?php echo $accion['icono']; ?> fa-fw"></i>
                    <strong><?php echo htmlspecialchars($accion['titulo'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    <span><?php echo htmlspecialchars($accion['detalle'], ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

    <?php if (!empty($informesPermitidos)) { ?>
        <h4 class="home-section-title">Informes frecuentes</h4>
        <div class="home-report-list">
            <?php foreach ($informesPermitidos as $informe) { ?>
                <a href="<?php echo $informe['url']; ?>" class="home-report">
                    <i class="fas fa-chart-bar fa-fw"></i>
                    <?php echo htmlspecialchars($informe['titulo'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

    <?php if (empty($metricas) && empty($metricasServicios) && empty($accionesPermitidas) && empty($informesPermitidos)) { ?>
        <div class="alert alert-info">
            No hay accesos disponibles para el perfil actual.
        </div>
    <?php } ?>
</div>
