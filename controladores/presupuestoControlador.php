<?php
if ($peticionAjax) {
    require_once "../modelos/presupuestoModelo.php";
} else {
    require_once "./modelos/presupuestoModelo.php";
}

class presupuestoControlador extends presupuestoModelo
{
    /**controlador buscador proveedor */
    public function buscar_proveedor_controlador()
    {
        $proveedor  = mainModel::limpiar_string($_POST['buscar_proveedorPre']);

        if ($proveedor == "") {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    Debes introducir el RUC o RAZON SOCIAL
                                </p>
                            </div>';
            exit();
        }
        /**seleccionar proveedor */
        $datos_proveedor = mainModel::ejecutar_consulta_simple("SELECT * FROM proveedores where ruc like '%$proveedor%' or razon_social like '%$proveedor%' or 
        telefono like '%$proveedor%' order by razon_social desc");

        if ($datos_proveedor->rowCount() >= 1) {
            $datos_proveedor = $datos_proveedor->fetchAll();
            $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>';
            foreach ($datos_proveedor as $rows) {
                $tabla .= '
                        <tr class="text-center">
                            <td>' . $rows['ruc'] . ' ' . $rows['razon_social'] . '</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="agregar_proveedorPre(' . $rows['idproveedores'] . ')"><i class="fas fa-user-plus"></i></button>
                            </td>
                        </tr>';
            }
            $tabla .= '</tbody></table></div>';
            return $tabla;
        } else {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún proveedor en el sistema que coincida con <strong>“' . $proveedor . '”</strong>
                                </p>
                            </div>';
        }
    }
    /**fin controlador */

    /**Controlador agregar proveedor */
    public function agregar_proveedor_controlador()
    {
        $id  = mainModel::limpiar_string($_POST['id_agregar_proveedorPre']);

        $check_proveedor = mainModel::ejecutar_consulta_simple("SELECT * FROM proveedores WHERE idproveedores = '$id'");

        if ($check_proveedor->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error!",
                "Texto" => "Proveedor no encontrado",
                "Tipo" => "error"
            ]);
            exit();
        }

        $campos = $check_proveedor->fetch();

        session_start(['name' => 'STR']);

        // 🔥 CLAVE: detectar tipo
        unset($_SESSION['Cdatos_proveedorPre']);

        $_SESSION['Cdatos_proveedorPre'] = [
            "ID" => $campos['idproveedores'],
            "RUC" => $campos['ruc'],
            "RAZON" => $campos['razon_social'],
            "TELEFONO" => $campos['telefono']
        ];

