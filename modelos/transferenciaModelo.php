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
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return $e->getMessage();
        }
    }

    public static function buscar_producto_modelo($q, $idSucursal)
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
        LIMIT 10
    ";

        $stmt = mainModel::conectar()->prepare($sql);

        $stmt->execute([
            ':suc' => (int)$idSucursal,
            ':q'   => '%' . $q . '%'
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscar_sucursal_destino_modelo($q, $idSucursalOrigen)
    {
        $sql = "
        SELECT 
            id_sucursal,
            suc_descri
        FROM sucursales
        WHERE estado = 1
          AND id_sucursal <> :origen
          AND suc_descri LIKE :q
        LIMIT 10
    ";

        $stmt = mainModel::conectar()->prepare($sql);

        $stmt->execute([
            ':origen' => (int)$idSucursalOrigen,
            ':q'      => '%' . $q . '%'
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
