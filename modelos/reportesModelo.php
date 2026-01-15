<?php
require_once "mainModel.php";

class reportesModelo extends mainModel
{

    /* ==================================================
        REPORTE ARTICULOS - DETALLE
    ================================================== */
    protected static function reporte_articulos_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['sucursal']) && $f['sucursal'] != 0) {
            $where .= " AND suc.id_sucursal = :sucursal ";
            $params[':sucursal'] = $f['sucursal'];
        }

        if (!empty($f['categoria']) && $f['categoria'] != 0) {
            $where .= " AND c.id_categoria = :categoria ";
            $params[':categoria'] = $f['categoria'];
        }

        if (!empty($f['proveedor']) && $f['proveedor'] != 0) {
            $where .= " AND p.idproveedores = :proveedor ";
            $params[':proveedor'] = $f['proveedor'];
        }

        if (!empty($f['codigo'])) {
            $where .= " AND a.codigo = :codigo ";
            $params[':codigo'] = $f['codigo'];
        }

        if (!empty($f['estado']) && $f['estado'] != 'T') {
            if ($f['estado'] == 'A') {
                $where .= " AND a.estado = 1 ";
            } elseif ($f['estado'] == 'I') {
                $where .= " AND a.estado = 0 ";
            }
        }

        if (!empty($f['stock']) && $f['stock'] != 'T') {
            if ($f['stock'] == 'C') {
                $where .= " AND IFNULL(st.stockDisponible,0) > 0 ";
            } elseif ($f['stock'] == 'S') {
                $where .= " AND IFNULL(st.stockDisponible,0) = 0 ";
            } elseif ($f['stock'] == 'B') {
                $where .= " AND IFNULL(st.stockDisponible,0) <= IFNULL(st.stockcant_min,0) ";
            }
        }

        $sql = self::conectar()->prepare("
        SELECT
            a.id_articulo,
            a.codigo,
            a.desc_articulo,
            a.tipo,
            a.precio_compra,
            a.precio_venta,
            a.estado,

            c.cat_descri AS categoria,
            m.mar_descri AS marca,
            p.razon_social AS proveedor,
            u.medida AS unidad,

            suc.suc_descri AS sucursal,
            IFNULL(st.stockDisponible, 0) AS stock,
            st.stockcant_min,
            st.stockcant_max
        FROM articulos a
        INNER JOIN categorias c ON c.id_categoria = a.id_categoria
        INNER JOIN marcas m ON m.id_marcas = a.id_marcas
        INNER JOIN proveedores p ON p.idproveedores = a.idproveedores
        INNER JOIN unidad_medida u ON u.idunidad_medida = a.idunidad_medida
        LEFT JOIN stock st ON st.id_articulo = a.id_articulo
        LEFT JOIN sucursales suc ON suc.id_sucursal = st.id_sucursal
        $where
        ORDER BY a.desc_articulo
        ");

        $sql->execute($params);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==================================================
        REPORTE ARTICULOS - RESUMEN
    ================================================== */
    protected static function resumen_articulos_modelo($f)
    {
        $sql = self::conectar()->prepare("
        SELECT
            COUNT(DISTINCT a.id_articulo) AS total,
            SUM(CASE WHEN a.estado = 1 THEN 1 ELSE 0 END) AS activos,
            SUM(CASE WHEN a.estado = 0 THEN 1 ELSE 0 END) AS inactivos,
            SUM(CASE WHEN IFNULL(st.stockDisponible,0) > 0 THEN 1 ELSE 0 END) AS con_stock,
            SUM(CASE WHEN IFNULL(st.stockDisponible,0) = 0 THEN 1 ELSE 0 END) AS sin_stock,
            SUM(CASE WHEN IFNULL(st.stockDisponible,0) <= IFNULL(st.stockcant_min,0) THEN 1 ELSE 0 END) AS bajo_minimo
        FROM articulos a
        LEFT JOIN stock st ON st.id_articulo = a.id_articulo
        LEFT JOIN sucursales suc ON suc.id_sucursal = st.id_sucursal
        WHERE (:sucursal = 0 OR suc.id_sucursal = :sucursal)
        ");

        $sql->execute([
            ":sucursal" => $f['sucursal']
        ]);

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /* ==================================================
        REPORTE PROVEEDORES - DETALLE
    ================================================== */
    public static function reporte_proveedores_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['estado']) && $f['estado'] != 'T') {
            if ($f['estado'] == 'A') {
                $where .= " AND p.estado = 1 ";
            } elseif ($f['estado'] == 'I') {
                $where .= " AND p.estado = 0 ";
            }
        }

        if (!empty($f['buscar'])) {
            $where .= " AND (p.razon_social LIKE :buscar OR p.ruc LIKE :buscar) ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
        }

        $sql = self::conectar()->prepare("
        SELECT
            p.idproveedores,
            p.razon_social,
            p.ruc,
            p.telefono,
            p.correo,
            p.direccion,
            p.estado
        FROM proveedores p
        $where
        ORDER BY p.razon_social
        ");

        $sql->execute($params);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==================================================
        REPORTE PROVEEDORES - RESUMEN
    ================================================== */
    public static function resumen_proveedores_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['estado']) && $f['estado'] != 'T') {
            if ($f['estado'] == 'A') {
                $where .= " AND p.estado = 1 ";
            } elseif ($f['estado'] == 'I') {
                $where .= " AND p.estado = 0 ";
            }
        }

        if (!empty($f['buscar'])) {
            $where .= " AND (p.razon_social LIKE :buscar OR p.ruc LIKE :buscar) ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
        }

        $sql = self::conectar()->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN p.estado = 1 THEN 1 ELSE 0 END) AS activos,
            SUM(CASE WHEN p.estado = 0 THEN 1 ELSE 0 END) AS inactivos
        FROM proveedores p
        $where
        ");

        $sql->execute($params);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /* ==================================================
        REPORTE CLIENTES - DETALLE
    ================================================== */
    public static function reporte_clientes_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['estado']) && $f['estado'] != 'T') {
            if ($f['estado'] == 'A') {
                $where .= " AND c.estado_cliente = 1 ";
            } elseif ($f['estado'] == 'I') {
                $where .= " AND c.estado_cliente = 0 ";
            }
        }

        if (!empty($f['buscar'])) {
            $where .= " AND (
            c.nombre_cliente LIKE :buscar
            OR c.apellido_cliente LIKE :buscar
            OR c.doc_number LIKE :buscar
            OR c.email_cliente LIKE :buscar
        ) ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
        }

        $sql = self::conectar()->prepare("
        SELECT
            c.id_cliente,
            c.doc_type,
            c.doc_number,
            c.digito_v,
            c.nombre_cliente,
            c.apellido_cliente,
            c.direccion_cliente,
            c.celular_cliente,
            c.email_cliente,
            c.estado_cliente,
            ci.ciu_descri AS ciudad
        FROM clientes c
        LEFT JOIN ciudades ci ON ci.id_ciudad = c.id_ciudad
        $where
        ORDER BY c.apellido_cliente, c.nombre_cliente
        ");

        $sql->execute($params);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==================================================
        REPORTE CLIENTES - RESUMEN
    ================================================== */
    public static function resumen_clientes_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['estado']) && $f['estado'] != 'T') {
            if ($f['estado'] == 'A') {
                $where .= " AND c.estado_cliente = 1 ";
            } elseif ($f['estado'] == 'I') {
                $where .= " AND c.estado_cliente = 0 ";
            }
        }

        if (!empty($f['buscar'])) {
            $where .= " AND (
            c.nombre_cliente LIKE :buscar
            OR c.apellido_cliente LIKE :buscar
            OR c.doc_number LIKE :buscar
            OR c.email_cliente LIKE :buscar
        ) ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
        }

        $sql = self::conectar()->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN c.estado_cliente = 1 THEN 1 ELSE 0 END) AS activos,
            SUM(CASE WHEN c.estado_cliente = 0 THEN 1 ELSE 0 END) AS inactivos
        FROM clientes c
        $where
        ");

        $sql->execute($params);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /* ==================================================
        REPORTE EMPLEADOS - DETALLE
    ================================================== */
    public static function reporte_empleados_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['sucursal']) && $f['sucursal'] != 0) {
            $where .= " AND e.id_sucursal = :sucursal ";
            $params[':sucursal'] = $f['sucursal'];
        }

        if (!empty($f['cargo']) && $f['cargo'] != 0) {
            $where .= " AND e.idcargos = :cargo ";
            $params[':cargo'] = $f['cargo'];
        }

        if (!empty($f['estado']) && $f['estado'] != 'T') {
            if ($f['estado'] == 'A') {
                $where .= " AND e.estado = 1 ";
            } elseif ($f['estado'] == 'I') {
                $where .= " AND e.estado = 0 ";
            }
        }

        if (!empty($f['buscar'])) {
            $where .= " AND (
            e.nombre LIKE :buscar
            OR e.apellido LIKE :buscar
            OR e.nro_cedula LIKE :buscar
        ) ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
        }

        $sql = self::conectar()->prepare("
        SELECT
            e.idempleados,
            e.nro_cedula,
            e.nombre,
            e.apellido,
            e.celular,
            e.direccion,
            e.estado,

            c.descripcion AS cargo,
            s.suc_descri AS sucursal
        FROM empleados e
        INNER JOIN cargos c ON c.idcargos = e.idcargos
        INNER JOIN sucursales s ON s.id_sucursal = e.id_sucursal
        $where
        ORDER BY e.apellido, e.nombre
        ");

        $sql->execute($params);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==================================================
        REPORTE EMPLEADOS - RESUMEN
    ================================================== */
    public static function resumen_empleados_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['sucursal']) && $f['sucursal'] != 0) {
            $where .= " AND e.id_sucursal = :sucursal ";
            $params[':sucursal'] = $f['sucursal'];
        }

        if (!empty($f['cargo']) && $f['cargo'] != 0) {
            $where .= " AND e.idcargos = :cargo ";
            $params[':cargo'] = $f['cargo'];
        }

        if (!empty($f['estado']) && $f['estado'] != 'T') {
            if ($f['estado'] == 'A') {
                $where .= " AND e.estado = 1 ";
            } elseif ($f['estado'] == 'I') {
                $where .= " AND e.estado = 0 ";
            }
        }

        if (!empty($f['buscar'])) {
            $where .= " AND (
            e.nombre LIKE :buscar
            OR e.apellido LIKE :buscar
            OR e.nro_cedula LIKE :buscar
        ) ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
        }

        $sql = self::conectar()->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN e.estado = 1 THEN 1 ELSE 0 END) AS activos,
            SUM(CASE WHEN e.estado = 0 THEN 1 ELSE 0 END) AS inactivos
        FROM empleados e
        $where
        ");

        $sql->execute($params);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

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
        REPORTE LIBRO DE COMPRAS
    ========================================= */
    protected static function reporte_libro_compras_modelo($desde, $hasta, $proveedor, $estado, $sucursal)
    {
        $sql = "
        SELECT
            lc.idlibro_compra,
            lc.fecha,
            lc.tipo_comprobante,
            lc.serie,
            lc.nro_comprobante,
            lc.proveedor_nombre,
            lc.proveedor_ruc,
            lc.estado,
            lc.exenta,
            lc.gravada_5,
            lc.iva_5,
            lc.gravada_10,
            lc.iva_10,
            lc.total,
            s.suc_descri AS sucursal
        FROM libro_compra lc
        INNER JOIN sucursales s
            ON s.id_sucursal = lc.id_sucursal
        WHERE 1 = 1
        ";

        $params = [];

        if (!empty($desde)) {
            $sql .= " AND lc.fecha >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $sql .= " AND lc.fecha <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if ($proveedor !== null) {
            $sql .= " AND lc.idproveedores = :proveedor";
            $params[':proveedor'] = (int)$proveedor;
        }

        if ($estado !== null) {
            $sql .= " AND lc.estado = :estado";
            $params[':estado'] = (int)$estado;
        }

        if ($sucursal !== null) {
            $sql .= " AND lc.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= " ORDER BY lc.fecha ASC";

        $stmt = mainModel::conectar()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /* =========================================
        REPORTE TRANSFERENCIAS
    ========================================= */
    protected static function reporte_transferencias_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['sucursal']) && $f['sucursal'] != 0) {

            if (!empty($f['tipo']) && $f['tipo'] != 'T') {

                if ($f['tipo'] == 'E') {
                    // Envíos
                    $where .= " AND t.sucursal_origen = :suc ";
                } elseif ($f['tipo'] == 'R') {
                    // Recepciones
                    $where .= " AND t.sucursal_destino = :suc ";
                }
            } else {
                // Todos (comportamiento actual)
                $where .= " AND (t.sucursal_origen = :suc OR t.sucursal_destino = :suc) ";
            }

            $params[':suc'] = $f['sucursal'];
        }


        if (!empty($f['estado']) && $f['estado'] != 'T') {
            $where .= " AND t.estado = :estado ";
            $params[':estado'] = $f['estado'];
        }

        if (!empty($f['desde']) && !empty($f['hasta'])) {
            $where .= " AND DATE(t.fecha) BETWEEN :desde AND :hasta ";
            $params[':desde'] = $f['desde'];
            $params[':hasta'] = $f['hasta'];
        }

        $sql = self::conectar()->prepare("
        SELECT
            t.idtransferencia,
            t.fecha,
            t.estado,

            so.suc_descri AS suc_origen,
            sd.suc_descri AS suc_destino,

            nr.idnota_remision,
            nr.nro_remision,
            nr.fechaenvio,
            nr.fechallegada,
            nr.motivo_remision
        FROM transferencia_stock t
        INNER JOIN sucursales so ON so.id_sucursal = t.sucursal_origen
        INNER JOIN sucursales sd ON sd.id_sucursal = t.sucursal_destino
        LEFT JOIN nota_remision nr ON nr.idtransferencia = t.idtransferencia
        $where
        ORDER BY t.fecha DESC
        ");

        $sql->execute($params);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function resumen_transferencias_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['sucursal']) && $f['sucursal'] != 0) {
            $where .= " AND (t.sucursal_origen = :suc OR t.sucursal_destino = :suc) ";
            $params[':suc'] = $f['sucursal'];
        }

        $sql = self::conectar()->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN t.estado = 'en_transito' THEN 1 ELSE 0 END) AS en_transito,
            SUM(CASE WHEN t.estado = 'recibido' THEN 1 ELSE 0 END) AS recibidos,
            SUM(CASE WHEN t.estado = 'recibido_parcial' THEN 1 ELSE 0 END) AS parciales
        FROM transferencia_stock t
        $where
        ");

        $sql->execute($params);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /* ==================================================
        REPORTE MOVIMIENTOS DE STOCK - DETALLE
    ================================================== */
    protected static function reporte_movimientos_stock_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['sucursal']) && $f['sucursal'] != 0) {
            $where .= " AND m.id_sucursal = :suc ";
            $params[':suc'] = $f['sucursal'];
        }

        if (!empty($f['tipo']) && $f['tipo'] != 'T') {
            $where .= " AND m.TipoMovStockId = :tipo ";
            $params[':tipo'] = $f['tipo'];
        }

        if (!empty($f['signo']) && $f['signo'] != 'T') {
            if ($f['signo'] == 'P') {
                $where .= " AND m.MovStockSigno = 1 ";
            } elseif ($f['signo'] == 'N') {
                $where .= " AND m.MovStockSigno = -1 ";
            }
        }

        if (!empty($f['desde'])) {
            $where .= " AND DATE(m.MovStockFechaHora) >= :desde ";
            $params[':desde'] = $f['desde'];
        }

        if (!empty($f['hasta'])) {
            $where .= " AND DATE(m.MovStockFechaHora) <= :hasta ";
            $params[':hasta'] = $f['hasta'];
        }

        $sql = self::conectar()->prepare("
        SELECT
            m.MovStockId,
            m.MovStockFechaHora,
            s.suc_descri AS sucursal,
            m.TipoMovStockId,
            a.desc_articulo,
            m.MovStockCantidad,
            m.MovStockSigno,
            m.MovStockCosto,
            m.MovStockPrecioVenta,
            m.MovStockNroTicket,
            m.MovStockReferencia,
            CONCAT(u.usu_nombre,' ',u.usu_apellido) AS usuario
        FROM sucmovimientostock m
        INNER JOIN sucursales s ON s.id_sucursal = m.id_sucursal
        LEFT JOIN articulos a ON a.id_articulo = m.MovStockProductoId
        INNER JOIN usuarios u ON u.id_usuario = m.MovStockUsuario
        $where
        ORDER BY m.MovStockFechaHora DESC
        ");

        $sql->execute($params);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==================================================
        REPORTE MOVIMIENTOS DE STOCK - RESUMEN
    ================================================== */
    protected static function resumen_movimientos_stock_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['sucursal']) && $f['sucursal'] != 0) {
            $where .= " AND m.id_sucursal = :suc ";
            $params[':suc'] = $f['sucursal'];
        }

        if (!empty($f['tipo']) && $f['tipo'] != 'T') {
            $where .= " AND m.TipoMovStockId = :tipo ";
            $params[':tipo'] = $f['tipo'];
        }

        if (!empty($f['signo']) && $f['signo'] != 'T') {
            if ($f['signo'] == 'P') {
                $where .= " AND m.MovStockSigno = 1 ";
            } elseif ($f['signo'] == 'N') {
                $where .= " AND m.MovStockSigno = -1 ";
            }
        }

        if (!empty($f['desde'])) {
            $where .= " AND DATE(m.MovStockFechaHora) >= :desde ";
            $params[':desde'] = $f['desde'];
        }

        if (!empty($f['hasta'])) {
            $where .= " AND DATE(m.MovStockFechaHora) <= :hasta ";
            $params[':hasta'] = $f['hasta'];
        }

        $sql = self::conectar()->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN m.MovStockSigno = 1 THEN 1 ELSE 0 END) AS entradas,
            SUM(CASE WHEN m.MovStockSigno = -1 THEN 1 ELSE 0 END) AS salidas
        FROM sucmovimientostock m
        $where
        ");

        $sql->execute($params);
        return $sql->fetch(PDO::FETCH_ASSOC);
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
    protected static function reporte_registro_servicio_modelo($desde, $hasta, $estado, $empleado, $sucursal)
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

            CONCAT(em.nombre,' ',em.apellido) as tecnico,

            s.suc_descri AS sucursal,
            rs.observacion,

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

        INNER JOIN empleados em
            ON em.idempleados = rs.tecnico_responsable

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

        if ($empleado !== null) {
            $sql .= " AND rs.tecnico_responsable = :tecnico_responsable";
            $params[':tecnico_responsable'] = $empleado;
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
