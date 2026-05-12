# Guia de programacion y validacion del proyecto Taller Reducto

Fecha de revision: 2026-05-10

## 1. Alcance de esta revision

Esta guia documenta como esta programado el proyecto, como se conectan sus partes y que reglas conviene seguir para seguir desarrollandolo.

Tambien incluye una validacion tecnica inicial del codigo fuente propio:

* Se reviso la estructura general del proyecto.
* Se revisaron los archivos principales de entrada, configuracion, modelo base, login, vistas, Ajax, controladores, modelos, permisos y reportes.
* Se ejecuto validacion de sintaxis PHP con `php -l`.
* Se excluyo `vendor/` de la revision sintactica porque contiene librerias externas instaladas por Composer.
* No se ejecuto una prueba funcional completa contra la base de datos desde navegador. Para validar comportamiento completo hay que probar cada flujo con XAMPP/MySQL y datos reales.

Resultado de sintaxis:

```txt
OK: 249 archivos PHP propios sin errores de sintaxis.
```

Conteo orientativo del proyecto:

* `ajax/`: 30 archivos PHP.
* `controladores/`: 30 archivos PHP.
* `modelos/`: 31 archivos PHP.
* `vistas/contenidos/`: 87 archivos PHP.
* `pdf/`: 21 archivos PHP principales.
* Funciones/metodos detectados en `controladores/` y `modelos/`: 528.

## 2. Arquitectura general

El proyecto usa una arquitectura MVC clasica adaptada a PHP procedural/orientado a objetos:

* `index.php`: punto de entrada principal.
* `config/`: constantes generales y conexion.
* `vistas/`: plantilla, pantallas, menus, scripts y estilos.
* `ajax/`: endpoints que reciben formularios o peticiones asincronas.
* `controladores/`: validan datos, permisos y coordinan operaciones.
* `modelos/`: ejecutan consultas SQL y transacciones contra MySQL.
* `pdf/`: genera reportes y documentos imprimibles.
* `database/`: respaldos y scripts SQL.
* `documentos/`: documentacion funcional y tecnica.
* `vendor/`: dependencias externas instaladas por Composer.

Flujo tipico:

```txt
Usuario
  -> Vista PHP en vistas/contenidos/
  -> JavaScript o formulario
  -> ajax/*Ajax.php
  -> controladores/*Controlador.php
  -> modelos/*Modelo.php
  -> MySQL
  -> respuesta HTML/JSON
  -> SweetAlert, tabla, redireccion o recarga
```

## 3. Punto de entrada y rutas

El archivo `index.php` carga:

```php
require_once "./config/APP.php";
require_once "./controladores/vistasControlador.php";
$plantilla = new vistasControlador();
$plantilla->obtenPlantilla_Controlador();
```

La plantilla principal esta en:

```txt
vistas/plantilla.php
```

Esa plantilla:

* Incluye estilos.
* Decide si mostrar `login`, `404` o una vista interna.
* Inicia sesion para pantallas protegidas.
* Verifica variables de sesion obligatorias:
  * `token_str`
  * `nick_str`
  * `id_str`
* Incluye menu lateral, navbar, contenido y scripts.

Las rutas se resuelven con `$_GET['vista']` en `vistasControlador` y `vistasModelo`.

El archivo `modelos/vistasModelo.php` contiene una lista blanca de vistas permitidas. Esto es importante porque evita incluir archivos arbitrarios por URL.

Para agregar una nueva pantalla hay que:

1. Crear el archivo en `vistas/contenidos/nombre-vista.php`.
2. Agregar `nombre` a la lista blanca de `modelos/vistasModelo.php`.
3. Agregar el enlace al menu si corresponde.
4. Crear controlador, modelo, Ajax y JS si la pantalla necesita operaciones.

## 4. Configuracion

Archivos principales:

```txt
config/APP.php
config/SERVER.php
```

`APP.php` define:

* `SERVERURL`
* `COMPANY`
* `MONEDA`
* zona horaria

