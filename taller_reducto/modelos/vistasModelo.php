<?php
class vistasModelo
{
    /** Modelo para obtener vistas  */
    protected static function obtenVista_modelo($vistas)
    {
        $lista_blanca = ["home", "articulo-actualizar", "articulo-buscar", "articulo-lista", "articulo-nuevo", 
        "cliente-actualizar", "cliente-buscar", "cliente-lista", "cliente-nuevo", "company", "reservacion-actualizar", 
        "reservacion-buscar", "reservacion-lista", "reservacion-nuevo", "reservacion-pendiente", "reservacion", 
        "usuario-actualizar", "usuario-buscar", "usuario-lista", "usuario-nuevo"];
        if (in_array($vistas, $lista_blanca)) {
            if (is_file("./vistas/contenidos/" . $vistas . "-vista.php")) {
                $contenido = "./vistas/contenidos/" . $vistas . "-vista.php";
            } else {
                $contenido = "404";
            }
        } elseif ($vistas == "login" || $vistas == "index") {
            $contenido = "login";
        } else {
            $contenido = "404";
        }
        return $contenido;
    }
}