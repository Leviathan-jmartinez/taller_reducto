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
            v.placa
        FROM diagnostico_servicio d
        INNER JOIN recepcion_servicio r ON r.idrecepcion = d.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        WHERE d.estado = 1
        AND (
            c.nombre_cliente LIKE :b OR v.placa LIKE :b
        )
        ORDER BY d.id_diagnostico DESC
        ");

        $sql->bindValue(":b", "%$texto%");
        $sql->execute();

        $html = '<table class="table"><tbody>';

        foreach ($sql->fetchAll() as $row) {

            $html .= "
        <tr>
            <td>{$row['nombre_cliente']}</td>
            <td>{$row['placa']}</td>
            <td>
                <button class='btn btn-success btn-sm'
                    onclick=\"seleccionarDiagnostico(
                        {$row['id_diagnostico']},
                        '{$row['nombre_cliente']} - {$row['placa']}'
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
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
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

    public function paginador_presupuestoservi_controlador($pagina, $registros, $url, $busqueda1, $busqueda2)
    {

        $pagina    = (int) mainModel::limpiar_string($pagina);
        $registros = (int) mainModel::limpiar_string($registros);
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

        /* ================= DATOS ================= */

        $res = presupuestoServicioModelo::listar_presupuestos_modelo($inicio, $registros, $filtrosSQL);

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
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Creado por</th>
                    <th>Estado</th>
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

                    if ($puedeGenerarOT && $rows['estadoPre'] == 2) {
                        $tabla .= '
                    <form class="FormularioAjax d-inline"
                        action="' . SERVERURL . 'ajax/ordenTrabajoAjax.php"
                        method="POST" data-form="save">
                        <input type="hidden" name="accion" value="generar_ot">
                        <input type="hidden" name="id" value="' . mainModel::encryption($rows['idpresupuesto_servicio']) . '">
                        <button class="btn btn-primary btn-sm">
                            <i class="fas fa-tools"></i>
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
            SELECT rs.id_sucursal 
            FROM presupuesto_servicio ps          
            INNER JOIN diagnostico_servicio d on d.id_diagnostico = ps.id_diagnostico 
            INNER JOIN recepcion_servicio rs on rs.idrecepcion = d.idrecepcion 
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
