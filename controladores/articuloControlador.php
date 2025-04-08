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
}
