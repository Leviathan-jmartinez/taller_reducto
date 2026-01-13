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
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }
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
        $datosINV = mainModel::ejecutar_consulta_simple("SELECT  idajuste_inventario, id_usuario, sucursal_id,estado, fecha, tipo_inv, descripcion, fecha_ajuste
        FROM ajuste_inventario
        where (idajuste_inventario like '%$inventario%' or descripcion like '%$inventario%' or tipo_inv like '%$inventario%') and estado = '1' and sucursal_id = '" . $_SESSION['nick_sucursal'] . "'
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
        $idSucursal = $_SESSION['nick_sucursal'];
        /* ==================================================
       1️⃣ CABECERA DEL AJUSTE
        ================================================== */
        $sqlCabecera = mainModel::ejecutar_consulta_simple("
        SELECT ai.idajuste_inventario, ai.sucursal_id,ai.tipo_inv, ai.descripcion, ai.fecha_ajuste, u.usu_nombre
        FROM ajuste_inventario ai
        INNER JOIN usuarios u ON u.id_usuario = ai.id_usuario
        WHERE ai.idajuste_inventario = '$idajuste' and ai.sucursal_id = '$idSucursal'");

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
        $conexion = mainModel::conectar();

        $sqlDetalle = $conexion->prepare("
            SELECT 
                aid.id_articulo,
                a.codigo,
                a.desc_articulo,
                aid.cantidad_teorica,
                aid.cantidad_fisica,
                aid.costo
            FROM ajuste_inventario_detalle aid
            INNER JOIN ajuste_inventario ai
                ON ai.idajuste_inventario = aid.idajuste_inventario
            INNER JOIN articulos a 
                ON a.id_articulo = aid.id_articulo
            WHERE aid.idajuste_inventario = :idajuste
            AND ai.sucursal_id = :sucursal_id");

        $sqlDetalle->bindParam(":idajuste", $idajuste, PDO::PARAM_INT);
        $sqlDetalle->bindParam(":sucursal_id", $idSucursal, PDO::PARAM_INT);

        $sqlDetalle->execute();
        $detalle = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);


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

        if (empty($_SESSION['Cdatos_articuloINV'])) {
            return ["status" => "error", "msg" => "No hay artículos para guardar"];
        }

        if (empty($_SESSION['nick_sucursal'])) {
            return ["status" => "error", "msg" => "Sucursal no definida"];
        }

        $idajuste   = (int) $_SESSION['id_inv_seleccionado'];
        $idSucursal = (int) $_SESSION['nick_sucursal'];

        $conexion = mainModel::conectar();
        $conexion->beginTransaction();

        try {

            // 🔐 Validar que el ajuste pertenezca a la sucursal
            $check = $conexion->prepare("
            SELECT 1
            FROM ajuste_inventario
            WHERE idajuste_inventario = :idajuste
              AND sucursal_id = :sucursal_id
        ");
            $check->execute([
                ":idajuste"     => $idajuste,
                ":sucursal_id"  => $idSucursal
            ]);

            if ($check->rowCount() === 0) {
                throw new Exception("El ajuste no pertenece a la sucursal activa");
            }

            // 🔁 Actualizar detalles
            $sqlUpd = $conexion->prepare("
            UPDATE ajuste_inventario_detalle d
            INNER JOIN ajuste_inventario a
                ON a.idajuste_inventario = d.idajuste_inventario
            SET d.cantidad_fisica = :cantidad_fisica,
                d.diferencia      = :diferencia,
                d.costo           = :costo
            WHERE d.idajuste_inventario = :idajuste
              AND d.id_articulo = :id_articulo
              AND a.sucursal_id = :sucursal_id
        ");

            foreach ($_SESSION['Cdatos_articuloINV'] as $item) {

                $sqlUpd->execute([
                    ":cantidad_fisica" => (float) $item['cantidad_fisica'],
                    ":diferencia"      => (float) $item['diferencia'],
                    ":costo"           => (float) $item['costo'],
                    ":idajuste"        => $idajuste,
                    ":id_articulo"     => (int) $item['ID'],
                    ":sucursal_id"     => $idSucursal
                ]);

                if ($sqlUpd->rowCount() === 0) {
                    throw new Exception("No se pudo actualizar un artículo del ajuste");
                }
            }

            $conexion->commit();
            return ["status" => "ok"];
        } catch (Exception $e) {

            $conexion->rollBack();
            return [
                "status" => "error",
                "msg"    => $e->getMessage()
            ];
        }
    }

    /* ===============================
        Aplicar ajuste de stock
    =============================== */
    public function aplicar_ajuste_stock_controlador($idsucursal)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (
            empty($_SESSION['id_inv_seleccionado']) ||
            empty($_SESSION['Cdatos_articuloINV'])
        ) {
            return ["status" => "error", "msg" => "No hay ajuste seleccionado para aplicar"];
        }

        $idajuste = (int) $_SESSION['id_inv_seleccionado'];
        $usuario  = $_SESSION['id_str'];
        $fecha    = date("Y-m-d H:i:s");

        $ajustesAplicados = 0;

        foreach ($_SESSION['Cdatos_articuloINV'] as $item) {

            $cantidad = (float) $item['diferencia'];

            // 🔴 CLAVE: si no hay diferencia, no se hace nada
            if ($cantidad == 0) {
                continue;
            }

            $id_articulo     = (int) $item['ID'];
            $costo           = (float) $item['costo'];
            $cantidad_fisica = (float) $item['cantidad_fisica'];

            /* ================= STOCK ================= */
            $stock = mainModel::ejecutar_consulta_simple("
            SELECT stockDisponible 
            FROM stock
            WHERE id_articulo = '$id_articulo'
              AND id_sucursal = '$idsucursal'
        ")->fetch();

            if ($stock) {
                $nuevo_stock = (float) $stock['stockDisponible'] + $cantidad;

                mainModel::ejecutar_consulta_simple("
                UPDATE stock
                SET stockDisponible = '$nuevo_stock',
                    stockUltActualizacion = '$fecha',
                    stockUsuActualizacion = '$usuario',
                    stockultimoIdActualizacion = '$idajuste'
                WHERE id_articulo = '$id_articulo'
                  AND id_sucursal = '$idsucursal'
            ");
            } else {
                // Solo crear stock si hay diferencia real
                mainModel::ejecutar_consulta_simple("
                INSERT INTO stock
                (id_sucursal, id_articulo, stockcant_max, stockcant_min,
                 stockDisponible, stockUltActualizacion,
                 stockUsuActualizacion, stockultimoIdActualizacion)
                VALUES
                ('$idsucursal', '$id_articulo', 200, 15,
                 '$cantidad_fisica', '$fecha', '$usuario', '$idajuste')
            ");
            }

            /* ================= MOVIMIENTO ================= */
            $signo = $cantidad > 0 ? 1 : -1;

            mainModel::ejecutar_consulta_simple("
            INSERT INTO sucmovimientostock (
                id_sucursal,
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

            $ajustesAplicados++;
        }

        // 🔴 Si no hubo diferencias reales, no aplicar el ajuste
        if ($ajustesAplicados === 0) {
            return [
                "status" => "warning",
                "msg" => "No se aplicó ningún ajuste porque no había diferencias"
            ];
        }

        /* ================= CERRAR AJUSTE ================= */
        mainModel::ejecutar_consulta_simple("
        UPDATE ajuste_inventario
        SET estado = 2,
            ajustadoPor = '$usuario',
            fecha_ajuste = NOW()
        WHERE idajuste_inventario = '$idajuste'
        ");

        /* ================= LIMPIAR SESIÓN ================= */
        unset(
            $_SESSION['Cdatos_articuloINV'],
            $_SESSION['id_inv_seleccionado'],
            $_SESSION['datos_ajuste_inv'],
            $_SESSION['alerta_inv']
        );

        return [
            "status" => "ok",
            "msg" => "Ajuste aplicado correctamente ($ajustesAplicados productos ajustados)"
        ];
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
            $consulta = "SELECT SQL_CALC_FOUND_ROWS ai.idajuste_inventario as idajuste_inventario, ai.sucursal_id as sucursal_id, ai.id_usuario as id_usuario, ai.estado as estadoInv, ai.fecha as fecha, ai.tipo_inv as tipo_inv, 
            ai.descripcion as descripcion, ai.fecha_ajuste as fecha_ajuste, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick
            FROM ajuste_inventario ai 
            INNER JOIN usuarios u on u.id_usuario = ai.id_usuario
            WHERE date(fecha) >= '$busqueda1' AND date(fecha) <='$busqueda2' AND ai.sucursal_id = '" . $_SESSION['nick_sucursal'] . "'
            ORDER BY fecha ASC LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS ai.idajuste_inventario as idajuste_inventario, ai.sucursal_id as sucursal_id,ai.id_usuario as id_usuario, ai.estado as estadoInv, ai.fecha as fecha, ai.tipo_inv as tipo_inv, 
            ai.descripcion as descripcion, ai.fecha_ajuste as fecha_ajuste, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick
            FROM ajuste_inventario ai 
            INNER JOIN usuarios u on u.id_usuario = ai.id_usuario
            WHERE ai.estado != 0 and ai.sucursal_id = '" . $_SESSION['nick_sucursal'] . "'
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
    /* ===============================
           ANULAR INVENTARIO  
    =============================== */

    public function anular_inv_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $id = (int) mainModel::limpiar_string(
            mainModel::decryption($_POST['inv_id_del'])
        );

        $db = mainModel::conectar();
        $db->beginTransaction();

        try {

            /* ===== CABECERA CON BLOQUEO ===== */
            $ajuste = $db->query("
            SELECT estado, sucursal_id
            FROM ajuste_inventario
            WHERE idajuste_inventario = $id
            FOR UPDATE
        ")->fetch(PDO::FETCH_ASSOC);

            if (!$ajuste) {
                throw new Exception("Ajuste no encontrado");
            }

            // 🔴 ya anulado
            if ((int)$ajuste['estado'] === 0) {
                throw new Exception("El ajuste ya fue anulado");
            }

            /* ===== ESTADO 1: NO APLICADO ===== */
            if ((int)$ajuste['estado'] === 1) {

                $db->exec("
                UPDATE ajuste_inventario
                SET estado = 0
                WHERE idajuste_inventario = $id
            ");

                $db->commit();

                return [
                    "Alerta" => "recargar",
                    "Titulo" => "Ajuste anulado",
                    "Texto"  => "El ajuste fue anulado correctamente",
                    "Tipo"   => "success"
                ];
            }

            /* ===== ESTADO 2: APLICADO ===== */
            if ((int)$ajuste['estado'] === 2) {

                $detalle = $db->query("
                SELECT id_articulo, diferencia, costo
                FROM ajuste_inventario_detalle
                WHERE idajuste_inventario = $id
                  AND diferencia <> 0
            ")->fetchAll(PDO::FETCH_ASSOC);

                if (empty($detalle)) {
                    throw new Exception("No hay movimientos para revertir");
                }

                foreach ($detalle as $d) {

                    /* ===== REVERTIR STOCK ===== */
                    $db->exec("
                    UPDATE stock
                    SET stockDisponible = stockDisponible - {$d['diferencia']},
                        stockUltActualizacion = NOW(),
                        stockUsuActualizacion = {$_SESSION['id_str']},
                        stockultimoIdActualizacion = $id
                    WHERE id_sucursal = {$ajuste['sucursal_id']}
                      AND id_articulo = {$d['id_articulo']}
                ");

                    /* ===== MOVIMIENTO INVERSO ===== */
                    $signo = ($d['diferencia'] > 0) ? -1 : 1;

                    $db->exec("
                    INSERT INTO sucmovimientostock (
                        id_sucursal,
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
                        {$ajuste['sucursal_id']},
                        'ANULACION_AJUSTE_INV',
                        {$d['id_articulo']},
                        " . abs($d['diferencia']) . ",
                        0,
                        {$d['costo']},
                        NOW(),
                        {$_SESSION['id_str']},
                        $signo,
                        'Anulación ajuste inventario #$id'
                    )
                ");
                }

                /* ===== MARCAR COMO ANULADO ===== */
                $db->exec("
                UPDATE ajuste_inventario
                SET estado = 0
                WHERE idajuste_inventario = $id
            ");
            }

            $db->commit();

            return [
                "Alerta" => "recargar",
                "Titulo" => "Ajuste anulado",
                "Texto"  => "El ajuste fue anulado y el stock fue revertido correctamente",
                "Tipo"   => "success"
            ];
        } catch (Exception $e) {
            $db->rollBack();

            return [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => $e->getMessage(),
                "Tipo"   => "error"
            ];
        }
    }
}
