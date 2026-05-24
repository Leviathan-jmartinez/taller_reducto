<?php
require_once __DIR__ . "/../modelos/presupuestoServicioModelo.php";

class presupuestoServicioControlador  extends presupuestoServicioModelo
{

    public function datos_diagnostico_controlador()
    {
        $id = $_POST['id_diagnostico'] ?? null;

        $data = presupuestoServicioModelo::datos_diagnostico_modelo($id);

        return json_encode($data);
    }

    public function buscar_diagnostico_controlador()
    {
        $texto = $_POST['buscar_diagnostico'] ?? '';

        $sql = mainModel::conectar()->prepare("
        SELECT 
            d.id_diagnostico,
            c.nombre_cliente,
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
            OR v.placa LIKE :b
            OR ma.mar_descri LIKE :b
            OR m.mod_descri LIKE :b
        )
        ORDER BY d.id_diagnostico DESC
        ");

        $sql->bindValue(":b", "%$texto%");
        $sql->execute();

        $html = '<table class="table table-sm">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Vehiculo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>';

        foreach ($sql->fetchAll() as $row) {
            $cliente = trim($row['nombre_cliente'] . ' ' . $row['apellido_cliente']);
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

        if ($txt === '') {
            return '';
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

        $detalle = json_decode($_POST['detalle_json'], true);
        $descuentos = json_decode($_POST['descuentos_json'], true);

        if (empty($detalle)) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto' => 'Debe agregar al menos un servicio',
                'Tipo' => 'error'
            ]);
        }

        $datos = [
            'usuario'         => $_SESSION['id_str'],
            'id_diagnostico'  => $_POST['id_diagnostico'],
            'fecha_venc'      => $_POST['fecha_venc'],
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
                    0 => ['Anulado', 'danger']
                ];

                $estado = $estadoMap[$rows['estadoPre']] ?? ['Desconocido', 'secondary'];

                $tabla .= '<tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $rows['nombre_cliente'] . ' ' . $rows['apellido_cliente'] . '</td>
                <td>' . $rows['modelo'] . ' ' . $rows['placa'] . '</td>
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

                    if ($puedeAprobar && $rows['estadoPre'] == 1) {
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
                        method="POST" data-form="delete">
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
            $colspan = $mostrarAcciones ? 9 : 8;
            $tabla .= '<tr><td colspan="9">No hay registros en el sistema</td></tr>';
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

        $res = presupuestoServicioModelo::anular_presupuesto_modelo($id);

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
            'detalle'  => self::obtener_presupuesto_detalle($id)
        ];
    }
}
