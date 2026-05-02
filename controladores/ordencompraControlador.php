<?php
require_once __DIR__ . "/../modelos/ordencompraModelo.php";

class ordencompraControlador extends ordencompraModelo
{
    /**Controlador paginar articulos */
    public function paginador_presupuestos_controlador($pagina, $registros, $url, $busqueda)
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $busqueda = mainModel::limpiar_string($busqueda);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        if (!empty($busqueda)) {
            $consulta = "SELECT  SQL_CALC_FOUND_ROWS pc.idpresupuesto_compra as idpresupuesto_compra, pc.id_sucursal as id_sucursal,pc.id_usuario as id_usuario, pc.fecha as fecha, fecha_venc as fecha_venc,pc.estado as estadoPre, 
            pc.idproveedores as idproveedores, p.razon_social as razon_social, p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, 
            p.estado as estadoPro, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick, pc.updated as updated,
            pc.updatedby as updatedby
            FROM presupuesto_compra pc
            INNER JOIN proveedores p on p.idproveedores = pc.idproveedores
            INNER JOIN usuarios u on u.id_usuario = pc.id_usuario
            WHERE (pc.idpresupuesto_compra LIKE '%$busqueda%' OR p.razon_social LIKE '%$busqueda%' OR p.ruc LIKE '%$busqueda%') AND pc.estado != 0 AND id_sucursal = " . $_SESSION['nick_sucursal'] . "
            ORDER BY fecha desc LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT  SQL_CALC_FOUND_ROWS pc.idpresupuesto_compra as idpresupuesto_compra, pc.id_sucursal as id_sucursal,pc.id_usuario as id_usuario, pc.fecha as fecha, fecha_venc as fecha_venc,pc.estado as estadoPre, 
            pc.idproveedores as idproveedores, p.razon_social as razon_social, p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, 
            p.estado as estadoPro, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, u.usu_estado as usu_estado, u.usu_nick as usu_nick, pc.updated as updated,
            pc.updatedby as updatedby
            FROM presupuesto_compra pc
            INNER JOIN proveedores p on p.idproveedores = pc.idproveedores
            INNER JOIN usuarios u on u.id_usuario = pc.id_usuario
            WHERE pc.estado != 0 AND id_sucursal = " . $_SESSION['nick_sucursal'] . "
            ORDER BY pc.idpresupuesto_compra ASC LIMIT $inicio,$registros";
        }
        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta);
        $datos = $datos->fetchAll();

        $total = $conexion->query("SELECT FOUND_ROWS()");
        $total = (int) $total->fetchColumn();

        $Npaginas = ceil($total / $registros);
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        $tabla .= '<div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover text-center align-middle oc-table">
                                <thead>
                                    <tr class="text-center roboto-medium">
                                        <th>#</th>
                                        <th>Nro Presupuesto</th>
                                        <th>Proveedor</th>
                                        <th>Fecha Creacion</th>
                                        <th>Vencimiento</th>
                                        <th>Estado</th>
                                        <th>Acción</th>
                                    </tr>
        </thead>
						    <tbody>';
        if ($total >= 1 && $pagina <= $Npaginas) {
            $contador = $reg_inicio;
            foreach ($datos as $rows) {
                switch ($rows['estadoPre']) {
                    case 1:
                        $estadoBadge = '<span class="badge bg-primary">Activo</span>';
                        break;
                    case 2:
                        $estadoBadge = '<span class="badge bg-success">OC generada</span>';
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
								<td>' . date("d-m-Y", strtotime($rows['fecha_venc'])) . '</td>
                                <td>' . $estadoBadge . '</td>
                                <td>
                                <button class="btn btn-primary btn-sm generar-oc-btn" 
                                    data-id="' . $rows['idpresupuesto_compra'] . '">Generar OC</button>
                                </td>';
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
    /**Controlador agregar OC */
    public function generar_oc_controlador()
    {



        // -----------------------------
        // 2) Obtener y sanear POST
        // -----------------------------
        $idpresupuesto = $_POST['idpresupuesto'] ?? null;
        $cantidades = $_POST['cantidades'] ?? [];
        $fecha_entrega = $_POST['fecha_entrega'] ?? null;

        if (is_array($idpresupuesto)) {
            $idpresupuesto = $idpresupuesto[0] ?? null;
        }

        $idpresupuesto = trim($idpresupuesto);

        // -----------------------------
        // 3) Validaciones iniciales
        // -----------------------------
        if (!$idpresupuesto || $idpresupuesto === "undefined" || !is_numeric($idpresupuesto)) {
            return "error:no_id_valido";
        }

        if (empty($cantidades)) {
            return "error:no_cantidades";
        }

        $cantidades_validas = [];
        foreach ($cantidades as $idArticulo => $cantidad) {
            $idArticulo = mainModel::limpiar_string($idArticulo);
            $cantidad = mainModel::limpiar_string($cantidad);

            if ($cantidad === "" || !is_numeric($cantidad)) {
                continue;
            }

            $cantidad = (int) $cantidad;
            if ($cantidad > 0) {
                $cantidades_validas[$idArticulo] = $cantidad;
            }
        }

        if (empty($cantidades_validas)) {
            return "error:sin_articulos_cantidad";
        }

        $conexion = mainModel::conectar();

        // -----------------------------
        // 4) Obtener cabecera del presupuesto
        // -----------------------------
        $consultaPre = $conexion->prepare("
        SELECT idpresupuesto_compra,idproveedores, id_usuario
        FROM presupuesto_compra
        WHERE idpresupuesto_compra = :id AND id_sucursal = :sucursal AND estado != 0");
        $consultaPre->execute([":id" => $idpresupuesto, ":sucursal" => $_SESSION['nick_sucursal']]);
        $pre = $consultaPre->fetch(PDO::FETCH_ASSOC);

        if (!$pre) {
            return "error:presupuesto_no_existe";
        }

        // -----------------------------
        // 6) Obtener detalle del presupuesto
        // -----------------------------
        $consultaDet = $conexion->prepare("
            SELECT d.id_articulo, d.precio
            FROM presupuesto_detalle d
            INNER JOIN presupuesto_compra c 
                ON c.idpresupuesto_compra = d.idpresupuesto_compra
            WHERE d.idpresupuesto_compra = :id
            AND c.id_sucursal = :sucursal");
        $consultaDet->execute([":id" => $idpresupuesto, ":sucursal" => $_SESSION['nick_sucursal']]);
        $detallePre = $consultaDet->fetchAll(PDO::FETCH_ASSOC);
        if (empty($detallePre)) {
            return "error:detalle_vacio";
        }

        $articulos_presupuesto = array_column($detallePre, null, "id_articulo");
        $detalles_validos = [];

        foreach ($cantidades_validas as $idArt => $cantidad) {
            if (!isset($articulos_presupuesto[$idArt])) {
                continue;
            }

            $item = $articulos_presupuesto[$idArt];
            if (!isset($item["precio"]) || !is_numeric($item["precio"]) || (float) $item["precio"] <= 0) {
                return "error:precio_invalido";
            }

            $detalles_validos[] = [
                "id_articulo" => $idArt,
                "cantidad" => $cantidad,
                "precio" => (float) $item["precio"]
            ];
        }

        if (empty($detalles_validos)) {
            return "error:sin_articulos_cantidad";
        }

        // -----------------------------
        // 5) Crear cabecera de OC
        // -----------------------------
        $datos_oc_cab = [
            "proveedor" => $pre['idproveedores'],
            "presupuestoid" => $pre['idpresupuesto_compra'],
            "sucursal" => $_SESSION['nick_sucursal'],
            "usuario"   => $_SESSION['id_str'],
            "fecha_entrega"   => $fecha_entrega
        ];

        $idOC = ordenCompraModelo::agregar_ocC_modelo1($datos_oc_cab);

        if ($idOC <= 0) {
            return "error:oc_cabecera";
        }
        // -----------------------------
        // 7) Insertar detalle de OC
        // -----------------------------
        $errores = 0;

        foreach ($detalles_validos as $item) {
            $datos_det = [
                "ocid"     => $idOC,
                "articulo" => $item["id_articulo"],
                "cantidad" => $item["cantidad"],
                "precio"   => $item["precio"],
                "pendiente" => $item["cantidad"]
            ];

            $insert = ordenCompraModelo::agregar_ocD_modelo($datos_det);

            if ($insert->rowCount() != 1) {
                $errores++;
            }
        }
        // -----------------------------
        // 8) Actualizar presupuesto
        // -----------------------------
        if ($errores === 0) {
            $upd = $conexion->prepare("
            UPDATE presupuesto_compra 
            SET estado = 2
            WHERE idpresupuesto_compra = :id
            AND id_sucursal = :sucursal
            ");
            $upd->execute([
                ":id" => $idpresupuesto,
                ":sucursal" => $_SESSION['nick_sucursal']
            ]);
        }

        // -----------------------------
        // 9) Respuesta final al AJAX
        // -----------------------------
        if ($errores > 0) {
            return "warning:" . $idOC;
        } else {
            return "ok:" . $idOC;
        }
    }
    /**fin controlador */

    public function obtener_detalle_presupuesto_controlador()
    {
        $idpresupuesto = $_POST['idpresupuesto'] ?? null;

        if (is_array($idpresupuesto)) {
            $idpresupuesto = $idpresupuesto[0] ?? null;
        }

        $idpresupuesto = mainModel::limpiar_string((string) $idpresupuesto);

        if ($idpresupuesto === "" || !is_numeric($idpresupuesto)) {
            return '<tr><td colspan="4" class="text-center">Presupuesto invalido</td></tr>';
        }

        $conexion = mainModel::conectar();
        $consulta = $conexion->prepare("
            SELECT d.id_articulo, a.codigo, a.desc_articulo, d.precio
            FROM presupuesto_detalle d
            INNER JOIN presupuesto_compra c
                ON c.idpresupuesto_compra = d.idpresupuesto_compra
            INNER JOIN articulos a
                ON a.id_articulo = d.id_articulo
            WHERE d.idpresupuesto_compra = :id
            AND c.id_sucursal = :sucursal
        ");
        $consulta->execute([
            ":id" => $idpresupuesto,
            ":sucursal" => $_SESSION['nick_sucursal']
        ]);

        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

        if (empty($datos)) {
            return '<tr><td colspan="4" class="text-center">No se encontraron articulos para este presupuesto</td></tr>';
        }

        $html = "";

        foreach ($datos as $row) {
            $idArticulo = htmlspecialchars($row['id_articulo'], ENT_QUOTES, 'UTF-8');
            $codigo = htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8');
            $descripcion = htmlspecialchars($row['desc_articulo'], ENT_QUOTES, 'UTF-8');
            $precio = htmlspecialchars($row['precio'], ENT_QUOTES, 'UTF-8');

            $html .= '
                <tr>
                    <td>' . $codigo . '</td>
                    <td>' . $descripcion . '</td>
                    <td>' . $precio . '</td>
                    <td>
                        <input type="number" name="cantidades[' . $idArticulo . ']" min="1" class="form-control">
                    </td>
                </tr>';
        }

        return $html;
    }

    /**controlador agregar orden de compra */
    public function agregar_oc_controlador()
    {

        $fecha_entrega = $_POST['fecha_entrega'] ?? null;

        if ($_SESSION['tipo_ordencompra'] == "sin_presupuesto") {
            if (empty($_SESSION['Sdatos_proveedorOC'])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error!",
                    "Texto" => "No has seleccionado ningun proveedor",
                    "Tipo" => "error"
                ];
                return json_encode($alerta);
            }
            if (empty($_SESSION['Sdatos_articuloOC'])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error!",
                    "Texto" => "No has seleccionado ningun artículo para la orden de compra",
                    "Tipo" => "error"
                ];
                return json_encode($alerta);
            }

            if (empty($fecha_entrega) || $fecha_entrega == null) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error!",
                    "Texto" => "Debes seleccionar la fecha de entrega",
                    "Tipo" => "error"
                ];
                return json_encode($alerta);
            }

            foreach ($_SESSION['Sdatos_articuloOC'] as $article) {
                $cantidad = $article['cantidad'] ?? 0;
                $precio = $article['precio'] ?? 0;

                if (!is_numeric($cantidad) || (int) $cantidad <= 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Error!",
                        "Texto" => "Todos los articulos deben tener cantidad mayor a 0",
                        "Tipo" => "error"
                    ];
                    return json_encode($alerta);
                }

                if (!is_numeric($precio) || (float) $precio <= 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Error!",
                        "Texto" => "Todos los articulos deben tener precio mayor a 0",
                        "Tipo" => "error"
                    ];
                    return json_encode($alerta);
                }
            }


            /** Insertar cabecera */
            $datos_OC_agg = [
                "usuario"   => $_SESSION['id_str'],
                "proveedor" => $_SESSION['Sdatos_proveedorOC']['ID'],
                "sucursal" => $_SESSION['nick_sucursal'],
                "fecha_entrega" => $fecha_entrega
            ];

            $idocCab = ordencompraModelo::agregar_ocC_modelo2($datos_OC_agg);

            if ($idocCab <= 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrió un error inesperado!",
                    "Texto" => "No pudimos registrar la cabecera del pedido",
                    "Tipo" => "error"
                ];
                return json_encode($alerta);
            }

            /** Insertar detalles */
            $errores_detalles = 0;
            foreach ($_SESSION['Sdatos_articuloOC'] as $article) {

                $detalle_reg = [
                    "ocid" => $idocCab,
                    "articulo" => $article['ID'],
                    "cantidad" => $article['cantidad'],
                    "precio" => $article['precio'],
                    "pendiente" => $article['cantidad']
                ];

                $detalleInsert = ordencompraModelo::agregar_ocD_modelo($detalle_reg);

                if ($detalleInsert->rowCount() != 1) {
                    $errores_detalles++;
                } else {
                    self::registrar_articulo_proveedor_modelo(
                        $article['ID'],
                        $_SESSION['Sdatos_proveedorOC']['ID'],
                        $article['precio']
                    );
                }
            }

            if ($errores_detalles > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error parcial",
                    "Texto" => "La OC se creó, pero algunos artículos no se guardaron",
                    "Tipo" => "warning"
                ];
            } else {
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Pedido guardado!",
                    "Texto" => "La Orden compra se genero correctamente",
                    "Tipo" => "success"
                ];
                $_SESSION['tipo_ordencompra'] = "con_presupuesto";
                unset($_SESSION['Sdatos_proveedorOC'], $_SESSION['Sdatos_articuloOC']);
            }
            return json_encode($alerta);
        }

        return "";
    }

    /**Controlador paginar ordencompra */
    public function paginador_ordencompra_controlador($pagina, $registros, $url, $busqueda1, $busqueda2)
    {
        $pagina    = (int) mainModel::limpiar_string($pagina);
        $registros = (int) mainModel::limpiar_string($registros);
        $url       = SERVERURL . mainModel::limpiar_string($url) . "/";
        $busqueda1 = mainModel::limpiar_string($busqueda1);
        $busqueda2 = mainModel::limpiar_string($busqueda2);

        $pagina = ($pagina > 0) ? $pagina : 1;
        $registros = ($registros > 0) ? $registros : 15;
        $inicio = ($pagina - 1) * $registros;

        /* ================= FILTROS ================= */

        $filtros = [];

        // FECHA
        $fecha_inicio = $_SESSION['fecha_inicio_ordencompra2'] ?? $busqueda1;
        $fecha_final  = $_SESSION['fecha_final_ordencompra2'] ?? $busqueda2;

        if (!empty($fecha_inicio) && !empty($fecha_final)) {
            $filtros[] = [
                "campo" => "oc.fecha",
                "tipo"  => "DATE_RANGE",
                "desde" => $fecha_inicio,
                "hasta" => $fecha_final
            ];
        }

        // PROVEEDOR
        $proveedor = $_SESSION['proveedor_oc'] ?? '';
        if (!empty($proveedor)) {
            $filtros[] = [
                "campo" => "p.razon_social",
                "tipo"  => "LIKE",
                "valor" => $proveedor
            ];
        }

        // ESTADO
        $estado = $_SESSION['estado_oc'] ?? '';
        if ($estado !== '') {
            $filtros[] = [
                "campo" => "oc.estado",
                "tipo"  => "=",
                "valor" => $estado
            ];
        } else {
            $filtros[] = [
                "campo" => "oc.estado",
                "tipo"  => "!=",
                "valor" => 0
            ];
        }

        $filtrosSQL = mainModel::construirFiltros($filtros);

        /* ================= DATOS ================= */

        $res = ordencompraModelo::listar_oc_modelo($inicio, $registros, $filtrosSQL);

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        /* ================= TABLA ================= */

        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center roboto-medium">
                <th>#</th>
                <th>CÓDIGO OC</th>
                <th>PROVEEDOR</th>
                <th>FECHA CREACION</th>
                <th>FECHA ENTREGA</th>
                <th>CREADO POR</th>
                <th>ESTADO</th>
                <th>PDF</th>';

        $puedeAnular = mainModel::tienePermiso('compra.oc.anular');

        if ($puedeAnular) {
            $tabla .= '<th>ANULAR</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador = $reg_inicio;

            foreach ($datos as $rows) {

                switch ($rows['estodoOC']) {
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
                        $estadoBadge = '<span class="badge bg-secondary">?</span>';
                }

                $tabla .= '
            <tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $rows['idorden_compra'] . '</td>
                <td>' . $rows['razon_social'] . '</td>
                <td>' . date("d-m-Y", strtotime($rows['fecha'])) . '</td>
                <td>' . date("d-m-Y", strtotime($rows['fecha_entrega'])) . '</td>
                <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                <td>' . $estadoBadge . '</td>
                <td>
                    <a href="' . SERVERURL . 'pdf/orden_compra.php?id=' . $this->encryption($rows['idorden_compra']) . '"
                        target="_blank"
                        class="btn btn-info">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </td>';

                if ($puedeAnular) {
                    $tabla .= '
                <td>
                    <form class="FormularioAjax"
                        action="' . SERVERURL . 'ajax/ordencompraAjax.php"
                        method="POST"
                        data-form="delete">

                        <input type="hidden" name="ordencompra_id_del"
                            value="' . $this->encryption($rows['idorden_compra']) . '">

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
            $tabla .= '<tr><td colspan="' . $colspan . '" class="text-center">Sin registros</td></tr>';
        }

        $tabla .= '</tbody></table></div>';

        /* ================= PAGINADOR ================= */

        if ($total >= 1 && $pagina <= $Npaginas) {

            $tabla .= '<p class="text-right">
            Mostrando ' . $reg_inicio . ' al ' . $reg_final . ' de ' . $total . '
        </p>';

            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        return $tabla;
    }
    /**fin controlador */


    /**Controlador anular ordencompra */
    public function anular_ordencompra_controlador()
    {
        $id = mainModel::decryption($_POST['ordencompra_id_del']);
        $id = mainModel::limpiar_string($id);

        $check_presupuesto = mainModel::ejecutar_consulta_simple("SELECT idorden_compra FROM orden_compra WHERE idorden_compra = '$id' AND id_sucursal = " . $_SESSION['nick_sucursal'] . "");
        if ($check_presupuesto->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La ORDEN DE COMPRA que intenta anular no existe en el sistema",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }
        $check_presupuestoestado = mainModel::ejecutar_consulta_simple("SELECT idorden_compra FROM orden_compra WHERE idorden_compra = '$id' AND estado = 2 AND id_sucursal = " . $_SESSION['nick_sucursal'] . "");
        if ($check_presupuestoestado->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La ORDEN DE COMPRA que intenta anular se encuentra procesado",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }


        if (!mainModel::tienePermiso('compra.oc.anular')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Advertencia!",
                "Texto" => "No posee los permisos necesarios para realizar esta acción",
                "Tipo" => "error"
            ]);
        }
        $datos_oc_del = [
            "updatedby" => $_SESSION['id_str'],
            "idsucursal" => $_SESSION['nick_sucursal'],
            "idorden_compra" => $id
        ];

        if (ordencompraModelo::anular_ordencompra_modelo($datos_oc_del)) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Pedido Anulado!",
                "Texto" => "La ORDEN DE COMPRA ha sido anulada correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No se pudo anular la ORDEN DE COMPRA seleccionada, por favor intente nuevamente",
                "Tipo" => "error"
            ];
        }
        return json_encode($alerta);
    }
    /**fin controlador */

    /**controlador buscador proveedor */
    public function buscar_proveedor_controlador()
    {
        $proveedor  = mainModel::limpiar_string($_POST['buscar_proveedorOC']);

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
                                <button type="button" class="btn btn-primary" onclick="agregar_proveedorOC(' . $rows['idproveedores'] . ')"><i class="fas fa-user-plus"></i></button>
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
        $id  = mainModel::limpiar_string($_POST['id_agregar_proveedorOC']);

        $check_proveedor = mainModel::ejecutar_consulta_simple("select * from proveedores where idproveedores = '$id' ");
        if ($check_proveedor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido encontrar el proveedor en el sistema",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        } else {
            $campos = $check_proveedor->fetch();
        }

        unset($_SESSION['Sdatos_proveedorOC']);
        if (!isset($_SESSION['Sdatos_proveedorOC'])) {
            $_SESSION['Sdatos_proveedorOC'] = [
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
            return json_encode($alerta);
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido agregar el proveedor al pedido",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }
    }
    /**fin controlador */

    /**controlador buscar articulo */
    public function buscar_articulo_controlador()
    {
        // BUSCAR ARTÍCULO (HTML)

        if (isset($_POST['buscar_articuloOC'])) {
            $articulo = mainModel::limpiar_string($_POST['buscar_articuloOC']);
            if ($articulo == "") return '<div class="alert alert-warning">Debes introducir código o descripción</div>';

            if (!isset($_SESSION['Sdatos_proveedorOC']['ID'])) {
                return '<div class="alert alert-danger">No se ha seleccionado un proveedor</div>';
                exit();
            }
            $id_proveedor = $_SESSION['Sdatos_proveedorOC']['ID'];
            $datos_articuloPre = mainModel::ejecutar_consulta_simple("
                SELECT a.*, ap.precio_compra
                FROM articulos a
                LEFT JOIN articulo_proveedor ap
                    ON ap.id_articulo = a.id_articulo
                   AND ap.idproveedores = '$id_proveedor'
                   AND ap.activo = 1
                WHERE (a.codigo LIKE '%$articulo%' OR a.desc_articulo LIKE '%$articulo%')
                  AND a.estado = 1
                ORDER BY a.desc_articulo DESC
            ");

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
                        <input type="number" id="precio_' . $rows['id_articulo'] . '" class="form-control form-control-sm" step="0.01" min="0.01" value="' . ($rows['precio_compra'] ?? 0) . '">
                    </td>

                    <!-- Botón agregar -->
                    <td>
                        <button type="button" class="btn btn-primary btn-sm" onclick="agregar_articuloOC(' . $rows['id_articulo'] . ')">
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
        if (isset($_POST['id_agregar_articuloOC'])) {

            $id = mainModel::limpiar_string($_POST['id_agregar_articuloOC']);
            $cantidad = mainModel::limpiar_string($_POST['detalle_cantidad']);
            $precio = mainModel::limpiar_string($_POST['detalle_precio']); // <-- nuevo

            // Validaciones
            $check_articulo = mainModel::ejecutar_consulta_simple("SELECT * FROM articulos WHERE id_articulo='$id' AND estado=1");
            if ($check_articulo->rowCount() <= 0)
                return json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "No se encontró el artículo", "Tipo" => "error"]);

            $campos = $check_articulo->fetch();

            if ($cantidad == "" || !is_numeric($cantidad) || intval($cantidad) <= 0)
                return json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "Cantidad inválida", "Tipo" => "error"]);

            if ($precio == "" || !is_numeric($precio) || floatval($precio) <= 0)
                return json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "El precio debe ser mayor a 0", "Tipo" => "error"]);

            $cantidad = intval($cantidad);
            $precio = floatval($precio);
            $subtotal = $cantidad * $precio; // <-- opcional, para mostrar o guardar

            if (isset($_SESSION['Sdatos_articuloOC'][$id])) {
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El artículo que intenta agregar ya se encuentra agregado",
                    "Tipo" => "error"
                ];
            } else {
                $_SESSION['Sdatos_articuloOC'][$id] = [
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
            return json_encode($alerta);
        }

        return "";
    }
    /**fin controlador */
    public function decrypt($valor)
    {
        return mainModel::decryption($valor);
    }

    public function datos_orden_compra_controlador($idOC)
    {
        return [
            'cabecera' => ordenCompraModelo::obtener_orden_compra_cabecera($idOC),
            'detalle'  => ordenCompraModelo::obtener_orden_compra_detalle($idOC)
        ];
    }

    public function eliminar_proveedor_controlador()
    {
        unset($_SESSION['Sdatos_proveedorOC']);

        return json_encode([
            "Alerta" => "recargar",
            "Titulo" => "Proveedor eliminado",
            "Texto" => "El proveedor fue quitado correctamente",
            "Tipo" => "success"
        ]);
    }

    public function eliminar_articulo_controlador()
    {
        $id = mainModel::limpiar_string($_POST['id_eliminar_articuloOC']);

        if (isset($_SESSION['Sdatos_articuloOC'][$id])) {
            unset($_SESSION['Sdatos_articuloOC'][$id]);

            return json_encode([
                "Alerta" => "recargar",
                "Titulo" => "Artículo eliminado",
                "Texto" => "El artículo fue quitado correctamente",
                "Tipo" => "success"
            ]);
        } else {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El artículo no existe en la sesión",
                "Tipo" => "error"
            ]);
        }
    }
}

