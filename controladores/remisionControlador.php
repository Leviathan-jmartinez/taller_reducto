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

    /**controlador paginador remision */
    public function paginador_remision_controlador($pagina, $registros, $privilegio, $url, $busqueda1, $busqueda2)
    {
        $pagina     = mainModel::limpiar_string($pagina);
        $registros  = mainModel::limpiar_string($registros);
        $privilegio = mainModel::limpiar_string($privilegio);
        $busqueda1  = mainModel::limpiar_string($busqueda1);
        $busqueda2  = mainModel::limpiar_string($busqueda2);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        /* 🔹 CONSULTA */
        if (!empty($busqueda1) && !empty($busqueda2)) {

            $consulta = "
        SELECT SQL_CALC_FOUND_ROWS
            r.idnota_remision,
            r.fecha_emision,
            r.nro_remision,
            r.nombre_transpo,
            r.motivo_remision,
            r.estado,
            u.usu_nombre,
            u.usu_apellido
        FROM nota_remision r
        INNER JOIN usuarios u ON u.id_usuario = r.id_usuario
        WHERE DATE(r.fecha_emision) >= '$busqueda1'
        AND DATE(r.fecha_emision) <= '$busqueda2'
        ORDER BY r.fecha_emision ASC
        LIMIT $inicio, $registros
        ";
        } else {

            $consulta = "
        SELECT SQL_CALC_FOUND_ROWS
            r.idnota_remision,
            r.fecha_emision,
            r.nro_remision,
            r.nombre_transpo,
            r.motivo_remision,
            r.estado,
            u.usu_nombre,
            u.usu_apellido
        FROM nota_remision r
        INNER JOIN usuarios u ON u.id_usuario = r.id_usuario
        WHERE r.estado != 0
        ORDER BY r.idnota_remision DESC
        LIMIT $inicio, $registros
        ";
        }

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta)->fetchAll();

        $total = (int)$conexion->query("SELECT FOUND_ROWS()")->fetchColumn();
        $Npaginas = ceil($total / $registros);

        /* 🔹 TABLA */
        $tabla .= '<div class="table-responsive">
        <table class="table table-dark table-sm">
            <thead>
                <tr class="text-center roboto-medium">
                    <th>#</th>
                    <th>N° REMISIÓN</th>
                    <th>FECHA</th>
                    <th>TRANSPORTISTA</th>
                    <th>MOTIVO</th>
                    <th>GENERADO POR</th>
                    <th>ESTADO</th>
                    <th>PDF</th>';

        if ($privilegio == 1 || $privilegio == 2) {
            $tabla .= '<th>ANULAR</th>';
        }

        $tabla .= '
                </tr>
            </thead>
            <tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador   = $inicio + 1;
            $reg_inicio = $contador;

            foreach ($datos as $rows) {

                /* 🔹 ESTADO */
                switch ($rows['estado']) {
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
                <td>' . $rows['nro_remision'] . '</td>
                <td>' . date("d-m-Y", strtotime($rows['fecha_emision'])) . '</td>
                <td>' . $rows['nombre_transpo'] . '</td>
                <td>' . $rows['motivo_remision'] . '</td>
                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                <td>' . $estadoBadge . '</td>

                <!-- PDF -->
                <td>
                    <a href="' . SERVERURL . 'pdf/remision.php?id=' . mainModel::encryption($rows['idnota_remision']) . '" 
                       target="_blank"
                       class="btn btn-info btn-sm">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </td>';

                /* 🔹 ANULAR */
                if ($privilegio == 1 || $privilegio == 2) {
                    $tabla .= '
                <td>
                    <form class="FormularioAjax"
                          action="' . SERVERURL . 'ajax/remisionAjax.php"
                          method="POST"
                          data-form="delete"
                          autocomplete="off">

                        <input type="hidden" name="remision_id_del"
                               value="' . mainModel::encryption($rows['idnota_remision']) . '">

                        <button type="submit" class="btn btn-warning btn-sm">
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
                $tabla .= '
            <tr class="text-center">
                <td colspan="9">
                    <a href="' . $url . '" class="btn btn-raised btn-primary btn-sm">
                        Haga click aquí para recargar el listado
                    </a>
                </td>
            </tr>';
            } else {
                $tabla .= '
            <tr class="text-center">
                <td colspan="9">No hay registros en el sistema</td>
            </tr>';
            }
        }

        $tabla .= '
            </tbody>
        </table></div>';

        /* 🔹 PAGINADOR */
        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '
        <p class="text-right">
            Mostrando registro ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '
        </p>';

            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        echo $tabla;
    }
    /**fin controlador */
    /**controlador anular remision */
    public function anular_remision_controlador()
    {
        $id = mainModel::decryption($_POST['remision_id_del'] ?? '');

        if (!$id || !is_numeric($id)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "ID inválido",
                "Tipo" => "error"
            ]);
            exit();
        }

        $usuario = $_SESSION['id_str'];

        $anular = remisionModelo::anular_remision_modelo($id, $usuario);

        if ($anular) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Remisión Anulada",
                "Texto" => "La remisión fue anulada correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo anular la remisión",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }
}
