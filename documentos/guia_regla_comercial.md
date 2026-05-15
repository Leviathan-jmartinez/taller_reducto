# Guia del modulo de reglas comerciales

Esta guia explica como funciona el codigo de reglas comerciales en el proyecto y que sintaxis PHP, JavaScript y SQL se esta usando.

## 1. Que es una regla comercial

Una regla comercial define:

- Datos generales: nombre, descripcion, vigencia, sucursal, prioridad, modo de competencia y estado.
- Condiciones: cuando se debe aplicar la regla.
- Descuentos: que beneficio se aplica si se cumplen las condiciones.

Ejemplos:

- Si el total de la operacion es mayor o igual a 500000, aplicar 10% al total.
- Si el articulo es X, aplicar promocion "lleva 2 paga 1".
- Si el cliente es X, aplicar monto fijo de descuento.

## 2. Archivos principales

El modulo esta separado en varias capas:

- `vistas/contenidos/regla-comercial-nuevo-vista.php`: pantalla para registrar o editar reglas.
- `vistas/inc/reglaComercial.php`: JavaScript que agrega/quita condiciones y descuentos antes de enviar el formulario.
- `ajax/reglaComercialAjax.php`: recibe el formulario por AJAX y decide si guarda o edita.
- `controladores/reglaComercialControlador.php`: valida permisos, limpia datos, valida reglas y responde al navegador.
- `modelos/reglaComercialModelo.php`: ejecuta consultas SQL con PDO.
- `database/sql/reglas_comerciales.sql`: estructura de tablas y permisos.

## 3. Flujo general

1. El usuario entra a `regla-comercial-nuevo/` o `regla-comercial-nuevo/{id}/`.
2. La vista PHP arma el formulario.
3. El usuario agrega condiciones y descuentos.
4. JavaScript guarda esas condiciones/descuentos en arrays.
5. Antes de enviar, JavaScript convierte esos arrays a JSON.
6. El formulario se envia a `ajax/reglaComercialAjax.php`.
7. El AJAX llama al controlador.
8. El controlador normaliza y valida los datos.
9. El modelo guarda en la base de datos usando una transaccion.
10. El controlador devuelve JSON con el resultado.
11. El sistema muestra una alerta, limpia o recarga segun corresponda.

## 4. Vista PHP: formulario de nueva/editar regla

Archivo: `vistas/contenidos/regla-comercial-nuevo-vista.php`

La vista decide si esta registrando o editando:

```php
$vistaActual = $_GET['vista'] ?? '';
$pagina = explode('/', trim($vistaActual, '/'));
$idRegla = $pagina[1] ?? '';
$esEditar = $idRegla !== '';
```

Sintaxis usada:

- `$_GET`: lee parametros recibidos por URL.
- `??`: operador null coalescing. Si no existe el valor, usa un valor por defecto.
- `explode()`: divide un texto en partes.
- `trim()`: elimina caracteres al inicio y al final.
- `$esEditar`: booleano para saber si hay ID de regla.

Luego verifica permisos:

```php
if ($esEditar) {
    if (!mainModel::tienePermiso('servicio.regla_comercial.editar')) {
        echo '<div class="alert alert-danger">Acceso no autorizado</div>';
        return;
    }
}
```

Si se esta editando, carga la regla, sus condiciones y descuentos:

```php
$regla = $insRegla->datos_regla_controlador($idRegla);
$condiciones = $insRegla->condiciones_regla_controlador($idRegla);
$descuentos = $insRegla->descuentos_regla_controlador($idRegla);
```

El formulario envia datos por POST:

```php
<form class="form-neon FormularioAjax"
    action="<?= SERVERURL; ?>ajax/reglaComercialAjax.php"
    method="POST"
    data-modulo="reglas_comerciales"
    data-form="<?= $dataForm ?>">
```

Campos importantes ocultos:

```php
<input type="hidden" name="accion" value="<?= $accion ?>">
<input type="hidden" name="condiciones_json" id="condiciones_json">
<input type="hidden" name="descuentos_json" id="descuentos_json">
```

Estos campos son llenados por JavaScript antes de enviar.

## 5. JavaScript: condiciones y descuentos dinamicos

Archivo: `vistas/inc/reglaComercial.php`

Al iniciar, carga datos existentes si se esta editando:

```javascript
let condicionesRegla = Array.isArray(window.REGLA_CONDICIONES_INICIALES)
    ? window.REGLA_CONDICIONES_INICIALES
    : [];
```

Sintaxis usada:

- `let`: variable que puede cambiar.
- `const`: variable que no se reasigna.
- `Array.isArray()`: verifica si un valor es un array.
- Operador ternario `condicion ? valorSiTrue : valorSiFalse`.
- `window`: objeto global del navegador.

### Agregar una condicion

```javascript
function agregarCondicionRegla() {
    const condicion = {
        tipo_condicion: document.getElementById('cond_tipo').value,
        operador: document.getElementById('cond_operador').value,
        valor_ref: document.getElementById('cond_valor_ref').value || null,
        valor_texto: document.getElementById('cond_valor_texto').value.trim()
    };

    condicionesRegla.push(condicion);
    renderCondicionesRegla();
}
```

