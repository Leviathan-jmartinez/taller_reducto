<?php
require_once __DIR__ . "/../config/SERVER.php";

class mainModel
{
    /** conexion a la BD */
    public static function conectar()
    {
        $conexion = new PDO(SGBD, USER, PASS);
        $conexion->exec("SET CHARACTER SET utf8");
        return $conexion;
    }

    /** Ejecutar consultas simples */
    protected static function ejecutar_consulta_simple($consulta)
    {
        $sql = self::conectar()->prepare($consulta);
        $sql->execute();
        return $sql;
    }

    protected static function mov_stock_tiene_saldos(PDO $conexion)
    {
        static $disponible = null;

        if ($disponible !== null) {
            return $disponible;
        }

        try {
            $sql = $conexion->prepare("SHOW COLUMNS FROM movimientostock LIKE 'MovStockSaldoActual'");
            $sql->execute();
            $disponible = $sql->rowCount() > 0;
        } catch (Exception $e) {
            $disponible = false;
        }

        return $disponible;
    }

    public static function registrar_movimiento_stock_modelo(PDO $conexion, array $datos)
    {
        $idSucursal = (int)($datos['id_sucursal'] ?? 0);
        $idArticulo = (int)($datos['id_articulo'] ?? 0);
        $cantidad = abs((float)($datos['cantidad'] ?? 0));
        $signo = ((int)($datos['signo'] ?? 0) >= 0) ? 1 : -1;
        $usuario = (int)($datos['usuario'] ?? 0);
        $referencia = (string)($datos['referencia'] ?? '');

        if ($idSucursal <= 0 || $idArticulo <= 0 || $cantidad <= 0 || $usuario <= 0) {
            throw new Exception('Datos incompletos para registrar movimiento de stock');
        }

        $stock = $conexion->prepare("
            SELECT stockDisponible
            FROM stock
            WHERE id_sucursal = :sucursal
              AND id_articulo = :articulo
            LIMIT 1
            FOR UPDATE
        ");
        $stock->execute([
            ':sucursal' => $idSucursal,
            ':articulo' => $idArticulo
        ]);

        $filaStock = $stock->fetch(PDO::FETCH_ASSOC);
        $saldoAnterior = $filaStock ? (float)$filaStock['stockDisponible'] : 0.0;
        $saldoActual = $saldoAnterior + ($cantidad * $signo);

        if ($saldoActual < 0) {
            throw new Exception('Stock insuficiente para el articulo ID ' . $idArticulo);
        }

        if ($filaStock) {
            $actualizar = $conexion->prepare("
                UPDATE stock
                SET stockDisponible = :saldo,
                    stockUltActualizacion = NOW(),
                    stockUsuActualizacion = :usuario,
                    stockultimoIdActualizacion = :referencia
                WHERE id_sucursal = :sucursal
                  AND id_articulo = :articulo
            ");
            $actualizar->execute([
                ':saldo' => $saldoActual,
                ':usuario' => $usuario,
                ':referencia' => (int)($datos['id_referencia_stock'] ?? 0),
                ':sucursal' => $idSucursal,
                ':articulo' => $idArticulo
            ]);
        } else {
            $insertarStock = $conexion->prepare("
                INSERT INTO stock
                (id_sucursal, id_articulo, stockcant_max, stockcant_min,
                 stockDisponible, stockUltActualizacion, stockUsuActualizacion, stockultimoIdActualizacion)
                VALUES
                (:sucursal, :articulo, 200, 15, :saldo, NOW(), :usuario, :referencia)
            ");
            $insertarStock->execute([
                ':sucursal' => $idSucursal,
                ':articulo' => $idArticulo,
                ':saldo' => $saldoActual,
                ':usuario' => $usuario,
                ':referencia' => (int)($datos['id_referencia_stock'] ?? 0)
            ]);
        }

        $camposSaldo = self::mov_stock_tiene_saldos($conexion)
            ? ", MovStockSaldoAnterior, MovStockSaldoActual"
            : "";
        $valoresSaldo = self::mov_stock_tiene_saldos($conexion)
            ? ", :saldo_anterior, :saldo_actual"
            : "";

        $sql = $conexion->prepare("
            INSERT INTO movimientostock
            (id_sucursal, TipoMovStockId, MovStockArticuloId, MovStockCantidad,
             MovStockPrecioVenta, MovStockCosto, MovStockFechaHora,
             MovStockNroTicket, MovStockPOS, MovStockUsuario,
             MovStockSigno, MovStockReferencia{$camposSaldo})
            VALUES
            (:sucursal, :tipo, :articulo, :cantidad,
             :precio_venta, :costo, COALESCE(:fecha, NOW()),
             :nro_ticket, :pos, :usuario,
             :signo, :referencia{$valoresSaldo})
        ");

        $params = [
            ':sucursal' => $idSucursal,
            ':tipo' => (string)($datos['tipo'] ?? 'MOVIMIENTO'),
            ':articulo' => $idArticulo,
            ':cantidad' => $cantidad,
            ':precio_venta' => (float)($datos['precio_venta'] ?? 0),
            ':costo' => (float)($datos['costo'] ?? 0),
            ':fecha' => $datos['fecha'] ?? null,
            ':nro_ticket' => $datos['nro_ticket'] ?? null,
            ':pos' => $datos['pos'] ?? null,
            ':usuario' => $usuario,
            ':signo' => $signo,
            ':referencia' => $referencia
        ];

        if (self::mov_stock_tiene_saldos($conexion)) {
            $params[':saldo_anterior'] = $saldoAnterior;
            $params[':saldo_actual'] = $saldoActual;
        }

        $sql->execute($params);
        $idMovimiento = (int)$conexion->lastInsertId();

        if ($idMovimiento > 0) {
            $actualizarMovimiento = $conexion->prepare("
                UPDATE stock
                SET stockultimoIdActualizacion = :movimiento
                WHERE id_sucursal = :sucursal
                  AND id_articulo = :articulo
            ");
            $actualizarMovimiento->execute([
                ':movimiento' => $idMovimiento,
                ':sucursal' => $idSucursal,
                ':articulo' => $idArticulo
            ]);
        }

        return $idMovimiento;
    }
    /** Encriptar */
    public static function encryption($string)
    {
        $output = FALSE;
        $key = hash('sha256', SECRET_KEY);
        $iv = substr(hash('sha256', SECRET_IV), 0, 16);
        $output = openssl_encrypt($string, METHOD, $key, 0, $iv);
        $output = base64_encode($output);
        return $output;
    }
    /** desencriptar */
    protected static function decryption($string)
    {
        $key = hash('sha256', SECRET_KEY);
        $iv = substr(hash('sha256', SECRET_IV), 0, 16);
        $output = openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
        return $output;
    }

    /**generar numeros aleatorios */
    protected static function generar_codigos_ALT($letra, $longitud, $numero)
    {
        for ($i = 1; $i <= $longitud; $i++) {
            $aleatorio = rand(0, 9);
            $letra .= $aleatorio;
        }
        return $letra . "-" . $numero;
    }

    /**limpiar texto o caracteres en cadenas*/
    protected static function limpiar_string($cadena)
    {
        $cadena = trim($cadena);
        $cadena = stripslashes($cadena);
        $cadena = str_ireplace("<script>", "", $cadena);
        $cadena = str_ireplace("</script>", "", $cadena);
        $cadena = str_ireplace("<script src", "", $cadena);
        $cadena = str_ireplace("<script type=", "", $cadena);
        $cadena = str_ireplace("SELECT * FROM", "", $cadena);
        $cadena = str_ireplace("DELETE FROM", "", $cadena);
        $cadena = str_ireplace("INSERT INTO", "", $cadena);
        $cadena = str_ireplace("DROP TABLE", "", $cadena);
        $cadena = str_ireplace("DROP DATABASE", "", $cadena);
        $cadena = str_ireplace("TRUNCATE TABLE", "", $cadena);
        $cadena = str_ireplace("SHOW TABLES", "", $cadena);
        $cadena = str_ireplace("SHOW DATABASES", "", $cadena);
        $cadena = str_ireplace("<?php", "", $cadena);
        $cadena = str_ireplace("?>", "", $cadena);
        $cadena = str_ireplace("--", "", $cadena);
        $cadena = str_ireplace(">", "", $cadena);
        $cadena = str_ireplace("<", "", $cadena);
        $cadena = str_ireplace("[", "", $cadena);
        $cadena = str_ireplace("]", "", $cadena);
        $cadena = str_ireplace("^", "", $cadena);
        $cadena = str_ireplace("==", "", $cadena);
        $cadena = str_ireplace(";", "", $cadena);
        $cadena = str_ireplace("::", "", $cadena);
        $cadena = str_ireplace("'", "", $cadena);
        $cadena = stripslashes($cadena);
        $cadena = trim($cadena);
        return $cadena;
    }

    /**funciona para verificar datos de campos */
    protected static function verificarDatos($filtro, $cadena)
    {
        if (preg_match("/^" . $filtro . "$/", $cadena)) {
            return false;
        } else {
            return true;
        }
    }

    /**funciona para verificar datos de campos */
    protected static function verificarFecha($fecha)
    {
        $valores = explode('-', $fecha);
        if (count($valores) == 3 && checkdate($valores[1], $valores[2], $valores[0])) {
            return false;
        } else {
            return true;
        }
    }

    /** Ordenamiento reutilizable para listados */
    public static function cargar_ordenamiento_sesion($prefijo, $columnasPermitidas, $ordenDefault = 'fecha', $direccionDefault = 'DESC')
    {
        $paramOrden = $prefijo . '_orden';
        $paramDireccion = $prefijo . '_direccion';
        $direccionDefault = strtoupper($direccionDefault);

        if (isset($_GET[$paramOrden]) && in_array((string)$_GET[$paramOrden], $columnasPermitidas, true)) {
            $_SESSION[$paramOrden] = (string)$_GET[$paramOrden];
        }

        if (isset($_GET[$paramDireccion]) && in_array(strtoupper((string)$_GET[$paramDireccion]), ['ASC', 'DESC'], true)) {
            $_SESSION[$paramDireccion] = strtoupper((string)$_GET[$paramDireccion]);
        }

        if (!isset($_SESSION[$paramOrden]) || !in_array((string)$_SESSION[$paramOrden], $columnasPermitidas, true)) {
            $_SESSION[$paramOrden] = $ordenDefault;
        }

        if (!isset($_SESSION[$paramDireccion]) || !in_array((string)$_SESSION[$paramDireccion], ['ASC', 'DESC'], true)) {
            $_SESSION[$paramDireccion] = in_array($direccionDefault, ['ASC', 'DESC'], true) ? $direccionDefault : 'DESC';
        }

        return [
            'orden' => $_SESSION[$paramOrden],
            'direccion' => $_SESSION[$paramDireccion],
            'param_orden' => $paramOrden,
            'param_direccion' => $paramDireccion
        ];
    }

    public static function preparar_ordenamiento($orden, $direccion, $columnasSql, $ordenDefault = 'fecha', $direccionDefault = 'DESC')
    {
        $orden = self::limpiar_string($orden);
        $direccion = strtoupper(self::limpiar_string($direccion));
        $direccionDefault = strtoupper($direccionDefault);

        if (!isset($columnasSql[$orden])) {
            $orden = isset($columnasSql[$ordenDefault]) ? $ordenDefault : array_key_first($columnasSql);
        }

        if (!in_array($direccion, ['ASC', 'DESC'], true)) {
            $direccion = in_array($direccionDefault, ['ASC', 'DESC'], true) ? $direccionDefault : 'DESC';
        }

        return [
            'orden' => $orden,
            'direccion' => $direccion,
            'sql' => $columnasSql[$orden] . ' ' . $direccion
        ];
    }

    public static function link_orden_tabla($url, $columna, $texto, $ordenActual, $direccionActual, $paramOrden = 'orden', $paramDireccion = 'direccion')
    {
        $siguienteDireccion = ($ordenActual === $columna && $direccionActual === 'ASC') ? 'DESC' : 'ASC';
        $icono = 'fa-sort';

        if ($ordenActual === $columna) {
            $icono = $direccionActual === 'ASC' ? 'fa-sort-up' : 'fa-sort-down';
        }

        return '<a class="text-white" href="' . $url . '1/?' . $paramOrden . '=' . rawurlencode($columna) . '&' . $paramDireccion . '=' . $siguienteDireccion . '">' .
            htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') . ' <i class="fas ' . $icono . '"></i></a>';
    }

    /**paginador de tablas */
    protected static function paginador($pagina, $Npaginas, $url, $botones)
    {
        $tabla = '<nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">';
        if ($pagina == 1) {
            $tabla .= '
                        <li class="page-item disabled">
                                <a class="page-link">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                        </li>';
        } else {
            $tabla .= '
                        <li class="page-item">
                                <a class="page-link" href="' . $url . '1/">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                        </li>
                        <li class="page-item">
                                <a class="page-link" href="' . $url . ($pagina - 1) . '/">
                                    Anterior
                                </a>
                        </li>';
        }
        $ci = 0;
        for ($i = $pagina; $i <= $Npaginas; $i++) {
            if ($ci >= $botones) {
                break;
            }
            if ($pagina == $i) {
                $tabla .= '<li class="page-item">
                                <a class="page-link active" href="' . $url . $i . '/">
                                    ' . $i . '
                                </a>
                        </li>';
            } else {
                $tabla .= '<li class="page-item">
                                <a class="page-link" href="' . $url . $i . '/">
                                    ' . $i . '
                                </a>
                        </li>';
            }
            $ci++;
        }

        if ($pagina == $Npaginas) {
            $tabla .= '
                        <li class="page-item disabled">
                                <a class="page-link">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                        </li>';
        } else {
            $tabla .= '
                        <li class="page-item">
                                <a class="page-link" href="' . $url . ($pagina + 1) . '/">
                                    Siguiente
                                </a>
                        </li>
                        <li class="page-item">
                                <a class="page-link" href="' . $url . $Npaginas . '/">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                        </li>
                        ';
        }

        $tabla .= '   </ul>
                </nav>';
        return $tabla;
    }

    public static function cargarPermisosSesion($idUsuario)
    {
        $sql = self::conectar()->prepare("
        SELECT p.clave
        FROM permisos p
        INNER JOIN rol_permiso rp ON rp.id_permiso = p.id_permiso
        INNER JOIN usuarios u ON u.id_rol = rp.id_rol
        WHERE u.id_usuario = ?
        ");
        $sql->execute([$idUsuario]);

        $_SESSION['permisos'] = array_column(
            $sql->fetchAll(PDO::FETCH_ASSOC),
            'clave'
        );
    }

    public static function tienePermiso(string $permiso): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        return isset($_SESSION['permisos']) &&
            is_array($_SESSION['permisos']) &&
            in_array($permiso, $_SESSION['permisos']);
    }
    public static function ejecutarPaginador($conexion, $baseSQL, $selectSQL, $orderSQL, $inicio, $registros)
    {
        // 🔹 DATOS
        $datos = $conexion->query("
        $selectSQL
        $baseSQL
        $orderSQL
        LIMIT $inicio, $registros
        ")->fetchAll();

        // 🔹 TOTAL
        $total = (int)$conexion->query("
        SELECT COUNT(*) 
        $baseSQL
        ")->fetchColumn();

        return [
            "datos" => $datos,
            "total" => $total
        ];
    }

    public static function construirFiltros($filtros = [])
    {
        $sql = "";

        foreach ($filtros as $f) {

            if (!isset($f['campo'], $f['tipo'])) continue;

            $campo = $f['campo'];
            $tipo  = strtoupper($f['tipo']);

            switch ($tipo) {
                case 'IN':
                    if (is_array($f['valor']) && count($f['valor']) > 0) {
                        $valores = implode(",", $f['valor']);
                        $sql .= " AND $campo IN ($valores)";
                    }
                    break;
                case 'LIKE':
                    if (!empty($f['valor'])) {
                        $valor = self::limpiar_string($f['valor']);
                        $sql .= " AND $campo LIKE '%$valor%'";
                    }
                    break;

                case '=':
                    if (isset($f['valor'])) {
                        $valor = self::limpiar_string($f['valor']);
                        $sql .= " AND $campo = '$valor'";
                    }
                    break;

                case '!=':
                    if (isset($f['valor'])) {
                        $valor = self::limpiar_string($f['valor']);
                        $sql .= " AND $campo != '$valor'";
                    }
                    break;

                case 'RAW':
                    if (!empty($campo)) {
                        $sql .= " AND $campo";
                    }
                    break;

                case 'DATE_RANGE':

                    if (!empty($f['desde']) && !empty($f['hasta'])) {

                        $desde = self::limpiar_string($f['desde']);
                        $hasta = self::limpiar_string($f['hasta']);

                        $sql .= " AND DATE($campo) BETWEEN '$desde' AND '$hasta'";
                    } elseif (!empty($f['desde'])) {

                        $desde = self::limpiar_string($f['desde']);

                        $sql .= " AND DATE($campo) >= '$desde'";
                    } elseif (!empty($f['hasta'])) {

                        $hasta = self::limpiar_string($f['hasta']);

                        $sql .= " AND DATE($campo) <= '$hasta'";
                    }

                    break;
            }
        }

        return $sql;
    }
    public static function validarSelect($valor, $nombre)
    {
        if ($valor === "" || $valor === null) {

            echo json_encode([
                "Alerta" => "simple",
                "Titulo" => "Campo requerido",
                "Texto" => "Debe seleccionar " . $nombre,
                "Tipo" => "error"
            ]);

            exit();
        }
    }

    protected static function registrar_articulo_proveedor_modelo($id_articulo, $idproveedores, $precio_compra)
    {
        $sql = self::conectar()->prepare("
            INSERT INTO articulo_proveedor
            (id_articulo, idproveedores, precio_compra, activo)
            VALUES (:id_articulo, :idproveedores, :precio_compra, 1)
            ON DUPLICATE KEY UPDATE
                precio_compra = VALUES(precio_compra),
                activo = 1
        ");

        $sql->execute([
            ":id_articulo" => $id_articulo,
            ":idproveedores" => $idproveedores,
            ":precio_compra" => $precio_compra
        ]);

        return $sql;
    }
}
