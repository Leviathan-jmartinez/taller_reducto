<?php
require_once "mainModel.php";

class notasCreDeModelo
{
    /* ================= BUSCAR FACTURAS ================= */
    public static function buscarFacturas($texto)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT 
            idcompra_cabecera,
            nro_factura,
            fecha_factura,
            total_compra,
            idproveedores
        FROM compra_cabecera
        WHERE REPLACE(nro_factura, ' ', '') LIKE :t
        ORDER BY idcompra_cabecera DESC
        LIMIT 10
    ");

        $sql->bindValue(':t', '%' . $texto . '%', PDO::PARAM_STR);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    /* ================= OBTENER FACTURA ================= */
    public static function obtenerFactura($id)
    {
        $sql = mainModel::conectar()->prepare("
            SELECT *
            FROM compra_cabecera
            WHERE idcompra_cabecera = :id
            LIMIT 1
        ");

        $sql->bindValue(':id', $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /* ================= OBTENER DETALLE COMPRA ================= */
    public static function obtenerDetalleCompra($idcompra)
    {
        $sql = mainModel::conectar()->prepare("
            SELECT 
                d.id_articulo,
                a.desc_articulo,
                d.cantidad_recibida,
                d.precio_unitario,
                d.subtotal,
                ti.tipo_impuesto_descri,
                ti.ratevalueiva,
                ti.divisor
            FROM compra_detalle d
            INNER JOIN articulos a ON a.id_articulo = d.id_articulo
            INNER JOIN tipo_impuesto ti ON ti.idiva = a.idiva
            WHERE d.idcompra_cabecera = :id
        ");

        $sql->bindValue(':id', $idcompra, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insertarNotaCompraModelo($d)
    {
        $sql = mainModel::conectar()->prepare("
            INSERT INTO nota_compra
            (idproveedor, tipo, serie, nro_documento, fecha,
             idcompra_cabecera, subtotal, iva_5, iva_10, total,
             descripcion, estado, idusuario, fecha_creacion, timbrado)
            VALUES
            (:prov, :tipo, :serie, :nro, :fecha,
             :idcompra, :subtotal, :iva5, :iva10, :total,
             :desc, 1, :usuario, NOW(), :timbrado)
        ");

        $sql->execute([
            ':prov'     => $d['idproveedor'],
            ':tipo'     => $d['tipo'],
            ':serie'    => $d['serie'],
            ':nro'      => $d['nro'],
            ':fecha'    => $d['fecha'],
            ':idcompra' => $d['idcompra'],
            ':subtotal' => $d['subtotal'],
            ':iva5'     => $d['iva5'],
            ':iva10'    => $d['iva10'],
            ':total'    => $d['total'],
            ':desc'     => $d['descripcion'],
            ':usuario'  => $d['usuario'],
            ':timbrado' => $d['timbrado']
        ]);

        return mainModel::conectar()->lastInsertId();
    }
    public static function insertarDetalleNotaCompraModelo($idNota, $detalle)
    {
        $sql = mainModel::conectar()->prepare("
            INSERT INTO nota_compra_detalle
            (idnota_compra, id_articulo, descripcion, cantidad,
             precio_unitario, valor_exenta, valor_5, valor_10)
            VALUES
            (:nota, :art, :desc, :cant, :precio, :exenta, :v5, :v10)
        ");

        foreach ($detalle as $d) {

            $monto = $d['cantidad'] * $d['precio'];

            $base5 = $d['iva_5'] > 0 ? $monto - $d['iva_5'] : 0;
            $base10 = $d['iva_10'] > 0 ? $monto - $d['iva_10'] : 0;
            $exenta = ($d['iva_5'] == 0 && $d['iva_10'] == 0) ? $monto : 0;

            $sql->execute([
                ':nota'   => $idNota,
                ':art'    => $d['id_articulo'],
                ':desc'   => $d['descripcion'],
                ':cant'   => $d['cantidad'],
                ':precio' => $d['precio'],
                ':exenta' => $exenta,
                ':v5'     => round($base5, 2),
                ':v10'    => round($base10, 2)
            ]);
        }
    }

    public static function impactarNotaCompraModelo($d)
    {
        $monto = $d['monto'];
        if ($d['tipo'] === 'credito') {
            $monto *= -1;
        }

        $sql = mainModel::conectar()->prepare("
            INSERT INTO cuentas_a_pagar
            (idcompra_cabecera, tipo_movimiento, referencia_tipo,
             referencia_id, monto, saldo, fecha_movimiento, estado)
            VALUES
            (:idcompra, :tipo, 'nota_compra',
             :ref, :monto, :monto, NOW(), 1)
        ");

        $sql->execute([
            ':idcompra' => $d['idcompra'],
            ':tipo'     => $d['tipo'],
            ':ref'      => $d['idnota'],
            ':monto'    => $monto
        ]);
    }
}
