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
        $clave = mainModel::encryption($clave);
        $datosLogin = [
            "Usuario" => $usuario,
            "Clave" => $clave
        ];
        $datos_cuenta = loginModelo::iniciar_sesion_modelo($datosLogin);
        if ($datos_cuenta->rowCount() == 1) {
            $row = $datos_cuenta->fetch();
            session_start(['name' => 'STR']);
            $_SESSION['id_str'] = $row['id_usuario'];
            $_SESSION['nombre_str'] = $row['usu_nombre'];
            $_SESSION['apellido_str'] = $row['usu_apellido'];
            $_SESSION['nivel_str'] = $row['usu_nivel'];
            $_SESSION['nick_str'] = $row['usu_nick'];
            #$_SESSION['nick_sucursal'] = $row['sucursalid'];
            $_SESSION['nick_sucursal'] = $row['sucursalid'];
            $_SESSION['id_rol']        = $row['id_rol'];
            $_SESSION['permisos'] = loginModelo::obtener_permisos_usuario($row['id_usuario']);
            /**procesar con md5 */
            $_SESSION['token_str'] = md5(uniqid(mt_rand(), true));
            return header("Location: " . SERVERURL . "home/");
        } else {
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
