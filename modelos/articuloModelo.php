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
        $pdo = mainModel::conectar();

        // Insertar artículo
        $sql = $pdo->prepare("INSERT INTO articulos 
        (id_categoria, idunidad_medida, idiva, id_marcas, desc_articulo, precio_venta, codigo, estado, date_updated, date_created, tipo) 
        VALUES(:id_categoria, :idunidad_medida, :idiva, :id_marcas, :descrip, :pricesale, :code, 1, now(), now(), :tipo)");

        $sql->execute([
            ":id_categoria" => $datos['id_categoria'],
            ":idunidad_medida" => $datos['idunidad_medida'],
            ":idiva" => $datos['idiva'],
            ":id_marcas" => $datos['id_marcas'],
            ":descrip" => $datos['descrip'],
            ":pricesale" => $datos['pricesale'],
            ":code" => $datos['code'],
            ":tipo" => $datos['tipo']
        ]);

        return $sql;
    }
    /** fin modelo*/

    /** modelo eliminar articulo */
    protected static function eliminar_articulo_modelo($id)
    {
        $pdo = mainModel::conectar();

        // 1) Verificar si el artículo ya fue usado
        $check = $pdo->prepare("
        SELECT 1 
        FROM movimientostock 
        WHERE MovStockArticuloId = :id
        LIMIT 1
        ");
        $check->bindParam(":id", $id, PDO::PARAM_INT);
        $check->execute();

        if ($check->rowCount() > 0) {
            // Ya fue usado → solo desactivar
            $stmt = $pdo->prepare("
            UPDATE articulos 
            SET estado = 0 
            WHERE id_articulo = :id
        ");
        } else {
            // No está relacionado → se puede eliminar
            $stmt = $pdo->prepare("
            DELETE FROM articulos 
            WHERE id_articulo = :id
        ");
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($stmt->queryString && stripos($stmt->queryString, 'DELETE FROM articulos') !== false) {
                $stmt = $pdo->prepare("
            UPDATE articulos
            SET estado = 0
            WHERE id_articulo = :id
        ");
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                throw $e;
            }
        }

        return $stmt;
    }

    /**fin modelo */

    /**modelo actualizar articulo */
    protected static function actualizar_articulo_modelo($datos)
    {
        $pdo = mainModel::conectar();

        // 1Update artículo
        $sql = $pdo->prepare("UPDATE articulos
        SET id_categoria=:id_categoria, idunidad_medida=:idunidad_medida, idiva=:idiva, id_marcas=:id_marcas,
            desc_articulo=:desc_articulo, precio_venta=:precio_venta, codigo=:codigo,
            estado=:estado, date_updated=now(), tipo=:tipo
        WHERE id_articulo=:id_articulo");

        $sql->execute([
            ":id_categoria" => $datos['id_categoria'],
            ":idunidad_medida" => $datos['idunidad_medida'],
            ":idiva" => $datos['idiva'],
            ":id_marcas" => $datos['id_marcas'],
            ":desc_articulo" => $datos['desc_articulo'],
            ":precio_venta" => $datos['precio_venta'],
            ":codigo" => $datos['codigo'],
            ":estado" => $datos['estado'],
            ":tipo" => $datos['tipo'],
            ":id_articulo" => $datos['id_articulo']
        ]);

        return $sql;
    }
    /**fin modelo */

    protected static function listar_articulos_modelo($inicio, $registros, $filtrosSQL)
    {
        $conexion = mainModel::conectar();

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                a.*
            FROM articulos a
            WHERE 1=1 $filtrosSQL
            ORDER BY desc_articulo ASC
            LIMIT :inicio, :registros";

        $stmt = $conexion->prepare($sql);

        $stmt->bindValue(":inicio", (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(":registros", (int)$registros, PDO::PARAM_INT);

        $stmt->execute();

        $datos = $stmt->fetchAll();
        $total = $conexion->query("SELECT FOUND_ROWS()")->fetchColumn();

        return [
            "datos" => $datos,
            "total" => (int)$total
        ];
    }

}
