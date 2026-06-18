<?php
require_once __DIR__ . '/../config/SERVER.php';

$expected = [
    'pedido_detalle.cantidad' => 'decimal',
    'pedido_detalle.stock_actual' => 'decimal',
    'orden_compra_detalle.cantidad' => 'decimal',
    'orden_compra_detalle.cantidad_pendiente' => 'decimal',
    'presupuesto_detalle.cantidad' => 'decimal',
    'diagnostico_detalle.cantidad_repuesto' => 'decimal',
    'presupuesto_detalleservicio.cantidad' => 'decimal',
    'orden_trabajo_detalle.cantidad' => 'decimal',
    'registro_servicio_detalle.cantidad' => 'decimal',
    'compra_detalle.cantidad_facturada' => 'decimal',
    'compra_detalle.cantidad_recibida' => 'decimal',
    'salida_insumo_detalle.cantidad' => 'decimal',
    'transferencia_stock_detalle.cantidad' => 'decimal',
    'transferencia_stock_detalle.cantidad_recibida' => 'decimal',
    'stock.stockDisponible' => 'decimal',
    'movimientostock.MovStockCantidad' => 'decimal',
];

try {
    $pdo = new PDO(SGBD, USER, PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    fwrite(STDERR, "ERROR: no se pudo conectar a la base de datos: " . $e->getMessage() . PHP_EOL);
    exit(2);
}

$stmt = $pdo->prepare("
    SELECT COLUMN_TYPE
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = :tabla
      AND COLUMN_NAME = :columna
");

$fallos = [];

foreach ($expected as $qualified => $type) {
    [$tabla, $columna] = explode('.', $qualified, 2);
    $stmt->execute([
        ':tabla' => $tabla,
        ':columna' => $columna,
    ]);
    $columnType = (string)$stmt->fetchColumn();

    if ($columnType === '') {
        $fallos[] = $qualified . ' no existe';
        continue;
    }

    if (stripos($columnType, $type) !== 0) {
        $fallos[] = $qualified . ' usa ' . $columnType . ', esperado ' . $type;
    }
}

if ($fallos) {
    echo "FAIL: columnas pendientes para cantidades decimales" . PHP_EOL;
    foreach ($fallos as $fallo) {
        echo "- " . $fallo . PHP_EOL;
    }
    exit(1);
}

echo "OK: columnas de stock/cantidad preparadas para decimales" . PHP_EOL;
