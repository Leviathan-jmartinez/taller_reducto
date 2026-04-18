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

            CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
            v.placa,
            v.anho
        FROM recepcion_servicio rs
        INNER JOIN clientes c ON c.id_cliente = rs.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
        WHERE rs.estado = 1 
          AND (
                c.nombre_cliente LIKE :b
             OR c.apellido_cliente LIKE :b OR c.doc_number LIKE :b
             OR v.placa LIKE :b
          )
        ORDER BY rs.fecha_ingreso DESC
        LIMIT 20
        ");

        $sql->bindParam(":b", $busqueda);
        $sql->execute();

        $datos = $sql->fetchAll(PDO::FETCH_ASSOC);

        if (!$datos) {
            return '<div class="alert alert-warning text-center">
                    No se encontraron recepciones
                </div>';
        }

        $tabla = '<table class="table table-bordered table-hover table-sm">
        <thead class="thead-light">
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>KM</th>
                <th>Acción</th>
            </tr>
        </thead><tbody>';

        foreach ($datos as $r) {

            $desc = $r['cliente'] . ' - ' . $r['placa'];

            $tabla .= '
        <tr>
            <td>' . date("d/m/Y H:i", strtotime($r['fecha_ingreso'])) . '</td>
            <td>' . $r['cliente'] . '</td>
            <td>' . $r['placa'] . ' (' . $r['anho'] . ')</td>
            <td>' . $r['kilometraje'] . '</td>
            <td class="text-center">
                <button class="btn btn-success btn-sm"
                    onclick="seleccionarRecepcion(
                        ' . $r['idrecepcion'] . ',
                        \'' . addslashes($desc) . '\'
                    )">
                    Seleccionar
                </button>
            </td>
        </tr>';
        }

        $tabla .= '</tbody></table>';

        return $tabla;
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
                (idrecepcion, id_usuario, id_equipo, fecha_diagnostico, observaciones, estado)
                VALUES
                (:recepcion, :usuario, :equipo, :fecha, :obs, :estado)
            ");

            $sql->bindParam(":recepcion", $d['idrecepcion'], PDO::PARAM_INT);
            $sql->bindParam(":usuario",   $d['id_usuario'], PDO::PARAM_INT);
            $sql->bindParam(":equipo",    $d['id_equipo'], PDO::PARAM_INT);
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
}
