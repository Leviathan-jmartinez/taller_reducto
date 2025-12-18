<?php
$peticionAjax = true;

require_once "../config/APP.php";
require_once "../modelos/notasCreDeModelo.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start(['name' => 'STR']);
}

/* ================= BUSCAR FACTURAS ================= */
if (isset($_POST['buscar_factura'])) {

    $texto = $_POST['buscar_factura'];

    $facturas = notasCreDeModelo::buscar_facturas_modelo($texto);

    if (!$facturas) {
        echo '<div class="alert alert-warning">No se encontraron facturas</div>';
        exit;
    }

    echo '<table class="table table-bordered table-sm">';
    echo '<tr>
            <th>N°</th>
            <th>Fecha</th>
            <th>Total</th>
            <th></th>
          </tr>';

    foreach ($facturas as $f) {
        echo '<tr>
                <td>' . $f['nro_factura'] . '</td>
                <td>' . $f['fecha_factura'] . '</td>
                <td>' . number_format($f['total_compra'], 0, ',', '.') . '</td>
                <td>
                    <button class="btn btn-success btn-sm"
                        onclick="seleccionarFactura(' . $f['idcompra_cabecera'] . ')">
                        Seleccionar
                    </button>
                </td>
              </tr>';
    }

    echo '</table>';
    exit;
}

/* ================= GUARDAR EN SESIÓN ================= */
/* ================= GUARDAR FACTURA + DETALLE EN SESIÓN ================= */
if (isset($_POST['seleccionar_factura'])) {

    $_SESSION['DEBUG'] = 'ENTRÓ A SELECCIONAR FACTURA';

    $id = intval($_POST['seleccionar_factura']);

    /* ===== FACTURA ===== */
    $factura = notasCreDeModelo::obtener_factura_modelo($id);

    $_SESSION['NC_FACTURA'] = [
        'idcompra_cabecera' => $factura['idcompra_cabecera'],
        'nro_factura'       => $factura['nro_factura'],
        'fecha_factura'    => $factura['fecha_factura'],
        'total_compra'     => $factura['total_compra'],
        'idproveedor'      => $factura['idproveedores']
    ];

    /* ===== DETALLE ===== */
    $detalle = notasCreDeModelo::obtener_detalle_compra_modelo($id);
    $_SESSION['NC_DETALLE'] = [];

    foreach ($detalle as $d) {

        $descripcion = trim($d['desc_articulo'] ?? '');
        if ($descripcion === '') {
            $descripcion = 'ARTICULO #' . $d['id_articulo'];
        }

        $exenta = 0;
        $base5  = 0;
        $base10 = 0;

        if ($d['divisor'] > 0) {
            $iva  = round($d['subtotal'] / $d['divisor'], 2);
            $base = $d['subtotal'] - $iva;

            if ($d['divisor'] == 11) {
                $base10 = $base;
            } elseif ($d['divisor'] == 21) {
                $base5 = $base;
            }
        } else {
            $exenta = $d['subtotal'];
        }

        $_SESSION['NC_DETALLE'][] = [
            'id_articulo' => $d['id_articulo'],
            'descripcion' => $descripcion,
            'cantidad'    => $d['cantidad_recibida'],
            'precio'      => $d['precio_unitario'],
            'iva'         => $d['tipo_impuesto_descri'],
            'exenta'      => $exenta,
            'iva_5'       => $base5,
            'iva_10'      => $base10
        ];
    }
    exit;
}

if (isset($_POST['accion']) && $_POST['accion'] === 'actualizar_item_nc') {

    if (session_status() == PHP_SESSION_NONE) {
        session_start(['name' => 'STR']);
    }

    $i        = intval($_POST['index']);
    $cantidad = floatval($_POST['cantidad']);
    $precio   = floatval($_POST['precio']);

    if (!isset($_SESSION['NC_DETALLE'][$i])) {
        echo json_encode(['status' => 'error', 'msg' => 'Ítem no encontrado']);
        exit;
    }

    if ($cantidad <= 0 || $precio <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Cantidad y precio deben ser mayores a cero']);
        exit;
    }

    /* ===== ITEM ===== */
    /* ===== ITEM ===== */
    $item = &$_SESSION['NC_DETALLE'][$i];

    $monto = round($cantidad * $precio, 2);

    /*
  IMPORTANTE:
  - Los precios SON IVA INCLUIDO
  - El tipo se deduce del item original
*/

    $exenta = 0;
    $base5  = 0;
    $base10 = 0;

    if ($item['iva_10'] > 0) {
        // IVA 10% (incluido)
        $iva = round($monto / 11, 2);
        $base10 = $monto - $iva;
    } elseif ($item['iva_5'] > 0) {
        // IVA 5% (incluido)
        $iva = round($monto / 21, 2);
        $base5 = $monto - $iva;
    } else {
        // Exento
        $exenta = $monto;
    }

    $item['cantidad'] = $cantidad;
    $item['precio']  = $precio;
    $item['exenta']  = round($exenta, 2);
    $item['iva_5']   = round($base5, 2);
    $item['iva_10']  = round($base10, 2);


    /* ===== TOTALES ===== */
    $subtotal = 0;
    $iva5 = 0;
    $iva10 = 0;

    foreach ($_SESSION['NC_DETALLE'] as $d) {
        $subtotal += $d['exenta'] + $d['iva_5'] + $d['iva_10'];
        $iva5  += ($d['iva_5']  > 0) ? round($d['iva_5'] / 21, 2) : 0;
        $iva10 += ($d['iva_10'] > 0) ? round($d['iva_10'] / 11, 2) : 0;
    }

    $total = $subtotal + $iva5 + $iva10;

    echo json_encode([
        'status' => 'ok',

        /* fila */
        'fila' => [
            'exenta' => number_format($item['exenta'], 0, ',', '.'),
            'iva_5'  => number_format($item['iva_5'], 0, ',', '.'),
            'iva_10' => number_format($item['iva_10'], 0, ',', '.')
        ],

        /* totales */
        'totales' => [
            'subtotal' => number_format($subtotal, 0, ',', '.'),
            'iva_5'    => number_format($iva5, 0, ',', '.'),
            'iva_10'   => number_format($iva10, 0, ',', '.'),
            'total'    => number_format($total, 0, ',', '.')
        ]
    ]);
    exit;
}
