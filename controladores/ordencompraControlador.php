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
        WHERE idpresupuesto_compra = :id
    ");
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

        $idOC = ordenCompraModelo::agregar_ocC_modelo($datos_oc_cab);

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
        WHERE idpresupuesto_compra = :id
    ");
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
}
