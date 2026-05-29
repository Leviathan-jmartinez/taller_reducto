<?php
require_once __DIR__ . "/../modelos/ordenTrabajoModelo.php";


class ordenTrabajoControlador extends ordenTrabajoModelo
{

    public function listar_ot_controlador($pagina, $registros, $url, $busqueda1, $busqueda2, $orden = 'fecha', $direccion = 'DESC')
    {
        $pagina    = (int) mainModel::limpiar_string($pagina);
        $registros = (int) mainModel::limpiar_string($registros);
        $url       = SERVERURL . mainModel::limpiar_string($url) . "/";
        $orden = mainModel::limpiar_string($orden);
        $direccion = strtoupper(mainModel::limpiar_string($direccion));

        $estadoFiltro = $_SESSION['estado_ot'] ?? '';

        $pagina = ($pagina > 0) ? $pagina : 1;
        $inicio = ($pagina - 1) * $registros;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        /* ================= FILTROS ================= */

        $filtros = [];

        if (!empty($busqueda1) && !empty($busqueda2)) {
            $filtros[] = [
                "campo" => "ot.fecha_inicio",
                "tipo"  => "DATE_RANGE",
                "desde" => $busqueda1,
                "hasta" => $busqueda2
            ];
        }

        if ($estadoFiltro !== '') {
            $filtros[] = [
                "campo" => "ot.estado",
                "tipo"  => "=",
                "valor" => $estadoFiltro
            ];
        }

        $filtrosSQL = mainModel::construirFiltros($filtros);
        $columnasOrdenSql = [
            'fecha' => 'ot.fecha_inicio',
            'estado' => 'ot.estado'
        ];
        $ordenamiento = mainModel::preparar_ordenamiento($orden, $direccion, $columnasOrdenSql, 'fecha', 'DESC');
        $orden = $ordenamiento['orden'];
        $direccion = $ordenamiento['direccion'];

        /* ================= DATOS ================= */

        $res = ordenTrabajoModelo::listar_ot_modelo($inicio, $registros, $filtrosSQL, "ORDER BY " . $ordenamiento['sql'] . ", ot.idorden_trabajo DESC");

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>' . mainModel::link_orden_tabla($url, 'fecha', 'Fecha', $orden, $direccion, 'ot_orden', 'ot_direccion') . '</th>
                <th>Presupuesto</th>
                <th>Creado por</th>
                <th>' . mainModel::link_orden_tabla($url, 'estado', 'Estado', $orden, $direccion, 'ot_orden', 'ot_direccion') . '</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador   = $inicio + 1;
            $reg_inicio = $inicio + 1;

            foreach ($datos as $rows) {

                /* ================= ESTADO ================= */

                switch ($rows['estado']) {
                    case 1:
                        $estado = '<span class="badge badge-warning">Activa</span>';
                        break;
                    case 2:
                        $estado = '<span class="badge badge-success">Servicio registrado</span>';
                        break;
                    case 3:
                        $estado = '<span class="badge badge-info">Pendiente completar</span>';
                        break;
                    case 0:
                        $estado = '<span class="badge badge-danger">Anulada</span>';
                        break;
                    default:
                        $estado = '<span class="badge badge-secondary">?</span>';
                }

                /* ================= FILA ================= */

                $tabla .= '
            <tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $rows['nombre_cliente'] . ' ' . $rows['apellido_cliente'] . '</td>
                <td>' . $rows['modelo'] . ' ' . $rows['placa'] . '</td>
                <td>' . date("d-m-Y", strtotime($rows['fecha_inicio'])) . '</td>
                <td>' . (!empty($rows['idpresupuesto_servicio']) ? '#' . $rows['idpresupuesto_servicio'] : 'Reclamo') . '</td>
                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                <td>' . $estado . '</td>
                <td>';
                       
                if (($rows['origen'] ?? '') === 'RECLAMO' && (int)$rows['estado'] === 3) {
                    $tabla .= '
                    <a href="' . SERVERURL . 'ordenTrabajo-asignar/?id=' . urlencode(mainModel::encryption($rows['idorden_trabajo'])) . '"
                        class="btn btn-primary btn-sm"
                        title="Completar OT por reclamo">
                        <i class="fas fa-tools"></i>
                    </a>';
                }

                /* ================= ACCIONES ================= */

                // ✔ asignar técnico
                // ✔ imprimir
                $tabla .= '
            <a href="' . SERVERURL . 'pdf/ordenTrabajo.php?id=' . urlencode(mainModel::encryption($rows['idorden_trabajo'])) . '"
                target="_blank"
                class="btn btn-info btn-sm"
                title="Imprimir OT">
                <i class="fas fa-print"></i>
            </a>';

                // ✔ anular
                if (in_array((int)$rows['estado'], [1, 3], true)) {
                    $tabla .= '
                <form class="FormularioAjax d-inline"
                    action="' . SERVERURL . 'ajax/ordenTrabajoAjax.php"
                    method="POST"
                    data-form="delete"
                    autocomplete="off">

                    <input type="hidden" name="accion" value="anular">
                    <input type="hidden" name="id"
                        value="' . mainModel::encryption($rows['idorden_trabajo']) . '">

                    <button type="submit"
                            class="btn btn-danger btn-sm"
                            title="Anular OT">
                        <i class="fas fa-ban"></i>
                    </button>
                </form>';
                }

                $tabla .= '
                </td>
            </tr>';

                $contador++;
            }

            $reg_final = $contador - 1;
        } else {
            $tabla .= '<tr><td colspan="8" class="text-center">Sin registros</td></tr>';
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

    public function cargar_tecnicos_equipo_controlador()
    {
        $idEquipo = mainModel::limpiar_string($_POST['cargar_tecnicos_equipo']);
        $tecnicos = ordenTrabajoModelo::obtener_tecnicos_equipo_modelo($idEquipo);

        $html = '<option value="">Seleccione un técnico</option>';

        if (empty($tecnicos)) {
            return $html . '<option value="">Sin técnicos</option>';
        }

        foreach ($tecnicos as $t) {
            $html .= '<option value="' . $t['idempleados'] . '">' . $t['nombre'] . '</option>';
        }

        return $html;
    }

    public function asignar_equipo_controlador()
    {
        session_start(['name' => 'STR']);

        if (empty($_POST['id_ot']) || empty($_POST['idtrabajos']) || empty($_POST['tecnico_responsable'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Debe seleccionar equipo y técnico',
                'Tipo'   => 'error'
            ]);
        }

        $ot       = mainModel::decryption($_POST['id_ot']);
        $equipo   = intval($_POST['idtrabajos']);
        $tecnico  = intval($_POST['tecnico_responsable']);

        // Validar sucursal 
        $sucursalOT = mainModel::ejecutar_consulta_simple("
        SELECT id_sucursal 
        FROM orden_trabajo
        WHERE idorden_trabajo = '$ot'
            ")->fetchColumn();

        if ($sucursalOT != $_SESSION['nick_sucursal']) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'OT no pertenece a su sucursal',
                'Tipo'   => 'error'
            ]);
        }

