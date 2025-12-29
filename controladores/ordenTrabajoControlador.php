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
                        $estado = '<span class="badge badge-success">Terminada</span>';
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
                if ($rows['estado'] == 1) {
                    $tabla .= '
                <button class="btn btn-primary btn-sm"
                    onclick="abrirModalTecnico(\'' . mainModel::encryption($rows['idorden_trabajo']) . '\')"
                    title="Asignar técnico">
                    <i class="fas fa-user-cog"></i>
                </button>';
                }
                $tabla .= '
                <a href="' . SERVERURL . 'pdf/ordenTrabajo.php?id=' . mainModel::encryption($rows['idorden_trabajo']) . '"
                    target="_blank"
                    class="btn btn-info btn-sm"
                    title="Imprimir OT">
                        <i class="fas fa-print"></i>
                </a>';
                if (in_array($rows['estado'], [1, 2])) {
                    $tabla .= '
                <button class="btn btn-danger btn-sm"
                    onclick="anularOT(\'' . mainModel::encryption($rows['idorden_trabajo']) . '\')"
                    title="Anular OT">
                    <i class="fas fa-ban"></i>
                </button>';
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
        $ot = mainModel::decryption($_POST['id_ot']);
        $tec = $_POST['idtrabajos'];

        ordenTrabajoModelo::asignar_tecnico_modelo($ot, $tec);

        return json_encode([
            'Alerta' => 'recargar',
            'Titulo' => 'Técnico asignado',
            'Texto' => 'La OT pasó a EN PROCESO',
            'Tipo' => 'success'
        ]);
    }
    public function listar_tecnicos_controlador()
    {
        return ordenTrabajoModelo::listar_tecnicos_modelo();
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
              AND (
                    c.nombre_cliente LIKE :busqueda
                 OR v.placa LIKE :busqueda
              )
            ORDER BY ps.idpresupuesto_servicio DESC
        ";

        $sql = self::conectar()->prepare($consulta);
        $sql->bindValue(":busqueda", "%$texto%");
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
        if (empty($_POST['id_ot'])) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'OT no válida',
                'Tipo'   => 'error'
            ]);
        }

        $idOT    = mainModel::decryption($_POST['id_ot']);
        $usuario = $_SESSION['id_str'];

        $res = self::anular_ot_modelo($idOT, $usuario);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'recargar',
                'Titulo' => 'OT anulada',
                'Texto'  => 'La orden de trabajo fue anulada correctamente',
                'Tipo'   => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto'  => $res['msg'] ?? 'No se pudo anular la OT',
            'Tipo'   => 'error'
        ]);
    }
}
