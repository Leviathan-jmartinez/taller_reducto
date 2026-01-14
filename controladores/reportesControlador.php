<?php
require_once __DIR__ . "/../modelos/reportesModelo.php";
require_once __DIR__ . "/../vendor/autoload.php";

use Dompdf\Dompdf;

class reporteControlador extends reportesModelo
{
    public function listar_sucursales_controlador()
    {
        $sql = mainModel::conectar()->query("SELECT id_sucursal, suc_descri FROM sucursales");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar_categorias_controlador()
    {
        $sql = mainModel::conectar()->query("SELECT id_categoria, cat_descri FROM categorias");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar_proveedores_controlador()
    {
        $sql = mainModel::conectar()->query("SELECT idproveedores, razon_social FROM proveedores");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listar_cargos_controlador()
    {
        $sql = mainModel::conectar()->query("SELECT idcargos, descripcion FROM cargos");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    /* ==================================================
        REPORTE ARTICULOS
    ================================================== */
    public function reporte_articulos_controlador()
    {
        $filtros = [
            "sucursal"  => mainModel::limpiar_string($_POST['sucursal']) ?? 0,
            "categoria" => mainModel::limpiar_string($_POST['categoria']) ?? 0,
            "proveedor" => mainModel::limpiar_string($_POST['proveedor']) ?? 0,
            "estado"    => mainModel::limpiar_string($_POST['estado']) ?? 'T',
            "codigo"    => trim($_POST['codigo']) ?? '',
            "stock"     => mainModel::limpiar_string($_POST['stock']) ?? 'T'
        ];


        $data = reportesModelo::reporte_articulos_modelo($filtros);
        $resumen = reportesModelo::resumen_articulos_modelo($filtros);

        return json_encode([
            "data"    => $data,
            "resumen" => $resumen
        ]);
    }
    public function imprimir_reporte_articulos_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('articulo.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "sucursal"  => ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : 0,
            "categoria" => ($_POST['categoria'] !== '') ? mainModel::limpiar_string($_POST['categoria']) : 0,
            "proveedor" => ($_POST['proveedor'] !== '') ? mainModel::limpiar_string($_POST['proveedor']) : 0,
            "estado"    => ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : 'T',
            "stock"     => ($_POST['stock'] !== '') ? mainModel::limpiar_string($_POST['stock']) : 'T',
            "codigo"    => trim($_POST['codigo'] ?? '')
        ];

        $datos = reportesModelo::reporte_articulos_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/articulos_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_articulos.pdf", ["Attachment" => false]);
        exit();
    }

    /* ==================================================
        REPORTE PROVEEDORES (PREVISUALIZACIÓN)
    ================================================== */
    public function reporte_proveedores_controlador()
    {
        $filtros = [
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $data = reportesModelo::reporte_proveedores_modelo($filtros);
        $resumen = reportesModelo::resumen_proveedores_modelo($filtros);

        return json_encode([
            "data"    => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_proveedores_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('proveedor.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $datos = reportesModelo::reporte_proveedores_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/proveedores_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_proveedores.pdf", ["Attachment" => false]);
        exit();
    }

    /* ==================================================
        REPORTE CLIENTES (PREVISUALIZACIÓN)
    ================================================== */
    public function reporte_clientes_controlador()
    {
        $filtros = [
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $data = reportesModelo::reporte_clientes_modelo($filtros);
        $resumen = reportesModelo::resumen_clientes_modelo($filtros);

        return json_encode([
            "data"    => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_clientes_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('cliente.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $datos = reportesModelo::reporte_clientes_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/clientes_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_clientes.pdf", ["Attachment" => false]);
        exit();
    }

    /* ==================================================
        REPORTE EMPLEADOS (PREVISUALIZACIÓN)
    ================================================== */
    public function reporte_empleados_controlador()
    {
        $filtros = [
            "sucursal" => mainModel::limpiar_string($_POST['sucursal'] ?? 0),
            "cargo"    => mainModel::limpiar_string($_POST['cargo'] ?? 0),
            "estado"   => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar"   => trim($_POST['buscar'] ?? '')
        ];

        $data = reportesModelo::reporte_empleados_modelo($filtros);
        $resumen = reportesModelo::resumen_empleados_modelo($filtros);

        return json_encode([
            "data"    => $data,
            "resumen" => $resumen
        ]);
    }

    /* ==================================================
        REPORTE EMPLEADOS (PDF)
    ================================================== */
    public function imprimir_reporte_empleados_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('empleado.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "sucursal" => mainModel::limpiar_string($_POST['sucursal'] ?? 0),
            "cargo"    => mainModel::limpiar_string($_POST['cargo'] ?? 0),
            "estado"   => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar"   => trim($_POST['buscar'] ?? '')
        ];

        $datos = reportesModelo::reporte_empleados_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/empleados_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_empleados.pdf", ["Attachment" => false]);
        exit();
    }


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
