<?php
if ($peticionAjax) {
    require_once "../modelos/equipoModelo.php";
} else {
    require_once "./modelos/equipoModelo.php";
}

class equipoControlador extends equipoModelo
{
    /* ==================================================
       LISTAR EQUIPOS
    ================================================== */
    public function listar_equipos_controlador()
    {
        return equipoModelo::listar_equipos_modelo();
    }

    /* ==================================================
       CREAR EQUIPO
    ================================================== */
    public function crear_equipo_controlador()
    {
        $sucursal    = mainModel::limpiar_string($_POST['sucursal']);
        $nombre      = mainModel::limpiar_string($_POST['nombre']);
        $descripcion = mainModel::limpiar_string($_POST['descripcion']);

        if ($sucursal == "" || $nombre == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe completar los campos obligatorios",
                "Tipo" => "error"
            ]);
            exit();
        }

        equipoModelo::agregar_equipo_modelo([
            "sucursal"    => $sucursal,
            "nombre"      => $nombre,
            "descripcion" => $descripcion
        ]);

        echo json_encode([
            "Alerta" => "recargar",
            "Titulo" => "Equipo",
            "Texto" => "Equipo creado correctamente",
            "Tipo" => "success"
        ]);
    }

    /* ==================================================
       EMPLEADOS DISPONIBLES POR SUCURSAL
    ================================================== */
    public function empleados_disponibles_controlador($id_sucursal)
    {
        return equipoModelo::empleados_disponibles_modelo($id_sucursal);
    }

    /* ==================================================
       ASIGNAR EMPLEADOS A EQUIPO
    ================================================== */
    public function asignar_empleados_controlador()
    {
        $id_equipo = mainModel::limpiar_string($_POST['id_equipo']);
        $empleados = $_POST['empleados'] ?? [];

        if ($id_equipo == "" || empty($empleados)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe seleccionar un equipo y al menos un empleado",
                "Tipo" => "error"
            ]);
            exit();
        }

        foreach ($empleados as $id_empleado) {
            equipoModelo::asignar_empleado_equipo_modelo(
                $id_equipo,
                $id_empleado,
                "Miembro"
            );
        }

        echo json_encode([
            "Alerta" => "simple",
            "Titulo" => "Equipo",
            "Texto" => "Empleados asignados correctamente",
            "Tipo" => "success"
        ]);
    }

    /* ==================================================
       VER MIEMBROS DE UN EQUIPO
    ================================================== */
    public function miembros_equipo_controlador($id_equipo_enc)
    {
        $id_equipo = mainModel::decryption($id_equipo_enc);
        $id_equipo = mainModel::limpiar_string($id_equipo);

        return equipoModelo::miembros_equipo_modelo($id_equipo);
    }
    public function empleados_con_equipo_controlador($id_sucursal)
    {
        $id_sucursal = mainModel::limpiar_string($id_sucursal);
        return equipoModelo::empleados_con_equipo_modelo($id_sucursal);
    }
}
