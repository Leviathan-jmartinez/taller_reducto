<?php
require_once "mainModel.php";

class salidaInsumoModelo extends mainModel
{
    protected static function buscar_consumible_modelo($texto)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $sql = self::conectar()->prepare("
            SELECT 
                a.id_articulo,
                a.desc_articulo,
                s.stockDisponible
            FROM articulos a
            INNER JOIN stock s 
                ON s.id_articulo = a.id_articulo
            WHERE a.tipo = 'insumo'
              AND a.estado = 1
              AND s.id_sucursal = :sucursal
              AND a.desc_articulo LIKE :b
            ORDER BY a.desc_articulo ASC
            LIMIT 20
        ");

        $sql->bindValue(':b', "%$texto%");
        $sql->bindValue(':sucursal', $_SESSION['nick_sucursal'], PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function registrar_salida_insumo_modelo($datos)
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            $items = json_decode($datos['consumibles_json'] ?? '[]', true);

            if (empty($items) || !is_array($items)) {
                $pdo->rollBack();
                return ['msg' => 'Debe agregar al menos un insumo'];
            }

            $cab = $pdo->prepare("
                INSERT INTO salida_insumo
                (id_sucursal, id_usuario, id_tecnico, fecha, observacion, estado)
                VALUES (?, ?, ?, now(), ?, 1)
            ");

            $cab->execute([
                $datos['id_sucursal'],
                $datos['usuario'],
                $datos['idempleado'],
                $datos['observacion']
            ]);

            $idSalida = $pdo->lastInsertId();

            foreach ($items as $item) {
                $idArticulo = (int) $item['id_articulo'];
                $cantidad   = (float) $item['cantidad'];

                if ($idArticulo <= 0 || $cantidad <= 0) {
                    throw new Exception('Cantidad inválida en uno de los insumos');
                }

                $q = $pdo->prepare("
                SELECT 
                    a.id_articulo,
                    a.desc_articulo,
                    s.stockDisponible,
                    a.precio_venta,
                    COALESCE(ap.precio_compra, 0) AS costo
                FROM articulos a
                INNER JOIN stock s 
                    ON s.id_articulo = a.id_articulo
                LEFT JOIN articulo_proveedor ap
                    ON ap.id_articulo = a.id_articulo
                AND ap.id = (
                        SELECT MAX(ap2.id)
                        FROM articulo_proveedor ap2
                        WHERE ap2.id_articulo = a.id_articulo
                        AND ap2.activo = 1
                )
                WHERE a.id_articulo = ?
                AND a.tipo = 'insumo'
                AND a.estado = 1
                AND s.id_sucursal = ?
                FOR UPDATE
            ");
                $q->execute([$idArticulo, $datos['id_sucursal']]);
                $art = $q->fetch(PDO::FETCH_ASSOC);

                if (!$art) {
                    throw new Exception('El artículo ID ' . $idArticulo . ' no es un insumo válido');
                }

                if ((float)$art['stockDisponible'] < $cantidad) {
                    throw new Exception('Stock insuficiente para: ' . $art['desc_articulo']);
                }

                $det = $pdo->prepare("
                    INSERT INTO salida_insumo_detalle
                    (idsalida_insumo, id_articulo, cantidad)
                    VALUES (?, ?, ?)
                ");
                $det->execute([
                    $idSalida,
                    $idArticulo,
                    $cantidad
                ]);

                mainModel::registrar_movimiento_stock_modelo($pdo, [
                    "id_sucursal" => $datos['id_sucursal'],
                    "tipo" => "SALIDA INSUMO",
                    "id_articulo" => $idArticulo,
                    "cantidad" => $cantidad,
                    "precio_venta" => $art['precio_venta'],
                    "costo" => $art['costo'],
                    "usuario" => $datos['usuario'],
                    "signo" => -1,
                    "referencia" => 'SAL_INS #' . $idSalida
                ]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['msg' => $e->getMessage()];
        }
    }

    protected static function anular_salida_insumo_modelo($datos)
    {
        $pdo = self::conectar();

        try {
            $pdo->beginTransaction();

            /* ================= VALIDAR CABECERA ================= */
            $q = $pdo->prepare("
            SELECT idsalida_insumo, id_sucursal, estado
            FROM salida_insumo
            WHERE idsalida_insumo = ?
            FOR UPDATE
        ");
            $q->execute([$datos['idsalida_insumo']]);
            $salida = $q->fetch(PDO::FETCH_ASSOC);

            if (!$salida) {
                throw new Exception('La salida de insumos no existe');
            }

            if ((int)$salida['estado'] !== 1) {
                throw new Exception('La salida no puede ser anulada');
            }

            if ((int)$salida['id_sucursal'] !== (int)$datos['id_sucursal']) {
                throw new Exception('No puede anular salidas de otra sucursal');
            }

            /* ================= OBTENER DETALLE ================= */
            $det = $pdo->prepare("
                SELECT 
                    d.id_articulo,
                    d.cantidad,
                    a.desc_articulo,
                    s.stockDisponible,
                    a.precio_venta,
                    COALESCE(ap.precio_compra, 0) AS costo
                FROM salida_insumo_detalle d
                INNER JOIN articulos a
                    ON a.id_articulo = d.id_articulo
                INNER JOIN stock s 
                    ON s.id_articulo = a.id_articulo
                AND s.id_sucursal = ?
                LEFT JOIN articulo_proveedor ap
                    ON ap.id_articulo = a.id_articulo
                AND ap.id = (
                        SELECT MAX(ap2.id)
                        FROM articulo_proveedor ap2
                        WHERE ap2.id_articulo = a.id_articulo
                        AND ap2.activo = 1
                )
                WHERE d.idsalida_insumo = ?
                AND a.tipo = 'insumo'
                AND a.estado = 1
                FOR UPDATE
            ");

            $det->execute([
                $datos['id_sucursal'],
                $datos['idsalida_insumo']
            ]);

            $items = $det->fetchAll(PDO::FETCH_ASSOC);

            if (!$items) {
                throw new Exception('La salida no tiene detalle válido');
            }

            foreach ($items as $item) {
                $idArticulo = (int)$item['id_articulo'];
                $cantidad   = (float)$item['cantidad'];

                if ($idArticulo <= 0 || $cantidad <= 0) {
                    throw new Exception('Cantidad inválida en uno de los insumos');
                }

                /* ================= VALIDAR STOCK ================= */
                $qStock = $pdo->prepare("
                SELECT stockDisponible
                FROM stock
                WHERE id_articulo = ?
                  AND id_sucursal = ?
                FOR UPDATE
            ");
                $qStock->execute([
                    $idArticulo,
                    $datos['id_sucursal']
                ]);

                $stock = $qStock->fetch(PDO::FETCH_ASSOC);

                if (!$stock) {
                    throw new Exception('No existe stock para el artículo ID ' . $idArticulo);
                }

                mainModel::registrar_movimiento_stock_modelo($pdo, [
                    "id_sucursal" => $datos['id_sucursal'],
                    "tipo" => "ANUL SALIDA INSUMO",
                    "id_articulo" => $idArticulo,
                    "cantidad" => $cantidad,
                    "precio_venta" => $item['precio_venta'],
                    "costo" => $item['costo'],
                    "usuario" => $datos['usuario'],
                    "signo" => 1,
                    "referencia" => 'ANUL_SAL_INS #' . $datos['idsalida_insumo']
                ]);
            }

            /* ================= ANULAR CABECERA ================= */
            $updCab = $pdo->prepare("
            UPDATE salida_insumo
            SET estado = 0
            WHERE idsalida_insumo = ?
              AND estado = 1
        ");

            $updCab->execute([$datos['idsalida_insumo']]);

            if ($updCab->rowCount() === 0) {
                throw new Exception('No se pudo anular la salida');
            }

            mainModel::registrar_anulacion_auditoria_modelo($pdo, [
                'modulo' => 'salida_insumo',
                'tabla_afectada' => 'salida_insumo',
                'id_registro' => $datos['idsalida_insumo'],
                'id_sucursal' => $datos['id_sucursal'],
                'estado_anterior' => (string)($salida['estado'] ?? '1'),
                'estado_nuevo' => '0',
                'motivo' => $datos['motivo'] ?? '',
                'usuario_anula' => $datos['usuario'],
                'referencia' => 'SALIDA_INSUMO #' . $datos['idsalida_insumo']
            ]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['msg' => $e->getMessage()];
        }
    }

    protected static function buscar_empleado_modelo($texto)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $sql = self::conectar()->prepare("
        SELECT 
            idempleados,
            nombre,
            apellido,
            nro_cedula
        FROM empleados
        WHERE estado = 1
          AND id_sucursal = :sucursal
          AND (
                nombre LIKE :b
                OR apellido LIKE :b
                OR nro_cedula LIKE :b
          )
        ORDER BY nombre ASC
        LIMIT 20
        ");

        $sql->bindValue(':b', "%$texto%");
        $sql->bindValue(':sucursal', $_SESSION['nick_sucursal'], PDO::PARAM_INT);

        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function listar_salida_insumo_modelo($inicio, $registros, $filtrosSQL, $orderSQL = "ORDER BY sc.fecha DESC, sc.idsalida_insumo DESC")
    {
        $conexion = mainModel::conectar();

        $baseSQL = "
        FROM salida_insumo sc
        INNER JOIN empleados e 
            ON e.idempleados = sc.id_tecnico
        INNER JOIN usuarios u 
            ON u.id_usuario = sc.id_usuario
        WHERE sc.id_sucursal = '{$_SESSION['nick_sucursal']}'
        $filtrosSQL
        ";

        $selectSQL = "
        SELECT 
            sc.idsalida_insumo,
            sc.fecha,
            sc.estado,
            sc.observacion,
            CONCAT(e.nombre, ' ', e.apellido) AS empleado,
            e.nro_cedula,
            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_registra
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
