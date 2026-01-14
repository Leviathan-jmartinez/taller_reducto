<?php
class vistasModelo
{
    /** Modelo para obtener vistas  */
    protected static function obtenVista_modelo($vistas)
    {
        $lista_blanca = ["home", "articulo-actualizar", "articulo-buscar", "articulo-lista", "articulo-nuevo", 
        "cliente-actualizar", "cliente-buscar", "cliente-lista", "cliente-nuevo", "company",  "proveedor-actualizar", "proveedor-buscar", "proveedor-lista", "proveedor-nuevo",      
        "usuario-actualizar", "usuario-buscar", "usuario-lista", "usuario-rol", "rol-permisos","usuario-nuevo", "usuario-sucursal","pedido-lista","pedido-nuevo","pedido-buscar",
        "sucursal-nuevo","sucursal-lista","sucursal-actualizar","sucursal-buscar", "vehiculo-lista","vehiculo-nuevo","vehiculo-actualizar","vehiculo-buscar",
        "empleado-actualizar","empleado-nuevo","empleado-lista","empleado-buscar","cargo-nuevo","cargo-lista","cargo-actualizar","cargo-buscar", "empleado-equipo-asignar", 
        "empleado-equipo","empleado-equipo-miembros",
        "presupuesto-nuevo", "presupuesto-lista","presupuesto-buscar","oc-nuevo","oc-lista","oc-buscar","factura-nuevo","factura-lista","factura-buscar"
        ,"inventario", "inventario-buscar", "remision-nuevo", "remision-buscar", "notasCreDe-nuevo", "notasCreDe-buscar","recepcionServicio-nuevo","recepcionServicio-buscar",
        "promocion-nuevo","promocion-lista", "promocion-editar","descuento-nuevo","descuento-asignar","descuento-lista","descuento-editar","presupuesto-servicio-nuevo",
        "presupuesto-servicio-lista", "presupuesto-servicio-buscar", "ordenTrabajo-lista","ordenTrabajo-nuevo","ordenTrabajo-buscar","registro-servicio-nuevo","registro-servicio-lista",
        "registro-servicio-buscar"   ,"reclamo-servicio-nuevo","transferencia-nuevo","transferencia-historial","transferencia-recibir",
        "reporte-pedidos", "reporte-presupuestos", "reporte-ordenes-compra", "reporte-compras", "reporte-recepcion-servicio", "reporte-presupuesto-servicio","reporte-orden-trabajo"
         ,"reporte-registro-servicio", "reporte-LibroCompras","reporte-articulos", "reporte-proveedores", "reporte-clientes", "reporte-empleados"];
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