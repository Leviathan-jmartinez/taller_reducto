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
        protected static function upsert_stock_modelo($datos)
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
                :cantidadIngreso,
                :stockUltActualizacion,
                :stockUsuActualizacion,
                :stockultimoIdActualizacion
            )
            ON DUPLICATE KEY UPDATE
                stockDisponible = stockDisponible + :cantidadIngresoUpdate,
                stockUltActualizacion = VALUES(stockUltActualizacion),
                stockUsuActualizacion = VALUES(stockUsuActualizacion),
                stockultimoIdActualizacion = VALUES(stockultimoIdActualizacion),
                stockcant_max = 200,
                stockcant_min = 15";

            $conexion = mainModel::conectar();
            $stmt = $conexion->prepare($sql);

            $stmt->bindParam(":id_sucursal", $datos['id_sucursal'], PDO::PARAM_INT);
            $stmt->bindParam(":id_articulo", $datos['id_articulo'], PDO::PARAM_INT);
            $stmt->bindParam(":cantidadIngreso", $datos['cantidadIngreso']);
            $stmt->bindParam(":cantidadIngresoUpdate", $datos['cantidadIngreso']);
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
            $conexion = mainModel::conectar();
            mainModel::registrar_movimiento_stock_modelo($conexion, [
                "id_sucursal" => $detalle['local'],
                "tipo" => $detalle['tipo'],
                "id_articulo" => $detalle['producto'],
                "cantidad" => $detalle['cantidad'],
                "precio_venta" => $detalle['precioVenta'],
                "costo" => $detalle['costo'],
                "nro_ticket" => $detalle['nroTicket'],
                "pos" => $detalle['pos'],
                "usuario" => $detalle['usuario'],
                "signo" => $detalle['signo'],
                "referencia" => $detalle['referencia']
            ]);

            return true;
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

        protected static function listar_compras_modelo($inicio, $registros, $filtrosSQL, $orderSQL)
        {
            $conexion = mainModel::conectar();

            $selectSQL = "
                SELECT
                    co.idcompra_cabecera,
                    co.id_usuario,
                    co.id_sucursal,
                    co.fecha_creacion,
                    co.estado AS estadoCO,
                    co.nro_factura,
                    co.condicion,
                    co.fecha_factura,
                    co.total_compra,
                    co.idproveedores,
                    co.updated,
                    co.updatedby,
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
                FROM compra_cabecera co
                INNER JOIN proveedores p ON p.idproveedores = co.idproveedores
                INNER JOIN usuarios u ON u.id_usuario = co.id_usuario
                WHERE co.id_sucursal = '" . $_SESSION['nick_sucursal'] . "'
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

        protected static function detalle_compra_modelo($idcompra_cabecera, $id_sucursal)
        {
            $conexion = mainModel::conectar();

            $cabecera = $conexion->prepare("
                SELECT
                    co.idcompra_cabecera,
                    co.nro_factura,
                    co.fecha_factura,
                    co.nro_timbrado,
                    co.vencimiento_timbrado,
                    co.condicion,
                    co.compra_intervalo,
                    co.total_compra,
                    co.estado,
                    p.razon_social,
                    p.ruc,
                    u.usu_nombre,
                    u.usu_apellido
                FROM compra_cabecera co
                INNER JOIN proveedores p ON p.idproveedores = co.idproveedores
                INNER JOIN usuarios u ON u.id_usuario = co.id_usuario
                WHERE co.idcompra_cabecera = :idcompra
                AND co.id_sucursal = :sucursal
                LIMIT 1
            ");
            $cabecera->execute([
                ':idcompra' => $idcompra_cabecera,
                ':sucursal' => $id_sucursal
            ]);

            $detalle = $conexion->prepare("
                SELECT
                    a.codigo,
                    a.desc_articulo,
                    cd.cantidad_recibida,
                    cd.precio_unitario,
                    cd.tipo_iva,
                    cd.ivaPro,
                    cd.subtotal
                FROM compra_detalle cd
                INNER JOIN compra_cabecera co ON co.idcompra_cabecera = cd.idcompra_cabecera
                INNER JOIN articulos a ON a.id_articulo = cd.id_articulo
                WHERE cd.idcompra_cabecera = :idcompra
                AND co.id_sucursal = :sucursal
                ORDER BY a.desc_articulo ASC
            ");
            $detalle->execute([
                ':idcompra' => $idcompra_cabecera,
                ':sucursal' => $id_sucursal
            ]);

            $libro = $conexion->prepare("
                SELECT exenta, gravada_5, iva_5, gravada_10, iva_10, total, estado
                FROM libro_compra
                WHERE idcompra_cabecera = :idcompra
                AND id_sucursal = :sucursal
                LIMIT 1
            ");
            $libro->execute([
                ':idcompra' => $idcompra_cabecera,
                ':sucursal' => $id_sucursal
            ]);

            $cuentas = $conexion->prepare("
                SELECT COUNT(*) AS cuotas, COALESCE(SUM(monto), 0) AS monto, COALESCE(SUM(saldo), 0) AS saldo
                FROM cuentas_a_pagar
                WHERE idcompra_cabecera = :idcompra
                AND id_sucursal = :sucursal
                AND estado <> 0
            ");
            $cuentas->execute([
                ':idcompra' => $idcompra_cabecera,
                ':sucursal' => $id_sucursal
            ]);

            return [
                'cabecera' => $cabecera->fetch(PDO::FETCH_ASSOC),
                'detalle' => $detalle->fetchAll(PDO::FETCH_ASSOC),
                'libro' => $libro->fetch(PDO::FETCH_ASSOC),
                'cuentas' => $cuentas->fetch(PDO::FETCH_ASSOC)
            ];
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
            $conexion = $datos['conexion'] ?? mainModel::conectar();
            $sql = $conexion->prepare("
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

        /** modelo revertir OC al anular compra */
        protected static function revertir_oc_compra_modelo($datos)
        {
            $pdo = $datos['conexion'] ?? mainModel::conectar();
            $detalles = compraModelo::datos_detalle_compra_modelo($datos['idcompra_cabecera'], $datos['id_sucursal'], $pdo)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($detalles as $d) {
                $sql_det = $pdo->prepare("
                    UPDATE orden_compra_detalle d
                    INNER JOIN orden_compra c
                        ON c.idorden_compra = d.idorden_compra
                    SET d.cantidad_pendiente = LEAST(d.cantidad, d.cantidad_pendiente + :cantidad_recibida)
                    WHERE d.idorden_compra = :idorden_compra
                    AND d.id_articulo = :id_articulo
                    AND c.id_sucursal = :id_sucursal
                    AND c.estado <> 0");

                $sql_det->bindParam(":cantidad_recibida", $d['cantidad_recibida'], PDO::PARAM_STR);
                $sql_det->bindParam(":idorden_compra", $datos['idorden_compra'], PDO::PARAM_INT);
                $sql_det->bindParam(":id_articulo", $d['id_articulo'], PDO::PARAM_INT);
                $sql_det->bindParam(":id_sucursal", $datos['id_sucursal'], PDO::PARAM_INT);
                $sql_det->execute();

                if ($sql_det->rowCount() === 0) {
                    throw new Exception("No se pudo revertir el detalle de la OC para el articulo " . $d['id_articulo']);
                }
            }

            $sql_oc = $pdo->prepare("
                UPDATE orden_compra
                SET estado = 1,
                    updatedby = :updatedby,
                    updated = NOW()
                WHERE idorden_compra = :idorden_compra
                AND id_sucursal = :id_sucursal
                AND estado <> 0");

            $sql_oc->bindParam(":updatedby", $datos['updatedby'], PDO::PARAM_INT);
            $sql_oc->bindParam(":idorden_compra", $datos['idorden_compra'], PDO::PARAM_INT);
            $sql_oc->bindParam(":id_sucursal", $datos['id_sucursal'], PDO::PARAM_INT);
            $sql_oc->execute();

            return true;
        }
        /** fin modelo */

        /** modelo obtener detalles de compra (multisucursal) */
        protected static function datos_detalle_compra_modelo($idcompra_cabecera, $id_sucursal, $conexion = null)
        {
            $conexion = $conexion ?? mainModel::conectar();

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
            $conexion = $datos['conexion'] ?? mainModel::conectar();
            mainModel::registrar_movimiento_stock_modelo($conexion, [
                "id_sucursal" => $datos['id_sucursal'],
                "tipo" => $datos['tipo'] ?? "SALIDA STOCK",
                "id_articulo" => $datos['id_articulo'],
                "cantidad" => $datos['cantidad'],
                "precio_venta" => 0,
                "costo" => $datos['costo'] ?? 0,
                "usuario" => $datos['usuario'],
                "signo" => -1,
                "referencia" => $datos['referencia']
            ]);
            return true;
        }
        /** fin modelo */
        /** modelo movimiento stock anulacion */
        protected static function movimiento_stock_anulacion_modelo($datos)
        {
            $conexion = $datos['conexion'] ?? mainModel::conectar();
            mainModel::registrar_movimiento_stock_modelo($conexion, [
                "id_sucursal" => $datos['LocalId'],
                "tipo" => "ANULACION COMPRA",
                "id_articulo" => $datos['ProductoId'],
                "cantidad" => $datos['Cantidad'],
                "precio_venta" => 0,
                "costo" => $datos['Costo'],
                "nro_ticket" => $datos['Referencia'],
                "pos" => null,
                "usuario" => $datos['Usuario'],
                "signo" => -1,
                "referencia" => $datos['Referencia']
            ]);
            return true;
        }
        /** fin modelo */
        /** modelo anular cuentas a pagar (multisucursal) */
        protected static function anular_cuentas_pagar_modelo($idcompra_cabecera, $id_sucursal, $conexion = null)
        {
            $conexion = $conexion ?? mainModel::conectar();

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
            gravada_5, iva_5, gravada_10, iva_10, total,estado, fecha_registro) 
            VALUES (:idcompra, :id_sucursal, :fecha, :tipo, :serie, :numero, :proveedor, :prov_nom, :prov_ruc, :exenta, :gravada5, :iva5, :gravada10, :iva10, :total, 1, NOW())");

            return $sql->execute($d) ? $sql : $sql;
        }

        public static function anular_libro_compra_modelo($idcompra, $idsucursal, $conexion = null)
        {
            $sql = "
            UPDATE libro_compra
            SET estado = 0
            WHERE idcompra_cabecera = :idcompra
            AND id_sucursal = :idsucursal
            AND estado = 1
            ";

            $conexion = $conexion ?? mainModel::conectar();
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":idcompra", $idcompra, PDO::PARAM_INT);
            $stmt->bindParam(":idsucursal", $idsucursal, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt;
        }
    }
