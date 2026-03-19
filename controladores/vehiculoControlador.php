<?php
if ($peticionAjax) {
    require_once "../modelos/vehiculoModelo.php";
} else {
    require_once "./modelos/vehiculoModelo.php";
}

class vehiculoControlador extends vehiculoModelo
{
    /* ==================================================
       LISTAS REFERENCIALES
    ================================================== */

    public function listar_clientes_controlador()
    {
        return vehiculoModelo::obtener_clientes_modelo();
    }

    public function listar_modelos_controlador()
    {
        return vehiculoModelo::obtener_modelos_modelo();
    }

    public function listar_colores_controlador()
    {
        return vehiculoModelo::obtener_colores_modelo();
    }

    /* ==================================================
       DATOS VEHICULO
    ================================================== */

    public function datos_vehiculo_controlador($tipo, $id)
    {
        $tipo = mainModel::limpiar_string($tipo);
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_string($id);

        return vehiculoModelo::datos_vehiculo_modelo($tipo, $id);
    }

    /* ==================================================
       PAGINADOR VEHICULOS
    ================================================== */

    public function paginador_vehiculos_controlador($pagina, $registros, $privilegio, $url, $busqueda)
{
    $pagina = mainModel::limpiar_string($pagina);
    $registros = mainModel::limpiar_string($registros);
    $privilegio = mainModel::limpiar_string($privilegio);
    $busqueda = mainModel::limpiar_string($busqueda);

    $url = mainModel::limpiar_string($url);
    $url = SERVERURL . $url . "/";

    $tabla = "";

    $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
    $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

    if (isset($busqueda) && $busqueda != "") {
        $consulta = "SELECT SQL_CALC_FOUND_ROWS v.*, 
                        c.nombre_cliente, c.apellido_cliente,
                        m.mod_descri,
                        co.col_descripcion
                    FROM vehiculos v
                    INNER JOIN clientes c ON c.id_cliente = v.id_cliente
                    INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
                    LEFT JOIN colores co ON co.id_color = v.id_color
                    WHERE (v.placa LIKE '%$busqueda%' 
                        OR c.nombre_cliente LIKE '%$busqueda%'
                        OR c.apellido_cliente LIKE '%$busqueda%')
                    ORDER BY v.id_vehiculo ASC 
                    LIMIT $inicio,$registros";
    } else {
        $consulta = "SELECT SQL_CALC_FOUND_ROWS v.*, 
                        c.nombre_cliente, c.apellido_cliente,
                        m.mod_descri,
                        co.col_descripcion
                    FROM vehiculos v
                    INNER JOIN clientes c ON c.id_cliente = v.id_cliente
                    INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
                    LEFT JOIN colores co ON co.id_color = v.id_color
                    ORDER BY v.id_vehiculo ASC 
                    LIMIT $inicio,$registros";
    }

    $conexion = mainModel::conectar();
    $datos = $conexion->query($consulta);
    $datos = $datos->fetchAll();

    $total = $conexion->query("SELECT FOUND_ROWS()");
    $total = (int)$total->fetchColumn();

    $Npaginas = ceil($total / $registros);

    $tabla .= '<div class="table-responsive">
    <table class="table table-dark table-sm">
    <thead>
        <tr class="text-center roboto-medium">
            <th>#</th>
            <th>PLACA</th>
            <th>CLIENTE</th>
            <th>MODELO</th>
            <th>COLOR</th>
            <th>ESTADO</th>';

    if (mainModel::tienePermisoVista('vehiculo.editar')) {
        $tabla .= '<th>ACTUALIZAR</th>';
    }
    if (mainModel::tienePermisoVista('vehiculo.eliminar')) {
        $tabla .= '<th>ELIMINAR</th>';
    }

    $tabla .= '</tr></thead><tbody>';

    if ($total >= 1 && $pagina <= $Npaginas) {

        $contador = $inicio + 1;
        $reg_inicio = $inicio + 1;

        foreach ($datos as $rows) {

            $cliente = $rows['nombre_cliente'] . " " . $rows['apellido_cliente'];

            $tabla .= '<tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $rows['placa'] . '</td>
                <td>' . $cliente . '</td>
                <td>' . $rows['mod_descri'] . '</td>
                <td>' . ($rows['col_descripcion'] ?? '-') . '</td>
                <td>' . ($rows['estado'] == 1 
                        ? '<span class="badge badge-success">Activo</span>' 
                        : '<span class="badge badge-danger">Inactivo</span>') . '</td>';

            if (mainModel::tienePermisoVista('vehiculo.editar')) {
                $tabla .= '<td>
                    <a href="' . SERVERURL . 'vehiculo-actualizar/' . mainModel::encryption($rows['id_vehiculo']) . '/" 
                       class="btn btn-success">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </td>';
            }

            if (mainModel::tienePermisoVista('vehiculo.eliminar')) {
                $tabla .= '<td>
                    <form class="FormularioAjax" action="' . SERVERURL . 'ajax/vehiculoAjax.php"
                          method="POST" data-form="delete">
                        <input type="hidden" name="vehiculo_id_del"
                               value="' . mainModel::encryption($rows['id_vehiculo']) . '">
                        <button type="submit" class="btn btn-warning">
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
        <td colspan="8">No hay registros</td>
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

    /* ==================================================
       AGREGAR VEHICULO
    ================================================== */

    public function agregar_vehiculo_controlador()
    {
        $cliente = mainModel::limpiar_string($_POST['cliente_reg']);
        $modelo  = mainModel::limpiar_string($_POST['modelo_reg']);
        $color   = mainModel::limpiar_string($_POST['color_reg']);
        $placa   = mainModel::limpiar_string($_POST['placa_reg']);
        $anho    = mainModel::limpiar_string($_POST['anho_reg']);
        $serie   = mainModel::limpiar_string($_POST['serie_reg']);
        $estado  = mainModel::limpiar_string($_POST['estado_reg']);

        if ($cliente == "" || $modelo == "" || $color == "" || $placa == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe completar los campos obligatorios",
                "Tipo" => "error"
            ]);
            exit();
        }

        $datos = [
            "id_cliente" => $cliente,
            "id_modeloauto" => $modelo,
            "id_color" => $color,
            "placa" => $placa,
            "anho" => $anho,
            "nro_serie" => $serie,
            "estado" => $estado
        ];

        vehiculoModelo::agregar_vehiculo_modelo($datos);

        echo json_encode([
            "Alerta" => "limpiar",
            "Titulo" => "Vehículo",
            "Texto" => "Vehículo registrado correctamente",
            "Tipo" => "success"
        ]);
    }

    /* ==================================================
       ACTUALIZAR VEHICULO
    ================================================== */

    public function actualizar_vehiculo_controlador()
    {
        $id = mainModel::decryption($_POST['vehiculo_id_up']);

        $datos = [
            "id_vehiculo" => $id,
            "id_cliente" => mainModel::limpiar_string($_POST['cliente_up']),
            "id_modeloauto" => mainModel::limpiar_string($_POST['modelo_up']),
            "id_color" => mainModel::limpiar_string($_POST['color_up']),
            "placa" => mainModel::limpiar_string($_POST['placa_up']),
            "anho" => mainModel::limpiar_string($_POST['anho_up']),
            "nro_serie" => mainModel::limpiar_string($_POST['serie_up']),
            "estado" => mainModel::limpiar_string($_POST['estado_up'])
        ];

        vehiculoModelo::actualizar_vehiculo_modelo($datos);

        echo json_encode([
            "Alerta" => "recargar",
            "Titulo" => "Vehículo",
            "Texto" => "Vehículo actualizado correctamente",
            "Tipo" => "success"
        ]);
    }

    /* ==================================================
       ELIMINAR VEHICULO (CON BLOQUEO)
    ================================================== */

    public function eliminar_vehiculo_controlador()
    {
        $id = mainModel::decryption($_POST['vehiculo_id_del']);
        $id = mainModel::limpiar_string($id);

        $check = mainModel::ejecutar_consulta_simple(
            "SELECT id_vehiculo, estado 
         FROM vehiculos 
         WHERE id_vehiculo = '$id'"
        );

        if ($check->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "El vehículo no existe en el sistema",
                "Tipo"   => "error"
            ]);
            exit();
        }

        session_start(['name' => 'STR']);
        if ($_SESSION['nivel_str'] == 3) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "No tiene los permisos necesarios para realizar esta operación",
                "Tipo"   => "error"
            ]);
            exit();
        }

        $stmt = vehiculoModelo::eliminar_vehiculo_modelo($id);

        if ($stmt->rowCount() > 0) {

            // Verificar cómo quedó
            $verificar = mainModel::ejecutar_consulta_simple(
                "SELECT estado 
             FROM vehiculos 
             WHERE id_vehiculo = '$id'"
            );

            if ($verificar->rowCount() > 0) {
                // Sigue existiendo → fue desactivado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Vehículo desactivado",
                    "Texto"  => "El vehículo ya tiene movimientos asociados, por lo que fue desactivado.",
                    "Tipo"   => "warning"
                ];
            } else {
                // Ya no existe → fue eliminado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Vehículo eliminado",
                    "Texto"  => "El vehículo fue eliminado correctamente.",
                    "Tipo"   => "success"
                ];
            }
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "No se pudo eliminar el vehículo seleccionado",
                "Tipo"   => "error"
            ];
        }

        echo json_encode($alerta);
    }
}
