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
            $sql = mainModel::conectar()->prepare("SELECT id_cliente FROM clientes WHERE id_cliente != '1' ");
        }
        $sql->execute();
        return $sql;
    }
}
