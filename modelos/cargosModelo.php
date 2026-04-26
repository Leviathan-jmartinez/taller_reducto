<?php
require_once "mainModel.php";

class cargosModelo extends mainModel
{
    /** Datos cargo */
    protected static function datos_cargo_modelo($tipo, $id)
    {
        if ($tipo == "Unico") {
            $sql = mainModel::conectar()->prepare(
                "SELECT * FROM cargos WHERE idcargos = :id"
            );
            $sql->bindParam(":id", $id);
        } elseif ($tipo == "Conteo") {
            $sql = mainModel::conectar()->prepare(
                "SELECT idcargos FROM cargos WHERE estado = 1"
            );
        }

        $sql->execute();
        return $sql;
    }

    /** Agregar cargo */
    protected static function agregar_cargo_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "INSERT INTO cargos
            (descripcion, estado)
            VALUES
            (:descripcion, :estado)"
        );

        $sql->bindParam(":descripcion", $datos['descripcion']);
        $sql->bindParam(":estado", $datos['estado']);

        $sql->execute();
        return $sql;
    }

    /** Eliminar cargo */
    protected static function eliminar_cargo_modelo($id)
    {
        $pdo = mainModel::conectar();

        // 1) Verificar si el cargo está usado (ejemplo: en usuarios)
        $check = $pdo->prepare("
        SELECT 1 
        FROM empleados 
        WHERE idcargos = :id 
        LIMIT 1
        ");
        $check->bindParam(":id", $id, PDO::PARAM_INT);
        $check->execute();

        if ($check->rowCount() > 0) {
            // Ya fue usado → solo desactivar
            $stmt = $pdo->prepare("
            UPDATE cargos 
            SET estado = 0 
            WHERE idcargos = :id
        ");
        } else {
            // No está relacionado → se puede eliminar
            $stmt = $pdo->prepare("
            DELETE FROM cargos 
            WHERE idcargos = :id
        ");
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    /** Actualizar cargo */
    protected static function actualizar_cargo_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "UPDATE cargos SET
                descripcion = :descripcion,
                estado = :estado
            WHERE idcargos = :idcargos"
        );

        $sql->bindParam(":descripcion", $datos['descripcion']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":idcargos", $datos['idcargos']);

        $sql->execute();
        return $sql;
    }

    /** Listar cargos */
    public static function listar_cargos_modelo($inicio, $registros, $filtrosSQL)
    {
        $conexion = mainModel::conectar();

        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM cargos 
        WHERE 1=1 $filtrosSQL
        ORDER BY descripcion ASC
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
}
