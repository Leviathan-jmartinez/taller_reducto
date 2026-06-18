<?php
if ($peticionAjax) {
    require_once "../modelos/compraModelo.php";
} else {
    require_once "./modelos/compraModelo.php";
}

class compraControlador extends compraModelo
{

    /**controlador buscar oc */
    public function buscar_oc_controlador()
    {
        $ordencompa  = mainModel::limpiar_string($_POST['buscar_oc']);

        if ($ordencompa == "") {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    Debes introducir el RUC, RAZON SOCIAL o NUMERO DE PEDIDO
                                </p>
                            </div>';
            exit();
        }
        /**seleccionar proveedor */
        $datoscompra = mainModel::ejecutar_consulta_simple("SELECT SQL_CALC_FOUND_ROWS oc.idorden_compra as idorden_compra, oc.id_sucursal as id_sucursal,oc.idproveedores as idproveedores, 
        oc.id_usuario as id_usuario, oc.fecha as fecha, oc.estado as estodoOC, oc.fecha_entrega as fecha_entrega, oc.updated as updated, 
        oc.updatedby as updatedby, p.idproveedores as idproveedores, p.id_ciudad as id_ciudad, p.razon_social as razon_social, 
        p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, p.estado as estadoPro, 
        u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick
        from orden_compra oc 
        INNER JOIN proveedores p on p.idproveedores = oc.idproveedores 
        INNER JOIN usuarios u on u.id_usuario = oc.id_usuario
        where (oc.idorden_compra like '%$ordencompa%' or p.razon_social like '%$ordencompa%' or p.ruc like '%$ordencompa%') and oc.estado = '1' and oc.id_sucursal = '" . $_SESSION['nick_sucursal'] . "'
        order by idorden_compra desc");

        if ($datoscompra->rowCount() >= 1) {
            $datoscompra = $datoscompra->fetchAll();
            $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>
                        <tr class="text-center">
                            <th>Número de OC</th>
                            <th>Proveedor</th>
                            <th></th>
                        </tr>';
            foreach ($datoscompra as $rows) {
                $tabla .= '
                        <tr class="text-center">
                            <td>' . $rows['idorden_compra'] . '</td>
                            <td>' . $rows['razon_social'] . '</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="agregar_OC(' . $rows['idorden_compra'] . ')"><i class="fas fa-user-plus"></i></button>
                            </td>
                        </tr>';
            }
            $tabla .= '</tbody></table></div>';
            return $tabla;
        } else {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún pedido en el sistema que coincida con <strong>“' . $ordencompa . '”</strong>
                                </p>
                            </div>';
        }
    }
    /**fin controlador */

    /**controlador cargar oc */
    public function cargar_oc_controlador()
    {

        $idoccompra = mainModel::limpiar_string($_POST['id_oc_seleccionado'] ?? '');
        if (empty($idoccompra)) {
            $_SESSION['alerta_oc'] = [
                "tipo" => "error",
                "mensaje" => "No se recibió ID de la orden de compra"
            ];
            header("Location: " . SERVERURL . "factura-nuevo/");
            exit();
        }
        $_SESSION['id_oc_seleccionado'] = $idoccompra;
        // 1️⃣ Cabecera del pedido (proveedor)
        $sqlCabecera = mainModel::ejecutar_consulta_simple("
        SELECT oc.idorden_compra, p.razon_social, p.ruc, oc.idproveedores
        FROM orden_compra oc
        INNER JOIN proveedores p ON p.idproveedores = oc.idproveedores
        WHERE oc.idorden_compra = '$idoccompra' and id_sucursal = '" . $_SESSION['nick_sucursal'] . "'");
        $cabecera = $sqlCabecera->fetch();
        if ($cabecera) {
            $_SESSION['datos_proveedorCO'] = [
                "ID" => $cabecera['idproveedores'],
                "RAZON" => $cabecera['razon_social'],
                "RUC" => $cabecera['ruc']
            ];
        }

        // 2️⃣ Detalle del pedido (artículos)
        $sqlDetalle = mainModel::ejecutar_consulta_simple("
        SELECT ocd.id_articulo, ocd.cantidad_pendiente, a.desc_articulo, a.codigo, ocd.precio_unitario, ti.idiva, ti.tipo_impuesto_descri, ti.ratevalueiva, ti.divisor
        FROM orden_compra_detalle ocd
        INNER JOIN articulos a ON a.id_articulo = ocd.id_articulo
        INNER JOIN tipo_impuesto ti ON ti.idiva = a.idiva
        WHERE ocd.idorden_compra = '$idoccompra'");
        $detalle = $sqlDetalle->fetchAll();

        $_SESSION['Cdatos_articuloCO'] = $_SESSION['Cdatos_articuloCO'] ?? [];
        foreach ($detalle as $i => $row) {

            // Si ya existe en la sesión (modificado), no sobreescribas cantidad y precio
            $cantidad = $_SESSION['Cdatos_articuloCO'][$i]['cantidad'] ?? $row['cantidad_pendiente'];
            $cantidadFacturada = $_SESSION['Cdatos_articuloCO'][$i]['cantidad_facturada'] ?? $cantidad;
            $precio   = $_SESSION['Cdatos_articuloCO'][$i]['precio'] ?? $row['precio_unitario'];
            $subtotal = $cantidadFacturada * $precio;
            if ($row['divisor'] == 0) {
                $iva = 0;
            } else {
                $iva = $subtotal / $row['divisor'];
            }
            $_SESSION['Cdatos_articuloCO'][$i] = [
                "ID" => $row['id_articulo'],
                "codigo" => $row['codigo'],
                "descripcion" => $row['desc_articulo'],
                "cantidad" => $cantidad,
                "cantidad_facturada" => $cantidadFacturada,
                "cantidad_pendiente" => $row['cantidad_pendiente'],
                "precio" => $precio,
                "subtotal" => $subtotal,
                "tipo_iva" => $row['idiva'],
                "iva_descri" => $row['tipo_impuesto_descri'],
                "iva" => $iva,
                "ratevalueiva" => $row['ratevalueiva'],
                "divisor" => $row['divisor']
            ];
        }


        // 3️⃣ Redirigir a la página para que se recargue
        header("Location: " . SERVERURL . "factura-nuevo/");
        exit();
    }
    /**fin controlador */

    public function guardar_compra_controlador()
    {
        if (!mainModel::tienePermiso('compra.crear')) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Advertencia!",
                "Texto" => "No posee los permisos necesarios para realizar esta accion",
                "Tipo" => "error"
            ];
        }

        if (empty($_SESSION['Cdatos_articuloCO'])) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No hay detalles para guardar la compra.",
                "Tipo" => "error"
            ];
        }

