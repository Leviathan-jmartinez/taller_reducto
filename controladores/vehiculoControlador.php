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

    public function listar_modelos_controlador()
    {
        return vehiculoModelo::obtener_modelos_modelo();
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

    public function paginador_vehiculos_controlador($pagina, $registros, $url, $busqueda)
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $busqueda = mainModel::limpiar_string($busqueda);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $pagina = ($pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina - 1) * $registros;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        /* ===== FILTROS ===== */
        $filtrosSQL = "";

        if ($busqueda != "") {
            $filtrosSQL .= " AND (
            v.placa LIKE '%$busqueda%' 
            OR c.nombre_cliente LIKE '%$busqueda%'
            OR c.apellido_cliente LIKE '%$busqueda%'
        )";
        }

        /* ===== DATOS ===== */
        $res = vehiculoModelo::listar_vehiculos_modelo($inicio, $registros, $filtrosSQL);

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center roboto-medium">
                <th>#</th>
                <th>PLACA</th>
                <th>CLIENTE</th>
                <th>MODELO</th>
                <th>COLOR</th>
            <th>ESTADO</th>';

        if (mainModel::tienePermiso('vehiculo.editar')) {
            $tabla .= '<th>ACTUALIZAR</th>';
        }
        if (mainModel::tienePermiso('vehiculo.eliminar')) {
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
                <td>' . ($rows['color'] ?? '-') . '</td>
                <td>' . ($rows['estado'] == 1
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>') . '</td>';

                if (mainModel::tienePermiso('vehiculo.editar')) {
                    $tabla .= '<td>
                    <a href="' . SERVERURL . 'vehiculo-actualizar/' . mainModel::encryption($rows['id_vehiculo']) . '/"
                    class="btn btn-success">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </td>';
                }

                if (mainModel::tienePermiso('vehiculo.eliminar')) {
                    $tabla .= '<td>
                    <form class="FormularioAjax"
                        action="' . SERVERURL . 'ajax/vehiculoAjax.php"
                        method="POST"
                        data-form="delete">

                        <input type="hidden"
                        name="vehiculo_id_del"
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
            <td colspan="7">No hay registros</td>
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
        if (!mainModel::tienePermiso('vehiculo.crear')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para registrar vehiculos",
                "Tipo" => "error"
            ]);
            exit();
        }

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

        if ($estado != "0" && $estado != "1") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El estado seleccionado no es valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        $check_cliente = mainModel::ejecutar_consulta_simple(
            "SELECT id_cliente FROM clientes WHERE id_cliente='$cliente' AND estado_cliente=1"
        );
        if ($check_cliente->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El cliente seleccionado no es valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        $check_modelo = mainModel::ejecutar_consulta_simple(
            "SELECT id_modeloauto FROM modelo_auto WHERE id_modeloauto='$modelo' AND estado=1"
        );
        if ($check_modelo->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El modelo seleccionado no es valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        $check_placa = mainModel::ejecutar_consulta_simple(
            "SELECT id_vehiculo FROM vehiculos WHERE placa='$placa'"
        );
        if ($check_placa->rowCount() > 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La placa ya esta registrada",
                "Tipo" => "error"
            ]);
            exit();
        }

        $datos = [
            "id_cliente" => $cliente,
            "id_modeloauto" => $modelo,
            "color" => $color,
            "placa" => $placa,
            "anho" => $anho,
            "nro_serie" => $serie,
            "estado" => $estado
        ];

        vehiculoModelo::agregar_vehiculo_modelo($datos);

        echo json_encode([
            "Alerta" => "recargar",
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
        if (!mainModel::tienePermiso('vehiculo.editar')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para actualizar vehiculos",
                "Tipo" => "error"
            ]);
            exit();
        }

        $id = mainModel::decryption($_POST['vehiculo_id_up']);
        $id = mainModel::limpiar_string($id);

        $datos = [
            "id_vehiculo" => $id,
            "id_cliente" => mainModel::limpiar_string($_POST['cliente_up']),
            "id_modeloauto" => mainModel::limpiar_string($_POST['modelo_up']),
            "color" => mainModel::limpiar_string($_POST['color_up']),
            "placa" => mainModel::limpiar_string($_POST['placa_up']),
            "anho" => mainModel::limpiar_string($_POST['anho_up']),
            "nro_serie" => mainModel::limpiar_string($_POST['serie_up']),
            "estado" => mainModel::limpiar_string($_POST['estado_up'])
        ];

        $check_vehiculo = mainModel::ejecutar_consulta_simple(
            "SELECT id_vehiculo, placa FROM vehiculos WHERE id_vehiculo='$id'"
        );
        if ($check_vehiculo->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El vehiculo no existe en el sistema",
                "Tipo" => "error"
            ]);
            exit();
        }
        $vehiculo_actual = $check_vehiculo->fetch();

        if ($datos['id_cliente'] == "" || $datos['id_modeloauto'] == "" || $datos['color'] == "" || $datos['placa'] == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe completar los campos obligatorios",
                "Tipo" => "error"
            ]);
            exit();
        }

        if ($datos['estado'] != "0" && $datos['estado'] != "1") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El estado seleccionado no es valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        $check_cliente = mainModel::ejecutar_consulta_simple(
            "SELECT id_cliente FROM clientes WHERE id_cliente='{$datos['id_cliente']}' AND estado_cliente=1"
        );
        if ($check_cliente->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El cliente seleccionado no es valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        $check_modelo = mainModel::ejecutar_consulta_simple(
            "SELECT id_modeloauto FROM modelo_auto WHERE id_modeloauto='{$datos['id_modeloauto']}' AND estado=1"
        );
        if ($check_modelo->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El modelo seleccionado no es valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        if ($datos['placa'] != $vehiculo_actual['placa']) {
            $check_placa = mainModel::ejecutar_consulta_simple(
                "SELECT id_vehiculo FROM vehiculos WHERE placa='{$datos['placa']}'"
            );
            if ($check_placa->rowCount() > 0) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto" => "La placa ya esta registrada",
                    "Tipo" => "error"
                ]);
                exit();
            }
        }

        vehiculoModelo::actualizar_vehiculo_modelo($datos);

        echo json_encode([
            "Alerta" => "redireccionar_confirmado",
            "URL" => SERVERURL . "vehiculo-nuevo/",
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
        if (!mainModel::tienePermiso('vehiculo.eliminar')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Advertencia!",
                "Texto" => "No posee los permisos necesarios para realizar esta acción",
                "Tipo" => "error"
            ]);
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

    public function buscar_cliente_controlador()
    {
        $term = mainModel::limpiar_string($_POST['term']);

        $datos = vehiculoModelo::buscar_cliente_modelo($term);

        $resultado = [];

        foreach ($datos as $row) {
            $resultado[] = [
                "id" => $row['id_cliente'],
                "text" => $row['cliente']
            ];
        }

        return json_encode($resultado);
    }
}
