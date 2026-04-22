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
            AND estado = 1
        ");
            $v->execute([$datos['idregistro_servicio']]);

            if ($v->rowCount() > 0) {
                return ['msg' => 'Ya existe reclamo activo'];
            }

            /* INSERT RECLAMO */
            $ins = $pdo->prepare("
            INSERT INTO reclamo_servicio
            (
                idregistro_servicio,
                id_sucursal,
                fecha_reclamo,
                descripcion,
                tipo_reclamo,
                origen,
                prioridad,
                requiere_garantia,
                estado,
                usuario_registra
            )
            VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, 1, ?)
        ");

            $ins->execute([
                $datos['idregistro_servicio'],
                $datos['id_sucursal'],
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
            ot.idorden_trabajo,
            c.nombre_cliente,
            c.apellido_cliente,
            m.mod_descri,
            v.placa,

            GROUP_CONCAT(a.desc_articulo SEPARATOR '|') AS trabajos

        FROM registro_servicio rs
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rs.idorden_trabajo
        INNER JOIN orden_trabajo_detalle d ON d.idorden_trabajo = ot.idorden_trabajo
        INNER JOIN articulos a ON a.id_articulo = d.id_articulo

        INNER JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        INNER JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ds.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto

        WHERE rs.estado = 1
        AND r.id_sucursal = :sucursal

        AND (
            c.nombre_cliente LIKE :b1
            OR c.apellido_cliente LIKE :b2
            OR v.placa LIKE :b3
            OR ot.idorden_trabajo LIKE :b4
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

        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ================= LISTAR / BUSCAR RECLAMOS ================= */
    protected static function listar_reclamos_modelo($inicio, $registros, $filtrosSQL)
    {
        $pdo = self::conectar();

        $sql = "
        SELECT 
            rs.*,
            c.nombre_cliente,
            c.apellido_cliente,
            v.placa
        FROM reclamo_servicio rs
        INNER JOIN registro_servicio rgs ON rgs.idregistro_servicio = rs.idregistro_servicio
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rgs.idorden_trabajo
        INNER JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        INNER JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ds.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        WHERE 1=1 $filtrosSQL
        ORDER BY rs.idreclamo_servicio DESC
        LIMIT $inicio, $registros
        ";

        $datos = $pdo->query($sql)->fetchAll();

        $total = $pdo->query("
        SELECT COUNT(*)
        FROM reclamo_servicio rs
        INNER JOIN registro_servicio rgs ON rgs.idregistro_servicio = rs.idregistro_servicio
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rgs.idorden_trabajo
        INNER JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        INNER JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ds.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
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
            SELECT idregistro_servicio
            FROM reclamo_servicio
            WHERE idreclamo_servicio = ?
        ");
            $q->execute([$id]);
            $idRegistro = $q->fetchColumn();

            if (!$idRegistro) {
                return false;
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
            AND estado = 1
        ");
            $v->execute([$idRegistro]);
            $activos = $v->fetchColumn();

            /* 🔄 SI NO HAY MÁS → VOLVER ESTADO */
            if ($activos == 0) {
                $updReg = $pdo->prepare("
                UPDATE registro_servicio
                SET estado = 1
                WHERE idregistro_servicio = ?
            ");
                $updReg->execute([$idRegistro]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}
