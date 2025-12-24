<?php
require_once "mainModel.php";

class recepcionservicioModelo extends mainModel
{
    protected static function buscar_cliente_modelo($busqueda)
    {
        $busqueda = "%$busqueda%";

        $sql = mainModel::conectar()->prepare("
            SELECT
                id_cliente,
                CONCAT(nombre_cliente,' ',apellido_cliente) AS cliente,
                doc_number,
                celular_cliente
            FROM clientes
            WHERE estado_cliente = 1
              AND (
                   nombre_cliente LIKE :b
                OR apellido_cliente LIKE :b
                OR doc_number LIKE :b
              )
            ORDER BY nombre_cliente
            LIMIT 20
        ");

        $sql->bindParam(":b", $busqueda);
        $sql->execute();

        $clientes = $sql->fetchAll(PDO::FETCH_ASSOC);

        if (!$clientes) {
            return '<div class="alert alert-warning text-center">
                        No se encontraron clientes
                    </div>';
        }

        $tabla = '<table class="table table-bordered table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th>Celular</th>
                            <th>Acción</th>
                        </tr>
                    </thead><tbody>';

        foreach ($clientes as $c) {
            $tabla .= '
            <tr>
                <td>' . $c['cliente'] . '</td>
                <td>' . $c['doc_number'] . '</td>
                <td>' . $c['celular_cliente'] . '</td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm"
                        onclick="seleccionarCliente(
                            ' . $c['id_cliente'] . ',
                            \'' . addslashes($c['cliente']) . '\',
                            \'' . addslashes($c['doc_number']) . '\'
                        )">
                        Seleccionar
                    </button>
                </td>
            </tr>';
        }

        $tabla .= '</tbody></table>';

        return $tabla;
    }


    protected static function buscar_vehiculo_modelo($busqueda, $id_cliente)
    {
        $busqueda = "%$busqueda%";

        $sql = mainModel::conectar()->prepare("
            SELECT
                v.id_vehiculo,
                v.placa,
                v.anho,
                ma.mar_descri AS marca,
                m.mod_descri AS modelo,
                c.col_descripcion AS color
            FROM vehiculos v
            INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
            INNER JOIN marcas ma ON ma.id_marcas= m.id_marcas
            INNER JOIN colores c ON c.id_color = v.id_color
            WHERE v.id_cliente = :cliente
              AND v.estado = 1
              AND (
                   v.placa LIKE :b
                OR m.mod_descri LIKE :b
              )
            ORDER BY v.placa
        ");

        $sql->bindParam(":cliente", $id_cliente, PDO::PARAM_INT);
        $sql->bindParam(":b", $busqueda);
        $sql->execute();

        $vehiculos = $sql->fetchAll(PDO::FETCH_ASSOC);

        if (!$vehiculos) {
            return '<div class="alert alert-warning text-center">
                        No se encontraron vehículos
                    </div>';
        }

        $tabla = '<table class="table table-bordered table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Placa</th>
                            <th>Vehículo</th>
                            <th>Año</th>
                            <th>Acción</th>
                        </tr>
                    </thead><tbody>';

        foreach ($vehiculos as $v) {
            $desc = $v['marca'] . ' ' . $v['modelo'] . ' (' . $v['placa'] . ')';

            $tabla .= '
            <tr>
                <td>' . $v['placa'] . '</td>
                <td>' . $v['marca'] . ' ' . $v['modelo'] . ' - ' . $v['color'] . '</td>
                <td>' . $v['anho'] . '</td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm"
                        onclick="seleccionarVehiculo(
                            ' . $v['id_vehiculo'] . ',
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

    protected static function guardar_recepcion_modelo($d)
    {
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            $sql = $pdo->prepare("
            INSERT INTO recepcion_servicio
            (id_usuario, id_cliente, id_vehiculo, fecha_ingreso, kilometraje, observacion, estado)
            VALUES
            (:usuario, :cliente, :vehiculo, :fecha, :km, :obs, :estado)
        ");

            $sql->bindParam(":usuario",  $d['id_usuario'],   PDO::PARAM_INT);
            $sql->bindParam(":cliente",  $d['id_cliente'],   PDO::PARAM_INT);
            $sql->bindParam(":vehiculo", $d['id_vehiculo'],  PDO::PARAM_INT);
            $sql->bindParam(":fecha",    $d['fecha_ingreso']);
            $sql->bindParam(":km",       $d['kilometraje'],  PDO::PARAM_INT);
            $sql->bindParam(":obs",      $d['observacion']);
            $sql->bindParam(":estado",   $d['estado'],       PDO::PARAM_INT);

            if (!$sql->execute()) {
                $error = $sql->errorInfo();
                $pdo->rollBack();
                return [
                    "error" => true,
                    "msg"   => $error[2] ?? 'Error SQL desconocido'
                ];
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return [
                "error" => true,
                "msg"   => $e->getMessage()
            ];
        }
    }

    protected static function anular_recepcion_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE recepcion_servicio
        SET estado = 0,
            fecha_actualizacion = NOW()
        WHERE idrecepcion = :id
          AND estado = 1");

        $sql->bindParam(":id", $id, PDO::PARAM_INT);

        return $sql->execute();
    }
}
