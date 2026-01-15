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

            /* VALIDAR REGISTRO */
            $v = $pdo->prepare("
            SELECT estado
            FROM registro_servicio
            WHERE idregistro_servicio = ?");
            $v->execute([$datos['idregistro_servicio']]);
            $reg = $v->fetch(PDO::FETCH_ASSOC);

            if (!$reg) {
                return ['msg' => 'Servicio no existe'];
            }

            /* VALIDAR RECLAMO DUPLICADO */
            $d = $pdo->prepare("
            SELECT idreclamo_servicio
            FROM reclamo_servicio
            WHERE idregistro_servicio = ?
            AND estado IN (1,2)");
            $d->execute([$datos['idregistro_servicio']]);

            if ($d->rowCount() > 0) {
                return ['msg' => 'El servicio ya tiene un reclamo activo'];
            }

            /* INSERTAR RECLAMO */
            $ins = $pdo->prepare("
            INSERT INTO reclamo_servicio
            (idregistro_servicio, fecha_reclamo,
            descripcion, estado, usuario_registra)
            VALUES (?, NOW(), ?, 1, ?)");
            $ins->execute([
                $datos['idregistro_servicio'],
                $datos['descripcion'],
                $datos['usuario']
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
        $sql = self::conectar()->prepare("
        SELECT rs.idregistro_servicio,
        c.nombre_cliente,
        c.apellido_cliente,
        m.mod_descri,
        v.placa
        FROM registro_servicio rs
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rs.idorden_trabajo
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ot.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        WHERE rs.estado = 1
        AND (
        c.nombre_cliente LIKE :b
        OR v.placa LIKE :b
        )
        ORDER BY rs.idregistro_servicio DESC");
        $sql->bindValue(':b', "%$texto%");
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ================= LISTAR / BUSCAR RECLAMOS ================= */
    protected static function listar_reclamos_modelo($inicio, $registros, $busqueda = "")
    {
        $filtro = "";
        if ($busqueda != "") {
            $filtro = "WHERE  (
            c.nombre_cliente LIKE :b
            OR c.apellido_cliente LIKE :b
            OR v.placa LIKE :b
        )";
        }

        $sql = self::conectar()->prepare("
        SELECT SQL_CALC_FOUND_ROWS
            rs.idreclamo_servicio,
            rs.fecha_reclamo,
            rs.descripcion,
            rs.estado,
            c.nombre_cliente,
            c.apellido_cliente,
            v.placa
        FROM reclamo_servicio rs
        INNER JOIN registro_servicio rgs ON rgs.idregistro_servicio = rs.idregistro_servicio
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rgs.idorden_trabajo
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ot.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        $filtro
        ORDER BY rs.idreclamo_servicio DESC
        LIMIT :ini,:reg
        ");

        if ($busqueda != "") {
            $sql->bindValue(":b", "%$busqueda%");
        }

        $sql->bindValue(":ini", (int)$inicio, PDO::PARAM_INT);
        $sql->bindValue(":reg", (int)$registros, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ================= ANULAR RECLAMO ================= */
    protected static function anular_reclamo_modelo($id, $usuario)
    {
        $sql = self::conectar()->prepare("
        UPDATE reclamo_servicio
        SET estado = 0,
            usuario_cierre = ?,
            fecha_cierre = NOW(),
            observacion_cierre = 'Anulado'
        WHERE idreclamo_servicio = ?
        ");
        return $sql->execute([$usuario, $id]);
    }
}
