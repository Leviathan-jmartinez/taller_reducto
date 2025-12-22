<?php
require_once "mainModel.php";

class promocionModelo extends mainModel
{
    /* ================= GUARDAR PROMOCIÓN ================= */
    protected static function guardar_promocion_modelo($promo, $articulos)
    {
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            /* Insertar promoción */
            $sql = $pdo->prepare("
                INSERT INTO promociones
                (nombre, descripcion, tipo, valor, fecha_inicio, fecha_fin, id_usuario_crea)
                VALUES
                (:nombre, :descripcion, :tipo, :valor, :inicio, :fin, :usuario)
            ");

            $sql->execute([
                ":nombre"      => $promo['nombre'],
                ":descripcion" => $promo['descripcion'],
                ":tipo"        => $promo['tipo'],
                ":valor"       => $promo['valor'],
                ":inicio"      => $promo['fecha_inicio'],
                ":fin"         => $promo['fecha_fin'],
                ":usuario" =>   $_SESSION['id_str']
            ]);

            $idPromocion = $pdo->lastInsertId();

            /* Relacionar artículos */
            if (!empty($articulos)) {
                $sqlRel = $pdo->prepare("
                    INSERT INTO promocion_producto (id_promocion, id_articulo)
                    VALUES (:promo, :articulo)
                ");

                foreach ($articulos as $idArticulo) {
                    $sqlRel->execute([
                        ":promo"    => $idPromocion,
                        ":articulo" => intval($idArticulo)
                    ]);
                }
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return [
                "msg" => $e->getMessage()
            ];
        }
    }

    /* ================= BUSCAR ARTÍCULOS ================= */
    protected static function buscar_articulos_modelo($busqueda)
    {
        $busqueda = "%$busqueda%";

        $sql = mainModel::conectar()->prepare("
            SELECT id_articulo, desc_articulo, codigo
            FROM articulos
            WHERE (desc_articulo LIKE :b or codigo LIKE :b)
              AND estado = 1
            ORDER BY desc_articulo
            LIMIT 20
        ");

        $sql->bindParam(":b", $busqueda);
        $sql->execute();

        $articulos = $sql->fetchAll(PDO::FETCH_ASSOC);

        if (!$articulos) {
            return '<div class="alert alert-warning text-center">
                        No se encontraron artículos
                    </div>';
        }

        /* HTML para select2 o modal */
        $html = '<ul class="list-group">';
        foreach ($articulos as $a) {
            $html .= '
            <li class="list-group-item d-flex justify-content-between align-items-center">
                ' . $a['codigo'] . ' - ' . $a['desc_articulo'] . '
                <button type="button" class="btn btn-sm btn-success"
                    onclick="agregarArticuloPromo(' . $a['id_articulo'] . ', \'' . addslashes($a['desc_articulo']) . '\')">
                <i class="fas fa-plus"></i>
                </button>
            </li>';
        }
        $html .= '</ul>';

        return $html;
    }

    protected static function listar_promociones_modelo()
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            p.id_promocion,
            p.nombre,
            p.tipo,
            p.valor,
            p.fecha_inicio,
            p.fecha_fin,
            p.estado,
            CONCAT(u.usu_nombre,' ',u.usu_apellido) AS creado_por
        FROM promociones p
        INNER JOIN usuarios u ON u.id_usuario = p.id_usuario_crea
        ORDER BY p.fecha_creacion DESC");

        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    protected static function cambiar_estado_promocion_modelo($id, $estado)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE promociones
        SET estado = :estado,
            id_usuario_modifica = :usuario,
            fecha_actualizacion = NOW()
        WHERE id_promocion = :id");

        $sql->bindParam(":estado", $estado, PDO::PARAM_INT);
        $sql->bindParam(":usuario", $_SESSION['id_usuario'], PDO::PARAM_INT);
        $sql->bindParam(":id", $id, PDO::PARAM_INT);

        return $sql->execute();
    }

    protected static function datos_promocion_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            p.*,
            CONCAT(u.usu_nombre,' ',u.usu_apellido) AS creado_por,
            CONCAT(um.usu_nombre,' ',um.usu_apellido) AS modificado_por
        FROM promociones p
        INNER JOIN usuarios u ON u.id_usuario = p.id_usuario_crea
        LEFT JOIN usuarios um ON um.id_usuario = p.id_usuario_modifica
        WHERE p.id_promocion = :id
        LIMIT 1");

        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function articulos_promocion_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            a.id_articulo,
            a.desc_articulo
        FROM promocion_producto pp
        INNER JOIN articulos a ON a.id_articulo = pp.id_articulo
        WHERE pp.id_promocion = :id");

        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function editar_promocion_modelo($datos, $articulos)
    {
        $conexion = mainModel::conectar();

        try {
            $conexion->beginTransaction();

            /* PROMOCIÓN */
            $sql = $conexion->prepare("
               UPDATE promociones SET
                    nombre = :nombre,
                    descripcion = :descripcion,
                    tipo = :tipo,
                    valor = :valor,
                    fecha_inicio = :inicio,
                    fecha_fin = :fin,
                    estado = :estado,
                    id_usuario_modifica = :usuario,
                    fecha_actualizacion = NOW()
                WHERE id_promocion = :id");


            $sql->bindParam(":nombre", $datos['nombre']);
            $sql->bindParam(":descripcion", $datos['descripcion']);
            $sql->bindParam(":tipo", $datos['tipo']);
            $sql->bindParam(":valor", $datos['valor']);
            $sql->bindParam(":inicio", $datos['fecha_inicio']);
            $sql->bindParam(":fin", $datos['fecha_fin']);
            $sql->bindParam(":usuario", $_SESSION['id_usuario'], PDO::PARAM_INT);
            $sql->bindParam(":id", $datos['id'], PDO::PARAM_INT);
            $sql->bindParam(":estado", $datos['estado'], PDO::PARAM_INT);


            $sql->execute();

            /* ARTÍCULOS */
            $conexion->prepare("
            DELETE FROM promocion_producto
            WHERE id_promocion = :id
        ")->execute([":id" => $datos['id']]);

            if (!empty($articulos)) {
                $sqlRel = $conexion->prepare("
                INSERT INTO promocion_producto (id_promocion, id_articulo)
                VALUES (:promo, :articulo)
            ");

                foreach ($articulos as $idArt) {
                    $sqlRel->execute([
                        ":promo"    => $datos['id'],
                        ":articulo" => $idArt
                    ]);
                }
            }

            $conexion->commit();
            return true;
        } catch (Exception $e) {
            $conexion->rollBack();
            return ["msg" => $e->getMessage()];
        }
    }
}
