<?php
if ($peticionAjax) {
    require_once "../modelos/inventarioModelo.php";
} else {
    require_once "./modelos/inventarioModelo.php";
}

class inventarioControlador extends inventarioModelo
{
    /* ===============================
       CATEGORÍAS
    =============================== */
    public function cargar_categorias_controlador()
    {
        $categorias = inventarioModelo::cargarCategoriasModelo();

        $data = [];
        foreach ($categorias as $row) {
            $data[] = [
                'id' => $row['id_categoria'],
                'nombre' => $row['cat_descri']
            ];
        }

        return json_encode($data);
    }
    /* ===============================
       PROVEEDORES
    =============================== */
    public function cargar_proveedores_controlador()
    {
        $proveedores = inventarioModelo::cargarProveedoresModelo();

        $data = [];
        foreach ($proveedores as $row) {
            $data[] = [
                'id' => $row['idproveedores'],
                'nombre' => $row['razon_social']
            ];
        }

        return json_encode($data);
    }
    /* ===============================
       artículos
    =============================== */
    public function cargarArticulosControlador($buscar = '', $tipoArticulo = 'producto')
    {
        $articulos = inventarioModelo::cargarArticulosModelo($buscar, $tipoArticulo);

        $data = [];
        foreach ($articulos as $row) {
            $tipo = ucfirst($row['tipo'] ?? 'articulo');
            $data[] = [
                "id" => $row['id_articulo'],
                "text" => $row['codigo'] . ' - ' . $row['desc_articulo'] . ' [' . $tipo . '] (' . number_format($row['precio_venta'], 2) . ')'
            ];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit();
    }
    /* ===============================
       Guardar inventario
    =============================== */
    public function guardarInventarioControlador()
    {
        if (!isset($_POST['tipo_inventario'])) {
            return ['status' => 'error', 'msg' => 'Tipo de inventario no definido'];
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $usuario_id  = $_SESSION['id_str'] ?? 0;
        $sucursal_id = $_SESSION['nick_sucursal'] ?? 0;

        if ($usuario_id <= 0 || $sucursal_id <= 0) {
            return ['status' => 'error', 'msg' => 'Usuario o sucursal no válidos'];
        }

        if(!mainModel::tienePermiso('inventario.crear')) {
            return ['status' => 'error', 'msg' => 'Acceso no autorizado'];
        }

        $data = [
            'tipo'               => $_POST['tipo_inventario'],
            'observacion'        => $_POST['observacion'] ?? '',
            'usuario_id'         => $usuario_id,
            'sucursal_id'        => $sucursal_id,
            'fecha'              => date('Y-m-d H:i:s'),
            'subtipo_categoria'  => $_POST['subtipo_categoria'] ?? 0,
            'subtipo_proveedor'  => $_POST['subtipo_proveedor'] ?? 0,
            'subtipo_producto'   => $_POST['subtipo_producto'] ?? [],
            'tipo_articulo'      => $_POST['tipo_articulo'] ?? 'producto'
        ];

        try {
            inventarioModelo::guardarInventario($data);

            return [
                "Alerta" => "recargar",
                "Titulo" => "Inventario generado",
                "Texto"  => "El inventario se guardó correctamente.",
                "Tipo"   => "success"
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'msg' => 'Error al generar inventario: ' . $e->getMessage()
            ];
        }
    }
    /* ===============================
        Buscar inventario  
    =============================== */
    public function buscar_inv_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }
        $inventario  = mainModel::limpiar_string($_POST['buscar_inv']);

        if ($inventario == "") {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún pedido en el sistema que coincida.”</strong>
                                </p>
                            </div>';
        }
        /**seleccionar proveedor */
        $datosINV = mainModel::ejecutar_consulta_simple("SELECT  idajuste_inventario, id_usuario, sucursal_id,estado, fecha, tipo_inv, descripcion, fecha_ajuste
        FROM ajuste_inventario
        where (idajuste_inventario like '%$inventario%' or descripcion like '%$inventario%' or tipo_inv like '%$inventario%') and estado in ('1','2') and sucursal_id = '" . $_SESSION['nick_sucursal'] . "'
        order by idajuste_inventario desc
        LIMIT 15");

        if ($datosINV->rowCount() >= 1) {
            $datosINV = $datosINV->fetchAll();
            $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>
                        <tr class="text-center">
                            <th>Número de Código</th>
                            <th>Tipo de Inventario</th>
                            <th>Observación</th>
                            <th></th>
                        </tr>';
            foreach ($datosINV as $rows) {
                $tabla .= '
                        <tr class="text-center">
                            <td>' . $rows['idajuste_inventario'] . '</td>
                            <td>' . $rows['tipo_inv'] . '</td>
                            <td>' . $rows['descripcion'] . '</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="agregar_inv(' . $rows['idajuste_inventario'] . ')"><i class="fas fa-user-plus"></i></button>
                            </td>
                        </tr>';
            }
            $tabla .= '</tbody></table></div>';
            return $tabla;
        } else {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún pedido en el sistema que coincida con <strong>“' . $inventario . '”</strong>
                                </p>
                            </div>';
        }
    }
    /* ===============================
        Cargar INV en sesión   
    =============================== */
    public function cargar_inv_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $idajuste = mainModel::limpiar_string($_POST['id_inv_seleccionado'] ?? '');

        if (empty($idajuste)) {
            $_SESSION['alerta_inv'] = [
                "tipo" => "error",
                "mensaje" => "No se recibió ID del ajuste de inventario"
            ];
            header("Location: " . SERVERURL . "inventario/");
            exit();
        }

        $_SESSION['id_inv_seleccionado'] = $idajuste;
        $idSucursal = $_SESSION['nick_sucursal'];
        /* ==================================================
       1️⃣ CABECERA DEL AJUSTE
        ================================================== */
        $sqlCabecera = mainModel::ejecutar_consulta_simple("
        SELECT ai.idajuste_inventario, ai.sucursal_id, ai.tipo_inv, ai.descripcion, ai.fecha_ajuste,
               CONCAT(u.usu_nombre, ' ', u.usu_apellido) AS usuario, ai.estado
        FROM ajuste_inventario ai
        INNER JOIN usuarios u ON u.id_usuario = ai.id_usuario
        WHERE ai.idajuste_inventario = '$idajuste' and ai.sucursal_id = '$idSucursal'");

        $cabecera = $sqlCabecera->fetch();

        if ($cabecera) {
            $_SESSION['datos_ajuste_inv'] = [
                "ID"          => $cabecera['idajuste_inventario'],
                "TIPO"        => $cabecera['tipo_inv'],
                "DESCRIPCION" => $cabecera['descripcion'],
                "FECHA"       => $cabecera['fecha_ajuste'],
                "USUARIO"     => $cabecera['usuario'],
                "ESTADO"     => $cabecera['estado']
            ];
        }

        /* ==================================================
       2️⃣ DETALLE DEL AJUSTE
        ================================================== */
        $conexion = mainModel::conectar();

        $sqlDetalle = $conexion->prepare("
            SELECT 
                aid.id_articulo,
                a.codigo,
                a.desc_articulo,
                aid.cantidad_teorica,
                aid.cantidad_fisica,
                aid.costo
            FROM ajuste_inventario_detalle aid
            INNER JOIN ajuste_inventario ai
                ON ai.idajuste_inventario = aid.idajuste_inventario
            INNER JOIN articulos a 
                ON a.id_articulo = aid.id_articulo
            WHERE aid.idajuste_inventario = :idajuste
            AND ai.sucursal_id = :sucursal_id");

        $sqlDetalle->bindParam(":idajuste", $idajuste, PDO::PARAM_INT);
        $sqlDetalle->bindParam(":sucursal_id", $idSucursal, PDO::PARAM_INT);

        $sqlDetalle->execute();
        $detalle = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);


        $_SESSION['Cdatos_articuloINV'] = [];

        foreach ($detalle as $i => $row) {

            // Si el usuario ya modificó cantidades, no pisarlas
            $cant_teorica = $_SESSION['Cdatos_articuloINV'][$i]['cantidad_teorica'] ?? $row['cantidad_teorica'];
            $cant_fisica  = $_SESSION['Cdatos_articuloINV'][$i]['cantidad_fisica']  ?? $row['cantidad_fisica'];
            $costo        = $_SESSION['Cdatos_articuloINV'][$i]['costo']             ?? $row['costo'];

            $_SESSION['Cdatos_articuloINV'][$i] = [
                "ID"               => $row['id_articulo'],
                "codigo"           => $row['codigo'],
                "descripcion"      => $row['desc_articulo'],
                "cantidad_teorica" => $cant_teorica,
                "cantidad_fisica"  => $cant_fisica,
                "costo"            => $costo,
                "diferencia"       => ($cant_fisica - $cant_teorica)
            ];
        }

        /* ==================================================
       3️⃣ REDIRECCIÓN
        ================================================== */
        header("Location: " . SERVERURL . "inventario/");
        exit();
    }
    /* ===============================
        Guardar ajuste de inventario   
    =============================== */
    public function guardar_ajuste_inv_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('inventario.editar')) {
            return ["status" => "error", "msg" => "Acceso no autorizado"];
        }

