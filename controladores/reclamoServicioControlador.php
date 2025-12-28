<?php
if ($peticionAjax) {
    require_once "../modelos/reclamoServicioModelo.php";
} else {
    require_once "./modelos/reclamoServicioModelo.php";
}

class reclamoServicioControlador extends reclamoServicioModelo
{
    public function registrar_reclamo_controlador()
    {
        session_start(['name' => 'STR']);

        if (
            empty($_POST['idregistro_servicio']) ||
            empty($_POST['descripcion'])
        ) {
            return json_encode([
                'Alerta' => 'simple',
                'Titulo' => 'Error',
                'Texto'  => 'Datos incompletos',
                'Tipo'   => 'error'
            ]);
        }

        $datos = [
            'idregistro_servicio' =>
            mainModel::decryption($_POST['idregistro_servicio']),
            'descripcion' => $_POST['descripcion'],
            'usuario'     => $_SESSION['id_str']
        ];

        $res = self::registrar_reclamo_modelo($datos);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'recargar',
                'Titulo' => 'Reclamo registrado',
                'Texto'  => 'El reclamo fue registrado correctamente',
                'Tipo'   => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto'  => $res['msg'] ?? 'No se pudo registrar el reclamo',
            'Tipo'   => 'error'
        ]);
    }

    public function buscar_registro_controlador()
    {
        $texto = trim($_POST['buscar'] ?? '');

        $datos = self::buscar_registro_modelo($texto);

        if (!$datos) {
            return '<div class="alert alert-warning">
                No se encontraron servicios
            </div>';
        }

        $html = '<table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th></th>
                </tr>
            </thead><tbody>';

        foreach ($datos as $r) {
            $html .= '
            <tr>
                <td>#' . $r['idregistro_servicio'] . '</td>
                <td>' . $r['nombre_cliente'] . ' ' .
                $r['apellido_cliente'] . '</td>
                <td>' . $r['mod_descri'] . ' ' . $r['placa'] . '</td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm"
                        onclick="seleccionarRegistro(
                            \'' . mainModel::encryption($r['idregistro_servicio']) . '\',
                            \'' . $r['idregistro_servicio'] . '\',
                            \'' . $r['nombre_cliente'] . ' ' .
                $r['apellido_cliente'] . '\',
                            \'' . $r['mod_descri'] . ' ' . $r['placa'] . '\'
                        )">
                        Seleccionar
                    </button>
                </td>
            </tr>';
        }

        return $html . '</tbody></table>';
    }
}
