<?php
require_once "mainModel.php";

class loginModelo extends mainModel
{
    /** Modelo para iniciar sesion */
    protected static function iniciar_sesion_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("SELECT * FROM usuarios WHERE usu_nick = :Usuario AND usu_clave =:Clave AND usu_estado = '1'");
        $sql->bindParam(":Usuario", $datos['Usuario']);
        $sql->bindParam(":Clave", $datos['Clave']);
        $sql->execute();
        return $sql;
    }

    protected static function obtener_permisos_usuario($idUsuario)
    {
        $sql = self::conectar()->prepare("
        SELECT DISTINCT p.clave
        FROM usuario_rol ur
        INNER JOIN rol_permiso rp ON rp.id_rol = ur.id_rol
        INNER JOIN permisos p ON p.id_permiso = rp.id_permiso
        WHERE ur.id_usuario = ?
        ");

        $sql->execute([$idUsuario]);

        return $sql->fetchAll(PDO::FETCH_COLUMN);
    }

    protected static function obtener_roles_usuario($idUsuario)
    {
        $sql = self::conectar()->prepare("
        SELECT r.nombre
        FROM usuario_rol ur
        INNER JOIN roles r ON r.id_rol = ur.id_rol
        WHERE ur.id_usuario = ?
        ");

        $sql->execute([$idUsuario]);

        return $sql->fetchAll(PDO::FETCH_COLUMN);
    }
}
