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
        $empresa   = mainModel::limpiar_string($_POST['empresa_reg']);
        $descri    = mainModel::limpiar_string($_POST['sucursal_descri_reg']);
        $direccion = mainModel::limpiar_string($_POST['sucursal_direccion_reg']);
        $telefono  = mainModel::limpiar_string($_POST['sucursal_telefono_reg']);
        $nro_est   = mainModel::limpiar_string($_POST['nro_establecimiento_reg']);
        $estado    = mainModel::limpiar_string($_POST['estado_reg']);

        if ($empresa == "" || $descri == "" || $estado == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Campos obligatorios incompletos",
                "Tipo" => "error"
            ]);
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

        $datos = [
            "id_sucursal" => $id,
            "id_empresa" => mainModel::limpiar_string($_POST['empresa_up']),
            "suc_descri" => mainModel::limpiar_string($_POST['sucursal_descri_up']),
            "suc_direccion" => mainModel::limpiar_string($_POST['sucursal_direccion_up']),
            "suc_telefono" => mainModel::limpiar_string($_POST['sucursal_telefono_up']),
            "nro_establecimiento" => mainModel::limpiar_string($_POST['nro_establecimiento_up']),
            "estado" => mainModel::limpiar_string($_POST['estado_up'])
        ];

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
                    "Texto"  => "La sucursal ya tenía movimientos, por lo que fue desactivada.",
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
