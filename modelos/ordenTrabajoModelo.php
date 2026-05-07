<?php
require_once "mainModel.php";

class ordenTrabajoModelo extends mainModel
{
    protected static function obtener_tecnicos_equipo_modelo($idEquipo)
    {
        $pdo = self::conectar();

        $qTec = $pdo->prepare("
        SELECT e.idempleados, CONCAT(e.nombre,' ',e.apellido) AS nombre
        FROM equipo_empleado ee
        INNER JOIN empleados e ON e.idempleados = ee.idempleados
        WHERE ee.id_equipo = ?
          AND ee.estado = 1
          AND e.estado = 1
        ");
        $qTec->execute([$idEquipo]);

        return $qTec->fetchAll(PDO::FETCH_ASSOC);
    }


    protected static function obtener_presupuesto_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
            SELECT *
            FROM presupuesto_servicio
            WHERE idpresupuesto_servicio = :id
              AND estado = 2
            LIMIT 1
        ");
        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function obtener_detalle_presupuesto_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
            SELECT
                id_articulo,
                cantidad,
                preciouni AS precio_unitario,
                subtotal
            FROM presupuesto_detalleservicio
            WHERE idpresupuesto_servicio = :id
        ");
        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function obtener_ot_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            ot.*,

            /* ================= CLIENTE ================= */
            MAX(COALESCE(c.nombre_cliente, cR.nombre_cliente)) AS nombre_cliente,
            MAX(COALESCE(c.apellido_cliente, cR.apellido_cliente)) AS apellido_cliente,

            /* ================= VEHICULO ================= */
            MAX(COALESCE(v.placa, vR.placa)) AS placa,
            ma.mod_descri AS modelo,

            /* ================= KM ================= */
            MAX(COALESCE(r.kilometraje, rR.kilometraje)) AS kilometraje,

            /* ================= RECLAMO ================= */
            rs.tipo_reclamo,
            rs.prioridad,
            rs.fecha_reclamo,
            rs.descripcion,

            /* ================= EQUIPO ================= */
            GROUP_CONCAT(CONCAT(e.nombre,' ',e.apellido) SEPARATOR ', ') AS miembros

        FROM orden_trabajo ot

        /* ===== FLUJO NORMAL ===== */
        LEFT JOIN presupuesto_servicio ps 
            ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio

        LEFT JOIN diagnostico_servicio ds 
            ON ds.id_diagnostico = ps.id_diagnostico

        LEFT JOIN recepcion_servicio r 
            ON r.idrecepcion = ds.idrecepcion

        LEFT JOIN clientes c 
            ON c.id_cliente = r.id_cliente

        LEFT JOIN vehiculos v 
            ON v.id_vehiculo = r.id_vehiculo

        LEFT JOIN modelo_auto ma 
            ON ma.id_modeloauto = v.id_modeloauto

        /* ===== FLUJO RECLAMO (CORRECTO) ===== */
        LEFT JOIN reclamo_servicio rs 
            ON rs.idreclamo_servicio = ot.idreclamo_servicio

        LEFT JOIN recepcion_servicio rR 
            ON rR.idreclamo_servicio = rs.idreclamo_servicio

        LEFT JOIN clientes cR 
            ON cR.id_cliente = rR.id_cliente

        LEFT JOIN vehiculos vR 
            ON vR.id_vehiculo = rR.id_vehiculo

        /* ===== EQUIPO ===== */
        LEFT JOIN equipo_trabajo et 
            ON et.id_equipo = ot.idtrabajos

        LEFT JOIN equipo_empleado ee 
            ON ee.id_equipo = et.id_equipo

        LEFT JOIN empleados e 
            ON e.idempleados = ee.idempleados

        WHERE ot.idorden_trabajo = :id
        GROUP BY ot.idorden_trabajo
        LIMIT 1
        ");

        $sql->bindParam(":id", $id);
        $sql->execute();

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function obtener_detalle_ot_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            d.cantidad,
            d.precio_unitario,
            d.subtotal,
            a.desc_articulo
        FROM orden_trabajo_detalle d
        INNER JOIN articulos a ON a.id_articulo = d.id_articulo
        WHERE d.idorden_trabajo = :id");
        $sql->bindParam(":id", $id);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function listar_equipos_modelo($idSucursal)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            et.id_equipo,
            et.nombre
        FROM equipo_trabajo et
        WHERE et.estado = 1
          AND et.id_sucursal = :sucursal
        ORDER BY et.nombre
     ");

        $sql->bindParam(':sucursal', $idSucursal, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function asignar_equipo_modelo($ot, $equipo, $tecnico)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE orden_trabajo
        SET idtrabajos = :equipo,
            tecnico_responsable = :tecnico,
            estado = 2
        WHERE idorden_trabajo = :ot
        ");

        return $sql->execute([
            ':equipo'  => $equipo,
            ':tecnico' => $tecnico,
            ':ot'      => $ot
        ]);
    }

    protected static function obtener_ot_completa($idOT)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            ot.idorden_trabajo,
            ot.fecha_inicio,
            ot.fecha_fin,
            ot.estado,
            ot.origen,
            ot.observacion,

            COALESCE(ps.subtotal, tot.total_detalle, 0) AS subtotal,
            COALESCE(ps.total_descuento, 0) AS total_descuento,
            COALESCE(ps.total_final, tot.total_detalle, 0) AS total_final,

            COALESCE(r_normal.kilometraje, r_reclamo.kilometraje) AS kilometraje,

            c.nombre_cliente,
            c.apellido_cliente,
            c.celular_cliente,
            c.direccion_cliente,

            v.placa,
            ma.mod_descri AS modelo,

            rs.tipo_reclamo,
            rs.prioridad,
            rs.fecha_reclamo,
            rs.descripcion AS descripcion_reclamo,

            et.nombre AS nombre_equipo,

            (
                SELECT GROUP_CONCAT(CONCAT(e.nombre,' ',e.apellido) SEPARATOR ', ')
                FROM equipo_empleado ee
                INNER JOIN empleados e ON e.idempleados = ee.idempleados
                WHERE ee.id_equipo = et.id_equipo
                  AND ee.estado = 1
                  AND e.estado = 1
            ) AS miembros_equipo

        FROM orden_trabajo ot

        LEFT JOIN presupuesto_servicio ps 
            ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio

        LEFT JOIN diagnostico_servicio ds 
            ON ds.id_diagnostico = ps.id_diagnostico

        LEFT JOIN recepcion_servicio r_normal
            ON r_normal.idrecepcion = ds.idrecepcion

        LEFT JOIN reclamo_servicio rs
            ON rs.idreclamo_servicio = ot.idreclamo_servicio

        LEFT JOIN recepcion_servicio r_reclamo
            ON r_reclamo.idreclamo_servicio = rs.idreclamo_servicio

        LEFT JOIN clientes c 
            ON c.id_cliente = COALESCE(r_normal.id_cliente, r_reclamo.id_cliente)

        LEFT JOIN vehiculos v 
            ON v.id_vehiculo = COALESCE(r_normal.id_vehiculo, r_reclamo.id_vehiculo)

        LEFT JOIN modelo_auto ma 
            ON ma.id_modeloauto = v.id_modeloauto

        LEFT JOIN (
            SELECT idorden_trabajo, SUM(subtotal) AS total_detalle
            FROM orden_trabajo_detalle
            GROUP BY idorden_trabajo
        ) tot
            ON tot.idorden_trabajo = ot.idorden_trabajo

        LEFT JOIN equipo_trabajo et
            ON et.id_equipo = ot.idtrabajos

        WHERE ot.idorden_trabajo = :id
        LIMIT 1
        ");

        $sql->bindParam(":id", $idOT, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function obtener_detalle_ot($idOT)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT 
            a.desc_articulo,
            d.cantidad,
            d.precio_unitario,
            d.subtotal,
            p.nombre AS promocion
        FROM orden_trabajo_detalle d
        INNER JOIN articulos a ON a.id_articulo = d.id_articulo
        LEFT JOIN presupuesto_promocion pp 
            ON pp.idpresupuesto_servicio = d.idorden_trabajo
        LEFT JOIN promociones p 
            ON p.id_promocion = pp.id_promocion
        WHERE d.idorden_trabajo = :id");
        $sql->bindParam(":id", $idOT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function crear_ot_modelo2($datos)
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            /* VALIDAR OT EXISTENTE */
            $val = $pdo->prepare("
                SELECT idorden_trabajo
                FROM orden_trabajo
                WHERE idpresupuesto_servicio = ?
                  AND estado != 0
            ");
            $val->execute([$datos['idpresupuesto']]);

            if ($val->rowCount() > 0) {
                $pdo->rollBack();
                return ['msg' => 'Este presupuesto ya tiene una OT'];
            }

            $qSuc = $pdo->prepare("
            SELECT ps.estado, r.id_sucursal
            FROM presupuesto_servicio ps
            INNER JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
            INNER JOIN recepcion_servicio r ON r.idrecepcion = ds.idrecepcion
            WHERE ps.idpresupuesto_servicio = ?
            FOR UPDATE
        ");
            $qSuc->execute([$datos['idpresupuesto']]);
            $presupuesto = $qSuc->fetch(PDO::FETCH_ASSOC);
            $idSucursal = $presupuesto['id_sucursal'] ?? null;

            if (!$idSucursal) {
                $pdo->rollBack();
                return ['msg' => 'No se pudo obtener sucursal'];
            }

            if ((int)$presupuesto['estado'] !== 2) {
                $pdo->rollBack();
                return ['msg' => 'El presupuesto no esta aprobado'];
            }

            if ((int)$idSucursal !== (int)$datos['idsucursal']) {
                $pdo->rollBack();
                return ['msg' => 'No puede generar OT de otra sucursal'];
            }

            /* CABECERA OT */
            $cab = $pdo->prepare("
                    INSERT INTO orden_trabajo
                    (idpresupuesto_servicio, id_usuario, id_sucursal, idtrabajos, tecnico_responsable, observacion, estado)
                    VALUES (?, ?, ?, ?, ?, ?, 1)
                ");
            $cab->execute([
                $datos['idpresupuesto'],
                $datos['idusuario'],
                $idSucursal,
                $datos['idtrabajos'],
                $datos['tecnico_responsable'],
                $datos['observacion']
            ]);

            $idOT = $pdo->lastInsertId();

            /* DETALLE OT */
            $det = $pdo->prepare("
                INSERT INTO orden_trabajo_detalle
                (idorden_trabajo, id_articulo, cantidad, precio_unitario, subtotal)
                SELECT ?, id_articulo, cantidad, preciouni, subtotal
                FROM presupuesto_detalleservicio
                WHERE idpresupuesto_servicio = ?
            ");
            $det->execute([$idOT, $datos['idpresupuesto']]);

            /* ACTUALIZAR ESTADO PRESUPUESTO */
            $upd = $pdo->prepare("
                UPDATE presupuesto_servicio
                SET estado = 3
                WHERE idpresupuesto_servicio = ?
            ");
            $upd->execute([$datos['idpresupuesto']]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['msg' => $e->getMessage()];
        }
    }

    protected static function listar_ot_modelo($inicio, $registros, $filtrosSQL)
    {
        $conexion = mainModel::conectar();

        $baseSQL = "
        FROM orden_trabajo ot

        /* ================= NORMAL ================= */
        LEFT JOIN presupuesto_servicio ps 
            ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio

        LEFT JOIN diagnostico_servicio ds 
            ON ds.id_diagnostico = ps.id_diagnostico

        LEFT JOIN recepcion_servicio r_normal 
            ON r_normal.idrecepcion = ds.idrecepcion

        /* ================= RECLAMO ================= */
        LEFT JOIN recepcion_servicio r_reclamo 
            ON r_reclamo.idreclamo_servicio = ot.idreclamo_servicio

        /* ================= DATOS ================= */
        LEFT JOIN clientes c 
            ON c.id_cliente = COALESCE(r_normal.id_cliente, r_reclamo.id_cliente)

        LEFT JOIN vehiculos v 
            ON v.id_vehiculo = COALESCE(r_normal.id_vehiculo, r_reclamo.id_vehiculo)

        LEFT JOIN modelo_auto ma 
            ON ma.id_modeloauto = v.id_modeloauto

        INNER JOIN usuarios u 
            ON u.id_usuario = ot.id_usuario
        WHERE 1=1
        $filtrosSQL
        ";

        $selectSQL = "
        SELECT 
            ot.idorden_trabajo,
            ot.fecha_inicio,
            ot.estado,
            ot.origen,
            ps.idpresupuesto_servicio,
            u.usu_nombre,
            u.usu_apellido,
            COALESCE(c.nombre_cliente, '') AS nombre_cliente,
            COALESCE(c.apellido_cliente, '') AS apellido_cliente,
            COALESCE(v.placa, '') AS placa,
            COALESCE(ma.mod_descri, '') AS modelo
        ";

        $orderSQL = "ORDER BY ot.idorden_trabajo DESC";

        return mainModel::ejecutarPaginador(
            $conexion,
            $baseSQL,
            $selectSQL,
            $orderSQL,
            $inicio,
            $registros
        );
    }

    protected static function anular_ot_modelo($idOT, $usuario)
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            $q = $pdo->prepare("
                SELECT estado, idpresupuesto_servicio, idreclamo_servicio
                FROM orden_trabajo
                WHERE idorden_trabajo = ?
                FOR UPDATE
            ");
            $q->execute([$idOT]);
            $ot = $q->fetch(PDO::FETCH_ASSOC);

            if (!$ot) {
                $pdo->rollBack();
                return ['msg' => 'OT no existe'];
            }

            if (!in_array((int)$ot['estado'], [1, 3], true)) {
                $pdo->rollBack();
                return ['msg' => 'No se puede anular esta OT'];
            }

            $qReg = $pdo->prepare("
                SELECT COUNT(*)
                FROM registro_servicio
                WHERE idorden_trabajo = ?
                  AND estado != 0
            ");
            $qReg->execute([$idOT]);

            if ($qReg->fetchColumn() > 0) {
                $pdo->rollBack();
                return ['msg' => 'No se puede anular una OT con servicio registrado'];
            }

            // Anular OT
            $upd = $pdo->prepare("
                UPDATE orden_trabajo
                SET estado = 0,
                    updated_at = NOW(),
                    updated_by = ?
                WHERE idorden_trabajo = ?
            ");
            $upd->execute([$usuario, $idOT]);

            if (!empty($ot['idpresupuesto_servicio'])) {
                // Volver presupuesto a aprobado
                $updPres = $pdo->prepare("
                    UPDATE presupuesto_servicio
                    SET estado = 2
                    WHERE idpresupuesto_servicio = ?
                ");
                $updPres->execute([$ot['idpresupuesto_servicio']]);
            }

            if (!empty($ot['idreclamo_servicio'])) {
                $updReclamo = $pdo->prepare("
                    UPDATE reclamo_servicio
                    SET estado = 1
                    WHERE idreclamo_servicio = ?
                ");
                $updReclamo->execute([$ot['idreclamo_servicio']]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['msg' => $e->getMessage()];
        }
    }

    protected static function crear_ot_reclamo_modelo($idReclamo, $usuario, $sucursal)
    {
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            /* 🔒 VALIDAR QUE NO EXISTA OT */
            $check = $pdo->prepare("
            SELECT idorden_trabajo 
            FROM orden_trabajo
            WHERE idreclamo_servicio = ?
            AND estado != 0
        ");
            $check->execute([$idReclamo]);

            if ($check->rowCount() > 0) {
                return false; // ya existe
            }

            /* 🔥 CREAR OT */
            $sql = $pdo->prepare("
            INSERT INTO orden_trabajo
            (
                idtrabajos,
                tecnico_responsable,
                idpresupuesto_servicio,
                id_usuario,
                id_sucursal,
                fecha_inicio,
                estado,
                origen,
                idreclamo_servicio
            )
            VALUES (?, ?, NULL, ?, ?, NOW(), 3, 'RECLAMO', ?)
        ");

            $sql->execute([
                null,
                null,
                $usuario,
                $sucursal,
                $idReclamo
            ]);

            /* 🔥 ACTUALIZAR RECLAMO */
            $pdo->prepare("
            UPDATE reclamo_servicio
            SET estado = 2
            WHERE idreclamo_servicio = ?
        ")->execute([$idReclamo]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return $e->getMessage();
        }
    }

    protected static function completar_ot_modelo($d)
    {
        $pdo = mainModel::conectar();

        try {

            $pdo->beginTransaction();

            $qOT = $pdo->prepare("
                SELECT estado, id_sucursal, origen
                FROM orden_trabajo
                WHERE idorden_trabajo = ?
                FOR UPDATE
            ");
            $qOT->execute([$d['idorden_trabajo']]);
            $ot = $qOT->fetch(PDO::FETCH_ASSOC);

            if (!$ot) {
                $pdo->rollBack();
                return 'OT no existe';
            }

            if ((int)$ot['id_sucursal'] !== (int)$_SESSION['nick_sucursal']) {
                $pdo->rollBack();
                return 'No puede completar una OT de otra sucursal';
            }

            if (($ot['origen'] ?? '') !== 'RECLAMO') {
                $pdo->rollBack();
                return 'Solo las OT por reclamo requieren completar';
            }

            if ((int)$ot['estado'] !== 3) {
                $pdo->rollBack();
                return 'Solo se puede completar una OT pendiente por reclamo';
            }

            /* BORRAR DETALLE */
            $pdo->prepare("
            DELETE FROM orden_trabajo_detalle 
            WHERE idorden_trabajo = ?
        ")->execute([$d['idorden_trabajo']]);

            /* ================= REPUESTOS ================= */
            if (!empty($d['repuestos'])) {

                $sql = $pdo->prepare("
                INSERT INTO orden_trabajo_detalle
                (idorden_trabajo, id_articulo, cantidad, precio_unitario, subtotal)
                VALUES (?, ?, ?, 0, 0)
            ");

                $qStock = $pdo->prepare("
                SELECT stockDisponible 
                FROM stock 
                WHERE id_articulo = ? AND id_sucursal = ?
            ");

                foreach ($d['repuestos'] as $r) {

                    $qStock->execute([$r['id_articulo'], $_SESSION['nick_sucursal']]);
                    $stock = $qStock->fetchColumn();

                    if ($stock < $r['cantidad']) {
                        throw new Exception("Stock insuficiente para artículo ID " . $r['id_articulo']);
                    }

                    $sql->execute([
                        $d['idorden_trabajo'],
                        $r['id_articulo'],
                        $r['cantidad']
                    ]);
                }
            }

            /* ================= TRABAJOS ================= */
            if (!empty($d['trabajos'])) {

                $sql = $pdo->prepare("
                INSERT INTO orden_trabajo_detalle
                (idorden_trabajo, id_articulo, cantidad, precio_unitario, subtotal)
                VALUES (?, ?, 1, 0, 0)
            ");

                foreach ($d['trabajos'] as $t) {
                    $sql->execute([
                        $d['idorden_trabajo'],
                        $t['id_articulo']
                    ]);
                }
            }

            /* ================= CABECERA ================= */
            $sql = $pdo->prepare("
            UPDATE orden_trabajo
            SET 
                tecnico_responsable = :tecnico,
                idtrabajos = :equipo,
                observacion = :obs,
                estado = 1
            WHERE idorden_trabajo = :id
        ");

            $sql->execute([
                ":tecnico" => $d['tecnico'],
                ":equipo" => $d['equipo'],
                ":obs" => $d['obs'],
                ":id" => $d['idorden_trabajo']
            ]);

            $pdo->commit();

            return true;
        } catch (Exception $e) {

            $pdo->rollBack();
            return $e->getMessage();
        }
    }
}
