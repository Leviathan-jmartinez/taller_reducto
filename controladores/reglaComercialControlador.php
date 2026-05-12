<?php
require_once __DIR__ . "/../modelos/reglaComercialModelo.php";

class reglaComercialControlador extends reglaComercialModelo
{
    public function guardar_regla_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.regla_comercial.crear')) {
            return $this->respuesta('Acceso no autorizado', 'No tiene permiso para crear reglas comerciales', 'error');
        }

        $datos = $this->normalizar_regla_post();
        $validacion = $this->validar_regla($datos['regla'], $datos['condiciones'], $datos['descuentos']);

        if ($validacion !== true) {
            return $this->respuesta('Datos invalidos', $validacion, 'warning');
        }

        $res = reglaComercialModelo::guardar_regla_modelo($datos['regla'], $datos['condiciones'], $datos['descuentos']);

        if (is_int($res) && $res > 0) {
            return json_encode([
                'Alerta' => 'limpiar',
                'Titulo' => 'Regla registrada',
                'Texto' => 'La regla comercial se guardo correctamente',
                'Tipo' => 'success'
            ]);
        }

        return $this->respuesta('Error', $res['msg'] ?? 'No se pudo guardar la regla comercial', 'error');
    }

    public function editar_regla_controlador()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(['name' => 'STR']);
        }

        if (!mainModel::tienePermiso('servicio.regla_comercial.editar')) {
            return $this->respuesta('Acceso no autorizado', 'No tiene permiso para editar reglas comerciales', 'error');
        }

        $datos = $this->normalizar_regla_post();
        $datos['regla']['id'] = mainModel::decryption($_POST['id_regla'] ?? '');

        if ((int)$datos['regla']['id'] <= 0) {
            return $this->respuesta('Datos invalidos', 'Identificador de regla invalido', 'error');
        }

        $validacion = $this->validar_regla($datos['regla'], $datos['condiciones'], $datos['descuentos']);

        if ($validacion !== true) {
            return $this->respuesta('Datos invalidos', $validacion, 'warning');
        }

        $res = reglaComercialModelo::editar_regla_modelo($datos['regla'], $datos['condiciones'], $datos['descuentos']);

        if ($res === true) {
            return json_encode([
                'Alerta' => 'recargar',
                'Titulo' => 'Regla actualizada',
                'Texto' => 'La regla comercial se actualizo correctamente',
                'Tipo' => 'success'
            ]);
        }

        return $this->respuesta('Error', $res['msg'] ?? 'No se pudo actualizar la regla comercial', 'error');
    }

    public function listar_reglas_controlador($pagina = 1, $registros = 15, $url = 'regla-comercial-lista')
    {
        $pagina = max(1, (int)$pagina);
        $inicio = ($pagina - 1) * $registros;
        $url = SERVERURL . $url . "/";

        $filtros = [
            'buscar' => trim($_GET['buscar'] ?? ''),
            'estado' => $_GET['estado'] ?? '',
            'vigente' => $_GET['vigente'] ?? '',
            'id_sucursal' => $_GET['id_sucursal'] ?? ''
        ];

        $res = reglaComercialModelo::listar_reglas_modelo($inicio, $registros, $filtros);
        $datos = $res['datos'];
        $total = $res['total'];
        $nPaginas = ceil($total / $registros);
        $contador = $inicio + 1;

        $tabla = '<div class="table-responsive">
            <table class="table table-dark table-sm">
            <thead><tr class="text-center">
                <th>#</th>
                <th>Nombre</th>
                <th>Vigencia</th>
                <th>Sucursal</th>
                <th>Prioridad</th>
                <th>Competencia</th>
                <th>Cond.</th>
                <th>Desc.</th>
                <th>Estado</th>
                <th>Editar</th>
            </tr></thead><tbody>';

        if ($total >= 1 && $pagina <= $nPaginas) {
            foreach ($datos as $r) {
                $estado = $r['estado'] == 1
                    ? '<span class="badge badge-success">Activa</span>'
                    : '<span class="badge badge-danger">Inactiva</span>';

                $tabla .= '<tr class="text-center">
                    <td>' . $contador++ . '</td>
                    <td>' . htmlspecialchars($r['nombre'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . $r['fecha_inicio'] . ' - ' . $r['fecha_fin'] . '</td>
                    <td>' . htmlspecialchars($r['suc_descri'] ?? 'Todas', ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . (int)$r['prioridad'] . '</td>
                    <td>' . htmlspecialchars($this->texto_modo_competencia($r['modo_competencia'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . (int)$r['condiciones'] . '</td>
                    <td>' . (int)$r['descuentos'] . '</td>
                    <td>' . $estado . '</td>
                    <td>
                        <a class="btn btn-sm btn-warning" href="' . SERVERURL . 'regla-comercial-nuevo/' . mainModel::encryption($r['id_regla']) . '/">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>';
            }
        } else {
            $tabla .= '<tr><td colspan="10" class="text-center">No hay reglas comerciales</td></tr>';
        }

        $tabla .= '</tbody></table></div>';

        if ($total >= 1 && $pagina <= $nPaginas) {
            $tabla .= '<p class="text-right">Mostrando ' . ($inicio + 1) . ' al ' . ($contador - 1) . ' de ' . $total . '</p>';
            $tabla .= mainModel::paginador($pagina, $nPaginas, $url, 10);
        }

        return $tabla;
    }

    public function datos_regla_controlador($id)
    {
        $id = mainModel::decryption($id);
        return (int)$id > 0 ? reglaComercialModelo::datos_regla_modelo($id) : false;
    }

    public function condiciones_regla_controlador($id)
    {
        $id = mainModel::decryption($id);
        return (int)$id > 0 ? reglaComercialModelo::condiciones_regla_modelo($id) : [];
    }

    public function descuentos_regla_controlador($id)
    {
        $id = mainModel::decryption($id);
        return (int)$id > 0 ? reglaComercialModelo::descuentos_regla_modelo($id) : [];
    }

    private function normalizar_regla_post()
    {
        $condiciones = json_decode($_POST['condiciones_json'] ?? '[]', true);
        $descuentos = json_decode($_POST['descuentos_json'] ?? '[]', true);

        return [
            'regla' => [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
                'fecha_fin' => $_POST['fecha_fin'] ?? '',
                'id_sucursal' => empty($_POST['id_sucursal']) ? null : (int)$_POST['id_sucursal'],
                'prioridad' => (int)($_POST['prioridad'] ?? 0),
                'modo_competencia' => $this->normalizar_modo_competencia($_POST['modo_competencia'] ?? ''),
                'estado' => isset($_POST['estado']) ? 1 : 0,
                'usuario' => $_SESSION['id_str']
            ],
            'condiciones' => is_array($condiciones) ? $this->normalizar_condiciones($condiciones) : [],
            'descuentos' => is_array($descuentos) ? $this->normalizar_descuentos($descuentos) : []
        ];
    }

    private function normalizar_condiciones($condiciones)
    {
        $permitidos = ['CLIENTE', 'ARTICULO', 'CATEGORIA', 'TOTAL_OPERACION', 'CANTIDAD_ITEMS', 'SUCURSAL'];
        $operadores = ['=', '!=', '>=', '<=', '>', '<'];
        $limpio = [];

        foreach ($condiciones as $c) {
            $tipo = $this->normalizar_tipo_condicion($c['tipo_condicion'] ?? '');
            $operador = trim($c['operador'] ?? '=');
            $valorTexto = trim($c['valor_texto'] ?? '');
            $valorRef = $c['valor_ref'] ?? null;

            if (!in_array($tipo, $permitidos, true) || !in_array($operador, $operadores, true)) {
                continue;
            }

            if (!$this->operador_valido_para_condicion($tipo, $operador)) {
                continue;
            }

            $limpio[] = [
                'tipo_condicion' => $tipo,
                'operador' => $operador,
                'valor_ref' => is_numeric($valorRef) ? (int)$valorRef : null,
                'valor_texto' => $valorTexto !== '' ? $valorTexto : null
            ];
        }

        return $limpio;
    }

    private function normalizar_tipo_condicion($tipo)
    {
        $tipo = strtoupper(trim($tipo));
        $alias = [
            'TOTAL_MINIMO' => 'TOTAL_OPERACION',
            'CANTIDAD_MINIMA' => 'CANTIDAD_ITEMS'
        ];

        return $alias[$tipo] ?? $tipo;
    }

    private function operador_valido_para_condicion($tipo, $operador)
    {
        $condicionesPorId = ['CLIENTE', 'ARTICULO', 'CATEGORIA', 'SUCURSAL'];

        if (in_array($tipo, $condicionesPorId, true)) {
            return in_array($operador, ['=', '!='], true);
        }

        return true;
    }

    private function normalizar_modo_competencia($modo)
    {
        $modo = strtoupper(trim($modo));
        $permitidas = ['NO_COMPITE', 'COMPITE_MISMO_ALCANCE', 'EXCLUSIVA'];

        return in_array($modo, $permitidas, true)
            ? $modo
            : 'COMPITE_MISMO_ALCANCE';
    }

    private function texto_modo_competencia($modo)
    {
        $textos = [
            'NO_COMPITE' => 'No compite',
            'COMPITE_MISMO_ALCANCE' => 'Compite mismo alcance',
            'EXCLUSIVA' => 'Exclusiva'
        ];

        return $textos[$modo] ?? 'Compite mismo alcance';
    }

    private function normalizar_descuentos($descuentos)
    {
        $tipos = ['PORCENTAJE', 'MONTO_FIJO', 'PRECIO_FIJO', 'NXM', 'GRATIS'];
        $aplica = ['TOTAL', 'LINEA', 'ARTICULO', 'CATEGORIA'];
        $limpio = [];

        foreach ($descuentos as $d) {
            $tipo = strtoupper(trim($d['tipo'] ?? ''));
            $aplicaA = strtoupper(trim($d['aplica_a'] ?? 'TOTAL'));
            $valor = (float)($d['valor'] ?? 0);
            $cantidadRequerida = (float)($d['cantidad_requerida'] ?? 0);
            $cantidadCobrada = (float)($d['cantidad_cobrada'] ?? 0);

            if (!in_array($tipo, $tipos, true) || !in_array($aplicaA, $aplica, true)) {
                continue;
            }

            $limpio[] = [
                'nombre' => trim($d['nombre'] ?? 'Descuento'),
                'tipo' => $tipo,
                'valor' => $tipo === 'GRATIS' ? 0 : $valor,
                'cantidad_requerida' => $tipo === 'NXM' ? $cantidadRequerida : null,
                'cantidad_cobrada' => $tipo === 'NXM' ? $cantidadCobrada : null,
                'aplica_a' => $aplicaA,
                'alcance_tipo' => trim($d['alcance_tipo'] ?? '') ?: null,
                'alcance_ref' => is_numeric($d['alcance_ref'] ?? null) ? (int)$d['alcance_ref'] : null
            ];
        }

        return $limpio;
    }

    private function validar_regla($regla, $condiciones, $descuentos)
    {
        if ($regla['nombre'] === '' || $regla['fecha_inicio'] === '' || $regla['fecha_fin'] === '') {
            return 'Debe completar nombre y vigencia';
        }

        if ($regla['fecha_inicio'] > $regla['fecha_fin']) {
            return 'La fecha de inicio no puede ser mayor a la fecha fin';
        }

        if (empty($condiciones)) {
            return 'Debe agregar al menos una condicion';
        }

        if (empty($descuentos)) {
            return 'Debe agregar al menos un descuento';
        }

        $alcancesDescuento = [];

        foreach ($descuentos as $d) {
            if ($d['nombre'] === '') {
                return 'Verifique el nombre de los descuentos';
            }

            $claveAlcance = ($d['aplica_a'] ?? '') . '|' . ($d['alcance_tipo'] ?? '') . '|' . ($d['alcance_ref'] ?? '');

            if (isset($alcancesDescuento[$claveAlcance])) {
                return 'Ya existe un descuento para el mismo alcance dentro de la regla';
            }

            $alcancesDescuento[$claveAlcance] = true;

            if ($d['tipo'] === 'NXM') {
                if ($d['cantidad_requerida'] <= 0 || $d['cantidad_cobrada'] <= 0 || $d['cantidad_cobrada'] >= $d['cantidad_requerida']) {
                    return 'En promociones N x M, la cantidad cobrada debe ser menor a la requerida';
                }
                continue;
            }

            if ($d['tipo'] === 'GRATIS') {
                continue;
            }

            if ($d['valor'] <= 0 || ($d['tipo'] === 'PORCENTAJE' && $d['valor'] > 100)) {
                return 'Verifique nombre, tipo y valor de los descuentos';
            }
        }

        return true;
    }

    private function respuesta($titulo, $texto, $tipo)
    {
        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => $titulo,
            'Texto' => $texto,
            'Tipo' => $tipo
        ]);
    }
}