        if (empty($_SESSION['datos_proveedorCO']['ID'])) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Proveedor requerido",
                "Texto" => "Debe seleccionar un proveedor antes de registrar la factura.",
                "Tipo" => "warning"
            ];
        }

        // 1) Sincronizar y filtrar articulos con cantidad facturada > 0
        if (!empty($_POST['detalle_indices']) && is_array($_POST['detalle_indices'])) {
            foreach ($_POST['detalle_indices'] as $pos => $indice) {
                if (!isset($_SESSION['Cdatos_articuloCO'][$indice])) {
                    continue;
                }

                $cantidadRecibidaPost = (float)($_POST['cantidades'][$pos] ?? 0);
                $cantidadFacturadaPost = (float)($_POST['cantidades_facturadas'][$pos] ?? $cantidadRecibidaPost);
                $precioPost = (float)($_POST['precios'][$pos] ?? 0);
                $divisorPost = (float)($_SESSION['Cdatos_articuloCO'][$indice]['divisor'] ?? 0);
                $subtotalPost = round($cantidadFacturadaPost * $precioPost, 2);
                $ivaPost = $divisorPost > 0 ? round($subtotalPost / $divisorPost, 2) : 0;

                $_SESSION['Cdatos_articuloCO'][$indice]['cantidad'] = $cantidadRecibidaPost;
                $_SESSION['Cdatos_articuloCO'][$indice]['cantidad_facturada'] = $cantidadFacturadaPost;
                $_SESSION['Cdatos_articuloCO'][$indice]['precio'] = $precioPost;
                $_SESSION['Cdatos_articuloCO'][$indice]['subtotal'] = $subtotalPost;
                $_SESSION['Cdatos_articuloCO'][$indice]['iva'] = $ivaPost;
            }
        }

        $itemsValidos = [];
        foreach ($_SESSION['Cdatos_articuloCO'] as $item) {
            $cantidadRecibida = (float)($item['cantidad'] ?? 0);
            $cantidadFacturada = (float)($item['cantidad_facturada'] ?? $cantidadRecibida);
            if ($cantidadRecibida < 0 || $cantidadFacturada < 0) {
                return [
                    "Alerta" => "simple",
                    "Titulo" => "Cantidad invalida",
                    "Texto" => "Las cantidades facturadas y recibidas no pueden ser negativas.",
                    "Tipo" => "error"
                ];
            }
            if ($cantidadRecibida > $cantidadFacturada) {
                return [
                    "Alerta" => "simple",
                    "Titulo" => "Cantidad excedida",
                    "Texto" => "La cantidad recibida del articulo " . $item['descripcion'] . " no puede superar la cantidad facturada.",
                    "Tipo" => "error"
                ];
            }
            if ($cantidadFacturada > 0) {
                if (
                    isset($_SESSION['id_oc_seleccionado'], $item['cantidad_pendiente']) &&
                    $cantidadRecibida > (float)$item['cantidad_pendiente']
                ) {
                    return [
                        "Alerta" => "simple",
                        "Titulo" => "Cantidad excedida",
                        "Texto" => "La cantidad recibida del artículo " . $item['descripcion'] . " no puede superar la cantidad pendiente de la orden de compra.",
                        "Tipo" => "error"
                    ];
                }
                if (
                    isset($_SESSION['id_oc_seleccionado'], $item['cantidad_pendiente']) &&
                    $cantidadFacturada > (float)$item['cantidad_pendiente']
                ) {
                    return [
                        "Alerta" => "simple",
                        "Titulo" => "Cantidad excedida",
                        "Texto" => "La cantidad facturada del articulo " . $item['descripcion'] . " no puede superar la cantidad pendiente de la orden de compra.",
                        "Tipo" => "error"
                    ];
                }
                $itemsValidos[] = $item;
            }
        }

        if (count($itemsValidos) === 0) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debes ingresar al menos un producto con cantidad facturada mayor a 0.",
                "Tipo" => "error"
            ];
        }

        // 2) Recalcular total real
        $totalReal = 0;
        $tieneDiferencia = false;
        foreach ($itemsValidos as $item) {
            $cantidadRecibida = (float)($item['cantidad'] ?? 0);
            $cantidadFacturada = (float)($item['cantidad_facturada'] ?? $cantidadRecibida);
            $totalReal += round($cantidadFacturada * (float)$item['precio'], 2);
            if (abs($cantidadFacturada - $cantidadRecibida) > 0.0001) {
                $tieneDiferencia = true;
            }
        }
        $totalReal = round($totalReal, 2);

        $condicion = mainModel::limpiar_string($_POST['condicion'] ?? '');
        $vencimiento_timbrado = mainModel::limpiar_string($_POST['vencimiento_timbrado'] ?? '');
        $intervalo = isset($_POST['intervalo']) ? (int) $_POST['intervalo'] : 0;
        $cuotas = isset($_POST['cuotas']) ? (int) $_POST['cuotas'] : 0;

        if (!in_array($condicion, ['contado', 'credito'], true)) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe seleccionar una condicion de venta valida.",
                "Tipo" => "error"
            ];
        }

        if ($intervalo <= 0) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Para compras debe indicar un intervalo mayor a 0.",
                "Tipo" => "error"
            ];
        }

        if ($condicion === 'contado') {
            $cuotas = 1;
        } elseif ($cuotas <= 0) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Para compras a credito debe indicar cuotas mayores a 0.",
                "Tipo" => "error"
            ];
        }

        if ($vencimiento_timbrado == "" || mainModel::verificarFecha($vencimiento_timbrado)) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El vencimiento del timbrado no es valido.",
                "Tipo" => "error"
            ];
        }

        if (strtotime($vencimiento_timbrado) < strtotime(date('Y-m-d'))) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El vencimiento del timbrado no puede ser menor a hoy.",
                "Tipo" => "error"
            ];
        }

        $pdo = null;

        try {
            $pdo = mainModel::conectar();
            $pdo->beginTransaction();

            /* ===============================
            CABECERA
        ================================ */
            $datosCab = [
                "proveedor"            => $_SESSION['datos_proveedorCO']['ID'],
                "usuario"              => $_SESSION['id_str'],
                "idsucursal"           => $_SESSION['nick_sucursal'],
                "nro_factura"          => $_POST['factura_numero'],
                "fecha_factura"        => $_POST['fecha_emision'],
                "timbrado"             => $_POST['timbrado'],
                "vencimiento_timbrado" => $vencimiento_timbrado,
                "estado"               => $tieneDiferencia ? "3" : "1",
                "total"                => $totalReal,
                "condicion"            => $condicion,
                "intervalo"            => $intervalo,
                "idoc"                 => isset($_SESSION['id_oc_seleccionado']) ? $_SESSION['id_oc_seleccionado'] : null
            ];

            $proveedor = $_SESSION['datos_proveedorCO']['ID'];
            $nro       = $_POST['factura_numero'];
            $timbrado  = $_POST['timbrado'];
            $sucursal  = $_SESSION['nick_sucursal'];

            $validarFactura = mainModel::ejecutar_consulta_simple("
            SELECT 1
            FROM compra_cabecera
            WHERE idproveedores = '$proveedor'
              AND nro_factura = '$nro'
              AND nro_timbrado = '$timbrado'
              AND id_sucursal = '$sucursal'
              AND estado <> 0
        ")->rowCount();

            if ($validarFactura > 0) {
                return [
                    "Alerta" => "simple",
                    "Titulo" => "Factura duplicada",
                    "Texto"  => "Esta factura ya fue registrada para este proveedor.",
                    "Tipo"   => "warning"
                ];
            }

            $guardarCab = compraModelo::insertar_compra_cabecera_modelo($datosCab);
            if ($guardarCab["stmt"]->rowCount() < 1) {
                throw new Exception("No se pudo guardar la cabecera de la compra.");
            }
            $idcab = $guardarCab["last_id"];

            /* ===============================
           DETALLES + STOCK + ACUMULADORES
        ================================ */
            $exenta     = 0.00;
            $gravada5   = 0.00;
            $iva5       = 0.00;
            $gravada10  = 0.00;
            $iva10      = 0.00;
            $totalLibro = 0.00;

            foreach ($itemsValidos as $item) {

                $cantidadRecibida = (float)($item['cantidad'] ?? 0);
                $cantidadFacturada = (float)($item['cantidad_facturada'] ?? $cantidadRecibida);
                $precioUnitario = round((float)$item['precio'], 2);
                $subtotal = round($cantidadFacturada * $precioUnitario, 2);
                $ivaItem  = ((float)$item['divisor'] > 0) ? round($subtotal / (float)$item['divisor'], 2) : 0;

                $totalLibro = round($totalLibro + $subtotal, 2);

                switch ($item['tipo_iva']) {
                    case '1': // IVA 5%
                        $base = round($subtotal - $ivaItem, 2);
                        $gravada5 = round($gravada5 + $base, 2);
                        $iva5     = round($iva5 + $ivaItem, 2);
                        break;

                    case '2': // IVA 10%
                        $base = round($subtotal - $ivaItem, 2);
                        $gravada10 = round($gravada10 + $base, 2);
                        $iva10     = round($iva10 + $ivaItem, 2);
                        break;

                    default: // EXENTA
                        $exenta = round($exenta + $subtotal, 2);
                        break;
                }

                /* ===== Guardar detalle ===== */
                $detalle = [
                    "idcab"       => $idcab,
                    "id_articulo" => $item['ID'],
                    "precio"      => $precioUnitario,
                    "cantidad_facturada" => $cantidadFacturada,
                    "cantidad_recibida" => $cantidadRecibida,
                    "tipo_iva"    => $item['tipo_iva'],
                    "subtotal"    => $subtotal,
                    "iva"         => $ivaItem
                ];

                $guardarDet = compraModelo::insertar_compra_detalle_modelo($detalle);
                if ($guardarDet->rowCount() < 1) {
                    throw new Exception("Error al guardar detalle del artículo ID " . $item['ID']);
                }

                self::registrar_articulo_proveedor_modelo(
                    $item['ID'],
                    $_SESSION['datos_proveedorCO']['ID'],
                    $precioUnitario
                );

                /* ===== Movimiento de stock ===== */
                if ($cantidadRecibida > 0) {
                    $mov = [
                        "local"        => $_SESSION['nick_sucursal'],
                        "tipo"         => "RECEPCION COMPRA",
                        "producto"     => $item['ID'],
                        "cantidad"     => $cantidadRecibida,
                        "precioVenta"  => 0,
                        "costo"        => $precioUnitario,
                        "nroTicket"    => $_POST['factura_numero'],
                        "pos"          => null,
                        "usuario"      => $_SESSION['id_str'],
                        "signo"        => 1,
                        "referencia"   => $idcab
                    ];
                    compraModelo::agregar_movimiento_stock($mov);
                }
            }

            /* ===============================
            ACTUALIZAR ORDEN DE COMPRA
        ================================ */
            if (!empty($datosCab['idoc'])) {
                compraModelo::actualizar_oc_modelo([
                    "idorden_compra"    => $datosCab['idoc'],
                    "idcompra_cabecera" => $idcab,
                    "updatedby"         => $_SESSION['id_str'],
                    "id_sucursal"       => $_SESSION['nick_sucursal']
                ]);
            }

            /* ===============================
            CUENTAS A PAGAR
        ================================ */
            $total     = $totalReal;

            if ($condicion === 'credito' || $condicion === 'contado') {
                $monto_cuota = round($total / $cuotas, 2);
                $monto_acumulado = 0;

                for ($i = 1; $i <= $cuotas; $i++) {
                    $monto = $i === $cuotas ? round($total - $monto_acumulado, 2) : $monto_cuota;
                    $monto_acumulado = round($monto_acumulado + $monto, 2);
                    $fecha_vencimiento = date('Y-m-d', strtotime("+" . ($intervalo * $i) . " days"));
                    $datos_cuenta = [
                        "idcompra"          => $idcab,
                        "idsucursal"        => $_SESSION['nick_sucursal'],
                        "monto"             => $monto,
                        "saldo"             => $monto,
                        "cuotas"            => $i,
                        "fecha_vencimiento" => $fecha_vencimiento,
                        "observacion"       => "Factura " . $_POST['factura_numero'],
                        "estado"            => 1
                    ];

                    $guardarCuenta = compraModelo::insertar_cuentas_a_pagar_modelo($datos_cuenta);
                    if ($guardarCuenta->rowCount() < 1) {
                        throw new Exception("Error al guardar cuenta a pagar cuota $i");
                    }
                }
            }

            /* ===============================
            LIBRO DE COMPRAS
            ================================ */
            $datosLibro = [
                "idcompra"    => $idcab,
                "id_sucursal" => $_SESSION['nick_sucursal'],
                "fecha"       => $_POST['fecha_emision'],
                "tipo"        => "factura",
                "serie"       => substr($_POST['factura_numero'], 0, 7),
                "numero"      => $_POST['factura_numero'],
                "proveedor"   => $_SESSION['datos_proveedorCO']['ID'],
                "prov_nom"    => $_SESSION['datos_proveedorCO']['RAZON'],
                "prov_ruc"    => $_SESSION['datos_proveedorCO']['RUC'],
                "exenta"      => round($exenta, 2),
                "gravada5"    => round($gravada5, 2),
                "iva5"        => round($iva5, 2),
                "gravada10"   => round($gravada10, 2),
                "iva10"       => round($iva10, 2),
                "total"       => round($totalLibro, 2)
            ];

            $guardarLibro = compraModelo::insertar_libro_compra_modelo($datosLibro);
            if ($guardarLibro->rowCount() < 1) {
                throw new Exception("No se pudo guardar el libro de compras.");
            }

            $pdo->commit();

            unset($_SESSION['Cdatos_articuloCO'], $_SESSION['datos_proveedorCO'], $_SESSION['id_oc_seleccionado']);

            return [
                "Alerta" => "recargar",
                "Titulo" => "Compra registrada",
                "Texto"  => "La compra se guardó correctamente.",
                "Tipo"   => "success"
            ];
        } catch (Exception $e) {
            if ($pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack();
            return [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado!",
                "Texto"  => $e->getMessage(),
                "Tipo"   => "error"
            ];
        }
    }

    /**controlador buscador proveedor */
    public function buscar_proveedor_controlador()
    {
        $proveedor  = mainModel::limpiar_string($_POST['buscar_proveedorCO']);

        if ($proveedor == "") {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    Debes introducir el RUC o RAZON SOCIAL
                                </p>
                            </div>';
            exit();
        }
        /**seleccionar proveedor */
        $datos_proveedor = mainModel::ejecutar_consulta_simple("SELECT * FROM proveedores where ruc like '%$proveedor%' or razon_social like '%$proveedor%' or 
        telefono like '%$proveedor%' order by razon_social desc");

        if ($datos_proveedor->rowCount() >= 1) {
            $datos_proveedor = $datos_proveedor->fetchAll();
            $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>';
            foreach ($datos_proveedor as $rows) {
                $tabla .= '
                        <tr class="text-center">
                            <td>' . $rows['ruc'] . ' ' . $rows['razon_social'] . '</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="agregar_proveedorCO(' . $rows['idproveedores'] . ')"><i class="fas fa-user-plus"></i></button>
                            </td>
                        </tr>';
            }
            $tabla .= '</tbody></table></div>';
            return $tabla;
        } else {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún proveedor en el sistema que coincida con <strong>“' . $proveedor . '”</strong>
                                </p>
                            </div>';
        }
    }
    /**fin controlador */

    /**Controlador agregar proveedor */
    public function agregar_proveedor_controlador()
    {
        $id  = mainModel::limpiar_string($_POST['id_agregar_proveedorCO']);

        $check_proveedor = mainModel::ejecutar_consulta_simple("select * from proveedores where idproveedores = '$id' ");
        if ($check_proveedor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido encontrar el proveedor en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $campos = $check_proveedor->fetch();
        }
        /**iniciar sesion para utilizar variables de sesion */
        unset($_SESSION['datos_proveedorCO']);
        if (!isset($_SESSION['datos_proveedorCO'])) {
            $_SESSION['datos_proveedorCO'] = [
                "ID" => $campos['idproveedores'],
                "RUC" => $campos['ruc'],
                "RAZON" => $campos['razon_social'],
                "TELEFONO" => $campos['telefono']
            ];
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Proveedor Agregado!",
                "Texto" => "Proveedor agregado correctamente al pedido",
                "Tipo" => "success"
            ];
            echo json_encode($alerta);
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido agregar el proveedor al pedido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
        }
    }
    /**fin controlador */

    /**Controlador eliminar proveedor */
    public function eliminar_proveedor_controlador()
    {
        $id = mainModel::limpiar_string($_POST['id_eliminar_proveedorCO'] ?? '');

        if ($id == "" || !isset($_SESSION['datos_proveedorCO']['ID']) || (string) $_SESSION['datos_proveedorCO']['ID'] !== (string) $id) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido validar el proveedor seleccionado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            return;
        }

        unset($_SESSION['datos_proveedorCO']);
        if (empty($_SESSION['datos_proveedorCO'])) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Proveedor removido!",
                "Texto" => "Los datos del Proveedor fueron removidos correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido remover los datos del Proveedor",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /**fin controlador */

    /**controlador buscar articulo */
    public function buscar_articulo_controlador()
    {
        // BUSCAR ARTÍCULO (HTML)
        if (isset($_POST['buscar_articuloCO'])) {
            $articulo = mainModel::limpiar_string($_POST['buscar_articuloCO']);
            if ($articulo == "") return '<div class="alert alert-warning">Debes introducir código o descripción</div>';

            $datos_articuloPre = mainModel::ejecutar_consulta_simple("
                SELECT a.*
                FROM articulos a
                WHERE (a.codigo LIKE '%$articulo%' OR a.desc_articulo LIKE '%$articulo%')
                  AND a.estado = 1
                  AND a.tipo IN ('producto', 'insumo')
                ORDER BY a.desc_articulo DESC
                LIMIT 15
            ");

            if ($datos_articuloPre->rowCount() >= 1) {
                $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><thead class="thead-light text-center">
                        <tr>
                            <th>Artículo</th>
                            <th style="width:100px;">Cantidad</th>
                            <th style="width:100px;">Precio</th>
                            <th>Acción</th>
                        </tr>
                    </thead><tbody>';
                foreach ($datos_articuloPre->fetchAll() as $rows) {
                    $tabla .= '<tr class="text-center">
                    <td>' . $rows['codigo'] . ' - ' . $rows['desc_articulo'] . '</td>
                    
                    <!-- Cantidad -->
                    <td style="width:100px;">
                        <input type="number" id="cantidad_' . $rows['id_articulo'] . '" class="form-control form-control-sm" value="1" min="0.01" step="0.01">
                    </td>

                    <!-- Precio -->
                    <td style="width:100px;">
                        <input type="number" id="precio_' . $rows['id_articulo'] . '" class="form-control form-control-sm" step="0.01" min="0" value="0">
                    </td>

                    <!-- Botón agregar -->
                    <td>
                        <button type="button" class="btn btn-primary btn-sm" onclick="agregar_articuloCO(' . $rows['id_articulo'] . ')">
                            <i class="fas fa-plus-circle"></i>
                        </button>
                    </td>
                </tr>';
                }
                $tabla .= '</tbody></table></div>';
                return $tabla;
            } else return '<div class="alert alert-warning">No se encontraron artículos que coincidan</div>';
        }
    }
    /**controlador buscador articulo */

    /**controlador buscador articulo */
    public function articulo_controlador()
    {
        // AGREGAR ARTÍCULO
        if (isset($_POST['id_agregar_articuloCO'])) {

            if (empty($_SESSION['datos_proveedorCO']['ID'])) {
                die(json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Proveedor requerido",
                    "Texto"  => "Debe seleccionar un proveedor antes de agregar articulos",
                    "Tipo"   => "warning"
                ]));
            }

            // Limpiar y obtener datos del POST
            $id = mainModel::limpiar_string($_POST['id_agregar_articuloCO']);
            $cantidad = isset($_POST['detalle_cantidad']) ? floatval($_POST['detalle_cantidad']) : 0;
            $precio   = isset($_POST['detalle_precio']) ? floatval($_POST['detalle_precio']) : 0;

            // Validaciones
            if ($cantidad <= 0) {
                die(json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error!",
                    "Texto"  => "Cantidad inválida",
                    "Tipo"   => "error"
                ]));
            }

            if ($precio < 0) {
                die(json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error!",
                    "Texto"  => "Precio inválido",
                    "Tipo"   => "error"
                ]));
            }

            // Consulta segura al artículo
            try {
                $sqlDetalle = mainModel::ejecutar_consulta_simple("
                SELECT a.id_articulo, a.desc_articulo, a.codigo,
                       ti.idiva, ti.tipo_impuesto_descri, ti.ratevalueiva, ti.divisor
                FROM articulos a
                INNER JOIN tipo_impuesto ti ON ti.idiva = a.idiva
                WHERE a.id_articulo = '$id'
                  AND a.estado = 1
                  AND a.tipo IN ('producto', 'insumo')
            ");
            } catch (PDOException $e) {
                die(json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error BD",
                    "Texto"  => $e->getMessage(),
                    "Tipo"   => "error"
                ]));
            }

            if ($sqlDetalle->rowCount() <= 0) {
                die(json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error!",
                    "Texto"  => "No se encontró el artículo",
                    "Tipo"   => "error"
                ]));
            }

            $detalle = $sqlDetalle->fetchAll();

            // Inicializar sesión si no existe
            if (!isset($_SESSION['Cdatos_articuloCO'])) {
                $_SESSION['Cdatos_articuloCO'] = [];
            }

            // Revisar si ya está agregado
            if (isset($_SESSION['Cdatos_articuloCO'][$id])) {
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Ocurrió un error inesperado!",
                    "Texto"  => "El artículo que intenta agregar ya se encuentra agregado",
                    "Tipo"   => "error"
                ];
            } else {
                foreach ($detalle as $row) {
                    $subtotal = $cantidad * $precio;
                    $iva = ($row['divisor'] != 0) ? ($subtotal / $row['divisor']) : 0;

                    // Guardar en sesión usando el ID del artículo como key
                    $_SESSION['Cdatos_articuloCO'][$row['id_articulo']] = [
                        "ID"          => $row['id_articulo'],
                        "codigo"      => $row['codigo'],
                        "descripcion" => $row['desc_articulo'],
                        "cantidad"    => $cantidad,
                        "cantidad_facturada" => $cantidad,
                        "precio"      => $precio,
                        "subtotal"    => $subtotal,
                        "tipo_iva"    => $row['idiva'],
                        "iva_descri"  => $row['tipo_impuesto_descri'],
                        "iva"         => $iva,
                        "ratevalueiva" => $row['ratevalueiva'],
                        "divisor"     => $row['divisor']
                    ];
                }

                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Artículo agregado!",
                    "Texto"  => "El artículo ha sido agregado correctamente",
                    "Tipo"   => "success"
                ];
            }

            echo json_encode($alerta);
            exit();
        }
    }
    /**fin controlador */

    /** Controlador paginar compras */
    public function paginador_compra_controlador($pagina, $registros, $url, $busqueda1, $busqueda2, $nro_factura = '', $razon_social = '', $orden = 'fecha', $direccion = 'DESC')
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $busqueda1 = mainModel::limpiar_string($busqueda1);
        $busqueda2 = mainModel::limpiar_string($busqueda2);
        $nro_factura  = mainModel::limpiar_string($nro_factura);
        $razon_social = mainModel::limpiar_string($razon_social);
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
        $filtros = [];

        $busquedaPorFecha = !empty($busqueda1) && !empty($busqueda2);
        if ($busquedaPorFecha) {
            $filtros[] = [
                "campo" => "co.fecha_creacion",
                "tipo"  => "DATE_RANGE",
                "desde" => $busqueda1,
                "hasta" => $busqueda2
            ];
        } else {
            $filtros[] = [
                "campo" => "co.estado",
                "tipo"  => "!=",
                "valor" => 0
            ];
        }

        if ($nro_factura != "") {
            $filtros[] = [
                "campo" => "co.nro_factura",
                "tipo"  => "=",
                "valor" => $nro_factura
            ];
        }

        if ($razon_social != "") {
            $filtros[] = [
                "campo" => "p.razon_social",
                "tipo"  => "LIKE",
                "valor" => $razon_social
            ];
        }

        $columnasOrdenSql = [
            'fecha' => 'co.fecha_creacion',
            'estado' => 'co.estado'
        ];

        $ordenamiento = mainModel::preparar_ordenamiento($orden, $direccion, $columnasOrdenSql, 'fecha', 'DESC');
        $orden = $ordenamiento['orden'];
        $direccion = $ordenamiento['direccion'];
        $filtrosSQL = mainModel::construirFiltros($filtros);
        $resultado = compraModelo::listar_compras_modelo($inicio, $registros, $filtrosSQL, "ORDER BY " . $ordenamiento['sql'] . ", co.idcompra_cabecera DESC");

        $datos = $resultado['datos'];
        $total = $resultado['total'];

        $Npaginas = ceil($total / $registros);

        $tabla .= '<div class="table-responsive">
					<table class="table table-dark table-sm">
						<thead>
							<tr class="text-center roboto-medium">
								<th>#</th>
                                <th>PROVEEDOR</th>
                                <th>FACTURA</th>
                                <th>' . mainModel::link_orden_tabla($url, 'fecha', 'FECHA', $orden, $direccion, 'compra_orden', 'compra_direccion') . '</th>
                                <th>TOTAL COMPRA</th>
                                <th>CARGADO POR</th>
                                <th>' . mainModel::link_orden_tabla($url, 'estado', 'ESTADO', $orden, $direccion, 'compra_orden', 'compra_direccion') . '</th>
                                <th>DETALLE</th>';
        $puedeAnular = mainModel::tienePermiso('compra.anular');

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
                switch ($rows['estadoCO']) {
                    case 1:
                        $estadoBadge = '<span class="badge bg-primary">Activo</span>';
                        break;
                    case 3:
                        $estadoBadge = '<span class="badge bg-warning text-dark">Con diferencia</span>';
                        break;
                    case 4:
                        $estadoBadge = '<span class="badge bg-info text-dark">Regularizada con NC</span>';
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
								<td>' . $rows['nro_factura'] . '</td>
								<td>' . date("d-m-Y", strtotime($rows['fecha_factura'])) . '</td>
								<td>' . number_format($rows['total_compra'], 0, ',', '.') . '</td>
                                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                                <td>' . $estadoBadge . '</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" onclick="verDetalleCompra(\'' . mainModel::encryption($rows['idcompra_cabecera']) . '\')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>';
                if ($puedeAnular) {
                    $tabla .= '<td>
									<form class="FormularioAjax" action="' . SERVERURL . 'ajax/compraAjax.php" method="POST" data-form="delete" data-anulacion="true" data-anulacion-titulo="Anular compra" autocomplete="off" action="">
                                    <input type="hidden" name="compra_id_del" value=' . mainModel::encryption($rows['idcompra_cabecera']) . '>
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
            $colspan = $puedeAnular ? 9 : 8;
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

    public function detalle_compra_controlador()
    {
        if (!mainModel::tienePermiso('compra.ver')) {
            return json_encode([
                'status' => 'error',
                'html' => '<div class="alert alert-danger mb-0">Acceso no autorizado</div>'
            ]);
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        $id = (int) mainModel::limpiar_string(mainModel::decryption($_POST['detalle_compra'] ?? ''));
        $sucursal = (int) ($_SESSION['nick_sucursal'] ?? 0);

        if ($id <= 0 || $sucursal <= 0) {
            return json_encode([
                'status' => 'error',
                'html' => '<div class="alert alert-warning mb-0">No se pudo validar la compra solicitada.</div>'
            ]);
        }

        $datos = compraModelo::detalle_compra_modelo($id, $sucursal);
        if (!$datos['cabecera']) {
            return json_encode([
                'status' => 'error',
                'html' => '<div class="alert alert-warning mb-0">No se encontro la compra en la sucursal activa.</div>'
            ]);
        }

        $cab = $datos['cabecera'];
        $libro = $datos['libro'] ?: [];
        $cuentas = $datos['cuentas'] ?: [];
        $estadoTexto = ['0' => 'Anulado', '1' => 'Activo', '3' => 'Con diferencia', '4' => 'Regularizada con NC'];
        $total = 0;

        $html = '
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Compra:</strong> #' . (int)$cab['idcompra_cabecera'] . '<br>
                    <strong>Factura:</strong> ' . htmlspecialchars($cab['nro_factura'], ENT_QUOTES, 'UTF-8') . '<br>
                    <strong>Proveedor:</strong> ' . htmlspecialchars($cab['razon_social'], ENT_QUOTES, 'UTF-8') . '<br>
                    <strong>RUC:</strong> ' . htmlspecialchars($cab['ruc'], ENT_QUOTES, 'UTF-8') . '
                </div>
                <div class="col-md-6">
                    <strong>Fecha factura:</strong> ' . (!empty($cab['fecha_factura']) ? date('d/m/Y', strtotime($cab['fecha_factura'])) : '-') . '<br>
                    <strong>Timbrado:</strong> ' . htmlspecialchars($cab['nro_timbrado'] ?? '-', ENT_QUOTES, 'UTF-8') . '<br>
                    <strong>Condicion:</strong> ' . htmlspecialchars(ucfirst($cab['condicion'] ?? '-'), ENT_QUOTES, 'UTF-8') . '<br>
                    <strong>Estado:</strong> ' . ($estadoTexto[(string)$cab['estado']] ?? 'Desconocido') . '<br>
                    <strong>Usuario:</strong> ' . htmlspecialchars(trim($cab['usu_nombre'] . ' ' . $cab['usu_apellido']), ENT_QUOTES, 'UTF-8') . '
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>Codigo</th>
                            <th>Articulo</th>
                            <th>Facturada</th>
                            <th>Recibida</th>
                            <th>Diferencia</th>
                            <th>Precio</th>
                            <th>IVA</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($datos['detalle'] as $row) {
            $total += (float)$row['subtotal'];
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . htmlspecialchars($row['desc_articulo'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td class="text-right">' . number_format((float)$row['cantidad_facturada'], 2, ',', '.') . '</td>
                    <td class="text-right">' . number_format((float)$row['cantidad_recibida'], 2, ',', '.') . '</td>
                    <td class="text-right">' . number_format(((float)$row['cantidad_facturada'] - (float)$row['cantidad_recibida']), 2, ',', '.') . '</td>
                    <td class="text-right">' . number_format((float)$row['precio_unitario'], 0, ',', '.') . '</td>
                    <td class="text-right">' . number_format((float)$row['ivaPro'], 0, ',', '.') . '</td>
                    <td class="text-right">' . number_format((float)$row['subtotal'], 0, ',', '.') . '</td>
                </tr>';
        }

        $html .= '
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" class="text-right">Total detalle</th>
                            <th class="text-right">Gs. ' . number_format($total, 0, ',', '.') . '</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <strong>Libro de compras</strong><br>
                    Exenta: Gs. ' . number_format((float)($libro['exenta'] ?? 0), 0, ',', '.') . '<br>
                    Gravada 5%: Gs. ' . number_format((float)($libro['gravada_5'] ?? 0), 0, ',', '.') . ' | IVA 5%: Gs. ' . number_format((float)($libro['iva_5'] ?? 0), 0, ',', '.') . '<br>
                    Gravada 10%: Gs. ' . number_format((float)($libro['gravada_10'] ?? 0), 0, ',', '.') . ' | IVA 10%: Gs. ' . number_format((float)($libro['iva_10'] ?? 0), 0, ',', '.') . '<br>
                    Total libro: Gs. ' . number_format((float)($libro['total'] ?? 0), 0, ',', '.') . '
                </div>
                <div class="col-md-6">
                    <strong>Cuentas a pagar</strong><br>
                    Cuotas activas: ' . number_format((float)($cuentas['cuotas'] ?? 0), 0, ',', '.') . '<br>
                    Monto: Gs. ' . number_format((float)($cuentas['monto'] ?? 0), 0, ',', '.') . '<br>
                    Saldo: Gs. ' . number_format((float)($cuentas['saldo'] ?? 0), 0, ',', '.') . '
                </div>
            </div>';

        return json_encode(['status' => 'ok', 'html' => $html]);
    }

    /**Controlador anular compra */
    public function anular_compra_controlador()
    {
        $id = mainModel::decryption($_POST['compra_id_del']);
        $id = mainModel::limpiar_string($id);
        $motivo = trim(mainModel::limpiar_string($_POST['observacion_anulacion'] ?? ''));

        if ($motivo === '') {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Motivo requerido",
                "Texto" => "Debe ingresar la observacion o motivo de anulacion",
                "Tipo" => "warning"
            ]);
            exit();
        }

        // Verificar que la compra exista
        $check_compra = mainModel::ejecutar_consulta_simple(
            "SELECT idcompra_cabecera, idOcompra, estado FROM compra_cabecera WHERE idcompra_cabecera = '$id' AND id_sucursal = '" . $_SESSION['nick_sucursal'] . "'"
        );
        if ($check_compra->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La recepcion de compra que intenta anular no existe en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Verificar que no esté anulada
        $compra = $check_compra->fetch(PDO::FETCH_ASSOC);
        if ((int)$compra['estado'] === 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La recepcion de compra que intenta anular ya se encuentra anulada",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (!mainModel::tienePermiso('compra.anular')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Advertencia!",
                "Texto" => "No posee los permisos necesarios para realizar esta acción",
                "Tipo" => "error"
            ]);
        }

        $usuario = $_SESSION['id_str'];
        $id_sucursal = $_SESSION['nick_sucursal']; // Ajustar si aplica

        $pdo = null;

        try {
            // Iniciar transacción
            $pdo = mainModel::conectar();
            $pdo->beginTransaction();

            // 1) Anular compra cabecera
            compraModelo::anular_compra_modelo([
                "idcompra_cabecera" => $id,
                "updatedby" => $usuario,
                "idsucursal" => $id_sucursal,
                "conexion" => $pdo
            ]);

            // 2) Obtener detalles de la compra
            $detalles = compraModelo::datos_detalle_compra_modelo($id, $id_sucursal, $pdo)->fetchAll(PDO::FETCH_ASSOC);

            // 3) Descontar stock y generar movimientos
            foreach ($detalles as $d) {
                mainModel::registrar_movimiento_stock_modelo($pdo, [
                    "id_sucursal" => $id_sucursal,
                    "tipo" => "ANULACION COMPRA",
                    "id_articulo" => $d['id_articulo'],
                    "cantidad" => $d['cantidad_recibida'],
                    "precio_venta" => 0,
                    "costo" => $d['precio_unitario'],
                    "nro_ticket" => "ANUL_COMPRA# " . $id,
                    "pos" => null,
                    "usuario" => $usuario,
                    "signo" => -1,
                    "referencia" => "ANUL_COMPRA# " . $id
                ]);
            }

            // 4) Revertir cantidades pendientes de la OC si corresponde
            if (!empty($compra['idOcompra'])) {
                compraModelo::revertir_oc_compra_modelo([
                    "idorden_compra"    => $compra['idOcompra'],
                    "idcompra_cabecera" => $id,
                    "updatedby"         => $usuario,
                    "id_sucursal"       => $id_sucursal,
                    "conexion"          => $pdo
                ]);
            }

            // 5) Anular cuentas a pagar
            compraModelo::anular_cuentas_pagar_modelo($id, $id_sucursal, $pdo);

            // 6) Anular libro de compras
            compraModelo::anular_libro_compra_modelo($id, $id_sucursal, $pdo);
            mainModel::registrar_anulacion_auditoria_modelo($pdo, [
                'modulo' => 'compra',
                'tabla_afectada' => 'compra_cabecera',
                'id_registro' => $id,
                'id_sucursal' => $id_sucursal,
                'estado_anterior' => (string)$compra['estado'],
                'estado_nuevo' => '0',
                'motivo' => $motivo,
                'usuario_anula' => $usuario,
                'referencia' => 'COMPRA #' . $id
            ]);

            // Confirmar transacción
            $pdo->commit();

            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Compra Anulada!",
                "Texto" => "La recepcion de compra ha sido anulada correctamente",
                "Tipo" => "success"
            ];
        } catch (Exception $e) {
            if ($pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack();
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No se pudo anular la recepcion de compra seleccionada: " . $e->getMessage(),
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
    }
    /**fin controlador */
}
