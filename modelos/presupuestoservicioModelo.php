<?php
require_once "mainModel.php";

class presupuestoservicioModelo extends mainModel
{
    protected static function datos_diagnostico_modelo($id)
    {
        $pdo = mainModel::conectar();

        /* CABECERA */
        $sql = $pdo->prepare("
        SELECT 
            c.id_cliente,
            CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
            v.id_vehiculo,
            CONCAT(ma.mar_descri, ' ', m.mod_descri, ' ', v.placa) AS vehiculo,
            ma.mar_descri AS marca,
            m.mod_descri AS modelo,
            v.placa,
            r.kilometraje,
            d.observaciones,
            r.id_sucursal
        FROM diagnostico_servicio d
        INNER JOIN recepcion_servicio r ON r.idrecepcion = d.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        INNER JOIN marcas ma ON ma.id_marcas = m.id_marcas
        WHERE d.id_diagnostico = ?
          AND d.estado = 1
          AND NOT EXISTS (
              SELECT 1
              FROM orden_trabajo ot
              WHERE ot.idreclamo_servicio = r.idreclamo_servicio
                AND ot.estado != 0
          )
        LIMIT 1
        ");
        $sql->execute([$id]);

        $cabecera = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$cabecera) return [];

        /* DETALLE */
        $sqlDet = $pdo->prepare("
        SELECT
            dd.id_articulo_servicio,
            dd.id_articulo_repuesto,
            dd.problema,
            dd.gravedad,
            dd.cantidad_repuesto,
            dd.repuesto_origen,
            serv.codigo AS servicio_codigo,
            serv.desc_articulo AS servicio_desc,
            serv.precio_venta AS servicio_precio,
            serv.tipo AS servicio_tipo,
            rep.codigo AS repuesto_codigo,
            rep.desc_articulo AS repuesto_desc,
            rep.precio_venta AS repuesto_precio,
            rep.tipo AS repuesto_tipo,
            IFNULL(stock_rep.stockDisponible, 0) AS repuesto_stock
        FROM diagnostico_detalle dd
        LEFT JOIN articulos serv ON serv.id_articulo = dd.id_articulo_servicio
        LEFT JOIN articulos rep ON rep.id_articulo = dd.id_articulo_repuesto
        LEFT JOIN stock stock_rep
            ON stock_rep.id_articulo = rep.id_articulo
            AND stock_rep.id_sucursal = :sucursal
        WHERE dd.id_diagnostico = :id");
        $sqlDet->execute([
            ':sucursal' => $cabecera['id_sucursal'],
            ':id' => $id
        ]);

        $detalleDiagnostico = $sqlDet->fetchAll(PDO::FETCH_ASSOC);
        $cabecera['detalle'] = $detalleDiagnostico;

        $detallePresupuesto = [];
        foreach ($detalleDiagnostico as $det) {
            $idServicio = (int)($det['id_articulo_servicio'] ?? 0);
            if ($idServicio > 0 && ($det['servicio_tipo'] ?? '') === 'servicio') {
                if (!isset($detallePresupuesto[$idServicio])) {
                    $detallePresupuesto[$idServicio] = [
                        'id_articulo' => $idServicio,
                        'codigo' => $det['servicio_codigo'],
                        'desc_articulo' => $det['servicio_desc'],
                        'precio_venta' => (float)$det['servicio_precio'],
                        'tipo' => 'servicio',
                        'stock' => 0,
                        'cantidad' => 0
                    ];
                }

                $detallePresupuesto[$idServicio]['cantidad'] += 1;
            }

            if (
                strtoupper($det['repuesto_origen'] ?? '') === 'TALLER' &&
                !empty($det['id_articulo_repuesto']) &&
                ($det['repuesto_tipo'] ?? '') === 'producto'
            ) {
                $idRepuesto = (int)$det['id_articulo_repuesto'];
                $cantidad = (float)($det['cantidad_repuesto'] ?? 0);
                if ($cantidad <= 0) {
                    $cantidad = 1;
                }

                if (!isset($detallePresupuesto[$idRepuesto])) {
                    $detallePresupuesto[$idRepuesto] = [
                        'id_articulo' => $idRepuesto,
                        'codigo' => $det['repuesto_codigo'],
                        'desc_articulo' => $det['repuesto_desc'],
                        'precio_venta' => (float)$det['repuesto_precio'],
                        'tipo' => 'producto',
                        'stock' => (float)$det['repuesto_stock'],
                        'cantidad' => 0
                    ];
                }

                $detallePresupuesto[$idRepuesto]['cantidad'] += $cantidad;
            }
        }

        $cabecera['detalle_presupuesto'] = array_values($detallePresupuesto);

        return $cabecera;
    }

    protected static function buscar_servicios_modelo($txt, $sucursal)
    {
        $txt = "%$txt%";

        $sql = mainModel::conectar()->prepare("
        SELECT
            a.id_articulo,
            a.desc_articulo,
            a.codigo,
            a.precio_venta,
            a.tipo,
            IFNULL(s.stockDisponible, 0) AS stock
        FROM articulos a
        LEFT JOIN stock s 
            ON s.id_articulo = a.id_articulo 
            AND s.id_sucursal = :sucursal
        WHERE a.estado = 1
          AND (a.desc_articulo LIKE :b OR a.codigo LIKE :b)
        ORDER BY a.desc_articulo
        LIMIT 20
        ");

        $sql->bindParam(':b', $txt);
        $sql->bindParam(':sucursal', $sucursal, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function promo_articulo_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            p.id_promocion,
            p.nombre,
            p.tipo,
            p.valor
        FROM promociones p
        INNER JOIN promocion_producto pp
            ON pp.id_promocion = p.id_promocion
        WHERE pp.id_articulo = :id
          AND p.estado = 1
          AND CURDATE() BETWEEN p.fecha_inicio AND p.fecha_fin
          AND (p.id_sucursal IS NULL OR p.id_sucursal = :sucursal)
        ORDER BY p.valor DESC, p.id_promocion DESC
        LIMIT 1
        ");

        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        $sql->bindValue(':sucursal', $_SESSION['nick_sucursal'] ?? 0, PDO::PARAM_INT);
        $sql->execute();

        $promo = $sql->fetch(PDO::FETCH_ASSOC);

        return json_encode($promo ?: []);
    }

    protected static function descuentos_cliente_modelo($idCliente)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            d.id_descuento,
            d.nombre,
            d.tipo,
            d.valor,
            d.aplica_a,
            d.es_reutilizable
        FROM descuentos d
        INNER JOIN descuento_cliente dc
            ON dc.id_descuento = d.id_descuento
        WHERE dc.id_cliente = :cliente
          AND d.estado = 1
          AND (d.fecha_inicio IS NULL OR d.fecha_inicio <= CURDATE())
          AND (d.fecha_fin IS NULL OR d.fecha_fin >= CURDATE())
          AND (d.id_sucursal IS NULL OR d.id_sucursal = :sucursal)");

        $sql->bindParam(':cliente', $idCliente, PDO::PARAM_INT);
        $sql->bindValue(':sucursal', $_SESSION['nick_sucursal'] ?? 0, PDO::PARAM_INT);
        $sql->execute();

        return json_encode($sql->fetchAll(PDO::FETCH_ASSOC));
    }

    protected static function guardar_presupuesto_modelo($d)
    {
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            if (empty($d['fecha_venc'])) {
                $pdo->rollBack();
                return [
                    'error' => true,
                    'msg' => 'Debe indicar la fecha de vencimiento del presupuesto'
                ];
            }

            /* ================= OBTENER SUCURSAL DESDE DIAGNÓSTICO ================= */
            $origen = (($d['origen'] ?? 'DIAGNOSTICO') === 'PRELIMINAR') ? 'PRELIMINAR' : 'DIAGNOSTICO';
            $idDiagnostico = $origen === 'DIAGNOSTICO' ? (int)($d['id_diagnostico'] ?? 0) : null;

            if ($origen === 'DIAGNOSTICO') {
                $sqlSuc = $pdo->prepare("
                    SELECT r.id_sucursal, r.id_cliente, r.id_vehiculo
                    FROM diagnostico_servicio d
                    INNER JOIN recepcion_servicio r 
                        ON r.idrecepcion = d.idrecepcion
                    WHERE d.id_diagnostico = :id
                      AND d.estado = 1
                      AND NOT EXISTS (
                          SELECT 1
                          FROM orden_trabajo ot
                          WHERE ot.idreclamo_servicio = r.idreclamo_servicio
                            AND ot.estado != 0
                      )
                ");

                $sqlSuc->execute([
                    ':id' => $idDiagnostico
                ]);

                $diag = $sqlSuc->fetch(PDO::FETCH_ASSOC);
                $idSucursal = $diag['id_sucursal'] ?? null;
                $idCliente = $diag['id_cliente'] ?? null;
                $idVehiculo = $diag['id_vehiculo'] ?? null;

                if (!$idSucursal || !$idCliente || !$idVehiculo) {
                    $pdo->rollBack();
                    return [
                        'error' => true,
                        'msg' => 'Debe seleccionar un diagnostico valido y disponible para presupuesto'
                    ];
                }
            } else {
                $idSucursal = $_SESSION['nick_sucursal'] ?? null;
                $idCliente = (int)($d['id_cliente'] ?? 0);
                $idVehiculo = (int)($d['id_vehiculo'] ?? 0);

                $sqlPre = $pdo->prepare("
                    SELECT COUNT(*)
                    FROM vehiculos v
                    INNER JOIN clientes c ON c.id_cliente = v.id_cliente
                    WHERE v.id_vehiculo = :vehiculo
                      AND v.id_cliente = :cliente
                      AND v.estado = 1
                      AND c.estado_cliente = 1
                ");
                $sqlPre->execute([
                    ':vehiculo' => $idVehiculo,
                    ':cliente' => $idCliente
                ]);

                if (!$idSucursal || $idCliente <= 0 || $idVehiculo <= 0 || (int)$sqlPre->fetchColumn() === 0) {
                    $pdo->rollBack();
                    return [
                        'error' => true,
                        'msg' => 'Debe seleccionar un cliente y vehiculo validos para el presupuesto preliminar'
                    ];
                }
            }

            /* ================= VALIDAR SUCURSAL ================= */
            if ($idSucursal != $_SESSION['nick_sucursal']) {
                $pdo->rollBack();
                return [
                    'error' => true,
                    'msg' => 'Sucursal inválida'
                ];
            }

            $convertidoDesde = !empty($d['convertido_desde']) ? (int)$d['convertido_desde'] : null;
            if ($convertidoDesde && $origen === 'DIAGNOSTICO') {
                $sqlConvertido = $pdo->prepare("
                    SELECT idpresupuesto_servicio
                    FROM presupuesto_servicio
                    WHERE idpresupuesto_servicio = :id
                      AND origen = 'PRELIMINAR'
                      AND id_cliente = :cliente
                      AND id_vehiculo = :vehiculo
                      AND id_sucursal = :sucursal
                      AND estado IN (1,2)
                      AND fecha_venc >= CURDATE()
                    FOR UPDATE
                ");
                $sqlConvertido->execute([
                    ':id' => $convertidoDesde,
                    ':cliente' => $idCliente,
                    ':vehiculo' => $idVehiculo,
                    ':sucursal' => $idSucursal
                ]);

                if (!$sqlConvertido->fetchColumn()) {
                    $pdo->rollBack();
                    return [
                        'error' => true,
                        'msg' => 'El presupuesto preliminar seleccionado no esta disponible para conversion'
                    ];
                }
            } elseif ($origen === 'PRELIMINAR') {
                $convertidoDesde = null;
            }

            $detalleCalculado = [];
            $subtotalServicios = 0;
            $totalPromociones = 0;

            $sqlArticulo = $pdo->prepare("
                SELECT id_articulo, desc_articulo, tipo
                FROM articulos
                WHERE id_articulo = :articulo
                  AND estado = 1
                LIMIT 1
            ");

            $sqlStock = $pdo->prepare("
                SELECT stockDisponible
                FROM stock
                WHERE id_articulo = :articulo
                  AND id_sucursal = :sucursal
            ");

            $sqlPromoExiste = $pdo->prepare("
                SELECT id_promocion
                FROM promociones
                WHERE id_promocion = :promocion
                LIMIT 1
            ");

            /* ================= VALIDAR DETALLE Y CONSERVAR IMPORTES ACEPTADOS ================= */
            foreach ($d['detalle'] as $it) {
                $idArticulo = (int)($it['id_articulo'] ?? 0);
                $cantidad = (float)($it['cantidad'] ?? 0);
                $precioBase = (float)($it['precio_base'] ?? 0);

                if ($idArticulo <= 0 || $cantidad <= 0 || $precioBase < 0) {
                    $pdo->rollBack();
                    return [
                        'error' => true,
                        'msg' => 'Detalle de presupuesto invalido'
                    ];
                }

                $sqlArticulo->execute([':articulo' => $idArticulo]);
                $articulo = $sqlArticulo->fetch(PDO::FETCH_ASSOC);

                if (!$articulo) {
                    $pdo->rollBack();
                    return [
                        'error' => true,
                        'msg' => 'Articulo invalido en el detalle'
                    ];
                }

                if ($articulo['tipo'] === 'producto') {
                    $sqlStock->execute([
                        ':articulo' => $idArticulo,
                        ':sucursal' => $idSucursal
                    ]);

                    $stockActual = $sqlStock->fetchColumn();

                    if ($stockActual === false) {
                        $pdo->rollBack();
                        return [
                            'error' => true,
                            'msg' => "No existe stock para {$articulo['desc_articulo']}"
                        ];
                    }

                    if ($cantidad > $stockActual) {
                        $pdo->rollBack();
                        return [
                            'error' => true,
                            'msg' => "Stock insuficiente para {$articulo['desc_articulo']}"
                        ];
                    }
                }

                $montoPromoUnitario = 0;
                $idPromo = null;
                $promoEnviada = $it['promocion'] ?? null;

                if (is_array($promoEnviada) && !empty($promoEnviada['id_promocion'])) {
                    $idPromoEnviada = (int)$promoEnviada['id_promocion'];
                    $sqlPromoExiste->execute([':promocion' => $idPromoEnviada]);

                    if ($sqlPromoExiste->fetchColumn()) {
                        $idPromo = $idPromoEnviada;
                        $tipoPromo = $promoEnviada['tipo'] ?? '';
                        $valorPromo = (float)($promoEnviada['valor'] ?? 0);

                        if ($tipoPromo === 'PORCENTAJE') {
                            $montoPromoUnitario = $precioBase * ($valorPromo / 100);
                        } elseif ($tipoPromo === 'MONTO_FIJO') {
                            $montoPromoUnitario = min($precioBase, $valorPromo);
                        } elseif ($tipoPromo === 'PRECIO_FIJO') {
                            $montoPromoUnitario = max(0, $precioBase - $valorPromo);
                        }

                        $montoPromoUnitario = min($precioBase, max(0, $montoPromoUnitario));
                    }
                }

                $montoPromoLinea = $montoPromoUnitario * $cantidad;
                $subtotalLinea = $precioBase * $cantidad;
                $subtotalServicios += $subtotalLinea;
                $totalPromociones += $montoPromoLinea;

                $detalleCalculado[] = [
                    'id_articulo' => $idArticulo,
                    'cantidad' => $cantidad,
                    'precio_base' => $precioBase,
                    'subtotal' => $subtotalLinea,
                    'tipo_articulo' => strtolower((string)($articulo['tipo'] ?? '')),
                    'id_promocion' => $idPromo,
                    'monto_promocion_unitario' => $montoPromoUnitario,
                    'monto_promocion' => $montoPromoLinea
                ];
            }

            $descuentosAplicados = [];
            $totalDescuento = 0;
            $baseDescuentos = max(0, $subtotalServicios - $totalPromociones);
            $descuentosPorAlcance = [
                'TOTAL' => 0,
                'PRODUCTO' => 0,
                'SERVICIO' => 0
            ];

            $sqlDescExiste = $pdo->prepare("
                SELECT id_descuento, aplica_a
                FROM descuentos
                WHERE id_descuento = :descuento
                LIMIT 1
            ");

            foreach (($d['descuentos'] ?? []) as $desc) {
                $idDesc = (int)($desc['id_descuento'] ?? 0);
                if ($idDesc <= 0) {
                    continue;
                }

                $sqlDescExiste->execute([':descuento' => $idDesc]);
                $descuentoBD = $sqlDescExiste->fetch(PDO::FETCH_ASSOC);
                if (!$descuentoBD) {
                    continue;
                }

                $tipoDesc = $desc['tipo'] ?? '';
                $valorDesc = (float)($desc['valor'] ?? 0);
                if (!in_array($tipoDesc, ['PORCENTAJE', 'MONTO_FIJO'], true) || $valorDesc <= 0) {
                    continue;
                }

                $aplicaA = strtoupper((string)($descuentoBD['aplica_a'] ?? $desc['aplica_a'] ?? 'TOTAL'));
                if (!in_array($aplicaA, ['TOTAL', 'PRODUCTO', 'SERVICIO'], true)) {
                    $aplicaA = 'TOTAL';
                }

                $baseAlcance = 0;
                foreach ($detalleCalculado as $linea) {
                    $tipoLinea = $linea['tipo_articulo'] ?? '';
                    if ($aplicaA === 'PRODUCTO' && $tipoLinea !== 'producto') {
                        continue;
                    }
                    if ($aplicaA === 'SERVICIO' && $tipoLinea !== 'servicio') {
                        continue;
                    }
                    $baseAlcance += max(0, $linea['subtotal'] - $linea['monto_promocion']);
                }

                $baseDisponibleAlcance = max(0, $baseAlcance - ($descuentosPorAlcance[$aplicaA] ?? 0));
                $baseDisponibleGlobal = max(0, $baseDescuentos - $totalDescuento);
                $baseDisponible = min($baseDisponibleAlcance, $baseDisponibleGlobal);

                $monto = $tipoDesc === 'PORCENTAJE'
                        ? $baseAlcance * ($valorDesc / 100)
                        : $valorDesc;

                $monto = min($monto, $baseDisponible);
                $totalDescuento += $monto;
                $descuentosPorAlcance[$aplicaA] = ($descuentosPorAlcance[$aplicaA] ?? 0) + $monto;

                $descuentosAplicados[] = [
                    'id_descuento' => $idDesc,
                    'tipo' => $tipoDesc,
                    'valor' => $valorDesc,
                    'aplica_a' => $aplicaA,
                    'base_aplicada' => $baseAlcance,
                    'monto' => $monto,
                    'motivo' => $desc['nombre'] ?? ''
                ];
            }

            $totalFinal = max(0, $subtotalServicios - $totalPromociones - $totalDescuento);

            $totalesVista = [
                'subtotal' => (float)($d['subtotal'] ?? -1),
                'total_descuento' => (float)($d['total_descuento'] ?? -1),
                'total_final' => (float)($d['total_final'] ?? -1)
            ];

            $totalesCalculados = [
                'subtotal' => $subtotalServicios,
                'total_descuento' => $totalDescuento,
                'total_final' => $totalFinal
            ];

            foreach ($totalesCalculados as $campo => $valorCalculado) {
                if (abs(round($totalesVista[$campo]) - round($valorCalculado)) > 1) {
                    $pdo->rollBack();
                    return [
                        'error' => true,
                        'msg' => 'Los importes enviados no coinciden con el detalle del presupuesto. Verifique nuevamente antes de guardar.'
                    ];
                }
            }

            /* ================= INSERT PRESUPUESTO ================= */
            $sql = $pdo->prepare("
            INSERT INTO presupuesto_servicio
            (id_usuario, id_sucursal, id_cliente, id_vehiculo, fecha, estado, fecha_venc,
             subtotal, total_descuento, total_final, id_diagnostico, origen, convertido_desde)
            VALUES
            (:usuario, :sucursal, :cliente, :vehiculo, CURDATE(), 1, :fecha_venc,
             :subtotal, :total_desc, :total_final, :id_diagnostico, :origen, :convertido_desde)
        ");

            $sql->execute([
                ':usuario'       => $d['usuario'],
                ':sucursal'      => $idSucursal,
                ':cliente'       => $idCliente,
                ':vehiculo'      => $idVehiculo,
                ':fecha_venc'    => $d['fecha_venc'],
                ':subtotal'      => $subtotalServicios,
                ':total_desc'    => $totalDescuento,
                ':total_final'   => $totalFinal,
                ':id_diagnostico' => $idDiagnostico,
                ':origen'         => $origen,
                ':convertido_desde' => $convertidoDesde
            ]);

            $idPresupuesto = $pdo->lastInsertId();

            /* ================= DETALLE ================= */
            $sqlDet = $pdo->prepare("
            INSERT INTO presupuesto_detalleservicio
            (id_articulo, idpresupuesto_servicio, cantidad, preciouni, subtotal)
            VALUES
            (:articulo, :presupuesto, :cantidad, :precio, :subtotal)
            ");

            foreach ($detalleCalculado as $it) {
                $sqlDet->execute([
                    ':articulo'    => $it['id_articulo'],
                    ':presupuesto' => $idPresupuesto,
                    ':cantidad'    => $it['cantidad'],
                    ':precio'      => $it['precio_base'],
                    ':subtotal'    => $it['subtotal']
                ]);

                $it['id_detalle_presupuesto'] = (int)$pdo->lastInsertId();

                if (!empty($it['id_promocion']) && $it['monto_promocion'] > 0) {
                    $sqlPresPromo = $pdo->prepare("
                    INSERT INTO presupuesto_promocion
                    (idpresupuesto_servicio, id_detalle_presupuesto, id_promocion, id_articulo, cantidad, monto_unitario, monto_aplicado, fecha_aplicacion)
                    VALUES
                    (:presupuesto, :detalle, :promocion, :articulo, :cantidad, :monto_unitario, :monto, NOW())
                ");

                    $sqlPresPromo->execute([
                        ':presupuesto' => $idPresupuesto,
                        ':detalle' => $it['id_detalle_presupuesto'],
                        ':promocion' => $it['id_promocion'],
                        ':articulo' => $it['id_articulo'],
                        ':cantidad' => $it['cantidad'],
                        ':monto_unitario' => $it['monto_promocion_unitario'],
                        ':monto' => $it['monto_promocion']
                    ]);
                }
            }

            /* ================= ACTUALIZAR DIAGNÓSTICO ================= */
            if ($descuentosAplicados) {
                $usaAlcancePresupuestoDescuento = false;
                try {
                    $columnasPresupuestoDescuento = $pdo->query("SHOW COLUMNS FROM presupuesto_descuento")->fetchAll(PDO::FETCH_COLUMN);
                    $usaAlcancePresupuestoDescuento = in_array('aplica_a', $columnasPresupuestoDescuento, true)
                        && in_array('base_aplicada', $columnasPresupuestoDescuento, true);
                } catch (Exception $e) {
                    $usaAlcancePresupuestoDescuento = false;
                }

                if ($usaAlcancePresupuestoDescuento) {
                    $sqlPresDesc = $pdo->prepare("
                    INSERT INTO presupuesto_descuento
                    (id_presupuesto, id_descuento, id_usuario, tipo, valor, aplica_a, base_aplicada, monto_aplicado, motivo)
                    VALUES
                    (:presupuesto, :descuento, :usuario, :tipo, :valor, :aplica_a, :base_aplicada, :monto, :motivo)
                ");
                } else {
                    $sqlPresDesc = $pdo->prepare("
                    INSERT INTO presupuesto_descuento
                    (id_presupuesto, id_descuento, id_usuario, tipo, valor, monto_aplicado, motivo)
                    VALUES
                    (:presupuesto, :descuento, :usuario, :tipo, :valor, :monto, :motivo)
                ");
                }

                foreach ($descuentosAplicados as $desc) {
                    $paramsDesc = [
                        ':presupuesto' => $idPresupuesto,
                        ':descuento' => $desc['id_descuento'],
                        ':usuario' => $d['usuario'],
                        ':tipo' => $desc['tipo'],
                        ':valor' => $desc['valor'],
                        ':monto' => $desc['monto'],
                        ':motivo' => $desc['motivo']
                    ];
                    if ($usaAlcancePresupuestoDescuento) {
                        $paramsDesc[':aplica_a'] = $desc['aplica_a'];
                        $paramsDesc[':base_aplicada'] = $desc['base_aplicada'];
                    }
                    $sqlPresDesc->execute($paramsDesc);
                }
            }

            if ($origen === 'DIAGNOSTICO') {
                $sqlUpd = $pdo->prepare("
                UPDATE diagnostico_servicio
                SET estado = 2
                WHERE id_diagnostico = :id
                ");
                $sqlUpd->execute([
                    ':id' => $idDiagnostico
                ]);

                if ($convertidoDesde) {
                    $sqlConv = $pdo->prepare("
                    UPDATE presupuesto_servicio
                    SET estado = 5
                    WHERE idpresupuesto_servicio = :id
                    ");
                    $sqlConv->execute([
                        ':id' => $convertidoDesde
                    ]);
                }
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {

            $pdo->rollBack();

            return [
                'error' => true,
                'msg'   => $e->getMessage()
            ];
        }
    }

    protected static function listar_presupuestos_modelo($inicio, $registros, $filtrosSQL, $orderSQL = "ORDER BY ps.fecha DESC, ps.idpresupuesto_servicio DESC")
    {
        $conexion = mainModel::conectar();

        $baseSQL = "
        FROM presupuesto_servicio ps
        LEFT JOIN clientes c ON c.id_cliente = ps.id_cliente
        LEFT JOIN vehiculos v ON v.id_vehiculo = ps.id_vehiculo
        LEFT JOIN modelo_auto ma ON ma.id_modeloauto = v.id_modeloauto 
        INNER JOIN usuarios u ON u.id_usuario = ps.id_usuario
        WHERE 1=1
        $filtrosSQL
        ";

        $selectSQL = "
        SELECT 
            ps.idpresupuesto_servicio,
            ps.fecha,
            ps.estado AS estadoPre,
            ps.origen,
            ps.total_final,
            c.nombre_cliente,
            c.apellido_cliente,
            v.placa,
            ma.mod_descri AS modelo,
            u.usu_nombre,
            u.usu_apellido
        ";

        return mainModel::ejecutarPaginador(
            $conexion,
            $baseSQL,
            $selectSQL,
            $orderSQL,
            $inicio,
            $registros
        );
    }
    
    protected static function anular_presupuesto_modelo($id)
    {
        $pdo = mainModel::conectar();

        try {

            $pdo->beginTransaction();

            // 🔹 obtener datos
            $sql = $pdo->prepare("
            SELECT estado, id_sucursal, id_diagnostico, convertido_desde
            FROM presupuesto_servicio
            WHERE idpresupuesto_servicio = :id
            ");
            $sql->execute([':id' => $id]);
            $pres = $sql->fetch(PDO::FETCH_ASSOC);

            if (!$pres) {
                $pdo->rollBack();
                return ['error' => true, 'msg' => 'No existe'];
            }

            // 🔹 validar OT
            $sql = $pdo->prepare("
            SELECT COUNT(*) 
            FROM orden_trabajo
            WHERE idpresupuesto_servicio = :id and estado != 0
            ");
            $sql->execute([':id' => $id]);

            if ($sql->fetchColumn() > 0) {
                $pdo->rollBack();
                return ['error' => true, 'msg' => 'Tiene OT'];
            }

            // 🔹 anular
            $sql = $pdo->prepare("
            UPDATE presupuesto_servicio
            SET estado = 0
            WHERE idpresupuesto_servicio = :id
            ");
            $sql->execute([':id' => $id]);

            if (!empty($pres['id_diagnostico'])) {
                $sql = $pdo->prepare("
                UPDATE diagnostico_servicio
                SET estado = 1
                WHERE id_diagnostico = :id_diag
                ");
                $sql->execute([
                    ':id_diag' => $pres['id_diagnostico']
                ]);
            }

            if (!empty($pres['convertido_desde'])) {
                $sql = $pdo->prepare("
                UPDATE presupuesto_servicio
                SET estado = 1
                WHERE idpresupuesto_servicio = :id_preliminar
                  AND origen = 'PRELIMINAR'
                  AND estado = 5
                ");
                $sql->execute([
                    ':id_preliminar' => $pres['convertido_desde']
                ]);
            }

            $pdo->commit();

            return [
                'ok' => true,
                'data' => $pres
            ];
        } catch (Exception $e) {

            $pdo->rollBack();

            return [
                'error' => true,
                'msg' => $e->getMessage()
            ];
        }
    }

    protected static function aprobar_presupuesto_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
            UPDATE presupuesto_servicio
            SET estado = 2
            WHERE idpresupuesto_servicio = :id
              AND estado = 1
              AND origen <> 'PRELIMINAR'
        ");

        return $sql->execute([
            ':id' => $id
        ]);
    }

    protected static function obtener_presupuesto_cabecera($id)
    {
        $sql = self::conectar()->prepare("
            SELECT
                ps.idpresupuesto_servicio,
                ps.fecha,
                ps.fecha_venc,
                ps.estado,
                ps.origen,
                ps.subtotal,
                ps.total_descuento,
                ps.total_final,

                c.nombre_cliente,
                c.apellido_cliente,
                c.celular_cliente,
                c.direccion_cliente,

                v.placa,
                ma.mod_descri AS modelo,
                m.mar_descri AS marca,

                u.usu_nombre,
                u.usu_apellido
            FROM presupuesto_servicio ps
            INNER JOIN clientes c ON c.id_cliente = ps.id_cliente
            INNER JOIN vehiculos v ON v.id_vehiculo = ps.id_vehiculo
            INNER JOIN modelo_auto ma ON ma.id_modeloauto = v.id_modeloauto
            INNER JOIN usuarios u ON u.id_usuario = ps.id_usuario
            INNER JOIN marcas m ON m.id_marcas = ma.id_marcas
            WHERE ps.idpresupuesto_servicio = :id
            LIMIT 1
        ");
        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function obtener_presupuesto_detalle($id)
    {
        $sql = self::conectar()->prepare("
            SELECT
                d.id_detalle_presupuesto,
                a.desc_articulo,
                d.cantidad,
                d.preciouni,
                d.subtotal
            FROM presupuesto_detalleservicio d
            INNER JOIN articulos a ON a.id_articulo = d.id_articulo
            WHERE d.idpresupuesto_servicio = :id
        ");
        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function obtener_presupuesto_promociones($id)
    {
        $sql = self::conectar()->prepare("
            SELECT
                pp.id_detalle_presupuesto,
                p.nombre,
                a.desc_articulo,
                pp.cantidad,
                pp.monto_unitario,
                pp.monto_aplicado,
                pp.fecha_aplicacion
            FROM presupuesto_promocion pp
            INNER JOIN promociones p ON p.id_promocion = pp.id_promocion
            INNER JOIN articulos a ON a.id_articulo = pp.id_articulo
            WHERE pp.idpresupuesto_servicio = :id
            ORDER BY p.nombre, a.desc_articulo
        ");
        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