Que hace:

- Lee valores del formulario con `document.getElementById()`.
- Crea un objeto JavaScript.
- Lo agrega al array con `.push()`.
- Vuelve a dibujar la tabla.

### Dibujar la tabla

```javascript
tbody.innerHTML += `
    <tr class="text-center">
        <td>${escapeHtml(c.tipo_condicion || '')}</td>
        <td>${escapeHtml(c.operador || '=')}</td>
    </tr>`;
```

Sintaxis usada:

- Template literals con backticks: permiten texto en varias lineas.
- `${variable}`: inserta valores dentro del texto.
- `innerHTML`: cambia el HTML interno de un elemento.
- `escapeHtml()`: evita que un texto ingresado por el usuario rompa el HTML o inyecte codigo.

### Preparar el envio

```javascript
function prepararEnvioRegla() {
    document.getElementById('condiciones_json').value = JSON.stringify(condicionesRegla);
    document.getElementById('descuentos_json').value = JSON.stringify(descuentosRegla);
}
```

`JSON.stringify()` convierte arrays/objetos JavaScript en texto JSON para enviarlos por POST.

Ejemplo:

```json
[
  {
    "tipo_condicion": "TOTAL_OPERACION",
    "operador": ">=",
    "valor_ref": null,
    "valor_texto": "500000"
  }
]
```

## 6. AJAX: entrada del formulario

Archivo: `ajax/reglaComercialAjax.php`

```php
$peticionAjax = true;
require_once "../config/SERVER.php";
require_once "../controladores/reglaComercialControlador.php";

$regla = new reglaComercialControlador();
```

Sintaxis usada:

- `require_once`: importa un archivo una sola vez.
- `new`: crea un objeto.

Luego decide que metodo ejecutar:

```php
if ($_POST['accion'] === 'guardar_regla') {
    echo $regla->guardar_regla_controlador();
    exit;
}
```

`$_POST['accion']` viene desde el input oculto del formulario.

## 7. Controlador: permisos, normalizacion y validacion

Archivo: `controladores/reglaComercialControlador.php`

La clase extiende al modelo:

```php
class reglaComercialControlador extends reglaComercialModelo
```

Esto significa que el controlador hereda comportamiento relacionado al modelo.

### Guardar regla

```php
public function guardar_regla_controlador()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start(['name' => 'STR']);
    }

    if (!mainModel::tienePermiso('servicio.regla_comercial.crear')) {
        return $this->respuesta('Acceso no autorizado', 'No tiene permiso para crear reglas comerciales', 'error');
    }
}
```

Sintaxis usada:

- `public function`: metodo publico de una clase.
- `$this`: referencia al objeto actual.
- `::`: llama metodos estaticos, por ejemplo `mainModel::tienePermiso()`.
- `!==`: compara valor y tipo.
- `return`: termina la funcion y devuelve un valor.

### Normalizar datos

```php
$condiciones = json_decode($_POST['condiciones_json'] ?? '[]', true);
$descuentos = json_decode($_POST['descuentos_json'] ?? '[]', true);
```

`json_decode(..., true)` convierte JSON a arrays asociativos de PHP.

Luego arma una estructura limpia:

```php
return [
    'regla' => [
        'nombre' => trim($_POST['nombre'] ?? ''),
        'prioridad' => (int)($_POST['prioridad'] ?? 0),
        'estado' => isset($_POST['estado']) ? 1 : 0,
        'usuario' => $_SESSION['id_str']
    ],
    'condiciones' => is_array($condiciones) ? $this->normalizar_condiciones($condiciones) : [],
    'descuentos' => is_array($descuentos) ? $this->normalizar_descuentos($descuentos) : []
];
```

Sintaxis usada:

- Arrays asociativos: `['clave' => 'valor']`.
- Casting `(int)`: convierte a entero.
- `isset()`: verifica si existe una variable o indice.
- `is_array()`: verifica si es array.

### Validaciones importantes

La funcion `validar_regla()` controla:

- Nombre y fechas obligatorias.
- Fecha inicio no mayor a fecha fin.
- Al menos una condicion.
- Al menos un descuento.
- Descuentos sin alcance duplicado.
- En N x M, la cantidad cobrada debe ser menor a la requerida.
- Porcentaje no puede superar 100.

Ejemplo:

```php
if ($regla['fecha_inicio'] > $regla['fecha_fin']) {
    return 'La fecha de inicio no puede ser mayor a la fecha fin';
}
```

Como las fechas vienen en formato `YYYY-MM-DD`, se pueden comparar como texto.

## 8. Modelo: guardar en base de datos

Archivo: `modelos/reglaComercialModelo.php`

El modelo usa PDO:

```php
$pdo = mainModel::conectar();
$pdo->beginTransaction();
```

Una transaccion asegura que se guarde todo completo o nada.

### Insert principal

