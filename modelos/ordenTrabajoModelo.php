<?php
require_once "mainModel.php";

class ordenTrabajoModelo extends mainModel
{
    /* ================= OBTENER PRESUPUESTO ================= */
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

    /* ================= DETALLE PRESUPUESTO ================= */
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

    /* ================= CREAR OT ================= */
    protected static function crear_ot_modelo($d)
    {
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            /* CABECERA OT */
            $sqlOT = $pdo->prepare("
                INSERT INTO orden_trabajo
                (idpresupuesto_servicio, idrecepcion, id_usuario, observacion)
                VALUES
                (:presupuesto, :recepcion, :usuario, :obs)
            ");

            $sqlOT->execute([
                ':presupuesto' => $d['idpresupuesto'],
                ':recepcion'   => $d['idrecepcion'],
                ':usuario'     => $d['usuario'],
                ':obs'         => $d['observacion']
            ]);

            $idOT = $pdo->lastInsertId();

            /* DETALLE OT */
            $sqlDet = $pdo->prepare("
                INSERT INTO orden_trabajo_detalle
                (idorden_trabajo, id_articulo, cantidad, precio_unitario, subtotal)
                VALUES
                (:ot, :articulo, :cantidad, :precio, :subtotal)
            ");

            foreach ($d['detalle'] as $it) {
                $sqlDet->execute([
                    ':ot'        => $idOT,
                    ':articulo'  => $it['id_articulo'],
                    ':cantidad'  => $it['cantidad'],
                    ':precio'    => $it['precio_unitario'],
                    ':subtotal'  => $it['subtotal']
                ]);
            }

            /* ACTUALIZAR PRESUPUESTO → OT GENERADA */
            $pdo->prepare("
                UPDATE presupuesto_servicio
                SET estado = 3
                WHERE idpresupuesto_servicio = :id
            ")->execute([
                ':id' => $d['idpresupuesto']
            ]);

            /* ACTUALIZAR RECEPCIÓN → EN PROCESO */
            $pdo->prepare("
                UPDATE recepcion_servicio
                SET estado = 2
                WHERE idrecepcion = :id
            ")->execute([
                ':id' => $d['idrecepcion']
            ]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['msg' => $e->getMessage()];
        }
    }

    protected static function paginador_ot_modelo($inicio, $registros, $busqueda1, $busqueda2)
    {
        if (!empty($busqueda1) && !empty($busqueda2)) {
            $consulta = "
        SELECT SQL_CALC_FOUND_ROWS
            ot.idorden_trabajo,
            ot.fecha_inicio,
            ot.estado,
            ps.idpresupuesto_servicio,
            c.nombre_cliente,
            c.apellido_cliente,
            v.placa,
            ma.mod_descri AS modelo,
            u.usu_nombre,
            u.usu_apellido
        FROM orden_trabajo ot
        INNER JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ot.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto ma ON ma.id_modeloauto = v.id_modeloauto
        INNER JOIN usuarios u ON u.id_usuario = ot.id_usuario
        WHERE DATE(ot.fecha_inicio) BETWEEN '$busqueda1' AND '$busqueda2'
        ORDER BY ot.idorden_trabajo DESC
        LIMIT $inicio,$registros";
        } else {
            $consulta = "
        SELECT SQL_CALC_FOUND_ROWS
            ot.idorden_trabajo,
            ot.fecha_inicio,
            ot.estado,
            ps.idpresupuesto_servicio,
            c.nombre_cliente,
            c.apellido_cliente,
            v.placa,
            ma.mod_descri AS modelo,
            u.usu_nombre,
            u.usu_apellido
        FROM orden_trabajo ot
        INNER JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ot.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto ma ON ma.id_modeloauto = v.id_modeloauto
        INNER JOIN usuarios u ON u.id_usuario = ot.id_usuario
        ORDER BY ot.idorden_trabajo DESC
        LIMIT $inicio,$registros";
        }

        return $consulta;
    }

    protected static function obtener_ot_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            ot.*,
            ps.idpresupuesto_servicio,
            c.nombre_cliente,
            c.apellido_cliente,
            v.placa,
            ma.mod_descri AS modelo,
            e.nombre AS tecnico_nombre,
            e.apellido AS tecnico_apellido
        FROM orden_trabajo ot
        INNER JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ot.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto ma ON ma.id_modeloauto = v.id_modeloauto
        LEFT JOIN equipo_trabajo et ON et.idtrabajos = ot.idtrabajos
        LEFT JOIN empleados e ON e.idempleados = et.idempleados
        WHERE ot.idorden_trabajo = :id
        LIMIT 1");
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

