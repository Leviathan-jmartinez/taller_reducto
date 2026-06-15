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
    "diagnostico" => "diagnostico-servicio-buscar",
    "salida_insumo" => "registro-insumos-buscar",
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
    "recepcion",
    "remision",
    "notasCreDe",
    "presupuesto_servicio",
    "orden_trabajo",
    "registro_servicio",
    "diagnostico",
    "reclamo_servicio",
    "salida_insumo",
];

if (in_array($modulo, $modulos_con_fecha)) {

    if (
        $modulo == "diagnostico" || $modulo == "presupuesto" || $modulo == "presupuesto_servicio" || $modulo == "orden_trabajo" || $modulo == "registro_servicio" || $modulo == "reclamo_servicio"
        || $modulo == "ordencompra2" || $modulo == "salida_insumo"
    ) {
        /* ===== MAPEO DE SESIONES ===== */
        $config = [
            "diagnostico" => [
                "fecha_inicio" => "fecha_inicio_diag",
                "fecha_final"  => "fecha_final_diag",
                "extra" => [
                    "nro_diagnostico" => "nro_diagnostico_diag",
                    "nro_recepcion"   => "nro_recepcion_diag",
                    "cliente"         => "cliente_diag",
                    "placa"           => "placa_diag",
                    "estado"          => "estado_diag",
                    "origen"          => "origen_diag",
                    "busqueda_general" => "busqueda_general_diag"
                ]
            ],
            "presupuesto_servicio" => [
                "fecha_inicio" => "fecha_inicio_presupuesto_servicio",
                "fecha_final"  => "fecha_final_presupuesto_servicio",
                "extra" => [
                    "cliente" => "cliente_presupuesto_servicio",
                    "placa" => "placa_presupuesto_servicio",
                    "estado_presupuesto" => "estado_presupuesto"
                ]
            ],
            "presupuesto" => [
                "fecha_inicio" => "fecha_inicio_presupuesto",
                "fecha_final"  => "fecha_final_presupuesto",
                "extra" => [
                    "nro_presupuesto" => "nro_presupuesto",
                    "proveedor_presupuesto" => "proveedor_presupuesto",
                    "estado_presupuesto_compra" => "estado_presupuesto_compra"
                ]
            ],
            "orden_trabajo" => [
                "fecha_inicio" => "fecha_inicio_orden_trabajo",
                "fecha_final"  => "fecha_final_orden_trabajo",
                "extra" => [
                    "nro_ot" => "nro_ot",
                    "cliente" => "cliente_ot",
                    "vehiculo" => "vehiculo_ot",
                    "estado_ot" => "estado_ot"
                ]
            ],
            "registro_servicio" => [
                "fecha_inicio" => "fecha_inicio_registro_servicio",
                "fecha_final"  => "fecha_final_registro_servicio",
                "extra" => [
                    "nro_registro" => "nro_registro_servicio",
                    "cliente" => "cliente_registro_servicio",
                    "vehiculo" => "vehiculo_registro_servicio",
                    "estado_regSer" => "estado_regSer"
                ]
            ],
            "reclamo_servicio" => [
                "fecha_inicio" => "fecha_inicio_reclamo_servicio",
                "fecha_final"  => "fecha_final_reclamo_servicio",
                "extra" => [
                    "busqueda_inicial" => "busqueda_reclamo_servicio",
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
            ],
            "salida_insumo" => [
                "fecha_inicio" => "fecha_inicio_salida_insumo",
                "fecha_final"  => "fecha_final_salida_insumo",
                "extra" => [
                    "nro_salida" => "nro_salida_insumo",
                    "empleado"   => "empleado_salida_insumo",
                    "estado"     => "estado_salida_insumo"
                ]
            ],
        ];

        $cfg = $config[$modulo];

        /* ===== ELIMINAR ===== */
        if (isset($_POST['eliminar_busqueda'])) {
            unset($_SESSION['estado_presupuesto']);
            unset($_SESSION['filtro_presupuesto_servicio_activo']);
            unset($_SESSION['estado_ot']);
            unset($_SESSION['filtro_orden_trabajo_activo']);
            unset($_SESSION['estado_regSer']);
            unset($_SESSION['filtro_registro_servicio_activo']);
            unset($_SESSION['estado_reclamo_servicio']);
            unset($_SESSION[$cfg['fecha_inicio']]);
            unset($_SESSION[$cfg['fecha_final']]);
            unset($_SESSION['filtro_diagnostico_activo']);
            unset($_SESSION['filtro_salida_insumo_activo']);
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
        } elseif (($modulo == "diagnostico" || $modulo == "orden_trabajo" || $modulo == "registro_servicio" || $modulo == "reclamo_servicio" || $modulo == "salida_insumo") && ($fecha_ini != '' || $fecha_fin != '')) {
            if ($fecha_ini != '') {
                $_SESSION[$cfg['fecha_inicio']] = $fecha_ini;
            } else {
                unset($_SESSION[$cfg['fecha_inicio']]);
            }

            if ($fecha_fin != '') {
                $_SESSION[$cfg['fecha_final']] = $fecha_fin;
            } else {
                unset($_SESSION[$cfg['fecha_final']]);
            }
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

        /* ===== CAMPOS EXTRA  ===== */
        foreach ($cfg['extra'] as $postKey => $sessionKey) {
            $_SESSION[$sessionKey] = $_POST[$postKey] ?? '';
        }

        if ($modulo == "diagnostico") {
            $_SESSION['filtro_diagnostico_activo'] = '1';
        }

        if ($modulo == "presupuesto_servicio") {
            $_SESSION['filtro_presupuesto_servicio_activo'] = '1';
        }

        if ($modulo == "orden_trabajo") {
            $_SESSION['filtro_orden_trabajo_activo'] = '1';
        }

        if ($modulo == "registro_servicio") {
            $_SESSION['filtro_registro_servicio_activo'] = '1';
        }

        if (
            $modulo == "presupuesto" &&
            $fecha_ini == '' &&
            $fecha_fin == '' &&
            ($_SESSION['nro_presupuesto'] ?? '') == '' &&
            ($_SESSION['proveedor_presupuesto'] ?? '') == '' &&
            ($_SESSION['estado_presupuesto_compra'] ?? '') == '' &&
            !isset($_POST['estado_presupuesto_compra'])
        ) {
            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Busqueda invalida",
                "Texto" => "Debe ingresar al menos un criterio de busqueda",
                "Tipo" => "error"
            ]);
            exit();
        }
        if ($modulo == "salida_insumo") {
            $_SESSION['filtro_salida_insumo_activo'] = '1';
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

        if ($modulo == "remision") {
            unset($_SESSION['nro_remision_remision']);
            unset($_SESSION['nro_factura_remision']);
            unset($_SESSION['estado_remision']);
            unset($_SESSION['filtro_remision_activo']);
        }

        if ($modulo == "presupuesto") {
            unset($_SESSION['nro_presupuesto']);
            unset($_SESSION['proveedor_presupuesto']);
            unset($_SESSION['estado_presupuesto_compra']);
        }

        if ($modulo == "pedido") {
            unset($_SESSION['estado_pedido']);
        }

        if ($modulo == "notasCreDe") {
            unset($_SESSION['nro_documento_notasCreDe']);
            unset($_SESSION['tipo_nota_notasCreDe']);
        }

        if ($modulo == "inventario") {
            unset($_SESSION['nro_inventario']);
            unset($_SESSION['tipo_inv']);
            unset($_SESSION['estado_inv']);
            unset($_SESSION['observacion_inv']);
            unset($_SESSION['usuario_inv']);
            unset($_SESSION['filtro_inventario_activo']);
        }

        if ($modulo == "recepcion") {
            unset($_SESSION['nro_recepcion']);
            unset($_SESSION['cliente_recepcion']);
            unset($_SESSION['documento_recepcion']);
            unset($_SESSION['placa_recepcion']);
            unset($_SESSION['estado_recepcion']);
            unset($_SESSION['origen_recepcion']);
            unset($_SESSION['usuario_recepcion']);
            unset($_SESSION['tipo_servicio_recepcion']);
            unset($_SESSION['prioridad_recepcion']);
            unset($_SESSION['filtro_recepcion_activo']);
            unset($_SESSION['busqueda_recepcion']);
        }
    } else {

        /* ===============================
           PEDIDO (FECHA OPCIONAL + ESTADO)
           =============================== */
        if ($modulo == "pedido") {

            $fecha_ini = $_POST['fecha_inicio'] ?? '';
            $fecha_fin = $_POST['fecha_final'] ?? '';

            if ($fecha_ini != '' && $fecha_fin != '' && $fecha_ini > $fecha_fin) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error en fechas",
                    "Texto" => "La fecha de inicio no puede ser mayor a la fecha final",
                    "Tipo" => "error"
                ]);
                exit();
            }

            $_SESSION['estado_pedido'] = $_POST['estado_pedido'] ?? '';

            if ($fecha_ini == '' && $fecha_fin == '' && $_SESSION['estado_pedido'] == '' && !isset($_POST['estado_pedido'])) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Busqueda invalida",
                    "Texto" => "Debe ingresar al menos un criterio de busqueda",
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
           INVENTARIO (FILTROS OPCIONALES)
           =============================== */ elseif ($modulo == "inventario") {

            $fecha_ini = $_POST['fecha_inicio'] ?? '';
            $fecha_fin = $_POST['fecha_final'] ?? '';

            if ($fecha_ini != '' && $fecha_fin != '' && $fecha_ini > $fecha_fin) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error en fechas",
                    "Texto" => "La fecha de inicio no puede ser mayor a la fecha final",
                    "Tipo" => "error"
                ]);
                exit();
            }

            $_SESSION['nro_inventario']  = $_POST['nro_inventario'] ?? '';
            $_SESSION['tipo_inv']        = $_POST['tipo_inv'] ?? '';
            $_SESSION['estado_inv']      = $_POST['estado_inv'] ?? '';
            $_SESSION['observacion_inv'] = $_POST['observacion'] ?? '';
            $_SESSION['usuario_inv']     = $_POST['usuario'] ?? '';
            $_SESSION['filtro_inventario_activo'] = '1';

            if ($fecha_ini != '') {
                $_SESSION[$fecha_inicio_key] = $fecha_ini;
            } else {
                unset($_SESSION[$fecha_inicio_key]);
            }

            if ($fecha_fin != '') {
                $_SESSION[$fecha_final_key] = $fecha_fin;
            } else {
                unset($_SESSION[$fecha_final_key]);
            }
        }

        /* ===============================
           RECEPCION (FILTROS OPCIONALES)
           =============================== */ elseif ($modulo == "recepcion") {

            $fecha_ini = $_POST['fecha_inicio'] ?? '';
            $fecha_fin = $_POST['fecha_final'] ?? '';

            if ($fecha_ini != '' && $fecha_fin != '' && $fecha_ini > $fecha_fin) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error en fechas",
                    "Texto" => "La fecha de inicio no puede ser mayor a la fecha final",
                    "Tipo" => "error"
                ]);
                exit();
            }

            $_SESSION['nro_recepcion']           = $_POST['nro_recepcion'] ?? '';
            $_SESSION['cliente_recepcion']       = $_POST['cliente'] ?? '';
            $_SESSION['documento_recepcion']     = $_POST['documento'] ?? '';
            $_SESSION['placa_recepcion']         = $_POST['placa'] ?? '';
            $_SESSION['estado_recepcion']        = $_POST['estado_recepcion'] ?? '';
            $_SESSION['origen_recepcion']        = $_POST['origen_recepcion'] ?? '';
            $_SESSION['usuario_recepcion']       = $_POST['usuario'] ?? '';
            $_SESSION['tipo_servicio_recepcion'] = $_POST['tipo_servicio'] ?? '';
            $_SESSION['prioridad_recepcion']     = $_POST['prioridad'] ?? '';
            $_SESSION['filtro_recepcion_activo'] = '1';

            if ($fecha_ini != '') {
                $_SESSION[$fecha_inicio_key] = $fecha_ini;
            } else {
                unset($_SESSION[$fecha_inicio_key]);
            }

            if ($fecha_fin != '') {
                $_SESSION[$fecha_final_key] = $fecha_fin;
            } else {
                unset($_SESSION[$fecha_final_key]);
            }
        }

        /* ===============================
           COMPRA (FECHA OPCIONAL)
           =============================== */ elseif ($modulo == "compra") {

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
           REMISION (FECHA OPCIONAL)
           =============================== */ elseif ($modulo == "remision") {

            $fecha_ini = $_POST['fecha_inicio'] ?? '';
            $fecha_fin = $_POST['fecha_final'] ?? '';

            if ($fecha_ini != '' && $fecha_fin != '' && $fecha_ini > $fecha_fin) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error en fechas",
                    "Texto" => "La fecha de inicio no puede ser mayor a la fecha final",
                    "Tipo" => "error"
                ]);
                exit();
            }

            $_SESSION['nro_remision_remision'] = $_POST['nro_remision'] ?? '';
            unset($_SESSION['nro_factura_remision']);
            $_SESSION['estado_remision']      = $_POST['estado_remision'] ?? '';
            $_SESSION['filtro_remision_activo'] = true;

            if (
                $fecha_ini == '' &&
                $fecha_fin == '' &&
                $_SESSION['nro_remision_remision'] == '' &&
                $_SESSION['estado_remision'] == '' &&
                !isset($_POST['estado_remision'])
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
