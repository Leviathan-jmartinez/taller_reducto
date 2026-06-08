<?php
if ($peticionAjax) {
    require_once "../modelos/registroServicioModelo.php";
} else {
    require_once "./modelos/registroServicioModelo.php";
}
require_once __DIR__ . '/../config/APP.php';
class registroServicioControlador extends registroServicioModelo
{
    public function registrar_servicio_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }
        if (!mainModel::tienePermiso('servicio.registro.crear')) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto'  => 'No tiene permiso para registrar servicios',
                'Tipo'   => 'error'
            ]);
        }
        if (
            empty($_POST['idorden_trabajo']) ||
            empty($_POST['fecha_servicio'])
        ) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Datos incompletos',
                'Tipo'   => 'error'
            ]);
        }

        $idUsuario  = $_SESSION['id_str'];
        $idSucursal = $_SESSION['nick_sucursal'];

        if (!$idUsuario || !$idSucursal) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Sesión inválida',
                'Tipo'   => 'error'
            ]);
        }

        $idOT = mainModel::decryption($_POST['idorden_trabajo']);

        if (!$idOT) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Orden inválida',
                'Tipo'   => 'error'
            ]);
        }

        /* ================= VALIDAR ESTADO OT ================= */
        $estado = registroServicioModelo::estado_ot_modelo($idOT);

        if ($estado != 1) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'La OT no está disponible para registro',
                'Tipo'   => 'error'
            ]);
        }

        $datos = [
            'idorden_trabajo' => $idOT,
            'fecha_servicio'      => $_POST['fecha_servicio'],
            'kilometraje_salida'  => $_POST['kilometraje_salida'] ?? null,
            'observacion'     => $_POST['observacion'] ?? '',
            'usuario'         => $idUsuario,
            'updatedby'       => $idUsuario
        ];

        $res = registroServicioModelo::registrar_servicio_modelo($datos);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'limpiar',
                'Titulo' => 'Servicio registrado',
                'Texto'  => 'La orden fue cerrada correctamente',
                'Tipo'   => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto'  => $res['msg'] ?? 'Error al registrar',
            'Tipo'   => 'error'
        ]);
    }

    /* ================= BUSCAR OT ================= */
    public function buscar_ot_para_registro_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $texto = trim($_POST['buscar_ot'] ?? '');

        $datos = self::buscar_ot_para_registro_modelo($texto);

        if (!$datos) {
            return '<div class="alert alert-warning">No se encontraron órdenes</div>';
        }

        $html = '<table class="table table-dark table-sm">
        <thead>
            <tr>
                <th>OT</th>
                <th>Número de documento</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th></th>
            </tr>
        </thead><tbody>';

        foreach ($datos as $ot) {
            $html .= '
        <tr>
            <td>#' . $ot['idorden_trabajo'] . '</td>
            <td>' . $ot['doc_number'] . '</td>
            <td>' . $ot['nombre_cliente'] . ' ' . $ot['apellido_cliente'] . '</td>
            <td>' . $ot['mod_descri'] . ' ' . $ot['placa'] . '</td>
            <td class="text-center">
                <button class="btn btn-success btn-sm"
                    onclick="seleccionarOT(\'' . mainModel::encryption($ot['idorden_trabajo']) . '\')">
                    Seleccionar
                </button>
            </td>
        </tr>';
        }

        return $html . '</tbody></table>';
    }

    /* ================= CARGAR OT ================= */
    public function cargar_ot_para_registro_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $idOT = mainModel::decryption($_POST['id_ot']);

        $ot = self::obtener_ot_para_registro_modelo($idOT);
        if (!$ot) {
            return json_encode(['error' => true]);
        }

        $detalle = self::detalle_ot_para_registro_modelo($idOT);

        return json_encode([
            'ot'      => $ot,
            'detalle' => $detalle
        ]);
    }

    public function detalle_registro_servicio_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.registro.ver')) {
            return json_encode([
                'error' => true,
                'msg' => 'Acceso denegado'
            ]);
        }

        $idSucursal = $_SESSION['nick_sucursal'] ?? null;
        $idRegistro = mainModel::decryption($_POST['id_registro'] ?? '');

        if (!$idSucursal || !$idRegistro) {
            return json_encode([
                'error' => true,
                'msg' => 'Registro invalido'
            ]);
        }

        $datos = self::detalle_registro_servicio_modelo($idRegistro, $idSucursal);

        if (!$datos) {
            return json_encode([
                'error' => true,
                'msg' => 'No se encontro el registro'
            ]);
        }

        return json_encode($datos, JSON_UNESCAPED_UNICODE);
    }

    public function listar_registro_servicio_controlador($pagina, $registros, $url, $busqueda1, $busqueda2, $orden = 'fecha', $direccion = 'DESC')
    {
        $pagina    = (int) mainModel::limpiar_string($pagina);
        $registros = (int) mainModel::limpiar_string($registros);
        $url       = SERVERURL . mainModel::limpiar_string($url) . "/";
        $orden = mainModel::limpiar_string($orden);
        $direccion = strtoupper(mainModel::limpiar_string($direccion));
        $estadoFiltro = $_SESSION['estado_regSer'] ?? '';
        $pagina = ($pagina > 0) ? $pagina : 1;
        $inicio = ($pagina - 1) * $registros;

        $filtros = [];

        if (!empty($busqueda1) && !empty($busqueda2)) {
            $filtros[] = [
                "campo" => "rs.fecha_servicio",
                "tipo"  => "DATE_RANGE",
                "desde" => $busqueda1,
                "hasta" => $busqueda2
            ];
        }

        if ($estadoFiltro !== '') {
            $filtros[] = [
                "campo" => "rs.estado",
                "tipo"  => "=",
                "valor" => $estadoFiltro
            ];
        }

        $filtrosSQL = mainModel::construirFiltros($filtros);
        $columnasOrdenSql = [
            'fecha' => 'rs.fecha_servicio',
            'estado' => 'rs.estado'
        ];
        $ordenamiento = mainModel::preparar_ordenamiento($orden, $direccion, $columnasOrdenSql, 'fecha', 'DESC');
        $orden = $ordenamiento['orden'];
        $direccion = $ordenamiento['direccion'];

        $res = registroServicioModelo::listar_registro_servicio_modelo(
            $inicio,
            $registros,
            $filtrosSQL,
            "ORDER BY " . $ordenamiento['sql'] . ", rs.idregistro_servicio DESC"
        );

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>OT</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>' . mainModel::link_orden_tabla($url, 'fecha', 'Fecha Servicio', $orden, $direccion, 'registro_servicio_orden', 'registro_servicio_direccion') . '</th>
                <th>Registrado por</th>
                <th>' . mainModel::link_orden_tabla($url, 'estado', 'Estado', $orden, $direccion, 'registro_servicio_orden', 'registro_servicio_direccion') . '</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>';

        if ($total >= 1) {
            $contador = $inicio + 1;

            foreach ($datos as $row) {

                switch ($row['estado']) {
                    case 1:
                        $estado = '<span class="badge badge-success">Registrado</span>';
                        break;
                    case 2:
                        $estado = '<span class="badge badge-primary">Facturado</span>';
                        break;
                    case 0:
                        $estado = '<span class="badge badge-secondary">Anulado</span>';
                        break;
                    case 3:
                        $estado = '<span class="badge badge-warning">Con Reclamo</span>';
                        break;
                    default:
                        $estado = '<span class="badge badge-default">?</span>';
                }

                $tabla .= '
        <tr class="text-center">
            <td>' . $contador . '</td>
            <td>#' . $row['idorden_trabajo'] . '</td>
            <td>' . $row['nombre_cliente'] . ' ' . $row['apellido_cliente'] . '</td>
            <td>' . $row['mod_descri'] . ' ' . $row['placa'] . '</td>
            <td>' . date("d-m-Y", strtotime($row['fecha_servicio'])) . '</td>
            <td>' . $row['nombre_usuario'] . '</td>
            <td>' . $estado . '</td>
            <td>';
                $tabla .= '
                <button type="button"
                    class="btn btn-info btn-sm mr-1"
                    title="Ver detalle"
                    onclick="verDetalleRegistroServicio(\'' . mainModel::encryption($row['idregistro_servicio']) . '\')">
                    <i class="fas fa-eye"></i>
                </button>';
                if (mainModel::tienePermiso('servicio.registro.anular') && $row['estado'] == 1) {
                    $tabla .= '
                <form class="FormularioAjax d-inline"
                    action="' . SERVERURL . 'ajax/registroServicioAjax.php"
                    method="POST"
                    data-form="delete"
                    data-anulacion="true"
                    data-anulacion-titulo="Anular registro de servicio"
                    autocomplete="off">

                    <input type="hidden" name="accion" value="anular">
                    <input type="hidden" name="id_registro"
                        value="' . mainModel::encryption($row['idregistro_servicio']) . '">

                    <button type="submit"
                        class="btn btn-danger btn-sm"
                        title="Anular registro">
                        <i class="fas fa-ban"></i>
                    </button>
                </form>';
                }
                $tabla .= '
            </td>';
                $tabla .= '</tr>';

                $contador++;
            }
        } else {
            $tabla .= '
        <tr>
            <td colspan="8" class="text-center">Sin registros</td>
        </tr>';
        }

        $tabla .= '</tbody></table></div>';

        if ($total >= 1) {
            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        return $tabla;
    }


    public function anular_registro_servicio_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
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

        if (empty($_POST['id_registro'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'ID inválido',
                'Tipo'   => 'error'
            ]);
        }

        if (!mainModel::tienePermiso('servicio.registro.anular')) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto'  => 'No tiene permiso para anular registros',
                'Tipo'   => 'error'
            ]);
        }

        $idRegistro = mainModel::decryption($_POST['id_registro']);
        $motivo = trim(mainModel::limpiar_string($_POST['observacion_anulacion'] ?? ''));

        if (!$idRegistro) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Registro inválido',
                'Tipo'   => 'error'
            ]);
        }

        if ($motivo === '') {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Motivo requerido',
                'Texto'  => 'Debe ingresar la observacion o motivo de anulacion',
                'Tipo'   => 'warning'
            ]);
        }

        $datos = [
            'idregistro_servicio' => $idRegistro,
            'usuario'             => $idUsuario,
            'id_sucursal'         => $idSucursal,
            'motivo'              => $motivo
        ];

        $res = self::anular_registro_servicio_modelo($datos);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'redireccionar_confirmado',
                'URL' => SERVERURL . 'registro-servicio-buscar/',
                'Titulo' => 'Registro anulado',
                'Texto'  => 'El servicio fue anulado y el stock revertido',
                'Tipo'   => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto'  => $res['msg'] ?? 'No se pudo anular',
            'Tipo'   => 'error'
        ]);
    }
}
