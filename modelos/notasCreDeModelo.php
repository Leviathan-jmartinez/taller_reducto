<?php
require_once "mainModel.php";

class notasCreDeModelo extends mainModel
{
    private static $notaCompraTieneAlcance = null;

    private static function iniciarSesionSiHaceFalta()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }
    }

    protected static function buscarFacturas($texto)
    {
        $conexion = mainModel::conectar();
        self::iniciarSesionSiHaceFalta();
        $sql = $conexion->prepare("
        SELECT 
            idcompra_cabecera,
            nro_factura,
            fecha_factura,
            total_compra,
            idproveedores,
            estado
        FROM compra_cabecera
        WHERE 
            id_sucursal = :sucursal
            AND estado <> 0
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
        self::iniciarSesionSiHaceFalta();
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
        self::iniciarSesionSiHaceFalta();
        $conexion = mainModel::conectar();
        $sql = $conexion->prepare("
        SELECT 
            d.id_articulo,
            a.desc_articulo,
            d.cantidad_facturada,
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
        $params = [
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
        ];

        $campoAlcance = '';
        $valorAlcance = '';
        if (self::notaCompraTieneCampoAlcance($pdo)) {
            $campoAlcance = ', alcance';
            $valorAlcance = ', :alcance';
            $params[':alcance'] = $d['alcance'] ?? 'regularizar_diferencia';
        }

        $sql = $pdo->prepare("
        INSERT INTO nota_compra
        (idproveedor, id_sucursal, tipo, movimiento_stock, nro_documento, fecha,
         idcompra_cabecera, total, descripcion,
         estado, idusuario, fecha_creacion, timbrado{$campoAlcance})
        VALUES
        (:prov, :sucursal, :tipo, :mov_stock, :nro, :fecha,
         :idcompra, :total, :desc,
         1, :usuario, NOW(), :timbrado{$valorAlcance})");

        $sql->execute($params);

        return $pdo->lastInsertId();
    }

    private static function notaCompraTieneCampoAlcance(PDO $pdo)
    {
        if (self::$notaCompraTieneAlcance !== null) {
            return self::$notaCompraTieneAlcance;
        }

        try {
            $sql = $pdo->query("SHOW COLUMNS FROM nota_compra LIKE 'alcance'");
            self::$notaCompraTieneAlcance = (bool)$sql->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            self::$notaCompraTieneAlcance = false;
        }

        return self::$notaCompraTieneAlcance;
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

    protected static function paginarNotasCompraModelo($inicio, $registros, $filtros, $orderSQL = "ORDER BY nc.fecha_creacion DESC")
    {
        $where = ["nc.id_sucursal = :sucursal"];
        $params = [
            ':sucursal' => (int)$filtros['id_sucursal']
        ];

        if ($filtros['nro_documento'] !== '') {
            $where[] = "nc.nro_documento LIKE :nro_documento";
            $params[':nro_documento'] = '%' . $filtros['nro_documento'] . '%';
        }

        if ($filtros['tipo_nota'] !== '') {
            $where[] = "nc.tipo = :tipo_nota";
            $params[':tipo_nota'] = $filtros['tipo_nota'];
        }

        if ($filtros['fecha_inicio'] !== '' && $filtros['fecha_final'] !== '') {
            $where[] = "DATE(nc.fecha_creacion) BETWEEN :fecha_inicio AND :fecha_final";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            $params[':fecha_final'] = $filtros['fecha_final'];
        }

        $whereSql = implode(" AND ", $where);
        $fromSql = "
        FROM nota_compra nc
        INNER JOIN proveedores p ON p.idproveedores = nc.idproveedor
        INNER JOIN usuarios u ON u.id_usuario = nc.idusuario
        INNER JOIN compra_cabecera co ON co.idcompra_cabecera = nc.idcompra_cabecera
        WHERE $whereSql
        ";

        $conexion = mainModel::conectar();

        $consulta = "
        SELECT
            nc.idnota_compra,
            nc.id_sucursal,
            nc.tipo AS tipo_nota,
            nc.nro_documento,
            nc.fecha AS fecha_nota,
            nc.total AS total_nota,
            nc.estado AS estado_nota,
            nc.fecha_creacion,
            co.nro_factura,
            p.razon_social,
            u.usu_nombre,
            u.usu_apellido
        $fromSql
        $orderSQL
        LIMIT :inicio, :registros
        ";

        $datos = $conexion->prepare($consulta);
        foreach ($params as $param => $valor) {
            $datos->bindValue($param, $valor);
        }
        $datos->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
        $datos->bindValue(':registros', (int)$registros, PDO::PARAM_INT);
        $datos->execute();

        $total = $conexion->prepare("SELECT COUNT(*) $fromSql");
        foreach ($params as $param => $valor) {
            $total->bindValue($param, $valor);
        }
        $total->execute();

        return [
            'datos' => $datos->fetchAll(PDO::FETCH_ASSOC),
            'total' => (int)$total->fetchColumn()
        ];
    }

    protected static function totalNCActivasPorFactura($idcompra)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT COALESCE(SUM(total), 0) AS total_nc
        FROM nota_compra
        WHERE idcompra_cabecera = :id
          AND id_sucursal = :sucursal
          AND tipo = 'credito'
          AND estado = 1
        ");
        $sql->bindValue(':id', $idcompra, PDO::PARAM_INT);
        $sql->bindValue(':sucursal', $_SESSION['nick_sucursal'], PDO::PARAM_INT);
        $sql->execute();
        return (float)$sql->fetchColumn();
    }

    protected static function totalNDActivasPorFactura($idcompra)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT COALESCE(SUM(total), 0) AS total_nd
        FROM nota_compra
        WHERE idcompra_cabecera = :id
          AND id_sucursal = :sucursal
          AND tipo = 'debito'
          AND estado = 1
        ");
        $sql->bindValue(':id', $idcompra, PDO::PARAM_INT);
        $sql->bindValue(':sucursal', $_SESSION['nick_sucursal'], PDO::PARAM_INT);
        $sql->execute();
        return (float)$sql->fetchColumn();
    }

    protected static function saldoDisponibleNCFactura($idcompra, $totalCompra)
    {
        $totalND = self::totalNDActivasPorFactura($idcompra);
        $totalNC = self::totalNCActivasPorFactura($idcompra);

        return round(((float)$totalCompra + $totalND) - $totalNC, 2);
    }

    protected static function diferenciaCompraModelo($idcompra)
    {
        self::iniciarSesionSiHaceFalta();
        $sql = mainModel::conectar()->prepare("
        SELECT
            d.id_articulo,
            a.desc_articulo,
            GREATEST(COALESCE(d.cantidad_facturada, d.cantidad_recibida) - d.cantidad_recibida, 0) AS cantidad_diferencia,
            d.precio_unitario,
            (GREATEST(COALESCE(d.cantidad_facturada, d.cantidad_recibida) - d.cantidad_recibida, 0) * d.precio_unitario) AS monto_diferencia
        FROM compra_detalle d
        INNER JOIN compra_cabecera c ON c.idcompra_cabecera = d.idcompra_cabecera
        INNER JOIN articulos a ON a.id_articulo = d.id_articulo
        WHERE d.idcompra_cabecera = :id
          AND c.id_sucursal = :sucursal
        ");

        $sql->execute([
            ':id' => $idcompra,
            ':sucursal' => $_SESSION['nick_sucursal']
        ]);

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function regularizarCompraModelo(PDO $pdo, $idcompra, $idSucursal, $usuario)
    {
        $sql = $pdo->prepare("
        UPDATE compra_cabecera
        SET estado = 4,
            updated = NOW(),
            updatedby = :usuario
        WHERE idcompra_cabecera = :idcompra
          AND id_sucursal = :sucursal
          AND estado <> 0
        ");

        $sql->execute([
            ':usuario' => $usuario,
            ':idcompra' => $idcompra,
            ':sucursal' => $idSucursal
        ]);

        return $sql;
    }

    protected static function anularCompraPorNotaTotalModelo(PDO $pdo, $idcompra, $idSucursal, $usuario)
    {
        $sql = $pdo->prepare("
        UPDATE compra_cabecera
        SET estado = 0,
            updated = NOW(),
            updatedby = :usuario
        WHERE idcompra_cabecera = :idcompra
          AND id_sucursal = :sucursal
          AND estado <> 0
        ");

        $sql->execute([
            ':usuario' => $usuario,
            ':idcompra' => $idcompra,
            ':sucursal' => $idSucursal
        ]);

        return $sql;
    }

    protected static function restaurarCompraPorAnulacionNotaTotalModelo(PDO $pdo, $idcompra, $idSucursal, $usuario)
    {
        $estado = self::estadoCompraSegunDetalleModelo($pdo, $idcompra, $idSucursal);

        $sql = $pdo->prepare("
        UPDATE compra_cabecera
        SET estado = :estado,
            updated = NOW(),
            updatedby = :usuario
        WHERE idcompra_cabecera = :idcompra
          AND id_sucursal = :sucursal
          AND estado = 0
        ");

        $sql->execute([
            ':estado' => $estado,
            ':usuario' => $usuario,
            ':idcompra' => $idcompra,
            ':sucursal' => $idSucursal
        ]);

        return $sql;
    }

    private static function estadoCompraSegunDetalleModelo(PDO $pdo, $idcompra, $idSucursal)
    {
        $sql = $pdo->prepare("
        SELECT COUNT(*) AS diferencias
        FROM compra_detalle d
        INNER JOIN compra_cabecera c
            ON c.idcompra_cabecera = d.idcompra_cabecera
        WHERE d.idcompra_cabecera = :idcompra
          AND c.id_sucursal = :sucursal
          AND COALESCE(d.cantidad_facturada, d.cantidad_recibida) > d.cantidad_recibida
        ");

        $sql->execute([
            ':idcompra' => $idcompra,
            ':sucursal' => $idSucursal
        ]);

        return ((int)$sql->fetchColumn() > 0) ? 3 : 1;
    }

    protected static function marcarCompraConDiferenciaModelo(PDO $pdo, $idcompra, $idSucursal, $usuario)
    {
        $sql = $pdo->prepare("
        UPDATE compra_cabecera
        SET estado = 3,
            updated = NOW(),
            updatedby = :usuario
        WHERE idcompra_cabecera = :idcompra
          AND id_sucursal = :sucursal
          AND estado = 4
          AND EXISTS (
              SELECT 1
              FROM compra_detalle d
              WHERE d.idcompra_cabecera = compra_cabecera.idcompra_cabecera
                AND COALESCE(d.cantidad_facturada, d.cantidad_recibida) > d.cantidad_recibida
          )
        ");

        $sql->execute([
            ':usuario' => $usuario,
            ':idcompra' => $idcompra,
            ':sucursal' => $idSucursal
        ]);

        return $sql;
    }

    protected static function obtenerStockDisponibleModelo($idSucursal, $idArticulo)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT COALESCE(stockDisponible, 0)
        FROM stock
        WHERE id_sucursal = :sucursal
          AND id_articulo = :articulo
        LIMIT 1
        ");

        $sql->execute([
            ':sucursal' => $idSucursal,
            ':articulo' => $idArticulo
        ]);

        return (float)$sql->fetchColumn();
    }
}