        echo json_encode([
            "Alerta" => "recargar",
            "Titulo" => "Proveedor agregado!",
            "Texto" => "Proveedor agregado correctamente",
            "Tipo" => "success"
        ]);
    }
    /**fin controlador */

    /**Controlador eliminar proveedor */
    public function eliminar_proveedor_controlador()
    {
        session_start(['name' => 'STR']);
        unset($_SESSION['Cdatos_proveedorPre']);
        if (empty($_SESSION['datos_proveedorPre'])) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Proveedor removido!",
                "Texto" => "Los datos del Proveedor fueron removidos correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido remover los datos del Proveedor",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /**fin controlador */
/**controlador agregar presupuesto */
    public function agregar_presupuesto_controlador()
    {
        session_start(['name' => 'STR']);
        $fecha_venc = mainModel::limpiar_string($_POST['fecha_vencimientoPre'] ?? '');
        if (!mainModel::tienePermiso('compra.presupuesto.crear')) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Acceso no autorizado!",
                "Texto" => "No tienes permisos para realizar esta acción",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (empty($_SESSION['Cdatos_proveedorPre']) || empty($_SESSION['Cdatos_articuloPre'])) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Datos incompletos para generar el presupuesto",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (empty($fecha_venc)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error!",
                "Texto" => "Debes seleccionar la fecha de vencimiento",
                "Tipo" => "error"
            ]);
            exit();
        }

            if (mainModel::verificarFecha($fecha_venc)) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error!",
                    "Texto" => "La fecha de vencimiento no es valida",
                    "Tipo" => "error"
                ]);
                exit();
            }

            if (strtotime($fecha_venc) < strtotime(date('Y-m-d'))) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error!",
                    "Texto" => "La fecha de vencimiento no puede ser menor a hoy",
                    "Tipo" => "error"
                ]);
                exit();
            }

            /* ========= CABECERA ========= */
            $datos_presu_agg = [
                "idPedido"   => $_SESSION['id_pedido_seleccionado'],
                "usuario"    => $_SESSION['id_str'],
                "sucursal"  => $_SESSION['nick_sucursal'],
                "proveedor"  => $_SESSION['Cdatos_proveedorPre']['ID'],
                "total"      => $_SESSION['total_pre'],
                "fecha_venc" => $fecha_venc
            ];

            presupuestoModelo::actualizar_pedido_modelo([
                "idpedido_cabecera" => $_SESSION['id_pedido_seleccionado'],
                "sucursal" => $_SESSION['nick_sucursal'],
                "updatedby" => $_SESSION['id_str']
            ]);

            $idpresupuestoCab = presupuestoModelo::agregar_presupuestoC_modelo2($datos_presu_agg);

            if ($idpresupuestoCab <= 0) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto" => "No se pudo crear la cabecera del presupuesto",
                    "Tipo" => "error"
                ]);
                exit();
            }

            /* ========= DETALLE ========= */
            $insertados = [];

            foreach ($_SESSION['Cdatos_articuloPre'] as $article) {

                $idArticulo = (int)$article['ID'];

                // 🔒 evita insertar dos veces el mismo artículo
                if (in_array($idArticulo, $insertados, true)) {
                    continue;
                }

                presupuestoModelo::agregar_presupuestoD_modelo([
                    "presupuestoid" => $idpresupuestoCab,
                    "articulo"      => $idArticulo,
                    "cantidad"      => $article['cantidad'],
                    "precio"        => $article['precio'],
                    "subtotal"      => $article['subtotal']
                ]);

                $insertados[] = $idArticulo;
            }

            unset($_SESSION['Cdatos_proveedorPre']);
            unset($_SESSION['Cdatos_articuloPre']);

            echo json_encode([
                "Alerta" => "recargar",
                "Titulo" => "Presupuesto guardado!",
                "Texto" => "El presupuesto se registró correctamente",
                "Tipo" => "success"
            ]);
    }
    /**fin controlador */

    public function buscar_pedido_controlador()
    {
        $pedidoCompra  = mainModel::limpiar_string($_POST['buscar_pedidoPre']);
        session_start(['name' => 'STR']);
        if ($pedidoCompra == "") {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    Debes introducir el parámetro de búsqueda
                                </p>
                            </div>';
            exit();
        }
        /**seleccionar proveedor */
        $datosPedido = mainModel::ejecutar_consulta_simple("select pc.idpedido_cabecera as idpedido_cabecera, pc.id_sucursal as id_sucursal,pc.id_usuario as id_usuario, 
        pc.fecha as fecha, pc.estado as estadoPe, pc.updated as updated, pc.updatedby as updatedby, concat(u.usu_nombre, ' ',u.usu_apellido ) as usuario
        from pedido_cabecera pc 
        inner join usuarios u on u.id_usuario = pc.id_usuario
        where (pc.idpedido_cabecera like '%$pedidoCompra%' or pc.fecha like '%$pedidoCompra%' or 
        concat(u.usu_nombre, ' ',u.usu_apellido ) like '%$pedidoCompra%') and pc.estado = '1'
        and pc.id_sucursal = '" . $_SESSION['nick_sucursal'] . "'
        order by pc.fecha desc");

        if ($datosPedido->rowCount() >= 1) {
            $datosPedido = $datosPedido->fetchAll();
            $tabla = '<div class="table-responsive"><table class="table table-dark table-sm"><tbody>
                        <tr class="text-center">
                            <th>Número de Pedido</th>
                            <th>Fecha Creación</th>
                            <th>Creado Por</th>
                            <th></th>
                        </tr>';
            foreach ($datosPedido as $rows) {
                $tabla .= '
                        <tr class="text-center">
                            <td>' . $rows['idpedido_cabecera'] . '</td>
                            <td>' . date("d-m-Y", strtotime($rows['fecha'])) . '</td>
                            <td>' . $rows['usuario'] . '</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="agregar_pedidoPre(' . $rows['idpedido_cabecera'] . ')"><i class="fas fa-user-plus"></i></button>
                            </td>
                        </tr>';
            }
            $tabla .= '</tbody></table></div>';
            return $tabla;
        } else {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún pedido en el sistema que coincida con <strong>“' . $pedidoCompra . '”</strong>
                                </p>
                            </div>';
        }
    }

    /**controlador buscar pedido */
    public function cargar_pedido_controlador()
    {
        session_start(['name' => 'STR']);

        $idPedido = mainModel::limpiar_string($_POST['id_pedido_seleccionado'] ?? '');
        if (empty($idPedido)) {
            $_SESSION['alerta_presupuesto'] = [
                "tipo" => "error",
                "mensaje" => "No se recibió ID de pedido"
            ];
            header("Location: " . SERVERURL . "presupuesto-nuevo/");
            exit();
        }
        $_SESSION['id_pedido_seleccionado'] = $idPedido;

        // Detalle del pedido (artículos)
        $sqlDetalle = mainModel::ejecutar_consulta_simple("
        SELECT pd.id_articulo, pd.cantidad, a.desc_articulo, a.codigo
        FROM pedido_detalle pd
        INNER JOIN articulos a ON a.id_articulo = pd.id_articulo
        WHERE pd.idpedido_cabecera = '$idPedido'");
        $detalle = $sqlDetalle->fetchAll();

        unset($_SESSION['Cdatos_articuloPre']);
        $_SESSION['Cdatos_articuloPre'] = [];

        foreach ($detalle as $row) {
            $_SESSION['Cdatos_articuloPre'][$row['id_articulo']] = [
                "ID" => $row['id_articulo'],
                "codigo" => $row['codigo'],
                "descripcion" => $row['desc_articulo'],
                "cantidad" => $row['cantidad'],
                "precio" => 0,
                "subtotal" => 0
            ];
        }


        // Redirigir a la página para que se recargue
        header("Location: " . SERVERURL . "presupuesto-nuevo/");
        exit();
    }
    /**fin controlador */

    /**Controlador paginar presupuestos */
    public function paginador_presupuestos_controlador($pagina, $registros,  $url, $busqueda1, $busqueda2, $nro_presupuesto = '', $proveedor = '', $estado_presupuesto = '', $orden = 'fecha', $direccion = 'DESC')
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $busqueda1 = mainModel::limpiar_string($busqueda1);
        $busqueda2 = mainModel::limpiar_string($busqueda2);
        $nro_presupuesto = mainModel::limpiar_string($nro_presupuesto);
        $proveedor = mainModel::limpiar_string($proveedor);
        $estado_presupuesto = mainModel::limpiar_string($estado_presupuesto);
        $orden = mainModel::limpiar_string($orden);
        $direccion = strtoupper(mainModel::limpiar_string($direccion));

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        $filtros = [];
        if (!empty($busqueda1) && !empty($busqueda2)) {
            $filtros[] = [
                "campo" => "pc.fecha",
                "tipo"  => "DATE_RANGE",
                "desde" => $busqueda1,
                "hasta" => $busqueda2
            ];
        }

        if ($nro_presupuesto != "") {
            $filtros[] = [
                "campo" => "pc.idpresupuesto_compra",
                "tipo"  => "=",
                "valor" => $nro_presupuesto
            ];
        }
        if ($proveedor != "") {
            $filtros[] = [
                "campo" => "p.razon_social",
                "tipo"  => "LIKE",
                "valor" => $proveedor
            ];
        }
        if ($estado_presupuesto !== "") {
            $filtros[] = [
                "campo" => "pc.estado",
                "tipo"  => "=",
                "valor" => $estado_presupuesto
            ];
        }

        $columnasOrdenSql = [
            'fecha' => 'pc.fecha',
            'estado' => 'pc.estado'
        ];

        $ordenamiento = mainModel::preparar_ordenamiento($orden, $direccion, $columnasOrdenSql, 'fecha', 'DESC');
        $orden = $ordenamiento['orden'];
        $direccion = $ordenamiento['direccion'];
        $filtrosSQL = mainModel::construirFiltros($filtros);
        $resultado = presupuestoModelo::listar_presupuestos_modelo($inicio, $registros, $filtrosSQL, "ORDER BY " . $ordenamiento['sql'] . ", pc.idpresupuesto_compra DESC");

        $datos = $resultado['datos'];
        $total = $resultado['total'];

        $Npaginas = ceil($total / $registros);

        $tabla .= '<div class="table-responsive">
					<table class="table table-dark table-sm">
						<thead>
							<tr class="text-center roboto-medium">
								<th>#</th>
								<th>CÓDIGO PRESUPUESTO</th>
                                <th>PROVEEDOR</th>
                                <th>' . mainModel::link_orden_tabla($url, 'fecha', 'FECHA', $orden, $direccion, 'presupuesto_orden', 'presupuesto_direccion') . '</th>
                                <th>CREADO POR</th>
                                <th>' . mainModel::link_orden_tabla($url, 'estado', 'ESTADO', $orden, $direccion, 'presupuesto_orden', 'presupuesto_direccion') . '</th>
                                <th>DETALLE</th>';
        if (mainModel::tienePermiso('compra.presupuesto.anular')) {
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
                switch ($rows['estadoPre']) {
                    case 1:
                        $estadoBadge = '<span class="badge bg-primary">Pendiente</span>';
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
								<td>' . $rows['idpresupuesto_compra'] . '</td>
								<td>' . $rows['razon_social'] . '</td>
								<td>' . date("d-m-Y", strtotime($rows['fecha'])) . '</td>
                                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                                <td>' . $estadoBadge . '</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" onclick="verDetallePresupuestoCompra(\'' . mainModel::encryption($rows['idpresupuesto_compra']) . '\')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>';
                if (mainModel::tienePermiso('compra.presupuesto.anular')) {
                    $tabla .= '<td>
									<form class="FormularioAjax" action="' . SERVERURL . 'ajax/presupuestoAjax.php" method="POST" data-form="delete" data-anulacion="true" data-anulacion-titulo="Anular presupuesto de compra" autocomplete="off" action="">
                                    <input type="hidden" name="presupuesto_id_del" value=' . mainModel::encryption($rows['idpresupuesto_compra']) . '>
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
            $colspan = mainModel::tienePermiso('compra.presupuesto.anular') ? 8 : 7;
            if ($total >= 1) {
                $tabla .= '<tr class="text-center"> <td colspan="' . $colspan . '"> <a href="' . $url . '" class="btn btn-reaised btn-primary btn-sm"> Haga click aqui para recargar el listado </a> </td> </tr> ';
            } else {
                $tabla .= '<tr class="text-center"> <td colspan="' . $colspan . '"> No hay regitros en el sistema</td> </tr> ';
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
    /**fin controlador */

    public function detalle_presupuesto_compra_controlador()
    {
        if (!mainModel::tienePermiso('compra.presupuesto.ver')) {
            return json_encode([
                'status' => 'error',
                'html' => '<div class="alert alert-danger mb-0">Acceso no autorizado</div>'
            ]);
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }
        $id = (int) mainModel::limpiar_string(mainModel::decryption($_POST['detalle_presupuesto_compra'] ?? ''));
        $sucursal = (int) ($_SESSION['nick_sucursal'] ?? 0);

        if ($id <= 0 || $sucursal <= 0) {
            return json_encode([
                'status' => 'error',
                'html' => '<div class="alert alert-warning mb-0">No se pudo validar el presupuesto solicitado.</div>'
            ]);
        }

        $datos = presupuestoModelo::detalle_presupuesto_compra_modelo($id, $sucursal);
        if (!$datos['cabecera']) {
            return json_encode([
                'status' => 'error',
                'html' => '<div class="alert alert-warning mb-0">No se encontro el presupuesto en la sucursal activa.</div>'
            ]);
        }

        $cab = $datos['cabecera'];
        $estadoTexto = ['0' => 'Anulado', '1' => 'Pendiente', '2' => 'Procesado'];
        $total = 0;

        $html = '
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Presupuesto:</strong> #' . (int)$cab['idpresupuesto_compra'] . '<br>
                    <strong>Proveedor:</strong> ' . htmlspecialchars($cab['razon_social'], ENT_QUOTES, 'UTF-8') . '<br>
                    <strong>RUC:</strong> ' . htmlspecialchars($cab['ruc'], ENT_QUOTES, 'UTF-8') . '
                </div>
                <div class="col-md-6">
                    <strong>Fecha:</strong> ' . date('d/m/Y', strtotime($cab['fecha'])) . '<br>
                    <strong>Vencimiento:</strong> ' . (!empty($cab['fecha_venc']) ? date('d/m/Y', strtotime($cab['fecha_venc'])) : '-') . '<br>
                    <strong>Estado:</strong> ' . ($estadoTexto[(string)$cab['estado']] ?? 'Desconocido') . '<br>
                    <strong>Usuario:</strong> ' . htmlspecialchars(trim($cab['usu_nombre'] . ' ' . $cab['usu_apellido']), ENT_QUOTES, 'UTF-8') . '
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>Codigo</th>
                            <th>Articulo</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($datos['detalle'] as $row) {
            $total += (float)$row['subtotal'];
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . htmlspecialchars($row['desc_articulo'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td class="text-right">' . number_format((float)$row['cantidad'], 2, ',', '.') . '</td>
                    <td class="text-right">' . number_format((float)$row['precio'], 0, ',', '.') . '</td>
                    <td class="text-right">' . number_format((float)$row['subtotal'], 0, ',', '.') . '</td>
                </tr>';
        }

        $html .= '
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total</th>
                            <th class="text-right">Gs. ' . number_format($total, 0, ',', '.') . '</th>
                        </tr>
                    </tfoot>
                </table>
            </div>';

        return json_encode(['status' => 'ok', 'html' => $html]);
    }

    /**Controlador anular presupuesto */
    public function anular_presupuesto_controlador()
    {

        if (!mainModel::tienePermiso('compra.presupuesto.anular')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Advertencia!",
                "Texto" => "No posee los permisos necesarios para realizar esta acción",
                "Tipo" => "error"
            ]);
        }
        $id = mainModel::decryption($_POST['presupuesto_id_del']);
        $id = mainModel::limpiar_string($id);
        $motivo = trim(mainModel::limpiar_string($_POST['observacion_anulacion'] ?? ''));
        session_start(['name' => 'STR']);

        if ($motivo === '') {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Motivo requerido",
                "Texto" => "Debe ingresar la observacion o motivo de anulacion",
                "Tipo" => "warning"
            ]);
            exit();
        }
        $check_presupuesto = mainModel::ejecutar_consulta_simple("SELECT idpresupuesto_compra FROM presupuesto_compra WHERE idpresupuesto_compra = '$id' AND id_sucursal = '" . $_SESSION['nick_sucursal'] . "'");
        if ($check_presupuesto->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El PRESUPUESTO que intenta anular no existe en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $check_presupuestoestado = mainModel::ejecutar_consulta_simple("SELECT idpresupuesto_compra FROM presupuesto_compra WHERE idpresupuesto_compra = '$id' AND estado = 2 AND id_sucursal = '" . $_SESSION['nick_sucursal'] . "'");
        if ($check_presupuestoestado->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El PRESUPUESTO que intenta anular se encuentra procesado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }



        $datos_presupuesto_del = [
            "updatedby" => $_SESSION['id_str'],
            "sucursal" => $_SESSION['nick_sucursal'],
            "idpresupuesto_compra" => $id,
            "motivo" => $motivo
        ];

        if (presupuestoModelo::anular_presupuesto_modelo($datos_presupuesto_del)) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Pedido Anulado!",
                "Texto" => "El PRESUPUESTO ha sido anulado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No se pudo anular el PRESUPUESTO seleccionado",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /**fin controlador */

    public function datos_presupuesto_controlador($tipo, $id = null)
    {
        $tipo = mainModel::limpiar_string($tipo);

        // Solo desencriptar cuando el tipo lo necesita
        if ($tipo === "unico") {

            if (empty($id)) {
                return false;
            }

            $id = mainModel::decryption($id);
            $id = mainModel::limpiar_string($id);
        } else {
            $id = null;
        }

        return presupuestoModelo::datos_presupuesto_modelo($tipo, $id);
    }

    /**fin controlador */

    public function eliminar_articulo_controlador()
    {
        session_start(['name' => 'STR']);

        $id = $_POST['id_eliminar_articuloPre'] ?? null;

        if (!$id) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "ID inválido",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (isset($_SESSION['Cdatos_articuloPre'][$id])) {
            unset($_SESSION['Cdatos_articuloPre'][$id]);
        }

        echo json_encode([
            "Alerta" => "recargar",
            "Titulo" => "Artículo eliminado",
            "Texto" => "El artículo fue eliminado del presupuesto",
            "Tipo" => "success"
        ]);
        exit();
    }
}
