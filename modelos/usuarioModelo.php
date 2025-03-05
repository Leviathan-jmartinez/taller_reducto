<?php
require_once "mainModel.php";

class usuarioModelo extends mainModel
{
    /** modelo agregar usuario*/
    protected static function agregar_usuario_modelo($datos)
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
    /**modelo eliminar usuario */
    protected static function eliminar_usuario_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("DELETE FROM usuarios where id_usuario = :id ");
        $sql->bindParam(":id", $id);
        $sql->execute();
        return $sql;
    }

    /**modelo datos usuario */
    protected static function datos_usuario_modelo($tipo, $id)
    {
        if ($tipo == "Unico") {
            $sql = mainModel::conectar()->prepare("SELECT * FROM usuarios where id_usuario = :id ");
            $sql->bindParam(":id", $id);
        } elseif ($tipo == "Conteo") {
            $sql = mainModel::conectar()->prepare("SELECT id_usuario FROM usuarios WHERE id_usuario != '1' ");
        }
        $sql->execute();
        return $sql;
    }

    /**modelo actualizar usuario */
    protected static function actualizar_usuario_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("UPDATE usuarios SET usu_nombre=:nombre, usu_clave=:clave, usu_nivel=:nivel, 
        usu_estado=:estado, usu_nick=:nick, usu_apellido=:apellido, usu_email=:email, usu_telefono=:telefono, usu_ci=:ci where id_usuario=:iduser");
        $sql->bindParam(":ci", $datos['ci']);
        $sql->bindParam(":nombre", $datos['nombre']);
        $sql->bindParam(":clave", $datos['clave']);
        $sql->bindParam(":nivel", $datos['nivel']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":nick", $datos['nick']);
        $sql->bindParam(":apellido", $datos['apellido']);
        $sql->bindParam(":email", $datos['email']);
        $sql->bindParam(":telefono", $datos['telefono']);
        $sql->bindParam(":iduser", $datos['iduser']);
        $sql->execute();
        return $sql;
    }
}
