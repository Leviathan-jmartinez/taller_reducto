<?php
class vistasModelo
{
    /** Modelo para obtener vistas  */
    protected static function obtenVista_modelo($vistas)
    {
        $lista_blanca = ["home", "articulo-actualizar", "articulo-buscar", "articulo-lista", "articulo-nuevo", 
        "cliente-actualizar", "cliente-buscar", "cliente-lista", "cliente-nuevo", "company",        
        "usuario-actualizar", "usuario-buscar", "usuario-lista", "usuario-nuevo","pedido-lista","pedido-nuevo","pedido-buscar",
        "presupuesto-nuevo", "presupuesto-lista","presupuesto-buscar","oc-nuevo","oc-lista"];
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