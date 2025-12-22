<?php
require_once "mainModel.php";

class descuentoModelo extends mainModel
{

    protected static function guardar_descuento_modelo($datos)
    {
        try {
            $sql = mainModel::conectar()->prepare("
            INSERT INTO descuentos
            (nombre, descripcion, tipo, valor, estado, es_reutilizable, id_usuario_crea)
            VALUES
            (:nombre, :descripcion, :tipo, :valor, :estado, :reutilizable, :usuario)
        ");

            $sql->execute([
                ":nombre"       => $datos['nombre'],
                ":descripcion"  => $datos['descripcion'],
                ":tipo"         => $datos['tipo'],
                ":valor"        => $datos['valor'],
                ":estado"       => $datos['estado'],
                ":reutilizable" => $datos['es_reutilizable'],
                ":usuario"      => $datos['usuario']
            ]);

            return true;
        } catch (Exception $e) {
            return [
                "msg" => $e->getMessage()
            ];
        }
    }

    protected static function guardar_descuento_cliente_modelo($id, $clientes)
    {
        $pdo = mainModel::conectar();
        $pdo->beginTransaction();

        $pdo->prepare("DELETE FROM descuento_cliente WHERE id_descuento = :id")
            ->execute([":id" => $id]);

        if (!empty($clientes)) {
            $sql = $pdo->prepare("
            INSERT INTO descuento_cliente (id_descuento, id_cliente)
            VALUES (:d, :c)
        ");

            foreach ($clientes as $cli) {
                $sql->execute([
                    ":d" => $id,
                    ":c" => $cli
                ]);
            }
        }

        $pdo->commit();
        return true;
    }

    protected static function datos_descuento_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT 
            id_descuento,
            nombre,
            descripcion,
            tipo,
            valor,
            estado,
            es_reutilizable
        FROM descuentos
        WHERE id_descuento = :id
        LIMIT 1  ");

        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function buscar_clientes_modelo($busqueda)
    {
        $busqueda = "%" . trim($busqueda) . "%";

        $sql = mainModel::conectar()->prepare("
        SELECT 
            id_cliente,
            doc_number,
            nombre_cliente,
            apellido_cliente
        FROM clientes
        WHERE (
            doc_number LIKE :b
            OR nombre_cliente LIKE :b
            OR apellido_cliente LIKE :b
        )
        AND estado_cliente = 1
        ORDER BY nombre_cliente
        LIMIT 20 ");

        $sql->bindParam(":b", $busqueda, PDO::PARAM_STR);
        $sql->execute();

        $clientes = $sql->fetchAll(PDO::FETCH_ASSOC);

        if (!$clientes) {
            return '<div class="alert alert-warning text-center">
                    No se encontraron clientes
                </div>';
        }

        $html = '<ul class="list-group">';

        foreach ($clientes as $c) {
            $nombre = $c['nombre_cliente'] . ' ' . $c['apellido_cliente'];

            $html .= '
            <li class="list-group-item d-flex justify-content-between align-items-center">
                ' . $c['doc_number'] . ' - ' . $nombre . '
                <button type="button"
                        class="btn btn-sm btn-success"
                        onclick="agregarClienteDescuento(' . $c['id_cliente'] . ', \'' . addslashes($nombre) . '\')">
                    <i class="fas fa-plus"></i>
                </button>
            </li>';
        }

        $html .= '</ul>';

        return $html;
    }

    protected static function descuentos_por_cliente_modelo($id_cliente)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            d.id_descuento,
            d.nombre,
            d.tipo,
            d.valor
        FROM descuentos d
        INNER JOIN descuento_cliente dc
            ON dc.id_descuento = d.id_descuento
        WHERE dc.id_cliente = :cliente
          AND d.estado = 1
          AND d.es_reutilizable = 1");

        $sql->bindParam(":cliente", $id_cliente, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    protected static function listar_descuentos_modelo()
    {
        $sql = mainModel::conectar()->prepare("
        SELECT
            id_descuento,
            nombre,
            tipo,
            valor,
            estado
        FROM descuentos
        ORDER BY fecha_creacion DESC");

        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    protected static function editar_descuento_modelo($d)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE descuentos SET
            nombre = :nombre,
            descripcion = :descripcion,
            tipo = :tipo,
            valor = :valor,
            estado = :estado,
            id_usuario_modifica = :usuario,
            fecha_actualizacion = NOW()
        WHERE id_descuento = :id    ");

        return $sql->execute([
            ":nombre" => $d['nombre'],
            ":descripcion" => $d['descripcion'],
            ":tipo" => $d['tipo'],
            ":valor" => $d['valor'],
            ":estado" => $d['estado'],
            ":usuario" => $d['usuario'],
            ":id" => $d['id']
        ]);
    }
}
