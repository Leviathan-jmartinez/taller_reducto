<?php
require_once "../modelos/promocionModelo.php";

class promocionControlador extends promocionModelo
{
    /* ================= GUARDAR PROMOCIÓN ================= */
    public function guardar_promocion_controlador()
    {
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
            "fecha_fin"     => $_POST['fecha_fin']
        ];

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
}
