<?php
if ($peticionAjax) {
    require_once "../modelos/diagnosticoModelo.php";
} else {
    require_once "./modelos/diagnosticoModelo.php";
}

class diagnosticoControlador extends diagnosticoModelo
{

    public function buscar_recepcion_controlador()
    {
        $busqueda = $_POST['buscar_recepcion'] ?? '';

        if ($busqueda === '') {
            return '<div class="alert alert-warning text-center">
                Ingrese un criterio de búsqueda
            </div>';
        }

        $datos = diagnosticoModelo::buscar_recepcion_modelo($busqueda);

        if (!$datos) {
            return '<div class="alert alert-warning text-center">
                No se encontraron recepciones
            </div>';
        }

        $tabla = '<table class="table table-bordered table-hover table-sm">
        <thead class="thead-light">
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>KM</th>
                <th>Acción</th>
            </tr>
        </thead><tbody>';

        foreach ($datos as $r) {

            $desc = $r['cliente'] . ' - ' . $r['placa'];

            $tabla .= '
        <tr>
            <td>' . date("d/m/Y H:i", strtotime($r['fecha_ingreso'])) . '</td>
            <td>' . $r['cliente'] . '</td>
            <td>' . $r['placa'] . ' (' . $r['anho'] . ')</td>
            <td>' . $r['kilometraje'] . '</td>
            <td class="text-center">
                <button class="btn btn-success btn-sm"
                    onclick="seleccionarRecepcion(
                        ' . $r['idrecepcion'] . ',
                        \'' . addslashes($desc) . '\',
                        ' . $r['id_sucursal'] . ',
                        \'' . $r['origen'] . '\',
                        ' . ($r['idreclamo_servicio'] ?? 'null') . '
                    )">
                    Seleccionar
                </button>
            </td>
        </tr>';
        }

        $tabla .= '</tbody></table>';

