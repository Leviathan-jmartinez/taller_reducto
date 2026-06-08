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

    /**modelo generar ordencompra desde presupuesto */
    protected static function generar_oc_desde_presupuesto_modelo($datos)
    {
        $conexion = mainModel::conectar();

        try {
            $conexion->beginTransaction();

            $consultaPre = $conexion->prepare("
                SELECT idpresupuesto_compra, idproveedores, id_usuario
                FROM presupuesto_compra
                WHERE idpresupuesto_compra = :id
                AND id_sucursal = :sucursal
                AND estado != 0
                LIMIT 1
            ");
            $consultaPre->execute([
                ":id" => $datos['idpresupuesto'],
                ":sucursal" => $datos['sucursal']
            ]);
            $pre = $consultaPre->fetch(PDO::FETCH_ASSOC);

            if (!$pre) {
                $conexion->rollBack();
                return ["estado" => false, "codigo" => "presupuesto_no_existe"];
            }

            $consultaDet = $conexion->prepare("
                SELECT d.id_articulo, d.precio
                FROM presupuesto_detalle d
                INNER JOIN presupuesto_compra c
                    ON c.idpresupuesto_compra = d.idpresupuesto_compra
                WHERE d.idpresupuesto_compra = :id
                AND c.id_sucursal = :sucursal
            ");
            $consultaDet->execute([
                ":id" => $datos['idpresupuesto'],
                ":sucursal" => $datos['sucursal']
            ]);
            $detallePre = $consultaDet->fetchAll(PDO::FETCH_ASSOC);

            if (empty($detallePre)) {
                $conexion->rollBack();
                return ["estado" => false, "codigo" => "detalle_vacio"];
            }

            $articulos_presupuesto = array_column($detallePre, null, "id_articulo");
            $detalles_validos = [];

            foreach ($datos['cantidades'] as $idArt => $cantidad) {
                if (!isset($articulos_presupuesto[$idArt])) {
                    continue;
                }

                $item = $articulos_presupuesto[$idArt];
                if (!isset($item["precio"]) || !is_numeric($item["precio"]) || (float) $item["precio"] <= 0) {
                    $conexion->rollBack();
                    return ["estado" => false, "codigo" => "precio_invalido"];
                }

                $detalles_validos[] = [
                    "id_articulo" => $idArt,
                    "cantidad" => $cantidad,
                    "precio" => (float) $item["precio"]
                ];
            }

            if (empty($detalles_validos)) {
                $conexion->rollBack();
                return ["estado" => false, "codigo" => "sin_articulos_cantidad"];
            }

            $insertCabecera = $conexion->prepare("
                INSERT INTO orden_compra
                (idproveedores, presupuestoid, id_sucursal, id_usuario, fecha, estado, fecha_entrega)
                VALUES (:proveedor, :presupuestoid, :sucursal, :usuario, NOW(), 1, :fecha_entrega)
            ");
            $insertCabecera->execute([
                ":proveedor" => $pre['idproveedores'],
                ":presupuestoid" => $pre['idpresupuesto_compra'],
                ":sucursal" => $datos['sucursal'],
                ":usuario" => $datos['usuario'],
                ":fecha_entrega" => $datos['fecha_entrega']
            ]);

            if ($insertCabecera->rowCount() != 1) {
                $conexion->rollBack();
                return ["estado" => false, "codigo" => "oc_cabecera"];
            }

            $idOC = $conexion->lastInsertId();

            $insertDetalle = $conexion->prepare("
                INSERT INTO orden_compra_detalle
                (idorden_compra, id_articulo, cantidad, precio_unitario, cantidad_pendiente)
                VALUES (:ocid, :articulo, :cantidad, :precio, :pendiente)
            ");

            foreach ($detalles_validos as $item) {
                $insertDetalle->execute([
                    ":ocid" => $idOC,
                    ":articulo" => $item["id_articulo"],
                    ":cantidad" => $item["cantidad"],
                    ":precio" => $item["precio"],
                    ":pendiente" => $item["cantidad"]
                ]);

                if ($insertDetalle->rowCount() != 1) {
                    $conexion->rollBack();
                    return ["estado" => false, "codigo" => "oc_detalle"];
                }
            }

            $actualizarPresupuesto = $conexion->prepare("
                UPDATE presupuesto_compra
                SET estado = 2,
                    updatedby = :updatedby,
                    updated = NOW()
                WHERE idpresupuesto_compra = :id
                AND id_sucursal = :sucursal
            ");
            $actualizarPresupuesto->execute([
                ":updatedby" => $datos['usuario'],
                ":id" => $datos['idpresupuesto'],
                ":sucursal" => $datos['sucursal']
            ]);

            $conexion->commit();
            return ["estado" => true, "idoc" => $idOC];
        } catch (Exception $e) {
            $conexion->rollBack();
            return ["estado" => false, "codigo" => "transaccion"];
        }
    }
    /**fin modelo */

    /**modelo anular ordencompra */
    protected static function anular_ordencompra_modelo($datos)
    {
        $conexion = mainModel::conectar();

        try {
            $conexion->beginTransaction();

            $buscar_oc = $conexion->prepare("
                SELECT presupuestoid
                FROM orden_compra
                WHERE idorden_compra = :idorden_compra
                AND id_sucursal = :idsucursal
                LIMIT 1
            ");
            $buscar_oc->bindParam(":idorden_compra", $datos['idorden_compra']);
            $buscar_oc->bindParam(":idsucursal", $datos['idsucursal']);
            $buscar_oc->execute();
            $oc = $buscar_oc->fetch(PDO::FETCH_ASSOC);

            if (!$oc) {
                $conexion->rollBack();
                return false;
            }

            $sql = $conexion->prepare("
                UPDATE orden_compra
                SET estado = 0,
                    updatedby = :updatedby,
                    updated = NOW()
                WHERE idorden_compra = :idorden_compra
                AND id_sucursal = :idsucursal
            ");
            $sql->bindParam(":updatedby", $datos['updatedby']);
            $sql->bindParam(":idorden_compra", $datos['idorden_compra']);
            $sql->bindParam(":idsucursal", $datos['idsucursal']);
            $sql->execute();

            if ($sql->rowCount() <= 0) {
                $conexion->rollBack();
                return false;
            }

            if (!empty($oc['presupuestoid'])) {
                $actualizar_presupuesto = $conexion->prepare("
                    UPDATE presupuesto_compra
                    SET estado = 1,
                        updatedby = :updatedby,
                        updated = NOW()
                    WHERE idpresupuesto_compra = :idpresupuesto_compra
                    AND id_sucursal = :idsucursal
                    AND estado = 2
                    AND NOT EXISTS (
                        SELECT 1
                        FROM orden_compra oc
                        WHERE oc.presupuestoid = presupuesto_compra.idpresupuesto_compra
                        AND oc.id_sucursal = presupuesto_compra.id_sucursal
                        AND oc.estado != 0
                    )
                ");
                $actualizar_presupuesto->bindParam(":updatedby", $datos['updatedby']);
                $actualizar_presupuesto->bindParam(":idpresupuesto_compra", $oc['presupuestoid']);
                $actualizar_presupuesto->bindParam(":idsucursal", $datos['idsucursal']);
                $actualizar_presupuesto->execute();
            }

            mainModel::registrar_anulacion_auditoria_modelo($conexion, [
                'modulo' => 'orden_compra',
                'tabla_afectada' => 'orden_compra',
                'id_registro' => $datos['idorden_compra'],
                'id_sucursal' => $datos['idsucursal'],
                'estado_anterior' => '1',
                'estado_nuevo' => '0',
                'motivo' => $datos['motivo'] ?? '',
                'usuario_anula' => $datos['updatedby'],
                'referencia' => 'ORDEN_COMPRA #' . $datos['idorden_compra']
            ]);

            $conexion->commit();
            return $sql;
        } catch (Exception $e) {
            $conexion->rollBack();
            return false;
        }
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
                oc.id_sucursal,
                oc.estado,

                p.razon_social,
                p.ruc,
                p.telefono,
                p.direccion,
                p.correo,

                s.suc_descri AS sucursal_destino,
                s.suc_direccion AS sucursal_destino_direccion,
                s.suc_telefono AS sucursal_destino_telefono,

                u.usu_nombre,
                u.usu_apellido
            FROM orden_compra oc
            INNER JOIN proveedores p ON p.idproveedores = oc.idproveedores
            INNER JOIN sucursales s ON s.id_sucursal = oc.id_sucursal
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

    protected static function listar_oc_modelo($inicio, $registros, $filtrosSQL, $orderSQL = "ORDER BY oc.idorden_compra DESC")
    {
        $conexion = mainModel::conectar();

        $baseSQL = "
        FROM orden_compra oc
        INNER JOIN proveedores p ON p.idproveedores = oc.idproveedores
        INNER JOIN usuarios u ON u.id_usuario = oc.id_usuario
        WHERE oc.id_sucursal = " . $_SESSION['nick_sucursal'] . "
        $filtrosSQL
        ";

        $selectSQL = "
        SELECT 
            oc.idorden_compra,
            oc.fecha,
            oc.fecha_entrega,
            oc.estado as estodoOC,
            p.razon_social,
            u.usu_nombre,
            u.usu_apellido
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
}
