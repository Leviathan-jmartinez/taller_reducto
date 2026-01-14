<?php
require_once "mainModel.php";

class notasCreDeModelo extends mainModel
{

    protected static function buscarFacturas($texto)
    {
        $conexion = mainModel::conectar();
        session_start(['name' => 'STR']);
        $sql = $conexion->prepare("
        SELECT 
            idcompra_cabecera,
            nro_factura,
            fecha_factura,
            total_compra,
            idproveedores
        FROM compra_cabecera
        WHERE 
            id_sucursal = :sucursal
            AND REPLACE(nro_factura, ' ', '') LIKE :t
        ORDER BY idcompra_cabecera DESC
        LIMIT 10");

        $sql->bindValue(':sucursal', $_SESSION['nick_sucursal'], PDO::PARAM_INT);
        $sql->bindValue(':t', '%' . str_replace(' ', '', $texto) . '%', PDO::PARAM_STR);

        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    protected static function obtenerFactura($id)
    {
        session_start(['name' => 'STR']);
        $sql = mainModel::conectar()->prepare("
            SELECT 
            *
        FROM compra_cabecera c
        INNER JOIN proveedores p ON p.idproveedores = c.idproveedores
        WHERE c.idcompra_cabecera = :id and c.id_sucursal = :sucursal
        LIMIT 1");

        $sql->bindValue(':id', $id, PDO::PARAM_INT);
        $sql->bindValue(':sucursal', $_SESSION['nick_sucursal'], PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }


    protected static function obtenerDetalleCompra($idcompra)
    {
        session_start(['name' => 'STR']);
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("
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
        INNER JOIN compra_cabecera c 
            ON c.idcompra_cabecera = d.idcompra_cabecera
        INNER JOIN articulos a 
            ON a.id_articulo = d.id_articulo
        INNER JOIN tipo_impuesto ti 
            ON ti.idiva = a.idiva
        WHERE 
            d.idcompra_cabecera = :id
            AND c.id_sucursal = :sucursal");

        $sql->bindValue(':id', $idcompra, PDO::PARAM_INT);
        $sql->bindValue(':sucursal', $_SESSION['nick_sucursal'], PDO::PARAM_INT);

        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function insertarNotaCompraModelo(PDO $pdo, $d)
    {
        $sql = $pdo->prepare("
        INSERT INTO nota_compra
        (idproveedor, id_sucursal, tipo, movimiento_stock, nro_documento, fecha,
         idcompra_cabecera, total, descripcion,
         estado, idusuario, fecha_creacion, timbrado)
        VALUES
        (:prov, :sucursal, :tipo, :mov_stock, :nro, :fecha,
         :idcompra, :total, :desc,
         1, :usuario, NOW(), :timbrado)");

        $sql->execute([
            ':prov'     => $d['idproveedor'],
            ':sucursal' => $d['id_sucursal'],
            ':tipo'     => $d['tipo'],
            ':mov_stock' => $d['movimiento_stock'],
            ':nro'      => $d['nro'],
            ':fecha'    => $d['fecha'],
            ':idcompra' => $d['idcompra'],
            ':total'    => $d['total'],
            ':desc'     => $d['descripcion'],
            ':usuario'  => $d['usuario'],
            ':timbrado' => $d['timbrado']
        ]);

        return $pdo->lastInsertId();
    }
    protected static function insertarDetalleNotaCompraModelo(PDO $pdo, $idNota, $detalle)
    {
        $sql = $pdo->prepare("
        INSERT INTO nota_compra_detalle
        (idnota_compra, id_articulo, descripcion,
         cantidad, precio_unitario, subtotal)
        VALUES
        (:nota, :art, :desc, :cant, :precio, :sub)");

        foreach ($detalle as $d) {
            $subtotal = round($d['cantidad'] * $d['precio'], 2);

            $sql->execute([
                ':nota'   => $idNota,
                ':art'    => $d['id_articulo'],
                ':desc'   => $d['descripcion'],
                ':cant'   => $d['cantidad'],
                ':precio' => $d['precio'],
                ':sub'    => $subtotal
            ]);
        }
    }

    protected static function impactarNotaCompraModelo(PDO $pdo, $d)
    {
        $sql = $pdo->prepare("
        INSERT INTO cuentas_a_pagar
        (idcompra_cabecera, id_sucursal, tipo_movimiento, referencia_tipo,
         referencia_id, monto, saldo,
         fecha_movimiento, observacion, estado)
        VALUES
        (:idcompra, :sucursal, :tipo, 'nota_compra',
         :ref, :monto, :monto,
         NOW(), :obs, 1)");

        $sql->execute([
            ':idcompra' => $d['idcompra'],
            ':sucursal' => $d['id_sucursal'],
            ':tipo'     => $d['tipo'],
            ':ref'      => $d['idnota'],
            ':monto'    => $d['monto'],
            ':obs'      => $d['obs']
        ]);
    }

    protected static function obtenerNotaCompraPorId($idNota)
    {

        $sql = mainModel::conectar()->prepare("
        SELECT *
        FROM nota_compra
        WHERE idnota_compra = :id and id_sucursal = :sucursal
        LIMIT 1   ");
        $sql->execute([':id' => $idNota, ':sucursal' => $_SESSION['nick_sucursal']]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function anularNotaCompraModelo(PDO $pdo, $idNota)
    {

        $sql = $pdo->prepare("
        UPDATE nota_compra
        SET estado = 0,
            fecha_actualizacion = NOW()
        WHERE idnota_compra = :id and id_sucursal = :sucursal");
        $sql->execute([':id' => $idNota, ':sucursal' => $_SESSION['nick_sucursal']]);
    }

    protected static function impactarAnulacionNotaModelo(PDO $pdo, $d)
    {
        $sql = $pdo->prepare("
        INSERT INTO cuentas_a_pagar
        (idcompra_cabecera, id_sucursal, tipo_movimiento, referencia_tipo,
         referencia_id, monto, saldo,
         fecha_movimiento, observacion, estado)
        VALUES
        (:idcompra, :sucursal, 'anulacion', 'nota_compra',
         :ref, :monto, :monto,
         NOW(), :obs, 1)");

        $sql->execute([
            ':idcompra' => $d['idcompra'],
            ':sucursal' => $d['id_sucursal'],
            ':ref'      => $d['idnota'],
            ':monto'    => $d['monto'],
            ':obs'      => $d['obs']
        ]);
    }

    protected static function totalNCActivasPorFactura($idcompra)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT COALESCE(SUM(total), 0) AS total_nc
        FROM nota_compra
        WHERE idcompra_cabecera = :id
          AND tipo = 'credito'
          AND estado = 1
        ");
        $sql->bindValue(':id', $idcompra, PDO::PARAM_INT);
        $sql->execute();
        return (float)$sql->fetchColumn();
    }
}
