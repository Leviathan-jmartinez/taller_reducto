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
        $datoscompra = mainModel::ejecutar_consulta_simple("SELECT SQL_CALC_FOUND_ROWS oc.idorden_compra as idorden_compra, oc.idproveedores as idproveedores, 
        oc.id_usuario as id_usuario, oc.fecha as fecha, oc.estado as estodoOC, oc.fecha_entrega as fecha_entrega, oc.updated as updated, 
        oc.updatedby as updatedby, p.idproveedores as idproveedores, p.id_ciudad as id_ciudad, p.razon_social as razon_social, 
        p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, p.estado as estadoPro, 
        u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick
        from orden_compra oc 
        INNER JOIN proveedores p on p.idproveedores = oc.idproveedores 
        INNER JOIN usuarios u on u.id_usuario = oc.id_usuario
        where (oc.idorden_compra like '%$ordencompa%' or p.razon_social like '%$ordencompa%' or p.ruc like '%$ordencompa%') and oc.estado = '1'
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
        WHERE oc.idorden_compra = '$idoccompra'");
        $cabecera = $sqlCabecera->fetch();
        if ($cabecera) {
            $_SESSION['datos_proveedorOC'] = [
                "ID" => $cabecera['idproveedores'],
                "RAZON" => $cabecera['razon_social'],
                "RUC" => $cabecera['ruc']
            ];
        }

        // 2️⃣ Detalle del pedido (artículos)
        $sqlDetalle = mainModel::ejecutar_consulta_simple("
        SELECT ocd.id_articulo, ocd.cantidad_pendiente, a.desc_articulo, a.codigo, ocd.precio_unitario, ti.tipo_impuesto_descri, ti.ratevalueiva, ti.divisor
        FROM orden_compra_detalle ocd
        INNER JOIN articulos a ON a.id_articulo = ocd.id_articulo
        INNER JOIN tipo_impuesto ti ON ti.idiva = a.idiva
        WHERE ocd.idorden_compra = '$idoccompra'");
        $detalle = $sqlDetalle->fetchAll();

        $_SESSION['Cdatos_articuloOC'] = $_SESSION['Cdatos_articuloOC'] ?? [];
        foreach ($detalle as $i => $row) {

            // Si ya existe en la sesión (modificado), no sobreescribas cantidad y precio
            $cantidad = $_SESSION['Cdatos_articuloOC'][$i]['cantidad'] ?? $row['cantidad_pendiente'];
            $precio   = $_SESSION['Cdatos_articuloOC'][$i]['precio'] ?? $row['precio_unitario'];
            $subtotal = $cantidad * $precio;
            if ($row['divisor'] == 0) {
                $iva = 0;
            } else {
                $iva = $subtotal / $row['divisor'];
            }
            $_SESSION['Cdatos_articuloOC'][$i] = [
                "ID" => $row['id_articulo'],
                "codigo" => $row['codigo'],
                "descripcion" => $row['desc_articulo'],
                "cantidad" => $cantidad,
                "precio" => $precio,
                "subtotal" => $subtotal,
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
        if (empty($_SESSION['Cdatos_articuloOC'])) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No hay detalles para guardar la compra.",
                "Tipo" => "error"
            ];
        }

        /* ===============================
       DATOS DE LA CABECERA
    ================================= */
        $datosCab = [
            "proveedor"           => $_SESSION['datos_proveedorOC']['ID'],
            "usuario"             => $_SESSION['id_str'],
            "nro_factura"         => $_POST['factura_numero'],
            "fecha_factura"       => $_POST['fecha_emision'],
            "timbrado"            => $_POST['timbrado'],
            "vencimiento_timbrado" => $_POST['vencimiento_timbrado'],
            "estado"              => "1",
            "total"               => $_POST['total_factura'],
            "condicion"           => $_POST['condicion'],
            "intervalo"           => $_POST['intervalo'],
            "idoc"                => $_SESSION['id_oc_seleccionado']
        ];

        $guardarCab = compraModelo::insertar_compra_cabecera_modelo($datosCab);

        if ($guardarCab["stmt"]->rowCount() < 1) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo guardar la cabecera de la compra.",
                "Tipo" => "error"
            ];
        }

        $idcab = $guardarCab["last_id"];

        /* ===============================
       GUARDAR DETALLES + ACTUALIZAR STOCK
    ================================= */
        $errores = 0;
        $iddeposito = 1;

        foreach ($_SESSION['Cdatos_articuloOC'] as $item) {
            if ($item['cantidad'] <= 0) continue;

            /* Guardar detalle */
            $detalle = [
                "idcab"       => $idcab,
                "id_articulo" => $item['ID'],
                "precio"      => $item['precio'],
                "cantidad"    => $item['cantidad'],
                "subtotal"    => $item['subtotal'],
                "iva"         => $item['iva']
            ];

            $guardarDet = compraModelo::insertar_compra_detalle_modelo($detalle);
            if ($guardarDet->rowCount() < 1) {
                $errores++;
                continue;
            }

            /* ================================================
           INSERTAR MOVIMIENTO STOCK
        =================================================== */
            $mov = [
                "local"        => $_SESSION['nick_sucursal'],
                "tipo"         => "RECEPCION COMPRA",
                "producto"     => $item['ID'],
                "cantidad"     => $item['cantidad'],
                "precioVenta"  => 0,
                "costo"        => $item['precio'],
                "nroTicket"    => $_POST['factura_numero'],
                "pos"          => null,
                "usuario"      => $_SESSION['id_str'],
                "signo"        => 1,
                "referencia"   => $idcab
            ];

            compraModelo::agregar_movimiento_stock($mov);

            // SUMAR al stock actual
            $stockActual = compraModelo::obtener_stock_actual_modelo($iddeposito, $item['ID']);
            $nuevoStock = $stockActual + $item['cantidad'];

            $datos_stock = [
                "iddeposito"                => $iddeposito,
                "id_articulo"               => $item['ID'],
                "stockDisponible"           => $nuevoStock,
                "stockUltActualizacion"     => date("Y-m-d H:i:s"),
                "stockUsuActualizacion"     => $_SESSION['id_str'],
                "stockultimoIdActualizacion" => $idcab
            ];

            compraModelo::upsert_stock_modelo($datos_stock);
        }

        /* ===============================
       GENERAR CUENTAS A PAGAR
    ================================= */
        $condicion = $_POST['condicion'];
        $intervalo = intval($_POST['intervalo']);
        $cuotas    = intval($_POST['cuotas']);
        $total     = floatval($_POST['total_factura']);

        if ($condicion === 'contado') $cuotas = 1;
        $monto_cuota = round($total / $cuotas, 2);
        $errores_cuentas = 0;

        for ($i = 1; $i <= $cuotas; $i++) {
            $fecha_vencimiento = date('Y-m-d', strtotime("+" . ($intervalo * $i) . " days"));

            $datos_cuenta = [
                "idcompra"        => $idcab,
                "monto"           => $monto_cuota,
                "saldo"           => $monto_cuota,
                "cuotas"          => $i,
                "fecha_vencimiento" => $fecha_vencimiento,
                "estado"          => 1
            ];

            $guardarCuenta = compraModelo::insertar_cuentas_a_pagar_modelo($datos_cuenta);
            if ($guardarCuenta->rowCount() < 1) $errores_cuentas++;
        }

        /* ERROR PARCIAL EN DETALLES */
        if ($errores > 0 || $errores_cuentas > 0) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Error parcial",
                "Texto" => "La compra se guardó, pero algunos detalles o cuentas a pagar no pudieron insertarse.",
                "Tipo" => "warning"
            ];
        }

        /* LIMPIAR SESIÓN */
        unset($_SESSION['Cdatos_articuloOC']);
        unset($_SESSION['datos_proveedorOC']);
        unset($_SESSION['id_oc_seleccionado']);

        return [
            "Alerta" => "recargar",
            "Titulo" => "Compra registrada",
            "Texto" => "La compra se guardó correctamente.",
            "Tipo" => "success"
        ];
    }



    /**controlador buscador proveedor */
    public function buscar_proveedor_controlador()
    {
        $proveedor  = mainModel::limpiar_string($_POST['buscar_proveedorPre']);

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
                                <button type="button" class="btn btn-primary" onclick="agregar_proveedorPre(' . $rows['idproveedores'] . ')"><i class="fas fa-user-plus"></i></button>
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
        $id  = mainModel::limpiar_string($_POST['id_agregar_proveedorPre']);

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
        session_start(['name' => 'STR']);
        unset($_SESSION['Sdatos_proveedorPre']);
        if (!isset($_SESSION['Sdatos_proveedorPre'])) {
            $_SESSION['Sdatos_proveedorPre'] = [
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
        session_start(['name' => 'STR']);
        unset($_SESSION['datos_proveedorPre']);
        if (empty($_SESSION['datos_proveedorPre'])) {
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
        session_start(['name' => 'STR']);
        if (isset($_POST['buscar_articuloPre'])) {
            $articulo = mainModel::limpiar_string($_POST['buscar_articuloPre']);
            if ($articulo == "") return '<div class="alert alert-warning">Debes introducir código o descripción</div>';

            if (!isset($_SESSION['Sdatos_proveedorPre']['ID'])) {
                return '<div class="alert alert-danger">No se ha seleccionado un proveedor</div>';
                exit();
            }
            $id_proveedor = $_SESSION['Sdatos_proveedorPre']['ID'];
            $datos_articuloPre = mainModel::ejecutar_consulta_simple("SELECT * FROM articulos WHERE (codigo like '%$articulo%' OR desc_articulo like '%$articulo%') AND estado=1 AND idproveedores='$id_proveedor' ORDER BY desc_articulo DESC");

            if ($datos_articuloPre->rowCount() >= 1) {
                $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>';
                foreach ($datos_articuloPre->fetchAll() as $rows) {
                    $tabla .= '<tr class="text-center">
                    <td>' . $rows['codigo'] . ' - ' . $rows['desc_articulo'] . '</td>
                    
                    <!-- Cantidad -->
                    <td style="width:100px;">
                        <input type="number" id="cantidad_' . $rows['id_articulo'] . '" class="form-control form-control-sm" value="1" min="1">
                    </td>

                    <!-- Precio -->
                    <td style="width:100px;">
                        <input type="number" id="precio_' . $rows['id_articulo'] . '" class="form-control form-control-sm" step="0.01" min="0">
                    </td>

                    <!-- Botón agregar -->
                    <td>
                        <button type="button" class="btn btn-primary btn-sm" onclick="agregar_articuloPre(' . $rows['id_articulo'] . ')">
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
        session_start(['name' => 'STR']);
        // AGREGAR ARTÍCULO
        if (isset($_POST['id_agregar_articuloPre'])) {

            $id = mainModel::limpiar_string($_POST['id_agregar_articuloPre']);
            $cantidad = mainModel::limpiar_string($_POST['detalle_cantidad']);
            $precio = mainModel::limpiar_string($_POST['detalle_precio']); // <-- nuevo

            // Validaciones
            $check_articulo = mainModel::ejecutar_consulta_simple("SELECT * FROM articulos WHERE id_articulo='$id' AND estado=1");
            if ($check_articulo->rowCount() <= 0)
                die(json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "No se encontró el artículo", "Tipo" => "error"]));

            $campos = $check_articulo->fetch();

            if ($cantidad == "" || !is_numeric($cantidad) || intval($cantidad) <= 0)
                die(json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "Cantidad inválida", "Tipo" => "error"]));

            if ($precio == "" || !is_numeric($precio) || floatval($precio) < 0)
                die(json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "Precio inválido", "Tipo" => "error"]));

            $cantidad = intval($cantidad);
            $precio = floatval($precio);
            $subtotal = $cantidad * $precio; // <-- opcional, para mostrar o guardar

            if (isset($_SESSION['Sdatos_articuloPre'][$id])) {
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El artículo que intenta agregar ya se encuentra agregado",
                    "Tipo" => "error"
                ];
            } else {
                $_SESSION['Sdatos_articuloPre'][$id] = [
                    "ID" => $campos['id_articulo'],
                    "codigo" => $campos['codigo'],
                    "descripcion" => $campos['desc_articulo'],
                    "cantidad" => $cantidad,
                    "precio" => $precio,
                    "subtotal" => $subtotal
                ];
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Artículo agregado!",
                    "Texto" => "El artículo ha sido agregado",
                    "Tipo" => "success"
                ];
            }
            echo json_encode($alerta);
            exit();
        }
    }
    /**fin controlador */

    /**controlador agregar presupuesto */
    public function agregar_presupuesto_controlador()
    {
        session_start(['name' => 'STR']);
        $fecha_venc = $_POST['fecha_vencimientoPre'] ?? null;

        if ($_SESSION['tipo_presupuesto'] == "sin_pedido") {
            if (empty($_SESSION['Sdatos_proveedorPre'])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error!",
                    "Texto" => "No has seleccionado ningun proveedor",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
            if (empty($_SESSION['Sdatos_articuloPre'])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error!",
                    "Texto" => "No has seleccionado ningun artículo para el presupuesto",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if (empty($fecha_venc) || $fecha_venc == null) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error!",
                    "Texto" => "Debes seleccionar la fecha de vencimiento",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }


            /** Insertar cabecera */
            $datos_presu_agg = [
                "usuario"   => $_SESSION['id_str'],
                "proveedor" => $_SESSION['Sdatos_proveedorPre']['ID'],
                "total" => $_SESSION['total_pre'],
                "fecha_venc" => $fecha_venc
            ];

            $idpresupuestoCab = presupuestoModelo::agregar_presupuestoC_modelo1($datos_presu_agg);

            if ($idpresupuestoCab <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado!",
                    "Texto" => "No pudimos registrar la cabecera del pedido",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /** Insertar detalles */
            $errores_detalles = 0;
            foreach ($_SESSION['Sdatos_articuloPre'] as $article) {

                $detalle_reg = [
                    "presupuestoid" => $idpresupuestoCab,
                    "articulo" => $article['ID'],
                    "cantidad" => $article['cantidad'],
                    "precio" => $article['precio'],
                    "subtotal" => $article['subtotal']
                ];

                $detalleInsert = presupuestoModelo::agregar_presupuestoD_modelo($detalle_reg);

                if ($detalleInsert->rowCount() != 1) {
                    $errores_detalles++;
                }
            }

            if ($errores_detalles > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error parcial",
                    "Texto" => "El presupuesto se creó, pero algunos artículos no se guardaron",
                    "Tipo" => "warning"
                ];
            } else {
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Pedido guardado!",
                    "Texto" => "El presupuesto se registró correctamente",
                    "Tipo" => "success"
                ];
                unset($_SESSION['tipo_presupuesto']);
                unset($_SESSION['Sdatos_proveedorPre']);
                unset($_SESSION['Sdatos_articuloPre']);
            }
            echo json_encode($alerta);
        } elseif ($_SESSION['tipo_presupuesto'] == "con_pedido") {
            if (empty($_SESSION['Cdatos_proveedorPre'])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error!",
                    "Texto" => "No has seleccionado ningun proveedor",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
            if (empty($_SESSION['Cdatos_articuloPre'])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error!",
                    "Texto" => "No has seleccionado ningun artículo para el presupuesto",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if (empty($fecha_venc) || $fecha_venc == null) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error!",
                    "Texto" => "Debes seleccionar la fecha de vencimiento",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /** Insertar cabecera */
            $datos_presu_agg = [
                "idPedido"   => $_SESSION['id_pedido_seleccionado'],
                "usuario"   => $_SESSION['id_str'],
                "proveedor" => $_SESSION['Cdatos_proveedorPre']['ID'],
                "total" => $_SESSION['total_pre'],
                "fecha_venc" => $fecha_venc
            ];

            $datos_update_pedido = [
                "idpedido_cabecera" => $_SESSION['id_pedido_seleccionado'],
                "updatedby" => $_SESSION['id_str']
            ];

            $updatePedido = presupuestoModelo::actualizar_pedido_modelo($datos_update_pedido);
            $idpresupuestoCab = presupuestoModelo::agregar_presupuestoC_modelo2($datos_presu_agg);

            if ($idpresupuestoCab <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado!",
                    "Texto" => "No pudimos registrar la cabecera del pedido",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /** Insertar detalles */
            $errores_detalles = 0;
            foreach ($_SESSION['Cdatos_articuloPre'] as $article) {

                $detalle_reg = [
                    "presupuestoid" => $idpresupuestoCab,
                    "articulo" => $article['ID'],
                    "cantidad" => $article['cantidad'],
                    "precio" => $article['precio'],
                    "subtotal" => $article['subtotal']
                ];

                $detalleInsert = presupuestoModelo::agregar_presupuestoD_modelo($detalle_reg);

                if ($detalleInsert->rowCount() != 1) {
                    $errores_detalles++;
                }
            }

            if ($errores_detalles > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error parcial",
                    "Texto" => "El presupuesto se creó, pero algunos artículos no se guardaron",
                    "Tipo" => "warning"
                ];
            } else {
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Pedido guardado!",
                    "Texto" => "El presupuesto se registró correctamente",
                    "Tipo" => "success"
                ];
            }
            unset($_SESSION['Cdatos_proveedorPre']);
            unset($_SESSION['Cdatos_articuloPre']);
            unset($_SESSION['tipo_presupuesto']);
            echo json_encode($alerta);
        }
    }
    /**fin controlador */


    /**Controlador paginar presupuestos */
    public function paginador_presupuestos_controlador($pagina, $registros, $privilegio, $url, $busqueda1, $busqueda2)
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
            $consulta = "SELECT  SQL_CALC_FOUND_ROWS pc.idpresupuesto_compra as idpresupuesto_compra, pc.id_usuario as id_usuario, pc.fecha as fecha, pc.estado as estadoPre, 
            pc.idproveedores as idproveedores, p.razon_social as razon_social, p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, 
            p.estado as estadoPro, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick, pc.updated as updated,
            pc.updatedby as updatedby
            FROM presupuesto_compra pc
            INNER JOIN proveedores p on p.idproveedores = pc.idproveedores
            INNER JOIN usuarios u on u.id_usuario = pc.id_usuario
            WHERE date(fecha) >= '$busqueda1' AND date(fecha) <='$busqueda2'
            ORDER BY fecha ASC LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT  SQL_CALC_FOUND_ROWS pc.idpresupuesto_compra as idpresupuesto_compra, pc.id_usuario as id_usuario, pc.fecha as fecha, pc.estado as estadoPre, 
            pc.idproveedores as idproveedores, p.razon_social as razon_social, p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, 
            p.estado as estadoPro, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick, pc.updated as updated,
            pc.updatedby as updatedby
            FROM presupuesto_compra pc
            INNER JOIN proveedores p on p.idproveedores = pc.idproveedores
            INNER JOIN usuarios u on u.id_usuario = pc.id_usuario
            WHERE pc.estado != 0
            ORDER BY pc.idpresupuesto_compra ASC LIMIT $inicio,$registros";
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
								<th>CÓDIGO PRESUPUESTO</th>
                                <th>PROVEEDOR</th>
                                <th>FECHA</th>
                                <th>CREADO POR</th>
                                <th>ESTADO</th>';
        if ($privilegio == 1 || $privilegio == 2) {
            $tabla .=           '<th>ELIMINAR</th>';
        }
        $tabla .= '
						</tr>
						</thead>
						<tbody>';
        if ($total >= 1 && $pagina <= $Npaginas) {
            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;
            foreach ($datos as $rows) {
                switch ($rows['estadoPre']) {
                    case 1:
                        $estadoBadge = '<span class="badge bg-primary">Pendiente</span>';
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
								<td>' . $rows['idpresupuesto_compra'] . '</td>
								<td>' . $rows['razon_social'] . '</td>
								<td>' . date("d-m-Y", strtotime($rows['fecha'])) . '</td>
                                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                                <td>' . $estadoBadge . '</td>';
                if ($privilegio == 1 || $privilegio == 2) {
                    $tabla .= '<td>
									<form class="FormularioAjax" action="' . SERVERURL . 'ajax/presupuestoAjax.php" method="POST" data-form="delete" autocomplete="off" action="">
                                    <input type="hidden" name="presupuesto_id_del" value=' . mainModel::encryption($rows['idpresupuesto_compra']) . '>
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

    /**Controlador anular presupuesto */
    public function anular_presupuesto_controlador()
    {
        $id = mainModel::decryption($_POST['presupuesto_id_del']);
        $id = mainModel::limpiar_string($id);

        $check_presupuesto = mainModel::ejecutar_consulta_simple("SELECT idpresupuesto_compra FROM presupuesto_compra WHERE idpresupuesto_compra = '$id'");
        if ($check_presupuesto->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El PRESUPUESTO que intenta anular no existe en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $check_presupuestoestado = mainModel::ejecutar_consulta_simple("SELECT idpresupuesto_compra FROM presupuesto_compra WHERE idpresupuesto_compra = '$id' AND estado = 2");
        if ($check_presupuestoestado->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El PRESUPUESTO que intenta anular se encuentra procesado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        session_start(['name' => 'STR']);
        if ($_SESSION['nivel_str'] > 2) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No tiene los permisos necesario para realizar esta operación",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $datos_presupuesto_del = [
            "updatedby" => $_SESSION['id_str'],
            "idpresupuesto_compra" => $id
        ];

        if (presupuestoModelo::anular_presupuesto_modelo($datos_presupuesto_del)) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Pedido Anulado!",
                "Texto" => "El PRESUPUESTO ha sido anulado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No se pudo anular el PRESUPUESTO seleccionado",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /**fin controlador */

    public function datos_presupuesto_controlador($tipo, $id)
    {
        $tipo  = mainModel::limpiar_string($tipo);

        $id  = mainModel::decryption($id);
        $id  = mainModel::limpiar_string($id);

        return presupuestoModelo::datos_presupuesto_modelo($tipo, $id);
    }
    /**fin controlador */
}
