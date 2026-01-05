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
                        Seleccionar
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
            'idproveedor'       => $factura['idproveedores'],
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

        $factura = $_SESSION['NC_FACTURA'] ?? null;

        if (
            !isset($_SESSION['NC_FACTURA']) ||
            empty($_SESSION['NC_FACTURA']['idcompra_cabecera'])
        ) {
            return [
                'status' => 'error',
                'msg' => 'Debe seleccionar una factura válida'
            ];
        }

        if (empty($_SESSION['NC_DETALLE'])) {
            return ['status' => 'error', 'msg' => 'No hay detalle'];
        }

        /* ================= FILTRAR ÍTEMS EN CERO ================= */
        $detalle = array_filter($_SESSION['NC_DETALLE'], function ($d) {
            return $d['cantidad'] > 0 && $d['precio'] > 0;
        });

        if (count($detalle) === 0) {
            return ['status' => 'error', 'msg' => 'Todos los ítems están en cero'];
        }

        /* ================= TOTALES (IVA INCLUIDO) ================= */
        $total = 0;
        foreach ($detalle as $d) {
            $total += round($d['cantidad'] * $d['precio'], 2);
        }

        /* ==========================================================
       🔒 VALIDACIÓN TOPE NOTA DE CRÉDITO (ACÁ VA)
       ========================================================== */
        if ($_POST['tipo'] === 'credito') {

            $totalFactura = (float)$factura['total'];

            $totalNCPrevias = notasCreDeModelo::totalNCActivasPorFactura(
                $factura['idcompra_cabecera']
            );

            $totalNCResultado = $totalNCPrevias + $total;

            if ($totalNCResultado > $totalFactura) {
                return [
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto"  => "La Nota de Crédito supera el monto de la factura. Disponible: "
                        . number_format($totalFactura - $totalNCPrevias, 0, ',', '.'),
                    "Tipo"   => "error"
                ];
            }
        }
        /* ========================================================== */

        /* ================= SIGNO CONTABLE ================= */
        $montoMovimiento = $total;
        if ($_POST['tipo'] === 'credito') {
            $montoMovimiento *= -1;
        }

        $pdo = mainModel::conectar();

        try {
            $pdo->beginTransaction();

            $idNota = notasCreDeModelo::insertarNotaCompraModelo($pdo, [
                'idproveedor' => $factura['idproveedor'],
                'id_sucursal' => $_SESSION['nick_sucursal'],
                'tipo'        => $_POST['tipo'],
                'nro'         => $_POST['nro_nota'],
                'fecha'       => $_POST['fecha'],
                'idcompra'    => $factura['idcompra_cabecera'],
                'total'       => $total,
                'descripcion' => $_POST['descripcion'] ?? '',
                'usuario'     => $_SESSION['id_str'],
                'timbrado'    => $_POST['timbrado'] ?? null
            ]);

            notasCreDeModelo::insertarDetalleNotaCompraModelo($pdo, $idNota, $detalle);

            notasCreDeModelo::impactarNotaCompraModelo($pdo, [
                'idcompra'   => $factura['idcompra_cabecera'],
                'id_sucursal' => $_SESSION['nick_sucursal'],
                'tipo'       => $_POST['tipo'],
                'idnota'     => $idNota,
                'monto'      => $montoMovimiento,
                'obs'        => 'Nota ' . $_POST['tipo']
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
            echo json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
            exit;
        }
    }


    /** Controlador paginar compras */
    public function paginador_notasCreDe_controlador($pagina, $registros, $privilegio, $url, $busqueda1, $busqueda2)
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $privilegio = mainModel::limpiar_string($privilegio);
        $busqueda1 = mainModel::limpiar_string($busqueda1);
        $busqueda2 = mainModel::limpiar_string($busqueda2);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        if (!empty($busqueda1) && !empty($busqueda2)) {
            $consulta = " SELECT SQL_CALC_FOUND_ROWS nc.idnota_compra AS idnota_compra, nc.id_sucursal AS id_sucursal, nc.tipo AS tipo_nota, nc.nro_documento AS nro_documento, nc.fecha AS fecha_nota, nc.total AS 
            total_nota, nc.estado   AS estado_nota, nc.fecha_creacion   AS fecha_creacion, nc.idcompra_cabecera AS idcompra_cabecera, co.nro_factura  
            AS nro_factura, co.fecha_factura AS fecha_factura, p.idproveedores AS idproveedor, p.razon_social  AS razon_social, p.ruc   AS ruc, p.telefono  
            AS telefono, p.direccion AS direccion, p.correo AS correo, p.estado AS estado_proveedor, u.id_usuario AS id_usuario, u.usu_nombre AS usu_nombre, 
            u.usu_apellido  AS usu_apellido, u.usu_nick  AS usu_nick, u.usu_estado AS usu_estado 
            FROM nota_compra nc 
            INNER JOIN proveedores p ON p.idproveedores = nc.idproveedor 
            INNER JOIN usuarios u ON u.id_usuario = nc.idusuario 
            INNER JOIN compra_cabecera co ON co.idcompra_cabecera = nc.idcompra_cabecera 
            WHERE DATE(nc.fecha_creacion) >= '$busqueda1'   AND DATE(nc.fecha_creacion) <= '$busqueda2' AND nc.id_sucursal = " . $_SESSION['nick_sucursal'] . "
            ORDER BY nc.fecha_creacion ASC LIMIT $inicio, $registros ";
        } else {
            $consulta = " SELECT SQL_CALC_FOUND_ROWS nc.idnota_compra AS idnota_compra, nc.id_sucursal AS id_sucursal, nc.tipo AS tipo_nota, nc.nro_documento AS nro_documento, nc.fecha AS fecha_nota, nc.total AS 
            total_nota, nc.estado   AS estado_nota, nc.fecha_creacion   AS fecha_creacion, nc.idcompra_cabecera AS idcompra_cabecera, co.nro_factura  
            AS nro_factura, co.fecha_factura AS fecha_factura, p.idproveedores AS idproveedor, p.razon_social  AS razon_social, p.ruc   AS ruc, p.telefono  
            AS telefono, p.direccion AS direccion, p.correo AS correo, p.estado AS estado_proveedor, u.id_usuario AS id_usuario, u.usu_nombre AS usu_nombre, 
            u.usu_apellido  AS usu_apellido, u.usu_nick  AS usu_nick, u.usu_estado AS usu_estado 
            FROM nota_compra nc 
            INNER JOIN proveedores p ON p.idproveedores = nc.idproveedor 
            INNER JOIN usuarios u ON u.id_usuario = nc.idusuario 
            INNER JOIN compra_cabecera co ON co.idcompra_cabecera = nc.idcompra_cabecera 
            WHERE DATE(nc.fecha_creacion) >= '$busqueda1'   AND DATE(nc.fecha_creacion) <= '$busqueda2' AND nc.id_sucursal = " . $_SESSION['nick_sucursal'] . "
            ORDER BY nc.fecha_creacion ASC LIMIT $inicio, $registros ";
        }
        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta);
        $datos = $datos->fetchAll();

        $total = $conexion->query("SELECT FOUND_ROWS()");
        $total = (int) $total->fetchColumn();

        $Npaginas = ceil($total / $registros);

        $tabla .= '<div class="table-responsive">
					<table class="table table-dark table-sm">
						<thead>
							<tr class="text-center roboto-medium">
								<th>#</th>
                                <th>PROVEEDOR</th>
                                <th>NUMERO DE DOCUMENTO</th>
                                <th>FECHA</th>
                                <th>TOTAL DOCUMENTO</th>
                                <th>FACTURA ASOCIADA</th>
                                <th>TIPO DOCUMENTO</th>
                                <th>CARGADO POR</th>
                                <th>ESTADO</th>';
        if ($privilegio == 1 || $privilegio == 2) {
            $tabla .=           '<th>ANULAR</th>';
        }
        $tabla .= '
						</tr>
						</thead>
						<tbody>';
        if ($total >= 1 && $pagina <= $Npaginas) {
            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;
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
                if ($privilegio == 1 || $privilegio == 2) {
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
            if ($total >= 1) {
                $tabla .= '<tr class="text-center"> <td colspan="6"> <a href="' . $url . '" class="btn btn-reaised btn-primary btn-sm"> Haga click aqui para recargar el listado </a> </td> </tr> ';
            } else {
                $tabla .= '<tr class="text-center"> <td colspan="6"> No hay regitros en el sistema</td> </tr> ';
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

            /* 2️⃣ Movimiento inverso */
            if ($nota['tipo'] === 'credito') {
                $montoInverso = abs($nota['total']);
            } else {
                $montoInverso = -abs($nota['total']);
            }

            notasCreDeModelo::impactarAnulacionNotaModelo($pdo, [
                'idcompra' => $nota['idcompra_cabecera'],
                'id_sucursal' => $nota['id_sucursal'],
                'idnota'   => $idNota,
                'monto'    => $montoInverso,
                'obs'      => 'Anulación nota ' . $nota['tipo']
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