`SERVER.php` define:

* servidor MySQL
* base de datos
* usuario y clave
* DSN PDO
* constantes para cifrado AES

Recomendacion importante:

Las credenciales de base de datos y claves de cifrado estan versionadas en codigo. Para desarrollo local funciona, pero para produccion conviene moverlas a variables de entorno o un archivo no versionado.

## 5. Modelo base

El archivo central es:

```txt
modelos/mainModel.php
```

Responsabilidades principales:

* Crear conexion PDO.
* Ejecutar consultas simples.
* Cifrar y descifrar identificadores.
* Limpiar cadenas.
* Validar formatos con expresiones regulares.
* Validar fechas.
* Generar paginador HTML.
* Cargar permisos en sesion.
* Validar permisos con `tienePermiso`.
* Construir filtros SQL.
* Registrar relaciones articulo-proveedor.

Metodos mas usados:

```php
mainModel::conectar()
mainModel::limpiar_string($cadena)
mainModel::verificarDatos($regex, $valor)
mainModel::verificarFecha($fecha)
mainModel::paginador(...)
mainModel::tienePermiso('clave.permiso')
mainModel::encryption($id)
mainModel::decryption($idCifrado)
```

Regla practica:

Todo dato recibido por `$_POST`, `$_GET` o `$_FILES` debe validarse en el controlador antes de enviarse al modelo. La validacion de JavaScript ayuda al usuario, pero la validacion real debe estar del lado del servidor.

## 6. Patron de Ajax

Los archivos en `ajax/` suelen hacer esto:

```php
$peticionAjax = true;
require_once "../config/APP.php";
require_once "../controladores/moduloControlador.php";
$instancia = new moduloControlador();

if (isset($_POST['campo_o_accion'])) {
    echo $instancia->metodo_controlador();
}
```

Algunos endpoints usan campos especificos:

```php
if (isset($_POST['cliente_doc_reg'])) { ... }
if (isset($_POST['cliente_id_del'])) { ... }
if (isset($_POST['cliente_id_up'])) { ... }
```

Otros usan una accion:

```php
if (isset($_POST['accion']) && $_POST['accion'] === 'guardar_recepcion') { ... }
```

Recomendacion:

Para nuevos modulos es mas claro usar `accion`, porque permite que un solo Ajax tenga varias operaciones sin depender de nombres de campos.

Ejemplo recomendado:

```php
if (!isset($_POST['accion'])) {
    exit(json_encode([
        "Alerta" => "simple",
        "Titulo" => "Solicitud invalida",
        "Texto" => "No se recibio una accion valida",
        "Tipo" => "error"
    ]));
}

switch ($_POST['accion']) {
    case 'crear':
        echo $controlador->crear_modulo_controlador();
        break;
    case 'anular':
        echo $controlador->anular_modulo_controlador();
        break;
}
```

## 7. Patron de controladores

Los controladores se ubican en:

```txt
controladores/
```

Cada controlador normalmente:

* Incluye el modelo correspondiente.
* Extiende la clase del modelo.
* Lee `$_POST` o parametros de URL.
* Limpia y valida datos.
* Verifica permisos.
* Arma arrays de datos.
* Llama al modelo.
* Devuelve HTML o JSON.

Ejemplo conceptual:

```php
class clienteControlador extends clienteModelo
{
    public function agregar_cliente_controlador()
    {
        $doc = mainModel::limpiar_string($_POST['cliente_doc_reg']);

        if ($doc === '') {
            return json_encode([...]);
        }

        $datos = [
            "doc_number" => $doc,
            ...
        ];

        $guardar = clienteModelo::agregar_cliente_modelo($datos);

        return json_encode([...]);
    }
}
```

Buenas practicas para controladores:

* No confiar en datos del navegador.
* Convertir IDs numericos a `(int)`.
* Validar permisos antes de modificar datos.
* Validar que el registro pertenezca a la sucursal del usuario.
* Devolver siempre respuestas consistentes.
* Evitar SQL directo en el controlador cuando la consulta pertenece al modelo.

