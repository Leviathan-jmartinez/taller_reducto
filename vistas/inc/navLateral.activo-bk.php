<?php
if (isset($pagina[0]) && $pagina[0] !== '') {
    $vistaActual = (string)$pagina[0];
} elseif (isset($_GET['vista'])) {
    $vistaActual = explode('/', trim((string)$_GET['vista'], '/'))[0] ?? 'home';
} else {
    $vistaActual = 'home';
}

if (!function_exists('navActivo')) {
    function navActivo($vistas)
    {
        global $vistaActual;
        return in_array($vistaActual, (array)$vistas, true);
    }
}

if (!function_exists('navClase')) {
    function navClase($vistas, $base = '')
    {
        $clase = trim($base . (navActivo($vistas) ? ' active' : ''));
        return $clase !== '' ? ' class="' . $clase . '"' : '';
    }
}

if (!function_exists('navSubmenu')) {
    function navSubmenu($vistas)
    {
        return navActivo($vistas) ? ' class="show-nav-lateral-submenu"' : '';
    }
}

if (!function_exists('navChevron')) {
    function navChevron($vistas)
    {
        return 'fas fa-chevron-down' . (navActivo($vistas) ? ' fa-rotate-180' : '');
    }
}

$navCompras = [
    'pedido-nuevo', 'pedido-lista', 'pedido-buscar',
    'presupuesto-nuevo', 'presupuesto-lista', 'presupuesto-buscar',
    'oc-nuevo', 'oc-lista', 'oc-buscar',
    'factura-nuevo', 'factura-lista', 'factura-buscar',
    'remision-nuevo', 'remision-buscar',
    'notasCreDe-nuevo', 'notasCreDe-buscar',
    'transferencia-nuevo', 'transferencia-historial', 'transferencia-recibir',
    'inventario', 'inventario-buscar'
];

$navServicios = [
    'recepcionServicio-nuevo', 'recepcionServicio-buscar',
    'diagnostico-servicio-nuevo', 'diagnostico-servicio-buscar',
    'promocion-nuevo', 'promocion-lista',
    'descuento-nuevo', 'descuento-lista',
    'presupuesto-servicio-nuevo', 'presupuesto-servicio-lista', 'presupuesto-servicio-buscar',
    'ordenTrabajo-nuevo', 'ordenTrabajo-lista', 'ordenTrabajo-asignar', 'ordenTrabajo-buscar',
    'registro-servicio-nuevo', 'registro-servicio-lista', 'registro-servicio-buscar',
    'reclamo-servicio-nuevo', 'reclamo-servicio-lista'
];

$navMantCompras = [
    'sucursal-nuevo', 'sucursal-lista', 'sucursal-actualizar', 'sucursal-buscar',
    'articulo-nuevo', 'articulo-lista', 'articulo-actualizar', 'articulo-buscar',
    'proveedor-nuevo', 'proveedor-lista', 'proveedor-actualizar', 'proveedor-buscar'
];

$navMantServicios = [
    'cliente-nuevo', 'cliente-lista', 'cliente-actualizar', 'cliente-buscar',
    'vehiculo-nuevo', 'vehiculo-lista', 'vehiculo-actualizar', 'vehiculo-buscar',
    'empleado-nuevo', 'empleado-lista', 'empleado-actualizar', 'empleado-buscar',
    'empleado-equipo', 'empleado-equipo-asignar', 'empleado-equipo-actualizar', 'empleado-equipo-miembros'
];

$navSeguridad = ['usuario-nuevo', 'usuario-lista', 'usuario-actualizar', 'usuario-buscar', 'rol-nuevo', 'rol-actualizar', 'rol-permisos'];
$navMantenimiento = array_merge($navMantCompras, $navMantServicios, $navSeguridad);

$navInfoRefCompras = ['reporte-articulos', 'reporte-proveedores', 'reporte-sucursales'];
$navInfoRefServicios = ['reporte-clientes', 'reporte-vehiculos', 'reporte-empleados'];
$navInfoReferenciales = array_merge($navInfoRefCompras, $navInfoRefServicios);

