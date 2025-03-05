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
        /**inicio */
        $ci = mainModel::limpiar_string($_POST['usuario_dni_reg']);
        $nombre = mainModel::limpiar_string($_POST['usuario_nombre_reg']);
        $apellido = mainModel::limpiar_string($_POST['usuario_apellido_reg']);
        $telefono = mainModel::limpiar_string($_POST['usuario_telefono_reg']);

        $nick = mainModel::limpiar_string($_POST['usuario_usuario_reg']);
        $email = mainModel::limpiar_string($_POST['usuario_email_reg']);

        $clave1 = mainModel::limpiar_string($_POST['usuario_clave_1_reg']);
        $clave2 = mainModel::limpiar_string($_POST['usuario_clave_2_reg']);
        $nivel = mainModel::limpiar_string($_POST['usuario_privilegio_reg']);
        /** Comprobar campos vacios */
        if ($nombre == "" || $apellido == "" || $nick == "" || $clave1 == "" || $clave2 == "") {
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
        if (mainModel::verificarDatos("[0-9-]{7,20}", $ci)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo CI no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}", $nombre)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo NOMBRE no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}", $apellido)) {
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
            if (mainModel::verificarDatos("[0-9()+]{8,20}", $telefono)) {
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
        if (mainModel::verificarDatos("[a-zA-Z0-9]{1,35}", $nick)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo USUARIO no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave1) || mainModel::verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave2)) {
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
        /**Comprobar privilegios */
        if ($nivel < 1 || $nivel > 3) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No ha seleccionado un nivel de usuario valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $datos_usuario_reg = [
            "ci" => $ci,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "nick" => $nick,
            "email" => $email,
            "telefono" => $telefono,
            "clave" => $clave,
            "nivel" => $nivel,
            "estado" => "1"
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
    public function paginador_usuario_controlador($pagina, $registros, $privilegio, $id, $url, $busqueda)
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $privilegio = mainModel::limpiar_string($privilegio);
        $id = mainModel::limpiar_string($id);
        $busqueda = mainModel::limpiar_string($busqueda);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        if (isset($busqueda) && $busqueda != "") {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM usuarios 
            WHERE ((id_usuario != '$id' AND id_usuario !='1') AND (usu_ci LIKE '%$busqueda%' OR usu_nick LIKE '%$busqueda%')) 
            ORDER BY usu_nombre ASC LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM usuarios 
            WHERE id_usuario != '$id' AND id_usuario !='1' 
            ORDER BY usu_nombre ASC LIMIT $inicio,$registros";
        }
        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta);
        $datos = $datos->fetchAll();

        $total = $conexion->query("SELECT FOUND_ROWS()");
        $total = (int) $total->fetchColumn();

        $Npaginas = ceil($total / $registros);

        $tabla .= '<div class="table-responsive">
					<table class="table table-dark table-sm">
						<thead>
							<tr class="text-center roboto-medium">
								<th>#</th>
								<th>CI</th>
								<th>NOMBRE COMPLETO</th>
								<th>TELÉFONO</th>
								<th>USUARIO</th>
								<th>EMAIL</th>
								<th>ACTUALIZAR</th>
								<th>ELIMINAR</th>
							</tr>
						</thead>
						<tbody>';
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
								<td>
									<a href="' . SERVERURL . 'usuario-actualizar/' . mainModel::encryption($rows['id_usuario']) . '/" class="btn btn-success">
										<i class="fas fa-sync-alt"></i>
									</a>
								</td>
								<td>
									<form class="FormularioAjax" action="' . SERVERURL . 'ajax/usuarioAjax.php" method="POST" data-form="delete" autocomplete="off" action="">
                                    <input type="hidden" name="usuario_id_del" value=' . mainModel::encryption($rows['id_usuario']) . '>
										<button type="submit" class="btn btn-warning">
											<i class="far fa-trash-alt"></i>
										</button>
									</form>
								</td>
							</tr>';
                $contador++;
            }
            $reg_final = $contador - 1;
        } else {
            if ($total >= 1) {
                $tabla .= '<tr class="text-center"> <td colspan="9"> <a href="' . $url . '" class="btn btn-reaised btn-primary btn-sm"> Haga click aqui para recargar el listado </a> </td> </tr> ';
            } else {
                $tabla .= '<tr class="text-center"> <td colspan="9"> No hay regitros en el sistema</td> </tr> ';
            }
        }

        $tabla .= '       </tbody>
					</table>
				</div>';
        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '<p class="text-right"> Mostrando registro ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '</p>';
            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }
        echo $tabla;
    }
    /**fin controlador */

    /**controlador eliminar usuario */
    public function eliminar_usuario_controlador()
    {
        $usuario = mainModel::decryption($_POST['usuario_id_del']);
        $usuario = mainModel::limpiar_string($usuario);

        if ($usuario == 1) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No podemos eliminar el usuario principal del sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $check_user = mainModel::ejecutar_consulta_simple("SELECT id_usuario FROM usuarios WHERE id_usuario = '$usuario'");
        if ($check_user->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El usuario que intenta eliminar no existe en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        session_start(['name' => 'STR']);
        if ($_SESSION['nivel_str'] != 1) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No tiene los permisos necesario para realizar esta operación",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $eliminar_user = usuarioModelo::eliminar_usuario_modelo($usuario);
        if ($eliminar_user->rowCount() == 1) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Usuario eliminado!",
                "Texto" => "El usuario ha sido eliminado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No se pudo eliminar el usuario seleccionado",
                "Tipo" => "error"
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
        $ci = mainModel::limpiar_string($_POST['usuario_ci_up']);
        $nombre = mainModel::limpiar_string($_POST['usuario_nombre_up']);
        $apellido = mainModel::limpiar_string($_POST['usuario_apellido_up']);
        $telefono = mainModel::limpiar_string($_POST['usuario_telefono_up']);
        $nick = mainModel::limpiar_string($_POST['usuario_usuario_up']);
        $email = mainModel::limpiar_string($_POST['usuario_email_up']);

        /**validar estado si viene definido */
        if (isset($_POST['usuario_estado_up'])) {
            $estado = mainModel::limpiar_string($_POST['usuario_estado_up']);
        } else {
            $estado = $campos_usuario_up['usu_estado'];
        }
        /**validar estado si viene definido */
        if (isset($_POST['usuario_privilegio_up'])) {
            $nivel = mainModel::limpiar_string($_POST['usuario_privilegio_up']);
        } else {
            $nivel = $campos_usuario_up['usu_nivel'];
        }
        $admin_user = mainModel::limpiar_string($_POST['usuario_admin']);
        $admin_clave = mainModel::limpiar_string($_POST['clave_admin']);
        $tipo_cuenta = mainModel::limpiar_string($_POST['tipo_cuenta']);

        if ($nombre == "" || $apellido == "" || $nick == "" || $admin_user == "" || $admin_clave == "") {
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
        if (mainModel::verificarDatos("[0-9-]{7,20}", $ci)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo CI no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}", $nombre)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo NOMBRE no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}", $apellido)) {
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
            if (mainModel::verificarDatos("[0-9()+]{8,20}", $telefono)) {
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
        if (mainModel::verificarDatos("[a-zA-Z0-9]{1,35}", $nick)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo USUARIO no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificarDatos("[a-zA-Z0-9]{1,35}", $admin_user)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "Tu USUARIO no coincide con el formato solicitado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $admin_clave)) {
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
        if ($nivel < 1 || $nivel > 3) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El nivel de Privelegio seleccionado no corresponde",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($estado != "1" && $estado != "2") {
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
                if (mainModel::verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $_POST['usuario_clave_nueva_1']) || mainModel::verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $_POST['usuario_clave_nueva_2'])) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrio un error inesperado!",
                        "Texto" => "Las nuevas claves ingresadas no coinciden con el formato solicitado",
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
            session_start(['name' => 'STR']);
            if ($_SESSION['nivel_str'] != 1) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "No tienes los permisos necesarios para realizar esta operación",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
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
            "nivel" => $nivel,
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
}
