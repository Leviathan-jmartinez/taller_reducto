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

            $qOrigen = $pdo->prepare("
                SELECT
                    id_cliente,
                    id_vehiculo,
                    fecha_servicio
                FROM registro_servicio
                WHERE idregistro_servicio = ?
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

            $requiereGarantia = (int)($datos['requiere_garantia'] ?? 0);
            if ($requiereGarantia === 1) {
                $fechaVencimiento = (new DateTime($origenServicio['fecha_servicio']))
                    ->modify('+3 months')
                    ->format('Y-m-d');

                if (date('Y-m-d') > $fechaVencimiento) {
                    $pdo->rollBack();
                    return ['msg' => 'La garantia esta vencida por fecha'];
                }
            }

            $tipoReclamo = strtoupper(trim($datos['tipo_reclamo'] ?? 'GENERAL'));
            $tiposPermitidos = ['SERVICIO', 'REPUESTO', 'ATENCION', 'GENERAL'];
            if (!in_array($tipoReclamo, $tiposPermitidos, true)) {
                $pdo->rollBack();
                return ['msg' => 'Tipo de reclamo invalido'];
            }

            $detalles = json_decode($datos['detalles_reclamo_json'] ?? '[]', true);
            if (!is_array($detalles)) {
                $detalles = [];
            }

            if (in_array($tipoReclamo, ['SERVICIO', 'REPUESTO'], true) && empty($detalles)) {
                $pdo->rollBack();
                return ['msg' => 'Debe seleccionar al menos un detalle reclamado'];
            }

            if (in_array($tipoReclamo, ['ATENCION', 'GENERAL'], true)) {
                $v = $pdo->prepare("
                    SELECT idreclamo_servicio
                    FROM reclamo_servicio
                    WHERE idregistro_servicio = ?
                      AND tipo_reclamo = ?
                      AND estado IN (1, 2, 3)
                    LIMIT 1
                ");
                $v->execute([$datos['idregistro_servicio'], $tipoReclamo]);

                if ($v->fetchColumn()) {
                    $pdo->rollBack();
                    return ['msg' => 'Ya existe un reclamo activo de este tipo para el servicio'];
                }
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
                $tipoReclamo,
                $datos['origen'],
                $datos['prioridad'],
                $requiereGarantia,
                $datos['usuario']
            ]);

            $idReclamo = (int)$pdo->lastInsertId();

            if (!empty($detalles)) {
                $insDet = $pdo->prepare("
                    INSERT INTO reclamo_servicio_detalle
                    (
                        idreclamo_servicio,
                        id_registro_servicio_detalle,
                        motivo,
                        requiere_garantia,
                        estado
                    )
                    VALUES (?, ?, ?, ?, 1)
                ");

                $idsSeleccionados = [];
                $motivosPorDetalle = [];

                foreach ($detalles as $det) {
                    $idDetalle = (int)($det['id_registro_servicio_detalle'] ?? 0);
                    if ($idDetalle <= 0 || isset($idsSeleccionados[$idDetalle])) {
                        continue;
                    }

                    $idsSeleccionados[$idDetalle] = true;
                    $motivo = trim($det['motivo'] ?? '');
                    $motivosPorDetalle[$idDetalle] = $motivo !== '' ? $motivo : $datos['descripcion'];
                }

                if (in_array($tipoReclamo, ['SERVICIO', 'REPUESTO'], true) && empty($idsSeleccionados)) {
                    throw new Exception('Debe seleccionar al menos un detalle reclamado');
                }

                if (!empty($idsSeleccionados)) {
                    $idsDetalle = array_keys($idsSeleccionados);
                    $placeholders = implode(',', array_fill(0, count($idsDetalle), '?'));
                    $paramsDetalle = array_merge([$datos['idregistro_servicio']], $idsDetalle);

                    $valDetalle = $pdo->prepare("
                        SELECT
                            d.id_registro_servicio_detalle,
                            a.tipo,
                            COUNT(r.idreclamo_servicio) AS reclamos_activos
                        FROM registro_servicio_detalle d
                        INNER JOIN articulos a ON a.id_articulo = d.id_articulo
                        LEFT JOIN reclamo_servicio_detalle rd
                            ON rd.id_registro_servicio_detalle = d.id_registro_servicio_detalle
                        LEFT JOIN reclamo_servicio r
                            ON r.idreclamo_servicio = rd.idreclamo_servicio
                        AND r.estado IN (1, 2, 3)
                        WHERE d.idregistro_servicio = ?
                        AND d.id_registro_servicio_detalle IN ($placeholders)
                        GROUP BY d.id_registro_servicio_detalle, a.tipo
                    ");
                    $valDetalle->execute($paramsDetalle);

                    $detallesValidos = [];
                    foreach ($valDetalle->fetchAll(PDO::FETCH_ASSOC) as $detalleValido) {
                        $detallesValidos[(int)$detalleValido['id_registro_servicio_detalle']] = $detalleValido;
                    }

                    if (count($detallesValidos) !== count($idsDetalle)) {
                        throw new Exception('Uno de los detalles no pertenece al servicio seleccionado');
                    }

                    foreach ($idsDetalle as $idDetalle) {
                        $detalleValido = $detallesValidos[$idDetalle];

                        if ($tipoReclamo === 'SERVICIO' && $detalleValido['tipo'] !== 'servicio') {
                            throw new Exception('El reclamo de servicio solo puede incluir servicios');
                        }

                        if ($tipoReclamo === 'REPUESTO' && $detalleValido['tipo'] !== 'producto') {
                            throw new Exception('El reclamo de repuesto solo puede incluir productos');
                        }

                        if ((int)$detalleValido['reclamos_activos'] > 0) {
                            throw new Exception('Uno de los detalles seleccionados ya tiene un reclamo activo');
                        }

                        $insDet->execute([
                            $idReclamo,
                            $idDetalle,
                            $motivosPorDetalle[$idDetalle],
                            $requiereGarantia
                        ]);
                    }
                }
            }

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
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }
        $sql = self::conectar()->prepare("
        SELECT 
            rs.idregistro_servicio,
            rs.fecha_servicio,
            DATE_ADD(rs.fecha_servicio, INTERVAL 3 MONTH) AS garantia_fecha_vencimiento,
            rs.kilometraje_salida AS garantia_km_inicio,
            CASE
                WHEN rs.kilometraje_salida IS NULL THEN NULL
                ELSE CAST(rs.kilometraje_salida AS UNSIGNED) + 2000
            END AS garantia_km_limite,
            COALESCE(c.nombre_cliente, '') AS nombre_cliente,
            COALESCE(c.apellido_cliente, '') AS apellido_cliente,
            COALESCE(m.mod_descri, '') AS mod_descri,
            COALESCE(v.placa, '') AS placa

        FROM registro_servicio rs
        LEFT JOIN clientes c ON c.id_cliente = rs.id_cliente
        LEFT JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
        LEFT JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto

        WHERE rs.estado = 1
        AND rs.id_sucursal = :sucursal

        AND (
            c.nombre_cliente LIKE :b1
            OR c.apellido_cliente LIKE :b2
            OR c.doc_number LIKE :b1
            OR v.placa LIKE :b3
            OR rs.idregistro_servicio LIKE :b4
            OR rs.idorden_trabajo LIKE :b4
        )

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

    protected static function cargar_registro_reclamo_modelo($idRegistro, $idSucursal)
    {
        $pdo = self::conectar();

        $cab = $pdo->prepare("
            SELECT
                rs.idregistro_servicio,
                rs.fecha_servicio,
                DATE_ADD(rs.fecha_servicio, INTERVAL 3 MONTH) AS garantia_fecha_vencimiento,
                rs.kilometraje_salida AS garantia_km_inicio,
                CASE
                    WHEN rs.kilometraje_salida IS NULL THEN NULL
                    ELSE CAST(rs.kilometraje_salida AS UNSIGNED) + 2000
                END AS garantia_km_limite,
                c.nombre_cliente,
                c.apellido_cliente,
                v.placa,
                m.mod_descri
            FROM registro_servicio rs
            INNER JOIN clientes c ON c.id_cliente = rs.id_cliente
            INNER JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
            INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
            WHERE rs.idregistro_servicio = ?
              AND rs.id_sucursal = ?
              AND rs.estado = 1
            LIMIT 1
        ");
        $cab->execute([$idRegistro, $idSucursal]);
        $registro = $cab->fetch(PDO::FETCH_ASSOC);

        if (!$registro) {
            return [];
        }

        $det = $pdo->prepare("
            SELECT
                d.id_registro_servicio_detalle,
                d.id_articulo,
                a.desc_articulo,
                a.tipo,
                d.cantidad,
                d.precio_unitario,
                d.subtotal,
                d.origen
            FROM registro_servicio_detalle d
            INNER JOIN articulos a ON a.id_articulo = d.id_articulo
            WHERE d.idregistro_servicio = ?
            ORDER BY d.id_registro_servicio_detalle ASC
        ");
        $det->execute([$idRegistro]);

        return [
            'registro' => $registro,
            'detalle' => $det->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    /* ================= LISTAR / BUSCAR RECLAMOS ================= */
    protected static function listar_reclamos_modelo($inicio, $registros, $filtrosSQL, $orderSQL = "ORDER BY rs.fecha_reclamo DESC, rs.idreclamo_servicio DESC")
    {
        $pdo = self::conectar();

        $sql = "
        SELECT 
            rs.*,
            MAX(COALESCE(c.nombre_cliente, '')) AS nombre_cliente,
            MAX(COALESCE(c.apellido_cliente, '')) AS apellido_cliente,
            MAX(COALESCE(v.placa, '')) AS placa,
            MAX(COALESCE(m.mod_descri, '')) AS modelo
        FROM reclamo_servicio rs
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
    protected static function anular_reclamo_modelo($id, $usuario, $motivo = '')
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            /* 🔍 OBTENER REGISTRO_SERVICIO */
            $q = $pdo->prepare("
            SELECT idregistro_servicio, id_sucursal, estado
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

            mainModel::registrar_anulacion_auditoria_modelo($pdo, [
                'modulo' => 'reclamo_servicio',
                'tabla_afectada' => 'reclamo_servicio',
                'id_registro' => $id,
                'id_sucursal' => $reclamo['id_sucursal'],
                'estado_anterior' => $reclamo['estado'],
                'estado_nuevo' => '0',
                'motivo' => $motivo,
                'usuario_anula' => $usuario,
                'referencia' => 'RECLAMO_SERVICIO #' . $id
            ]);

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
