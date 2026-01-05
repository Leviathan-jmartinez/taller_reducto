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
        $sql = mainModel::conectar()->prepare(
            "DELETE FROM proveedores WHERE idproveedores = :id"
        );
        $sql->bindParam(":id", $id);
        $sql->execute();
        return $sql;
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
}
