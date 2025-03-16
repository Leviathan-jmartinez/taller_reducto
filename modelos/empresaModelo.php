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
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":email", $datos['email']);
        $sql->bindParam(":telefono", $datos['telefono']);
        $sql->execute();
        return $sql;
    }
}
