<?php
require_once "mainModel.php";

class ordencompraModelo extends mainModel
{
    /** modelo agregar presupuesto cabecera con presupuesto*/
    protected static function agregar_ocC_modelo1($datos)
    {
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("INSERT INTO orden_compra 
        (idproveedores, presupuestoid, id_usuario, fecha, estado, fecha_entrega)
        VALUES (:proveedor, :presupuestoid, :usuario, NOW(), 1, :fecha_entrega)");

        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->bindParam(":presupuestoid", $datos['presupuestoid']);
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":fecha_entrega", $datos['fecha_entrega']);

        $sql->execute();

        return $conexion->lastInsertId();
    }
    /**fin modelo */
    /** modelo agregar presupuesto cabecera sin presupuesto*/
    protected static function agregar_ocC_modelo2($datos)
    {
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("INSERT INTO orden_compra 
        (idproveedores, id_usuario, fecha, estado, fecha_entrega)
        VALUES (:proveedor, :usuario, NOW(), 1, :fecha_entrega)");

        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":fecha_entrega", $datos['fecha_entrega']);

        $sql->execute();

        return $conexion->lastInsertId();
    }
    /**fin modelo */
    /** modelo agregar presupuesto cabecera con pedido*/
    protected static function agregar_ocD_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("
        INSERT INTO orden_compra_detalle
        (idorden_compra, id_articulo, cantidad, precio_unitario, cantidad_pendiente)
        VALUES (:ocid, :articulo, :cantidad, :precio, :pendiente)");

        $sql->bindParam(":ocid", $datos['ocid']);
        $sql->bindParam(":articulo", $datos['articulo']);
        $sql->bindParam(":cantidad", $datos['cantidad']);
        $sql->bindParam(":precio", $datos['precio']);
        $sql->bindParam(":pendiente", $datos['pendiente']);
        $sql->execute();

        return $sql;
    }
    /**fin modelo */

    /**modelo anular ordencompra */
    protected static function anular_ordencompra_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("UPDATE orden_compra
        SET estado=0, updatedby=:updatedby, updated=now()
        WHERE idorden_compra=:idorden_compra");
        $sql->bindParam(":updatedby", $datos['updatedby']);
        $sql->bindParam(":idorden_compra", $datos['idorden_compra']);
        $sql->execute();
        return $sql;
    }
    /**fin modelo */
}