$navInfoMovCompras = ['reporte-pedidos', 'reporte-presupuestos', 'reporte-ordenes-compra', 'reporte-compras', 'reporte-LibroCompras', 'reporte-stock', 'reporte-movimientostock'];
$navInfoMovServicios = ['reporte-recepcion-servicio', 'reporte-presupuesto-servicio', 'reporte-orden-trabajo', 'reporte-registro-servicio'];
$navInfoMovimientos = array_merge($navInfoMovCompras, $navInfoMovServicios);

$usuarioNav = trim(($_SESSION['nombre_str'] ?? '') . ' ' . ($_SESSION['apellido_str'] ?? ''));
$empresaNav = $_SESSION['empresa_nombre'] ?? '';
$inicialesNav = '';

foreach (preg_split('/\s+/', $usuarioNav) as $parteNombre) {
    if ($parteNombre !== '') {
        $inicialesNav .= strtoupper(substr($parteNombre, 0, 1));
    }

    if (strlen($inicialesNav) >= 2) {
        break;
    }
}

if ($inicialesNav === '') {
    $inicialesNav = 'US';
}
?>
<!-- Nav lateral -->
<section class="full-box nav-lateral">
    <div class="full-box nav-lateral-bg show-nav-lateral"></div>
    <div class="full-box nav-lateral-content">

        <figure class="full-box nav-lateral-avatar">
            <i class="far fa-times-circle show-nav-lateral"></i>
            <div class="nav-user-card">
                <div class="nav-user-initials"><?= htmlspecialchars($inicialesNav, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="nav-user-info">
                    <span class="nav-user-name"><?= htmlspecialchars($usuarioNav, ENT_QUOTES, 'UTF-8') ?></span>
                    <small class="nav-user-company"><?= htmlspecialchars($empresaNav, ENT_QUOTES, 'UTF-8') ?></small>
                </div>
            </div>
            <figcaption class="roboto-medium text-center">
                <span class="nav-user-legacy-name"><?= $_SESSION['nombre_str'] . " " . $_SESSION['apellido_str'] ?></span><br>
                <small class="roboto-condensed-light"> </small>
            </figcaption>
        </figure>

        <div class="full-box nav-lateral-bar"></div>

        <nav class="full-box nav-lateral-menu">
            <ul>
                <!-- PANEL -->
                <li>
                    <a href="<?= SERVERURL; ?>home/"<?= navClase('home') ?>>
                        <i class="fab fa-dashcube fa-fw"></i> &nbsp; Panel Principal
                    </a>
                </li>

                <!-- INVENTARIOS -->


                <!-- COMPRAS -->
                <?php if (mainModel::tienePermiso('compra.ver')) { ?>
                    <li>
                        <a href="#"<?= navClase($navCompras, 'nav-btn-submenu') ?>>
                            <i class="fas fa-shopping-cart"></i> &nbsp; Compras
                            <i class="<?= navChevron($navCompras) ?>"></i>
                        </a>
                        <ul<?= navSubmenu($navCompras) ?>>
                            <?php if (mainModel::tienePermiso('compra.pedido.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>pedido-nuevo/"<?= navClase(['pedido-nuevo', 'pedido-lista', 'pedido-buscar']) ?>><i class="fas fa-file-alt fa-fw"></i> &nbsp; Pedidos</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('compra.presupuesto.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>presupuesto-nuevo/"<?= navClase(['presupuesto-nuevo', 'presupuesto-lista', 'presupuesto-buscar']) ?>><i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; Presupuestos</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('compra.oc.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>oc-nuevo/"<?= navClase(['oc-nuevo', 'oc-lista', 'oc-buscar']) ?>><i class="fas fa-file-invoice fa-fw"></i> &nbsp; Ordenes de Compra</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('compra.factura.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>factura-nuevo/"<?= navClase(['factura-nuevo', 'factura-lista', 'factura-buscar']) ?>><i class="fas fa-shopping-cart fa-fw"></i> &nbsp; Ingreso de Facturas</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('compra.remision.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>remision-nuevo/"<?= navClase(['remision-nuevo', 'remision-buscar']) ?>><i class="fas fa-box fa-fw"></i> &nbsp; Remisiones</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('compra.nota.ver')) { ?>
                                <?php if (navActivo(['notasCreDe-nuevo', 'notasCreDe-buscar'])) { ?>
                                    <li><a href="<?= SERVERURL; ?>notasCreDe-nuevo/" class="active"><i class="fas fa-file-alt fa-fw"></i> &nbsp; Notas de Credito y Debito</a></li>
                                <?php } else { ?>
                                <li><a href="<?= SERVERURL; ?>notasCreDe-nuevo/"><i class="fas fa-file-alt fa-fw"></i> &nbsp; Notas de Crédito y Débito</a></li>
                            <?php } ?>
                                <?php } ?>
                            <?php if (mainModel::tienePermiso('compra.transferencia.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>transferencia-nuevo/"<?= navClase(['transferencia-nuevo', 'transferencia-historial', 'transferencia-recibir']) ?>><i class="fas fa-file-alt fa-fw"></i> &nbsp; Transferencias</a></li>
                            <?php } ?>
                            <?php if (mainModel::tienePermiso('inventario.ver')) { ?>
                                <li>
                                    <a href="<?= SERVERURL; ?>inventario/"<?= navClase(['inventario', 'inventario-buscar']) ?>>
                                        <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Inventarios
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- SERVICIOS -->
                <?php if (mainModel::tienePermiso('servicio.ver')) { ?>
                    <li>
                        <a href="#"<?= navClase($navServicios, 'nav-btn-submenu') ?>>
                            <i class="fas fa-tools fa-fw"></i> &nbsp; Servicios
                            <i class="<?= navChevron($navServicios) ?>"></i>
                        </a>
                        <ul<?= navSubmenu($navServicios) ?>>
                            <?php if (mainModel::tienePermiso('servicio.recepcion.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>recepcionServicio-nuevo/"<?= navClase(['recepcionServicio-nuevo', 'recepcionServicio-buscar']) ?>><i class="fas fa-file-signature fa-fw"></i> &nbsp; Solicitud de Servicios</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.diagnostico.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>diagnostico-servicio-nuevo/"<?= navClase(['diagnostico-servicio-nuevo', 'diagnostico-servicio-buscar']) ?>><i class="fas fa-stethoscope fa-fw"></i> &nbsp; Diagnostico</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.promocion.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>promocion-nuevo/"<?= navClase(['promocion-nuevo', 'promocion-lista']) ?>><i class="fas fa-tags fa-fw"></i> &nbsp; Promociones</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.descuento.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>descuento-nuevo/"<?= navClase(['descuento-nuevo', 'descuento-lista']) ?>><i class="fas fa-tags fa-fw"></i> &nbsp; Descuentos</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.presupuesto.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>presupuesto-servicio-nuevo"<?= navClase(['presupuesto-servicio-nuevo', 'presupuesto-servicio-lista', 'presupuesto-servicio-buscar']) ?>><i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; Presupuesto de Trabajo</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.ot.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>ordenTrabajo-nuevo/"<?= navClase(['ordenTrabajo-nuevo', 'ordenTrabajo-lista', 'ordenTrabajo-asignar', 'ordenTrabajo-buscar']) ?>><i class="fas fa-clipboard-check fa-fw"></i> &nbsp; Ordenes de Trabajo</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.registro.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>registro-servicio-nuevo/"<?= navClase(['registro-servicio-nuevo', 'registro-servicio-lista', 'registro-servicio-buscar']) ?>><i class="fas fa-cogs fa-fw"></i> &nbsp; Registro de Servicios</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.reclamo.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>reclamo-servicio-nuevo/"<?= navClase(['reclamo-servicio-nuevo', 'reclamo-servicio-lista']) ?>><i class="fas fa-exclamation-circle fa-fw"></i> &nbsp; Reclamos</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- ADMINISTRACIÓN -->
                <?php if (
                    mainModel::tienePermiso('mantenimiento.ver')
                ) { ?>
                    <li>
                        <a href="#"<?= navClase($navMantenimiento, 'nav-btn-submenu') ?>>
                            <i class="fas fa-cog fa-fw"></i> &nbsp; Mantenimiento
                            <i class="<?= navChevron($navMantenimiento) ?>"></i>
                        </a>

                        <ul<?= navSubmenu($navMantenimiento) ?>>
                            <?php if (mainModel::tienePermiso('compra.ver')) { ?>
                                <!-- COMPRAS -->
                                <li>
                                    <a href="#"<?= navClase($navMantCompras, 'nav-btn-submenu') ?>>
                                        <i class="fas fa-cog fa-fw"></i> &nbsp; Compras
                                        <i class="<?= navChevron($navMantCompras) ?>"></i>
                                    </a>

                                    <ul<?= navSubmenu($navMantCompras) ?>>
                                        <?php if (mainModel::tienePermiso('sucursal.ver')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>sucursal-nuevo/"<?= navClase(['sucursal-nuevo', 'sucursal-lista', 'sucursal-actualizar', 'sucursal-buscar']) ?>>
                                                    <i class="fas fa-city fa-fw"></i> &nbsp; Sucursales
                                                </a>
                                            </li>
                                        <?php } ?>

                                        <?php if (mainModel::tienePermiso('articulo.ver')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>articulo-nuevo/"<?= navClase(['articulo-nuevo', 'articulo-lista', 'articulo-actualizar', 'articulo-buscar']) ?>>
                                                    <i class="fas fa-pallet fa-fw"></i> &nbsp; Artículos
                                                </a>
                                            </li>
                                        <?php } ?>

                                        <?php if (mainModel::tienePermiso('proveedor.ver')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>proveedor-nuevo/"<?= navClase(['proveedor-nuevo', 'proveedor-lista', 'proveedor-actualizar', 'proveedor-buscar']) ?>>
                                                    <i class="fas fa-truck fa-fw"></i> &nbsp; Proveedores
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if (mainModel::tienePermiso('servicio.ver') || mainModel::tienePermiso('equipo.crear') || mainModel::tienePermiso('equipo.editar')) { ?>
                                <!-- SERVICIOS -->
                                <li>
                                    <a href="#"<?= navClase($navMantServicios, 'nav-btn-submenu') ?>>
                                        <i class="fas fa-cog fa-fw"></i> &nbsp; Servicios
                                        <i class="<?= navChevron($navMantServicios) ?>"></i>
                                    </a>

                                    <ul<?= navSubmenu($navMantServicios) ?>>
                                        <?php if (mainModel::tienePermiso('cliente.ver')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>cliente-nuevo/"<?= navClase(['cliente-nuevo', 'cliente-lista', 'cliente-actualizar', 'cliente-buscar']) ?>>
                                                    <i class="fas fa-users fa-fw"></i> &nbsp; Clientes
                                                </a>
                                            </li>
                                        <?php } ?>

                                        <?php if (mainModel::tienePermiso('vehiculo.ver')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>vehiculo-nuevo/"<?= navClase(['vehiculo-nuevo', 'vehiculo-lista', 'vehiculo-actualizar', 'vehiculo-buscar']) ?>>
                                                    <i class="fas fa-car fa-fw"></i> &nbsp; Vehículos
                                                </a>
                                            </li>
                                        <?php } ?>

                                        <?php if (mainModel::tienePermiso('empleado.ver')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>empleado-nuevo/"<?= navClase(['empleado-nuevo', 'empleado-lista', 'empleado-actualizar', 'empleado-buscar']) ?>>
                                                    <i class="fas fa-user fa-fw"></i> &nbsp; Empleados
                                                </a>
                                            </li>
                                        <?php } ?>

                                        <?php if (mainModel::tienePermiso('equipo.crear') || mainModel::tienePermiso('equipo.editar')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>empleado-equipo/"<?= navClase(['empleado-equipo', 'empleado-equipo-asignar', 'empleado-equipo-actualizar', 'empleado-equipo-miembros']) ?>>
                                                    <i class="fas fa-users fa-fw"></i> &nbsp; Equipos
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            <!-- SEGURIDAD -->
                            <?php if (mainModel::tienePermiso('usuarios.ver')) { ?>
                                <li>
                                    <a href="#"<?= navClase($navSeguridad, 'nav-btn-submenu') ?>>
                                        <i class="fas fa-shield-alt fa-fw"></i> &nbsp; Seguridad
                                        <i class="<?= navChevron($navSeguridad) ?>"></i>
                                    </a>
                                    <ul<?= navSubmenu($navSeguridad) ?>>
                                        <?php if (mainModel::tienePermiso('usuarios.ver')) { ?>
                                            <li><a href="<?= SERVERURL; ?>usuario-nuevo/"<?= navClase(['usuario-nuevo', 'usuario-lista', 'usuario-actualizar', 'usuario-buscar']) ?>><i class="fas fa-user fa-fw"></i> &nbsp; Usuarios</a></li>
                                        <?php } ?>

                                        <?php if (mainModel::tienePermiso('roles.ver')) { ?>
                                            <li><a href="<?= SERVERURL; ?>rol-nuevo/"<?= navClase(['rol-nuevo', 'rol-actualizar', 'rol-permisos']) ?>><i class="fas fa-key fa-fw"></i> &nbsp; Roles y Permisos</a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if (
                    mainModel::tienePermiso('reportes.articulos.ver') ||
                    mainModel::tienePermiso('reportes.proveedores.ver') ||
                    mainModel::tienePermiso('reportes.sucursales.ver') ||
                    mainModel::tienePermiso('reportes.clientes.ver') ||
                    mainModel::tienePermiso('reportes.vehiculos.ver') ||
                    mainModel::tienePermiso('reportes.empleados.ver')
                ) { ?>
                <li>
                    <a href="#"<?= navClase($navInfoReferenciales, 'nav-btn-submenu') ?>>
                        <i class="fas fa-chart-bar fa-fw"></i> &nbsp; Informes Referenciales
                        <i class="<?= navChevron($navInfoReferenciales) ?>"></i>
                    </a>
                    <ul<?= navSubmenu($navInfoReferenciales) ?>>
                        <?php if (
                            mainModel::tienePermiso('reportes.articulos.ver') ||
                            mainModel::tienePermiso('reportes.proveedores.ver') ||
                            mainModel::tienePermiso('reportes.sucursales.ver')
                        ) { ?>
                            <!-- ================= COMPRAS ================= -->
                            <li>
                                <a href="#"<?= navClase($navInfoRefCompras, 'nav-btn-submenu') ?>>
                                    <i class="fas fa-shopping-cart fa-fw"></i> &nbsp; Referenciales de Compras
                                    <i class="<?= navChevron($navInfoRefCompras) ?>"></i>
                                </a>
                                <ul<?= navSubmenu($navInfoRefCompras) ?>>
                                    <?php if (mainModel::tienePermiso('reportes.articulos.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-articulos/"<?= navClase('reporte-articulos') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Artículos
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.proveedores.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-proveedores/"<?= navClase('reporte-proveedores') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Proveedores
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.sucursales.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-sucursales/"<?= navClase('reporte-sucursales') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Sucursales
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if (
                            mainModel::tienePermiso('reportes.clientes.ver') ||
                            mainModel::tienePermiso('reportes.vehiculos.ver') ||
                            mainModel::tienePermiso('reportes.empleados.ver')
                        ) { ?>
                            <!-- ================= SERVICIOS ================= -->
                            <li>
                                <a href="#"<?= navClase($navInfoRefServicios, 'nav-btn-submenu') ?>>
                                    <i class="fas fa-tools fa-fw"></i> &nbsp; Referenciales de Servicios
                                    <i class="<?= navChevron($navInfoRefServicios) ?>"></i>
                                </a>
                                <ul<?= navSubmenu($navInfoRefServicios) ?>>
                                    <?php if (mainModel::tienePermiso('reportes.clientes.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-clientes/"<?= navClase('reporte-clientes') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Clientes
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.vehiculos.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-vehiculos/"<?= navClase('reporte-vehiculos') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Vehículos
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.empleados.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-empleados/"<?= navClase('reporte-empleados') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Empleados
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>


                <?php if (
                    mainModel::tienePermiso('reportes.pedidos.ver') ||
                    mainModel::tienePermiso('reportes.presupuestos_compra.ver') ||
                    mainModel::tienePermiso('reportes.ordenes_compra.ver') ||
                    mainModel::tienePermiso('reportes.compras.ver') ||
                    mainModel::tienePermiso('reportes.libro_compras.ver') ||
                    mainModel::tienePermiso('reportes.stock.ver') ||
                    mainModel::tienePermiso('reportes.movimientos_stock.ver') ||
                    mainModel::tienePermiso('reportes.recepcion_servicio.ver') ||
                    mainModel::tienePermiso('reportes.presupuesto_servicio.ver') ||
                    mainModel::tienePermiso('reportes.orden_trabajo.ver') ||
                    mainModel::tienePermiso('reportes.registro_servicio.ver')
                ) { ?>
                <li>
                    <a href="#"<?= navClase($navInfoMovimientos, 'nav-btn-submenu') ?>>
                        <i class="fas fa-chart-bar fa-fw"></i> &nbsp; Informes de movimientos
                        <i class="<?= navChevron($navInfoMovimientos) ?>"></i>
                    </a>

                    <ul<?= navSubmenu($navInfoMovimientos) ?>>
                        <?php if (
                            mainModel::tienePermiso('reportes.pedidos.ver') ||
                            mainModel::tienePermiso('reportes.presupuestos_compra.ver') ||
                            mainModel::tienePermiso('reportes.ordenes_compra.ver') ||
                            mainModel::tienePermiso('reportes.compras.ver') ||
                            mainModel::tienePermiso('reportes.libro_compras.ver') ||
                            mainModel::tienePermiso('reportes.stock.ver') ||
                            mainModel::tienePermiso('reportes.movimientos_stock.ver')
                        ) { ?>
                            <!-- ================= INFORMES DE COMPRAS ================= -->
                            <li>
                                <a href="#"<?= navClase($navInfoMovCompras, 'nav-btn-submenu') ?>>
                                    <i class="fas fa-file-invoice fa-fw"></i> &nbsp; Informes de Compras
                                    <i class="<?= navChevron($navInfoMovCompras) ?>"></i>
                                </a>
                                <ul<?= navSubmenu($navInfoMovCompras) ?>>
                                    <?php if (mainModel::tienePermiso('reportes.pedidos.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-pedidos/"<?= navClase('reporte-pedidos') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Pedidos
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.presupuestos_compra.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-presupuestos/"<?= navClase('reporte-presupuestos') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Presupuestos
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.ordenes_compra.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-ordenes-compra/"<?= navClase('reporte-ordenes-compra') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Órdenes de Compra
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.compras.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-compras/"<?= navClase('reporte-compras') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Compras
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.libro_compras.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-LibroCompras/"<?= navClase('reporte-LibroCompras') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe Libro de Compras
                                        </a>
                                    </li>
                                    <?php } ?>

                                    <?php if (mainModel::tienePermiso('reportes.stock.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-stock/"<?= navClase('reporte-stock') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Stock
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.movimientos_stock.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-movimientostock/"<?= navClase('reporte-movimientostock') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Movimientos de Stock
                                        </a>
                                    </li>
                                    <?php } ?>

                                </ul>
                            </li>
                        <?php } ?>
                        <?php if (
                            mainModel::tienePermiso('reportes.recepcion_servicio.ver') ||
                            mainModel::tienePermiso('reportes.presupuesto_servicio.ver') ||
                            mainModel::tienePermiso('reportes.orden_trabajo.ver') ||
                            mainModel::tienePermiso('reportes.registro_servicio.ver')
                        ) { ?>
                            <!-- ================= INFORMES DE SERVICIOS ================= -->
                            <li>
                                <a href="#"<?= navClase($navInfoMovServicios, 'nav-btn-submenu') ?>>
                                    <i class="fas fa-tools fa-fw"></i> &nbsp; Informes de Servicios
                                    <i class="<?= navChevron($navInfoMovServicios) ?>"></i>
                                </a>
                                <ul<?= navSubmenu($navInfoMovServicios) ?>>


                                    <?php if (mainModel::tienePermiso('reportes.recepcion_servicio.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-recepcion-servicio/"<?= navClase('reporte-recepcion-servicio') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Rec. de Servicios
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.presupuesto_servicio.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-presupuesto-servicio/"<?= navClase('reporte-presupuesto-servicio') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Presupuestos
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.orden_trabajo.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-orden-trabajo/"<?= navClase('reporte-orden-trabajo') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de OT
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.registro_servicio.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-registro-servicio/"<?= navClase('reporte-registro-servicio') ?>>
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Reg. Servicios
                                        </a>
                                    </li>
                                    <?php } ?>

                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>


                <li>
                    <a href="<?= SERVERURL; ?>public/docs/userManual.pdf" download>
                        <i class="fas fa-question-circle fa-fw"></i> &nbsp; Ayuda
                    </a>
                </li>


            </ul>
        </nav>
    </div>
</section>
