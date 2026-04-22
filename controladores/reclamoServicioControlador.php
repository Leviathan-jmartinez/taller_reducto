<?php
if ($peticionAjax) {
    require_once "../modelos/reclamoServicioModelo.php";
} else {
    require_once "./modelos/reclamoServicioModelo.php";
}

class reclamoServicioControlador extends reclamoServicioModelo
{
    public function registrar_reclamo_controlador()
    {
        session_start(['name' => 'STR']);

        if (
            empty($_POST['idregistro_servicio']) ||
            empty($_POST['descripcion'])
        ) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Datos incompletos',
                'Tipo'   => 'error'
            ]);
        }

        $idRegistro = mainModel::decryption($_POST['idregistro_servicio']);

        /* 🔥 OBTENER SUCURSAL */
        $idSucursal = mainModel::ejecutar_consulta_simple("
        SELECT id_sucursal 
        FROM registro_servicio
        WHERE idregistro_servicio = '$idRegistro'
        ")->fetchColumn();

        $datos = [
            'idregistro_servicio' => $idRegistro,
            'id_sucursal' => $idSucursal,
            'descripcion' => $_POST['descripcion'],
            'tipo_reclamo' => $_POST['tipo_reclamo'],
            'origen' => $_POST['origen'],
            'prioridad' => $_POST['prioridad'],
            'requiere_garantia' => $_POST['requiere_garantia'] ?? 0,
            'usuario' => $_SESSION['id_str']
        ];

        $res = self::registrar_reclamo_modelo($datos);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'recargar',
                'Titulo' => 'Reclamo registrado',
                'Texto'  => 'Se generó correctamente',
                'Tipo'   => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto'  => $res['msg'] ?? 'Error',
            'Tipo'   => 'error'
        ]);
    }

    public function buscar_registro_controlador()
    {
        $texto = trim($_POST['buscar'] ?? '');

        $datos = self::buscar_registro_modelo($texto);

        if (!$datos) {
            return '<div class="alert alert-warning">
            No se encontraron servicios
        </div>';
        }

        $html = '<table class="table table-hover table-sm">
        <thead>
            <tr>
                <th>Servicio</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Trabajos</th>
                <th></th>
            </tr>
        </thead><tbody>';

        foreach ($datos as $r) {

            // 🔥 GENERAR TRABAJOS POR FILA
            $trabajos = '';

            if (!empty($r['trabajos'])) {

                $items = explode('|', $r['trabajos']);

                $trabajos .= '<ul style="margin:0;padding-left:15px;">';

                foreach ($items as $t) {
                    $trabajos .= '<li>' . $t . '</li>';
                }

                $trabajos .= '</ul>';
            }

            $html .= '
            <tr>
                <td>#' . $r['idregistro_servicio'] . '</td>
                <td>' . $r['nombre_cliente'] . ' ' . $r['apellido_cliente'] . '</td>
                <td>' . $r['mod_descri'] . ' ' . $r['placa'] . '</td>
                <td>' . $trabajos . '</td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm"
                        onclick="seleccionarRegistro(
                            \'' . mainModel::encryption($r['idregistro_servicio']) . '\',
                            \'' . $r['idregistro_servicio'] . '\',
                            \'' . $r['nombre_cliente'] . ' ' . $r['apellido_cliente'] . '\',
                            \'' . $r['mod_descri'] . ' ' . $r['placa'] . '\',
                            \'' . addslashes($r['trabajos']) . '\'
                        )">
                        Seleccionar
                    </button>
                </td>
            </tr>';
        }

        return $html . '</tbody></table>';
    }

    public function paginador_reclamo_controlador($pagina, $registros, $url, $busqueda = "")
    {
        $pagina    = (int) mainModel::limpiar_string($pagina);
        $registros = (int) mainModel::limpiar_string($registros);
        $url       = SERVERURL . mainModel::limpiar_string($url) . "/";

        $pagina = ($pagina > 0) ? $pagina : 1;
        $inicio = ($pagina - 1) * $registros;

        /* ================= FILTROS ================= */
        $filtros = [];

        // 🔒 sucursal SIEMPRE
        $filtros[] = [
            "campo" => "rs.id_sucursal",
            "tipo"  => "=",
            "valor" => $_SESSION['nick_sucursal']
        ];

        // 🔍 búsqueda
        if (!empty($busqueda)) {
            $filtros[] = [
                "campo" => "(c.nombre_cliente LIKE '%$busqueda%' 
                     OR c.apellido_cliente LIKE '%$busqueda%' 
                     OR v.placa LIKE '%$busqueda%')",
                "tipo"  => "RAW"
            ];
        }

        $estadoFiltro = $_SESSION['estado_reclamo_servicio'] ?? '';

        if ($estadoFiltro !== '') {
            $filtros[] = [
                "campo" => "rs.estado",
                "tipo"  => "=",
                "valor" => $estadoFiltro
            ];
        }

        $filtrosSQL = mainModel::construirFiltros($filtros);

        /* ================= CONSULTA ================= */
        $res = reclamoServicioModelo::listar_reclamos_modelo(
            $inicio,
            $registros,
            $filtrosSQL
        );

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        /* ================= TABLA ================= */
        $tabla = '<div class="table-responsive">
        <table class="table table-sm table-dark">
        <thead class="text-center">
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Estado</th>';

        if (mainModel::tienePermiso('servicio.reclamo.anular')) {
            $tabla .= '<th>ANULAR</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1) {

            $contador = $inicio + 1;

            foreach ($datos as $r) {

                switch ($r['estado']) {
                    case 1:
                        $estado = '<span class="badge badge-primary">Activo</span>';
                        break;
                    case 0:
                        $estado = '<span class="badge badge-secondary">Anulado</span>';
                        break;
                    default:
                        $estado = '<span class="badge badge-default">?</span>';
                }

                $tabla .= '
            <tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $r['nombre_cliente'] . ' ' . $r['apellido_cliente'] . '</td>
                <td>' . $r['placa'] . '</td>
                <td>' . date("d-m-Y H:i", strtotime($r['fecha_reclamo'])) . '</td>
                <td>' . $r['descripcion'] . '</td>
                <td>' . $estado . '</td>';

                if (mainModel::tienePermiso('servicio.reclamo.anular')) {

                    if ($r['estado'] == 1) {
                        $tabla .= '
                    <td>
                        <form class="FormularioAjax"
                            action="' . SERVERURL . 'ajax/reclamoServicioAjax.php"
                            method="POST"
                            data-form="delete">

                            <input type="hidden" name="accion" value="anular_reclamo">
                            <input type="hidden" name="id"
                                value="' . mainModel::encryption($r['idreclamo_servicio']) . '">

                            <button type="submit"
                                class="btn btn-danger btn-sm">
                                <i class="fas fa-ban"></i>
                            </button>
                        </form>
                    </td>';
                    } else {
                        $tabla .= '<td>-</td>';
                    }
                }

                $tabla .= '</tr>';
                $contador++;
            }
        } else {

            $tabla .= '
        <tr>
            <td colspan="7" class="text-center">Sin registros</td>
        </tr>';
        }

        $tabla .= '</tbody></table></div>';

        if ($total >= 1) {
            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        return $tabla;
    }

    public function anular_reclamo_controlador()
    {
        session_start(['name' => 'STR']);

        if (!mainModel::tienePermiso('servicio.reclamo.anular')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Advertencia!",
                "Texto" => "No posee los permisos necesarios para realizar esta acción",
                "Tipo" => "error"
            ]);
        }

        if (empty($_POST['id'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "ID inválido",
                "Tipo" => "error"
            ]);
        }

        $id = mainModel::decryption($_POST['id']);
        $id = mainModel::limpiar_string($id);

        $check = mainModel::ejecutar_consulta_simple(
            "SELECT estado FROM reclamo_servicio WHERE idreclamo_servicio = '$id' LIMIT 1"
        );

        if ($check->rowCount() <= 0) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El reclamo no existe",
                "Tipo" => "error"
            ]);
        }

        $row = $check->fetch();

        if ($row['estado'] == 0) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Advertencia",
                "Texto" => "El reclamo ya se encuentra anulado",
                "Tipo" => "warning"
            ]);
        }

        $ok = self::anular_reclamo_modelo($id, $_SESSION['id_str']);

        if ($ok) {
            return json_encode([
                "Alerta" => "recargar",
                "Titulo" => "Reclamo",
                "Texto" => "Reclamo anulado correctamente",
                "Tipo" => "success"
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto" => "No se pudo anular el reclamo",
            "Tipo" => "error"
        ]);
    }

    public function obtener_reclamo_para_recepcion_controlador()
    {
        $id = mainModel::decryption($_POST['id']);

        $sql = mainModel::conectar()->prepare("
        SELECT 
            rs.idreclamo_servicio,
            c.id_cliente,
            CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
            v.id_vehiculo,
            CONCAT(m.mar_descri,' ',mo.mod_descri,' ',v.placa) AS vehiculo
        FROM reclamo_servicio rs
        INNER JOIN registro_servicio rgs ON rgs.idregistro_servicio = rs.idregistro_servicio
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rgs.idorden_trabajo
        INNER JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
        INNER JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico 
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ds.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto mo ON mo.id_modeloauto = v.id_modeloauto
        INNER JOIN marcas m ON m.id_marcas = mo.id_marcas
        WHERE rs.idreclamo_servicio = ?
        LIMIT 1
        ");

        $sql->execute([$id]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function buscar_reclamo_recepcion_controlador()
    {
        $texto = trim($_POST['buscar'] ?? '');

        $sql = mainModel::conectar()->prepare("
            SELECT 
                rs.idreclamo_servicio,
                c.nombre_cliente,
                c.apellido_cliente,
                v.placa,
                m.mod_descri
            FROM reclamo_servicio rs
            INNER JOIN registro_servicio rgs 
                ON rgs.idregistro_servicio = rs.idregistro_servicio
            INNER JOIN orden_trabajo ot 
                ON ot.idorden_trabajo = rgs.idorden_trabajo
            INNER JOIN presupuesto_servicio ps 
                ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
            INNER JOIN diagnostico_servicio ds 
                ON ds.id_diagnostico = ps.id_diagnostico
            INNER JOIN recepcion_servicio r 
                ON r.idrecepcion = ds.idrecepcion

            INNER JOIN clientes c 
                ON c.id_cliente = r.id_cliente
            INNER JOIN vehiculos v 
                ON v.id_vehiculo = r.id_vehiculo
            INNER JOIN modelo_auto m 
                ON m.id_modeloauto = v.id_modeloauto

            WHERE rs.estado = 1
            AND (
                c.nombre_cliente LIKE :b
                OR c.apellido_cliente LIKE :b
                OR v.placa LIKE :b
            )
            ORDER BY rs.idreclamo_servicio DESC
            LIMIT 20
            ");

        $sql->bindValue(':b', "%$texto%");
        $sql->execute();

        $datos = $sql->fetchAll(PDO::FETCH_ASSOC);

        if (!$datos) {
            return '<div class="alert alert-warning">Sin reclamos</div>';
        }

        $html = '<table class="table table-sm table-hover">
        <thead>
            <tr>
                <th>Reclamo</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th></th>
            </tr>
        </thead><tbody>';

        foreach ($datos as $r) {

            $html .= '
        <tr>
            <td>#' . $r['idreclamo_servicio'] . '</td>
            <td>' . $r['nombre_cliente'] . ' ' . $r['apellido_cliente'] . '</td>
            <td>' . $r['mod_descri'] . ' ' . $r['placa'] . '</td>
            <td>
                <button class="btn btn-success btn-sm"
                    onclick="seleccionarReclamo(
                        \'' . mainModel::encryption($r['idreclamo_servicio']) . '\'
                    )">
                    Seleccionar
                </button>
            </td>
        </tr>';
        }

        return $html . '</tbody></table>';
    }
}
