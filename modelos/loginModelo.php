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

    protected static function obtener_usuario_login_modelo($usuario)
    {
        $sql = mainModel::conectar()->prepare("SELECT * FROM usuarios WHERE usu_nick = :Usuario LIMIT 1");
        $sql->bindParam(":Usuario", $usuario);
        $sql->execute();
        return $sql;
    }

    protected static function registrar_intento_fallido_modelo($idUsuario)
    {
        $conexion = mainModel::conectar();

        $sql = $conexion->prepare("
        UPDATE usuarios
        SET usu_intentos_fallidos = COALESCE(usu_intentos_fallidos, 0) + 1
        WHERE id_usuario = :Usuario
        ");
        $sql->bindParam(":Usuario", $idUsuario);
        $sql->execute();

        $sql = $conexion->prepare("
        UPDATE usuarios
        SET usu_bloqueado = 1
        WHERE id_usuario = :Usuario
        AND usu_intentos_fallidos >= 3
        ");
        $sql->bindParam(":Usuario", $idUsuario);
        $sql->execute();

        $sql = $conexion->prepare("
        SELECT usu_intentos_fallidos, usu_bloqueado
        FROM usuarios
        WHERE id_usuario = :Usuario
        ");
        $sql->bindParam(":Usuario", $idUsuario);
        $sql->execute();

        $estado = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$estado) {
            return [
                "usu_intentos_fallidos" => 0,
                "usu_bloqueado" => 0
            ];
        }

        return $estado;
    }

    protected static function reiniciar_intentos_login_modelo($idUsuario)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE usuarios
        SET usu_intentos_fallidos = 0
        WHERE id_usuario = :Usuario
        ");
        $sql->bindParam(":Usuario", $idUsuario);
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
