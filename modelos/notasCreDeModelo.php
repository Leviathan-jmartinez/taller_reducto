<?php

/* ========= DETECTAR PETICIÓN AJAX ========= */
if ($peticionAjax) {
    require_once "../config/SERVER.php";
    require_once "../config/APP.php";
    require_once "../modelos/mainModel.php";
} else {
    require_once "./config/SERVER.php";
    require_once "./config/APP.php";
    require_once "./modelos/mainModel.php";
}

class notasCreDeModelo extends mainModel
{

    /* ================= BUSCAR FACTURAS ================= */
    public static function buscar_facturas_modelo($texto)
    {

        $sql = mainModel::conectar()->prepare("
            SELECT 
                idcompra_cabecera,
                nro_factura,
                fecha_factura,
                total_compra,
                idproveedores
            FROM compra_cabecera
            WHERE nro_factura LIKE :t
            ORDER BY idcompra_cabecera DESC
            LIMIT 10
        ");

        $sql->bindValue(":t", "%$texto%", PDO::PARAM_STR);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ================= OBTENER FACTURA ================= */
    public static function obtener_factura_modelo($id)
    {

        $sql = mainModel::conectar()->prepare("
            SELECT *
            FROM compra_cabecera
            WHERE idcompra_cabecera = :id
            LIMIT 1
        ");

        $sql->bindValue(":id", $id, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /* ================= OBTENER DETALLE COMPRA ================= */
    public static function obtener_detalle_compra_modelo($idcompra)
    {

        $sql = mainModel::conectar()->prepare("
        SELECT 
            d.id_articulo,
            a.desc_articulo,
            d.cantidad_recibida,
            d.precio_unitario,
            d.subtotal,
            ti.tipo_impuesto_descri,
            ti.ratevalueiva,
            ti.divisor
        FROM compra_detalle d
        INNER JOIN articulos a 
            ON a.id_articulo = d.id_articulo
        INNER JOIN tipo_impuesto ti
            ON ti.idiva = a.idiva
        WHERE d.idcompra_cabecera = :id
    ");

        $sql->bindValue(":id", $idcompra, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
