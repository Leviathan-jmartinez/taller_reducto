<?php
if ($peticionAjax) {
    require_once "../modelos/remisionModelo.php";
} else {
    require_once "./modelos/remisionModelo.php";
}

class remisionControlador extends remisionModelo
{
    /**controlador buscar factura */
    public function buscar_factura_controlador()
    {
        $facturacompra  = mainModel::limpiar_string($_POST['buscar_factura']);

        if ($facturacompra == "") {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    Debes introducir el número de la factura
                                </p>
                            </div>';
            exit();
        }
        /**seleccionar proveedor */
        $datoscompra = mainModel::ejecutar_consulta_simple("SELECT SQL_CALC_FOUND_ROWS co.idcompra_cabecera as idcompra_cabecera, co.id_usuario as id_usuario, co.fecha_creacion as fecha_creacion, co.estado as estadoCO, co.nro_factura AS nro_factura, co.condicion as condicion,
        co.fecha_factura as fecha_factura, total_compra AS total_compra, co.idproveedores as idproveedores, p.razon_social as razon_social, p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, 
        p.estado as estadoPro, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick, co.updated as updated,
        co.updatedby as updatedby
        FROM compra_cabecera co
        INNER JOIN proveedores p on p.idproveedores = co.idproveedores
        INNER JOIN usuarios u on u.id_usuario = co.id_usuario
        where (co.nro_factura like '%$facturacompra%') and co.estado = '1'
        order by idcompra_cabecera desc");

        if ($datoscompra->rowCount() >= 1) {
            $datoscompra = $datoscompra->fetchAll();
            $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>
                        <tr class="text-center">
                            <th>Número de Factura</th>
                            <th>Proveedor</th>
                            <th></th>
                        </tr>';
            foreach ($datoscompra as $rows) {
                $tabla .= '
                        <tr class="text-center">
                            <td>' . $rows['nro_factura'] . '</td>
                            <td>' . $rows['razon_social'] . '</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="agregar_factura(' . $rows['idcompra_cabecera'] . ')"><i class="fas fa-user-plus"></i></button>
                            </td>
                        </tr>';
            }
            $tabla .= '</tbody></table></div>';
            return $tabla;
        } else {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún pedido en el sistema que coincida con <strong>“' . $facturacompra . '”</strong>
                                </p>
                            </div>';
        }
    }
    /**fin controlador */

    /**controlador cargar factura */
    public function cargar_factura_controlador()
    {
        $idcompra = mainModel::limpiar_string($_POST['idfacturaseleccionado'] ?? '');
        if (empty($idcompra)) {
            $_SESSION['alerta_oc'] = [
                "tipo" => "error",
                "mensaje" => "No se recibió ID de la compra"
            ];
            header("Location: " . SERVERURL . "remision-nuevo/");
            exit();
        }

        $_SESSION['idfacturaseleccionado'] = $idcompra;

        // 1️⃣ Cabecera de la compra (proveedor)
        $sqlCabecera = mainModel::ejecutar_consulta_simple("
        SELECT cc.idcompra_cabecera, p.razon_social, p.ruc, cc.idproveedores, cc.nro_factura, cc.fecha_factura, cc.total_compra
        FROM compra_cabecera cc
        INNER JOIN proveedores p ON p.idproveedores = cc.idproveedores
        WHERE cc.idcompra_cabecera = '$idcompra'");
        $cabecera = $sqlCabecera->fetch();
        if ($cabecera) {
            $_SESSION['datos_dactura'] = [
                "ID" => $cabecera['idcompra_cabecera'],
                "IDPRO" => $cabecera['idproveedores'],
                "RAZON" => $cabecera['razon_social'],
                "RUC" => $cabecera['ruc'],
                "NRO_FACTURA" => $cabecera['nro_factura'],
                "FECHA_FACTURA" => $cabecera['fecha_factura'],
                "TOTAL_COMPRA" => $cabecera['total_compra']
            ];
        }

        // 2️⃣ Detalle de la compra (artículos)
        $sqlDetalle = mainModel::ejecutar_consulta_simple("
        SELECT cd.id_articulo, cd.cantidad_recibida, a.desc_articulo, a.codigo, cd.precio_unitario, cd.subtotal, cd.ivaPro, ti.tipo_impuesto_descri, ti.ratevalueiva, ti.divisor
        FROM compra_detalle cd
        INNER JOIN articulos a ON a.id_articulo = cd.id_articulo
        INNER JOIN tipo_impuesto ti ON ti.idiva = a.idiva
        WHERE cd.idcompra_cabecera = '$idcompra'");
        $detalle = $sqlDetalle->fetchAll();

        $_SESSION['datos_articulofactura'] = $_SESSION['datos_articulofactura'] ?? [];
        foreach ($detalle as $i => $row) {
            // Si ya existe en la sesión (modificado), no sobreescribas cantidad y precio
            $cantidad = $_SESSION['datos_articulofactura'][$i]['cantidad'] ?? $row['cantidad_recibida'];
            $precio   = $_SESSION['datos_articulofactura'][$i]['precio'] ?? $row['precio_unitario'];
            $subtotal = $cantidad * $precio;

            // IVA: si divisor = 0, IVA = 0, sino se calcula como subtotal / divisor
            $iva = ($row['divisor'] == 0) ? 0 : $subtotal / $row['divisor'];

            $_SESSION['datos_articulofactura'][$i] = [
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
        header("Location: " . SERVERURL . "remision-nuevo/");
        exit();
    }
    /**fin controlador */

    /**controlador guardar remision */
    public function guardar_remision_controlador()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Método de envío no permitido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Validación de idcompra_cabecera
        $idcompra = mainModel::limpiar_string($_POST['idcompra_cabecera'] ?? '');
        if (empty($idcompra) || !is_numeric($idcompra)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se recibió un ID de compra válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $idcompra = intval($idcompra);

        // Validar otros campos obligatorios
        $id_usuario = mainModel::limpiar_string($_SESSION['id_str']);
        $nro_remision = mainModel::limpiar_string($_POST['nro_remision'] ?? '');
        $fecha_emision = mainModel::limpiar_string($_POST['fecha_emision'] ?? '');
        $nombre_transpo = mainModel::limpiar_string($_POST['nombre_transpo'] ?? '');
        $fechaenvio = mainModel::limpiar_string($_POST['fechaenvio'] ?? '');
        $fechallegada = mainModel::limpiar_string($_POST['fechallegada'] ?? '');
        $motivo_remision = mainModel::limpiar_string($_POST['motivo_remision'] ?? '');
        $estado = mainModel::limpiar_string($_POST['estado'] ?? 1);

        if (empty($id_usuario) || empty($nro_remision) || empty($fecha_emision) || empty($nombre_transpo) || empty($fechaenvio) || empty($fechallegada) || empty($motivo_remision)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Faltan datos obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Armar array para nota_remision
        $datos = [
            "idcompra_cabecera" => $idcompra,
            "id_usuario" => $id_usuario,
            "fecha_emision" => $fecha_emision,
            "nro_remision" => $nro_remision,
            "nombre_transpo" => $nombre_transpo,
            "ci_transpo" => mainModel::limpiar_string($_POST['ci_transpo'] ?? ''),
            "cel_transpo" => mainModel::limpiar_string($_POST['cel_transpo'] ?? ''),
            "transportista" => $nombre_transpo,
            "ruc_transport" => mainModel::limpiar_string($_POST['ruc_transport'] ?? ''),
            "vehimarca" => mainModel::limpiar_string($_POST['vehimarca'] ?? ''),
            "vehimodelo" => mainModel::limpiar_string($_POST['vehimodelo'] ?? ''),
            "vehichapa" => mainModel::limpiar_string($_POST['vehichapa'] ?? ''),
            "fechaenvio" => $fechaenvio,
            "fechallegada" => $fechallegada,
            "motivo_remision" => $motivo_remision,
            "estado" => $estado
        ];

        // Guardar nota_remision
        $idnota = remisionModelo::guardar_remision_modelo($datos);
        if (isset($idnota['error'])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo guardar la remisión: " . $idnota['error'],
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Guardar detalle
        $cantidades = $_POST['cantidades'] ?? [];
        $costos = $_POST['costos'] ?? [];
        $detalle = [];

        foreach ($cantidades as $i => $cantidad) {
            $cantidad = floatval($cantidad);
            $costo = floatval($costos[$i]);
            $id_articulo = $_SESSION['datos_articulofactura'][$i]['ID'] ?? null;
            if (!$id_articulo) continue;

            $detalle[] = [
                "id_articulo" => $id_articulo,
                "cantidad" => $cantidad,
                "costo" => $costo,
                "subtotal" => $cantidad * $costo
            ];
        }

        remisionModelo::guardar_remision_detalle_modelo($idnota, $detalle);

        // Limpiar variables de sesión
        unset($_SESSION['datos_articulofactura']);

        // ✅ Respuesta de éxito
        $alerta = [
            "Alerta" => "recargar", // puede ser "simple" o "recargar" según tu JS
            "Titulo" => "Registro Agregado",
            "Texto" => "La remisión fue guardada correctamente",
            "Tipo" => "success"
        ];
        echo json_encode($alerta);
        exit();
    }

    /**fin controlador */
}
