<?php
require_once __DIR__ . "/../modelos/pedidoModelo.php";


class pedidoControlador extends pedidoModelo
{
    /**controlador buscar articulo */
    public function buscar_articulo_controlador()
    {
        // BUSCAR ARTÍCULO (HTML)
        session_start(['name' => 'STR']);
        if (isset($_POST['buscar_articulo'])) {
            $articulo = mainModel::limpiar_string($_POST['buscar_articulo']);
            if ($articulo == "") return '<div class="alert alert-warning">Debes introducir código o descripción</div>';

            $datos_articulo = mainModel::ejecutar_consulta_simple("
                SELECT a.*, COALESCE(s.stockDisponible, 0) AS stock_actual
                FROM articulos a
                LEFT JOIN stock s
                    ON s.id_articulo = a.id_articulo
                    AND s.id_sucursal = '" . $_SESSION['nick_sucursal'] . "'
                WHERE (a.codigo like '%$articulo%' OR a.desc_articulo like '%$articulo%')
                AND a.estado=1
                AND a.tipo !='servicio'
                ORDER BY a.desc_articulo DESC
            ");

            if ($datos_articulo->rowCount() >= 1) {
                $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>';
                foreach ($datos_articulo->fetchAll() as $rows) {
                    $tabla .= '<tr class="text-center">
                    <td>' . $rows['codigo'] . ' - ' . $rows['desc_articulo'] . '</td>
                    <td>Stock: ' . number_format((float)$rows['stock_actual'], 0, ',', '.') . '</td>
                    <td style="width:100px;"><input type="number" id="cantidad_' . $rows['id_articulo'] . '" class="form-control form-control-sm" value="1" min="1"></td>
                    <td><button type="button" class="btn btn-primary btn-sm" onclick="agregar_articulo(' . $rows['id_articulo'] . ')"><i class="fas fa-plus-circle"></i></button></td>
                </tr>';
                }
                $tabla .= '</tbody></table></div>';
                return $tabla;
            } else return '<div class="alert alert-warning">No se encontraron artículos que coincidan</div>';
        }
    }
    /**controlador buscador articulo */
    public function articulo_controlador()
    {
        session_start(['name' => 'STR']);
        // AGREGAR ARTÍCULO
        if (isset($_POST['id_agregar_articulo'])) {

            $id = mainModel::limpiar_string($_POST['id_agregar_articulo']);
            $cantidad = mainModel::limpiar_string($_POST['detalle_cantidad']);

            $check_articulo = mainModel::ejecutar_consulta_simple("
                SELECT a.*, COALESCE(s.stockDisponible, 0) AS stock_actual
                FROM articulos a
                LEFT JOIN stock s
                    ON s.id_articulo = a.id_articulo
                    AND s.id_sucursal = '" . $_SESSION['nick_sucursal'] . "'
                WHERE a.id_articulo='$id'
                AND a.estado=1
                LIMIT 1
            ");
            if ($check_articulo->rowCount() <= 0)
                die(json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "No se encontró el artículo", "Tipo" => "error"]));

            $campos = $check_articulo->fetch();
            if ($cantidad == "" || !is_numeric($cantidad) || intval($cantidad) <= 0)
                die(json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "Cantidad inválida", "Tipo" => "error"]));

