<?php
if (isset($peticionAjax) && $peticionAjax) {
    require_once "../modelos/descuentoModelo.php";
} else {
    require_once "./modelos/descuentoModelo.php";
}

class descuentoControlador extends descuentoModelo
{

    public function buscar_clientes_controlador()
    {
        $busqueda = $_POST['buscar_cliente'] ?? '';
        return descuentoModelo::buscar_clientes_modelo($busqueda);
    }

    public function asignar_descuento_cliente_controlador()
    {
        $id = mainModel::decryption($_POST['id_descuento']);
        $clientes = $_POST['clientes'] ?? [];

        descuentoModelo::guardar_descuento_cliente_modelo($id, $clientes);

        echo json_encode([
            "Alerta" => "recargar",
            "Titulo" => "Asociación guardada",
            "Texto" => "Clientes asociados correctamente",
            "Tipo" => "success"
        ]);
        exit;
    }

    public function eliminar_cliente_descuento_controlador()
    {
        $id_descuento = mainModel::decryption($_POST['id_descuento']);
        $id_cliente   = mainModel::decryption($_POST['id_cliente']);

        if ($id_descuento <= 0 || $id_cliente <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Datos inválidos",
                "Tipo"   => "error"
            ]);
            exit;
        }

        descuentoModelo::eliminar_cliente_descuento_modelo($id_descuento, $id_cliente);

        echo json_encode([
            "Alerta" => "simple",
            "Titulo" => "Eliminado",
            "Texto"  => "Cliente eliminado del descuento",
            "Tipo"   => "success"
        ]);
        exit;
    }


    public function datos_descuento_controlador($id)
    {
        $id = mainModel::decryption($id);

        if ($id <= 0) {
            return false;
        }

        return descuentoModelo::datos_descuento_modelo($id);
    }

    public function clientes_asignados_descuento_controlador($id)
    {
        $id = mainModel::decryption($id);

        if ($id <= 0) {
            return [];
        }

        return descuentoModelo::clientes_asignados_modelo($id);
    }

    public function guardar_descuento_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }
        if(!mainModel::tienePermiso('servicio.descuento.crear')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso no autorizado",
                "Texto" => "No tienes permiso para crear descuentos",
                "Tipo" => "error"
            ]);
        }

        if (!isset($_POST['nombre'], $_POST['tipo'], $_POST['valor'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Datos incompletos",
                "Tipo" => "error"
            ]);
        }

        $tipo = $_POST['tipo'];
        $valor = (float)$_POST['valor'];
        $tiposPermitidos = ['PORCENTAJE', 'MONTO_FIJO'];

        if (!in_array($tipo, $tiposPermitidos, true) || $valor <= 0 || ($tipo === 'PORCENTAJE' && $valor > 100)) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Datos invalidos",
                "Texto" => "Verifique el tipo y valor del descuento",
                "Tipo" => "warning"
            ]);
        }

        $aplicaA = $_POST['aplica_a'] ?? 'TOTAL';
        if (!in_array($aplicaA, ['PRODUCTO', 'SERVICIO', 'TOTAL'], true)) {
            $aplicaA = 'TOTAL';
        }

        $fechaInicio = $_POST['fecha_inicio'] ?? null;
        $fechaFin = $_POST['fecha_fin'] ?? null;

        if ($fechaInicio && $fechaFin && $fechaInicio > $fechaFin) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Vigencia invalida",
                "Texto" => "La fecha de inicio no puede ser mayor a la fecha fin",
                "Tipo" => "warning"
            ]);
        }

        $datos = [
            "nombre"         => trim($_POST['nombre']),
            "descripcion"    => $_POST['descripcion'] ?? '',
            "tipo"           => $tipo,
            "valor"          => $valor,
            "aplica_a"       => $aplicaA,
            "fecha_inicio"   => $fechaInicio ?: null,
            "fecha_fin"      => $fechaFin ?: null,
            "estado"         => isset($_POST['estado']) ? 1 : 0,
            "es_reutilizable" => $_POST['es_reutilizable'] ?? 0,
            "usuario"        => $_SESSION['id_str'],
            "id_sucursal"    => empty($_POST['id_sucursal']) ? null : (int)$_POST['id_sucursal']
        ];

        $resultado = descuentoModelo::guardar_descuento_modelo($datos);

        if (is_int($resultado) && $resultado > 0) {
            $clientes = $_POST['clientes'] ?? [];
            if (!empty($clientes)) {
                descuentoModelo::guardar_descuento_cliente_modelo($resultado, $clientes);
            }

            return json_encode([
                "Alerta" => "limpiar",
                "Titulo" => "Descuento registrado",
                "Texto" => "El descuento se guardó correctamente",
                "Tipo" => "success"
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto" => $resultado['msg'] ?? "No se pudo guardar el descuento",
            "Tipo" => "error"
        ]);
    }

    public function descuentos_por_cliente_controlador()
    {
        if (!isset($_POST['id_cliente'])) {
            return '';
        }

        $id_cliente = intval($_POST['id_cliente']);

        $descuentos = descuentoModelo::descuentos_por_cliente_modelo($id_cliente);

        if (!$descuentos) {
            return '<div class="alert alert-info">
                    El cliente no posee descuentos automáticos
                </div>';
        }

        $html = '<ul class="list-group">';

        foreach ($descuentos as $d) {
            $label = $d['tipo'] === 'PORCENTAJE'
                ? $d['valor'] . '%'
                : 'Gs. ' . number_format($d['valor'], 0, ',', '.');

            $html .= '
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <strong>' . $d['nombre'] . '</strong><br>
                <small>' . $label . '</small>
            </div>
            <button type="button"
                class="btn btn-sm btn-success"
                onclick="aplicarDescuentoAuto(' . $d['id_descuento'] . ')">
                Aplicar
            </button>
        </li>';
        }

        $html .= '</ul>';

        return $html;
    }

    public function listar_descuentos_controlador($pagina = 1, $registros = 15, $url = 'descuento-lista')
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

        $res = descuentoModelo::listar_descuentos_modelo($inicio, $registros, $filtros);
        $datos = $res['datos'];
        $total = $res['total'];
        $Npaginas = ceil($total / $registros);
        $regInicio = $total > 0 ? $inicio + 1 : 0;
        $regFinal = $inicio;

        $tabla = '
        <div class="table-responsive">
        <table class="table table-dark table-sm">
            <thead class="text-center">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Aplica a</th>
                <th>Vigencia</th>
                <th>Sucursal</th>
                <th>Creado por</th>
                <th>Modificado por</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>';

        if ($total >= 1 && $pagina <= $Npaginas) {
            $i = $inicio + 1;

            foreach ($datos as $d) {

                $estado = $d['estado'] == 1
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';

                $valor = ($d['tipo'] === 'PORCENTAJE')
                    ? $d['valor'] . '%'
                    : 'Gs. ' . number_format($d['valor'], 0, ',', '.');

                $tabla .= '
        <tr class="text-center">
            <td>' . $i++ . '</td>
            <td>' . htmlspecialchars($d['nombre'], ENT_QUOTES, 'UTF-8') . '</td>
            <td>' . $d['tipo'] . '</td>
            <td>' . $valor . '</td>
            <td>' . ($d['aplica_a'] ?? 'TOTAL') . '</td>
            <td>' . (($d['fecha_inicio'] ?? '') ?: 'Sin inicio') . ' - ' . (($d['fecha_fin'] ?? '') ?: 'Sin fin') . '</td>
            <td>' . htmlspecialchars(($d['suc_descri'] ?? '') ?: 'Todas', ENT_QUOTES, 'UTF-8') . '</td>
            <td>' . htmlspecialchars(($d['creado_por'] ?? '') ?: '-', ENT_QUOTES, 'UTF-8') . '</td>
            <td>' . htmlspecialchars(($d['modificado_por'] ?? '') ?: '-', ENT_QUOTES, 'UTF-8') . '</td>
            <td>' . $estado . '</td>
            <td>

                <a href="' . SERVERURL . 'descuento-nuevo/' . mainModel::encryption($d['id_descuento']) . '/" 
                   class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i>
                </a>

            </td>
        </tr>';
            }

            $regFinal = $i - 1;
        } else {
            $tabla .= '<tr><td colspan="11" class="text-center">No hay descuentos registrados</td></tr>';
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

    public function editar_descuento_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if(!mainModel::tienePermiso('servicio.descuento.editar')) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso no autorizado",
                "Texto" => "No tienes permiso para editar descuentos",
                "Tipo" => "error"
            ]);
        }

        if (!isset($_POST['id_descuento'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Datos inválidos",
                "Tipo" => "error"
            ]);
        }

        $datos = [
            "id"          => mainModel::decryption($_POST['id_descuento']),
            "nombre"      => trim($_POST['nombre']),
            "descripcion" => $_POST['descripcion'] ?? '',
            "tipo"        => $_POST['tipo'],
            "valor"       => (float)$_POST['valor'],
            "aplica_a"    => $_POST['aplica_a'] ?? 'TOTAL',
            "fecha_inicio" => empty($_POST['fecha_inicio']) ? null : $_POST['fecha_inicio'],
            "fecha_fin"   => empty($_POST['fecha_fin']) ? null : $_POST['fecha_fin'],
            "estado"      => isset($_POST['estado']) ? 1 : 0,
            "usuario"     => $_SESSION['id_str'],
            "id_sucursal" => empty($_POST['id_sucursal']) ? null : (int)$_POST['id_sucursal']
        ];

        if (!in_array($datos['aplica_a'], ['PRODUCTO', 'SERVICIO', 'TOTAL'], true)) {
            $datos['aplica_a'] = 'TOTAL';
        }

        if (!in_array($datos['tipo'], ['PORCENTAJE', 'MONTO_FIJO'], true) || $datos['valor'] <= 0 || ($datos['tipo'] === 'PORCENTAJE' && $datos['valor'] > 100)) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Datos invalidos",
                "Texto" => "Verifique el tipo y valor del descuento",
                "Tipo" => "warning"
            ]);
        }

        if ($datos['fecha_inicio'] && $datos['fecha_fin'] && $datos['fecha_inicio'] > $datos['fecha_fin']) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Vigencia invalida",
                "Texto" => "La fecha de inicio no puede ser mayor a la fecha fin",
                "Tipo" => "warning"
            ]);
        }

        $ok = descuentoModelo::editar_descuento_modelo($datos);

        if ($ok) {
            $clientes = $_POST['clientes'] ?? [];
            if (!empty($clientes)) {
                descuentoModelo::guardar_descuento_cliente_modelo($datos['id'], $clientes);
            }

            return json_encode([
                "Alerta" => "recargar",
                "Titulo" => "Descuento actualizado",
                "Texto" => "Los cambios se guardaron correctamente",
                "Tipo" => "success"
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto" => "No se pudo actualizar el descuento",
            "Tipo" => "error"
        ]);
    }
}
