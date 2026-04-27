<?php
require_once "mainModel.php";

class rolesModelo extends mainModel
{
    /* ========= AGREGAR ========= */
    protected static function agregar_roles_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("
            INSERT INTO roles (nombre, descripcion, estado)
            VALUES (:nombre, :descripcion, :estado)
        ");

        $sql->bindParam(":nombre", $datos['nombre']);
        $sql->bindParam(":descripcion", $datos['descripcion']);
        $sql->bindParam(":estado", $datos['estado']);

        $sql->execute();
        return $sql;
    }

    /* ========= DATOS ========= */
    protected static function datos_roles_modelo($tipo, $id)
    {
        if ($tipo == "Unico") {

            $sql = mainModel::conectar()->prepare("
                SELECT * FROM roles 
                WHERE id_rol = :id
            ");

            $sql->bindParam(":id", $id);
        } elseif ($tipo == "Conteo") {

            $sql = mainModel::conectar()->prepare("
                SELECT id_rol FROM roles 
                WHERE estado = 1
            ");
        }

        $sql->execute();
        return $sql;
    }

    /* ========= LISTAR ========= */
    protected static function listar_roles_modelo($inicio, $registros, $filtrosSQL)
    {
        $conexion = mainModel::conectar();

        $sql = "
        SELECT SQL_CALC_FOUND_ROWS *
        FROM roles
        WHERE 1=1 $filtrosSQL
        ORDER BY nombre ASC
        LIMIT :inicio, :registros
        ";

        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(":inicio", (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(":registros", (int)$registros, PDO::PARAM_INT);

        $stmt->execute();

        $datos = $stmt->fetchAll();
        $total = (int)$conexion->query("SELECT FOUND_ROWS()")->fetchColumn();

        return [
            "datos" => $datos,
            "total" => $total
        ];
    }

    /* ========= ACTUALIZAR ========= */
    protected static function actualizar_roles_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("
            UPDATE roles SET
                nombre = :nombre,
                descripcion = :descripcion,
                estado = :estado
            WHERE id_rol = :id
        ");

        $sql->bindParam(":nombre", $datos['nombre']);
        $sql->bindParam(":descripcion", $datos['descripcion']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":id", $datos['id']);

        $sql->execute();
        return $sql;
    }

    /* ========= ELIMINAR ========= */
    protected static function eliminar_roles_modelo($id)
    {
        $pdo = mainModel::conectar();

        // 🔥 Si querés lógica futura (ej: rol en uso), acá se valida
        // Por ahora elimina directo

        $sql = $pdo->prepare("
            DELETE FROM roles
            WHERE id_rol = :id
        ");

        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();

        return $sql;
    }

    /* ========= PERMISOS POR ROL ========= */
    protected static function obtener_permisos_rol_modelo($idRol)
    {
        $sql = self::conectar()->prepare("
        SELECT p.id_permiso,
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

        try {

            $pdo->beginTransaction();

            // 1. borrar permisos actuales
            $pdo->prepare("DELETE FROM rol_permiso WHERE id_rol = ?")
                ->execute([$idRol]);

            // 2. insertar nuevos
            $stmt = $pdo->prepare("
            INSERT INTO rol_permiso (id_rol, id_permiso)
            VALUES (?, ?)
        ");

            foreach ($permisos as $idPermiso) {
                $stmt->execute([$idRol, $idPermiso]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {

            $pdo->rollBack();
            return false;
        }
    }

    protected static function listar_roles_modeloSelect()
    {
        return self::conectar()
            ->query("SELECT id_rol, nombre FROM roles WHERE estado = 1")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
