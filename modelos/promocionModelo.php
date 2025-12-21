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
                (nombre, descripcion, tipo, valor, fecha_inicio, fecha_fin)
                VALUES
                (:nombre, :descripcion, :tipo, :valor, :inicio, :fin)
            ");

            $sql->execute([
                ":nombre"      => $promo['nombre'],
                ":descripcion" => $promo['descripcion'],
                ":tipo"        => $promo['tipo'],
                ":valor"       => $promo['valor'],
                ":inicio"      => $promo['fecha_inicio'],
                ":fin"         => $promo['fecha_fin']
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
}
