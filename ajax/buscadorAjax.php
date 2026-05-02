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
    "usuario" => "usuario-nuevo",
    "sucursal" => "sucursal-nuevo",
    "cargo" => "cargo-nuevo",
    "empleado" => "empleado-nuevo",
    "vehiculo" => "vehiculo-nuevo",
    "proveedor" => "proveedor-nuevo",
    "cliente" => "cliente-nuevo",
    "articulo" => "articulo-nuevo",
    "pedido" => "pedido-buscar",
    "presupuesto" => "presupuesto-buscar",
    "ordencompra" => "oc-nuevo",
    "roles" => "rol-nuevo",
    "ordencompra2" => "oc-buscar",
    "compra" => "factura-buscar",
    "inventario" => "inventario-buscar",
    "remision" => "remision-buscar",
    "notasCreDe" => "notasCreDe-buscar",
    "recepcion" => "recepcionServicio-buscar",
    "presupuesto_servicio" => "presupuesto-servicio-buscar",
    "orden_trabajo" => "ordenTrabajo-buscar",
    "registro_servicio" => "registro-servicio-buscar",
    "reclamo_servicio" => "reclamo-servicio-lista",
    "diagnostico" => "diagnostico-servicio-buscar"
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
    "registro_servicio",
    "diagnostico",
    "reclamo_servicio"
];

if (in_array($modulo, $modulos_con_fecha)) {

    if ($modulo == "diagnostico" || $modulo == "presupuesto" || $modulo == "presupuesto_servicio" || $modulo == "orden_trabajo" || $modulo == "registro_servicio" || $modulo == "reclamo_servicio"
    || $modulo == "ordencompra2") {
        /* ===== MAPEO DE SESIONES ===== */
        $config = [
            "diagnostico" => [
                "fecha_inicio" => "fecha_inicio_diag",
                "fecha_final"  => "fecha_final_diag",
                "extra" => [
                    "cliente" => "cliente_diag",
                    "placa"   => "placa_diag"
                ]
            ],
            "presupuesto_servicio" => [
                "fecha_inicio" => "fecha_inicio_presupuesto_servicio",
                "fecha_final"  => "fecha_final_presupuesto_servicio",
                "extra" => []
            ],
            "presupuesto" => [
                "fecha_inicio" => "fecha_inicio_presupuesto",
                "fecha_final"  => "fecha_final_presupuesto",
                "extra" => [
                    "nro_presupuesto" => "nro_presupuesto",
                    "proveedor_presupuesto" => "proveedor_presupuesto"
                ]
            ],
            "orden_trabajo" => [
                "fecha_inicio" => "fecha_inicio_orden_trabajo",
                "fecha_final"  => "fecha_final_orden_trabajo",
                "extra" => [
                    "estado_ot" => "estado_ot"
                ]
            ],
            "registro_servicio" => [
                "fecha_inicio" => "fecha_inicio_registro_servicio",
                "fecha_final"  => "fecha_final_registro_servicio",
                "extra" => [
                    "estado_regSer" => "estado_regSer"
                ]
            ],
            "reclamo_servicio" => [
                "fecha_inicio" => "fecha_inicio_reclamo_servicio",
                "fecha_final"  => "fecha_final_reclamo_servicio",
                "extra" => [
                    "estado_reclamo_servicio" => "estado_reclamo_servicio"
                ]
            ],
            "ordencompra2" => [
                "fecha_inicio" => "fecha_inicio_ordencompra2",
                "fecha_final"  => "fecha_final_ordencompra2",
                "extra" => [
                    "proveedor" => "proveedor_oc",
                    "estado_oc" => "estado_oc"
                ]
            ]
        ];

        $cfg = $config[$modulo];

        /* ===== ELIMINAR ===== */
        if (isset($_POST['eliminar_busqueda'])) {
            unset($_SESSION['estado_presupuesto']);
            unset($_SESSION['estado_ot']);
            unset($_SESSION['estado_regSer']);
            unset($_SESSION['estado_reclamo_servicio']);
            unset($_SESSION[$cfg['fecha_inicio']]);
            unset($_SESSION[$cfg['fecha_final']]);

            foreach ($cfg['extra'] as $key => $sessionKey) {
                unset($_SESSION[$sessionKey]);
            }

            echo json_encode([
                "Alerta" => "redireccionar",
                "URL" => SERVERURL . $data_url[$modulo] . "/"
            ]);
            exit();
        }

        $fecha_ini = $_POST['fecha_inicio'] ?? '';
        $fecha_fin = $_POST['fecha_final'] ?? '';

        /* ===== VALIDACIÓN FLEXIBLE ===== */
        if ($fecha_ini != '' && $fecha_fin != '') {

            if ($fecha_ini != '' && $fecha_fin != '' && $fecha_ini > $fecha_fin) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error en fechas",
                    "Texto" => "La fecha inicial no puede ser mayor",
                    "Tipo" => "error"
                ]);
                exit();
            }

            $_SESSION[$cfg['fecha_inicio']] = $fecha_ini;
            $_SESSION[$cfg['fecha_final']]  = $fecha_fin;
        } elseif ($modulo == "presupuesto" && ($fecha_ini != '' || $fecha_fin != '')) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Error en fechas",
                "Texto" => "Debe ingresar fecha de inicio y final",
                "Tipo" => "error"
            ]);
            exit();
        } else {
            unset($_SESSION[$cfg['fecha_inicio']]);
            unset($_SESSION[$cfg['fecha_final']]);
        }

        /* ===== CAMPOS EXTRA (solo diagnóstico) ===== */
        foreach ($cfg['extra'] as $postKey => $sessionKey) {
            $_SESSION[$sessionKey] = $_POST[$postKey] ?? '';
        }

        if (
            $modulo == "presupuesto" &&
            $fecha_ini == '' &&
            $fecha_fin == '' &&
            ($_SESSION['nro_presupuesto'] ?? '') == '' &&
            ($_SESSION['proveedor_presupuesto'] ?? '') == ''
        ) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Busqueda invalida",
                "Texto" => "Debe ingresar al menos un criterio de busqueda",
                "Tipo" => "error"
            ]);
            exit();
        }

        echo json_encode([
            "Alerta" => "redireccionar",
            "URL" => SERVERURL . $data_url[$modulo] . "/"
        ]);
        exit();
    }

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

        if ($modulo == "presupuesto") {
            unset($_SESSION['nro_presupuesto']);
            unset($_SESSION['proveedor_presupuesto']);
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

            if ($fecha_ini != '' && $fecha_fin != '') {
                if ($fecha_ini > $fecha_fin) {
                    echo json_encode([
                        "Alerta" => "simple",
                        "Titulo" => "Error en fechas",
                        "Texto" => "La fecha de inicio no puede ser mayor a la fecha final",
                        "Tipo" => "error"
                    ]);
                    exit();
                }
            }


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

            $fecha_ini = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_final'];

            if ($fecha_ini > $fecha_fin) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error en fechas",
                    "Texto" => "La fecha de inicio no puede ser mayor a la fecha final",
                    "Tipo" => "error"
                ]);
                exit();
            }

            $_SESSION[$fecha_inicio_key] = $fecha_ini;
            $_SESSION[$fecha_final_key]  = $fecha_fin;
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
