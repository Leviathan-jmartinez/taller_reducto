<?php
require_once "mainModel.php";

class loginModelo extends mainModel {
    /** Modelo para iniciar sesion */
    protected static function iniciar_sesion_modelo($datos){
        $sql = mainModel::conectar()->prepare("SELECT * FROM usuarios WHERE usu_nick = :Usuario AND usu_clave =:Clave AND usu_estado = '1'");
        $sql->bindParam(":Usuario",$datos['Usuario']);
        $sql->bindParam(":Clave",$datos['Clave']);
        $sql->execute();
        return $sql;
    }
}