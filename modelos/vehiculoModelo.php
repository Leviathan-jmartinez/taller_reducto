<?php
require_once "mainModel.php";

class vehiculoModelo extends mainModel
{
    /** datos vehiculo */
    protected static function datos_vehiculo_modelo($tipo, $id)
    {
        if ($tipo == "Unico") {
            $sql = mainModel::conectar()->prepare(
                "SELECT * FROM vehiculos WHERE id_vehiculo = :id"
            );
            $sql->bindParam(":id", $id);
        } elseif ($tipo == "Conteo") {
            $sql = mainModel::conectar()->prepare(
                "SELECT id_vehiculo FROM vehiculos WHERE estado = 1"
            );
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

    protected static function obtener_colores_modelo()
    {
        $sql = mainModel::conectar()->prepare(
            "SELECT id_color, col_descripcion FROM colores ORDER BY col_descripcion ASC"
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
            (id_cliente,id_modeloauto,id_color,nro_serie,placa,anho,estado)
            VALUES
            (:cliente,:modelo,:color,:serie,:placa,:anho,:estado)"
        );

        $sql->bindParam(":cliente", $datos['id_cliente']);
        $sql->bindParam(":modelo", $datos['id_modeloauto']);
        $sql->bindParam(":color", $datos['id_color']);
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
}
