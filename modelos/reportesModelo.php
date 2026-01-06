<?php
require_once "mainModel.php";

class reportesModelo extends mainModel
{
    /* =========================================
       REPORTE DE PEDIDOS (SIN COSTOS)
    ========================================= */
    protected static function reporte_pedidos_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            pc.idpedido_cabecera,
            pc.fecha,
            pc.estado,

            pc.updatedby,

            pr.razon_social AS proveedor,

            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_crea,
            CONCAT(uu.usu_nombre, ' ', uu.usu_apellido) AS usuario_actualiza,

            /* cantidad de artículos distintos */
            COUNT(pd.id_articulo) AS cantidad_items,

            /* cantidad total de unidades */
            COALESCE(SUM(pd.cantidad), 0) AS cantidad_unidades

        FROM pedido_cabecera pc

        /* proveedor puede ser NULL */
        LEFT JOIN proveedores pr
            ON pr.idproveedores = pc.id_proveedor

        /* usuario creador */
        LEFT JOIN usuarios u
            ON u.id_usuario = pc.id_usuario

        /* usuario que actualizó */
        LEFT JOIN usuarios uu
            ON uu.id_usuario = pc.updatedby

        LEFT JOIN pedido_detalle pd
            ON pd.idpedido_cabecera = pc.idpedido_cabecera

        WHERE 1 = 1
        ";

        $params = [];

        if (!empty($desde)) {
            $sql .= " AND DATE(pc.fecha) >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $sql .= " AND DATE(pc.fecha) <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if (!empty($estado)) {
            $sql .= " AND pc.estado = :estado";
            $params[':estado'] = $estado;
        }

        if (!empty($sucursal)) {
            $sql .= " AND pc.id_sucursal = :sucursal";
            $params[':sucursal'] = $sucursal;
        }

        $sql .= "
        GROUP BY pc.idpedido_cabecera
        ORDER BY pc.fecha ASC
    ";

        $stmt = mainModel::conectar()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
