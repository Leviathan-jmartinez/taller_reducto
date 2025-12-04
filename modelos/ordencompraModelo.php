<?php
require_once "mainModel.php";

class ordencompraModelo extends mainModel
{
    /** modelo agregar presupuesto cabecera con pedido*/
    protected static function agregar_ocC_modelo($datos)
    {
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("INSERT INTO orden_compra 
        (idproveedores, id_usuario, fecha, estado, fecha_entrega)
        VALUES (:proveedor, :usuario, NOW(), 1, NULL)");

        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->bindParam(":usuario", $datos['usuario']);

        $sql->execute();

        return $conexion->lastInsertId();
    }
    /**fin modelo */
    /** modelo agregar presupuesto cabecera con pedido*/
    protected static function agregar_ocD_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("
        INSERT INTO orden_compra_detalle
        (idorden_compra, id_articulo, cantidad, precio, cantidad_pendiente)
        VALUES (:ocid, :articulo, :cantidad, :precio, :pendiente)
    ");

        $sql->bindParam(":ocid", $datos['ocid']);
        $sql->bindParam(":articulo", $datos['articulo']);
        $sql->bindParam(":cantidad", $datos['cantidad']);
        $sql->bindParam(":precio", $datos['precio']);
        $sql->bindParam(":pendiente", $datos['pendiente']);
        $sql->execute();

        return $sql;
    }
    /**fin modelo */
}
