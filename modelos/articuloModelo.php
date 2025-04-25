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
}
