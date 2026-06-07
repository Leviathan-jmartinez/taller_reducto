<?php
require_once "mainModel.php";

class proveedorModelo extends mainModel
{
    /** Datos proveedor */
    protected static function datos_proveedor_modelo($tipo, $id)
    {
        if ($tipo == "Unico") {
            $sql = mainModel::conectar()->prepare(
                "SELECT * FROM proveedores WHERE idproveedores = :id"
            );
            $sql->bindParam(":id", $id);
        } elseif ($tipo == "Conteo") {
            $sql = mainModel::conectar()->prepare(
                "SELECT idproveedores FROM proveedores WHERE estado = 1"
            );
        }
        $sql->execute();
        return $sql;
    }

    /** Listar ciudades */
    protected static function obtener_ciudades_modelo()
    {
        $sql = mainModel::conectar()->prepare(
            "SELECT id_ciudad, ciu_descri FROM ciudades ORDER BY ciu_descri ASC"
        );
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Agregar proveedor */
    protected static function agregar_proveedor_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "INSERT INTO proveedores
            (id_ciudad, razon_social, ruc, telefono, direccion, correo, estado)
            VALUES
            (:id_ciudad, :razon_social, :ruc, :telefono, :direccion, :correo, :estado)"
        );

        $sql->bindParam(":id_ciudad", $datos['id_ciudad']);
        $sql->bindParam(":razon_social", $datos['razon_social']);
        $sql->bindParam(":ruc", $datos['ruc']);
        $sql->bindParam(":telefono", $datos['telefono']);
        $sql->bindParam(":direccion", $datos['direccion']);
        $sql->bindParam(":correo", $datos['correo']);
        $sql->bindParam(":estado", $datos['estado']);

        $sql->execute();
        return $sql;
    }

    /** Eliminar proveedor */
    protected static function eliminar_proveedor_modelo($id)
    {
        $pdo = mainModel::conectar();


        $check = $pdo->prepare("
            SELECT 1
            WHERE EXISTS (SELECT 1 FROM articulo_proveedor WHERE idproveedores = ?)
               OR EXISTS (SELECT 1 FROM presupuesto_compra WHERE idproveedores = ?)
               OR EXISTS (SELECT 1 FROM orden_compra WHERE idproveedores = ?)
               OR EXISTS (SELECT 1 FROM compra_cabecera WHERE idproveedores = ?)
               OR EXISTS (SELECT 1 FROM libro_compra WHERE idproveedores = ?)
        ");

        for ($i = 1; $i <= 5; $i++) {
            $check->bindValue($i, (int)$id, PDO::PARAM_INT);
        }
        $check->execute();

        if ($check->rowCount() > 0) {


            $stmt = $pdo->prepare("
            UPDATE proveedores 
            SET estado = 0 
            WHERE idproveedores = :id
        ");
        } else {


            $stmt = $pdo->prepare("
            DELETE FROM proveedores 
            WHERE idproveedores = :id
        ");
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($stmt->queryString && stripos($stmt->queryString, 'DELETE FROM proveedores') !== false) {
                $stmt = $pdo->prepare("
                    UPDATE proveedores
                    SET estado = 0
                    WHERE idproveedores = :id
                ");
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                throw $e;
            }
        }

        return $stmt;
    }

    /** Actualizar proveedor */
    protected static function actualizar_proveedor_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "UPDATE proveedores SET
                id_ciudad = :id_ciudad,
                razon_social = :razon_social,
                ruc = :ruc,
                telefono = :telefono,
                direccion = :direccion,
                correo = :correo,
                estado = :estado
            WHERE idproveedores = :idproveedores"
        );

        $sql->bindParam(":id_ciudad", $datos['id_ciudad']);
        $sql->bindParam(":razon_social", $datos['razon_social']);
        $sql->bindParam(":ruc", $datos['ruc']);
        $sql->bindParam(":telefono", $datos['telefono']);
        $sql->bindParam(":direccion", $datos['direccion']);
        $sql->bindParam(":correo", $datos['correo']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":idproveedores", $datos['idproveedores']);

        $sql->execute();
        return $sql;
    }

    protected static function listar_proveedores_modelo($inicio, $registros, $filtrosSQL)
    {
        $conexion = mainModel::conectar();

        $sql = "SELECT SQL_CALC_FOUND_ROWS p.*, c.ciu_descri
            FROM proveedores p
            INNER JOIN ciudades c ON c.id_ciudad = p.id_ciudad
            WHERE 1=1 $filtrosSQL
            ORDER BY p.razon_social ASC
            LIMIT :inicio, :registros";

        $stmt = $conexion->prepare($sql);

        $stmt->bindValue(":inicio", (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(":registros", (int)$registros, PDO::PARAM_INT);

        $stmt->execute();

        $datos = $stmt->fetchAll();
        $total = $conexion->query("SELECT FOUND_ROWS()")->fetchColumn();

        return [
            "datos" => $datos,
            "total" => (int)$total
        ];
    }
}
