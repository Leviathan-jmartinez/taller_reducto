<?php
if ($peticionAjax) {
    require_once "../modelos/sucursalModelo.php";
} else {
    require_once "./modelos/sucursalModelo.php";
}

class sucursalControlador extends sucursalModelo
{
    /** listar empresas */
    public function listar_empresas_controlador()
    {
        return sucursalModelo::obtener_empresas_modelo();
    }

    /** datos sucursal */
    public function datos_sucursal_controlador($tipo, $id)
    {
        $tipo = mainModel::limpiar_string($tipo);
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_string($id);
        return sucursalModelo::datos_sucursal_modelo($tipo, $id);
    }

    /** agregar sucursal */
    public function agregar_sucursal_controlador()
    {
        if (!mainModel::tienePermiso('sucursal.crear')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para registrar sucursales",
                "Tipo" => "error"
            ]);
            exit();
        }

        $empresa   = mainModel::limpiar_string($_POST['empresa_reg'] ?? "");
        $descri    = mainModel::limpiar_string($_POST['sucursal_descri_reg'] ?? "");
        $direccion = mainModel::limpiar_string($_POST['sucursal_direccion_reg'] ?? "");
        $telefono  = mainModel::limpiar_string($_POST['sucursal_telefono_reg'] ?? "");
        $nro_est   = mainModel::limpiar_string($_POST['nro_establecimiento_reg'] ?? "");
        $estado    = mainModel::limpiar_string($_POST['estado_reg'] ?? "");

        if ($empresa == "" || $descri == "" || $nro_est == "" || $estado == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Campos obligatorios incompletos",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificarDatos("[0-9]{1,10}", $empresa)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe seleccionar una empresa valida",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,#°-]{3,80}", $descri)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La descripcion de la sucursal no tiene un formato valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if ($direccion != "" && mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,#°\/-]{3,120}", $direccion)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La direccion no tiene un formato valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if ($telefono != "" && mainModel::verificarDatos("[0-9()+ -]{6,20}", $telefono)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El telefono no tiene un formato valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificarDatos("[0-9]{1,3}", $nro_est)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El numero de establecimiento debe contener de 1 a 3 digitos",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if ($estado != "0" && $estado != "1") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Estado invalido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $datos = [
            "id_empresa" => $empresa,
            "suc_descri" => $descri,
            "suc_direccion" => $direccion,
            "suc_telefono" => $telefono,
            "nro_establecimiento" => $nro_est,
            "estado" => $estado
        ];

        $check_dup = sucursalModelo::existe_sucursal_duplicada_modelo($datos);
        if ($check_dup->rowCount() > 0) {
            $duplicado = $check_dup->fetch(PDO::FETCH_ASSOC);
            $texto = ((int)$duplicado['nro_establecimiento'] === (int)$nro_est)
                ? "Ya existe una sucursal con ese numero de establecimiento para la empresa seleccionada"
                : "Ya existe una sucursal con esa descripcion para la empresa seleccionada";

            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Duplicado",
                "Texto" => $texto,
                "Tipo" => "error"
            ]);
            exit();
        }

        $guardar = sucursalModelo::agregar_sucursal_modelo($datos);

        if ($guardar->rowCount() == 1) {
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Sucursal",
                "Texto" => "Sucursal registrada correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo registrar la sucursal",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }


    public function listar_sucursales_controlador($pagina, $registros, $url)
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);

        $url = mainModel::limpiar_string($url);
        $url = SERVERURL . $url . "/";

        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        /* ===== FILTROS ===== */

        $busqueda = $_SESSION['busqueda_sucursal'] ?? "";

        $filtros = [];

        if ($busqueda != "") {
            $filtros[] = [
                "campo" => "s.suc_descri",
                "tipo"  => "LIKE",
                "valor" => $busqueda
            ];
        }

        $filtrosSQL = mainModel::construirFiltros($filtros);

        /* ===== DATOS ===== */

        $res = sucursalModelo::listar_sucursales_modelo($inicio, $registros, $filtrosSQL);

        $datos = $res["datos"];
        $total = $res["total"];
        $Npaginas = ceil($total / $registros);

        /* ===== TABLA ===== */

        $tabla .= '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
        <tr class="text-center roboto-medium">
            <th>#</th>
            <th>SUCURSAL</th>
            <th>EMPRESA</th>
            <th>ESTABLEC.</th>
            <th>ESTADO</th>';

        if (mainModel::tienePermiso('sucursal.editar')) {
            $tabla .= '<th>ACTUALIZAR</th>';
        }
        if (mainModel::tienePermiso('sucursal.eliminar')) {
            $tabla .= '<th>ELIMINAR</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;

            foreach ($datos as $rows) {

                $estado = $rows['estado'] == 1
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';

                $tabla .= '
            <tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $rows['suc_descri'] . '</td>
                <td>' . $rows['razon_social'] . '</td>
                <td>' . $rows['nro_establecimiento'] . '</td>
                <td>' . $estado . '</td>';

                if (mainModel::tienePermiso('sucursal.editar')) {
                    $tabla .= '
                <td>
                    <a href="' . SERVERURL . 'sucursal-actualizar/' . mainModel::encryption($rows['id_sucursal']) . '/"
                    class="btn btn-success">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </td>';
                }

                if (mainModel::tienePermiso('sucursal.eliminar')) {
                    $tabla .= '
                <td>
                    <form class="FormularioAjax"
                        action="' . SERVERURL . 'ajax/sucursalAjax.php"
                        method="POST"
                        data-form="delete">

                        <input type="hidden"
                        name="sucursal_id_del"
                        value="' . mainModel::encryption($rows['id_sucursal']) . '">

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

            $tabla .= '<tr class="text-center">
            <td colspan="6">No hay registros en el sistema</td>
        </tr>';
        }

        $tabla .= '</tbody></table></div>';

        /* ===== PAGINACIÓN ===== */

        if ($total >= 1 && $pagina <= $Npaginas) {

            $tabla .= '<p class="text-right">
            Mostrando registro ' . $reg_inicio . ' al ' . $reg_final . ' de un total de ' . $total . '
        </p>';

            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        return $tabla;
    }

    public function actualizar_sucursal_controlador()
    {
        if (!mainModel::tienePermiso('sucursal.editar')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para actualizar sucursales",
                "Tipo" => "error"
            ]);
            exit();
        }

        $id = mainModel::decryption($_POST['sucursal_id_up']);
        $id = mainModel::limpiar_string($id);

        $check = mainModel::ejecutar_consulta_simple(
            "SELECT * FROM sucursales WHERE id_sucursal='$id'"
        );

        if ($check->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Sucursal no encontrada",
                "Tipo" => "error"
            ]);
            exit();
        }

        $empresa   = mainModel::limpiar_string($_POST['empresa_up'] ?? "");
        $descri    = mainModel::limpiar_string($_POST['sucursal_descri_up'] ?? "");
        $direccion = mainModel::limpiar_string($_POST['sucursal_direccion_up'] ?? "");
        $telefono  = mainModel::limpiar_string($_POST['sucursal_telefono_up'] ?? "");
        $nro_est   = mainModel::limpiar_string($_POST['nro_establecimiento_up'] ?? "");
        $estado    = mainModel::limpiar_string($_POST['estado_up'] ?? "");

        if ($empresa == "" || $descri == "" || $nro_est == "" || $estado == "") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Campos obligatorios incompletos",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificarDatos("[0-9]{1,10}", $empresa)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe seleccionar una empresa valida",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,#°-]{3,80}", $descri)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La descripcion de la sucursal no tiene un formato valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if ($direccion != "" && mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,#°\/-]{3,120}", $direccion)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La direccion no tiene un formato valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if ($telefono != "" && mainModel::verificarDatos("[0-9()+ -]{6,20}", $telefono)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El telefono no tiene un formato valido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if (mainModel::verificarDatos("[0-9]{1,3}", $nro_est)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El numero de establecimiento debe contener de 1 a 3 digitos",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        if ($estado != "0" && $estado != "1") {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Estado invalido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        $datos = [
            "id_sucursal" => $id,
            "id_empresa" => $empresa,
            "suc_descri" => $descri,
            "suc_direccion" => $direccion,
            "suc_telefono" => $telefono,
            "nro_establecimiento" => $nro_est,
            "estado" => $estado
        ];

        $check_dup = sucursalModelo::existe_sucursal_duplicada_modelo($datos);
        if ($check_dup->rowCount() > 0) {
            $duplicado = $check_dup->fetch(PDO::FETCH_ASSOC);
            $texto = ((int)$duplicado['nro_establecimiento'] === (int)$nro_est)
                ? "Ya existe una sucursal con ese numero de establecimiento para la empresa seleccionada"
                : "Ya existe una sucursal con esa descripcion para la empresa seleccionada";

            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Duplicado",
                "Texto" => $texto,
                "Tipo" => "error"
            ]);
            exit();
        }

        sucursalModelo::actualizar_sucursal_modelo($datos);

        echo json_encode([
            "Alerta" => "redireccionar_confirmado",
            "URL" => SERVERURL . "sucursal-nuevo/",
            "Titulo" => "Sucursal",
            "Texto" => "Sucursal actualizada correctamente",
            "Tipo" => "success"
        ]);
    }

    public function eliminar_sucursal_controlador()
    {
        if (!mainModel::tienePermiso('sucursal.eliminar')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para eliminar sucursales",
                "Tipo" => "error"
            ]);
            exit();
        }

        $id = mainModel::decryption($_POST['sucursal_id_del']);
        $id = mainModel::limpiar_string($id);

        $check = mainModel::ejecutar_consulta_simple(
            "SELECT id_sucursal, estado 
         FROM sucursales 
         WHERE id_sucursal='$id'"
        );

        if ($check->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "La sucursal no existe",
                "Tipo"   => "error"
            ]);
            exit();
        }

        $sucursalActual = $check->fetch();
        if ((int)$sucursalActual['estado'] === 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Sucursal inactiva",
                "Texto"  => "La sucursal ya se encuentra inactiva.",
                "Tipo"   => "info"
            ]);
            exit();
        }

        $stmt = sucursalModelo::eliminar_sucursal_modelo($id);

        if ($stmt->rowCount() > 0) {

            // Verificar cómo quedó
            $verificar = mainModel::ejecutar_consulta_simple(
                "SELECT estado 
             FROM sucursales 
             WHERE id_sucursal='$id'"
            );

            if ($verificar->rowCount() > 0) {
                // Sigue existiendo → fue desactivada
                $alerta = [
                    "Alerta" => "redireccionar_confirmado",
                    "URL" => SERVERURL . "sucursal-nuevo/",
                    "Titulo" => "Sucursal desactivada",
                    "Texto"  => "La sucursal tiene registros relacionados o está asignada a usuarios, por lo que fue desactivada.",
                    "Tipo"   => "warning"
                ];
            } else {
                // Ya no existe → fue eliminada
                $alerta = [
                    "Alerta" => "redireccionar_confirmado",
                    "URL" => SERVERURL . "sucursal-nuevo/",
                    "Titulo" => "Sucursal eliminada",
                    "Texto"  => "Sucursal eliminada correctamente.",
                    "Tipo"   => "success"
                ];
            }
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "No se pudo eliminar la sucursal",
                "Tipo"   => "error"
            ];
        }

        echo json_encode($alerta);
    }
}
