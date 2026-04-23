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

    public function guardar_diagnostico_controlador()
    {
        session_start(['name' => 'STR']);

        if (empty($_POST['idrecepcion']) || empty($_POST['fecha'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Datos incompletos",
                "Texto"  => "Debe seleccionar una recepción y fecha",
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

        if (empty($_POST['id_sucursal'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo obtener la sucursal",
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
        $fecha = str_replace("T", " ", $_POST['fecha']);

        /* ================= DETALLES ================= */

        $datos = [
            "idrecepcion" => $_POST['idrecepcion'],
            "id_usuario"  => $_SESSION['id_str'],
            "id_equipo"   => $_POST['id_equipo'],
            "id_sucursal" => $_POST['id_sucursal'],
            "fecha" => $fecha,
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

            return json_encode([
                "Alerta" => "limpiar",
                "Titulo" => "Diagnóstico registrado",
                "Texto"  => "Se guardó correctamente",
                "Tipo"   => "success",
                "id_diagnostico" => $guardar['id_diagnostico']
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto"  => $guardar['msg'] ?? "Error desconocido",
            "Tipo"   => "error"
        ]);
    }

    public function paginador_diagnostico_controlador($pagina, $registros, $url, $busqueda1, $busqueda2, $cliente = '', $placa = '')
    {
        $pagina    = (int) mainModel::limpiar_string($pagina);
        $registros = (int) mainModel::limpiar_string($registros);
        $cliente   = mainModel::limpiar_string($cliente);
        $placa     = mainModel::limpiar_string($placa);

        $url = SERVERURL . $url . "/";
        $tabla = "";

        $pagina = ($pagina > 0) ? $pagina : 1;
        $inicio = ($pagina - 1) * $registros;

        /* ================= FILTROS ================= */

        $filtros = [
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
            ]
        ];

        $filtrosSQL = mainModel::construirFiltros($filtros);

        /* ================= DATOS ================= */

        $res = diagnosticoModelo::listar_diagnosticos_modelo($inicio, $registros, $filtrosSQL);

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
            <th>#</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Vehículo</th>
            <th>Usuario</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            foreach ($datos as $rows) {

                $estadoMap = [
                    1 => ['En proceso', 'info'],
                    2 => ['Presupuestado', 'success'],
                    3 => ['Finalizado', 'primary'],
                    0 => ['Anulado', 'warning']
                ];

                $estado = $estadoMap[$rows['estado']] ?? ['Pendiente', 'secondary'];

                $tabla .= '<tr class="text-center">
            <td>' . $contador . '</td>
            <td>' . date("d/m/Y H:i", strtotime($rows['fecha_diagnostico'])) . '</td>
            <td>' . $rows['cliente'] . '</td>
            <td>' . $rows['placa'] . '</td>
            <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
            <td><span class="badge bg-' . $estado[1] . '">' . $estado[0] . '</span></td>
            <td>';

                /* ================= BOTÓN INTELIGENTE ================= */

                $tabla .= '
                    <button class="btn btn-info btn-sm mr-1"
                    onclick="evaluarDiagnostico(
                        ' . $rows['id_diagnostico'] . ',
                        ' . ($rows['origen'] == 'RECLAMO' ? 1 : 0) . ',
                        ' . ($rows['es_garantia'] ?? 0) . ',
                        ' . ($rows['requiere_cobro'] ?? 0) . ',
                        ' . ($rows['idreclamo_servicio'] !== null ? $rows['idreclamo_servicio'] : 'null') . '
                    )">
                        <i class="fas fa-cogs"></i>
                    </button>';

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
                        data-form="delete">

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
        <td colspan="7">No hay registros en el sistema</td>
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
        if (empty($_POST['id_diagnostico'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "ID inválido",
                "Tipo" => "error"
            ]);
        }

        $id = $_POST['id_diagnostico'];

        $resp = diagnosticoModelo::anular_diagnostico_modelo($id);

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

        return json_encode($data ?: []);
    }
}
