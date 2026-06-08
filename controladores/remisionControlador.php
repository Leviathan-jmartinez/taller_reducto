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
        if (!mainModel::tienePermiso('compra.remision.crear')) {
            return '<div class="alert alert-danger">Acceso no autorizado</div>';
        }

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
        $datoscompra = mainModel::ejecutar_consulta_simple("SELECT SQL_CALC_FOUND_ROWS
        co.idcompra_cabecera,
        co.nro_factura,
        p.razon_social
        FROM compra_cabecera co
        INNER JOIN proveedores p on p.idproveedores = co.idproveedores
        where (co.nro_factura like '%$facturacompra%') and co.estado = '1'  and co.id_sucursal = '" . $_SESSION['nick_sucursal'] . "'
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
        if (!mainModel::tienePermiso('compra.remision.crear')) {
            $_SESSION['alerta_oc'] = [
                "tipo" => "error",
                "mensaje" => "No posee permisos para cargar facturas en remisiones"
            ];
            header("Location: " . SERVERURL . "remision-nuevo/");
            exit();
        }

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
        $id_sucursal = $_SESSION['nick_sucursal'];
        
        // 1️⃣ Cabecera de la compra (proveedor)
        $sqlCabecera = mainModel::ejecutar_consulta_simple("
        SELECT cc.idcompra_cabecera, p.razon_social, p.ruc, cc.idproveedores, cc.nro_factura, cc.fecha_factura, cc.total_compra
        FROM compra_cabecera cc
        INNER JOIN proveedores p ON p.idproveedores = cc.idproveedores
        WHERE cc.idcompra_cabecera = '$idcompra' and cc.id_sucursal = '$id_sucursal'
        LIMIT 1");
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
        $conexion = mainModel::conectar();

        $sqlDetalle = $conexion->prepare("
        SELECT cd.id_articulo, cd.cantidad_recibida, a.desc_articulo, a.codigo, cd.precio_unitario
        FROM compra_detalle cd
        INNER JOIN compra_cabecera cc ON cc.idcompra_cabecera = cd.idcompra_cabecera
        INNER JOIN articulos a ON a.id_articulo = cd.id_articulo
        WHERE cd.idcompra_cabecera = :idcompra AND cc.id_sucursal = :id_sucursal");

        $sqlDetalle->bindParam(":idcompra", $idcompra, PDO::PARAM_INT);
        $sqlDetalle->bindParam(":id_sucursal", $id_sucursal, PDO::PARAM_INT);

        $sqlDetalle->execute();
        $detalle = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);



        $_SESSION['datos_articulofactura'] = [];
        foreach ($detalle as $i => $row) {
            $cantidad = (float)$row['cantidad_recibida'];
            $precio = (float)$row['precio_unitario'];
            $subtotal = $cantidad * $precio;

            $_SESSION['datos_articulofactura'][$i] = [
                "ID" => $row['id_articulo'],
                "codigo" => $row['codigo'],
                "descripcion" => $row['desc_articulo'],
                "cantidad" => $cantidad,
                "precio" => $precio,
                "subtotal" => $subtotal
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
        if (!mainModel::tienePermiso('compra.remision.crear')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso no autorizado",
                "Texto" => "No posee permisos para crear remisiones",
                "Tipo" => "error"
            ]);
            exit();
        }

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

        $idcompra_sesion = (int)($_SESSION['datos_dactura']['ID'] ?? 0);
        if ($idcompra_sesion <= 0 || $idcompra_sesion !== $idcompra) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe cargar nuevamente la factura antes de guardar la remision",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Validar otros campos obligatorios
        $id_usuario = mainModel::limpiar_string($_SESSION['id_str']);
        $id_sucursal = mainModel::limpiar_string($_SESSION['nick_sucursal']);
        $nro_remision = mainModel::limpiar_string($_POST['nro_remision'] ?? '');
        $fecha_emision = mainModel::limpiar_string($_POST['fecha_emision'] ?? '');
        $nombre_transpo = mainModel::limpiar_string($_POST['nombre_transpo'] ?? '');
        $transportista = mainModel::limpiar_string($_POST['transportista'] ?? $nombre_transpo);
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

        if (empty($_SESSION['datos_articulofactura']) || !is_array($_SESSION['datos_articulofactura'])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe cargar una factura con detalle antes de guardar la remision",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // El detalle se arma desde la factura cargada en sesion, no desde campos editables.
        $detalle = [];
        foreach ($_SESSION['datos_articulofactura'] as $item) {
            $id_articulo = (int)($item['ID'] ?? 0);
            $cantidad = (float)($item['cantidad'] ?? 0);
            $costo = (float)($item['precio'] ?? 0);
            if ($id_articulo <= 0 || $cantidad <= 0) continue;

            $detalle[] = [
                "id_articulo" => $id_articulo,
                "cantidad" => $cantidad,
                "costo" => $costo,
                "subtotal" => $cantidad * $costo
            ];
        }

        if (empty($detalle)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La factura cargada no posee articulos validos para remitir",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Armar array para nota_remision
        $datos = [
            "idcompra_cabecera" => $idcompra,
            "id_usuario" => $id_usuario,
            "id_sucursal" => $id_sucursal,
            "fecha_emision" => $fecha_emision,
            "nro_remision" => $nro_remision,
            "nombre_transpo" => $nombre_transpo,
            "ci_transpo" => mainModel::limpiar_string($_POST['ci_transpo'] ?? ''),
            "cel_transpo" => mainModel::limpiar_string($_POST['cel_transpo'] ?? ''),
            "transportista" => $transportista,
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

        remisionModelo::guardar_remision_detalle_modelo($idnota, $detalle);

        // Limpiar variables de sesión
        unset($_SESSION['datos_dactura']);
        unset($_SESSION['datos_articulofactura']);
        unset($_SESSION['idfacturaseleccionado']);

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
    public function paginador_remision_controlador($pagina, $registros, $url, $busqueda1, $busqueda2, $nro_factura = '', $estado = '', $orden = 'fecha', $direccion = 'DESC')
    {
        if (!mainModel::tienePermiso('compra.remision.ver')) {
            echo '<div class="alert alert-danger">Acceso no autorizado</div>';
            return;
        }

        $pagina     = mainModel::limpiar_string($pagina);
        $registros  = mainModel::limpiar_string($registros);
        $busqueda1  = mainModel::limpiar_string($busqueda1);
        $busqueda2  = mainModel::limpiar_string($busqueda2);
        $nro_factura = mainModel::limpiar_string($nro_factura);
        $estado      = mainModel::limpiar_string($estado);
        $orden = mainModel::limpiar_string($orden);
        $direccion = strtoupper(mainModel::limpiar_string($direccion));

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        /* ================= FILTROS ================= */
        $filtros = [];

        if (!empty($busqueda1) && !empty($busqueda2)) {
            $filtros[] = [
                "campo" => "r.fecha_emision",
                "tipo"  => "DATE_RANGE",
                "desde" => $busqueda1,
                "hasta" => $busqueda2
            ];
        }

        if ($nro_factura !== '') {
            $filtros[] = [
                "campo" => "cc.nro_factura",
                "tipo"  => "LIKE",
                "valor" => $nro_factura
            ];
        }

        if ($estado !== '') {
            $filtros[] = [
                "campo" => "r.estado",
                "tipo"  => "=",
                "valor" => $estado
            ];
        } else {
            $filtros[] = [
                "campo" => "r.estado",
                "tipo"  => "!=",
                "valor" => 0
            ];
        }

        $filtrosSQL = mainModel::construirFiltros($filtros);
        $columnasOrdenSql = [
            'fecha' => 'r.fecha_emision',
            'estado' => 'r.estado'
        ];
        $ordenamiento = mainModel::preparar_ordenamiento($orden, $direccion, $columnasOrdenSql, 'fecha', 'DESC');
        $orden = $ordenamiento['orden'];
        $direccion = $ordenamiento['direccion'];

        $res = remisionModelo::listar_remisiones_modelo($inicio, $registros, $filtrosSQL, "ORDER BY " . $ordenamiento['sql'] . ", r.idnota_remision DESC");
        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        /* 🔹 TABLA */
        $tabla .= '<div class="table-responsive">
        <table class="table table-dark table-sm">
            <thead>
                <tr class="text-center roboto-medium">
                    <th>#</th>
                    <th>N° REMISIÓN</th>
                    <th>FACTURA</th>
                    <th>' . mainModel::link_orden_tabla($url, 'fecha', 'FECHA', $orden, $direccion, 'remision_orden', 'remision_direccion') . '</th>
                    <th>TRANSPORTISTA</th>
                    <th>MOTIVO</th>
                    <th>GENERADO POR</th>
                    <th>' . mainModel::link_orden_tabla($url, 'estado', 'ESTADO', $orden, $direccion, 'remision_orden', 'remision_direccion') . '</th>';

        if (mainModel::tienePermiso('compra.remision.anular')) {
            $tabla .=           '<th>ANULAR</th>';
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
                <td>' . ($rows['nro_factura'] ?? '-') . '</td>
                <td>' . date("d-m-Y", strtotime($rows['fecha_emision'])) . '</td>
                <td>' . $rows['nombre_transpo'] . '</td>
                <td>' . $rows['motivo_remision'] . '</td>
                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                <td>' . $estadoBadge . '</td>

                ';

                /* 🔹 ANULAR */
                if (mainModel::tienePermiso('compra.remision.anular')) {
                    $tabla .= '
                <td>
                    <form class="FormularioAjax"
                          action="' . SERVERURL . 'ajax/remisionAjax.php"
                          method="POST"
                          data-form="delete"
                          data-anulacion="true"
                          data-anulacion-titulo="Anular nota de remision"
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
        if (!mainModel::tienePermiso('compra.remision.anular')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso no autorizado",
                "Texto" => "No posee permisos para anular remisiones",
                "Tipo" => "error"
            ]);
            exit();
        }

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
        $id_sucursal = $_SESSION['nick_sucursal'];
        $motivo = mainModel::limpiar_string($_POST['motivo_anulacion'] ?? '');

        $anular = remisionModelo::anular_remision_modelo($id, $usuario, $id_sucursal, $motivo);

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
