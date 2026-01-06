<?php
require_once __DIR__ . "/../modelos/reportesModelo.php";
require_once __DIR__ . "/../vendor/autoload.php";

use Dompdf\Dompdf;

class reporteControlador extends reportesModelo
{
    public function imprimir_reporte_pedidos_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('compra.pedido.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        /* FILTROS */
        $desde  = !empty($_POST['desde'])  ? mainModel::limpiar_string($_POST['desde'])  : null;
        $hasta  = !empty($_POST['hasta'])  ? mainModel::limpiar_string($_POST['hasta'])  : null;
        $estado = !empty($_POST['estado']) ? mainModel::limpiar_string($_POST['estado']) : null;

        $sucursal = $_SESSION['nick_sucursal'] ?? null;

        /* DATOS */
        $datos = reportesModelo::reporte_pedidos_modelo(
            $desde,
            $hasta,
            $estado,
            $sucursal
        );

        /* DATOS EXTRA PARA EL PDF */
        $empresa  = $_SESSION['empresa_str']  ?? 'Empresa';
        $usuario  = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];
        $sucursal_nombre = $_SESSION['sucursal_nombre'] ?? 'Sucursal';

        $filtros = [
            'desde'  => $desde,
            'hasta'  => $hasta,
            'estado' => $estado ?: 'Todos'
        ];

        /* PDF */
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
}
