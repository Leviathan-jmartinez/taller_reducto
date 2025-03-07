<?php
require_once "mainModel.php";

class clienteModelo extends mainModel
{
    /** modelo agregar cliente*/
    protected static function agregar_cliente_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("INSERT INTO usuarios (usu_nombre, usu_clave, usu_nivel, usu_estado, usu_nick, usu_apellido, usu_email, usu_telefono,usu_ci) 
        VALUES (:nombre, :clave, :nivel, :estado, :nick, :apellido, :email, :telefono,:ci)");
        $sql->bindParam(":ci", $datos['ci']);
        $sql->bindParam(":nombre", $datos['nombre']);
        $sql->bindParam(":clave", $datos['clave']);
        $sql->bindParam(":nivel", $datos['nivel']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":nick", $datos['nick']);
        $sql->bindParam(":apellido", $datos['apellido']);
        $sql->bindParam(":email", $datos['email']);
        $sql->bindParam(":telefono", $datos['telefono']);
        $sql->execute();
        return $sql;
    }
}