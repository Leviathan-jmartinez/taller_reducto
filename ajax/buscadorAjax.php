<?php
session_start(['name' => 'STR']);
require_once "../config/APP.php";

if (isset($_POST['busqueda_inicial']) || isset($_POST['eliminar_busqueda']) || isset($_POST['fecha_inicio']) || isset($_POST['fecha_final']) || isset($_POST['nro_factura']) || isset($_POST['idproveedor'])) {

    $data_url = [
        "usuario" => "usuario-buscar",
        "cliente" => "cliente-buscar",
        "articulo" => "articulo-buscar",
        "pedido" => "pedido-buscar",
        "presupuesto" => "presupuesto-buscar",
        "ordencompra" => "oc-nuevo",
        "ordencompra2" => "oc-buscar",
        "factura" => "factura-buscar"
    ];

    if (isset($_POST['modulo'])) {
        $modulo = $_POST['modulo'];
        if (!isset($data_url[$modulo])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrió un error inesperado!",
                "Texto" => "No podemos continuar con la búsqueda debido a un error",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }

        // Módulos que usan solo fecha (pedido, presupuesto, ordencompra2)
        if ($modulo == "pedido" || $modulo == "presupuesto" || $modulo == "ordencompra2") {

            $fecha_inicio = "fecha_inicio_" . $modulo;
            $fecha_final  = "fecha_final_" . $modulo;

            // Iniciar búsqueda
            if (isset($_POST['fecha_inicio']) || isset($_POST['fecha_final'])) {
                if ($_POST['fecha_inicio'] == "" || $_POST['fecha_final'] == "") {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado!",
                        "Texto" => "Por favor ingrese una fecha de inicio y final válida",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
                $_SESSION[$fecha_inicio] = $_POST['fecha_inicio'];
                $_SESSION[$fecha_final] = $_POST['fecha_final'];
            }

            // Eliminar búsqueda
            if (isset($_POST['eliminar_busqueda'])) {
                unset($_SESSION[$fecha_inicio]);
                unset($_SESSION[$fecha_final]);
            }
        }
        // Módulo factura con filtros avanzados
        elseif ($modulo == "factura") {

            // Iniciar búsqueda
            $fecha_inicio  = $_POST['fecha_inicio'] ?? '';
            $fecha_final   = $_POST['fecha_final'] ?? '';
            $nro_factura   = $_POST['nro_factura'] ?? '';
            $idproveedor   = $_POST['idproveedor'] ?? '';

            // Validaciones básicas
            if (!empty($fecha_inicio) && empty($fecha_final)) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto" => "Debe ingresar fecha final",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
            if (!empty($fecha_final) && empty($fecha_inicio)) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error",
                    "Texto" => "Debe ingresar fecha inicio",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // Guardar filtros en sesión
            $_SESSION['fecha_inicio_factura'] = $fecha_inicio;
            $_SESSION['fecha_final_factura']  = $fecha_final;
            $_SESSION['nro_factura']          = $nro_factura;
            $_SESSION['idproveedor']          = $idproveedor;

            // Eliminar búsqueda
            if (isset($_POST['eliminar_busqueda'])) {
                unset($_SESSION['fecha_inicio_factura']);
                unset($_SESSION['fecha_final_factura']);
                unset($_SESSION['nro_factura']);
                unset($_SESSION['idproveedor']);
            }
        }
        // Otros módulos que usan busqueda_inicial
        else {
            $namevar = "busqueda_" . $modulo;

            // Iniciar búsqueda
            if (isset($_POST['busqueda_inicial'])) {
                if ($_POST['busqueda_inicial'] == "") {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrió un error inesperado!",
                        "Texto" => "Favor ingresar un valor de búsqueda",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
                $_SESSION[$namevar] = $_POST['busqueda_inicial'];
            }

            // Eliminar búsqueda
            if (isset($_POST['eliminar_busqueda'])) {
                unset($_SESSION[$namevar]);
            }
        }

        // Redireccionar
        $url = $data_url[$modulo];
        $alerta = [
            "Alerta" => "redireccionar",
            "URL" => SERVERURL . $url . "/"
        ];
        echo json_encode($alerta);
    } else {
        $alerta = [
            "Alerta" => "simple",
            "Titulo" => "Ocurrió un error inesperado!",
            "Texto" => "No podemos continuar con la búsqueda debido a un error de configuración",
            "Tipo" => "error"
        ];
        echo json_encode($alerta);
        exit();
    }
} else {
    session_unset();
    session_destroy();
    header("Location: " . SERVERURL . "login/");
    exit();
}
