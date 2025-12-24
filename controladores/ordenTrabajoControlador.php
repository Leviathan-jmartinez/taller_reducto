<?php
if ($peticionAjax) {
    require_once "../modelos/ordenTrabajoModelo.php";
} else {
    require_once "./modelos/ordenTrabajoModelo.php";
}

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
                <td>
                    <a href="' . SERVERURL . 'ordenTrabajo-detalle/' . mainModel::encryption($rows['idorden_trabajo']) . '/" 
                       class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i>
                    </a>
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
}
