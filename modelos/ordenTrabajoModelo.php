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
            ps.idpresupuesto_servicio,
            c.nombre_cliente,
            c.apellido_cliente,
            v.placa,
            ma.mod_descri AS modelo,
            GROUP_CONCAT(CONCAT(e.nombre,' ',e.apellido) SEPARATOR ', ') AS miembros
        FROM orden_trabajo ot
        INNER JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        INNER JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ds.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto ma ON ma.id_modeloauto = v.id_modeloauto
        LEFT JOIN equipo_trabajo et ON et.id_equipo = ot.idtrabajos
        LEFT JOIN equipo_empleado ee ON ee.id_equipo = et.id_equipo
        LEFT JOIN empleados e ON e.idempleados = ee.idempleados
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

            et.nombre AS nombre_equipo,

            GROUP_CONCAT(
                CONCAT(e.nombre,' ',e.apellido)
                SEPARATOR ', '
            ) AS miembros_equipo

        FROM orden_trabajo ot

        INNER JOIN presupuesto_servicio ps 
            ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio

        INNER JOIN diagnostico_servicio ds 
            ON ds.id_diagnostico = ps.id_diagnostico

        INNER JOIN recepcion_servicio r 
            ON r.idrecepcion = ds.idrecepcion

        INNER JOIN clientes c 
            ON c.id_cliente = r.id_cliente

        INNER JOIN vehiculos v 
            ON v.id_vehiculo = r.id_vehiculo

        INNER JOIN modelo_auto ma 
            ON ma.id_modeloauto = v.id_modeloauto

        LEFT JOIN equipo_trabajo et
            ON et.id_equipo = ot.idtrabajos

        LEFT JOIN equipo_empleado ee
            ON ee.id_equipo = et.id_equipo
            AND ee.estado = 1

        LEFT JOIN empleados e
            ON e.idempleados = ee.idempleados

        WHERE ot.idorden_trabajo = :id
        GROUP BY ot.idorden_trabajo
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
            ");
            $val->execute([$datos['idpresupuesto']]);

            if ($val->rowCount() > 0) {
                return ['msg' => 'Este presupuesto ya tiene una OT'];
            }

            $qSuc = $pdo->prepare("
            SELECT r.id_sucursal
            FROM presupuesto_servicio ps
            INNER JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
            INNER JOIN recepcion_servicio r ON r.idrecepcion = ds.idrecepcion
            WHERE ps.idpresupuesto_servicio = ?
        ");
            $qSuc->execute([$datos['idpresupuesto']]);
            $idSucursal = $qSuc->fetchColumn();

            if (!$idSucursal) {
                return ['msg' => 'No se pudo obtener sucursal'];
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
        INNER JOIN presupuesto_servicio ps 
            ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        INNER JOIN diagnostico_servicio ds 
            ON ds.id_diagnostico = ps.id_diagnostico
        INNER JOIN recepcion_servicio r 
            ON r.idrecepcion = ds.idrecepcion
        INNER JOIN clientes c 
            ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v 
            ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto ma 
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
            ps.idpresupuesto_servicio,
            c.nombre_cliente,
            c.apellido_cliente,
            v.placa,
            ma.mod_descri AS modelo,
            u.usu_nombre,
            u.usu_apellido
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
                    updated_at = NOW(),
                    updated_by = ?
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


    protected static function crear_ot_desde_diagnostico_modelo($datos)
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            /* ================= VALIDAR QUE EXISTA DIAGNOSTICO ================= */
            $check = $pdo->prepare("
            SELECT id_diagnostico, idrecepcion
            FROM diagnostico_servicio
            WHERE id_diagnostico = ?
        ");
            $check->execute([$datos['id_diagnostico']]);

            if ($check->rowCount() == 0) {
                return ["error" => true, "msg" => "Diagnóstico no existe"];
            }

            $diag = $check->fetch();

            /* ================= INSERT OT ================= */
            $sql = $pdo->prepare("
            INSERT INTO orden_trabajo
            (
                id_diagnostico,
                idpresupuesto_servicio,
                idrecepcion,
                id_usuario,
                fecha_inicio,
                estado,
                id_sucursal,
                origen,
                idreclamo_servicio
            )
            VALUES
            (?, NULL, ?, ?, NOW(), 1, ?, 'RECLAMO', ?)
        ");

            $sql->execute([
                $datos['id_diagnostico'],
                $diag['idrecepcion'],
                $datos['usuario'],
                $datos['id_sucursal'],
                $datos['idreclamo_servicio']
            ]);

            $idOT = $pdo->lastInsertId();

            /* ================= (OPCIONAL) DETALLE DESDE DIAGNOSTICO ================= */
            if (!empty($datos['detalle'])) {

                $ins = $pdo->prepare("
                INSERT INTO orden_trabajo_detalle
                (idorden_trabajo, id_articulo, cantidad, precio, subtotal)
                VALUES (?, ?, ?, ?, ?)
            ");

                foreach ($datos['detalle'] as $item) {

                    $subtotal = $item['cantidad'] * $item['precio'];

                    $ins->execute([
                        $idOT,
                        $item['id_articulo'],
                        $item['cantidad'],
                        $item['precio'],
                        $subtotal
                    ]);
                }
            }

            /* ================= CAMBIAR ESTADO DIAGNOSTICO ================= */
            $upd = $pdo->prepare("
            UPDATE diagnostico_servicio
            SET estado = 3
            WHERE id_diagnostico = ?
        ");
            $upd->execute([$datos['id_diagnostico']]);

            $pdo->commit();

            return [
                "success" => true,
                "idorden" => $idOT
            ];
        } catch (Exception $e) {

            $pdo->rollBack();

            return [
                "error" => true,
                "msg" => $e->getMessage()
            ];
        }
    }
}
