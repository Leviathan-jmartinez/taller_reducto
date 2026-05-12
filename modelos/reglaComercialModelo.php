<?php
require_once "mainModel.php";

class reglaComercialModelo extends mainModel
{
    protected static function filtros_reglas_sql($filtros, $alias = 'r')
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
            $where[] = "CURDATE() BETWEEN $alias.fecha_inicio AND $alias.fecha_fin";
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

    protected static function guardar_regla_modelo($regla, $condiciones, $descuentos)
    {
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            $sql = $pdo->prepare("
                INSERT INTO regla_comercial
                (nombre, descripcion, fecha_inicio, fecha_fin, id_sucursal, prioridad,
                 modo_competencia, estado, id_usuario_crea, fecha_creacion)
                VALUES
                (:nombre, :descripcion, :fecha_inicio, :fecha_fin, :sucursal, :prioridad,
                 :modo_competencia, :estado, :usuario, NOW())
            ");

            $sql->execute([
                ':nombre' => $regla['nombre'],
                ':descripcion' => $regla['descripcion'],
                ':fecha_inicio' => $regla['fecha_inicio'],
                ':fecha_fin' => $regla['fecha_fin'],
                ':sucursal' => $regla['id_sucursal'],
                ':prioridad' => $regla['prioridad'],
                ':modo_competencia' => $regla['modo_competencia'],
                ':estado' => $regla['estado'],
                ':usuario' => $regla['usuario']
            ]);

            $idRegla = (int)$pdo->lastInsertId();
            self::guardar_condiciones_descuentos($pdo, $idRegla, $condiciones, $descuentos);

            $pdo->commit();
            return $idRegla;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['error' => true, 'msg' => $e->getMessage()];
        }
    }

    protected static function editar_regla_modelo($regla, $condiciones, $descuentos)
    {
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            $sql = $pdo->prepare("
                UPDATE regla_comercial SET
                    nombre = :nombre,
                    descripcion = :descripcion,
                    fecha_inicio = :fecha_inicio,
                    fecha_fin = :fecha_fin,
                    id_sucursal = :sucursal,
                    prioridad = :prioridad,
                    modo_competencia = :modo_competencia,
                    estado = :estado,
                    id_usuario_modifica = :usuario,
                    fecha_actualizacion = NOW()
                WHERE id_regla = :id
            ");

            $sql->execute([
                ':nombre' => $regla['nombre'],
                ':descripcion' => $regla['descripcion'],
                ':fecha_inicio' => $regla['fecha_inicio'],
                ':fecha_fin' => $regla['fecha_fin'],
                ':sucursal' => $regla['id_sucursal'],
                ':prioridad' => $regla['prioridad'],
                ':modo_competencia' => $regla['modo_competencia'],
                ':estado' => $regla['estado'],
                ':usuario' => $regla['usuario'],
                ':id' => $regla['id']
            ]);

            $pdo->prepare("DELETE FROM regla_comercial_condicion WHERE id_regla = :id")
                ->execute([':id' => $regla['id']]);
            $pdo->prepare("DELETE FROM regla_comercial_descuento WHERE id_regla = :id")
                ->execute([':id' => $regla['id']]);

            self::guardar_condiciones_descuentos($pdo, (int)$regla['id'], $condiciones, $descuentos);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['error' => true, 'msg' => $e->getMessage()];
        }
    }

    private static function guardar_condiciones_descuentos($pdo, $idRegla, $condiciones, $descuentos)
    {
        $sqlCond = $pdo->prepare("
            INSERT INTO regla_comercial_condicion
            (id_regla, tipo_condicion, operador, valor_ref, valor_texto)
            VALUES (:regla, :tipo, :operador, :valor_ref, :valor_texto)
        ");

        foreach ($condiciones as $cond) {
            $sqlCond->execute([
                ':regla' => $idRegla,
                ':tipo' => $cond['tipo_condicion'],
                ':operador' => $cond['operador'],
                ':valor_ref' => $cond['valor_ref'],
                ':valor_texto' => $cond['valor_texto']
            ]);
        }

        $sqlDesc = $pdo->prepare("
            INSERT INTO regla_comercial_descuento
            (id_regla, nombre, tipo, valor, cantidad_requerida, cantidad_cobrada,
             aplica_a, alcance_tipo, alcance_ref)
            VALUES (:regla, :nombre, :tipo, :valor, :cantidad_requerida, :cantidad_cobrada,
             :aplica_a, :alcance_tipo, :alcance_ref)
        ");

        foreach ($descuentos as $desc) {
            $sqlDesc->execute([
                ':regla' => $idRegla,
                ':nombre' => $desc['nombre'],
                ':tipo' => $desc['tipo'],
                ':valor' => $desc['valor'],
                ':cantidad_requerida' => $desc['cantidad_requerida'],
                ':cantidad_cobrada' => $desc['cantidad_cobrada'],
                ':aplica_a' => $desc['aplica_a'],
                ':alcance_tipo' => $desc['alcance_tipo'],
                ':alcance_ref' => $desc['alcance_ref']
            ]);
        }
    }

    protected static function listar_reglas_modelo($inicio = 0, $registros = 15, $filtros = [])
    {
        $f = self::filtros_reglas_sql($filtros);
        $pdo = mainModel::conectar();
        $inicio = max(0, (int)$inicio);
        $registros = max(1, (int)$registros);

        $sql = $pdo->prepare("
            SELECT r.id_regla, r.nombre, r.fecha_inicio, r.fecha_fin, r.prioridad,
                   r.modo_competencia, r.estado, s.suc_descri,
                   COUNT(DISTINCT c.id_condicion) AS condiciones,
                   COUNT(DISTINCT d.id_regla_descuento) AS descuentos
            FROM regla_comercial r
            LEFT JOIN sucursales s ON s.id_sucursal = r.id_sucursal
            LEFT JOIN regla_comercial_condicion c ON c.id_regla = r.id_regla
            LEFT JOIN regla_comercial_descuento d ON d.id_regla = r.id_regla
            {$f['where']}
            GROUP BY r.id_regla
            ORDER BY r.prioridad DESC, r.id_regla DESC
            LIMIT :inicio, :registros
        ");

        foreach ($f['params'] as $param => $value) {
            $sql->bindValue($param, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $sql->bindValue(':inicio', $inicio, PDO::PARAM_INT);
        $sql->bindValue(':registros', $registros, PDO::PARAM_INT);
        $sql->execute();

        $sqlTotal = $pdo->prepare("SELECT COUNT(*) FROM regla_comercial r {$f['where']}");
        foreach ($f['params'] as $param => $value) {
            $sqlTotal->bindValue($param, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $sqlTotal->execute();

        return [
            'datos' => $sql->fetchAll(PDO::FETCH_ASSOC),
            'total' => (int)$sqlTotal->fetchColumn()
        ];
    }

    protected static function datos_regla_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("SELECT * FROM regla_comercial WHERE id_regla = :id LIMIT 1");
        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    protected static function condiciones_regla_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
            SELECT tipo_condicion, operador, valor_ref, valor_texto
            FROM regla_comercial_condicion
            WHERE id_regla = :id
            ORDER BY id_condicion
        ");
        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function descuentos_regla_modelo($id)
    {
        $sql = mainModel::conectar()->prepare("
            SELECT nombre, tipo, valor, cantidad_requerida, cantidad_cobrada,
                   aplica_a, alcance_tipo, alcance_ref
            FROM regla_comercial_descuento
            WHERE id_regla = :id
            ORDER BY id_regla_descuento
        ");
        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
