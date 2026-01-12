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
    public function paginador_sucursales_controlador($pagina, $registros, $privilegio, $url, $busqueda)
    {
        $pagina = mainModel::limpiar_string($pagina);
        $registros = mainModel::limpiar_string($registros);
        $busqueda = mainModel::limpiar_string($busqueda);

        $url = SERVERURL . $url . "/";
        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina * $registros) - $registros;

        $consulta = "SELECT SQL_CALC_FOUND_ROWS s.*, e.razon_social
                 FROM sucursales s
                 INNER JOIN empresa e ON e.id_empresa = s.id_empresa";

        if ($busqueda != "") {
            $consulta .= " WHERE s.suc_descri LIKE '%$busqueda%'";
        }

        $consulta .= " ORDER BY s.suc_descri ASC LIMIT $inicio,$registros";

        $conexion = mainModel::conectar();
        $datos = $conexion->query($consulta)->fetchAll();
        $total = (int)$conexion->query("SELECT FOUND_ROWS()")->fetchColumn();
        $Npaginas = ceil($total / $registros);

        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead><tr class="text-center">
            <th>#</th>
        <th>SUCURSAL</th>
        <th>EMPRESA</th>
        <th>ESTABLEC.</th>
        <th>ESTADO</th>';

        if ($privilegio <= 2) {
            $tabla .= '<th>ACTUALIZAR</th><th>ELIMINAR</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        $contador = $inicio + 1;
        foreach ($datos as $row) {
            $estado = $row['estado'] == 1
                ? '<span class="badge badge-success">Activo</span>'
                : '<span class="badge badge-danger">Inactivo</span>';

            $tabla .= "<tr class='text-center'>
            <td>$contador</td>
            <td>{$row['suc_descri']}</td>
            <td>{$row['razon_social']}</td>
            <td>{$row['nro_establecimiento']}</td>
            <td>$estado</td>";

            if ($privilegio <= 2) {
                $tabla .= '
            <td>
                <a href="' . SERVERURL . 'sucursal-actualizar/' . mainModel::encryption($row['id_sucursal']) . '/" class="btn btn-success">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </td>
            <td>
                <form class="FormularioAjax" action="' . SERVERURL . 'ajax/sucursalAjax.php" method="POST" data-form="delete">
                    <input type="hidden" name="sucursal_id_del" value="' . mainModel::encryption($row['id_sucursal']) . '">
                    <button type="submit" class="btn btn-warning">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </form>
            </td>';
            }

            $tabla .= '</tr>';
            $contador++;
        }

        $tabla .= '</tbody></table></div>';

        echo $tabla;
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
            "Alerta" => "recargar",
            "Titulo" => "Sucursal",
            "Texto" => "Sucursal actualizada correctamente",
            "Tipo" => "success"
        ]);
    }
    public function eliminar_sucursal_controlador()
    {
        $id = mainModel::decryption($_POST['sucursal_id_del']);

        $check = mainModel::ejecutar_consulta_simple(
            "SELECT id_sucursal FROM recepcion_servicio WHERE id_sucursal='$id' LIMIT 1"
        );

        if ($check->rowCount() > 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La sucursal está en uso y no puede eliminarse",
                "Tipo" => "error"
            ]);
            exit();
        }

        sucursalModelo::eliminar_sucursal_modelo($id);

        echo json_encode([
            "Alerta" => "recargar",
            "Titulo" => "Sucursal eliminada",
            "Texto" => "Sucursal eliminada correctamente",
            "Tipo" => "success"
        ]);
    }
    public function listar_sucursales_controlador()
    {
        return sucursalModelo::listar_sucursales_modelo();
    }
    public function listar_empleados_controlador()
    {
        return sucursalModelo::listar_empleados_modelo();
    }
}