    protected static function listar_tecnicos_modelo()
    {
        $sql = mainModel::conectar()->query("
        SELECT
            et.idtrabajos,
            CONCAT(e.nombre, ' ', e.apellido) AS tecnico
        FROM equipo_trabajo et
        INNER JOIN empleados e ON e.idempleados = et.idempleados
        WHERE et.estado = 1 ");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function asignar_tecnico_modelo($ot, $tecnico)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE orden_trabajo
        SET idtrabajos = :t,
            estado = 2
        WHERE idorden_trabajo = :ot");
        return $sql->execute([
            ':t' => $tecnico,
            ':ot' => $ot
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
            ot.observacion,

            ps.subtotal,
            ps.total_descuento,
            ps.total_final,

            r.kilometraje,

            c.nombre_cliente,
            c.apellido_cliente,
            c.celular_cliente,
            c.direccion_cliente,

            v.placa,
            ma.mod_descri AS modelo,

            e.nombre AS tecnico_nombre,
            e.apellido AS tecnico_apellido

        FROM orden_trabajo ot

        INNER JOIN presupuesto_servicio ps 
            ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio

        INNER JOIN recepcion_servicio r 
            ON r.idrecepcion = ot.idrecepcion

        INNER JOIN clientes c 
            ON c.id_cliente = r.id_cliente

        INNER JOIN vehiculos v 
            ON v.id_vehiculo = r.id_vehiculo

        INNER JOIN modelo_auto ma 
            ON ma.id_modeloauto = v.id_modeloauto

        LEFT JOIN equipo_trabajo et 
            ON et.idtrabajos = ot.idtrabajos

        LEFT JOIN empleados e 
            ON e.idempleados = et.idempleados

        WHERE ot.idorden_trabajo = :id
        LIMIT 1");

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
            ");
            $val->execute([$datos['idpresupuesto']]);

            if ($val->rowCount() > 0) {
                return ['msg' => 'Este presupuesto ya tiene una OT'];
            }

            /* OBTENER RECEPCION DESDE PRESUPUESTO (SEGURIDAD) */
            $qRec = $pdo->prepare("
                SELECT idrecepcion
                FROM presupuesto_servicio
                WHERE idpresupuesto_servicio = ?
            ");
            $qRec->execute([$datos['idpresupuesto']]);
            $rec = $qRec->fetch(PDO::FETCH_ASSOC);

            if (!$rec) {
                return ['msg' => 'Presupuesto no válido'];
            }

            /* CABECERA OT */
            $cab = $pdo->prepare("
                INSERT INTO orden_trabajo
                (idpresupuesto_servicio, idrecepcion, id_usuario, idtrabajos, observacion, estado)
                VALUES (?, ?, ?, ?, ?, 2)
            ");
            $cab->execute([
                $datos['idpresupuesto'],
                $rec['idrecepcion'],
                $datos['idusuario'],
                $datos['idtrabajos'],
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

    protected static function anular_ot_modelo($idOT, $usuario)
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            $q = $pdo->prepare("
                SELECT estado, idpresupuesto_servicio
                FROM orden_trabajo
                WHERE idorden_trabajo = ?
            ");
            $q->execute([$idOT]);
            $ot = $q->fetch(PDO::FETCH_ASSOC);

            if (!$ot) {
                return ['msg' => 'OT no existe'];
            }

            if (!in_array($ot['estado'], [1, 2])) {
                return ['msg' => 'No se puede anular esta OT'];
            }

            // Anular OT
            $upd = $pdo->prepare("
                UPDATE orden_trabajo
                SET estado = 0,
                    updated = NOW(),
                    updatedby = ?
                WHERE idorden_trabajo = ?
            ");
            $upd->execute([$usuario, $idOT]);

            // Volver presupuesto a aprobado
            $updPres = $pdo->prepare("
                UPDATE presupuesto_servicio
                SET estado = 2
                WHERE idpresupuesto_servicio = ?
            ");
            $updPres->execute([$ot['idpresupuesto_servicio']]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['msg' => $e->getMessage()];
        }
    }
}
