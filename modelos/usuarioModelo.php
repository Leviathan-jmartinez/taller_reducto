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

    protected static function listar_roles_modelo()
    {
        return self::conectar()
            ->query("SELECT id_rol, nombre FROM roles WHERE estado = 1")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function asignar_rol_modelo($idUsuario, $idRol)
    {
        $sql = self::conectar()->prepare("
        UPDATE usuarios
        SET id_rol = ?
        WHERE id_usuario = ?
        ");
        return $sql->execute([$idRol, $idUsuario]);
    }

    protected static function obtener_permisos_rol_modelo($idRol)
    {
        $sql = self::conectar()->prepare("
        SELECT p.id_permiso, p.clave,
               IF(rp.id_permiso IS NULL, 0, 1) AS activo
        FROM permisos p
        LEFT JOIN rol_permiso rp
            ON rp.id_permiso = p.id_permiso
           AND rp.id_rol = ?
        ORDER BY p.clave
      ");
        $sql->execute([$idRol]);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
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

    protected static function permisos_por_rol_modelo($idRol)
    {
        $sql = self::conectar()->prepare("
        SELECT 
            p.id_permiso,
            p.clave,
            p.descripcion,
            IF(rp.id_permiso IS NULL, 0, 1) AS activo
        FROM permisos p
        LEFT JOIN rol_permiso rp 
            ON rp.id_permiso = p.id_permiso
           AND rp.id_rol = ?
        ORDER BY p.clave ASC
        ");
        $sql->execute([$idRol]);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function guardar_permisos_rol_modelo($idRol, $permisos)
    {
        $pdo = self::conectar();
        $pdo->beginTransaction();

        $pdo->prepare("DELETE FROM rol_permiso WHERE id_rol = ?")
            ->execute([$idRol]);

        $stmt = $pdo->prepare("
        INSERT INTO rol_permiso (id_rol, id_permiso)
        VALUES (?, ?)
        ");

        foreach ($permisos as $idPermiso) {
            $stmt->execute([$idRol, $idPermiso]);
        }

        $pdo->commit();
        return true;
    }

    protected static function asignar_sucursal_modelo($idUsuario, $idSucursal)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE usuarios
        SET sucursalid = :sucursal
        WHERE id_usuario = :usuario
        ");

        return $sql->execute([
            ':sucursal' => $idSucursal,
            ':usuario'  => $idUsuario
        ]);
    }
}
