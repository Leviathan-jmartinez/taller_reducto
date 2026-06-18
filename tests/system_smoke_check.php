<?php
require_once __DIR__ . '/../modelos/reportesModelo.php';

class SystemSmokeReportes extends reportesModelo
{
    public static function callProtected($method, array $args = [])
    {
        return self::$method(...$args);
    }
}

function smokeFail($message)
{
    fwrite(STDERR, 'FAIL: ' . $message . PHP_EOL);
    exit(1);
}

function smokeAssert($condition, $message)
{
    if (!$condition) {
        smokeFail($message);
    }
}

function smokeCount(PDO $pdo, $table)
{
    return (int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
}

function smokeTableExists(PDO $pdo, $table)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
    ");
    $stmt->execute([$table]);
    return (int)$stmt->fetchColumn() > 0;
}

function smokeCall($label, callable $callback)
{
    try {
        $result = $callback();
        if (!is_array($result)) {
            smokeFail($label . ' no devolvio un arreglo');
        }
        echo 'OK: ' . $label . ' (' . count($result) . " filas)\n";
    } catch (Throwable $e) {
        smokeFail($label . ' fallo: ' . $e->getMessage());
    }
}

$pdo = mainModel::conectar();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$requiredFiles = [
    'index.php',
    'vistas/plantilla.php',
    'controladores/vistasControlador.php',
    'modelos/vistasModelo.php',
    'ajax/loginAjax.php',
    'ajax/reportesAjax.php',
    'vistas/contenidos/reporte-referenciales-vista.php',
    'vistas/contenidos/reporte-movimientos-vista.php',
    'vistas/inc/navLateral.php',
];

foreach ($requiredFiles as $file) {
    smokeAssert(is_file(__DIR__ . '/../' . $file), 'Falta archivo requerido: ' . $file);
}
echo "OK: archivos base del sistema presentes\n";

$moduleTriples = [
    'articulo', 'cliente', 'proveedor', 'sucursal', 'empleado', 'usuario', 'roles',
    'pedido', 'presupuesto', 'ordencompra', 'compra', 'notasCreDe', 'remision',
    'inventario', 'transferencia', 'recepcionservicio', 'diagnostico',
    'presupuestoservicio', 'ordenTrabajo', 'registroServicio', 'reclamoServicio',
    'salidaInsumo', 'reportes'
];

foreach ($moduleTriples as $module) {
    $controller = __DIR__ . '/../controladores/' . $module . 'Controlador.php';
    $model = __DIR__ . '/../modelos/' . $module . 'Modelo.php';
    $ajax = __DIR__ . '/../ajax/' . $module . 'Ajax.php';

    if ($module === 'roles') {
        $model = __DIR__ . '/../modelos/rolesModelo.php';
    }

    smokeAssert(is_file($controller), 'Falta controlador de modulo: ' . $module);
    smokeAssert(is_file($model), 'Falta modelo de modulo: ' . $module);
    smokeAssert(is_file($ajax), 'Falta ajax de modulo: ' . $module);
}
echo "OK: controladores/modelos/ajax principales presentes\n";

$requiredTables = [
    'empresa', 'sucursales', 'usuarios', 'roles', 'permisos', 'rol_permiso',
    'clientes', 'vehiculos', 'modelo_auto', 'marcas', 'ciudades',
    'empleados', 'cargos', 'equipo_trabajo', 'equipo_empleado',
    'articulos', 'categorias', 'unidad_medida', 'tipo_impuesto', 'articulo_proveedor',
    'proveedores', 'stock', 'movimientostock',
    'pedido_cabecera', 'pedido_detalle',
    'presupuesto_compra', 'presupuesto_detalle',
    'orden_compra', 'orden_compra_detalle',
    'compra_cabecera', 'compra_detalle', 'libro_compra',
    'nota_compra', 'nota_compra_detalle', 'nota_remision',
    'transferencia_stock', 'transferencia_stock_detalle',
    'ajuste_inventario', 'ajuste_inventario_detalle',
    'recepcion_servicio', 'recepcion_fotos',
    'diagnostico_servicio', 'diagnostico_detalle',
    'presupuesto_servicio', 'presupuesto_detalleservicio',
    'orden_trabajo', 'orden_trabajo_detalle',
    'registro_servicio', 'registro_servicio_detalle',
    'salida_insumo', 'salida_insumo_detalle',
    'reclamo_servicio', 'reclamo_servicio_detalle',
    'promociones', 'promocion_producto', 'descuentos', 'descuento_cliente',
    'anulacion_auditoria'
];

