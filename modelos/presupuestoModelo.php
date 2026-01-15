<?php
require_once "mainModel.php";

class presupuestoModelo extends mainModel
{

    /** modelo agregar presupuesto cabecera sin pedido*/
    protected static function agregar_presupuestoC_modelo1($datos)
    {
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("
        INSERT INTO presupuesto_compra 
            (idproveedores, id_usuario, id_sucursal, fecha, estado, fecha_venc, total)
        VALUES
            (:proveedor, :usuario, :sucursal, now(), 1, :fechaVe, :total)
        ");

        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":sucursal", $datos['sucursal']);
        $sql->bindParam(":fechaVe", $datos['fecha_venc']);
        $sql->bindParam(":total", $datos['total']);

        $sql->execute();

        return $conexion->lastInsertId();
    }

    /**fin modelo */
    /** modelo agregar presupuesto cabecera con pedido*/
    protected static function agregar_presupuestoC_modelo2($datos)
    {
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("INSERT INTO presupuesto_compra (idPedido, id_usuario, idproveedores, id_sucursal, fecha, estado, fecha_venc, total)
                               VALUES(:idPedido, :usuario, :proveedor, :sucursal, now(), 1, :fechaVe, :total)");
        $sql->bindParam(":idPedido", $datos['idPedido']);
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->bindParam(":sucursal", $datos['sucursal']);
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
        WHERE idpedido_cabecera=:idpedido_cabecera and id_sucursal = :sucursal");
        $sql->bindParam(":updatedby", $datos['updatedby']);
        $sql->bindParam(":idpedido_cabecera", $datos['idpedido_cabecera']);
        $sql->bindParam(":sucursal", $datos['sucursal']);
        $sql->execute();
        return $sql;
    }
    /**fin modelo */

    /**modelo anular presupuesto */
    protected static function anular_presupuesto_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("UPDATE presupuesto_compra
        SET estado=0, updatedby=:updatedby, updated=now()
        WHERE idpresupuesto_compra=:idpresupuesto_compra and id_sucursal = :sucursal");
        $sql->bindParam(":updatedby", $datos['updatedby']);
        $sql->bindParam(":idpresupuesto_compra", $datos['idpresupuesto_compra']);
        $sql->bindParam(":sucursal", $datos['sucursal']);
        $sql->execute();
        return $sql;
    }
    /**fin modelo */

    /**modelo datos presupuesto detalle*/
    protected static function datos_presupuesto_modelo($tipo, $id = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $sucursal = $_SESSION['nick_sucursal'];

        $conexion = mainModel::conectar();

        switch ($tipo) {

            case "unico":
                $sql = $conexion->prepare("
                SELECT *
                FROM presupuesto_compra
                WHERE idpresupuesto_compra = :id
                AND id_sucursal = :sucursal
            ");
                $sql->bindParam(":id", $id, PDO::PARAM_INT);
                break;

            case "conteoActivos":
                $sql = $conexion->prepare("
                SELECT COUNT(*) AS total
                FROM presupuesto_compra
                WHERE estado = '1'
                AND id_sucursal = :sucursal
            ");
                break;

            case "conteoProcesados":
                $sql = $conexion->prepare("
                SELECT COUNT(*) AS total
                FROM presupuesto_compra
                WHERE estado = '2'
                AND id_sucursal = :sucursal
            ");
                break;

            case "conteo":
                $sql = $conexion->prepare("
                SELECT COUNT(*) AS total
                FROM presupuesto_compra
                WHERE id_sucursal = :sucursal
            ");
                break;

            default:
                return false;
        }

        // ESTE bind va para TODOS los casos
        $sql->bindParam(":sucursal", $sucursal, PDO::PARAM_INT);

        $sql->execute();
        return $sql;
    }

    /**fin modelo */
}
