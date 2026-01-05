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
             nro_cedula, estado_civil, empleado_estado, estado)
            VALUES
            (:cargo, :sucursal, :nombre, :apellido, :direccion, :celular,
             :cedula, :estado_civil, :empleado_estado, :estado)"
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
                empleado_estado = :empleado_estado,
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
        $sql = mainModel::conectar()->prepare(
            "DELETE FROM empleados WHERE idempleados = :id"
        );
        $sql->bindParam(":id", $id);
        $sql->execute();
        return $sql;
    }
}
