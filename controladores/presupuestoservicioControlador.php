<?php
if ($peticionAjax) {
    require_once "../modelos/presupuestoservicioModelo.php";
} else {
    require_once "./modelos/presupuestoservicioModelo.php";
}

class presupuestoservicioControlador extends presupuestoservicioModelo
{
    public function datos_recepcion_controlador($id_encriptado)
    {
        $id = mainModel::decryption($id_encriptado);

        if ($id <= 0) {
            return false;
        }

        return presupuestoservicioModelo::datos_recepcion_modelo($id);
    }

    public function buscar_recepciones_controlador()
    {
        session_start(['name' => 'STR']);
        $txt = trim($_POST['buscar_recepcion']);

        return presupuestoservicioModelo::buscar_recepciones_modelo(
            $txt,
            $_SESSION['id_sucursal']
        );
    }


    public function buscar_servicios_controlador()
    {
        $txt = trim($_POST['buscar_servicio'] ?? '');

        if ($txt === '') {
            return '';
        }

        return presupuestoServicioModelo::buscar_servicios_modelo($txt);
    }

    public function promo_articulo_controlador()
    {
        $id = intval($_POST['promo_articulo']);

        return presupuestoServicioModelo::promo_articulo_modelo($id);
    }

    public function descuentos_cliente_controlador()
    {
        $idCliente = intval($_POST['descuentos_cliente']);

        return presupuestoServicioModelo::descuentos_cliente_modelo($idCliente);
    }