        if (!isset($_SESSION['id_inv_seleccionado'])) {
            return ["status" => "error", "msg" => "No hay ajuste seleccionado"];
        }

        if (empty($_SESSION['Cdatos_articuloINV'])) {
            return ["status" => "error", "msg" => "No hay artículos para guardar"];
        }

        if (empty($_SESSION['nick_sucursal'])) {
            return ["status" => "error", "msg" => "Sucursal no definida"];
        }
        

        $idajuste   = (int) $_SESSION['id_inv_seleccionado'];
        $idSucursal = (int) $_SESSION['nick_sucursal'];

        foreach ($_SESSION['Cdatos_articuloINV'] as $item) {
            $descripcion = $item['descripcion'] ?? 'Artículo sin descripción';
            $cantidadFisica = (float)($item['cantidad_fisica'] ?? 0);
            $cantidadTeorica = (float)($item['cantidad_teorica'] ?? 0);
            $costo = (float)($item['costo'] ?? 0);

            if ($cantidadFisica < 0) {
                return [
                    "status" => "error",
                    "msg" => "La cantidad inventariada no puede ser negativa: " . $descripcion
                ];
            }

            if ($costo < 0) {
                return [
                    "status" => "error",
                    "msg" => "El costo no puede ser negativo: " . $descripcion
                ];
            }
        }

