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
            (idproveedores, id_usuario, id_sucursal,fecha_creacion, nro_factura, fecha_factura, nro_timbrado, vencimiento_timbrado, estado, total_compra, condicion, compra_intervalo, idOcompra)
            VALUES (:proveedor, :usuario, :idsucursal, NOW(), :nro_factura, :fecha_factura, :timbrado, :vto_timbrado, :estado, :total, :condicion, :intervalo, :idoc)");

            $sql->bindParam(":proveedor", $datos['proveedor']);
            $sql->bindParam(":usuario", $datos['usuario']);
            $sql->bindParam(":idsucursal", $datos['idsucursal']);
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
            (idcompra_cabecera, id_articulo, precio_unitario, cantidad_recibida, subtotal, tipo_iva, ivaPro)
            VALUES (:idcab, :articulo, :precio, :cantidad, :subtotal, :tipo_iva, :iva)");

            $sql->bindParam(":idcab", $detalle['idcab']);
            $sql->bindParam(":articulo", $detalle['id_articulo']);
            $sql->bindParam(":precio", $detalle['precio']);
            $sql->bindParam(":cantidad", $detalle['cantidad']);
            $sql->bindParam(":subtotal", $detalle['subtotal']);
            $sql->bindParam(":tipo_iva", $detalle['tipo_iva']);
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
                id_sucursal,
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
            (idcompra_cabecera, id_sucursal, monto, saldo, nro_cuotas, fecha_vencimiento, referencia_tipo, tipo_movimiento, fecha_movimiento, observacion,estado)
            VALUES (:idcompra, :idsucursal, :monto, :saldo, :cuotas, :fecha, 'INGRESO_COMPRA','COMPRA', NOW(), :observacion, :estado)");

            $sql->bindParam(':idcompra', $datos['idcompra']);
            $sql->bindParam(':idsucursal', $datos['idsucursal']);
            $sql->bindParam(':monto', $datos['monto']);
            $sql->bindParam(':saldo', $datos['saldo']);
            $sql->bindParam(':cuotas', $datos['cuotas']);
            $sql->bindParam(':fecha', $datos['fecha_vencimiento']);
            $sql->bindParam(':observacion', $datos['observacion']);
            $sql->bindParam(':estado', $datos['estado']);

            $sql->execute();
            return $sql;
        }

        /** modelo actualizar OC y restar cantidad pendiente */
        protected static function actualizar_oc_modelo($datos)
        {
            $pdo = mainModel::conectar();

            // 1) Obtener detalles de la compra para restar de la OC
            $detalles = compraModelo::datos_detalle_compra_modelo($datos['idcompra_cabecera'], $datos['id_sucursal'])->fetchAll(PDO::FETCH_ASSOC);

            foreach ($detalles as $d) {

                $sql_det = $pdo->prepare("
                    UPDATE orden_compra_detalle d
                    INNER JOIN orden_compra c 
                        ON c.idorden_compra = d.idorden_compra
                    SET d.cantidad_pendiente = d.cantidad_pendiente - :cantidad_recibida
                    WHERE d.idorden_compra = :idorden_compra
                    AND d.id_articulo = :id_articulo
                    AND c.id_sucursal = :id_sucursal");

                $sql_det->bindParam(":cantidad_recibida", $d['cantidad_recibida'], PDO::PARAM_STR);
                $sql_det->bindParam(":idorden_compra", $datos['idorden_compra'], PDO::PARAM_INT);
                $sql_det->bindParam(":id_articulo", $d['id_articulo'], PDO::PARAM_INT);
                $sql_det->bindParam(":id_sucursal", $datos['id_sucursal'], PDO::PARAM_INT);

                $sql_det->execute();

                // Seguridad extra
                if ($sql_det->rowCount() === 0) {
                    throw new Exception("Detalle no actualizado (sucursal incorrecta o datos inválidos)");
                }
            }

            // 2) Verificar si todas las cantidades pendientes quedaron en cero
            $checkPendientes = $pdo->prepare("
            SELECT COUNT(*) AS pendientes
            FROM orden_compra_detalle d
            INNER JOIN orden_compra c
                ON c.idorden_compra = d.idorden_compra
            WHERE d.idorden_compra = :idorden_compra
            AND d.cantidad_pendiente > 0
            AND c.id_sucursal = :id_sucursal");

            $checkPendientes->bindParam(":idorden_compra", $datos['idorden_compra'], PDO::PARAM_INT);
            $checkPendientes->bindParam(":id_sucursal", $datos['id_sucursal'], PDO::PARAM_INT);
            $checkPendientes->execute();

            $res = $checkPendientes->fetch(PDO::FETCH_ASSOC);


            // 3) Si no quedan pendientes, actualizar estado de la OC a 2 (completada)
            if ((int)$res['pendientes'] === 0) {

                $sql_oc = $pdo->prepare("
                UPDATE orden_compra
                SET estado = 2,
                    updatedby = :updatedby,
                    updated = NOW()
                WHERE idorden_compra = :idorden_compra
                AND id_sucursal = :id_sucursal");

                $sql_oc->bindParam(":updatedby", $datos['updatedby'], PDO::PARAM_INT);
                $sql_oc->bindParam(":idorden_compra", $datos['idorden_compra'], PDO::PARAM_INT);
                $sql_oc->bindParam(":id_sucursal", $datos['id_sucursal'], PDO::PARAM_INT);

                $sql_oc->execute();

                // Seguridad extra
                if ($sql_oc->rowCount() === 0) {
                    throw new Exception("No se pudo cerrar la OC (sucursal incorrecta o ya cerrada)");
                }
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
            WHERE idcompra_cabecera = :idcompra_cabecera and id_sucursal = :idsucursal");
            $sql->bindParam(":updatedby", $datos['updatedby']);
            $sql->bindParam(":idcompra_cabecera", $datos['idcompra_cabecera']);
            $sql->bindParam(":idsucursal", $datos['idsucursal']);
            $sql->execute();
            return $sql;
        }
        /** fin modelo */
        /** modelo obtener detalles de compra (multisucursal) */
        protected static function datos_detalle_compra_modelo($idcompra_cabecera, $id_sucursal)
        {
            $conexion = mainModel::conectar();

            $sql = $conexion->prepare("
            SELECT d.id_articulo,
                d.cantidad_recibida,
                d.precio_unitario
            FROM compra_detalle d
            INNER JOIN compra_cabecera c 
                ON c.idcompra_cabecera = d.idcompra_cabecera
            WHERE d.idcompra_cabecera = :idcompra_cabecera
            AND c.id_sucursal = :id_sucursal");

            $sql->bindParam(":idcompra_cabecera", $idcompra_cabecera, PDO::PARAM_INT);
            $sql->bindParam(":id_sucursal", $id_sucursal, PDO::PARAM_INT);

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
            (id_sucursal, TipoMovStockId, MovStockProductoId, MovStockCantidad,
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
        /** modelo anular cuentas a pagar (multisucursal) */
        protected static function anular_cuentas_pagar_modelo($idcompra_cabecera, $id_sucursal)
        {
            $conexion = mainModel::conectar();

            $sql = $conexion->prepare("
                UPDATE cuentas_a_pagar cap
                INNER JOIN compra_cabecera c
                    ON c.idcompra_cabecera = cap.idcompra_cabecera
                SET cap.estado = 0
                WHERE cap.idcompra_cabecera = :idcompra_cabecera
                AND c.id_sucursal = :id_sucursal  ");

            $sql->bindParam(":idcompra_cabecera", $idcompra_cabecera, PDO::PARAM_INT);
            $sql->bindParam(":id_sucursal", $id_sucursal, PDO::PARAM_INT);

            $sql->execute();
            return $sql;
        }
        /** fin modelo */


        public static function insertar_libro_compra_modelo($d)
        {
            $sql = mainModel::conectar()->prepare("
            INSERT INTO libro_compra (idcompra_cabecera, id_sucursal,fecha, tipo_comprobante, serie, nro_comprobante, idproveedores, proveedor_nombre, proveedor_ruc, exenta, 
            gravada_5, iva_5, gravada_10, iva_10, total) 
            VALUES (:idcompra, :id_sucursal, :fecha, :tipo, :serie, :numero, :proveedor, :prov_nom, :prov_ruc, :exenta, :gravada5, :iva5, :gravada10, :iva10, :total)");

            return $sql->execute($d) ? $sql : $sql;
        }
    }
