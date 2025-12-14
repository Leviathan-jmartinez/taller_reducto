<?php
require_once "mainModel.php";

class inventarioModelo extends mainModel
{

    protected static function cargarCategoriasModelo()
    {
        $stmt = mainModel::conectar()->prepare(
            "SELECT id_categoria, cat_descri
             FROM categorias
             WHERE estado = 1
             ORDER BY cat_descri ASC"
        );

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function cargarProveedoresModelo()
    {
        $stmt = mainModel::conectar()->prepare(
            "SELECT idproveedores, razon_social
             FROM proveedores
             WHERE estado = 1
             ORDER BY razon_social ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function cargarArticulosModelo($buscar = '')
    {
        $sql = "SELECT id_articulo, codigo, desc_articulo, precio_venta
                FROM articulos
                WHERE estado = 1 
                AND (desc_articulo LIKE :buscar OR codigo LIKE :buscar)
                ORDER BY desc_articulo ASC
                LIMIT 50"; // límite para no sobrecargar la tabla
        $stmt = mainModel::conectar()->prepare($sql);
        $stmt->bindValue(':buscar', '%' . $buscar . '%');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
