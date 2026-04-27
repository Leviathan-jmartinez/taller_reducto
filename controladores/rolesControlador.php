<?php
if ($peticionAjax) {
    require_once "../modelos/rolesModelo.php";
} else {
    require_once "./modelos/rolesModelo.php";
}

class rolesControlador extends rolesModelo
{
    /* ========= AGREGAR ========= */
    public function agregar_roles_controlador()
    {
        $nombre = mainModel::limpiar_string($_POST['rol_nombre_reg']);
        $descripcion = mainModel::limpiar_string($_POST['rol_descripcion_reg']);

        /* ===== VALIDAR ===== */
        if ($nombre == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Campo requerido",
                "Texto" => "Debe ingresar el nombre del rol",
                "Tipo" => "error"
            ]);
            exit();
        }

        /* ===== DUPLICADO ===== */
        $check = mainModel::ejecutar_consulta_simple(
            "SELECT id_rol FROM roles WHERE nombre = '$nombre'"
        );

        if ($check->rowCount() > 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Duplicado",
                "Texto" => "El rol ya existe",
                "Tipo" => "error"
            ]);
            exit();
        }

        /* ===== DATA ===== */
        $datos = [
            "nombre" => $nombre,
            "descripcion" => $descripcion,
            "estado" => 1
        ];

        $guardar = rolesModelo::agregar_roles_modelo($datos);

        if ($guardar->rowCount() == 1) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Rol registrado",
                "Texto" => "El rol fue creado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo registrar el rol",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
    }

    /* ========= LISTAR ========= */
    public function listar_roles_controlador($pagina, $registros, $url, $busqueda)
    {
        $pagina = (int) mainModel::limpiar_string($pagina);
        $registros = (int) mainModel::limpiar_string($registros);
        $url = SERVERURL . mainModel::limpiar_string($url) . "/";

        $pagina = ($pagina > 0) ? $pagina : 1;
        $inicio = ($pagina - 1) * $registros;

        /* ===== FILTRO ===== */
        $filtrosSQL = "";

        if ($busqueda != "") {
            $busqueda = mainModel::limpiar_string($busqueda);

            $filtrosSQL = " AND (
                nombre LIKE '%$busqueda%' OR
                descripcion LIKE '%$busqueda%'
            )";
        }

        /* ===== DATOS ===== */
        $res = rolesModelo::listar_roles_modelo($inicio, $registros, $filtrosSQL);

        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);

        /* ===== TABLA ===== */
        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>NOMBRE</th>
                <th>DESCRIPCIÓN</th>
                <th>ESTADO</th>';

        if (mainModel::tienePermiso('roles.editar')) {
            $tabla .= '<th>ACTUALIZAR</th>';
        }
        if (mainModel::tienePermiso('roles.eliminar')) {
            $tabla .= '<th>ELIMINAR</th>';
        }

        $tabla .= '</tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {

            $contador = $inicio + 1;
            $reg_inicio = $inicio + 1;

            foreach ($datos as $row) {

                $tabla .= '<tr class="text-center">
                    <td>' . $contador . '</td>
                    <td>' . $row['nombre'] . '</td>
                    <td>' . $row['descripcion'] . '</td>
                    <td>' . ($row['estado'] == 1
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>') . '</td>';

                if (mainModel::tienePermiso('roles.editar')) {
                    $tabla .= '<td>
                        <a href="' . SERVERURL . 'rol-actualizar/' . mainModel::encryption($row['id_rol']) . '/"
                        class="btn btn-success btn-sm">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </td>';
                }

                if (mainModel::tienePermiso('roles.eliminar')) {
                    $tabla .= '<td>
                        <form class="FormularioAjax"
                            action="' . SERVERURL . 'ajax/rolesAjax.php"
                            method="POST"
                            data-form="delete">

                            <input type="hidden"
                            name="rol_id_del"
                            value="' . mainModel::encryption($row['id_rol']) . '">

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
                <td colspan="6" class="text-center">No hay registros</td>
            </tr>';
        }

        $tabla .= '</tbody></table></div>';

        /* ===== PAGINADOR ===== */
        if ($total >= 1 && $pagina <= $Npaginas) {

            $tabla .= '<p class="text-right">
                Mostrando ' . $reg_inicio . ' al ' . $reg_final . ' de ' . $total . '
            </p>';

            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        return $tabla;
    }

    /* ========= DATOS ========= */
    public function datos_roles_controlador($tipo, $id)
    {
        $tipo = mainModel::limpiar_string($tipo);
        $id = mainModel::decryption($id);
        $id = mainModel::limpiar_string($id);

        return rolesModelo::datos_roles_modelo($tipo, $id);
    }

    /* ========= ACTUALIZAR ========= */
    public function actualizar_roles_controlador()
    {
        $id = mainModel::decryption($_POST['rol_id_up']);
        $id = mainModel::limpiar_string($id);

        $nombre = mainModel::limpiar_string($_POST['rol_nombre_up']);
        $descripcion = mainModel::limpiar_string($_POST['rol_descripcion_up']);
        $estado = mainModel::limpiar_string($_POST['rol_estado_up']);

        /* ===== VALIDAR ===== */
        if ($nombre == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Campo requerido",
                "Texto" => "Debe ingresar el nombre",
                "Tipo" => "error"
            ]);
            exit();
        }

        /* ===== VALIDAR ESTADO ===== */
        if ($estado != "0" && $estado != "1") {
            $estado = "1";
        }

        $datos = [
            "id" => $id,
            "nombre" => $nombre,
            "descripcion" => $descripcion,
            "estado" => $estado
        ];

        $update = rolesModelo::actualizar_roles_modelo($datos);

        if ($update->rowCount() >= 0) {
            $alerta = [
                "Alerta" => "redireccionar_confirmado",
                "Titulo" => "Actualizado",
                "Texto" => "Rol actualizado correctamente",
                "Tipo" => "success",
                "URL" => SERVERURL . "roles-nuevo/"
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
    }

    /* ========= ELIMINAR ========= */
    public function eliminar_roles_controlador()
    {
        $id = mainModel::decryption($_POST['rol_id_del']);
        $id = mainModel::limpiar_string($id);

        $delete = rolesModelo::eliminar_roles_modelo($id);

        if ($delete->rowCount() > 0) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Eliminado",
                "Texto" => "Rol eliminado correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo eliminar",
                "Tipo" => "error"
            ];
        }

        echo json_encode($alerta);
    }

    public function permisos_por_rol_controlador()
    {
        session_start(['name' => 'STR']);

        if (!mainModel::tienePermiso('roles.editar')) {
            return '<div class="alert alert-danger">Acceso denegado</div>';
        }

        $idRol = mainModel::limpiar_string($_POST['id_rol']);

        if (empty($idRol)) {
            return '<div class="alert alert-danger">Rol inválido</div>';
        }

        $permisos = rolesModelo::obtener_permisos_rol_modelo($idRol);

        $grupos = [];

        foreach ($permisos as $p) {
            $modulo = explode('.', $p['clave'])[0];
            $grupos[$modulo][] = $p;
        }

        $html = '<div class="accordion" id="accordionPermisos">';
        $i = 0;

        foreach ($grupos as $modulo => $items) {
            $i++;

            $html .= '
        <div class="card">
        <div class="card-header p-2">
            <input type="checkbox"
                id="check_' . $modulo . '"
                class="check-modulo"
                data-target="' . $modulo . '">
            <strong>' . ucfirst($modulo) . '</strong>
        </div>

        <div class="card-body">
            <div class="row">';

            foreach ($items as $p) {
                $checked = $p['activo'] ? 'checked' : '';

                $html .= '
            <div class="col-md-4 mb-2">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox"
                        class="custom-control-input permiso-item permiso-' . $modulo . '"
                        data-grupo="' . $modulo . '"
                        id="perm_' . $p['id_permiso'] . '"
                        name="permisos[]"
                        value="' . $p['id_permiso'] . '"
                        ' . $checked . '>

                    <label class="custom-control-label" for="perm_' . $p['id_permiso'] . '">
                        <small>' . $p['descripcion'] . '</small>
                    </label>
                </div>
            </div>';
            }

            $html .= '</div></div></div>';
        }

        $html .= '</div>';

        return $html;
    }

    public function guardar_permisos_rol_controlador()
    {
        session_start(['name' => 'STR']);

        if (!mainModel::tienePermiso('roles.editar')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto"  => "No tiene permisos",
                "Tipo"   => "error"
            ]);
        }

        $idRol = mainModel::limpiar_string($_POST['id_rol']);
        $permisos = $_POST['permisos'] ?? [];

        if (empty($idRol)) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Rol inválido",
                "Tipo"   => "error"
            ]);
        }

        $res = rolesModelo::guardar_permisos_rol_modelo($idRol, $permisos);

        if ($res) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Permisos actualizados",
                "Texto"  => "Guardado correctamente",
                "Tipo"   => "success"
            ]);
        } else {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "No se pudieron guardar",
                "Tipo"   => "error"
            ]);
        }
    }

    public function listar_rolesSelect_controlador()
    {
        return rolesModelo::listar_roles_modeloSelect();
    }
}
