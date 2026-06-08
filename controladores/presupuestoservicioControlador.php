<?php
require_once __DIR__ . "/../modelos/presupuestoServicioModelo.php";

class presupuestoServicioControlador  extends presupuestoServicioModelo
{
    public function decrypt($valor)
    {
        return mainModel::decryption($valor);
    }

    public function datos_diagnostico_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $id = (int)($_POST['id_diagnostico'] ?? 0);

        $data = presupuestoServicioModelo::datos_diagnostico_modelo($id);

        return json_encode($data);
    }

    public function buscar_diagnostico_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $texto = $_POST['buscar_diagnostico'] ?? '';

        $sql = mainModel::conectar()->prepare("
        SELECT 
            d.id_diagnostico,
            c.nombre_cliente,
            c.doc_number,
            c.apellido_cliente,
            v.placa,
            ma.mar_descri AS marca,
            m.mod_descri AS modelo
        FROM diagnostico_servicio d
        INNER JOIN recepcion_servicio r ON r.idrecepcion = d.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        INNER JOIN marcas ma ON ma.id_marcas = m.id_marcas
        WHERE d.estado = 1
        AND (
            c.nombre_cliente LIKE :b
            OR c.apellido_cliente LIKE :b
            OR c.doc_number LIKE :b
            OR v.placa LIKE :b
            OR ma.mar_descri LIKE :b
            OR m.mod_descri LIKE :b
        )
        AND r.id_sucursal = :sucursal
        AND NOT EXISTS (
            SELECT 1
            FROM orden_trabajo ot
            WHERE ot.idreclamo_servicio = r.idreclamo_servicio
              AND ot.estado != 0
        )
        ORDER BY d.id_diagnostico DESC
        ");

        $sql->bindValue(":b", "%$texto%");
        $sql->bindValue(":sucursal", $_SESSION['nick_sucursal'] ?? 0, PDO::PARAM_INT);
        $sql->execute();

        $html = '<table class="table table-dark table-sm">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Vehiculo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>';

        foreach ($sql->fetchAll() as $row) {
            $cliente = trim($row['doc_number'] . ' - ' . $row['nombre_cliente'] . ' ' . $row['apellido_cliente']);
            $vehiculo = trim($row['marca'] . ' ' . $row['modelo'] . ' ' . $row['placa']);
            $clienteHtml = htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8');
            $vehiculoHtml = htmlspecialchars($vehiculo, ENT_QUOTES, 'UTF-8');
            $descJs = htmlspecialchars(
                json_encode($cliente . ' - ' . $vehiculo, JSON_UNESCAPED_UNICODE),
                ENT_QUOTES,
                'UTF-8'
            );
            $idDiagnostico = (int) $row['id_diagnostico'];

            $html .= "
        <tr>
            <td>{$clienteHtml}</td>
            <td>{$vehiculoHtml}</td>
            <td>
                <button class='btn btn-success btn-sm'
                    onclick=\"seleccionarDiagnostico(
                        {$idDiagnostico},
                        {$descJs}
                    )\">
                    Seleccionar
                </button>
            </td>
        </tr>";
        }

        $html .= '</tbody></table>';

        return $html;
    }

    public function buscar_servicios_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $txt = trim($_POST['buscar_servicio'] ?? '');
        $origen = strtoupper(trim($_POST['origen_presupuesto'] ?? 'DIAGNOSTICO'));
        $idDiagnostico = (int)($_POST['id_diagnostico'] ?? 0);
        $idCliente = (int)($_POST['id_cliente'] ?? 0);
        $idVehiculo = (int)($_POST['id_vehiculo'] ?? 0);

        if ($txt === '') {
            return '';
        }

        if ($origen === 'PRELIMINAR') {
            if ($idCliente <= 0 || $idVehiculo <= 0) {
                return '<div class="alert alert-warning text-center">
                    Seleccione cliente y vehiculo antes de agregar servicios
                </div>';
            }

            $vehiculoValido = mainModel::conectar()->prepare("
                SELECT COUNT(*)
                FROM vehiculos
                WHERE id_vehiculo = :vehiculo
                  AND id_cliente = :cliente
                  AND estado = 1
            ");
            $vehiculoValido->execute([
                ':vehiculo' => $idVehiculo,
                ':cliente' => $idCliente
            ]);

            if ((int)$vehiculoValido->fetchColumn() === 0) {
                return '<div class="alert alert-warning text-center">
                    El vehiculo no corresponde al cliente seleccionado
                </div>';
            }
        } else {
            if ($idDiagnostico <= 0) {
                return '<div class="alert alert-warning text-center">
                    Seleccione un diagnostico antes de agregar servicios
                </div>';
            }

            $diagnosticoDisponible = mainModel::conectar()->prepare("
                SELECT COUNT(*)
                FROM diagnostico_servicio d
                INNER JOIN recepcion_servicio r ON r.idrecepcion = d.idrecepcion
                WHERE d.id_diagnostico = :id
                  AND d.estado = 1
                  AND r.id_sucursal = :sucursal
                  AND NOT EXISTS (
                      SELECT 1
                      FROM orden_trabajo ot
                      WHERE ot.idreclamo_servicio = r.idreclamo_servicio
                        AND ot.estado != 0
                  )
            ");
            $diagnosticoDisponible->execute([
                ':id' => $idDiagnostico,
                ':sucursal' => $_SESSION['nick_sucursal'] ?? 0
            ]);

            if ((int)$diagnosticoDisponible->fetchColumn() === 0) {
                return '<div class="alert alert-warning text-center">
                    El diagnostico no esta disponible para presupuesto
                </div>';
            }
        }

        $datos = presupuestoServicioModelo::buscar_servicios_modelo(
            $txt,
            $_SESSION['nick_sucursal']
        );

        if (!$datos) {
            return '<div class="alert alert-warning text-center">
                No se encontraron servicios
            </div>';
        }

        $html = '<ul class="list-group">';

        foreach ($datos as $d) {

            $desc = addslashes($d['desc_articulo']);
            $precio = (int)$d['precio_venta'];
            $tipo = $d['tipo'];
            $stock = (float)$d['stock'];

            // 🔥 stock visual
            $stockHtml = '';

            if ($tipo === 'producto') {
                if ($stock <= 0) {
                    $stockHtml = "<span class='text-danger'>Sin stock</span>";
                } else {
                    $stockHtml = "<span class='text-success'>Stock: {$stock}</span>";
                }
            } else {
                $stockHtml = "<span class='badge bg-info'>Servicio</span>";
            }

            // 🔥 deshabilitar botón si no hay stock
            $disabled = ($tipo === 'producto' && $stock <= 0) ? 'disabled' : '';

            $html .= "
        <li class='list-group-item d-flex justify-content-between align-items-center'>
            
            <div>
                <strong>{$d['codigo']}</strong> - {$d['desc_articulo']}
                <br>
                <small class='text-muted'>
                    Precio: Gs. " . number_format($precio, 0, ',', '.') . "
                </small>
                <br>
                {$stockHtml}
            </div>

            <button type='button'
                    class='btn btn-success btn-sm'
                    {$disabled}
                    onclick=\"agregarServicio(
                        {$d['id_articulo']},
                        '{$desc}',
                        {$precio},
                        '{$tipo}',
                        {$stock}
                    )\">
                <i class='fas fa-plus'></i>
            </button>
        </li>";
        }

        $html .= '</ul>';

        return $html;
    }

    public function promo_articulo_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $id = intval($_POST['promo_articulo']);

        return presupuestoServicioModelo::promo_articulo_modelo($id);
    }

    public function descuentos_cliente_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $idCliente = intval($_POST['descuentos_cliente']);

        return presupuestoServicioModelo::descuentos_cliente_modelo($idCliente);
    }

    public function buscar_preliminares_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $idCliente = (int)($_POST['id_cliente'] ?? 0);
        $idVehiculo = (int)($_POST['id_vehiculo'] ?? 0);

        if ($idCliente <= 0 || $idVehiculo <= 0) {
            return '';
        }

        $sql = mainModel::conectar()->prepare("
            SELECT idpresupuesto_servicio, fecha, total_final, estado
            FROM presupuesto_servicio
            WHERE origen = 'PRELIMINAR'
              AND id_cliente = :cliente
              AND id_vehiculo = :vehiculo
              AND id_sucursal = :sucursal
              AND estado IN (1,2)
              AND fecha_venc >= CURDATE()
            ORDER BY idpresupuesto_servicio DESC
            LIMIT 5
        ");
        $sql->execute([
            ':cliente' => $idCliente,
            ':vehiculo' => $idVehiculo,
            ':sucursal' => $_SESSION['nick_sucursal'] ?? 0
        ]);

        $datos = $sql->fetchAll(PDO::FETCH_ASSOC);
        if (!$datos) {
            return '';
        }

        $html = '<div class="alert alert-info mb-2">Hay presupuestos preliminares para este cliente/vehiculo.</div>';
        $html .= '<table class="table table-sm table-bordered"><thead><tr><th>Nro.</th><th>Fecha</th><th>Total</th><th></th></tr></thead><tbody>';

        foreach ($datos as $row) {
            $html .= '<tr>
                <td>#' . (int)$row['idpresupuesto_servicio'] . '</td>
                <td>' . date('d/m/Y', strtotime($row['fecha'])) . '</td>
                <td>' . number_format((float)$row['total_final'], 0, ',', '.') . '</td>
                <td class="text-center">
                    <button type="button" class="btn btn-success btn-sm" onclick="usarPresupuestoPreliminar(' . (int)$row['idpresupuesto_servicio'] . ')">
                        Usar
                    </button>
                </td>
            </tr>';
        }

        return $html . '</tbody></table>';
    }

    public function detalle_preliminar_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $id = (int)($_POST['id_presupuesto'] ?? 0);

        $sql = mainModel::conectar()->prepare("
            SELECT ps.idpresupuesto_servicio, ps.id_cliente, ps.id_vehiculo, ps.estado
            FROM presupuesto_servicio ps
            WHERE ps.idpresupuesto_servicio = :id
              AND ps.origen = 'PRELIMINAR'
              AND ps.id_sucursal = :sucursal
              AND ps.estado IN (1,2)
              AND ps.fecha_venc >= CURDATE()
            LIMIT 1
        ");
        $sql->execute([
            ':id' => $id,
            ':sucursal' => $_SESSION['nick_sucursal'] ?? 0
        ]);

        $cab = $sql->fetch(PDO::FETCH_ASSOC);
        if (!$cab) {
            return json_encode(['error' => true, 'msg' => 'Presupuesto preliminar no disponible']);
        }

        $det = mainModel::conectar()->prepare("
            SELECT
                pd.id_articulo,
                pd.cantidad,
                pd.preciouni,
                pd.subtotal,
                a.desc_articulo,
                a.tipo,
                COALESCE(s.stockDisponible, 0) AS stock
            FROM presupuesto_detalleservicio pd
            INNER JOIN articulos a ON a.id_articulo = pd.id_articulo
            LEFT JOIN stock s ON s.id_articulo = a.id_articulo AND s.id_sucursal = :sucursal
            WHERE pd.idpresupuesto_servicio = :id
        ");
        $det->execute([
            ':id' => $id,
            ':sucursal' => $_SESSION['nick_sucursal'] ?? 0
        ]);

        return json_encode([
            'error' => false,
            'id_presupuesto' => $id,
            'detalle' => $det->fetchAll(PDO::FETCH_ASSOC)
        ], JSON_UNESCAPED_UNICODE);
    }

    public function guardar_presupuesto_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.presupuesto.crear')) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto' => 'No tiene permiso para registrar presupuestos',
                'Tipo' => 'error'
            ]);
        }

        $detalle = json_decode($_POST['detalle_json'] ?? '[]', true);
        $descuentos = json_decode($_POST['descuentos_json'] ?? '[]', true);
        if (!is_array($detalle)) {
            $detalle = [];
        }
        if (!is_array($descuentos)) {
            $descuentos = [];
        }
        $origen = strtoupper(trim($_POST['origen_presupuesto'] ?? 'DIAGNOSTICO'));
        $origen = ($origen === 'PRELIMINAR') ? 'PRELIMINAR' : 'DIAGNOSTICO';
        $idDiagnostico = (int)($_POST['id_diagnostico'] ?? 0);
        $idCliente = (int)($_POST['id_cliente'] ?? 0);
        $idVehiculo = (int)($_POST['id_vehiculo'] ?? 0);
        $convertidoDesde = (int)($_POST['convertido_desde'] ?? 0);
        $fechaVenc = trim($_POST['fecha_venc'] ?? '');

        if ($fechaVenc === '') {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Fecha requerida',
                'Texto' => 'Debe indicar la fecha de vencimiento del presupuesto',
                'Tipo' => 'error'
            ]);
        }

        $fechaVenc = trim($_POST['fecha_venc'] ?? '');

        $fechaVencObj = DateTime::createFromFormat('!Y-m-d', $fechaVenc);
        $erroresFecha = DateTime::getLastErrors();

        if (
            !$fechaVencObj ||
            ($erroresFecha !== false && ($erroresFecha['warning_count'] > 0 || $erroresFecha['error_count'] > 0)) ||
            $fechaVencObj->format('Y-m-d') !== $fechaVenc
        ) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Fecha invalida',
                'Texto' => 'La fecha de vencimiento no tiene un formato valido',
                'Tipo' => 'error'
            ]);
        }

        $hoyObj = new DateTime('today');
        if ($fechaVencObj < $hoyObj) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Fecha invalida',
                'Texto' => 'La fecha de vencimiento no puede ser anterior a la fecha actual',
                'Tipo' => 'error'
            ]);
        }

        if ($origen === 'DIAGNOSTICO' && $idDiagnostico <= 0) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Diagnostico requerido',
                'Texto' => 'Debe seleccionar un diagnostico antes de generar el presupuesto de servicio',
                'Tipo' => 'error'
            ]);
        }

        if ($origen === 'PRELIMINAR' && ($idCliente <= 0 || $idVehiculo <= 0)) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Datos requeridos',
                'Texto' => 'Debe seleccionar cliente y vehiculo para un presupuesto preliminar',
                'Tipo' => 'error'
            ]);
        }

        if (empty($detalle)) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto' => 'Debe agregar al menos un servicio',
                'Tipo' => 'error'
            ]);
        }

        foreach ($detalle as $item) {
            $cantidad = (float)($item['cantidad'] ?? 0);
            $precio = (float)($item['precio_base'] ?? 0);

            if ($cantidad <= 0) {
                return json_encode([
                    'Alerta' => 'simple',
                    'Titulo' => 'Detalle invalido',
                    'Texto' => 'La cantidad del detalle debe ser mayor a cero',
                    'Tipo' => 'error'
                ]);
            }

            if ($precio < 0) {
                return json_encode([
                    'Alerta' => 'simple',
                    'Titulo' => 'Detalle invalido',
                    'Texto' => 'El precio del detalle no puede ser negativo',
                    'Tipo' => 'error'
                ]);
            }
        }

        $datos = [
            'usuario'         => $_SESSION['id_str'],
            'id_diagnostico'  => $idDiagnostico,
            'origen'          => $origen,
            'id_cliente'      => $idCliente,
            'id_vehiculo'     => $idVehiculo,
            'convertido_desde' => $convertidoDesde > 0 ? $convertidoDesde : null,
            'fecha_venc'      => $fechaVenc,
            'subtotal'        => $_POST['subtotal_servicios'],
            'total_descuento' => $_POST['total_descuento'],
            'total_final'     => $_POST['total_final'],
            'detalle'         => $detalle,
            'descuentos'      => $descuentos
        ];

        $res = presupuestoServicioModelo::guardar_presupuesto_modelo($datos);

        if (is_array($res) && isset($res['error'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto' => $res['msg'],
                'Tipo' => 'error'
            ]);
        }

        return json_encode([
            'Alerta' => 'limpiar',
            'Titulo' => 'Presupuesto registrado',
            'Texto' => 'El presupuesto se guardó correctamente',
            'Tipo' => 'success'
        ]);
    }

    public function paginador_presupuestoservi_controlador($pagina, $registros, $url, $busqueda1, $busqueda2, $orden = 'fecha', $direccion = 'DESC')
    {

        $pagina    = (int) mainModel::limpiar_string($pagina);
        $registros = (int) mainModel::limpiar_string($registros);
        $orden = mainModel::limpiar_string($orden);
        $direccion = strtoupper(mainModel::limpiar_string($direccion));
        $estado = $_SESSION['estado_presupuesto'] ?? '';
        $url = SERVERURL . $url . "/";
        $tabla = "";

        $pagina = ($pagina > 0) ? $pagina : 1;
        $inicio = ($pagina - 1) * $registros;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        /* ================= FILTROS ================= */

        $filtros = [
            [
                "campo" => "ps.fecha",
                "tipo"  => "DATE_RANGE",
                "desde" => $busqueda1,
                "hasta" => $busqueda2
            ]
        ];

        if ($estado !== '') {
            $filtros[] = [
                "campo" => "ps.estado",
                "tipo"  => "=",
                "valor" => $estado
            ];
        }


        $filtrosSQL = mainModel::construirFiltros($filtros);
        $columnasOrdenSql = [
            'fecha' => 'ps.fecha',
            'estado' => 'ps.estado'
        ];
        $ordenamiento = mainModel::preparar_ordenamiento($orden, $direccion, $columnasOrdenSql, 'fecha', 'DESC');
        $orden = $ordenamiento['orden'];
        $direccion = $ordenamiento['direccion'];

        /* ================= DATOS ================= */

        $res = presupuestoServicioModelo::listar_presupuestos_modelo($inicio, $registros, $filtrosSQL, "ORDER BY " . $ordenamiento['sql'] . ", ps.idpresupuesto_servicio DESC");

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        $contador   = $inicio + 1;
        $reg_inicio = $inicio + 1;

        /* ================= PERMISOS ================= */

        $puedeAnular    = mainModel::tienePermiso('servicio.presupuesto.anular');
        $puedeAprobar   = mainModel::tienePermiso('servicio.presupuesto.aprobar');
        $puedeGenerarOT = mainModel::tienePermiso('servicio.ot.generar');

        $mostrarAcciones = $puedeAnular || $puedeAprobar || $puedeGenerarOT;

        /* ================= TABLA ================= */

        $tabla .= '<div class="table-responsive">
            <table class="table table-dark table-sm">
            <thead>
                <tr class="text-center">
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th>Origen</th>
                    <th>' . mainModel::link_orden_tabla($url, 'fecha', 'Fecha', $orden, $direccion, 'presupuesto_servicio_orden', 'presupuesto_servicio_direccion') . '</th>
                    <th>Total</th>
                    <th>Creado por</th>
                    <th>' . mainModel::link_orden_tabla($url, 'estado', 'Estado', $orden, $direccion, 'presupuesto_servicio_orden', 'presupuesto_servicio_direccion') . '</th>
                    <th>PDF</th>';

        if ($mostrarAcciones) {
            $tabla .= '<th>Acciones</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            foreach ($datos as $rows) {

                $estadoMap = [
                    1 => ['Pendiente', 'warning'],
                    2 => ['Aprobado', 'success'],
                    3 => ['OT generada', 'primary'],
                    4 => ['Facturado', 'info'],
                    5 => ['Convertido', 'secondary'],
                    0 => ['Anulado', 'danger']
                ];

                $estado = $estadoMap[$rows['estadoPre']] ?? ['Desconocido', 'secondary'];
                $origenTexto = (($rows['origen'] ?? 'DIAGNOSTICO') === 'PRELIMINAR') ? 'Preliminar' : 'Diagnostico';
                $origenBadge = (($rows['origen'] ?? 'DIAGNOSTICO') === 'PRELIMINAR') ? 'info' : 'secondary';

                $tabla .= '<tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $rows['nombre_cliente'] . ' ' . $rows['apellido_cliente'] . '</td>
                <td>' . $rows['modelo'] . ' ' . $rows['placa'] . '</td>
                <td><span class="badge bg-' . $origenBadge . '">' . $origenTexto . '</span></td>
                <td>' . date("d-m-Y", strtotime($rows['fecha'])) . '</td>
                <td>' . number_format($rows['total_final'], 0, ',', '.') . '</td>
                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                <td><span class="badge bg-' . $estado[1] . '">' . $estado[0] . '</span></td>
                <td>
                    <a href="' . SERVERURL . 'pdf/presupuesto_servicio.php?id=' . mainModel::encryption($rows['idpresupuesto_servicio']) . '"
                        target="_blank"
                        class="btn btn-info btn-sm">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </td>';

                if ($mostrarAcciones) {

                    $tabla .= '<td><div style="display:flex; gap:6px; justify-content:center;">';

                    if ($puedeAprobar && $rows['estadoPre'] == 1 && ($rows['origen'] ?? 'DIAGNOSTICO') !== 'PRELIMINAR') {
                        $tabla .= '
                    <form class="FormularioAjax d-inline"
                        action="' . SERVERURL . 'ajax/presupuestoServicioAjax.php"
                        method="POST" data-form="update">
                        <input type="hidden" name="accion" value="aprobar">
                        <input type="hidden" name="id" value="' . mainModel::encryption($rows['idpresupuesto_servicio']) . '">
                        <button class="btn btn-success btn-sm">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>';
                    }

                    if ($puedeAnular && ($rows['estadoPre'] == 1 || $rows['estadoPre'] == 2)) {
                        $tabla .= '
                    <form class="FormularioAjax d-inline"
                        action="' . SERVERURL . 'ajax/presupuestoServicioAjax.php"
                        method="POST" data-form="delete" data-anulacion="true" data-anulacion-titulo="Anular presupuesto de servicio">
                        <input type="hidden" name="accion" value="anular">
                        <input type="hidden" name="id" value="' . mainModel::encryption($rows['idpresupuesto_servicio']) . '">
                        <button class="btn btn-warning btn-sm">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </form>';
                    }

                    $tabla .= '</div></td>';
                }

                $tabla .= '</tr>';
                $contador++;
            }

            $reg_final = $contador - 1;
        } else {
            $colspan = $mostrarAcciones ? 10 : 9;
            $tabla .= '<tr><td colspan="' . $colspan . '">No hay registros en el sistema</td></tr>';
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

    public function aprobar_presupuesto_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.presupuesto.aprobar')) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto' => 'No tiene permiso para aprobar presupuestos',
                'Tipo' => 'error'
            ]);
        }

        if (!isset($_POST['id'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto' => 'ID inválido',
                'Tipo' => 'error'
            ]);
        }

        $id = mainModel::decryption($_POST['id']);
        $sucursalPresupuesto = mainModel::ejecutar_consulta_simple("
            SELECT ps.id_sucursal 
            FROM presupuesto_servicio ps          
            WHERE ps.idpresupuesto_servicio = '$id'
        ")->fetchColumn();

        if ($sucursalPresupuesto != $_SESSION['nick_sucursal']) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto'  => 'No puede operar presupuestos de otra sucursal',
                'Tipo'   => 'error'
            ]);
        }

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
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.presupuesto.anular')) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto' => 'No tiene permiso para anular presupuestos',
                'Tipo' => 'error'
            ]);
        }

        $id = mainModel::decryption($_POST['id']);
        $motivo = trim(mainModel::limpiar_string($_POST['observacion_anulacion'] ?? ''));

        if ($motivo === '') {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Motivo requerido',
                'Texto' => 'Debe ingresar la observacion o motivo de anulacion',
                'Tipo' => 'warning'
            ]);
        }

        $res = presupuestoServicioModelo::anular_presupuesto_modelo($id, $_SESSION['id_str'], $motivo);

        if (isset($res['error'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto' => $res['msg'],
                'Tipo' => 'error'
            ]);
        }

        // 🔥 validación de sucursal AQUÍ
        if ($res['data']['id_sucursal'] != $_SESSION['nick_sucursal']) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto' => 'Otra sucursal',
                'Tipo' => 'error'
            ]);
        }

        return json_encode([
            'Alerta' => 'recargar',
            'Titulo' => 'Presupuesto anulado',
            'Texto' => 'Correcto',
            'Tipo' => 'success'
        ]);
    }

    public function datos_presupuesto_controlador($id)
    {
        return [
            'cabecera' => self::obtener_presupuesto_cabecera($id),
            'detalle'  => self::obtener_presupuesto_detalle($id),
            'promociones' => self::obtener_presupuesto_promociones($id)
        ];
    }
}
