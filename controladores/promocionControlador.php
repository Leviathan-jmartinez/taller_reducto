<?php
require_once __DIR__ . "/../modelos/promocionModelo.php";


class promocionControlador extends promocionModelo
{
    /* ================= GUARDAR PROMOCIÓN ================= */
    public function guardar_promocion_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.promocion.crear')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso no autorizado",
                "Texto" => "No tienes permiso para crear promociones",
                "Tipo" => "error"
            ]);
        }
        /* Validaciones mínimas */
        if (
            empty($_POST['nombre']) ||
            empty($_POST['tipo']) ||
            empty($_POST['valor']) ||
            empty($_POST['fecha_inicio']) ||
            empty($_POST['fecha_fin'])
        ) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Datos incompletos",
                "Texto"  => "Debe completar todos los campos obligatorios",
                "Tipo"   => "warning"
            ]);
        }

        $datosPromo = [
            "nombre"        => trim($_POST['nombre']),
            "descripcion"   => trim($_POST['descripcion'] ?? ''),
            "tipo"          => $_POST['tipo'],
            "valor"         => floatval($_POST['valor']),
            "fecha_inicio"  => $_POST['fecha_inicio'],
            "fecha_fin"     => $_POST['fecha_fin'],
            "id_sucursal"   => empty($_POST['id_sucursal']) ? null : (int)$_POST['id_sucursal']
        ];

        $validacion = $this->validar_promocion($datosPromo);
        if ($validacion !== true) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Datos invalidos",
                "Texto"  => $validacion,
                "Tipo"   => "warning"
            ]);
        }

        $articulos = $_POST['articulos'] ?? [];

        $guardar = promocionModelo::guardar_promocion_modelo($datosPromo, $articulos);

        if ($guardar === true) {
            return json_encode([
                "Alerta" => "limpiar",
                "Titulo" => "Promoción registrada",
                "Texto"  => "La promoción se guardó correctamente",
                "Tipo"   => "success"
            ]);
        }

        if (is_array($guardar)) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => $guardar['msg'],
                "Tipo"   => "error"
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto"  => "No se pudo guardar la promoción",
            "Tipo"   => "error"
        ]);
    }

    /* ================= BUSCAR ARTÍCULOS ================= */
    public function buscar_articulos_controlador()
    {
        $busqueda = trim($_POST['buscar_articulo'] ?? '');
        return promocionModelo::buscar_articulos_modelo($busqueda);
    }

    public function listar_promociones_controlador($pagina = 1, $registros = 15, $url = 'promocion-lista')
    {
        $pagina = max(1, (int)$pagina);
        $registros = max(1, (int)$registros);
        $inicio = ($pagina - 1) * $registros;
        $url = SERVERURL . $url . "/";

        $filtros = [
            'buscar' => trim($_GET['buscar'] ?? ''),
            'estado' => $_GET['estado'] ?? '',
            'vigente' => $_GET['vigente'] ?? '',
            'id_sucursal' => $_GET['id_sucursal'] ?? ''
        ];

        $res = promocionModelo::listar_promociones_modelo($inicio, $registros, $filtros);
        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);
        $regInicio = $total > 0 ? $inicio + 1 : 0;
        $regFinal = $inicio;

        $tabla = '<div class="table-responsive">
        <table class="table table-dark table-sm">
            <thead>
                <tr class="text-center">
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Vigencia</th>
                    <th>Sucursal</th>
                    <th>Estado</th>
                    <th>Creado por</th>
                    <th>Editar</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {
            $contador = $inicio + 1;

            foreach ($datos as $p) {
                $estado = $p['estado'] == 1
                    ? '<span class="badge badge-success">Activa</span>'
                    : '<span class="badge badge-danger">Inactiva</span>';

                $tabla .= '<tr class="text-center">
                    <td>' . $contador . '</td>
                    <td>' . htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . $p['tipo'] . '</td>
                    <td>' . number_format($p['valor'], 0, ',', '.') . '</td>
                    <td>' . $p['fecha_inicio'] . ' - ' . $p['fecha_fin'] . '</td>
                    <td>' . htmlspecialchars($p['suc_descri'] ?? 'Todas', ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . $estado . '</td>
                    <td>' . htmlspecialchars($p['creado_por'] ?? '-', ENT_QUOTES, 'UTF-8') . '</td>
                    <td>
                        <a href="' . SERVERURL . 'promocion-nuevo/' . mainModel::encryption($p['id_promocion']) . '/">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                    <td>
                        <form class="FormularioAjax d-inline"
                            action="' . SERVERURL . 'ajax/promocionAjax.php"
                            method="POST"
                            data-form="update">
                            <input type="hidden" name="accion" value="cambiar_estado">
                            <input type="hidden" name="id" value="' . mainModel::encryption($p['id_promocion']) . '">
                            <input type="hidden" name="estado" value="' . ($p['estado'] == 1 ? 0 : 1) . '">
                            <button type="submit"
                                class="btn ' . ($p['estado'] == 1 ? 'btn-success' : 'btn-secondary') . ' btn-sm"
                                title="' . ($p['estado'] == 1 ? 'Desactivar' : 'Activar') . '">
                                <i class="fas ' . ($p['estado'] == 1 ? 'fa-toggle-on' : 'fa-toggle-off') . '"></i>
                                ' . ($p['estado'] == 1 ? 'Activo' : 'Inactivo') . '
                            </button>
                        </form>
                    </td>
                </tr>';

                $contador++;
            }

            $regFinal = $contador - 1;
        } else {
            $tabla .= '<tr><td colspan="10" class="text-center">No hay promociones</td></tr>';
        }

        $tabla .= '</tbody></table></div>';

        if ($total >= 1 && $pagina <= $Npaginas) {
            $tabla .= '<p class="text-right">
                Mostrando ' . $regInicio . ' al ' . $regFinal . ' de ' . $total . '
            </p>';

            $tabla .= mainModel::paginador($pagina, $Npaginas, $url, 10);
        }

        return $tabla;
    }

    public function cambiar_estado_promocion_controlador()
    {
        $id = mainModel::decryption($_POST['id']);
        $estado = (int)$_POST['estado'];

        $ok = promocionModelo::cambiar_estado_promocion_modelo($id, $estado);

        if ($ok) {
            return json_encode([
                "Alerta" => "recargar",
                "Titulo" => "Estado actualizado",
                "Texto" => "La promoción fue actualizada",
                "Tipo" => "success"
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto" => "No se pudo actualizar",
            "Tipo" => "error"
        ]);
    }

    public function datos_promocion_controlador($id)
    {
        $id = mainModel::decryption($id);
        return promocionModelo::datos_promocion_modelo($id);
    }

    public function articulos_promocion_controlador($id)
    {
        $id = mainModel::decryption($id);
        return promocionModelo::articulos_promocion_modelo($id);
    }

    public function editar_promocion_controlador()
    {
        $id = mainModel::decryption($_POST['id_promocion']);

        if (!mainModel::tienePermiso('servicio.promocion.editar')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso no autorizado",
                "Texto" => "No tienes permiso para editar promociones",
                "Tipo" => "error"
            ]);
        }

        $estado = isset($_POST['estado']) ? 1 : 0;

        $datos = [
            "id"           => $id,
            "nombre"       => $_POST['nombre'],
            "descripcion"  => $_POST['descripcion'],
            "tipo"         => $_POST['tipo'],
            "valor"        => $_POST['valor'],
            "fecha_inicio" => $_POST['fecha_inicio'],
            "fecha_fin"    => $_POST['fecha_fin'],
            "id_sucursal"  => empty($_POST['id_sucursal']) ? null : (int)$_POST['id_sucursal'],
            "estado"       => $estado
        ];

        $validacion = $this->validar_promocion($datos);
        if ($validacion !== true) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Datos invalidos",
                "Texto" => $validacion,
                "Tipo" => "warning"
            ]);
        }

        $articulos = $_POST['articulos'] ?? [];

        $ok = promocionModelo::editar_promocion_modelo($datos, $articulos);

        if ($ok === true) {
            return json_encode([
                "Alerta" => "recargar",
                "Titulo" => "Promoción actualizada",
                "Texto" => "La promoción fue actualizada correctamente",
                "Tipo" => "success"
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto" => $ok['msg'] ?? 'No se pudo actualizar',
            "Tipo" => "error"
        ]);
    }

    private function validar_promocion($datos)
    {
        $tipos = ['PORCENTAJE', 'MONTO_FIJO', 'PRECIO_FIJO'];
        $valor = (float)$datos['valor'];

        if (!in_array($datos['tipo'], $tipos, true)) {
            return 'Tipo de promocion invalido';
        }

        if ($valor <= 0 || ($datos['tipo'] === 'PORCENTAJE' && $valor > 100)) {
            return 'Valor de promocion invalido';
        }

        if ($datos['fecha_inicio'] > $datos['fecha_fin']) {
            return 'La fecha de inicio no puede ser mayor a la fecha fin';
        }

        return true;
    }
}
