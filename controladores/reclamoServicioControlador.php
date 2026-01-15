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

        $datos = [
            'idregistro_servicio' =>
            mainModel::decryption($_POST['idregistro_servicio']),
            'descripcion' => $_POST['descripcion'],
            'usuario'     => $_SESSION['id_str']
        ];

        $res = self::registrar_reclamo_modelo($datos);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'recargar',
                'Titulo' => 'Reclamo registrado',
                'Texto'  => 'El reclamo fue registrado correctamente',
                'Tipo'   => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto'  => $res['msg'] ?? 'No se pudo registrar el reclamo',
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
                    <th></th>
                </tr>
            </thead><tbody>';

        foreach ($datos as $r) {
            $html .= '
            <tr>
                <td>#' . $r['idregistro_servicio'] . '</td>
                <td>' . $r['nombre_cliente'] . ' ' .
                $r['apellido_cliente'] . '</td>
                <td>' . $r['mod_descri'] . ' ' . $r['placa'] . '</td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm"
                        onclick="seleccionarRegistro(
                            \'' . mainModel::encryption($r['idregistro_servicio']) . '\',
                            \'' . $r['idregistro_servicio'] . '\',
                            \'' . $r['nombre_cliente'] . ' ' .
                $r['apellido_cliente'] . '\',
                            \'' . $r['mod_descri'] . ' ' . $r['placa'] . '\'
                        )">
                        Seleccionar
                    </button>
                </td>
            </tr>';
        }

        return $html . '</tbody></table>';
    }

    public function paginador_reclamo_controlador($pagina, $registros, $privilegio, $url, $busqueda = "")
    {
        $pagina   = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $busqueda = mainModel::limpiar_string($busqueda);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        $filtro = "";
        if ($busqueda != "") {
            $filtro = "WHERE (
            c.nombre_cliente LIKE '%$busqueda%'
            OR c.apellido_cliente LIKE '%$busqueda%'
            OR v.placa LIKE '%$busqueda%'
        )";
        }

        $consulta = "
        SELECT SQL_CALC_FOUND_ROWS
            rs.idreclamo_servicio,
            rs.fecha_reclamo,
            rs.descripcion,
            rs.estado,
            c.nombre_cliente,
            c.apellido_cliente,
            v.placa
        FROM reclamo_servicio rs
        INNER JOIN registro_servicio rgs ON rgs.idregistro_servicio = rs.idregistro_servicio
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rgs.idorden_trabajo
        INNER JOIN recepcion_servicio r ON r.idrecepcion = ot.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        $filtro
        ORDER BY rs.idreclamo_servicio DESC
        LIMIT $inicio,$registros
     ";

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta)->fetchAll();

        $total = $conexion->query("SELECT FOUND_ROWS()")->fetchColumn();
        $Npaginas = ceil($total / $registros);

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

        if ($privilegio == 1 || $privilegio == 2) {
            $tabla .= '<th>ANULAR</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {
            $contador = $inicio + 1;
            $reg_inicio = $contador;

            foreach ($datos as $r) {
                $estado = ($r['estado'] == 1)
                    ? '<span class="badge bg-primary">Activo</span>'
                    : '<span class="badge bg-secondary">Cerrado</span>';

                $tabla .= '<tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $r['nombre_cliente'] . ' ' . $r['apellido_cliente'] . '</td>
                <td>' . $r['placa'] . '</td>
                <td>' . date("d-m-Y H:i", strtotime($r['fecha_reclamo'])) . '</td>
                <td>' . $r['descripcion'] . '</td>
                <td>' . $estado . '</td>';

                if ($privilegio == 1 || $privilegio == 2) {
                    $tabla .= '<td>
                    <form class="FormularioAjax" action="' . SERVERURL . 'ajax/reclamoServicioAjax.php" method="POST" data-form="delete">
                        <input type="hidden" name="accion" value="anular_reclamo">
                        <input type="hidden" name="id" value="' . mainModel::encryption($r['idreclamo_servicio']) . '">
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </form>
                </td>';
                }

                $tabla .= '</tr>';
                $contador++;
            }

            $reg_final = $contador - 1;
        } else {
            $tabla .= '<tr class="text-center">
            <td colspan="7">No hay registros</td>
            </tr>';
            $reg_inicio = 0;
            $reg_final = 0;
        }

        $tabla .= '</tbody></table></div>';

        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '<p class="text-right">
            Mostrando registro ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '
            </p>';
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
}
