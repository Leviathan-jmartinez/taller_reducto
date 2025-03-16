<?php
if ($peticionAjax) {
    require_once "../modelos/empresaModelo.php";
} else {
    require_once "./modelos/empresaModelo.php";
}

class empresaControlador extends empresaModelo
{
    /**controlador datos empresa */
    public function datos_empresa_controlador()
    {
        return empresaModelo::datos_empresa_modelo();
    }
    /**fin controlador */

    /**controlador datos empresa */
    public function agregar_empresa_controlador()
    {
        $ruc = mainModel::limpiar_string($_POST['empresa_ruc_reg']);
        $nombre = mainModel::limpiar_string($_POST['empresa_nombre_reg']);
        $email = mainModel::limpiar_string($_POST['empresa_email_reg']);
        $telefono = mainModel::limpiar_string($_POST['empresa_telefono_reg']);
        $direccion = mainModel::limpiar_string($_POST['empresa_direccion_reg']);

        /** Comprobar campos vacios */
        if ($ruc == "" || $nombre == "" || $direccion == "") {
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
        if (mainModel::verificarDatos("[0-9()+]{7,20}", $ruc)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo RUC no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[0-9()+]{7,20}", $telefono)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo RUC no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El EMAIL ingresado no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $datos_empresa = [
            "razon" => $nombre,
            "direccion" => $direccion,
            "ruc" => $ruc,
            "estado" => '1',
            "email" => $email,
            "telefono" => $telefono
        ];
        $add_empresa = empresaModelo::agregar_empresa_modelo($datos_empresa);
        if ($add_empresa->rowCount() == 1) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Registros Agregado",
                "Texto" => "Los datos fueron registrados correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "Los datos no fueron registrados, favor intente nuevamente",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
        exit();
    }
    /**fin controlador */
}
