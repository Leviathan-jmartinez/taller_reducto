<?php
require_once "mainModel.php";

class articuloModelo extends mainModel
{
    /**modelo datos articulo */
    protected static function datos_articulos_modelo($tipo, $id)
    {
        if ($tipo == "Unico") {
            $sql = mainModel::conectar()->prepare("SELECT * FROM articulos where id_articulo = :id ");
            $sql->bindParam(":id", $id);
        } elseif ($tipo == "Conteo") {
            $sql = mainModel::conectar()->prepare("SELECT id_articulo FROM articulos where estado=1");
        }
        $sql->execute();
        return $sql;
    }
    protected static function obtener_impuestos_modelo()
    {
        $sql = mainModel::conectar()->prepare("SELECT idiva, tipo_impuesto_descri FROM tipo_impuesto ORDER BY idiva ASC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function obtener_proveedores_modelo()
    {
        $sql = mainModel::conectar()->prepare("SELECT idproveedores, razon_social FROM proveedores ORDER BY razon_social ASC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function obtener_UM_modelo()
    {
        $sql = mainModel::conectar()->prepare("SELECT idunidad_medida, medida FROM unidad_medida ORDER BY medida ASC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    protected static function obtener_cate_modelo()
    {
        $sql = mainModel::conectar()->prepare("SELECT id_categoria, cat_descri FROM categorias ORDER BY cat_descri ASC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    protected static function obtener_marca_modelo()
    {
        $sql = mainModel::conectar()->prepare("SELECT id_marcas, mar_descri FROM marcas ORDER BY mar_descri ASC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    /** modelo agregar articulo*/
    protected static function agregar_articulo_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("INSERT INTO articulos 
        (id_categoria, idproveedores, idunidad_medida, idiva, id_marcas, desc_articulo, precio_venta, precio_compra, codigo, estado, date_updated, date_created, tipo) 
        VALUES(:id_categoria, :idproveedores, :idunidad_medida, :idiva, :id_marcas, :descrip, :pricesale, :pricebuy, :code, :estado, now(), now(), :tipo)");
        $sql->bindParam(":id_categoria", $datos['id_categoria']);
        $sql->bindParam(":idproveedores", $datos['idproveedores']);
        $sql->bindParam(":idunidad_medida", $datos['idunidad_medida']);
        $sql->bindParam(":idiva", $datos['idiva']);
        $sql->bindParam(":id_marcas", $datos['id_marcas']);
        $sql->bindParam(":descrip", $datos['descrip']);
        $sql->bindParam(":pricesale", $datos['pricesale']);
        $sql->bindParam(":pricebuy", $datos['pricebuy']);
        $sql->bindParam(":code", $datos['code']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":tipo", $datos['tipo']);
        $sql->execute();
        return $sql;
    }
    /** fin modelo*/
    /** modelo eliminar articulo */
    protected static function eliminar_articulo_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("DELETE FROM articulos WHERE id_articulo = :id");
        $sql->bindParam(":id", $id);
        $sql->execute();
        return $sql;
    }
    /**fin modelo */
    /**modelo actualizar articulo */
    protected static function actualizar_articulo_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("UPDATE articulos
        SET id_categoria=:id_categoria, idproveedores=:idproveedores, idunidad_medida=:idunidad_medida, idiva=:idiva, id_marcas=:id_marcas, desc_articulo=:desc_articulo, 
        precio_venta=:precio_venta, precio_compra=:precio_compra, codigo=:codigo, estado=:estado, date_updated=now(), tipo=:tipo
        WHERE id_articulo=:id_articulo");
        $sql->bindParam(":id_categoria", $datos['id_categoria']);
        $sql->bindParam(":idproveedores", $datos['idproveedores']);
        $sql->bindParam(":idunidad_medida", $datos['idunidad_medida']);
        $sql->bindParam(":idiva", $datos['idiva']);
        $sql->bindParam(":id_marcas", $datos['id_marcas']);
        $sql->bindParam(":desc_articulo", $datos['desc_articulo']);
        $sql->bindParam(":precio_venta", $datos['precio_venta']);
        $sql->bindParam(":precio_compra", $datos['precio_compra']);
        $sql->bindParam(":codigo", $datos['codigo']);
        $sql->bindParam(":estado", $datos['estado']);
        $sql->bindParam(":tipo", $datos['tipo']);
        $sql->bindParam(":id_articulo", $datos['id_articulo']);
        $sql->execute();
        return $sql;
    }
    /**fin modelo */
}
