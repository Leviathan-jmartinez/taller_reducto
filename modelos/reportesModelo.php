<?php
require_once "mainModel.php";

class reportesModelo extends mainModel
{

    /* ==================================================
        REPORTE ARTICULOS - DETALLE
    ================================================== */
    protected static function reporte_articulos_simple_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        // FILTROS
        if (!empty($f['categoria']) && $f['categoria'] != 0) {
            $where .= " AND a.id_categoria = :categoria ";
            $params[':categoria'] = $f['categoria'];
        }

        if (!empty($f['proveedor']) && $f['proveedor'] != 0) {
            $where .= " AND ap.idproveedores = :proveedor ";
            $params[':proveedor'] = $f['proveedor'];
        }

        if (!empty($f['codigo'])) {
            $where .= " AND a.codigo = :codigo ";
            $params[':codigo'] = $f['codigo'];
        }

        if (!empty($f['buscar'])) {
            $where .= " AND (
                a.codigo LIKE :buscar OR
                a.desc_articulo LIKE :buscar OR
                c.cat_descri LIKE :buscar OR
                m.mar_descri LIKE :buscar OR
                p.razon_social LIKE :buscar
            ) ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
        }

        if (!empty($f['estado']) && $f['estado'] != 'T') {
            if ($f['estado'] == 'A') {
                $where .= " AND a.estado = 1 ";
            } elseif ($f['estado'] == 'I') {
                $where .= " AND a.estado = 0 ";
            }
        }

        if (!empty($f['buscar'])) {
            $where .= " AND (
                a.codigo LIKE :buscar OR
                a.desc_articulo LIKE :buscar OR
                c.cat_descri LIKE :buscar OR
                m.mar_descri LIKE :buscar OR
                p.razon_social LIKE :buscar
            ) ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
        }

        $sql = self::conectar()->prepare("
        SELECT
            a.id_articulo,
            a.codigo,
            a.desc_articulo,
            a.tipo,
            ap.precio_compra,
            a.precio_venta,
            a.estado,
            a.date_created,

            c.cat_descri AS categoria,
            m.mar_descri AS marca,
            p.razon_social AS proveedor,
            u.medida AS unidad,
            i.tipo_impuesto_descri AS iva

        FROM articulos a

        INNER JOIN categorias c ON c.id_categoria = a.id_categoria
        INNER JOIN marcas m ON m.id_marcas = a.id_marcas
        LEFT JOIN articulo_proveedor ap ON ap.id_articulo = a.id_articulo AND ap.activo = 1
        LEFT JOIN proveedores p ON p.idproveedores = ap.idproveedores
        INNER JOIN unidad_medida u ON u.idunidad_medida = a.idunidad_medida
        INNER JOIN tipo_impuesto i ON i.idiva = a.idiva

        $where

        ORDER BY a.desc_articulo ASC
        ");

        $sql->execute($params);
        return self::filtrar_movimientos_stock_por_naturaleza(
            $sql->fetchAll(PDO::FETCH_ASSOC),
            (string)($f['naturaleza'] ?? '')
        );
    }

    protected static function reporte_articulos_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['sucursal']) && $f['sucursal'] != 0) {
            $where .= " AND st.id_sucursal = :sucursal ";
            $params[':sucursal'] = $f['sucursal'];
        }
        $where .= " AND st.stockDisponible > 0 ";

        if (!empty($f['categoria']) && $f['categoria'] != 0) {
            $where .= " AND c.id_categoria = :categoria ";
            $params[':categoria'] = $f['categoria'];
        }

        if (!empty($f['proveedor']) && $f['proveedor'] != 0) {
            $where .= " AND p.idproveedores = :proveedor ";
            $params[':proveedor'] = $f['proveedor'];
        }

        if (!empty($f['articulo']) && $f['articulo'] != 0) {
            $where .= " AND a.id_articulo = :articulo ";
            $params[':articulo'] = $f['articulo'];
        }

        if (!empty($f['codigo'])) {
            $where .= " AND a.codigo = :codigo ";
            $params[':codigo'] = $f['codigo'];
        }

        if (!empty($f['buscar'])) {
            $where .= " AND (
                a.codigo LIKE :buscar OR
                a.desc_articulo LIKE :buscar OR
                c.cat_descri LIKE :buscar OR
                m.mar_descri LIKE :buscar OR
                p.razon_social LIKE :buscar
            ) ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
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
            ap.precio_compra,
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
        LEFT JOIN categorias c ON c.id_categoria = a.id_categoria
        LEFT JOIN marcas m ON m.id_marcas = a.id_marcas
        LEFT JOIN articulo_proveedor ap ON ap.id_articulo = a.id_articulo AND ap.activo = 1
        LEFT JOIN proveedores p ON p.idproveedores = ap.idproveedores
        LEFT JOIN unidad_medida u ON u.idunidad_medida = a.idunidad_medida
        INNER JOIN stock st ON st.id_articulo = a.id_articulo
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
    protected static function resumen_articulos_simple_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['categoria']) && $f['categoria'] != 0) {
            $where .= " AND a.id_categoria = :categoria ";
            $params[':categoria'] = $f['categoria'];
        }

        if (!empty($f['proveedor']) && $f['proveedor'] != 0) {
            $where .= " AND ap.idproveedores = :proveedor ";
            $params[':proveedor'] = $f['proveedor'];
        }

        if (!empty($f['estado']) && $f['estado'] != 'T') {
            if ($f['estado'] == 'A') {
                $where .= " AND a.estado = 1 ";
            } elseif ($f['estado'] == 'I') {
                $where .= " AND a.estado = 0 ";
            }
        }

        $sql = self::conectar()->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) AS activos,
            SUM(CASE WHEN estado = 0 THEN 1 ELSE 0 END) AS inactivos
        FROM (
            SELECT DISTINCT a.id_articulo, a.estado
            FROM articulos a
            LEFT JOIN categorias c ON c.id_categoria = a.id_categoria
            LEFT JOIN marcas m ON m.id_marcas = a.id_marcas
            LEFT JOIN articulo_proveedor ap ON ap.id_articulo = a.id_articulo AND ap.activo = 1
            LEFT JOIN proveedores p ON p.idproveedores = ap.idproveedores
            $where
        ) articulos_filtrados
        ");

        $sql->execute($params);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function resumen_articulos_modelo($f)
    {
        $sql = self::conectar()->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN a.estado = 1 THEN 1 ELSE 0 END) AS activos,
            SUM(CASE WHEN a.estado = 0 THEN 1 ELSE 0 END) AS inactivos,
            SUM(CASE WHEN IFNULL(st.stockDisponible,0) > 0 THEN 1 ELSE 0 END) AS con_stock,
            SUM(CASE WHEN IFNULL(st.stockDisponible,0) = 0 THEN 1 ELSE 0 END) AS sin_stock,
            SUM(CASE WHEN IFNULL(st.stockDisponible,0) <= IFNULL(st.stockcant_min,0) THEN 1 ELSE 0 END) AS bajo_minimo
        FROM articulos a
        INNER JOIN stock st ON st.id_articulo = a.id_articulo
        WHERE (:sucursal = 0 OR st.id_sucursal = :sucursal)
        ");

        $sql->execute([
            ":sucursal" => $f['sucursal']
        ]);

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /* ==================================================
        REPORTE SUCURSALES
    ================================================== */
    public static function reporte_sucursales_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['estado']) && $f['estado'] != 'T') {
            if ($f['estado'] == 'A') {
                $where .= " AND s.estado = 1 ";
            } elseif ($f['estado'] == 'I') {
                $where .= " AND s.estado = 0 ";
            }
        }

        if (!empty($f['buscar'])) {
            $where .= " AND (
                s.suc_descri LIKE :buscar OR
                s.suc_direccion LIKE :buscar OR
                s.suc_telefono LIKE :buscar OR
                e.razon_social LIKE :buscar
            ) ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
        }

        $sql = self::conectar()->prepare("
        SELECT
            s.id_sucursal,
            s.suc_descri,
            s.suc_direccion,
            s.suc_telefono,
            s.nro_establecimiento,
            s.estado,
            e.razon_social AS empresa
        FROM sucursales s
        INNER JOIN empresa e ON e.id_empresa = s.id_empresa
        $where
        ORDER BY s.suc_descri ASC
        ");

        $sql->execute($params);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function resumen_sucursales_modelo($f)
    {
        $datos = self::reporte_sucursales_modelo($f);
        $resumen = [
            "total" => count($datos),
            "activos" => 0,
            "inactivos" => 0
        ];

        foreach ($datos as $row) {
            if ((int)$row['estado'] === 1) {
                $resumen['activos']++;
            } else {
                $resumen['inactivos']++;
            }
        }

        return $resumen;
    }

    /* ==================================================
        REPORTE VEHICULOS
    ================================================== */
    public static function reporte_vehiculos_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['modelo']) && $f['modelo'] != 0) {
            $where .= " AND v.id_modeloauto = :modelo ";
            $params[':modelo'] = $f['modelo'];
        }

        if (!empty($f['estado']) && $f['estado'] != 'T') {
            if ($f['estado'] == 'A') {
                $where .= " AND v.estado = 1 ";
            } elseif ($f['estado'] == 'I') {
                $where .= " AND v.estado = 0 ";
            }
        }

        if (!empty($f['buscar'])) {
            $where .= " AND (
                v.placa LIKE :buscar OR
                v.version LIKE :buscar OR
                v.color LIKE :buscar OR
                c.doc_number LIKE :buscar OR
                c.nombre_cliente LIKE :buscar OR
                c.apellido_cliente LIKE :buscar
            ) ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
        }

        $sql = self::conectar()->prepare("
        SELECT
            v.id_vehiculo,
            v.placa,
            v.version,
            v.anho,
            v.color,
            v.estado,
            m.mod_descri AS modelo,
            CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente,
            c.doc_number
        FROM vehiculos v
        INNER JOIN clientes c ON c.id_cliente = v.id_cliente
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        $where
        ORDER BY v.placa ASC
        ");

        $sql->execute($params);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function resumen_vehiculos_modelo($f)
    {
        $datos = self::reporte_vehiculos_modelo($f);
        $resumen = [
            "total" => count($datos),
            "activos" => 0,
            "inactivos" => 0
        ];

        foreach ($datos as $row) {
            if ((int)$row['estado'] === 1) {
                $resumen['activos']++;
            } else {
                $resumen['inactivos']++;
            }
        }

        return $resumen;
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
            '' AS proveedor,
            s.suc_descri AS sucursal,

            COUNT(pd.id_articulo) AS cantidad_items,
            GROUP_CONCAT(DISTINCT pd.id_articulo) AS articulos_ids

        FROM pedido_cabecera pc

        INNER JOIN usuarios u
            ON u.id_usuario = pc.id_usuario

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
        ORDER BY pc.fecha DESC, pc.idpedido_cabecera DESC
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
            pc.idproveedores,

            pr.razon_social AS proveedor,

            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_crea,
            CONCAT(uu.usu_nombre, ' ', uu.usu_apellido) AS usuario_actualiza,

            COUNT(pd.id_articulo) AS cantidad_items,
            COALESCE(SUM(pd.cantidad), 0) AS cantidad_unidades,
            GROUP_CONCAT(DISTINCT pd.id_articulo) AS articulos_ids,

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
        ORDER BY pc.fecha DESC, pc.idpresupuesto_compra DESC
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
            oc.idproveedores,

            pr.razon_social AS proveedor,

            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_crea,
            CONCAT(uu.usu_nombre, ' ', uu.usu_apellido) AS usuario_actualiza,

            s.suc_descri AS sucursal,

            COUNT(ocd.id_articulo) AS cantidad_items,
            COALESCE(SUM(ocd.cantidad), 0) AS cantidad_total,
            COALESCE(SUM(ocd.cantidad_pendiente), 0) AS cantidad_pendiente,

            COALESCE(SUM(ocd.cantidad * ocd.precio_unitario), 0) AS total,
            GROUP_CONCAT(DISTINCT ocd.id_articulo) AS articulos_ids

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
        ORDER BY oc.fecha DESC, oc.idorden_compra DESC
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
            cc.idproveedores,

            pr.razon_social AS proveedor,

            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario_crea,
            CONCAT(uu.usu_nombre, ' ', uu.usu_apellido) AS usuario_actualiza,

            s.suc_descri AS sucursal,

            COUNT(cd.id_articulo) AS cantidad_items,
            COALESCE(SUM(cd.cantidad_recibida), 0) AS cantidad_total,
            GROUP_CONCAT(DISTINCT cd.id_articulo) AS articulos_ids

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
        ORDER BY cc.fecha_factura DESC, cc.idcompra_cabecera DESC
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
            lc.idproveedores,
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

        $sql .= " ORDER BY lc.fecha DESC, lc.idlibro_compra DESC";

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
            nr.motivo_remision,
            GROUP_CONCAT(DISTINCT td.id_articulo) AS articulos_ids
        FROM transferencia_stock t
        INNER JOIN sucursales so ON so.id_sucursal = t.sucursal_origen
        INNER JOIN sucursales sd ON sd.id_sucursal = t.sucursal_destino
        LEFT JOIN nota_remision nr ON nr.idtransferencia = t.idtransferencia
        LEFT JOIN transferencia_stock_detalle td ON td.idtransferencia = t.idtransferencia
        $where
        GROUP BY t.idtransferencia
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

        if (!empty($f['naturaleza']) && $f['naturaleza'] != 'T') {

            switch ($f['naturaleza']) {

                case 'entrada':
                    $where .= " AND m.MovStockSigno = 1 ";
                    break;

                case 'salida':
                    $where .= " AND m.MovStockSigno = -1 ";
                    break;

                case 'ajuste':
                    $where .= " AND m.TipoMovStockId IN (
                'AJUSTE_INV',
                'ANULACION_AJUSTE_INV'
            ) ";
                    break;

                case 'compra':
                    $where .= " AND m.TipoMovStockId IN (
                'RECEPCION COMPRA',
                'ANULACION COMPRA',
                'NC_COMPRA_DEV',
                'ANULA_NC_COMPRA'
            ) ";
                    break;

                case 'transferencia':
                    $where .= " AND m.TipoMovStockId IN (
                'TRANSFERENCIA_SALIDA',
                'TRANSFERENCIA_ENTRADA'
            ) ";
                    break;

                case 'servicio':
                    $where .= " AND m.TipoMovStockId IN (
                'REG. SERVICIO',
                'ANULACION REG. SERVICIO'
            ) ";
                    break;

                case 'insumo':
                    $where .= " AND m.TipoMovStockId IN (
                'SALIDA INSUMO',
                'ANUL SALIDA INSUMO'
            ) ";
                    break;
            }
        }

        if (!empty($f['articulo']) && $f['articulo'] != 0) {
            $where .= " AND m.MovStockArticuloId = :articulo ";
            $params[':articulo'] = $f['articulo'];
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
            m.MovStockArticuloId AS id_articulo,
            a.desc_articulo,
            m.MovStockCantidad,
            m.MovStockSigno,
            m.MovStockCosto,
            m.MovStockPrecioVenta,
            CASE
                WHEN m.MovStockSigno = 1 THEN 'Entrada'
                WHEN m.MovStockSigno = -1 THEN 'Salida'
                ELSE 'Ajuste'
            END AS naturaleza_movimiento,
            (ABS(m.MovStockCantidad) * COALESCE(m.MovStockCosto, 0)) AS importe_costo,
            (ABS(m.MovStockCantidad) * COALESCE(m.MovStockPrecioVenta, 0)) AS importe_venta,
            m.MovStockNroTicket,
            m.MovStockReferencia,
            CONCAT(u.usu_nombre,' ',u.usu_apellido) AS usuario
        FROM movimientostock m
        INNER JOIN sucursales s ON s.id_sucursal = m.id_sucursal
        LEFT JOIN articulos a ON a.id_articulo = m.MovStockArticuloId
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

        if (!empty($f['naturaleza']) && $f['naturaleza'] != 'T') {

            switch ($f['naturaleza']) {

                case 'entrada':
                    $where .= " AND m.MovStockSigno = 1 ";
                    break;

                case 'salida':
                    $where .= " AND m.MovStockSigno = -1 ";
                    break;

                case 'ajuste':
                    $where .= " AND m.TipoMovStockId IN (
                'AJUSTE_INV',
                'ANULACION_AJUSTE_INV'
            ) ";
                    break;

                case 'compra':
                    $where .= " AND m.TipoMovStockId IN (
                'RECEPCION COMPRA',
                'ANULACION COMPRA',
                'NC_COMPRA_DEV',
                'ANULA_NC_COMPRA'
            ) ";
                    break;

                case 'transferencia':
                    $where .= " AND m.TipoMovStockId IN (
                'TRANSFERENCIA_SALIDA',
                'TRANSFERENCIA_ENTRADA'
            ) ";
                    break;

                case 'servicio':
                    $where .= " AND m.TipoMovStockId IN (
                'REG. SERVICIO',
                'ANULACION REG. SERVICIO'
            ) ";
                    break;

                case 'insumo':
                    $where .= " AND m.TipoMovStockId IN (
                'SALIDA INSUMO',
                'ANUL SALIDA INSUMO'
            ) ";
                    break;
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
        FROM movimientostock m
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
            rs.id_cliente,

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
            $sql .= " AND rs.fecha_ingreso >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $sql .= " AND rs.fecha_ingreso <= :hasta";
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

        $sql .= " ORDER BY rs.fecha_ingreso DESC, rs.idrecepcion DESC";

        $stmt = mainModel::conectar()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function reporte_pedidos_detalle_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            pc.idpedido_cabecera,
            pc.fecha,
            pc.estado,
            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario,
            s.suc_descri AS sucursal,
            pd.id_articulo,
            a.codigo,
            a.desc_articulo AS articulo,
            pd.cantidad,
            pd.stock_actual
        FROM pedido_cabecera pc
        INNER JOIN pedido_detalle pd ON pd.idpedido_cabecera = pc.idpedido_cabecera
        LEFT JOIN articulos a ON a.id_articulo = pd.id_articulo
        INNER JOIN usuarios u ON u.id_usuario = pc.id_usuario
        INNER JOIN sucursales s ON s.id_sucursal = pc.id_sucursal
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

        $sql .= " ORDER BY pc.fecha DESC, pc.idpedido_cabecera DESC, a.desc_articulo ASC";
        $stmt = mainModel::conectar()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function reporte_presupuestos_detalle_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            pc.idpresupuesto_compra,
            pc.fecha,
            pc.estado,
            pc.idproveedores,
            pr.razon_social AS proveedor,
            s.suc_descri AS sucursal,
            pd.id_articulo,
            a.codigo,
            a.desc_articulo AS articulo,
            pd.cantidad,
            pd.precio,
            pd.subtotal
        FROM presupuesto_compra pc
        INNER JOIN presupuesto_detalle pd ON pd.idpresupuesto_compra = pc.idpresupuesto_compra
        LEFT JOIN articulos a ON a.id_articulo = pd.id_articulo
        LEFT JOIN proveedores pr ON pr.idproveedores = pc.idproveedores
        INNER JOIN sucursales s ON s.id_sucursal = pc.id_sucursal
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

        $sql .= " ORDER BY pc.fecha DESC, pc.idpresupuesto_compra DESC, a.desc_articulo ASC";
        $stmt = mainModel::conectar()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function reporte_ordenes_compra_detalle_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            oc.idorden_compra,
            oc.fecha,
            oc.estado,
            oc.idproveedores,
            pr.razon_social AS proveedor,
            s.suc_descri AS sucursal,
            ocd.id_articulo,
            a.codigo,
            a.desc_articulo AS articulo,
            ocd.cantidad,
            ocd.cantidad_pendiente,
            ocd.precio_unitario,
            (ocd.cantidad * ocd.precio_unitario) AS subtotal
        FROM orden_compra oc
        INNER JOIN orden_compra_detalle ocd ON ocd.idorden_compra = oc.idorden_compra
        LEFT JOIN articulos a ON a.id_articulo = ocd.id_articulo
        LEFT JOIN proveedores pr ON pr.idproveedores = oc.idproveedores
        INNER JOIN sucursales s ON s.id_sucursal = oc.id_sucursal
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
            $params[':estado'] = (int)$estado;
        }
        if ($sucursal !== null) {
            $sql .= " AND oc.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= " ORDER BY oc.fecha DESC, oc.idorden_compra DESC, a.desc_articulo ASC";
        $stmt = mainModel::conectar()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function reporte_compras_detalle_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            cc.idcompra_cabecera,
            cc.fecha_creacion,
            cc.fecha_factura,
            cc.nro_factura,
            cc.estado,
            cc.idproveedores,
            pr.razon_social AS proveedor,
            s.suc_descri AS sucursal,
            cd.id_articulo,
            a.codigo,
            a.desc_articulo AS articulo,
            cd.cantidad_recibida,
            cd.precio_unitario,
            cd.subtotal
        FROM compra_cabecera cc
        INNER JOIN compra_detalle cd ON cd.idcompra_cabecera = cc.idcompra_cabecera
        LEFT JOIN articulos a ON a.id_articulo = cd.id_articulo
        LEFT JOIN proveedores pr ON pr.idproveedores = cc.idproveedores
        INNER JOIN sucursales s ON s.id_sucursal = cc.id_sucursal
        WHERE 1 = 1
        ";
        $params = [];

        if (!empty($desde)) {
            $sql .= " AND DATE(cc.fecha_factura) >= :desde";
            $params[':desde'] = $desde;
        }
        if (!empty($hasta)) {
            $sql .= " AND DATE(cc.fecha_factura) <= :hasta";
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

        $sql .= " ORDER BY cc.fecha_factura DESC, cc.idcompra_cabecera DESC, a.desc_articulo ASC";
        $stmt = mainModel::conectar()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function reporte_presupuesto_servicio_detalle_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            ps.idpresupuesto_servicio,
            ps.fecha,
            ps.estado,
            ps.id_cliente,
            CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente,
            COALESCE(
                NULLIF(CONCAT_WS(' ', mp.mar_descri, mop.mod_descri, vp.anho), ''),
                NULLIF(CONCAT_WS(' ', mr.mar_descri, mor.mod_descri, vr.anho), ''),
                vp.placa,
                vr.placa,
                '-'
            ) AS vehiculo,
            s.suc_descri AS sucursal,
            pds.id_articulo,
            a.codigo,
            a.desc_articulo AS articulo,
            pds.cantidad,
            pds.preciouni,
            pds.subtotal
        FROM presupuesto_servicio ps
        INNER JOIN presupuesto_detalleservicio pds ON pds.idpresupuesto_servicio = ps.idpresupuesto_servicio
        LEFT JOIN articulos a ON a.id_articulo = pds.id_articulo
        LEFT JOIN clientes c ON c.id_cliente = ps.id_cliente
        LEFT JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
        LEFT JOIN recepcion_servicio rs ON rs.idrecepcion = ds.idrecepcion
        LEFT JOIN vehiculos vp ON vp.id_vehiculo = ps.id_vehiculo
        LEFT JOIN modelo_auto mop ON mop.id_modeloauto = vp.id_modeloauto
        LEFT JOIN marcas mp ON mp.id_marcas = mop.id_marcas
        LEFT JOIN vehiculos vr ON vr.id_vehiculo = rs.id_vehiculo
        LEFT JOIN modelo_auto mor ON mor.id_modeloauto = vr.id_modeloauto
        LEFT JOIN marcas mr ON mr.id_marcas = mor.id_marcas
        INNER JOIN sucursales s ON s.id_sucursal = ps.id_sucursal
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
            $sql .= " AND ps.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= " ORDER BY ps.fecha DESC, ps.idpresupuesto_servicio DESC, a.desc_articulo ASC";
        $stmt = mainModel::conectar()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function reporte_orden_trabajo_detalle_modelo($desde, $hasta, $estado, $sucursal)
    {
        $sql = "
        SELECT
            ot.idorden_trabajo,
            ot.fecha_inicio,
            ot.estado,
            ot.id_cliente,
            CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente,
            CONCAT(m.mar_descri, ' ', mo.mod_descri, ' ', v.anho) AS vehiculo,
            s.suc_descri AS sucursal,
            otd.id_articulo,
            a.codigo,
            a.desc_articulo AS articulo,
            otd.cantidad,
            otd.precio_unitario,
            otd.subtotal
        FROM orden_trabajo ot
        INNER JOIN orden_trabajo_detalle otd ON otd.idorden_trabajo = ot.idorden_trabajo
        LEFT JOIN articulos a ON a.id_articulo = otd.id_articulo
        INNER JOIN clientes c ON c.id_cliente = ot.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = ot.id_vehiculo
        INNER JOIN modelo_auto mo ON mo.id_modeloauto = v.id_modeloauto
        INNER JOIN marcas m ON m.id_marcas = mo.id_marcas
        INNER JOIN sucursales s ON s.id_sucursal = ot.id_sucursal
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
            $sql .= " AND ot.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= " ORDER BY ot.fecha_inicio DESC, ot.idorden_trabajo DESC, a.desc_articulo ASC";
        $stmt = mainModel::conectar()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function reporte_registro_servicio_detalle_modelo($desde, $hasta, $estado, $empleado, $sucursal)
    {
        $sql = "
        SELECT
            rs.idregistro_servicio,
            rs.fecha_servicio,
            rs.estado,
            rs.id_cliente,
            CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente,
            CONCAT(m.mar_descri, ' ', mo.mod_descri, ' ', v.anho) AS vehiculo,
            CONCAT(em.nombre, ' ', em.apellido) AS tecnico,
            s.suc_descri AS sucursal,
            rsd.id_articulo,
            a.codigo,
            a.desc_articulo AS articulo,
            rsd.origen,
            rsd.cantidad,
            rsd.precio_unitario,
            rsd.subtotal
        FROM registro_servicio rs
        INNER JOIN registro_servicio_detalle rsd ON rsd.idregistro_servicio = rs.idregistro_servicio
        LEFT JOIN articulos a ON a.id_articulo = rsd.id_articulo
        INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rs.idorden_trabajo
        LEFT JOIN empleados em ON em.idempleados = ot.tecnico_responsable
        INNER JOIN clientes c ON c.id_cliente = rs.id_cliente
        LEFT JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
        LEFT JOIN modelo_auto mo ON mo.id_modeloauto = v.id_modeloauto
        LEFT JOIN marcas m ON m.id_marcas = mo.id_marcas
        INNER JOIN sucursales s ON s.id_sucursal = rs.id_sucursal
        WHERE 1 = 1
        ";
        $params = [];

        if (!empty($desde)) {
            $sql .= " AND DATE(rs.fecha_servicio) >= :desde";
            $params[':desde'] = $desde;
        }
        if (!empty($hasta)) {
            $sql .= " AND DATE(rs.fecha_servicio) <= :hasta";
            $params[':hasta'] = $hasta;
        }
        if ($estado !== null) {
            $sql .= " AND rs.estado = :estado";
            $params[':estado'] = (int)$estado;
        }
        if ($empleado !== null) {
            $sql .= " AND ot.tecnico_responsable = :tecnico_responsable";
            $params[':tecnico_responsable'] = (int)$empleado;
        }
        if ($sucursal !== null) {
            $sql .= " AND rs.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= " ORDER BY rs.fecha_servicio DESC, rs.idregistro_servicio DESC, a.desc_articulo ASC";
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
            ps.id_cliente,

            COUNT(pds.id_articulo) AS cantidad_items,
            GROUP_CONCAT(DISTINCT pds.id_articulo) AS articulos_ids,

            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario,

            rs.idrecepcion,
            s.suc_descri AS sucursal,

            CONCAT(c.nombre_cliente, ' ',c.apellido_cliente) AS cliente,
            COALESCE(
                NULLIF(CONCAT_WS(' ', mp.mar_descri, mop.mod_descri, vp.anho), ''),
                NULLIF(CONCAT_WS(' ', mr.mar_descri, mor.mod_descri, vr.anho), ''),
                vp.placa,
                vr.placa,
                '-'
            ) AS vehiculo

        FROM presupuesto_servicio ps

        INNER JOIN usuarios u
            ON u.id_usuario = ps.id_usuario

        LEFT JOIN diagnostico_servicio ds
            ON ds.id_diagnostico = ps.id_diagnostico

        LEFT JOIN recepcion_servicio rs
            ON rs.idrecepcion = ds.idrecepcion

        LEFT JOIN sucursales s
            ON s.id_sucursal = ps.id_sucursal

        LEFT JOIN clientes c
            ON c.id_cliente = ps.id_cliente

        LEFT JOIN vehiculos vp
            ON vp.id_vehiculo = ps.id_vehiculo

        LEFT JOIN modelo_auto mop
            ON mop.id_modeloauto = vp.id_modeloauto

        LEFT JOIN marcas mp
            ON mp.id_marcas = mop.id_marcas

        LEFT JOIN vehiculos vr
            ON vr.id_vehiculo = rs.id_vehiculo

        LEFT JOIN modelo_auto mor
            ON mor.id_modeloauto = vr.id_modeloauto

        LEFT JOIN marcas mr
            ON mr.id_marcas = mor.id_marcas

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
            $sql .= " AND ps.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= "
        GROUP BY ps.idpresupuesto_servicio
        ORDER BY ps.fecha DESC, ps.idpresupuesto_servicio DESC
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
            ot.id_cliente,

            ps.idpresupuesto_servicio,
            rs.idrecepcion,

            CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario,

            CONCAT(et.nombre, ' - ' ,et.descripcion) AS equipo,

            CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente,
            CONCAT(m.mar_descri, ' ', mo.mod_descri, ' ', v.anho) AS vehiculo,

            s.suc_descri AS sucursal,

            COUNT(otd.id_articulo) AS cantidad_items,
            GROUP_CONCAT(DISTINCT otd.id_articulo) AS articulos_ids

        FROM orden_trabajo ot

        LEFT JOIN presupuesto_servicio ps
            ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio

        LEFT JOIN diagnostico_servicio ds
            ON ds.id_diagnostico = ps.id_diagnostico

        LEFT JOIN recepcion_servicio rs
            ON rs.idrecepcion = ds.idrecepcion

        INNER JOIN sucursales s
            ON s.id_sucursal = ot.id_sucursal

        INNER JOIN usuarios u
            ON u.id_usuario = ot.id_usuario

        LEFT JOIN equipo_trabajo et
            ON et.id_equipo = ot.idtrabajos

        INNER JOIN clientes c
            ON c.id_cliente = ot.id_cliente

        INNER JOIN vehiculos v
            ON v.id_vehiculo = ot.id_vehiculo

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
            $sql .= " AND ot.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= "
        GROUP BY ot.idorden_trabajo
        ORDER BY ot.fecha_inicio DESC, ot.idorden_trabajo DESC
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
            rs.fecha_servicio,
            rs.fecha_registro,
            rs.estado,
            rs.id_cliente,

            ot.idorden_trabajo,

            CONCAT(ur.usu_nombre, ' ', ur.usu_apellido) AS usuario_registra,
            CONCAT(et.nombre, ' - ' ,et.descripcion) AS equipo,

            CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente,
            CONCAT(m.mar_descri, ' ', mo.mod_descri, ' ', v.anho) AS vehiculo,

            CONCAT(em.nombre,' ',em.apellido) as tecnico,

            s.suc_descri AS sucursal,
            rs.observacion,

            COALESCE(rd.cantidad_items, 0) AS cantidad_items,
            COALESCE(rd.cantidad_repuestos, 0) AS cantidad_repuestos,
            COALESCE(rd.cantidad_insumos, 0) AS cantidad_insumos,
            COALESCE(rd.total_repuestos, 0) AS total_repuestos,
            COALESCE(rd.total_insumos, 0) AS total_insumos,
            COALESCE(rd.total, 0) AS total,
            rd.articulos_ids

        FROM registro_servicio rs

        INNER JOIN orden_trabajo ot
            ON ot.idorden_trabajo = rs.idorden_trabajo

        INNER JOIN sucursales s
            ON s.id_sucursal = rs.id_sucursal

        LEFT JOIN clientes c
            ON c.id_cliente = rs.id_cliente

        LEFT JOIN vehiculos v
            ON v.id_vehiculo = rs.id_vehiculo

        LEFT JOIN modelo_auto mo
            ON mo.id_modeloauto = v.id_modeloauto

        LEFT JOIN marcas m
            ON m.id_marcas = mo.id_marcas

        INNER JOIN usuarios ur
            ON ur.id_usuario = rs.usuario_registra

        LEFT JOIN equipo_trabajo et
            ON et.id_equipo = ot.idtrabajos

        LEFT JOIN (
            SELECT
                idregistro_servicio,
                COUNT(id_articulo) AS cantidad_items,
                COALESCE(SUM(CASE WHEN origen = 'OT' THEN cantidad ELSE 0 END), 0) AS cantidad_repuestos,
                COALESCE(SUM(CASE WHEN origen = 'INSUMO' THEN cantidad ELSE 0 END), 0) AS cantidad_insumos,
                COALESCE(SUM(CASE WHEN origen = 'OT' THEN subtotal ELSE 0 END), 0) AS total_repuestos,
                COALESCE(SUM(CASE WHEN origen = 'INSUMO' THEN subtotal ELSE 0 END), 0) AS total_insumos,
                COALESCE(SUM(subtotal), 0) AS total,
                GROUP_CONCAT(DISTINCT id_articulo) AS articulos_ids
            FROM registro_servicio_detalle
            GROUP BY idregistro_servicio
        ) rd
            ON rd.idregistro_servicio = rs.idregistro_servicio

        LEFT JOIN empleados em
            ON em.idempleados = ot.tecnico_responsable
        WHERE 1 = 1
        ";

        $params = [];

        if (!empty($desde)) {
            $sql .= " AND DATE(rs.fecha_servicio) >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $sql .= " AND DATE(rs.fecha_servicio) <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if ($estado !== null) {
            $sql .= " AND rs.estado = :estado";
            $params[':estado'] = (int)$estado;
        }

        if ($empleado !== null) {
            $sql .= " AND ot.tecnico_responsable = :tecnico_responsable";
            $params[':tecnico_responsable'] = $empleado;
        }

        if ($sucursal !== null) {
            $sql .= " AND rs.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql .= " ORDER BY rs.fecha_servicio DESC, rs.idregistro_servicio DESC";

        $stmt = mainModel::conectar()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function reporte_marcas_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['buscar'])) {
            $where .= " AND m.mar_descri LIKE :buscar ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
        }

        $sql = self::conectar()->prepare("
            SELECT
                m.id_marcas,
                m.mar_descri
            FROM marcas m
            $where
            ORDER BY m.mar_descri ASC
        ");

        $sql->execute($params);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function reporte_kardex_articulo_modelo($f)
    {
        $idArticulo = (int)($f['articulo'] ?? 0);
        $idSucursal = (int)($f['sucursal'] ?? 0);

        if ($idArticulo <= 0 || $idSucursal <= 0) {
            return [];
        }

        $pdo = self::conectar();
        $usaSaldos = self::mov_stock_tiene_saldos($pdo);
        $saldoAnterior = 0.0;

        if (!empty($f['desde'])) {
            $sqlSaldo = $pdo->prepare("
                SELECT COALESCE(SUM(MovStockCantidad * MovStockSigno), 0)
                FROM movimientostock
                WHERE MovStockArticuloId = :articulo
                  AND id_sucursal = :sucursal
                  AND DATE(MovStockFechaHora) < :desde
            ");
            $sqlSaldo->execute([
                ':articulo' => $idArticulo,
                ':sucursal' => $idSucursal,
                ':desde' => $f['desde']
            ]);
            $saldoAnterior = (float)$sqlSaldo->fetchColumn();
        }

        $where = "
            WHERE m.MovStockArticuloId = :articulo
              AND m.id_sucursal = :sucursal
        ";
        $params = [
            ':articulo' => $idArticulo,
            ':sucursal' => $idSucursal
        ];

        if (!empty($f['desde'])) {
            $where .= " AND DATE(m.MovStockFechaHora) >= :desde ";
            $params[':desde'] = $f['desde'];
        }

        if (!empty($f['hasta'])) {
            $where .= " AND DATE(m.MovStockFechaHora) <= :hasta ";
            $params[':hasta'] = $f['hasta'];
        }

        $camposSaldo = $usaSaldos
            ? "m.MovStockSaldoAnterior, m.MovStockSaldoActual,"
            : "NULL AS MovStockSaldoAnterior, NULL AS MovStockSaldoActual,";

        $sql = $pdo->prepare("
            SELECT
                m.MovStockId,
                m.MovStockFechaHora,
                s.suc_descri AS sucursal,
                a.codigo,
                a.desc_articulo,
                m.TipoMovStockId,
                m.MovStockCantidad,
                m.MovStockSigno,
                m.MovStockCosto,
                m.MovStockPrecioVenta,
                m.MovStockNroTicket,
                m.MovStockReferencia,
                {$camposSaldo}
                CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario
            FROM movimientostock m
            INNER JOIN sucursales s ON s.id_sucursal = m.id_sucursal
            LEFT JOIN articulos a ON a.id_articulo = m.MovStockArticuloId
            INNER JOIN usuarios u ON u.id_usuario = m.MovStockUsuario
            {$where}
            ORDER BY m.MovStockFechaHora ASC, m.MovStockId ASC
        ");
        $sql->execute($params);

        $filas = [];
        $saldo = $saldoAnterior;
        $naturaleza = (string)($f['naturaleza'] ?? '');
        if ($naturaleza === 'T') {
            $naturaleza = '';
        }
        $tipoStock = (string)($f['tipo_stock'] ?? '');

        foreach ($sql->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $cantidad = (float)$row['MovStockCantidad'];
            $signo = (int)$row['MovStockSigno'];
            $entrada = $signo > 0 ? $cantidad : 0;
            $salida = $signo < 0 ? $cantidad : 0;
            $saldoFilaAnterior = $row['MovStockSaldoAnterior'] !== null ? (float)$row['MovStockSaldoAnterior'] : $saldo;
            $saldoFilaActual = $row['MovStockSaldoActual'] !== null ? (float)$row['MovStockSaldoActual'] : ($saldo + ($cantidad * $signo));
            $saldo = $saldoFilaActual;
            $grupo = self::grupo_movimiento_stock((string)$row['TipoMovStockId'], $signo);

            if ($tipoStock !== '' && $row['TipoMovStockId'] !== $tipoStock) {
                continue;
            }

            if (!self::movimiento_stock_coincide_naturaleza($grupo, $signo, $naturaleza)) {
                continue;
            }

            $filas[] = [
                'MovStockId' => $row['MovStockId'],
                'id_articulo' => $idArticulo,
                'MovStockFechaHora' => $row['MovStockFechaHora'],
                'sucursal' => $row['sucursal'],
                'codigo' => $row['codigo'],
                'desc_articulo' => $row['desc_articulo'],
                'TipoMovStockId' => $row['TipoMovStockId'],
                'grupo' => ucfirst($grupo),
                'MovStockReferencia' => $row['MovStockReferencia'],
                'entrada' => $entrada,
                'salida' => $salida,
                'MovStockCosto' => $row['MovStockCosto'],
                'saldo_anterior' => $saldoFilaAnterior,
                'saldo_actual' => $saldoFilaActual,
                'usuario' => $row['usuario']
            ];
        }

        return $filas;
    }

    private static function grupo_movimiento_stock($tipo, $signo)
    {
        if (in_array($tipo, ['AJUSTE_INV', 'ANULACION_AJUSTE_INV'], true)) {
            return 'ajuste';
        }

        if (in_array($tipo, ['RECEPCION COMPRA', 'ANULACION COMPRA', 'NC_COMPRA_DEV', 'ANULA_NC_COMPRA'], true)) {
            return 'compra';
        }

        if (in_array($tipo, ['TRANSFERENCIA_SALIDA', 'TRANSFERENCIA_ENTRADA'], true)) {
            return 'transferencia';
        }

        if (in_array($tipo, ['REG. SERVICIO', 'ANULACION REG. SERVICIO'], true)) {
            return 'servicio';
        }

        if (in_array($tipo, ['SALIDA INSUMO', 'ANUL SALIDA INSUMO'], true)) {
            return 'insumo';
        }

        return $signo > 0 ? 'entrada' : 'salida';
    }

    private static function movimiento_stock_coincide_naturaleza($grupo, $signo, $naturaleza)
    {
        if ($naturaleza === '' || $naturaleza === 'T') {
            return true;
        }

        if ($naturaleza === 'entrada') {
            return (int)$signo > 0;
        }

        if ($naturaleza === 'salida') {
            return (int)$signo < 0;
        }

        return $grupo === $naturaleza;
    }

    private static function filtrar_movimientos_stock_por_naturaleza($filas, $naturaleza)
    {
        if ($naturaleza === '' || $naturaleza === 'T') {
            return $filas;
        }

        return array_values(array_filter($filas, function ($row) use ($naturaleza) {
            $signo = (int)($row['MovStockSigno'] ?? 0);
            $grupo = self::grupo_movimiento_stock((string)($row['TipoMovStockId'] ?? ''), $signo);

            return self::movimiento_stock_coincide_naturaleza($grupo, $signo, $naturaleza);
        }));
    }

    public static function resumen_marcas_modelo($f)
    {
        return [
            "total" => count(self::reporte_marcas_modelo($f))
        ];
    }

    public static function reporte_categorias_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['buscar'])) {
            $where .= " AND c.cat_descri LIKE :buscar ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
        }

        $sql = self::conectar()->prepare("
            SELECT
                c.id_categoria,
                c.cat_descri
            FROM categorias c
            $where
            ORDER BY c.cat_descri ASC
        ");

        $sql->execute($params);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function resumen_categorias_modelo($f)
    {
        return [
            "total" => count(self::reporte_categorias_modelo($f))
        ];
    }

    public static function reporte_usuarios_modelo($f)
    {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($f['estado']) && $f['estado'] != 'T') {
            if ($f['estado'] == 'A') {
                $where .= " AND u.usu_estado = 1 ";
            } elseif ($f['estado'] == 'I') {
                $where .= " AND u.usu_estado = 0 ";
            }
        }

        if (!empty($f['buscar'])) {
            $where .= " AND (
                u.usu_nombre LIKE :buscar OR
                u.usu_apellido LIKE :buscar OR
                u.usu_nick LIKE :buscar OR
                u.usu_email LIKE :buscar OR
                u.usu_ci LIKE :buscar
            ) ";
            $params[':buscar'] = '%' . $f['buscar'] . '%';
        }

        $sql = self::conectar()->prepare("
            SELECT
                u.id_usuario,
                u.usu_nombre,
                u.usu_apellido,
                u.usu_nick,
                u.usu_email,
                u.usu_telefono,
                u.usu_ci,
                u.usu_estado,
                s.suc_descri AS sucursal
            FROM usuarios u
            LEFT JOIN sucursales s ON s.id_sucursal = u.sucursalid
            $where
            ORDER BY u.usu_nombre ASC, u.usu_apellido ASC
        ");

        $sql->execute($params);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function resumen_usuarios_modelo($f)
    {
        $datos = self::reporte_usuarios_modelo($f);
        $resumen = [
            "total" => count($datos),
            "activos" => 0,
            "inactivos" => 0
        ];

        foreach ($datos as $row) {
            if ((int)($row['usu_estado'] ?? 0) === 1) {
                $resumen['activos']++;
            } else {
                $resumen['inactivos']++;
            }
        }

        return $resumen;
    }

    public static function resumen_registro_servicio_modelo($desde, $hasta, $estado, $empleado, $sucursal)
    {
        $where = " WHERE 1 = 1 ";
        $params = [];

        if (!empty($desde)) {
            $where .= " AND DATE(rs.fecha_servicio) >= :desde";
            $params[':desde'] = $desde;
        }

        if (!empty($hasta)) {
            $where .= " AND DATE(rs.fecha_servicio) <= :hasta";
            $params[':hasta'] = $hasta;
        }

        if ($estado !== null) {
            $where .= " AND rs.estado = :estado";
            $params[':estado'] = (int)$estado;
        }

        if ($empleado !== null) {
            $where .= " AND ot.tecnico_responsable = :tecnico_responsable";
            $params[':tecnico_responsable'] = $empleado;
        }

        if ($sucursal !== null) {
            $where .= " AND rs.id_sucursal = :sucursal";
            $params[':sucursal'] = (int)$sucursal;
        }

        $sql = "
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) AS registrados,
            SUM(CASE WHEN estado = 2 THEN 1 ELSE 0 END) AS facturados,
            SUM(CASE WHEN estado = 3 THEN 1 ELSE 0 END) AS con_reclamo,
            SUM(CASE WHEN estado = 0 THEN 1 ELSE 0 END) AS anulados,
            COALESCE(SUM(cantidad_items), 0) AS cantidad_items,
            COALESCE(SUM(cantidad_repuestos), 0) AS cantidad_repuestos,
            COALESCE(SUM(cantidad_insumos), 0) AS cantidad_insumos,
            COALESCE(SUM(total_repuestos), 0) AS total_repuestos,
            COALESCE(SUM(total_insumos), 0) AS total_insumos,
            COALESCE(SUM(total), 0) AS total_importe,
            COALESCE(AVG(total), 0) AS promedio_importe
        FROM (
            SELECT
                rs.idregistro_servicio,
                rs.estado,
                COUNT(rsd.id_articulo) AS cantidad_items,
                COALESCE(SUM(CASE WHEN rsd.origen = 'OT' THEN rsd.cantidad ELSE 0 END), 0) AS cantidad_repuestos,
                COALESCE(SUM(CASE WHEN rsd.origen = 'INSUMO' THEN rsd.cantidad ELSE 0 END), 0) AS cantidad_insumos,
                COALESCE(SUM(CASE WHEN rsd.origen = 'OT' THEN rsd.subtotal ELSE 0 END), 0) AS total_repuestos,
                COALESCE(SUM(CASE WHEN rsd.origen = 'INSUMO' THEN rsd.subtotal ELSE 0 END), 0) AS total_insumos,
                COALESCE(SUM(rsd.subtotal), 0) AS total
            FROM registro_servicio rs
            INNER JOIN orden_trabajo ot
                ON ot.idorden_trabajo = rs.idorden_trabajo
            LEFT JOIN registro_servicio_detalle rsd
                ON rsd.idregistro_servicio = rs.idregistro_servicio
            $where
            GROUP BY rs.idregistro_servicio
        ) registros
        ";

        $stmt = mainModel::conectar()->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        $resumen = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            "total" => (int)($resumen['total'] ?? 0),
            "registrados" => (int)($resumen['registrados'] ?? 0),
            "facturados" => (int)($resumen['facturados'] ?? 0),
            "con_reclamo" => (int)($resumen['con_reclamo'] ?? 0),
            "anulados" => (int)($resumen['anulados'] ?? 0),
            "cantidad_items" => (int)($resumen['cantidad_items'] ?? 0),
            "cantidad_repuestos" => (float)($resumen['cantidad_repuestos'] ?? 0),
            "cantidad_insumos" => (float)($resumen['cantidad_insumos'] ?? 0),
            "total_repuestos" => (float)($resumen['total_repuestos'] ?? 0),
            "total_insumos" => (float)($resumen['total_insumos'] ?? 0),
            "total_importe" => (float)($resumen['total_importe'] ?? 0),
            "promedio_importe" => (float)($resumen['promedio_importe'] ?? 0)
        ];
    }
}
