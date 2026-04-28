<?php
require_once "mainModel.php";

class vehiculoModelo extends mainModel
{

    protected static function listar_vehiculos_modelo($inicio, $registros, $filtrosSQL)
    {
        $conexion = mainModel::conectar();

        $sql = "SELECT SQL_CALC_FOUND_ROWS v.*, 
                c.nombre_cliente, c.apellido_cliente,
                m.mod_descri,
                v.color
            FROM vehiculos v
            INNER JOIN clientes c ON c.id_cliente = v.id_cliente
            INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
            WHERE 1=1 $filtrosSQL
            ORDER BY v.id_vehiculo ASC
            LIMIT :inicio, :registros";

        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(":inicio", (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(":registros", (int)$registros, PDO::PARAM_INT);

        $stmt->execute();

        $datos = $stmt->fetchAll();
        $total = $conexion->query("SELECT FOUND_ROWS()")->fetchColumn();

        return [
            "datos" => $datos,
            "total" => (int)$total
        ];
    }
    /** datos vehiculo */
    protected static function datos_vehiculo_modelo($tipo, $id)
    {
        if ($tipo == "Unico") {

            $sql = mainModel::conectar()->prepare("
            SELECT v.*, 
                   CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente
            FROM vehiculos v
            INNER JOIN clientes c ON c.id_cliente = v.id_cliente
            WHERE v.id_vehiculo = :id
        ");

            $sql->bindParam(":id", $id);
        } elseif ($tipo == "Conteo") {

            $sql = mainModel::conectar()->prepare("
            SELECT id_vehiculo 
            FROM vehiculos 
            WHERE estado = 1
        ");
        }

        $sql->execute();
        return $sql;
    }

    /** listas referenciales */
    protected static function obtener_clientes_modelo()
    {
        $sql = mainModel::conectar()->prepare(
            "SELECT id_cliente,
                    CONCAT(nombre_cliente,' ',apellido_cliente) AS cliente
             FROM clientes
             ORDER BY nombre_cliente ASC"
        );
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function obtener_modelos_modelo()
    {
        $sql = mainModel::conectar()->prepare(
            "SELECT id_modeloauto, mod_descri FROM modelo_auto ORDER BY mod_descri ASC"
        );
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /** agregar vehiculo */
    protected static function agregar_vehiculo_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "INSERT INTO vehiculos
            (id_cliente,id_modeloauto,color,nro_serie,placa,anho,estado)
            VALUES
            (:cliente,:modelo,:color,:serie,:placa,:anho,:estado)"
        );

        $sql->bindParam(":cliente", $datos['id_cliente']);
        $sql->bindParam(":modelo", $datos['id_modeloauto']);
        $sql->bindParam(":color", $datos['color']);
        $sql->bindParam(":serie", $datos['nro_serie']);
        $sql->bindParam(":placa", $datos['placa']);
        $sql->bindParam(":anho", $datos['anho']);
        $sql->bindParam(":estado", $datos['estado']);

        $sql->execute();
        return $sql;
    }

    /** actualizar vehiculo */
    protected static function actualizar_vehiculo_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "UPDATE vehiculos SET
                id_cliente=:cliente,
                id_modeloauto=:modelo,
                id_color=:color,
                nro_serie=:serie,
                placa=:placa,
                anho=:anho,
                estado=:estado
            WHERE id_vehiculo=:id"
        );

        $sql->bindParam(":cliente", $datos['id_cliente']);
        $sql->bindParam(":modelo", $datos['id_modeloauto']);
        $sql->bindParam(":color", $datos['id_color']);
        $sql->bindParam(":serie", $datos['nro_serie']);
        $sql->bindParam(":placa", $datos['placa']);
        $sql->bindParam(":anho", $datos['anho']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":id", $datos['id_vehiculo']);

        $sql->execute();
        return $sql;
    }

    /** eliminar vehiculo */
    protected static function eliminar_vehiculo_modelo($id)
    {
        $pdo = mainModel::conectar();

        // 1) Verificar si el vehículo ya fue usado
        $check = $pdo->prepare("
        SELECT 1 
        FROM recepcion_servicio 
        WHERE id_vehiculo = :id
        LIMIT 1
        ");
        $check->bindParam(":id", $id, PDO::PARAM_INT);
        $check->execute();

        if ($check->rowCount() > 0) {
            // Ya fue usado → solo desactivar
            $stmt = $pdo->prepare("
            UPDATE vehiculos 
            SET estado = 0 
            WHERE id_vehiculo = :id
        ");
        } else {
            // No está relacionado → se puede eliminar
            $stmt = $pdo->prepare("
            DELETE FROM vehiculos 
            WHERE id_vehiculo = :id
        ");
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    protected static function buscar_cliente_modelo($term)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT id_cliente,
               CONCAT(doc_number, ' - ',nombre_cliente,' ',apellido_cliente) AS cliente
        FROM clientes
        WHERE nombre_cliente LIKE :term
        OR apellido_cliente LIKE :term 
        OR doc_number LIKE :term
        ORDER BY nombre_cliente ASC
        LIMIT 20
        ");

        $sql->bindValue(":term", "%$term%");
        $sql->execute();

        return $sql->fetchAll();
    }
}
