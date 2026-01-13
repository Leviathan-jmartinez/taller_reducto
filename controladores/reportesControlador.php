<?php
require_once __DIR__ . "/../modelos/reportesModelo.php";
require_once __DIR__ . "/../vendor/autoload.php";

use Dompdf\Dompdf;

class reporteControlador extends reportesModelo
{

    /* =========================================
        INFORMES DE COMPRAS 
    ========================================= */
    public function imprimir_reporte_pedidos_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('compra.pedido.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_pedidos_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/pedidos_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_pedidos.pdf", ["Attachment" => false]);
        exit();
    }


    public function imprimir_reporte_presupuestos_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('compra.presupuesto.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_presupuestos_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa  = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario  = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];
        $filtros  = compact('desde', 'hasta', 'estado', 'sucursal');

        ob_start();
        require_once __DIR__ . "/../pdf/presupuestos_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_presupuestos_compra.pdf", ["Attachment" => false]);
        exit();
    }

    public function imprimir_reporte_ordenes_compra_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('compra.oc.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_ordenes_compra_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/ordenescompras_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_ordenes_compra.pdf", ["Attachment" => false]);
        exit();
    }

    public function imprimir_reporte_compras_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('compra.factura.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_compras_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/compras_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_compras.pdf", ["Attachment" => false]);
        exit();
    }

    public function imprimir_reporte_libro_compras_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('compras.reporte.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde     = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta     = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $proveedor = ($_POST['proveedor'] !== '') ? mainModel::limpiar_string($_POST['proveedor']) : null;
        $sucursal  = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;
        $estado  = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;

        $datos = reportesModelo::reporte_libro_compras_modelo(
            $desde,
            $hasta,
            $proveedor,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/libro_compras_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_libro_compras.pdf", ["Attachment" => false]);
        exit();
    }

    /* =========================================
        FIN INFORMES DE COMPRAS
    ========================================= */

    /* =========================================
        INFORMES DE SERVICIOS 
    ========================================= */
    public function imprimir_reporte_recepcion_servicio_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.recepcion.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_recepcion_servicio_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/recepcion_servicio_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_recepcion_servicio.pdf", ["Attachment" => false]);
        exit();
    }

    public function imprimir_reporte_presupuesto_servicio_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.presupuesto.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_presupuesto_servicio_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/presupuesto_servicio_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream(
            "reporte_presupuesto_servicio.pdf",
            ["Attachment" => false]
        );
        exit();
    }

    public function imprimir_reporte_orden_trabajo_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.ot.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $datos = reportesModelo::reporte_orden_trabajo_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/orden_trabajo_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_orden_trabajo.pdf", ["Attachment" => false]);
        exit();
    }

    public function imprimir_reporte_registro_servicio_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.registro.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $desde    = ($_POST['desde'] !== '') ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] !== '') ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : null;
        $empleado = ($_POST['empleado'] !== '') ? mainModel::limpiar_string($_POST['empleado']) : null;

        $datos = reportesModelo::reporte_registro_servicio_modelo(
            $desde,
            $hasta,
            $estado,
            $empleado,
            $sucursal
        );

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/registro_servicio_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_registro_servicio.pdf", ["Attachment" => false]);
        exit();
    }


    /* =========================================
        FIN INFORMES DE SERVICIOS   
    ========================================= */
}
