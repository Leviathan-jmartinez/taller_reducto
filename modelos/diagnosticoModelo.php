<?php
require_once "mainModel.php";

class diagnosticoModelo extends mainModel
{

    protected static function buscar_recepcion_modelo($busqueda)
    {
        $busqueda = "%$busqueda%";

        $sql = mainModel::conectar()->prepare("
        SELECT
            rs.idrecepcion,
            rs.fecha_ingreso,
            rs.kilometraje,
            rs.id_sucursal,
            rs.tipo_servicio,
            rs.prioridad,
            CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
            c.doc_number,
            v.placa,
            v.anho,
            ma.mar_descri AS marca,
            m.mod_descri AS modelo,
            rs.origen,
            rs.idreclamo_servicio
        FROM recepcion_servicio rs
        INNER JOIN clientes c ON c.id_cliente = rs.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        INNER JOIN marcas ma ON ma.id_marcas = m.id_marcas
        WHERE rs.estado = 1 
          AND (
                c.nombre_cliente LIKE :b
             OR c.apellido_cliente LIKE :b 
             OR c.doc_number LIKE :b
             OR v.placa LIKE :b
             OR ma.mar_descri LIKE :b
             OR m.mod_descri LIKE :b
          )
        ORDER BY rs.fecha_ingreso DESC
        LIMIT 20
        ");

        $sql->bindParam(":b", $busqueda);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function obtener_recepcion_detalle_modelo($id, $sucursal)
    {
        $sql = mainModel::conectar()->prepare("
                    SELECT
                rs.idrecepcion,
                DATE_FORMAT(rs.fecha_ingreso, '%d/%m/%Y %H:%i') AS fecha_ingreso,
                rs.kilometraje,
                rs.nivel_combustible,
                rs.estado_exterior,
                rs.objetos_vehiculo,
                rs.tipo_servicio,
                rs.area_problema,
                rs.prioridad,
                rs.accesorios,
                rs.observacion,
                rs.origen,
                rs.id_sucursal,
                rs.idreclamo_servicio,
                c.doc_number,
                c.celular_cliente,
                CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
                v.placa,
                v.anho,
                v.color,
                CONCAT(
                    COALESCE(ma.mar_descri, ''),
                    ' ',
                    COALESCE(m.mod_descri, ''),
                    ' ',
                    COALESCE(v.placa, ''),
                    ' (',
                    COALESCE(v.anho, ''),
                    ')'
                ) AS vehiculo
            FROM recepcion_servicio rs
            INNER JOIN clientes c ON c.id_cliente = rs.id_cliente
            LEFT JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
            LEFT JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
            LEFT JOIN marcas ma ON ma.id_marcas = m.id_marcas
            WHERE rs.idrecepcion = :id
            AND rs.id_sucursal = :sucursal
            LIMIT 1;
        ");

        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->bindParam(":sucursal", $sucursal, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function buscar_articulos_modelo($texto, $tipo)
    {
        $texto = "%$texto%";

        $sql = mainModel::conectar()->prepare("
            SELECT id_articulo, codigo, desc_articulo, tipo
            FROM articulos
            WHERE estado = 1
              AND tipo = :tipo
              AND (desc_articulo LIKE :b OR codigo LIKE :b)
            ORDER BY desc_articulo
            LIMIT 20
        ");

        $sql->bindParam(":tipo", $tipo);
        $sql->bindParam(":b", $texto);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function asegurar_columnas_diagnostico_detalle($pdo)
    {
        $columnas = [
            "id_articulo_servicio INT UNSIGNED DEFAULT NULL AFTER id_diagnostico",
            "id_articulo_repuesto INT UNSIGNED DEFAULT NULL AFTER id_articulo_servicio",
            "cantidad_repuesto DECIMAL(12,2) DEFAULT 1 AFTER id_articulo_repuesto",
            "repuesto_origen VARCHAR(20) DEFAULT 'TALLER' AFTER cantidad_repuesto"
        ];

        foreach ($columnas as $columna) {
            try {
                $pdo->exec("ALTER TABLE diagnostico_detalle ADD COLUMN " . $columna);
            } catch (Exception $e) {
                // La columna ya existe en instalaciones actualizadas.
            }
        }
    }

    protected static function guardar_diagnostico_modelo($d)
    {
        $pdo = mainModel::conectar();
        self::asegurar_columnas_diagnostico_detalle($pdo);

        try {
            $pdo->beginTransaction();

            /* ================= VALIDACIONES ================= */

            if (empty($d['idrecepcion']) || empty($d['id_usuario'])) {
                return [
                    "error" => true,
                    "msg" => "Datos inválidos (recepción o usuario)"
                ];
            }

            /* ================= CABECERA ================= */

            $sql = $pdo->prepare("
                INSERT INTO diagnostico_servicio
                (idrecepcion,id_usuario,id_equipo,id_sucursal,fecha_diagnostico,observaciones,estado,es_garantia,es_reclamo_valido,requiere_cobro)
                VALUES
                (:recepcion, :usuario, :equipo, :sucursal, NOW(), :obs, :estado, :garantia, :reclamo, :cobro)
            ");

            $sql->bindParam(":recepcion", $d['idrecepcion'], PDO::PARAM_INT);
            $sql->bindParam(":usuario",   $d['id_usuario'], PDO::PARAM_INT);
            $sql->bindParam(":equipo",    $d['id_equipo'], PDO::PARAM_INT);
            $sql->bindParam(":sucursal", $d['id_sucursal'], PDO::PARAM_INT);
            $sql->bindParam(":obs",       $d['observacion']);
            $sql->bindParam(":estado",    $d['estado'], PDO::PARAM_INT);
            $sql->bindParam(":garantia",  $d['es_garantia'], PDO::PARAM_INT);
            $sql->bindParam(":reclamo",   $d['es_reclamo_valido'], PDO::PARAM_INT);
            $sql->bindParam(":cobro",     $d['requiere_cobro'], PDO::PARAM_INT);

            if (!$sql->execute()) {
                $error = $sql->errorInfo();
                $pdo->rollBack();
                return [
                    "error" => true,
                    "msg" => "Error cabecera: " . ($error[2] ?? "SQL desconocido")
                ];
            }

            $id_diagnostico = $pdo->lastInsertId();

            if (!$id_diagnostico) {
                $pdo->rollBack();
                return [
                    "error" => true,
                    "msg" => "No se pudo obtener ID del diagnóstico"
                ];
            }

            /* ================= DETALLES ================= */

            if (!empty($d['detalles'])) {
                $detallesGuardados = 0;

                $sql_det = $pdo->prepare("
                    INSERT INTO diagnostico_detalle
                    (id_diagnostico, id_articulo_servicio, id_articulo_repuesto, cantidad_repuesto, repuesto_origen, problema, gravedad)
                    VALUES
                    (:diag, :servicio, :repuesto, :cantidad_repuesto, :repuesto_origen, :problema, :gravedad)
                ");

                $sqlArticulo = $pdo->prepare("
                    SELECT id_articulo, tipo
                    FROM articulos
                    WHERE id_articulo = :id
                      AND estado = 1
                    LIMIT 1
                ");

                foreach ($d['detalles'] as $i => $det) {

                    $idServicio = (int)($det['id_articulo_servicio'] ?? 0);
                    $idRepuesto = (int)($det['id_articulo_repuesto'] ?? 0);
                    $problema = trim($det['problema'] ?? '');
                    $cantidadRepuesto = (float)($det['cantidad_repuesto'] ?? 0);
                    $repuestoOrigen = strtoupper(trim($det['repuesto_origen'] ?? 'TALLER'));
                    if (!in_array($repuestoOrigen, ['TALLER', 'CLIENTE', 'NINGUNO'], true)) {
                        $repuestoOrigen = 'TALLER';
                    }

                    if ($idServicio <= 0 || $problema === '') {
                        continue;
                    }

                    $sqlArticulo->execute([':id' => $idServicio]);
                    $servicio = $sqlArticulo->fetch(PDO::FETCH_ASSOC);
                    if (!$servicio || $servicio['tipo'] !== 'servicio') {
                        continue;
                    }

                    if ($repuestoOrigen === 'NINGUNO') {
                        $idRepuesto = null;
                        $cantidadRepuesto = 0;
                    } elseif ($idRepuesto > 0) {
                        $sqlArticulo->execute([':id' => $idRepuesto]);
                        $repuesto = $sqlArticulo->fetch(PDO::FETCH_ASSOC);
                        if (!$repuesto || $repuesto['tipo'] !== 'producto') {
                            $idRepuesto = null;
                            $cantidadRepuesto = 0;
                        } elseif ($cantidadRepuesto <= 0) {
                            $cantidadRepuesto = 1;
                        }
                    } else {
                        $idRepuesto = null;
                        $cantidadRepuesto = 0;
                    }

                    if (!$sql_det->execute([
                        ':diag'      => $id_diagnostico,
                        ':servicio'  => $idServicio,
                        ':repuesto'  => $idRepuesto,
                        ':cantidad_repuesto' => $cantidadRepuesto,
                        ':repuesto_origen' => $repuestoOrigen,
                        ':problema'  => $problema,
                        ':gravedad'  => $det['gravedad'] ?? 'media'
                    ])) {
                        $error = $sql_det->errorInfo();
                        $pdo->rollBack();
                        return [
                            "error" => true,
                            "msg" => "Error detalle: " . ($error[2] ?? "SQL desconocido")
                        ];
                    }

                    $detallesGuardados++;
                }

                if ($detallesGuardados < 1) {
                    $pdo->rollBack();
                    return [
                        "error" => true,
                        "msg" => "Debe seleccionar al menos un servicio valido en el detalle"
                    ];
                }
            }

            $sql_recepcion = $pdo->prepare("
                UPDATE recepcion_servicio
                SET estado = 2,
                    fecha_actualizacion = NOW()
                WHERE idrecepcion = :recepcion
                  AND id_sucursal = :sucursal
                  AND estado = 1
            ");

            if (!$sql_recepcion->execute([
                ':recepcion' => $d['idrecepcion'],
                ':sucursal' => $d['id_sucursal']
            ])) {
                $error = $sql_recepcion->errorInfo();
                $pdo->rollBack();
                return [
                    "error" => true,
                    "msg" => "Error actualizando recepcion: " . ($error[2] ?? "SQL desconocido")
                ];
            }

            if ($sql_recepcion->rowCount() < 1) {
                $pdo->rollBack();
                return [
                    "error" => true,
                    "msg" => "La recepcion no esta disponible para diagnostico"
                ];
            }

            $updReclamo = $pdo->prepare("
                UPDATE reclamo_servicio rc
                INNER JOIN recepcion_servicio r
                    ON r.idreclamo_servicio = rc.idreclamo_servicio
                SET rc.estado = 3
                WHERE r.idrecepcion = :recepcion
                  AND rc.id_sucursal = :sucursal
                  AND rc.estado = 2
            ");
            $updReclamo->execute([
                ':recepcion' => $d['idrecepcion'],
                ':sucursal' => $d['id_sucursal']
            ]);

            $pdo->commit();

            return [
                "success" => true,
                "id_diagnostico" => $id_diagnostico
            ];
        } catch (Exception $e) {

            $pdo->rollBack();

            return [
                "error" => true,
                "msg" => "Excepción: " . $e->getMessage()
            ];
        }
    }

    protected static function listar_diagnosticos_modelo($inicio, $registros, $filtrosSQL, $orderSQL = "ORDER BY d.fecha_diagnostico DESC, d.id_diagnostico DESC")
    {
        $conexion = mainModel::conectar();

        $baseSQL = "
        FROM diagnostico_servicio d
        INNER JOIN recepcion_servicio rs ON rs.idrecepcion = d.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = rs.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        INNER JOIN marcas ma ON ma.id_marcas = m.id_marcas
        INNER JOIN usuarios u ON u.id_usuario = d.id_usuario
        INNER JOIN equipo_trabajo et ON et.id_equipo = d.id_equipo
        LEFT JOIN (
            SELECT idreclamo_servicio, MAX(idorden_trabajo) AS idorden_trabajo
            FROM orden_trabajo
            WHERE estado != 0
            GROUP BY idreclamo_servicio
        ) ot_reclamo
            ON ot_reclamo.idreclamo_servicio = rs.idreclamo_servicio
        WHERE rs.id_sucursal = '{$_SESSION['nick_sucursal']}'
        $filtrosSQL
        ";

        $selectSQL = "
        SELECT 
            d.id_diagnostico,
            rs.idrecepcion,
            d.fecha_diagnostico,
            d.estado,

            d.es_reclamo_valido,
            d.es_garantia,
            d.requiere_cobro,

            rs.origen,
            rs.idreclamo_servicio,
            ot_reclamo.idorden_trabajo AS id_ot_reclamo,

            CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
            v.placa,
            CONCAT(ma.mar_descri, ' ', m.mod_descri, ' ', v.placa) AS vehiculo,
            rs.tipo_servicio,
            et.nombre AS equipo,
            u.usu_nombre,
            u.usu_apellido
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

    protected static function obtener_diagnostico_detalle_modelo($id, $sucursal)
    {
        $pdo = mainModel::conectar();

        $sql = $pdo->prepare("
        SELECT
            d.id_diagnostico,
            rs.idrecepcion,
            DATE_FORMAT(d.fecha_diagnostico, '%d/%m/%Y %H:%i') AS fecha_diagnostico,
            d.estado,
            d.observaciones,
            d.es_reclamo_valido,
            d.es_garantia,
            d.requiere_cobro,
            rs.origen,
            rs.tipo_servicio,
            rs.observacion AS recepcion_observacion,
            CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
            c.doc_number,
            CONCAT(ma.mar_descri, ' ', m.mod_descri, ' ', v.placa) AS vehiculo,
            v.placa,
            et.nombre AS equipo,
            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario
        FROM diagnostico_servicio d
        INNER JOIN recepcion_servicio rs ON rs.idrecepcion = d.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = rs.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        INNER JOIN marcas ma ON ma.id_marcas = m.id_marcas
        INNER JOIN equipo_trabajo et ON et.id_equipo = d.id_equipo
        INNER JOIN usuarios u ON u.id_usuario = d.id_usuario
        WHERE d.id_diagnostico = :id
          AND rs.id_sucursal = :sucursal
        LIMIT 1
        ");

        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->bindParam(":sucursal", $sucursal, PDO::PARAM_INT);
        $sql->execute();
        $cabecera = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$cabecera) {
            return [];
        }

        self::asegurar_columnas_diagnostico_detalle($pdo);
        $sqlDetalle = $pdo->prepare("
        SELECT
            dd.problema,
            dd.gravedad,
            dd.id_articulo_servicio,
            dd.id_articulo_repuesto,
            dd.cantidad_repuesto,
            dd.repuesto_origen,
            serv.codigo AS servicio_codigo,
            serv.desc_articulo AS servicio_desc,
            rep.codigo AS repuesto_codigo,
            rep.desc_articulo AS repuesto_desc
        FROM diagnostico_detalle
        dd
        LEFT JOIN articulos serv ON serv.id_articulo = dd.id_articulo_servicio
        LEFT JOIN articulos rep ON rep.id_articulo = dd.id_articulo_repuesto
        WHERE id_diagnostico = :id
        ORDER BY id_diagnostico_detalle ASC
        ");
        $sqlDetalle->bindParam(":id", $id, PDO::PARAM_INT);
        $sqlDetalle->execute();

        return [
            "cabecera" => $cabecera,
            "detalles" => $sqlDetalle->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    protected static function anular_diagnostico_modelo($id, $usuario = null, $motivo = '')
    {
        $pdo = mainModel::conectar();

        try {

            // ================= OBTENER DATOS =================
            $sql = $pdo->prepare("
            SELECT d.estado, d.idrecepcion, d.id_sucursal, r.idreclamo_servicio
            FROM diagnostico_servicio d
            INNER JOIN recepcion_servicio r ON r.idrecepcion = d.idrecepcion
            WHERE d.id_diagnostico = :id
        ");
            $sql->execute([':id' => $id]);
            $diag = $sql->fetch(PDO::FETCH_ASSOC);

            if (!$diag) {
                return [
                    "error" => true,
                    "msg" => "Diagnóstico no encontrado"
                ];
            }

            // ================= VALIDAR YA ANULADO =================
            if ($diag['estado'] == 0) {
                return [
                    "error" => true,
                    "msg" => "El diagnóstico ya está anulado"
                ];
            }

            // ================= VALIDAR PRESUPUESTO =================
            $sql = $pdo->prepare("
            SELECT COUNT(*) 
            FROM presupuesto_servicio 
            WHERE id_diagnostico = :id and estado !=0
        ");
            $sql->execute([':id' => $id]);

            if ($sql->fetchColumn() > 0) {
                return [
                    "error" => true,
                    "msg" => "No se puede anular, ya tiene presupuesto"
                ];
            }

            if (!empty($diag['idreclamo_servicio'])) {
                $sql = $pdo->prepare("
                    SELECT COUNT(*)
                    FROM orden_trabajo
                    WHERE idreclamo_servicio = :reclamo
                      AND estado != 0
                ");
                $sql->execute([':reclamo' => $diag['idreclamo_servicio']]);

                if ($sql->fetchColumn() > 0) {
                    return [
                        "error" => true,
                        "msg" => "No se puede anular, el reclamo ya tiene una OT activa"
                    ];
                }
            }

            // ================= TRANSACTION =================
            $pdo->beginTransaction();

            // 🔥 ANULAR DIAGNÓSTICO
            $sql = $pdo->prepare("
            UPDATE diagnostico_servicio
            SET estado = 0
            WHERE id_diagnostico = :id
        ");
            $sql->execute([':id' => $id]);

            // LIBERAR RECEPCIÓN (volver a estado inicial)
            $sql = $pdo->prepare("
            UPDATE recepcion_servicio
            SET estado = 1
            WHERE idrecepcion = :idrecepcion
        ");
            $sql->execute([
                ':idrecepcion' => $diag['idrecepcion']
            ]);

            if (!empty($diag['idreclamo_servicio'])) {
                $sql = $pdo->prepare("
                    UPDATE reclamo_servicio
                    SET estado = 2
                    WHERE idreclamo_servicio = :reclamo
                      AND estado = 3
                ");
                $sql->execute([
                    ':reclamo' => $diag['idreclamo_servicio']
                ]);
            }

            mainModel::registrar_anulacion_auditoria_modelo($pdo, [
                'modulo' => 'diagnostico_servicio',
                'tabla_afectada' => 'diagnostico_servicio',
                'id_registro' => $id,
                'id_sucursal' => $diag['id_sucursal'],
                'estado_anterior' => $diag['estado'],
                'estado_nuevo' => '0',
                'motivo' => $motivo,
                'usuario_anula' => $usuario ?: ($_SESSION['id_str'] ?? 0),
                'referencia' => 'DIAGNOSTICO #' . $id
            ]);

            $pdo->commit();
        } catch (Exception $e) {

            $pdo->rollBack();

            return [
                "error" => true,
                "msg" => $e->getMessage()
            ];
        }
    }
}