## 8. Patron de modelos

Los modelos se ubican en:

```txt
modelos/
```

Cada modelo normalmente:

* Extiende `mainModel`.
* Usa `mainModel::conectar()`.
* Prepara consultas SQL.
* Ejecuta inserts, updates, deletes y selects.
* Devuelve `PDOStatement`, arrays o valores booleanos.

Ejemplo recomendado:

```php
$sql = mainModel::conectar()->prepare("
    SELECT *
    FROM clientes
    WHERE id_cliente = :id
");
$sql->bindValue(":id", $id, PDO::PARAM_INT);
$sql->execute();
return $sql;
```

Regla importante:

Preferir siempre parametros (`:id`, `?`) antes que concatenar valores dentro del SQL.

## 9. Vistas

Las vistas de contenido estan en:

```txt
vistas/contenidos/
```

El menu y componentes compartidos estan en:

```txt
vistas/inc/
```

La vista debe ocuparse principalmente de:

* Mostrar formularios.
* Mostrar tablas.
* Incluir modales.
* Llamar controladores para listar datos.
* Validar permisos visibles.

La vista no deberia contener reglas complejas de negocio. Si una regla decide si se puede registrar, anular, aprobar, descontar stock o cambiar estado, debe estar en controlador/modelo.

## 10. JavaScript del proyecto

JavaScript comun:

```txt
vistas/js/main.js
vistas/js/alertas.js
vistas/inc/*JS.php
```

`main.js` maneja principalmente:

* Submenus.
* Menu lateral.
* Scroll personalizado.
* Popovers.

`alertas.js` procesa formularios con clase `FormularioAjax` y respuestas tipo:

```json
{
  "Alerta": "simple",
  "Titulo": "Titulo",
  "Texto": "Mensaje",
  "Tipo": "success"
}
```

Patrones de respuesta comunes:

* `simple`: muestra alerta.
* `limpiar`: muestra alerta y limpia formulario.
* `recargar`: recarga pagina.
* `redireccionar`: redirige.
* `redireccionar_confirmado`: redirige despues de confirmar.

## 11. Login, sesiones, roles y permisos

Login:

```txt
controladores/loginControlador.php
modelos/loginModelo.php
ajax/loginAjax.php
```

Al iniciar sesion:

* Se valida usuario y clave.
* Se cifra la clave ingresada.
* Se consulta `usuarios`.
* Se inicia sesion `STR`.
* Se guardan datos del usuario.
* Se guardan roles y permisos.
* Se genera token de sesion.

Variables principales:

```php
$_SESSION['id_str']
$_SESSION['nombre_str']
$_SESSION['apellido_str']
$_SESSION['nick_str']
$_SESSION['nick_sucursal']
$_SESSION['roles']
$_SESSION['permisos']
$_SESSION['empresa_nombre']
$_SESSION['token_str']
```

Permisos:

```php
mainModel::tienePermiso('cliente.crear')
mainModel::tienePermiso('servicio.recepcion.ver')
mainModel::tienePermiso('compra.pedido.anular')
```

Tablas relacionadas:

* `usuarios`
* `roles`
* `usuario_rol`
* `permisos`
* `rol_permiso`

Regla de seguridad:

Ocultar botones en la vista no alcanza. Las acciones sensibles tambien deben validar permiso en el controlador.

## 12. Base de datos y transacciones

El sistema usa MySQL via PDO.

Para operaciones simples se usa `prepare` y `execute`.

Para movimientos que afectan varias tablas, se recomienda usar transacciones:

```php
$pdo = mainModel::conectar();
$pdo->beginTransaction();

try {
    // inserts, updates, movimientos de stock
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    throw $e;
}
```

Esto es especialmente importante en:

* Compras.
* Ordenes de compra.
* Presupuestos.
* Notas de credito/debito.
* Transferencias.
* Inventario.
* Registro de servicios.
* Anulaciones.

## 13. Paginacion y listados

