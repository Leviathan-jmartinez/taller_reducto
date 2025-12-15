<?php
require_once "mainModel.php";

class inventarioModelo extends mainModel
{
    // Cargar categorías
    protected static function cargarCategoriasModelo()
    {
        $stmt = mainModel::conectar()->prepare(
            "SELECT id_categoria, cat_descri
             FROM categorias
             WHERE estado = 1
             ORDER BY cat_descri ASC"
        );

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Cargar proveedores
    protected static function cargarProveedoresModelo()
    {
        $stmt = mainModel::conectar()->prepare(
            "SELECT idproveedores, razon_social
             FROM proveedores
             WHERE estado = 1
             ORDER BY razon_social ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Cargar artículos para Select2
    protected static function cargarArticulosModelo($buscar = '')
    {
        $sql = "SELECT id_articulo, codigo, desc_articulo, precio_venta
                FROM articulos
                WHERE estado = 1 
                AND (desc_articulo LIKE :buscar OR codigo LIKE :buscar)
                ORDER BY desc_articulo ASC
                LIMIT 50"; // límite para no sobrecargar la tabla
        $stmt = mainModel::conectar()->prepare($sql);
        $stmt->bindValue(':buscar', '%' . $buscar . '%');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Guardar inventario
    protected static function guardarInventario($data)
    {
        $pdo = mainModel::conectar();

        try {
            /* ================= TRANSACCIÓN ================= */
            $pdo->beginTransaction();

            /* ================= CABECERA ================= */
            $stmtCab = $pdo->prepare("
            INSERT INTO ajuste_inventario
            (id_usuario, sucursal_id, estado, fecha, tipo_inv, descripcion)
            VALUES (:usuario,:sucursal,1,:fecha,:tipo,:desc)
        ");

            $stmtCab->execute([
                ':usuario' => $data['usuario_id'],
                ':sucursal' => $data['sucursal_id'],
                ':fecha'   => $data['fecha'],
                ':tipo'    => $data['tipo'],
                ':desc'    => $data['observacion']
            ]);

            $idajuste = $pdo->lastInsertId();

            if ($idajuste <= 0) {
                throw new Exception('No se pudo generar el ajuste de inventario');
            }

            /* ================= OBTENER ARTÍCULOS ================= */
            switch ($data['tipo']) {

                case 'General':
                    // Todos los artículos activos, con stock disponible si existe, 0 si no
                    $stmtArt = $pdo->query("
                    SELECT a.id_articulo, COALESCE(s.stockDisponible, 0) AS stockDisponible, a.precio_compra
                    FROM articulos a
                    LEFT JOIN stock s ON s.id_articulo = a.id_articulo AND s.id_sucursal = " . intval($data['sucursal_id']) . "
                    WHERE a.estado = 1
                ");
                    $articulos = $stmtArt->fetchAll(PDO::FETCH_ASSOC);
                    break;

                case 'Categoria':
                    $stmtArt = $pdo->prepare("
                    SELECT a.id_articulo, COALESCE(s.stockDisponible,0) AS stockDisponible, a.precio_compra
                    FROM articulos a
                    LEFT JOIN stock s ON s.id_articulo = a.id_articulo AND s.id_sucursal = :sucursal
                    WHERE a.id_categoria = :idcat AND a.estado=1
                ");
                    $stmtArt->execute([
                        ':idcat' => $data['subtipo_categoria'],
                        ':sucursal' => $data['sucursal_id']
                    ]);
                    $articulos = $stmtArt->fetchAll(PDO::FETCH_ASSOC);
                    break;

                case 'Proveedor':
                    $stmtArt = $pdo->prepare("
                    SELECT a.id_articulo, COALESCE(s.stockDisponible,0) AS stockDisponible, a.precio_compra
                    FROM articulos a
                    LEFT JOIN stock s ON s.id_articulo = a.id_articulo AND s.id_sucursal = :sucursal
                    WHERE a.idproveedores = :idprov AND a.estado=1
                ");
                    $stmtArt->execute([
                        ':idprov' => $data['subtipo_proveedor'],
                        ':sucursal' => $data['sucursal_id']
                    ]);
                    $articulos = $stmtArt->fetchAll(PDO::FETCH_ASSOC);
                    break;

                case 'Producto':
                    $articulos = [];
                    foreach ($data['subtipo_producto'] as $id_art) {
                        $stmtStock = $pdo->prepare("
                        SELECT a.id_articulo, COALESCE(s.stockDisponible,0) AS stockDisponible, a.precio_compra
                        FROM articulos a
                        LEFT JOIN stock s ON s.id_articulo = a.id_articulo AND s.id_sucursal = :sucursal
                        WHERE a.id_articulo = :id_art
                    ");
                        $stmtStock->execute([
                            ':id_art' => $id_art,
                            ':sucursal' => $data['sucursal_id']
                        ]);
                        $articulos[] = $stmtStock->fetch(PDO::FETCH_ASSOC);
                    }
                    break;

                default:
                    $articulos = [];
            }

            if (empty($articulos)) {
                throw new Exception('No se encontraron artículos para el inventario');
            }

            /* ================= DETALLE ================= */
            $stmtDet = $pdo->prepare("
            INSERT INTO ajuste_inventario_detalle
            (idajuste_inventario,id_articulo,cantidad_teorica,cantidad_fisica,costo)
            VALUES (:idajuste,:id_art,:teo,:fis,:costo)
        ");

            foreach ($articulos as $art) {
                if (!$art) continue;

                $stmtDet->execute([
                    ':idajuste' => $idajuste,
                    ':id_art'   => $art['id_articulo'],
                    ':teo'      => floatval($art['stockDisponible']),
                    ':fis'      => floatval($art['stockDisponible']),
                    ':costo'    => floatval($art['precio_compra'])
                ]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
    // Anular inventario
    protected static function anular_inv_modelo($idajuste)
    {
        $stmt = mainModel::conectar()->prepare(
            "UPDATE ajuste_inventario
             SET estado = 0
             WHERE idajuste_inventario = :idajuste"
        );
        $stmt->bindParam(":idajuste", $idajuste);
        $stmt->execute();
        return $stmt->rowCount();
    }
    // reversar ajuste de stock
    protected static function revertir_ajuste_modelo($idajuste, $idsucursal, $usuario)
    {
        $pdo = mainModel::conectar();
        $pdo->beginTransaction();

        try {
            $movs = $pdo->prepare("
            SELECT * 
            FROM sucmovimientostock
            WHERE MovStockReferencia = :ref
        ");
            $movs->execute([
                ':ref' => "AJUSTE #$idajuste"
            ]);

            foreach ($movs as $mov) {

                $cantidad = floatval($mov['MovStockCantidad']);
                $signo    = intval($mov['MovStockSigno']) * -1; // 🔁 INVERSO
                $ajuste   = $cantidad * $signo;

                // Revertir stock
                $pdo->prepare("
                UPDATE stock
                SET stockDisponible = stockDisponible + :ajuste
                WHERE id_articulo = :art
                AND id_sucursal = :suc
            ")->execute([
                    ':ajuste' => $ajuste,
                    ':art'    => $mov['MovStockProductoId'],
                    ':suc'    => $idsucursal
                ]);

                // Registrar movimiento de anulación
                $pdo->prepare("
                INSERT INTO sucmovimientostock (
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
                ) VALUES (
                    :suc,
                    'ANULACION_AJUSTE',
                    :art,
                    :cant,
                    0,
                    :costo,
                    NOW(),
                    :usr,
                    :signo,
                    :ref
                )
            ")->execute([
                    ':suc'   => $idsucursal,
                    ':art'   => $mov['MovStockProductoId'],
                    ':cant'  => $cantidad,
                    ':costo' => $mov['MovStockCosto'],
                    ':usr'   => $usuario,
                    ':signo' => $signo,
                    ':ref'   => "ANULACIÓN AJUSTE #$idajuste"
                ]);
            }

            // Marcar inventario anulado
            $pdo->prepare("
            UPDATE ajuste_inventario
            SET estado = 0, fecha_anulacion = NOW()
            WHERE idajuste_inventario = :id
        ")->execute([':id' => $idajuste]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}
