<?php
if ($peticionAjax) {
    require_once "../modelos/ordencompraModelo.php";
} else {
    require_once "./modelos/ordencompraModelo.php";
}

class ordencompraControlador extends ordencompraModelo
{
    /**Controlador paginar articulos */
    public function paginador_presupuestos_controlador($pagina, $registros, $privilegio, $url, $busqueda)
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

        if (!empty($busqueda)) {
            $consulta = "SELECT  SQL_CALC_FOUND_ROWS pc.idpresupuesto_compra as idpresupuesto_compra, pc.id_usuario as id_usuario, pc.fecha as fecha, fecha_venc as fecha_venc,pc.estado as estadoPre, 
            pc.idproveedores as idproveedores, p.razon_social as razon_social, p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, 
            p.estado as estadoPro, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick, pc.updated as updated,
            pc.updatedby as updatedby
            FROM presupuesto_compra pc
            INNER JOIN proveedores p on p.idproveedores = pc.idproveedores
            INNER JOIN usuarios u on u.id_usuario = pc.id_usuario
            WHERE (pc.idpresupuesto_compra LIKE '%$busqueda%' OR p.razon_social LIKE '%$busqueda%' OR p.ruc LIKE '%$busqueda%') AND pc.estado = 1
            ORDER BY fecha desc LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT  SQL_CALC_FOUND_ROWS pc.idpresupuesto_compra as idpresupuesto_compra, pc.id_usuario as id_usuario, fecha_venc as fecha_venc,pc.fecha as fecha, pc.estado as estadoPre, 
            pc.idproveedores as idproveedores, p.razon_social as razon_social, p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, 
            p.estado as estadoPro, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick, pc.updated as updated,
            pc.updatedby as updatedby
            FROM presupuesto_compra pc
            INNER JOIN proveedores p on p.idproveedores = pc.idproveedores
            INNER JOIN usuarios u on u.id_usuario = pc.id_usuario
            WHERE pc.estado != 0
            ORDER BY pc.idpresupuesto_compra ASC LIMIT $inicio,$registros";
        }
        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta);
        $datos = $datos->fetchAll();

        $total = $conexion->query("SELECT FOUND_ROWS()");
        $total = (int) $total->fetchColumn();

        $Npaginas = ceil($total / $registros);

        $tabla .= '<div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover text-center align-middle oc-table">
                                <thead>
                                    <tr class="text-center roboto-medium">
                                        <th>#</th>
                                        <th>Nro Presupuesto</th>
                                        <th>Proveedor</th>
                                        <th>Fecha Creacion</th>
                                        <th>Vencimiento</th>
                                        <th>Estado</th>
                                        <th>Acción</th>
                                    </tr>
						        </thead>
						    <tbody>';
        if ($total >= 1 && $pagina <= $Npaginas) {
            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;
            foreach ($datos as $rows) {
                switch ($rows['estadoPre']) {
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
								<td>' . $rows['idpresupuesto_compra'] . '</td>
								<td>' . $rows['razon_social'] . '</td>
								<td>' . date("d-m-Y", strtotime($rows['fecha'])) . '</td>
								<td>' . date("d-m-Y", strtotime($rows['fecha_venc'])) . '</td>
                                <td>' . $estadoBadge . '</td>
                                <td>
                                <button class="btn btn-primary btn-sm generar-oc-btn" 
                                    data-id="' . $rows['idpresupuesto_compra'] . '">Generar OC</button>
                                </td>';
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
    /**Controlador agregar OC */
    public function generar_oc_controlador()
    {
        session_start(['name' => 'STR']);


        // -----------------------------
        // 2) Obtener y sanear POST
        // -----------------------------
        $idpresupuesto = $_POST['idpresupuesto'] ?? null;
        $cantidades = $_POST['cantidades'] ?? [];
        $fecha_entrega = $_POST['fecha_entrega'] ?? null;

        if (is_array($idpresupuesto)) {
            $idpresupuesto = $idpresupuesto[0] ?? null;
        }

        $idpresupuesto = trim($idpresupuesto);

        // -----------------------------
        // 3) Validaciones iniciales
        // -----------------------------
        if (!$idpresupuesto || $idpresupuesto === "undefined" || !is_numeric($idpresupuesto)) {
            echo "error:no_id_valido";
        }

        if (empty($cantidades)) {
            echo "error:no_cantidades";
        }

        $conexion = mainModel::conectar();

        // -----------------------------
        // 4) Obtener cabecera del presupuesto
        // -----------------------------
        $consultaPre = $conexion->prepare("
        SELECT idpresupuesto_compra,idproveedores, id_usuario
        FROM presupuesto_compra
        WHERE idpresupuesto_compra = :id");
        $consultaPre->execute([":id" => $idpresupuesto]);
        $pre = $consultaPre->fetch(PDO::FETCH_ASSOC);

        if (!$pre) {
            echo "error:presupuesto_no_existe";
        }


        // -----------------------------
        // 5) Crear cabecera de OC
        // -----------------------------
        $datos_oc_cab = [
            "proveedor" => $pre['idproveedores'],
            "presupuestoid" => $pre['idpresupuesto_compra'],
            "usuario"   => $_SESSION['id_str'],
            "fecha_entrega"   => $fecha_entrega
        ];

        $idOC = ordenCompraModelo::agregar_ocC_modelo1($datos_oc_cab);

        if ($idOC <= 0) {
            echo "error:oc_cabecera";
            exit();
        }


        // -----------------------------
        // 6) Obtener detalle del presupuesto
        // -----------------------------
        $consultaDet = $conexion->prepare("
        SELECT id_articulo, precio
        FROM presupuesto_detalle
        WHERE idpresupuesto_compra = :id");
        $consultaDet->execute([":id" => $idpresupuesto]);
        $detallePre = $consultaDet->fetchAll(PDO::FETCH_ASSOC);
        if (empty($detallePre)) {
            echo "error:detalle_vacio";
            exit();
        }
        // -----------------------------
        // 7) Insertar detalle de OC
        // -----------------------------
        $errores = 0;

        foreach ($detallePre as $item) {
            $idArt = $item["id_articulo"];

            if (!isset($cantidades[$idArt]) || $cantidades[$idArt] <= 0) {
                continue; // No insertar si no hay cantidad
            }

            $datos_det = [
                "ocid"     => $idOC,
                "articulo" => $idArt,
                "cantidad" => $cantidades[$idArt],
                "precio"   => $item["precio"],
                "pendiente" => $cantidades[$idArt]
            ];

            if (!isset($item["precio"])) {
                var_dump("ERROR: precio no existe en item", $item);
                exit();
            }

            $insert = ordenCompraModelo::agregar_ocD_modelo($datos_det);

            if ($insert->rowCount() != 1) {
                $errores++;
            }
        }

        // -----------------------------
        // 8) Respuesta final al AJAX
        // -----------------------------
        if ($errores > 0) {
            echo "warning:" . $idOC;
        } else {
            echo "ok:" . $idOC;
        }
    }
    /**fin controlador */

    /**controlador agregar orden de compra */
    public function agregar_oc_controlador()
    {
        session_start(['name' => 'STR']);
        $fecha_entrega = $_POST['fecha_entrega'] ?? null;

        if ($_SESSION['tipo_ordencompra'] == "sin_presupuesto") {
            if (empty($_SESSION['Sdatos_proveedorOC'])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error!",
                    "Texto" => "No has seleccionado ningun proveedor",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
            if (empty($_SESSION['Sdatos_articuloOC'])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error!",
                    "Texto" => "No has seleccionado ningun artículo para la orden de compra",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if (empty($fecha_entrega) || $fecha_entrega == null) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error!",
                    "Texto" => "Debes seleccionar la fecha de entrega",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }


            /** Insertar cabecera */
            $datos_OC_agg = [
                "usuario"   => $_SESSION['id_str'],
                "proveedor" => $_SESSION['Sdatos_proveedorOC']['ID'],
                "fecha_entrega" => $fecha_entrega
            ];

            $idocCab = ordencompraModelo::agregar_ocC_modelo2($datos_OC_agg);

            if ($idocCab <= 0) {
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
            foreach ($_SESSION['Sdatos_articuloOC'] as $article) {

                $detalle_reg = [
                    "ocid" => $idocCab,
                    "articulo" => $article['ID'],
                    "cantidad" => $article['cantidad'],
                    "precio" => $article['precio'],
                    "pendiente" => $article['cantidad']
                ];

                $detalleInsert = ordencompraModelo::agregar_ocD_modelo($detalle_reg);

                if ($detalleInsert->rowCount() != 1) {
                    $errores_detalles++;
                }
            }

            if ($errores_detalles > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error parcial",
                    "Texto" => "La OC se creó, pero algunos artículos no se guardaron",
                    "Tipo" => "warning"
                ];
            } else {
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Pedido guardado!",
                    "Texto" => "La Orden compra se genero correctamente",
                    "Tipo" => "success"
                ];
                $_SESSION['tipo_ordencompra'] = "con_presupuesto";
                unset($_SESSION['Sdatos_proveedorOC'], $_SESSION['Sdatos_articuloOC']);
            }
            echo json_encode($alerta);
        }
    }

    /**Controlador paginar ordencompra */
    public function paginador_ordencompra_controlador($pagina, $registros, $privilegio, $url, $busqueda1, $busqueda2)
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
            $consulta = "SELECT SQL_CALC_FOUND_ROWS oc.idorden_compra as idorden_compra, oc.idproveedores as idproveedores, oc.id_usuario as id_usuario, oc.fecha as fecha, oc.estado as estodoOC, 
            oc.fecha_entrega as fecha_entrega, oc.presupuestoid as presupuestoid, oc.updated as updated, oc.updatedby as updatedby, p.idproveedores as idproveedores, p.id_ciudad as id_ciudad, p.razon_social as razon_social, 
            p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, p.estado as estadoPro, 
            u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick
            from orden_compra oc 
            INNER JOIN proveedores p on p.idproveedores = oc.idproveedores 
            INNER JOIN usuarios u on u.id_usuario = oc.id_usuario
            WHERE date(fecha) >= '$busqueda1' AND date(fecha) <='$busqueda2'
            ORDER BY idorden_compra desc LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS oc.idorden_compra as idorden_compra, oc.idproveedores as idproveedores, oc.id_usuario as id_usuario, oc.fecha as fecha, oc.estado as estodoOC, 
            oc.fecha_entrega as fecha_entrega, oc.presupuestoid as presupuestoid, oc.updated as updated, oc.updatedby as updatedby, p.idproveedores as idproveedores, p.id_ciudad as id_ciudad, p.razon_social as razon_social, 
            p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, p.estado as estadoPro, 
            u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick
            from orden_compra oc 
            INNER JOIN proveedores p on p.idproveedores = oc.idproveedores 
            INNER JOIN usuarios u on u.id_usuario = oc.id_usuario
            WHERE oc.estado != 0
            ORDER BY idorden_compra desc LIMIT $inicio,$registros";
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
								<th>CÓDIGO OC</th>
                                <th>PROVEEDOR</th>
                                <th>FECHA CREACION</th>
                                <th>FECHA ENTREGA</th>
                                <th>CREADO POR</th>
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
                switch ($rows['estodoOC']) {
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
								<td>' . $rows['idorden_compra'] . '</td>
								<td>' . $rows['razon_social'] . '</td>
								<td>' . date("d-m-Y", strtotime($rows['fecha'])) . '</td>
								<td>' . date("d-m-Y", strtotime($rows['fecha_entrega'])) . '</td>
                                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                                <td>' . $estadoBadge . '</td>';
                if ($privilegio == 1 || $privilegio == 2) {
                    $tabla .= '<td>
									<form class="FormularioAjax" action="' . SERVERURL . 'ajax/ordencompraAjax.php" method="POST" data-form="delete" autocomplete="off" action="">
                                    <input type="hidden" name="ordencompra_id_del" value=' . mainModel::encryption($rows['idorden_compra']) . '>
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


    /**Controlador anular ordencompra */
    public function anular_ordencompra_controlador()
    {
        $id = mainModel::decryption($_POST['ordencompra_id_del']);
        $id = mainModel::limpiar_string($id);

        $check_presupuesto = mainModel::ejecutar_consulta_simple("SELECT idorden_compra FROM orden_compra WHERE idorden_compra = '$id'");
        if ($check_presupuesto->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La ORDEN DE COMPRA que intenta anular no existe en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $check_presupuestoestado = mainModel::ejecutar_consulta_simple("SELECT idorden_compra FROM orden_compra WHERE idorden_compra = '$id' AND estado = 2");
        if ($check_presupuestoestado->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La ORDEN DE COMPRA que intenta anular se encuentra procesado",
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
        $datos_oc_del = [
            "updatedby" => $_SESSION['id_str'],
            "idorden_compra" => $id
        ];

        if (ordencompraModelo::anular_ordencompra_modelo($datos_oc_del)) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Pedido Anulado!",
                "Texto" => "La ORDEN DE COMPRA ha sido anulada correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No se pudo anular la ORDEN DE COMPRA seleccionada, por favor intente nuevamente",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /**fin controlador */

    /**controlador buscador proveedor */
    public function buscar_proveedor_controlador()
    {
        $proveedor  = mainModel::limpiar_string($_POST['buscar_proveedorOC']);

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
                                <button type="button" class="btn btn-primary" onclick="agregar_proveedorOC(' . $rows['idproveedores'] . ')"><i class="fas fa-user-plus"></i></button>
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
        $id  = mainModel::limpiar_string($_POST['id_agregar_proveedorOC']);

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
        unset($_SESSION['Sdatos_proveedorOC']);
        if (!isset($_SESSION['Sdatos_proveedorOC'])) {
            $_SESSION['Sdatos_proveedorOC'] = [
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

    /**controlador buscar articulo */
    public function buscar_articulo_controlador()
    {
        // BUSCAR ARTÍCULO (HTML)
        session_start(['name' => 'STR']);
        if (isset($_POST['buscar_articuloOC'])) {
            $articulo = mainModel::limpiar_string($_POST['buscar_articuloOC']);
            if ($articulo == "") return '<div class="alert alert-warning">Debes introducir código o descripción</div>';

            if (!isset($_SESSION['Sdatos_proveedorOC']['ID'])) {
                return '<div class="alert alert-danger">No se ha seleccionado un proveedor</div>';
                exit();
            }
            $id_proveedor = $_SESSION['Sdatos_proveedorOC']['ID'];
            $datos_articuloPre = mainModel::ejecutar_consulta_simple("SELECT * FROM articulos WHERE (codigo like '%$articulo%' OR desc_articulo like '%$articulo%') AND estado=1 AND idproveedores='$id_proveedor' ORDER BY desc_articulo DESC");

            if ($datos_articuloPre->rowCount() >= 1) {
                $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>';
                foreach ($datos_articuloPre->fetchAll() as $rows) {
                    $tabla .= '<tr class="text-center">
                    <td>' . $rows['codigo'] . ' - ' . $rows['desc_articulo'] . '</td>
                    
                    <!-- Cantidad -->
                    <td style="width:100px;">
                        <input type="number" id="cantidad_' . $rows['id_articulo'] . '" class="form-control form-control-sm" value="1" min="1">
                    </td>

                    <!-- Precio -->
                    <td style="width:100px;">
                        <input type="number" id="precio_' . $rows['id_articulo'] . '" class="form-control form-control-sm" step="0.01" min="0">
                    </td>

                    <!-- Botón agregar -->
                    <td>
                        <button type="button" class="btn btn-primary btn-sm" onclick="agregar_articuloOC(' . $rows['id_articulo'] . ')">
                            <i class="fas fa-plus-circle"></i>
                        </button>
                    </td>
                </tr>';
                }
                $tabla .= '</tbody></table></div>';
                return $tabla;
            } else return '<div class="alert alert-warning">No se encontraron artículos que coincidan</div>';
        }
    }
    /**controlador buscador articulo */

    /**controlador buscador articulo */
    public function articulo_controlador()
    {
        session_start(['name' => 'STR']);
        // AGREGAR ARTÍCULO
        if (isset($_POST['id_agregar_articuloOC'])) {

            $id = mainModel::limpiar_string($_POST['id_agregar_articuloOC']);
            $cantidad = mainModel::limpiar_string($_POST['detalle_cantidad']);
            $precio = mainModel::limpiar_string($_POST['detalle_precio']); // <-- nuevo

            // Validaciones
            $check_articulo = mainModel::ejecutar_consulta_simple("SELECT * FROM articulos WHERE id_articulo='$id' AND estado=1");
            if ($check_articulo->rowCount() <= 0)
                die(json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "No se encontró el artículo", "Tipo" => "error"]));

            $campos = $check_articulo->fetch();

            if ($cantidad == "" || !is_numeric($cantidad) || intval($cantidad) <= 0)
                die(json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "Cantidad inválida", "Tipo" => "error"]));

            if ($precio == "" || !is_numeric($precio) || floatval($precio) < 0)
                die(json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "Precio inválido", "Tipo" => "error"]));

            $cantidad = intval($cantidad);
            $precio = floatval($precio);
            $subtotal = $cantidad * $precio; // <-- opcional, para mostrar o guardar

            if (isset($_SESSION['Sdatos_articuloOC'][$id])) {
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El artículo que intenta agregar ya se encuentra agregado",
                    "Tipo" => "error"
                ];
            } else {
                $_SESSION['Sdatos_articuloOC'][$id] = [
                    "ID" => $campos['id_articulo'],
                    "codigo" => $campos['codigo'],
                    "descripcion" => $campos['desc_articulo'],
                    "cantidad" => $cantidad,
                    "precio" => $precio,
                    "subtotal" => $subtotal
                ];
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Artículo agregado!",
                    "Texto" => "El artículo ha sido agregado",
                    "Tipo" => "success"
                ];
            }
            echo json_encode($alerta);
            exit();
        }
    }
    /**fin controlador */
}