El proyecto usa varios patrones:

* `SQL_CALC_FOUND_ROWS` + `FOUND_ROWS()`.
* `COUNT(*)` separado.
* Helper `mainModel::paginador`.
* Helper `mainModel::ejecutarPaginador`.

`SQL_CALC_FOUND_ROWS` aparece en varios archivos. Funciona, pero en MySQL moderno esta desaconsejado para rendimiento. Para nuevos desarrollos conviene usar:

```sql
SELECT ... LIMIT :inicio, :registros;
SELECT COUNT(*) FROM ... WHERE ...;
```

## 14. Generacion de PDF

Dependencias en Composer:

```json
{
  "mpdf/mpdf": "^8.2",
  "dompdf/dompdf": "^3.1"
}
```

Tambien existe FPDF en:

```txt
facturas/
```

Los PDFs estan en:

```txt
pdf/
pdf/plantillas/
```

Reglas para PDFs:

* Validar sesion.
* Validar permiso.
* Validar que el ID exista.
* Validar que el registro pertenezca a la sucursal correspondiente.
* Convertir IDs a enteros o descifrarlos con `mainModel::decryption`.
* Escapar datos que se imprimen en HTML cuando corresponda.

## 15. Carga de archivos

Se detecto carga de fotos de recepcion en `recepcionservicioControlador`.

Flujo actual resumido:

* Recibe `$_FILES['fotos_vehiculo']`.
* Genera nombre con `time()`, indice y nombre original.
* Mueve el archivo a `uploads/recepciones/`.
* Guarda ruta en `recepcion_fotos`.

Recomendaciones para mejorar:

* Validar extension permitida: `jpg`, `jpeg`, `png`, `webp`.
* Validar MIME real con `finfo_file`.
* Limitar tamano por archivo.
* Generar nombres sin usar directamente el nombre original.
* Verificar error de subida antes de `move_uploaded_file`.
* Rechazar archivos que no sean imagen.

Ejemplo de validacion conceptual:

```php
$permitidos = ['image/jpeg', 'image/png', 'image/webp'];
$mime = finfo_file($finfo, $_FILES['fotos_vehiculo']['tmp_name'][$i]);

if (!in_array($mime, $permitidos, true)) {
    // rechazar archivo
}
```

## 16. Seguridad: hallazgos y recomendaciones

### 16.1. Consultas SQL concatenadas

Hay muchas consultas con variables concatenadas dentro del SQL, por ejemplo:

```php
"SELECT ... WHERE campo = '$valor'"
```

Aunque se usa `limpiar_string`, lo correcto es usar `prepare` con parametros. Esto reduce riesgo de inyeccion SQL y errores con comillas.

Recomendado:

```php
$sql = mainModel::conectar()->prepare("
    SELECT *
    FROM clientes
    WHERE doc_number = :doc
");
$sql->bindValue(":doc", $doc);
$sql->execute();
```

### 16.2. Passwords cifradas reversiblemente

Las claves de usuario se manejan con `mainModel::encryption`, que usa AES. Eso permite comparar, pero no es la practica ideal para contrasenas.

Recomendado para nuevas versiones:

```php
password_hash($clave, PASSWORD_DEFAULT);
password_verify($claveIngresada, $hashGuardado);
```

Migrar esto requiere plan porque afectaria usuarios existentes.

### 16.3. Credenciales en codigo

`config/SERVER.php` contiene usuario, clave y secretos. Conviene moverlos a variables de entorno o a un archivo local ignorado por Git.

### 16.4. CSRF

Los formularios Ajax no muestran un token CSRF general. Hay token de sesion para logout, pero las acciones de creacion/anulacion deberian tener token anti-CSRF.

Recomendado:

* Generar token en sesion.
* Incluirlo como hidden input.
* Validarlo en Ajax/controlador.

### 16.5. Salida HTML

Algunos listados ya usan `htmlspecialchars`, pero otros imprimen valores directamente dentro de HTML. Para evitar XSS, todo dato proveniente de base de datos o usuario deberia escaparse al mostrarse.

