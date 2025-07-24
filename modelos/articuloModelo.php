<?php
require_once "mainModel.php";

class articuloModelo extends mainModel
{

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
    /** modelo agregar cliente*/
    protected static function agregar_articulo_modelo($datos)
    {
        $sql = mainModel::conectar()->prepare("INSERT INTO articulos 
        (id_categoria, idproveedores, idunidad_medida, idiva, id_marcas, desc_articulo, precio_venta, precio_compra, codigo, estado, date_updated, date_created) 
        VALUES(:id_categoria, :idproveedores, :idunidad_medida, :idiva, :id_marcas, :descrip, :pricesale, :pricebuy, :code, :estado, now(), now())");
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
        $sql->execute();
        return $sql;
    }
}
