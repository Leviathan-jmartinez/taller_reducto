<?php
require_once __DIR__ . "/../modelos/ordenTrabajoModelo.php";

class ordenTrabajoControlador extends ordenTrabajoModelo
{
    public function generar_ot_controlador()
    {
        session_start(['name' => 'STR']);

        if (!isset($_POST['id'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto' => 'ID inválido',
                'Tipo' => 'error'
            ]);
        }

        $idPresupuesto = mainModel::decryption($_POST['id']);

        /* PRESUPUESTO */
        $presupuesto = ordenTrabajoModelo::obtener_presupuesto_modelo($idPresupuesto);
        if (!$presupuesto) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto' => 'Presupuesto no aprobado o inexistente',
                'Tipo' => 'error'
            ]);
        }

        /* DETALLE */
        $detalle = ordenTrabajoModelo::obtener_detalle_presupuesto_modelo($idPresupuesto);
        if (!$detalle) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto' => 'El presupuesto no tiene detalle',
                'Tipo' => 'error'
            ]);
        }

        $datos = [
            'idpresupuesto' => $idPresupuesto,
            'idrecepcion'   => $presupuesto['idrecepcion'],
            'usuario'       => $_SESSION['id_str'],
            'observacion'   => 'OT generada desde presupuesto',
            'detalle'       => $detalle
        ];
        $sucursalPresupuesto = mainModel::ejecutar_consulta_simple("
            SELECT r.id_sucursal
            FROM presupuesto_servicio ps
            INNER JOIN recepcion_servicio r ON r.idrecepcion = ps.idrecepcion
            WHERE ps.idpresupuesto_servicio = '$idPresupuesto'
        ")->fetchColumn();

        if ($sucursalPresupuesto != $_SESSION['nick_sucursal']) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Acceso denegado',
                'Texto'  => 'El presupuesto no pertenece a su sucursal',
                'Tipo'   => 'error'
            ]);
        }

        $res = ordenTrabajoModelo::crear_ot_modelo($datos);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'recargar',
                'Titulo' => 'OT generada',
                'Texto' => 'La orden de trabajo fue creada correctamente',
                'Tipo' => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto' => $res['msg'] ?? 'No se pudo generar OT',
            'Tipo' => 'error'
        ]);
    }

    public function paginador_ot_controlador($pagina, $registros, $privilegio, $url, $busqueda1, $busqueda2)
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $url = SERVERURL . mainModel::limpiar_string($url) . "/";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina * $registros) - $registros;

        $consulta = ordenTrabajoModelo::paginador_ot_modelo($inicio, $registros, $busqueda1, $busqueda2);

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta)->fetchAll();
        $total = (int)$conexion->query("SELECT FOUND_ROWS()")->fetchColumn();
        $Npaginas = ceil($total / $registros);

        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Fecha</th>
                <th>Presupuesto</th>
                <th>Creado por</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>';

        if ($total >= 1) {
            $contador = $inicio + 1;
            foreach ($datos as $rows) {

                switch ($rows['estado']) {
                    case 1:
                        $estado = '<span class="badge badge-warning">Abierta</span>';
                        break;
                    case 2:
                        $estado = '<span class="badge badge-primary">En proceso</span>';
                        break;
                    case 3:
                        $estado = '<span class="badge badge-success">Finalizado</span>';
                        break;
                    case 4:
                        $estado = '<span class="badge badge-info">Facturada</span>';
                        break;
                    case 0:
                        $estado = '<span class="badge badge-default">Anulada</span>';
                        break;
                    default:
                        $estado = '<span class="badge badge-secondary">?</span>';
                }

                $tabla .= '
        <tr class="text-center">
            <td>' . $contador . '</td>
            <td>' . $rows['nombre_cliente'] . ' ' . $rows['apellido_cliente'] . '</td>
            <td>' . $rows['modelo'] . ' ' . $rows['placa'] . '</td>
            <td>' . date("d-m-Y", strtotime($rows['fecha_inicio'])) . '</td>
            <td>#' . $rows['idpresupuesto_servicio'] . '</td>
            <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
            <td>' . $estado . '</td>
            <td>';

                /* asignar técnico */
                if ($rows['estado'] == 1 && $url !== SERVERURL . 'ordenTrabajo-buscar/') {
                    $tabla .= '
                <button class="btn btn-primary btn-sm"
                    onclick="abrirModalEquipo(\'' . mainModel::encryption($rows['idorden_trabajo']) . '\')"
                    title="Asignar técnico">
                    <i class="fas fa-user-cog"></i>
                </button>';
                }

                /* imprimir */
                $tabla .= '
            <a href="' . SERVERURL . 'pdf/ordenTrabajo.php?id=' . mainModel::encryption($rows['idorden_trabajo']) . '"
                target="_blank"
                class="btn btn-info btn-sm"
                title="Imprimir OT">
                <i class="fas fa-print"></i>
            </a>';

                /* anular OT → FormularioAjax */
                if (in_array($rows['estado'], [1, 2])) {
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
        } else {
            $tabla .= '<tr><td colspan="8" class="text-center">Sin registros</td></tr>';
        }

        $tabla .= '</tbody></table></div>';

        if ($total >= 1) {
            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        return $tabla;
    }


    public function detalle_ot_controlador($idEnc)
    {
        $id = mainModel::decryption($idEnc);
        $ot = ordenTrabajoModelo::obtener_ot_modelo($id);
        $detalle = ordenTrabajoModelo::obtener_detalle_ot_modelo($id);

        return [
            'ot' => $ot,
            'detalle' => $detalle
        ];
    }

    public function asignar_tecnico_controlador()
    {
        return $this->asignar_equipo_controlador();
    }

    public function listar_tecnicos_controlador()
    {
        return $this->listar_equipos_controlador();
    }

    public function asignar_equipo_controlador()
    {
        session_start(['name' => 'STR']);

        if (empty($_POST['id_ot']) || empty($_POST['idtrabajos'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Datos incompletos',
                'Tipo'   => 'error'
            ]);
        }

        $ot     = mainModel::decryption($_POST['id_ot']);
        $equipo = mainModel::limpiar_string($_POST['idtrabajos']);

        // Validar sucursal (tu lógica ya existente)
        $sucursalOT = mainModel::ejecutar_consulta_simple("
        SELECT r.id_sucursal
        FROM orden_trabajo ot
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ot.idrecepcion
        WHERE ot.idorden_trabajo = '$ot'
        ")->fetchColumn();

        if ($sucursalOT != $_SESSION['nick_sucursal']) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'OT no pertenece a su sucursal',
                'Tipo'   => 'error'
            ]);
        }

        ordenTrabajoModelo::asignar_equipo_modelo($ot, $equipo);

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
            SELECT ps.idpresupuesto_servicio, ps.idrecepcion,
                   c.nombre_cliente, v.placa
            FROM presupuesto_servicio ps
            INNER JOIN recepcion_servicio r ON r.idrecepcion = ps.idrecepcion
            INNER JOIN clientes c ON c.id_cliente = r.id_cliente
            INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
            LEFT JOIN orden_trabajo ot
                ON ot.idpresupuesto_servicio = ps.idpresupuesto_servicio
            WHERE ps.estado = '2'
                AND ot.idorden_trabajo IS NULL
                AND r.id_sucursal = :sucursal
                AND (
                        c.nombre_cliente LIKE :busqueda
                    OR v.placa LIKE :busqueda
                )
            ORDER BY ps.idpresupuesto_servicio DESC
        ";

        $sql = self::conectar()->prepare($consulta);
        $sql->bindValue(":busqueda", "%$texto%");
        $sql->bindValue(':sucursal', $_SESSION['nick_sucursal'], PDO::PARAM_INT);
        $sql->execute();

        if ($sql->rowCount() == 0) {
            return '<div class="alert alert-warning">No se encontraron presupuestos</div>';
        }

        $tabla = '<table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th></th>
                </tr>
            </thead><tbody>';

        foreach ($sql->fetchAll() as $row) {
            $tabla .= '
            <tr>
                <td>' . $row['nombre_cliente'] . '</td>
                <td>' . $row['placa'] . '</td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm"
                        onclick="seleccionarPresupuesto(
                            ' . $row['idpresupuesto_servicio'] . ',
                            ' . $row['idrecepcion'] . ',
                            \'' . $row['nombre_cliente'] . '\',
                            \'' . $row['placa'] . '\'
                        )">
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
               d.cantidad,
               d.preciouni,
               d.subtotal
        FROM presupuesto_detalleservicio d
        INNER JOIN articulos a ON a.id_articulo = d.id_articulo
        WHERE d.idpresupuesto_servicio = ?");
        $sql->execute([$idpresupuesto]);

        if ($sql->rowCount() == 0) {
            return '<tr><td colspan="4" class="text-center">Sin detalle</td></tr>';
        }

        $html = '';
        foreach ($sql->fetchAll() as $row) {
            $html .= '
            <tr>
                <td>' . $row['desc_articulo'] . '</td>
                <td class="text-center">' . $row['cantidad'] . '</td>
                <td class="text-right">' . number_format($row['preciouni'], 0, ',', '.') . '</td>
                <td class="text-right">' . number_format($row['subtotal'], 0, ',', '.') . '</td>
            </tr>
        ';
        }

        return $html;
    }

    public function generar_ot_controlador2()
    {
        if (empty($_POST['idpresupuesto_servicio']) || empty($_POST['idtrabajos'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Datos incompletos',
                'Tipo'   => 'error'
            ]);
        }

        $datos = [
            'idpresupuesto' => $_POST['idpresupuesto_servicio'],
            'idusuario'     => $_POST['id_usuario'],
            'idtrabajos'    => $_POST['idtrabajos'],
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
        SELECT r.id_sucursal
        FROM orden_trabajo ot
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ot.idrecepcion
        WHERE ot.idorden_trabajo = '$idOT'
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
}
