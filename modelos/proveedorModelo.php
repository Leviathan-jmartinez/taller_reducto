<?php
require_once "mainModel.php";

class proveedorModelo extends mainModel
{
    /** Datos proveedor */
    protected static function datos_proveedor_modelo($tipo, $id)
    {
        if ($tipo == "Unico") {
            $sql = mainModel::conectar()->prepare(
                "SELECT * FROM proveedores WHERE idproveedores = :id"
            );
            $sql->bindParam(":id", $id);
        } elseif ($tipo == "Conteo") {
            $sql = mainModel::conectar()->prepare(
                "SELECT idproveedores FROM proveedores WHERE estado = 1"
            );
        }
        $sql->execute();
        return $sql;
    }

    /** Listar ciudades */
    protected static function obtener_ciudades_modelo()
    {
        $sql = mainModel::conectar()->prepare(
            "SELECT id_ciudad, ciu_descri FROM ciudades ORDER BY ciu_descri ASC"
        );
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Agregar proveedor */
    protected static function agregar_proveedor_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "INSERT INTO proveedores
            (id_ciudad, razon_social, ruc, telefono, direccion, correo, estado)
            VALUES
            (:id_ciudad, :razon_social, :ruc, :telefono, :direccion, :correo, :estado)"
        );

        $sql->bindParam(":id_ciudad", $datos['id_ciudad']);
        $sql->bindParam(":razon_social", $datos['razon_social']);
        $sql->bindParam(":ruc", $datos['ruc']);
        $sql->bindParam(":telefono", $datos['telefono']);
        $sql->bindParam(":direccion", $datos['direccion']);
        $sql->bindParam(":correo", $datos['correo']);
        $sql->bindParam(":estado", $datos['estado']);

        $sql->execute();
        return $sql;
    }

    /** Eliminar proveedor */
    protected static function eliminar_proveedor_modelo($id)
    {
        $pdo = mainModel::conectar();

        // 1) Verificar si el proveedor está usado en compras o pedidos
        $check = $pdo->prepare("
        SELECT 1 
        FROM pedido_cabecera 
        WHERE id_proveedor = :id 
        LIMIT 1
        ");
        $check->bindParam(":id", $id, PDO::PARAM_INT);
        $check->execute();

        if ($check->rowCount() > 0) {
            // Ya fue usado → solo desactivar
            $stmt = $pdo->prepare("
            UPDATE proveedores 
            SET estado = 0 
            WHERE idproveedores = :id
        ");
        } else {
            // No está relacionado → se puede eliminar
            $stmt = $pdo->prepare("
            DELETE FROM proveedores 
            WHERE idproveedores = :id
        ");
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }


    /** Actualizar proveedor */
    protected static function actualizar_proveedor_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "UPDATE proveedores SET
                id_ciudad = :id_ciudad,
                razon_social = :razon_social,
                ruc = :ruc,
                telefono = :telefono,
                direccion = :direccion,
                correo = :correo,
                estado = :estado
            WHERE idproveedores = :idproveedores"
        );

        $sql->bindParam(":id_ciudad", $datos['id_ciudad']);
        $sql->bindParam(":razon_social", $datos['razon_social']);
        $sql->bindParam(":ruc", $datos['ruc']);
        $sql->bindParam(":telefono", $datos['telefono']);
        $sql->bindParam(":direccion", $datos['direccion']);
        $sql->bindParam(":correo", $datos['correo']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":idproveedores", $datos['idproveedores']);

        $sql->execute();
        return $sql;
    }

    protected static function listar_proveedores_modelo()
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            idproveedores,
            razon_social
        FROM proveedores
        WHERE estado = 1
        ORDER BY razon_social ASC
        ");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
