<?php
require_once "mainModel.php";

class presupuestoModelo extends mainModel
{

    /** modelo agregar presupuesto cabecera sin pedido*/
    protected static function agregar_presupuestoC_modelo1($datos)
    {
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("INSERT INTO presupuesto_compra (idproveedores, id_usuario, fecha, estado, fecha_venc, total)
                               VALUES(:usuario, :proveedor, now(), 1, :fechaVe, :total)");
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->bindParam(":fechaVe", $datos['fecha_venc']);
        $sql->bindParam(":total", $datos['total']);
        $sql->execute();

        // retornar el ID autoincremental
        return $conexion->lastInsertId();
    }
    /**fin modelo */
    /** modelo agregar presupuesto cabecera con pedido*/
    protected static function agregar_presupuestoC_modelo2($datos)
    {
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("INSERT INTO presupuesto_compra (idPedido, id_usuario, idproveedores,  fecha, estado, fecha_venc, total)
                               VALUES(:idPedido, :usuario, :proveedor, now(), 1, :fechaVe, :total)");
        $sql->bindParam(":idPedido", $datos['idPedido']);
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->bindParam(":fechaVe", $datos['fecha_venc']);
        $sql->bindParam(":total", $datos['total']);
        $sql->execute();

        // retornar el ID autoincremental
        return $conexion->lastInsertId();
    }
    /**fin modelo */
    /** modelo agregar presupuesto detalle*/
    protected static function agregar_presupuestoD_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "INSERT INTO presupuesto_detalle (idpresupuesto_compra, id_articulo, cantidad, precio, subtotal)
         VALUES (:presupuestoid, :articulo, :cantidad, :precio, :subtotal)"
        );
        $sql->bindParam(":presupuestoid", $datos['presupuestoid']);
        $sql->bindParam(":articulo", $datos['articulo']);
        $sql->bindParam(":cantidad", $datos['cantidad']);
        $sql->bindParam(":precio", $datos['precio']);
        $sql->bindParam(":subtotal", $datos['subtotal']);
        $sql->execute();
        return $sql;
    }
    /**fin modelo */


}
