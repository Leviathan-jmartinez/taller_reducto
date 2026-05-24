<?php
require_once "mainModel.php";

class remisionModelo extends mainModel
{
    /**
     * Guardar una nueva nota de remisión
     */
    public static function guardar_remision_modelo($datos)
    {
        try {
            $conexion = mainModel::conectar();
            $sql = $conexion->prepare("
            INSERT INTO nota_remision 
            (idcompra_cabecera, id_usuario, id_sucursal,fecha_emision, nro_remision, nombre_transpo, ci_transpo, cel_transpo, transportista, ruc_transport, vehimarca, vehimodelo, vehichapa, fechaenvio, fechallegada, motivo_remision, estado, tipo)
            VALUES
            (:idcompra_cabecera, :id_usuario, :id_sucursal, :fecha_emision, :nro_remision, :nombre_transpo, :ci_transpo, :cel_transpo, :transportista, :ruc_transport, :vehimarca, :vehimodelo, :vehichapa, :fechaenvio, :fechallegada, :motivo_remision, :estado, 'recepcion compra')
        ");

            $sql->bindParam(":idcompra_cabecera", $datos['idcompra_cabecera'], PDO::PARAM_INT);
            $sql->bindParam(":id_usuario", $datos['id_usuario'], PDO::PARAM_INT);
            $sql->bindParam(":id_sucursal", $datos['id_sucursal'], PDO::PARAM_INT);
            $sql->bindParam(":fecha_emision", $datos['fecha_emision']);
            $sql->bindParam(":nro_remision", $datos['nro_remision']);
            $sql->bindParam(":nombre_transpo", $datos['nombre_transpo']);
            $sql->bindParam(":ci_transpo", $datos['ci_transpo']);
            $sql->bindParam(":cel_transpo", $datos['cel_transpo']);
            $sql->bindParam(":transportista", $datos['transportista']);
            $sql->bindParam(":ruc_transport", $datos['ruc_transport']);
            $sql->bindParam(":vehimarca", $datos['vehimarca']);
            $sql->bindParam(":vehimodelo", $datos['vehimodelo']);
            $sql->bindParam(":vehichapa", $datos['vehichapa']);
            $sql->bindParam(":fechaenvio", $datos['fechaenvio']);
            $sql->bindParam(":fechallegada", $datos['fechallegada']);
            $sql->bindParam(":motivo_remision", $datos['motivo_remision']);
            $sql->bindParam(":estado", $datos['estado'], PDO::PARAM_INT);

            $sql->execute();
            return $conexion->lastInsertId(); // 🔹 usar la misma conexión
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    /**
     * Guardar detalle de remisión
     */
    protected static function guardar_remision_detalle_modelo($idnota, $detalle)
    {
        $sql = mainModel::conectar()->prepare("
            INSERT INTO nota_remision_detalle (
                idnota_remision,
                id_articulo,
                cantidad,
                costo,
                subtotal
            ) VALUES (
                :idnota_remision,
                :id_articulo,
                :cantidad,
                :costo,
                :subtotal
            )
        ");

        foreach ($detalle as $item) {
            $sql->bindValue(":idnota_remision", $idnota);
            $sql->bindValue(":id_articulo", $item['id_articulo']);
            $sql->bindValue(":cantidad", $item['cantidad']);
            $sql->bindValue(":costo", $item['costo']);
            $sql->bindValue(":subtotal", $item['subtotal']);
            $sql->execute();
        }

        return true;
    }

    /**
     * Anular remisión
     */
    protected static function anular_remision_modelo($id, $usuario, $id_sucursal)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE nota_remision
        SET estado = 0,
            updated = NOW(),
            updatedby = :usuario
        WHERE idnota_remision = :id and id_sucursal = :id_sucursal");

        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->bindParam(":usuario", $usuario, PDO::PARAM_INT);
        $sql->bindParam(":id_sucursal", $id_sucursal, PDO::PARAM_INT);
        return $sql->execute();
    }

    protected static function listar_remisiones_modelo($inicio, $registros, $filtrosSQL, $orderSQL = "ORDER BY r.idnota_remision DESC")
    {
        $conexion = mainModel::conectar();
        $id_sucursal = mainModel::limpiar_string($_SESSION['nick_sucursal']);

        $baseSQL = "
        FROM nota_remision r
        INNER JOIN usuarios u ON u.id_usuario = r.id_usuario
        LEFT JOIN compra_cabecera cc ON cc.idcompra_cabecera = r.idcompra_cabecera
        WHERE r.id_sucursal = '$id_sucursal'
        $filtrosSQL";

        $selectSQL = "
        SELECT
            r.idnota_remision,
            r.id_sucursal,
            r.id_usuario,
            r.fecha_emision,
            r.nro_remision,
            r.nombre_transpo,
            r.motivo_remision,
            r.estado,
            cc.nro_factura,
            u.usu_nombre,
            u.usu_apellido
        ";

        return mainModel::ejecutarPaginador(
            $conexion,
            $baseSQL,
            $selectSQL,
            $orderSQL,
            $inicio,
            $registros
        );
    }


    protected static function obtener_remision_modelo($idnota, $id_sucursal)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT * 
        FROM nota_remision
        WHERE idnota_remision = :id and id_sucursal = :id_sucursal
        LIMIT 1");
        $sql->bindParam(":id", $idnota, PDO::PARAM_INT);
        $sql->bindParam(":id_sucursal", $id_sucursal, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
}
