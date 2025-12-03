<?php
if ($peticionAjax) {
    require_once "../modelos/ordencompraModelo";
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
        $busqueda1 = mainModel::limpiar_string($busqueda);
        
        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        if (!empty($busqueda)) {
            $consulta = "SELECT  SQL_CALC_FOUND_ROWS pc.idpresupuesto_compra as idpresupuesto_compra, pc.id_usuario as id_usuario, pc.fecha as fecha, pc.estado as estadoPre, 
            pc.idproveedores as idproveedores, p.razon_social as razon_social, p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, 
            p.estado as estadoPro, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick, pc.updated as updated,
            pc.updatedby as updatedby
            FROM presupuesto_compra pc
            INNER JOIN proveedores p on p.idproveedores = pc.idproveedores
            INNER JOIN usuarios u on u.id_usuario = pc.id_usuario
            WHERE (pc.idpresupuesto_compra LIKE '%$busqueda%' OR p.razon_social LIKE '%$busqueda%' OR p.ruc LIKE '%$busqueda%') AND pc.estado = 1
            ORDER BY fecha ASC LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT  SQL_CALC_FOUND_ROWS pc.idpresupuesto_compra as idpresupuesto_compra, pc.id_usuario as id_usuario, pc.fecha as fecha, pc.estado as estadoPre, 
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

        $tabla .= '<div class="table-responsive">
					<table class="table table-dark table-sm">
						<thead>
							<tr class="text-center roboto-medium">
								<th>#</th>
								<th>CÓDIGO PRESUPUESTO</th>
                                <th>PROVEEDOR</th>
                                <th>FECHA</th>
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
                                <td>' . $estadoBadge . '</td>';
                if ($privilegio == 1 || $privilegio == 2) {
                    $tabla .= '<td>
									<form class="FormularioAjax" action="' . SERVERURL . 'ajax/presupuestoAjax.php" method="POST" data-form="delete" autocomplete="off" action="">
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
}
