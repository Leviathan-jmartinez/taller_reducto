<?php
if ($peticionAjax) {
    require_once "../modelos/recepcionservicioModelo.php";
} else {
    require_once "./modelos/recepcionservicioModelo.php";
}

class recepcionservicioControlador extends recepcionservicioModelo
{
    public function listar_modelos_controlador()
    {
        return recepcionservicioModelo::listar_modelos_modelo();
    }

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

    public function buscar_cliente_autocomplete_controlador()
    {
        $busqueda = mainModel::limpiar_string($_POST['termino'] ?? '');

        if (strlen($busqueda) < 4) {
            return json_encode([]);
        }

        return json_encode(
            recepcionservicioModelo::buscar_cliente_autocomplete_modelo($busqueda),
            JSON_UNESCAPED_UNICODE
        );
    }

    public function buscar_vehiculo_autocomplete_controlador()
    {
        $busqueda = mainModel::limpiar_string($_POST['termino'] ?? '');
        $idCliente = intval($_POST['id_cliente'] ?? 0);

        if ($idCliente <= 0 || strlen($busqueda) < 4) {
            return json_encode([]);
        }

        return json_encode(
            recepcionservicioModelo::buscar_vehiculo_autocomplete_modelo($busqueda, $idCliente),
            JSON_UNESCAPED_UNICODE
        );
    }

