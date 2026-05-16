<?php
if ($peticionAjax) {
    require_once "../modelos/loginModelo.php";
} else {
    require_once "./modelos/loginModelo.php";
}

class loginControlador extends loginModelo
{
    /**Controlador iniciar sesion */
    public function iniciar_sesion_controlador()
    {
        $usuario = mainModel::limpiar_string($_POST["usuario_login"]);
        $clave = mainModel::limpiar_string($_POST["clave_login"]);

        /**comprobar campos */
        if ($usuario == "" || $clave == "") {
            echo '
            <script>
                Swal.fire({
                    title: "Ocurrio un error inesperado!",
                    text: "No has llenado todos los campos requeridos",
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            </script>
            ';
            exit();
        }
        /**integridad de datos */
        if (mainModel::verificarDatos("[a-zA-Z0-9]{1,35}", $usuario)) {
            echo '
            <script>
                Swal.fire({
                    title: "Ocurrio un error inesperado!",
                    text: "El formato del campo USUARIO no es válido",
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            </script>
            ';
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave)) {
            echo '
            <script>
                Swal.fire({
                    title: "Ocurrio un error inesperado!",
                    text: "El formato del campo CLAVE no es válido",
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            </script>
            ';
            exit();
        }
        $datos_cuenta = loginModelo::obtener_usuario_login_modelo($usuario);
        if ($datos_cuenta->rowCount() != 1) {
            echo '
            <script>
                Swal.fire({
                    title: "Ocurrio un error inesperado!",
                    text: "El usuario o la clave ingresados no son correctos",
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            </script>
            ';
            exit();
        }

        $row = $datos_cuenta->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            echo '
            <script>
                Swal.fire({
                    title: "Ocurrio un error inesperado!",
                    text: "No se pudo obtener la informacion del usuario",
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            </script>
            ';
            exit();
        }

        if ($row['usu_estado'] != 1) {
            echo '
            <script>
                Swal.fire({
                    title: "Cuenta inactiva",
                    text: "La cuenta de usuario se encuentra inactiva",
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            </script>
            ';
            exit();
        }

        if (isset($row['usu_bloqueado']) && $row['usu_bloqueado'] == 1) {
            echo '
            <script>
                Swal.fire({
                    title: "Cuenta bloqueada",
                    text: "La cuenta fue bloqueada por superar los 3 intentos fallidos. Contacte con un administrador.",
                    type: "error",
                    confirmButtonText: "Aceptar"
                });
            </script>
            ';
            exit();
        }

        $clave = mainModel::encryption($clave);

        if ($row['usu_clave'] != $clave) {
            $estado_intentos = loginModelo::registrar_intento_fallido_modelo($row['id_usuario']);
            $intentos = (int)$estado_intentos['usu_intentos_fallidos'];
            $intentos_restantes = max(0, 3 - $intentos);

            if ($intentos_restantes <= 0) {
                echo '
                <script>
                    Swal.fire({
                        title: "Cuenta bloqueada",
                        text: "Supero los 3 intentos fallidos. La cuenta fue bloqueada.",
                        type: "error",
                        confirmButtonText: "Aceptar"
                    });
                </script>
                ';
            } else {
                echo '
                <script>
                    Swal.fire({
                        title: "Clave incorrecta",
                        text: "La clave ingresada no es correcta. Intentos restantes: ' . $intentos_restantes . '",
                        type: "error",
                        confirmButtonText: "Aceptar"
                    });
                </script>
                ';
            }
            exit();
        }

        loginModelo::reiniciar_intentos_login_modelo($row['id_usuario']);

        session_start(['name' => 'STR']);
        $_SESSION['id_str'] = $row['id_usuario'];
        $_SESSION['nombre_str'] = $row['usu_nombre'];
        $_SESSION['apellido_str'] = $row['usu_apellido'];
        $_SESSION['nick_str'] = $row['usu_nick'];
        $_SESSION['nick_sucursal'] = $row['sucursalid'];
        $_SESSION['roles'] = loginModelo::obtener_roles_usuario($row['id_usuario']);
        $_SESSION['permisos'] = loginModelo::obtener_permisos_usuario($row['id_usuario']);
        $empresa = mainModel::ejecutar_consulta_simple("
            SELECT razon_social 
            FROM empresa 
            LIMIT 1
            ");

        if ($empresa && $empresa->rowCount() > 0) {
            $rowEmp = $empresa->fetch(PDO::FETCH_ASSOC);
            $_SESSION['empresa_nombre'] = $rowEmp['razon_social'];
        } else {
            $_SESSION['empresa_nombre'] = 'Empresa';
        }
        /**procesar con md5 */
        $_SESSION['token_str'] = md5(uniqid(mt_rand(), true));
        return header("Location: " . SERVERURL . "home/");
    }
    /**Fin controlador */

    /**Controladr cerrar sesion*/
    public function forzarCierre_sesion_controlador()
    {
        session_unset();
        session_destroy();
        if (headers_sent()) {
            echo "<script> window.location.href='" . SERVERURL . "login/'; </script>";
            exit();
        } else {
            return header("Location: " . SERVERURL . "login/");
            exit();
        }
    }
    /**fin controlador */

    /**cerrar sesion */
    public function cierre_sesion_controlador()
    {
        session_start(['name' => 'STR']);
        $token = mainModel::decryption($_POST['token']);
        $usuario = mainModel::decryption($_POST['usuario']);

        if ($token == $_SESSION['token_str'] && $usuario == $_SESSION['nick_str']) {
            session_unset();
            session_destroy();
            $alerta = [
                "Alerta" => "redireccionar",
                "URL" => SERVERURL . "login/"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No se pudo cerrar la sesion en el sistema",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /**fin controlador */
}
