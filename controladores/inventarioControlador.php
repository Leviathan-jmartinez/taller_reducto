<?php
if ($peticionAjax) {
    require_once "../modelos/inventarioModelo.php";
} else {
    require_once "./modelos/inventarioModelo.php";
}

class inventarioControlador extends inventarioModelo
{
    /* ===============================
       CATEGORÍAS
    =============================== */
    public function cargar_categorias_controlador()
    {
        $categorias = inventarioModelo::cargarCategoriasModelo();

        $data = [];
        foreach ($categorias as $row) {
            $data[] = [
                'id' => $row['id_categoria'],
                'nombre' => $row['cat_descri']
            ];
        }

        return json_encode($data);
    }

    /* ===============================
       PROVEEDORES
    =============================== */
    public function cargar_proveedores_controlador()
    {
        $proveedores = inventarioModelo::cargarProveedoresModelo();

        $data = [];
        foreach ($proveedores as $row) {
            $data[] = [
                'id' => $row['idproveedores'],
                'nombre' => $row['razon_social']
            ];
        }

        return json_encode($data);
    }

    /* ===============================
       artículos
    =============================== */
    public function cargarArticulosControlador($buscar = '')
    {
        $articulos = inventarioModelo::cargarArticulosModelo($buscar);

        $data = [];
        foreach ($articulos as $row) {
            $data[] = [
                "id" => $row['id_articulo'],
                "text" => $row['codigo'] . ' - ' . $row['desc_articulo'] . ' (' . number_format($row['precio_venta'], 2) . ')'
            ];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit();
    }

    public function guardarInventarioControlador()
    {
        if (!isset($_POST['tipo_inventario'])) {
            return ['status' => 'error', 'msg' => 'Tipo de inventario no definido'];
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $tipo        = $_POST['tipo_inventario'];
        $usuario_id  = $_SESSION['id_str'] ?? 0;
        $sucursal_id = $_SESSION['nick_sucursal'] ?? 0;
        $fecha       = date('Y-m-d H:i:s');
        $observacion = $_POST['observacion'] ?? '';

        if ($usuario_id <= 0 || $sucursal_id <= 0) {
            return ['status' => 'error', 'msg' => 'Usuario o sucursal no válidos'];
        }

        $pdo = mainModel::conectar();

        try {

            /* ================= TRANSACCIÓN ================= */
            $pdo->beginTransaction();

            /* ================= CABECERA ================= */
            $stmtCab = $pdo->prepare("
            INSERT INTO ajuste_inventario
            (id_usuario, estado, fecha, tipo_inv, descripcion, fecha_ajuste)
            VALUES (:usuario,1,:fecha,:tipo,:desc,:fecha_ajuste)
        ");
            $stmtCab->execute([
                ':usuario'       => $usuario_id,
                ':fecha'         => $fecha,
                ':tipo'          => $tipo,
                ':desc'          => $observacion,
                ':fecha_ajuste'  => $fecha
            ]);

            $idajuste = $pdo->lastInsertId();

            if ($idajuste <= 0) {
                throw new Exception('No se pudo generar el ajuste de inventario');
            }

            /* ================= ARTÍCULOS ================= */
            switch ($tipo) {

                case 'general':
                    $stmtArt = $pdo->prepare("
                    SELECT id_articulo FROM articulos WHERE estado=1
                ");
                    $stmtArt->execute();
                    $articulos = $stmtArt->fetchAll(PDO::FETCH_COLUMN);
                    break;

                case 'categoria':
                    $stmtArt = $pdo->prepare("
                    SELECT id_articulo 
                    FROM articulos 
                    WHERE id_categoria = :idcat AND estado=1
                ");
                    $stmtArt->execute([
                        ':idcat' => $_POST['subtipo_categoria'] ?? 0
                    ]);
                    $articulos = $stmtArt->fetchAll(PDO::FETCH_COLUMN);
                    break;

                case 'proveedor':
                    $stmtArt = $pdo->prepare("
                    SELECT id_articulo 
                    FROM articulos 
                    WHERE idproveedores = :idprov AND estado=1
                ");
                    $stmtArt->execute([
                        ':idprov' => $_POST['subtipo_proveedor'] ?? 0
                    ]);
                    $articulos = $stmtArt->fetchAll(PDO::FETCH_COLUMN);
                    break;

                case 'producto':
                    $articulos = $_POST['subtipo_producto'] ?? [];
                    break;

                default:
                    $articulos = [];
            }

            if (empty($articulos)) {
                throw new Exception('No se encontraron artículos para el inventario');
            }

            /* ================= PREPARAR QUERIES ================= */
            $stmtStock = $pdo->prepare("
            SELECT s.stockDisponible, a.precio_compra
            FROM stock s
            JOIN articulos a ON a.id_articulo = s.id_articulo
            WHERE s.id_articulo = :id_art
            AND s.id_sucursal = :id_sucursal
        ");

            $stmtDet = $pdo->prepare("
            INSERT INTO ajuste_inventario_detalle
            (idajuste_inventario,id_articulo,cantidad_teorica,cantidad_fisica,costo,estado)
            VALUES (:idajuste,:id_art,:teo,:fis,:costo,1)
        ");

            /* ================= DETALLE ================= */
            foreach ($articulos as $id_art) {

                $stmtStock->execute([
                    ':id_art'       => $id_art,
                    ':id_sucursal'  => $sucursal_id
                ]);

                $stock = $stmtStock->fetch(PDO::FETCH_ASSOC);

                if (!$stock) {
                    continue;
                }

                $stmtDet->execute([
                    ':idajuste' => $idajuste,
                    ':id_art'   => $id_art,
                    ':teo'      => $stock['stockDisponible'],
                    ':fis'      => $stock['stockDisponible'],
                    ':costo'    => $stock['precio_compra']
                ]);
            }

            /* ================= COMMIT ================= */
            $pdo->commit();

            return [
                "Alerta" => "recargar",
                "Titulo" => "Inventario generado",
                "Texto"  => "El inventario se guardó correctamente.",
                "Tipo"   => "success"
            ];
        } catch (Exception $e) {

            $pdo->rollBack();

            return [
                'status' => 'error',
                'msg'    => 'Error al generar inventario: ' . $e->getMessage()
            ];
        }
    }
}
