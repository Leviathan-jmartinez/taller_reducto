<?php
require_once "mainModel.php";

class clienteModelo extends mainModel
{
    /** modelo agregar cliente*/
    protected static function agregar_cliente_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("
        INSERT INTO clientes (
            doc_number,
            nombre_cliente,
            apellido_cliente,
            celular_cliente,
            email_cliente,
            direccion_cliente,
            id_ciudad,
            doc_type,
            digito_v,
            estado_civil,
            estado_cliente
        ) VALUES (
            :doc_number,
            :nombre_cliente,
            :apellido_cliente,
            :celular_cliente,
            :email_cliente,
            :direccion_cliente,
            :id_ciudad,
            :doc_type,
            :digito_v,
            :estado_civil,
            :estado_cliente
        )
        ");

        $sql->bindValue(":doc_number", $datos['doc_number']);
        $sql->bindValue(":nombre_cliente", $datos['nombre_cliente']);
        $sql->bindValue(":apellido_cliente", $datos['apellido_cliente']);
        $sql->bindValue(":celular_cliente", $datos['celular_cliente']);
        $sql->bindValue(":email_cliente", $datos['email_cliente']);
        $sql->bindValue(":direccion_cliente", $datos['direccion_cliente']);
        $sql->bindValue(":id_ciudad", (int)$datos['id_ciudad'], PDO::PARAM_INT);
        $sql->bindValue(":doc_type", $datos['doc_type']);
        $sql->bindValue(":digito_v", $datos['digito_v']);
        $sql->bindValue(":estado_civil", $datos['estado_civil']);
        $sql->bindValue(":estado_cliente", $datos['estado_cliente']);

        $sql->execute();

        return $sql;
    }
    /**modelo datos cliente */
    protected static function datos_cliente_modelo($tipo, $id)
    {
        if ($tipo == "Unico") {
            $sql = mainModel::conectar()->prepare("SELECT * FROM clientes where id_cliente = :id ");
            $sql->bindParam(":id", $id);
        } elseif ($tipo == "Conteo") {
            $sql = mainModel::conectar()->prepare("SELECT id_cliente FROM clientes");
        }
        $sql->execute();
        return $sql;
    }

    /**eliminar cliente */
    protected static function eliminar_cliente_modelo($id)
    {
        $pdo = mainModel::conectar();

        // 1) Verificar si el cliente ya fue usado en ventas o pedidos
        $check = $pdo->prepare("
        SELECT 1 
        FROM vehiculos 
        WHERE id_cliente = :id
        LIMIT 1
        ");
        $check->bindParam(":id", $id, PDO::PARAM_INT);
        $check->execute();

        if ($check->rowCount() > 0) {
            // Ya fue usado → solo desactivar
            $stmt = $pdo->prepare("
            UPDATE clientes 
            SET estado_cliente = 0 
            WHERE id_cliente = :id
        ");
        } else {
            // No está relacionado → se puede eliminar
            $stmt = $pdo->prepare("
            DELETE FROM clientes 
            WHERE id_cliente = :id
        ");
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            $stmt = $pdo->prepare("
            UPDATE clientes
            SET estado_cliente = 0
            WHERE id_cliente = :id
        ");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
        }

        return $stmt;
    }

    protected static function obtener_ciudades_modelo()
    {
        $sql = mainModel::conectar()->prepare("SELECT id_ciudad, ciu_descri FROM ciudades ORDER BY ciu_descri ASC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function actualizar_cliente_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE clientes SET
            doc_number = :doc_number,
            nombre_cliente = :nombre_cliente,
            apellido_cliente = :apellido_cliente,
            celular_cliente = :celular_cliente,
            email_cliente = :email_cliente,
            direccion_cliente = :direccion_cliente,
            id_ciudad = :id_ciudad,
            doc_type = :doc_type,
            digito_v = :digito_v,
            estado_civil = :estado_civil,
            estado_cliente = :estado_cliente
        WHERE id_cliente = :id_cliente
        ");

        $sql->bindValue(":doc_number", $datos['doc_number']);
        $sql->bindValue(":nombre_cliente", $datos['nombre_cliente']);
        $sql->bindValue(":apellido_cliente", $datos['apellido_cliente']);
        $sql->bindValue(":celular_cliente", $datos['celular_cliente']);
        $sql->bindValue(":email_cliente", $datos['email_cliente']);
        $sql->bindValue(":direccion_cliente", $datos['direccion_cliente']);
        $sql->bindValue(":id_ciudad", (int)$datos['id_ciudad'], PDO::PARAM_INT);

        // 🔥 ESTO ES LO QUE TE FALTABA
        $sql->bindValue(":doc_type", $datos['doc_type']);
        $sql->bindValue(":digito_v", $datos['digito_v']);
        $sql->bindValue(":estado_civil", $datos['estado_civil']);

        $sql->bindValue(":estado_cliente", $datos['estado_cliente']);
        $sql->bindValue(":id_cliente", (int)$datos['id_cliente'], PDO::PARAM_INT);

        $sql->execute();

        return $sql;
    }

    protected static function listar_clientes_modelo($inicio, $registros, $filtrosSQL)
    {
        $conexion = mainModel::conectar();

        $sql = "SELECT SQL_CALC_FOUND_ROWS *
            FROM clientes
            WHERE 1=1 $filtrosSQL
            ORDER BY nombre_cliente ASC
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
