<?php
if ($peticionAjax) {
    require_once "../modelos/proveedorModelo.php";
} else {
    require_once "./modelos/proveedorModelo.php";
}

class proveedorControlador extends proveedorModelo
{
    /** Listar ciudades */
    public function listar_ciudades_controlador()
    {
        return proveedorModelo::obtener_ciudades_modelo();
    }

    /** Agregar proveedor */
    public function agregar_proveedor_controlador()
    {
        $razon   = mainModel::limpiar_string($_POST['razon_social_reg']);
        $ruc     = mainModel::limpiar_string($_POST['ruc_reg']);
        $telefono = mainModel::limpiar_string($_POST['telefono_reg']);
        $direccion = mainModel::limpiar_string($_POST['direccion_reg']);
        $correo  = mainModel::limpiar_string($_POST['correo_reg']);
        $ciudad  = mainModel::limpiar_string($_POST['ciudad_reg']);
        $estado  = mainModel::limpiar_string($_POST['estado_reg']);

        if ($razon == "" || $ciudad == "" || $estado == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe completar los campos obligatorios",
                "Tipo" => "error"
            ]);
            exit();
        }

        if ($correo != "" && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Correo no válido",
                "Tipo" => "error"
            ]);
            exit();
        }

        $check = mainModel::ejecutar_consulta_simple(
            "SELECT idproveedores FROM proveedores WHERE razon_social='$razon'"
        );
        if ($check->rowCount() > 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El proveedor ya existe",
                "Tipo" => "error"
            ]);
            exit();
        }

        $datos = [
            "id_ciudad" => $ciudad,
            "razon_social" => $razon,
            "ruc" => $ruc,
            "telefono" => $telefono,
            "direccion" => $direccion,
            "correo" => $correo,
            "estado" => $estado
        ];

        $guardar = proveedorModelo::agregar_proveedor_modelo($datos);

        if ($guardar->rowCount() == 1) {
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Proveedor",
                "Texto" => "Proveedor registrado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo registrar el proveedor",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }

    /** Eliminar proveedor */
    public function eliminar_proveedor_controlador()
    {
        $id = mainModel::decryption($_POST['proveedor_id_del']);
        $id = mainModel::limpiar_string($id);

        $check = mainModel::ejecutar_consulta_simple(
            "SELECT idproveedores, estado FROM proveedores WHERE idproveedores='$id'"
        );

        if ($check->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "El proveedor no existe",
                "Tipo"   => "error"
            ]);
            exit();
        }

        $antes = $check->fetch(PDO::FETCH_ASSOC);

        $stmt = proveedorModelo::eliminar_proveedor_modelo($id);

        if ($stmt->rowCount() > 0) {

            // Verificar cómo quedó
            $verificar = mainModel::ejecutar_consulta_simple(
                "SELECT estado FROM proveedores WHERE idproveedores='$id'"
            );

            if ($verificar->rowCount() > 0) {
                // Sigue existiendo → fue desactivado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Proveedor desactivado",
                    "Texto"  => "El proveedor ya tenía movimientos, por lo que fue desactivado.",
                    "Tipo"   => "warning"
                ];
            } else {
                // Ya no existe → fue eliminado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Proveedor eliminado",
                    "Texto"  => "Proveedor eliminado correctamente.",
                    "Tipo"   => "success"
                ];
            }
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "No se pudo eliminar el proveedor",
                "Tipo"   => "error"
            ];
        }

        echo json_encode($alerta);
    }


    public function paginador_proveedores_controlador($pagina, $registros, $privilegio, $url, $busqueda)
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

        if ($busqueda != "") {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS p.*, c.ciu_descri
                     FROM proveedores p
                     INNER JOIN ciudades c ON c.id_ciudad = p.id_ciudad
                     WHERE (
                        p.razon_social LIKE '%$busqueda%' OR
                        p.ruc LIKE '%$busqueda%'
                     )
                     ORDER BY p.razon_social ASC
                     LIMIT $inicio,$registros";
        } else {
            $consulta = "SELECT SQL_CALC_FOUND_ROWS p.*, c.ciu_descri
                     FROM proveedores p
                     INNER JOIN ciudades c ON c.id_ciudad = p.id_ciudad
                     ORDER BY p.razon_social ASC
                     LIMIT $inicio,$registros";
        }

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta)->fetchAll();

        $total = (int)$conexion->query("SELECT FOUND_ROWS()")->fetchColumn();
        $Npaginas = ceil($total / $registros);

        $tabla .= '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center roboto-medium">
                <th>#</th>
                <th>RAZÓN SOCIAL</th>
                <th>RUC</th>
                <th>CIUDAD</th>
                <th>ESTADO</th>';

        if ($privilegio == 1 || $privilegio == 2) {
            $tabla .= '<th>ACTUALIZAR</th><th>ELIMINAR</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {
            $contador = $inicio + 1;

            foreach ($datos as $rows) {
                $estado = $rows['estado'] == 1
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';

                $tabla .= '<tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $rows['razon_social'] . '</td>
                <td>' . $rows['ruc'] . '</td>
                <td>' . $rows['ciu_descri'] . '</td>
                <td>' . $estado . '</td>';

                if ($privilegio == 1 || $privilegio == 2) {
                    $tabla .= '
                <td>
                    <a href="' . SERVERURL . 'proveedor-actualizar/' . mainModel::encryption($rows['idproveedores']) . '/" 
                       class="btn btn-success">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </td>
                <td>
                    <form class="FormularioAjax" action="' . SERVERURL . 'ajax/proveedorAjax.php"
                          method="POST" data-form="delete">
                        <input type="hidden" name="proveedor_id_del"
                               value="' . mainModel::encryption($rows['idproveedores']) . '">
                        <button type="submit" class="btn btn-warning">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </form>
                </td>';
                }

                $tabla .= '</tr>';
                $contador++;
            }
        } else {
            $tabla .= '<tr class="text-center">
            <td colspan="7">No hay registros</td>
        </tr>';
        }

        $tabla .= '</tbody></table></div>';

        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '<p class="text-right">
            Mostrando ' . $inicio . ' al ' . ($contador - 1) . ' de ' . $total . '
        </p>';
            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        echo $tabla;
    }

    /** controlador datos proveedor */
    public function datos_proveedor_controlador($tipo, $id)
    {
        $tipo = mainModel::limpiar_string($tipo);
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_string($id);

        return proveedorModelo::datos_proveedor_modelo($tipo, $id);
    }

    /** controlador actualizar proveedor */
    public function actualizar_proveedor_controlador()
    {
        $id = mainModel::decryption($_POST['proveedor_id_up']);
        $id = mainModel::limpiar_string($id);

        /* === comprobar existencia === */
        $check_prov = mainModel::ejecutar_consulta_simple(
            "SELECT * FROM proveedores WHERE idproveedores='$id'"
        );

        if ($check_prov->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "El PROVEEDOR no existe en el sistema",
                "Tipo"   => "error"
            ]);
            exit();
        } else {
            $campos = $check_prov->fetch();
        }

        /* === recibir datos === */
        $razon     = mainModel::limpiar_string($_POST['razon_social_up']);
        $ruc       = mainModel::limpiar_string($_POST['ruc_up']);
        $telefono  = mainModel::limpiar_string($_POST['telefono_up']);
        $correo    = mainModel::limpiar_string($_POST['correo_up']);
        $direccion = mainModel::limpiar_string($_POST['direccion_up']);
        $ciudad    = mainModel::limpiar_string($_POST['ciudad_up']);
        $estado    = mainModel::limpiar_string($_POST['estado_up']);

        /* === campos obligatorios === */
        if ($razon == "" || $ciudad == "" || $estado == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe completar los campos obligatorios",
                "Tipo" => "error"
            ]);
            exit();
        }

        /* === validar correo === */
        if ($correo != "" && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El correo no es válido",
                "Tipo" => "error"
            ]);
            exit();
        }

        /* === validar estado === */
        if (!in_array($estado, ['0', '1'], true)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El estado seleccionado no es válido",
                "Tipo" => "error"
            ]);
            exit();
        }

        /* === validar duplicado de razon social === */
        if ($razon != $campos['razon_social']) {
            $check_dup = mainModel::ejecutar_consulta_simple(
                "SELECT idproveedores FROM proveedores 
             WHERE razon_social='$razon' LIMIT 1"
            );
            if ($check_dup->rowCount() > 0) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto" => "La razón social ya se encuentra registrada",
                    "Tipo" => "error"
                ]);
                exit();
            }
        }

        /* === permisos === */
        session_start(['name' => 'STR']);
        if ($_SESSION['nivel_str'] < 1 || $_SESSION['nivel_str'] > 2) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No posee permisos para realizar esta operación",
                "Tipo" => "error"
            ]);
            exit();
        }

        /* === preparar datos === */
        $datos = [
            "id_ciudad"      => $ciudad,
            "razon_social"   => $razon,
            "ruc"            => $ruc,
            "telefono"       => $telefono,
            "direccion"      => $direccion,
            "correo"         => $correo,
            "estado"         => $estado,
            "idproveedores"  => $id
        ];

        /* === ejecutar update === */
        $update = proveedorModelo::actualizar_proveedor_modelo($datos);

        if ($update) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Proveedor actualizado",
                "Texto" => "Los datos del proveedor fueron modificados correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudieron actualizar los datos del proveedor",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }

    public function listar_proveedores_controlador()
    {
        return proveedorModelo::listar_proveedores_modelo();
    }
}
