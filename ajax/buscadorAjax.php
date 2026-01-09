<?php
session_start(['name' => 'STR']);
require_once "../config/APP.php";

/* ===============================
   VALIDACIÓN BÁSICA
   =============================== */
if (!isset($_POST['modulo'])) {
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}

$modulo = $_POST['modulo'];

/* ===============================
   MAPA DE REDIRECCIONES
   =============================== */
$data_url = [
    "usuario" => "usuario-buscar",
    "sucursal" => "sucursal-buscar",
    "cargo" => "cargo-buscar",
    "empleado" => "empleado-buscar",
    "vehiculo" => "vehiculo-buscar",
    "proveedor" => "proveedor-buscar",
    "cliente" => "cliente-buscar",
    "articulo" => "articulo-buscar",
    "pedido" => "pedido-buscar",
    "presupuesto" => "presupuesto-buscar",
    "ordencompra" => "oc-nuevo",
    "ordencompra2" => "oc-buscar",
    "compra" => "factura-buscar",
    "inventario" => "inventario-buscar",
    "remision" => "remision-buscar",
    "notasCreDe" => "notasCreDe-buscar",
    "recepcion" => "recepcionServicio-buscar",
    "presupuesto_servicio" => "presupuesto-servicio-buscar",
    "orden_trabajo" => "ordenTrabajo-buscar",
    "registro_servicio" => "registro-servicio-buscar",
];

if (!isset($data_url[$modulo])) {
    echo json_encode([
        "Alerta" => "simple",
        "Titulo" => "Error",
        "Texto" => "Módulo no válido",
        "Tipo" => "error"
    ]);
    exit();
}

/* ===============================
   MÓDULOS CON FECHA
   =============================== */
$modulos_con_fecha = [
    "pedido",
    "presupuesto",
    "ordencompra2",
    "compra",
    "inventario",
    "remision",
    "notasCreDe",
    "presupuesto_servicio",
    "orden_trabajo",
    "registro_servicio"
];

if (in_array($modulo, $modulos_con_fecha)) {

    $fecha_inicio_key = "fecha_inicio_" . $modulo;
    $fecha_final_key  = "fecha_final_" . $modulo;

    /* ===== ELIMINAR BÚSQUEDA ===== */
    if (isset($_POST['eliminar_busqueda'])) {

        unset($_SESSION[$fecha_inicio_key]);
        unset($_SESSION[$fecha_final_key]);

        if ($modulo == "compra") {
            unset($_SESSION['nro_factura_compra']);
            unset($_SESSION['razon_social_compra']);
        }

        if ($modulo == "notasCreDe") {
            unset($_SESSION['nro_documento_notasCreDe']);
            unset($_SESSION['tipo_nota_notasCreDe']);
        }
    } else {

        /* ===============================
           COMPRA (FECHA OPCIONAL)
           =============================== */
        if ($modulo == "compra") {

            $fecha_ini = $_POST['fecha_inicio'] ?? '';
            $fecha_fin = $_POST['fecha_final'] ?? '';

            $_SESSION['nro_factura_compra']  = $_POST['nro_factura'] ?? '';
            $_SESSION['razon_social_compra'] = $_POST['razon_social'] ?? '';

            if (
                $fecha_ini == '' &&
                $fecha_fin == '' &&
                $_SESSION['nro_factura_compra'] == '' &&
                $_SESSION['razon_social_compra'] == ''
            ) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Búsqueda inválida",
                    "Texto" => "Debe ingresar al menos un criterio de búsqueda",
                    "Tipo" => "error"
                ]);
                exit();
            }

            if ($fecha_ini != '' && $fecha_fin != '') {
                $_SESSION[$fecha_inicio_key] = $fecha_ini;
                $_SESSION[$fecha_final_key]  = $fecha_fin;
            } else {
                unset($_SESSION[$fecha_inicio_key]);
                unset($_SESSION[$fecha_final_key]);
            }
        }

        /* ===============================
           NOTAS CRÉDITO / DÉBITO
           =============================== */ elseif ($modulo == "notasCreDe") {

            $_SESSION['nro_documento_notasCreDe'] = $_POST['nro_documento'] ?? '';
            $_SESSION['tipo_nota_notasCreDe']     = $_POST['tipo_nota'] ?? '';

            $fecha_ini = $_POST['fecha_inicio'] ?? '';
            $fecha_fin = $_POST['fecha_final'] ?? '';

            if (
                $fecha_ini == '' &&
                $fecha_fin == '' &&
                $_SESSION['nro_documento_notasCreDe'] == '' &&
                $_SESSION['tipo_nota_notasCreDe'] == ''
            ) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Búsqueda inválida",
                    "Texto" => "Debe ingresar al menos un criterio",
                    "Tipo" => "error"
                ]);
                exit();
            }

            if ($fecha_ini != '' && $fecha_fin != '') {
                $_SESSION[$fecha_inicio_key] = $fecha_ini;
                $_SESSION[$fecha_final_key]  = $fecha_fin;
            } else {
                unset($_SESSION[$fecha_inicio_key]);
                unset($_SESSION[$fecha_final_key]);
            }
        }

        /* ===============================
           RESTO DE MÓDULOS (FECHA OBLIGATORIA)
           =============================== */ else {

            if (
                ($_POST['fecha_inicio'] ?? '') == '' ||
                ($_POST['fecha_final'] ?? '') == ''
            ) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto" => "Debe ingresar fecha de inicio y final",
                    "Tipo" => "error"
                ]);
                exit();
            }

            $_SESSION[$fecha_inicio_key] = $_POST['fecha_inicio'];
            $_SESSION[$fecha_final_key]  = $_POST['fecha_final'];
        }
    }
}

/* ===============================
   MÓDULOS DE BÚSQUEDA SIMPLE
   =============================== */ else {

    $namevar = "busqueda_" . $modulo;

    if (isset($_POST['eliminar_busqueda'])) {
        unset($_SESSION[$namevar]);
    } else {

        if (($_POST['busqueda_inicial'] ?? '') == '') {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error",
                "Texto" => "Debe ingresar un valor de búsqueda",
                "Tipo" => "error"
            ]);
            exit();
        }

        $_SESSION[$namevar] = $_POST['busqueda_inicial'];
    }
}

/* ===============================
   REDIRECCIÓN FINAL
   =============================== */
echo json_encode([
    "Alerta" => "redireccionar",
    "URL" => SERVERURL . $data_url[$modulo] . "/"
]);
exit();
