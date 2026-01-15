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

        if (empty($data['sucursal_id'])) {
            throw new Exception('Sucursal no definida');
        }

        try {
            $pdo->beginTransaction();

            /* ================= CABECERA ================= */
            $stmtCab = $pdo->prepare("
            INSERT INTO ajuste_inventario
            (id_usuario, sucursal_id, estado, fecha, tipo_inv, descripcion)
            VALUES (:usuario, :sucursal, 1, NOW(), :tipo, :desc)");

            $stmtCab->execute([
                ':usuario'  => $data['usuario_id'],
                ':sucursal' => $data['sucursal_id'],
                ':tipo'     => $data['tipo'],
                ':desc'     => $data['observacion']
            ]);

            $idajuste = $pdo->lastInsertId();

            if (!$idajuste) {
                throw new Exception('No se pudo generar el ajuste de inventario');
            }

            /* ================= OBTENER ARTÍCULOS ================= */
            switch ($data['tipo']) {

                case 'General':
                    $stmtArt = $pdo->prepare("
                    SELECT 
                        a.id_articulo,
                        COALESCE(s.stockDisponible, 0) AS stockDisponible,
                        a.precio_compra
                    FROM articulos a
                    LEFT JOIN stock s 
                        ON s.id_articulo = a.id_articulo
                       AND s.id_sucursal = :sucursal
                    WHERE a.estado = 1 and a.tipo='producto' ");
                    $stmtArt->execute([
                        ':sucursal' => $data['sucursal_id']
                    ]);
                    $articulos = $stmtArt->fetchAll(PDO::FETCH_ASSOC);
                    break;

                case 'Categoria':
                    $stmtArt = $pdo->prepare("
                    SELECT 
                        a.id_articulo,
                        COALESCE(s.stockDisponible, 0) AS stockDisponible,
                        a.precio_compra
                    FROM articulos a
                    LEFT JOIN stock s 
                        ON s.id_articulo = a.id_articulo
                       AND s.id_sucursal = :sucursal
                    WHERE a.id_categoria = :idcat
                      AND a.estado = 1");
                    $stmtArt->execute([
                        ':idcat'    => $data['subtipo_categoria'],
                        ':sucursal' => $data['sucursal_id']
                    ]);
                    $articulos = $stmtArt->fetchAll(PDO::FETCH_ASSOC);
                    break;

                case 'Proveedor':
                    $stmtArt = $pdo->prepare("
                    SELECT 
                        a.id_articulo,
                        COALESCE(s.stockDisponible, 0) AS stockDisponible,
                        a.precio_compra
                    FROM articulos a
                    LEFT JOIN stock s 
                        ON s.id_articulo = a.id_articulo
                       AND s.id_sucursal = :sucursal
                    WHERE a.idproveedores = :idprov
                      AND a.estado = 1");
                    $stmtArt->execute([
                        ':idprov'   => $data['subtipo_proveedor'],
                        ':sucursal' => $data['sucursal_id']
                    ]);
                    $articulos = $stmtArt->fetchAll(PDO::FETCH_ASSOC);
                    break;

                case 'Producto':
                    $articulos = [];

                    if (empty($data['subtipo_producto'])) {
                        throw new Exception('No se seleccionaron productos');
                    }

                    $stmtStock = $pdo->prepare("
                    SELECT 
                        a.id_articulo,
                        COALESCE(s.stockDisponible, 0) AS stockDisponible,
                        a.precio_compra
                    FROM articulos a
                    LEFT JOIN stock s 
                        ON s.id_articulo = a.id_articulo
                       AND s.id_sucursal = :sucursal
                    WHERE a.id_articulo = :id_art
                      AND a.estado = 1");

                    foreach ($data['subtipo_producto'] as $id_art) {
                        $stmtStock->execute([
                            ':id_art'   => $id_art,
                            ':sucursal' => $data['sucursal_id']
                        ]);
                        $row = $stmtStock->fetch(PDO::FETCH_ASSOC);
                        if ($row) {
                            $articulos[] = $row;
                        }
                    }
                    break;

                default:
                    throw new Exception('Tipo de inventario inválido');
            }

            if (empty($articulos)) {
                throw new Exception('No se encontraron artículos para el inventario');
            }

            /* ================= DETALLE ================= */
            $stmtDet = $pdo->prepare("
            INSERT INTO ajuste_inventario_detalle
            (idajuste_inventario, id_articulo, cantidad_teorica, cantidad_fisica, costo)
            VALUES (:idajuste, :id_art, :teo, :fis, :costo) ");

            foreach ($articulos as $art) {
                $stmtDet->execute([
                    ':idajuste' => $idajuste,
                    ':id_art'   => $art['id_articulo'],
                    ':teo'      => (float) $art['stockDisponible'],
                    ':fis'      => (float) $art['stockDisponible'],
                    ':costo'    => (float) $art['precio_compra']
                ]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
