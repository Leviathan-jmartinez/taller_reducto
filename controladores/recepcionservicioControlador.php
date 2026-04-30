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
        if ($_POST['origen'] == 'RECLAMO' && empty($_POST['idreclamo_servicio'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Debe seleccionar un reclamo",
                "Tipo"   => "error"
            ]);
        }

        $accesorios = "";
        if (isset($_POST['accesorios'])) {
            $accesorios = implode(",", $_POST['accesorios']);
        }

        $datos = [
            "id_usuario"   => $_SESSION['id_str'],
            "id_cliente"   => intval($_POST['id_cliente']),
            "id_sucursal"  => intval($_SESSION['nick_sucursal']),
            "id_vehiculo"  => intval($_POST['id_vehiculo']),
            "kilometraje"  => intval($_POST['kilometraje']),

            "nivel_combustible" => $_POST['nivel_combustible'] ?? null,
            "estado_exterior"   => $_POST['estado_exterior'] ?? null,
            "objetos_vehiculo"  => $_POST['objetos_vehiculo'] ?? null,

            "tipo_servicio" => $_POST['tipo_servicio'] ?? null,
            "area_problema" => $_POST['area_problema'] ?? null,
            "prioridad"     => $_POST['prioridad'] ?? null,

            "accesorios"   => $accesorios,

            "origen" => $_POST['origen'] ?? 'NORMAL',
            "idreclamo_servicio" => !empty($_POST['idreclamo_servicio'])
                ? intval($_POST['idreclamo_servicio'])
                : null,

            "observacion"  => trim($_POST['observacion']),
            "estado"       => 1
        ];

        /* ================= MODELO ================= */

        $guardar = recepcionServicioModelo::guardar_recepcion_modelo($datos);

        if (is_array($guardar) && isset($guardar['success'])) {

            $id_recepcion = $guardar['id_recepcion'];

            /* ================= GUARDAR FOTOS ================= */
            if (!empty($_FILES['fotos_vehiculo']['name'][0])) {

                $total = count($_FILES['fotos_vehiculo']['name']);

                for ($i = 0; $i < $total; $i++) {

                    $nombre = time() . '_' . $i . '_' . $_FILES['fotos_vehiculo']['name'][$i];
                    $ruta = "uploads/recepciones/" . $nombre;

                    move_uploaded_file(
                        $_FILES['fotos_vehiculo']['tmp_name'][$i],
                        "../" . $ruta
                    );

                    $pdo = mainModel::conectar();

                    $sqlFoto = $pdo->prepare("
                INSERT INTO recepcion_fotos
                (id_recepcion,ruta_foto)
                VALUES
                (:recepcion,:ruta)
            ");

                    $sqlFoto->bindParam(":recepcion", $id_recepcion);
                    $sqlFoto->bindParam(":ruta", $ruta);
                    $sqlFoto->execute();
                }
            }

            return json_encode([
                "Alerta" => "limpiar",
                "Titulo" => "Recepción registrada",
                "Texto"  => "La recepción fue guardada correctamente",
                "Tipo"   => "success"
            ]);
        }

        /* 🔥 ESTE FALTABA */
        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto"  => $guardar['msg'] ?? 'No se pudo guardar la recepción',
            "Tipo"   => "error"
        ]);
    }

    public function listar_recepcion_controlador($pagina, $registros, $url, $busqueda)
    {
        $pagina    = (int) mainModel::limpiar_string($pagina);
        $registros = (int) mainModel::limpiar_string($registros);
        $url       = SERVERURL . mainModel::limpiar_string($url) . "/";

        $registros = ($registros > 0) ? $registros : 15;
        $pagina = ($pagina > 0) ? $pagina : 1;
        $inicio = ($pagina - 1) * $registros;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        $busqueda = mainModel::limpiar_string($busqueda);

        /* ================= FILTROS ================= */

        $filtros = [
            [
                "campo" => "rs.id_sucursal",
                "tipo"  => "=",
                "valor" => $_SESSION['nick_sucursal']
            ]
        ];

        if ($busqueda != "") {
            $filtros[] = [
                "campo" => "CONCAT(c.nombre_cliente,' ',c.apellido_cliente, ' ', c.doc_number, ' ', v.placa)",
                "tipo"  => "LIKE",
                "valor" => $busqueda
            ];
        }
        if (!empty($_SESSION['estado_recepcion'])) {
            $filtros[] = [
                "campo" => "rs.estado",
                "tipo"  => "=",
                "valor" => $_SESSION['estado_recepcion']
            ];
        }

        $filtrosSQL = mainModel::construirFiltros($filtros);

        /* ================= MODELO ================= */

        $res = recepcionservicioModelo::listar_recepcion_modelo($inicio, $registros, $filtrosSQL);

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        $tabla = '';

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

        $puedeAnular = mainModel::tienePermiso('servicio.recepcion.anular');

        if ($puedeAnular) {
            $tabla .=           '<th>ANULAR</th>';
        }

        $tabla .= '</tr>
            </thead>
            <tbody>';

        /* ================= REGISTROS ================= */

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador = $reg_inicio;

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
                        if ($rows['origen'] == 'RECLAMO') {
                            $estado .= '<br><span class="badge badge-danger">Reclamo</span>';
                        }
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

                if ($puedeAnular) {
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
            $colspan = $puedeAnular ? 9 : 8;
            if ($total >= 1) {
                $tabla .= '
            <tr class="text-center">
                <td colspan="' . $colspan . '">
                    <a href="' . $url . '" class="btn btn-primary btn-sm">
                        Haga click aquí para recargar el listado
                    </a>
                </td>
            </tr>';
            } else {
                $tabla .= '
            <tr class="text-center">
                <td colspan="' . $colspan . '">No hay registros en el sistema</td>
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
        session_start(['name' => 'STR']);
        $id = mainModel::decryption($_POST['recepcion_id_del']);
        $id = mainModel::limpiar_string($id);

        $anular = recepcionServicioModelo::anular_recepcion_modelo($id, $_SESSION['nick_sucursal']);

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
