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
            CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
            v.placa,
            v.anho
        FROM recepcion_servicio rs
        INNER JOIN clientes c ON c.id_cliente = rs.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
        WHERE rs.estado = 1 
          AND (
                c.nombre_cliente LIKE :b
             OR c.apellido_cliente LIKE :b 
             OR c.doc_number LIKE :b
             OR v.placa LIKE :b
          )
        ORDER BY rs.fecha_ingreso DESC
        LIMIT 20
        ");

        $sql->bindParam(":b", $busqueda);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function guardar_diagnostico_modelo($d)
    {
        $pdo = mainModel::conectar();

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
                (idrecepcion, id_usuario, id_equipo, id_sucursal, fecha_diagnostico, observaciones, estado)
                VALUES
                (:recepcion, :usuario, :equipo, :sucursal, :fecha, :obs, :estado)
            ");

            $sql->bindParam(":recepcion", $d['idrecepcion'], PDO::PARAM_INT);
            $sql->bindParam(":usuario",   $d['id_usuario'], PDO::PARAM_INT);
            $sql->bindParam(":equipo",    $d['id_equipo'], PDO::PARAM_INT);
            $sql->bindParam(":sucursal", $d['id_sucursal'], PDO::PARAM_INT);
            $sql->bindParam(":fecha",     $d['fecha']);
            $sql->bindParam(":obs",       $d['observacion']);
            $sql->bindParam(":estado",    $d['estado'], PDO::PARAM_INT);

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

                $sql_det = $pdo->prepare("
                    INSERT INTO diagnostico_detalle
                    (id_diagnostico, sistema, problema, gravedad, solucion_propuesta, requiere_repuesto, requiere_mano_obra)
                    VALUES
                    (:diag, :sistema, :problema, :gravedad, :solucion, :rep, :mano)
                ");

                foreach ($d['detalles'] as $i => $det) {

                    if (empty(trim($det['problema']))) continue;

                    $item = $i + 1;

                    if (!$sql_det->execute([
                        ':diag'      => $id_diagnostico,
                        ':sistema'   => $det['sistema'] ?? null,
                        ':problema'  => $det['problema'],
                        ':gravedad'  => $det['gravedad'] ?? 'media',
                        ':solucion'  => $det['solucion_propuesta'] ?? null,
                        ':rep'       => $det['requiere_repuesto'] ?? 0,
                        ':mano'      => $det['requiere_mano_obra'] ?? 1
                    ])) {
                        $error = $sql_det->errorInfo();
                        $pdo->rollBack();
                        return [
                            "error" => true,
                            "msg" => "Error detalle: " . ($error[2] ?? "SQL desconocido")
                        ];
                    }
                }
            }

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

    protected static function listar_diagnosticos_modelo($inicio, $registros, $filtrosSQL)
    {
        $conexion = mainModel::conectar();

        $baseSQL = "
        FROM diagnostico_servicio d
        INNER JOIN recepcion_servicio rs ON rs.idrecepcion = d.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = rs.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
        INNER JOIN usuarios u ON u.id_usuario = d.id_usuario
        WHERE rs.id_sucursal = '{$_SESSION['nick_sucursal']}'
        $filtrosSQL
        ";

        $selectSQL = "
        SELECT 
            d.id_diagnostico,
            d.fecha_diagnostico,
            d.estado,
            CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
            v.placa,
            u.usu_nombre,
            u.usu_apellido
        ";

        $orderSQL = "ORDER BY d.id_diagnostico DESC";

        return mainModel::ejecutarPaginador(
            $conexion,
            $baseSQL,
            $selectSQL,
            $orderSQL,
            $inicio,
            $registros
        );
    }

    protected static function anular_diagnostico_modelo($id)
    {
        $pdo = mainModel::conectar();

        try {

            // ================= OBTENER DATOS =================
            $sql = $pdo->prepare("
            SELECT estado, idrecepcion
            FROM diagnostico_servicio
            WHERE id_diagnostico = :id
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
            WHERE id_diagnostico = :id
        ");
            $sql->execute([':id' => $id]);

            if ($sql->fetchColumn() > 0) {
                return [
                    "error" => true,
                    "msg" => "No se puede anular, ya tiene presupuesto"
                ];
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
