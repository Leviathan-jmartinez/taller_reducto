<?php
require_once __DIR__ . "/../modelos/promocionModelo.php";


class promocionControlador extends promocionModelo
{
    /* ================= GUARDAR PROMOCIÓN ================= */
    public function guardar_promocion_controlador()
    {
        session_start(['name' => 'STR']);
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

    public function listar_promociones_controlador()
    {
        return promocionModelo::listar_promociones_modelo();
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

        $estado = isset($_POST['estado']) ? 1 : 0;

        $datos = [
            "id"           => $id,
            "nombre"       => $_POST['nombre'],
            "descripcion"  => $_POST['descripcion'],
            "tipo"         => $_POST['tipo'],
            "valor"        => $_POST['valor'],
            "fecha_inicio" => $_POST['fecha_inicio'],
            "fecha_fin"    => $_POST['fecha_fin'],
            "estado"       => $estado
        ];

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
}
