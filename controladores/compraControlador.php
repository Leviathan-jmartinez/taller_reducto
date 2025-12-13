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
            $_SESSION['datos_proveedorCO'] = [
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

        $_SESSION['Cdatos_articuloCO'] = $_SESSION['Cdatos_articuloCO'] ?? [];
        foreach ($detalle as $i => $row) {

            // Si ya existe en la sesión (modificado), no sobreescribas cantidad y precio
            $cantidad = $_SESSION['Cdatos_articuloCO'][$i]['cantidad'] ?? $row['cantidad_pendiente'];
            $precio   = $_SESSION['Cdatos_articuloCO'][$i]['precio'] ?? $row['precio_unitario'];
            $subtotal = $cantidad * $precio;
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
        if (empty($_SESSION['Cdatos_articuloCO'])) {
            return [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No hay detalles para guardar la compra.",
                "Tipo" => "error"
            ];
        }

        $pdo = mainModel::conectar();
        $pdo->beginTransaction();

        try {
            /* ===============================
            DATOS DE LA CABECERA
        ================================= */
            $datosCab = [
                "proveedor"           => $_SESSION['datos_proveedorCO']['ID'],
                "usuario"             => $_SESSION['id_str'],
                "nro_factura"         => $_POST['factura_numero'],
                "fecha_factura"       => $_POST['fecha_emision'],
                "timbrado"            => $_POST['timbrado'],
                "vencimiento_timbrado" => $_POST['vencimiento_timbrado'],
                "estado"              => "1",
                "total"               => $_POST['total_factura'],
                "condicion"           => $_POST['condicion'],
                "intervalo"           => $_POST['intervalo'],
                "idoc"                => isset($_SESSION['id_oc_seleccionado']) ? $_SESSION['id_oc_seleccionado'] : null
            ];

            // Guardar cabecera
            $guardarCab = compraModelo::insertar_compra_cabecera_modelo($datosCab);
            if ($guardarCab["stmt"]->rowCount() < 1) {
                throw new Exception("No se pudo guardar la cabecera de la compra.");
            }
            $idcab = $guardarCab["last_id"];

            /* ===============================
            GUARDAR DETALLES + ACTUALIZAR STOCK
            ================================= */
            foreach ($_SESSION['Cdatos_articuloCO'] as $item) {
                if ($item['cantidad'] <= 0) continue;

                // Guardar detalle
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
                    throw new Exception("Error al guardar detalle del artículo ID " . $item['ID']);
                }

                // INSERTAR MOVIMIENTO STOCK
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
                $stockActual = compraModelo::obtener_stock_actual_modelo($_SESSION['nick_sucursal'], $item['ID']);
                $nuevoStock = $stockActual + $item['cantidad'];

                $datos_stock = [
                    "id_sucursal"                => $_SESSION['nick_sucursal'],
                    "id_articulo"               => $item['ID'],
                    "stockDisponible"           => $nuevoStock,
                    "stockUltActualizacion"     => date("Y-m-d H:i:s"),
                    "stockUsuActualizacion"     => $_SESSION['id_str'],
                    "stockultimoIdActualizacion" => $idcab
                ];
                compraModelo::upsert_stock_modelo($datos_stock);
            }

            /* ===============================
            ACTUALIZAR ORDEN DE COMPRA
            ================================= */
            if (!empty($datosCab['idoc'])) {
                compraModelo::actualizar_oc_modelo([
                    "idorden_compra" => $datosCab['idoc'],
                    "idcompra_cabecera" => $idcab,
                    "updatedby" => $_SESSION['id_str']
                ]);
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
                if ($guardarCuenta->rowCount() < 1) {
                    throw new Exception("Error al guardar cuenta a pagar cuota $i");
                }
            }

            // CONFIRMAR TRANSACCIÓN
            $pdo->commit();

            // LIMPIAR SESIÓN
            unset($_SESSION['Cdatos_articuloCO']);
            unset($_SESSION['datos_proveedorCO']);
            unset($_SESSION['id_oc_seleccionado']);

            return [
                "Alerta" => "recargar",
                "Titulo" => "Compra registrada",
                "Texto" => "La compra se guardó correctamente.",
                "Tipo" => "success"
            ];
        } catch (Exception $e) {
            $pdo->rollBack();
            return [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado!",
                "Texto" => $e->getMessage(),
                "Tipo" => "error"
            ];
        }
    }

    /**fin controlador */


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

            if (!isset($_SESSION['datos_proveedorCO']['ID'])) {
                return '<div class="alert alert-danger">No se ha seleccionado un proveedor</div>';
                exit();
            }
            $id_proveedor = $_SESSION['datos_proveedorCO']['ID'];
            $datos_articuloPre = mainModel::ejecutar_consulta_simple("SELECT * FROM articulos WHERE (codigo like '%$articulo%' OR desc_articulo like '%$articulo%') AND estado=1 AND idproveedores='$id_proveedor' ORDER BY desc_articulo DESC LIMIT 15");

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
                        <input type="number" id="cantidad_' . $rows['id_articulo'] . '" class="form-control form-control-sm" value="1" min="1">
                    </td>

                    <!-- Precio -->
                    <td style="width:100px;">
                        <input type="number" id="precio_' . $rows['id_articulo'] . '" class="form-control form-control-sm" step="0.01" min="0">
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
                       ti.tipo_impuesto_descri, ti.ratevalueiva, ti.divisor
                FROM articulos a
                INNER JOIN tipo_impuesto ti ON ti.idiva = a.idiva
                WHERE a.id_articulo = '$id'
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
                        "precio"      => $precio,
                        "subtotal"    => $subtotal,
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
    public function paginador_compra_controlador($pagina, $registros, $privilegio, $url, $busqueda1, $busqueda2)
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
            $consulta = "SELECT SQL_CALC_FOUND_ROWS co.idcompra_cabecera as idcompra_cabecera, co.id_usuario as id_usuario, co.fecha_creacion as fecha_creacion, co.estado as estadoCO, nro_factura AS nro_factura, condicion as condicion,
            co.fecha_factura as fecha_factura, total_compra AS total_compra, co.idproveedores as idproveedores, p.razon_social as razon_social, p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, 
            p.estado as estadoPro, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick, co.updated as updated,
            co.updatedby as updatedby
            FROM compra_cabecera co
            INNER JOIN proveedores p on p.idproveedores = co.idproveedores
            INNER JOIN usuarios u on u.id_usuario = co.id_usuario
            WHERE date(fecha_creacion) >= '$busqueda1' AND date(fecha_creacion) <='$busqueda2'
            ORDER BY fecha_creacion ASC LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS co.idcompra_cabecera as idcompra_cabecera, co.id_usuario as id_usuario, co.fecha_creacion as fecha_creacion, co.estado as estadoCO, nro_factura AS nro_factura, condicion as condicion,
            co.fecha_factura as fecha_factura, total_compra AS total_compra, co.idproveedores as idproveedores, p.razon_social as razon_social, p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, 
            p.estado as estadoPro, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick, co.updated as updated,
            co.updatedby as updatedby
            FROM compra_cabecera co
            INNER JOIN proveedores p on p.idproveedores = co.idproveedores
            INNER JOIN usuarios u on u.id_usuario = co.id_usuario
            WHERE oc.estado != 0
            ORDER BY pc.idcompra_cabecera ASC LIMIT $inicio,$registros";
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
                                <th>FACTURA</th>
                                <th>FECHA</th>
                                <th>TOTAL COMPRA</th>
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
                switch ($rows['estadoCO']) {
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
								<td>' . $rows['nro_factura'] . '</td>
								<td>' . date("d-m-Y", strtotime($rows['fecha_factura'])) . '</td>
								<td>' . number_format($rows['total_compra'], 0, ',', '.') . '</td>
                                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                                <td>' . $estadoBadge . '</td>';
                if ($privilegio == 1 || $privilegio == 2) {
                    $tabla .= '<td>
									<form class="FormularioAjax" action="' . SERVERURL . 'ajax/compraAjax.php" method="POST" data-form="delete" autocomplete="off" action="">
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

    /**Controlador anular compra */
    public function anular_compra_controlador()
    {
        $id = mainModel::decryption($_POST['compra_id_del']);
        $id = mainModel::limpiar_string($id);

        // Verificar que la compra exista
        $check_compra = mainModel::ejecutar_consulta_simple(
            "SELECT idcompra_cabecera FROM compra_cabecera WHERE idcompra_cabecera = '$id'"
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
        $check_compraestado = mainModel::ejecutar_consulta_simple(
            "SELECT idcompra_cabecera FROM compra_cabecera WHERE idcompra_cabecera = '$id' AND estado = 0"
        );
        if ($check_compraestado->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La recepcion de compra que intenta anular ya se encuentra anulada",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

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

        $usuario = $_SESSION['id_str'];
        $id_sucursal = $_SESSION['nick_sucursal']; // Ajustar si aplica

        try {
            // Iniciar transacción
            $pdo = mainModel::conectar();
            $pdo->beginTransaction();

            // 1) Anular compra cabecera
            compraModelo::anular_compra_modelo([
                "idcompra_cabecera" => $id,
                "updatedby" => $usuario
            ]);

            // 2) Obtener detalles de la compra
            $detalles = compraModelo::datos_detalle_compra_modelo($id)->fetchAll(PDO::FETCH_ASSOC);

            // 3) Descontar stock y generar movimientos
            foreach ($detalles as $d) {
                // Descontar stock
                compraModelo::descontar_stock_modelo([
                    "id_articulo" => $d['id_articulo'],
                    "cantidad"    => $d['cantidad_recibida'],
                    "usuario"     => $usuario,
                    "id_sucursal"  => $id_sucursal,
                    "referencia"  => $id
                ]);

                // Insertar movimiento de stock
                compraModelo::movimiento_stock_anulacion_modelo([
                    "LocalId"     => $id_sucursal,
                    "ProductoId"  => $d['id_articulo'],
                    "Cantidad"    => $d['cantidad_recibida'],
                    "Costo"       => $d['precio_unitario'],
                    "Usuario"     => $usuario,
                    "Referencia"  => "ANUL_COMPRA# " . $id
                ]);
            }

            // 4) Anular cuentas a pagar
            compraModelo::anular_cuentas_pagar_modelo($id);

            // Confirmar transacción
            $pdo->commit();

            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Compra Anulada!",
                "Texto" => "La recepcion de compra ha sido anulada correctamente",
                "Tipo" => "success"
            ];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
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
