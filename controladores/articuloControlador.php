<?php
if ($peticionAjax) {
    require_once "../modelos/articuloModelo.php";
} else {
    require_once "./modelos/articuloModelo.php";
}

class articuloControlador extends articuloModelo
{
    /**Controlador listar articulos */
    public function listar_articulos_controlador($pagina, $registros, $url, $busqueda)
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $busqueda = mainModel::limpiar_string($busqueda);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        /* ================= FILTROS ================= */

        $filtrosSQL = "";

        if ($busqueda != "") {

            $busqueda = mainModel::limpiar_string($busqueda);

            $filtrosSQL .= " AND (
            id_articulo LIKE '%$busqueda%' 
            OR codigo LIKE '%$busqueda%' 
            OR desc_articulo LIKE '%$busqueda%'
        )";
        }

        /* ================= DATOS ================= */

        $res = articuloModelo::listar_articulos_modelo($inicio, $registros, $filtrosSQL);

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        /* ================= TABLA ================= */

        $tabla .= '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center roboto-medium">
                <th>#</th>
                <th>CÓDIGO</th>
                <th>NOMBRE</th>
                <th>DETALLE</th>';

        if (mainModel::tienePermiso('articulo.editar')) {
            $tabla .= '<th>ACTUALIZAR</th>';
        }
        if (mainModel::tienePermiso('articulo.eliminar')) {
            $tabla .= '<th>ELIMINAR</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;

            foreach ($datos as $rows) {

                $tabla .= '
            <tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $rows['codigo'] . '</td>
                <td>' . $rows['desc_articulo'] . '</td>
                <td>
                    <button type="button" class="btn btn-info"
                        data-toggle="popover"
                        data-trigger="hover"
                        title="Precio Compra: ' . number_format((float)$rows['precio_compra'], 0, ',', '.') . '"
                        data-content="Precio Venta: ' . number_format((float)$rows['precio_venta'], 0, ',', '.') . '">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </td>';

                if (mainModel::tienePermiso('articulo.editar')) {
                    $tabla .= '
                <td>
                    <a href="' . SERVERURL . 'articulo-actualizar/' . mainModel::encryption($rows['id_articulo']) . '/"
                    class="btn btn-success">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </td>';
                }

                if (mainModel::tienePermiso('articulo.eliminar')) {
                    $tabla .= '
                <td>
                    <form class="FormularioAjax"
                        action="' . SERVERURL . 'ajax/articuloAjax.php"
                        method="POST"
                        data-form="delete">

                        <input type="hidden"
                        name="articulo_id_del"
                        value="' . mainModel::encryption($rows['id_articulo']) . '">

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
                $tabla .= '<tr class="text-center">
                <td colspan="6">
                    <a href="' . $url . '" class="btn btn-raised btn-primary btn-sm">
                        Haga click aquí para recargar el listado
                    </a>
                </td>
            </tr>';
            } else {
                $tabla .= '<tr class="text-center">
                <td colspan="6">No hay registros en el sistema</td>
            </tr>';
            }
        }

        $tabla .= '</tbody></table></div>';

        /* ================= PAGINADOR ================= */

        if ($total >= 1 && $pagina <= $Npaginas) {

            $tabla .= '<p class="text-right">
            Mostrando registro ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '
        </p>';

            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        echo $tabla;
    }
    /**fin controlador */

    /** controlador datos clientes  */
    public function datos_articulos_controlador($tipo, $id)
    {
        $tipo = mainModel::limpiar_string($tipo);
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_string($id);
        return articuloModelo::datos_articulos_modelo($tipo, $id);
    }


    public function datos_articulo_proveedor_controlador($id)
    {
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_string($id);

        return articuloModelo::obtener_articulo_con_proveedor_modelo($id);
    }

    /**fin controlador */
    public function listar_iva_controlador()
    {
        $iva = articuloModelo::obtener_impuestos_modelo(); // Llamamos al método protegido desde la clase hija
        return $iva;
    }

    public function listar_proveedores_controlador()
    {
        $proveedor = articuloModelo::obtener_proveedores_modelo(); // Llamamos al método protegido desde la clase hija
        return $proveedor;
    }

    public function listar_um_controlador()
    {
        $um = articuloModelo::obtener_UM_modelo(); // Llamamos al método protegido desde la clase hija
        return $um;
    }

    public function listar_cate_controlador()
    {
        $categoria = articuloModelo::obtener_cate_modelo(); // Llamamos al método protegido desde la clase hija
        return $categoria;
    }

    public function listar_marca_controlador()
    {
        $marca = articuloModelo::obtener_marca_modelo(); // Llamamos al método protegido desde la clase hija
        return $marca;
    }

    /**agregar articulo */
    public function agregar_articulo_controlador()
    {
        $categoria = mainModel::limpiar_string($_POST['categoria_reg']);
        $proveedor = mainModel::limpiar_string($_POST['proveedor_reg'] ?? '');
        $umedida = mainModel::limpiar_string($_POST['um_reg']);
        $iva = mainModel::limpiar_string($_POST['tipo_iva_reg']);
        $imarca = mainModel::limpiar_string($_POST['marca_reg']);
        $descrip = mainModel::limpiar_string($_POST['articulo_nombre_reg']);
        $pricesale = mainModel::limpiar_string($_POST['articulo_priceV_reg']);
        $pricebuy = mainModel::limpiar_string($_POST['articulo_priceC_reg'] ?? '');
        $code = mainModel::limpiar_string($_POST['articulo_codigo_reg']);
        $tipo = mainModel::limpiar_string($_POST['tipoprodReg']);

        /** Comprobar campos vacios */
        if ($code == "" || $descrip == "" || $pricesale == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No has llenado todos los campos que son obligatorios",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**verificar integridad de datos  */
        if (mainModel::verificarDatos("[0-9]{1,15}", $code)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo Código no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($pricebuy !== "" && mainModel::verificarDatos("[0-9]+(\.[0-9]{1,2})?", $pricebuy)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo Precio de compra no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[0-9]+(\.[0-9]{1,2})?", $pricesale)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo Precio de Venta no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**verificar integridad de datos  */
        if (mainModel::verificarDatos("[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,60}", $descrip)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo Descripción no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($iva < 0 || $iva == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El tipo de Impuesto seaccionado no corresponde",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($umedida < 0 || $umedida == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La Unidad de medida seleaccionado no corresponde",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($categoria < 0 || $categoria == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La categoria seleaccionada no corresponde",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($imarca < 0 || $imarca == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "La marca seleccionada no corresponde",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        /**Comprobacion de registros */
        $check_code = mainModel::ejecutar_consulta_simple("SELECT codigo from articulos where codigo='$code'");
        if ($check_code->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El articulo ingresado ya se encuentra registrado!",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $datos_articulo = [
            "id_categoria" => $categoria,
            "idproveedores" => $proveedor,
            "idunidad_medida" => $umedida,
            "idiva" => $iva,
            "id_marcas" => $imarca,
            "descrip" => $descrip,
            "pricesale" => $pricesale,
            "pricebuy" => $pricebuy,
            "code" => $code,
            "tipo" => $tipo
        ];
        $agregar_articulo = articuloModelo::agregar_articulo_modelo($datos_articulo);
        if ($agregar_articulo->rowCount() == 1) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Articulo  Registrado",
                "Texto" => "Los datos fueron registrados correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido registrar el articulo, favor intente nuevamente",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /** fin controlador */

    /** controlador eliminar articulo */
    public function eliminar_articulo_controlador()
    {
        $id = mainModel::decryption($_POST['articulo_id_del']);
        $id = mainModel::limpiar_string($id);

        $check_article = mainModel::ejecutar_consulta_simple(
            "SELECT id_articulo, estado 
         FROM articulos 
         WHERE id_articulo = '$id'"
        );

        if ($check_article->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "El artículo no existe en el sistema",
                "Tipo"   => "error"
            ]);
            exit();
        }

        session_start(['name' => 'STR']);
        if (!mainModel::tienePermiso('articulo.eliminar')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Advertencia!",
                "Texto" => "No posee los permisos necesarios para realizar esta acción",
                "Tipo" => "error"
            ]);
        }

        $stmt = articuloModelo::eliminar_articulo_modelo($id);

        if ($stmt->rowCount() > 0) {

            // Verificar cómo quedó
            $verificar = mainModel::ejecutar_consulta_simple(
                "SELECT estado 
             FROM articulos 
             WHERE id_articulo = '$id'"
            );

            if ($verificar->rowCount() > 0) {
                // Sigue existiendo → fue desactivado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Artículo desactivado",
                    "Texto"  => "El artículo ya tiene movimientos asociados, por lo que fue desactivado.",
                    "Tipo"   => "warning"
                ];
            } else {
                // Ya no existe → fue eliminado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Artículo eliminado",
                    "Texto"  => "El artículo fue eliminado correctamente.",
                    "Tipo"   => "success"
                ];
            }
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "No se pudo eliminar el artículo seleccionado",
                "Tipo"   => "error"
            ];
        }

        echo json_encode($alerta);
    }

    /**fin controlador */

    /** controlador actualizar articulo */
    public function actualizar_articulo_controlador()
    {
        $id = mainModel::decryption($_POST['articulo_id_up']);
        $id = mainModel::limpiar_string($id);

        /* ===== VALIDAR EXISTENCIA ===== */
        $check_id = mainModel::ejecutar_consulta_simple("SELECT * FROM articulos WHERE id_articulo='$id'");

        if ($check_id->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El ARTICULO no existe",
                "Tipo" => "error"
            ]);
            exit();
        }

        $campos_articulo_up = $check_id->fetch();

        /* ===== CAPTURA DE DATOS ===== */
        $id_categoria     = mainModel::limpiar_string($_POST['categoria_up'] ?? null);
        $idproveedores    = mainModel::limpiar_string($_POST['proveedor_up'] ?? '');
        $idunidad_medida  = mainModel::limpiar_string($_POST['um_up'] ?? null);
        $idiva            = mainModel::limpiar_string($_POST['tipo_iva_up'] ?? null);
        $id_marcas        = mainModel::limpiar_string($_POST['marca_up'] ?? null);

        $desc_articulo = mainModel::limpiar_string($_POST['articulo_nombre_up']);
        $precio_venta = trim($_POST['articulo_priceV_up']);
        $precio_venta = str_replace(',', '.', $precio_venta);
        $precio_compra = trim($_POST['articulo_priceC_up'] ?? '');
        $precio_compra = str_replace(',', '.', $precio_compra);
        $codigo        = mainModel::limpiar_string($_POST['articulo_codigo_up']);
        $estado        = mainModel::limpiar_string($_POST['articulo_estado_up'] ?? 1);
        $tipo          = mainModel::limpiar_string($_POST['tipoprodUp'] ?? 1);

        /* ===== VALIDAR SELECTS ===== */
        mainModel::validarSelect($id_categoria, "una categoría");
        mainModel::validarSelect($idunidad_medida, "una unidad de medida");
        mainModel::validarSelect($idiva, "el tipo de IVA");
        mainModel::validarSelect($id_marcas, "una marca");

        /* ===== VALIDAR CAMPOS ===== */
        if ($codigo == "" || $desc_articulo == "" || $precio_venta == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Campos obligatorios incompletos",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (mainModel::verificarDatos("[0-9]{1,15}", $codigo)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Código inválido",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{1,140}", $desc_articulo)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Descripción inválida",
                "Tipo" => "error"
            ]);
            exit();
        }

        if ($precio_compra !== "" && !is_numeric($precio_compra)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Precio compra inválido",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (!is_numeric($precio_venta)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Precio venta inválido",
                "Tipo" => "error"
            ]);
            exit();
        }

        /* ===== VALIDAR DUPLICADO ===== */
        if ($codigo != $campos_articulo_up['codigo']) {
            $check_doc = mainModel::ejecutar_consulta_simple("SELECT codigo FROM articulos WHERE codigo='$codigo'");
            if ($check_doc->rowCount() > 0) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto" => "El código ya existe",
                    "Tipo" => "error"
                ]);
                exit();
            }
        }

        /* ===== VALIDAR ESTADO ===== */
        if (!in_array($estado, ['0', '1'], true)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Estado inválido",
                "Tipo" => "error"
            ]);
            exit();
        }

        /* ===== PERMISOS ===== */
        session_start(['name' => 'STR']);
        if (!mainModel::tienePermiso('articulo.editar')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Sin permisos",
                "Texto" => "No puede editar",
                "Tipo" => "error"
            ]);
            exit();
        }

        /* ===== DATA ===== */
        $datos_articulo_up = [
            "id_categoria" => $id_categoria,
            "idproveedores" => $idproveedores,
            "idunidad_medida" => $idunidad_medida,
            "idiva" => $idiva,
            "id_marcas" => $id_marcas,
            "desc_articulo" => $desc_articulo,
            "precio_venta" => $precio_venta,
            "precio_compra" => $precio_compra,
            "estado" => $estado,
            "codigo" => $codigo,
            "tipo" => $tipo,
            "id_articulo" => $id
        ];

        /* ===== UPDATE ===== */
        if (articuloModelo::actualizar_articulo_modelo($datos_articulo_up)) {

            echo json_encode([
                "Alerta" => "recargar",
                "Titulo" => "Actualizado",
                "Texto" => "Artículo actualizado correctamente",
                "Tipo" => "success",
            ]);
        } else {

            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo actualizar",
                "Tipo" => "error"
            ]);
        }

        exit();
    }
    /**fin controlador */
}
