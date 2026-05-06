<?php
require_once "mainModel.php";

class descuentoModelo extends mainModel
{
    protected static function filtros_descuentos_sql($filtros, $alias = 'd')
    {
        $where = [];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $where[] = "($alias.nombre LIKE :buscar OR $alias.descripcion LIKE :buscar)";
            $params[':buscar'] = '%' . $filtros['buscar'] . '%';
        }

        if ($filtros['estado'] !== '' && $filtros['estado'] !== null) {
            $where[] = "$alias.estado = :estado";
            $params[':estado'] = (int)$filtros['estado'];
        }

        if (!empty($filtros['vigente'])) {
            $where[] = "($alias.fecha_inicio IS NULL OR $alias.fecha_inicio <= CURDATE())";
            $where[] = "($alias.fecha_fin IS NULL OR $alias.fecha_fin >= CURDATE())";
        }

        if (!empty($filtros['id_sucursal'])) {
            $where[] = "($alias.id_sucursal IS NULL OR $alias.id_sucursal = :sucursal)";
            $params[':sucursal'] = (int)$filtros['id_sucursal'];
        }

        return [
            'where' => $where ? 'WHERE ' . implode(' AND ', $where) : '',
            'params' => $params
        ];
    }

    protected static function guardar_descuento_modelo($datos)
    {
        try {
            $pdo = mainModel::conectar();
            $sql = $pdo->prepare("
            INSERT INTO descuentos
            (nombre, descripcion, tipo, valor, aplica_a, fecha_inicio, fecha_fin,
             estado, es_reutilizable, id_usuario_crea, id_usuario_modifica,
             id_sucursal, fecha_creacion)
            VALUES
            (:nombre, :descripcion, :tipo, :valor, :aplica_a, :fecha_inicio, :fecha_fin,
             :estado, :reutilizable, :usuario, :usuario_modifica,
             :sucursal, NOW())
        ");

            $sql->execute([
                ":nombre"       => $datos['nombre'],
                ":descripcion"  => $datos['descripcion'],
                ":tipo"         => $datos['tipo'],
                ":valor"        => $datos['valor'],
                ":aplica_a"     => $datos['aplica_a'],
                ":fecha_inicio" => $datos['fecha_inicio'],
                ":fecha_fin"    => $datos['fecha_fin'],
                ":estado"       => $datos['estado'],
                ":reutilizable" => $datos['es_reutilizable'],
                ":usuario"      => $datos['usuario'],
                ":usuario_modifica" => null,
                ":sucursal"     => $datos['id_sucursal']
            ]);

            return (int)$pdo->lastInsertId();
        } catch (Exception $e) {
            return [
                "msg" => $e->getMessage()
            ];
        }
    }

    protected static function guardar_descuento_cliente_modelo($id, $clientes)
    {
        if (empty($clientes)) {
            return false;
        }

        $pdo = mainModel::conectar();
        $pdo->beginTransaction();

        try {

            $sql = $pdo->prepare("
            INSERT IGNORE INTO descuento_cliente (id_descuento, id_cliente)
            VALUES (:d, :c)
        ");

            foreach ($clientes as $cli) {
                $sql->execute([
                    ":d" => $id,
                    ":c" => $cli
                ]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    protected static function eliminar_cliente_descuento_modelo($id_descuento, $id_cliente)
    {
        $sql = mainModel::conectar()->prepare("
        DELETE FROM descuento_cliente
        WHERE id_descuento = :d
          AND id_cliente   = :c
        LIMIT 1
        ");

        return $sql->execute([
            ":d" => $id_descuento,
            ":c" => $id_cliente
        ]);
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
            aplica_a,
            fecha_inicio,
            fecha_fin,
            estado,
            es_reutilizable,
            id_sucursal
        FROM descuentos
        WHERE id_descuento = :id
        LIMIT 1  ");

        $sql->bindParam(":id", $id, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function clientes_asignados_modelo($id_descuento)
    {
        $sql = mainModel::conectar()->prepare("
        SELECT 
            c.id_cliente,
            c.nombre_cliente,
            c.apellido_cliente,
            c.doc_number
        FROM descuento_cliente dc
        INNER JOIN clientes c ON c.id_cliente = dc.id_cliente
        WHERE dc.id_descuento = :id
        ORDER BY c.nombre_cliente ASC
        ");

        $sql->bindParam(":id", $id_descuento, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
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
          AND d.es_reutilizable = 1
          AND (d.fecha_inicio IS NULL OR d.fecha_inicio <= CURDATE())
          AND (d.fecha_fin IS NULL OR d.fecha_fin >= CURDATE())");

        $sql->bindParam(":cliente", $id_cliente, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function listar_descuentos_modelo($inicio = 0, $registros = 15, $filtros = [])
    {
        $f = self::filtros_descuentos_sql($filtros);
        $inicio = max(0, (int)$inicio);
        $registros = max(1, (int)$registros);
        $pdo = mainModel::conectar();

        $sql = $pdo->prepare("
        SELECT
            d.id_descuento,
            d.nombre,
            d.tipo,
            d.valor,
            d.aplica_a,
            d.fecha_inicio,
            d.fecha_fin,
            d.estado,
            s.suc_descri,
            CONCAT(uc.usu_nombre,' ',uc.usu_apellido) AS creado_por,
            CONCAT(um.usu_nombre,' ',um.usu_apellido) AS modificado_por
        FROM descuentos d
        LEFT JOIN sucursales s ON s.id_sucursal = d.id_sucursal
        INNER JOIN usuarios uc ON uc.id_usuario = d.id_usuario_crea
        LEFT JOIN usuarios um ON um.id_usuario = d.id_usuario_modifica
        {$f['where']}
        ORDER BY d.fecha_creacion DESC
        LIMIT :inicio, :registros");

        foreach ($f['params'] as $param => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $sql->bindValue($param, $value, $type);
        }
        $sql->bindValue(':inicio', $inicio, PDO::PARAM_INT);
        $sql->bindValue(':registros', $registros, PDO::PARAM_INT);
        $sql->execute();

        $sqlTotal = $pdo->prepare("
            SELECT COUNT(*)
            FROM descuentos d
            {$f['where']}
        ");

        foreach ($f['params'] as $param => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $sqlTotal->bindValue($param, $value, $type);
        }
        $sqlTotal->execute();

        return [
            'datos' => $sql->fetchAll(PDO::FETCH_ASSOC),
            'total' => (int)$sqlTotal->fetchColumn()
        ];
    }

    protected static function editar_descuento_modelo($d)
    {
        $sql = mainModel::conectar()->prepare("
        UPDATE descuentos SET
            nombre = :nombre,
            descripcion = :descripcion,
            tipo = :tipo,
            valor = :valor,
            aplica_a = :aplica_a,
            fecha_inicio = :fecha_inicio,
            fecha_fin = :fecha_fin,
            estado = :estado,
            id_usuario_modifica = :usuario,
            id_sucursal = :sucursal,
            fecha_actualizacion = NOW()
        WHERE id_descuento = :id    ");

        return $sql->execute([
            ":nombre" => $d['nombre'],
            ":descripcion" => $d['descripcion'],
            ":tipo" => $d['tipo'],
            ":valor" => $d['valor'],
            ":aplica_a" => $d['aplica_a'],
            ":fecha_inicio" => $d['fecha_inicio'],
            ":fecha_fin" => $d['fecha_fin'],
            ":estado" => $d['estado'],
            ":usuario" => $d['usuario'],
            ":sucursal" => $d['id_sucursal'],
            ":id" => $d['id']
        ]);
    }
}
