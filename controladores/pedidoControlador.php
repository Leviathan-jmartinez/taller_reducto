<?php
if ($peticionAjax) {
    require_once "../modelos/pedidoModelo.php";
} else {
    require_once "./modelos/pedidoModelo.php";
}

class pedidoControlador extends pedidoModelo
{
    /**controlador buscador proveedor */
    public function buscar_proveedor_controlador()
    {
        $proveedor  = mainModel::limpiar_string($_POST['buscar_proveedor']);

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
                                <button type="button" class="btn btn-primary" onclick="agregar_proveedor(' . $rows['idproveedores'] . ')"><i class="fas fa-user-plus"></i></button>
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
        $id  = mainModel::limpiar_string($_POST['id_agregar_proveedor']);

        $check_proveedor = mainModel::ejecutar_consulta_simple("select * from proveedores where idproveedores = '$id' ");
        if ($check_proveedor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido encontrar el proveedor en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $campos = $check_proveedor->fetch();
        }
        /**iniciar sesion para utilizar variables de sesion */
        session_start(['name' => 'STR']);
        unset($_SESSION['datos_proveedor']);
        if (empty($_SESSION['datos_proveedor'])) {
            $_SESSION['datos_proveedor'] = [
                "ID" => $campos['idproveedores'],
                "RUC" => $campos['ruc'],
                "RAZON" => $campos['razon_social'],
                "TELEFONO" => $campos['telefono']
            ];
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Proveedor Agregado!",
                "Texto" => "Proveedor agregado correctamente al pedido",
                "Tipo" => "success"
            ];
            echo json_encode($alerta);
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido agregar el proveedor al pedido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
        }
    }
    /**fin controlador */
    /**Controlador eliminar proveedor */
    public function eliminar_proveedor_controlador()
    {
        session_start(['name' => 'STR']);
        unset($_SESSION['datos_proveedor']);
        if (empty($_SESSION['datos_proveedor'])) {
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
    /**controlador buscar articulo */
    public function buscar_articulo_controlador()
    {
        // BUSCAR ARTÍCULO (HTML)
        session_start(['name' => 'STR']);
        if (isset($_POST['buscar_articulo'])) {
            $articulo = mainModel::limpiar_string($_POST['buscar_articulo']);
            if ($articulo == "") return '<div class="alert alert-warning">Debes introducir código o descripción</div>';

            $id_proveedor = $_SESSION['datos_proveedor']['ID'];
            if (!isset($_SESSION['datos_proveedor']['ID'])) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error!",
                    "Texto" => "No se ha seleccionado un proveedor",
                    "Tipo" => "error"
                ]);
                exit();
            }
            $datos_articulo = mainModel::ejecutar_consulta_simple("SELECT * FROM articulos WHERE (codigo like '%$articulo%' OR desc_articulo like '%$articulo%') AND estado=1 AND idproveedores='$id_proveedor' ORDER BY desc_articulo DESC");

            if ($datos_articulo->rowCount() >= 1) {
                $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>';
                foreach ($datos_articulo->fetchAll() as $rows) {
                    $tabla .= '<tr class="text-center">
                    <td>' . $rows['codigo'] . ' - ' . $rows['desc_articulo'] . '</td>
                    <td style="width:100px;"><input type="number" id="cantidad_' . $rows['id_articulo'] . '" class="form-control form-control-sm" value="1" min="1"></td>
                    <td><button type="button" class="btn btn-primary btn-sm" onclick="agregar_articulo(' . $rows['id_articulo'] . ')"><i class="fas fa-plus-circle"></i></button></td>
                </tr>';
                }
                $tabla .= '</tbody></table></div>';
                return $tabla;
            } else return '<div class="alert alert-warning">No se encontraron artículos que coincidan</div>';
        }
    }
    /**controlador buscador articulo */
    public function articulo_controlador()
    {
        session_start(['name' => 'STR']);
        ini_set('log_errors', 1);
        ini_set('error_log', '/path/to/php-error.log');
        // AGREGAR ARTÍCULO
        if (isset($_POST['id_agregar_articulo'])) {

            $id = mainModel::limpiar_string($_POST['id_agregar_articulo']);
            $cantidad = mainModel::limpiar_string($_POST['detalle_cantidad']);

            $check_articulo = mainModel::ejecutar_consulta_simple("SELECT * FROM articulos WHERE id_articulo='$id' AND estado=1");
            if ($check_articulo->rowCount() <= 0)
                die(json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "No se encontró el artículo", "Tipo" => "error"]));

            $campos = $check_articulo->fetch();
            if ($cantidad == "" || !is_numeric($cantidad) || intval($cantidad) <= 0)
                die(json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "Cantidad inválida", "Tipo" => "error"]));

            if (isset($_SESSION['datos_articulo'][$id])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" =>  "El articulo que intenta agregar ya se encuentra agregado",
                    "Tipo" => "error"
                ];
            } else {
                $_SESSION['datos_articulo'][$id] = [
                    "ID" => $campos['id_articulo'],
                    "codigo" => $campos['codigo'],
                    "descripcion" => $campos['desc_articulo'],
                    "cantidad" => $cantidad
                ];
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Articulo agregado!",
                    "Texto" =>  "El articulo ha sido agregado",
                    "Tipo" => "success"
                ];
            }
            echo json_encode($alerta);
            exit();
        }
    }

    /**controlador eliminar articulo */
    public function eliminar_articulo_controlador()
    {
        $id  = mainModel::limpiar_string($_POST['id_eliminar_articulo']);
        session_start(['name' => 'STR']);
        unset($_SESSION['datos_articulo'][$id]);
        if (empty($_SESSION['datos_articulo'][$id])) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Articulo removido!",
                "Texto" => "Los datos del articulo fueron removidos correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido remover los datos del articulo",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /**fin controlador */
    public function datos_pedido_controlador($tipo, $id)
    {
        $tipo  = mainModel::limpiar_string($tipo);

        $id  = mainModel::decryption($id);
        $id  = mainModel::limpiar_string($id);

        return pedidoModelo::datos_pedido_modelo($tipo, $id);
    }

    /**controlador agregar pedido */
    public function agregar_pedido_controlador()
    {
        session_start(['name' => 'STR']);

        if (empty($_SESSION['datos_articulo'])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error!",
                "Texto" => "No has seleccionado ningun artículo para el pedido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (empty($_SESSION['datos_proveedor'])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error!",
                "Texto" => "No has seleccionado ningun proveedor",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        /** Insertar cabecera */
        $datos_pedido_agg = [
            "usuario"   => $_SESSION['id_str'],
            "proveedor" => $_SESSION['datos_proveedor']['ID']
        ];

        $idPedidoCabecera = pedidoModelo::agregar_pedidoC_modelo($datos_pedido_agg);

        if ($idPedidoCabecera <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado!",
                "Texto" => "No pudimos registrar la cabecera del pedido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        /** Insertar detalles */
        $errores_detalles = 0;
        foreach ($_SESSION['datos_articulo'] as $article) {

            $detalle_reg = [
                "pedidoid" => $idPedidoCabecera,
                "articulo" => $article['ID'],
                "cantidad" => $article['cantidad']
            ];

            $detalleInsert = pedidoModelo::agregar_pedidoD_modelo($detalle_reg);

            if ($detalleInsert->rowCount() != 1) {
                $errores_detalles++;
            }
        }

        if ($errores_detalles > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error parcial",
                "Texto" => "El pedido se creó, pero algunos artículos no se guardaron",
                "Tipo" => "warning"
            ];
        } else {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Pedido guardado!",
                "Texto" => "El pedido se registró correctamente",
                "Tipo" => "success"
            ];
        }
        unset($_SESSION['datos_proveedor']);
        unset($_SESSION['datos_articulo']);
        echo json_encode($alerta);
    }

    /**Controlador paginar articulos */
    public function paginador_pedidos_controlador($pagina, $registros, $privilegio, $url, $busqueda)
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $privilegio = mainModel::limpiar_string($privilegio);
        $busqueda = mainModel::limpiar_string($busqueda);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        if (isset($busqueda) && $busqueda != "") {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS pc.idpedido_cabecera as idpedido_cabecera, pc.id_usuario as id_usuario, pc.fecha as fecha, pc.estado as estadoPe, 
            pc.id_proveedor as id_proveedor, p.razon_social as razon_social, p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, 
            p.estado as estadoPro, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick FROM pedido_cabecera pc
            INNER JOIN proveedores p on p.idproveedores = pc.id_proveedor
            INNER JOIN usuarios u on u.id_usuario = pc.id_usuario
            WHERE ((ruc LIKE '%$busqueda%' OR idpedido_cabecera LIKE '%$busqueda%')) 
            ORDER BY fecha ASC LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS pc.idpedido_cabecera as idpedido_cabecera, pc.id_usuario as id_usuario, pc.fecha as fecha, pc.estado as estadoPe, 
            pc.id_proveedor as id_proveedor, p.razon_social as razon_social, p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, 
            p.estado as estadoPro, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick FROM pedido_cabecera pc
            INNER JOIN proveedores p on p.idproveedores = pc.id_proveedor 
            INNER JOIN usuarios u on u.id_usuario = pc.id_usuario
            WHERE pc.estado != 0
            ORDER BY idpedido_cabecera ASC LIMIT $inicio,$registros";
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
								<th>CÓDIGO PEDIDO</th>
                                <th>PROVEEDOR</th>
                                <th>FECHA</th>
                                <th>USUARIO</th>
                                <th>ESTADO</th>';
        if ($privilegio == 1 || $privilegio == 2) {
            $tabla .=           '<th>ELIMINAR</th>';
        }
        $tabla .= '
						</tr>
						</thead>
						<tbody>';
        if ($total >= 1 && $pagina <= $Npaginas) {
            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;
            foreach ($datos as $rows) {
                switch ($rows['estadoPe']) {
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
								<td>' . $rows['idpedido_cabecera'] . '</td>
								<td>' . $rows['razon_social'] . '</td>
								<td>' . date("d-m-Y", strtotime($rows['fecha'])) . '</td>
                                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                                <td>' . $estadoBadge . '</td>';
                if ($privilegio == 1 || $privilegio == 2) {
                    $tabla .= '<td>
									<form class="FormularioAjax" action="' . SERVERURL . 'ajax/pedidoAjax.php" method="POST" data-form="delete" autocomplete="off" action="">
                                    <input type="hidden" name="pedido_id_del" value=' . mainModel::encryption($rows['idpedido_cabecera']) . '>
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
    /**fin controlador */

    /**Controlador anular pedido */
    public function anular_pedido_controlador()
    {
        $id = mainModel::decryption($_POST['pedido_id_del']);
        $id = mainModel::limpiar_string($id);

        $check_pedido = mainModel::ejecutar_consulta_simple("SELECT idpedido_cabecera FROM pedido_cabecera WHERE idpedido_cabecera = '$id'");
        if ($check_pedido->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El pedido que intenta anular no existe en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $check_pedidoestado = mainModel::ejecutar_consulta_simple("SELECT idpedido_cabecera FROM pedido_cabecera WHERE idpedido_cabecera = '$id' AND estado = 2");
        if ($check_pedidoestado->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El pedido que intenta anular se encuentra procesado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        session_start(['name' => 'STR']);
        if ($_SESSION['nivel_str'] > 2) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No tiene los permisos necesario para realizar esta operación",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $anularPedido = pedidoModelo::anular_pedido_modelo($id);
        if ($anularPedido->rowCount() == 1) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Pedido Anulado!",
                "Texto" => "El PEDIDO ha sido anulado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No se pudo anular el PEDIDO seleccionado",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
}
