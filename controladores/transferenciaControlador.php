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

        if (!isset($_POST['accion']) || $_POST['accion'] !== 'crear_transferencia') {
            return;
        }

        /* ================= VALIDACIONES BÁSICAS ================= */
        if (empty($_POST['productos'])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => "Debe agregar al menos un producto a la transferencia",
                "Tipo"   => "error"
            ];
            echo json_encode($alerta);
            return;
        }

        $datos = [
            'sucursal_origen'  => $_SESSION['id_sucursal'],
            'sucursal_destino' => $_POST['sucursal_destino'],
            'usuario'          => $_SESSION['id_usuario'],
            'observacion'      => $_POST['motivo'],
            'productos'        => $_POST['productos'],

            // transporte
            'transportista' => $_POST['transportista'],
            'ruc_transport' => $_POST['ruc_transport'] ?? null,
            'nombre_transpo' => $_POST['nombre_transpo'],
            'ci_transpo'    => $_POST['ci_transpo'] ?? null,
            'cel_transpo'   => $_POST['cel_transpo'] ?? null,
            'vehimarca'     => $_POST['vehimarca'],
            'vehimodelo'    => $_POST['vehimodelo'] ?? null,
            'vehichapa'     => $_POST['vehichapa'],
            'fechaenvio'    => $_POST['fechaenvio'],
            'fechallegada'  => $_POST['fechallegada'],
            'motivo'        => $_POST['motivo']
        ];

        /* ================= EJECUTAR MODELO ================= */
        $respuesta = transferenciaModelo::crear_transferencia_modelo($datos);

        if ($respuesta === true) {

            $alerta = [
                "Alerta" => "redireccionar",
                "Titulo" => "Transferencia generada",
                "Texto"  => "La transferencia y la nota de remisión fueron creadas correctamente",
                "Tipo"   => "success",
                "URL"    => SERVERURL . "transferencia-lista/"
            ];
        } else {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto"  => $respuesta, // mensaje devuelto por el modelo
                "Tipo"   => "error"
            ];
        }

        echo json_encode($alerta);
    }
}
