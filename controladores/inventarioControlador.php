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
    /* ===============================
        Guardar ajuste de inventario   
    =============================== */
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

    /* ===============================
        PAGINADOR COMPRA    
    =============================== */
    public function paginador_inv_controlador($pagina, $registros, $privilegio, $url, $busqueda1, $busqueda2)
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $privilegio = mainModel::limpiar_string($privilegio);
        $busqueda1 = mainModel::limpiar_string($busqueda1);
        $busqueda2 = mainModel::limpiar_string($busqueda2);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        if (!empty($busqueda1) && !empty($busqueda2)) {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS ai.idajuste_inventario as idajuste_inventario, ai.id_usuario as id_usuario, ai.estado as estadoInv, ai.fecha as fecha, ai.tipo_inv as tipo_inv, 
            ai.descripcion as descripcion, ai.fecha_ajuste as fecha_ajuste, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick
            FROM ajuste_inventario ai 
            INNER JOIN usuarios u on u.id_usuario = ai.id_usuario
            WHERE date(fecha) >= '$busqueda1' AND date(fecha) <='$busqueda2'
            ORDER BY fecha ASC LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS ai.idajuste_inventario as idajuste_inventario, ai.id_usuario as id_usuario, ai.estado as estadoInv, ai.fecha as fecha, ai.tipo_inv as tipo_inv, 
            ai.descripcion as descripcion, ai.fecha_ajuste as fecha_ajuste, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick
            FROM ajuste_inventario ai 
            INNER JOIN usuarios u on u.id_usuario = ai.id_usuario
            WHERE ai.estado != 0
            ORDER BY ai.idajuste_inventario ASC LIMIT $inicio,$registros";
        }
        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta);
        $datos = $datos->fetchAll();

        $total = $conexion->query("SELECT FOUND_ROWS()");
        $total = (int) $total->fetchColumn();

        $Npaginas = ceil($total / $registros);

        $tabla .= '<div class="table-responsive">
					<table class="table table-dark table-sm">
						<thead>
							<tr class="text-center roboto-medium">
								<th>#</th>
                                <th>Número de Inventario</th>
                                <th>Tipo de Inventario</th>
                                <th>Fecha Creación</th>
                                <th>Observación</th>
                                <th>Creado Por</th>
                                <th>Estado</th>';
        if ($privilegio == 1 || $privilegio == 2) {
            $tabla .=           '<th>ANULAR</th>';
        }
        $tabla .= '
						</tr>
						</thead>
						<tbody>';
        if ($total >= 1 && $pagina <= $Npaginas) {
            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;
            foreach ($datos as $rows) {
                switch ($rows['estadoInv']) {
                    case 1:
                        $estadoBadge = '<span class="badge bg-primary">Activo</span>';
                        break;
                    case 2:
                        $estadoBadge = '<span class="badge bg-success">Procesado</span>';
                        break;
                    case 0:
                        $estadoBadge = '<span class="badge bg-danger">Anulado</span>';
                        break;
                    default:
                        $estadoBadge = '<span class="badge bg-secondary">Desconocido</span>';
                }
                $tabla .= '
                            <tr class="text-center">
								<td>' . $contador . '</td>
								<td>' . $rows['idajuste_inventario'] . '</td>
								<td>' . $rows['tipo_inv'] . '</td>
								<td>' . date("d-m-Y", strtotime($rows['fecha'])) . '</td>
								<td>' . $rows['descripcion'] . '</td>
                                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                                <td>' . $estadoBadge . '</td>';
                if ($privilegio == 1 || $privilegio == 2) {
                    $tabla .= '<td>
									<form class="FormularioAjax" action="' . SERVERURL . 'ajax/inventarioAjax.php" method="POST" data-form="delete" autocomplete="off" action="">
                                    <input type="hidden" name="inv_id_del" value=' . mainModel::encryption($rows['idajuste_inventario']) . '>
										<button type="submit" class="btn btn-warning">
											<i class="far fa-trash-alt"></i>
										</button>
									</form>
								</td>';
                }

                $tabla .= '</tr>';
                $contador++;
            }
            $reg_final = $contador - 1;
        } else {
            if ($total >= 1) {
                $tabla .= '<tr class="text-center"> <td colspan="6"> <a href="' . $url . '" class="btn btn-reaised btn-primary btn-sm"> Haga click aqui para recargar el listado </a> </td> </tr> ';
            } else {
                $tabla .= '<tr class="text-center"> <td colspan="6"> No hay regitros en el sistema</td> </tr> ';
            }
        }

        $tabla .= '       </tbody>
					</table>
				</div>';
        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '<p class="text-right"> Mostrando registro ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '</p>';
            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }
        echo $tabla;
    }


    private function revertir_ajuste_stock_controlador($idajuste)
    {
        $pdo = mainModel::conectar();
        $pdo->beginTransaction();

        try {

            $detalle = $pdo->query("
            SELECT id_articulo, diferencia, costo
            FROM ajuste_inventario_detalle
            WHERE idajuste_inventario = '$idajuste'
        ")->fetchAll(PDO::FETCH_ASSOC);

            $sucursal = $_SESSION['nick_sucursal'];
            $usuario  = $_SESSION['id_str'];
            $fecha    = date("Y-m-d H:i:s");

            foreach ($detalle as $item) {

                $id_art = $item['id_articulo'];
                $cant   = floatval($item['diferencia']);

                // revertir stock
                $pdo->query("
                UPDATE stock
                SET stockDisponible = stockDisponible - ($cant),
                    stockUltActualizacion = '$fecha',
                    stockUsuActualizacion = '$usuario'
                WHERE id_articulo = '$id_art'
                AND id_sucursal = '$sucursal'
            ");

                // movimiento de anulación
                $signo = $cant >= 0 ? -1 : 1;

                $pdo->query("
                INSERT INTO sucmovimientostock (
                    LocalId, TipoMovStockId, MovStockProductoId,
                    MovStockCantidad, MovStockCosto,
                    MovStockFechaHora, MovStockUsuario,
                    MovStockSigno, MovStockReferencia
                ) VALUES (
                    '$sucursal',
                    'AJUSTE_INV_REV',
                    '$id_art',
                    '" . abs($cant) . "',
                    '{$item['costo']}',
                    '$fecha',
                    '$usuario',
                    '$signo',
                    'REVERSIÓN AJUSTE #$idajuste'
                )
            ");
            }

            $pdo->query("
            UPDATE ajuste_inventario
            SET estado = 3, fecha_ajuste = '$fecha'
            WHERE idajuste_inventario = '$idajuste'
        ");

            $pdo->commit();

            unset($_SESSION['id_inv_seleccionado']);

            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return $e->getMessage();
        }
    }

    public function anular_inv_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $idinv = mainModel::decryption($_POST['inv_id_del']);
        $idinv = mainModel::limpiar_string($idinv);

        $inv = mainModel::ejecutar_consulta_simple("
        SELECT estado 
        FROM ajuste_inventario 
        WHERE idajuste_inventario = '$idinv'")->fetch(PDO::FETCH_ASSOC);

        if (!$inv) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Inventario no encontrado",
                "Tipo"   => "error"
            ];
        }

        /* ============================
       INVENTARIO YA AJUSTADO
        ============================ */
        if ($inv['estado'] == 2 && !isset($_POST['confirmar_reversion'])) {

            $_SESSION['id_inv_seleccionado'] = $idinv;

            return [
                "Alerta" => "confirmar",
                "Titulo" => "Inventario ya ajustado",
                "Texto"  => "Este inventario ya modificó el stock. ¿Desea revertir los ajustes?",
                "Tipo"   => "warning"
            ];
        }

        /* ============================
       REVERTIR AJUSTE DE STOCK
        ============================ */
        if ($inv['estado'] == 2 && isset($_POST['confirmar_reversion'])) {

            $resp = $this->revertir_ajuste_stock_controlador($idinv);

            if ($resp !== true) {
                return [
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto"  => $resp,
                    "Tipo"   => "error"
                ];
            }

            return [
                "Alerta" => "recargar",
                "Titulo" => "Inventario revertido",
                "Texto"  => "El stock fue restaurado correctamente",
                "Tipo"   => "success"
            ];
        }

        /* ============================
       INVENTARIO NO AJUSTADO
        ============================ */
        if ($inv['estado'] == 1) {

            inventarioModelo::anular_inv_modelo($idinv);

            return [
                "Alerta" => "recargar",
                "Titulo" => "Inventario anulado",
                "Texto"  => "El inventario fue anulado correctamente",
                "Tipo"   => "success"
            ];
        }
    }
}
