<?php
require_once __DIR__ . '/../modelos/reportesModelo.php';

class ReportesDecimalTest extends reportesModelo
{
    public static function pedidosDetalle($desde, $hasta, $estado, $sucursal)
    {
        return self::reporte_pedidos_detalle_modelo($desde, $hasta, $estado, $sucursal);
    }

    public static function presupuestosDetalle($desde, $hasta, $estado, $sucursal)
    {
        return self::reporte_presupuestos_detalle_modelo($desde, $hasta, $estado, $sucursal);
    }

    public static function ordenesCompraDetalle($desde, $hasta, $estado, $sucursal)
    {
        return self::reporte_ordenes_compra_detalle_modelo($desde, $hasta, $estado, $sucursal);
    }

    public static function comprasDetalle($desde, $hasta, $estado, $sucursal)
    {
        return self::reporte_compras_detalle_modelo($desde, $hasta, $estado, $sucursal);
    }

    public static function presupuestoServicioDetalle($desde, $hasta, $estado, $sucursal)
    {
        return self::reporte_presupuesto_servicio_detalle_modelo($desde, $hasta, $estado, $sucursal);
    }

    public static function ordenTrabajoDetalle($desde, $hasta, $estado, $sucursal)
    {
        return self::reporte_orden_trabajo_detalle_modelo($desde, $hasta, $estado, $sucursal);
    }

    public static function registroServicioDetalle($desde, $hasta, $estado, $empleado, $sucursal)
    {
        return self::reporte_registro_servicio_detalle_modelo($desde, $hasta, $estado, $empleado, $sucursal);
    }

    public static function movimientosStock($filtros)
    {
        return self::reporte_movimientos_stock_modelo($filtros);
    }

    public static function articulosStock($filtros)
    {
        return self::reporte_articulos_modelo($filtros);
    }
}

function fail($message)
{
    fwrite(STDERR, 'FAIL: ' . $message . PHP_EOL);
    exit(1);
}

function assertDecimal($label, $expected, $actual, $delta = 0.0001)
{
    if (abs((float)$expected - (float)$actual) > $delta) {
        fail($label . ' esperado ' . $expected . ', obtenido ' . $actual);
    }
}

function findRow(array $rows, callable $predicate, $label)
{
    foreach ($rows as $row) {
        if ($predicate($row)) {
            return $row;
        }
    }
    fail('No se encontro fila en reporte: ' . $label);
}

