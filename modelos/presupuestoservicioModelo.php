<?php
require_once "mainModel.php";

class presupuestoservicioModelo extends mainModel
{
    protected static function datos_diagnostico_modelo($id)
    {
        $pdo = mainModel::conectar();

        /* CABECERA */
        $sql = $pdo->prepare("
        SELECT 
            c.id_cliente,
            CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
            v.id_vehiculo,
            v.placa AS vehiculo,
            r.kilometraje,
            d.observaciones,
            r.id_sucursal
        FROM diagnostico_servicio d
        INNER JOIN recepcion_servicio r ON r.idrecepcion = d.idrecepcion
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        WHERE d.id_diagnostico = ?
        LIMIT 1
        ");
        $sql->execute([$id]);

        $cabecera = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$cabecera) return [];

        /* DETALLE */
        $sqlDet = $pdo->prepare("
                SELECT 
            problema,
            requiere_repuesto,
            requiere_mano_obra
        FROM diagnostico_detalle
        WHERE id_diagnostico = ?");
        $sqlDet->execute([$id]);

        $cabecera['detalle'] = $sqlDet->fetchAll(PDO::FETCH_ASSOC);

        return $cabecera;
    }

    protected static function buscar_recepciones_modelo($txt, $idSucursal)
    {
        $txt = "%$txt%";

        $sql = mainModel::conectar()->prepare("
        SELECT r.idrecepcion, r.id_cliente, r.id_vehiculo,
               r.kilometraje, r.observacion,
               CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
               CONCAT(ma.mod_descri,' - ',v.placa) AS vehiculo
        FROM recepcion_servicio r
        INNER JOIN clientes c ON c.id_cliente = r.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN modelo_auto ma ON ma.id_modeloauto = v.id_modeloauto
        WHERE r.id_sucursal = :sucursal AND r.estado = 1
          AND (c.nombre_cliente LIKE :b OR v.placa LIKE :b)
        ORDER BY r.fecha_ingreso DESC
        LIMIT 20    ");

        $sql->execute([
            ':sucursal' => $idSucursal,
            ':b' => $txt
        ]);

        $html = '<table class="table table-sm">';
        foreach ($sql->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $json = htmlspecialchars(json_encode($r), ENT_QUOTES);
            $html .= "
        <tr>
            <td>{$r['cliente']}</td>
            <td>{$r['vehiculo']}</td>
            <td>
                <button class='btn btn-success btn-sm'
                        onclick='seleccionarRecepcion($json)'>
                    Seleccionar
                </button>
            </td>
        </tr>";
        }
        $html .= '</table>';

        return $html;
    }

    protected static function buscar_servicios_modelo($txt, $sucursal)
    {
        $txt = "%$txt%";

        $sql = mainModel::conectar()->prepare("
        SELECT
            a.id_articulo,
            a.desc_articulo,
            a.codigo,
            a.precio_venta,
            a.tipo,
            IFNULL(s.stockDisponible, 0) AS stock
        FROM articulos a
        LEFT JOIN stock s 
            ON s.id_articulo = a.id_articulo 
            AND s.id_sucursal = :sucursal
        WHERE a.estado = 1
          AND (a.desc_articulo LIKE :b OR a.codigo LIKE :b)
        ORDER BY a.desc_articulo
        LIMIT 20
        ");

        $sql->bindParam(':b', $txt);
        $sql->bindParam(':sucursal', $sucursal, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function promo_articulo_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            p.id_promocion,
            p.nombre,
            p.tipo,
            p.valor
        FROM promociones p
        INNER JOIN promocion_producto pp
            ON pp.id_promocion = p.id_promocion
        WHERE pp.id_articulo = :id
          AND p.estado = 1
          AND CURDATE() BETWEEN p.fecha_inicio AND p.fecha_fin
        LIMIT 1
        ");

        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        $sql->execute();

        $promo = $sql->fetch(PDO::FETCH_ASSOC);

        return json_encode($promo ?: []);
    }

    protected static function descuentos_cliente_modelo($idCliente)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            d.id_descuento,
            d.nombre,
            d.tipo,
            d.valor,
            d.es_reutilizable
        FROM descuentos d
        INNER JOIN descuento_cliente dc
            ON dc.id_descuento = d.id_descuento
        WHERE dc.id_cliente = :cliente
          AND d.estado = 1");

        $sql->bindParam(':cliente', $idCliente, PDO::PARAM_INT);
        $sql->execute();

        return json_encode($sql->fetchAll(PDO::FETCH_ASSOC));
    }

    protected static function guardar_presupuesto_modelo($d)
    {
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            /* ================= OBTENER SUCURSAL DESDE DIAGNÓSTICO ================= */
            $sqlSuc = $pdo->prepare("
                SELECT r.id_sucursal
                FROM diagnostico_servicio d
                INNER JOIN recepcion_servicio r 
                    ON r.idrecepcion = d.idrecepcion
                WHERE d.id_diagnostico = :id
            ");

            $sqlSuc->execute([
                ':id' => $d['id_diagnostico']
            ]);

            $idSucursal = $sqlSuc->fetchColumn();

            if (!$idSucursal) {
                $pdo->rollBack();
                return [
                    'error' => true,
                    'msg' => 'No se pudo obtener la sucursal del diagnóstico'
                ];
            }

            /* ================= VALIDAR SUCURSAL ================= */
            if ($idSucursal != $_SESSION['nick_sucursal']) {
                $pdo->rollBack();
                return [
                    'error' => true,
                    'msg' => 'Sucursal inválida'
                ];
            }

            /* ================= VALIDAR STOCK ================= */
            foreach ($d['detalle'] as $it) {

                if (!empty($it['tipo']) && $it['tipo'] === 'ARTICULO') {

                    $sqlStock = $pdo->prepare("
                    SELECT stockDisponible
                    FROM stock
                    WHERE id_articulo = :articulo
                    AND id_sucursal = :sucursal
                ");

                    $sqlStock->execute([
                        ':articulo' => $it['id_articulo'],
                        ':sucursal' => $idSucursal
                    ]);

                    $stockActual = $sqlStock->fetchColumn();

                    if ($stockActual === false) {
                        $pdo->rollBack();
                        return [
                            'error' => true,
                            'msg' => "No existe stock para {$it['descripcion']}"
                        ];
                    }

                    if ($it['cantidad'] > $stockActual) {
                        $pdo->rollBack();
                        return [
                            'error' => true,
                            'msg' => "Stock insuficiente para {$it['descripcion']}"
                        ];
                    }
                }
            }

            /* ================= INSERT PRESUPUESTO ================= */
            $sql = $pdo->prepare("
            INSERT INTO presupuesto_servicio
            (id_usuario, id_sucursal, fecha, estado, fecha_venc,
             subtotal, total_descuento, total_final, id_diagnostico)
            VALUES
            (:usuario, :sucursal, CURDATE(), 1, :fecha_venc,
             :subtotal, :total_desc, :total_final, :id_diagnostico)
        ");

            $sql->execute([
                ':usuario'       => $d['usuario'],
                ':sucursal'      => $idSucursal,
                ':fecha_venc'    => $d['fecha_venc'],
                ':subtotal'      => $d['subtotal'],
                ':total_desc'    => $d['total_descuento'],
                ':total_final'   => $d['total_final'],
                ':id_diagnostico' => $d['id_diagnostico']
            ]);

            $idPresupuesto = $pdo->lastInsertId();

            /* ================= DETALLE ================= */
            $sqlDet = $pdo->prepare("
            INSERT INTO presupuesto_detalleservicio
            (id_articulo, idpresupuesto_servicio, cantidad, preciouni, subtotal)
            VALUES
            (:articulo, :presupuesto, :cantidad, :precio, :subtotal)
            ");

            foreach ($d['detalle'] as $it) {
                $sqlDet->execute([
                    ':articulo'    => $it['id_articulo'],
                    ':presupuesto' => $idPresupuesto,
                    ':cantidad'    => $it['cantidad'],
                    ':precio'      => $it['precio_final'],
                    ':subtotal'    => $it['subtotal']
                ]);
            }

            /* ================= ACTUALIZAR DIAGNÓSTICO ================= */
            $sqlUpd = $pdo->prepare("
            UPDATE diagnostico_servicio
            SET estado = 3
            WHERE id_diagnostico = :id
            ");
            $sqlUpd->execute([
                ':id' => $d['id_diagnostico']
            ]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {

            $pdo->rollBack();

            return [
                'error' => true,
                'msg'   => $e->getMessage()
            ];
        }
    }

    protected static function actualizar_estado_recepcion_modelo($idrecepcion)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE recepcion_servicio
        SET estado = 2,
            fecha_actualizacion = NOW()
        WHERE idrecepcion = :id");

        $sql->bindParam(':id', $idrecepcion, PDO::PARAM_INT);
        return $sql->execute();
    }

    protected static function listar_presupuestos_modelo($inicio, $registros, $filtrosSQL)
    {
        $conexion = mainModel::conectar();

        $baseSQL = "
        FROM presupuesto_servicio ps
        LEFT JOIN diagnostico_servicio d ON d.id_diagnostico = ps.id_diagnostico
        LEFT JOIN recepcion_servicio r ON r.idrecepcion = d.idrecepcion
        LEFT JOIN clientes c ON c.id_cliente = r.id_cliente 
        LEFT JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo 
        LEFT JOIN modelo_auto ma ON ma.id_modeloauto = v.id_modeloauto 
        INNER JOIN usuarios u ON u.id_usuario = ps.id_usuario
        WHERE 1=1
        $filtrosSQL
        ";

        $selectSQL = "
        SELECT 
            ps.idpresupuesto_servicio,
            ps.fecha,
            ps.estado AS estadoPre,
            ps.total_final,
            c.nombre_cliente,
            c.apellido_cliente,
            v.placa,
            ma.mod_descri AS modelo,
            u.usu_nombre,
            u.usu_apellido
        ";

        $orderSQL = "ORDER BY ps.idpresupuesto_servicio DESC";

        return mainModel::ejecutarPaginador(
            $conexion,
            $baseSQL,
            $selectSQL,
            $orderSQL,
            $inicio,
            $registros
        );
    }
    protected static function anular_presupuesto_full_modelo($id)
    {
        $pdo = mainModel::conectar();

        try {

            $pdo->beginTransaction();

            // 🔹 obtener datos
            $sql = $pdo->prepare("
            SELECT estado, id_sucursal, id_diagnostico
            FROM presupuesto_servicio
            WHERE idpresupuesto_servicio = :id
            ");
            $sql->execute([':id' => $id]);
            $pres = $sql->fetch(PDO::FETCH_ASSOC);

            if (!$pres) {
                return ['error' => true, 'msg' => 'No existe'];
            }

            // 🔹 validar OT
            $sql = $pdo->prepare("
            SELECT COUNT(*) 
            FROM orden_trabajo
            WHERE id_presupuesto = :id
            ");
            $sql->execute([':id' => $id]);

            if ($sql->fetchColumn() > 0) {
                return ['error' => true, 'msg' => 'Tiene OT'];
            }

            // 🔹 anular
            $sql = $pdo->prepare("
            UPDATE presupuesto_servicio
            SET estado = 0
            WHERE idpresupuesto_servicio = :id
            ");
            $sql->execute([':id' => $id]);

            // 🔹 actualizar diagnóstico
            $sql = $pdo->prepare("
            UPDATE diagnostico_servicio
            SET estado = 2
            WHERE id_diagnostico = :id_diag
            ");
            $sql->execute([
                ':id_diag' => $pres['id_diagnostico']
            ]);

            $pdo->commit();

            return [
                'ok' => true,
                'data' => $pres
            ];
        } catch (Exception $e) {

            $pdo->rollBack();

            return [
                'error' => true,
                'msg' => $e->getMessage()
            ];
        }
    }

    protected static function revertir_estado_recepcion_modelo($idrecepcion)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE recepcion_servicio
        SET estado = 1,
            fecha_actualizacion = NOW()
        WHERE idrecepcion = :id");

        $sql->bindParam(':id', $idrecepcion, PDO::PARAM_INT);
        return $sql->execute();
    }


    protected static function aprobar_presupuesto_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
            UPDATE presupuesto_servicio
            SET estado = 2
            WHERE idpresupuesto_servicio = :id
              AND estado = 1
        ");

        return $sql->execute([
            ':id' => $id
        ]);
    }

    protected static function obtener_presupuesto_cabecera($id)
    {
        $sql = self::conectar()->prepare("
            SELECT
                ps.idpresupuesto_servicio,
                ps.fecha,
                ps.fecha_venc,
                ps.estado,
                ps.subtotal,
                ps.total_descuento,
                ps.total_final,

                c.nombre_cliente,
                c.apellido_cliente,
                c.celular_cliente,
                c.direccion_cliente,

                v.placa,
                ma.mod_descri AS modelo,

                u.usu_nombre,
                u.usu_apellido
            FROM presupuesto_servicio ps
            INNER JOIN diagnostico_servicio d ON d.id_diagnostico = ps.id_diagnostico
            INNER JOIN recepcion_servicio r ON r.idrecepcion = d.idrecepcion
            INNER JOIN clientes c ON c.id_cliente = r.id_cliente
            INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
            INNER JOIN modelo_auto ma ON ma.id_modeloauto = v.id_modeloauto
            INNER JOIN usuarios u ON u.id_usuario = ps.id_usuario
            WHERE ps.idpresupuesto_servicio = :id
            LIMIT 1
        ");
        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function obtener_presupuesto_detalle($id)
    {
        $sql = self::conectar()->prepare("
            SELECT
                a.desc_articulo,
                d.cantidad,
                d.preciouni,
                d.subtotal
            FROM presupuesto_detalleservicio d
            INNER JOIN articulos a ON a.id_articulo = d.id_articulo
            WHERE d.idpresupuesto_servicio = :id
        ");
        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