Recomendado:

```php
htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')
```

### 16.6. Permisos inconsistentes

Hay buena presencia de `mainModel::tienePermiso`, pero las claves deben mantenerse consistentes. Ejemplo a revisar: aparecen permisos similares como `stock.movimientos.ver` y `stock.movimiento.ver`.

Recomendado:

* Mantener un catalogo unico de permisos.
* Usar siempre la misma clave en vista, controlador y base de datos.

## 17. Como agregar un modulo nuevo

Supongamos que se quiere agregar un modulo `marca`.

### 17.1. Crear modelo

Archivo:

```txt
modelos/marcaModelo.php
```

Responsabilidad:

* Insertar marca.
* Listar marcas.
* Actualizar marca.
* Eliminar o desactivar marca.

### 17.2. Crear controlador

Archivo:

```txt
controladores/marcaControlador.php
```

Responsabilidad:

* Leer POST.
* Validar campos.
* Validar permisos.
* Llamar al modelo.
* Responder JSON o HTML.

### 17.3. Crear Ajax

Archivo:

```txt
ajax/marcaAjax.php
```

Responsabilidad:

* Recibir `accion`.
* Instanciar controlador.
* Ejecutar metodo correcto.

### 17.4. Crear vista

Archivo:

```txt
vistas/contenidos/marca-nuevo-vista.php
```

Debe incluir:

* Validacion de permiso al inicio.
* Formulario.
* Tabla o listado.
* Formularios con clase `FormularioAjax` si se usa `alertas.js`.

### 17.5. Registrar ruta

Agregar `marca-nuevo` en la lista blanca de:

```txt
modelos/vistasModelo.php
```

### 17.6. Agregar menu

Editar:

```txt
vistas/inc/navLateral.php
```

### 17.7. Crear permisos

Agregar en base de datos permisos como:

```txt
marca.ver
marca.crear
marca.editar
marca.eliminar
```

### 17.8. Probar

Checklist:

* Usuario sin permiso no puede entrar.
* Usuario sin permiso no puede ejecutar Ajax.
* Campos obligatorios se validan.
* Duplicados se controlan.
* Se registra correctamente.
* Se lista correctamente.
* Se actualiza correctamente.
* Se elimina o desactiva correctamente.
* La paginacion funciona.

## 18. Como programar un formulario Ajax

Estructura tipica:

```html
<form class="FormularioAjax"
      action="<?php echo SERVERURL; ?>ajax/marcaAjax.php"
      method="POST"
      data-form="save"
      autocomplete="off">

    <input type="hidden" name="accion" value="crear">

    <input type="text" name="marca_nombre_reg" required>

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
```

Respuesta esperada:

```php
return json_encode([
    "Alerta" => "limpiar",
    "Titulo" => "Registro guardado",
    "Texto"  => "Los datos fueron registrados correctamente",
    "Tipo"   => "success"
]);
```

## 19. Como validar datos

Validaciones minimas:

* Campo requerido.
* Tipo de dato.
* Longitud.
* Formato.
* Duplicados.
* Existencia de claves foraneas.
* Estado permitido.
* Pertenencia a sucursal.
* Permiso del usuario.

Ejemplo:

```php
$nombre = mainModel::limpiar_string($_POST['nombre'] ?? '');

if ($nombre === '') {
    return json_encode([
        "Alerta" => "simple",
        "Titulo" => "Campo requerido",
        "Texto"  => "Debe ingresar el nombre",
        "Tipo"   => "warning"
    ]);
}

if (mainModel::verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,80}", $nombre)) {
    return json_encode([
        "Alerta" => "simple",
        "Titulo" => "Formato invalido",
        "Texto"  => "El nombre contiene caracteres no permitidos",
        "Tipo"   => "warning"
    ]);
}
```

## 20. Como validar permisos

En vista:

```php
if (!mainModel::tienePermiso('modulo.ver')) {
    echo '<div class="alert alert-danger">No tiene permisos</div>';
    return;
}
```

