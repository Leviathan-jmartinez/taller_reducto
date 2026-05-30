<?php
require_once "mainModel.php";

class recepcionservicioModelo extends mainModel
{
    protected static function listar_ciudades_modelo()
    {
        $sql = mainModel::conectar()->prepare("
            SELECT id_ciudad, ciu_descri
            FROM ciudades
            ORDER BY ciu_descri ASC
        ");
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function listar_modelos_modelo()
    {
        $sql = mainModel::conectar()->prepare("
            SELECT id_modeloauto, mod_descri
            FROM modelo_auto
            WHERE estado = 1
            ORDER BY mod_descri ASC
        ");
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function existe_cliente_documento_modelo($doc)
    {
        $sql = mainModel::conectar()->prepare("
            SELECT id_cliente
            FROM clientes
            WHERE doc_number = :doc
            LIMIT 1
        ");
        $sql->bindParam(":doc", $doc);
        $sql->execute();

        return $sql->rowCount() > 0;
    }

    protected static function existe_vehiculo_placa_modelo($placa)
    {
        $sql = mainModel::conectar()->prepare("
            SELECT id_vehiculo
            FROM vehiculos
            WHERE placa = :placa
            LIMIT 1
        ");
        $sql->bindParam(":placa", $placa);
        $sql->execute();

        return $sql->rowCount() > 0;
    }

    protected static function guardar_cliente_rapido_modelo($datos)
    {
        $pdo = mainModel::conectar();

        try {
            if (empty($datos['id_ciudad'])) {
                $ciudad = $pdo->query("
                    SELECT id_ciudad
                    FROM ciudades
                    WHERE estado = 1
                    ORDER BY id_ciudad ASC
                    LIMIT 1
                ")->fetchColumn();

                if (!$ciudad) {
                    return [
                        "error" => true,
                        "msg" => "No existe una ciudad activa para registrar el cliente rapido"
                    ];
                }

                $datos['id_ciudad'] = $ciudad;
            }

            $sql = $pdo->prepare("
                INSERT INTO clientes (
                    doc_number,
                    nombre_cliente,
                    apellido_cliente,
                    celular_cliente,
                    email_cliente,
                    direccion_cliente,
                    id_ciudad,
                    doc_type,
                    digito_v,
                    estado_civil,
                    estado_cliente
                ) VALUES (
                    :doc_number,
                    :nombre_cliente,
                    :apellido_cliente,
                    :celular_cliente,
                    :email_cliente,
                    :direccion_cliente,
                    :id_ciudad,
                    :doc_type,
                    :digito_v,
                    :estado_civil,
                    :estado_cliente
                )
            ");

            $sql->bindValue(":doc_number", $datos['doc_number']);
            $sql->bindValue(":nombre_cliente", $datos['nombre_cliente']);
            $sql->bindValue(":apellido_cliente", $datos['apellido_cliente']);
            $sql->bindValue(":celular_cliente", $datos['celular_cliente']);
            $sql->bindValue(":email_cliente", $datos['email_cliente']);
            $sql->bindValue(":direccion_cliente", $datos['direccion_cliente']);
            $sql->bindValue(":id_ciudad", (int) $datos['id_ciudad'], PDO::PARAM_INT);
            $sql->bindValue(":doc_type", $datos['doc_type']);
            $sql->bindValue(":digito_v", $datos['digito_v']);
            $sql->bindValue(":estado_civil", $datos['estado_civil']);
            $sql->bindValue(":estado_cliente", (int) $datos['estado_cliente'], PDO::PARAM_INT);

            if (!$sql->execute()) {
                $error = $sql->errorInfo();
                return [
                    "error" => true,
                    "msg" => $error[2] ?? 'Error SQL desconocido'
                ];
            }

            return [
                "success" => true,
                "id_cliente" => $pdo->lastInsertId()
            ];
        } catch (Exception $e) {
            return [
                "error" => true,
                "msg" => $e->getMessage()
            ];
        }
    }

    protected static function guardar_vehiculo_rapido_modelo($datos)
    {
        $pdo = mainModel::conectar();

        try {
            $sql = $pdo->prepare("
                INSERT INTO vehiculos
                (id_cliente,id_modeloauto,color,placa,anho,version,tipo_vehiculo,estado)
                VALUES
                (:cliente,:modelo,:color,:placa,:anho,:version,:tipo_vehiculo,:estado)
            ");

            $sql->bindValue(":cliente", (int) $datos['id_cliente'], PDO::PARAM_INT);
            $sql->bindValue(":modelo", (int) $datos['id_modeloauto'], PDO::PARAM_INT);
            $sql->bindValue(":color", $datos['color']);
            $sql->bindValue(":placa", $datos['placa']);
            $sql->bindValue(":anho", $datos['anho']);
            $sql->bindValue(":version", $datos['version']);
            $sql->bindValue(":tipo_vehiculo", $datos['tipo_vehiculo']);
            $sql->bindValue(":estado", (int) $datos['estado'], PDO::PARAM_INT);

            if (!$sql->execute()) {
                $error = $sql->errorInfo();
                return [
                    "error" => true,
                    "msg" => $error[2] ?? 'Error SQL desconocido'
                ];
            }

            $idVehiculo = $pdo->lastInsertId();
            $descripcion = self::descripcion_vehiculo_modelo($idVehiculo);

            return [
                "success" => true,
                "id_vehiculo" => $idVehiculo,
                "descripcion" => $descripcion
            ];
        } catch (Exception $e) {
            return [
                "error" => true,
                "msg" => $e->getMessage()
            ];
        }
    }

    protected static function descripcion_vehiculo_modelo($idVehiculo)
    {
        $sql = mainModel::conectar()->prepare("
            SELECT
                v.placa,
                ma.mar_descri AS marca,
                m.mod_descri AS modelo
            FROM vehiculos v
            INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
            INNER JOIN marcas ma ON ma.id_marcas = m.id_marcas
            WHERE v.id_vehiculo = :id
            LIMIT 1
        ");
        $sql->bindParam(":id", $idVehiculo, PDO::PARAM_INT);
        $sql->execute();
        $vehiculo = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$vehiculo) {
            return '';
        }

        return $vehiculo['marca'] . ' ' . $vehiculo['modelo'] . ' (' . $vehiculo['placa'] . ')';
    }

    protected static function buscar_cliente_autocomplete_modelo($busqueda)
    {
        $pdo = mainModel::conectar();
        $busqueda = trim($busqueda);
        $soloNumeros = preg_replace('/\D+/', '', $busqueda);

        if ($soloNumeros !== '' && strlen($soloNumeros) >= 4) {
            $sql = $pdo->prepare("
                SELECT
                    id_cliente,
                    CONCAT(nombre_cliente, ' ', apellido_cliente) AS cliente,
                    doc_number,
                    celular_cliente
                FROM clientes
                WHERE estado_cliente = 1
                  AND doc_number LIKE :doc
                ORDER BY doc_number ASC
                LIMIT 20
            ");
            $sql->bindValue(":doc", $soloNumeros . '%');
            $sql->execute();

            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }

        $partes = preg_split('/\s+/', $busqueda, -1, PREG_SPLIT_NO_EMPTY);
        $primero = $partes[0] ?? '';
        $segundo = $partes[1] ?? '';

        if ($segundo !== '') {
            $sql = $pdo->prepare("
                SELECT
                    id_cliente,
                    CONCAT(nombre_cliente, ' ', apellido_cliente) AS cliente,
                    doc_number,
                    celular_cliente
                FROM clientes
                WHERE estado_cliente = 1
                  AND (
                       (nombre_cliente LIKE :primero AND apellido_cliente LIKE :segundo)
                    OR (nombre_cliente LIKE :segundo AND apellido_cliente LIKE :primero)
                  )
                ORDER BY nombre_cliente ASC, apellido_cliente ASC
                LIMIT 20
            ");
            $sql->bindValue(":primero", $primero . '%');
            $sql->bindValue(":segundo", $segundo . '%');
            $sql->execute();

            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }

        $sql = $pdo->prepare("
            SELECT
                id_cliente,
                CONCAT(nombre_cliente, ' ', apellido_cliente) AS cliente,
                doc_number,
                celular_cliente
            FROM clientes
            WHERE estado_cliente = 1
              AND (
                   nombre_cliente LIKE :termino
                OR apellido_cliente LIKE :termino
              )
            ORDER BY nombre_cliente ASC, apellido_cliente ASC
            LIMIT 20
        ");
        $sql->bindValue(":termino", $primero . '%');
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function buscar_vehiculo_autocomplete_modelo($busqueda, $idCliente)
    {
        $busqueda = trim($busqueda);

        $sql = mainModel::conectar()->prepare("
            SELECT
                v.id_vehiculo,
                v.placa,
                v.anho,
                COALESCE(v.color, '-') AS color,
                ma.mar_descri AS marca,
                m.mod_descri AS modelo
            FROM vehiculos v
            INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
            INNER JOIN marcas ma ON ma.id_marcas = m.id_marcas
            WHERE v.id_cliente = :cliente
              AND v.estado = 1
              AND (
                   v.placa LIKE :termino
                OR m.mod_descri LIKE :termino
              )
            ORDER BY v.placa ASC
            LIMIT 20
        ");
        $sql->bindValue(":cliente", (int) $idCliente, PDO::PARAM_INT);
        $sql->bindValue(":termino", $busqueda . '%');
        $sql->execute();

        $vehiculos = $sql->fetchAll(PDO::FETCH_ASSOC);

        foreach ($vehiculos as &$vehiculo) {
            $vehiculo['descripcion'] = $vehiculo['marca'] . ' ' . $vehiculo['modelo'] . ' (' . $vehiculo['placa'] . ')';
        }

        return $vehiculos;
    }

    protected static function buscar_cliente_modelo($busqueda)
    {
        $busqueda = "%$busqueda%";

        $sql = mainModel::conectar()->prepare("
            SELECT
                id_cliente,
                CONCAT(nombre_cliente,' ',apellido_cliente) AS cliente,
                doc_number,
                celular_cliente
            FROM clientes
            WHERE estado_cliente = 1
              AND (
                   nombre_cliente LIKE :b
                OR apellido_cliente LIKE :b
                OR doc_number LIKE :b
              )
            ORDER BY nombre_cliente
            LIMIT 20
        ");

        $sql->bindParam(":b", $busqueda);
        $sql->execute();

        $clientes = $sql->fetchAll(PDO::FETCH_ASSOC);

        if (!$clientes) {
            return '<div class="alert alert-warning text-center">
                        No se encontraron clientes
                    </div>';
        }

        $tabla = '<table class="table table-bordered table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th>Celular</th>
                            <th>Acción</th>
                        </tr>
                    </thead><tbody>';

        foreach ($clientes as $c) {
            $tabla .= '
            <tr>
                <td>' . $c['cliente'] . '</td>
                <td>' . $c['doc_number'] . '</td>
                <td>' . $c['celular_cliente'] . '</td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm"
                        onclick="seleccionarCliente(
                            ' . $c['id_cliente'] . ',
                            \'' . addslashes($c['cliente']) . '\',
                            \'' . addslashes($c['doc_number']) . '\'
                        )">
                        Seleccionar
                    </button>
                </td>
            </tr>';
        }

        $tabla .= '</tbody></table>';

        return $tabla;
    }


    protected static function buscar_vehiculo_modelo($busqueda, $id_cliente)
    {
        $busqueda = "%$busqueda%";

        $sql = mainModel::conectar()->prepare("
            SELECT
                v.id_vehiculo,
                v.placa,
                v.anho,
                ma.mar_descri AS marca,
                m.mod_descri AS modelo,
                COALESCE(v.color, '-') AS color
            FROM vehiculos v
            INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
            INNER JOIN marcas ma ON ma.id_marcas= m.id_marcas
            WHERE v.id_cliente = :cliente
              AND v.estado = 1
              AND (
                   v.placa LIKE :b
                OR m.mod_descri LIKE :b
              )
            ORDER BY v.placa
        ");

        $sql->bindParam(":cliente", $id_cliente, PDO::PARAM_INT);
        $sql->bindParam(":b", $busqueda);
        $sql->execute();

        $vehiculos = $sql->fetchAll(PDO::FETCH_ASSOC);

        if (!$vehiculos) {
            return '<div class="alert alert-warning text-center">
                        No se encontraron vehículos
                    </div>';
        }

        $tabla = '<table class="table table-bordered table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Placa</th>
                            <th>Vehículo</th>
                            <th>Año</th>
                            <th>Acción</th>
                        </tr>
                    </thead><tbody>';

        foreach ($vehiculos as $v) {
            $desc = $v['marca'] . ' ' . $v['modelo'] . ' (' . $v['placa'] . ')';

            $tabla .= '
            <tr>
                <td>' . $v['placa'] . '</td>
                <td>' . $v['marca'] . ' ' . $v['modelo'] . ' - ' . $v['color'] . '</td>
                <td>' . $v['anho'] . '</td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm"
                        onclick="seleccionarVehiculo(
                            ' . $v['id_vehiculo'] . ',
                            \'' . addslashes($desc) . '\'
                        )">
                        Seleccionar
                    </button>
                </td>
            </tr>';
        }

        $tabla .= '</tbody></table>';

        return $tabla;
    }

    protected static function guardar_recepcion_modelo($d)
    {
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            $sql = $pdo->prepare("
            INSERT INTO recepcion_servicio(id_usuario,id_cliente,id_sucursal,id_vehiculo,fecha_ingreso, kilometraje,nivel_combustible,estado_exterior,objetos_vehiculo, tipo_servicio,area_problema,prioridad,accesorios,observacion,estado, origen,idreclamo_servicio)
            VALUES
            (:usuario,:cliente,:sucursal,:vehiculo,now(), :km,:combustible,:estado_exterior,:objetos, :tipo_servicio,:area_problema,:prioridad,:accesorios,:obs,:estado, :origen,:idreclamo)");
            $origen = $d['origen'] ?? 'NORMAL';
            $idreclamo = $d['idreclamo_servicio'] ?? null;

            if ($origen === 'RECLAMO' && !empty($idreclamo)) {

                $qGarantia = $pdo->prepare("
                SELECT
                    rc.requiere_garantia,
                    rs.kilometraje_salida
                FROM reclamo_servicio rc
                INNER JOIN registro_servicio rs
                    ON rs.idregistro_servicio = rc.idregistro_servicio
                WHERE rc.idreclamo_servicio = ?
                AND rc.id_sucursal = ?
                LIMIT 1
            ");

                $qGarantia->execute([
                    $idreclamo,
                    $d['id_sucursal']
                ]);

                $garantia = $qGarantia->fetch(PDO::FETCH_ASSOC);

                if (!$garantia) {
                    $pdo->rollBack();
                    return [
                        "error" => true,
                        "msg" => "No se pudo validar la garantía del reclamo"
                    ];
                }

                if ((int)$garantia['requiere_garantia'] === 1) {

                    $kmServicio = (int)$garantia['kilometraje_salida'];
                    $kmActual   = (int)$d['kilometraje'];
                    $kmLimite   = $kmServicio + 5000;

                    if ($kmServicio <= 0) {
                        $pdo->rollBack();
                        return [
                            "error" => true,
                            "msg" => "El servicio original no tiene kilometraje de salida registrado"
                        ];
                    }

                    if ($kmActual > $kmLimite) {
                        $pdo->rollBack();
                        return [
                            "error" => true,
                            "msg" => "La garantía está vencida por kilometraje. KM servicio: {$kmServicio}, límite: {$kmLimite}, KM actual: {$kmActual}"
                        ];
                    }
                }
            }

            $sql->bindParam(":usuario",  $d['id_usuario'],   PDO::PARAM_INT);
            $sql->bindParam(":cliente",  $d['id_cliente'],   PDO::PARAM_INT);
            $sql->bindParam(":sucursal", $d['id_sucursal'],  PDO::PARAM_INT);
            $sql->bindParam(":vehiculo", $d['id_vehiculo'],  PDO::PARAM_INT);
            $sql->bindParam(":km",       $d['kilometraje'],  PDO::PARAM_INT);
            $sql->bindParam(":obs",      $d['observacion']);
            $sql->bindParam(":estado",   $d['estado'],       PDO::PARAM_INT);
            $sql->bindParam(":combustible", $d['nivel_combustible']);
            $sql->bindParam(":estado_exterior", $d['estado_exterior']);
            $sql->bindParam(":objetos", $d['objetos_vehiculo']);

            $sql->bindParam(":tipo_servicio", $d['tipo_servicio']);
            $sql->bindParam(":area_problema", $d['area_problema']);
            $sql->bindParam(":prioridad", $d['prioridad']);

            $sql->bindParam(":origen", $origen);
            $sql->bindParam(":idreclamo", $idreclamo);
            $sql->bindParam(":accesorios", $d['accesorios']);

            if (!$sql->execute()) {
                $error = $sql->errorInfo();
                $pdo->rollBack();
                return [
                    "error" => true,
                    "msg"   => $error[2] ?? 'Error SQL desconocido'
                ];
            }

            /* obtener ID antes del commit */
            $id_recepcion = $pdo->lastInsertId();

            if ($origen === 'RECLAMO' && !empty($idreclamo)) {
                $updReclamo = $pdo->prepare("
                    UPDATE reclamo_servicio
                    SET estado = 2
                    WHERE idreclamo_servicio = :reclamo
                      AND id_sucursal = :sucursal
                      AND estado = 1
                ");
                $updReclamo->execute([
                    ':reclamo' => $idreclamo,
                    ':sucursal' => $d['id_sucursal']
                ]);

                if ($updReclamo->rowCount() < 1) {
                    $pdo->rollBack();
                    return [
                        "error" => true,
                        "msg" => "El reclamo no esta disponible para recepcion"
                    ];
                }
            }

            $pdo->commit();

            /* devolver ID */
            return [
                "success" => true,
                "id_recepcion" => $id_recepcion
            ];
        } catch (Exception $e) {

            $pdo->rollBack();

            return [
                "error" => true,
                "msg"   => $e->getMessage()
            ];
        }
    }


    protected static function listar_recepcion_modelo($inicio, $registros, $filtrosSQL, $orderSQL = "ORDER BY rs.fecha_ingreso DESC, rs.idrecepcion DESC")
    {
        $pdo = self::conectar();

        $selectSQL = "
        SELECT
            rs.idrecepcion,
            rs.fecha_ingreso,
            rs.kilometraje,
            rs.estado,
            rs.origen,
            rs.tipo_servicio,
            rs.prioridad,
            c.doc_number,
            CONCAT(c.nombre_cliente,' ',c.apellido_cliente) AS cliente,
            v.placa,
            v.anho,
            CONCAT(ma.mar_descri, ' ', m.mod_descri, ' ', v.placa, ' (', v.anho, ')') AS vehiculo,
            CONCAT(u.usu_nombre,' ',u.usu_apellido) AS usuario,
            (
                SELECT COUNT(*)
                FROM recepcion_fotos rf
                WHERE rf.id_recepcion = rs.idrecepcion
            ) AS total_fotos
        ";

        $baseSQL = "
        FROM recepcion_servicio rs
        INNER JOIN clientes c ON c.id_cliente = rs.id_cliente
        INNER JOIN vehiculos v ON v.id_vehiculo = rs.id_vehiculo
        INNER JOIN modelo_auto m ON m.id_modeloauto = v.id_modeloauto
        INNER JOIN marcas ma ON ma.id_marcas = m.id_marcas
        INNER JOIN usuarios u ON u.id_usuario = rs.id_usuario
        WHERE 1=1 $filtrosSQL
        ";

        return mainModel::ejecutarPaginador(
            $pdo,
            $baseSQL,
            $selectSQL,
            $orderSQL,
            $inicio,
            $registros
        );
    }

    protected static function fotos_recepcion_modelo($idRecepcion, $idSucursal)
    {
        $sql = mainModel::conectar()->prepare("
            SELECT rf.ruta_foto
            FROM recepcion_fotos rf
            INNER JOIN recepcion_servicio rs ON rs.idrecepcion = rf.id_recepcion
            WHERE rf.id_recepcion = :id
              AND rs.id_sucursal = :sucursal
            ORDER BY rf.id_foto ASC
        ");
        $sql->bindParam(":id", $idRecepcion, PDO::PARAM_INT);
        $sql->bindParam(":sucursal", $idSucursal, PDO::PARAM_INT);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    protected static function anular_recepcion_modelo($id, $sucursal)
    {
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            $q = $pdo->prepare("
                SELECT idreclamo_servicio
                FROM recepcion_servicio
                WHERE idrecepcion = :id
                  AND estado = 1
                  AND id_sucursal = :sucursal
                FOR UPDATE
            ");
            $q->execute([
                ":id" => $id,
                ":sucursal" => $sucursal
            ]);
            $recepcion = $q->fetch(PDO::FETCH_ASSOC);

            if (!$recepcion) {
                $pdo->rollBack();
                return false;
            }

            $sql = $pdo->prepare("
                UPDATE recepcion_servicio
                SET estado = 0,
                    fecha_actualizacion = NOW()
                WHERE idrecepcion = :id
                  AND estado = 1
                  AND id_sucursal = :sucursal
            ");
            $sql->execute([
                ":id" => $id,
                ":sucursal" => $sucursal
            ]);

            if (!empty($recepcion['idreclamo_servicio'])) {
                $updReclamo = $pdo->prepare("
                    UPDATE reclamo_servicio
                    SET estado = 1
                    WHERE idreclamo_servicio = :reclamo
                      AND id_sucursal = :sucursal
                      AND estado = 2
                ");
                $updReclamo->execute([
                    ':reclamo' => $recepcion['idreclamo_servicio'],
                    ':sucursal' => $sucursal
                ]);

                if ($updReclamo->rowCount() < 1) {
                    $pdo->rollBack();
                    return false;
                }
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}
