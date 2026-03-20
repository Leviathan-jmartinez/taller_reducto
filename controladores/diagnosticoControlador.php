<?php
if ($peticionAjax) {
    require_once "../modelos/diagnosticoModelo.php";
} else {
    require_once "./modelos/diagnosticoModelo.php";
}

class diagnosticoControlador extends diagnosticoModelo
{

    public function buscar_recepcion_controlador()
    {
        $busqueda = $_POST['buscar_recepcion'] ?? '';

        if ($busqueda === '') {
            return '<div class="alert alert-warning text-center">
                    Ingrese un criterio de búsqueda
                </div>';
        }

        return diagnosticoModelo::buscar_recepcion_modelo($busqueda);
    }

    public function guardar_diagnostico_controlador()
    {
        session_start(['name' => 'STR']);

        if (empty($_POST['idrecepcion']) || empty($_POST['fecha'])) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Datos incompletos",
                "Texto"  => "Debe seleccionar una recepción y fecha",
                "Tipo"   => "warning"
            ]);
        }

        /* ================= FIX FECHA ================= */
        $fecha = str_replace("T", " ", $_POST['fecha']);

        /* ================= DETALLES ================= */

        $detalles = [];

        if (isset($_POST['descripcion'])) {
            foreach ($_POST['descripcion'] as $i => $desc) {

                if (trim($desc) == "") continue;

                $detalles[] = [
                    "descripcion" => $desc,
                    "tipo" => intval($_POST['tipo'][$i])
                ];
            }
        }

        $datos = [
            "idrecepcion" => intval($_POST['idrecepcion']),
            "id_usuario"  => $_SESSION['id_str'] ?? 0,
            "fecha"       => $fecha,
            "observacion" => $_POST['observacion'] ?? null,
            "estado"      => intval($_POST['estado']),
            "detalles"    => $detalles
        ];

        if ($datos['id_usuario'] == 0) {
            return json_encode([
                "Alerta" => "simple",
                "Titulo" => "Sesión inválida",
                "Texto"  => "Usuario no identificado",
                "Tipo"   => "error"
            ]);
        }

        $guardar = diagnosticoModelo::guardar_diagnostico_modelo($datos);

        if (isset($guardar['success'])) {

            return json_encode([
                "Alerta" => "limpiar",
                "Titulo" => "Diagnóstico registrado",
                "Texto"  => "Se guardó correctamente",
                "Tipo"   => "success",
                "id_diagnostico" => $guardar['id_diagnostico']
            ]);
        }

        return json_encode([
            "Alerta" => "simple",
            "Titulo" => "Error",
            "Texto"  => $guardar['msg'] ?? "Error desconocido",
            "Tipo"   => "error"
        ]);
    }
}
