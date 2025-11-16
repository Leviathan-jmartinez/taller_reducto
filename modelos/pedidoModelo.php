<?php
require_once "mainModel.php";

class pedidoModelo extends mainModel
{
    /** modelo agregar pedido*/
    protected static function agregar_pedidoC_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("INSERT INTO pedido_cabecera (id_usuario, fecha, id_proveedor, estado) VALUES(:usuario, now(), :proveedor, 1)");
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->execute();
        return $sql;
    }
    /**modelo datos pedido */
    /** modelo agregar pedido detalle*/

    protected static function agregar_pedidoD_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("INSERT INTO pedido_detalle (idpedido_cabecera, id_articulo, cantidad) VALUES(:pedidoid, :articulo, :cantidad)");
        $sql->bindParam(":pedidoid", $datos['pedidoid']);
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":cantidad", $datos['cantidad']);
        $sql->execute();
        return $sql;
    }
    /**modelo datos pedido detalle*/

    /** modelo eliminar pedido*/

    protected static function eliminar_pedido_modelo($id, $tipo)
    {
        if ($tipo == "pedido") {
            $sql = mainModel::conectar()->prepare("DELETE FROM pedido_cabecera WHERE idpedido_cabecera=:id");
        } elseif ($tipo == "pedidoDetalle") {
            $sql = mainModel::conectar()->prepare("DELETE FROM pedido_detalle WHERE idpedido_cabecera=:id");
        }
        $sql->bindParam(":id", $id);
        $sql->execute();
        return $sql;
    }
    /**modelo eliminar pedido */
    /** modelo seleccionar pedido*/
    protected static function datos_pedido_modelo($id, $tipo)
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

    /** */
}