            if (isset($_SESSION['datos_articulo'][$id])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" =>  "El articulo que intenta agregar ya se encuentra agregado",
                    "Tipo" => "error"
                ];
            } else {
                $_SESSION['datos_articulo'][$id] = [
                    "ID" => $campos['id_articulo'],
                    "codigo" => $campos['codigo'],
                    "descripcion" => $campos['desc_articulo'],
                    "cantidad" => $cantidad,
                    "stock_actual" => (int)$campos['stock_actual']
                ];
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Articulo agregado!",
                    "Texto" =>  "El articulo ha sido agregado",
                    "Tipo" => "success"
                ];
            }
            echo json_encode($alerta);
            exit();
        }
    }
    /**fin controlador */
    /**controlador eliminar articulo */
    public function eliminar_articulo_controlador()
    {
        $id  = mainModel::limpiar_string($_POST['id_eliminar_articulo']);
        session_start(['name' => 'STR']);
        unset($_SESSION['datos_articulo'][$id]);
        if (empty($_SESSION['datos_articulo'][$id])) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Articulo removido!",
                "Texto" => "Los datos del articulo fueron removidos correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido remover los datos del articulo",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /**fin controlador */
    public function datos_pedido_controlador($tipo, $id)
    {
        $tipo  = mainModel::limpiar_string($tipo);

        $id  = mainModel::decryption($id);
        $id  = mainModel::limpiar_string($id);

        return pedidoModelo::datos_pedido_modelo($tipo, $id);
    }
    /**fin controlador */

    /**controlador agregar pedido */
    public function agregar_pedido_controlador()
    {
        session_start(['name' => 'STR']);
        if (!mainModel::tienePermiso('compra.pedido.crear')) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Acceso no autorizado!",
                "Texto" => "No tienes permisos para realizar esta acción",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (empty($_SESSION['datos_articulo'])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error!",
                "Texto" => "No has seleccionado ningun artículo para el pedido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        /** Insertar cabecera */
        $datos_pedido_agg = [
            "usuario"   => $_SESSION['id_str'],
            "sucursal"  => $_SESSION['nick_sucursal']
        ];

        $idPedidoCabecera = pedidoModelo::agregar_pedidoC_modelo($datos_pedido_agg);

        if ($idPedidoCabecera <= 0) {
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
        foreach ($_SESSION['datos_articulo'] as $article) {

            $detalle_reg = [
                "pedidoid" => $idPedidoCabecera,
                "articulo" => $article['ID'],
                "cantidad" => $article['cantidad'],
                "stock_actual" => $article['stock_actual'] ?? 0
            ];

            $detalleInsert = pedidoModelo::agregar_pedidoD_modelo($detalle_reg);

            if ($detalleInsert->rowCount() != 1) {
                $errores_detalles++;
            }
        }

        if ($errores_detalles > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error parcial",
                "Texto" => "El pedido se creó, pero algunos artículos no se guardaron",
                "Tipo" => "warning"
            ];
        } else {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Pedido guardado!",
                "Texto" => "El pedido se registró correctamente",
                "Tipo" => "success"
            ];
        }
        unset($_SESSION['datos_articulo']);
        echo json_encode($alerta);
    }
    /**fin controlador */
    /**Controlador paginar articulos */
    public function paginador_pedidos_controlador($pagina, $registros, $url, $busqueda1, $busqueda2, $estado_pedido = '')
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $busqueda1 = mainModel::limpiar_string($busqueda1);
        $busqueda2 = mainModel::limpiar_string($busqueda2);
        $estado_pedido = mainModel::limpiar_string($estado_pedido);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        $filtros = "";
        if ($estado_pedido !== '') {
            $filtros .= " AND pc.estado = '$estado_pedido'";
        }

        if (!empty($busqueda1) && !empty($busqueda2)) {
            $consulta = "
            SELECT SQL_CALC_FOUND_ROWS
                pc.idpedido_cabecera,
                pc.id_sucursal,
                pc.id_usuario,
                pc.fecha,
                pc.estado AS estadoPe,
                pc.updated,
                pc.updatedby,

                u.usu_nombre,
                u.usu_apellido,
                u.usu_estado,
                u.usu_nick

            FROM pedido_cabecera pc
            INNER JOIN usuarios u ON u.id_usuario = pc.id_usuario
            WHERE DATE(pc.fecha) >= '$busqueda1'
              AND DATE(pc.fecha) <= '$busqueda2'
              AND pc.id_sucursal = '" . $_SESSION['nick_sucursal'] . "'
              $filtros
            ORDER BY pc.fecha DESC
            LIMIT $inicio,$registros
        ";
        } else {
            $consulta = "
            SELECT SQL_CALC_FOUND_ROWS
                pc.idpedido_cabecera,
                pc.id_sucursal,
                pc.id_usuario,
                pc.fecha,
                pc.estado AS estadoPe,
                pc.updated,
                pc.updatedby,

                u.usu_nombre,
                u.usu_apellido,
                u.usu_estado,
                u.usu_nick

            FROM pedido_cabecera pc
            INNER JOIN usuarios u ON u.id_usuario = pc.id_usuario
            WHERE pc.id_sucursal = '" . $_SESSION['nick_sucursal'] . "'
              " . ($estado_pedido === '' ? "AND pc.estado != 0" : "") . "
              $filtros
            ORDER BY pc.fecha DESC
            LIMIT $inicio,$registros
        ";
        }

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta)->fetchAll();

        $total = (int)$conexion->query("SELECT FOUND_ROWS()")->fetchColumn();
        $Npaginas = ceil($total / $registros);

        $tabla .= '
        <div class="table-responsive">
        <table class="table table-dark table-sm">
            <thead>
                <tr class="text-center roboto-medium">
                    <th>#</th>
                    <th>CÓDIGO PEDIDO</th>
                    <th>FECHA</th>
                    <th>CREADO POR</th>
                    <th>ESTADO</th>
                    <th>PDF</th>';

        if (mainModel::tienePermiso('compra.pedido.anular')) {
            $tabla .=           '<th>ANULAR</th>';
        }

        $tabla .= '
                </tr>
            </thead>
            <tbody>
        ';

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;

            foreach ($datos as $rows) {

                switch ($rows['estadoPe']) {
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
                    <td>' . $rows['idpedido_cabecera'] . '</td>
                    <td>' . date("d-m-Y", strtotime($rows['fecha'])) . '</td>
                    <td>' . $rows['usu_nombre'] . ' ' . $rows['usu_apellido'] . '</td>
                    <td>' . $estadoBadge . '</td>';

                /* ===== PDF ===== */
                if ($rows['estadoPe'] != 0) {
                    $tabla .= '
                    <td>
                        <a href="' . SERVERURL . 'pdf/pedido.php?id=' . mainModel::encryption($rows['idpedido_cabecera']) . '"
                            target="_blank"
                            class="btn btn-danger btn-sm"
                            title="Imprimir pedido">
                            <i class="fas fa-file-pdf"></i>
                        </a>

                    </td>';
                } else {
                    $tabla .= '<td>-</td>';
                }

                if (mainModel::tienePermiso('compra.pedido.anular')) {
                    $tabla .= '
                    <td>
                        <form class="FormularioAjax"
                              action="' . SERVERURL . 'ajax/pedidoAjax.php"
                              method="POST"
                              data-form="delete"
                              autocomplete="off">
                            <input type="hidden" name="pedido_id_del"
                                   value="' . mainModel::encryption($rows['idpedido_cabecera']) . '">
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
                    <td colspan="8">
                        <a href="' . $url . '" class="btn btn-raised btn-primary btn-sm">
                            Haga click aquí para recargar el listado
                        </a>
                    </td>
                </tr>';
            } else {
                $tabla .= '
                <tr class="text-center">
                    <td colspan="8">No hay registros en el sistema</td>
                </tr>';
            }
        }

        $tabla .= '
            </tbody>
        </table>
        </div>
        ';

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

    /**Controlador anular pedido */
    public function anular_pedido_controlador()
    {
        $id = mainModel::decryption($_POST['pedido_id_del']);
        $id = mainModel::limpiar_string($id);
        session_start(['name' => 'STR']);

        $check_pedido = mainModel::ejecutar_consulta_simple("SELECT idpedido_cabecera FROM pedido_cabecera WHERE idpedido_cabecera = '$id' AND id_sucursal = '" . $_SESSION['nick_sucursal'] . "'");
        if ($check_pedido->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El pedido que intenta anular no existe en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $check_pedidoestado = mainModel::ejecutar_consulta_simple("SELECT idpedido_cabecera FROM pedido_cabecera WHERE idpedido_cabecera = '$id' AND estado = 2 AND id_sucursal = '" . $_SESSION['nick_sucursal'] . "'");
        if ($check_pedidoestado->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El pedido que intenta anular se encuentra procesado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (!mainModel::tienePermiso('compra.pedido.anular')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Advertencia!",
                "Texto" => "No posee los permisos necesarios para realizar esta acción",
                "Tipo" => "error"
            ]);
        }
        $datos_pedido_del = [
            "updatedby" => $_SESSION['id_str'],
            "sucursal" => $_SESSION['nick_sucursal'],
            "idpedido_cabecera" => $id
        ];

        if (pedidoModelo::anular_pedido_modelo($datos_pedido_del)) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Pedido Anulado!",
                "Texto" => "El PEDIDO ha sido anulado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No se pudo anular el PEDIDO seleccionado",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /**fin controlador */
    public function decrypt($valor)
    {
        return mainModel::decryption($valor);
    }

    public function datos_pedido_controladorPDF($idPedido)
    {
        return [
            'cabecera' => pedidoModelo::obtener_pedido_cabecera($idPedido),
            'detalle'  => pedidoModelo::obtener_pedido_detalle($idPedido)
        ];
    }
}
