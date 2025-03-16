<?php
require_once "mainModel.php";

class clienteModelo extends mainModel
{
    /** modelo agregar cliente*/
    protected static function agregar_cliente_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("INSERT INTO clientes (id_ciudad, doc_number, nombre_cliente, apellido_cliente, direccion_cliente, celular_cliente, estado_civil, estado_cliente, digito_v, email_cliente, doc_type) 
        VALUES (:ciudad, :doc_number, :nombre, :apellido, :direccion, :celular, :estadoC, :estado, :dv, :email, :doctype)");
        $sql->bindParam(":ciudad", $datos['ciudad']);
        $sql->bindParam(":doc_number", $datos['doc_number']);
        $sql->bindParam(":nombre", $datos['nombre']);
        $sql->bindParam(":apellido", $datos['apellido']);
        $sql->bindParam(":direccion", $datos['direccion']);
        $sql->bindParam(":celular", $datos['celular']);
        $sql->bindParam(":estadoC", $datos['estadoC']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":dv", $datos['dv']);
        $sql->bindParam(":email", $datos['email']);
        $sql->bindParam(":doctype", $datos['doctype']);
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
        $sql = mainModel::conectar()->prepare("DELETE FROM clientes WHERE id_cliente = :id");
        $sql->bindParam(":id", $id);
        $sql->execute();
        return $sql;
    }

    protected static function obtener_ciudades_modelo()
    {
        $sql = mainModel::conectar()->prepare("SELECT id_ciudad, ciu_descri FROM ciudades ORDER BY ciu_descri ASC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /** */
    protected static function actualizar_cliente_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("UPDATE clientes
        SET id_ciudad=:ciudad, doc_number=:nrodoc, nombre_cliente=:nombre, apellido_cliente=:apellido, direccion_cliente=:direccion, celular_cliente=:telefeono, 
        estado_civil=:estadoC, estado_cliente=:estado, digito_v=:dv, doc_type=:doctype, email_cliente=:email
        WHERE id_cliente=:id");
        $sql->bindParam(":ciudad", $datos['ciudad']);
        $sql->bindParam(":nrodoc", $datos['nrodoc']);
        $sql->bindParam(":nombre", $datos['nombre']);
        $sql->bindParam(":apellido", $datos['apellido']);
        $sql->bindParam(":direccion", $datos['direccion']);
        $sql->bindParam(":telefeono", $datos['telefeono']);
        $sql->bindParam(":estadoC", $datos['estadoC']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":dv", $datos['dv']);
        $sql->bindParam(":doctype", $datos['doctype']);
        $sql->bindParam(":email", $datos['email']);
        $sql->bindParam(":id", $datos['id']);
        $sql->execute();
        return $sql;
    }
}
