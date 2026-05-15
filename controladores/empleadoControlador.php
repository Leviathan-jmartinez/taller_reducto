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
        if (!mainModel::tienePermiso('empleado.crear')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para registrar empleados",
                "Tipo" => "error"
            ]);
            exit();
        }

        $datos = [
            "cargo"        => mainModel::limpiar_string($_POST['cargo_reg']),
            "sucursal"     => mainModel::limpiar_string($_POST['sucursal_reg']),
            "nombre"       => mainModel::limpiar_string($_POST['nombre_reg']),
            "apellido"     => mainModel::limpiar_string($_POST['apellido_reg']),
            "direccion"    => mainModel::limpiar_string($_POST['direccion_reg']),
            "celular"      => mainModel::limpiar_string($_POST['celular_reg']),
            "cedula"       => mainModel::limpiar_string($_POST['cedula_reg']),
            "estado_civil" => mainModel::limpiar_string($_POST['estado_civil_reg']),
            "estado"       => 1
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
        if (!mainModel::tienePermiso('empleado.editar')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para actualizar empleados",
                "Tipo" => "error"
            ]);
            exit();
        }

        $id = mainModel::decryption($_POST['empleado_id_up']);

        $datos = [
            "id"           => $id,
            "cargo"        => mainModel::limpiar_string($_POST['cargo_up']),
            "sucursal"     => mainModel::limpiar_string($_POST['sucursal_up']),
            "nombre"       => mainModel::limpiar_string($_POST['nombre_up']),
            "apellido"     => mainModel::limpiar_string($_POST['apellido_up']),
            "direccion"    => mainModel::limpiar_string($_POST['direccion_up']),
            "celular"      => mainModel::limpiar_string($_POST['celular_up']),
            "cedula"       => mainModel::limpiar_string($_POST['cedula_up']),
            "estado_civil" => mainModel::limpiar_string($_POST['estado_civil_up']),
            "estado"       => mainModel::limpiar_string($_POST['estado_up'])
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
        if (!mainModel::tienePermiso('empleado.eliminar')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Advertencia!",
                "Texto" => "No posee los permisos necesarios para realizar esta acción",
                "Tipo" => "error"
            ]);
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


    public function listar_empleados_controlador($pagina, $registros, $url, $busqueda)
    {
        $pagina = (int) mainModel::limpiar_string($pagina);
        $registros = (int) mainModel::limpiar_string($registros);
        $url = SERVERURL . mainModel::limpiar_string($url) . "/";

        $pagina = ($pagina > 0) ? $pagina : 1;
        $inicio = ($pagina - 1) * $registros;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        /* ===== FILTROS ===== */
        $filtrosSQL = "";

        if ($busqueda != "") {
            $busqueda = mainModel::limpiar_string($busqueda);

            $filtrosSQL = " AND (
            e.nombre LIKE '%$busqueda%' OR
            e.apellido LIKE '%$busqueda%' OR
            e.nro_cedula LIKE '%$busqueda%'
        )";
        }

        /* ===== DATOS ===== */
        $res = empleadoModelo::listar_empleados_modelo($inicio, $registros, $filtrosSQL);

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        /* ===== TABLA ===== */
        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>Empleado</th>
                <th>Cargo</th>
                <th>Sucursal</th>';

        if (mainModel::tienePermiso('empleado.editar')) {
            $tabla .= '<th>ACTUALIZAR</th>';
        }
        if (mainModel::tienePermiso('empleado.eliminar')) {
            $tabla .= '<th>ELIMINAR</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;

            foreach ($datos as $row) {

                $tabla .= '<tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $row['apellido'] . ' ' . $row['nombre'] . '</td>
                <td>' . $row['cargo'] . '</td>
                <td>' . $row['sucursal'] . '</td>';

                if (mainModel::tienePermiso('empleado.editar')) {
                    $tabla .= '<td>
                    <a href="' . SERVERURL . 'empleado-actualizar/' . mainModel::encryption($row['idempleados']) . '/"
                    class="btn btn-success btn-sm">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </td>';
                }

                if (mainModel::tienePermiso('empleado.eliminar')) {
                    $tabla .= '<td>
                    <form class="FormularioAjax"
                        action="' . SERVERURL . 'ajax/empleadoAjax.php"
                        method="POST"
                        data-form="delete">

                        <input type="hidden"
                        name="empleado_id_del"
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

            $reg_final = $contador - 1;
        } else {
            $tabla .= '<tr>
            <td colspan="6" class="text-center">No hay registros</td>
        </tr>';
        }

        $tabla .= '</tbody></table></div>';

        /* ===== PAGINADOR ===== */
        if ($total >= 1 && $pagina <= $Npaginas) {

            $tabla .= '<p class="text-right">
            Mostrando ' . $reg_inicio . ' al ' . $reg_final . ' de ' . $total . '
        </p>';

            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        return $tabla;
    }
}
