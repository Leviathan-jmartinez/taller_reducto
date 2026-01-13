<?php
require_once "mainModel.php";

class sucursalModelo extends mainModel
{
    /** datos sucursal */
    protected static function datos_sucursal_modelo($tipo, $id)
    {
        if ($tipo == "Unico") {
            $sql = mainModel::conectar()->prepare(
                "SELECT * FROM sucursales WHERE id_sucursal = :id"
            );
            $sql->bindParam(":id", $id);
        } elseif ($tipo == "Conteo") {
            $sql = mainModel::conectar()->prepare(
                "SELECT id_sucursal FROM sucursales WHERE estado = 1"
            );
        }
        $sql->execute();
        return $sql;
    }

    /** listar empresas */
    protected static function obtener_empresas_modelo()
    {
        $sql = mainModel::conectar()->prepare(
            "SELECT id_empresa, razon_social FROM empresa ORDER BY razon_social ASC"
        );
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /** agregar sucursal */
    protected static function agregar_sucursal_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "INSERT INTO sucursales
            (id_empresa, suc_descri, suc_direccion, suc_telefono, nro_establecimiento, estado)
            VALUES
            (:id_empresa, :descri, :direccion, :telefono, :nro_est, :estado)"
        );

        $sql->bindParam(":id_empresa", $datos['id_empresa']);
        $sql->bindParam(":descri", $datos['suc_descri']);
        $sql->bindParam(":direccion", $datos['suc_direccion']);
        $sql->bindParam(":telefono", $datos['suc_telefono']);
        $sql->bindParam(":nro_est", $datos['nro_establecimiento']);
        $sql->bindParam(":estado", $datos['estado']);

        $sql->execute();
        return $sql;
    }

    /** actualizar sucursal */
    protected static function actualizar_sucursal_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "UPDATE sucursales SET
                id_empresa = :id_empresa,
                suc_descri = :descri,
                suc_direccion = :direccion,
                suc_telefono = :telefono,
                nro_establecimiento = :nro_est,
                estado = :estado
            WHERE id_sucursal = :id_sucursal"
        );

        $sql->bindParam(":id_empresa", $datos['id_empresa']);
        $sql->bindParam(":descri", $datos['suc_descri']);
        $sql->bindParam(":direccion", $datos['suc_direccion']);
        $sql->bindParam(":telefono", $datos['suc_telefono']);
        $sql->bindParam(":nro_est", $datos['nro_establecimiento']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":id_sucursal", $datos['id_sucursal']);

        $sql->execute();
        return $sql;
    }

    /** eliminar sucursal */
    protected static function eliminar_sucursal_modelo($id)
    {
        $pdo = mainModel::conectar();

        // 1) Verificar si la sucursal está siendo usada
        $check = $pdo->prepare("
        SELECT 1 
        FROM empleados 
        WHERE id_sucursal = :id 
        LIMIT 1
        ");
        $check->bindParam(":id", $id, PDO::PARAM_INT);
        $check->execute();

        if ($check->rowCount() > 0) {
            // Ya fue usada → solo desactivar
            $stmt = $pdo->prepare("
            UPDATE sucursales 
            SET estado = 0 
            WHERE id_sucursal = :id
        ");
        } else {
            // No está relacionada → se puede eliminar
            $stmt = $pdo->prepare("
            DELETE FROM sucursales 
            WHERE id_sucursal = :id
        ");
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }


    protected static function listar_sucursales_modelo()
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            id_sucursal,
            suc_descri
        FROM sucursales
        WHERE estado = 1
        ORDER BY suc_descri ASC
        ");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function listar_empleados_modelo()
    {
        $sql = mainModel::conectar()->prepare("
        SELECT ee.id_equipo, CONCAT(et.nombre,' - ',et.descripcion) as equipo ,e.idempleados, CONCAT(e.nombre,' ',e.apellido) AS nombre
        FROM equipo_empleado ee
        INNER JOIN empleados e ON e.idempleados = ee.idempleados
        INNER JOIN equipo_trabajo et ON et.id_equipo = ee.id_equipo
        WHERE ee.estado = 1 AND e.estado = 1
        ORDER BY nombre ASC
        ");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
