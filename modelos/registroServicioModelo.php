<?php
require_once "mainModel.php";

class registroServicioModelo extends mainModel
{
    /* ================= Registrar servicio ================= */
    protected static function registrar_servicio_modelo($datos)
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            /* ================= VALIDAR OT ================= */
            $q = $pdo->prepare("
                SELECT estado, idtrabajos, tecnico_responsable
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

            if ($ot['estado'] == 3 || $ot['estado'] == 4) {
                return ['msg' => 'La orden de trabajo ya fue finalizada'];
            }

            /* ================= VALIDAR REGISTRO PREVIO ================= */
            $v = $pdo->prepare("
                SELECT idregistro_servicio
                FROM registro_servicio
                WHERE idorden_trabajo = ? and estado = 1
            ");
            $v->execute([$datos['idorden_trabajo']]);

            if ($v->rowCount() > 0) {
                return ['msg' => 'El servicio ya fue registrado'];
            }

            /* ================= CREAR CABECERA ================= */
            $ins = $pdo->prepare("
                INSERT INTO registro_servicio
                (idorden_trabajo, fecha_ejecucion, tecnico_responsable,
                 usuario_registra, observacion, ip_registro, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $ins->execute([
                $datos['idorden_trabajo'],
                $datos['fecha_ejecucion'],
                $ot['tecnico_responsable'],
                $datos['usuario'],
                $datos['observacion'],
                $datos['ip'],
                $datos['user_agent']
            ]);

            $idRegistro = $pdo->lastInsertId();

            /* ================= COPIAR DETALLE OT ================= */
            $det = $pdo->prepare("
                INSERT INTO registro_servicio_detalle
                (idregistro_servicio, id_articulo, cantidad, precio_unitario, subtotal)
                SELECT ?, id_articulo, cantidad, precio_unitario, subtotal
                FROM orden_trabajo_detalle
                WHERE idorden_trabajo = ?
            ");
            $det->execute([
                $idRegistro,
                $datos['idorden_trabajo']
            ]);
            /* ================= OBTENER SUCURSAL DESDE OT ================= */
            $qSuc = $pdo->prepare("
                SELECT r.id_sucursal
                FROM orden_trabajo ot
                INNER JOIN recepcion_servicio r ON r.idrecepcion = ot.idrecepcion
                WHERE ot.idorden_trabajo = ?
            ");
            $qSuc->execute([$datos['idorden_trabajo']]);
            $idSucursalReal = $qSuc->fetchColumn();

            if (!$idSucursalReal) {
                throw new Exception('No se pudo determinar la sucursal del servicio');
            }
            if ($idSucursalReal != $_SESSION['nick_sucursal']) {
                throw new Exception('No puede registrar servicios de otra sucursal');
            }

            self::aplicar_stock_registro_servicio(
                $pdo,
                $idRegistro,
                $idSucursalReal,
                $datos['usuario']
            );
            /* ================= CERRAR OT ================= */
            $upd = $pdo->prepare("
                UPDATE orden_trabajo
                SET estado = 3,
                    updated=NOW(),
                    updatedby=?,
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
                    SELECT idrecepcion
                    FROM orden_trabajo
                    WHERE idorden_trabajo = ?
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

    /* ================= BUSCAR OT PARA REGISTRO ================= */
    protected static function buscar_ot_para_registro_modelo($texto)
    {
        session_start(['name' => 'STR']);
        $sql = self::conectar()->prepare("
        SELECT ot.idorden_trabajo,
               c.nombre_cliente,
               c.apellido_cliente,
               m.mod_descri,
               v.placa
        FROM orden_trabajo ot
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ot.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        LEFT JOIN registro_servicio rs ON rs.idorden_trabajo = ot.idorden_trabajo
        WHERE ot.estado = 2 AND r.id_sucursal = :sucursal 
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

    /* ================= OBTENER OT + DETALLE ================= */
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
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ot.idrecepcion
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
          AND a.tipo = 'producto'");
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
            AND a.tipo = 'producto'");
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


    protected static function paginador_registro_servicio_modelo($inicio, $registros, $busqueda1, $busqueda2)
    {
        return "
        SELECT SQL_CALC_FOUND_ROWS
               rs.idregistro_servicio,
               rs.estado,
               rs.fecha_ejecucion,
               ot.idorden_trabajo,
               c.nombre_cliente,
               c.apellido_cliente,
               m.mod_descri,
               v.placa,
               CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_registra
        FROM registro_servicio rs
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rs.idorden_trabajo
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ot.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        INNER JOIN usuarios u ON u.id_usuario = rs.usuario_registra
        WHERE r.id_sucursal = '{$_SESSION['nick_sucursal']}'
        ORDER BY rs.idregistro_servicio DESC
        LIMIT $inicio, $registros";
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
                INNER JOIN recepcion_servicio r ON r.idrecepcion = ot.idrecepcion
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
            SET estado = 2,
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
