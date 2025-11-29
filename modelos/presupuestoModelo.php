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
    /**modelo actualizar pedido procesado */
    protected static function actualizar_pedido_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("UPDATE pedido_cabecera
        SET estado=2, updatedby=:updatedby, updated=now()
        WHERE idpedido_cabecera=:idpedido_cabecera");
        $sql->bindParam(":updatedby", $datos['updatedby']);
        $sql->bindParam(":idpedido_cabecera", $datos['idpedido_cabecera']);
        $sql->execute();
        return $sql;
    }
    /**fin modelo */

    /**modelo anular presupuesto */
    protected static function anular_presupuesto_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("UPDATE presupuesto_compra
        SET estado=0, updatedby=:updatedby, updated=now()
        WHERE idpresupuesto_compra=:idpresupuesto_compra");
        $sql->bindParam(":updatedby", $datos['updatedby']);
        $sql->bindParam(":idpresupuesto_compra", $datos['idpresupuesto_compra']);
        $sql->execute();
        return $sql;
    }
    /**fin modelo */

    /**modelo datos presupuesto detalle*/
    protected static function datos_presupuesto_modelo($tipo, $id)
    {
        if ($tipo == "unico") {
            $sql = mainModel::conectar()->prepare("SELECT * FROM presupuesto_compra WHERE idpresupuesto_compra=:id");
            $sql->bindParam(":id", $id);
        } elseif ($tipo == "conteoActivos") {
            $sql = mainModel::conectar()->prepare("SELECT idpresupuesto_compra FROM presupuesto_compra WHERE estado='1'");
        } elseif ($tipo == "conteoProcesados") {
            $sql = mainModel::conectar()->prepare("SELECT idpresupuesto_compra FROM presupuesto_compra WHERE estado='2'");
        } elseif ($tipo == "conteo") {
            $sql = mainModel::conectar()->prepare("SELECT idpresupuesto_compra FROM presupuesto_compra");
        }
        $sql->execute();
        return $sql;
    }
    /**fin modelo */
}
