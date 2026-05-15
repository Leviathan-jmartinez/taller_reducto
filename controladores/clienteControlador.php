<?php
if ($peticionAjax) {
    require_once "../modelos/clienteModelo.php";
} else {
    require_once "./modelos/clienteModelo.php";
}

class clienteControlador extends clienteModelo
{
    /** controlador agregar cliente*/
    public function agregar_cliente_controlador()
    {
        if (!mainModel::tienePermiso('cliente.crear')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para registrar clientes",
                "Tipo" => "error"
            ]);
            exit();
        }

        $doc = mainModel::limpiar_string($_POST['cliente_doc_reg']);
        $nombre = mainModel::limpiar_string($_POST['cliente_nombre_reg']);
        $apellido = mainModel::limpiar_string($_POST['cliente_apellido_reg']);
        $telefono = mainModel::limpiar_string($_POST['cliente_telefono_reg']);
        $email = mainModel::limpiar_string($_POST['cliente_email_reg']);
        $direccion = mainModel::limpiar_string($_POST['cliente_direccion_reg']);

        $ciudad = isset($_POST['ciudad_reg']) ? (int) $_POST['ciudad_reg'] : 0;

        // 🔥 AHORA BIEN MAPEADO
        $tipo_documento = isset($_POST['tipo_documento_reg']) ? mainModel::limpiar_string($_POST['tipo_documento_reg']) : "";
        $dv = isset($_POST['cliente_dv_reg']) ? mainModel::limpiar_string($_POST['cliente_dv_reg']) : "";
        $estado_civil = isset($_POST['cliente_estadoC_reg']) ? mainModel::limpiar_string($_POST['cliente_estadoC_reg']) : "";

        $estado = "1";

        if ($doc == "" || $nombre == "" || $direccion == "" || $ciudad <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "Debe completar los campos obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_doc = mainModel::ejecutar_consulta_simple("SELECT doc_number FROM clientes WHERE doc_number='$doc'");
        if ($check_doc->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El cliente ya se encuentra registrado!",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $datos_cliente = [
            "doc_number" => $doc,
            "nombre_cliente" => $nombre,
            "apellido_cliente" => $apellido,
            "celular_cliente" => $telefono,
            "email_cliente" => $email,
            "direccion_cliente" => $direccion,
            "id_ciudad" => (int)$ciudad,
            "doc_type" => $tipo_documento,  
            "digito_v" => $dv,              
            "estado_civil" => $estado_civil,
            "estado_cliente" => $estado
        ];

        $agregar_cliente = clienteModelo::agregar_cliente_modelo($datos_cliente);

        if ($agregar_cliente->rowCount() == 1) {
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Cliente Registrado",
                "Texto" => "Los datos fueron registrados correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo registrar",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
    }
    /** fin controlador */

    /**Controlador listar clientes */
    public function listar_cliente_controlador($pagina, $registros, $url, $busqueda)
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $busqueda = mainModel::limpiar_string($busqueda);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = ($pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina - 1) * $registros;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        /* ===== FILTRO (OR MANUAL) ===== */

        $filtrosSQL = "";

        if ($busqueda != "") {

            $busqueda = mainModel::limpiar_string($busqueda);

            $filtrosSQL .= " AND (
            doc_number LIKE '%$busqueda%' 
            OR nombre_cliente LIKE '%$busqueda%'
            OR apellido_cliente LIKE '%$busqueda%'
        )";
        }

        /* ===== DATOS ===== */

        $res = clienteModelo::listar_clientes_modelo($inicio, $registros, $filtrosSQL);

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        /* ===== TABLA ===== */

        $tabla .= '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center roboto-medium">
                <th>#</th>
                <th>CI</th>
                <th>CLIENTE</th>
                <th>TELÉFONO</th>
                <th>DIRECCIÓN</th>';

        if (mainModel::tienePermiso('cliente.editar')) {
            $tabla .= '<th>ACTUALIZAR</th>';
        }
        if (mainModel::tienePermiso('cliente.eliminar')) {
            $tabla .= '<th>ELIMINAR</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;

            foreach ($datos as $rows) {

                $tabla .= '
            <tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $rows['doc_number'] . '</td>
                <td>' . $rows['nombre_cliente'] . ' ' . $rows['apellido_cliente'] . '</td>
                <td>' . $rows['celular_cliente'] . '</td>
                <td>
                    <button type="button" class="btn btn-info"
                        data-toggle="popover"
                        data-trigger="hover"
                        title="' . $rows['nombre_cliente'] . ' ' . $rows['apellido_cliente'] . '"
                        data-content="' . $rows['direccion_cliente'] . '">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </td>';

                if (mainModel::tienePermiso('cliente.editar')) {
                    $tabla .= '
                <td>
                    <a href="' . SERVERURL . 'cliente-actualizar/' . mainModel::encryption($rows['id_cliente']) . '/"
                    class="btn btn-success">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </td>';
                }

                if (mainModel::tienePermiso('cliente.eliminar')) {
                    $tabla .= '
                <td>
                    <form class="FormularioAjax"
                        action="' . SERVERURL . 'ajax/clienteAjax.php"
                        method="POST"
                        data-form="delete">

                        <input type="hidden"
                        name="cliente_id_del"
                        value="' . mainModel::encryption($rows['id_cliente']) . '">

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
            <td colspan="6">No hay registros en el sistema</td>
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
    /**fin controlador */

    /**Controlador eliminar cliente */
    public function eliminar_cliente_controlador()
    {
        $id = mainModel::decryption($_POST['cliente_id_del']);
        $id = mainModel::limpiar_string($id);

        $check_client = mainModel::ejecutar_consulta_simple(
            "SELECT id_cliente, estado_cliente 
         FROM clientes 
         WHERE id_cliente = '$id'"
        );

        if ($check_client->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "El cliente no existe en el sistema",
                "Tipo"   => "error"
            ]);
            exit();
        }

        session_start(['name' => 'STR']);
        if (!mainModel::tienePermiso('cliente.eliminar')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Advertencia!",
                "Texto" => "No posee los permisos necesarios para realizar esta acción",
                "Tipo" => "error"
            ]);
        }

        $stmt = clienteModelo::eliminar_cliente_modelo($id);

        if ($stmt->rowCount() > 0) {

            // Verificar cómo quedó
            $verificar = mainModel::ejecutar_consulta_simple(
                "SELECT estado_cliente
             FROM clientes 
             WHERE id_cliente = '$id'"
            );

            if ($verificar->rowCount() > 0) {
                // Sigue existiendo → fue desactivado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Cliente desactivado",
                    "Texto"  => "El cliente ya tiene movimientos asociados, por lo que fue desactivado.",
                    "Tipo"   => "warning"
                ];
            } else {
                // Ya no existe → fue eliminado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Cliente eliminado",
                    "Texto"  => "El cliente fue eliminado correctamente.",
                    "Tipo"   => "success"
                ];
            }
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "No se pudo eliminar el cliente seleccionado",
                "Tipo"   => "error"
            ];
        }

        echo json_encode($alerta);
    }
    /**fin controlador */

    /** controlador datos clientes  */
    public function datos_cliente_controlador($tipo, $id)
    {
        $tipo = mainModel::limpiar_string($tipo);
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_string($id);
        return clienteModelo::datos_cliente_modelo($tipo, $id);
    }
    /**fin controlador */

    public function listar_ciudades_controlador()
    {
        $ciudades = clienteModelo::obtener_ciudades_modelo();
        $options = '<option value="" selected>Seleccione una opción</option>';

        foreach ($ciudades as $ciudad) {
            $options .= '<option value="' . $ciudad['id_ciudad'] . '">' . $ciudad['ciu_descri'] . '</option>';
        }

        return $options;
    }

    public function listar_ciudades_controlador_up()
    {
        $ciudades = clienteModelo::obtener_ciudades_modelo(); // Llamamos al método protegido desde la clase hija
        return $ciudades;
    }
    /** controlador actualizar cliente */
    public function actualizar_cliente_controlador()
    {
        if (!mainModel::tienePermiso('cliente.editar')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para actualizar clientes",
                "Tipo" => "error"
            ]);
            exit();
        }

        $id = mainModel::decryption($_POST['cliente_id_up']);
        $id = mainModel::limpiar_string($id);

        $doc = mainModel::limpiar_string($_POST['cliente_doc_up']);
        $nombre = mainModel::limpiar_string($_POST['cliente_nombre_up']);
        $apellido = mainModel::limpiar_string($_POST['cliente_apellido_up']);
        $telefono = mainModel::limpiar_string($_POST['cliente_telefono_up']);
        $email = mainModel::limpiar_string($_POST['cliente_email_up']);
        $direccion = mainModel::limpiar_string($_POST['cliente_direccion_up']);
        $ciudad = isset($_POST['ciudad_up']) ? (int) $_POST['ciudad_up'] : 0;

        $tipo_documento = isset($_POST['tipo_documento_up']) ? mainModel::limpiar_string($_POST['tipo_documento_up']) : "CI";
        $dv = isset($_POST['cliente_dv_up']) ? mainModel::limpiar_string($_POST['cliente_dv_up']) : "";
        $estado_civil = isset($_POST['cliente_estadoC_up']) ? mainModel::limpiar_string($_POST['cliente_estadoC_up']) : "";
        $estado = isset($_POST['usuario_estado_up']) ? mainModel::limpiar_string($_POST['usuario_estado_up']) : "1";

        /* ===== VALIDAR EXISTENCIA ===== */
        $check = mainModel::ejecutar_consulta_simple("SELECT * FROM clientes WHERE id_cliente='$id'");
        if ($check->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El cliente no existe",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $datos_actuales = $check->fetch();

        /* ===== VALIDACIONES ===== */
        if ($doc == "" || $nombre == "" || $direccion == "" || $ciudad == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No has llenado todos los campos obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if ($ciudad < 0 || $ciudad == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La ciudad seleccionada no corresponde",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        /* ===== VALIDAR ESTADO ===== */
        if ($estado != "0" && $estado != "1") {
            $estado = "1";
        }

        /* ===== DUPLICADO ===== */
        if ($doc != $datos_actuales['doc_number']) {
            $check_doc = mainModel::ejecutar_consulta_simple("SELECT doc_number FROM clientes WHERE doc_number='$doc'");
            if ($check_doc->rowCount() > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El documento ya existe",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }

        /* ===== DATA ===== */
        $datos_cliente = [
            "doc_number" => $doc,
            "nombre_cliente" => $nombre,
            "apellido_cliente" => $apellido,
            "celular_cliente" => $telefono,
            "email_cliente" => $email,
            "direccion_cliente" => $direccion,
            "id_ciudad" => (int)$ciudad,
            "doc_type" => $tipo_documento,
            "digito_v" => $dv,
            "estado_civil" => $estado_civil,
            "estado_cliente" => $estado,
            "id_cliente" => $id
        ];

        $actualizar_cliente = clienteModelo::actualizar_cliente_modelo($datos_cliente);

        /* ===== FIX FINAL ===== */
        if ($actualizar_cliente) {

            $alerta = [
                "Alerta" => "redireccionar_confirmado",
                "Titulo" => "Cliente Actualizado",
                "Texto" => "Los datos fueron actualizados correctamente",
                "Tipo" => "success",
                "URL" => SERVERURL . "cliente-nuevo/"
            ];
        } else {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido actualizar el cliente",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
    }
    /**fin controlador */
}
