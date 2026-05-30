<?php
require_once "mainModel.php";

class registroServicioModelo extends mainModel
{
    protected static function registrar_servicio_modelo($datos)
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            /* ================= VALIDAR OT ================= */
            $q = $pdo->prepare("
            SELECT estado, id_sucursal, id_cliente, id_vehiculo
            FROM orden_trabajo
            WHERE idorden_trabajo = ?
            FOR UPDATE
        ");
            $q->execute([$datos['idorden_trabajo']]);
            $ot = $q->fetch(PDO::FETCH_ASSOC);

            if (!$ot) {
                $pdo->rollBack();
                return ['msg' => 'Orden de trabajo no existe'];
            }

            if (empty($ot['id_cliente']) || empty($ot['id_vehiculo'])) {
                $pdo->rollBack();
                return ['msg' => 'La orden de trabajo no tiene cliente o vehiculo asociado'];
            }

            $qVehiculoCliente = $pdo->prepare("
                SELECT id_vehiculo
                FROM vehiculos
                WHERE id_vehiculo = ?
                  AND id_cliente = ?
                LIMIT 1
            ");
            $qVehiculoCliente->execute([$ot['id_vehiculo'], $ot['id_cliente']]);

            if (!$qVehiculoCliente->fetchColumn()) {
                $pdo->rollBack();
                return ['msg' => 'El vehiculo de la orden no pertenece al cliente'];
            }

            if ((int)$ot['estado'] !== 1) {
                $pdo->rollBack();
                return ['msg' => 'La orden de trabajo no esta activa'];
            }

            if ($ot['estado'] == 0) {
                return ['msg' => 'La orden de trabajo está anulada'];
            }

            if (in_array($ot['estado'], [3, 4])) {
                return ['msg' => 'La orden de trabajo ya fue finalizada'];
            }

            /* ================= VALIDAR SUCURSAL ================= */
            if ($ot['id_sucursal'] != $_SESSION['nick_sucursal']) {
                $pdo->rollBack();
                return ['msg' => 'No puede registrar servicios de otra sucursal'];
            }

            /* ================= VALIDAR REGISTRO PREVIO ================= */
            $v = $pdo->prepare("
            SELECT idregistro_servicio
            FROM registro_servicio
            WHERE idorden_trabajo = ? AND estado = 1
        ");
            $v->execute([$datos['idorden_trabajo']]);

            if ($v->rowCount() > 0) {
                $pdo->rollBack();
                return ['msg' => 'El servicio ya fue registrado'];
            }

            $qRec = $pdo->prepare("
            SELECT COALESCE(
                (SELECT ds.idrecepcion
                 FROM presupuesto_servicio ps
                 INNER JOIN diagnostico_servicio ds 
                    ON ds.id_diagnostico = ps.id_diagnostico
                 WHERE ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
                 LIMIT 1),
                (SELECT r.idrecepcion
                 FROM recepcion_servicio r
                 WHERE r.idreclamo_servicio = ot.idreclamo_servicio
                 LIMIT 1)
            )
            FROM orden_trabajo ot
            WHERE ot.idorden_trabajo = ?
        ");
            $qRec->execute([$datos['idorden_trabajo']]);
            $idRecepcion = $qRec->fetchColumn();

            /* ================= CREAR CABECERA ================= */
            $ins = $pdo->prepare("
            INSERT INTO registro_servicio
            (idorden_trabajo, id_vehiculo, id_cliente, id_sucursal, usuario_registra, fecha_servicio, kilometraje_salida, estado, observacion)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)
        ");
            $ins->execute([
                $datos['idorden_trabajo'],
                $ot['id_vehiculo'],
                $ot['id_cliente'],
                $ot['id_sucursal'],
                $datos['usuario'],
                $datos['fecha_servicio'],
                $datos['kilometraje_salida'],
                $datos['observacion']
            ]);

            $idRegistro = $pdo->lastInsertId();

            /* ================= COPIAR DETALLE OT ================= */
            $det = $pdo->prepare("
            INSERT INTO registro_servicio_detalle
            (idregistro_servicio, id_articulo, cantidad, precio_unitario, subtotal, origen)
            SELECT ?, id_articulo, cantidad, precio_unitario, subtotal, 'OT'
            FROM orden_trabajo_detalle
            WHERE idorden_trabajo = ?
        ");
            $det->execute([$idRegistro, $datos['idorden_trabajo']]);

            /* ================= INSERTAR INSUMOS ================= */
            $insumos = json_decode($datos['insumos_json'] ?? '[]', true);

            if (!empty($insumos)) {
                $ins = $pdo->prepare("
                INSERT INTO registro_servicio_detalle
                (idregistro_servicio, id_articulo, cantidad, precio_unitario, subtotal, origen)
                VALUES (?, ?, ?, 0, 0, 'INSUMO')
            ");

                foreach ($insumos as $i) {
                    $ins->execute([
                        $idRegistro,
                        $i['id_articulo'],
                        $i['cantidad']
                    ]);
                }
            }

            /* ================= APLICAR STOCK ================= */
            self::aplicar_stock_registro_servicio(
                $pdo,
                $idRegistro,
                $ot['id_sucursal'],
                $datos['usuario']
            );

            /* ================= CERRAR OT ================= */
            $upd = $pdo->prepare("
            UPDATE orden_trabajo
            SET estado = 2,
                updated_at = NOW(),
                updated_by = ?,
                fecha_fin = NOW()
            WHERE idorden_trabajo = ?
        ");
            $upd->execute([
                $datos['updatedby'],
                $datos['idorden_trabajo']
            ]);

            /* ================= OBTENER RECEPCIÓN ================= */
            /* ================= CERRAR RECEPCIÓN ================= */
            if ($idRecepcion) {
                $updRec = $pdo->prepare("
                UPDATE recepcion_servicio
                SET estado = 3,
                    fecha_salida = NOW(),
                    fecha_actualizacion = NOW()
                WHERE idrecepcion = ?
            ");
                $updRec->execute([$idRecepcion]);

                $updReclamo = $pdo->prepare("
                    UPDATE reclamo_servicio rc
                    INNER JOIN recepcion_servicio r
                        ON r.idreclamo_servicio = rc.idreclamo_servicio
                    SET rc.estado = 3,
                        rc.fecha_cierre = NOW(),
                        rc.observacion_cierre = 'Servicio registrado'
                    WHERE r.idrecepcion = ?
                      AND rc.estado != 0
                ");
                $updReclamo->execute([$idRecepcion]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['msg' => $e->getMessage()];
        }
    }


    protected static function estado_ot_modelo($idOT)
    {
        $pdo = self::conectar();

        $q = $pdo->prepare("
        SELECT estado 
        FROM orden_trabajo 
        WHERE idorden_trabajo = ?
    ");
        $q->execute([$idOT]);

        return $q->fetchColumn();
    }
    protected static function buscar_insumo_modelo($texto)
    {
        $sql = self::conectar()->prepare("
        SELECT a.id_articulo, a.desc_articulo, s.stockDisponible
        FROM articulos a
        INNER JOIN stock s ON s.id_articulo = a.id_articulo
        WHERE a.tipo = 'insumo'
        AND a.estado = 1
        AND a.desc_articulo LIKE :b
        LIMIT 20
        ");

        $sql->bindValue(':b', "%$texto%");
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function buscar_ot_para_registro_modelo($texto)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $sql = self::conectar()->prepare("
                SELECT 
            ot.idorden_trabajo,
            c.nombre_cliente,
            c.apellido_cliente,
            c.doc_number,
            m.mod_descri,
            v.placa
        FROM orden_trabajo ot
        LEFT JOIN clientes c 
            ON c.id_cliente = ot.id_cliente

        LEFT JOIN vehiculos v 
            ON v.id_vehiculo = ot.id_vehiculo

        LEFT JOIN modelo_auto m 
            ON m.id_modeloauto = v.id_modeloauto

        LEFT JOIN registro_servicio rs 
            ON rs.idorden_trabajo = ot.idorden_trabajo
            AND rs.estado = 1
        WHERE ot.estado = 1 
        AND ot.id_sucursal = :sucursal
        AND rs.idorden_trabajo IS NULL 
        AND EXISTS (
            SELECT 1
            FROM orden_trabajo_detalle otd
            WHERE otd.idorden_trabajo = ot.idorden_trabajo
        )
        AND (
                c.nombre_cliente LIKE :b
            OR c.apellido_cliente LIKE :b
            OR c.doc_number LIKE :b
            OR v.placa LIKE :b
            OR ot.idorden_trabajo LIKE :b
        )
        ORDER BY ot.idorden_trabajo DESC
        LIMIT 20");

        $sql->bindValue(':b', "%$texto%");
        $sql->bindValue(':sucursal', $_SESSION['nick_sucursal'], PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function obtener_ot_para_registro_modelo($idOT)
    {
        $sql = self::conectar()->prepare("
        SELECT ot.idorden_trabajo,
               ot.idpresupuesto_servicio,
               c.nombre_cliente,
               c.apellido_cliente,
               m.mod_descri,
               v.placa
        FROM orden_trabajo ot
        LEFT JOIN clientes c ON c.id_cliente = ot.id_cliente
        LEFT JOIN vehiculos v ON v.id_vehiculo = ot.id_vehiculo
        LEFT JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        WHERE ot.idorden_trabajo = ?
          AND ot.id_sucursal = ?
          AND ot.estado = 1
          AND EXISTS (
              SELECT 1
              FROM orden_trabajo_detalle d
              WHERE d.idorden_trabajo = ot.idorden_trabajo
          )
        LIMIT 1");
        $sql->execute([$idOT, $_SESSION['nick_sucursal']]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function detalle_ot_para_registro_modelo($idOT)
    {
        $sql = self::conectar()->prepare("
        SELECT a.desc_articulo,
               d.cantidad,
               d.precio_unitario,
               d.subtotal
        FROM orden_trabajo_detalle d
        INNER JOIN articulos a ON a.id_articulo = d.id_articulo
        WHERE d.idorden_trabajo = ?");
        $sql->execute([$idOT]);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function aplicar_stock_registro_servicio(PDO $pdo, $idRegistro, $idSucursal, $usuario)
    {
        $sql = $pdo->prepare("
        SELECT d.id_articulo,
               d.cantidad,
               d.precio_unitario
        FROM registro_servicio_detalle d
        INNER JOIN articulos a ON a.id_articulo = d.id_articulo
        WHERE d.idregistro_servicio = ?
          AND a.tipo IN ('producto','insumo') ");
        $sql->execute([$idRegistro]);

        $items = $sql->fetchAll(PDO::FETCH_ASSOC);

        if (!$items) {
            return; // no hay productos → no hay stock
        }

        foreach ($items as $item) {

            /* ===== 1. INSERTAR MOVIMIENTO STOCK ===== */
            $mov = $pdo->prepare("
            INSERT INTO movimientostock
            (id_sucursal, TipoMovStockId, MovStockArticuloId, MovStockCantidad,
             MovStockPrecioVenta, MovStockCosto,
             MovStockFechaHora, MovStockUsuario,
             MovStockSigno, MovStockReferencia)
            VALUES (?, 'REG. SERVICIO', ?, ?, ?, ?, NOW(), ?, -1, ?)
        ");
            $mov->execute([
                $idSucursal,
                $item['id_articulo'],
                $item['cantidad'],
                $item['precio_unitario'],
                0,
                $usuario,
                'REG_SERV #' . $idRegistro
            ]);

            $idMovimiento = $pdo->lastInsertId();
            if (!$idMovimiento) {
                throw new Exception('No se pudo generar el movimiento de stock');
            }

            /* ===== 2. ACTUALIZAR STOCK ===== */
            $upd = $pdo->prepare("
            UPDATE stock
            SET stockDisponible = stockDisponible - ?,
                stockUltActualizacion = NOW(),
                stockUsuActualizacion = ?,
                stockUltimoIdActualizacion = ?
            WHERE id_sucursal = ?
              AND id_articulo = ?
              AND stockDisponible >= ?
        ");
            $upd->execute([
                $item['cantidad'],
                $usuario,
                $idMovimiento,
                $idSucursal,
                $item['id_articulo'],
                $item['cantidad']
            ]);

            if ($upd->rowCount() === 0) {
                throw new Exception(
                    'No existe stock para el artículo ID ' . $item['id_articulo']
                );
            }
        }
    }

    protected static function revertir_stock_registro_servicio(PDO $pdo, $idRegistro, $idSucursal, $usuario)
    {
        $sql = $pdo->prepare("
            SELECT d.id_articulo,
                d.cantidad,
                d.precio_unitario
            FROM registro_servicio_detalle d
            INNER JOIN articulos a ON a.id_articulo = d.id_articulo
            WHERE d.idregistro_servicio = ?
            AND a.tipo IN ('producto','insumo') ");
        $sql->execute([$idRegistro]);

        foreach ($sql->fetchAll(PDO::FETCH_ASSOC) as $item) {

            /* ===== DEVOLVER STOCK ===== */
            $upd = $pdo->prepare("
            UPDATE stock
            SET stockDisponible = stockDisponible + ?,
                stockUltActualizacion = NOW(),
                stockUsuActualizacion = ?
            WHERE id_sucursal = ?
              AND id_articulo = ?
        ");
            $upd->execute([
                $item['cantidad'],
                $usuario,
                $idSucursal,
                $item['id_articulo']
            ]);

            if ($upd->rowCount() === 0) {
                throw new Exception(
                    'No existe stock para el artículo ID ' . $item['id_articulo']
                );
            }

            /* ===== MOVIMIENTO INVERSO ===== */
            $mov = $pdo->prepare("
            INSERT INTO movimientostock
            (id_sucursal, TipoMovStockId, MovStockArticuloId, MovStockCantidad,
             MovStockPrecioVenta, MovStockCosto,
             MovStockFechaHora, MovStockUsuario,
             MovStockSigno, MovStockReferencia)
            VALUES (?, 'ANULACION REG. SERVICIO', ?, ?, ?, ?, NOW(), ?, 1, ?)
        ");
            $mov->execute([
                $idSucursal,
                $item['id_articulo'],
                $item['cantidad'],
                $item['precio_unitario'],
                0,
                $usuario,
                'ANUL_REG_SERV #' . $idRegistro
            ]);
        }
    }


    protected static function listar_registro_servicio_modelo($inicio, $registros, $filtrosSQL, $orderSQL = "ORDER BY rs.fecha_servicio DESC, rs.idregistro_servicio DESC")
    {
        $pdo = self::conectar();

        $sql = "
        SELECT 
            rs.idregistro_servicio,
            rs.fecha_servicio,
            rs.estado,
            rs.usuario_registra,
            rs.idorden_trabajo,

            COALESCE(c.nombre_cliente, '') AS nombre_cliente,
            COALESCE(c.apellido_cliente, '') AS apellido_cliente,
            COALESCE(m.mod_descri, '') AS mod_descri,
            COALESCE(v.placa, '') AS placa,
            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS nombre_usuario


        FROM registro_servicio rs

        /* DATOS */
        LEFT JOIN clientes c 
            ON c.id_cliente = rs.id_cliente

        LEFT JOIN vehiculos v 
            ON v.id_vehiculo = rs.id_vehiculo

        LEFT JOIN modelo_auto m 
            ON m.id_modeloauto = v.id_modeloauto

        LEFT JOIN usuarios u 
            ON u.id_usuario = rs.usuario_registra

        WHERE 1=1 $filtrosSQL

        $orderSQL
        LIMIT $inicio, $registros
        ";

        $datos = $pdo->query($sql)->fetchAll();

        $total = $pdo->query("
        SELECT COUNT(*)
        FROM registro_servicio rs
        WHERE 1=1 $filtrosSQL
        ")->fetchColumn();

        return [
            "datos" => $datos,
            "total" => $total
        ];
    }

    protected static function anular_registro_servicio_modelo($datos)
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            /* ================= VALIDAR REGISTRO ================= */
            $q = $pdo->prepare("
            SELECT rs.estado,
                   rs.idorden_trabajo,
                   rs.id_sucursal AS sucursal_registro
            FROM registro_servicio rs
            WHERE rs.idregistro_servicio = ?
            FOR UPDATE
        ");
            $q->execute([$datos['idregistro_servicio']]);
            $reg = $q->fetch(PDO::FETCH_ASSOC);

            if (!$reg) {
                $pdo->rollBack();
                return ['msg' => 'Registro de servicio no existe'];
            }

            if ($reg['estado'] != 1) {
                $pdo->rollBack();
                return ['msg' => 'El registro no puede ser anulado'];
            }

            if ((int)$reg['sucursal_registro'] !== (int)$datos['id_sucursal']) {
                throw new Exception('No puede anular registros de otra sucursal');
            }


            /* ================= REVERTIR STOCK ================= */
            self::revertir_stock_registro_servicio(
                $pdo,
                $datos['idregistro_servicio'],
                $datos['id_sucursal'],
                $datos['usuario']
            );

            /* ================= ANULAR REGISTRO ================= */
            $upd = $pdo->prepare("
            UPDATE registro_servicio
            SET estado = 0
            WHERE idregistro_servicio = ?
        ");
            $upd->execute([$datos['idregistro_servicio']]);

            /* ================= REABRIR OT ================= */
            $updOT = $pdo->prepare("
            UPDATE orden_trabajo
            SET estado = 1,
                fecha_fin = NULL
            WHERE idorden_trabajo = ?
              AND id_sucursal = ?
        ");
            $updOT->execute([$reg['idorden_trabajo'], $datos['id_sucursal']]);

            /* ================= REABRIR RECEPCIÓN ================= */
            $qOrigen = $pdo->prepare("
                SELECT
                    ot.idreclamo_servicio,
                    COALESCE(
                        (
                            SELECT ds.idrecepcion
                            FROM presupuesto_servicio ps
                            INNER JOIN diagnostico_servicio ds
                                ON ds.id_diagnostico = ps.id_diagnostico
                            WHERE ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
                            LIMIT 1
                        ),
                        (
                            SELECT r.idrecepcion
                            FROM recepcion_servicio r
                            WHERE r.idreclamo_servicio = ot.idreclamo_servicio
                            LIMIT 1
                        )
                    ) AS idrecepcion
                FROM orden_trabajo ot
                WHERE ot.idorden_trabajo = ?
                LIMIT 1
            ");
            $qOrigen->execute([$reg['idorden_trabajo']]);
            $origen = $qOrigen->fetch(PDO::FETCH_ASSOC);

            if (!empty($origen['idrecepcion'])) {
                $updRec = $pdo->prepare("
                    UPDATE recepcion_servicio
                    SET estado = 2,
                        fecha_salida = NULL,
                        fecha_actualizacion = NOW()
                    WHERE idrecepcion = ?
                ");
                $updRec->execute([$origen['idrecepcion']]);
            }

            if (!empty($origen['idreclamo_servicio'])) {
                $updReclamo = $pdo->prepare("
                    UPDATE reclamo_servicio
                    SET estado = 2,
                        fecha_cierre = NULL,
                        observacion_cierre = NULL
                    WHERE idreclamo_servicio = ?
                      AND estado = 3
                ");
                $updReclamo->execute([$origen['idreclamo_servicio']]);
            }


            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['msg' => $e->getMessage()];
        }
    }
}
