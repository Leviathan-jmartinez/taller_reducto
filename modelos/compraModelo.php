<?php
require_once "mainModel.php";

class compraModelo extends mainModel
{
    /* ===============================
       INSERTAR COMPRA CABECERA
    ================================= */
    protected static function insertar_compra_cabecera_modelo($datos)
    {
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("
            INSERT INTO compra_cabecera
            (idproveedores, id_usuario, fecha_creacion, nro_factura, fecha_factura, nro_timbrado, vencimiento_timbrado, estado, total_compra, condicion, compra_intervalo, idOcompra)
            VALUES (:proveedor, :usuario, NOW(), :nro_factura, :fecha_factura, :timbrado, :vto_timbrado, :estado, :total, :condicion, :intervalo, :idoc)
        ");

        $sql->bindParam(":proveedor", $datos['proveedor']);
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":nro_factura", $datos['nro_factura']);
        $sql->bindParam(":fecha_factura", $datos['fecha_factura']);
        $sql->bindParam(":timbrado", $datos['timbrado']);
        $sql->bindParam(":vto_timbrado", $datos['vencimiento_timbrado']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":total", $datos['total']);
        $sql->bindParam(":condicion", $datos['condicion']);
        $sql->bindParam(":intervalo", $datos['intervalo']);
        $sql->bindParam(":idoc", $datos['idoc']);

        $sql->execute();
        return [
            "stmt" => $sql,
            "conexion" => $conexion,
            "last_id" => $conexion->lastInsertId()
        ];
    }