        ordenTrabajoModelo::asignar_equipo_modelo($ot, $equipo, $tecnico);

        return json_encode([
            'Alerta' => 'recargar',
            'Titulo' => 'Equipo asignado',
            'Texto'  => 'La OT fue asignada al equipo correctamente',
            'Tipo'   => 'success'
        ]);
    }

    public function listar_equipos_controlador()
    {
        session_start(['name' => 'STR']);

        $equipos = ordenTrabajoModelo::listar_equipos_modelo(
            $_SESSION['nick_sucursal']
        );

        $salida = [];
        foreach ($equipos as $eq) {
            $salida[] = [
                'idtrabajos' => $eq['id_equipo'],
                'nombre'     => $eq['nombre']
            ];
        }

        return $salida;
    }

    public function obtener_ot_pdf_controlador($idOT)
    {
        return ordenTrabajoModelo::obtener_ot_modelo($idOT);
    }

    public function obtener_detalle_ot_pdf_controlador($idOT)
    {
        return ordenTrabajoModelo::obtener_detalle_ot_modelo($idOT);
    }

    public function datos_ot_controlador($idOT)
    {
        return [
            'cabecera' => ordenTrabajoModelo::obtener_ot_completa($idOT),
            'detalle'  => ordenTrabajoModelo::obtener_detalle_ot($idOT)
        ];
    }
    public static function decrypt($valor)
    {
        return mainModel::decryption($valor);
    }

    public function buscar_presupuesto_aprobado_controlador()
    {
        $texto = trim($_POST['buscar_presupuesto'] ?? '');
        session_start(['name' => 'STR']);
        $consulta = "
                    SELECT
                ps.idpresupuesto_servicio,
                ps.fecha,
                ps.id_cliente,
                ps.id_vehiculo,
                c.nombre_cliente,
                c.apellido_cliente,
                v.placa,
                ma.mod_descri AS modelo
            FROM presupuesto_servicio ps
            INNER JOIN clientes c 
                ON c.id_cliente = ps.id_cliente
            INNER JOIN vehiculos v 
                ON v.id_vehiculo = ps.id_vehiculo
            LEFT JOIN modelo_auto ma 
                ON ma.id_modeloauto = v.id_modeloauto
            WHERE ps.estado = '2'
            AND ps.id_sucursal = :sucursal
            AND NOT EXISTS (
                SELECT 1
                FROM orden_trabajo ot
                WHERE ot.idpresupuesto_servicio = ps.idpresupuesto_servicio
                    AND ot.estado != 0
            )
            AND (
                    c.nombre_cliente LIKE :busqueda
                OR c.apellido_cliente LIKE :busqueda
                OR v.placa LIKE :busqueda
                OR ma.mod_descri LIKE :busqueda
            )
            ORDER BY ps.fecha DESC
        ";

        $sql = self::conectar()->prepare($consulta);
        $sql->bindValue(":busqueda", "%$texto%");
        $sql->bindValue(':sucursal', $_SESSION['nick_sucursal'], PDO::PARAM_INT);
        $sql->execute();

        if ($sql->rowCount() == 0) {
            return '<div class="alert alert-warning">No se encontraron presupuestos</div>';
        }

        $tabla = '<table class="table table-dark table-sm">
            <thead>
                <tr>
                    <th>Presupuesto</th>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead><tbody>';

        foreach ($sql->fetchAll() as $row) {
            $cliente = trim(($row['nombre_cliente'] ?? '') . ' ' . ($row['apellido_cliente'] ?? ''));
            $vehiculo = trim(($row['modelo'] ?? '') . ' ' . ($row['placa'] ?? ''));
            $fecha = !empty($row['fecha']) ? date("d/m/Y", strtotime($row['fecha'])) : '';
            $args = htmlspecialchars(json_encode([
                (int)$row['idpresupuesto_servicio'],
                $cliente,
                $vehiculo,
                $fecha
            ], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');

            $tabla .= '
            <tr>
                <td class="text-center">#' . (int)$row['idpresupuesto_servicio'] . '</td>
                <td>' . htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($vehiculo, ENT_QUOTES, 'UTF-8') . '</td>
                <td class="text-center">' . htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8') . '</td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm"
                        onclick="seleccionarPresupuesto(...JSON.parse(this.dataset.presupuesto))"
                        data-presupuesto="' . $args . '">
                        Seleccionar
                    </button>
                </td>
            </tr>';
        }

        return $tabla . '</tbody></table>';
    }

    public function obtener_detalle_presupuesto_controlador()
    {
        $idpresupuesto = $_POST['idpresupuesto_servicio'];

        $sql = self::conectar()->prepare("
        SELECT a.desc_articulo,
               a.tipo,
               d.cantidad
        FROM presupuesto_detalleservicio d
        INNER JOIN articulos a ON a.id_articulo = d.id_articulo
        WHERE d.idpresupuesto_servicio = ?");
        $sql->execute([$idpresupuesto]);

        if ($sql->rowCount() == 0) {
            return '<tr><td colspan="4" class="text-center">Sin detalle</td></tr>';
        }

        $html = '';
        foreach ($sql->fetchAll() as $row) {
            $tipoArticulo = strtolower((string)($row['tipo'] ?? ''));
            $tipo = ($tipoArticulo === 'servicio') ? 'Trabajo' : 'Repuesto';
            $badge = ($tipoArticulo === 'servicio') ? 'badge-info' : 'badge-secondary';

            $html .= '
            <tr>
                <td>' . htmlspecialchars($row['desc_articulo'], ENT_QUOTES, 'UTF-8') . '</td>
                <td class="text-center"><span class="badge ' . $badge . '">' . $tipo . '</span></td>
                <td class="text-center">' . number_format((float)$row['cantidad'], 2, ',', '.') . '</td>
                <td class="text-center"><span class="badge badge-warning">Pendiente</span></td>
            </tr>
        ';
        }

        return $html;
    }

    public function generar_ot_controlador2()
    {
        session_start(['name' => 'STR']);

        if (!mainModel::tienePermiso('servicio.ot.generar')) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto'  => 'No tiene permiso para generar orden de trabajo',
                'Tipo'   => 'error'
            ]);
        }

        $idPresupuesto = (int) mainModel::limpiar_string($_POST['idpresupuesto_servicio'] ?? '0');
        $idEquipo = (int) mainModel::limpiar_string($_POST['idtrabajos'] ?? '0');
        $idTecnico = (int) mainModel::limpiar_string($_POST['tecnico_responsable'] ?? '0');

        if ($idPresupuesto <= 0) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Debe seleccionar un presupuesto aprobado',
                'Tipo'   => 'error'
            ]);
        }
        if ($idEquipo <= 0 || $idTecnico <= 0) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Debe seleccionar un equipo y un técnico responsable',
                'Tipo'   => 'warning'
            ]);
        }


        $datos = [
            'idpresupuesto' => $idPresupuesto,
            'idusuario'     => $_SESSION['id_str'],
            'idsucursal'    => $_SESSION['nick_sucursal'],
            'idtrabajos'          => $idEquipo,
            'tecnico_responsable' => $idTecnico,
            'observacion'   => $_POST['observacion'] ?? ''
        ];

        $res = ordenTrabajoModelo::crear_ot_modelo2($datos);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'recargar',
                'Titulo' => 'OT generada',
                'Texto'  => 'La orden de trabajo fue creada correctamente',
                'Tipo'   => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto'  => $res['msg'] ?? 'No se pudo generar OT',
            'Tipo'   => 'error'
        ]);
    }

    public function anular_ot_controlador()
    {
        session_start(['name' => 'STR']);

        if (empty($_POST['id'])) {
            echo json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'OT no válida',
                'Tipo'   => 'error'
            ]);
            exit();
        }

        $idOT    = mainModel::decryption($_POST['id']);
        $usuario = $_SESSION['id_str'];

        $sucursalOT = mainModel::ejecutar_consulta_simple("
            SELECT id_sucursal
            FROM orden_trabajo
            WHERE idorden_trabajo = '$idOT'
        ")->fetchColumn();

        if ($sucursalOT != $_SESSION['nick_sucursal']) {
            echo json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto'  => 'No puede anular una OT de otra sucursal',
                'Tipo'   => 'error'
            ]);
            exit();
        }

        $res = ordenTrabajoModelo::anular_ot_modelo($idOT, $usuario);

        if ($res === true) {
            echo json_encode([
                'Alerta' => 'recargar',
                'Titulo' => 'OT anulada',
                'Texto'  => 'La orden de trabajo fue anulada correctamente',
                'Tipo'   => 'success'
            ]);
            exit();
        }

        echo json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto'  => is_array($res) ? ($res['msg'] ?? 'No se pudo anular la OT') : 'No se pudo anular la OT',
            'Tipo'   => 'error'
        ]);
        exit();
    }

    public function crear_ot_reclamo_controlador()
    {
        session_start(['name' => 'STR']);

        if (!mainModel::tienePermiso('servicio.ot.generar')) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto' => 'No tiene permiso para generar orden de trabajo',
                'Tipo' => 'error'
            ]);
        }

        if (empty($_POST['idreclamo_servicio'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Reclamo no válido",
                "Tipo" => "error"
            ]);
        }

        $idReclamo = mainModel::limpiar_string($_POST['idreclamo_servicio']);

        $ok = ordenTrabajoModelo::crear_ot_reclamo_modelo(
            $idReclamo,
            $_SESSION['id_str'],
            $_SESSION['nick_sucursal']
        );

        if ($ok === true) {
            return json_encode([
                "Alerta" => "recargar",
                "Titulo" => "OT creada",
                "Texto" => "Orden de trabajo generada correctamente",
                "Tipo" => "success"
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto" => $ok === false ? "El reclamo ya tiene una OT activa" : $ok,
            "Tipo" => "error"
        ]);
    }

    public function completar_ot_controlador()
    {
        session_start(['name' => 'STR']);

        if (!mainModel::tienePermiso('servicio.ot.asignar_tecnico')) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto' => 'No tiene permiso para registrar el servicio de esta OT',
                'Tipo' => 'error'
            ]);
        }
        
        if (empty($_POST['idorden_trabajo']) || empty($_POST['idtrabajos']) || empty($_POST['tecnico_responsable'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe seleccionar equipo y tecnico",
                "Tipo" => "error"
            ]);
        }

        $trabajos = json_decode($_POST['trabajos_json'] ?? '[]', true);
        $repuestos = json_decode($_POST['repuestos_json'] ?? '[]', true);

        if (!is_array($trabajos) || !is_array($repuestos)) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Detalle de OT invalido",
                "Tipo" => "error"
            ]);
        }

        if (empty($trabajos) && empty($repuestos)) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe agregar al menos un trabajo o repuesto",
                "Tipo" => "warning"
            ]);
        }

        $datos = [
            "idorden_trabajo" => $_POST['idorden_trabajo'],
            "tecnico" => $_POST['tecnico_responsable'],
            "equipo" => $_POST['idtrabajos'],
            "obs" => $_POST['observacion'] ?? '',
            "trabajos" => $trabajos,
            "repuestos" => $repuestos
        ];

        $res = ordenTrabajoModelo::completar_ot_modelo($datos);

        if ($res === true) {
            return json_encode([
                "Alerta" => "redireccionar_confirmado",
                "URL" => SERVERURL . "ordenTrabajo-buscar/",
                "Titulo" => "OT completada",
                "Texto" => "La OT por reclamo quedo activa para registro",
                "Tipo" => "success"
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto" => $res,
            "Tipo" => "error"
        ]);
    }

    public function obtener_ot_controlador($id)
    {
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_string($id);
        return ordenTrabajoModelo::obtener_ot_modelo($id);
    }

    public function obtener_detalle_diagnostico_ot_controlador($idDiagnostico)
    {
        $idDiagnostico = (int) mainModel::limpiar_string($idDiagnostico);

        if ($idDiagnostico <= 0) {
            return [];
        }

        return ordenTrabajoModelo::obtener_detalle_diagnostico_modelo($idDiagnostico);
    }

    public function listar_tecnicos_select()
    {
        $pdo = mainModel::conectar();

        $sql = $pdo->query("
        SELECT idempleados, CONCAT(nombre,' ',apellido) AS nombre
        FROM empleados
        WHERE estado = 1
        ORDER BY nombre
        ");

        $html = '<option value="">Seleccione técnico</option>';

        foreach ($sql->fetchAll(PDO::FETCH_ASSOC) as $t) {
            $html .= '<option value="' . $t['idempleados'] . '">' . $t['nombre'] . '</option>';
        }

        return $html;
    }

    public function listar_equipos_select()
    {
        session_start(['name' => 'STR']);

        $equipos = ordenTrabajoModelo::listar_equipos_modelo(
            $_SESSION['nick_sucursal']
        );

        $html = '<option value="">Seleccione equipo</option>';

        foreach ($equipos as $eq) {
            $html .= '<option value="' . $eq['id_equipo'] . '">' . $eq['nombre'] . '</option>';
        }

        return $html;
    }
    public function buscar_articulos_controlador()
    {
        session_start(['name' => 'STR']);

        $texto = mainModel::limpiar_string($_POST['texto'] ?? '');

        $pdo = mainModel::conectar();

        $sql = $pdo->prepare("
        SELECT 
            a.id_articulo,
            a.desc_articulo,
            a.precio_venta,
            COALESCE(s.stockDisponible,0) AS stock
        FROM articulos a
        LEFT JOIN stock s 
            ON s.id_articulo = a.id_articulo 
            AND s.id_sucursal = ?
        WHERE a.estado = 1 AND a.tipo='producto' 
        AND a.desc_articulo LIKE ?
        LIMIT 10
        ");

        $sql->execute([
            $_SESSION['nick_sucursal'],
            "%$texto%"
        ]);

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar_servicios_controlador()
    {
        $texto = mainModel::limpiar_string($_POST['texto'] ?? '');

        $pdo = mainModel::conectar();

        $sql = $pdo->prepare("
        SELECT 
            id_articulo,
            desc_articulo,
            precio_venta
        FROM articulos
        WHERE estado = 1
        AND tipo = 'SERVICIO'
        AND desc_articulo LIKE ?
        LIMIT 10
        ");

        $sql->execute(["%$texto%"]);

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
