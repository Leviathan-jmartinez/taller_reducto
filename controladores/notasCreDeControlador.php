<?php
if ($peticionAjax) {
    require_once "../modelos/notasCreDeModelo.php";
} else {
    require_once "./modelos/notasCreDeModelo.php";
}

class notasCreDeControlador extends notasCreDeModelo
{
    /* ================= BUSCAR FACTURAS ================= */
    // controladores/notasCreDeControlador.php
    public static function buscarFacturas($texto)
    {
        $facturas = notasCreDeModelo::buscarFacturas($texto);

        if (empty($facturas)) {
            return '<div class="alert alert-warning">No se encontraron facturas</div>';
        }

        $html = '<table class="table table-bordered table-sm">';
        $html .= '<thead>
                <tr>
                    <th>N°</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th></th>
                </tr>
              </thead><tbody>';

        foreach ($facturas as $f) {
            $html .= '<tr>
            <td>' . htmlspecialchars($f['nro_factura']) . '</td>
            <td>' . $f['fecha_factura'] . '</td>
            <td>' . number_format($f['total_compra'], 0, ',', '.') . '</td>
            <td>
                <button class="btn btn-success btn-sm"
                    onclick="seleccionarFactura(' . (int)$f['idcompra_cabecera'] . ')">
                    Seleccionar
                </button>
            </td>
        </tr>';
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
            'nro_factura'       => $factura['nro_factura'],
            'fecha_factura'    => $factura['fecha_factura'],
            'total_compra'     => $factura['total_compra'],
            'idproveedor'      => $factura['idproveedores']
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
                'precio'      => $d['precio_unitario'],
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

        if (!isset($_SESSION['NC_DETALLE'][$i])) {
            return ['status' => 'error', 'msg' => 'Ítem no encontrado'];
        }

        if ($cantidad <= 0 || $precio <= 0) {
            return ['status' => 'error', 'msg' => 'Cantidad o precio inválido'];
        }

        $item = &$_SESSION['NC_DETALLE'][$i];

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

        if (empty($_SESSION['NC_DETALLE'])) {
            return ['status' => 'error', 'msg' => 'No hay detalle'];
        }

        /* ================= FILTRAR ÍTEMS ================= */
        $detalle = array_filter($_SESSION['NC_DETALLE'], function ($d) {
            return $d['cantidad'] > 0 && $d['precio'] > 0;
        });

        if (count($detalle) === 0) {
            return ['status' => 'error', 'msg' => 'Todos los ítems están en cero'];
        }

        /* ================= TOTALES ================= */
        $subtotal = 0;
        $iva5 = 0;
        $iva10 = 0;

        foreach ($detalle as $d) {
            $monto = round($d['cantidad'] * $d['precio'], 2);
            $subtotal += $monto;
            $iva5  += $d['iva_5'];
            $iva10 += $d['iva_10'];
        }

        $total = $subtotal;

        /* ================= TRANSACCIÓN ================= */
        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            /* === CABECERA === */
            $idNota = notasCreDeModelo::insertarNotaCompraModelo([
                'idproveedor' => $_POST['idproveedor'],
                'tipo'        => $_POST['tipo'],
                'serie'       => $_POST['serie'],
                'nro'         => $_POST['nro_documento'],
                'fecha'       => $_POST['fecha'],
                'idcompra'    => $_POST['idcompra_cabecera'],
                'subtotal'    => $subtotal,
                'iva5'        => $iva5,
                'iva10'       => $iva10,
                'total'       => $total,
                'descripcion' => $_POST['descripcion'] ?? '',
                'usuario'     => $_SESSION['id_str'],
                'timbrado'    => $_POST['timbrado'] ?? null
            ]);

            /* === DETALLE === */
            notasCreDeModelo::insertarDetalleNotaCompraModelo($idNota, $detalle);

            /* === CUENTAS A PAGAR === */
            notasCreDeModelo::impactarNotaCompraModelo([
                'idcompra' => $_POST['idcompra_cabecera'],
                'tipo'     => $_POST['tipo'],
                'idnota'   => $idNota,
                'monto'    => $total
            ]);

            $pdo->commit();

            unset($_SESSION['NC_DETALLE'], $_SESSION['NC_FACTURA']);

            return ['status' => 'ok'];

        } catch (Exception $e) {
            $pdo->rollBack();
            return ['status' => 'error', 'msg' => $e->getMessage()];
        }
    }
}

