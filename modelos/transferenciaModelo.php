<?php
require_once "mainModel.php";

class transferenciaModelo extends mainModel
{
    /* =========================================
       CREAR TRANSFERENCIA + REMISIÓN
    ========================================= */
    protected static function crear_transferencia_modelo($datos)
    {
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            /* ================= VALIDAR TIMBRADO ================= */
            $stmtTim = $pdo->prepare("
                SELECT * FROM sucursal_timbrado
                WHERE id_sucursal = :suc
                  AND activo = 1
                  AND fecha_vencimiento >= CURDATE()
                LIMIT 1
                FOR UPDATE
            ");
            $stmtTim->execute([':suc' => $datos['sucursal_origen']]);
            $timbrado = $stmtTim->fetch(PDO::FETCH_ASSOC);

            if (!$timbrado) {
                throw new Exception("No existe timbrado activo para la sucursal");
            }

            /* ================= NUMERACIÓN REMISIÓN ================= */
            $stmtDoc = $pdo->prepare("
                SELECT * FROM sucursal_documento
                WHERE id_sucursal = :suc
                  AND tipo_documento = 'remision'
                  AND activo = 1
                LIMIT 1
                FOR UPDATE
            ");
            $stmtDoc->execute([':suc' => $datos['sucursal_origen']]);
            $doc = $stmtDoc->fetch(PDO::FETCH_ASSOC);

            if (!$doc) {
                throw new Exception("No existe numeración activa de remisión");
            }

            $nuevoNumero = $doc['numero_actual'] + 1;
            $nroRemision = sprintf(
                "%s-%s-%07d",
                $doc['establecimiento'],
                $doc['punto_expedicion'],
                $nuevoNumero
            );

            /* ================= CREAR TRANSFERENCIA ================= */
            $stmtTrans = $pdo->prepare("
                INSERT INTO transferencia_stock
                (sucursal_origen, sucursal_destino, estado, observacion, usuario_envia)
                VALUES (:origen, :destino, 'en_transito', :obs, :usuario)
            ");
            $stmtTrans->execute([
                ':origen'  => $datos['sucursal_origen'],
                ':destino' => $datos['sucursal_destino'],
                ':obs'     => $datos['observacion'],
                ':usuario' => $datos['usuario']
            ]);

            $idTransferencia = $pdo->lastInsertId();

            /* ================= DETALLE + STOCK ================= */
            foreach ($datos['productos'] as $idProducto => $cantidad) {

                // Validar stock
                $stmtStock = $pdo->prepare("
                    SELECT stock FROM stock
                    WHERE id_producto = :prod
                      AND id_sucursal = :suc
                    FOR UPDATE
                ");
                $stmtStock->execute([
                    ':prod' => $idProducto,
                    ':suc'  => $datos['sucursal_origen']
                ]);
                $stock = $stmtStock->fetchColumn();

                if ($stock < $cantidad) {
                    throw new Exception("Stock insuficiente para producto ID {$idProducto}");
                }

                // Insert detalle transferencia
                $stmtDet = $pdo->prepare("
                    INSERT INTO transferencia_stock_detalle
                    (idtransferencia, id_producto, cantidad)
                    VALUES (:id, :prod, :cant)
                ");
                $stmtDet->execute([
                    ':id'   => $idTransferencia,
                    ':prod' => $idProducto,
                    ':cant' => $cantidad
                ]);

                // Descontar stock origen
                $stmtUpd = $pdo->prepare("
                    UPDATE stock
                    SET stock = stock - :cant
                    WHERE id_producto = :prod
                      AND id_sucursal = :suc
                ");
                $stmtUpd->execute([
                    ':cant' => $cantidad,
                    ':prod' => $idProducto,
                    ':suc'  => $datos['sucursal_origen']
                ]);
            }

            /* ================= CREAR NOTA DE REMISIÓN ================= */
            $stmtRem = $pdo->prepare("
                INSERT INTO nota_remision
                (idcompra_cabecera, id_usuario, id_sucursal,
                 nro_remision, nombre_transpo, ci_transpo, cel_transpo,
                 transportista, ruc_transport,
                 vehimarca, vehimodelo, vehichapa,
                 fechaenvio, fechallegada, motivo_remision,
                 tipo, idtransferencia)
                VALUES
                (0, :usuario, :suc,
                 :nro, :chofer, :ci, :cel,
                 :transp, :ruc,
                 :marca, :modelo, :chapa,
                 :envio, :llegada, :motivo,
                 'transferencia', :idtrans)
            ");
            $stmtRem->execute([
                ':usuario'  => $datos['usuario'],
                ':suc'      => $datos['sucursal_origen'],
                ':nro'      => $nroRemision,
                ':chofer'   => $datos['nombre_transpo'],
                ':ci'       => $datos['ci_transpo'],
                ':cel'      => $datos['cel_transpo'],
                ':transp'   => $datos['transportista'],
                ':ruc'      => $datos['ruc_transport'],
                ':marca'    => $datos['vehimarca'],
                ':modelo'   => $datos['vehimodelo'],
                ':chapa'    => $datos['vehichapa'],
                ':envio'    => $datos['fechaenvio'],
                ':llegada'  => $datos['fechallegada'],
                ':motivo'   => $datos['motivo'],
                ':idtrans'  => $idTransferencia
            ]);

            // actualizar numeración
            $pdo->prepare("
                UPDATE sucursal_documento
                SET numero_actual = :num
                WHERE id_documento = :id
            ")->execute([
                ':num' => $nuevoNumero,
                ':id'  => $doc['id_documento']
            ]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return $e->getMessage();
        }
    }
}
