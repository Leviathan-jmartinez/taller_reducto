<?php
require_once "mainModel.php";

class transferenciaModelo extends mainModel
{
    protected static function crear_transferencia($d)
    {
        $pdo = mainModel::conectar();
        // ================= NORMALIZAR CAMPOS OPCIONALES =================
        $d['ci_transpo']    = $d['ci_transpo']    ?? null;
        $d['cel_transpo']   = $d['cel_transpo']   ?? null;
        $d['transportista'] = $d['transportista'] ?? null;
        $d['ruc_transport'] = $d['ruc_transport'] ?? null;
        $d['vehimarca']     = $d['vehimarca']     ?? null;
        $d['vehimodelo']    = $d['vehimodelo']    ?? null;
        $d['vehichapa']     = $d['vehichapa']     ?? null;

        // obligatorios BD con fallback seguro
        $d['fechaenvio']    = $d['fechaenvio']    ?? date('Y-m-d');
        $d['fechallegada']  = $d['fechallegada']  ?? date('Y-m-d');
        $d['observacion']   = $d['observacion']   ?? '';

        try {
            $pdo->beginTransaction();

            /* ================= VALIDAR TIMBRADO ================= */
            $qTim = $pdo->prepare("
                SELECT timbrado, fecha_vencimiento
                FROM sucursal_timbrado
                WHERE id_sucursal = :suc
                  AND activo = 1
                  AND fecha_vencimiento >= CURDATE()
                LIMIT 1
                FOR UPDATE
            ");
            $qTim->execute([':suc' => $d['id_sucursal_origen']]);
            $timbrado = $qTim->fetch(PDO::FETCH_ASSOC);

            if (!$timbrado) {
                throw new Exception("La sucursal no tiene timbrado activo");
            }

            /* ================= NUMERACIÓN ================= */
            $qDoc = $pdo->prepare("
                SELECT id_documento, establecimiento, punto_expedicion, numero_actual
                FROM sucursal_documento
                WHERE id_sucursal = :suc
                  AND tipo_documento = 'remision'
                  AND activo = 1
                LIMIT 1
                FOR UPDATE
            ");
            $qDoc->execute([':suc' => $d['id_sucursal_origen']]);
            $doc = $qDoc->fetch(PDO::FETCH_ASSOC);

            if (!$doc) {
                throw new Exception("No hay numeración de remisión configurada");
            }

            $nuevoNumero = $doc['numero_actual'] + 1;
            $nroRemision = sprintf(
                "%s-%s-%07d",
                $doc['establecimiento'],
                $doc['punto_expedicion'],
                $nuevoNumero
            );

            /* ================= CREAR TRANSFERENCIA ================= */
            $qTrans = $pdo->prepare("
                INSERT INTO transferencia_stock
                (sucursal_origen, sucursal_destino, estado, observacion, usuario_envia)
                VALUES (:origen,:destino,'en_transito',:obs,:usr)
            ");
            $qTrans->execute([
                ':origen' => $d['id_sucursal_origen'],
                ':destino' => $d['id_sucursal_destino'],
                ':obs' => $d['observacion'],
                ':usr' => $d['id_usuario']
            ]);

            $idTransferencia = $pdo->lastInsertId();

            /* ================= DETALLE + STOCK ================= */
            foreach ($d['productos'] as $idProducto => $cantidad) {

                $cantidad = (float)$cantidad;

                $qStock = $pdo->prepare("
                    SELECT stockDisponible
                    FROM stock
                    WHERE id_articulo = :prod
                    AND id_sucursal = :suc
                    FOR UPDATE    ");
                $qStock->execute([
                    ':prod' => $idProducto,
                    ':suc'  => $d['id_sucursal_origen']
                ]);

                $stock = (float)$qStock->fetchColumn();

                if ($stock === false || $stock < $cantidad) {
                    throw new Exception(
                        "Stock insuficiente para el producto ID {$idProducto}. Stock actual: {$stock}"
                    );
                }

                // detalle transferencia
                $pdo->prepare("
                        INSERT INTO transferencia_stock_detalle
                        (idtransferencia, id_articulo, cantidad)
                        VALUES (:id, :prod, :cant)")->execute([
                    ':id'   => $idTransferencia,
                    ':prod' => $idProducto,
                    ':cant' => $cantidad
                ]);

                // descontar stock
                $pdo->prepare("
                        UPDATE stock
                        SET stockDisponible = stockDisponible - :cant
                        WHERE id_articulo = :prod
                        AND id_sucursal = :suc")->execute([
                    ':cant' => $cantidad,
                    ':prod' => $idProducto,
                    ':suc'  => $d['id_sucursal_origen']
                ]);

                // ================= MOVIMIENTO DE STOCK (SALIDA) =================

                // obtener costo (ya lo usás para remisión)
                $qCosto = $pdo->prepare("
                SELECT precio_compra
                FROM articulos
                WHERE id_articulo = :id");
                $qCosto->execute([':id' => $idProducto]);
                $costo = (float)$qCosto->fetchColumn();

                $pdo->prepare("
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
                        MovStockUsuario,
                        MovStockSigno,
                        MovStockReferencia
                    )
                    VALUES
                    (
                        :local,
                        'TRANSFERENCIA_SALIDA',
                        :prod,
                        :cant,
                        0,
                        :costo,
                        NOW(),
                        :nro,
                        :usr,
                        -1,
                        :ref)")->execute([
                    ':local' => $d['id_sucursal_origen'],
                    ':prod'  => $idProducto,
                    ':cant'  => $cantidad,
                    ':costo' => $costo,
                    ':nro'   => $nroRemision,
                    ':usr'   => $d['id_usuario'],
                    ':ref'   => 'TRANSFERENCIA #' . $idTransferencia
                ]);
            }


            /* ================= NOTA DE REMISIÓN ================= */
            $pdo->prepare("
                INSERT INTO nota_remision
                (idcompra_cabecera,id_usuario,id_sucursal,
                nro_remision,nombre_transpo,ci_transpo,cel_transpo,
                transportista,ruc_transport,
                vehimarca,vehimodelo,vehichapa,
                fechaenvio,fechallegada,motivo_remision,
                tipo,idtransferencia)
                VALUES
                (NULL,:usr,:suc,
                :nro,:chofer,:ci,:cel,
                :transp,:ruc,
                :marca,:modelo,:chapa,
                :envio,:llegada,:motivo,
                'transferencia',:idtrans)")->execute([
                ':usr'    => $d['id_usuario'],
                ':suc'    => $d['id_sucursal_origen'],
                ':nro'    => $nroRemision,
                ':chofer' => $d['chofer'],
                ':ci'     => $d['ci_transpo'],
                ':cel'    => $d['cel_transpo'],
                ':transp' => $d['transportista'],
                ':ruc'    => $d['ruc_transport'],
                ':marca'  => $d['vehimarca'],
                ':modelo' => $d['vehimodelo'],
                ':chapa'  => $d['vehichapa'],
                ':envio'  => $d['fechaenvio'],
                ':llegada' => $d['fechallegada'],
                ':motivo' => $d['observacion'],
                ':idtrans' => $idTransferencia
            ]);
            $idNotaRemision = $pdo->lastInsertId();

            foreach ($d['productos'] as $idProducto => $cantidad) {

                $cantidad = (float)$cantidad;

                // Traer costo actual del artículo
                $qArt = $pdo->prepare("
                SELECT precio_compra
                FROM articulos
                WHERE id_articulo = :id");
                $qArt->execute([':id' => $idProducto]);
                $costo = (float)$qArt->fetchColumn();

                $subtotal = $cantidad * $costo;

                $pdo->prepare("
                        INSERT INTO nota_remision_detalle
                        (idnota_remision, id_articulo, cantidad, costo, subtotal)
                        VALUES (:nota, :art, :cant, :costo, :sub)")->execute([
                    ':nota'  => $idNotaRemision,
                    ':art'   => $idProducto,
                    ':cant'  => $cantidad,
                    ':costo' => $costo,
                    ':sub'   => $subtotal
                ]);
            }


            /* ================= ACTUALIZAR NUMERACIÓN ================= */
            $pdo->prepare("
                UPDATE sucursal_documento
                SET numero_actual = :num
                WHERE id_documento = :id
            ")->execute([
                ':num' => $nuevoNumero,
                ':id' => $doc['id_documento']
            ]);

            $pdo->commit();
            return [
                'ok' => true,
                'idnota_remision' => $idNotaRemision
            ];
        } catch (Exception $e) {
            $pdo->rollBack();
            return $e->getMessage();
        }
    }

    protected static function buscar_producto_modelo($q, $idSucursal)
    {
        $sql = "
        SELECT 
            a.id_articulo,
            a.desc_articulo,
            s.stockDisponible
        FROM stock s
        INNER JOIN articulos a 
            ON a.id_articulo = s.id_articulo
        WHERE s.id_sucursal = :suc
          AND s.stockDisponible > 0
          AND a.desc_articulo LIKE :q
        LIMIT 10";

        $stmt = mainModel::conectar()->prepare($sql);

        $stmt->execute([
            ':suc' => (int)$idSucursal,
            ':q'   => '%' . $q . '%'
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function buscar_sucursal_destino_modelo($q, $idSucursalOrigen)
    {
        $sql = "
        SELECT 
            id_sucursal,
            suc_descri
        FROM sucursales
        WHERE estado = 1
          AND id_sucursal <> :origen
          AND suc_descri LIKE :q
        LIMIT 10";

        $stmt = mainModel::conectar()->prepare($sql);

        $stmt->execute([
            ':origen' => (int)$idSucursalOrigen,
            ':q'      => '%' . $q . '%'
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function obtener_transferencia_para_recibir_modelo($idTransferencia)
    {
        $pdo = mainModel::conectar();

        /* ===== CABECERA ===== */
        $cab = $pdo->prepare("
        SELECT
            t.idtransferencia,
            t.estado,
            t.sucursal_origen,
            so.suc_descri AS suc_origen,
            t.sucursal_destino,
            sd.suc_descri AS suc_destino
        FROM transferencia_stock t
        INNER JOIN sucursales so ON so.id_sucursal = t.sucursal_origen
        INNER JOIN sucursales sd ON sd.id_sucursal = t.sucursal_destino
        WHERE t.idtransferencia = :id");
        $cab->execute([':id' => $idTransferencia]);
        $cabecera = $cab->fetch(PDO::FETCH_ASSOC);

        if (!$cabecera) {
            return null;
        }

        /* ===== DETALLE ===== */
        $det = $pdo->prepare("
        SELECT
            d.id_articulo,
            a.desc_articulo,
            d.cantidad,
            d.cantidad_recibida
        FROM transferencia_stock_detalle d
        INNER JOIN articulos a ON a.id_articulo = d.id_articulo
        WHERE d.idtransferencia = :id  ");
        $det->execute([':id' => $idTransferencia]);
        $detalle = $det->fetchAll(PDO::FETCH_ASSOC);

        return [
            'cabecera' => $cabecera,
            'detalle'  => $detalle
        ];
    }


    protected static function recibir_transferencia_modelo($idTransferencia, $idUsuario, $cantidadesRecibidas)
    {
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            /* ========= BLOQUEAR TRANSFERENCIA ========= */
            $q = $pdo->prepare("
            SELECT sucursal_destino, estado
            FROM transferencia_stock
            WHERE idtransferencia = :id
            FOR UPDATE
        ");
            $q->execute([':id' => $idTransferencia]);
            $t = $q->fetch(PDO::FETCH_ASSOC);

            if (!$t || $t['estado'] !== 'en_transito') {
                throw new Exception("Transferencia inválida");
            }

            // 🔒 VALIDACIÓN DE SUCURSAL DESTINO
            if ($t['sucursal_destino'] != $_SESSION['nick_sucursal']) {
                throw new Exception("La transferencia no corresponde a su sucursal");
            }

            $destino = $t['sucursal_destino'];

            /* ========= DETALLE ENVIADO ========= */
            $det = $pdo->prepare("
            SELECT id_articulo, cantidad
            FROM transferencia_stock_detalle
            WHERE idtransferencia = :id
        ");
            $det->execute([':id' => $idTransferencia]);
            $detalle = $det->fetchAll(PDO::FETCH_ASSOC);

            if (!$detalle) {
                throw new Exception("Transferencia sin detalle");
            }

            foreach ($detalle as $d) {

                $idArticulo = $d['id_articulo'];
                $enviado    = (float)$d['cantidad'];
                $recibido   = isset($cantidadesRecibidas[$idArticulo])
                    ? (float)$cantidadesRecibidas[$idArticulo]
                    : 0;

                // ===== VALIDACIONES =====
                if ($recibido < 0) {
                    throw new Exception("Cantidad inválida para artículo {$idArticulo}");
                }

                if ($recibido > $enviado) {
                    throw new Exception("Cantidad recibida mayor a la enviada (artículo {$idArticulo})");
                }

                /* ========= ACTUALIZAR CANTIDAD RECIBIDA ========= */
                $pdo->prepare("
                UPDATE transferencia_stock_detalle
                SET cantidad_recibida = :rec
                WHERE idtransferencia = :id
                  AND id_articulo = :art
            ")->execute([
                    ':rec' => $recibido,
                    ':id'  => $idTransferencia,
                    ':art' => $idArticulo
                ]);

                /* ========= SUMAR STOCK DESTINO (SOLO RECIBIDO) ========= */
                if ($recibido > 0) {

                    $pdo->prepare("
                        INSERT INTO stock
                            (id_articulo, id_sucursal, stockcant_max, stockcant_min, stockDisponible, stockUltActualizacion, stockUsuActualizacion, stockultimoIdActualizacion)
                        VALUES
                            (:art, :suc, 200, 15, :cant, NOW(), :usr, :id)
                        ON DUPLICATE KEY UPDATE
                            stockDisponible = stockDisponible + :cant,
                            stockUltActualizacion = NOW(),
                            stockultimoIdActualizacion = :id,
                            stockUsuActualizacion = :usr")->execute([
                        ':art' => $idArticulo,
                        ':suc' => $destino,
                        ':cant' => $recibido,
                        ':usr' => $idUsuario,
                        ':id' => $idTransferencia
                    ]);


                    /* ========= MOVIMIENTO DE STOCK (ENTRADA) ========= */
                    $pdo->prepare("
                        INSERT INTO sucmovimientostock
                            (
                                LocalId,
                                TipoMovStockId,
                                MovStockProductoId,
                                MovStockCantidad,
                                MovStockPrecioVenta,
                                MovStockCosto,
                                MovStockFechaHora,
                                MovStockUsuario,
                                MovStockSigno,
                                MovStockReferencia
                            )
                        VALUES
                            (:loc, 'TRANSFERENCIA_ENTRADA', :art,
                            :cant, 0, 0, NOW(), :usr, 1, :ref)")->execute([
                        ':loc' => $destino,
                        ':art' => $idArticulo,
                        ':cant' => $recibido,
                        ':usr' => $idUsuario,
                        ':ref' => 'TRANSFERENCIA #' . $idTransferencia
                    ]);
                }
            }

            /* ========= MARCAR TRANSFERENCIA RECIBIDA ========= */
            $pdo->prepare("
            UPDATE transferencia_stock
            SET estado = 'recibido',
                usuario_recibe = :usr
            WHERE idtransferencia = :id
        ")->execute([
                ':usr' => $idUsuario,
                ':id'  => $idTransferencia
            ]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return $e->getMessage();
        }
    }
}
