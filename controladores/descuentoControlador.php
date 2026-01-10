<?php
if ($peticionAjax) {
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
        session_start(['name' => 'STR']);
        if (!isset($_POST['nombre'], $_POST['tipo'], $_POST['valor'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Datos incompletos",
                "Tipo" => "error"
            ]);
        }

        $datos = [
            "nombre"         => $_POST['nombre'],
            "descripcion"    => $_POST['descripcion'] ?? '',
            "tipo"           => $_POST['tipo'],
            "valor"          => $_POST['valor'],
            "estado"         => isset($_POST['estado']) ? 1 : 0,
            "es_reutilizable" => $_POST['es_reutilizable'] ?? 0,
            "usuario"        => $_SESSION['id_str']
        ];

        $resultado = descuentoModelo::guardar_descuento_modelo($datos);

        if ($resultado === true) {
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

    public function listar_descuentos_controlador()
    {
        $datos = descuentoModelo::listar_descuentos_modelo();

        if (!$datos) {
            return '<div class="alert alert-info">No hay descuentos registrados</div>';
        }

        $tabla = '
        <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="text-center">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>';

        $i = 1;
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
            <td>' . $d['nombre'] . '</td>
            <td>' . $d['tipo'] . '</td>
            <td>' . $valor . '</td>
            <td>' . $estado . '</td>
            <td>

                <a href="' . SERVERURL . 'descuento-editar/' . mainModel::encryption($d['id_descuento']) . '/" 
                   class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i>
                </a>

                <a href="' . SERVERURL . 'descuento-asignar/' . mainModel::encryption($d['id_descuento']) . '/" 
                   class="btn btn-sm btn-info">
                    <i class="fas fa-user-tag"></i>
                </a>

            </td>
        </tr>';
        }

        $tabla .= '</tbody></table></div>';

        return $tabla;
    }

    public function editar_descuento_controlador()
    {
        session_start(['name' => 'STR']);
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
            "nombre"      => $_POST['nombre'],
            "descripcion" => $_POST['descripcion'] ?? '',
            "tipo"        => $_POST['tipo'],
            "valor"       => $_POST['valor'],
            "estado"      => isset($_POST['estado']) ? 1 : 0,
            "usuario"     => $_SESSION['id_str']
        ];

        $ok = descuentoModelo::editar_descuento_modelo($datos);

        if ($ok) {
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
