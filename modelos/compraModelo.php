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
}
