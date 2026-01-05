<?php
require_once "mainModel.php";

class ordencompraModelo extends mainModel
{
    /** modelo agregar presupuesto cabecera con presupuesto*/
    protected static function agregar_ocC_modelo1($datos)
    {
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("INSERT INTO orden_compra 
        (idproveedores, presupuestoid, id_sucursal, id_usuario, fecha, estado, fecha_entrega)
        VALUES (:proveedor, :presupuestoid, :sucursal, :usuario, NOW(), 1, :fecha_entrega)");

        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->bindParam(":presupuestoid", $datos['presupuestoid']);
        $sql->bindParam(":sucursal", $datos['sucursal']);
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
        (idproveedores, id_usuario, id_sucursal, fecha, estado, fecha_entrega)
        VALUES (:proveedor, :usuario, :sucursal, NOW(), 1, :fecha_entrega)");

        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":sucursal", $datos['sucursal']);
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
        WHERE idorden_compra=:idorden_compra and id_sucursal=:idsucursal");
        $sql->bindParam(":updatedby", $datos['updatedby']);
        $sql->bindParam(":idorden_compra", $datos['idorden_compra']);
        $sql->bindParam(":idsucursal", $datos['idsucursal']);
        $sql->execute();
        return $sql;
    }
    /**fin modelo */
    /* ================= CABECERA ================= */
    protected static function obtener_orden_compra_cabecera($id)
    {
        $sql = self::conectar()->prepare("
            SELECT
                oc.idorden_compra,
                oc.fecha,
                oc.fecha_entrega,
                oc.estado,

                p.razon_social,
                p.ruc,
                p.telefono,
                p.direccion,
                p.correo,

                u.usu_nombre,
                u.usu_apellido
            FROM orden_compra oc
            INNER JOIN proveedores p ON p.idproveedores = oc.idproveedores
            INNER JOIN usuarios u ON u.id_usuario = oc.id_usuario
            WHERE oc.idorden_compra = :id
            LIMIT 1
        ");
        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /* ================= DETALLE ================= */
    protected static function obtener_orden_compra_detalle($id)
    {
        $sql = self::conectar()->prepare("
            SELECT
                a.codigo,
                a.desc_articulo,
                d.cantidad,
                d.precio_unitario,
                (d.cantidad * d.precio_unitario) AS subtotal
            FROM orden_compra_detalle d
            INNER JOIN articulos a ON a.id_articulo = d.id_articulo
            WHERE d.idorden_compra = :id
        ");
        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
