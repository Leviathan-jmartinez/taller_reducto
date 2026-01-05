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
        $pagina     = mainModel::limpiar_string($pagina);
        $registros  = mainModel::limpiar_string($registros);
        $busqueda   = mainModel::limpiar_string($busqueda);

        $url = SERVERURL . $url . "/";
        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina * $registros) - $registros;

        if ($busqueda != "") {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS v.*,
                                CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
                                m.mod_descri,
                                co.col_descripcion
                         FROM vehiculos v
                         INNER JOIN clientes c ON c.id_cliente = v.id_cliente
                         INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
                         INNER JOIN colores co ON co.id_color = v.id_color
                         WHERE (v.placa LIKE '%$busqueda%'
                            OR c.nombre_cliente LIKE '%$busqueda%'
                            OR c.apellido_cliente LIKE '%$busqueda%')
                         ORDER BY v.placa ASC
                         LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS v.*,
                                CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
                                m.mod_descri,
                                co.col_descripcion
                         FROM vehiculos v
                         INNER JOIN clientes c ON c.id_cliente = v.id_cliente
                         INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
                         INNER JOIN colores co ON co.id_color = v.id_color
                         ORDER BY v.placa ASC
                         LIMIT $inicio,$registros";
        }

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta)->fetchAll();

        $total = (int)$conexion->query("SELECT FOUND_ROWS()")->fetchColumn();
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

        if ($privilegio <= 2) {
            $tabla .= '<th>ACTUALIZAR</th><th>ELIMINAR</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {
            $contador = $inicio + 1;

            foreach ($datos as $row) {
                $estado = $row['estado'] == 1
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';

                $tabla .= '<tr class="text-center">
                    <td>' . $contador . '</td>
                    <td>' . $row['placa'] . '</td>
                    <td>' . $row['cliente'] . '</td>
                    <td>' . $row['mod_descri'] . '</td>
                    <td>' . $row['col_descripcion'] . '</td>
                    <td>' . $estado . '</td>';

                if ($privilegio <= 2) {
                    $tabla .= '
                    <td>
                        <a href="' . SERVERURL . 'vehiculo-actualizar/' . mainModel::encryption($row['id_vehiculo']) . '/" class="btn btn-success">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </td>
                    <td>
                        <form class="FormularioAjax"
                              action="' . SERVERURL . 'ajax/vehiculoAjax.php"
                              method="POST"
                              data-form="delete">
                            <input type="hidden" name="vehiculo_id_del"
                                   value="' . mainModel::encryption($row['id_vehiculo']) . '">
                            <button type="submit" class="btn btn-warning">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>';
                }

                $tabla .= '</tr>';
                $contador++;
            }
        } else {
            $tabla .= '<tr class="text-center">
                <td colspan="8">No hay registros</td>
            </tr>';
        }

        $tabla .= '</tbody></table></div>';

        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '<p class="text-right">
                Mostrando ' . ($inicio + 1) . ' al ' . ($contador - 1) . ' de ' . $total . '
            </p>';
            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        echo $tabla;
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

        $check = mainModel::ejecutar_consulta_simple(
            "SELECT id_vehiculo FROM recepcion_servicio WHERE id_vehiculo='$id' LIMIT 1"
        );

        if ($check->rowCount() > 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El vehículo tiene recepciones asociadas",
                "Tipo" => "error"
            ]);
            exit();
        }

        vehiculoModelo::eliminar_vehiculo_modelo($id);

        echo json_encode([
            "Alerta" => "recargar",
            "Titulo" => "Vehículo",
            "Texto" => "Vehículo eliminado correctamente",
            "Tipo" => "success"
        ]);
    }
}
