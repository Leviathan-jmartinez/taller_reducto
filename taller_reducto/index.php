<?php
require_once "./config/APP.php";
require_once "./controladores/vistasControlador.php";
/**acceder a la clase */
$plantilla = new vistasControlador();
/**obtener */
$plantilla->obtenPlantilla_Controlador();