    public function guardar_presupuesto_controlador()
    {
        session_start(['name' => 'STR']);

        $datos = [
            'usuario'         => $_SESSION['id_str'],
            'idrecepcion'     => $_POST['idrecepcion'],
            'fecha_venc'      => $_POST['fecha_venc'],

            // 👇 estos vienen de los inputs hidden correctos
            'subtotal'        => $_POST['subtotal_servicios'],
            'total_descuento' => $_POST['total_descuento'],
            'total_final'     => $_POST['total_final'],

            'detalle'         => json_decode($_POST['detalle_json'], true),
            'descuentos'      => json_decode($_POST['descuentos_json'], true)
        ];
        $sucursalRecepcion = mainModel::ejecutar_consulta_simple("
            SELECT id_sucursal
            FROM recepcion_servicio
            WHERE idrecepcion = '{$datos['idrecepcion']}'
            ")->fetchColumn();

        if ($sucursalRecepcion != $_SESSION['id_sucursal']) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto'  => 'La recepción no pertenece a su sucursal',
                'Tipo'   => 'error'
            ]);
        }
        $res = presupuestoServicioModelo::guardar_presupuesto_modelo($datos);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'limpiar',
                'Titulo' => 'Presupuesto registrado',
                'Texto'  => 'El presupuesto se guardó correctamente',
                'Tipo'   => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto'  => $res['msg'] ?? 'No se pudo guardar',
            'Tipo'   => 'error'
        ]);
    }


    /**Controlador paginar presupuestos */
    public function paginador_presupuestoservi_controlador($pagina, $registros, $privilegio, $url, $busqueda1, $busqueda2)
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
            $consulta = "SELECT SQL_CALC_FOUND_ROWS ps.idpresupuesto_servicio AS idpresupuesto_servicio, ps.id_usuario   AS id_usuario, ps.fecha AS fecha, 
            ps.estado   AS estadoPre, ps.fecha_venc   AS fecha_venc, ps.subtotal AS subtotal, ps.total_descuento AS total_descuento, ps.total_final  AS total_final, 
            ps.idrecepcion  AS idrecepcion, c.id_cliente AS id_cliente, c.nombre_cliente AS nombre_cliente, c.apellido_cliente AS apellido_cliente, v.placa AS placa, 
            ma.mod_descri   AS modelo, u.usu_nombre AS usu_nombre, u.usu_apellido  AS usu_apellido, u.usu_estado AS usu_estado, u.usu_nick  AS usu_nick 
        FROM presupuesto_servicio ps 
        LEFT JOIN recepcion_servicio r ON r.idrecepcion = ps.idrecepcion 
        LEFT JOIN clientes c ON c.id_cliente = r.id_cliente 
        LEFT JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo 
        LEFT JOIN modelo_auto ma   ON ma.id_modeloauto = v.id_modeloauto 
        INNER JOIN usuarios u ON u.id_usuario = ps.id_usuario 
        WHERE DATE(ps.fecha) >= '$busqueda1'   AND DATE(ps.fecha) <= '$busqueda2' 
        ORDER BY ps.fecha ASC LIMIT $inicio, $registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS ps.idpresupuesto_servicio AS idpresupuesto_servicio, ps.id_usuario   AS id_usuario, ps.fecha AS fecha, 
            ps.estado   AS estadoPre, ps.fecha_venc   AS fecha_venc, ps.subtotal AS subtotal, ps.total_descuento AS total_descuento, ps.total_final  AS total_final, 
            ps.idrecepcion  AS idrecepcion, c.id_cliente AS id_cliente, c.nombre_cliente AS nombre_cliente, c.apellido_cliente AS apellido_cliente, v.placa AS placa, 
            ma.mod_descri   AS modelo, u.usu_nombre AS usu_nombre, u.usu_apellido  AS usu_apellido, u.usu_estado AS usu_estado, u.usu_nick  AS usu_nick 
        FROM presupuesto_servicio ps
        LEFT JOIN recepcion_servicio r ON r.idrecepcion = ps.idrecepcion 
        LEFT JOIN clientes c ON c.id_cliente = r.id_cliente 
        LEFT JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo 
        LEFT JOIN modelo_auto ma   ON ma.id_modeloauto = v.id_modeloauto 
        INNER JOIN usuarios u ON u.id_usuario = ps.id_usuario
        WHERE ps.estado != 0
            ORDER BY ps.idpresupuesto_servicio ASC LIMIT $inicio,$registros";
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
								<th>CLIENTE</th>
                                <th>PROVEEDOR</th>
                                <th>FECHA</th>
                                <th>TOTAL</th>
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
                        $estadoBadge = '<span class="badge badge-warning">Pendiente</span>';
                        break;
                    case 2:
                        $estadoBadge = '<span class="badge badge-success">Aprobado</span>';
                        break;
                    case 3:
                        $estadoBadge = '<span class="badge badge-primary">OT generada</span>';
                        break;
                    case 4:
                        $estadoBadge = '<span class="badge badge-info">Facturado</span>';
                        break;
                    case 0:
                        $estadoBadge = '<span class="badge badge-danger">Anulado</span>';
                        break;
                    default:
                        $estadoBadge = '<span class="badge bg-secondary">Desconocido</span>';
                }
                $tabla .= '
                            <tr class="text-center">
								<td>' . $contador . '</td>
								<td>' . $rows['nombre_cliente'] . ' ' . $rows['apellido_cliente'] . '</td>
								<td>' . $rows['modelo'] . ' ' . $rows['placa'] . '</td>
								<td>' . date("d-m-Y", strtotime($rows['fecha'])) . '</td>
								<td>' .  number_format($rows['total_final'], 0, ',', '.') . '</td>
                                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                                <td>' . $estadoBadge . '</td>';
                if ($privilegio == 1 || $privilegio == 2) {
                    if ($rows['estadoPre'] == 1 || $rows['estadoPre'] == 2) {
                        $tabla .= '<td>
                                <div style="display:flex; gap:6px; justify-content:center;">

                                    <form class="FormularioAjax"
                                        action="' . SERVERURL . 'ajax/presupuestoAjax.php"
                                        method="POST"
                                        data-form="delete"
                                        autocomplete="off">
                                        <input type="hidden" name="accion" value="anular">
                                        <input type="hidden"
                                            name="presupuesto_id_del"
                                            value=' . mainModel::encryption($rows['idpresupuesto_servicio']) . '>

                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </form>';
                        if ($rows['estadoPre'] == 2) {
                            $tabla .= '
                                    <form class="FormularioAjax d-inline"
                                    action="' . SERVERURL . 'ajax/ordenTrabajoAjax.php"
                                    method="POST"
                                    data-form="save">

                                    <input type="hidden" name="accion" value="generar_ot">
                                    <input type="hidden" name="id"
                                    value=' . mainModel::encryption($rows['idpresupuesto_servicio']) . '>

                                    <button class="btn btn-primary btn-sm">
                                        <i class="fas fa-tools"></i>
                                    </button>
                                    </form>
                                    ';
                        }
                    }
                    if ($rows['estadoPre'] == 1) {
                        $tabla .= '  
                                    <form class="FormularioAjax d-inline"
                                        action="' . SERVERURL . 'ajax/presupuestoServicioAjax.php"
                                        method="POST"
                                        data-form="update">

                                        <input type="hidden" name="accion" value="aprobar">
                                        <input type="hidden" name="id"
                                            value=' . mainModel::encryption($rows['idpresupuesto_servicio']) . '>

                                        <button type="submit"
                                                class="btn btn-success btn-sm"
                                                title="Aprobar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>';
                    }
                    $tabla .= ' </div>
                            </td>
                            ';
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

    public function listar_presupuestos_controlador()
    {
        return presupuestoServicioModelo::listar_presupuestos_modelo();
    }

    /* ================= APROBAR ================= */
    public function aprobar_presupuesto_controlador()
    {
        session_start(['name' => 'STR']);

        if (!isset($_POST['id'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto' => 'ID inválido',
                'Tipo' => 'error'
            ]);
        }

        $id = mainModel::decryption($_POST['id']);

        $res = presupuestoServicioModelo::aprobar_presupuesto_modelo($id);

        if ($res) {
            return json_encode([
                'Alerta' => 'recargar',
                'Titulo' => 'Presupuesto aprobado',
                'Texto' => 'El presupuesto fue aprobado correctamente',
                'Tipo' => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto' => 'No se pudo aprobar',
            'Tipo' => 'error'
        ]);
    }
    public function anular_presupuesto_controlador()
    {
        session_start(['name' => 'STR']);

        if (!isset($_POST['id'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto' => 'ID inválido',
                'Tipo' => 'error'
            ]);
        }

        $id = mainModel::decryption($_POST['id']);

        $res = presupuestoServicioModelo::anular_estado_recepcion_modelo($id);

        if ($res) {
            return json_encode([
                'Alerta' => 'recargar',
                'Titulo' => 'Presupuesto anulado',
                'Texto' => 'El presupuesto fue anulado correctamente',
                'Tipo' => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto' => 'No se pudo anular',
            'Tipo' => 'error'
        ]);
    }
}
