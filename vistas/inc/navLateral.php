<?php
if (isset($pagina[0]) && $pagina[0] !== '') {
    $vistaActual = (string)$pagina[0];
} elseif (isset($_GET['vista'])) {
    $vistaActual = explode('/', trim((string)$_GET['vista'], '/'))[0] ?? 'home';
} else {
    $vistaActual = 'home';
}

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

if (!function_exists('nav_tiene_permiso')) {
    function nav_tiene_permiso($permisos)
    {
        if ($permisos === null || $permisos === '') {
            return true;
        }

        foreach ((array)$permisos as $permiso) {
            if (mainModel::tienePermiso($permiso)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('nav_item_visible')) {
    function nav_item_visible($item)
    {
        if (isset($item['permiso']) && !nav_tiene_permiso($item['permiso'])) {
            return false;
        }

        if (!empty($item['items'])) {
            foreach ($item['items'] as $subitem) {
                if (nav_item_visible($subitem)) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }
}

if (!function_exists('nav_item_activo')) {
    function nav_item_activo($item, $vistaActual)
    {
        if (!empty($item['vistas']) && in_array($vistaActual, (array)$item['vistas'], true)) {
            return true;
        }

        if (!empty($item['vista']) && $item['vista'] === $vistaActual) {
            return true;
        }

        if (!empty($item['items'])) {
            foreach ($item['items'] as $subitem) {
                if (nav_item_activo($subitem, $vistaActual)) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (!function_exists('nav_render_items')) {
    function nav_render_items($items, $vistaActual)
    {
        foreach ($items as $item) {
            if (!nav_item_visible($item)) {
                continue;
            }

            $activo = nav_item_activo($item, $vistaActual);
            $icono = $item['icono'] ?? 'fas fa-circle';
            $titulo = htmlspecialchars($item['titulo'], ENT_QUOTES, 'UTF-8');

            if (!empty($item['items'])) {
                $claseLink = 'nav-btn-submenu' . ($activo ? ' active' : '');
                $claseUl = $activo ? ' class="show-nav-lateral-submenu"' : '';
                $claseChevron = 'fas fa-chevron-down' . ($activo ? ' fa-rotate-180' : '');

                echo '<li>';
                echo '<a href="#" class="' . $claseLink . '">';
                echo '<i class="' . $icono . ' fa-fw"></i> &nbsp; ' . $titulo;
                echo '<i class="' . $claseChevron . '"></i>';
                echo '</a>';
                echo '<ul' . $claseUl . '>';
                nav_render_items($item['items'], $vistaActual);
                echo '</ul>';
                echo '</li>';
                continue;
            }

            $href = $item['href'] ?? '#';
            $download = !empty($item['download']) ? ' download' : '';
            $claseActivo = $activo ? ' class="active"' : '';

            echo '<li>';
            echo '<a href="' . SERVERURL . $href . '"' . $download . $claseActivo . '>';
            echo '<i class="' . $icono . ' fa-fw"></i> &nbsp; ' . $titulo;
            echo '</a>';
            echo '</li>';
        }
    }
}

$menuLateral = [
    [
        'titulo' => 'Panel Principal',
        'icono' => 'fab fa-dashcube',
        'href' => 'home/',
        'vista' => 'home'
    ],
    [
        'titulo' => 'Compras',
        'icono' => 'fas fa-shopping-cart',
        'permiso' => 'compra.ver',
        'items' => [
            [
                'titulo' => 'Pedidos',
                'icono' => 'fas fa-file-alt',
                'href' => 'pedido-buscar/',
                'vistas' => ['pedido-nuevo', 'pedido-lista', 'pedido-buscar'],
                'permiso' => 'compra.pedido.ver'
            ],
            [
                'titulo' => 'Presupuestos',
                'icono' => 'fas fa-file-invoice-dollar',
                'href' => 'presupuesto-buscar/',
                'vistas' => ['presupuesto-nuevo', 'presupuesto-lista', 'presupuesto-buscar'],
                'permiso' => 'compra.presupuesto.ver'
            ],
            [
                'titulo' => 'Ordenes de Compra',
                'icono' => 'fas fa-file-invoice',
                'href' => 'oc-buscar/',
                'vistas' => ['oc-nuevo', 'oc-lista', 'oc-buscar'],
                'permiso' => 'compra.oc.ver'
            ],
            [
                'titulo' => 'Ingreso de Facturas',
                'icono' => 'fas fa-shopping-cart',
                'href' => 'factura-buscar/',
                'vistas' => ['factura-nuevo', 'factura-lista', 'factura-buscar'],
                'permiso' => 'compra.factura.ver'
            ],
            [
                'titulo' => 'Remisiones',
                'icono' => 'fas fa-box',
                'href' => 'remision-buscar/',
                'vistas' => ['remision-nuevo', 'remision-buscar'],
                'permiso' => 'compra.remision.ver'
            ],
            [
                'titulo' => 'Notas de Credito y Debito',
                'icono' => 'fas fa-file-alt',
                'href' => 'notasCreDe-buscar/',
                'vistas' => ['notasCreDe-nuevo', 'notasCreDe-buscar'],
                'permiso' => 'compra.nota.ver'
            ],
            [
                'titulo' => 'Transferencias',
                'icono' => 'fas fa-exchange-alt',
                'href' => 'transferencia-historial/',
                'vistas' => ['transferencia-nuevo', 'transferencia-historial', 'transferencia-recibir'],
                'permiso' => 'compra.transferencia.ver'
            ],
            [
                'titulo' => 'Inventarios',
                'icono' => 'fas fa-clipboard-list',
                'href' => 'inventario/',
                'vistas' => ['inventario', 'inventario-buscar'],
                'permiso' => 'inventario.ver'
            ]
        ]
    ],
    [
        'titulo' => 'Servicios',
        'icono' => 'fas fa-tools',
        'permiso' => 'servicio.ver',
        'items' => [
            [
                'titulo' => 'Solicitud de Servicios',
                'icono' => 'fas fa-file-signature',
                'href' => 'recepcionServicio-buscar/',
                'vistas' => ['recepcionServicio-nuevo', 'recepcionServicio-buscar'],
                'permiso' => 'servicio.recepcion.ver'
            ],
            [
                'titulo' => 'Diagnostico',
                'icono' => 'fas fa-stethoscope',
                'href' => 'diagnostico-servicio-buscar/',
                'vistas' => ['diagnostico-servicio-nuevo', 'diagnostico-servicio-buscar'],
                'permiso' => 'servicio.diagnostico.ver'
            ],
            [
                'titulo' => 'Promociones',
                'icono' => 'fas fa-tags',
                'href' => 'promocion-lista/',
                'vistas' => ['promocion-nuevo', 'promocion-lista'],
                'permiso' => 'servicio.promocion.ver'
            ],
            [
                'titulo' => 'Descuentos',
                'icono' => 'fas fa-percent',
                'href' => 'descuento-lista/',
                'vistas' => ['descuento-nuevo', 'descuento-lista'],
                'permiso' => 'servicio.descuento.ver'
            ],
            [
                'titulo' => 'Reglas Comerciales',
                'icono' => 'fas fa-project-diagram',
                'href' => 'regla-comercial-lista/',
                'vistas' => ['regla-comercial-nuevo', 'regla-comercial-lista'],
                'permiso' => 'servicio.regla_comercial.ver'
            ],
            [
                'titulo' => 'Presupuesto de Trabajo',
                'icono' => 'fas fa-file-invoice-dollar',
                'href' => 'presupuesto-servicio-buscar/',
                'vistas' => ['presupuesto-servicio-nuevo', 'presupuesto-servicio-lista', 'presupuesto-servicio-buscar'],
                'permiso' => 'servicio.presupuesto.ver'
            ],
            [
                'titulo' => 'Ordenes de Trabajo',
                'icono' => 'fas fa-clipboard-check',
                'href' => 'ordenTrabajo-buscar/',
                'vistas' => ['ordenTrabajo-nuevo', 'ordenTrabajo-lista', 'ordenTrabajo-asignar', 'ordenTrabajo-buscar'],
                'permiso' => 'servicio.ot.ver'
            ],
            [
                'titulo' => 'Registro de Servicios',
                'icono' => 'fas fa-cogs',
                'href' => 'registro-servicio-buscar/',
                'vistas' => ['registro-servicio-nuevo', 'registro-servicio-lista', 'registro-servicio-buscar'],
                'permiso' => 'servicio.registro.ver'
            ],
            [
                'titulo' => 'Reclamos',
                'icono' => 'fas fa-exclamation-circle',
                'href' => 'reclamo-servicio-lista/',
                'vistas' => ['reclamo-servicio-nuevo', 'reclamo-servicio-lista'],
                'permiso' => 'servicio.reclamo.ver'
            ],
            [
                'titulo' => 'Registro de Insumos',
                'icono' => 'fas fa-boxes',
                'href' => 'registro-insumos-buscar/',
                'vistas' => ['registro-insumos', 'registro-insumos-buscar'],
                'permiso' => ['servicio.insumo.ver', 'servicio.insumo.crear']
            ]
        ]
    ],
    [
        'titulo' => 'Mantenimiento',
        'icono' => 'fas fa-cog',
        'permiso' => 'mantenimiento.ver',
        'items' => [
            [
                'titulo' => 'Compras',
                'icono' => 'fas fa-shopping-cart',
                'permiso' => 'compra.ver',
                'items' => [
                    [
                        'titulo' => 'Sucursales',
                        'icono' => 'fas fa-city',
                        'href' => 'sucursal-nuevo/',
                        'vistas' => ['sucursal-nuevo', 'sucursal-lista', 'sucursal-actualizar', 'sucursal-buscar'],
                        'permiso' => 'sucursal.ver'
                    ],
                    [
                        'titulo' => 'Articulos',
                        'icono' => 'fas fa-pallet',
                        'href' => 'articulo-nuevo/',
                        'vistas' => ['articulo-nuevo', 'articulo-lista', 'articulo-actualizar', 'articulo-buscar'],
                        'permiso' => 'articulo.ver'
                    ],
                    [
                        'titulo' => 'Proveedores',
                        'icono' => 'fas fa-truck',
                        'href' => 'proveedor-nuevo/',
                        'vistas' => ['proveedor-nuevo', 'proveedor-lista', 'proveedor-actualizar', 'proveedor-buscar'],
                        'permiso' => 'proveedor.ver'
                    ]
                ]
            ],
            [
                'titulo' => 'Servicios',
                'icono' => 'fas fa-tools',
                'permiso' => ['servicio.ver'],
                'items' => [
                    [
                        'titulo' => 'Clientes',
                        'icono' => 'fas fa-users',
                        'href' => 'cliente-lista/',
                        'vistas' => ['cliente-nuevo', 'cliente-lista', 'cliente-actualizar', 'cliente-buscar'],
                        'permiso' => 'cliente.ver'
                    ],
                    [
                        'titulo' => 'Vehiculos',
                        'icono' => 'fas fa-car',
                        'href' => 'vehiculo-nuevo/',
                        'vistas' => ['vehiculo-nuevo', 'vehiculo-lista', 'vehiculo-actualizar', 'vehiculo-buscar'],
                        'permiso' => 'vehiculo.ver'
                    ],
                    [
                        'titulo' => 'Empleados',
                        'icono' => 'fas fa-user',
                        'href' => 'empleado-lista/',
                        'vistas' => ['empleado-nuevo', 'empleado-lista', 'empleado-actualizar', 'empleado-buscar'],
                        'permiso' => 'empleado.ver'
                    ],
                    [
                        'titulo' => 'Equipos',
                        'icono' => 'fas fa-users-cog',
                        'href' => 'empleado-equipo/',
                        'vistas' => ['empleado-equipo', 'empleado-equipo-asignar', 'empleado-equipo-actualizar', 'empleado-equipo-miembros'],
                        'permiso' => ['equipo.crear', 'equipo.editar']
                    ]
                ]
            ],
            [
                'titulo' => 'Seguridad',
                'icono' => 'fas fa-shield-alt',
                'permiso' => 'usuarios.ver',
                'items' => [
                    [
                        'titulo' => 'Usuarios',
                        'icono' => 'fas fa-user',
                        'href' => 'usuario-nuevo/',
                        'vistas' => ['usuario-nuevo', 'usuario-lista', 'usuario-actualizar', 'usuario-buscar'],
                        'permiso' => 'usuarios.ver'
                    ],
                    [
                        'titulo' => 'Roles y Permisos',
                        'icono' => 'fas fa-key',
                        'href' => 'rol-nuevo/',
                        'vistas' => ['rol-nuevo', 'rol-actualizar', 'rol-permisos'],
                        'permiso' => 'roles.ver'
                    ]
                ]
            ]
        ]
    ],
    [
        'titulo' => 'Informes Referenciales',
        'icono' => 'fas fa-chart-bar',
        'permiso' => [
            'reportes.articulos.ver',
            'reportes.proveedores.ver',
            'reportes.sucursales.ver',
            'reportes.clientes.ver',
            'reportes.vehiculos.ver',
            'usuarios.ver'
        ],
        'items' => [
            [
                'titulo' => 'Informes Referenciales',
                'icono' => 'fas fa-table',
                'href' => 'reporte-referenciales/',
                'vista' => 'reporte-referenciales',
                'permiso' => [
                    'reportes.articulos.ver',
                    'reportes.proveedores.ver',
                    'reportes.sucursales.ver',
                    'reportes.clientes.ver',
                    'reportes.vehiculos.ver',
                    'usuarios.ver'
                ]
            ]
        ]
    ],
    [
        'titulo' => 'Informes de Movimientos',
        'icono' => 'fas fa-chart-line',
        'permiso' => [
            'reportes.pedidos.ver',
            'reportes.presupuestos_compra.ver',
            'reportes.ordenes_compra.ver',
            'reportes.compras.ver',
            'reportes.libro_compras.ver',
            'reportes.stock.ver',
            'reportes.movimientos_stock.ver',
            'reportes.recepcion_servicio.ver',
            'reportes.presupuesto_servicio.ver',
            'reportes.orden_trabajo.ver',
            'reportes.registro_servicio.ver'
        ],
        'items' => [
            [
                'titulo' => 'Panel de Movimientos',
                'icono' => 'fas fa-table',
                'href' => 'reporte-movimientos/',
                'vista' => 'reporte-movimientos',
                'permiso' => [
                    'reportes.pedidos.ver',
                    'reportes.presupuestos_compra.ver',
                    'reportes.ordenes_compra.ver',
                    'reportes.compras.ver',
                    'reportes.libro_compras.ver',
                    'reportes.stock.ver',
                    'reportes.movimientos_stock.ver',
                    'reportes.recepcion_servicio.ver',
                    'reportes.presupuesto_servicio.ver',
                    'reportes.orden_trabajo.ver',
                    'reportes.registro_servicio.ver'
                ]
            ]
        ]
    ],
    [
        'titulo' => 'Ayuda',
        'icono' => 'fas fa-question-circle',
        'href' => 'public/docs/userManual.pdf',
        'download' => true
    ]
];
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
        </figure>

        <div class="full-box nav-lateral-bar"></div>

        <nav class="full-box nav-lateral-menu">
            <ul>
                <?php nav_render_items($menuLateral, $vistaActual); ?>
            </ul>
        </nav>
    </div>
</section>
