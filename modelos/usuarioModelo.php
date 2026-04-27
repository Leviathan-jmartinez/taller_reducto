<?php
require_once "mainModel.php";

class usuarioModelo extends mainModel
{
    /** modelo agregar usuario*/
    protected static function agregar_usuario_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("INSERT INTO usuarios (usu_nombre, usu_clave, usu_estado, usu_nick, usu_apellido, usu_email, usu_telefono,usu_ci) 
        VALUES (:nombre, :clave,:estado, :nick, :apellido, :email, :telefono,:ci)");
        $sql->bindParam(":ci", $datos['ci']);
        $sql->bindParam(":nombre", $datos['nombre']);
        $sql->bindParam(":clave", $datos['clave']);
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
        $pdo = mainModel::conectar();

        // 1) Verificar si el usuario ya fue usado en el sistema
        $check = $pdo->prepare("
        SELECT 1 
        FROM pedido_cabecera 
        WHERE id_usuario = :id
        LIMIT 1
        ");
        $check->bindParam(":id", $id, PDO::PARAM_INT);
        $check->execute();

        if ($check->rowCount() > 0) {
            // Ya fue usado → solo desactivar
            $stmt = $pdo->prepare("
            UPDATE usuarios 
            SET usu_estado = 0 
            WHERE id_usuario = :id
        ");
        } else {
            // No está relacionado → se puede eliminar
            $stmt = $pdo->prepare("
            DELETE FROM usuarios 
            WHERE id_usuario = :id
        ");
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
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
        $sql = mainModel::conectar()->prepare("UPDATE usuarios SET usu_nombre=:nombre, usu_clave=:clave,
        usu_estado=:estado, usu_nick=:nick, usu_apellido=:apellido, usu_email=:email, usu_telefono=:telefono, usu_ci=:ci where id_usuario=:iduser");
        $sql->bindParam(":ci", $datos['ci']);
        $sql->bindParam(":nombre", $datos['nombre']);
        $sql->bindParam(":clave", $datos['clave']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":nick", $datos['nick']);
        $sql->bindParam(":apellido", $datos['apellido']);
        $sql->bindParam(":email", $datos['email']);
        $sql->bindParam(":telefono", $datos['telefono']);
        $sql->bindParam(":iduser", $datos['iduser']);
        $sql->execute();
        return $sql;
    }

    protected static function listar_usuarios_modelo()
    {
        $sql = self::conectar()->prepare("
        SELECT id_usuario,
               usu_nombre,
               usu_apellido,
               usu_nick
        FROM usuarios
        WHERE usu_estado = 1
        ORDER BY usu_nombre, usu_apellido
        ");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function obtener_roles_usuario_modelo($idUsuario)
    {
        $sql = self::conectar()->prepare("
        SELECT r.id_rol, r.nombre,
               IF(ur.id_rol IS NULL, 0, 1) AS activo
        FROM roles r
        LEFT JOIN usuario_rol ur
            ON ur.id_rol = r.id_rol
           AND ur.id_usuario = ?
        ORDER BY r.nombre
        ");

        $sql->execute([$idUsuario]);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function guardar_roles_usuario_modelo($idUsuario, $roles)
    {
        $pdo = self::conectar();

        try {

            $pdo->beginTransaction();

            $pdo->prepare("DELETE FROM usuario_rol WHERE id_usuario = ?")
                ->execute([$idUsuario]);

            $stmt = $pdo->prepare("
            INSERT INTO usuario_rol (id_usuario, id_rol)
            VALUES (?, ?)
        ");

            foreach ($roles as $idRol) {
                $stmt->execute([$idUsuario, $idRol]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {

            $pdo->rollBack();
            return false;
        }
    }

    protected static function obtener_sucursal_usuario_modelo($idUsuario)
    {
        $pdo = self::conectar();

        $actual = $pdo->prepare("
        SELECT sucursalid FROM usuarios WHERE id_usuario = ?
        ");
        $actual->execute([$idUsuario]);
        $actual = $actual->fetchColumn();

        $sucursales = $pdo->query("
        SELECT id_sucursal, suc_descri
        FROM sucursales
        WHERE estado = 1
        ORDER BY suc_descri
        ")->fetchAll(PDO::FETCH_ASSOC);

        return [
            "actual" => $actual,
            "sucursales" => $sucursales
        ];
    }

    protected static function guardar_sucursal_usuario_modelo($idUsuario, $idSucursal)
    {
        $sql = self::conectar()->prepare("
        UPDATE usuarios
        SET sucursalid = ?
        WHERE id_usuario = ?
        ");

        $sql->execute([$idSucursal, $idUsuario]);

        return $sql->rowCount();
    }
}