        return $tabla;
    }

    public function buscar_recepcion_inline_controlador()
    {
        $busqueda = trim($_POST['buscar_recepcion'] ?? '');

        if (strlen($busqueda) < 3) {
            return '<div class="diagnostico-autocomplete-empty">
                Escriba al menos 3 caracteres
            </div>';
        }

        $datos = diagnosticoModelo::buscar_recepcion_modelo($busqueda);

        if (!$datos) {
            return '<div class="diagnostico-autocomplete-empty">
                No se encontraron recepciones
            </div>';
        }

        $html = '';

        foreach ($datos as $r) {
            $cliente = trim($r['cliente']);
            $vehiculo = trim(($r['marca'] ?? '') . ' ' . ($r['modelo'] ?? '') . ' ' . ($r['placa'] ?? ''));
            $fecha = date("d/m/Y H:i", strtotime($r['fecha_ingreso']));
            $servicio = $r['tipo_servicio'] ?: 'sin servicio';
            $prioridad = $r['prioridad'] ?: 'normal';
            $desc = $cliente . ' - ' . $r['placa'];
            $idReclamo = $r['idreclamo_servicio'] !== null ? (int) $r['idreclamo_servicio'] : 'null';

            $html .= '
            <button type="button"
                class="diagnostico-autocomplete-item"
                data-id="' . (int) $r['idrecepcion'] . '"
                data-desc="' . htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') . '"
                data-sucursal="' . (int) $r['id_sucursal'] . '"
                data-origen="' . htmlspecialchars($r['origen'] ?: 'NORMAL', ENT_QUOTES, 'UTF-8') . '"
                data-reclamo="' . htmlspecialchars((string) $idReclamo, ENT_QUOTES, 'UTF-8') . '">
                <span class="diagnostico-autocomplete-main">
                    <span class="diagnostico-autocomplete-title">' . htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8') . '</span>
                    <span class="diagnostico-autocomplete-meta">
                        ' . htmlspecialchars($vehiculo, ENT_QUOTES, 'UTF-8') . ' | Doc: ' . htmlspecialchars($r['doc_number'] ?: '-', ENT_QUOTES, 'UTF-8') . ' | KM: ' . htmlspecialchars($r['kilometraje'] ?: '-', ENT_QUOTES, 'UTF-8') . '
                    </span>
                    <span class="diagnostico-autocomplete-meta">
                        Ingreso: ' . htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8') . ' | Solicitado: ' . htmlspecialchars($servicio, ENT_QUOTES, 'UTF-8') . '
                    </span>
                </span>
                <span class="diagnostico-autocomplete-badge">' . htmlspecialchars($prioridad, ENT_QUOTES, 'UTF-8') . '</span>
            </button>';
        }

        return $html;
    }

    public function obtener_recepcion_detalle_controlador()
    {
        session_start(['name' => 'STR']);

        if (empty($_POST['idrecepcion']) || empty($_SESSION['nick_sucursal'])) {
            return json_encode([]);
        }

        $id = (int) mainModel::limpiar_string($_POST['idrecepcion']);
        $sucursal = (int) $_SESSION['nick_sucursal'];
        $data = diagnosticoModelo::obtener_recepcion_detalle_modelo($id, $sucursal);

        return json_encode($data ?: []);
    }

    public function obtener_diagnostico_detalle_controlador()
    {
        session_start(['name' => 'STR']);

        if (empty($_POST['id_diagnostico']) || empty($_SESSION['nick_sucursal'])) {
            return json_encode([]);
        }

        $id = (int) mainModel::limpiar_string($_POST['id_diagnostico']);
        $sucursal = (int) $_SESSION['nick_sucursal'];
        $data = diagnosticoModelo::obtener_diagnostico_detalle_modelo($id, $sucursal);

        return json_encode($data ?: []);
    }

    public function listar_equipos_controlador()
    {
        session_start(['name' => 'STR']);

        $sql = mainModel::conectar()->prepare("
        SELECT id_equipo, nombre
        FROM equipo_trabajo
        WHERE estado = 1
        AND id_sucursal = :sucursal
        ORDER BY nombre
        ");

        $sql->bindParam(':sucursal', $_SESSION['nick_sucursal'], PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar_articulos_controlador()
    {
        session_start(['name' => 'STR']);

        $texto = trim($_POST['buscar'] ?? '');
        $tipoUso = ($_POST['tipo_uso'] ?? '') === 'repuesto' ? 'repuesto' : 'servicio';
        $index = (int)($_POST['index'] ?? 0);
        $campo = ($_POST['campo'] ?? '') === 'repuesto' ? 'repuesto' : 'servicio';

        if (strlen($texto) < 2) {
            return '';
        }

        $tipoArticulo = $tipoUso === 'servicio' ? 'servicio' : 'producto';
        $datos = diagnosticoModelo::buscar_articulos_modelo($texto, $tipoArticulo);

        if (!$datos) {
            return '<div class="diagnostico-autocomplete-empty">Sin resultados</div>';
        }

        $html = '<div class="diagnostico-articulo-resultados">';
        foreach ($datos as $a) {
            $html .= '
            <button type="button" class="diagnostico-articulo-item"
                data-id="' . (int)$a['id_articulo'] . '"
                data-codigo="' . htmlspecialchars($a['codigo'], ENT_QUOTES, 'UTF-8') . '"
                data-desc="' . htmlspecialchars($a['desc_articulo'], ENT_QUOTES, 'UTF-8') . '"
                data-tipo="' . htmlspecialchars($tipoUso, ENT_QUOTES, 'UTF-8') . '"
                data-index="' . $index . '"
                data-campo="' . htmlspecialchars($campo, ENT_QUOTES, 'UTF-8') . '">
                <span>' . htmlspecialchars($a['desc_articulo'], ENT_QUOTES, 'UTF-8') . '</span>
                <small>' . htmlspecialchars($a['codigo'], ENT_QUOTES, 'UTF-8') . '</small>
            </button>';
        }

        return $html . '</div>';
    }

    public function guardar_diagnostico_controlador()
    {
        session_start(['name' => 'STR']);

        if (mainModel::tienePermiso('servicio.diagnostico.crear') === false) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Permiso denegado",
                "Texto"  => "No tienes permiso para registrar diagnósticos",
                "Tipo"   => "error"
            ]);
        }
        if (empty($_POST['idrecepcion'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Datos incompletos",
                "Texto"  => "Debe seleccionar una recepcion",
                "Tipo"   => "warning"
            ]);
        }

        if (empty($_POST['id_equipo'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe seleccionar un equipo de trabajo",
                "Tipo" => "warning"
            ]);
        }

        if (empty($_SESSION['nick_sucursal'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo obtener la sucursal del usuario",
                "Tipo" => "error"
            ]);
        }

        $idRecepcion = (int) mainModel::limpiar_string($_POST['idrecepcion']);
        $idSucursal = (int) $_SESSION['nick_sucursal'];
        $recepcionSeleccionada = diagnosticoModelo::obtener_recepcion_detalle_modelo($idRecepcion, $idSucursal);

        if (!$recepcionSeleccionada) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Recepción inválida",
                "Texto" => "La recepción seleccionada no existe o no pertenece a la sucursal del usuario",
                "Tipo" => "error"
            ]);
        }

        if (empty($_POST['detalles'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe agregar al menos un detalle al diagnóstico",
                "Tipo" => "warning"
            ]);
        }
        /* ================= DETALLES ================= */

        $datos = [
            "idrecepcion" => $idRecepcion,
            "id_usuario"  => $_SESSION['id_str'],
            "id_equipo"   => $_POST['id_equipo'],
            "id_sucursal" => $idSucursal,
            "observacion" => $_POST['observacion'],
            "estado"      => 1, // En proceso
            "es_garantia" => $_POST['es_garantia'] ?? 0,
            "es_reclamo_valido" => $_POST['es_reclamo_valido'] ?? 1,
            "requiere_cobro" => $_POST['requiere_cobro'] ?? 0,
            "detalles"    => $_POST['detalles'] ?? []
        ];

        if ($datos['id_usuario'] == 0) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Sesión inválida",
                "Texto"  => "Usuario no identificado",
                "Tipo"   => "error"
            ]);
        }

        $guardar = diagnosticoModelo::guardar_diagnostico_modelo($datos);

        if (isset($guardar['success'])) {
            $postAccion = [];

            if (
                (int)$datos['es_reclamo_valido'] === 1 &&
                (int)$datos['es_garantia'] === 1 &&
                (int)$datos['requiere_cobro'] === 0
            ) {
                $recepcion = $recepcionSeleccionada;

                if (
                    $recepcion &&
                    strtoupper($recepcion['origen'] ?? '') === 'RECLAMO' &&
                    !empty($recepcion['idreclamo_servicio'])
                ) {
                    $qGarantia = mainModel::conectar()->prepare("
                        SELECT
                            rc.requiere_garantia,
                            DATE_ADD(rs.fecha_servicio, INTERVAL 3 MONTH) AS fecha_vencimiento,
                            COALESCE(r_actual.kilometraje, 0) AS km_reclamo,
                            COALESCE(r_normal.kilometraje, r_reclamo_origen.kilometraje) AS km_origen
                        FROM reclamo_servicio rc
                        INNER JOIN registro_servicio rs
                            ON rs.idregistro_servicio = rc.idregistro_servicio
                        INNER JOIN orden_trabajo ot_origen
                            ON ot_origen.idorden_trabajo = rs.idorden_trabajo
                        LEFT JOIN presupuesto_servicio ps
                            ON ps.idpresupuesto_servicio = ot_origen.idpresupuesto_servicio
                        LEFT JOIN diagnostico_servicio ds
                            ON ds.id_diagnostico = ps.id_diagnostico
                        LEFT JOIN recepcion_servicio r_normal
                            ON r_normal.idrecepcion = ds.idrecepcion
                        LEFT JOIN recepcion_servicio r_reclamo_origen
                            ON r_reclamo_origen.idreclamo_servicio = ot_origen.idreclamo_servicio
                        LEFT JOIN recepcion_servicio r_actual
                            ON r_actual.idreclamo_servicio = rc.idreclamo_servicio
                        WHERE rc.idreclamo_servicio = ?
                          AND rc.id_sucursal = ?
                        LIMIT 1
                    ");
                    $qGarantia->execute([
                        (int)$recepcion['idreclamo_servicio'],
                        (int)$datos['id_sucursal']
                    ]);

                    $garantia = $qGarantia->fetch(PDO::FETCH_ASSOC);
                    $dentroGarantia = $garantia &&
                        (int)$garantia['requiere_garantia'] === 1 &&
                        date('Y-m-d') <= $garantia['fecha_vencimiento'] &&
                        (
                            $garantia['km_origen'] === null ||
                            $garantia['km_origen'] === '' ||
                            (int)$garantia['km_reclamo'] <= ((int)$garantia['km_origen'] + 2000)
                        );

                    if ($dentroGarantia) {
                        $postAccion = [
                            "PostAccion" => "generar_ot_reclamo",
                            "idreclamo_servicio" => (int)$recepcion['idreclamo_servicio']
                        ];
                    }
                }
            }

            return json_encode(array_merge([
                "Alerta" => "limpiar",
                "Titulo" => "Diagnóstico registrado",
                "Texto"  => "Se guardó correctamente",
                "Tipo"   => "success",
                "id_diagnostico" => $guardar['id_diagnostico']
            ], $postAccion));
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto"  => $guardar['msg'] ?? "Error desconocido",
            "Tipo"   => "error"
        ]);
    }

    public function paginador_diagnostico_controlador($pagina, $registros, $url, $busqueda1, $busqueda2, $cliente = '', $placa = '', $nro_diagnostico = '', $nro_recepcion = '', $estado_filtro = '', $origen = '', $busqueda_general = '', $orden = 'fecha', $direccion = 'DESC')
    {
        $pagina    = (int) mainModel::limpiar_string($pagina);
        $registros = (int) mainModel::limpiar_string($registros);
        $cliente   = mainModel::limpiar_string($cliente);
        $placa     = mainModel::limpiar_string($placa);
        $nro_diagnostico = mainModel::limpiar_string($nro_diagnostico);
        $nro_recepcion = mainModel::limpiar_string($nro_recepcion);
        $estado_filtro = mainModel::limpiar_string($estado_filtro);
        $origen = mainModel::limpiar_string($origen);
        $busqueda_general = mainModel::limpiar_string($busqueda_general);
        $orden = mainModel::limpiar_string($orden);
        $direccion = strtoupper(mainModel::limpiar_string($direccion));

        $url = SERVERURL . $url . "/";
        $tabla = "";

        $pagina = ($pagina > 0) ? $pagina : 1;
        $inicio = ($pagina - 1) * $registros;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        /* ================= FILTROS ================= */

        $filtros = [
            [
                "campo" => "d.id_diagnostico",
                "tipo"  => "=",
                "valor" => $nro_diagnostico
            ],
            [
                "campo" => "rs.idrecepcion",
                "tipo"  => "=",
                "valor" => $nro_recepcion
            ],
            [
                "campo" => "CONCAT(c.nombre_cliente,' ',c.apellido_cliente)",
                "tipo"  => "LIKE",
                "valor" => $cliente
            ],
            [
                "campo" => "v.placa",
                "tipo"  => "LIKE",
                "valor" => $placa
            ],
            [
                "campo" => "d.fecha_diagnostico",
                "tipo"  => "DATE_RANGE",
                "desde" => $busqueda1,
                "hasta" => $busqueda2
            ],
            [
                "campo" => "d.estado",
                "tipo"  => "=",
                "valor" => $estado_filtro
            ],
            [
                "campo" => "rs.origen",
                "tipo"  => "=",
                "valor" => $origen
            ],
            [
                "campo" => "CONCAT(d.id_diagnostico, ' ', rs.idrecepcion, ' ', c.nombre_cliente, ' ', c.apellido_cliente, ' ', c.doc_number, ' ', v.placa, ' ', IFNULL(ma.mar_descri,''), ' ', IFNULL(m.mod_descri,''), ' ', IFNULL(rs.tipo_servicio,''), ' ', IFNULL(rs.observacion,''), ' ', IFNULL(d.observaciones,''), ' ', IFNULL(et.nombre,''), ' ', u.usu_nombre, ' ', u.usu_apellido)",
                "tipo"  => "LIKE",
                "valor" => $busqueda_general
            ]
        ];

        $filtros = array_filter($filtros, function ($f) {
            if (($f['tipo'] ?? '') === 'DATE_RANGE') {
                return !empty($f['desde']) || !empty($f['hasta']);
            }

            return isset($f['valor']) && $f['valor'] !== '';
        });

        $filtrosSQL = mainModel::construirFiltros($filtros);

        $columnasOrdenSql = [
            'fecha' => 'd.fecha_diagnostico',
            'estado' => 'd.estado'
        ];

        $ordenamiento = mainModel::preparar_ordenamiento($orden, $direccion, $columnasOrdenSql, 'fecha', 'DESC');
        $orden = $ordenamiento['orden'];
        $direccion = $ordenamiento['direccion'];

        /* ================= DATOS ================= */

        $res = diagnosticoModelo::listar_diagnosticos_modelo(
            $inicio,
            $registros,
            $filtrosSQL,
            "ORDER BY " . $ordenamiento['sql'] . ", d.id_diagnostico DESC"
        );

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        $contador   = $inicio + 1;
        $reg_inicio = $inicio + 1;

        $puedeAnular = mainModel::tienePermiso('servicio.diagnostico.anular');

        /* ================= TABLA ================= */

        $tabla .= '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
        <tr class="text-center">
            <th>Diag.</th>
            <th>Recep.</th>
            <th>' . mainModel::link_orden_tabla($url, 'fecha', 'Fecha', $orden, $direccion, 'diagnostico_orden', 'diagnostico_direccion') . '</th>
            <th>Cliente</th>
            <th>Vehiculo</th>
            <th>Servicio</th>
            <th>Origen</th>
            <th>Equipo</th>
            <th>Usuario</th>
            <th>' . mainModel::link_orden_tabla($url, 'estado', 'Estado', $orden, $direccion, 'diagnostico_orden', 'diagnostico_direccion') . '</th>
            <th>Acciones</th>
        </tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            foreach ($datos as $rows) {

                $estadoMap = [
                    1 => ['En proceso', 'info'],
                    2 => ['Presupuestado', 'success'],
                    3 => ['Finalizado', 'primary'],
                    4 => ['OT generada', 'secondary'],
                    0 => ['Anulado', 'warning']
                ];

                $estado = $estadoMap[$rows['estado']] ?? ['Pendiente', 'secondary'];

                $origenBadge = ($rows['origen'] ?? '') === 'RECLAMO'
                    ? '<span class="badge badge-warning">Reclamo</span>'
                    : '<span class="badge badge-secondary">Normal</span>';

                $esReclamo = ($rows['origen'] ?? '') === 'RECLAMO';
                $tieneOTReclamo = $esReclamo && !empty($rows['id_ot_reclamo']);
                $reclamoValido = (int)($rows['es_reclamo_valido'] ?? 1) === 1;
                $puedeProcesarDiagnostico = (int)$rows['estado'] !== 0 && $esReclamo && $reclamoValido && !$tieneOTReclamo;

                $tabla .= '<tr class="text-center">
            <td>' . (int) $rows['id_diagnostico'] . '</td>
            <td>' . (int) $rows['idrecepcion'] . '</td>
            <td>' . date("d/m/Y H:i", strtotime($rows['fecha_diagnostico'])) . '</td>
            <td>' . htmlspecialchars($rows['cliente'], ENT_QUOTES, 'UTF-8') . '</td>
            <td>' . htmlspecialchars($rows['vehiculo'] ?: $rows['placa'], ENT_QUOTES, 'UTF-8') . '</td>
            <td>' . htmlspecialchars($rows['tipo_servicio'] ?: '-', ENT_QUOTES, 'UTF-8') . '</td>
            <td>' . $origenBadge . '</td>
            <td>' . htmlspecialchars($rows['equipo'] ?: '-', ENT_QUOTES, 'UTF-8') . '</td>
            <td>' . htmlspecialchars(trim(($rows['usu_nombre'] ?? '') . ' ' . ($rows['usu_apellido'] ?? '')) ?: '-', ENT_QUOTES, 'UTF-8') . '</td>
            <td><span class="badge bg-' . $estado[1] . '">' . $estado[0] . '</span></td>
            <td>';

                /* ================= BOTÓN INTELIGENTE ================= */

                $tabla .= '
                    <button type="button" class="btn btn-secondary btn-sm mr-1 btn-ver-diagnostico"
                        data-id="' . (int) $rows['id_diagnostico'] . '">
                        <i class="fas fa-eye"></i>
                    </button>';

                if ($puedeProcesarDiagnostico) {
                    $tabla .= '
                    <button class="btn btn-info btn-sm mr-1"
                    onclick="evaluarDiagnostico(
                        ' . $rows['id_diagnostico'] . ',
                        ' . ($esReclamo ? 1 : 0) . ',
                        ' . ($rows['es_garantia'] ?? 0) . ',
                        ' . ($rows['requiere_cobro'] ?? 0) . ',
                        ' . ($rows['idreclamo_servicio'] !== null ? $rows['idreclamo_servicio'] : 'null') . ',
                        ' . ($reclamoValido ? 1 : 0) . '
                    )">
                        <i class="fas fa-cogs"></i>
                    </button>';
                }

                /* ================= BOTÓN ANULAR ================= */

                if ($puedeAnular) {

                    if ($rows['estado'] == 0) {
                        $tabla .= '
                    <button class="btn btn-secondary btn-sm" disabled>
                        <i class="fas fa-ban"></i>
                    </button>';
                    } else {
                        $tabla .= '
                    <form class="FormularioAjax d-inline"
                        action="' . SERVERURL . 'ajax/diagnosticoAjax.php"
                        method="POST"
                        data-form="delete"
                        data-anulacion="true"
                        data-anulacion-titulo="Anular diagnostico">

                        <input type="hidden" name="accion" value="anular_diagnostico">
                        <input type="hidden" name="id_diagnostico" value="' . $rows['id_diagnostico'] . '">

                        <button class="btn btn-danger btn-sm">
                            <i class="fas fa-ban"></i>
                        </button>
                    </form>';
                    }
                }

                $tabla .= '</td></tr>';
                $contador++;
            }

            $reg_final = $contador - 1;
        } else {
            $tabla .= '<tr class="text-center">
        <td colspan="10">No hay registros en el sistema</td>
        </tr>';
        }

        $tabla .= '</tbody></table></div>';

        /* ================= PAGINADOR ================= */

        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '<p class="text-right">
        Mostrando ' . $reg_inicio . ' al ' . $reg_final . ' de ' . $total . '
        </p>';

            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        return $tabla;
    }

    public function anular_diagnostico_controlador()
    {
        if (!mainModel::tienePermiso('servicio.diagnostico.anular')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Permiso denegado",
                "Texto"  => "No tienes permiso para anular diagnósticos",
                "Tipo"   => "error"
            ]);
        }
        if (empty($_POST['id_diagnostico'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "ID inválido",
                "Tipo" => "error"
            ]);
        }

        $id = $_POST['id_diagnostico'];
        $motivo = mainModel::limpiar_string($_POST['observacion_anulacion'] ?? '');

        $resp = diagnosticoModelo::anular_diagnostico_modelo($id, $_SESSION['id_str'], $motivo);

        if (isset($resp['error'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => $resp['msg'],
                "Tipo" => "error"
            ]);
        }

        return json_encode([
            "Alerta" => "recargar",
            "Titulo" => "Anulado",
            "Texto" => "Diagnóstico anulado correctamente",
            "Tipo" => "success"
        ]);
    }

    public function obtener_reclamo_detalle_controlador()
    {
        if (empty($_POST['idreclamo'])) {
            return json_encode([]);
        }

        $id = mainModel::limpiar_string($_POST['idreclamo']);

        $sql = mainModel::conectar()->prepare("
        SELECT 
            descripcion,
            tipo_reclamo,
            prioridad,
            fecha_reclamo
        FROM reclamo_servicio 
        WHERE idreclamo_servicio = :id 
        LIMIT 1
        ");

        $sql->bindParam(":id", $id);
        $sql->execute();

        $data = $sql->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $det = mainModel::conectar()->prepare("
                SELECT
                    rd.idreclamo_detalle,
                    rd.motivo,
                    rd.requiere_garantia,
                    d.cantidad,
                    d.origen,
                    a.desc_articulo,
                    a.tipo
                FROM reclamo_servicio_detalle rd
                INNER JOIN registro_servicio_detalle d
                    ON d.id_registro_servicio_detalle = rd.id_registro_servicio_detalle
                INNER JOIN articulos a
                    ON a.id_articulo = d.id_articulo
                WHERE rd.idreclamo_servicio = :id
                  AND rd.estado = 1
                ORDER BY rd.idreclamo_detalle ASC
            ");
            $det->bindParam(":id", $id);
            $det->execute();
            $data['detalles'] = $det->fetchAll(PDO::FETCH_ASSOC);
        }

        return json_encode($data ?: []);
    }
}
