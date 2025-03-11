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
        $doc_number = mainModel::limpiar_string($_POST['cliente_doc_reg']);
        $digitoV = mainModel::limpiar_string($_POST['cliente_dv_reg']);
        $nombre = mainModel::limpiar_string($_POST['cliente_nombre_reg']);
        $apellido = mainModel::limpiar_string($_POST['cliente_apellido_reg']);
        $telefono = mainModel::limpiar_string($_POST['cliente_telefono_reg']);
        $direccion = mainModel::limpiar_string($_POST['cliente_direccion_reg']);
        $doctype = mainModel::limpiar_string($_POST['tipo_documento']);
        $estadoC = mainModel::limpiar_string($_POST['cliente_estadoC']);
        $city = mainModel::limpiar_string($_POST['ciudad_reg']);
        $email = mainModel::limpiar_string($_POST['cliente_email_reg']);

        /** Comprobar campos vacios */
        if ($doc_number == "" || $nombre == "" || $direccion == "") {
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
        if (mainModel::verificarDatos("[0-9-]{1,27}", $doc_number)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo Número de documento no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}", $nombre)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo NOMBRE no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($apellido != "") {
            if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}", $apellido)) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El formato del campo APELLIDO no es válido",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
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
        if ($digitoV != "") {
            if (mainModel::verificarDatos("[0-9()+]{1,2}", $digitoV)) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El formato del campo DV no es valido",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
        if (mainModel::verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,150}", $direccion)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo DIRECCIÓN no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**comprobar email */
        if ($email != "") {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $check_email = mainModel::ejecutar_consulta_simple("SELECT email_cliente from clientes where email_cliente='$email'");
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
        if ($estadoC != "") {
            // Si el valor NO está en la lista de permitidos, se muestra el error
            if (!in_array($estadoC, ['Soltero/a', 'Casado/a', 'Viudo/a', 'Divorciado/a'])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El formato del estado civil no es válido.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }

        /**Comprobacion de registros */
        $check_doc = mainModel::ejecutar_consulta_simple("SELECT doc_number from clientes where doc_number='$doc_number'");
        if ($check_doc->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El numero de documento ingresado ya se encuentra registrado!",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**Comprobar privilegios */
        if ($city < 1) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No ha seleccionado una ciudad valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $datos_cliente = [
            "ciudad" => $city,
            "doctype" => $doctype,
            "doc_number" => $doc_number,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "direccion" => $direccion,
            "celular" => $telefono,
            "estadoC" => $estadoC,
            "estado" => "1",
            "dv" => $digitoV,
            "email" => $email,
        ];
        $agregar_cliente = clienteModelo::agregar_cliente_modelo($datos_cliente);
        if ($agregar_cliente->rowCount() == 1) {
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Cliente",
                "Texto" => "Los datos fueron registrados correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido registrar al cliente, favor intente nuevamente",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
}
