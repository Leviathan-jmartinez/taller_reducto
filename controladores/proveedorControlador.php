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
        if (!mainModel::tienePermiso('proveedor.crear')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para registrar proveedores",
                "Tipo" => "error"
            ]);
            exit();
        }

        $razon   = mainModel::limpiar_string($_POST['razon_social_reg'] ?? "");
        $ruc     = mainModel::limpiar_string($_POST['ruc_reg'] ?? "");
        $telefono = mainModel::limpiar_string($_POST['telefono_reg'] ?? "");
        $direccion = mainModel::limpiar_string($_POST['direccion_reg'] ?? "");
        $correo  = mainModel::limpiar_string($_POST['correo_reg'] ?? "");
        $ciudad  = mainModel::limpiar_string($_POST['ciudad_reg'] ?? "");
        $estado  = mainModel::limpiar_string($_POST['estado_reg'] ?? "");

        if ($razon == "" || $ruc == "" || $ciudad == "" || $estado == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe completar los campos obligatorios",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,&-]{3,70}", $razon)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La razon social no tiene un formato valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (mainModel::verificarDatos("[0-9-]{6,15}", $ruc)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El RUC no tiene un formato valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        if ($telefono != "" && mainModel::verificarDatos("[0-9()+ -]{6,30}", $telefono)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El telefono no tiene un formato valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        if ($direccion != "" && mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,#°\/-]{3,120}", $direccion)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La direccion no tiene un formato valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (mainModel::verificarDatos("[0-9]{1,10}", $ciudad)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La ciudad seleccionada no corresponde",
                "Tipo" => "error"
            ]);
            exit();
        }

        if ($estado != "0" && $estado != "1") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El estado seleccionado no es valido",
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
            "SELECT idproveedores FROM proveedores WHERE ruc='$ruc'"
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
        if (!mainModel::tienePermiso('proveedor.eliminar')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para eliminar proveedores",
                "Tipo" => "error"
            ]);
            exit();
        }

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

        $stmt = proveedorModelo::eliminar_proveedor_modelo($id);

        if ($stmt->rowCount() > 0) {

            $verificar = mainModel::ejecutar_consulta_simple(
                "SELECT estado FROM proveedores WHERE idproveedores='$id'"
            );

            if ($verificar->rowCount() > 0) {

                $row = $verificar->fetch();

                if ($row['estado'] == 0) {

                    $alerta = [
                        "Alerta" => "recargar",
                        "Titulo" => "Proveedor desactivado",
                        "Texto"  => "El proveedor tiene registros relacionados (artículos o pedidos). Fue desactivado.",
                        "Tipo"   => "warning"
                    ];
                } else {

                    $alerta = [
                        "Alerta" => "recargar",
                        "Titulo" => "Proveedor actualizado",
                        "Texto"  => "Proveedor modificado.",
                        "Tipo"   => "success"
                    ];
                }
            } else {

                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Proveedor eliminado",
                    "Texto"  => "Proveedor eliminado correctamente.",
                    "Tipo"   => "success"
                ];
            }
        }

        echo json_encode($alerta);
    }


    public function paginador_proveedores_controlador($pagina, $registros, $url, $busqueda)
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
            p.razon_social LIKE '%$busqueda%' 
            OR p.ruc LIKE '%$busqueda%'
        )";
        }

        /* ================= DATOS ================= */

        $res = proveedorModelo::listar_proveedores_modelo($inicio, $registros, $filtrosSQL);

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        /* ================= TABLA ================= */

        $tabla .= '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center roboto-medium">
                <th>#</th>
                <th>RAZÓN SOCIAL</th> 
                <th>RUC</th> 
                <th>CIUDAD</th> 
                <th>ESTADO</th>';

        if (mainModel::tienePermiso('proveedor.editar')) {
            $tabla .= '<th>ACTUALIZAR</th>';
        }
        if (mainModel::tienePermiso('proveedor.eliminar')) {
            $tabla .= '<th>ELIMINAR</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;

            foreach ($datos as $rows) {

                $estado = ($rows['estado'] == 1)
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';

                $tabla .= '
            <tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $rows['razon_social'] . '</td>
                <td>' . $rows['ruc'] . '</td>
                <td>' . $rows['ciu_descri'] . '</td>
                <td>' . $estado . '</td>';

                if (mainModel::tienePermiso('proveedor.editar')) {
                    $tabla .= '
                <td>
                    <a href="' . SERVERURL . 'proveedor-actualizar/' . mainModel::encryption($rows['idproveedores']) . '/"
                    class="btn btn-success">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </td>';
                }

                if (mainModel::tienePermiso('proveedor.eliminar')) {
                    $tabla .= '
                <td>
                    <form class="FormularioAjax"
                        action="' . SERVERURL . 'ajax/proveedorAjax.php"
                        method="POST"
                        data-form="delete">

                        <input type="hidden"
                        name="proveedor_id_del"
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

            $reg_final = $contador - 1;
        } else {

            if ($total >= 1) {
                $tabla .= '<tr class="text-center">
                <td colspan="7">
                    <a href="' . SERVERURL . 'proveedor-lista/" 
                    class="btn btn-raised btn-primary btn-sm">
                    Haga click aquí para recargar el listado
                    </a>
                </td>
            </tr>';
            } else {
                $tabla .= '<tr class="text-center">
                <td colspan="7">No hay registros en el sistema</td>
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
        $razon     = mainModel::limpiar_string($_POST['razon_social_up'] ?? "");
        $ruc       = mainModel::limpiar_string($_POST['ruc_up'] ?? "");
        $telefono  = mainModel::limpiar_string($_POST['telefono_up'] ?? "");
        $correo    = mainModel::limpiar_string($_POST['correo_up'] ?? "");
        $direccion = mainModel::limpiar_string($_POST['direccion_up'] ?? "");
        $ciudad    = mainModel::limpiar_string($_POST['ciudad_up'] ?? "");
        $estado    = mainModel::limpiar_string($_POST['estado_up'] ?? "");

        /* === campos obligatorios === */
        if ($razon == "" || $ruc == "" || $ciudad == "" || $estado == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe completar los campos obligatorios",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,&-]{3,70}", $razon)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La razon social no tiene un formato valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (mainModel::verificarDatos("[0-9-]{6,15}", $ruc)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El RUC no tiene un formato valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        if ($telefono != "" && mainModel::verificarDatos("[0-9()+ -]{6,30}", $telefono)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El telefono no tiene un formato valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        if ($direccion != "" && mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,#°\/-]{3,120}", $direccion)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La direccion no tiene un formato valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (mainModel::verificarDatos("[0-9]{1,10}", $ciudad)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La ciudad seleccionada no corresponde",
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

        /* === validar duplicado de RUC === */
        if ($ruc != $campos['ruc']) {
            $check_dup = mainModel::ejecutar_consulta_simple(
                "SELECT idproveedores FROM proveedores 
             WHERE ruc='$ruc' LIMIT 1"
            );
            if ($check_dup->rowCount() > 0) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto" => "El RUC ya se encuentra registrado",
                    "Tipo" => "error"
                ]);
                exit();
            }
        }

        /* === permisos === */
        session_start(['name' => 'STR']);
        if (!mainModel::tienePermiso('proveedor.editar')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Advertencia!",
                "Texto" => "No posee los permisos necesarios para realizar esta acción",
                "Tipo" => "error"
            ]);
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
                "Alerta" => "redireccionar_confirmado",
                "URL" => SERVERURL . "proveedor-nuevo/",
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

}
