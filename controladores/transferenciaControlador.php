<?php
if ($peticionAjax) {
    require_once "../modelos/transferenciaModelo.php";
} else {
    require_once "./modelos/transferenciaModelo.php";
}

class transferenciaControlador extends transferenciaModelo
{
    public function crear_transferencia_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // ===== Validaciones mínimas =====
        if (empty($_POST['sucursal_destino'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Debe seleccionar una sucursal destino",
                "Tipo"   => "error"
            ]);
        }

        if (empty($_POST['productos'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Debe agregar al menos un producto",
                "Tipo"   => "error"
            ]);
        }

        if (empty($_POST['nombre_transpo'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Debe indicar el nombre del chofer",
                "Tipo"   => "error"
            ]);
        }

        // ===== Armar datos =====
        $datos = [
            'id_sucursal_origen'  => $_SESSION['nick_sucursal'],
            'id_sucursal_destino' => $_POST['sucursal_destino'],
            'id_usuario'          => $_SESSION['id_str'],
            'observacion'         => $_POST['observacion'] ?? '',
            'productos'           => $_POST['productos'],

            // obligatorios
            'chofer'       => $_POST['nombre_transpo'],
            'fechaenvio'   => $_POST['fechaenvio']   ?? date('Y-m-d'),
            'fechallegada' => $_POST['fechallegada'] ?? date('Y-m-d'),

            // opcionales
            'ci_transpo'    => $_POST['ci_transpo']    ?? null,
            'cel_transpo'   => $_POST['cel_transpo']   ?? null,
            'transportista' => $_POST['transportista'] ?? null,
            'ruc_transport' => $_POST['ruc_transport'] ?? null,
            'vehimarca'     => $_POST['vehimarca']     ?? null,
            'vehimodelo'    => $_POST['vehimodelo']    ?? null,
            'vehichapa'     => $_POST['vehichapa']     ?? null,
        ];

        $resultado = transferenciaModelo::crear_transferencia($datos);

        if (is_array($resultado) && $resultado['ok'] === true) {
            return json_encode([
                "Alerta" => "limpiar",
                "Titulo" => "Transferencia generada",
                "Texto"  => "La salida fue registrada correctamente",
                "Tipo"   => "success",
                "idnota_remision" => $resultado['idnota_remision']
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto"  => $resultado,
            "Tipo"   => "error"
        ]);
    }



    public function buscar_producto_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        $q = trim($_POST['termino'] ?? '');

        if ($q === '') {
            echo json_encode([]);
            return;
        }

        $idSucursal = $_SESSION['nick_sucursal'];

        $resultado = transferenciaModelo::buscar_producto_modelo($q, $idSucursal);

        echo json_encode($resultado);
    }

    public function buscar_sucursal_destino_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        $q = trim($_POST['termino'] ?? '');

        if ($q === '') {
            echo json_encode([]);
            return;
        }

        $idSucursalOrigen = $_SESSION['nick_sucursal'];

        $resultado = transferenciaModelo::buscar_sucursal_destino_modelo(
            $q,
            $idSucursalOrigen
        );

        echo json_encode($resultado);
    }
}