    /* ===============================
       INSERTAR COMPRA DETALLE
    ================================= */
    protected static function insertar_compra_detalle_modelo($detalle)
    {
        $sql = mainModel::conectar()->prepare("
            INSERT INTO compra_detalle
            (idcompra_cabecera, id_articulo, precio_unitario, cantidad_recibida, subtotal, ivaPro)
            VALUES (:idcab, :articulo, :precio, :cantidad, :subtotal, :iva)
        ");

        $sql->bindParam(":idcab", $detalle['idcab']);
        $sql->bindParam(":articulo", $detalle['id_articulo']);
        $sql->bindParam(":precio", $detalle['precio']);
        $sql->bindParam(":cantidad", $detalle['cantidad']);
        $sql->bindParam(":subtotal", $detalle['subtotal']);
        $sql->bindParam(":iva", $detalle['iva']);

        $sql->execute();
        return $sql;
    }
    /* ==============================
         Insertar o actualizar stock
    ============================== */
    protected function upsert_stock_modelo($datos)
    {

        $sql = "INSERT INTO stock (
                id_sucursal,
                id_articulo,
                stockcant_max,
                stockcant_min,
                stockDisponible,
                stockUltActualizacion,
                stockUsuActualizacion,
                stockultimoIdActualizacion
            ) VALUES (
                :id_sucursal,
                :id_articulo,
                200,
                15,
                :stockDisponible,
                :stockUltActualizacion,
                :stockUsuActualizacion,
                :stockultimoIdActualizacion
            )
            ON DUPLICATE KEY UPDATE
                stockDisponible = VALUES(stockDisponible),
                stockUltActualizacion = VALUES(stockUltActualizacion),
                stockUsuActualizacion = VALUES(stockUsuActualizacion),
                stockultimoIdActualizacion = VALUES(stockultimoIdActualizacion),
                stockcant_max = 200,
                stockcant_min = 15";

        $conexion = mainModel::conectar();
        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(":id_sucursal", $datos['id_sucursal'], PDO::PARAM_INT);
        $stmt->bindParam(":id_articulo", $datos['id_articulo'], PDO::PARAM_INT);
        $stmt->bindParam(":stockDisponible", $datos['stockDisponible']);
        $stmt->bindParam(":stockUltActualizacion", $datos['stockUltActualizacion']);
        $stmt->bindParam(":stockUsuActualizacion", $datos['stockUsuActualizacion'], PDO::PARAM_INT);
        $stmt->bindParam(":stockultimoIdActualizacion", $datos['stockultimoIdActualizacion'], PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }
    /* ==============================
       obtener stock actual
    ============================== */
    protected function obtener_stock_actual_modelo($id_sucursal, $id_articulo)
    {
        $sql = "SELECT stockDisponible 
            FROM stock 
            WHERE id_sucursal = :id_sucursal 
              AND id_articulo = :id_articulo 
            LIMIT 1";

        $conexion = mainModel::conectar();
        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(":id_sucursal", $id_sucursal, PDO::PARAM_INT);
        $stmt->bindParam(":id_articulo", $id_articulo, PDO::PARAM_INT);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return floatval($data["stockDisponible"]);
        }

        // No existe -> stock = 0
        return 0;
    }
    /* ==============================
       Insertar movimiento de stock
    ============================== */
    protected static function agregar_movimiento_stock($detalle)
    {

        $sql = mainModel::conectar()->prepare("
        INSERT INTO sucmovimientostock
        (
            LocalId,
            TipoMovStockId,
            MovStockProductoId,
            MovStockCantidad,
            MovStockPrecioVenta,
            MovStockCosto,
            MovStockFechaHora,
            MovStockNroTicket,
            MovStockPOS,
            MovStockUsuario,
            MovStockSigno,
            MovStockReferencia
        ) VALUES (
            :local,
            :tipo,
            :producto,
            :cantidad,
            :precioVenta,
            :costo,
            NOW(),
            :nroTicket,
            :pos,
            :usuario,
            :signo,
            :referencia
        )");

        // Bind de parámetros
        $sql->bindParam(":local",        $detalle['local']);
        $sql->bindParam(":tipo",         $detalle['tipo']);          // por ejemplo "COMPRA"
        $sql->bindParam(":producto",     $detalle['producto']);      // id_articulo
        $sql->bindParam(":cantidad",     $detalle['cantidad']);      // cantidad recibida
        $sql->bindParam(":precioVenta",  $detalle['precioVenta']);   // 0 si no aplica
        $sql->bindParam(":costo",        $detalle['costo']);         // costo de compra
        $sql->bindParam(":nroTicket",    $detalle['nroTicket']);     // factura
        $sql->bindParam(":pos",          $detalle['pos']);           // puede ir NULL
        $sql->bindParam(":usuario",      $detalle['usuario']);       // id usuario
        $sql->bindParam(":signo",        $detalle['signo']);         // 1 para compra
        $sql->bindParam(":referencia",   $detalle['referencia']);    // ID de OC

        $sql->execute();
        return $sql;
    }

    /* ==============================
       Insertar cuentas a pagar
    ============================== */
    protected static function insertar_cuentas_a_pagar_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("
            INSERT INTO cuentas_a_pagar
            (idcompra_cabecera, monto, saldo, nro_cuotas, fecha_vencimiento, estado)
            VALUES (:idcompra, :monto, :saldo, :cuotas, :fecha, :estado)");

        $sql->bindParam(':idcompra', $datos['idcompra']);
        $sql->bindParam(':monto', $datos['monto']);
        $sql->bindParam(':saldo', $datos['saldo']);
        $sql->bindParam(':cuotas', $datos['cuotas']);
        $sql->bindParam(':fecha', $datos['fecha_vencimiento']);
        $sql->bindParam(':estado', $datos['estado']);

        $sql->execute();
        return $sql;
    }

    /** modelo actualizar OC y restar cantidad pendiente */
    protected static function actualizar_oc_modelo($datos)
    {
        $pdo = mainModel::conectar();

        // 1) Obtener detalles de la compra para restar de la OC
        $detalles = compraModelo::datos_detalle_compra_modelo($datos['idcompra_cabecera'])->fetchAll(PDO::FETCH_ASSOC);

        foreach ($detalles as $d) {
            // Restar cantidad recibida de cantidad pendiente en OC
            $sql_det = $pdo->prepare("
            UPDATE orden_compra_detalle
            SET cantidad_pendiente = cantidad_pendiente - :cantidad_recibida
            WHERE idorden_compra = :idorden_compra
              AND id_articulo = :id_articulo
        ");
            $sql_det->bindParam(":cantidad_recibida", $d['cantidad_recibida']);
            $sql_det->bindParam(":idorden_compra", $datos['idorden_compra']);
            $sql_det->bindParam(":id_articulo", $d['id_articulo']);
            $sql_det->execute();
        }

        // 2) Verificar si todas las cantidades pendientes quedaron en cero
        $checkPendientes = $pdo->prepare("
        SELECT COUNT(*) AS pendientes
        FROM orden_compra_detalle
        WHERE idorden_compra = :idorden_compra
          AND cantidad_pendiente > 0");
        $checkPendientes->bindParam(":idorden_compra", $datos['idorden_compra']);
        $checkPendientes->execute();
        $res = $checkPendientes->fetch(PDO::FETCH_ASSOC);

        // 3) Si no quedan pendientes, actualizar estado de la OC a 2 (completada)
        if ($res['pendientes'] == 0) {
            $sql_oc = $pdo->prepare("
            UPDATE orden_compra
            SET estado = 2,
                updatedby = :updatedby,
                updated = NOW()
            WHERE idorden_compra = :idorden_compra
        ");
            $sql_oc->bindParam(":updatedby", $datos['updatedby']);
            $sql_oc->bindParam(":idorden_compra", $datos['idorden_compra']);
            $sql_oc->execute();
        }

        return true;
    }
    /** fin modelo */


    /** modelo anular compra */
    protected static function anular_compra_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE compra_cabecera
        SET estado = 0,
            updatedby = :updatedby,
            updated = NOW()
        WHERE idcompra_cabecera = :idcompra_cabecera");
        $sql->bindParam(":updatedby", $datos['updatedby']);
        $sql->bindParam(":idcompra_cabecera", $datos['idcompra_cabecera']);
        $sql->execute();
        return $sql;
    }
    /** fin modelo */
    /** modelo obtener detalles de compra */
    protected static function datos_detalle_compra_modelo($idcompra_cabecera)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT id_articulo, cantidad_recibida, precio_unitario
        FROM compra_detalle
        WHERE idcompra_cabecera = :idcompra_cabecera");
        $sql->bindParam(":idcompra_cabecera", $idcompra_cabecera);
        $sql->execute();
        return $sql;
    }
    /** fin modelo */
    /** modelo descontar stock */
    protected static function descontar_stock_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE stock
        SET stockDisponible = stockDisponible - :cantidad,
            stockUltActualizacion = NOW(),
            stockUsuActualizacion = :usuario,
            stockultimoIdActualizacion = :referencia
        WHERE id_sucursal = :id_sucursal
          AND id_articulo = :id_articulo");
        $sql->bindParam(":cantidad", $datos['cantidad']);
        $sql->bindParam(":usuario", $datos['usuario']);
        $sql->bindParam(":referencia", $datos['referencia']);
        $sql->bindParam(":id_sucursal", $datos['id_sucursal']);
        $sql->bindParam(":id_articulo", $datos['id_articulo']);
        $sql->execute();
        return $sql;
    }
    /** fin modelo */
    /** modelo movimiento stock anulacion */
    protected static function movimiento_stock_anulacion_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("
        INSERT INTO sucmovimientostock
        (LocalId, TipoMovStockId, MovStockProductoId, MovStockCantidad,
         MovStockPrecioVenta, MovStockCosto, MovStockFechaHora,
         MovStockNroTicket, MovStockPOS, MovStockUsuario,
         MovStockSigno, MovStockReferencia)
        VALUES
        (:LocalId, 'ANULACION COMPRA', :ProductoId, :Cantidad,
         0, :Costo, NOW(),
         :Referencia, NULL, :Usuario,
         -1, :Referencia)");

        $sql->bindParam(":LocalId", $datos['LocalId']);
        $sql->bindParam(":ProductoId", $datos['ProductoId']);
        $sql->bindParam(":Cantidad", $datos['Cantidad']);
        $sql->bindParam(":Costo", $datos['Costo']);
        $sql->bindParam(":Referencia", $datos['Referencia']);
        $sql->bindParam(":Usuario", $datos['Usuario']);
        $sql->execute();
        return $sql;
    }
    /** fin modelo */
    /** modelo anular cuentas a pagar */
    protected static function anular_cuentas_pagar_modelo($idcompra_cabecera)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE cuentas_a_pagar
        SET estado = 0
        WHERE idcompra_cabecera = :idcompra_cabecera");
        $sql->bindParam(":idcompra_cabecera", $idcompra_cabecera);
        $sql->execute();
        return $sql;
    }
    /** fin modelo */
}