        $conexion = mainModel::conectar();
        $conexion->beginTransaction();

        try {

            // 🔐 Validar que el ajuste pertenezca a la sucursal
            $check = $conexion->prepare("
            SELECT 1
            FROM ajuste_inventario
            WHERE idajuste_inventario = :idajuste
              AND sucursal_id = :sucursal_id
        ");
            $check->execute([
                ":idajuste"     => $idajuste,
                ":sucursal_id"  => $idSucursal
            ]);

            if ($check->rowCount() === 0) {
                throw new Exception("El ajuste no pertenece a la sucursal activa");
            }

            // 🔁 Actualizar detalles
            $sqlUpd = $conexion->prepare("
            UPDATE ajuste_inventario_detalle d
            INNER JOIN ajuste_inventario a
                ON a.idajuste_inventario = d.idajuste_inventario
            SET d.cantidad_fisica = :cantidad_fisica,
                d.diferencia      = :diferencia,
                d.costo           = :costo
            WHERE d.idajuste_inventario = :idajuste
              AND d.id_articulo = :id_articulo
              AND a.sucursal_id = :sucursal_id
        ");

            foreach ($_SESSION['Cdatos_articuloINV'] as $i => $item) {
                $cantidadFisica = (float) $item['cantidad_fisica'];
                $cantidadTeorica = (float) $item['cantidad_teorica'];
                $diferencia = $cantidadFisica - $cantidadTeorica;
                $_SESSION['Cdatos_articuloINV'][$i]['diferencia'] = $diferencia;

                $sqlUpd->execute([
                    ":cantidad_fisica" => $cantidadFisica,
                    ":diferencia"      => $diferencia,
                    ":costo"           => (float) $item['costo'],
                    ":idajuste"        => $idajuste,
                    ":id_articulo"     => (int) $item['ID'],
                    ":sucursal_id"     => $idSucursal
                ]);
                if ($sqlUpd->rowCount() === 0) {
                    // MySQL puede devolver 0 cuando los valores enviados son iguales.
                }
            }

            // 🔄 Cambiar estado del ajuste a 2
            $updEstado = $conexion->prepare("
                UPDATE ajuste_inventario
                SET estado = 2
                WHERE idajuste_inventario = :idajuste
                AND sucursal_id = :sucursal_id
            ");

            $updEstado->execute([
                ":idajuste"    => $idajuste,
                ":sucursal_id" => $idSucursal
            ]);

            if ($updEstado->rowCount() === 0) {
                $checkEstadoActual = $conexion->prepare("
                    SELECT 1
                    FROM ajuste_inventario
                    WHERE idajuste_inventario = :idajuste
                      AND sucursal_id = :sucursal_id
                    LIMIT 1
                ");
                $checkEstadoActual->execute([
                    ":idajuste"    => $idajuste,
                    ":sucursal_id" => $idSucursal
                ]);

                if (!$checkEstadoActual->fetchColumn()) {
                    throw new Exception("No se pudo actualizar el estado del ajuste");
                }
            }
            if (isset($_SESSION['datos_ajuste_inv'])) {
                $_SESSION['datos_ajuste_inv']['ESTADO'] = 2;
            }
            $conexion->commit();
            return ["status" => "ok"];
        } catch (Exception $e) {

            $conexion->rollBack();
            return [
                "status" => "error",
                "msg"    => $e->getMessage()
            ];
        }
    }

    /* ===============================
        Aplicar ajuste de stock
    =============================== */
    public function aplicar_ajuste_stock_controlador($idsucursal)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        return $this->aplicar_ajuste_stock_seguro_controlador($idsucursal);

        if (
            empty($_SESSION['id_inv_seleccionado']) ||
            empty($_SESSION['Cdatos_articuloINV'])
        ) {
            return ["status" => "error", "msg" => "No hay ajuste seleccionado para aplicar"];
        }

        $idajuste = (int) $_SESSION['id_inv_seleccionado'];
        $usuario  = $_SESSION['id_str'];
        $fecha    = date("Y-m-d H:i:s");

        $checkEstado = mainModel::ejecutar_consulta_simple("
            SELECT estado
            FROM ajuste_inventario
            WHERE idajuste_inventario = '$idajuste'
            AND sucursal_id = '$idsucursal'
            LIMIT 1
        ")->fetch();

        if (!$checkEstado) {
            return [
                "status" => "error",
                "msg" => "El ajuste no existe o no pertenece a la sucursal"
            ];
        }

        if ((int)$checkEstado['estado'] !== 2) {
            return [
                "status" => "error",
                "msg" => "El ajuste no está en estado válido para aplicar"
            ];
        }
        $ajustesAplicados = 0;

        foreach ($_SESSION['Cdatos_articuloINV'] as $item) {

            $cantidad = (float) $item['diferencia'];

            // 🔴 CLAVE: si no hay diferencia, no se hace nada
            if ($cantidad == 0) {
                continue;
            }

            $id_articulo     = (int) $item['ID'];
            $costo           = (float) $item['costo'];
            $cantidad_fisica = (float) $item['cantidad_fisica'];

            $signo = $cantidad > 0 ? 1 : -1;
            $conexionMovimiento = mainModel::conectar();
            mainModel::registrar_movimiento_stock_modelo($conexionMovimiento, [
                "id_sucursal" => $idsucursal,
                "tipo" => "AJUSTE_INV",
                "id_articulo" => $id_articulo,
                "cantidad" => abs($cantidad),
                "precio_venta" => 0,
                "costo" => $costo,
                "fecha" => $fecha,
                "usuario" => $usuario,
                "signo" => $signo,
                "referencia" => 'AJUSTE #' . $idajuste
            ]);

            $ajustesAplicados++;
        }

        // 🔴 Si no hubo diferencias reales, no aplicar el ajuste
        if ($ajustesAplicados === 0) {
            return [
                "status" => "warning",
                "msg" => "No se aplicó ningún ajuste porque no había diferencias"
            ];
        }

        /* ================= CERRAR AJUSTE ================= */
        mainModel::ejecutar_consulta_simple("
        UPDATE ajuste_inventario
        SET estado = 3,
            ajustadoPor = '$usuario',
            fecha_ajuste = NOW()
        WHERE idajuste_inventario = '$idajuste'
        AND sucursal_id = '$idsucursal'
        ");

        /* ================= LIMPIAR SESIÓN ================= */
        unset(
            $_SESSION['Cdatos_articuloINV'],
            $_SESSION['id_inv_seleccionado'],
            $_SESSION['datos_ajuste_inv'],
            $_SESSION['alerta_inv']
        );

        return [
            "status" => "ok",
            "msg" => "Ajuste aplicado correctamente ($ajustesAplicados productos ajustados)"
        ];
    }

    /* ===============================
        Ver detalle de inventario
    =============================== */
    public function detalle_inv_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('inventario.ver')) {
            return json_encode([
                'status' => 'error',
                'msg' => 'Acceso no autorizado'
            ]);
        }

        $id = (int) mainModel::limpiar_string(
            mainModel::decryption($_POST['detalle_inventario'] ?? '')
        );
        $idSucursal = (int) ($_SESSION['nick_sucursal'] ?? 0);
        $pagina = (int) ($_POST['detalle_pagina'] ?? 1);
        $registros = (int) ($_POST['detalle_registros'] ?? 100);
        $buscar = mainModel::limpiar_string($_POST['detalle_buscar'] ?? '');
        $filtroDiferencia = mainModel::limpiar_string($_POST['detalle_filtro'] ?? 'todos');

        $pagina = $pagina > 0 ? $pagina : 1;
        $registros = ($registros > 0 && $registros <= 200) ? $registros : 100;
        $inicio = ($pagina - 1) * $registros;

        if (!in_array($filtroDiferencia, ['todos', 'diferencias', 'sobrantes', 'faltantes'], true)) {
            $filtroDiferencia = 'todos';
        }

        if ($id <= 0 || $idSucursal <= 0) {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se pudo identificar el inventario solicitado.'
            ]);
        }

        $cabecera = inventarioModelo::obtenerCabeceraInventarioModelo($id, $idSucursal);

        if (!$cabecera) {
            return json_encode([
                'status' => 'error',
                'msg' => 'No se encontro el inventario en la sucursal activa.'
            ]);
        }

        $resultado = inventarioModelo::listarDetalleInventarioModelo($id, $inicio, $registros, $buscar, $filtroDiferencia);
        $detalle = $resultado['datos'];
        $total = $resultado['total'];
        $totalPaginas = max(1, (int) ceil($total / $registros));

        $estadoTexto = [
            0 => 'Anulado',
            1 => 'Pendiente',
            2 => 'Modificado',
            3 => 'Ajustado'
        ];

        $estado = $estadoTexto[(int)$cabecera['estado']] ?? 'Desconocido';
        $fecha = !empty($cabecera['fecha']) ? date('d-m-Y', strtotime($cabecera['fecha'])) : '-';
        $fechaAjuste = !empty($cabecera['fecha_ajuste']) ? date('d-m-Y', strtotime($cabecera['fecha_ajuste'])) : '-';
        $creadoPor = trim(($cabecera['usu_nombre'] ?? '') . ' ' . ($cabecera['usu_apellido'] ?? ''));
        $ajustadoPor = trim(($cabecera['ajustado_nombre'] ?? '') . ' ' . ($cabecera['ajustado_apellido'] ?? ''));
        $ajustadoPor = $ajustadoPor !== '' ? $ajustadoPor : '-';

        $cabeceraHtml = '
            <div class="row mb-3">
                <div class="col-md-4"><strong>Inventario:</strong> ' . htmlspecialchars($cabecera['idajuste_inventario'], ENT_QUOTES, 'UTF-8') . '</div>
                <div class="col-md-4"><strong>Tipo:</strong> ' . htmlspecialchars($cabecera['tipo_inv'], ENT_QUOTES, 'UTF-8') . '</div>
                <div class="col-md-4"><strong>Estado:</strong> ' . htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') . '</div>
                <div class="col-md-4"><strong>Sucursal:</strong> ' . htmlspecialchars($cabecera['suc_descri'], ENT_QUOTES, 'UTF-8') . '</div>
                <div class="col-md-4"><strong>Fecha creacion:</strong> ' . $fecha . '</div>
                <div class="col-md-4"><strong>Fecha ajuste:</strong> ' . $fechaAjuste . '</div>
                <div class="col-md-4"><strong>Creado por:</strong> ' . htmlspecialchars($creadoPor, ENT_QUOTES, 'UTF-8') . '</div>
                <div class="col-md-4"><strong>Ajustado por:</strong> ' . htmlspecialchars($ajustadoPor, ENT_QUOTES, 'UTF-8') . '</div>
                <div class="col-md-12"><strong>Observacion:</strong> ' . htmlspecialchars($cabecera['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') . '</div>
            </div>';

