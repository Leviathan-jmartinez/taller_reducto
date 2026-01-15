<?php
if ($peticionAjax) {
    require_once "../modelos/transferenciaModelo.php";
} else {
    require_once "./modelos/transferenciaModelo.php";
}

class transferenciaControlador extends transferenciaModelo
{
    public function crear_transferencia_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // ===== Validaciones mínimas =====
        if (empty($_POST['sucursal_destino'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Debe seleccionar una sucursal destino",
                "Tipo"   => "error"
            ]);
        }

        if (empty($_POST['productos'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Debe agregar al menos un producto",
                "Tipo"   => "error"
            ]);
        }

        if (empty($_POST['nombre_transpo'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Debe indicar el nombre del chofer",
                "Tipo"   => "error"
            ]);
        }

        // ===== Armar datos =====
        $datos = [
            'id_sucursal_origen'  => $_SESSION['nick_sucursal'],
            'id_sucursal_destino' => $_POST['sucursal_destino'],
            'id_usuario'          => $_SESSION['id_str'],
            'observacion'         => $_POST['observacion'] ?? '',
            'productos'           => $_POST['productos'],

            // obligatorios
            'chofer'       => $_POST['nombre_transpo'],
            'fechaenvio'   => $_POST['fechaenvio']   ?? date('Y-m-d'),
            'fechallegada' => $_POST['fechallegada'] ?? date('Y-m-d'),

            // opcionales
            'ci_transpo'    => $_POST['ci_transpo']    ?? null,
            'cel_transpo'   => $_POST['cel_transpo']   ?? null,
            'transportista' => $_POST['transportista'] ?? null,
            'ruc_transport' => $_POST['ruc_transport'] ?? null,
            'vehimarca'     => $_POST['vehimarca']     ?? null,
            'vehimodelo'    => $_POST['vehimodelo']    ?? null,
            'vehichapa'     => $_POST['vehichapa']     ?? null,
        ];

        $resultado = transferenciaModelo::crear_transferencia($datos);

        if (is_array($resultado) && $resultado['ok'] === true) {
            return json_encode([
                "Alerta" => "limpiar",
                "Titulo" => "Transferencia generada",
                "Texto"  => "La salida fue registrada correctamente",
                "Tipo"   => "success",
                "idnota_remision" => $resultado['idnota_remision']
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto"  => $resultado,
            "Tipo"   => "error"
        ]);
    }

    public function buscar_producto_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        $q = trim($_POST['termino'] ?? '');

        if ($q === '') {
            echo json_encode([]);
            return;
        }

        $idSucursal = $_SESSION['nick_sucursal'];

        $resultado = transferenciaModelo::buscar_producto_modelo($q, $idSucursal);

