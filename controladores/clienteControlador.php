<?php
if ($peticionAjax) {
    require_once "../modelos/clienteModelo.php";
} else {
    require_once "./modelos/clienteModelo.php";
}

class clienteControlador extends clienteModelo
{
    /** controlador agregar cliente*/
    public function agregar_cliente_controlador()
    {
        $doc_number = mainModel::limpiar_string($_POST['cliente_doc_reg']);
        $digitoV = mainModel::limpiar_string($_POST['cliente_dv_reg']);
        $nombre = mainModel::limpiar_string($_POST['cliente_nombre_reg']);
        $apellido = mainModel::limpiar_string($_POST['cliente_apellido_reg']);
        $telefono = mainModel::limpiar_string($_POST['cliente_telefono_reg']);
        $direccion = mainModel::limpiar_string($_POST['cliente_direccion_reg']);
        $doctype = mainModel::limpiar_string($_POST['tipo_documento']);
        $estadoC = mainModel::limpiar_string($_POST['cliente_estadoC']);
        $city = mainModel::limpiar_string($_POST['ciudad_reg']);
        $email = mainModel::limpiar_string($_POST['cliente_email_reg']);

        /** Comprobar campos vacios */
        if ($doc_number == "" || $nombre == "" || $direccion == "") {
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
        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{1,27}", $doc_number)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo Número de documento no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}", $nombre)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo NOMBRE no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($apellido != "") {
            if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}", $apellido)) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El formato del campo APELLIDO no es válido",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
        if ($telefono != "") {
            if (mainModel::verificarDatos("[0-9()+]{8,20}", $telefono)) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El formato del campo TELEFONO no es valido",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
        if ($digitoV != "") {
            if (mainModel::verificarDatos("[0-9()+]{1,2}", $digitoV)) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "El formato del campo DV no es valido",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
        if (mainModel::verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,150}", $direccion)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo DIRECCIÓN no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**comprobar email */
        if ($email != "") {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $check_email = mainModel::ejecutar_consulta_simple("SELECT email_cliente from clientes where email_cliente='$email'");
                if ($check_email->rowCount() > 0) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrio un error inesperado!",
                        "Texto" => "El EMAIL ingresado ya se encuentra registrado!",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado!",
                    "Texto" => "Ha ingresado un correo no valido!",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
        if ($estadoC != "") {
            // Si el valor NO está en la lista de permitidos, se muestra el error
            if (!in_array($estadoC, ['Soltero/a', 'Casado/a', 'Viudo/a', 'Divorciado/a'])) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El formato del estado civil no es válido.",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }

        /**Comprobacion de registros */
        $check_doc = mainModel::ejecutar_consulta_simple("SELECT doc_number from clientes where doc_number='$doc_number'");
        if ($check_doc->rowCount() > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El numero de documento ingresado ya se encuentra registrado!",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        /**Comprobar privilegios */
        if ($city < 1) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No ha seleccionado una ciudad valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        $datos_cliente = [
            "ciudad" => $city,
            "doctype" => $doctype,
            "doc_number" => $doc_number,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "direccion" => $direccion,
            "celular" => $telefono,
            "estadoC" => $estadoC,
            "estado" => "1",
            "dv" => $digitoV,
            "email" => $email,
        ];
        $agregar_cliente = clienteModelo::agregar_cliente_modelo($datos_cliente);
        if ($agregar_cliente->rowCount() == 1) {
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Cliente",
                "Texto" => "Los datos fueron registrados correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido registrar al cliente, favor intente nuevamente",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }

    /**Controlador paginar clientes */
    public function paginador_cliente_controlador($pagina, $registros, $privilegio, $url, $busqueda)
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
            $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM clientes 
            WHERE ((doc_number LIKE '%$busqueda%' OR nombre_cliente LIKE '%$busqueda%')) 
            ORDER BY nombre_cliente ASC LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM clientes 
            ORDER BY nombre_cliente ASC LIMIT $inicio,$registros";
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
								<th>CI</th>
								<th>CLIENTE</th>
								<th>TELÉFONO</th>
								<th>DIRECCIÓN</th>';
        if ($privilegio == 1 || $privilegio == 2) {
            $tabla .= '<th>ACTUALIZAR</th>
                                <th>ELIMINAR</th>';
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
								<td>' . $rows['doc_number'] . '</td>
								<td>' . $rows['nombre_cliente'] . ' ' . $rows['apellido_cliente'] . '</td>
								<td>' . $rows['celular_cliente'] . '</td>
								<td>
                                <button type="button" class="btn btn-info" data-toggle="popover"data-trigger="hover" title="' . $rows['nombre_cliente'] . ' ' . $rows['apellido_cliente'] . '"
                                    data-content="' . $rows['direccion_cliente'] . '">
                                         <i class="fas fa-info-circle"></i>
                                </button></td>';
                if ($privilegio == 1 || $privilegio == 2) {
                    $tabla .= '<td>
									<a href="' . SERVERURL . 'cliente-actualizar/' . mainModel::encryption($rows['id_cliente']) . '/" class="btn btn-success">
										<i class="fas fa-sync-alt"></i>
									</a>
								</td>
								<td>
									<form class="FormularioAjax" action="' . SERVERURL . 'ajax/clienteAjax.php" method="POST" data-form="delete" autocomplete="off" action="">
                                    <input type="hidden" name="cliente_id_del" value=' . mainModel::encryption($rows['id_cliente']) . '>
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
                $tabla .= '<tr class="text-center"> <td colspan="9"> <a href="' . $url . '" class="btn btn-reaised btn-primary btn-sm"> Haga click aqui para recargar el listado </a> </td> </tr> ';
            } else {
                $tabla .= '<tr class="text-center"> <td colspan="9"> No hay regitros en el sistema</td> </tr> ';
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

    /**Controlador eliminar cliente */
    public function eliminar_cliente_controlador()
    {
        $id = mainModel::decryption($_POST['cliente_id_del']);
        $id = mainModel::limpiar_string($id);

        $check_client = mainModel::ejecutar_consulta_simple("SELECT id_cliente FROM clientes WHERE id_cliente = '$id'");
        if ($check_client->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El CLIENTE que intenta eliminar no existe en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $check_ventas = mainModel::ejecutar_consulta_simple("SELECT id_cliente FROM factura WHERE id_cliente = '$id' LIMIT 1");
        if ($check_ventas->rowCount() < 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El CLIENTE no puede ser eliminado debido a que el cliente tiene facturas asociadas",
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
        $eliminar_cliente = clienteModelo::eliminar_cliente_modelo($id);
        if ($eliminar_cliente->rowCount() == 1) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Cliente eliminado!",
                "Texto" => "El Cliente ha sido eliminado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No se pudo eliminar el Cliente seleccionado",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
}
