<?php
session_start(['name' => 'STR']);
require_once "../config/APP.php";

if (isset($_POST['busqueda_inicial']) || isset($_POST['eliminar_busqueda'])  || isset($_POST['fecha_inicio']) || isset($_POST['fecha_final'])) {
    $data_url = [
        "usuario" => "usuario-buscar",
        "cliente" => "cliente-buscar",
        "articulo" => "articulo-buscar",
        "pedido" => "pedido-buscar",
        "presupuesto" => "presupuesto-buscar"
    ];
    if (isset($_POST['modulo'])) {
        $modulo = $_POST['modulo'];
        if (!isset($data_url[$modulo])) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No podemos continuar con la busqueda debido a un error",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        if ($modulo == "pedido" || $modulo == "presupuesto") {
            $fecha_inicio = "fecha_inicio_" . $modulo;
            $fecha_final = "fecha_final_" . $modulo;

            // iniciar busqueda
            if (isset($_POST['fecha_inicio']) || isset($_POST['fecha_final'])) {
                if ($_POST['fecha_inicio'] == "" || $_POST['fecha_final'] == "") {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrio un error inesperado!",
                        "Texto" => "Por favor ingrese una fecha de inicio y final válida",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
                $_SESSION[$fecha_inicio] = $_POST['fecha_inicio'];
                $_SESSION[$fecha_final] = $_POST['fecha_final'];
            }
            // eliminar busqueda
            if (isset($_POST['eliminar_busqueda'])) {
                unset($_SESSION[$fecha_inicio]);
                unset($_SESSION[$fecha_final]);
            }
        } else {
            $namevar = "busqueda_" . $modulo;
            // iniciar busqueda
            if (isset($_POST['busqueda_inicial'])) {
                if ($_POST['busqueda_inicial'] == "") {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrio un error inesperado!",
                        "Texto" => "Favor ingresar un valor de búsqueda",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
                $_SESSION[$namevar] = $_POST['busqueda_inicial'];
            }
            // eliminar busqueda
            if (isset($_POST['eliminar_busqueda'])) {
                unset($_SESSION[$namevar]);
            }
        }
        // redireccionar
        $url = $data_url[$modulo];
        $alerta = [
            "Alerta" => "redireccionar",
            "URL" => SERVERURL . $url . "/"
        ];
        echo json_encode($alerta);
    } else {
        $alerta = [
            "Alerta" => "simple",
            "Titulo" => "Ocurrio un error inesperado!",
            "Texto" => "No podemos continuar con la busqueda debido a un error de configuración",
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
