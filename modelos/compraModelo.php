<?php
require_once "mainModel.php";

class compraModelo extends mainModel
{
    /* ===============================
       INSERTAR COMPRA CABECERA
    ================================= */
    protected static function insertar_compra_cabecera_modelo($datos)
    {
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("
            INSERT INTO compra_cabecera
            (idproveedores, id_usuario, fecha, nro_factura, fecha_factura, nro_timbrado, vencimiento_timbrado, estado, total_compra, condicion, compra_intervalo, idOcompra)
            VALUES (:proveedor, :usuario, NOW(), :nro_factura, :fecha_factura, :timbrado, :vto_timbrado, :estado, :total, :condicion, :intervalo, :idoc)
        ");

        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":nro_factura", $datos['nro_factura']);
        $sql->bindParam(":fecha_factura", $datos['fecha_factura']);
        $sql->bindParam(":timbrado", $datos['timbrado']);
        $sql->bindParam(":vto_timbrado", $datos['vencimiento_timbrado']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":total", $datos['total']);
        $sql->bindParam(":condicion", $datos['condicion']);
        $sql->bindParam(":intervalo", $datos['intervalo']);
        $sql->bindParam(":idoc", $datos['idoc']);

        $sql->execute();
        return [
            "stmt" => $sql,
            "conexion" => $conexion,
            "last_id" => $conexion->lastInsertId()
        ];
    }

    /* ===============================
       INSERTAR COMPRA DETALLE
    ================================= */
    protected static function insertar_compra_detalle_modelo($detalle)
    {
        $sql = mainModel::conectar()->prepare("
            INSERT INTO compra_detalle
            (idcompra_cabecera, id_articulo, precio_unitario, cantidad_recibida, subtotal, ivaPro)
            VALUES (:idcab, :articulo, :precio, :cantidad, :subtotal, :iva)
        ");

        $sql->bindParam(":idcab", $detalle['idcab']);
        $sql->bindParam(":articulo", $detalle['id_articulo']);
        $sql->bindParam(":precio", $detalle['precio']);
        $sql->bindParam(":cantidad", $detalle['cantidad']);
        $sql->bindParam(":subtotal", $detalle['subtotal']);
        $sql->bindParam(":iva", $detalle['iva']);

        $sql->execute();
        return $sql;
    }

    protected function upsert_stock_modelo($datos)
    {

        $sql = "INSERT INTO stock (
                iddeposito,
                id_articulo,
                stockcant_max,
                stockcant_min,
                stockDisponible,
                stockUltActualizacion,
                stockUsuActualizacion,
                stockultimoIdActualizacion
            ) VALUES (
                :iddeposito,
                :id_articulo,
                200,
                15,
                :stockDisponible,
                :stockUltActualizacion,
                :stockUsuActualizacion,
                :stockultimoIdActualizacion
            )
            ON DUPLICATE KEY UPDATE
                stockDisponible = VALUES(stockDisponible),
                stockUltActualizacion = VALUES(stockUltActualizacion),
                stockUsuActualizacion = VALUES(stockUsuActualizacion),
                stockultimoIdActualizacion = VALUES(stockultimoIdActualizacion),
                stockcant_max = 200,
                stockcant_min = 15";

        $conexion = mainModel::conectar();
        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(":iddeposito", $datos['iddeposito'], PDO::PARAM_INT);
        $stmt->bindParam(":id_articulo", $datos['id_articulo'], PDO::PARAM_INT);
        $stmt->bindParam(":stockDisponible", $datos['stockDisponible']);
        $stmt->bindParam(":stockUltActualizacion", $datos['stockUltActualizacion']);
        $stmt->bindParam(":stockUsuActualizacion", $datos['stockUsuActualizacion'], PDO::PARAM_INT);
        $stmt->bindParam(":stockultimoIdActualizacion", $datos['stockultimoIdActualizacion'], PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }

    protected function obtener_stock_actual_modelo($iddeposito, $id_articulo)
{
    $sql = "SELECT stockDisponible 
            FROM stock 
            WHERE iddeposito = :iddeposito 
              AND id_articulo = :id_articulo 
            LIMIT 1";

    $conexion = mainModel::conectar();
    $stmt = $conexion->prepare($sql);

    $stmt->bindParam(":iddeposito", $iddeposito, PDO::PARAM_INT);
    $stmt->bindParam(":id_articulo", $id_articulo, PDO::PARAM_INT);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return floatval($data["stockDisponible"]);
    }

    // No existe -> stock = 0
    return 0;
}

}
