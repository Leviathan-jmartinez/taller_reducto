<?php
require_once "mainModel.php";

class inventarioModelo extends mainModel
{
    private static function normalizarTiposArticulo($tipoArticulo)
    {
        switch ($tipoArticulo) {
            case 'insumo':
                return ['insumo'];
            case 'todos':
            case 'ambos':
                return ['producto', 'insumo'];
            case 'producto':
            default:
                return ['producto'];
        }
    }

    private static function filtroTiposArticulo($tipoArticulo, &$params, $alias = 'a')
    {
        $tipos = self::normalizarTiposArticulo($tipoArticulo);
        $placeholders = [];

        foreach ($tipos as $i => $tipo) {
            $param = ':tipo_articulo_' . $i;
            $placeholders[] = $param;
            $params[$param] = $tipo;
        }

        return $alias . ".tipo IN (" . implode(', ', $placeholders) . ")";
    }

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
    protected static function cargarArticulosModelo($buscar = '', $tipoArticulo = 'producto')
    {
        $params = [
            ':buscar' => '%' . $buscar . '%'
        ];
        $filtroTipo = self::filtroTiposArticulo($tipoArticulo, $params, 'articulos');

        $sql = "SELECT id_articulo, codigo, desc_articulo, precio_venta, tipo
                FROM articulos
                WHERE estado = 1 
                AND $filtroTipo
                AND (desc_articulo LIKE :buscar OR codigo LIKE :buscar)
                ORDER BY desc_articulo ASC
                LIMIT 50"; // límite para no sobrecargar la tabla
        $stmt = mainModel::conectar()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function listarInventariosModelo($inicio, $registros, $filtrosSQL)
    {
        $conexion = mainModel::conectar();

        $selectSQL = "
            SELECT
                ai.idajuste_inventario,
                ai.sucursal_id,
                ai.id_usuario,
                ai.estado AS estadoInv,
                ai.fecha,
                ai.tipo_inv,
                ai.descripcion,
                ai.fecha_ajuste,
                ai.ajustadoPor,
                u.usu_nombre,
                u.usu_apellido,
                ua.usu_nombre AS ajustado_nombre,
                ua.usu_apellido AS ajustado_apellido
        ";

        $baseSQL = "
            FROM ajuste_inventario ai
            INNER JOIN usuarios u ON u.id_usuario = ai.id_usuario
            LEFT JOIN usuarios ua ON ua.id_usuario = ai.ajustadoPor
            WHERE 1=1 $filtrosSQL
        ";

        return mainModel::ejecutarPaginador(
            $conexion,
            $baseSQL,
            $selectSQL,
            "ORDER BY ai.idajuste_inventario DESC",
            $inicio,
            $registros
        );
    }

    protected static function obtenerCabeceraInventarioModelo($idajuste, $idsucursal)
    {
        $conexion = mainModel::conectar();

        $stmtCab = $conexion->prepare("
            SELECT
                ai.idajuste_inventario,
                ai.sucursal_id,
                ai.id_usuario,
                ai.estado,
                ai.fecha,
                ai.tipo_inv,
                ai.descripcion,
                ai.fecha_ajuste,
                ai.ajustadoPor,
                s.suc_descri,
                u.usu_nombre,
                u.usu_apellido,
                ua.usu_nombre AS ajustado_nombre,
                ua.usu_apellido AS ajustado_apellido
            FROM ajuste_inventario ai
            INNER JOIN usuarios u ON u.id_usuario = ai.id_usuario
            INNER JOIN sucursales s ON s.id_sucursal = ai.sucursal_id
            LEFT JOIN usuarios ua ON ua.id_usuario = ai.ajustadoPor
            WHERE ai.idajuste_inventario = :idajuste
              AND ai.sucursal_id = :idsucursal
            LIMIT 1
        ");

        $stmtCab->execute([
            ':idajuste' => $idajuste,
            ':idsucursal' => $idsucursal
        ]);

        return $stmtCab->fetch(PDO::FETCH_ASSOC);
    }

    protected static function listarDetalleInventarioModelo($idajuste, $inicio, $registros, $buscar = '', $filtroDiferencia = 'todos')
    {
        $conexion = mainModel::conectar();

        $where = "WHERE aid.idajuste_inventario = :idajuste";
        $params = [
            ':idajuste' => $idajuste
        ];

        if ($buscar !== '') {
            $where .= " AND (a.codigo LIKE :buscar OR a.desc_articulo LIKE :buscar)";
            $params[':buscar'] = '%' . $buscar . '%';
        }

        $exprDiferencia = "COALESCE(aid.diferencia, aid.cantidad_fisica - aid.cantidad_teorica)";

        switch ($filtroDiferencia) {
            case 'diferencias':
                $where .= " AND $exprDiferencia <> 0";
                break;
            case 'sobrantes':
                $where .= " AND $exprDiferencia > 0";
                break;
            case 'faltantes':
                $where .= " AND $exprDiferencia < 0";
                break;
        }

        $stmtDet = $conexion->prepare("
            SELECT
                aid.id_articulo,
                a.codigo,
                a.desc_articulo,
                a.tipo,
                aid.cantidad_teorica,
                aid.cantidad_fisica,
                $exprDiferencia AS diferencia,
                aid.costo
            FROM ajuste_inventario_detalle aid
            INNER JOIN articulos a ON a.id_articulo = aid.id_articulo
            $where
            ORDER BY a.desc_articulo ASC
            LIMIT $inicio, $registros
        ");

        $stmtDet->execute($params);

        $stmtTotal = $conexion->prepare("
            SELECT COUNT(*)
            FROM ajuste_inventario_detalle aid
            INNER JOIN articulos a ON a.id_articulo = aid.id_articulo
            $where
        ");

        $stmtTotal->execute($params);

        return [
            'datos' => $stmtDet->fetchAll(PDO::FETCH_ASSOC),
            'total' => (int) $stmtTotal->fetchColumn()
        ];
    }

    // Guardar inventario
    protected static function guardarInventario($data)
    {
        $pdo = mainModel::conectar();

        if (empty($data['sucursal_id'])) {
            throw new Exception('Sucursal no definida');
        }

        $tipoInventario = $data['tipo'] ?? '';
        $tipoArticulo = $data['tipo_articulo'] ?? 'producto';
        $tiposValidos = ['General', 'Categoria', 'Proveedor', 'Producto'];

        if (!in_array($tipoInventario, $tiposValidos, true)) {
            throw new Exception('Tipo de inventario inválido');
        }

        if ($tipoInventario === 'Categoria' && (int)($data['subtipo_categoria'] ?? 0) <= 0) {
            throw new Exception('Debe seleccionar una categoría');
        }

        if ($tipoInventario === 'Proveedor' && (int)($data['subtipo_proveedor'] ?? 0) <= 0) {
            throw new Exception('Debe seleccionar un proveedor');
        }

        if ($tipoInventario === 'Producto') {
            $data['subtipo_producto'] = array_values(array_unique(array_filter(
                array_map('intval', (array)($data['subtipo_producto'] ?? [])),
                fn($id) => $id > 0
            )));

            if (empty($data['subtipo_producto'])) {
                throw new Exception('Debe seleccionar al menos un artículo');
            }
        }

        try {
            $pdo->beginTransaction();

            /* ================= OBTENER ARTÍCULOS ================= */
            $paramsTipo = [];
            $filtroTipo = self::filtroTiposArticulo($tipoArticulo, $paramsTipo, 'a');

            switch ($tipoInventario) {

                case 'General':
                    $stmtArt = $pdo->prepare("
                    SELECT 
                        a.id_articulo,
                        COALESCE(s.stockDisponible, 0) AS stockDisponible,
                        COALESCE((
                            SELECT ap.precio_compra
                            FROM articulo_proveedor ap
                            WHERE ap.id_articulo = a.id_articulo
                              AND ap.activo = 1
                            ORDER BY ap.id ASC
                            LIMIT 1
                        ), 0) AS precio_compra
                    FROM articulos a
                    LEFT JOIN stock s 
                        ON s.id_articulo = a.id_articulo
                       AND s.id_sucursal = :sucursal
                    WHERE a.estado = 1 AND $filtroTipo
                    ORDER BY a.desc_articulo ASC ");
                    $stmtArt->execute(array_merge([
                        ':sucursal' => $data['sucursal_id']
                    ], $paramsTipo));
                    $articulos = $stmtArt->fetchAll(PDO::FETCH_ASSOC);
                    break;

                case 'Categoria':
                    $stmtArt = $pdo->prepare("
                    SELECT 
                        a.id_articulo,
                        COALESCE(s.stockDisponible, 0) AS stockDisponible,
                        COALESCE((
                            SELECT ap.precio_compra
                            FROM articulo_proveedor ap
                            WHERE ap.id_articulo = a.id_articulo
                              AND ap.activo = 1
                            ORDER BY ap.id ASC
                            LIMIT 1
                        ), 0) AS precio_compra
                    FROM articulos a
                    LEFT JOIN stock s 
                        ON s.id_articulo = a.id_articulo
                       AND s.id_sucursal = :sucursal
                    WHERE a.id_categoria = :idcat
                      AND a.estado = 1
                      AND $filtroTipo
                    ORDER BY a.desc_articulo ASC");
                    $stmtArt->execute(array_merge([
                        ':idcat'    => (int)$data['subtipo_categoria'],
                        ':sucursal' => $data['sucursal_id']
                    ], $paramsTipo));
                    $articulos = $stmtArt->fetchAll(PDO::FETCH_ASSOC);
                    break;

                case 'Proveedor':
                    $stmtArt = $pdo->prepare("
                    SELECT 
                        a.id_articulo,
                        COALESCE(s.stockDisponible, 0) AS stockDisponible,
                        COALESCE((
                            SELECT app.precio_compra
                            FROM articulo_proveedor app
                            WHERE app.id_articulo = a.id_articulo
                              AND app.idproveedores = :idprov_precio
                              AND app.activo = 1
                            ORDER BY app.id ASC
                            LIMIT 1
                        ), 0) AS precio_compra
                    FROM articulos a
                    LEFT JOIN stock s 
                        ON s.id_articulo = a.id_articulo
                       AND s.id_sucursal = :sucursal
                    WHERE a.estado = 1
                      AND $filtroTipo
                      AND EXISTS (
                          SELECT 1
                          FROM articulo_proveedor apf
                          WHERE apf.id_articulo = a.id_articulo
                            AND apf.idproveedores = :idprov
                            AND apf.activo = 1
                      )
                    ORDER BY a.desc_articulo ASC");
                    $idProveedor = (int)$data['subtipo_proveedor'];
                    $stmtArt->execute(array_merge([
                        ':idprov'   => $idProveedor,
                        ':idprov_precio' => $idProveedor,
                        ':sucursal' => $data['sucursal_id']
                    ], $paramsTipo));
                    $articulos = $stmtArt->fetchAll(PDO::FETCH_ASSOC);
                    break;

                case 'Producto':
                    $paramsProducto = [];
                    $placeholders = [];

                    foreach ($data['subtipo_producto'] as $i => $id_art) {
                        $param = ':id_art_' . $i;
                        $placeholders[] = $param;
                        $paramsProducto[$param] = $id_art;
                    }

                    $stmtStock = $pdo->prepare("
                    SELECT 
                        a.id_articulo,
                        COALESCE(s.stockDisponible, 0) AS stockDisponible,
                        COALESCE((
                            SELECT ap.precio_compra
                            FROM articulo_proveedor ap
                            WHERE ap.id_articulo = a.id_articulo
                              AND ap.activo = 1
                            ORDER BY ap.id ASC
                            LIMIT 1
                        ), 0) AS precio_compra
                    FROM articulos a
                    LEFT JOIN stock s 
                        ON s.id_articulo = a.id_articulo
                       AND s.id_sucursal = :sucursal
                    WHERE a.id_articulo IN (" . implode(', ', $placeholders) . ")
                      AND a.estado = 1
                      AND $filtroTipo
                    ORDER BY a.desc_articulo ASC");

                    $stmtStock->execute(array_merge([
                        ':sucursal' => $data['sucursal_id']
                    ], $paramsTipo, $paramsProducto));
                    $articulos = $stmtStock->fetchAll(PDO::FETCH_ASSOC);
                    break;

                default:
                    throw new Exception('Tipo de inventario inválido');
            }

            if (empty($articulos)) {
                throw new Exception('No se encontraron artículos para el inventario');
            }

            if ($tipoInventario === 'Producto' && count($articulos) !== count($data['subtipo_producto'])) {
                throw new Exception('Uno o más artículos seleccionados no existen, no están activos o no corresponden al tipo elegido');
            }

            /* ================= CABECERA ================= */
            $stmtCab = $pdo->prepare("
            INSERT INTO ajuste_inventario
            (id_usuario, sucursal_id, estado, fecha, tipo_inv, descripcion)
            VALUES (:usuario, :sucursal, 1, NOW(), :tipo, :desc)");

            $stmtCab->execute([
                ':usuario'  => $data['usuario_id'],
                ':sucursal' => $data['sucursal_id'],
                ':tipo'     => $tipoInventario,
                ':desc'     => $data['observacion']
            ]);

            $idajuste = $pdo->lastInsertId();

            if (!$idajuste) {
                throw new Exception('No se pudo generar el ajuste de inventario');
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
