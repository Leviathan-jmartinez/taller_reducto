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

    /* ===== EMPLEADOS DISPONIBLES ===== */
    protected static function empleados_disponibles_modelo($id_sucursal)
    {
        $sql = mainModel::conectar()->prepare(
            "SELECT e.*
             FROM empleados e
             WHERE e.estado = 1
             AND e.id_sucursal = :sucursal
             ORDER BY e.apellido"
        );
        $sql->bindParam(":sucursal", $id_sucursal);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
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

    protected static function empleados_con_equipo_modelo($id_sucursal)
    {
        $sql = mainModel::conectar()->prepare("
            SELECT 
                e.idempleados,
                e.nombre,
                e.apellido,
                GROUP_CONCAT(et.nombre ORDER BY et.nombre SEPARATOR ' | ') AS equipos
            FROM empleados e
            LEFT JOIN equipo_empleado ee
                ON ee.idempleados = e.idempleados
                AND ee.estado = 1
            LEFT JOIN equipo_trabajo et
                ON et.id_equipo = ee.id_equipo
                AND et.estado = 1
            WHERE e.estado = 1
            AND e.id_sucursal = :sucursal
            GROUP BY e.idempleados, e.nombre, e.apellido
            ORDER BY e.apellido, e.nombre
        ");

        $sql->bindParam(":sucursal", $id_sucursal);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===== ELIMINAR (ANULAR) EQUIPO ===== */
    protected static function eliminar_equipo_modelo($id_equipo)
    {
        $sql = mainModel::conectar()->prepare(
            "UPDATE equipo_trabajo
         SET estado = 0
         WHERE id_equipo = :id"
        );
        $sql->bindParam(":id", $id_equipo);
        $sql->execute();
        return $sql;
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
