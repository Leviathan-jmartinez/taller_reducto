<?php
require_once "mainModel.php";

class pedidoModelo extends mainModel
{
    /** modelo agregar pedido*/
    protected static function agregar_pedidoC_modelo($datos)
    {
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("INSERT INTO pedido_cabecera (id_usuario, fecha, id_proveedor, estado)
                               VALUES(:usuario, NOW(), :proveedor, 1)");
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->execute();

        // retornar el ID autoincremental
        return $conexion->lastInsertId();
    }

    /**modelo datos pedido */
    /** modelo agregar pedido detalle*/

    protected static function agregar_pedidoD_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare(
            "INSERT INTO pedido_detalle (idpedido_cabecera, id_articulo, cantidad)
         VALUES (:pedidoid, :articulo, :cantidad)"
        );

        $sql->bindParam(":pedidoid", $datos['pedidoid']);
        $sql->bindParam(":articulo", $datos['articulo']);
        $sql->bindParam(":cantidad", $datos['cantidad']);
        $sql->execute();
        return $sql;
    }

    /**modelo datos pedido detalle*/
    /** modelo seleccionar pedido*/
    protected static function datos_pedido_modelo($tipo,$id)
    {
        if ($tipo == "unico") {
            $sql = mainModel::conectar()->prepare("SELECT * FROM pedido_cabecera WHERE idpedido_cabecera=:id");
            $sql->bindParam(":id", $id);
        } elseif ($tipo == "conteoActivos") {
            $sql = mainModel::conectar()->prepare("SELECT idpedido_cabecera FROM pedido_cabecera WHERE estado='1'");
        } elseif ($tipo == "conteoProcesados") {
            $sql = mainModel::conectar()->prepare("SELECT idpedido_cabecera FROM pedido_cabecera WHERE estado='2'");
        } elseif ($tipo == "conteo") {
            $sql = mainModel::conectar()->prepare("SELECT idpedido_cabecera FROM pedido_cabecera");
        }
        $sql->execute();
        return $sql;
    }

    /**modelo anular pedido */
    protected static function anular_pedido_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("UPDATE pedido_cabecera
        SET estado=0, updatedby=:updatedby, updated=now()
        WHERE idpedido_cabecera=:idpedido_cabecera");
        $sql->bindParam(":updatedby", $datos['updatedby']);
        $sql->bindParam(":idpedido_cabecera", $datos['idpedido_cabecera']);
        $sql->execute();
        return $sql;
    }
    /**fin modelo */

}
