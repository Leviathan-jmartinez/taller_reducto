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

        return diagnosticoModelo::buscar_recepcion_modelo($busqueda);
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

        /* ================= FIX FECHA ================= */
        $fecha = str_replace("T", " ", $_POST['fecha']);

        /* ================= DETALLES ================= */

        $detalles = [];

        if (isset($_POST['descripcion'])) {
            foreach ($_POST['descripcion'] as $i => $desc) {

                if (trim($desc) == "") continue;

                $detalles[] = [
                    "descripcion" => $desc,
                    "tipo" => $_POST['tipo'][$i]
                ];
            }
        }

        $datos = [
            "idrecepcion" => intval($_POST['idrecepcion']),
            "id_usuario"  => $_SESSION['id_str'] ?? 0,
            "fecha"       => $fecha,
            "observacion" => $_POST['observacion'] ?? null,
            "estado"      => intval($_POST['estado']),
            "detalles"    => $detalles
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

    public function paginador_diagnostico_controlador($pagina, $registros, $privilegio, $url, $busqueda1, $busqueda2, $cliente = '', $placa = '')
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $privilegio = mainModel::limpiar_string($privilegio);
        $busqueda1 = mainModel::limpiar_string($busqueda1);
        $busqueda2 = mainModel::limpiar_string($busqueda2);
        $cliente = mainModel::limpiar_string($cliente);
        $placa = mainModel::limpiar_string($placa);

        $url = SERVERURL . $url . "/";
        $tabla = "";

        $pagina = ($pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        $filtros = "";

        if ($cliente != "") {
            $filtros .= " AND CONCAT(c.nombre_cliente,' ',c.apellido_cliente) LIKE '%$cliente%'";
        }

        if ($placa != "") {
            $filtros .= " AND v.placa LIKE '%$placa%'";
        }

        if (!empty($busqueda1) && !empty($busqueda2)) {

            $consulta = "
        SELECT SQL_CALC_FOUND_ROWS
            d.id_diagnostico,
            d.fecha,
            d.estado,
            rs.idrecepcion,

            CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
            v.placa,
            u.usu_nombre,
            u.usu_apellido

        FROM diagnostico_servicio d
        INNER JOIN recepcion_servicio rs ON rs.idrecepcion = d.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = rs.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
        INNER JOIN usuarios u ON u.id_usuario = d.id_usuario

        WHERE date(d.fecha) >= '$busqueda1'
        AND date(d.fecha) <= '$busqueda2'
        AND rs.id_sucursal = '{$_SESSION['nick_sucursal']}'
        $filtros

        ORDER BY d.fecha DESC
        LIMIT $inicio,$registros
        ";
        } else {

            $consulta = "
        SELECT SQL_CALC_FOUND_ROWS
            d.id_diagnostico,
            d.fecha,
            d.estado,
            rs.idrecepcion,

            CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
            v.placa,
            u.usu_nombre,
            u.usu_apellido

        FROM diagnostico_servicio d
        INNER JOIN recepcion_servicio rs ON rs.idrecepcion = d.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = rs.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
        INNER JOIN usuarios u ON u.id_usuario = d.id_usuario

        WHERE rs.id_sucursal = '{$_SESSION['nick_sucursal']}'
        $filtros

        ORDER BY d.id_diagnostico DESC
        LIMIT $inicio,$registros
        ";
        }

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta)->fetchAll();

        $total = (int)$conexion->query("SELECT FOUND_ROWS()")->fetchColumn();
        $Npaginas = ceil($total / $registros);

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
        </tr>
    </thead>
    <tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador = $inicio + 1;

            foreach ($datos as $rows) {

                switch ($rows['estado']) {
                    case 1:
                        $estado = '<span class="badge bg-info">En proceso</span>';
                        break;
                    case 2:
                        $estado = '<span class="badge bg-success">Finalizado</span>';
                        break;
                    default:
                        $estado = '<span class="badge bg-secondary">Pendiente</span>';
                }

                $tabla .= '
            <tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . date("d/m/Y H:i", strtotime($rows['fecha'])) . '</td>
                <td>' . $rows['cliente'] . '</td>
                <td>' . $rows['placa'] . '</td>
                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                <td>' . $estado . '</td>
                <td>
                    <a href="' . SERVERURL . 'presupuestoServicio-nuevo/' . $rows['idrecepcion'] . '/" class="btn btn-success btn-sm">
                        Presupuesto
                    </a>
                </td>
            </tr>';

                $contador++;
            }
        } else {
            $tabla .= '<tr><td colspan="7">No hay registros</td></tr>';
        }

        $tabla .= '</tbody></table></div>';

        if ($total >= 1) {
            $tabla .= '<p class="text-right">Total: ' . $total . '</p>';
            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        echo $tabla;
    }
}
