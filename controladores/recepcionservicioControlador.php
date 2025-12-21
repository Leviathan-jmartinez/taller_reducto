<?php
if ($peticionAjax) {
    require_once "../modelos/recepcionservicioModelo.php";
} else {
    require_once "./modelos/recepcionservicioModelo.php";
}

class recepcionservicioControlador extends recepcionservicioModelo
{

    public function buscar_cliente_controlador()
    {
        $busqueda = $_POST['buscar_cliente'] ?? '';

        if ($busqueda === '') {
            return '<div class="alert alert-warning text-center">
                        Ingrese un criterio de búsqueda
                    </div>';
        }

        return recepcionservicioModelo::buscar_cliente_modelo($busqueda);
    }

    public function buscar_vehiculo_controlador()
    {

        $busqueda  = $_POST['buscar_vehiculo'] ?? '';
        $idCliente = intval($_POST['id_cliente'] ?? 0);

        if ($idCliente <= 0) {
            return '<div class="alert alert-warning text-center">
                        Cliente no válido
                    </div>';
        }

        return recepcionservicioModelo::buscar_vehiculo_modelo($busqueda, $idCliente);
    }

    public function guardar_recepcion_controlador()
    {
        /* ================= VALIDACIONES ================= */
        session_start(['name' => 'STR']);
        if (
            empty($_POST['id_cliente']) ||
            empty($_POST['id_vehiculo']) ||
            empty($_POST['fecha_ingreso']) ||
            empty($_POST['kilometraje']) ||
            empty($_POST['observacion'])
        ) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Datos incompletos",
                "Texto"  => "Debe completar todos los campos obligatorios",
                "Tipo"   => "warning"
            ]);
        }

        $datos = [
            "id_usuario"   => $_SESSION['id_str'],
            "id_cliente"   => intval($_POST['id_cliente']),
            "id_vehiculo"  => intval($_POST['id_vehiculo']),
            "fecha_ingreso" => $_POST['fecha_ingreso'],
            "kilometraje"  => intval($_POST['kilometraje']),
            "observacion"  => trim($_POST['observacion']),
            "estado"       => 1
        ];

        /* ================= MODELO ================= */

        $guardar = recepcionServicioModelo::guardar_recepcion_modelo($datos);

        if ($guardar === true) {
            return json_encode([
                "Alerta" => "limpiar",
                "Titulo" => "Recepción registrada",
                "Texto"  => "La recepción fue guardada correctamente",
                "Tipo"   => "success"
            ]);
        }

        if (is_array($guardar) && isset($guardar['msg'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error SQL",
                "Texto"  => $guardar['msg'],
                "Tipo"   => "error"
            ]);
        }
    }

    /**Controlador paginar clientes */
    public function paginador_recepcion_servicio_controlador($pagina, $registros, $privilegio, $url, $busqueda)
    {
        $pagina     = mainModel::limpiar_string($pagina);
        $registros  = mainModel::limpiar_string($registros);
        $privilegio = mainModel::limpiar_string($privilegio);
        $busqueda   = mainModel::limpiar_string($busqueda);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        /* ================= CONSULTA ================= */

        if ($busqueda != "") {
            $consulta = "
            SELECT SQL_CALC_FOUND_ROWS
                rs.idrecepcion,
                rs.fecha_ingreso,
                rs.kilometraje,
                rs.estado,

                c.doc_number,
                CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,

                v.placa,
                v.anho,

                CONCAT(u.usu_nombre,' ',u.usu_apellido) AS usuario
            FROM recepcion_servicio rs
            INNER JOIN clientes c   ON c.id_cliente = rs.id_cliente
            INNER JOIN vehiculos v  ON v.id_vehiculo = rs.id_vehiculo
            INNER JOIN usuarios u   ON u.id_usuario = rs.id_usuario
            WHERE (
                   c.nombre_cliente LIKE '%$busqueda%'
                OR c.apellido_cliente LIKE '%$busqueda%'
                OR c.doc_number LIKE '%$busqueda%'
                OR v.placa LIKE '%$busqueda%'
            )
            ORDER BY rs.fecha_ingreso DESC
            LIMIT $inicio,$registros
        ";
        } else {
            $consulta = "
            SELECT SQL_CALC_FOUND_ROWS
                rs.idrecepcion,
                rs.fecha_ingreso,
                rs.kilometraje,
                rs.estado,

                c.doc_number,
                CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,

                v.placa,
                v.anho,

                CONCAT(u.usu_nombre,' ',u.usu_apellido) AS usuario
            FROM recepcion_servicio rs
            INNER JOIN clientes c   ON c.id_cliente = rs.id_cliente
            INNER JOIN vehiculos v  ON v.id_vehiculo = rs.id_vehiculo
            INNER JOIN usuarios u   ON u.id_usuario = rs.id_usuario
            ORDER BY rs.fecha_ingreso DESC
            LIMIT $inicio,$registros
        ";
        }

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta)->fetchAll();

        $total = (int)$conexion->query("SELECT FOUND_ROWS()")->fetchColumn();
        $Npaginas = ceil($total / $registros);

        /* ================= TABLA ================= */

        $tabla .= '
    <div class="table-responsive">
        <table class="table table-dark table-sm">
            <thead>
                <tr class="text-center roboto-medium">
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>CI/RUC</th>
                    <th>Vehículo</th>
                    <th>KM</th>
                    <th>Usuario</th>
                    <th>Estado</th>';

        if ($privilegio == 1 || $privilegio == 2) {
            $tabla .= '<th>ANULAR</th>';
        }

        $tabla .= '</tr>
            </thead>
            <tbody>';

        /* ================= REGISTROS ================= */

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador   = $inicio + 1;
            $reg_inicio = $inicio + 1;

            foreach ($datos as $rows) {

                /* Estado legible */
                switch ($rows['estado']) {
                    case 1:
                        $estado = '<span class="badge badge-info">Recepcionado</span>';
                        break;
                    case 2:
                        $estado = '<span class="badge badge-warning">En proceso</span>';
                        break;
                    case 3:
                        $estado = '<span class="badge badge-success">Finalizado</span>';
                        break;
                    default:
                        $estado = '<span class="badge badge-secondary">Anulado</span>';
                        break;
                }

                $tabla .= '
            <tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . date("d/m/Y H:i", strtotime($rows['fecha_ingreso'])) . '</td>
                <td>' . $rows['cliente'] . '</td>
                <td>' . $rows['doc_number'] . '</td>
                <td>' . $rows['placa'] . ' (' . $rows['anho'] . ')</td>
                <td>' . $rows['kilometraje'] . '</td>
                <td>' . $rows['usuario'] . '</td>
                <td>' . $estado . '</td>';

                if ($privilegio == 1 || $privilegio == 2) {
                    $tabla .= '
                
                <td>
                    <form class="FormularioAjax" action="' . SERVERURL . 'ajax/recepcionservicioAjax.php" method="POST" data-form="delete" autocomplete="off" action="">
                        <input type="hidden" name="recepcion_id_del" value=' . mainModel::encryption($rows['idrecepcion']) . '>
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
                $tabla .= '
            <tr class="text-center">
                <td colspan="9">
                    <a href="' . $url . '" class="btn btn-primary btn-sm">
                        Haga click aquí para recargar el listado
                    </a>
                </td>
            </tr>';
            } else {
                $tabla .= '
            <tr class="text-center">
                <td colspan="9">No hay registros en el sistema</td>
            </tr>';
            }
        }

        $tabla .= '
            </tbody>
        </table>
    </div>';

        /* ================= PAGINADOR ================= */

        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '<p class="text-right">
            Mostrando registros ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '
        </p>';

            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        echo $tabla;
    }

    /**fin controlador */

    public function anular_recepcion_controlador()
    {
        if (empty($_POST['recepcion_id_del'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "ID de recepción no válido",
                "Tipo"   => "error"
            ]);
        }

        $id = mainModel::decryption($_POST['recepcion_id_del']);
        $id = mainModel::limpiar_string($id);

        $anular = recepcionServicioModelo::anular_recepcion_modelo($id);

        if ($anular === true) {
            return json_encode([
                "Alerta" => "recargar",
                "Titulo" => "Recepción anulada",
                "Texto"  => "La recepción fue anulada correctamente",
                "Tipo"   => "success"
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto"  => "No se pudo anular la recepción",
            "Tipo"   => "error"
        ]);
    }
}
