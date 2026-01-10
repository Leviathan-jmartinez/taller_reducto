<?php
require_once "mainModel.php";

class reportesModelo extends mainModel
{
    /* =========================================
       REPORTE DE PEDIDOS 
    ========================================= */
    protected static function reporte_pedidos_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            pc.idpedido_cabecera,
            pc.fecha,
            pc.estado,

            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario,
            p.razon_social AS proveedor,
            s.suc_descri AS sucursal,

            COUNT(pd.id_articulo) AS cantidad_items

        FROM pedido_cabecera pc

        INNER JOIN usuarios u
            ON u.id_usuario = pc.id_usuario

        LEFT JOIN proveedores p
            ON p.idproveedores = pc.id_proveedor

        LEFT JOIN sucursales s
            ON s.id_sucursal = pc.id_sucursal

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

        if ($estado !== null) {
            $sql .= " AND pc.estado = :estado";
            $params[':estado'] = (int)$estado;
        }

        if ($sucursal !== null) {
            $sql .= " AND pc.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
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

    /* =========================================
        REPORTE DE PRESUPUESTOS DE COMPRA
    ========================================= */
    protected static function reporte_presupuestos_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            pc.idpresupuesto_compra,
            pc.fecha,
            pc.fecha_venc,
            pc.estado,
            pc.total,

            pr.razon_social AS proveedor,

            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_crea,
            CONCAT(uu.usu_nombre, ' ', uu.usu_apellido) AS usuario_actualiza,

            COUNT(pd.id_articulo) AS cantidad_items,
            COALESCE(SUM(pd.cantidad), 0) AS cantidad_unidades,

            s.suc_descri AS sucursal

        FROM presupuesto_compra pc

        LEFT JOIN proveedores pr
            ON pr.idproveedores = pc.idproveedores

        LEFT JOIN usuarios u
            ON u.id_usuario = pc.id_usuario

        LEFT JOIN usuarios uu
            ON uu.id_usuario = pc.updatedby

        LEFT JOIN presupuesto_detalle pd
            ON pd.idpresupuesto_compra = pc.idpresupuesto_compra

        LEFT JOIN sucursales s
            ON s.id_sucursal = pc.id_sucursal

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

        if ($estado !== null) {
            $sql .= " AND pc.estado = :estado";
            $params[':estado'] = $estado;
        }

        if ($sucursal !== null) {
            $sql .= " AND pc.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }


        $sql .= "
        GROUP BY pc.idpresupuesto_compra
        ORDER BY pc.fecha ASC
        ";

        $stmt = mainModel::conectar()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================
        REPORTE DE ORDENES DE COMPRA
    ========================================= */
    protected static function reporte_ordenes_compra_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            oc.idorden_compra,
            oc.fecha,
            oc.fecha_entrega,
            oc.estado,
            oc.presupuestoid,

            pr.razon_social AS proveedor,

            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_crea,
            CONCAT(uu.usu_nombre, ' ', uu.usu_apellido) AS usuario_actualiza,

            s.suc_descri AS sucursal,

            COUNT(ocd.id_articulo) AS cantidad_items,
            COALESCE(SUM(ocd.cantidad), 0) AS cantidad_total,
            COALESCE(SUM(ocd.cantidad_pendiente), 0) AS cantidad_pendiente,

            COALESCE(SUM(ocd.cantidad * ocd.precio_unitario), 0) AS total

        FROM orden_compra oc

        LEFT JOIN proveedores pr
            ON pr.idproveedores = oc.idproveedores

        LEFT JOIN usuarios u
            ON u.id_usuario = oc.id_usuario

        LEFT JOIN usuarios uu
            ON uu.id_usuario = oc.updatedby

        LEFT JOIN sucursales s
            ON s.id_sucursal = oc.id_sucursal

        LEFT JOIN orden_compra_detalle ocd
            ON ocd.idorden_compra = oc.idorden_compra

        WHERE 1 = 1
        ";

        $params = [];

        if (!empty($desde)) {
            $sql .= " AND DATE(oc.fecha) >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $sql .= " AND DATE(oc.fecha) <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if ($estado !== null) {
            $sql .= " AND oc.estado = :estado";
            $params[':estado'] = $estado;
        }

        if ($sucursal !== null) {
            $sql .= " AND oc.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= "
        GROUP BY oc.idorden_compra
        ORDER BY oc.fecha ASC
        ";

        $stmt = mainModel::conectar()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================
        REPORTE DE COMPRAS (FACTURAS)
    ========================================= */
    protected static function reporte_compras_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            cc.idcompra_cabecera,
            cc.fecha_creacion,
            cc.fecha_factura,
            cc.nro_factura,
            cc.nro_timbrado,
            cc.vencimiento_timbrado,
            cc.estado,
            cc.total_compra,
            cc.condicion,
            cc.compra_intervalo,
            cc.idOcompra,

            pr.razon_social AS proveedor,

            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_crea,
            CONCAT(uu.usu_nombre, ' ', uu.usu_apellido) AS usuario_actualiza,

            s.suc_descri AS sucursal,

            COUNT(cd.id_articulo) AS cantidad_items,
            COALESCE(SUM(cd.cantidad_recibida), 0) AS cantidad_total

        FROM compra_cabecera cc

        LEFT JOIN proveedores pr
            ON pr.idproveedores = cc.idproveedores

        LEFT JOIN usuarios u
            ON u.id_usuario = cc.id_usuario

        LEFT JOIN usuarios uu
            ON uu.id_usuario = cc.updatedby

        LEFT JOIN sucursales s
            ON s.id_sucursal = cc.id_sucursal

        LEFT JOIN compra_detalle cd
            ON cd.idcompra_cabecera = cc.idcompra_cabecera

        WHERE 1 = 1
        ";

        $params = [];

        if (!empty($desde)) {
            $sql .= " AND cc.fecha_factura >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $sql .= " AND cc.fecha_factura <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if ($estado !== null) {
            $sql .= " AND cc.estado = :estado";
            $params[':estado'] = (int)$estado;
        }

        if ($sucursal !== null) {
            $sql .= " AND cc.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= "
        GROUP BY cc.idcompra_cabecera
        ORDER BY cc.fecha_factura ASC
        ";

        $stmt = mainModel::conectar()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================
        REPORTE DE RECEPCIÓN DE SERVICIOS
    ========================================= */
    protected static function reporte_recepcion_servicio_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            rs.idrecepcion,
            rs.fecha_ingreso,
            rs.fecha_salida,
            rs.kilometraje,
            rs.estado,

            CONCAT(c.nombre_cliente, ' ',c.apellido_cliente) AS cliente,
            CONCAT(m.mar_descri, ' ', mo.mod_descri, ' ', v.anho) AS vehiculo,
            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario,

            s.suc_descri AS sucursal

        FROM recepcion_servicio rs

        INNER JOIN clientes c
            ON c.id_cliente = rs.id_cliente

        INNER JOIN vehiculos v
            ON v.id_vehiculo = rs.id_vehiculo
        
        INNER JOIN modelo_auto mo
            ON mo.id_modeloauto = v.id_modeloauto

        INNER JOIN marcas m
            ON m.id_marcas = mo.id_marcas

        INNER JOIN usuarios u
            ON u.id_usuario = rs.id_usuario

        INNER JOIN sucursales s
            ON s.id_sucursal = rs.id_sucursal

        WHERE 1 = 1
        ";

        $params = [];

        if (!empty($desde)) {
            $sql .= " AND DATE(rs.fecha_ingreso) >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $sql .= " AND DATE(rs.fecha_ingreso) <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if ($estado !== null) {
            $sql .= " AND rs.estado = :estado";
            $params[':estado'] = (int)$estado;
        }

        if ($sucursal !== null) {
            $sql .= " AND rs.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= " ORDER BY rs.fecha_ingreso ASC";

        $stmt = mainModel::conectar()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================
        REPORTE PRESUPUESTO DE SERVICIOS
    ========================================= */
    protected static function reporte_presupuesto_servicio_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            ps.idpresupuesto_servicio,
            ps.fecha,
            ps.fecha_venc,
            ps.estado,
            ps.subtotal,
            ps.total_descuento,
            ps.total_final,

            COUNT(pds.id_articulo) AS cantidad_items,

            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario,

            rs.idrecepcion,
            s.suc_descri AS sucursal,

            CONCAT(c.nombre_cliente, ' ',c.apellido_cliente) AS cliente,
            CONCAT(m.mar_descri, ' ', mo.mod_descri, ' ', v.anho) AS vehiculo

        FROM presupuesto_servicio ps

        INNER JOIN usuarios u
            ON u.id_usuario = ps.id_usuario

        LEFT JOIN recepcion_servicio rs
            ON rs.idrecepcion = ps.idrecepcion

        LEFT JOIN sucursales s
            ON s.id_sucursal = rs.id_sucursal

        LEFT JOIN clientes c
            ON c.id_cliente = rs.id_cliente

        LEFT JOIN vehiculos v
            ON v.id_vehiculo = rs.id_vehiculo

        INNER JOIN modelo_auto mo
            ON mo.id_modeloauto = v.id_modeloauto

        INNER JOIN marcas m
            ON m.id_marcas = mo.id_marcas

        LEFT JOIN presupuesto_detalleservicio pds
            ON pds.idpresupuesto_servicio = ps.idpresupuesto_servicio

        WHERE 1 = 1
        ";

        $params = [];

        if (!empty($desde)) {
            $sql .= " AND ps.fecha >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $sql .= " AND ps.fecha <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if ($estado !== null) {
            $sql .= " AND ps.estado = :estado";
            $params[':estado'] = (int)$estado;
        }

        if ($sucursal !== null) {
            $sql .= " AND rs.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= "
        GROUP BY ps.idpresupuesto_servicio
        ORDER BY ps.fecha ASC
        ";

        $stmt = mainModel::conectar()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================
        REPORTE ÓRDENES DE TRABAJO
    ========================================= */
    protected static function reporte_orden_trabajo_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            ot.idorden_trabajo,
            ot.fecha_inicio,
            ot.fecha_fin,
            ot.estado,

            ps.idpresupuesto_servicio,
            rs.idrecepcion,

            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario,

            CONCAT(et.nombre, ' - ' ,et.descripcion) AS equipo,

            CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente,
            CONCAT(m.mar_descri, ' ', mo.mod_descri, ' ', v.anho) AS vehiculo,

            s.suc_descri AS sucursal,

            COUNT(otd.id_articulo) AS cantidad_items

        FROM orden_trabajo ot

        INNER JOIN presupuesto_servicio ps
            ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio

        INNER JOIN recepcion_servicio rs
            ON rs.idrecepcion = ot.idrecepcion

        INNER JOIN sucursales s
            ON s.id_sucursal = rs.id_sucursal

        INNER JOIN usuarios u
            ON u.id_usuario = ot.id_usuario

        LEFT JOIN equipo_trabajo et
            ON et.id_equipo = ot.idtrabajos

        INNER JOIN clientes c
            ON c.id_cliente = rs.id_cliente

        INNER JOIN vehiculos v
            ON v.id_vehiculo = rs.id_vehiculo

        INNER JOIN modelo_auto mo
            ON mo.id_modeloauto = v.id_modeloauto

        INNER JOIN marcas m
            ON m.id_marcas = mo.id_marcas

        LEFT JOIN orden_trabajo_detalle otd
            ON otd.idorden_trabajo = ot.idorden_trabajo

        WHERE 1 = 1
        ";

        $params = [];

        if (!empty($desde)) {
            $sql .= " AND DATE(ot.fecha_inicio) >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $sql .= " AND DATE(ot.fecha_inicio) <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if ($estado !== null) {
            $sql .= " AND ot.estado = :estado";
            $params[':estado'] = (int)$estado;
        }

        if ($sucursal !== null) {
            $sql .= " AND rs.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= "
        GROUP BY ot.idorden_trabajo
        ORDER BY ot.fecha_inicio ASC
        ";

        $stmt = mainModel::conectar()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================
        REPORTE REGISTRO DE SERVICIOS
    ========================================= */
    protected static function reporte_registro_servicio_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            rs.idregistro_servicio,
            rs.fecha_ejecucion,
            rs.fecha_registro,
            rs.estado,

            ot.idorden_trabajo,

            CONCAT(ur.usu_nombre, ' ', ur.usu_apellido) AS usuario_registra,
            CONCAT(et.nombre, ' - ' ,et.descripcion) AS equipo,

            CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente,
            CONCAT(m.mar_descri, ' ', mo.mod_descri, ' ', v.anho) AS vehiculo,

            s.suc_descri AS sucursal,

            COUNT(rsd.id_articulo) AS cantidad_items,
            COALESCE(SUM(rsd.subtotal),0) AS total

        FROM registro_servicio rs

        INNER JOIN orden_trabajo ot
            ON ot.idorden_trabajo = rs.idorden_trabajo

        INNER JOIN recepcion_servicio r
            ON r.idrecepcion = ot.idrecepcion

        INNER JOIN sucursales s
            ON s.id_sucursal = r.id_sucursal

        INNER JOIN clientes c
            ON c.id_cliente = r.id_cliente

        INNER JOIN vehiculos v
            ON v.id_vehiculo = r.id_vehiculo

        INNER JOIN modelo_auto mo
            ON mo.id_modeloauto = v.id_modeloauto

        INNER JOIN marcas m
            ON m.id_marcas = mo.id_marcas

        INNER JOIN usuarios ur
            ON ur.id_usuario = rs.usuario_registra

        LEFT JOIN equipo_trabajo et
            ON et.id_equipo = ot.idtrabajos

        LEFT JOIN registro_servicio_detalle rsd
            ON rsd.idregistro_servicio = rs.idregistro_servicio

        WHERE 1 = 1
        ";

        $params = [];

        if (!empty($desde)) {
            $sql .= " AND rs.fecha_ejecucion >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $sql .= " AND rs.fecha_ejecucion <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if ($estado !== null) {
            $sql .= " AND rs.estado = :estado";
            $params[':estado'] = (int)$estado;
        }

        if ($sucursal !== null) {
            $sql .= " AND r.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= "
        GROUP BY rs.idregistro_servicio
        ORDER BY rs.fecha_ejecucion ASC
        ";

        $stmt = mainModel::conectar()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
