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
            MAX(c.nombre_cliente) AS nombre_cliente,
            MAX(c.apellido_cliente) AS apellido_cliente,

            /* ================= VEHICULO ================= */
            MAX(v.placa) AS placa,
            MAX(ma.mod_descri) AS modelo,

            /* ================= KM ================= */
            MAX(COALESCE(r.kilometraje, rR.kilometraje)) AS kilometraje,

            /* ================= RECLAMO ================= */
            MAX(rs.tipo_reclamo) AS tipo_reclamo,
            MAX(rs.prioridad) AS prioridad,
            MAX(rs.fecha_reclamo) AS fecha_reclamo,
            MAX(rs.descripcion) AS descripcion,

            MAX(dR.id_diagnostico) AS id_diagnostico_reclamo,
            MAX(dR.fecha_diagnostico) AS fecha_diagnostico,
            MAX(dR.observaciones) AS diagnostico_observaciones,
            MAX(dR.descripcion_cliente) AS diagnostico_descripcion_cliente,
            MAX(dR.diagnostico_general) AS diagnostico_general,
            MAX(dR.es_garantia) AS es_garantia,
            MAX(dR.es_reclamo_valido) AS es_reclamo_valido,
            MAX(dR.requiere_cobro) AS requiere_cobro,

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
            ON c.id_cliente = ot.id_cliente

        LEFT JOIN vehiculos v 
            ON v.id_vehiculo = ot.id_vehiculo

        LEFT JOIN modelo_auto ma 
            ON ma.id_modeloauto = v.id_modeloauto

        /* ===== FLUJO RECLAMO (CORRECTO) ===== */
        LEFT JOIN reclamo_servicio rs 
            ON rs.idreclamo_servicio = ot.idreclamo_servicio

        LEFT JOIN recepcion_servicio rR 
            ON rR.idreclamo_servicio = rs.idreclamo_servicio

        LEFT JOIN diagnostico_servicio dR
            ON dR.idrecepcion = rR.idrecepcion
            AND dR.estado != 0
            AND dR.id_diagnostico = (
                SELECT MAX(d2.id_diagnostico)
                FROM diagnostico_servicio d2
                WHERE d2.idrecepcion = rR.idrecepcion
                  AND d2.estado != 0
            )

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

    protected static function obtener_detalle_diagnostico_modelo($idDiagnostico)
    {
        $sql = mainModel::conectar()->prepare("
            SELECT
                sistema,
                problema,
                gravedad,
                solucion_propuesta,
                requiere_repuesto,
                requiere_mano_obra
            FROM diagnostico_detalle
            WHERE id_diagnostico = :id
            ORDER BY id_diagnostico_detalle ASC
        ");
        $sql->bindParam(":id", $idDiagnostico, PDO::PARAM_INT);
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
            ON c.id_cliente = ot.id_cliente

        LEFT JOIN vehiculos v 
            ON v.id_vehiculo = ot.id_vehiculo

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
            SELECT ps.estado, ps.id_sucursal, ps.id_cliente, ps.id_vehiculo, ps.id_diagnostico, ps.origen
            FROM presupuesto_servicio ps
            WHERE ps.idpresupuesto_servicio = ?
            FOR UPDATE
        ");
            $qSuc->execute([$datos['idpresupuesto']]);
            $presupuesto = $qSuc->fetch(PDO::FETCH_ASSOC);
            $idSucursal = $presupuesto['id_sucursal'] ?? null;
            $idCliente = $presupuesto['id_cliente'] ?? null;
            $idVehiculo = $presupuesto['id_vehiculo'] ?? null;

            if (!$idSucursal || !$idCliente || !$idVehiculo) {
                $pdo->rollBack();
                return ['msg' => 'No se pudo obtener sucursal, cliente o vehiculo'];
            }

            if ((int)$presupuesto['estado'] !== 2) {
                $pdo->rollBack();
                return ['msg' => 'El presupuesto no esta aprobado'];
            }

            if (($presupuesto['origen'] ?? 'DIAGNOSTICO') !== 'DIAGNOSTICO' || empty($presupuesto['id_diagnostico'])) {
                $pdo->rollBack();
                return ['msg' => 'Un presupuesto preliminar debe convertirse a presupuesto con diagnostico antes de generar OT'];
            }

            if ((int)$idSucursal !== (int)$datos['idsucursal']) {
                $pdo->rollBack();
                return ['msg' => 'No puede generar OT de otra sucursal'];
            }

            /* CABECERA OT */
            $cab = $pdo->prepare("
                    INSERT INTO orden_trabajo
                    (idpresupuesto_servicio, id_usuario, id_cliente, id_vehiculo, id_sucursal, idtrabajos, tecnico_responsable, observacion, estado)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
                ");
            $cab->execute([
                $datos['idpresupuesto'],
                $datos['idusuario'],
                $idCliente,
                $idVehiculo,
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

    protected static function listar_ot_modelo($inicio, $registros, $filtrosSQL, $orderSQL = "ORDER BY ot.fecha_inicio DESC, ot.idorden_trabajo DESC")
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
            ON c.id_cliente = ot.id_cliente

        LEFT JOIN vehiculos v 
            ON v.id_vehiculo = ot.id_vehiculo

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

                $updDiagnostico = $pdo->prepare("
                    UPDATE diagnostico_servicio d
                    INNER JOIN recepcion_servicio r ON r.idrecepcion = d.idrecepcion
                    SET d.estado = 1
                    WHERE r.idreclamo_servicio = ?
                      AND d.estado != 0
                ");
                $updDiagnostico->execute([$ot['idreclamo_servicio']]);
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
            $qReclamo = $pdo->prepare("
                SELECT
                    rs.estado,
                    rs.id_sucursal,
                    COALESCE(rs.id_cliente, r.id_cliente) AS id_cliente,
                    COALESCE(rs.id_vehiculo, r.id_vehiculo) AS id_vehiculo
                FROM reclamo_servicio rs
                LEFT JOIN recepcion_servicio r
                    ON r.idreclamo_servicio = rs.idreclamo_servicio
                WHERE rs.idreclamo_servicio = ?
                FOR UPDATE
            ");
            $qReclamo->execute([$idReclamo]);
            $reclamo = $qReclamo->fetch(PDO::FETCH_ASSOC);

            if (!$reclamo) {
                $pdo->rollBack();
                return 'Reclamo no existe';
            }

            if ((int)$reclamo['id_sucursal'] !== (int)$sucursal) {
                $pdo->rollBack();
                return 'No puede generar OT de un reclamo de otra sucursal';
            }

            if ((int)$reclamo['estado'] !== 2) {
                $pdo->rollBack();
                return 'El reclamo no esta en proceso';
            }

            if (empty($reclamo['id_cliente']) || empty($reclamo['id_vehiculo'])) {
                $pdo->rollBack();
                return 'No se pudo obtener cliente o vehiculo del reclamo';
            }

            $qDiagnostico = $pdo->prepare("
                SELECT d.id_diagnostico
                FROM diagnostico_servicio d
                INNER JOIN recepcion_servicio r ON r.idrecepcion = d.idrecepcion
                WHERE r.idreclamo_servicio = ?
                  AND d.estado != 0
                  AND d.es_reclamo_valido = 1
                  AND d.es_garantia = 1
                  AND d.requiere_cobro = 0
                ORDER BY d.id_diagnostico DESC
                LIMIT 1
            ");
            $qDiagnostico->execute([$idReclamo]);

            if (!$qDiagnostico->fetchColumn()) {
                $pdo->rollBack();
                return 'El reclamo no habilita OT directa. Debe ser valido, en garantia y sin cobro';
            }

            $check = $pdo->prepare("
            SELECT idorden_trabajo 
            FROM orden_trabajo
            WHERE idreclamo_servicio = ?
            AND estado != 0
        ");
            $check->execute([$idReclamo]);

            if ($check->rowCount() > 0) {
                $pdo->rollBack();
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
                id_cliente,
                id_vehiculo,
                id_sucursal,
                fecha_inicio,
                estado,
                origen,
                idreclamo_servicio
            )
            VALUES (?, ?, NULL, ?, ?, ?, ?, NOW(), 3, 'RECLAMO', ?)
        ");

            $sql->execute([
                null,
                null,
                $usuario,
                $reclamo['id_cliente'],
                $reclamo['id_vehiculo'],
                $sucursal,
                $idReclamo
            ]);

            /* 🔥 ACTUALIZAR RECLAMO */
            $pdo->prepare("
            UPDATE reclamo_servicio
            SET estado = 2
            WHERE idreclamo_servicio = ?
        ")->execute([$idReclamo]);

            $pdo->prepare("
            UPDATE diagnostico_servicio d
            INNER JOIN recepcion_servicio r ON r.idrecepcion = d.idrecepcion
            SET d.estado = 3
            WHERE r.idreclamo_servicio = ?
              AND d.estado != 0
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
