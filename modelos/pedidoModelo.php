<?php
require_once "mainModel.php";

class pedidoModelo extends mainModel
{
    /** modelo agregar pedido*/
    protected static function agregar_pedidoC_modelo($datos)
    {
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("INSERT INTO pedido_cabecera (id_usuario, fecha, id_proveedor, estado, id_sucursal)
                               VALUES(:usuario, NOW(), :proveedor, 1, :sucursal)");
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->bindParam(":sucursal", $datos['sucursal']);
        $sql->execute();

        // retornar el ID autoincremental
        return $conexion->lastInsertId();
    }
    /**fin modelo */
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
    /**fin modelo */
    /**modelo datos pedido detalle*/
    protected static function datos_pedido_modelo($tipo, $id)
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
    /**fin modelo */
    /**modelo anular pedido */
    protected static function anular_pedido_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("UPDATE pedido_cabecera
        SET estado=0, updatedby=:updatedby, updated=now()
        WHERE idpedido_cabecera=:idpedido_cabecera and id_sucursal = :sucursal");
        $sql->bindParam(":updatedby", $datos['updatedby']);
        $sql->bindParam(":idpedido_cabecera", $datos['idpedido_cabecera']);
        $sql->bindParam(":sucursal", $datos['sucursal']);
        $sql->execute();
        return $sql;
    }
    /**fin modelo */
    /** modelo obtener datos para PDF */
    protected static function obtener_pedido_cabecera($id)
    {
        $sql = self::conectar()->prepare("
            SELECT
                pc.idpedido_cabecera,
                pc.fecha,
                pc.estado,

                p.razon_social,
                p.ruc,
                p.telefono,
                p.direccion,
                p.correo,

                u.usu_nombre,
                u.usu_apellido
            FROM pedido_cabecera pc
            INNER JOIN proveedores p ON p.idproveedores = pc.id_proveedor
            INNER JOIN usuarios u ON u.id_usuario = pc.id_usuario
            WHERE pc.idpedido_cabecera = :id
            LIMIT 1
        ");
        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function obtener_pedido_detalle($id)
    {
        $sql = self::conectar()->prepare("
            SELECT
                a.codigo,
                a.desc_articulo,
                pd.cantidad
            FROM pedido_detalle pd
            INNER JOIN articulos a ON a.id_articulo = pd.id_articulo
            WHERE pd.idpedido_cabecera = :id
        ");
        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