        if (empty($detalle)) {
            return json_encode([
                'status' => 'ok',
                'cabecera' => $cabeceraHtml,
                'tabla' => '<div class="alert alert-info mb-0">No hay articulos para los filtros seleccionados.</div>',
                'paginacion' => '',
                'total' => $total,
                'pagina' => $pagina,
                'total_paginas' => $totalPaginas
            ]);
        }

        $tabla = '
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr class="text-center">
                            <th>Codigo</th>
                            <th>Articulo</th>
                            <th>Tipo</th>
                            <th>Teorica</th>
                            <th>Fisica</th>
                            <th>Diferencia</th>
                            <th>Costo</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($detalle as $row) {
            $diferencia = (float) $row['diferencia'];
            $claseDif = $diferencia > 0 ? 'text-success' : ($diferencia < 0 ? 'text-danger' : 'text-muted');

            $tabla .= '
                <tr>
                    <td>' . htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . htmlspecialchars($row['desc_articulo'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td class="text-center">' . htmlspecialchars(ucfirst($row['tipo']), ENT_QUOTES, 'UTF-8') . '</td>
                    <td class="text-right">' . number_format((float)$row['cantidad_teorica'], 2, ',', '.') . '</td>
                    <td class="text-right">' . number_format((float)$row['cantidad_fisica'], 2, ',', '.') . '</td>
                    <td class="text-right ' . $claseDif . '"><strong>' . number_format($diferencia, 2, ',', '.') . '</strong></td>
                    <td class="text-right">' . number_format((float)$row['costo'], 0, ',', '.') . '</td>
                </tr>';
        }

