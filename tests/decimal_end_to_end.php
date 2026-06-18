<?php
require_once __DIR__ . '/../config/SERVER.php';
require_once __DIR__ . '/../modelos/mainModel.php';

function assertSameDecimal($label, $expected, $actual, $scale = 0.0001)
{
    if (abs((float)$expected - (float)$actual) > $scale) {
        throw new RuntimeException($label . ' esperado ' . $expected . ', obtenido ' . $actual);
    }
}

function fetchValue(PDO $pdo, $sql, array $params = [])
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

$pdo = new PDO(SGBD, USER, PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$token = 'TEST_DECIMAL_' . date('Ymd_His');
$today = date('Y-m-d');
$future = date('Y-m-d', strtotime('+30 days'));

$admin = $pdo->query("SELECT id_usuario, usu_clave FROM usuarios WHERE usu_nick = 'admin' LIMIT 1")->fetch();
if (!$admin || !password_verify('8520123', $admin['usu_clave'])) {
    throw new RuntimeException('No se pudo validar usuario admin con la clave indicada');
}

$usuario = (int)$admin['id_usuario'];
$sucursal = 1;
$ciudad = 1;
$modelo = 1;
$marca = 1;
$categoria = 2;
$unidad = 1;
$iva = 2;
$equipo = (int)fetchValue($pdo, "SELECT id_equipo FROM equipo_trabajo WHERE id_sucursal = ? AND estado = 1 LIMIT 1", [$sucursal]);
$tecnico = (int)fetchValue($pdo, "SELECT idempleados FROM empleados WHERE id_sucursal = ? AND estado = 1 LIMIT 1", [$sucursal]);

if ($equipo <= 0 || $tecnico <= 0) {
    throw new RuntimeException('Faltan equipo o tecnico activo para la sucursal de prueba');
}

$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare("
        INSERT INTO proveedores
        (id_ciudad, razon_social, ruc, telefono, direccion, correo, estado)
        VALUES (?, ?, ?, '0981000000', 'Direccion test decimal', 'decimal@test.local', 1)
    ");
    $stmt->execute([$ciudad, $token . ' PROVEEDOR', substr('9' . date('His') . '-1', 0, 15)]);
    $proveedor = (int)$pdo->lastInsertId();

    $stmt = $pdo->prepare("
        INSERT INTO clientes
        (id_ciudad, doc_number, nombre_cliente, apellido_cliente, direccion_cliente, celular_cliente,
         digito_v, estado_civil, estado_cliente, doc_type, email_cliente)
        VALUES (?, ?, ?, 'Cliente', 'Direccion test', '0981000001', '1', 'Soltero', 1, 'CI', 'cliente.decimal@test.local')
    ");
    $stmt->execute([$ciudad, (string)random_int(7000000, 7999999), $token]);
    $cliente = (int)$pdo->lastInsertId();

    $placa = 'TD' . substr((string)time(), -5);
    $stmt = $pdo->prepare("
        INSERT INTO vehiculos
        (id_cliente, id_modeloauto, placa, anho, estado, color, version, transmision, motor, tipo_vehiculo)
        VALUES (?, ?, ?, YEAR(CURDATE()), 1, 'gris', 'test', 'manual', 'decimal', 'Automovil')
    ");
    $stmt->execute([$cliente, $modelo, $placa]);
    $vehiculo = (int)$pdo->lastInsertId();

    $insertArticulo = $pdo->prepare("
        INSERT INTO articulos
        (id_categoria, idunidad_medida, idiva, id_marcas, desc_articulo, precio_venta, codigo, estado, date_created, tipo)
        VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?)
    ");

    $insertArticulo->execute([$categoria, $unidad, $iva, $marca, $token . ' ACEITE PRODUCTO', 55000, 'TDP' . substr((string)time(), -6), 'producto']);
    $producto = (int)$pdo->lastInsertId();

    $insertArticulo->execute([$categoria, $unidad, $iva, $marca, $token . ' INSUMO LITRO', 22000, 'TDI' . substr((string)time(), -6), 'insumo']);
    $insumo = (int)$pdo->lastInsertId();

    $insertArticulo->execute([$categoria, $unidad, $iva, $marca, $token . ' SERVICIO CAMBIO', 80000, 'TDS' . substr((string)time(), -6), 'servicio']);
    $servicio = (int)$pdo->lastInsertId();

    mainModel::registrar_movimiento_stock_modelo($pdo, [
        'id_sucursal' => $sucursal,
        'tipo' => 'TEST STOCK INICIAL',
        'id_articulo' => $producto,
        'cantidad' => 10.7500,
        'precio_venta' => 55000,
        'costo' => 40000,
        'usuario' => $usuario,
        'signo' => 1,
        'referencia' => $token . ' PRODUCTO',
    ]);

    mainModel::registrar_movimiento_stock_modelo($pdo, [
        'id_sucursal' => $sucursal,
        'tipo' => 'TEST STOCK INICIAL',
        'id_articulo' => $insumo,
        'cantidad' => 5.5000,
        'precio_venta' => 22000,
        'costo' => 12000,
        'usuario' => $usuario,
        'signo' => 1,
        'referencia' => $token . ' INSUMO',
    ]);

    $stockProductoInicial = fetchValue($pdo, "SELECT stockDisponible FROM stock WHERE id_sucursal = ? AND id_articulo = ?", [$sucursal, $producto]);
    $stockInsumoInicial = fetchValue($pdo, "SELECT stockDisponible FROM stock WHERE id_sucursal = ? AND id_articulo = ?", [$sucursal, $insumo]);
    assertSameDecimal('stock producto inicial', 10.7500, $stockProductoInicial);
    assertSameDecimal('stock insumo inicial', 5.5000, $stockInsumoInicial);

    $pdo->prepare("INSERT INTO pedido_cabecera (id_sucursal, id_usuario, fecha, estado) VALUES (?, ?, NOW(), 1)")
        ->execute([$sucursal, $usuario]);
    $pedido = (int)$pdo->lastInsertId();
    $pdo->prepare("INSERT INTO pedido_detalle (idpedido_cabecera, id_articulo, cantidad, stock_actual) VALUES (?, ?, ?, ?)")
        ->execute([$pedido, $producto, 1.7500, $stockProductoInicial]);

    $pdo->prepare("
        INSERT INTO presupuesto_compra
        (id_sucursal, idproveedores, id_usuario, fecha, estado, fecha_venc, total, idPedido)
        VALUES (?, ?, ?, ?, 1, ?, ?, ?)
    ")->execute([$sucursal, $proveedor, $usuario, $today, $future, 70000, $pedido]);
    $presupuestoCompra = (int)$pdo->lastInsertId();
    $pdo->prepare("INSERT INTO presupuesto_detalle (idpresupuesto_compra, id_articulo, cantidad, precio, subtotal) VALUES (?, ?, ?, ?, ?)")
        ->execute([$presupuestoCompra, $producto, 1.7500, 40000, 70000]);

    $pdo->prepare("
        INSERT INTO orden_compra
        (id_sucursal, presupuestoid, idproveedores, id_usuario, fecha, estado, fecha_entrega)
        VALUES (?, ?, ?, ?, ?, 1, ?)
    ")->execute([$sucursal, $presupuestoCompra, $proveedor, $usuario, $today, $future]);
    $ordenCompra = (int)$pdo->lastInsertId();
    $pdo->prepare("
        INSERT INTO orden_compra_detalle
        (idorden_compra, id_articulo, cantidad, precio_unitario, cantidad_pendiente)
        VALUES (?, ?, ?, ?, ?)
    ")->execute([$ordenCompra, $producto, 1.7500, 40000, 1.7500]);

    $pdo->prepare("
        INSERT INTO compra_cabecera
        (id_sucursal, idproveedores, id_usuario, fecha_creacion, nro_factura, fecha_factura, nro_timbrado,
         vencimiento_timbrado, estado, total_compra, condicion, compra_intervalo, idOcompra)
        VALUES (?, ?, ?, NOW(), ?, ?, 987654, ?, 1, 70000, 'contado', '7', ?)
    ")->execute([$sucursal, $proveedor, $usuario, 'TEST-' . substr((string)time(), -8), $today, $future, $ordenCompra]);
    $compra = (int)$pdo->lastInsertId();
    $pdo->prepare("
        INSERT INTO compra_detalle
        (idcompra_cabecera, id_articulo, precio_unitario, cantidad_facturada, cantidad_recibida, subtotal, tipo_iva, ivaPro)
        VALUES (?, ?, ?, ?, ?, ?, '2', ?)
    ")->execute([$compra, $producto, 40000, 1.7500, 1.2500, 70000, 6363.64]);

    $pdo->prepare("
        UPDATE orden_compra_detalle
        SET cantidad_pendiente = cantidad_pendiente - ?
        WHERE idorden_compra = ? AND id_articulo = ?
    ")->execute([1.2500, $ordenCompra, $producto]);

    mainModel::registrar_movimiento_stock_modelo($pdo, [
        'id_sucursal' => $sucursal,
        'tipo' => 'TEST RECEPCION COMPRA',
        'id_articulo' => $producto,
        'cantidad' => 1.2500,
        'precio_venta' => 0,
        'costo' => 40000,
        'usuario' => $usuario,
        'signo' => 1,
        'referencia' => $token . ' COMPRA #' . $compra,
    ]);

    $pdo->prepare("
        INSERT INTO recepcion_servicio
        (id_usuario, id_vehiculo, id_cliente, fecha_ingreso, kilometraje, nivel_combustible, estado_exterior,
         objetos_vehiculo, tipo_servicio, area_problema, prioridad, accesorios, observacion, estado, id_sucursal)
        VALUES (?, ?, ?, NOW(), '1000', '1/2', 'sin_danos', '', 'mantenimiento', 'motor',
         'normal', 'llave', ?, 1, ?)
    ")->execute([$usuario, $vehiculo, $cliente, $token . ' recepcion', $sucursal]);
    $recepcion = (int)$pdo->lastInsertId();

    $pdo->prepare("
        INSERT INTO diagnostico_servicio
        (id_usuario, idrecepcion, id_equipo, id_sucursal, fecha_diagnostico, descripcion_cliente,
         diagnostico_general, estado, observaciones)
        VALUES (?, ?, ?, ?, NOW(), ?, ?, 2, ?)
    ")->execute([$usuario, $recepcion, $equipo, $sucursal, $token, 'Diagnostico decimal', 'Observacion decimal']);
    $diagnostico = (int)$pdo->lastInsertId();

    $pdo->prepare("
        INSERT INTO diagnostico_detalle
        (id_diagnostico, id_articulo_servicio, id_articulo_repuesto, cantidad_repuesto, problema, gravedad, repuesto_origen)
        VALUES (?, ?, ?, ?, 'Aceite por litro', 'media', 'TALLER')
    ")->execute([$diagnostico, $servicio, $producto, 0.7500]);
    $diagnosticoDetalle = (int)$pdo->lastInsertId();

    $subtotalServicio = 80000;
    $subtotalRepuesto = 0.7500 * 55000;
    $totalServicio = $subtotalServicio + $subtotalRepuesto;
    $pdo->prepare("
        INSERT INTO presupuesto_servicio
        (id_diagnostico, id_usuario, id_sucursal, id_cliente, id_vehiculo, fecha, estado, fecha_venc,
         subtotal, total_descuento, total_final, origen)
        VALUES (?, ?, ?, ?, ?, ?, 2, ?, ?, 0, ?, 'DIAGNOSTICO')
    ")->execute([$diagnostico, $usuario, $sucursal, $cliente, $vehiculo, $today, $future, $totalServicio, $totalServicio]);
    $presupuestoServicio = (int)$pdo->lastInsertId();

    $pdo->prepare("
        INSERT INTO presupuesto_detalleservicio
        (idpresupuesto_servicio, id_articulo, id_diagnostico_detalle, cantidad, preciouni, subtotal)
        VALUES (?, ?, ?, ?, ?, ?)
    ")->execute([$presupuestoServicio, $servicio, $diagnosticoDetalle, 1.0000, 80000, $subtotalServicio]);
    $pdo->prepare("
        INSERT INTO presupuesto_detalleservicio
        (idpresupuesto_servicio, id_articulo, id_diagnostico_detalle, cantidad, preciouni, subtotal)
        VALUES (?, ?, ?, ?, ?, ?)
    ")->execute([$presupuestoServicio, $producto, $diagnosticoDetalle, 0.7500, 55000, $subtotalRepuesto]);

    $pdo->prepare("
        INSERT INTO orden_trabajo
        (idtrabajos, tecnico_responsable, idpresupuesto_servicio, id_usuario, id_cliente, id_vehiculo,
         id_sucursal, estado, observacion)
        VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)
    ")->execute([$equipo, $tecnico, $presupuestoServicio, $usuario, $cliente, $vehiculo, $sucursal, $token . ' OT']);
    $ot = (int)$pdo->lastInsertId();

    $pdo->prepare("
        INSERT INTO orden_trabajo_detalle
        (cantidad, precio_unitario, subtotal, idorden_trabajo, id_articulo)
        SELECT cantidad, preciouni, subtotal, ?, id_articulo
        FROM presupuesto_detalleservicio
        WHERE idpresupuesto_servicio = ?
    ")->execute([$ot, $presupuestoServicio]);

    $pdo->prepare("
        INSERT INTO registro_servicio
        (idorden_trabajo, id_vehiculo, id_cliente, id_sucursal, fecha_servicio, kilometraje_salida,
         usuario_registra, estado, observacion)
        VALUES (?, ?, ?, ?, ?, 1005, ?, 1, ?)
    ")->execute([$ot, $vehiculo, $cliente, $sucursal, $today, $usuario, $token . ' registro']);
    $registro = (int)$pdo->lastInsertId();

    $pdo->prepare("
        INSERT INTO registro_servicio_detalle
        (idregistro_servicio, id_articulo, cantidad, precio_unitario, subtotal, origen)
        SELECT ?, id_articulo, cantidad, precio_unitario, subtotal, 'OT'
        FROM orden_trabajo_detalle
        WHERE idorden_trabajo = ?
    ")->execute([$registro, $ot]);

    mainModel::registrar_movimiento_stock_modelo($pdo, [
        'id_sucursal' => $sucursal,
        'tipo' => 'TEST REGISTRO SERVICIO',
        'id_articulo' => $producto,
        'cantidad' => 0.7500,
        'precio_venta' => 55000,
        'costo' => 40000,
        'usuario' => $usuario,
        'signo' => -1,
        'referencia' => $token . ' REG #' . $registro,
    ]);

    $pdo->prepare("
        INSERT INTO salida_insumo
        (id_sucursal, id_usuario, id_tecnico, fecha, observacion, estado)
        VALUES (?, ?, ?, NOW(), ?, 1)
    ")->execute([$sucursal, $usuario, $tecnico, $token . ' salida insumo']);
    $salida = (int)$pdo->lastInsertId();
    $pdo->prepare("INSERT INTO salida_insumo_detalle (idsalida_insumo, id_articulo, cantidad) VALUES (?, ?, ?)")
        ->execute([$salida, $insumo, 1.2500]);

    mainModel::registrar_movimiento_stock_modelo($pdo, [
        'id_sucursal' => $sucursal,
        'tipo' => 'TEST SALIDA INSUMO',
        'id_articulo' => $insumo,
        'cantidad' => 1.2500,
        'precio_venta' => 22000,
        'costo' => 12000,
        'usuario' => $usuario,
        'signo' => -1,
        'referencia' => $token . ' SALIDA #' . $salida,
    ]);

    $checks = [
        'pedido_detalle.cantidad' => fetchValue($pdo, "SELECT cantidad FROM pedido_detalle WHERE idpedido_cabecera = ? AND id_articulo = ?", [$pedido, $producto]),
        'presupuesto_detalle.cantidad' => fetchValue($pdo, "SELECT cantidad FROM presupuesto_detalle WHERE idpresupuesto_compra = ? AND id_articulo = ?", [$presupuestoCompra, $producto]),
        'orden_compra_detalle.cantidad' => fetchValue($pdo, "SELECT cantidad FROM orden_compra_detalle WHERE idorden_compra = ? AND id_articulo = ?", [$ordenCompra, $producto]),
        'orden_compra_detalle.cantidad_pendiente' => fetchValue($pdo, "SELECT cantidad_pendiente FROM orden_compra_detalle WHERE idorden_compra = ? AND id_articulo = ?", [$ordenCompra, $producto]),
        'compra_detalle.cantidad_facturada' => fetchValue($pdo, "SELECT cantidad_facturada FROM compra_detalle WHERE idcompra_cabecera = ? AND id_articulo = ?", [$compra, $producto]),
        'compra_detalle.cantidad_recibida' => fetchValue($pdo, "SELECT cantidad_recibida FROM compra_detalle WHERE idcompra_cabecera = ? AND id_articulo = ?", [$compra, $producto]),
        'diagnostico_detalle.cantidad_repuesto' => fetchValue($pdo, "SELECT cantidad_repuesto FROM diagnostico_detalle WHERE id_diagnostico_detalle = ?", [$diagnosticoDetalle]),
        'presupuesto_detalleservicio.repuesto' => fetchValue($pdo, "SELECT cantidad FROM presupuesto_detalleservicio WHERE idpresupuesto_servicio = ? AND id_articulo = ?", [$presupuestoServicio, $producto]),
        'orden_trabajo_detalle.repuesto' => fetchValue($pdo, "SELECT cantidad FROM orden_trabajo_detalle WHERE idorden_trabajo = ? AND id_articulo = ?", [$ot, $producto]),
        'registro_servicio_detalle.repuesto' => fetchValue($pdo, "SELECT cantidad FROM registro_servicio_detalle WHERE idregistro_servicio = ? AND id_articulo = ?", [$registro, $producto]),
        'salida_insumo_detalle.cantidad' => fetchValue($pdo, "SELECT cantidad FROM salida_insumo_detalle WHERE idsalida_insumo = ? AND id_articulo = ?", [$salida, $insumo]),
    ];

    assertSameDecimal('pedido decimal', 1.7500, $checks['pedido_detalle.cantidad']);
    assertSameDecimal('presupuesto compra decimal', 1.7500, $checks['presupuesto_detalle.cantidad']);
    assertSameDecimal('oc decimal', 1.7500, $checks['orden_compra_detalle.cantidad']);
    assertSameDecimal('oc pendiente decimal', 0.5000, $checks['orden_compra_detalle.cantidad_pendiente']);
    assertSameDecimal('compra facturada decimal', 1.7500, $checks['compra_detalle.cantidad_facturada']);
    assertSameDecimal('compra recibida decimal', 1.2500, $checks['compra_detalle.cantidad_recibida']);
    assertSameDecimal('diagnostico repuesto decimal', 0.7500, $checks['diagnostico_detalle.cantidad_repuesto']);
    assertSameDecimal('presupuesto servicio repuesto decimal', 0.7500, $checks['presupuesto_detalleservicio.repuesto']);
    assertSameDecimal('ot repuesto decimal', 0.7500, $checks['orden_trabajo_detalle.repuesto']);
    assertSameDecimal('registro servicio repuesto decimal', 0.7500, $checks['registro_servicio_detalle.repuesto']);
    assertSameDecimal('salida insumo decimal', 1.2500, $checks['salida_insumo_detalle.cantidad']);

    $stockProductoFinal = fetchValue($pdo, "SELECT stockDisponible FROM stock WHERE id_sucursal = ? AND id_articulo = ?", [$sucursal, $producto]);
    $stockInsumoFinal = fetchValue($pdo, "SELECT stockDisponible FROM stock WHERE id_sucursal = ? AND id_articulo = ?", [$sucursal, $insumo]);
    assertSameDecimal('stock producto final', 11.2500, $stockProductoFinal);
    assertSameDecimal('stock insumo final', 4.2500, $stockInsumoFinal);

    $movProducto = fetchValue($pdo, "
        SELECT COALESCE(SUM(MovStockCantidad * MovStockSigno), 0)
        FROM movimientostock
        WHERE MovStockArticuloId = ?
          AND MovStockReferencia LIKE ?
    ", [$producto, $token . '%']);
    $movInsumo = fetchValue($pdo, "
        SELECT COALESCE(SUM(MovStockCantidad * MovStockSigno), 0)
        FROM movimientostock
        WHERE MovStockArticuloId = ?
          AND MovStockReferencia LIKE ?
    ", [$insumo, $token . '%']);
    assertSameDecimal('kardex producto test', 11.2500, $movProducto);
    assertSameDecimal('kardex insumo test', 4.2500, $movInsumo);

    $informePresupuestoServicio = $pdo->prepare("
        SELECT SUM(pds.cantidad) AS cantidad
        FROM presupuesto_servicio ps
        INNER JOIN presupuesto_detalleservicio pds
            ON pds.idpresupuesto_servicio = ps.idpresupuesto_servicio
        WHERE ps.idpresupuesto_servicio = ?
    ");
    $informePresupuestoServicio->execute([$presupuestoServicio]);
    assertSameDecimal('informe presupuesto servicio suma cantidades', 1.7500, $informePresupuestoServicio->fetchColumn());

    $informeRegistroServicio = $pdo->prepare("
        SELECT SUM(CASE WHEN rsd.origen = 'OT' THEN rsd.cantidad ELSE 0 END) AS cantidad
        FROM registro_servicio rs
        INNER JOIN registro_servicio_detalle rsd
            ON rsd.idregistro_servicio = rs.idregistro_servicio
        WHERE rs.idregistro_servicio = ?
    ");
    $informeRegistroServicio->execute([$registro]);
    assertSameDecimal('informe registro servicio suma cantidades', 1.7500, $informeRegistroServicio->fetchColumn());

    $pdo->commit();

    echo "OK: prueba integral decimal completada" . PHP_EOL;
    echo "Token: " . $token . PHP_EOL;
    echo "Proveedor: " . $proveedor . PHP_EOL;
    echo "Cliente: " . $cliente . PHP_EOL;
    echo "Vehiculo: " . $vehiculo . PHP_EOL;
    echo "Producto: " . $producto . " stock_final=" . $stockProductoFinal . PHP_EOL;
    echo "Insumo: " . $insumo . " stock_final=" . $stockInsumoFinal . PHP_EOL;
    echo "Servicio: " . $servicio . PHP_EOL;
    echo "Pedido: " . $pedido . PHP_EOL;
    echo "Presupuesto compra: " . $presupuestoCompra . PHP_EOL;
    echo "Orden compra: " . $ordenCompra . PHP_EOL;
    echo "Compra: " . $compra . PHP_EOL;
    echo "Diagnostico: " . $diagnostico . PHP_EOL;
    echo "Presupuesto servicio: " . $presupuestoServicio . PHP_EOL;
    echo "Orden trabajo: " . $ot . PHP_EOL;
    echo "Registro servicio: " . $registro . PHP_EOL;
    echo "Salida insumo: " . $salida . PHP_EOL;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, "FAIL: " . $e->getMessage() . PHP_EOL);
    exit(1);
}
