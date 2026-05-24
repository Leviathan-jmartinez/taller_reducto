<?php
if ($peticionAjax) {
    require_once "../modelos/usuarioModelo.php";
} else {
    require_once "./modelos/usuarioModelo.php";
}

class usuarioControlador extends usuarioModelo
{
    /** controlador agregar usuario*/
    public function agregar_usuario_controlador()
    {
        if (!mainModel::tienePermiso('usuarios.crear')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para registrar usuarios",
                "Tipo" => "error"
            ]);
            exit();
        }

        /**inicio */
        $ci = mainModel::limpiar_string($_POST['usuario_dni_reg'] ?? "");
        $nombre = mainModel::limpiar_string($_POST['usuario_nombre_reg'] ?? "");
        $apellido = mainModel::limpiar_string($_POST['usuario_apellido_reg'] ?? "");
        $telefono = mainModel::limpiar_string($_POST['usuario_telefono_reg'] ?? "");
        $nick = mainModel::limpiar_string($_POST['usuario_usuario_reg'] ?? "");
        $email = mainModel::limpiar_string($_POST['usuario_email_reg'] ?? "");

        $clave1 = mainModel::limpiar_string($_POST['usuario_clave_1_reg'] ?? "");
        $clave2 = mainModel::limpiar_string($_POST['usuario_clave_2_reg'] ?? "");

        /** Comprobar campos vacios */
        if ($ci == "" || $nombre == "" || $apellido == "" || $nick == "" || $clave1 == "" || $clave2 == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No has llenado todos los campos que son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**verificar integridad de datos  */
        if (mainModel::verificarDatos("[0-9]{5,10}", $ci)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo CI no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{2,50}", $nombre)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo NOMBRE no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{2,50}", $apellido)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo APELLIDO no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($telefono != "") {
            if (mainModel::verificarDatos("[0-9()+ -]{6,50}", $telefono)) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El formato del campo TELEFONO no es valido",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
        if (mainModel::verificarDatos("[a-zA-Z0-9]{3,20}", $nick)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo USUARIO no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-Z0-9$@._-]{7,18}", $clave1) || mainModel::verificarDatos("[a-zA-Z0-9$@._-]{7,18}", $clave2)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "Las claves no coinciden con el formato solicitado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**Comprobacion de registros */
        $check_ci = mainModel::ejecutar_consulta_simple("SELECT usu_ci from usuarios where usu_ci='$ci'");
        if ($check_ci->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El numero de CI ingresado ya se encuentra registrado!",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_nick = mainModel::ejecutar_consulta_simple("SELECT usu_nick from usuarios where usu_nick='$nick'");
        if ($check_nick->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El USUARIO ingresado ya se encuentra registrado!",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**comprobar email */
        if ($email != "") {
            if (strlen($email) > 50) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El EMAIL no puede superar 50 caracteres",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $check_email = mainModel::ejecutar_consulta_simple("SELECT usu_email from usuarios where usu_email='$email'");
                if ($check_email->rowCount() > 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrio un error inesperado!",
                        "Texto" => "El EMAIL ingresado ya se encuentra registrado!",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "Ha ingresado un correo no valido!",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
        /**Comprobar claves */
        if ($clave1 != $clave2) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La claves ingresadas no coinciden, favor válidar",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $clave = mainModel::encryption($clave1);
        }
        $datos_usuario_reg = [
            "ci" => $ci,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "nick" => $nick,
            "email" => $email,
            "telefono" => $telefono,
            "clave" => $clave,
            "estado" => "1",
            "cambiar_clave" => "1"
        ];
        $agregar_usuario = usuarioModelo::agregar_usuario_modelo($datos_usuario_reg);
        if ($agregar_usuario->rowCount() == 1) {
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Usuario Registrado",
                "Texto" => "Los datos del usuario han sido registrados correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido registrar el usuario",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /**Fin controlador agregar_usuario_controlador */

    /**Controlador paginar usuarios */
    public function paginador_usuario_controlador($pagina, $registros, $id, $url, $busqueda)
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $id = mainModel::limpiar_string($id);
        $busqueda = mainModel::limpiar_string($busqueda);

        $url = SERVERURL . mainModel::limpiar_string($url) . "/";

        $pagina = ($pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina - 1) * $registros;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        /* ========= CONSULTA ========= */
        if ($busqueda != "") {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM usuarios 
        WHERE id_usuario != '$id' AND id_usuario !='1'
        AND (usu_ci LIKE '%$busqueda%' OR usu_nick LIKE '%$busqueda%')
        ORDER BY usu_nombre ASC LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM usuarios 
        WHERE id_usuario != '$id' AND id_usuario !='1'
        ORDER BY usu_nombre ASC LIMIT $inicio,$registros";
        }

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta)->fetchAll();

        $total = (int)$conexion->query("SELECT FOUND_ROWS()")->fetchColumn();
        $Npaginas = ceil($total / $registros);

        /* ========= TABLA ========= */
        $puede_ver_seguridad = mainModel::tienePermiso('usuarios.editar');
        $tiene_acciones = (
            mainModel::tienePermiso('usuarios.editar') ||
            mainModel::tienePermiso('usuarios.eliminar') ||
            mainModel::tienePermiso('usuarios.asignarrol') ||
            mainModel::tienePermiso('usuarios.asignarlocal')
        );

        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center roboto-medium">
                <th>#</th>
                <th>CI</th>
                <th>NOMBRE</th>
                <th>TELÉFONO</th>
                <th>USUARIO</th>
                <th>EMAIL</th>
                <th>ESTADO</th>';

        if ($puede_ver_seguridad) {
            $tabla .= '
                <th>INTENTOS</th>
                <th>BLOQUEO</th>';
        }

        if ($tiene_acciones) {
            $tabla .= '<th>ACCIONES</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;

            foreach ($datos as $rows) {

                $tabla .= '
            <tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $rows['usu_ci'] . '</td>
                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                <td>' . $rows['usu_telefono'] . '</td>
                <td>' . $rows['usu_nick'] . '</td>
                <td>' . $rows['usu_email'] . '</td>
                <td>' . ((int)$rows['usu_estado'] === 1 ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>') . '</td>';

                if ($puede_ver_seguridad) {
                    $intentosFallidos = isset($rows['usu_intentos_fallidos']) ? (int)$rows['usu_intentos_fallidos'] : 0;
                    $bloqueado = isset($rows['usu_bloqueado']) ? (int)$rows['usu_bloqueado'] : 0;
                    $cambiarClave = isset($rows['usu_cambiar_clave']) ? (int)$rows['usu_cambiar_clave'] : 0;

                    $tabla .= '
                <td>
                    <span class="badge badge-' . ($intentosFallidos >= 3 ? 'danger' : ($intentosFallidos > 0 ? 'warning' : 'success')) . '">' . $intentosFallidos . '/3</span>
                </td>
                <td>' . ($bloqueado == 1 ? '<span class="badge badge-danger">Bloqueada</span>' : '<span class="badge badge-success">Libre</span>') . ($cambiarClave == 1 ? '<br><span class="badge badge-warning">Debe cambiar clave</span>' : '') . '</td>';
                }

                /* ========= ACCIONES ========= */
                if ($tiene_acciones) {

                    $tabla .= '<td>';

                    // EDITAR
                    if (mainModel::tienePermiso('usuarios.editar')) {
                        $tabla .= '
                    <a href="' . SERVERURL . 'usuario-actualizar/' . mainModel::encryption($rows['id_usuario']) . '/"
                    class="btn btn-success btn-sm" data-toggle="tooltip" title="Editar usuario">
                        <i class="fas fa-sync-alt"></i>
                    </a> ';

                        if (isset($rows['usu_bloqueado']) && (int)$rows['usu_bloqueado'] === 1) {
                            $tabla .= '
                    <form class="FormularioAjax d-inline"
                        action="' . SERVERURL . 'ajax/usuarioAjax.php"
                        method="POST"
                        data-form="update">

                        <input type="hidden" name="accion" value="desbloquear_usuario">
                        <input type="hidden" name="id_usuario" value="' . mainModel::encryption($rows['id_usuario']) . '">

                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Desbloquear usuario">
                            <i class="fas fa-unlock"></i>
                        </button>
                    </form> ';
                        }
                    }

                    // ROLES
                    if (mainModel::tienePermiso('usuarios.asignarrol')) {
                        $tabla .= '
                    <button class="btn btn-info btn-sm btn-roles" 
                        data-id="' . mainModel::encryption($rows['id_usuario']) . '" data-toggle="tooltip" title="Asignar roles">
                         <i class="fas fa-user-tag"></i>
                    </button> ';
                    }

                    // SUCURSAL
                    if (mainModel::tienePermiso('usuarios.asignarlocal')) {
                        $tabla .= '
                        <button class="btn btn-warning btn-sm btn-sucursal"
                            data-id="' . mainModel::encryption($rows['id_usuario']) . '"
                            data-toggle="tooltip"
                            title="Asignar sucursal">
                            <i class="fas fa-store"></i>
                        </button> ';
                    }

                    // ELIMINAR
                    if (mainModel::tienePermiso('usuarios.eliminar')) {
                        $tabla .= '
                    <form class="FormularioAjax d-inline"
                        action="' . SERVERURL . 'ajax/usuarioAjax.php"
                        method="POST"
                        data-form="delete">

                        <input type="hidden"
                        name="usuario_id_del" 
                        value="' . mainModel::encryption($rows['id_usuario']) . '">

                        <button type="submit" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Eliminar usuario">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </form>';
                    }

                    $tabla .= '</td>';
                }

                $tabla .= '</tr>';
                $contador++;
            }

            $reg_final = $contador - 1;
        } else {
            $columnas = 7 + ($puede_ver_seguridad ? 2 : 0) + ($tiene_acciones ? 1 : 0);

            $tabla .= '<tr class="text-center">
            <td colspan="' . $columnas . '">No hay registros en el sistema</td>
        </tr>';
        }

        $tabla .= '</tbody></table></div>';

        /* ========= PAGINADOR ========= */
        if ($total >= 1 && $pagina <= $Npaginas) {

            $tabla .= '<p class="text-right">
            Mostrando ' . $reg_inicio . ' al ' . $reg_final . ' de ' . $total . '
        </p>';

            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        return $tabla;
    }
    /**fin controlador */

    /**controlador eliminar usuario */
    public function eliminar_usuario_controlador()
    {
        $usuario = mainModel::decryption($_POST['usuario_id_del']);
        $usuario = mainModel::limpiar_string($usuario);

        if ($usuario == 1) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "No podemos eliminar el usuario principal del sistema",
                "Tipo"   => "error"
            ]);
            exit();
        }

        $check_user = mainModel::ejecutar_consulta_simple(
            "SELECT id_usuario, usu_estado 
         FROM usuarios 
         WHERE id_usuario = '$usuario'"
        );

        if ($check_user->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "El usuario no existe en el sistema",
                "Tipo"   => "error"
            ]);
            exit();
        }

        session_start(['name' => 'STR']);
        if (!mainModel::tienePermiso('usuarios.eliminar')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Advertencia!",
                "Texto" => "No posee los permisos necesarios para realizar esta acción",
                "Tipo" => "error"
            ]);
        }

        $stmt = usuarioModelo::eliminar_usuario_modelo($usuario);

        if ($stmt->rowCount() > 0) {

            // Verificar cómo quedó
            $verificar = mainModel::ejecutar_consulta_simple(
                "SELECT usu_estado 
             FROM usuarios 
             WHERE id_usuario = '$usuario'"
            );

            if ($verificar->rowCount() > 0) {
                // Sigue existiendo → fue desactivado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Usuario desactivado",
                    "Texto"  => "El usuario ya tenía movimientos en el sistema, por lo que fue desactivado.",
                    "Tipo"   => "warning"
                ];
            } else {
                // Ya no existe → fue eliminado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Usuario eliminado",
                    "Texto"  => "El usuario fue eliminado correctamente.",
                    "Tipo"   => "success"
                ];
            }
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "No se pudo eliminar el usuario seleccionado",
                "Tipo"   => "error"
            ];
        }

        echo json_encode($alerta);
    }
    /**fin controlador */

    /**controlador datos usuario */
    public function datos_usuario_controlador($tipo, $id)
    {
        $tipo = mainModel::limpiar_string($tipo);
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_string($id);
        return usuarioModelo::datos_usuario_modelo($tipo, $id);
    }
    /**controlador actualizar usuario */

    public function actualizar_usuario_controlador()
    {
        // recibir ID usuario
        $id = mainModel::decryption($_POST['usuario_id_up']);
        $id = mainModel::limpiar_string($id);


        if (!mainModel::tienePermiso('usuarios.editar')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para actualizar usuarios",
                "Tipo" => "error"
            ]);
            exit();
        }

        // comparar registro si existe en la BD
        $checkUser = mainModel::ejecutar_consulta_simple("SELECT * FROM usuarios where id_usuario='$id'");
        if ($checkUser->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El usuario que intenta actualizar no existe en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $campos_usuario_up = $checkUser->fetch();
        }
        $ci = mainModel::limpiar_string($_POST['usuario_ci_up'] ?? "");
        $nombre = mainModel::limpiar_string($_POST['usuario_nombre_up'] ?? "");
        $apellido = mainModel::limpiar_string($_POST['usuario_apellido_up'] ?? "");
        $telefono = mainModel::limpiar_string($_POST['usuario_telefono_up'] ?? "");
        $nick = mainModel::limpiar_string($_POST['usuario_usuario_up'] ?? "");
        $email = mainModel::limpiar_string($_POST['usuario_email_up'] ?? "");
        /**validar estado si viene definido */
        if (isset($_POST['usuario_estado_up'])) {
            $estado = mainModel::limpiar_string($_POST['usuario_estado_up']);
        } else {
            $estado = $campos_usuario_up['usu_estado'];
        }
        $admin_user = mainModel::limpiar_string($_POST['usuario_admin'] ?? "");
        $admin_clave = mainModel::limpiar_string($_POST['clave_admin'] ?? "");
        $tipo_cuenta = mainModel::limpiar_string($_POST['tipo_cuenta'] ?? "");

        if ($tipo_cuenta != "propia" && $tipo_cuenta != "impropia") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El tipo de cuenta no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        $es_cuenta_propia = isset($_SESSION['id_str']) && (string)$_SESSION['id_str'] === (string)$id;

        if ($tipo_cuenta == "propia" && !$es_cuenta_propia) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La cuenta seleccionada no corresponde al usuario en sesion",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if ($tipo_cuenta == "propia") {
            $ci = $campos_usuario_up['usu_ci'];
            $nombre = $campos_usuario_up['usu_nombre'];
            $apellido = $campos_usuario_up['usu_apellido'];
            $nick = $campos_usuario_up['usu_nick'];
            $estado = $campos_usuario_up['usu_estado'];
        }

        if ($ci == "" || $nombre == "" || $apellido == "" || $nick == "" || $admin_user == "" || $admin_clave == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No has llenado todos los campos que son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**verificar integridad de datos  */
        if (mainModel::verificarDatos("[0-9]{5,10}", $ci)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo CI no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{2,50}", $nombre)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo NOMBRE no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{2,50}", $apellido)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo APELLIDO no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($telefono != "") {
            if (mainModel::verificarDatos("[0-9()+ -]{6,50}", $telefono)) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El formato del campo TELEFONO no es valido",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
        if (mainModel::verificarDatos("[a-zA-Z0-9]{3,20}", $nick)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo USUARIO no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificarDatos("[a-zA-Z0-9]{3,20}", $admin_user)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "Tu USUARIO no coincide con el formato solicitado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-Z0-9$@._-]{7,100}", $admin_clave)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "Tu CLAVE no coincide con el formato solicitado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $admin_clave = mainModel::encryption($admin_clave);

        if ($estado != "1" && $estado != "0") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El estado seleccionado no corresponde",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($ci != $campos_usuario_up['usu_ci']) {
            /**Comprobacion de registros */
            $check_ci = mainModel::ejecutar_consulta_simple("SELECT usu_ci from usuarios where usu_ci='$ci'");
            if ($check_ci->rowCount() > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El numero de CI ingresado ya se encuentra registrado!",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
        if ($nick != $campos_usuario_up['usu_nick']) {
            $check_nick = mainModel::ejecutar_consulta_simple("SELECT usu_nick from usuarios where usu_nick='$nick'");
            if ($check_nick->rowCount() > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El USUARIO ingresado ya se encuentra registrado!",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
        /**comprobar email */
        if ($email != $campos_usuario_up['usu_email'] && $email  != "") {
            if (strlen($email) > 50) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El email no puede superar 50 caracteres",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $check_email = mainModel::ejecutar_consulta_simple("SELECT usu_email from usuarios where usu_email='$email'");
                if ($check_email->rowCount() > 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrio un error inesperado!",
                        "Texto" => "El nuevo EMAIL ingresado ya se encuentra registrado!",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El email ingresado no es válido",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
        /**comprobar claves */
        if ($_POST['usuario_clave_nueva_1'] != "" && $_POST['usuario_clave_nueva_2'] != "") {
            if ($_POST['usuario_clave_nueva_1'] != $_POST['usuario_clave_nueva_2']) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "Las nuevas claves ingresadas no coinciden entre si",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            } else {
                if (mainModel::verificarDatos("[a-zA-Z0-9$@._-]{7,18}", $_POST['usuario_clave_nueva_1']) || mainModel::verificarDatos("[a-zA-Z0-9$@._-]{7,18}", $_POST['usuario_clave_nueva_2'])) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrio un error inesperado!",
                        "Texto" => "Las contraseñas nuevas deben tener entre 7 y 18 caracteres y solo puede contener letras, números y los símbolos: $ @ . _ - (sin espacios ni acentos).",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
                $clave = mainModel::encryption($_POST['usuario_clave_nueva_1']);
            }
        } else {
            $clave = $campos_usuario_up['usu_clave'];
        }
        /**comprobar credenciales  */
        if ($tipo_cuenta == "propia") {
            $check_cuenta = mainModel::ejecutar_consulta_simple("SELECT id_usuario from usuarios where usu_nick='$admin_user' and usu_clave = '$admin_clave' and id_usuario ='$id'");
        } else {
            if (!mainModel::tienePermiso('usuarios.editar')) {
                return json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Advertencia!",
                    "Texto" => "No posee los permisos necesarios para realizar esta acción",
                    "Tipo" => "error"
                ]);
            }
            $check_cuenta = mainModel::ejecutar_consulta_simple("SELECT id_usuario from usuarios where usu_nick='$admin_user' and usu_clave = '$admin_clave'");
        }
        if ($check_cuenta->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "Nombre y clave de usuario no válidos",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**preparando datos para envio al modelo */
        $datos_usuario_up = [
            "ci" => $ci,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "telefono" => $telefono,
            "email" => $email,
            "nick" => $nick,
            "clave" => $clave,
            "estado" => $estado,
            "iduser" => $id
        ];

        if (usuarioModelo::actualizar_usuario_modelo($datos_usuario_up)) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Usuario Actualizado",
                "Texto" => "Los datos del usuario han sido registrados correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido actualizar el usuario",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /**fin controlador */

    public function listar_usuarios_controlador()
    {
        if (
            !mainModel::tienePermiso('usuarios.ver')
        ) {
            return [];
        }

        return usuarioModelo::listar_usuarios_modelo();
    }


    public function roles_por_usuario_controlador()
    {
        if (!mainModel::tienePermiso('usuarios.asignarrol')) {
            return '<div class="alert alert-danger">Acceso denegado</div>';
        }

        $idUsuario = mainModel::decryption($_POST['id_usuario']);
        $idUsuario = mainModel::limpiar_string($idUsuario);

        $roles = usuarioModelo::obtener_roles_usuario_modelo($idUsuario);

        $html = '<div class="row">';

        foreach ($roles as $r) {

            $checked = $r['activo'] ? 'checked' : '';

            $html .= '
        <div class="col-md-6 mb-2">
            <div class="custom-control custom-checkbox">
                <input type="checkbox"
                    class="custom-control-input"
                    name="roles[]"
                    value="' . $r['id_rol'] . '"
                    id="rol_' . $r['id_rol'] . '"
                    ' . $checked . '>

                <label class="custom-control-label" for="rol_' . $r['id_rol'] . '">
                    ' . $r['nombre'] . '
                </label>
            </div>
        </div>';
        }

        $html .= '</div>';

        return $html;
    }


    public function guardar_roles_usuario_controlador()
    {
        if (!mainModel::tienePermiso('usuarios.asignarrol')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto"  => "No posee permisos para asignar roles",
                "Tipo"   => "error"
            ]);
        }

        $idUsuario = mainModel::decryption($_POST['id_usuario']);
        $idUsuario = mainModel::limpiar_string($idUsuario);

        if ($idUsuario == "") {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Debe seleccionar un usuario",
                "Tipo"   => "error"
            ]);
        }

        $checkUsuario = mainModel::ejecutar_consulta_simple(
            "SELECT id_usuario FROM usuarios WHERE id_usuario='$idUsuario'"
        );
        if ($checkUsuario->rowCount() <= 0) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "El usuario seleccionado no existe",
                "Tipo"   => "error"
            ]);
        }

        $roles = $_POST['roles'] ?? [];
        $roles = array_values(array_unique(array_map(function ($rol) {
            return mainModel::limpiar_string($rol);
        }, $roles)));

        foreach ($roles as $rol) {
            if ($rol == "" || !ctype_digit((string)$rol)) {
                return json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto"  => "Uno de los roles seleccionados no es valido",
                    "Tipo"   => "error"
                ]);
            }
        }

        if (!empty($roles)) {
            $rolesLista = implode(",", $roles);
            $checkRoles = mainModel::ejecutar_consulta_simple(
                "SELECT id_rol FROM roles WHERE id_rol IN ($rolesLista)"
            );
            if ($checkRoles->rowCount() != count($roles)) {
                return json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto"  => "Uno de los roles seleccionados no existe",
                    "Tipo"   => "error"
                ]);
            }
        }

        $res = usuarioModelo::guardar_roles_usuario_modelo($idUsuario, $roles);

        if ($res) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Roles actualizados",
                "Texto"  => "Los cambios se guardaron correctamente",
                "Tipo"   => "success"
            ]);
        } else {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "No se pudieron guardar los cambios",
                "Tipo"   => "error"
            ]);
        }
    }

    public function sucursal_por_usuario_controlador()
    {
        if (!mainModel::tienePermiso('usuarios.asignarlocal')) {
            return '<div class="alert alert-danger">Acceso denegado</div>';
        }

        $idUsuario = mainModel::decryption($_POST['id_usuario']);

        $data = usuarioModelo::obtener_sucursal_usuario_modelo($idUsuario);

        $sucursales = $data['sucursales'];
        $actual = $data['actual'];

        $html = '<div class="form-group">
        <label>Sucursal</label>
        <select name="id_sucursal" class="form-control select2" required>
        <option value="">Seleccione</option>';

        foreach ($sucursales as $s) {

            $selected = ($s['id_sucursal'] == $actual) ? 'selected' : '';

            $html .= '<option value="' . $s['id_sucursal'] . '" ' . $selected . '>
            ' . $s['suc_descri'] . '
        </option>';
        }

        $html .= '</select></div>';

        return $html;
    }

    public function asignar_sucursal_controlador()
    {
        if (!mainModel::tienePermiso('usuarios.asignarlocal')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto"  => "No posee permisos para asignar sucursal",
                "Tipo"   => "error"
            ]);
        }

        $idUsuario = mainModel::decryption($_POST['id_usuario']);
        $idUsuario = mainModel::limpiar_string($idUsuario);
        $idSucursal = mainModel::limpiar_string($_POST['id_sucursal'] ?? '');

        if ($idUsuario == "" || $idSucursal == "") {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Debe seleccionar un usuario y una sucursal",
                "Tipo"   => "error"
            ]);
        }

        $checkUsuario = mainModel::ejecutar_consulta_simple(
            "SELECT id_usuario FROM usuarios WHERE id_usuario='$idUsuario'"
        );
        if ($checkUsuario->rowCount() <= 0) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "El usuario seleccionado no existe",
                "Tipo"   => "error"
            ]);
        }

        $checkSucursal = mainModel::ejecutar_consulta_simple(
            "SELECT id_sucursal FROM sucursales WHERE id_sucursal='$idSucursal' AND estado=1"
        );
        if ($checkSucursal->rowCount() <= 0) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "La sucursal seleccionada no es valida",
                "Tipo"   => "error"
            ]);
        }

        $res = usuarioModelo::guardar_sucursal_usuario_modelo($idUsuario, $idSucursal);

        if ($res) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Sucursal actualizada",
                "Texto"  => "Se asignó correctamente",
                "Tipo"   => "success"
            ]);
        } else {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "No se pudieron guardar los cambios",
                "Tipo"   => "error"
            ]);
        }
    }

    public function listar_sucursales_select_controlador()
    {
        return usuarioModelo::obtener_sucursales_modelo();
    }

    public function desbloquear_usuario_controlador()
    {
        if (!mainModel::tienePermiso('usuarios.editar')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para desbloquear usuarios",
                "Tipo" => "error"
            ]);
        }

        $idUsuario = mainModel::decryption($_POST['id_usuario'] ?? '');
        $idUsuario = mainModel::limpiar_string($idUsuario);

        if ($idUsuario == "" || $idUsuario == "1") {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Usuario no valido",
                "Texto" => "No se puede desbloquear el usuario seleccionado",
                "Tipo" => "error"
            ]);
        }

        $checkUsuario = mainModel::ejecutar_consulta_simple(
            "SELECT id_usuario FROM usuarios WHERE id_usuario='$idUsuario'"
        );

        if ($checkUsuario->rowCount() <= 0) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Usuario no encontrado",
                "Texto" => "El usuario seleccionado no existe",
                "Tipo" => "error"
            ]);
        }

        if (usuarioModelo::desbloquear_usuario_modelo($idUsuario)) {
            return json_encode([
                "Alerta" => "recargar",
                "Titulo" => "Usuario desbloqueado",
                "Texto" => "El usuario fue desbloqueado y debera cambiar su contraseña al ingresar",
                "Tipo" => "success"
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto" => "No se pudo desbloquear el usuario",
            "Tipo" => "error"
        ]);
    }
}
