<?php
if ($peticionAjax) {
    require_once "../modelos/articuloModelo.php";
} else {
    require_once "./modelos/articuloModelo.php";
}

class articuloControlador extends articuloModelo
{

    public function listar_iva_controlador()
    {
        $articles = articuloModelo::obtener_impuestos_modelo();
        $options = '<option value="" selected>Seleccione una opción</option>';

        foreach ($articles as $article) {
            $options .= '<option value="' . $article['idiva'] . '">' . $article['tipo_impuesto_descri'] . '</option>';
        }

        return $options;
    }

    public function listar_proveedores_controlador()
    {
        $provee = articuloModelo::obtener_proveedores_modelo();
        $options = '<option value="" selected>Seleccione una opción</option>';

        foreach ($provee as $prove) {
            $options .= '<option value="' . $prove['idproveedores'] . '">' . $prove['razon_social'] . '</option>';
        }

        return $options;
    }

    public function listar_um_controlador()
    {
        $ums = articuloModelo::obtener_UM_modelo();
        $options = '<option value="" selected>Seleccione una opción</option>';

        foreach ($ums as $um) {
            $options .= '<option value="' . $um['idunidad_medida'] . '">' . $um['medida'] . '</option>';
        }

        return $options;
    }

    public function listar_cate_controlador()
    {
        $cate = articuloModelo::obtener_cate_modelo();
        $options = '<option value="" selected>Seleccione una opción</option>';

        foreach ($cate as $cat) {
            $options .= '<option value="' . $cat['id_categoria'] . '">' . $cat['cat_descri'] . '</option>';
        }

        return $options;
    }

    public function listar_marca_controlador()
    {
        $marca = articuloModelo::obtener_marca_modelo();
        $options = '<option value="" selected>Seleccione una opción</option>';

        foreach ($marca as $mar) {
            $options .= '<option value="' . $mar['id_marcas'] . '">' . $mar['mar_descri'] . '</option>';
        }

        return $options;
    }

    /**agregar articulo */
    public function agregar_articulo_controlador()
    {
        $categoria = mainModel::limpiar_string($_POST['categoria_reg']);
        $proveedor = mainModel::limpiar_string($_POST['proveedor_reg']);
        $umedida = mainModel::limpiar_string($_POST['um_reg']);
        $iva = mainModel::limpiar_string($_POST['tipo_iva_reg']);
        $imarca = mainModel::limpiar_string($_POST['marca_reg']);
        $descrip = mainModel::limpiar_string($_POST['articulo_nombre_reg']);
        $pricesale = mainModel::limpiar_string($_POST['articulo_priceV_reg']);
        $pricebuy = mainModel::limpiar_string($_POST['articulo_priceC_reg']);
        $code = mainModel::limpiar_string($_POST['articulo_codigo_reg']);
        $estado = mainModel::limpiar_string($_POST['articuloEstadoReg']);

        /** Comprobar campos vacios */
        if ($code == "" || $descrip == "" || $pricebuy == "" || $pricesale == "") {
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
        if (mainModel::verificarDatos("[0-9]{1,15}", $code)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo Código no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[0-9]{1,15}", $pricebuy)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo Precio de compra no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[0-9]{1,15}", $pricesale)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo Precio de Venta no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**verificar integridad de datos  */
        if (mainModel::verificarDatos("[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,60}", $descrip)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo Descripción no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($iva < 0 || $iva == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El tipo de Impuesto seaccionado no corresponde",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($proveedor < 0 || $proveedor == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El proveedor seleaccionado no corresponde",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($umedida < 0 || $umedida == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La Unidad de medida seleaccionado no corresponde",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($categoria < 0 || $categoria == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La categoria seleaccionada no corresponde",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($imarca < 0 || $imarca == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La marca seleccionada no corresponde",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if ($estado < 0 || $estado > 1 || $estado == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El estado seleccionado no corresponde",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**Comprobacion de registros */
        $check_code = mainModel::ejecutar_consulta_simple("SELECT codigo from articulos where codigo='$code'");
        if ($check_code->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El articulo ingresado ya se encuentra registrado!",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $datos_articulo = [
            "id_categoria" => $categoria,
            "idproveedores" => $proveedor,
            "idunidad_medida" => $umedida,
            "idiva" => $iva,
            "id_marcas" => $imarca,
            "descrip" => $descrip,
            "pricesale" => $pricesale,
            "pricebuy" => $pricebuy,
            "code" => $code,
            "estado" => $estado
        ];
        $agregar_articulo = articuloModelo::agregar_articulo_modelo($datos_articulo);
        if ($agregar_articulo->rowCount() == 1) {
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Articulo   ",
                "Texto" => "Los datos fueron registrados correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido registrar el articulo, favor intente nuevamente",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /** fin controlador */
}
