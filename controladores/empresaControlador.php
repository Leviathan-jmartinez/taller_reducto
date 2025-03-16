<?php
if ($peticionAjax) {
    require_once "../modelos/empresaModelo.php";
} else {
    require_once "./modelos/empresaModelo.php";
}

class empresaControlador extends empresaModelo
{
    /**controlador datos empresa */
    public function datos_empresa_controlador() {
        return empresaModelo::datos_empresa_modelo();
    }/**fin controlador */

    /**controlador datos empresa */
    public function agregar_empresa_controlador() {
        
    }/**fin controlador */
}
