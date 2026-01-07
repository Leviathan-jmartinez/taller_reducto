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
    /** Encriptar */
    public function encryption($string)
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

    public static function tienePermiso($clave)
    {
        if (isset($_SESSION['nivel_str']) && $_SESSION['nivel_str'] == 1) {
            return true;
        }

        return isset($_SESSION['permisos']) && in_array($clave, $_SESSION['permisos']);
    }

    public static function puedeVerMenu($modulo)
    {
        // Fallback super admin (opcional pero recomendado)
        if (isset($_SESSION['nivel_str']) && $_SESSION['nivel_str'] == 1) {
            return true;
        }

        if (!isset($_SESSION['permisos']) || empty($_SESSION['permisos'])) {
            return false;
        }

        foreach ($_SESSION['permisos'] as $permiso) {
            if (strpos($permiso, $modulo . '.') === 0) {
                return true;
            }
        }
        return false;
    }

    public static function tienePermisoVista(string $permiso): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        if (!isset($_SESSION['permisos']) || !is_array($_SESSION['permisos'])) {
            return false;
        }

        return in_array($permiso, $_SESSION['permisos']);
    }
}
