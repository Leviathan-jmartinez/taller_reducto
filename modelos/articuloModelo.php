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
}
