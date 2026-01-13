<?php
if ($peticionAjax) {
    require_once "../modelos/empleadoModelo.php";
} else {
    require_once "./modelos/empleadoModelo.php";
}

class empleadoControlador extends empleadoModelo
{
    /* ========= LISTAS ========= */
    public function listar_cargos_controlador()
    {
        return empleadoModelo::obtener_cargos_modelo();
    }

    public function listar_sucursales_controlador()
    {
        return empleadoModelo::obtener_sucursales_modelo();
    }

    /* ========= DATOS ========= */
    public function datos_empleado_controlador($tipo, $id)
    {
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_string($id);
        return empleadoModelo::datos_empleado_modelo($tipo, $id);
    }

    /* ========= AGREGAR ========= */
    public function agregar_empleado_controlador()
    {
        $datos = [
            "cargo"           => mainModel::limpiar_string($_POST['cargo_reg']),
            "sucursal"        => mainModel::limpiar_string($_POST['sucursal_reg']),
            "nombre"          => mainModel::limpiar_string($_POST['nombre_reg']),
            "apellido"        => mainModel::limpiar_string($_POST['apellido_reg']),
            "direccion"       => mainModel::limpiar_string($_POST['direccion_reg']),
            "celular"         => mainModel::limpiar_string($_POST['celular_reg']),
            "cedula"          => mainModel::limpiar_string($_POST['cedula_reg']),
            "estado_civil"    => mainModel::limpiar_string($_POST['estado_civil_reg']),
            "empleado_estado" => mainModel::limpiar_string($_POST['empleado_estado_reg']),
            "estado"          => mainModel::limpiar_string($_POST['estado_reg'])
        ];

        if ($datos['nombre'] == "" || $datos['apellido'] == "" || $datos['cedula'] == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Campos obligatorios incompletos",
                "Tipo" => "error"
            ]);
            exit();
        }

        $check = mainModel::ejecutar_consulta_simple(
            "SELECT idempleados FROM empleados WHERE nro_cedula='{$datos['cedula']}'"
        );
        if ($check->rowCount() > 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La cédula ya está registrada",
                "Tipo" => "error"
            ]);
            exit();
        }

        empleadoModelo::agregar_empleado_modelo($datos);

        echo json_encode([
            "Alerta" => "limpiar",
            "Titulo" => "Empleado",
            "Texto" => "Empleado registrado correctamente",
            "Tipo" => "success"
        ]);
    }

    /* ========= ACTUALIZAR ========= */
    public function actualizar_empleado_controlador()
    {
        $id = mainModel::decryption($_POST['empleado_id_up']);

        $datos = [
            "id"              => $id,
            "cargo"           => mainModel::limpiar_string($_POST['cargo_up']),
            "sucursal"        => mainModel::limpiar_string($_POST['sucursal_up']),
            "nombre"          => mainModel::limpiar_string($_POST['nombre_up']),
            "apellido"        => mainModel::limpiar_string($_POST['apellido_up']),
            "direccion"       => mainModel::limpiar_string($_POST['direccion_up']),
            "celular"         => mainModel::limpiar_string($_POST['celular_up']),
            "cedula"          => mainModel::limpiar_string($_POST['cedula_up']),
            "estado_civil"    => mainModel::limpiar_string($_POST['estado_civil_up']),
            "empleado_estado" => mainModel::limpiar_string($_POST['empleado_estado_up']),
            "estado"          => mainModel::limpiar_string($_POST['estado_up'])
        ];

        empleadoModelo::actualizar_empleado_modelo($datos);

        echo json_encode([
            "Alerta" => "recargar",
            "Titulo" => "Empleado",
            "Texto" => "Empleado actualizado correctamente",
            "Tipo" => "success"
        ]);
    }

    /* ========= ELIMINAR (CON BLOQUEO) ========= */
    public function eliminar_empleado_controlador()
    {
        $id = mainModel::decryption($_POST['empleado_id_del']);
        $id = mainModel::limpiar_string($id);

        $check = mainModel::ejecutar_consulta_simple(
            "SELECT idempleados, estado 
         FROM empleados 
         WHERE idempleados = '$id'"
        );

        if ($check->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "El empleado no existe en el sistema",
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

        $stmt = empleadoModelo::eliminar_empleado_modelo($id);

        if ($stmt->rowCount() > 0) {

            // Verificar cómo quedó
            $verificar = mainModel::ejecutar_consulta_simple(
                "SELECT estado 
             FROM empleados 
             WHERE idempleados = '$id'"
            );

            if ($verificar->rowCount() > 0) {
                // Sigue existiendo → fue desactivado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Empleado desactivado",
                    "Texto"  => "El empleado ya tiene movimientos asociados, por lo que fue desactivado.",
                    "Tipo"   => "warning"
                ];
            } else {
                // Ya no existe → fue eliminado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Empleado eliminado",
                    "Texto"  => "El empleado fue eliminado correctamente.",
                    "Tipo"   => "success"
                ];
            }
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "No se pudo eliminar el empleado seleccionado",
                "Tipo"   => "error"
            ];
        }

        echo json_encode($alerta);
    }


    public function paginador_empleados_controlador(
        $pagina,
        $registros,
        $privilegio,
        $url,
        $busqueda
    ) {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $busqueda = mainModel::limpiar_string($busqueda);

        $url = SERVERURL . mainModel::limpiar_string($url) . "/";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        if ($busqueda != "") {
            $consulta = "
        SELECT SQL_CALC_FOUND_ROWS e.*, 
               c.descripcion AS cargo,
               s.suc_descri AS sucursal
        FROM empleados e
        INNER JOIN cargos c ON c.idcargos = e.idcargos
        INNER JOIN sucursales s ON s.id_sucursal = e.id_sucursal
        WHERE (
            e.nombre LIKE '%$busqueda%' OR
            e.apellido LIKE '%$busqueda%' OR
            e.nro_cedula LIKE '%$busqueda%'
        )
        ORDER BY e.apellido ASC
        LIMIT $inicio,$registros";
        } else {
            $consulta = "
        SELECT SQL_CALC_FOUND_ROWS e.*, 
               c.descripcion AS cargo,
               s.suc_descri AS sucursal
        FROM empleados e
        INNER JOIN cargos c ON c.idcargos = e.idcargos
        INNER JOIN sucursales s ON s.id_sucursal = e.id_sucursal
        ORDER BY e.apellido ASC
        LIMIT $inicio,$registros";
        }

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta)->fetchAll();
        $total = (int)$conexion->query("SELECT FOUND_ROWS()")->fetchColumn();
        $Npaginas = ceil($total / $registros);

        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
        <tr class="text-center">
            <th>#</th>
        <th>Empleado</th>
        <th>Cargo</th>
        <th>Sucursal</th>';

        if ($privilegio == 1 || $privilegio == 2) {
            $tabla .= '<th>Actualizar</th><th>Eliminar</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1) {
            $contador = $inicio + 1;
            foreach ($datos as $row) {
                $tabla .= '<tr class="text-center">
            <td>' . $contador . '</td>
            <td>' . $row['apellido'] . ' ' . $row['nombre'] . '</td>
            <td>' . $row['cargo'] . '</td>
            <td>' . $row['sucursal'] . '</td>';

                if ($privilegio == 1 || $privilegio == 2) {
                    $tabla .= '
                <td>
                    <a href="' . SERVERURL . 'empleado-actualizar/' . mainModel::encryption($row['idempleados']) . '/" 
                    class="btn btn-success btn-sm">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </td>
                <td>
                    <form class="FormularioAjax"
                        action="' . SERVERURL . 'ajax/empleadoAjax.php"
                        method="POST"
                        data-form="delete">
                        <input type="hidden" name="empleado_id_del"
                        value="' . mainModel::encryption($row['idempleados']) . '">
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </form>
                </td>';
                }

                $tabla .= '</tr>';
                $contador++;
            }
        } else {
            $tabla .= '<tr><td colspan="6">No hay registros</td></tr>';
        }

        $tabla .= '</tbody></table></div>';

        if ($total > 0) {
            $tabla .= '<p class="text-right">
        Mostrando ' . $inicio . ' al ' . ($inicio + count($datos)) . ' de ' . $total . '
        </p>';
            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        echo $tabla;
    }
}
