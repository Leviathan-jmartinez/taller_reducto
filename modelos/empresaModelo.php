<?php
require_once "mainModel.php";

class empresaModelo extends mainModel
{
    /**datos empresa */
    protected static function datos_empresa_modelo()
    {
        $sql = mainModel::conectar()->prepare("SELECT * FROM empresa");
        $sql->execute();
        return $sql;
    }
    /**agregar empresa */
    protected static function agregar_empresa_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("INSERT INTO empresa
        (razon_social, direccion, ruc, estado, email_empresa, telefono_empresa)
        VALUES(:razon, :direccion, :ruc, :estado, :email, :telefono)");
        $sql->bindParam(":razon", $datos['razon']);
        $sql->bindParam(":direccion", $datos['direccion']);
        $sql->bindParam(":ruc", $datos['ruc']);
        $sql->bindParam(":email", $datos['email']);
        $sql->bindParam(":telefono", $datos['telefono']);
        $sql->execute();
        return $sql;
    }

    /**agregar empresa */
    protected static function actualizar_empresa_modelo($datos) {
        $sql = mainModel::conectar()->prepare("UPDATE empresa SET razon_social=:razonsocial, direccion=:direccion, ruc=:ruc, email_empresa=:email, telefono_empresa=:telefono
        WHERE id_empresa=:id");
        $sql->bindParam(":razonsocial", $datos['razonsocial']);
        $sql->bindParam(":direccion", $datos['direccion']);
        $sql->bindParam(":ruc", $datos['ruc']);
        $sql->bindParam(":email", $datos['email']);
        $sql->bindParam(":telefono", $datos['telefono']);
        $sql->bindParam(":id", $datos['id']);
        $sql->execute();
        return $sql;
    }
}
