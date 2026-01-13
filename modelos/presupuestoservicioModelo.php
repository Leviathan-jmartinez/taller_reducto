<?php
require_once "mainModel.php";

class presupuestoservicioModelo extends mainModel
{
    protected static function datos_recepcion_modelo($idrecepcion)
    {
        $sql = mainModel::conectar()->prepare("
            SELECT
                r.idrecepcion,
                r.id_cliente,
                r.id_vehiculo,
                r.fecha_ingreso,
                r.kilometraje,
                r.observacion,

                CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente,

                CONCAT(ma.descripcion, ' - ', v.placa) AS vehiculo

            FROM recepcion_servicio r
            INNER JOIN clientes c ON c.id_cliente = r.id_cliente
            INNER JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
            INNER JOIN modelo_auto ma ON ma.id_modeloauto = v.id_modeloauto

            WHERE r.idrecepcion = :id
            LIMIT 1
        ");

        $sql->bindParam(":id", $idrecepcion, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetch(PDO::FETCH_ASSOC);
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

    protected static function buscar_servicios_modelo($txt)
    {
        $txt = "%$txt%";

        $sql = mainModel::conectar()->prepare("
        SELECT
            id_articulo,
            desc_articulo,
            codigo,
            precio_venta
        FROM articulos
        WHERE estado = 1
          AND (desc_articulo LIKE :b OR codigo LIKE :b)
        ORDER BY desc_articulo
        LIMIT 20
        ");

        $sql->bindParam(':b', $txt);
        $sql->execute();

        $datos = $sql->fetchAll(PDO::FETCH_ASSOC);

        if (!$datos) {
            return '<div class="alert alert-warning text-center">
                    No se encontraron servicios
                </div>';
        }

        $html = '<ul class="list-group">';
        foreach ($datos as $d) {

            $desc = addslashes($d['desc_articulo']);
            $precio = (int)$d['precio_venta'];

            $html .= "
        <li class='list-group-item d-flex justify-content-between align-items-center'>
            <div>
                <strong>{$d['codigo']}</strong> - {$d['desc_articulo']}
                <br>
                <small class='text-muted'>Precio: Gs. " . number_format($precio, 0, ',', '.') . "</small>
            </div>

            <button type='button'
                    class='btn btn-success btn-sm'
                    onclick=\"agregarServicio({$d['id_articulo']}, '{$desc}', {$precio})\">
                <i class='fas fa-plus'></i>
            </button>
        </li>";
        }
        $html .= '</ul>';

        return $html;
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

            /* ================= CABECERA ================= */
            $sql = $pdo->prepare("
            INSERT INTO presupuesto_servicio
            (id_usuario, fecha, estado, fecha_venc,
             subtotal, total_descuento, total_final, idrecepcion)
            VALUES
            (:usuario, CURDATE(), 1, :fecha_venc,
             :subtotal, :total_desc, :total_final, :idrecepcion)
        ");

            $sql->execute([
                ':usuario'     => $d['usuario'],
                ':fecha_venc'  => $d['fecha_venc'],
                ':subtotal'    => $d['subtotal'],
                ':total_desc'  => $d['total_descuento'],
                ':total_final' => $d['total_final'],
                ':idrecepcion' => $d['idrecepcion']
            ]);

            $idPresupuesto = $pdo->lastInsertId();

            /* ================= ACTUALIZAR ESTADO RECEPCIÓN ================= */
            if (!empty($d['idrecepcion'])) {

                $sqlUpd = $pdo->prepare("
                UPDATE recepcion_servicio
                SET estado = 2,
                    fecha_actualizacion = NOW()
                WHERE idrecepcion = :id ");

                $sqlUpd->execute([
                    ':id' => $d['idrecepcion']
                ]);
            }


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
                    ':precio'      => $it['precio_final'], // precio ya con promo
                    ':subtotal'    => $it['subtotal']
                ]);
            }

            /* ================= PROMOCIONES ================= */
            /* ================= PROMOCIONES ================= */
            $sqlPromo = $pdo->prepare("
            INSERT INTO presupuesto_promocion
            (idpresupuesto_servicio, id_promocion, monto_aplicado)
            VALUES
            (:presupuesto, :promocion, :monto)");

            foreach ($d['detalle'] as $it) {

                if (
                    isset($it['promocion']) &&
                    is_array($it['promocion']) &&
                    !empty($it['promocion']['id_promocion'])
                ) {

                    $precioBase  = $it['precio_base'];
                    $precioFinal = $it['precio_final'];
                    $cantidad    = $it['cantidad'];

                    // 🔥 monto real aplicado por la promo
                    $montoPromo = ($precioBase - $precioFinal) * $cantidad;

                    if ($montoPromo > 0) {
                        $sqlPromo->execute([
                            ':presupuesto' => $idPresupuesto,
                            ':promocion'   => $it['promocion']['id_promocion'],
                            ':monto'       => $montoPromo
                        ]);
                    }
                }
            }


            /* ================= DESCUENTOS CLIENTE ================= */
            if (!empty($d['descuentos'])) {

                $sqlDesc = $pdo->prepare("
                INSERT INTO presupuesto_descuento
                (id_presupuesto, id_descuento, tipo, valor,
                 monto_aplicado, motivo, id_usuario)
                VALUES
                (:presupuesto, :descuento, :tipo, :valor,
                 :monto, :motivo, :usuario)
            ");

                foreach ($d['descuentos'] as $des) {

                    $sqlDesc->execute([
                        ':presupuesto' => $idPresupuesto,
                        ':descuento'   => $des['id_descuento'],
                        ':tipo'        => $des['tipo'],
                        ':valor'       => $des['valor'],
                        ':monto'       => $des['monto'],
                        ':motivo'      => $des['nombre'] ?? null,
                        ':usuario'     => $d['usuario']
                    ]);
                }
            }

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
    protected static function anular_presupuesto_servicio_modelo($idpresupuesto)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE presupuesto_servicio
        SET estado = 0
        WHERE idpresupuesto_servicio = :id
        ");

        $sql->bindParam(":id", $idpresupuesto, PDO::PARAM_INT);
        return $sql->execute();
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

    protected static function listar_presupuestos_modelo()
    {
        $sql = mainModel::conectar()->prepare("
            SELECT
                p.idpresupuesto_servicio,
                p.fecha,
                p.fecha_venc,
                p.subtotal,
                p.total_descuento,
                p.total_final,
                p.estado,
                CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
                CONCAT(ma.mod_descri,' - ',v.placa) AS vehiculo,
                CONCAT(u.usu_nombre,' ',u.usu_apellido) AS creado_por
            FROM presupuesto_servicio p
            LEFT JOIN recepcion_servicio r ON r.idrecepcion = p.idrecepcion
            LEFT JOIN clientes c ON c.id_cliente = r.id_cliente
            LEFT JOIN vehiculos v ON v.id_vehiculo = r.id_vehiculo
            LEFT JOIN modelo_auto ma ON ma.id_modeloauto = v.id_modeloauto
            INNER JOIN usuarios u ON u.id_usuario = p.id_usuario
            WHERE r.id_sucursal = '{$_SESSION['nick_sucursal']}'
            AND p.estado != 0
            ORDER BY p.idpresupuesto_servicio DESC
        ");

        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
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
            INNER JOIN recepcion_servicio r ON r.idrecepcion = ps.idrecepcion
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