foreach ($requiredTables as $table) {
    smokeAssert(smokeTableExists($pdo, $table), 'Falta tabla requerida: ' . $table);
}
echo "OK: tablas principales presentes\n";

$seedTables = [
    'usuarios', 'sucursales', 'roles', 'permisos', 'clientes', 'proveedores',
    'articulos', 'categorias', 'marcas', 'unidad_medida', 'tipo_impuesto',
    'empleados', 'cargos', 'vehiculos', 'modelo_auto'
];

foreach ($seedTables as $table) {
    smokeAssert(smokeCount($pdo, $table) > 0, 'Tabla base sin datos: ' . $table);
}
echo "OK: datos base minimos presentes\n";

$admin = $pdo->query("SELECT id_usuario, usu_clave FROM usuarios WHERE usu_nick = 'admin' LIMIT 1")->fetch();
smokeAssert($admin && password_verify('8520123', $admin['usu_clave']), 'No se pudo validar usuario admin');
echo "OK: usuario admin validado\n";

$permissionCount = smokeCount($pdo, 'permisos');
$rolePermissionCount = smokeCount($pdo, 'rol_permiso');
smokeAssert($permissionCount > 0, 'No hay permisos definidos');
smokeAssert($rolePermissionCount > 0, 'No hay permisos asignados a roles');
echo "OK: permisos y roles con asignaciones\n";

$today = date('Y-m-d');
$firstSucursal = (int)$pdo->query("SELECT id_sucursal FROM sucursales ORDER BY id_sucursal LIMIT 1")->fetchColumn();
$firstArticulo = (int)$pdo->query("SELECT id_articulo FROM articulos ORDER BY id_articulo LIMIT 1")->fetchColumn();

$baseFilter = [
    'estado' => 'T',
    'buscar' => '',
    'categoria' => 0,
    'proveedor' => 0,
    'sucursal' => 0,
    'articulo' => 0,
    'codigo' => '',
    'stock' => 'T',
    'modelo' => 0,
    'cargo' => 0,
    'naturaleza' => 'T',
    'tipo_stock' => '',
];

smokeCall('informe referencial articulos', fn() => SystemSmokeReportes::callProtected('reporte_articulos_simple_modelo', [$baseFilter]));
smokeCall('informe stock articulos', fn() => SystemSmokeReportes::callProtected('reporte_articulos_modelo', [$baseFilter]));
smokeCall('informe referencial proveedores', fn() => SystemSmokeReportes::reporte_proveedores_modelo($baseFilter));
smokeCall('informe referencial clientes', fn() => SystemSmokeReportes::reporte_clientes_modelo($baseFilter));
smokeCall('informe referencial vehiculos', fn() => SystemSmokeReportes::reporte_vehiculos_modelo($baseFilter));
smokeCall('informe referencial sucursales', fn() => SystemSmokeReportes::reporte_sucursales_modelo($baseFilter));
smokeCall('informe referencial empleados', fn() => SystemSmokeReportes::reporte_empleados_modelo($baseFilter));
smokeCall('informe referencial marcas', fn() => SystemSmokeReportes::reporte_marcas_modelo($baseFilter));
smokeCall('informe referencial categorias', fn() => SystemSmokeReportes::reporte_categorias_modelo($baseFilter));
smokeCall('informe referencial usuarios', fn() => SystemSmokeReportes::reporte_usuarios_modelo($baseFilter));

