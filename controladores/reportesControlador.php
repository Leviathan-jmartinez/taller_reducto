<?php
require_once __DIR__ . "/../modelos/reportesModelo.php";
require_once __DIR__ . "/../vendor/autoload.php";

use Dompdf\Dompdf;

class reporteControlador extends reportesModelo
{
    private function acceso_denegado_json()
    {
        return json_encode([
            "error" => "Acceso no autorizado",
            "data" => [],
            "resumen" => []
        ]);
    }

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

    public function listar_empleados_controlador()
    {
        $sql = mainModel::conectar()->query("SELECT idempleados, nombre, apellido FROM empleados WHERE estado = 1 ORDER BY apellido, nombre");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar_modelos_controlador()
    {
        $sql = mainModel::conectar()->query("SELECT id_modeloauto, mod_descri FROM modelo_auto WHERE estado = 1 ORDER BY mod_descri ASC");
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    /* ==================================================
        REPORTE ARTICULOS
    ================================================== */

    public function reporte_articulos_simple_controlador()
    {
        if (!mainModel::tienePermiso('reportes.articulos.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "categoria" => mainModel::limpiar_string($_POST['categoria']) ?? 0,
            "proveedor" => mainModel::limpiar_string($_POST['proveedor']) ?? 0,
            "estado"    => mainModel::limpiar_string($_POST['estado']) ?? 'T',
            "codigo"    => trim($_POST['codigo']) ?? ''
        ];

        $data = reportesModelo::reporte_articulos_simple_modelo($filtros);
        $resumen = reportesModelo::resumen_articulos_simple_modelo($filtros);

        return json_encode([
            "data" => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_articulos_simple_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.articulos.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "categoria" => mainModel::limpiar_string($_POST['categoria'] ?? 0),
            "proveedor" => mainModel::limpiar_string($_POST['proveedor'] ?? 0),
            "estado"    => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "codigo"    => trim($_POST['codigo'] ?? '')
        ];

        $datos = reportesModelo::reporte_articulos_simple_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/articulos_reportesimple_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape'); // 🔥 horizontal
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_articulos.pdf", ["Attachment" => true]);
        exit();
    }

    public function reporte_articulos_controlador()
    {
        if (!mainModel::tienePermiso('reportes.stock.ver')) {
            return $this->acceso_denegado_json();
        }

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

        if (!mainModel::tienePermiso('reportes.stock.ver')) {
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
        $dompdf->stream("reporte_articulos.pdf", ["Attachment" => true]);
        exit();
    }

    public function reporte_stock_controlador()
    {
        return $this->reporte_articulos_controlador();
    }

    public function imprimir_reporte_stock_controlador()
    {
        $this->imprimir_reporte_articulos_controlador();
    }

    /* ==================================================
        REPORTE SUCURSALES
    ================================================== */
    public function reporte_sucursales_controlador()
    {
        if (!mainModel::tienePermiso('reportes.sucursales.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $data = reportesModelo::reporte_sucursales_modelo($filtros);
        $resumen = reportesModelo::resumen_sucursales_modelo($filtros);

        return json_encode([
            "data" => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_sucursales_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.sucursales.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $datos = reportesModelo::reporte_sucursales_modelo($filtros);
        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/sucursales_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_sucursales.pdf", ["Attachment" => true]);
        exit();
    }

    /* ==================================================
        REPORTE VEHICULOS
    ================================================== */
    public function reporte_vehiculos_controlador()
    {
        if (!mainModel::tienePermiso('reportes.vehiculos.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "modelo" => mainModel::limpiar_string($_POST['modelo'] ?? 0),
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $data = reportesModelo::reporte_vehiculos_modelo($filtros);
        $resumen = reportesModelo::resumen_vehiculos_modelo($filtros);

        return json_encode([
            "data" => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_vehiculos_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.vehiculos.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "modelo" => mainModel::limpiar_string($_POST['modelo'] ?? 0),
            "estado" => mainModel::limpiar_string($_POST['estado'] ?? 'T'),
            "buscar" => trim($_POST['buscar'] ?? '')
        ];

        $datos = reportesModelo::reporte_vehiculos_modelo($filtros);
        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/vehiculos_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_vehiculos.pdf", ["Attachment" => true]);
        exit();
    }

    public function reporte_proveedores_controlador()
    {
        if (!mainModel::tienePermiso('reportes.proveedores.ver')) {
            return $this->acceso_denegado_json();
        }

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

        if (!mainModel::tienePermiso('reportes.proveedores.ver')) {
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
        $dompdf->stream("reporte_proveedores.pdf", ["Attachment" => true]);
        exit();
    }

    /* ==================================================
        REPORTE CLIENTES (PREVISUALIZACIÓN)
    ================================================== */
    public function reporte_clientes_controlador()
    {
        if (!mainModel::tienePermiso('reportes.clientes.ver')) {
            return $this->acceso_denegado_json();
        }

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

        if (!mainModel::tienePermiso('reportes.clientes.ver')) {
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
        $dompdf->stream("reporte_clientes.pdf", ["Attachment" => true]);
        exit();
    }

    /* ==================================================
        REPORTE EMPLEADOS (PREVISUALIZACIÓN)
    ================================================== */
    public function reporte_empleados_controlador()
    {
        if (!mainModel::tienePermiso('reportes.empleados.ver')) {
            return $this->acceso_denegado_json();
        }

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

        if (!mainModel::tienePermiso('reportes.empleados.ver')) {
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
        $dompdf->stream("reporte_empleados.pdf", ["Attachment" => true]);
        exit();
    }


    /* =========================================
        INFORMES DE COMPRAS 
    ========================================= */
    private function filtros_movimiento_basico()
    {
        return [
            ($_POST['desde'] ?? '') !== '' ? mainModel::limpiar_string($_POST['desde']) : null,
            ($_POST['hasta'] ?? '') !== '' ? mainModel::limpiar_string($_POST['hasta']) : null,
            ($_POST['estado'] ?? '') !== '' ? mainModel::limpiar_string($_POST['estado']) : null,
            ($_POST['sucursal'] ?? '') !== '' ? mainModel::limpiar_string($_POST['sucursal']) : null
        ];
    }

    public function reporte_pedidos_controlador()
    {
        if (!mainModel::tienePermiso('reportes.pedidos.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_pedidos_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_pedidos_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.pedidos.ver')) {
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
        $dompdf->stream("reporte_pedidos.pdf", ["Attachment" => true]);
        exit();
    }


    public function reporte_presupuestos_controlador()
    {
        if (!mainModel::tienePermiso('reportes.presupuestos_compra.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_presupuestos_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_presupuestos_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.presupuestos_compra.ver')) {
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
        $dompdf->stream("reporte_presupuestos_compra.pdf", ["Attachment" => true]);
        exit();
    }

    public function reporte_ordenes_compra_controlador()
    {
        if (!mainModel::tienePermiso('reportes.ordenes_compra.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_ordenes_compra_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_ordenes_compra_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.ordenes_compra.ver')) {
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
        $dompdf->stream("reporte_ordenes_compra.pdf", ["Attachment" => true]);
        exit();
    }

    public function reporte_compras_controlador()
    {
        if (!mainModel::tienePermiso('reportes.compras.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_compras_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_compras_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.compras.ver')) {
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
        $dompdf->stream("reporte_compras.pdf", ["Attachment" => true]);
        exit();
    }

    public function reporte_libro_compras_controlador()
    {
        if (!mainModel::tienePermiso('reportes.libro_compras.ver')) {
            return $this->acceso_denegado_json();
        }

        $desde     = ($_POST['desde'] ?? '') !== '' ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta     = ($_POST['hasta'] ?? '') !== '' ? mainModel::limpiar_string($_POST['hasta']) : null;
        $proveedor = ($_POST['proveedor'] ?? '') !== '' ? mainModel::limpiar_string($_POST['proveedor']) : null;
        $estado    = ($_POST['estado'] ?? '') !== '' ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal  = ($_POST['sucursal'] ?? '') !== '' ? mainModel::limpiar_string($_POST['sucursal']) : null;

        $data = reportesModelo::reporte_libro_compras_modelo($desde, $hasta, $proveedor, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_libro_compras_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.libro_compras.ver')) {
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
        $dompdf->stream("reporte_libro_compras.pdf", ["Attachment" => true]);
        exit();
    }

    public function reporte_transferencias_controlador()
    {
        if (!mainModel::tienePermiso('reportes.transferencias.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "sucursal" => mainModel::limpiar_string($_POST['sucursal']) ?? 0,
            "estado"   => mainModel::limpiar_string($_POST['estado']) ?? 'T',
            "desde"    => trim($_POST['desde'] ?? ''),
            "hasta"    => trim($_POST['hasta'] ?? ''),
            "tipo"     => mainModel::limpiar_string($_POST['tipo']) ?? 'T'
        ];


        $data = reportesModelo::reporte_transferencias_modelo($filtros);
        $resumen = reportesModelo::resumen_transferencias_modelo($filtros);

        return json_encode([
            "data"    => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_transferencias_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.transferencias.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "sucursal" => ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : 0,
            "estado"   => ($_POST['estado'] !== '') ? mainModel::limpiar_string($_POST['estado']) : 'T',
            "desde"    => trim($_POST['desde'] ?? ''),
            "hasta"    => trim($_POST['hasta'] ?? '')
        ];

        $datos = reportesModelo::reporte_transferencias_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/transferencias_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_transferencias.pdf", ["Attachment" => true]);
        exit();
    }


    /* ==================================================
        REPORTE MOVIMIENTOS DE STOCK - PREVIEW
    ================================================== */
    public function reporte_movimientos_stock_controlador()
    {
        if (!mainModel::tienePermiso('reportes.movimientos_stock.ver')) {
            return $this->acceso_denegado_json();
        }

        $filtros = [
            "sucursal" => mainModel::limpiar_string($_POST['sucursal']) ?? 0,
            "tipo"     => mainModel::limpiar_string($_POST['tipo']) ?? 'T',
            "signo"    => mainModel::limpiar_string($_POST['signo']) ?? 'T',
            "desde"    => trim($_POST['desde'] ?? ''),
            "hasta"    => trim($_POST['hasta'] ?? '')
        ];

        $data = reportesModelo::reporte_movimientos_stock_modelo($filtros);
        $resumen = reportesModelo::resumen_movimientos_stock_modelo($filtros);

        return json_encode([
            "data"    => $data,
            "resumen" => $resumen
        ]);
    }

    /* ==================================================
        REPORTE MOVIMIENTOS DE STOCK - PDF
    ================================================== */
    public function imprimir_reporte_movimientos_stock_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.movimientos_stock.ver')) {
            header("Location: " . SERVERURL . "home/");
            exit();
        }

        $filtros = [
            "sucursal" => ($_POST['sucursal'] !== '') ? mainModel::limpiar_string($_POST['sucursal']) : 0,
            "tipo"     => ($_POST['tipo'] !== '') ? mainModel::limpiar_string($_POST['tipo']) : 'T',
            "signo"    => ($_POST['signo'] !== '') ? mainModel::limpiar_string($_POST['signo']) : 'T',
            "desde"    => trim($_POST['desde'] ?? ''),
            "hasta"    => trim($_POST['hasta'] ?? '')
        ];

        $datos = reportesModelo::reporte_movimientos_stock_modelo($filtros);

        $empresa = $_SESSION['empresa_nombre'] ?? 'Empresa';
        $usuario = $_SESSION['nombre_str'] . ' ' . $_SESSION['apellido_str'];

        ob_start();
        require_once __DIR__ . "/../pdf/movimientos_stock_reporte_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $dompdf->stream("reporte_movimientos_stock.pdf", ["Attachment" => true]);
        exit();
    }

    /* =========================================
        FIN INFORMES DE COMPRAS
    ========================================= */

    /* =========================================
        INFORMES DE SERVICIOS 
    ========================================= */
    public function reporte_recepcion_servicio_controlador()
    {
        if (!mainModel::tienePermiso('reportes.recepcion_servicio.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_recepcion_servicio_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_recepcion_servicio_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.recepcion_servicio.ver')) {
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
        $dompdf->stream("reporte_recepcion_servicio.pdf", ["Attachment" => true]);
        exit();
    }

    public function reporte_presupuesto_servicio_controlador()
    {
        if (!mainModel::tienePermiso('reportes.presupuesto_servicio.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_presupuesto_servicio_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_presupuesto_servicio_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.presupuesto_servicio.ver')) {
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
            ["Attachment" => true]
        );
        exit();
    }

    public function reporte_orden_trabajo_controlador()
    {
        if (!mainModel::tienePermiso('reportes.orden_trabajo.ver')) {
            return $this->acceso_denegado_json();
        }

        [$desde, $hasta, $estado, $sucursal] = $this->filtros_movimiento_basico();
        $data = reportesModelo::reporte_orden_trabajo_modelo($desde, $hasta, $estado, $sucursal);

        return json_encode(["data" => $data]);
    }

    public function imprimir_reporte_orden_trabajo_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.orden_trabajo.ver')) {
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
        $dompdf->stream("reporte_orden_trabajo.pdf", ["Attachment" => true]);
        exit();
    }

    public function reporte_registro_servicio_controlador()
    {
        if (!mainModel::tienePermiso('reportes.registro_servicio.ver')) {
            return $this->acceso_denegado_json();
        }

        $desde    = ($_POST['desde'] ?? '') !== '' ? mainModel::limpiar_string($_POST['desde']) : null;
        $hasta    = ($_POST['hasta'] ?? '') !== '' ? mainModel::limpiar_string($_POST['hasta']) : null;
        $estado   = ($_POST['estado'] ?? '') !== '' ? mainModel::limpiar_string($_POST['estado']) : null;
        $sucursal = ($_POST['sucursal'] ?? '') !== '' ? mainModel::limpiar_string($_POST['sucursal']) : null;
        $empleado = ($_POST['empleado'] ?? '') !== '' ? mainModel::limpiar_string($_POST['empleado']) : null;

        $data = reportesModelo::reporte_registro_servicio_modelo($desde, $hasta, $estado, $empleado, $sucursal);
        $resumen = reportesModelo::resumen_registro_servicio_modelo($desde, $hasta, $estado, $empleado, $sucursal);

        return json_encode([
            "data" => $data,
            "resumen" => $resumen
        ]);
    }

    public function imprimir_reporte_registro_servicio_controlador()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('reportes.registro_servicio.ver')) {
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
        $resumen = reportesModelo::resumen_registro_servicio_modelo(
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
        $dompdf->stream("reporte_registro_servicio.pdf", ["Attachment" => true]);
        exit();
    }


    /* =========================================
        FIN INFORMES DE SERVICIOS   
    ========================================= */
}
