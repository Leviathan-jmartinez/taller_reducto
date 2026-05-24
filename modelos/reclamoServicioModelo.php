<?php
require_once "mainModel.php";

class reclamoServicioModelo extends mainModel
{
    /* ================= REGISTRAR RECLAMO ================= */
    protected static function registrar_reclamo_modelo($datos)
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            /* VALIDAR DUPLICADO */
            $v = $pdo->prepare("
            SELECT idreclamo_servicio
            FROM reclamo_servicio
            WHERE idregistro_servicio = ?
            AND estado IN (1, 2)
        ");
            $v->execute([$datos['idregistro_servicio']]);

            if ($v->rowCount() > 0) {
                $pdo->rollBack();
                return ['msg' => 'Ya existe reclamo activo'];
            }

            $qOrigen = $pdo->prepare("
                SELECT
                    COALESCE(r_normal.id_cliente, r_reclamo.id_cliente) AS id_cliente,
                    COALESCE(r_normal.id_vehiculo, r_reclamo.id_vehiculo) AS id_vehiculo
                FROM registro_servicio rgs
                INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rgs.idorden_trabajo
                LEFT JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
                LEFT JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
                LEFT JOIN recepcion_servicio r_normal ON r_normal.idrecepcion = ds.idrecepcion
                LEFT JOIN recepcion_servicio r_reclamo ON r_reclamo.idreclamo_servicio = ot.idreclamo_servicio
                WHERE rgs.idregistro_servicio = ?
                LIMIT 1
            ");
            $qOrigen->execute([$datos['idregistro_servicio']]);
            $origenServicio = $qOrigen->fetch(PDO::FETCH_ASSOC);

            if (
                empty($origenServicio['id_cliente']) ||
                empty($origenServicio['id_vehiculo'])
            ) {
                $pdo->rollBack();
                return ['msg' => 'No se pudo identificar cliente y vehiculo del servicio reclamado'];
            }

            /* INSERT RECLAMO */
            $ins = $pdo->prepare("
            INSERT INTO reclamo_servicio
            (
                idregistro_servicio,
                id_sucursal,
                id_cliente,
                id_vehiculo,
                fecha_reclamo,
                descripcion,
                tipo_reclamo,
                origen,
                prioridad,
                requiere_garantia,
                estado,
                usuario_registra
            )
            VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, 1, ?)
        ");

            $ins->execute([
                $datos['idregistro_servicio'],
                $datos['id_sucursal'],
                $origenServicio['id_cliente'],
                $origenServicio['id_vehiculo'],
                $datos['descripcion'],
                $datos['tipo_reclamo'],
                $datos['origen'],
                $datos['prioridad'],
                $datos['requiere_garantia'],
                $datos['usuario']
            ]);
            /* 🔥 MARCAR REGISTRO COMO CON RECLAMO */
            $upd = $pdo->prepare("
                UPDATE registro_servicio
                SET estado = 3
                WHERE idregistro_servicio = ?
            ");

            $upd->execute([
                $datos['idregistro_servicio']
            ]);
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['msg' => $e->getMessage()];
        }
    }

    /* ================= BUSCAR REGISTRO ================= */
    protected static function buscar_registro_modelo($texto)
    {
        session_start(['name' => 'STR']);
        $sql = self::conectar()->prepare("
        SELECT 
            rs.idregistro_servicio,
            MAX(ot.idorden_trabajo) AS idorden_trabajo,
            MAX(COALESCE(c.nombre_cliente, '')) AS nombre_cliente,
            MAX(COALESCE(c.apellido_cliente, '')) AS apellido_cliente,
            MAX(COALESCE(m.mod_descri, '')) AS mod_descri,
            MAX(COALESCE(v.placa, '')) AS placa,

            GROUP_CONCAT(DISTINCT a.desc_articulo SEPARATOR '|') AS trabajos

        FROM registro_servicio rs
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rs.idorden_trabajo
        LEFT JOIN registro_servicio_detalle d ON d.idregistro_servicio = rs.idregistro_servicio
        LEFT JOIN articulos a ON a.id_articulo = d.id_articulo

        LEFT JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        LEFT JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
        LEFT JOIN recepcion_servicio r_normal ON r_normal.idrecepcion = ds.idrecepcion
        LEFT JOIN recepcion_servicio r_reclamo ON r_reclamo.idreclamo_servicio = ot.idreclamo_servicio
        LEFT JOIN clientes c ON c.id_cliente = COALESCE(r_normal.id_cliente, r_reclamo.id_cliente)
        LEFT JOIN vehiculos v ON v.id_vehiculo = COALESCE(r_normal.id_vehiculo, r_reclamo.id_vehiculo)
        LEFT JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto

        WHERE rs.estado = 1
        AND rs.id_sucursal = :sucursal

        AND (
            c.nombre_cliente LIKE :b1
            OR c.apellido_cliente LIKE :b2
            OR v.placa LIKE :b3
            OR ot.idorden_trabajo LIKE :b4
            OR rs.idregistro_servicio LIKE :b5
        )

        GROUP BY rs.idregistro_servicio

        ORDER BY rs.idregistro_servicio DESC
        LIMIT 20
        ");
        $busqueda = "%$texto%";

        $sql->bindValue(':sucursal', $_SESSION['nick_sucursal'], PDO::PARAM_INT);
        $sql->bindValue(':b1', $busqueda);
        $sql->bindValue(':b2', $busqueda);
        $sql->bindValue(':b3', $busqueda);
        $sql->bindValue(':b4', $busqueda);
        $sql->bindValue(':b5', $busqueda);

        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ================= LISTAR / BUSCAR RECLAMOS ================= */
    protected static function listar_reclamos_modelo($inicio, $registros, $filtrosSQL, $orderSQL = "ORDER BY rs.fecha_reclamo DESC, rs.idreclamo_servicio DESC")
    {
        $pdo = self::conectar();

        $sql = "
        SELECT 
            rs.*,
            MAX(rgs.idorden_trabajo) AS idorden_trabajo,
            MAX(COALESCE(c.nombre_cliente, '')) AS nombre_cliente,
            MAX(COALESCE(c.apellido_cliente, '')) AS apellido_cliente,
            MAX(COALESCE(v.placa, '')) AS placa,
            MAX(COALESCE(m.mod_descri, '')) AS modelo
        FROM reclamo_servicio rs
        INNER JOIN registro_servicio rgs ON rgs.idregistro_servicio = rs.idregistro_servicio
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rgs.idorden_trabajo
        LEFT JOIN clientes c ON c.id_cliente = rs.id_cliente
        LEFT JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
        LEFT JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        WHERE 1=1 $filtrosSQL
        GROUP BY rs.idreclamo_servicio
        $orderSQL
        LIMIT $inicio, $registros
        ";

        $datos = $pdo->query($sql)->fetchAll();

        $total = $pdo->query("
        SELECT COUNT(DISTINCT rs.idreclamo_servicio)
        FROM reclamo_servicio rs
        INNER JOIN registro_servicio rgs ON rgs.idregistro_servicio = rs.idregistro_servicio
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rgs.idorden_trabajo
        LEFT JOIN clientes c ON c.id_cliente = rs.id_cliente
        LEFT JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
        LEFT JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        WHERE 1=1 $filtrosSQL
        ")->fetchColumn();

        return [
            "datos" => $datos,
            "total" => $total
        ];
    }

    /* ================= ANULAR RECLAMO ================= */
    protected static function anular_reclamo_modelo($id, $usuario)
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            /* 🔍 OBTENER REGISTRO_SERVICIO */
            $q = $pdo->prepare("
            SELECT idregistro_servicio, estado
            FROM reclamo_servicio
            WHERE idreclamo_servicio = ?
            FOR UPDATE
        ");
            $q->execute([$id]);
            $reclamo = $q->fetch(PDO::FETCH_ASSOC);

            if (!$reclamo) {
                $pdo->rollBack();
                return ['msg' => 'El reclamo no existe'];
            }

            if ((int)$reclamo['estado'] !== 1) {
                $pdo->rollBack();
                return ['msg' => 'Solo se puede anular un reclamo activo sin proceso iniciado'];
            }

            $qRecepcion = $pdo->prepare("
                SELECT COUNT(*)
                FROM recepcion_servicio
                WHERE idreclamo_servicio = ?
                  AND estado != 0
            ");
            $qRecepcion->execute([$id]);

            if ($qRecepcion->fetchColumn() > 0) {
                $pdo->rollBack();
                return ['msg' => 'No se puede anular un reclamo con recepcion generada'];
            }

            /* ❌ ANULAR RECLAMO */
            $upd = $pdo->prepare("
            UPDATE reclamo_servicio
            SET estado = 0,
                fecha_cierre = NOW(),
                observacion_cierre = 'Anulado'
            WHERE idreclamo_servicio = ?
        ");
            $upd->execute([$id]);

            /* 🔍 VERIFICAR SI QUEDAN RECLAMOS ACTIVOS */
            $v = $pdo->prepare("
            SELECT COUNT(*)
            FROM reclamo_servicio
            WHERE idregistro_servicio = ?
            AND estado != 0
        ");
            $v->execute([$reclamo['idregistro_servicio']]);
            $activos = $v->fetchColumn();

            /* 🔄 SI NO HAY MÁS → VOLVER ESTADO */
            if ($activos == 0) {
                $updReg = $pdo->prepare("
                UPDATE registro_servicio
                SET estado = 1
                WHERE idregistro_servicio = ?
            ");
                $updReg->execute([$reclamo['idregistro_servicio']]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['msg' => $e->getMessage()];
        }
    }
}
