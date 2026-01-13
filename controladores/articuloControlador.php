<?php
if ($peticionAjax) {
    require_once "../modelos/articuloModelo.php";
} else {
    require_once "./modelos/articuloModelo.php";
}

class articuloControlador extends articuloModelo
{
    /**Controlador paginar articulos */
    public function paginador_articulos_controlador($pagina, $registros, $privilegio, $url, $busqueda)
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $privilegio = mainModel::limpiar_string($privilegio);
        $busqueda = mainModel::limpiar_string($busqueda);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        if (isset($busqueda) && $busqueda != "") {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM articulos 
            WHERE ((id_articulo LIKE '%$busqueda%' OR codigo LIKE '%$busqueda%')) 
            ORDER BY desc_articulo ASC LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM articulos 
            ORDER BY desc_articulo ASC LIMIT $inicio,$registros";
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
								<th>CÓDIGO</th>
                                <th>NOMBRE</th>
                                <th>DETALLE</th>';
        if (mainModel::tienePermisoVista('articulo.editar')) {
            $tabla .=           '<th>ACTUALIZAR</th>';
        }
        if (mainModel::tienePermisoVista('articulo.eliminar')) {
            $tabla .= '<th>ELIMINAR</th>';
        }
        $tabla .= '
						</tr>
						</thead>
						<tbody>';
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
                                <button type="button" class="btn btn-info" data-toggle="popover"data-trigger="hover" title="' . 'Precio Compra: ' . number_format((float)$rows['precio_compra'], 0, ',', '.') . '"
                                    data-content="' . 'Precio Venta: ' . number_format((float)$rows['precio_venta'], 0, ',', '.') . '">
                                         <i class="fas fa-info-circle"></i>
                                </button></td>';
                if (mainModel::tienePermisoVista('articulo.editar')) {
                    $tabla .= '<td>
									<a href="' . SERVERURL . 'articulo-actualizar/' . mainModel::encryption($rows['id_articulo']) . '/" class="btn btn-success">
										<i class="fas fa-sync-alt"></i>
									</a>
								</td>
								';
                }
                if (mainModel::tienePermisoVista('articulo.eliminar')) {
                    $tabla .= ' <td>                          
									<form class="FormularioAjax" action="' . SERVERURL . 'ajax/articuloAjax.php" method="POST" data-form="delete" autocomplete="off" action="">
                                    <input type="hidden" name="articulo_id_del" value=' . mainModel::encryption($rows['id_articulo']) . '>
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

    /** controlador datos clientes  */
    public function datos_articulos_controlador($tipo, $id)
    {
        $tipo = mainModel::limpiar_string($tipo);
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_string($id);
        return articuloModelo::datos_articulos_modelo($tipo, $id);
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
        $proveedor = mainModel::limpiar_string($_POST['proveedor_reg']);
        $umedida = mainModel::limpiar_string($_POST['um_reg']);
        $iva = mainModel::limpiar_string($_POST['tipo_iva_reg']);
        $imarca = mainModel::limpiar_string($_POST['marca_reg']);
        $descrip = mainModel::limpiar_string($_POST['articulo_nombre_reg']);
        $pricesale = mainModel::limpiar_string($_POST['articulo_priceV_reg']);
        $pricebuy = mainModel::limpiar_string($_POST['articulo_priceC_reg']);
        $code = mainModel::limpiar_string($_POST['articulo_codigo_reg']);
        $estado = mainModel::limpiar_string($_POST['articuloEstadoReg']);
        $tipo = mainModel::limpiar_string($_POST['tipoprodReg']);

        /** Comprobar campos vacios */
        if ($code == "" || $descrip == "" || $pricebuy == "" || $pricesale == "") {
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
        if (mainModel::verificarDatos("[0-9]{1,15}", $pricebuy)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo Precio de compra no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[0-9]{1,15}", $pricesale)) {
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
        if ($proveedor < 0 || $proveedor == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El proveedor seleaccionado no corresponde",
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

        if ($estado < 0 || $estado > 1 || $estado == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El estado seleccionado no corresponde",
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
            "estado" => $estado,
            "tipo" => $tipo
        ];
        $agregar_articulo = articuloModelo::agregar_articulo_modelo($datos_articulo);
        if ($agregar_articulo->rowCount() == 1) {
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Articulo   ",
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

        $check_article = mainModel::ejecutar_consulta_simple("SELECT id_articulo FROM articulos WHERE id_articulo = '$id'");
        if ($check_article->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El ARTICULO que intenta eliminar no existe en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_compras = mainModel::ejecutar_consulta_simple("SELECT id_articulo FROM pedido_detalle WHERE id_articulo = '$id' LIMIT 1");
        if ($check_compras->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El ARTICULO no puede ser eliminado debido a que el registro tiene compras asociadas",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        session_start(['name' => 'STR']);
        if ($_SESSION['nivel_str'] == 3) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No tiene los permisos necesario para realizar esta operación",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $eliminar_articulo = articuloModelo::eliminar_articulo_modelo($id);
        if ($eliminar_articulo->rowCount() == 1) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Articulo eliminado!",
                "Texto" => "El ARTICULO ha sido eliminado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No se pudo eliminar el ARTICULO seleccionado",
                "Tipo" => "error"
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

        /**Comprobacion de registros */
        $check_id = mainModel::ejecutar_consulta_simple("SELECT * from articulos where id_articulo='$id'");
        if ($check_id->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El ARTICULO ingresado no existe!",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $campos_articulo_up = $check_id->fetch();
        }
        $id_categoria = mainModel::limpiar_string($_POST['categoria_up']);
        $idproveedores = mainModel::limpiar_string($_POST['proveedor_up']);
        $idunidad_medida = mainModel::limpiar_string($_POST['um_up']);
        $idiva = mainModel::limpiar_string($_POST['tipo_iva_up']);
        $id_marcas = mainModel::limpiar_string($_POST['marca_up']);
        $desc_articulo = mainModel::limpiar_string($_POST['articulo_nombre_up']);
        $precio_venta = mainModel::limpiar_string($_POST['articulo_priceV_up']);
        $precio_compra = mainModel::limpiar_string($_POST['articulo_priceC_up']);
        $codigo = mainModel::limpiar_string($_POST['articulo_codigo_up']);
        $estado = mainModel::limpiar_string($_POST['articulo_Estado_up']);
        $tipo = mainModel::limpiar_string($_POST['articulo_Tipo_up']);

        /** Comprobar campos vacios */
        if ($codigo == "" || $desc_articulo == "" || $precio_venta == "") {
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
        if (mainModel::verificarDatos("[0-9]{1,15}", $codigo)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo Código no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}", $desc_articulo)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo DESCRIPCION no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[0-9]{1,15}", $precio_compra)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo PRECIO COMPRA no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificarDatos("[0-9]{1,15}", $precio_venta)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo TELEFONO no es valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($codigo != $campos_articulo_up['codigo']) {
            $check_doc = mainModel::ejecutar_consulta_simple("SELECT codigo from articulos where codigo='$codigo'");
            if ($check_doc->rowCount() > 0) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El CÓDIGO ingresado ya se encuentra registrado!",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
        if (!in_array($estado, ['0', '1'], true)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto"  => "El estado seleccionado no es válido",
                "Tipo"   => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        session_start(['name' => 'STR']);
        if ($_SESSION['nivel_str'] < 1 || $_SESSION['nivel_str'] > 2) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No posee los permisos necesarios para realizar esta operación",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
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
        if (articuloModelo::actualizar_articulo_modelo($datos_articulo_up)) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "ARTICULO modificado",
                "Texto" => "Los datos del ARTICULO han sido modificados correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos pido actualizar los datos del ARTICULO!",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
        exit();
    }
    /**fin controlador */
}
