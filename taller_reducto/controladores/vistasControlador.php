<?php
require_once "./modelos/vistasModelo.php";

class vistasControlador extends vistasModelo
{
    /**Controlador para obtener plantillas  */
    public function obtenPlantilla_Controlador()
    {
        return require_once("./vistas/plantilla.php");
    }
    /**Controlador obtener vista */
    public function obtenVista_Controlador()
    {
        if (isset($_GET['vista'])) {
            $ruta = explode("/", $_GET['vista']);
            $respuesta = vistasModelo::obtenVista_modelo($ruta[0]);
        } else {
            $respuesta ="login";
        }
        return $respuesta;
    }
}
