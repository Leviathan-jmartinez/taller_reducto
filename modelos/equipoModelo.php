<?php
require_once "mainModel.php";

class equipoModelo extends mainModel
{
    /* ===== LISTAR EQUIPOS ===== */
    protected static function listar_equipos_modelo()
    {
        $sql = mainModel::conectar()->prepare(
            "SELECT e.*, s.suc_descri
             FROM equipo_trabajo e
             INNER JOIN sucursales s ON s.id_sucursal = e.id_sucursal
             WHERE e.estado = 1
             ORDER BY e.nombre"
        );
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===== CREAR EQUIPO ===== */
    protected static function agregar_equipo_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "INSERT INTO equipo_trabajo
            (id_sucursal, nombre, descripcion, estado)
            VALUES (:sucursal, :nombre, :descripcion, 1)"
        );

        $sql->bindParam(":sucursal", $datos['sucursal']);
        $sql->bindParam(":nombre", $datos['nombre']);
        $sql->bindParam(":descripcion", $datos['descripcion']);
        $sql->execute();

        return mainModel::conectar()->lastInsertId();
    }

    /* ===== DATOS EQUIPO ===== */
    protected static function datos_equipo_modelo($id_equipo)
    {
        $sql = mainModel::conectar()->prepare(
            "SELECT id_equipo, id_sucursal, nombre, descripcion, estado
             FROM equipo_trabajo
             WHERE id_equipo = :id
             LIMIT 1"
        );
        $sql->bindParam(":id", $id_equipo, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /* ===== ACTUALIZAR EQUIPO ===== */
    protected static function actualizar_equipo_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "UPDATE equipo_trabajo
             SET id_sucursal = :sucursal,
                 nombre = :nombre,
                 descripcion = :descripcion
             WHERE id_equipo = :id
               AND estado = 1"
        );

        $sql->bindParam(":sucursal", $datos['sucursal']);
        $sql->bindParam(":nombre", $datos['nombre']);
        $sql->bindParam(":descripcion", $datos['descripcion']);
        $sql->bindParam(":id", $datos['id_equipo'], PDO::PARAM_INT);
        $sql->execute();
        return $sql;
    }

    /* ===== ASIGNAR EMPLEADO ===== */
    protected static function asignar_empleado_equipo_modelo($id_equipo, $id_empleado, $rol)
    {
        $sql = mainModel::conectar()->prepare(
            "INSERT INTO equipo_empleado
        (id_equipo, idempleados, rol, estado)
        VALUES (:equipo, :empleado, :rol, 1)
        ON DUPLICATE KEY UPDATE
            estado = 1,
            rol = VALUES(rol)"
        );

        $sql->bindParam(":equipo", $id_equipo);
        $sql->bindParam(":empleado", $id_empleado);
        $sql->bindParam(":rol", $rol);
        $sql->execute();
    }

    /* ===== OBTENER MIEMBROS ===== */
    protected static function miembros_equipo_modelo($id_equipo)
    {
        $sql = mainModel::conectar()->prepare(
            "SELECT ee.*, e.nombre, e.apellido
             FROM equipo_empleado ee
             INNER JOIN empleados e ON e.idempleados = ee.idempleados
             WHERE ee.id_equipo = :equipo AND ee.estado = 1"
        );
        $sql->bindParam(":equipo", $id_equipo);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function empleados_asignacion_equipo_modelo($id_equipo)
    {
        $equipo = self::datos_equipo_modelo($id_equipo);
        if (!$equipo || (int)$equipo['estado'] !== 1) {
            return false;
        }

        $sql = mainModel::conectar()->prepare("
            SELECT
                e.idempleados,
                e.nombre,
                e.apellido,
                e.nro_cedula,
                CASE WHEN ee_actual.idempleados IS NULL THEN 0 ELSE 1 END AS es_miembro,
                GROUP_CONCAT(et.nombre ORDER BY et.nombre SEPARATOR ' | ') AS equipos
            FROM empleados e
            LEFT JOIN equipo_empleado ee_actual
                ON ee_actual.idempleados = e.idempleados
                AND ee_actual.id_equipo = :equipo_actual
                AND ee_actual.estado = 1
            LEFT JOIN equipo_empleado ee
                ON ee.idempleados = e.idempleados
                AND ee.estado = 1
            LEFT JOIN equipo_trabajo et
                ON et.id_equipo = ee.id_equipo
                AND et.estado = 1
            WHERE e.estado = 1
            AND e.id_sucursal = :sucursal
            GROUP BY e.idempleados, e.nombre, e.apellido, e.nro_cedula, ee_actual.idempleados
            ORDER BY e.apellido, e.nombre
        ");
        $sql->bindParam(":equipo_actual", $id_equipo, PDO::PARAM_INT);
        $sql->bindParam(":sucursal", $equipo['id_sucursal'], PDO::PARAM_INT);
        $sql->execute();

        return [
            "equipo" => $equipo,
            "empleados" => $sql->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    /* ===== ELIMINAR (ANULAR) EQUIPO ===== */
    protected static function eliminar_equipo_modelo($id_equipo)
    {
        $conexion = mainModel::conectar();

        $sql_relacion = $conexion->prepare(
            "SELECT id_equipo
             FROM equipo_empleado
             WHERE id_equipo = :id
             LIMIT 1"
        );
        $sql_relacion->bindParam(":id", $id_equipo, PDO::PARAM_INT);
        $sql_relacion->execute();

        if ($sql_relacion->rowCount() > 0) {
            $sql_equipo = $conexion->prepare(
                "UPDATE equipo_trabajo
                 SET estado = 0
                 WHERE id_equipo = :id"
            );
            $accion = "inactivado";
        } else {
            $sql_equipo = $conexion->prepare(
                "DELETE FROM equipo_trabajo
                 WHERE id_equipo = :id"
            );
            $accion = "eliminado";
        }

        $sql_equipo->bindParam(":id", $id_equipo, PDO::PARAM_INT);
        try {
            $ejecutado = $sql_equipo->execute();
        } catch (PDOException $e) {
            $sql_equipo = $conexion->prepare(
                "UPDATE equipo_trabajo
                 SET estado = 0
                 WHERE id_equipo = :id"
            );
            $sql_equipo->bindParam(":id", $id_equipo, PDO::PARAM_INT);
            $ejecutado = $sql_equipo->execute();
            $accion = "inactivado";
        }

        if ($ejecutado && $sql_equipo->rowCount() > 0) {
            return [
                "ok" => true,
                "accion" => $accion
            ];
        }

        $sql_equipo = $conexion->prepare(
            "UPDATE equipo_trabajo
             SET estado = 0
             WHERE id_equipo = :id"
        );
        $sql_equipo->bindParam(":id", $id_equipo, PDO::PARAM_INT);

        $ok = $sql_equipo->execute() && $sql_equipo->rowCount() > 0;
        if (!$ok) {
            $verificar = $conexion->prepare("SELECT estado FROM equipo_trabajo WHERE id_equipo = :id");
            $verificar->bindParam(":id", $id_equipo, PDO::PARAM_INT);
            $verificar->execute();
            $ok = ($verificar->rowCount() > 0 && (int)$verificar->fetchColumn() === 0);
        }

        return [
            "ok" => $ok,
            "accion" => "inactivado"
        ];
    }

    /* ===== QUITAR MIEMBRO DE EQUIPO ===== */
    protected static function quitar_miembro_modelo($id_equipo, $id_empleado)
    {
        $sql = mainModel::conectar()->prepare(
            "UPDATE equipo_empleado
         SET estado = 0
         WHERE id_equipo = :equipo
         AND idempleados = :empleado"
        );
        $sql->bindParam(":equipo", $id_equipo);
        $sql->bindParam(":empleado", $id_empleado);
        $sql->execute();
        return $sql;
    }
}