    public function guardar_cliente_rapido_controlador()
    {
        session_start(['name' => 'STR']);

        if (!mainModel::tienePermiso('cliente.crear')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso no autorizado",
                "Texto"  => "No posee permiso para registrar clientes",
                "Tipo"   => "error"
            ]);
        }

        $doc = mainModel::limpiar_string($_POST['cliente_doc_reg'] ?? '');
        $nombre = mainModel::limpiar_string($_POST['cliente_nombre_reg'] ?? '');
        $apellido = mainModel::limpiar_string($_POST['cliente_apellido_reg'] ?? '');
        $telefono = mainModel::limpiar_string($_POST['cliente_telefono_reg'] ?? '');
        $email = mainModel::limpiar_string($_POST['cliente_email_reg'] ?? '');
        $direccion = mainModel::limpiar_string($_POST['cliente_direccion_reg'] ?? 'No informado');
        $ciudad = (int) ($_POST['ciudad_reg'] ?? 0);
        $tipoDocumento = mainModel::limpiar_string($_POST['tipo_documento_reg'] ?? 'CI');
        $dv = mainModel::limpiar_string($_POST['cliente_dv_reg'] ?? '');

        if ($doc === '' || $nombre === '') {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Datos incompletos",
                "Texto"  => "Complete documento y nombre",
                "Tipo"   => "warning"
            ]);
        }

        if (recepcionservicioModelo::existe_cliente_documento_modelo($doc)) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Cliente existente",
                "Texto"  => "El documento ya se encuentra registrado",
                "Tipo"   => "warning"
            ]);
        }

        $datos = [
            "doc_number" => $doc,
            "nombre_cliente" => $nombre,
            "apellido_cliente" => $apellido,
            "celular_cliente" => $telefono,
            "email_cliente" => $email,
            "direccion_cliente" => $direccion,
            "id_ciudad" => $ciudad,
            "doc_type" => $tipoDocumento,
            "digito_v" => $dv,
            "estado_civil" => "",
            "estado_cliente" => 1
        ];

        $guardar = recepcionservicioModelo::guardar_cliente_rapido_modelo($datos);

        if (!empty($guardar['success'])) {
            $cliente = trim($nombre . ' ' . $apellido);

            return json_encode([
                "Alerta" => "seleccionar_cliente",
                "Titulo" => "Cliente registrado",
                "Texto"  => "El cliente fue agregado y seleccionado",
                "Tipo"   => "success",
                "cliente" => [
                    "id_cliente" => $guardar['id_cliente'],
                    "nombre" => $cliente,
                    "doc" => $doc
                ]
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto"  => $guardar['msg'] ?? 'No se pudo registrar el cliente',
            "Tipo"   => "error"
        ]);
    }

    public function guardar_vehiculo_rapido_controlador()
    {
        session_start(['name' => 'STR']);

        if (!mainModel::tienePermiso('vehiculo.crear')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso no autorizado",
                "Texto"  => "No posee permiso para registrar vehiculos",
                "Tipo"   => "error"
            ]);
        }

        $cliente = (int) ($_POST['cliente_reg'] ?? 0);
        $modelo = (int) ($_POST['modelo_reg'] ?? 0);
        $color = mainModel::limpiar_string($_POST['color_reg'] ?? '');
        $placa = mainModel::limpiar_string($_POST['placa_reg'] ?? '');
        $anho = trim($_POST['anho_reg'] ?? '') !== ''  ? mainModel::limpiar_string($_POST['anho_reg'])  : null;
        $version = mainModel::limpiar_string($_POST['version_reg'] ?? '');
        $tipoVehiculo = mainModel::limpiar_string($_POST['tipo_vehiculo_reg'] ?? '');

        if ($cliente <= 0 || $modelo <= 0 || $color === '' || $placa === '') {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Datos incompletos",
                "Texto"  => "Complete cliente, modelo, color y placa",
                "Tipo"   => "warning"
            ]);
        }

        if (recepcionservicioModelo::existe_vehiculo_placa_modelo($placa)) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Vehiculo existente",
                "Texto"  => "La placa ya se encuentra registrada",
                "Tipo"   => "warning"
            ]);
        }

        $datos = [
            "id_cliente" => $cliente,
            "id_modeloauto" => $modelo,
            "color" => $color,
            "placa" => $placa,
            "anho" => $anho,
            "version" => $version,
            "tipo_vehiculo" => $tipoVehiculo,
            "estado" => 1
        ];

        $guardar = recepcionservicioModelo::guardar_vehiculo_rapido_modelo($datos);

        if (!empty($guardar['success'])) {
            return json_encode([
                "Alerta" => "seleccionar_vehiculo",
                "Titulo" => "Vehiculo registrado",
                "Texto"  => "El vehiculo fue agregado y seleccionado",
                "Tipo"   => "success",
                "vehiculo" => [
                    "id_vehiculo" => $guardar['id_vehiculo'],
                    "descripcion" => $guardar['descripcion']
                ]
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto"  => $guardar['msg'] ?? 'No se pudo registrar el vehiculo',
            "Tipo"   => "error"
        ]);
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

    public function listar_recepcion_controlador($pagina, $registros, $url, $busqueda = '', $fecha_inicio = '', $fecha_final = '', $nro_recepcion = '', $cliente = '', $documento = '', $placa = '', $estado_recepcion = '', $origen = '', $usuario = '', $tipo_servicio = '', $prioridad = '', $orden = 'fecha', $direccion = 'DESC')
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
        $fecha_inicio = mainModel::limpiar_string($fecha_inicio);
        $fecha_final = mainModel::limpiar_string($fecha_final);
        $nro_recepcion = mainModel::limpiar_string($nro_recepcion);
        $cliente = mainModel::limpiar_string($cliente);
        $documento = mainModel::limpiar_string($documento);
        $placa = mainModel::limpiar_string($placa);
        $estado_recepcion = mainModel::limpiar_string($estado_recepcion);
        $origen = mainModel::limpiar_string($origen);
        $usuario = mainModel::limpiar_string($usuario);
        $tipo_servicio = mainModel::limpiar_string($tipo_servicio);
        $prioridad = mainModel::limpiar_string($prioridad);
        $orden = mainModel::limpiar_string($orden);
        $direccion = strtoupper(mainModel::limpiar_string($direccion));

        /* ================= FILTROS ================= */

        $filtros = [
            [
                "campo" => "rs.id_sucursal",
                "tipo"  => "=",
                "valor" => $_SESSION['nick_sucursal']
            ]
        ];

        $filtros[] = [
            "campo" => "rs.fecha_ingreso",
            "tipo"  => "DATE_RANGE",
            "desde" => $fecha_inicio,
            "hasta" => $fecha_final
        ];

        if ($nro_recepcion !== '') {
            $filtros[] = [
                "campo" => "rs.idrecepcion",
                "tipo"  => "=",
                "valor" => $nro_recepcion
            ];
        }

        if ($busqueda != "") {
            $filtros[] = [
                "campo" => "CONCAT(rs.idrecepcion, ' ', c.nombre_cliente,' ',c.apellido_cliente, ' ', c.doc_number, ' ', v.placa, ' ', ma.mar_descri, ' ', m.mod_descri, ' ', u.usu_nombre, ' ', u.usu_apellido, ' ', IFNULL(rs.tipo_servicio,''), ' ', IFNULL(rs.prioridad,''), ' ', IFNULL(rs.observacion,''))",
                "tipo"  => "LIKE",
                "valor" => $busqueda
            ];
        }
        if ($cliente !== '') {
            $filtros[] = [
                "campo" => "CONCAT(c.nombre_cliente,' ',c.apellido_cliente)",
                "tipo"  => "LIKE",
                "valor" => $cliente
            ];
        }
        if ($documento !== '') {
            $filtros[] = [
                "campo" => "c.doc_number",
                "tipo"  => "LIKE",
                "valor" => $documento
            ];
        }
        if ($placa !== '') {
            $filtros[] = [
                "campo" => "v.placa",
                "tipo"  => "LIKE",
                "valor" => $placa
            ];
        }
        if ($estado_recepcion !== '') {
            $filtros[] = [
                "campo" => "rs.estado",
                "tipo"  => "=",
                "valor" => $estado_recepcion
            ];
        }
        if ($origen !== '') {
            $filtros[] = [
                "campo" => "rs.origen",
                "tipo"  => "=",
                "valor" => $origen
            ];
        }
        if ($usuario !== '') {
            $filtros[] = [
                "campo" => "CONCAT(u.usu_nombre, ' ', u.usu_apellido)",
                "tipo"  => "LIKE",
                "valor" => $usuario
            ];
        }
        if ($tipo_servicio !== '') {
            $filtros[] = [
                "campo" => "rs.tipo_servicio",
                "tipo"  => "=",
                "valor" => $tipo_servicio
            ];
        }
        if ($prioridad !== '') {
            $filtros[] = [
                "campo" => "rs.prioridad",
                "tipo"  => "=",
                "valor" => $prioridad
            ];
        }

        $filtrosSQL = mainModel::construirFiltros($filtros);
        $columnasOrdenSql = [
            'fecha' => 'rs.fecha_ingreso',
            'estado' => 'rs.estado'
        ];
        $ordenamiento = mainModel::preparar_ordenamiento($orden, $direccion, $columnasOrdenSql, 'fecha', 'DESC');
        $orden = $ordenamiento['orden'];
        $direccion = $ordenamiento['direccion'];

        /* ================= MODELO ================= */

        $res = recepcionservicioModelo::listar_recepcion_modelo($inicio, $registros, $filtrosSQL, "ORDER BY " . $ordenamiento['sql'] . ", rs.idrecepcion DESC");

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
                    <th>' . mainModel::link_orden_tabla($url, 'fecha', 'Fecha', $orden, $direccion, 'recepcion_orden', 'recepcion_direccion') . '</th>
                    <th>Cliente</th>
                    <th>CI/RUC</th>
                    <th>Vehículo</th>
                    <th>KM</th>
                    <th>Servicio</th>
                    <th>Origen</th>
                    <th>Prioridad</th>
                    <th>Fotos</th>
                    <th>Usuario</th>
                    <th>' . mainModel::link_orden_tabla($url, 'estado', 'Estado', $orden, $direccion, 'recepcion_orden', 'recepcion_direccion') . '</th>';

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
                    default:
                        $estado = '<span class="badge badge-secondary">Anulado</span>';
                        break;
                }

                $origenBadge = ($rows['origen'] == 'RECLAMO')
                    ? '<span class="badge badge-danger">Reclamo</span>'
                    : '<span class="badge badge-light">Normal</span>';

                $prioridadBadge = '-';
                if (!empty($rows['prioridad'])) {
                    switch (strtolower($rows['prioridad'])) {
                        case 'urgente':
                            $prioridadBadge = '<span class="badge badge-danger">Urgente</span>';
                            break;
                        case 'normal':
                            $prioridadBadge = '<span class="badge badge-info">Normal</span>';
                            break;
                        default:
                            $prioridadBadge = htmlspecialchars($rows['prioridad'], ENT_QUOTES, 'UTF-8');
                    }
                }

                $fotosBtn = '<span class="text-muted">-</span>';
                if ((int) $rows['total_fotos'] > 0) {
                    $fotosBtn = '
                    <button type="button"
                        class="btn btn-info btn-sm"
                        onclick="verFotosRecepcion(\'' . mainModel::encryption($rows['idrecepcion']) . '\')">
                        <i class="far fa-images"></i> ' . (int) $rows['total_fotos'] . '
                    </button>';
                }

                $tabla .= '
            <tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . date("d/m/Y H:i", strtotime($rows['fecha_ingreso'])) . '</td>
                <td>' . htmlspecialchars($rows['cliente'], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($rows['doc_number'], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($rows['vehiculo'], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($rows['kilometraje'], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($rows['tipo_servicio'] ?: '-', ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . $origenBadge . '</td>
                <td>' . $prioridadBadge . '</td>
                <td>' . $fotosBtn . '</td>
                <td>' . htmlspecialchars($rows['usuario'], ENT_QUOTES, 'UTF-8') . '</td>
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
            $colspan = $puedeAnular ? 13 : 12;
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

    public function fotos_recepcion_controlador()
    {
        if (!mainModel::tienePermiso('servicio.recepcion.ver')) {
            return json_encode([
                "success" => false,
                "msg" => "Acceso no autorizado"
            ]);
        }

        if (empty($_POST['recepcion_id_fotos'])) {
            return json_encode([
                "success" => false,
                "msg" => "Recepcion no valida"
            ]);
        }

        $id = mainModel::decryption($_POST['recepcion_id_fotos']);
        $id = (int) mainModel::limpiar_string($id);

        if ($id <= 0) {
            return json_encode([
                "success" => false,
                "msg" => "Recepcion no valida"
            ]);
        }

        $fotos = recepcionservicioModelo::fotos_recepcion_modelo($id, (int) $_SESSION['nick_sucursal']);

        return json_encode([
            "success" => true,
            "fotos" => $fotos
        ], JSON_UNESCAPED_UNICODE);
    }

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