```php
$sql = $pdo->prepare("
    INSERT INTO regla_comercial
    (nombre, descripcion, fecha_inicio, fecha_fin, id_sucursal, prioridad,
     modo_competencia, estado, id_usuario_crea, fecha_creacion)
    VALUES
    (:nombre, :descripcion, :fecha_inicio, :fecha_fin, :sucursal, :prioridad,
     :modo_competencia, :estado, :usuario, NOW())
");
```

Sintaxis usada:

- `prepare()`: prepara una consulta SQL segura.
- `:nombre`: parametro nombrado.
- `execute([...])`: envia los valores.
- `NOW()`: fecha/hora actual en MySQL.

Luego obtiene el ID generado:

```php
$idRegla = (int)$pdo->lastInsertId();
```

Despues guarda condiciones y descuentos con ese ID.

### Editar regla

Al editar, primero actualiza la regla principal:

```php
UPDATE regla_comercial SET
    nombre = :nombre,
    descripcion = :descripcion,
    fecha_actualizacion = NOW()
WHERE id_regla = :id
```

Luego borra condiciones/descuentos anteriores y vuelve a insertar los nuevos:

```php
DELETE FROM regla_comercial_condicion WHERE id_regla = :id
DELETE FROM regla_comercial_descuento WHERE id_regla = :id
```

Este enfoque simplifica la edicion porque el formulario envia la version completa actualizada.

## 9. Base de datos

Archivo: `database/sql/reglas_comerciales.sql`

Tablas principales:

### `regla_comercial`

Guarda la cabecera:

- `id_regla`
- `nombre`
- `descripcion`
- `fecha_inicio`
- `fecha_fin`
- `id_sucursal`
- `prioridad`
- `modo_competencia`
- `estado`
- usuarios y fechas de auditoria

### `regla_comercial_condicion`

Guarda las condiciones de cada regla:

- `id_regla`
- `tipo_condicion`
- `operador`
- `valor_ref`
- `valor_texto`

### `regla_comercial_descuento`

Guarda los descuentos:

- `id_regla`
- `nombre`
- `tipo`
- `valor`
- `cantidad_requerida`
- `cantidad_cobrada`
- `aplica_a`
- `alcance_tipo`
- `alcance_ref`

Las tablas de condiciones y descuentos tienen:

```sql
FOREIGN KEY (id_regla) REFERENCES regla_comercial(id_regla)
ON DELETE CASCADE
```

Eso significa que si se elimina una regla, se eliminan automaticamente sus condiciones y descuentos.

## 10. Tipos de condiciones

Permitidos en el controlador:

- `CLIENTE`
- `ARTICULO`
- `CATEGORIA`
- `TOTAL_OPERACION`
- `CANTIDAD_ITEMS`
- `SUCURSAL`

Operadores permitidos:

- `=`
- `!=`
- `>=`
- `<=`
- `>`
- `<`

Pero para condiciones por ID, como cliente, articulo, categoria y sucursal, solo se permite:

- `=`
- `!=`

Esto evita reglas raras como "id_cliente mayor que 5".

## 11. Tipos de descuentos

Permitidos en el controlador:

- `PORCENTAJE`: descuento porcentual.
- `MONTO_FIJO`: resta un monto.
- `PRECIO_FIJO`: fija un precio.
- `NXM`: lleva N paga M.
- `GRATIS`: valor cero, regalo/promocion.

Campos de aplicacion:

- `TOTAL`
- `LINEA`
- `ARTICULO`
- `CATEGORIA`

## 12. Respuesta JSON al navegador

El controlador responde asi:

```php
return json_encode([
    'Alerta' => 'limpiar',
    'Titulo' => 'Regla registrada',
    'Texto' => 'La regla comercial se guardo correctamente',
    'Tipo' => 'success'
]);
```

Sintaxis usada:

- `json_encode()`: convierte array PHP a JSON.
- El frontend general del sistema interpreta `Alerta`, `Titulo`, `Texto` y `Tipo`.

Valores usados:

- `simple`: muestra alerta simple.
- `limpiar`: muestra alerta y limpia formulario.
- `recargar`: muestra alerta y recarga.

## 13. Listado de reglas

Archivo: `controladores/reglaComercialControlador.php`

La funcion `listar_reglas_controlador()`:

1. Lee filtros de `$_GET`.
2. Llama a `listar_reglas_modelo()`.
3. Construye una tabla HTML.
4. Agrega paginacion.

Ejemplo de seguridad:

```php
htmlspecialchars($r['nombre'], ENT_QUOTES, 'UTF-8')
```

`htmlspecialchars()` evita que contenido de la base de datos se interprete como HTML malicioso.

## 14. Resumen mental rapido

La idea principal es:

```text
Formulario PHP
    -> JavaScript arma condiciones/descuentos
    -> JSON oculto en inputs
    -> AJAX PHP
    -> Controlador valida
    -> Modelo guarda con PDO
    -> MySQL
    -> Respuesta JSON
```

Para entender o modificar este modulo, conviene seguir siempre ese recorrido.

