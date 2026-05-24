<?php
require_once "mainModel.php";

class presupuestoModelo extends mainModel
{

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
        $conexion = mainModel::conectar();

        try {
            $conexion->beginTransaction();

            // 1. Anular presupuesto
            $sql = $conexion->prepare("
            UPDATE presupuesto_compra
            SET estado = 0,
                updatedby = :updatedby,
                updated = NOW()
            WHERE idpresupuesto_compra = :idpresupuesto_compra
            AND id_sucursal = :sucursal
        ");

            $sql->bindParam(":updatedby", $datos['updatedby']);
            $sql->bindParam(":idpresupuesto_compra", $datos['idpresupuesto_compra']);
            $sql->bindParam(":sucursal", $datos['sucursal']);
            $sql->execute();

            if ($sql->rowCount() <= 0) {
                $conexion->rollBack();
                return false;
            }

            // 2. Obtener el idPedido correcto
            $buscar_pedido = $conexion->prepare("
            SELECT idPedido
            FROM presupuesto_compra
            WHERE idpresupuesto_compra = :idpresupuesto_compra
            AND id_sucursal = :sucursal
            LIMIT 1
        ");

            $buscar_pedido->bindParam(":idpresupuesto_compra", $datos['idpresupuesto_compra']);
            $buscar_pedido->bindParam(":sucursal", $datos['sucursal']);
            $buscar_pedido->execute();

            $pedido = $buscar_pedido->fetch(PDO::FETCH_ASSOC);

            // 3. Actualizar pedido si existe
            if ($pedido && !empty($pedido['idPedido'])) {

                $sql_pedido = $conexion->prepare("
                UPDATE pedido_cabecera
                SET estado = 1
                WHERE idpedido_cabecera = :idpedido
            ");

                $sql_pedido->bindParam(":idpedido", $pedido['idPedido']);
                $sql_pedido->execute();
            }

            $conexion->commit();
            return $sql;
        } catch (Exception $e) {
            $conexion->rollBack();
            echo $e->getMessage(); 
            return false;
        }
    }
    /**fin modelo */

    protected static function listar_presupuestos_modelo($inicio, $registros, $filtrosSQL, $orderSQL)
    {
        $conexion = mainModel::conectar();

        $selectSQL = "
            SELECT
                pc.idpresupuesto_compra,
                pc.id_sucursal,
                pc.id_usuario,
                pc.fecha,
                pc.estado AS estadoPre,
                pc.idproveedores,
                pc.updated,
                pc.updatedby,
                p.razon_social,
                p.ruc,
                p.telefono,
                p.direccion,
                p.correo,
                p.estado AS estadoPro,
                u.usu_nombre,
                u.usu_apellido,
                u.usu_estado,
                u.usu_nick
        ";

        $baseSQL = "
            FROM presupuesto_compra pc
            INNER JOIN proveedores p ON p.idproveedores = pc.idproveedores
            INNER JOIN usuarios u ON u.id_usuario = pc.id_usuario
            WHERE pc.id_sucursal = '" . $_SESSION['nick_sucursal'] . "'
            $filtrosSQL
        ";

        return mainModel::ejecutarPaginador(
            $conexion,
            $baseSQL,
            $selectSQL,
            $orderSQL,
            $inicio,
            $registros
        );
    }

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
