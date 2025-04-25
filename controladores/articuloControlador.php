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
}
