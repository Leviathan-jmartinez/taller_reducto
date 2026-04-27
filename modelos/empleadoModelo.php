<?php
require_once "mainModel.php";

class empleadoModelo extends mainModel
{
    /* ========= DATOS EMPLEADO ========= */
    protected static function datos_empleado_modelo($tipo, $id)
    {
        if ($tipo === "Unico") {
            $sql = mainModel::conectar()->prepare(
                "SELECT * FROM empleados WHERE idempleados = :id"
            );
            $sql->bindParam(":id", $id);
        } elseif ($tipo === "Conteo") {
            $sql = mainModel::conectar()->prepare(
                "SELECT idempleados FROM empleados WHERE estado = 1"
            );
        }
        $sql->execute();
        return $sql;
    }

    /* ========= LISTAS ========= */
    protected static function obtener_cargos_modelo()
    {
        $sql = mainModel::conectar()->prepare(
            "SELECT idcargos, descripcion FROM cargos WHERE estado = 1 ORDER BY descripcion"
        );
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function obtener_sucursales_modelo()
    {
        $sql = mainModel::conectar()->prepare(
            "SELECT id_sucursal, suc_descri FROM sucursales WHERE estado = 1 ORDER BY suc_descri"
        );
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ========= AGREGAR ========= */
    protected static function agregar_empleado_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "INSERT INTO empleados
            (idcargos, id_sucursal, nombre, apellido, direccion, celular,
            nro_cedula, estado_civil, estado)
            VALUES
            (:cargo, :sucursal, :nombre, :apellido, :direccion, :celular,
            :cedula, :estado_civil, :estado)"
        );

        foreach ($datos as $key => $value) {
            $sql->bindValue(":$key", $value);
        }

        $sql->execute();
        return $sql;
    }

    /* ========= ACTUALIZAR ========= */
    protected static function actualizar_empleado_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "UPDATE empleados SET
                idcargos = :cargo,
                id_sucursal = :sucursal,
                nombre = :nombre,
                apellido = :apellido,
                direccion = :direccion,
                celular = :celular,
                nro_cedula = :cedula,
                estado_civil = :estado_civil,
                estado = :estado
            WHERE idempleados = :id"
        );

        foreach ($datos as $key => $value) {
            $sql->bindValue(":$key", $value);
        }

        $sql->execute();
        return $sql;
    }

    /* ========= ELIMINAR ========= */
    protected static function eliminar_empleado_modelo($id)
    {
        $pdo = mainModel::conectar();

        // 1) Verificar si el empleado ya fue usado
        $check = $pdo->prepare("
        SELECT 1 
        FROM orden_trabajo 
        WHERE tecnico_responsable = :id
        LIMIT 1
        ");
        $check->bindParam(":id", $id, PDO::PARAM_INT);
        $check->execute();

        if ($check->rowCount() > 0) {
            // Ya fue usado → solo desactivar
            $stmt = $pdo->prepare("
            UPDATE empleados 
            SET estado = 0 
            WHERE idempleados = :id
        ");
        } else {
            // No está relacionado → se puede eliminar
            $stmt = $pdo->prepare("
            DELETE FROM empleados 
            WHERE idempleados = :id
        ");
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    protected static function listar_empleados_modelo($inicio, $registros, $filtrosSQL)
    {
        $conexion = mainModel::conectar();

        $sql = "
        SELECT SQL_CALC_FOUND_ROWS e.*,
            c.descripcion AS cargo,
            s.suc_descri AS sucursal
        FROM empleados e
        INNER JOIN cargos c ON c.idcargos = e.idcargos
        INNER JOIN sucursales s ON s.id_sucursal = e.id_sucursal
        WHERE 1=1 $filtrosSQL
        ORDER BY e.apellido ASC
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
}