$pdo = mainModel::conectar();
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$proveedor = $pdo->query("
    SELECT idproveedores, razon_social
    FROM proveedores
    WHERE razon_social LIKE 'TEST_DECIMAL_% PROVEEDOR'
    ORDER BY idproveedores DESC
    LIMIT 1
")->fetch();

if (!$proveedor) {
    fail('No hay datos TEST_DECIMAL. Ejecute primero tests/decimal_end_to_end.php');
}

$token = preg_replace('/ PROVEEDOR$/', '', $proveedor['razon_social']);
$today = date('Y-m-d');
$sucursal = 1;

$producto = $pdo->prepare("SELECT id_articulo FROM articulos WHERE desc_articulo = ?");
$producto->execute([$token . ' ACEITE PRODUCTO']);
$productoId = (int)$producto->fetchColumn();

$insumo = $pdo->prepare("SELECT id_articulo FROM articulos WHERE desc_articulo = ?");
$insumo->execute([$token . ' INSUMO LITRO']);
$insumoId = (int)$insumo->fetchColumn();

if ($productoId <= 0 || $insumoId <= 0) {
    fail('No se encontraron articulos del token ' . $token);
}

$pedidoId = (int)$pdo->query("SELECT MAX(idpedido_cabecera) FROM pedido_cabecera")->fetchColumn();
$presupuestoCompraId = (int)$pdo->query("SELECT MAX(idpresupuesto_compra) FROM presupuesto_compra")->fetchColumn();
$ordenCompraId = (int)$pdo->query("SELECT MAX(idorden_compra) FROM orden_compra")->fetchColumn();
$compraId = (int)$pdo->query("SELECT MAX(idcompra_cabecera) FROM compra_cabecera")->fetchColumn();
$presupuestoServicioId = (int)$pdo->query("SELECT MAX(idpresupuesto_servicio) FROM presupuesto_servicio")->fetchColumn();
$ordenTrabajoId = (int)$pdo->query("SELECT MAX(idorden_trabajo) FROM orden_trabajo")->fetchColumn();
$registroServicioId = (int)$pdo->query("SELECT MAX(idregistro_servicio) FROM registro_servicio")->fetchColumn();

$pedido = findRow(
    ReportesDecimalTest::pedidosDetalle($today, $today, 1, $sucursal),
    fn($row) => (int)$row['idpedido_cabecera'] === $pedidoId && (int)$row['id_articulo'] === $productoId,
    'pedido detalle decimal'
);
assertDecimal('reporte pedido cantidad', 1.7500, $pedido['cantidad']);

$presupuesto = findRow(
    ReportesDecimalTest::presupuestosDetalle($today, $today, 1, $sucursal),
    fn($row) => (int)$row['idpresupuesto_compra'] === $presupuestoCompraId && (int)$row['id_articulo'] === $productoId,
    'presupuesto compra detalle decimal'
);
assertDecimal('reporte presupuesto compra cantidad', 1.7500, $presupuesto['cantidad']);

$oc = findRow(
    ReportesDecimalTest::ordenesCompraDetalle($today, $today, 1, $sucursal),
    fn($row) => (int)$row['idorden_compra'] === $ordenCompraId && (int)$row['id_articulo'] === $productoId,
    'orden compra detalle decimal'
);
assertDecimal('reporte oc cantidad', 1.7500, $oc['cantidad']);
assertDecimal('reporte oc pendiente', 0.5000, $oc['cantidad_pendiente']);

$compra = findRow(
    ReportesDecimalTest::comprasDetalle($today, $today, 1, $sucursal),
    fn($row) => (int)$row['idcompra_cabecera'] === $compraId && (int)$row['id_articulo'] === $productoId,
    'compra detalle decimal'
);
assertDecimal('reporte compra facturada', 1.7500, $compra['cantidad_facturada']);
assertDecimal('reporte compra recibida', 1.2500, $compra['cantidad_recibida']);
assertDecimal('reporte compra diferencia', 0.5000, $compra['cantidad_diferencia']);

$ps = findRow(
    ReportesDecimalTest::presupuestoServicioDetalle($today, $today, 2, $sucursal),
    fn($row) => (int)$row['idpresupuesto_servicio'] === $presupuestoServicioId && (int)$row['id_articulo'] === $productoId,
    'presupuesto servicio detalle decimal'
);
assertDecimal('reporte presupuesto servicio repuesto', 0.7500, $ps['cantidad']);

$ot = findRow(
    ReportesDecimalTest::ordenTrabajoDetalle($today, $today, 1, $sucursal),
    fn($row) => (int)$row['idorden_trabajo'] === $ordenTrabajoId && (int)$row['id_articulo'] === $productoId,
    'orden trabajo detalle decimal'
);
assertDecimal('reporte ot repuesto', 0.7500, $ot['cantidad']);

$registro = findRow(
    ReportesDecimalTest::registroServicioDetalle($today, $today, 1, null, $sucursal),
    fn($row) => (int)$row['idregistro_servicio'] === $registroServicioId && (int)$row['id_articulo'] === $productoId,
    'registro servicio detalle decimal'
);
assertDecimal('reporte registro servicio repuesto', 0.7500, $registro['cantidad']);

$stock = findRow(
    ReportesDecimalTest::articulosStock([
        'sucursal' => $sucursal,
        'articulo' => $productoId,
        'estado' => 'A',
    ]),
    fn($row) => (int)$row['id_articulo'] === $productoId,
    'stock decimal'
);
assertDecimal('reporte stock producto', 11.2500, $stock['stock']);

$movs = ReportesDecimalTest::movimientosStock([
    'desde' => $today,
    'hasta' => $today,
    'sucursal' => $sucursal,
    'articulo' => $productoId,
]);

$movimientoDecimal = findRow(
    $movs,
    fn($row) => (int)$row['id_articulo'] === $productoId
        && in_array((string)$row['MovStockCantidad'], ['0.7500', '1.2500', '10.7500'], true),
    'movimiento stock decimal'
);

if ((float)$movimientoDecimal['MovStockCantidad'] <= 0) {
    fail('Movimiento stock decimal invalido');
}

echo "OK: reportes leen cantidades decimales para " . $token . PHP_EOL;
