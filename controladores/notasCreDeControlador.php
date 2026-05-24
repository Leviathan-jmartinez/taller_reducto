<?php
if ($peticionAjax) {
    require_once "../modelos/notasCreDeModelo.php";
} else {
    require_once "./modelos/notasCreDeModelo.php";
}

class notasCreDeControlador extends notasCreDeModelo
{
    /* ================= BUSCAR FACTURAS ================= */

    public static function buscarFacturas($texto)
    {
        $texto = mainModel::limpiar_string($texto);

        $facturas = notasCreDeModelo::buscarFacturas($texto);

        if (empty($facturas)) {
            return '<div class="alert alert-warning mb-0">No se encontraron facturas</div>';
        }

        $html = '<table class="table table-bordered table-sm mb-0">';
        $html .= '
        <thead class="thead-light">
            <tr>
                <th>N° Factura</th>
                <th>Fecha</th>
                <th class="text-right">Total</th>
                <th class="text-center">Acción</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($facturas as $f) {
            $html .= '
            <tr>
                <td>' . htmlspecialchars($f['nro_factura']) . '</td>
                <td>' . date("d/m/Y", strtotime($f['fecha_factura'])) . '</td>
                <td class="text-right">' . number_format($f['total_compra'], 0, ',', '.') . '</td>
                <td class="text-center">
                    <button 
                        type="button"
                        class="btn btn-success btn-sm"
                        onclick="seleccionarFactura(' . (int)$f['idcompra_cabecera'] . ')">
                        Agregar
                    </button>
                </td>
            </tr>
        ';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    /* ================= SELECCIONAR FACTURA ================= */
    public static function seleccionarFactura($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        $factura = notasCreDeModelo::obtenerFactura($id);
        if (!$factura) {
            return false;
        }

        $_SESSION['NC_FACTURA'] = [
            'idcompra_cabecera' => $factura['idcompra_cabecera'],
            'id_sucursal'       => $factura['id_sucursal'],
            'nro_factura'       => $factura['nro_factura'],
            'fecha_factura'     => $factura['fecha_factura'],
            'total'             => $factura['total_compra'],
            'idproveedor'       => (int)$factura['idproveedores'],
            'ruc'               => $factura['ruc'],
            'proveedor'         => $factura['razon_social']
        ];


        $detalleBD = notasCreDeModelo::obtenerDetalleCompra($id);
        $_SESSION['NC_DETALLE'] = [];

        foreach ($detalleBD as $d) {

            $subtotal = round($d['cantidad_recibida'] * $d['precio_unitario'], 2);

            $exenta = 0;
            $iva5   = 0;
            $iva10  = 0;

            if ((int)$d['divisor'] === 11) {
                // IVA 10%
                $iva10 = round($subtotal / 11, 2);
            } elseif ((int)$d['divisor'] === 21) {
                // IVA 5%
                $iva5 = round($subtotal / 21, 2);
            } else {
                // Exenta
                $exenta = $subtotal;
            }

            $_SESSION['NC_DETALLE'][] = [
                'id_articulo' => $d['id_articulo'],
                'descripcion' => $d['desc_articulo'],
                'cantidad'    => $d['cantidad_recibida'],
                'cantidad_original' => $d['cantidad_recibida'],
                'precio'      => $d['precio_unitario'],
                'precio_original' => $d['precio_unitario'],
                'iva_tipo'    => $d['tipo_impuesto_descri'],
                'divisor'     => (int)$d['divisor'],

                // 🔴 SOLO IVA / EXENTA
                'exenta' => $exenta,
                'iva_5'  => $iva5,
                'iva_10' => $iva10
            ];
        }

        return true;
    }

    public static function actualizarItemNC($data)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        $i        = (int)$data['index'];
        $cantidad = (float)$data['cantidad'];
        $precio   = (float)$data['precio'];
        $tipoNota = strtolower(mainModel::limpiar_string($data['tipo'] ?? ''));

        if (!isset($_SESSION['NC_DETALLE'][$i])) {
            return ['status' => 'error', 'msg' => 'Ítem no encontrado'];
        }

        $item = &$_SESSION['NC_DETALLE'][$i];

        if ($cantidad < 0 || $precio < 0) {
            return ['status' => 'error', 'msg' => 'Cantidad y precio no pueden ser negativos'];
        }

        if (isset($item['cantidad_original']) && $cantidad > (float)$item['cantidad_original']) {
            return [
                'status' => 'error',
                'msg' => 'La cantidad no puede superar la cantidad comprada: ' . number_format((float)$item['cantidad_original'], 0, ',', '.')
            ];
        }

        if ($tipoNota === 'credito' && isset($item['precio_original']) && $precio > (float)$item['precio_original']) {
            return [
                'status' => 'error',
                'msg' => 'El precio no puede superar el precio facturado: ' . number_format((float)$item['precio_original'], 0, ',', '.')
            ];
        }

        /* ================= SUBTOTAL ÍTEM ================= */
        $monto = round($cantidad * $precio, 2);

        $item['cantidad'] = $cantidad;
        $item['precio']   = $precio;

        /* ================= IVA ÍTEM ================= */
        $iva5  = 0;
        $iva10 = 0;
        $exenta = 0;

        if ((int)$item['divisor'] === 11) {
            $iva10 = round($monto / 11, 2);
        } elseif ((int)$item['divisor'] === 21) {
            $iva5 = round($monto / 21, 2);
        } else {
            $exenta = $monto;
        }

        $item['exenta'] = $exenta;
        $item['iva_5']  = $iva5;
        $item['iva_10'] = $iva10;

        /* ================= TOTALES ================= */
        $subtotal = 0;
        $total_iva5 = 0;
        $total_iva10 = 0;

        foreach ($_SESSION['NC_DETALLE'] as $d) {
            $sub = round($d['cantidad'] * $d['precio'], 2);
            $subtotal += $sub;
            $total_iva5 += $d['iva_5'];
            $total_iva10 += $d['iva_10'];
        }

        return [
            'status' => 'ok',

            'fila' => [
                'exenta' => number_format($item['exenta'], 0, ',', '.'),
                'iva_5'  => number_format($item['iva_5'], 0, ',', '.'),
                'iva_10' => number_format($item['iva_10'], 0, ',', '.')
            ],

            'totales' => [
                'subtotal' => number_format($subtotal, 0, ',', '.'),
                'iva_5'    => number_format($total_iva5, 0, ',', '.'),
                'iva_10'   => number_format($total_iva10, 0, ',', '.'),
                'total'    => number_format($subtotal, 0, ',', '.')
            ]
        ];
    }

    public static function guardarNotaCompraControlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('compra.nota.crear')) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Acceso no autorizado",
                "Texto"  => "No tiene permiso para realizar esta acción",
                "Tipo"   => "error"
            ];
        }

        $factura = $_SESSION['NC_FACTURA'] ?? null;

        if (!isset($_SESSION['NC_FACTURA']) || empty($_SESSION['NC_FACTURA']['idcompra_cabecera'])) {
            return ['status' => 'error', 'msg' => 'Debe seleccionar una factura válida'];
        }

        if (empty($_SESSION['NC_DETALLE'])) {
            return ['status' => 'error', 'msg' => 'No hay detalle'];
        }

        $tipoNota = strtolower(mainModel::limpiar_string($_POST['tipo'] ?? ''));
        if (!in_array($tipoNota, ['credito', 'debito'], true)) {
            return ['status' => 'error', 'msg' => 'Debe seleccionar un tipo de nota valido'];
        }

        $movStock = strtoupper(mainModel::limpiar_string($_POST['movimiento_stock'] ?? 'NINGUNO'));
        if ($tipoNota === 'debito') {
            $movStock = 'NINGUNO';
        }

        if (!in_array($movStock, ['NINGUNO', 'DEVOLUCION'], true)) {
            return ['status' => 'error', 'msg' => 'Movimiento de stock invalido'];
        }

        $detalle = array_filter($_SESSION['NC_DETALLE'], function ($d) {
            return $d['cantidad'] > 0 && $d['precio'] > 0;
        });

        if (count($detalle) === 0) {
            return ['status' => 'error', 'msg' => 'Todos los ítems están en cero'];
        }

        $total = 0;
        foreach ($detalle as $d) {
            if ((float)$d['cantidad'] > (float)($d['cantidad_original'] ?? $d['cantidad'])) {
                return [
                    'status' => 'error',
                    'msg' => 'La cantidad de ' . $d['descripcion'] . ' supera la cantidad comprada'
                ];
            }

            if ($tipoNota === 'credito' && (float)$d['precio'] > (float)($d['precio_original'] ?? $d['precio'])) {
                return [
                    'status' => 'error',
                    'msg' => 'El precio de ' . $d['descripcion'] . ' supera el precio facturado'
                ];
            }

            $total += round($d['cantidad'] * $d['precio'], 2);
        }

        /* ================= VALIDAR DUPLICADO NC/ND ================= */
        $timbrado = trim($_POST['timbrado'] ?? '');
        $nroNota  = trim($_POST['nro_nota']);

        $dup = mainModel::conectar()->prepare("
            SELECT COUNT(*) 
            FROM nota_compra
            WHERE tipo = :tipo
            AND nro_documento = :nro
            AND timbrado = :timbrado
            AND idproveedor = :proveedor
            AND id_sucursal = :suc
            AND estado = 1
        ");
        $dup->execute([
            ':tipo'     => $tipoNota,
            ':nro'      => $nroNota,
            ':timbrado' => $timbrado,
            ':proveedor' => (int)$factura['idproveedor'],
            ':suc'      => $_SESSION['nick_sucursal']
        ]);

        if ((int)$dup->fetchColumn() > 0) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Documento duplicado",
                "Texto"  => "Ya existe una nota con el mismo tipo, timbrado y número en esta sucursal.",
                "Tipo"   => "error"
            ];
        }


        if ($tipoNota === 'credito') {
            $totalFactura = (float)$factura['total'];
            $totalNCPrevias = notasCreDeModelo::totalNCActivasPorFactura($factura['idcompra_cabecera']);

            if (($totalNCPrevias + $total) > $totalFactura) {
                return [
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto"  => "La Nota de Crédito supera el monto de la factura. Disponible: "
                        . number_format($totalFactura - $totalNCPrevias, 0, ',', '.'),
                    "Tipo"   => "error"
                ];
            }
        }

        $montoMovimiento = $total;
        if ($tipoNota === 'credito') {
            $montoMovimiento *= -1;
        }

        if ($tipoNota === 'credito' && $movStock === 'DEVOLUCION') {
            foreach ($detalle as $d) {
                $stockDisponible = notasCreDeModelo::obtenerStockDisponibleModelo(
                    $_SESSION['nick_sucursal'],
                    $d['id_articulo']
                );

                if ($stockDisponible < (float)$d['cantidad']) {
                    return [
                        'status' => 'error',
                        'msg' => 'Stock insuficiente para devolver ' . $d['descripcion'] . '. Disponible: ' . number_format($stockDisponible, 0, ',', '.')
                    ];
                }
            }
        }

        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            $idNota = notasCreDeModelo::insertarNotaCompraModelo($pdo, [
                'idproveedor' => (int)$factura['idproveedor'],
                'id_sucursal' => $_SESSION['nick_sucursal'],
                'tipo'        => $tipoNota,
                'movimiento_stock' => $movStock,
                'nro'         => $_POST['nro_nota'],
                'fecha'       => $_POST['fecha'],
                'idcompra'    => $factura['idcompra_cabecera'],
                'total'       => $total,
                'descripcion' => $_POST['descripcion'] ?? '',
                'usuario'     => $_SESSION['id_str'],
                'timbrado'    => $_POST['timbrado'] ?? null
            ]);

            notasCreDeModelo::insertarDetalleNotaCompraModelo($pdo, $idNota, $detalle);
            $signo = ($tipoNota === 'credito') ? -1 : 1;

            // Totales ya calculados desde $_SESSION['NC_DETALLE']
            $exenta   = 0;
            $grav5    = 0;
            $iva5     = 0;
            $grav10   = 0;
            $iva10    = 0;
            $totalLC = 0;

            foreach ($detalle as $d) {
                $monto = round($d['cantidad'] * $d['precio'], 2);

                if ($d['divisor'] == 11) {           // IVA 10
                    $iva10   += round($monto / 11, 2);
                    $grav10  += $monto - round($monto / 11, 2);
                } elseif ($d['divisor'] == 21) {     // IVA 5
                    $iva5   += round($monto / 21, 2);
                    $grav5  += $monto - round($monto / 21, 2);
                } else {                             // Exenta
                    $exenta += $monto;
                }

                $totalLC += $monto;
            }

            // Aplicar signo según NC o ND
            $exenta  *= $signo;
            $grav5   *= $signo;
            $iva5    *= $signo;
            $grav10  *= $signo;
            $iva10   *= $signo;
            $totalLC *= $signo;

            $pdo->prepare("
                INSERT INTO libro_compra
                (idcompra_cabecera, id_sucursal, fecha, tipo_comprobante,
                serie, nro_comprobante, idproveedores,
                proveedor_nombre, proveedor_ruc,
                exenta, gravada_5, iva_5, gravada_10, iva_10, total, estado, fecha_registro)
                VALUES
                (:idcompra, :suc, :fecha, :tipo,
                :serie, :nro, :prov,
                :prov_nom, :prov_ruc,
                :exenta, :g5, :iva5, :g10, :iva10, :total, 1, NOW())
            ")->execute([
                ':idcompra' => $factura['idcompra_cabecera'],
                ':suc'      => $_SESSION['nick_sucursal'],
                ':fecha'    => $_POST['fecha'],
                ':tipo'     => ($tipoNota === 'credito') ? 'NC' : 'ND',
                ':serie'    => substr($_POST['nro_nota'], 0, 7),
                ':nro'      => $_POST['nro_nota'],
                ':prov'     => (int)$factura['idproveedor'],
                ':prov_nom' => $factura['proveedor'],
                ':prov_ruc' => $factura['ruc'] ?? '',
                ':exenta'   => $exenta,
                ':g5'       => $grav5,
                ':iva5'     => $iva5,
                ':g10'      => $grav10,
                ':iva10'    => $iva10,
                ':total'    => $totalLC
            ]);

            if ($tipoNota === 'credito' && $movStock === 'DEVOLUCION') {
                foreach ($detalle as $d) {

                    $stockUpdate = $pdo->prepare("
                    UPDATE stock
                    SET stockDisponible = stockDisponible - :cant,
                        stockUltActualizacion = NOW(),
                        stockUsuActualizacion = :usu
                    WHERE id_sucursal = :suc AND id_articulo = :art
                      AND stockDisponible >= :cant_stock
                ");

                    $stockUpdate->execute([
                        ':cant' => $d['cantidad'],
                        ':cant_stock' => $d['cantidad'],
                        ':usu'  => $_SESSION['id_str'],
                        ':suc'  => $_SESSION['nick_sucursal'],
                        ':art'  => $d['id_articulo']
                    ]);

                    if ($stockUpdate->rowCount() < 1) {
                        throw new Exception("Stock insuficiente para devolver " . $d['descripcion']);
                    }

                    $pdo->prepare("
                    INSERT INTO movimientostock
                    (id_sucursal, TipoMovStockId, MovStockArticuloId,
                     MovStockCantidad, MovStockPrecioVenta, MovStockCosto,
                     MovStockFechaHora, MovStockUsuario,
                     MovStockSigno, MovStockReferencia)
                    VALUES
                    (:suc, 'NC_COMPRA_DEV', :art,
                     :cant, 0, :costo,
                     NOW(), :usu,
                     -1, :ref)
                ")->execute([
                        ':suc'   => $_SESSION['nick_sucursal'],
                        ':art'   => $d['id_articulo'],
                        ':cant'  => $d['cantidad'],
                        ':costo' => $d['precio'],
                        ':usu'   => $_SESSION['id_str'],
                        ':ref'   => 'NC ' . $_POST['nro_nota']
                    ]);
                }
            }

            notasCreDeModelo::impactarNotaCompraModelo($pdo, [
                'idcompra'   => $factura['idcompra_cabecera'],
                'id_sucursal' => $_SESSION['nick_sucursal'],
                'tipo'       => $tipoNota,
                'idnota'     => $idNota,
                'monto'      => $montoMovimiento,
                'obs'        => 'Nota ' . $tipoNota . ' ' . $_POST['nro_nota']
            ]);

            $pdo->commit();

            unset($_SESSION['NC_DETALLE'], $_SESSION['NC_FACTURA']);

            echo json_encode([
                'Alerta' => 'recargar',
                'Titulo' => 'Correcto',
                'Texto'  => 'Nota guardada correctamente',
                'Tipo'   => 'success'
            ]);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
            exit;
        }
    }

    /** Controlador paginar compras */
    public function paginador_notasCreDe_controlador($pagina, $registros, $url, $busqueda1, $busqueda2, $nro_documento = '', $tipo_nota = '', $orden = 'fecha', $direccion = 'DESC')
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $busqueda1 = mainModel::limpiar_string($busqueda1);
        $busqueda2 = mainModel::limpiar_string($busqueda2);
        $nro_documento = mainModel::limpiar_string($nro_documento);
        $tipo_nota     = strtolower(mainModel::limpiar_string($tipo_nota));
        $orden = mainModel::limpiar_string($orden);
        $direccion = strtoupper(mainModel::limpiar_string($direccion));


        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $registros = ((int)$registros > 0) ? (int)$registros : 15;
        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        $columnasOrdenSql = [
            'fecha' => 'nc.fecha_creacion',
            'estado' => 'nc.estado'
        ];
        $ordenamiento = mainModel::preparar_ordenamiento($orden, $direccion, $columnasOrdenSql, 'fecha', 'DESC');
        $orden = $ordenamiento['orden'];
        $direccion = $ordenamiento['direccion'];

        $resultado = notasCreDeModelo::paginarNotasCompraModelo($inicio, $registros, [
            'id_sucursal'   => $_SESSION['nick_sucursal'],
            'fecha_inicio'  => $busqueda1,
            'fecha_final'   => $busqueda2,
            'nro_documento' => $nro_documento,
            'tipo_nota'     => $tipo_nota
        ], "ORDER BY " . $ordenamiento['sql'] . ", nc.idnota_compra DESC");

        $datos = $resultado['datos'];
        $total = $resultado['total'];

        $Npaginas = ceil($total / $registros);

        $tabla .= '<div class="table-responsive">
                        <table class="table table-dark table-sm">
                            <thead>
                                <tr class="text-center roboto-medium">
                                    <th>#</th>
                                    <th>PROVEEDOR</th>
                                    <th>NUMERO DE DOCUMENTO</th>
                                    <th>' . mainModel::link_orden_tabla($url, 'fecha', 'FECHA', $orden, $direccion, 'nota_orden', 'nota_direccion') . '</th>
                                    <th>TOTAL DOCUMENTO</th>
                                    <th>FACTURA ASOCIADA</th>
                                    <th>TIPO DOCUMENTO</th>
                                    <th>CARGADO POR</th>
                                    <th>' . mainModel::link_orden_tabla($url, 'estado', 'ESTADO', $orden, $direccion, 'nota_orden', 'nota_direccion') . '</th>';
        $puedeAnular = mainModel::tienePermiso('compra.nota.anular');

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
                switch ($rows['estado_nota']) {
                    case 1:
                        $estadoBadge = '<span class="badge bg-primary">Activo</span>';
                        break;
                    case 2:
                        $estadoBadge = '<span class="badge bg-success">Procesado</span>';
                        break;
                    case 0:
                        $estadoBadge = '<span class="badge bg-danger">Anulado</span>';
                        break;
                    default:
                        $estadoBadge = '<span class="badge bg-secondary">Desconocido</span>';
                }
                $tabla .= '
                                <tr class="text-center">
                                    <td>' . $contador . '</td>
                                    <td>' . $rows['razon_social'] . '</td>
                                    <td>' . $rows['nro_documento'] . '</td>
                                    <td>' . date("d-m-Y", strtotime($rows['fecha_nota'])) . '</td>
                                    <td>' . number_format($rows['total_nota'], 0, ',', '.') . '</td>
                                    <td>' . $rows['nro_factura'] . '</td>
                                    <td>' . $rows['tipo_nota'] . '</td>
                                    <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                                    <td>' . $estadoBadge . '</td>';
                if ($puedeAnular) {
                    $tabla .= '<td>
                                        <form class="FormularioAjax" action="' . SERVERURL . 'ajax/notasCreDeAjax.php" method="POST" data-form="delete" autocomplete="off" action="">
                                        <input type="hidden" name="notaCreDe_id_del" value=' . mainModel::encryption($rows['idnota_compra']) . '>
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
            $colspan = $puedeAnular ? 10 : 9;
            if ($total >= 1) {
                $tabla .= '<tr class="text-center"> <td colspan="' . $colspan . '"> <a href="' . $url . '" class="btn btn-reaised btn-primary btn-sm"> Haga click aqui para recargar el listado </a> </td> </tr> ';
            } else {
                $tabla .= '<tr class="text-center"> <td colspan="' . $colspan . '"> No hay regitros en el sistema</td> </tr> ';
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
    /**fin controlador */

    public static function anularNotaCompraControlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }
        if (!mainModel::tienePermiso('compra.nota.anular')) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Acceso no autorizado",
                "Texto"  => "No tiene permiso para realizar esta acción",
                "Tipo"   => "error"
            ];
        }

        if (!isset($_POST['notaCreDe_id_del'])) {
            return ['status' => 'error', 'msg' => 'ID no recibido'];
        }

        $idNota = mainModel::decryption($_POST['notaCreDe_id_del']);
        $nota = notasCreDeModelo::obtenerNotaCompraPorId($idNota);

        if (!$nota) {
            return ['status' => 'error', 'msg' => 'Nota no encontrada'];
        }

        if ((int)$nota['estado'] === 0) {
            return ['status' => 'error', 'msg' => 'La nota ya está anulada'];
        }

        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            /* 1️⃣ Anular nota */
            notasCreDeModelo::anularNotaCompraModelo($pdo, $idNota);

            /* 2️⃣ Movimiento inverso en cuentas a pagar */
            if ($nota['tipo'] === 'credito') {
                $montoInverso = abs($nota['total']);
            } else {
                $montoInverso = -abs($nota['total']);
            }

            notasCreDeModelo::impactarAnulacionNotaModelo($pdo, [
                'idcompra'   => $nota['idcompra_cabecera'],
                'id_sucursal' => $nota['id_sucursal'],
                'idnota'     => $idNota,
                'monto'      => $montoInverso,
                'obs'        => 'Anulación Nota ' . $nota['tipo']
            ]);

            /* 3️⃣ Revertir stock si correspondía */
            if ($nota['movimiento_stock'] === 'DEVOLUCION') {

                $det = $pdo->prepare("
                SELECT id_articulo, cantidad, precio_unitario
                FROM nota_compra_detalle
                WHERE idnota_compra = :id
            ");
                $det->execute([':id' => $idNota]);
                $items = $det->fetchAll(PDO::FETCH_ASSOC);

                foreach ($items as $d) {

                    $pdo->prepare("
                    UPDATE stock
                    SET stockDisponible = stockDisponible + :cant,
                        stockUltActualizacion = NOW(),
                        stockUsuActualizacion = :usu
                    WHERE id_sucursal = :suc AND id_articulo = :art
                ")->execute([
                        ':cant' => $d['cantidad'],
                        ':usu'  => $_SESSION['id_str'],
                        ':suc'  => $nota['id_sucursal'],
                        ':art'  => $d['id_articulo']
                    ]);

                    $pdo->prepare("
                    INSERT INTO movimientostock
                    (id_sucursal, TipoMovStockId, MovStockArticuloId,
                     MovStockCantidad, MovStockPrecioVenta, MovStockCosto,
                     MovStockFechaHora, MovStockUsuario,
                     MovStockSigno, MovStockReferencia)
                    VALUES
                    (:suc, 'ANULA_NC_COMPRA', :art,
                     :cant, 0, :costo,
                     NOW(), :usu,
                     1, :ref)
                ")->execute([
                        ':suc'   => $nota['id_sucursal'],
                        ':art'   => $d['id_articulo'],
                        ':cant'  => $d['cantidad'],
                        ':costo' => $d['precio_unitario'],
                        ':usu'   => $_SESSION['id_str'],
                        ':ref'   => 'ANULA NC ' . $nota['nro_documento']
                    ]);
                }
            }

            /* 4️⃣ Anular en Libro de Compras */
            $pdo->prepare("
            UPDATE libro_compra
            SET estado = 0
            WHERE idcompra_cabecera = :idcompra
              AND nro_comprobante = :nro
              AND tipo_comprobante = :tipo
              AND id_sucursal = :suc
        ")->execute([
                ':idcompra' => $nota['idcompra_cabecera'],
                ':nro'      => $nota['nro_documento'],
                ':tipo'     => ($nota['tipo'] === 'credito') ? 'NC' : 'ND',
                ':suc'      => $nota['id_sucursal']
            ]);

            $pdo->commit();

            return [
                "Alerta" => "recargar",
                "Titulo" => "Nota anulada",
                "Texto"  => "La nota fue anulada correctamente",
                "Tipo"   => "success"
            ];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['status' => 'error', 'msg' => $e->getMessage()];
        }
    }
}
