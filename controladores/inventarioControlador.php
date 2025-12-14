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
    /* ===============================
       Guardar inventario
    =============================== */
    public function guardarInventarioControlador()
    {
        if (!isset($_POST['tipo_inventario'])) {
            return ['status' => 'error', 'msg' => 'Tipo de inventario no definido'];
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $usuario_id  = $_SESSION['id_str'] ?? 0;
        $sucursal_id = $_SESSION['nick_sucursal'] ?? 0;

        if ($usuario_id <= 0 || $sucursal_id <= 0) {
            return ['status' => 'error', 'msg' => 'Usuario o sucursal no válidos'];
        }

        $data = [
            'tipo'               => $_POST['tipo_inventario'],
            'observacion'        => $_POST['observacion'] ?? '',
            'usuario_id'         => $usuario_id,
            'sucursal_id'        => $sucursal_id,
            'fecha'              => date('Y-m-d H:i:s'),
            'subtipo_categoria'  => $_POST['subtipo_categoria'] ?? 0,
            'subtipo_proveedor'  => $_POST['subtipo_proveedor'] ?? 0,
            'subtipo_producto'   => $_POST['subtipo_producto'] ?? []
        ];

        try {
            inventarioModelo::guardarInventario($data);

            return [
                "Alerta" => "recargar",
                "Titulo" => "Inventario generado",
                "Texto"  => "El inventario se guardó correctamente.",
                "Tipo"   => "success"
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'msg' => 'Error al generar inventario: ' . $e->getMessage()
            ];
        }
    }
    /* ===============================
        Buscar inventario  
    =============================== */
    public function buscar_inv_controlador()
    {
        $inventario  = mainModel::limpiar_string($_POST['buscar_inv']);

        if ($inventario == "") {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún pedido en el sistema que coincida.”</strong>
                                </p>
                            </div>';
        }
        /**seleccionar proveedor */
        $datosINV = mainModel::ejecutar_consulta_simple("SELECT  idajuste_inventario, id_usuario, estado, fecha, tipo_inv, descripcion, fecha_ajuste
        FROM ajuste_inventario
        where (idajuste_inventario like '%$inventario%' or descripcion like '%$inventario%' or tipo_inv like '%$inventario%') and estado = '1'
        order by idajuste_inventario desc
        LIMIT 15");

        if ($datosINV->rowCount() >= 1) {
            $datosINV = $datosINV->fetchAll();
            $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>
                        <tr class="text-center">
                            <th>Número de Código</th>
                            <th>Tipo de Inventario</th>
                            <th>Observación</th>
                            <th></th>
                        </tr>';
            foreach ($datosINV as $rows) {
                $tabla .= '
                        <tr class="text-center">
                            <td>' . $rows['idajuste_inventario'] . '</td>
                            <td>' . $rows['tipo_inv'] . '</td>
                            <td>' . $rows['descripcion'] . '</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="agregar_inv(' . $rows['idajuste_inventario'] . ')"><i class="fas fa-user-plus"></i></button>
                            </td>
                        </tr>';
            }
            $tabla .= '</tbody></table></div>';
            return $tabla;
        } else {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún pedido en el sistema que coincida con <strong>“' . $inventario . '”</strong>
                                </p>
                            </div>';
        }
    }
    /* ===============================
         Cargar INV en sesión   
    =============================== */
    public function cargar_inv_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $idajuste = mainModel::limpiar_string($_POST['id_inv_seleccionado'] ?? '');

        if (empty($idajuste)) {
            $_SESSION['alerta_inv'] = [
                "tipo" => "error",
                "mensaje" => "No se recibió ID del ajuste de inventario"
            ];
            header("Location: " . SERVERURL . "inventario/");
            exit();
        }

        $_SESSION['id_inv_seleccionado'] = $idajuste;

        /* ==================================================
       1️⃣ CABECERA DEL AJUSTE
        ================================================== */
        $sqlCabecera = mainModel::ejecutar_consulta_simple("
        SELECT ai.idajuste_inventario, ai.tipo_inv, ai.descripcion, ai.fecha_ajuste, u.usu_nombre
        FROM ajuste_inventario ai
        INNER JOIN usuarios u ON u.id_usuario = ai.id_usuario
        WHERE ai.idajuste_inventario = '$idajuste'");

        $cabecera = $sqlCabecera->fetch();

        if ($cabecera) {
            $_SESSION['datos_ajuste_inv'] = [
                "ID"          => $cabecera['idajuste_inventario'],
                "TIPO"        => $cabecera['tipo_inv'],
                "DESCRIPCION" => $cabecera['descripcion'],
                "FECHA"       => $cabecera['fecha_ajuste'],
                "USUARIO"     => $cabecera['usuario']
            ];
        }

        /* ==================================================
       2️⃣ DETALLE DEL AJUSTE
        ================================================== */
        $sqlDetalle = mainModel::ejecutar_consulta_simple("
        SELECT 
            aid.id_articulo,
            a.codigo,
            a.desc_articulo,
            aid.cantidad_teorica,
            aid.cantidad_fisica,
            aid.costo
        FROM ajuste_inventario_detalle aid
        INNER JOIN articulos a ON a.id_articulo = aid.id_articulo
        WHERE aid.idajuste_inventario = '$idajuste'");

        $detalle = $sqlDetalle->fetchAll();

        $_SESSION['Cdatos_articuloINV'] = [];

        foreach ($detalle as $i => $row) {

            // Si el usuario ya modificó cantidades, no pisarlas
            $cant_teorica = $_SESSION['Cdatos_articuloINV'][$i]['cantidad_teorica'] ?? $row['cantidad_teorica'];
            $cant_fisica  = $_SESSION['Cdatos_articuloINV'][$i]['cantidad_fisica']  ?? $row['cantidad_fisica'];
            $costo        = $_SESSION['Cdatos_articuloINV'][$i]['costo']             ?? $row['costo'];

            $_SESSION['Cdatos_articuloINV'][$i] = [
                "ID"               => $row['id_articulo'],
                "codigo"           => $row['codigo'],
                "descripcion"      => $row['desc_articulo'],
                "cantidad_teorica" => $cant_teorica,
                "cantidad_fisica"  => $cant_fisica,
                "costo"            => $costo,
                "diferencia"       => ($cant_fisica - $cant_teorica)
            ];
        }

        /* ==================================================
       3️⃣ REDIRECCIÓN
        ================================================== */
        header("Location: " . SERVERURL . "inventario/");
        exit();
    }

    public function guardar_ajuste_inv_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!isset($_SESSION['id_inv_seleccionado'])) {
            return ["status" => "error", "msg" => "No hay ajuste seleccionado"];
        }

        $idajuste = $_SESSION['id_inv_seleccionado'];

        if (empty($_SESSION['Cdatos_articuloINV'])) {
            return ["status" => "error", "msg" => "No hay artículos para guardar"];
        }

        foreach ($_SESSION['Cdatos_articuloINV'] as $item) {

            $idarticulo      = intval($item['ID']);
            $cantidad_fisica = floatval($item['cantidad_fisica']);
            $diferencia      = floatval($item['diferencia']);
            $costo           = floatval($item['costo']);

            mainModel::ejecutar_consulta_simple("
            UPDATE ajuste_inventario_detalle
            SET cantidad_fisica = '$cantidad_fisica',
                diferencia = '$diferencia',
                costo = '$costo'
            WHERE idajuste_inventario = '$idajuste'
              AND id_articulo = '$idarticulo'
        ");
        }

        return ["status" => "ok"];
    }
    /* ===============================
        Aplicar ajuste de stock
    =============================== */
    public function aplicar_ajuste_stock_controlador($idsucursal)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        // ✅ Validar si hay ajuste cargado
        if (!isset($_SESSION['id_inv_seleccionado']) || empty($_SESSION['Cdatos_articuloINV'])) {
            return ["status" => "error", "msg" => "No hay ajuste seleccionado para aplicar"];
        }

        $idajuste = $_SESSION['id_inv_seleccionado'];
        $usuario  = $_SESSION['id_str'];
        $fecha    = date("Y-m-d H:i:s");

        foreach ($_SESSION['Cdatos_articuloINV'] as $item) {
            $id_articulo = $item['ID'];
            $costo       = floatval($item['costo']);
            $cantidad    = floatval($item['diferencia']); // puede ser + o -
            $cantidad_fisica = floatval($item['cantidad_fisica']);

            // Actualizar stock
            $stock = mainModel::ejecutar_consulta_simple("
            SELECT stockDisponible FROM stock
            WHERE id_articulo = '$id_articulo' AND id_sucursal = '$idsucursal'
        ")->fetch();

            if ($stock) {
                $nuevo_stock = floatval($stock['stockDisponible']) + $cantidad;
                mainModel::ejecutar_consulta_simple("
                UPDATE stock
                SET stockDisponible = '$nuevo_stock',
                    stockUltActualizacion = '$fecha',
                    stockUsuActualizacion = '$usuario',
                    stockultimoIdActualizacion = '$idajuste'
                WHERE id_articulo = '$id_articulo' AND id_sucursal = '$idsucursal'
            ");
            } else {
                mainModel::ejecutar_consulta_simple("
                INSERT INTO stock (id_sucursal, id_articulo, stockDisponible, stockUltActualizacion, stockUsuActualizacion, stockultimoIdActualizacion)
                VALUES ('$idsucursal', '$id_articulo', '$cantidad_fisica', '$fecha', '$usuario', '$idajuste')
            ");
            }

            // Registrar movimiento
            $signo = $cantidad >= 0 ? 1 : -1;

            mainModel::ejecutar_consulta_simple("
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
                '$idsucursal',
                'AJUSTE_INV',
                '$id_articulo',
                '" . abs($cantidad) . "',
                0,
                '$costo',
                '$fecha',
                '$usuario',
                '$signo',
                'AJUSTE #$idajuste'
            )
        ");
        }

        // ✅ Actualizar ajuste como aplicado
        mainModel::ejecutar_consulta_simple("
        UPDATE ajuste_inventario
        SET estado = 2,
            ajustadoPor = '$usuario',
            fecha_ajuste = now()
        WHERE idajuste_inventario = '$idajuste'");

        // ✅ Limpiar sesión después de aplicar
        unset($_SESSION['Cdatos_articuloINV']);
        unset($_SESSION['id_inv_seleccionado']);
        unset($_SESSION['datos_ajuste_inv']);
        unset($_SESSION['alerta_inv']);

        return ["status" => "ok", "msg" => "Stock, movimientos y ajuste actualizados correctamente"];
    }
}
