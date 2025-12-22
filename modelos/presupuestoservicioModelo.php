<?php
require_once "mainModel.php";

class presupuestoservicioModelo extends mainModel
{
    protected static function guardar_presupuesto_modelo($datos, $detalle)
    {
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            /* CABECERA */
            $sql = $pdo->prepare("
            INSERT INTO presupuesto_servicio
            (id_usuario, fecha, estado, subtotal, total_descuento, total_final, fecha_venc)
            VALUES
            (:usuario, CURDATE(), 1, 0, 0, 0, :venc)
        ");

            $sql->execute([
                ":usuario" => $datos['id_usuario'],
                ":venc"    => $datos['fecha_venc']
            ]);

            $idPresupuesto = $pdo->lastInsertId();

            $subtotal = 0;

            /* DETALLE */
            $sqlDet = $pdo->prepare("
            INSERT INTO presupuesto_detalleservicio
            (idpresupuesto_servicio, id_articulo, cantidad, preciouni, subtotal)
            VALUES
            (:pres, :art, :cant, :precio, :sub)
        ");

            foreach ($detalle as $d) {
                $subLinea = $d['cantidad'] * $d['precio'];
                $subtotal += $subLinea;

                $sqlDet->execute([
                    ":pres"   => $idPresupuesto,
                    ":art"    => $d['id_articulo'],
                    ":cant"   => $d['cantidad'],
                    ":precio" => $d['precio'],
                    ":sub"    => $subLinea
                ]);
            }

            /* ACTUALIZAR TOTALES */
            $sqlUpd = $pdo->prepare("
            UPDATE presupuesto_servicio
            SET subtotal = :sub,
                total_descuento = 0,
                total_final = :sub
            WHERE idpresupuesto_servicio = :id
        ");

            $sqlUpd->execute([
                ":sub" => $subtotal,
                ":id"  => $idPresupuesto
            ]);

            $pdo->commit();
            return $idPresupuesto;
        } catch (Exception $e) {
            $pdo->rollBack();
            return [
                "error" => $e->getMessage()
            ];
        }
    }

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

    protected static function buscar_recepciones_modelo($txt)
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
        WHERE c.nombre_cliente LIKE :b OR v.placa LIKE :b
        ORDER BY r.fecha_ingreso DESC
        LIMIT 20
    ");

        $sql->bindParam(":b", $txt);
        $sql->execute();

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
}
