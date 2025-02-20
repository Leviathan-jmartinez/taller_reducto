<?php
if ($peticionAjax) {
    require_once "../modelos/usuarioModelo.php";
} else {
    require_once "./modelos/usuarioModelo.php";
}

class usuarioControlador extends usuarioModelo {
    /** controlador agregar usuario*/
    public function agregar_usuario_controlador(){
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
        if ($nombre == "" || $apellido =="" || $nick == "" || $clave1 == "" || $clave2 == "") {
            $alerta = [
                "Alerta"=>"simple",
                "Titulo"=>"Ocurrio un error inesperado!",
                "Texto"=>"No has llenado todos los campos que son obligatorios",
                "Tipo"=>"error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**verificar integridad de datos  */
        if (mainModel::verificarDatos("[0-9-]{7,20}",$ci)) {
            $alerta = [
                "Alerta"=>"simple",
                "Titulo"=>"Ocurrio un error inesperado!",
                "Texto"=>"El formato del campo CI no es valido",
                "Tipo"=>"error"
            ];
            echo json_encode($alerta);
        }
        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}",$nombre)) {
            $alerta = [
                "Alerta"=>"simple",
                "Titulo"=>"Ocurrio un error inesperado!",
                "Texto"=>"El formato del campo NOMBRE no es valido",
                "Tipo"=>"error"
            ];
            echo json_encode($alerta);
        }
        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}",$apellido)) {
            $alerta = [
                "Alerta"=>"simple",
                "Titulo"=>"Ocurrio un error inesperado!",
                "Texto"=>"El formato del campo APELLIDO no es valido",
                "Tipo"=>"error"
            ];
            echo json_encode($alerta);
        }
        if ($telefono!="") {
            if (mainModel::verificarDatos("[0-9()+]{8,20}",$telefono)) {
                $alerta = [
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrio un error inesperado!",
                    "Texto"=>"El formato del campo TELEFONO no es valido",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
            }
        }
        if ($telefono!="") {
            if (mainModel::verificarDatos("[0-9()+]{8,20}",$telefono)) {
                $alerta = [
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrio un error inesperado!",
                    "Texto"=>"El formato del campo TELEFONO no es valido",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
            }
        }
        if (mainModel::verificarDatos("[a-zA-Z0-9]{1,35}",$nick)) {
            $alerta = [
                "Alerta"=>"simple",
                "Titulo"=>"Ocurrio un error inesperado!",
                "Texto"=>"El formato del campo USUARIO no es valido",
                "Tipo"=>"error"
            ];
            echo json_encode($alerta);
        }
        if (mainModel::verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave1) || mainModel::verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave2)) {
            $alerta = [
                "Alerta"=>"simple",
                "Titulo"=>"Ocurrio un error inesperado!",
                "Texto"=>"Las claves no coinciden con el formato solicitado",
                "Tipo"=>"error"
            ];
            echo json_encode($alerta);
        }
    }
}
