<?php
if ($peticionAjax) {
    require_once "../modelos/salidaInsumoModelo.php";
} else {
    require_once "./modelos/salidaInsumoModelo.php";
}

class salidaInsumoControlador extends salidaInsumoModelo
{
    public function buscar_consumible_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.insumo.crear')) {
            return '<div class="alert alert-danger">Acceso no autorizado</div>';
        }

        $texto = trim($_POST['texto'] ?? '');

        $datos = self::buscar_consumible_modelo($texto);

        if (empty($datos)) {
            return '<div class="alert alert-warning">Sin resultados</div>';
        }

        $html = '
        <table class="table table-dark table-sm">
            <thead>
                <tr>
                    <th>Insumo</th>
                    <th>Stock</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>';

        foreach ($datos as $row) {
            $desc = htmlspecialchars($row['desc_articulo'], ENT_QUOTES, 'UTF-8');
            $stock = (float)$row['stockDisponible'];

            $html .= '
                <tr>
                    <td>' . $desc . '</td>
                    <td class="text-center">' . $stock . '</td>
                    <td class="text-center">
                        <button type="button"
                            class="btn btn-success btn-sm"
                            onclick="agregarConsumible(
                                ' . $row['id_articulo'] . ',
                                \'' . $desc . '\',
                                ' . $stock . '
                            )">
                            Agregar
                        </button>
                    </td>
                </tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    public function registrar_salida_insumo_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.insumo.crear')) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto'  => 'No tiene permiso para registrar salida de insumos',
                'Tipo'   => 'error'
            ]);
        }

        $idUsuario  = $_SESSION['id_str'] ?? null;
        $idSucursal = $_SESSION['nick_sucursal'] ?? null;

        if (!$idUsuario || !$idSucursal) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Sesión inválida',
                'Tipo'   => 'error'
            ]);
        }

        if (empty($_POST['idempleado'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Debe seleccionar un empleado',
                'Tipo'   => 'error'
            ]);
        }

        if (empty($_POST['consumibles_json'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Debe cargar al menos un insumo',
                'Tipo'   => 'error'
            ]);
        }

        $datos = [
            'id_sucursal'       => $idSucursal,
            'usuario'           => $idUsuario,
            'observacion'       => $_POST['observacion'] ?? '',
            'idempleado' => (int)$_POST['idempleado'],
            'consumibles_json'  => $_POST['consumibles_json']
        ];

        $res = self::registrar_salida_insumo_modelo($datos);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'recargar',
                'Titulo' => 'Salida registrada',
                'Texto'  => 'La salida de insumos fue registrada correctamente',
                'Tipo'   => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto'  => $res['msg'] ?? 'No se pudo registrar la salida',
            'Tipo'   => 'error'
        ]);
    }

    public function anular_salida_insumo_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.insumo.anular')) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto'  => 'No tiene permiso para anular salidas de insumos',
                'Tipo'   => 'error'
            ]);
        }

        $idUsuario  = $_SESSION['id_str'] ?? null;
        $idSucursal = $_SESSION['nick_sucursal'] ?? null;

        if (!$idUsuario || !$idSucursal) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Sesión inválida',
                'Tipo'   => 'error'
            ]);
        }

        if (empty($_POST['id_salida'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Salida inválida',
                'Tipo'   => 'error'
            ]);
        }

        $idSalida = mainModel::decryption($_POST['id_salida']);

        if (!$idSalida) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'ID de salida inválido',
                'Tipo'   => 'error'
            ]);
        }

        $datos = [
            'idsalida_insumo' => $idSalida,
            'usuario'             => $idUsuario,
            'id_sucursal'         => $idSucursal
        ];

        $res = self::anular_salida_insumo_modelo($datos);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'redireccionar_confirmado',
                'URL'    => SERVERURL . 'registro-insumos-buscar/',
                'Titulo' => 'Salida anulada',
                'Texto'  => 'La salida fue anulada y el stock fue devuelto correctamente',
                'Tipo'   => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto'  => $res['msg'] ?? 'No se pudo anular la salida',
            'Tipo'   => 'error'
        ]);
    }

    public function paginador_salida_insumo_controlador($pagina,$registros, $url, $fecha_inicio, $fecha_final, $nro_salida = '', $empleado = '', $estado_filtro = '', $orden = 'fecha', $direccion = 'DESC'
    ) {
        $pagina = (int) mainModel::limpiar_string($pagina);
        $registros = (int) mainModel::limpiar_string($registros);
        $nro_salida = mainModel::limpiar_string($nro_salida);
        $empleado = mainModel::limpiar_string($empleado);
        $estado_filtro = mainModel::limpiar_string($estado_filtro);
        $orden = mainModel::limpiar_string($orden);
        $direccion = strtoupper(mainModel::limpiar_string($direccion));

        $url = SERVERURL . $url . "/";
        $pagina = ($pagina > 0) ? $pagina : 1;
        $inicio = ($pagina - 1) * $registros;

        $filtros = [
            [
                "campo" => "sc.idsalida_insumo",
                "tipo"  => "=",
                "valor" => $nro_salida
            ],
            [
                "campo" => "CONCAT(e.nombre,' ',e.apellido) OR e.nro_cedula",
                "tipo"  => "LIKE",
                "valor" => $empleado
            ],
            [
                "campo" => "sc.fecha",
                "tipo"  => "DATE_RANGE",
                "desde" => $fecha_inicio,
                "hasta" => $fecha_final
            ],
            [
                "campo" => "sc.estado",
                "tipo"  => "=",
                "valor" => $estado_filtro
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
            'fecha'  => 'sc.fecha',
            'estado' => 'sc.estado'
        ];

        $ordenamiento = mainModel::preparar_ordenamiento(
            $orden,
            $direccion,
            $columnasOrdenSql,
            'fecha',
            'DESC'
        );

        $res = self::listar_salida_insumo_modelo(
            $inicio,
            $registros,
            $filtrosSQL,
            "ORDER BY " . $ordenamiento['sql'] . ", sc.idsalida_insumo DESC"
        );

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        $contador = $inicio + 1;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        $puedeAnular = mainModel::tienePermiso('servicio.insumo.anular');

        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>Nro.</th>
                <th>' . mainModel::link_orden_tabla($url, 'fecha', 'Fecha', $ordenamiento['orden'], $ordenamiento['direccion'], 'salida_insumo_orden', 'salida_insumo_direccion') . '</th>
                <th>Empleado</th>
                <th>Registrado por</th>
                <th>Observación</th>
                <th>' . mainModel::link_orden_tabla($url, 'estado', 'Estado', $ordenamiento['orden'], $ordenamiento['direccion'], 'salida_insumo_orden', 'salida_insumo_direccion') . '</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {
            foreach ($datos as $row) {
                $estado = ((int)$row['estado'] === 1)
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-secondary">Anulado</span>';

                $tabla .= '
            <tr class="text-center">
                <td>' . $contador . '</td>
                <td>#' . (int)$row['idsalida_insumo'] . '</td>
                <td>' . date("d/m/Y H:i", strtotime($row['fecha'])) . '</td>
                <td>' . htmlspecialchars($row['empleado'], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($row['usuario_registra'], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($row['observacion'] ?: '-', ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . $estado . '</td>
                <td>';

                if ($puedeAnular && (int)$row['estado'] === 1) {
                    $tabla .= '
                <form class="FormularioAjax d-inline"
                    action="' . SERVERURL . 'ajax/salidaInsumoAjax.php"
                    method="POST"
                    data-form="delete">

                    <input type="hidden" name="accion" value="anular">
                    <input type="hidden" name="id_salida" value="' . mainModel::encryption($row['idsalida_insumo']) . '">

                    <button class="btn btn-danger btn-sm">
                        <i class="fas fa-ban"></i>
                    </button>
                </form>';
                } else {
                    $tabla .= '
                <button class="btn btn-secondary btn-sm" disabled>
                    <i class="fas fa-ban"></i>
                </button>';
                }

                $tabla .= '</td></tr>';

                $contador++;
            }

            $reg_final = $contador - 1;
        } else {
            $tabla .= '
        <tr class="text-center">
            <td colspan="8">No hay registros en el sistema</td>
        </tr>';
        }

        $tabla .= '</tbody></table></div>';

        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '<p class="text-right">
            Mostrando ' . $reg_inicio . ' al ' . $reg_final . ' de ' . $total . '
        </p>';

            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        return $tabla;
    }

    public function buscar_empleado_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.insumo.crear')) {
            return '<div class="alert alert-danger">Acceso no autorizado</div>';
        }

        $texto = trim($_POST['texto'] ?? '');

        $datos = self::buscar_empleado_modelo($texto);

        if (!$datos) {
            return '<div class="alert alert-warning">Sin resultados</div>';
        }

        $html = '
        <table class="table table-dark table-sm">
            <thead>
                <tr>
                    <th>Cédula</th>
                    <th>Empleado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>';

        foreach ($datos as $row) {

            $nombre = htmlspecialchars(
                $row['nombre'] . ' ' . $row['apellido'],
                ENT_QUOTES,
                'UTF-8'
            );

            $html .= '
            <tr>
                <td>' . $row['nro_cedula'] . '</td>

                <td>' . $nombre . '</td>

                <td class="text-center">
                    <button type="button"
                        class="btn btn-success btn-sm"
                        onclick="seleccionarEmpleado(
                            ' . $row['idempleados'] . ',
                            \'' . $nombre . '\'
                        )">
                        Seleccionar
                    </button>
                </td>
            </tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }
}