smokeCall('movimiento pedidos resumen', fn() => SystemSmokeReportes::callProtected('reporte_pedidos_modelo', [null, null, null, null]));
smokeCall('movimiento pedidos detalle', fn() => SystemSmokeReportes::callProtected('reporte_pedidos_detalle_modelo', [null, null, null, null]));
smokeCall('movimiento presupuestos compra resumen', fn() => SystemSmokeReportes::callProtected('reporte_presupuestos_modelo', [null, null, null, null]));
smokeCall('movimiento presupuestos compra detalle', fn() => SystemSmokeReportes::callProtected('reporte_presupuestos_detalle_modelo', [null, null, null, null]));
smokeCall('movimiento ordenes compra resumen', fn() => SystemSmokeReportes::callProtected('reporte_ordenes_compra_modelo', [null, null, null, null]));
smokeCall('movimiento ordenes compra detalle', fn() => SystemSmokeReportes::callProtected('reporte_ordenes_compra_detalle_modelo', [null, null, null, null]));
smokeCall('movimiento compras resumen', fn() => SystemSmokeReportes::callProtected('reporte_compras_modelo', [null, null, null, null]));
smokeCall('movimiento compras detalle', fn() => SystemSmokeReportes::callProtected('reporte_compras_detalle_modelo', [null, null, null, null]));
smokeCall('movimiento libro compras', fn() => SystemSmokeReportes::callProtected('reporte_libro_compras_modelo', [null, null, null, null, null]));
smokeCall('movimiento transferencias', fn() => SystemSmokeReportes::callProtected('reporte_transferencias_modelo', [[
    'desde' => null,
    'hasta' => null,
    'estado' => '',
    'sucursal' => 0,
    'buscar' => '',
    'vista' => 'resumen',
]]));
smokeCall('movimiento stock', fn() => SystemSmokeReportes::callProtected('reporte_movimientos_stock_modelo', [[
    'desde' => null,
    'hasta' => null,
    'sucursal' => 0,
    'articulo' => 0,
    'naturaleza' => 'T',
    'tipo_stock' => '',
]]));

if ($firstSucursal > 0 && $firstArticulo > 0) {
    smokeCall('movimiento kardex articulo', fn() => SystemSmokeReportes::callProtected('reporte_kardex_articulo_modelo', [[
        'desde' => null,
        'hasta' => null,
        'sucursal' => $firstSucursal,
        'articulo' => $firstArticulo,
        'naturaleza' => 'T',
        'tipo_stock' => '',
    ]]));
}

smokeCall('servicio recepcion resumen', fn() => SystemSmokeReportes::callProtected('reporte_recepcion_servicio_modelo', [null, null, null, null]));
smokeCall('servicio presupuesto resumen', fn() => SystemSmokeReportes::callProtected('reporte_presupuesto_servicio_modelo', [null, null, null, null]));
smokeCall('servicio presupuesto detalle', fn() => SystemSmokeReportes::callProtected('reporte_presupuesto_servicio_detalle_modelo', [null, null, null, null]));
smokeCall('servicio orden trabajo resumen', fn() => SystemSmokeReportes::callProtected('reporte_orden_trabajo_modelo', [null, null, null, null]));
smokeCall('servicio orden trabajo detalle', fn() => SystemSmokeReportes::callProtected('reporte_orden_trabajo_detalle_modelo', [null, null, null, null]));
smokeCall('servicio registro resumen', fn() => SystemSmokeReportes::callProtected('reporte_registro_servicio_modelo', [null, null, null, null, null]));
smokeCall('servicio registro detalle', fn() => SystemSmokeReportes::callProtected('reporte_registro_servicio_detalle_modelo', [null, null, null, null, null]));

echo "OK: prueba de humo integral completada " . $today . PHP_EOL;
