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
            SELECT estado, id_sucursal
            FROM orden_trabajo
            WHERE idorden_trabajo = ?
        ");
            $q->execute([$datos['idorden_trabajo']]);
            $ot = $q->fetch(PDO::FETCH_ASSOC);

            if (!$ot) {
                return ['msg' => 'Orden de trabajo no existe'];
            }

            if ($ot['estado'] == 0) {
                return ['msg' => 'La orden de trabajo está anulada'];
            }

            if (in_array($ot['estado'], [3, 4])) {
                return ['msg' => 'La orden de trabajo ya fue finalizada'];
            }

            /* ================= VALIDAR SUCURSAL ================= */
            if ($ot['id_sucursal'] != $_SESSION['nick_sucursal']) {
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
                return ['msg' => 'El servicio ya fue registrado'];
            }

            /* ================= CREAR CABECERA ================= */
            $ins = $pdo->prepare("
            INSERT INTO registro_servicio
            (idorden_trabajo, id_sucursal, usuario_registra, fecha_ejecucion, estado, observacion)
            VALUES (?, ?, ?, NOW(), 1, ?)
        ");
            $ins->execute([
                $datos['idorden_trabajo'],
                $ot['id_sucursal'], // 🔥 ya viene de OT
                $datos['usuario'],
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
            $det->execute([
                $idRegistro,
                $datos['idorden_trabajo']
            ]);
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

            /* ================= CERRAR RECEPCIÓN ================= */
            $updRec = $pdo->prepare("
            UPDATE recepcion_servicio
            SET estado = 3,
                fecha_salida = NOW(),
                fecha_actualizacion = NOW()
            WHERE idrecepcion = (
                SELECT ds.idrecepcion
                FROM orden_trabajo ot
                INNER JOIN presupuesto_servicio ps 
                    ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
                INNER JOIN diagnostico_servicio ds 
                    ON ds.id_diagnostico = ps.id_diagnostico
                WHERE ot.idorden_trabajo = ?
            )
        ");
            $updRec->execute([
                $datos['idorden_trabajo']
            ]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['msg' => $e->getMessage()];
        }
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
        session_start(['name' => 'STR']);
        $sql = self::conectar()->prepare("
                SELECT 
            ot.idorden_trabajo,
            c.nombre_cliente,
            c.apellido_cliente,
            m.mod_descri,
            v.placa
        FROM orden_trabajo ot
        INNER JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        INNER JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ds.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto

        LEFT JOIN registro_servicio rs 
            ON rs.idorden_trabajo = ot.idorden_trabajo
            AND rs.estado = 1   

        WHERE ot.estado = 1 
        AND r.id_sucursal = :sucursal 
        AND rs.idorden_trabajo IS NULL 
        AND (
                c.nombre_cliente LIKE :b
            OR c.apellido_cliente LIKE :b
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
        INNER JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        INNER JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ds.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto m ON v.id_modeloauto = v.id_modeloauto
        WHERE ot.idorden_trabajo = ?
        LIMIT 1");
        $sql->execute([$idOT]);
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
            INSERT INTO sucmovimientostock
            (id_sucursal, TipoMovStockId, MovStockProductoId, MovStockCantidad,
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
        ");
            $upd->execute([
                $item['cantidad'],
                $usuario,
                $idMovimiento,
                $idSucursal,
                $item['id_articulo']
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
            INSERT INTO sucmovimientostock
            (id_sucursal, TipoMovStockId, MovStockProductoId, MovStockCantidad,
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


    protected static function listar_registro_servicio_modelo($inicio, $registros, $filtrosSQL)
    {
        $pdo = self::conectar();

        $sql = "
        SELECT 
            rs.*,
            ot.idorden_trabajo,
            c.nombre_cliente,
            c.apellido_cliente,
            m.mod_descri,
            v.placa
        FROM registro_servicio rs
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rs.idorden_trabajo
        INNER JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        INNER JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ds.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        WHERE 1=1 $filtrosSQL
        ORDER BY rs.idregistro_servicio DESC
        LIMIT $inicio, $registros
        ";

        $datos = $pdo->query($sql)->fetchAll();

        $total = $pdo->query("
                SELECT COUNT(*)
        FROM registro_servicio rs
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rs.idorden_trabajo
        INNER JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        INNER JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ds.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
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
                   rs.idorden_trabajo
            FROM registro_servicio rs
            WHERE rs.idregistro_servicio = ?
            FOR UPDATE
        ");
            $q->execute([$datos['idregistro_servicio']]);
            $reg = $q->fetch(PDO::FETCH_ASSOC);

            if (!$reg) {
                return ['msg' => 'Registro de servicio no existe'];
            }

            if ($reg['estado'] != 1) {
                return ['msg' => 'El registro no puede ser anulado'];
            }
            $qSuc = $pdo->prepare("
                SELECT r.id_sucursal
                FROM registro_servicio rs
                INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rs.idorden_trabajo
                INNER JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
                INNER JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
                INNER JOIN recepcion_servicio r ON r.idrecepcion = ds.idrecepcion
                WHERE rs.idregistro_servicio = ?
            ");
            $qSuc->execute([$datos['idregistro_servicio']]);
            $idSucursalReal = $qSuc->fetchColumn();

            if ($idSucursalReal != $_SESSION['nick_sucursal']) {
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
        ");
            $updOT->execute([$reg['idorden_trabajo']]);

            /* ================= REABRIR RECEPCIÓN ================= */
            $updRec = $pdo->prepare("
                UPDATE recepcion_servicio
                SET estado = 2,
                    fecha_salida = NULL,
                    fecha_actualizacion = NOW()
                WHERE idrecepcion = (
                    SELECT idrecepcion
                    FROM orden_trabajo
                    WHERE idorden_trabajo = ?
                )
            ");
            $updRec->execute([$reg['idorden_trabajo']]);


            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['msg' => $e->getMessage()];
        }
    }
}
