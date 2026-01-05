<?php
if ($peticionAjax) {
    require_once "../modelos/registroServicioModelo.php";
} else {
    require_once "./modelos/registroServicioModelo.php";
}

class registroServicioControlador extends registroServicioModelo
{
    public function registrar_servicio_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (
            empty($_POST['idorden_trabajo']) ||
            empty($_POST['fecha_ejecucion'])
        ) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Datos incompletos',
                'Tipo'   => 'error'
            ]);
        }

        /* ================= DATOS CRÍTICOS DE SESIÓN ================= */
        $idUsuario  = $_SESSION['id_str'];
        $idSucursal = $_SESSION['nick_sucursal'];

        if (!$idUsuario || !$idSucursal) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Sesión inválida. Vuelva a iniciar sesión',
                'Tipo'   => 'error'
            ]);
        }

        /* ================= DESENCRIPTAR OT ================= */
        $idOT = mainModel::decryption($_POST['idorden_trabajo']);

        if (!$idOT) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Orden de trabajo inválida',
                'Tipo'   => 'error'
            ]);
        }

        /* ================= ARMAR DATA ================= */
        $datos = [
            'idorden_trabajo' => $idOT,
            'fecha_ejecucion' => $_POST['fecha_ejecucion'],
            'observacion'     => $_POST['observacion'] ?? '',
            'usuario'         => $idUsuario,
            'updatedby'       => $idUsuario,
            'ip'              => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent'      => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];

        /* ================= EJECUTAR ================= */
        $res = self::registrar_servicio_modelo($datos);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'recargar',
                'Titulo' => 'Servicio registrado',
                'Texto'  => 'La orden de trabajo fue cerrada correctamente',
                'Tipo'   => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto'  => $res['msg'] ?? 'No se pudo registrar el servicio',
            'Tipo'   => 'error'
        ]);
    }

    /* ================= BUSCAR OT ================= */
    public function buscar_ot_para_registro_controlador()
    {
        $texto = trim($_POST['buscar_ot'] ?? '');

        $datos = self::buscar_ot_para_registro_modelo($texto);

        if (!$datos) {
            return '<div class="alert alert-warning">No se encontraron órdenes</div>';
        }

        $html = '<table class="table table-hover table-sm">
        <thead>
            <tr>
                <th>OT</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th></th>
            </tr>
        </thead><tbody>';

        foreach ($datos as $ot) {
            $html .= '
        <tr>
            <td>#' . $ot['idorden_trabajo'] . '</td>
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

    public function paginador_registro_servicio_controlador($pagina, $registros, $privilegio, $url, $busqueda1, $busqueda2)
    {
        $pagina    = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $url       = SERVERURL . mainModel::limpiar_string($url) . "/";

        $pagina = ($pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina * $registros) - $registros;

        $consulta = registroServicioModelo::paginador_registro_servicio_modelo(
            $inicio,
            $registros,
            $busqueda1,
            $busqueda2
        );

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta)->fetchAll();
        $total = (int)$conexion->query("SELECT FOUND_ROWS()")->fetchColumn();
        $Npaginas = ceil($total / $registros);

        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>OT</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Fecha ejecución</th>
                <th>Registrado por</th>
                <th>Estado</th>
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
                        $estado = '<span class="badge badge-danger">Anulado</span>';
                        break;
                    default:
                        $estado = '<span class="badge badge-secondary">?</span>';
                }

                $tabla .= '
        <tr class="text-center">
            <td>' . $contador . '</td>
            <td>#' . $row['idorden_trabajo'] . '</td>
            <td>' . $row['nombre_cliente'] . ' ' . $row['apellido_cliente'] . '</td>
            <td>' . $row['mod_descri'] . ' ' . $row['placa'] . '</td>
            <td>' . date("d-m-Y", strtotime($row['fecha_ejecucion'])) . '</td>
            <td>' . $row['usuario_registra'] . '</td>
            <td>' . $estado . '</td>
            <td>
                <a href="' . SERVERURL . 'pdf/registroServicio.php?id=' .
                    mainModel::encryption($row['idregistro_servicio']) . '"
                    target="_blank"
                    class="btn btn-info btn-sm"
                    title="Imprimir registro">
                    <i class="fas fa-print"></i>
                </a>';

                if ($row['estado'] == 1) {
                    $tabla .= '
                <form class="FormularioAjax d-inline"
                    action="' . SERVERURL . 'ajax/registroServicioAjax.php"
                    method="POST"
                    data-form="delete"
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
            </td>
        </tr>';

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

        $idRegistro = mainModel::decryption($_POST['id_registro']);

        if (!$idRegistro) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Registro inválido',
                'Tipo'   => 'error'
            ]);
        }

        $datos = [
            'idregistro_servicio' => $idRegistro,
            'usuario'             => $idUsuario,
            'id_sucursal'         => $idSucursal
        ];

        $res = self::anular_registro_servicio_modelo($datos);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'recargar',
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