        echo json_encode($resultado);
    }

    public function buscar_sucursal_destino_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        $q = trim($_POST['termino'] ?? '');

        if ($q === '') {
            echo json_encode([]);
            return;
        }

        $idSucursalOrigen = $_SESSION['nick_sucursal'];

        $resultado = transferenciaModelo::buscar_sucursal_destino_modelo(
            $q,
            $idSucursalOrigen
        );

        echo json_encode($resultado);
    }

    public function listar_transferencias_controlador()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $ruta = explode("/", trim($_GET['vista'], "/"));

        // valores por defecto
        $filtroEstado = '-';
        $filtroFecha  = '-';
        $filtroId     = '-';
        $pagina       = 1;

        // detectar filtros
        if (isset($ruta[1]) && $ruta[1] === 'filtro') {
            $filtroEstado = $ruta[2] ?? '-';
            $filtroFecha  = $ruta[3] ?? '-';
            $filtroId     = $ruta[4] ?? '-';
        }

        // detectar página
        $posPagina = array_search('pagina', $ruta);
        if ($posPagina !== false && isset($ruta[$posPagina + 1])) {
            $pagina = (int)$ruta[$posPagina + 1];
            if ($pagina < 1) $pagina = 1;
        }

        $porPagina = 10;
        $offset = ($pagina - 1) * $porPagina;


        $pdo = mainModel::conectar();
        $sucursal = $_SESSION['nick_sucursal'];

        $where = [];
        $params = [];

        // 🔒 Siempre limitar a mi sucursal
        $where[] = "(t.sucursal_origen = :suc OR t.sucursal_destino = :suc)";
        $params[':suc'] = $sucursal;

        // 🔎 Filtro por estado (opcional)
        // 🔎 FILTRO POR ESTADO
        if ($filtroEstado !== '-') {
            switch ($filtroEstado) {
                case 'recibido':
                    $where[] = "t.estado = 'recibido'";
                    break;

                case 'recibido_parcial':
                    $where[] = "t.estado = 'recibido_parcial'";
                    break;

                case 'en_transito':
                    $where[] = "t.estado = 'en_transito' AND t.idtransferencia_origen IS NULL";
                    break;

                case 'devolucion':
                    $where[] = "t.estado = 'en_transito' AND t.idtransferencia_origen IS NOT NULL";
                    break;
            }
        }

        // 🔎 FILTRO POR FECHA
        if ($filtroFecha !== '-') {
            $where[] = "DATE(t.fecha) = :fecha";
            $params[':fecha'] = $filtroFecha;
        }

        // 🔎 FILTRO POR ID
        if ($filtroId !== '-') {
            $where[] = "t.idtransferencia = :id";
            $params[':id'] = $filtroId;
        }



        // 🔎 Filtro por sucursal específica (opcional)
        if (!empty($_GET['sucursal'])) {
            $where[] = "(t.sucursal_origen = :filtro_suc OR t.sucursal_destino = :filtro_suc)";
            $params[':filtro_suc'] = $_GET['sucursal'];
        }

        $sql = "
            SELECT SQL_CALC_FOUND_ROWS
                t.idtransferencia,
                t.idtransferencia_origen,
                t.fecha,
                t.estado,
                t.sucursal_origen,
                so.suc_descri AS suc_origen,
                t.sucursal_destino,
                sd.suc_descri AS suc_destino,
                nr.idnota_remision,
                nr.nro_remision
            FROM transferencia_stock t
            INNER JOIN sucursales so ON so.id_sucursal = t.sucursal_origen
            INNER JOIN sucursales sd ON sd.id_sucursal = t.sucursal_destino
            LEFT JOIN nota_remision nr ON nr.idtransferencia = t.idtransferencia
            WHERE " . implode(" AND ", $where) . "
            ORDER BY t.fecha DESC
            LIMIT :offset, :limite";


        $stmt = $pdo->prepare($sql);

        // binds normales
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        // binds del limit
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);

        $stmt->execute();
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
        $totalPaginas = ceil($total / $porPagina);

        return [
            'datos'          => $registros,
            'total'          => $total,
            'paginas'        => $totalPaginas,
            'pagina_actual'  => $pagina,
            'filtroEstado'   => $filtroEstado,
            'filtroFecha'    => $filtroFecha,
            'filtroId'       => $filtroId
        ];
    }


    public function cargar_recibir_vista_controlador($idEnc)
    {
        $mainModel = new mainModel();

        $id = $mainModel->decryption($idEnc);

        if (!$id) {
            return null;
        }

        $data = transferenciaModelo::obtener_transferencia_para_recibir_modelo($id);
        if (!$data) {
            return null;
        }

        if ($data['cabecera']['estado'] !== 'en_transito') {
            return null;
        }

        if ($data['cabecera']['sucursal_destino'] != $_SESSION['nick_sucursal']) {
            return null;
        }

        return $data;
    }

    public function recibir_transferencia_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = $_POST['idtransferencia'] ?? null;
        $recibidos = $_POST['recibidos'] ?? [];

        if (!$id || empty($recibidos)) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Datos incompletos",
                "Tipo"   => "error"
            ]);
        }

        $res = transferenciaModelo::recibir_transferencia_modelo(
            $id,
            $_SESSION['id_str'],
            $recibidos
        );

        if (is_array($res) && $res['ok'] === true) {

            if (!empty($res['parcial'])) {
                return json_encode([
                    "Alerta" => "redireccionar_confirmado",
                    "Titulo" => "Recepción parcial",
                    "Texto"  => "La transferencia fue recibida parcialmente. Se generó una nueva transferencia con los faltantes.",
                    "Tipo"   => "warning",
                    "URL"    => SERVERURL . "transferencia-historial/"
                ]);
            }

            return json_encode([
                "Alerta" => "redireccionar_confirmado",
                "Titulo" => "Recepción completada",
                "Texto"  => "La transferencia fue recibida correctamente.",
                "Tipo"   => "success",
                "URL"    => SERVERURL . "transferencia-historial/"
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto"  => $res['mensaje'] ?? "La transferencia ya fue recibida",
            "Tipo"   => "error"
        ]);
    }
}