En controlador:

```php
session_start(['name' => 'STR']);

if (!mainModel::tienePermiso('modulo.crear')) {
    return json_encode([
        "Alerta" => "simple",
        "Titulo" => "Acceso no autorizado",
        "Texto" => "No posee permisos para esta accion",
        "Tipo" => "error"
    ]);
}
```

La validacion del controlador es obligatoria para acciones que modifican datos.

## 21. Como trabajar con sucursales

El sistema usa:

```php
$_SESSION['nick_sucursal']
```

Muchos movimientos deben limitarse a la sucursal del usuario.

Regla:

* Al listar, filtrar por sucursal.
* Al crear, guardar sucursal desde sesion, no desde POST.
* Al anular o modificar, verificar que el registro pertenece a la sucursal de sesion.

Ejemplo:

```php
$sql = $pdo->prepare("
    SELECT id
    FROM tabla
    WHERE id = :id
      AND id_sucursal = :sucursal
");
$sql->execute([
    ":id" => $id,
    ":sucursal" => $_SESSION['nick_sucursal']
]);
```

## 22. Convenciones del proyecto

Nombres frecuentes:

* Controlador: `moduloControlador.php`.
* Modelo: `moduloModelo.php`.
* Ajax: `moduloAjax.php`.
* Vista: `modulo-nuevo-vista.php`, `modulo-buscar-vista.php`, `modulo-lista-vista.php`.
* Metodo de controlador: `accion_modulo_controlador`.
* Metodo de modelo: `accion_modulo_modelo`.

Estados frecuentes:

* `1`: activo, pendiente o vigente.
* `0`: anulado o inactivo.
* `2`, `3`: procesado, en proceso, finalizado, segun modulo.

Cuando se programe un modulo nuevo, documentar explicitamente que significa cada estado.

## 23. Checklist antes de subir cambios

Ejecutar sintaxis:

```powershell
$files = Get-ChildItem -Recurse -File -Include *.php | Where-Object { $_.FullName -notmatch '\\vendor\\' }
foreach ($f in $files) { php -l $f.FullName }
```

Validar manualmente:

* Login correcto.
* Menu segun permisos.
* Alta de registro.
* Edicion de registro.
* Eliminacion/anulacion.
* Busqueda.
* Paginacion.
* Reporte PDF si aplica.
* Restriccion por sucursal.
* Usuario sin permiso.
* Datos invalidos.
* Duplicados.

Para movimientos:

* Verificar cabecera.
* Verificar detalle.
* Verificar stock.
* Verificar movimientos de stock.
* Verificar estado anterior y nuevo.
* Verificar rollback si algo falla.

## 24. Prioridades de mejora recomendadas

1. Reemplazar consultas concatenadas por consultas preparadas con parametros.
2. Agregar token CSRF general en formularios Ajax.
3. Migrar contrasenas a `password_hash` y `password_verify`.
4. Validar subidas de archivos por MIME, extension y tamano.
5. Unificar nombres de permisos.
6. Mover credenciales y secretos fuera del repositorio.
7. Escapar salida HTML de forma consistente.
8. Centralizar respuestas JSON para no repetir estructuras.
9. Reemplazar `SQL_CALC_FOUND_ROWS` por `COUNT(*)`.
10. Agregar pruebas funcionales por modulo critico.

## 25. Resumen para aprender a programar en este proyecto

Para programar correctamente en este sistema hay que pensar siempre en capas:

```txt
Vista: muestra formulario y botones.
JavaScript: mejora interaccion y envia Ajax.
Ajax: recibe la peticion y decide la accion.
Controlador: valida, verifica permisos y coordina.
Modelo: consulta o modifica la base de datos.
Base de datos: guarda el estado real del sistema.
```

La regla principal es:

> Lo visual ayuda, pero la seguridad y la logica real viven en controlador y modelo.

Si se mantiene ese orden, el proyecto se vuelve mas facil de entender, probar y ampliar.
