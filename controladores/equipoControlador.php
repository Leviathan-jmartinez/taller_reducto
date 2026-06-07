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

    public function datos_equipo_controlador($id_equipo_enc)
    {
        $id_equipo = mainModel::decryption($id_equipo_enc);
        $id_equipo = mainModel::limpiar_string($id_equipo);

        return equipoModelo::datos_equipo_modelo($id_equipo);
    }

    /* ==================================================
       CREAR EQUIPO
    ================================================== */
    public function crear_equipo_controlador()
    {
        if (!mainModel::tienePermiso('equipo.crear')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para crear equipos",
                "Tipo" => "error"
            ]);
            exit();
        }

        $sucursal    = mainModel::limpiar_string($_POST['sucursal'] ?? "");
        $nombre      = mainModel::limpiar_string($_POST['nombre'] ?? "");
        $descripcion = mainModel::limpiar_string($_POST['descripcion'] ?? "");

        if ($sucursal == "" || $nombre == "" || $descripcion == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe completar los campos obligatorios",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (
            mainModel::verificarDatos("[0-9]{1,10}", $sucursal) ||
            mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ._-]{3,80}", $nombre) ||
            mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,#\/_-]{3,100}", $descripcion)
        ) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Uno de los campos no tiene un formato valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        $check_sucursal = mainModel::ejecutar_consulta_simple(
            "SELECT id_sucursal FROM sucursales WHERE id_sucursal='$sucursal' AND estado=1"
        );
        if ($check_sucursal->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La sucursal seleccionada no es valida",
                "Tipo" => "error"
            ]);
            exit();
        }

        $check_equipo = mainModel::ejecutar_consulta_simple(
            "SELECT id_equipo FROM equipo_trabajo WHERE id_sucursal='$sucursal' AND nombre='$nombre' AND estado=1"
        );
        if ($check_equipo->rowCount() > 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Ya existe un equipo activo con ese nombre en la sucursal",
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
       ACTUALIZAR EQUIPO
    ================================================== */
    public function actualizar_equipo_controlador()
    {
        if (!mainModel::tienePermiso('equipo.editar')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para actualizar equipos",
                "Tipo" => "error"
            ]);
            exit();
        }

        $id = mainModel::decryption($_POST['equipo_id_up'] ?? '');
        $id = mainModel::limpiar_string($id);
        $sucursal = mainModel::limpiar_string($_POST['sucursal'] ?? "");
        $nombre = mainModel::limpiar_string($_POST['nombre'] ?? "");
        $descripcion = mainModel::limpiar_string($_POST['descripcion'] ?? "");

        if ($id == "" || $sucursal == "" || $nombre == "" || $descripcion == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe completar los campos obligatorios",
                "Tipo" => "error"
            ]);
            exit();
        }

        if (
            mainModel::verificarDatos("[0-9]{1,10}", $id) ||
            mainModel::verificarDatos("[0-9]{1,10}", $sucursal) ||
            mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ._-]{3,80}", $nombre) ||
            mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,#\/_-]{3,100}", $descripcion)
        ) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Uno de los campos no tiene un formato valido",
                "Tipo" => "error"
            ]);
            exit();
        }

        $check_sucursal = mainModel::ejecutar_consulta_simple(
            "SELECT id_sucursal FROM sucursales WHERE id_sucursal='$sucursal' AND estado=1"
        );
        if ($check_sucursal->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "La sucursal seleccionada no es valida",
                "Tipo" => "error"
            ]);
            exit();
        }

        $equipo = equipoModelo::datos_equipo_modelo($id);

        if (!$equipo) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El equipo no existe",
                "Tipo" => "error"
            ]);
            exit();
        }

        $check_equipo = mainModel::ejecutar_consulta_simple(
            "SELECT id_equipo FROM equipo_trabajo WHERE id_sucursal='$sucursal' AND nombre='$nombre' AND estado=1 AND id_equipo<>'$id'"
        );
        if ($check_equipo->rowCount() > 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Ya existe un equipo activo con ese nombre en la sucursal",
                "Tipo" => "error"
            ]);
            exit();
        }

        equipoModelo::actualizar_equipo_modelo([
            "id_equipo" => $id,
            "sucursal" => $sucursal,
            "nombre" => $nombre,
            "descripcion" => $descripcion
        ]);

        echo json_encode([
            "Alerta" => "recargar",
            "Titulo" => "Equipo",
            "Texto" => "Equipo actualizado correctamente",
            "Tipo" => "success"
        ]);
    }

    /* ==================================================
       ASIGNAR EMPLEADOS A EQUIPO
    ================================================== */
    public function asignar_empleados_controlador()
    {
        if (!mainModel::tienePermiso('equipo.editar')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para asignar empleados a equipos",
                "Tipo" => "error"
            ]);
            exit();
        }

        $id_equipo = mainModel::limpiar_string($_POST['id_equipo'] ?? "");
        $empleados = $_POST['empleados'] ?? [];

        if ($id_equipo == "" || mainModel::verificarDatos("[0-9]{1,10}", $id_equipo) || empty($empleados)) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe seleccionar un equipo y al menos un empleado",
                "Tipo" => "error"
            ]);
            exit();
        }

        $equipo = equipoModelo::datos_equipo_modelo($id_equipo);
        if (!$equipo || (int)$equipo['estado'] !== 1) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El equipo no existe o esta inactivo",
                "Tipo" => "error"
            ]);
            exit();
        }

        foreach ($empleados as $id_empleado) {
            $id_empleado = mainModel::limpiar_string($id_empleado);

            if (mainModel::verificarDatos("[0-9]{1,10}", $id_empleado)) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto" => "Uno de los empleados seleccionados no es valido",
                    "Tipo" => "error"
                ]);
                exit();
            }

            $check_empleado = mainModel::ejecutar_consulta_simple(
                "SELECT idempleados FROM empleados WHERE idempleados='$id_empleado' AND id_sucursal='{$equipo['id_sucursal']}' AND estado=1"
            );
            if ($check_empleado->rowCount() <= 0) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto" => "Uno de los empleados seleccionados no es valido para este equipo",
                    "Tipo" => "error"
                ]);
                exit();
            }

            equipoModelo::asignar_empleado_equipo_modelo(
                $id_equipo,
                $id_empleado,
                "Miembro"
            );
        }

        echo json_encode([
            "Alerta" => "recargar",
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

    public function empleados_asignacion_equipo_controlador()
    {
        if (!mainModel::tienePermiso('equipo.editar')) {
            echo json_encode([
                "ok" => false,
                "mensaje" => "No posee permisos para asignar empleados a equipos"
            ]);
            exit();
        }

        $id_equipo = mainModel::limpiar_string($_POST['id_equipo'] ?? '');
        if ($id_equipo == "") {
            echo json_encode([
                "ok" => false,
                "mensaje" => "Debe seleccionar un equipo"
            ]);
            exit();
        }

        $datos = equipoModelo::empleados_asignacion_equipo_modelo($id_equipo);
        if (!$datos) {
            echo json_encode([
                "ok" => false,
                "mensaje" => "El equipo no existe o esta inactivo"
            ]);
            exit();
        }

        echo json_encode([
            "ok" => true,
            "equipo" => $datos['equipo'],
            "empleados" => $datos['empleados']
        ]);
        exit();
    }

    /* ==================================================
        ELIMINAR EQUIPO
    ================================================== */
    public function eliminar_equipo_controlador()
    {
        if (!mainModel::tienePermiso('equipo.eliminar')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para eliminar equipos",
                "Tipo" => "error"
            ]);
            exit();
        }

        $id = mainModel::decryption($_POST['equipo_id_del']);
        $id = mainModel::limpiar_string($id);

        if ($id == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "ID de equipo no válido",
                "Tipo" => "error"
            ]);
            exit();
        }

        $equipo = equipoModelo::datos_equipo_modelo($id);
        if (!$equipo) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El equipo no existe",
                "Tipo" => "error"
            ]);
            exit();
        }

        if ((int)$equipo['estado'] === 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Equipo inactivo",
                "Texto" => "El equipo ya se encuentra inactivo.",
                "Tipo" => "info"
            ]);
            exit();
        }

        $eliminar = equipoModelo::eliminar_equipo_modelo($id);
        if (!$eliminar['ok']) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "No se pudo procesar el equipo seleccionado",
                "Tipo" => "error"
            ]);
            exit();
        }

        $mensaje = ($eliminar['accion'] === "inactivado")
            ? "El equipo tiene relaciones, por eso fue inactivado correctamente"
            : "Equipo eliminado correctamente";

        echo json_encode([
            "Alerta" => "recargar",
            "Titulo" => "Equipo",
            "Texto" => $mensaje,
            "Tipo" => "success"
        ]);
    }

    /* ==================================================
        QUITAR MIEMBRO DE EQUIPO
    ================================================== */
    public function quitar_miembro_controlador()
    {
        if (!mainModel::tienePermiso('equipo.editar')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Acceso denegado",
                "Texto" => "No posee permisos para quitar miembros del equipo",
                "Tipo" => "error"
            ]);
            exit();
        }

        $id_equipo = mainModel::decryption($_POST['equipo_id']);
        $id_equipo = mainModel::limpiar_string($id_equipo);

        $id_empleado = mainModel::decryption($_POST['empleado_id']);
        $id_empleado = mainModel::limpiar_string($id_empleado);

        if ($id_equipo == "" || $id_empleado == "") {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Datos incompletos",
                "Tipo" => "error"
            ]);
            exit();
        }

        $check_miembro = mainModel::ejecutar_consulta_simple(
            "SELECT id_equipo FROM equipo_empleado WHERE id_equipo='$id_equipo' AND idempleados='$id_empleado' AND estado=1"
        );
        if ($check_miembro->rowCount() <= 0) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "El empleado no pertenece al equipo",
                "Tipo" => "error"
            ]);
            exit();
        }

        equipoModelo::quitar_miembro_modelo($id_equipo, $id_empleado);

        echo json_encode([
            "Alerta" => "recargar",
            "Titulo" => "Equipo",
            "Texto" => "Empleado quitado del equipo",
            "Tipo" => "success"
        ]);
    }
}