        $tabla .= '
                    </tbody>
                </table>
            </div>';

        $desde = $total > 0 ? $inicio + 1 : 0;
        $hasta = min($inicio + $registros, $total);
        $prevDisabled = $pagina <= 1 ? 'disabled' : '';
        $nextDisabled = $pagina >= $totalPaginas ? 'disabled' : '';

        $paginacion = '
            <div class="d-flex justify-content-between align-items-center mt-2">
                <span>Mostrando ' . $desde . ' al ' . $hasta . ' de ' . $total . '</span>
                <div>
                    <button type="button" class="btn btn-sm btn-secondary" ' . $prevDisabled . ' onclick="cargarDetalleInventario(' . ($pagina - 1) . ')">Anterior</button>
                    <span class="mx-2">Pagina ' . $pagina . ' de ' . $totalPaginas . '</span>
                    <button type="button" class="btn btn-sm btn-secondary" ' . $nextDisabled . ' onclick="cargarDetalleInventario(' . ($pagina + 1) . ')">Siguiente</button>
                </div>
            </div>';

        return json_encode([
            'status' => 'ok',
            'cabecera' => $cabeceraHtml,
            'tabla' => $tabla,
            'paginacion' => $paginacion,
            'total' => $total,
            'pagina' => $pagina,
            'total_paginas' => $totalPaginas
        ]);
    }

    public function aplicar_ajuste_stock_seguro_controlador($idsucursal)
    {
        if (empty($_SESSION['id_inv_seleccionado'])) {
            return ["status" => "error", "msg" => "No hay ajuste seleccionado para aplicar"];
        }

        $idajuste = (int) $_SESSION['id_inv_seleccionado'];
        $idsucursal = (int) $idsucursal;
        $usuario = (int) ($_SESSION['id_str'] ?? 0);
        $fecha = date("Y-m-d H:i:s");

        if ($idajuste <= 0 || $idsucursal <= 0 || $usuario <= 0) {
            return ["status" => "error", "msg" => "Datos de sesión inválidos para aplicar el ajuste"];
        }

        $conexion = mainModel::conectar();

        try {
            $conexion->beginTransaction();

            $checkEstado = $conexion->prepare("
                SELECT estado
                FROM ajuste_inventario
                WHERE idajuste_inventario = :idajuste
                  AND sucursal_id = :sucursal
                LIMIT 1
                FOR UPDATE
            ");
            $checkEstado->execute([
                ':idajuste' => $idajuste,
                ':sucursal' => $idsucursal
            ]);
            $cabecera = $checkEstado->fetch(PDO::FETCH_ASSOC);

            if (!$cabecera) {
                throw new Exception("El ajuste no existe o no pertenece a la sucursal");
            }

            if ((int)$cabecera['estado'] !== 2) {
                throw new Exception("El ajuste no está en estado válido para aplicar");
            }

            $stmtDetalle = $conexion->prepare("
                SELECT
                    id_articulo,
                    cantidad_teorica,
                    cantidad_fisica,
                    COALESCE(diferencia, cantidad_fisica - cantidad_teorica) AS diferencia,
                    costo
                FROM ajuste_inventario_detalle
                WHERE idajuste_inventario = :idajuste
            ");
            $stmtDetalle->execute([':idajuste' => $idajuste]);
            $detalle = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

            if (empty($detalle)) {
                throw new Exception("No hay artículos en el detalle del ajuste");
            }

            $ajustesAplicados = 0;

            foreach ($detalle as $item) {
                $id_articulo = (int) $item['id_articulo'];
                $cantidad_teorica = (float) $item['cantidad_teorica'];
                $cantidad_fisica = (float) $item['cantidad_fisica'];
                $cantidad = (float) $item['diferencia'];
                $costo = (float) $item['costo'];

                if ($id_articulo <= 0 || $cantidad_teorica < 0 || $cantidad_fisica < 0 || $costo < 0) {
                    throw new Exception("El ajuste contiene artículos, cantidades o costos inválidos");
                }

                if ($cantidad == 0) {
                    continue;
                }

                mainModel::registrar_movimiento_stock_modelo($conexion, [
                    "id_sucursal" => $idsucursal,
                    "tipo" => "AJUSTE_INV",
                    "id_articulo" => $id_articulo,
                    "cantidad" => abs($cantidad),
                    "precio_venta" => 0,
                    "costo" => $costo,
                    "fecha" => $fecha,
                    "usuario" => $usuario,
                    "signo" => $cantidad > 0 ? 1 : -1,
                    "referencia" => 'AJUSTE #' . $idajuste
                ]);

                $ajustesAplicados++;
            }

            $updAjuste = $conexion->prepare("
                UPDATE ajuste_inventario
                SET estado = 3,
                    ajustadoPor = :usuario,
                    fecha_ajuste = NOW()
                WHERE idajuste_inventario = :idajuste
                  AND sucursal_id = :sucursal
            ");
            $updAjuste->execute([
                ':usuario' => $usuario,
                ':idajuste' => $idajuste,
                ':sucursal' => $idsucursal
            ]);

            $conexion->commit();

            unset(
                $_SESSION['Cdatos_articuloINV'],
                $_SESSION['id_inv_seleccionado'],
                $_SESSION['datos_ajuste_inv'],
                $_SESSION['alerta_inv']
            );

            return [
                "status" => "ok",
                "msg" => "Ajuste aplicado correctamente ($ajustesAplicados artículos con movimiento)"
            ];
        } catch (Exception $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }

            return [
                "status" => "error",
                "msg" => $e->getMessage()
            ];
        }
    }


    /* ===============================
        PAGINADOR INVENTARIO
    =============================== */
    public function paginador_inv_controlador($pagina, $registros, $url, $busqueda1 = '', $busqueda2 = '', $nro_inventario = '', $tipo_inv = '', $estado_inv = '', $observacion = '', $usuario = '')
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $busqueda1 = mainModel::limpiar_string($busqueda1);
        $busqueda2 = mainModel::limpiar_string($busqueda2);
        $nro_inventario = mainModel::limpiar_string($nro_inventario);
        $tipo_inv = mainModel::limpiar_string($tipo_inv);
        $estado_inv = mainModel::limpiar_string($estado_inv);
        $observacion = mainModel::limpiar_string($observacion);
        $usuario = mainModel::limpiar_string($usuario);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $registros = ((int)$registros > 0) ? (int)$registros : 15;
        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        $filtros = [
            [
                "campo" => "ai.sucursal_id",
                "tipo"  => "=",
                "valor" => $_SESSION['nick_sucursal']
            ],
            [
                "campo" => "ai.fecha",
                "tipo"  => "DATE_RANGE",
                "desde" => $busqueda1,
                "hasta" => $busqueda2
            ]
        ];

        if ($nro_inventario !== '') {
            $filtros[] = ["campo" => "ai.idajuste_inventario", "tipo" => "=", "valor" => $nro_inventario];
        }

        if ($tipo_inv !== '') {
            $filtros[] = ["campo" => "ai.tipo_inv", "tipo" => "=", "valor" => $tipo_inv];
        }

        if ($estado_inv !== '') {
            $filtros[] = ["campo" => "ai.estado", "tipo" => "=", "valor" => $estado_inv];
        }

        if ($observacion !== '') {
            $filtros[] = ["campo" => "ai.descripcion", "tipo" => "LIKE", "valor" => $observacion];
        }

        if ($usuario !== '') {
            $filtros[] = ["campo" => "CONCAT(u.usu_nombre, ' ', u.usu_apellido)", "tipo" => "LIKE", "valor" => $usuario];
        }

        $resultado = inventarioModelo::listarInventariosModelo($inicio, $registros, mainModel::construirFiltros($filtros));
        $datos = $resultado['datos'];
        $total = $resultado['total'];
        $Npaginas = ceil($total / $registros);
        $puedeAnular = mainModel::tienePermiso('inventario.anular');
        $colspan = $puedeAnular ? 11 : 10;

        $tabla .= '<div class="table-responsive">
					<table class="table table-dark table-sm">
						<thead>
							<tr class="text-center roboto-medium">
								<th>#</th>
                                <th>Número de Inventario</th>
                                <th>Tipo de Inventario</th>
                                <th>Fecha Creación</th>
                                <th>Observación</th>
                                <th>Creado Por</th>
                                <th>Fecha Ajuste</th>
                                <th>Ajustado Por</th>
                                <th>Estado</th>
                                <th>DETALLE</th>';
        if ($puedeAnular) {
            $tabla .=           '<th>ANULAR</th>';
        }
        $tabla .= '
						</tr>
						</thead>
						<tbody>';
        if ($total >= 1 && $pagina <= $Npaginas) {
            $contador = $reg_inicio;
            foreach ($datos as $rows) {
                switch ($rows['estadoInv']) {
                    case 1:
                        $estadoBadge = '<span class="badge bg-primary">Pendiente</span>';
                        break;
                    case 2:
                        $estadoBadge = '<span class="badge bg-success">Modificado</span>';
                        break;
                    case 3:
                        $estadoBadge = '<span class="badge bg-danger">Ajustado</span>';
                        break;
                    case 0:
                        $estadoBadge = '<span class="badge bg-secondary">Anulado</span>';
                        break;
                    default:
                        $estadoBadge = '<span class="badge bg-secondary">Desconocido</span>';
                }
                $fechaAjuste = (!empty($rows['fecha_ajuste']) && $rows['fecha_ajuste'] !== '0000-00-00 00:00:00') ? date("d-m-Y", strtotime($rows['fecha_ajuste'])) : '-';
                $ajustadoPor = trim(($rows['ajustado_nombre'] ?? '') . ' ' . ($rows['ajustado_apellido'] ?? ''));
                $ajustadoPor = $ajustadoPor !== '' ? $ajustadoPor : '-';

                $tabla .= '
                            <tr class="text-center">
								<td>' . $contador . '</td>
								<td>' . htmlspecialchars($rows['idajuste_inventario'], ENT_QUOTES, 'UTF-8') . '</td>
								<td>' . htmlspecialchars($rows['tipo_inv'], ENT_QUOTES, 'UTF-8') . '</td>
								<td>' . date("d-m-Y", strtotime($rows['fecha'])) . '</td>
								<td>' . htmlspecialchars($rows['descripcion'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($rows['usu_nombre'] . ' ' . $rows['usu_apellido'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . $fechaAjuste . '</td>
                                <td>' . htmlspecialchars($ajustadoPor, ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . $estadoBadge . '</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" onclick="verDetalleInventario(\'' . mainModel::encryption($rows['idajuste_inventario']) . '\')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>';
                if ($puedeAnular) {
                    $tabla .= '<td>
									<form class="FormularioAjax" action="' . SERVERURL . 'ajax/inventarioAjax.php" method="POST" data-form="delete" data-anulacion="true" data-anulacion-titulo="Anular ajuste de inventario" autocomplete="off" action="">
                                    <input type="hidden" name="inv_id_del" value=' . mainModel::encryption($rows['idajuste_inventario']) . '>
										<button type="submit" class="btn btn-warning">
											<i class="far fa-trash-alt"></i>
										</button>
									</form>
								</td>';
                }

                $tabla .= '</tr>';
                $contador++;
            }
            $reg_final = $contador - 1;
        } else {
            if ($total >= 1) {
                $tabla .= '<tr class="text-center"> <td colspan="' . $colspan . '"> <a href="' . $url . '" class="btn btn-reaised btn-primary btn-sm"> Haga click aqui para recargar el listado </a> </td> </tr> ';
            } else {
                $tabla .= '<tr class="text-center"> <td colspan="' . $colspan . '"> No hay registros en el sistema</td> </tr> ';
            }
        }

        $tabla .= '       </tbody>
					</table>
				</div>';
        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '<p class="text-right"> Mostrando registro ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '</p>';
            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }
        echo $tabla;
    }
    /* ===============================
           ANULAR INVENTARIO  
    =============================== */

    public function anular_inv_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $id = (int) mainModel::limpiar_string(
            mainModel::decryption($_POST['inv_id_del'])
        );
        $motivo = mainModel::limpiar_string($_POST['motivo_anulacion'] ?? '');

        $db = mainModel::conectar();
        $db->beginTransaction();

        try {

            /* ===== CABECERA CON BLOQUEO ===== */
            $ajuste = $db->query("
            SELECT estado, sucursal_id
            FROM ajuste_inventario
            WHERE idajuste_inventario = $id
            FOR UPDATE
        ")->fetch(PDO::FETCH_ASSOC);

            if (!$ajuste) {
                throw new Exception("Ajuste no encontrado");
            }

            // 🔴 ya anulado
            if ((int)$ajuste['estado'] === 0) {
                throw new Exception("El ajuste ya fue anulado");
            }

            /* ===== ESTADO 1/2: TODAVIA NO IMPACTO STOCK ===== */
            if (in_array((int)$ajuste['estado'], [1, 2], true)) {

                $db->exec("
                UPDATE ajuste_inventario
                SET estado = 0
                WHERE idajuste_inventario = $id
                AND sucursal_id = {$ajuste['sucursal_id']}
            ");

                mainModel::registrar_anulacion_auditoria_modelo($db, [
                    'modulo' => 'ajuste_inventario',
                    'tabla_afectada' => 'ajuste_inventario',
                    'id_registro' => $id,
                    'id_sucursal' => $ajuste['sucursal_id'],
                    'estado_anterior' => $ajuste['estado'],
                    'estado_nuevo' => '0',
                    'motivo' => $motivo,
                    'usuario_anula' => $_SESSION['id_str'],
                    'referencia' => 'AJUSTE_INVENTARIO #' . $id
                ]);

                $db->commit();

                return [
                    "Alerta" => "recargar",
                    "Titulo" => "Ajuste anulado",
                    "Texto"  => "El ajuste fue anulado correctamente",
                    "Tipo"   => "success"
                ];
            }

            /* ===== ESTADO 3: AJUSTADO, REVERTIR STOCK ===== */
            if ((int)$ajuste['estado'] === 3) {

                $detalle = $db->query("
                SELECT id_articulo, diferencia, costo
                FROM ajuste_inventario_detalle
                WHERE idajuste_inventario = $id
                  AND diferencia <> 0
            ")->fetchAll(PDO::FETCH_ASSOC);

                if (empty($detalle)) {
                    throw new Exception("No hay movimientos para revertir");
                }

                foreach ($detalle as $d) {

                    $signo = ($d['diferencia'] > 0) ? -1 : 1;
                    mainModel::registrar_movimiento_stock_modelo($db, [
                        "id_sucursal" => $ajuste['sucursal_id'],
                        "tipo" => "ANULACION_AJUSTE_INV",
                        "id_articulo" => $d['id_articulo'],
                        "cantidad" => abs($d['diferencia']),
                        "precio_venta" => 0,
                        "costo" => $d['costo'],
                        "usuario" => $_SESSION['id_str'],
                        "signo" => $signo,
                        "referencia" => 'Anulación ajuste inventario #' . $id
                    ]);
                }

                /* ===== MARCAR COMO ANULADO ===== */
                $db->exec("
                UPDATE ajuste_inventario
                SET estado = 0
                WHERE idajuste_inventario = $id
                AND sucursal_id = {$ajuste['sucursal_id']}
            ");
            }

            mainModel::registrar_anulacion_auditoria_modelo($db, [
                'modulo' => 'ajuste_inventario',
                'tabla_afectada' => 'ajuste_inventario',
                'id_registro' => $id,
                'id_sucursal' => $ajuste['sucursal_id'],
                'estado_anterior' => $ajuste['estado'],
                'estado_nuevo' => '0',
                'motivo' => $motivo,
                'usuario_anula' => $_SESSION['id_str'],
                'referencia' => 'AJUSTE_INVENTARIO #' . $id
            ]);

            $db->commit();

            return [
                "Alerta" => "recargar",
                "Titulo" => "Ajuste anulado",
                "Texto"  => "El ajuste fue anulado y el stock fue revertido correctamente",
                "Tipo"   => "success"
            ];
        } catch (Exception $e) {
            $db->rollBack();

            return [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => $e->getMessage(),
                "Tipo"   => "error"
            ];
        }
    }
}
