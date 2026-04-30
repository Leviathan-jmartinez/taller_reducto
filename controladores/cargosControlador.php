<?php
if ($peticionAjax) {
    require_once "../modelos/cargosModelo.php";
} else {
    require_once "./modelos/cargosModelo.php";
}

class cargosControlador extends cargosModelo
{

    /** Agregar cargo */
    public function agregar_cargo_controlador()
    {
        $descripcion = mainModel::limpiar_string($_POST['descripcion_reg']);
        $estado      = mainModel::limpiar_string($_POST['estado_reg']);

        if ($descripcion == "" || $estado == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe completar los campos obligatorios",
                "Tipo" => "error"
            ]);
            exit();
        }

        $check = mainModel::ejecutar_consulta_simple(
            "SELECT idcargos FROM cargos WHERE descripcion='$descripcion'"
        );
        if ($check->rowCount() > 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El cargo ya existe",
                "Tipo" => "error"
            ]);
            exit();
        }

        $datos = [
            "descripcion" => $descripcion,
            "estado" => $estado
        ];

        $guardar = cargosModelo::agregar_cargo_modelo($datos);

        if ($guardar->rowCount() == 1) {
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Cargo",
                "Texto" => "Cargo registrado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo registrar el cargo",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }


    /** Eliminar cargo */
    public function eliminar_cargo_controlador()
    {
        $id = mainModel::decryption($_POST['cargo_id_del']);
        $id = mainModel::limpiar_string($id);

        $check = mainModel::ejecutar_consulta_simple(
            "SELECT idcargos, estado FROM cargos WHERE idcargos='$id'"
        );

        if ($check->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "El cargo no existe",
                "Tipo"   => "error"
            ]);
            exit();
        }

        $stmt = cargosModelo::eliminar_cargo_modelo($id);

        if ($stmt->rowCount() > 0) {

            // Verificar cómo quedó
            $verificar = mainModel::ejecutar_consulta_simple(
                "SELECT estado FROM cargos WHERE idcargos='$id'"
            );

            if ($verificar->rowCount() > 0) {
                // Sigue existiendo → fue desactivado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Cargo desactivado",
                    "Texto"  => "El cargo ya tenía relación con otros registros, por lo que fue desactivado.",
                    "Tipo"   => "warning"
                ];
            } else {
                // Ya no existe → fue eliminado
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "Cargo eliminado",
                    "Texto"  => "Cargo eliminado correctamente.",
                    "Tipo"   => "success"
                ];
            }
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "No se pudo eliminar el cargo",
                "Tipo"   => "error"
            ];
        }

        echo json_encode($alerta);
    }


    public function listar_cargos_controlador($pagina, $registros, $url)
    {
        $pagina    = (int) mainModel::limpiar_string($pagina);
        $registros = (int) mainModel::limpiar_string($registros);
        $url       = SERVERURL . mainModel::limpiar_string($url) . "/";

        $pagina = ($pagina > 0) ? $pagina : 1;
        $inicio = ($pagina - 1) * $registros;
        $reg_inicio = $inicio + 1;
        $reg_final = $inicio;

        /* ================= FILTRO ================= */

        $busqueda = $_SESSION['busqueda_cargo'] ?? "";

        $filtros = [];

        if ($busqueda != "") {
            $filtros[] = [
                "campo" => "descripcion",
                "tipo"  => "LIKE",
                "valor" => $busqueda
            ];
        }

        $filtrosSQL = mainModel::construirFiltros($filtros);

        /* ================= DATOS ================= */

        $res = cargosModelo::listar_cargos_modelo($inicio, $registros, $filtrosSQL);

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        /* ================= TABLA ================= */

        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>DESCRIPCIÓN</th>
                <th>ESTADO</th>';

        if (mainModel::tienePermiso('cargo.editar')) {
            $tabla .= '<th>ACTUALIZAR</th>';
        }
        if (mainModel::tienePermiso('cargo.eliminar')) {
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

                $tabla .= '<tr class="text-center">
                <td>' . $contador . '</td>
                <td>' . $rows['descripcion'] . '</td>
                <td>' . $estado . '</td>';

                if (mainModel::tienePermiso('cargo.editar')) {
                    $tabla .= '<td>
                    <a href="' . SERVERURL . 'cargo-actualizar/' . mainModel::encryption($rows['idcargos']) . '/"
                    class="btn btn-success btn-sm">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </td>';
                }

                if (mainModel::tienePermiso('cargo.eliminar')) {
                    $tabla .= '<td>
                    <form class="FormularioAjax"
                        action="' . SERVERURL . 'ajax/cargoAjax.php"
                        method="POST"
                        data-form="delete">

                        <input type="hidden" name="cargo_id_del"
                        value="' . mainModel::encryption($rows['idcargos']) . '">

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

            $tabla .= '<tr>
            <td colspan="5" class="text-center">No hay registros</td>
        </tr>';
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


    /** datos cargo */
    public function datos_cargo_controlador($tipo, $id)
    {
        $tipo = mainModel::limpiar_string($tipo);
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_string($id);

        return cargosModelo::datos_cargo_modelo($tipo, $id);
    }


    /** actualizar cargo */
    public function actualizar_cargo_controlador()
    {
        $id = mainModel::decryption($_POST['cargo_id_up']);
        $id = mainModel::limpiar_string($id);

        $check = mainModel::ejecutar_consulta_simple(
            "SELECT * FROM cargos WHERE idcargos='$id'"
        );

        if ($check->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "El cargo no existe en el sistema",
                "Tipo"   => "error"
            ]);
            exit();
        } else {
            $campos = $check->fetch();
        }

        $descripcion = mainModel::limpiar_string($_POST['descripcion_up']);
        $estado      = mainModel::limpiar_string($_POST['estado_up']);

        if ($descripcion == "" || $estado == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe completar los campos obligatorios",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (!in_array($estado, ['0', '1'], true)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Estado inválido",
                "Tipo" => "error"
            ]);
            exit();
        }

        if ($descripcion != $campos['descripcion']) {
            $check_dup = mainModel::ejecutar_consulta_simple(
                "SELECT idcargos FROM cargos WHERE descripcion='$descripcion'"
            );
            if ($check_dup->rowCount() > 0) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto" => "El cargo ya existe",
                    "Tipo" => "error"
                ]);
                exit();
            }
        }

        session_start(['name' => 'STR']);
        if (!mainModel::tienePermiso('cargo.editar')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No tiene permisos",
                "Tipo" => "error"
            ]);
        }

        $datos = [
            "descripcion" => $descripcion,
            "estado" => $estado,
            "idcargos" => $id
        ];

        $update = cargosModelo::actualizar_cargo_modelo($datos);

        if ($update) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Cargo actualizado",
                "Texto" => "Datos actualizados correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo actualizar",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
        exit();
    }
}
