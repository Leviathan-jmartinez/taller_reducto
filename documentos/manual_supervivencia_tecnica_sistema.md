# Manual de supervivencia tecnica del sistema Taller Reducto

Este documento esta pensado para que puedas defenderte, modificar y explicar el sistema sin depender de una IA. No es un manual de usuario final. Es un manual tecnico-practico: donde esta cada cosa, que significa cada archivo, que tocar cuando pidan un cambio, como validar, como seguir el flujo de una operacion y como no romper el proyecto.

El sistema esta hecho en PHP tradicional con una estructura parecida a MVC:

- `vistas/`: pantallas HTML/PHP, CSS, JS e includes.
- `controladores/`: reglas de negocio, validaciones, permisos, armado de tablas/listados.
- `modelos/`: consultas SQL e interaccion directa con la base de datos.
- `ajax/`: entradas POST para formularios AJAX.
- `config/`: constantes de conexion y configuracion general.
- `pdf/` y `facturas/`: generacion de documentos PDF.
- `documentos/`: documentacion tecnica y funcional.
- `tests/`: verificaciones por consola.
- `_legacy/`: codigo retirado que no forma parte activa del sistema.

La idea central: cuando un usuario hace click en una pantalla, la ruta pasa por `index.php`, se resuelve una vista, esa vista puede incluir un formulario, el formulario envia por AJAX, el AJAX llama a un controlador, el controlador valida y llama al modelo, y el modelo ejecuta SQL.

---

## 1. Antes de tocar codigo

Siempre que te pidan un cambio, primero identifica:

1. Que modulo es.
2. Si afecta vista, controlador, modelo, AJAX, permisos, menu o base de datos.
3. Si el cambio es solo visual o tambien cambia reglas de negocio.
4. Si hay que agregar/modificar una columna en base de datos.
5. Si afecta reportes o PDF.
6. Si hay permisos involucrados.

Ejemplo:

- "Agregar columna estado en clientes": vista/listado y controlador.
- "No dejar eliminar proveedor si tiene compras": modelo y controlador de proveedor.
- "Agregar un campo RUC a proveedor": vista, controlador, modelo y base de datos.
- "Que aparezca en menu solo si tiene permiso": `vistas/inc/navLateral.php`.
- "Que se pueda buscar por fecha": vista, `ajax/buscadorAjax.php` y controlador/listado.

Regla de oro: no cambies todo a la vez. Ubica el flujo, toca lo minimo, valida sintaxis y prueba el caso.

---

## 2. Archivos de entrada del sistema

### `index.php`

Es la entrada principal del sistema.

Hace esto:

```php
require_once "./config/APP.php";
require_once "./controladores/vistasControlador.php";
$plantilla = new vistasControlador();
$plantilla->obtenPlantilla_Controlador();
```

Traduccion: carga configuracion, carga el controlador de vistas y abre la plantilla general.

Normalmente no se modifica salvo que cambie completamente la forma de cargar la aplicacion.

### `.htaccess`

Convierte URLs amigables en `index.php?vista=...`.

Ejemplo:

```text
http://localhost/taller_reducto/cliente-nuevo/
```

Internamente llega como:

```text
index.php?vista=cliente-nuevo/
```

Si una ruta da 404 aunque el archivo exista, revisar:

- Que la vista este en `modelos/vistasModelo.php`.
- Que exista el archivo en `vistas/contenidos/`.
- Que `.htaccess` este activo.
- Que Apache tenga `mod_rewrite` habilitado.

---

## 3. Configuracion

### `config/APP.php`

Contiene datos generales:

```php
const SERVERURL = "http://localhost/taller_reducto/";
const COMPANY = "SISTEMA";
const MONEDA = "Gs";
date_default_timezone_set("America/Asuncion");
```

Si cambia la carpeta del proyecto o dominio, se cambia `SERVERURL`.

Ejemplo:

```php
const SERVERURL = "http://localhost/mi_sistema/";
```

### `config/SERVER.php`

Contiene la conexion a base de datos:

```php
const SERVER = "localhost";
const DB = "bd_reduc";
const USER = "root";
const PASS = "8520123";
const SGBD = "mysql:host=" . SERVER . ";dbname=" . DB;
```

Tambien tiene claves para encriptar IDs:

```php
const METHOD = "AES-256-CBC";
const SECRET_KEY = '$TALLER@2024';
const SECRET_IV = '852012';
```

No cambies `SECRET_KEY` ni `SECRET_IV` si ya hay IDs encriptados o enlaces generados, porque puede afectar desencriptacion.

---

## 4. Como se cargan las vistas

### `controladores/vistasControlador.php`

Tiene dos responsabilidades:

- Cargar la plantilla (`vistas/plantilla.php`).
- Resolver que vista se debe incluir.

Metodo importante:

```php
public function obtenVista_Controlador()
```

Si existe `$_GET['vista']`, toma el primer segmento de la URL y pregunta al modelo si esa vista esta permitida.

### `modelos/vistasModelo.php`

Contiene la lista blanca de vistas permitidas:

```php
$lista_blanca = ["home", "articulo-nuevo", "cliente-nuevo", ...];
```

Si agregas una vista nueva, debes agregar su nombre aca.

Ejemplo: si creas:

```text
vistas/contenidos/marca-nuevo-vista.php
```

Tenes que agregar:

```php
"marca-nuevo"
```

Si no lo agregas, la ruta dara 404.

### `vistas/plantilla.php`

Es el layout general.

Hace:

- Carga `<head>` y estilos.
- Decide si muestra login o sistema interno.
- Inicia sesion.
- Verifica que exista usuario logueado.
- Incluye nav lateral.
- Incluye nav superior.
- Incluye la vista actual.
- Incluye scripts.

Puntos clave:

```php
include "./vistas/inc/navLateral.php";
include "./vistas/inc/navBar.php";
include $vistas;
```

Si una pantalla se ve sin menu o sin estilos, revisar esta plantilla.

---

## 5. Estructura de un modulo normal

Un modulo completo suele tener:

```text
vistas/contenidos/cliente-nuevo-vista.php
ajax/clienteAjax.php
controladores/clienteControlador.php
modelos/clienteModelo.php
```

Patron de nombres:

- Vista: `modulo-nuevo-vista.php`, `modulo-actualizar-vista.php`
- AJAX: `moduloAjax.php`
- Controlador: `moduloControlador.php`
- Modelo: `moduloModelo.php`
- Clase controlador: `moduloControlador`
- Clase modelo: `moduloModelo`

Ejemplo real:

```php
class clienteControlador extends clienteModelo
```

Eso significa que el controlador puede llamar metodos protegidos del modelo.

---

## 6. Flujo completo de guardado

Ejemplo: guardar cliente.

### 1. Vista

Archivo:

```text
vistas/contenidos/cliente-nuevo-vista.php
```

Contiene un formulario:

```php
<form class="form-neon FormularioAjax app-form"
    action="<?php echo SERVERURL; ?>ajax/clienteAjax.php"
    method="POST"
    data-form="save"
    autocomplete="off">
```

Puntos importantes:

- `FormularioAjax`: hace que `vistas/js/alertas.js` capture el submit.
- `action`: indica a que archivo AJAX se envia.
- `method="POST"`: envia datos por POST.
- `data-form="save"`: indica que es registro nuevo.

Los `name` de los inputs son fundamentales. El controlador lee esos nombres.

Ejemplo:

```php
name="cliente_doc_reg"
name="cliente_nombre_reg"
```

### 2. JavaScript AJAX

Archivo:

```text
vistas/js/alertas.js
```

Busca todos los formularios:

```js
const formulario_ajax = document.querySelectorAll(".FormularioAjax");
```

Cuando se envia, hace:

- Evita submit normal.
- Arma `FormData`.
- Muestra confirmacion SweetAlert.
- Envia con `fetch`.
- Espera JSON.
- Ejecuta `alertasAjax`.

El controlador debe devolver JSON con esta forma:

```php
[
    "Alerta" => "limpiar",
    "Titulo" => "Cliente Registrado",
    "Texto" => "Los datos fueron registrados correctamente",
    "Tipo" => "success"
]
```

Tipos comunes de `Alerta`:

- `simple`: muestra mensaje.
- `recargar`: muestra mensaje y recarga la pagina.
- `limpiar`: muestra mensaje y limpia formulario.
- `redireccionar`: cambia a otra URL directamente.
- `redireccionar_confirmado`: muestra mensaje y luego redirige.
- `confirmar`: pide confirmacion extra.

### 3. AJAX PHP

Archivo:

```text
ajax/clienteAjax.php
```

Ejemplo:

```php
$peticionAjax = true;
require_once "../config/APP.php";

if (isset($_POST['cliente_doc_reg']) || isset($_POST['cliente_id_del']) || isset($_POST['cliente_id_up'])) {
    require_once "../controladores/clienteControlador.php";
    $inst_cliente = new clienteControlador();

    if (isset($_POST['cliente_doc_reg']) && isset($_POST['cliente_nombre_reg'])) {
        echo $inst_cliente->agregar_cliente_controlador();
    }
}
```

Este archivo decide que metodo del controlador ejecutar segun los campos POST.

Si agregas una accion nueva, normalmente agregas un `if` aca.

### 4. Controlador

Archivo:

```text
controladores/clienteControlador.php
```

Metodo:

```php
public function agregar_cliente_controlador()
```

Aqui se hace:

- Validacion de permiso.
- Limpieza de datos.
- Validaciones obligatorias.
- Validaciones de formato.
- Validacion de duplicados.
- Armar arreglo `$datos_cliente`.
- Llamar al modelo.
- Devolver JSON.

### 5. Modelo

Archivo:

```text
modelos/clienteModelo.php
```

Metodo:

```php
protected static function agregar_cliente_modelo($datos)
```

Aqui se hace SQL:

```php
INSERT INTO clientes (...)
VALUES (...)
```

Regla: el modelo no deberia mostrar mensajes. Solo consulta base de datos y devuelve resultado.

---

## 7. Donde se valida cada cosa

### Validacion visual HTML

En la vista:

```php
pattern="[0-9]{5,10}"
maxlength="10"
```

Sirve para ayudar al usuario, pero no alcanza para seguridad.

### Validacion real

En el controlador:

```php
if (mainModel::verificarDatos("[0-9]{5,10}", $doc)) {
    // error
}
```

La validacion importante siempre debe estar en controlador.

### Validacion SQL / integridad

En modelo o base de datos:

- `UNIQUE`
- `FOREIGN KEY`
- consultas para verificar referencias

Ejemplo de duplicado:

```php
$check_doc = mainModel::ejecutar_consulta_simple("SELECT doc_number FROM clientes WHERE doc_number='$doc'");
```

### Permisos

En vista, controlador y menu.

- Vista: evita mostrar pantalla/formulario si no corresponde.
- Controlador: evita que alguien mande POST manualmente.
- Menu: evita mostrar accesos que no puede usar.

---

## 8. Permisos

Los permisos se cargan al iniciar sesion.

Archivo:

```text
controladores/loginControlador.php
```

En login exitoso:

```php
$_SESSION['permisos'] = loginModelo::obtener_permisos_usuario($row['id_usuario']);
```

Luego se valida asi:

```php
mainModel::tienePermiso('cliente.ver')
```

### Convencion de permisos

Los referenciales usan:

- `modulo.ver`
- `modulo.crear`
- `modulo.editar`
- `modulo.eliminar`

Ejemplos:

```text
cliente.ver
cliente.crear
cliente.editar
cliente.eliminar
```

Usuarios y roles:

```text
usuarios.ver
usuarios.crear
usuarios.editar
usuarios.eliminar
roles.ver
roles.crear
roles.editar
roles.eliminar
permisos.asignar_permisos
```

Compras y servicios tienen permisos mas especificos:

```text
compra.pedido.ver
compra.pedido.crear
compra.oc.ver
compra.factura.ver
servicio.recepcion.ver
servicio.diagnostico.ver
servicio.ot.ver
```

### Como agregar un permiso nuevo

1. Insertar el permiso en la tabla `permisos`.
2. Asignarlo a un rol desde pantalla de roles/permisos o con SQL en `rol_permiso`.
3. Usarlo en el menu, vista y controlador.
4. Cerrar sesion y volver a entrar para recargar permisos.

Ejemplo:

```php
if (!mainModel::tienePermiso('cliente.crear')) {
    echo json_encode([...]);
    exit();
}
```

---

## 9. Menu lateral

Archivo:

```text
vistas/inc/navLateral.php
```

El menu se arma con el arreglo:

```php
$menuLateral = [
    [
        'titulo' => 'Mantenimiento',
        'icono' => 'fas fa-cog',
        'items' => [...]
    ]
];
```

Cada item puede tener:

- `titulo`: texto del menu.
- `icono`: clase FontAwesome.
- `href`: ruta.
- `vista`: vista exacta activa.
- `vistas`: lista de vistas que activan el menu.
- `permiso`: permiso requerido.
- `items`: submenus.

Ejemplo:

```php
[
    'titulo' => 'Clientes',
    'icono' => 'fas fa-users',
    'href' => 'cliente-nuevo/',
    'vistas' => ['cliente-nuevo', 'cliente-lista', 'cliente-actualizar', 'cliente-buscar'],
    'permiso' => 'cliente.ver'
]
```

### Regla actual importante

Los referenciales estan unificados. El menu apunta a:

```text
cliente-nuevo/
proveedor-nuevo/
articulo-nuevo/
sucursal-nuevo/
vehiculo-nuevo/
empleado-nuevo/
usuario-nuevo/
rol-nuevo/
```

Aunque diga `nuevo`, esa vista tambien contiene buscador y listado.

Con solo `cliente.ver`:

- Debe ver el menu.
- Debe entrar a `cliente-nuevo/`.
- Debe ver buscador/listado.
- No debe ver formulario de alta si no tiene `cliente.crear`.

### Padres del menu

El menu tiene padres como `Mantenimiento`, `Compras`, `Servicios`. La funcion `nav_item_visible` permite mostrar un padre si tiene al menos un hijo visible.

Esto evita que un usuario con `cliente.ver` no vea el menu solo por no tener `mantenimiento.ver`.

---

## 10. Vistas unificadas de referenciales

Referenciales principales:

- Articulos
- Clientes
- Proveedores
- Sucursales
- Vehiculos
- Empleados
- Usuarios
- Roles

Las vistas unificadas siguen este patron:

```php
$vistaActual = $vistaPartes[0] ?? 'cliente-nuevo';
$id = ($vistaActual === 'cliente-actualizar') ? ($vistaPartes[1] ?? null) : null;
$permisoNecesario = ($vistaActual === 'cliente-actualizar') ? 'cliente.editar' : 'cliente.ver';
$puedeCrear = mainModel::tienePermiso('cliente.crear');
```

Luego:

```php
if (!mainModel::tienePermiso($permisoNecesario)) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
```

Y el formulario se muestra solo si:

```php
if ($editando || $puedeCrear) {
    // formulario
}
```

Esto separa dos cosas:

- `ver`: permiso para entrar y listar.
- `crear`: permiso para ver formulario de alta.
- `editar`: permiso para cargar formulario de actualizacion.

### Cuando te pidan "ocultar formulario si solo puede ver"

Revisar la vista `*-nuevo-vista.php` y aplicar ese patron.

### Cuando te pidan "el boton editar no debe aparecer"

Revisar el controlador que arma la tabla:

```php
if (mainModel::tienePermiso('cliente.editar')) {
    $tabla .= '<th>EDITAR</th>';
}
```

Y dentro de cada fila:

```php
if (mainModel::tienePermiso('cliente.editar')) {
    // boton editar
}
```

---

## 11. Buscador unificado

Archivo:

```text
ajax/buscadorAjax.php
```

Tiene un mapa:

```php
$data_url = [
    "usuario" => "usuario-nuevo",
    "sucursal" => "sucursal-nuevo",
    "empleado" => "empleado-nuevo",
    "vehiculo" => "vehiculo-nuevo",
    "proveedor" => "proveedor-nuevo",
    "cliente" => "cliente-nuevo",
    "articulo" => "articulo-nuevo",
    "roles" => "rol-nuevo",
];
```

Esto significa que al buscar clientes, vuelve a:

```text
cliente-nuevo/
```

No a `cliente-buscar/`.

### Como funciona

La vista envia:

```php
<input type="hidden" name="modulo" value="cliente">
<input type="text" name="busqueda_inicial">
```

`buscadorAjax.php` guarda en sesion algo como:

```php
$_SESSION['busqueda_cliente']
```

Y redirecciona a la vista unificada.

La vista luego lee:

```php
$busqueda = $_SESSION['busqueda_cliente'] ?? "";
```

Y pasa esa busqueda al listado:

```php
echo $ins_cliente->listar_cliente_controlador($pag_actual, 10, $pagina[0], $busqueda);
```

### Si una busqueda no funciona

Revisar:

1. Que el `name="modulo"` coincida con `$data_url`.
2. Que `buscadorAjax.php` use la sesion correcta.
3. Que la vista lea la misma sesion.
4. Que el controlador use `$busqueda` para armar filtros.
5. Que el SQL del modelo permita buscar por esa columna.

---

## 12. Listados y tablas

Los listados suelen estar en el controlador, no en la vista.

Ejemplo:

```text
controladores/clienteControlador.php
```

Metodo:

```php
public function listar_cliente_controlador($pagina, $registros, $url, $busqueda)
```

Hace:

- Limpia parametros.
- Calcula paginacion.
- Arma filtros SQL.
- Llama al modelo.
- Arma tabla HTML.
- Muestra botones segun permisos.
- Devuelve el HTML.

### Agregar una columna al listado

Ejemplo: agregar estado.

1. Verificar que el modelo traiga el campo. Si usa `SELECT *`, ya viene.
2. Agregar `<th>ESTADO</th>`.
3. Dentro del `foreach`, calcular texto/badge.
4. Agregar `<td>...</td>`.
5. Ajustar `colspan` si hay fila vacia.

Ejemplo:

```php
$estado = ((int)$rows['estado_cliente'] === 1)
    ? '<span class="badge badge-success">Activo</span>'
    : '<span class="badge badge-danger">Inactivo</span>';
```

Luego:

```php
<td>' . $estado . '</td>
```

### Botones segun permisos

Ejemplo:

```php
if (mainModel::tienePermiso('cliente.editar')) {
    // boton editar
}
```

No muestres botones que el usuario no puede usar.

Pero tambien valida en controlador, porque alguien puede mandar POST manual.

---

## 13. Crear un campo nuevo en un referencial

Supongamos que te piden agregar `telefono_secundario` a clientes.

### Paso 1: Base de datos

Agregar columna:

```sql
ALTER TABLE clientes ADD telefono_secundario VARCHAR(20) NULL;
```

### Paso 2: Vista

Archivo:

```text
vistas/contenidos/cliente-nuevo-vista.php
```

Agregar input:

```php
<label for="cliente_telefono_secundario">Telefono secundario</label>
<input type="text"
    class="form-control"
    id="cliente_telefono_secundario"
    name="<?php echo $editando ? 'cliente_telefono_secundario_up' : 'cliente_telefono_secundario_reg'; ?>"
    value="<?php echo $editando ? $campos['telefono_secundario'] : ''; ?>">
```

### Paso 3: Controlador agregar

Archivo:

```text
controladores/clienteControlador.php
```

En `agregar_cliente_controlador()`:

```php
$telefono_secundario = mainModel::limpiar_string($_POST['cliente_telefono_secundario_reg'] ?? "");
```

Validar si hace falta:

```php
if ($telefono_secundario != "" && mainModel::verificarDatos("[0-9()+ -]{6,20}", $telefono_secundario)) {
    // error
}
```

Agregar al array:

```php
"telefono_secundario" => $telefono_secundario,
```

### Paso 4: Controlador actualizar

En `actualizar_cliente_controlador()`:

```php
$telefono_secundario = mainModel::limpiar_string($_POST['cliente_telefono_secundario_up'] ?? "");
```

Agregar al array de datos.

### Paso 5: Modelo agregar

Archivo:

```text
modelos/clienteModelo.php
```

En el `INSERT`, agregar la columna:

```sql
telefono_secundario
```

En valores:

```sql
:telefono_secundario
```

Y bind:

```php
$sql->bindValue(":telefono_secundario", $datos['telefono_secundario']);
```

### Paso 6: Modelo actualizar

En el `UPDATE`:

```sql
telefono_secundario = :telefono_secundario
```

Y bind.

### Paso 7: Listado

Si debe verse en tabla, modificar `listar_cliente_controlador`.

### Paso 8: Validar

Ejecutar:

```powershell
php -l vistas\contenidos\cliente-nuevo-vista.php
php -l controladores\clienteControlador.php
php -l modelos\clienteModelo.php
```

Probar:

- Crear cliente.
- Editar cliente.
- Listar cliente.
- Buscar cliente.

---

## 14. Agregar un modulo nuevo

Ejemplo: modulo `marca`.

### Archivos necesarios

```text
vistas/contenidos/marca-nuevo-vista.php
vistas/contenidos/marca-actualizar-vista.php
ajax/marcaAjax.php
controladores/marcaControlador.php
modelos/marcaModelo.php
```

### Agregar a lista blanca

Archivo:

```text
modelos/vistasModelo.php
```

Agregar:

```php
"marca-nuevo", "marca-actualizar"
```

### Agregar al menu

Archivo:

```text
vistas/inc/navLateral.php
```

Agregar item:

```php
[
    'titulo' => 'Marcas',
    'icono' => 'fas fa-tags',
    'href' => 'marca-nuevo/',
    'vistas' => ['marca-nuevo', 'marca-actualizar'],
    'permiso' => 'marca.ver'
]
```

### Agregar al buscador

Archivo:

```text
ajax/buscadorAjax.php
```

Agregar:

```php
"marca" => "marca-nuevo",
```

Y manejar sesion `busqueda_marca`.

### Agregar permisos

En base de datos:

```text
marca.ver
marca.crear
marca.editar
marca.eliminar
```

Asignarlos al rol correspondiente.

---

## 15. Eliminar vs desactivar

En este sistema hay una regla frecuente:

- Si el registro no esta referenciado, se elimina fisicamente con `DELETE`.
- Si ya fue usado por otro modulo, se desactiva con `estado = 0`.

Ejemplo en clientes:

```php
SELECT 1 FROM vehiculos WHERE id_cliente = :id LIMIT 1
```

Si hay vehiculos asociados, no borra cliente, lo desactiva.

### Donde se decide

Normalmente en el modelo:

```text
modelos/clienteModelo.php
```

Metodo:

```php
eliminar_cliente_modelo($id)
```

### Donde se informa al usuario

En el controlador:

```text
controladores/clienteControlador.php
```

Metodo:

```php
eliminar_cliente_controlador()
```

### Si te piden "debe poder borrar aunque este inactivo"

No valides el estado como impedimento. Solo valida referencias.

Correcto:

- Si esta inactivo y no tiene referencias: borrar.
- Si esta activo y no tiene referencias: borrar.
- Si tiene referencias: desactivar o impedir, segun regla del modulo.

Incorrecto:

- "No se puede eliminar porque ya esta inactivo".

---

## 16. Login, sesiones y bloqueo

Archivos:

```text
ajax/loginAjax.php
controladores/loginControlador.php
modelos/loginModelo.php
vistas/contenidos/login-vista.php
```

### Flujo

1. Usuario ingresa nick y clave.
2. `loginAjax.php` llama a `loginControlador`.
3. Controlador valida formato.
4. Modelo busca usuario.
5. Se verifica password con `password_verify`.
6. Si falla, sube `usu_intentos_fallidos`.
7. A los 3 intentos, marca `usu_bloqueado = 1`.
8. Si entra, guarda sesion.

Variables de sesion importantes:

```php
$_SESSION['id_str']
$_SESSION['nombre_str']
$_SESSION['apellido_str']
$_SESSION['nick_str']
$_SESSION['nick_sucursal']
$_SESSION['roles']
$_SESSION['permisos']
$_SESSION['empresa_nombre']
```

### Si un usuario no ve un menu

Revisar:

1. Que el permiso exista en tabla `permisos`.
2. Que el permiso este asignado al rol.
3. Que el usuario tenga ese rol en `usuario_rol`.
4. Que haya cerrado sesion y vuelto a entrar.
5. Que `navLateral.php` use el permiso correcto.

---

## 17. Usuarios, roles y permisos

Archivos:

```text
vistas/contenidos/usuario-nuevo-vista.php
controladores/usuarioControlador.php
modelos/usuarioModelo.php
ajax/usuarioAjax.php

vistas/contenidos/rol-nuevo-vista.php
vistas/contenidos/rol-permisos-vista.php
controladores/rolesControlador.php
modelos/rolesModelo.php
ajax/rolesAjax.php
```

### Usuarios

Permisos:

- `usuarios.ver`
- `usuarios.crear`
- `usuarios.editar`
- `usuarios.eliminar`

La vista unificada permite:

- Ver listado con `usuarios.ver`.
- Crear con `usuarios.crear`.
- Editar con `usuarios.editar`.

### Roles

Permisos:

- `roles.ver`
- `roles.crear`
- `roles.editar`
- `roles.eliminar`
- `permisos.asignar_permisos`

Importante: para entrar a `rol-actualizar/...` debe tener `roles.editar`.

---

## 18. Select2 y combos AJAX

Select2 se inicializa en:

```text
vistas/inc/scripts.php
```

Funcion:

```js
activarSelect2();
```

### Select normal

Usa clase:

```html
class="form-control select2"
```

### Select de clientes AJAX

Usa:

```html
class="form-control select2-clientes"
```

Ese select busca clientes en:

```text
ajax/vehiculoAjax.php
```

Con POST:

```js
accion: "buscar_cliente"
```

Si un combo Select2 no funciona:

1. Revisar que tenga clase `select2`.
2. Revisar que `scripts.php` este cargado.
3. Revisar consola del navegador.
4. Revisar que no haya IDs duplicados.
5. Si se carga contenido dinamico, llamar `activarSelect2(context)`.

---

## 19. Compras

Modulos principales:

- Pedidos: `pedido`
- Presupuestos de compra: `presupuesto`
- Ordenes de compra: `ordencompra`
- Facturas/compras: `compra`
- Remisiones: `remision`
- Notas de credito/debito: `notasCreDe`
- Transferencias: `transferencia`
- Inventario: `inventario`

Archivos tipicos:

```text
controladores/compraControlador.php
modelos/compraModelo.php
ajax/compraAjax.php
vistas/contenidos/factura-nuevo-vista.php
vistas/contenidos/factura-buscar-vista.php
```

### Stock

El stock se actualiza principalmente en:

```text
modelos/compraModelo.php
modelos/transferenciaModelo.php
modelos/inventarioModelo.php
modelos/salidaInsumoModelo.php
modelos/mainModel.php
```

Metodo central importante:

```php
mainModel::registrar_movimiento_stock_modelo($conexion, $datos)
```

Ese metodo:

- Bloquea stock con `FOR UPDATE`.
- Calcula saldo anterior.
- Calcula saldo actual.
- No permite saldo negativo.
- Actualiza/crea fila en `stock`.
- Inserta fila en `movimientostock`.

Si hay error de stock, revisar:

- `stock.stockDisponible`
- `movimientostock`
- signo del movimiento (`MovStockSigno`)
- sucursal
- articulo

---

## 20. Servicios

Modulos principales:

- Recepcion de servicio.
- Diagnostico.
- Presupuesto de trabajo.
- Orden de trabajo.
- Registro de servicio.
- Reclamos.
- Registro/salida de insumos.
- Equipos de trabajo.

Archivos principales:

```text
controladores/recepcionservicioControlador.php
controladores/diagnosticoControlador.php
controladores/presupuestoservicioControlador.php
controladores/ordenTrabajoControlador.php
controladores/registroServicioControlador.php
controladores/reclamoServicioControlador.php
controladores/salidaInsumoControlador.php
controladores/equipoControlador.php
```

### Flujo comun de servicio

1. Recepcion de servicio.
2. Diagnostico.
3. Presupuesto de servicio.
4. Orden de trabajo.
5. Registro de servicio.
6. Reclamo si aplica.

No todos los pasos siempre aplican, pero esa es la idea funcional.

### Equipos

Pantallas:

```text
empleado-equipo
empleado-equipo-asignar
empleado-equipo-actualizar
empleado-equipo-miembros
```

Controlador:

```text
controladores/equipoControlador.php
```

Modelo:

```text
modelos/equipoModelo.php
```

Permisos:

- `equipo.crear`
- `equipo.editar`
- `equipo.eliminar`

---

## 21. Reportes

Archivos:

```text
vistas/contenidos/reporte-referenciales-vista.php
vistas/contenidos/reporte-movimientos-vista.php
ajax/reportesAjax.php
controladores/reportesControlador.php
modelos/reportesModelo.php
pdf/
```

### Reportes referenciales

Incluyen:

- Articulos
- Proveedores
- Sucursales
- Clientes
- Vehiculos
- Empleados
- Usuarios
- Marcas/categorias si estan implementadas

### Reportes de movimientos

Incluyen:

- Pedidos
- Ordenes de compra
- Compras
- Transferencias
- Movimientos de stock
- Kardex
- Recepciones
- Ordenes de trabajo
- Registros de servicio

### Si te piden agregar un filtro al reporte

Tocar:

1. Vista del reporte: agregar input/select.
2. `ajax/reportesAjax.php`: recibir parametro si aplica.
3. `controladores/reportesControlador.php`: leer filtro y pasarlo.
4. `modelos/reportesModelo.php`: agregar condicion SQL.
5. PDF/CSV si el filtro afecta impresion/exportacion.

---

## 22. PDFs

Directorios:

```text
pdf/
pdf/plantillas/
facturas/
vendor/
```

Dependencias:

```json
{
    "mpdf/mpdf": "^8.2",
    "dompdf/dompdf": "^3.1"
}
```

Si un PDF falla:

1. Revisar errores PHP.
2. Revisar si `vendor/autoload.php` existe.
3. Revisar consulta del controlador/modelo.
4. Revisar datos vacios.
5. Revisar rutas de imagen/logo.

---

## 23. `mainModel.php`

Archivo base:

```text
modelos/mainModel.php
```

Funciones importantes:

### Conexion

```php
public static function conectar()
```

Devuelve PDO.

### Consulta simple

```php
protected static function ejecutar_consulta_simple($consulta)
```

Ejecuta SQL simple. Usar con cuidado. Para datos de usuario, preferir `prepare` con parametros.

### Encriptar y desencriptar IDs

```php
public static function encryption($string)
protected static function decryption($string)
```

Se usa para no exponer IDs directos en URLs y formularios.

Ejemplo:

```php
mainModel::encryption($rows['id_cliente'])
```

Luego en controlador:

```php
$id = mainModel::decryption($_POST['cliente_id_del']);
```

### Limpieza

```php
mainModel::limpiar_string($cadena)
```

Elimina textos peligrosos basicos.

### Validar patron

```php
mainModel::verificarDatos($filtro, $cadena)
```

Ojo: devuelve `false` cuando el dato SI cumple el patron, y `true` cuando NO cumple.

Por eso se usa:

```php
if (mainModel::verificarDatos("[0-9]{5,10}", $doc)) {
    // dato invalido
}
```

### Paginador

```php
mainModel::paginador($pagina, $Npaginas, $url, 10)
```

Genera HTML de paginacion.

### Permisos

```php
mainModel::tienePermiso('cliente.ver')
```

Lee `$_SESSION['permisos']`.

---

## 24. Como modificar el nav lateral

### Agregar un item

En `vistas/inc/navLateral.php`, buscar `$menuLateral` y agregar item.

Ejemplo:

```php
[
    'titulo' => 'Nuevo modulo',
    'icono' => 'fas fa-folder',
    'href' => 'nuevo-modulo/',
    'vista' => 'nuevo-modulo',
    'permiso' => 'nuevo.ver'
]
```

### Agregar submenu

```php
[
    'titulo' => 'Grupo',
    'icono' => 'fas fa-cog',
    'items' => [
        [
            'titulo' => 'Subitem',
            'icono' => 'fas fa-circle',
            'href' => 'subitem-nuevo/',
            'vistas' => ['subitem-nuevo', 'subitem-actualizar'],
            'permiso' => 'subitem.ver'
        ]
    ]
]
```

### Si el menu no aparece

Revisar:

- Permiso del item.
- Permiso del usuario logueado.
- Si es padre, que tenga al menos un hijo visible.
- Que no haya error de sintaxis en el array.

Validar:

```powershell
php -l vistas\inc\navLateral.php
```

---

## 25. Como agregar una vista

1. Crear archivo:

```text
vistas/contenidos/mi-vista-vista.php
```

2. Agregar `mi-vista` a `modelos/vistasModelo.php`.
3. Agregar item al nav si corresponde.
4. Agregar permisos si corresponde.
5. Validar:

```powershell
php -l vistas\contenidos\mi-vista-vista.php
php -l modelos\vistasModelo.php
```

---

## 26. Como agregar una accion AJAX nueva

Ejemplo: `activar_cliente`.

### En vista

Crear formulario o boton que envie:

```php
<input type="hidden" name="cliente_id_activar" value="<?php echo mainModel::encryption($id); ?>">
```

### En `ajax/clienteAjax.php`

Agregar condicion al `if` principal:

```php
if (isset($_POST['cliente_doc_reg']) || isset($_POST['cliente_id_del']) || isset($_POST['cliente_id_up']) || isset($_POST['cliente_id_activar'])) {
```

Y dentro:

```php
if (isset($_POST['cliente_id_activar'])) {
    echo $inst_cliente->activar_cliente_controlador();
}
```

### En controlador

Crear:

```php
public function activar_cliente_controlador()
```

Validar:

- Permiso.
- ID desencriptado.
- Existencia.
- Llamar modelo.
- Devolver JSON.

### En modelo

Crear:

```php
protected static function activar_cliente_modelo($id)
```

SQL:

```sql
UPDATE clientes SET estado_cliente = 1 WHERE id_cliente = :id
```

---

## 27. Como debuggear errores frecuentes

### Pantalla blanca

Posibles causas:

- Error PHP fatal.
- Include con ruta mal.
- Falta `;`.
- Clase mal nombrada.

Que hacer:

```powershell
php -l archivo.php
```

Tambien revisar logs de Apache/PHP en XAMPP.

### 404

Revisar:

- `modelos/vistasModelo.php`.
- Nombre del archivo.
- `.htaccess`.
- URL.

### Acceso no autorizado

Revisar:

- Permiso requerido en vista.
- Permiso requerido en controlador.
- Permiso asignado al rol.
- Si el usuario cerro sesion y volvio a entrar.

### Formulario no responde

Revisar:

- Clase `FormularioAjax`.
- `action`.
- `method="POST"`.
- `data-form`.
- Que `ajax/moduloAjax.php` reconozca los campos enviados.
- Consola del navegador.
- Respuesta JSON valida.

### SweetAlert no muestra nada

Posible JSON roto.

El controlador debe devolver solo JSON. No debe imprimir HTML, warnings ni espacios extra antes del JSON.

### No guarda en base de datos

Revisar:

- Names de inputs.
- Variables `$_POST`.
- Validaciones que cortan con `exit`.
- SQL del modelo.
- Campos obligatorios en base.
- `bindValue`.

### No aparece columna en listado

Revisar:

- Query del modelo.
- Nombre real de la columna.
- `foreach` en controlador.
- `<th>` y `<td>`.
- `colspan`.

### Boton editar/eliminar no aparece

Revisar permisos:

```php
mainModel::tienePermiso('modulo.editar')
mainModel::tienePermiso('modulo.eliminar')
```

### Select2 se ve raro

Revisar:

- `select2.min.css`
- `select2.min.js`
- `activarSelect2()`
- Clase `select2`

---

## 28. Validaciones por consola

### Validar sintaxis PHP

```powershell
php -l vistas\inc\navLateral.php
php -l controladores\clienteControlador.php
php -l modelos\clienteModelo.php
php -l ajax\clienteAjax.php
```

### Buscar texto

Usar `rg`:

```powershell
rg -n "cliente.ver"
rg -n "cliente-nuevo"
rg -n "agregar_cliente_controlador"
```

### Ver estado de cambios

```powershell
git status --short
```

### Ver diferencias

```powershell
git diff -- archivo.php
```

### Pruebas existentes

```powershell
php tests\stock_decimal_schema_check.php
php tests\system_smoke_check.php
```

Ojo: `system_smoke_check.php` requiere base de datos cargada con tablas y datos minimos.

---

## 29. Mapa de modulos activos principales

### Referenciales

| Modulo | Vista principal | AJAX | Controlador | Modelo |
|---|---|---|---|---|
| Articulos | `articulo-nuevo-vista.php` | `articuloAjax.php` | `articuloControlador.php` | `articuloModelo.php` |
| Clientes | `cliente-nuevo-vista.php` | `clienteAjax.php` | `clienteControlador.php` | `clienteModelo.php` |
| Proveedores | `proveedor-nuevo-vista.php` | `proveedorAjax.php` | `proveedorControlador.php` | `proveedorModelo.php` |
| Sucursales | `sucursal-nuevo-vista.php` | `sucursalAjax.php` | `sucursalControlador.php` | `sucursalModelo.php` |
| Vehiculos | `vehiculo-nuevo-vista.php` | `vehiculoAjax.php` | `vehiculoControlador.php` | `vehiculoModelo.php` |
| Empleados | `empleado-nuevo-vista.php` | `empleadoAjax.php` | `empleadoControlador.php` | `empleadoModelo.php` |
| Usuarios | `usuario-nuevo-vista.php` | `usuarioAjax.php` | `usuarioControlador.php` | `usuarioModelo.php` |
| Roles | `rol-nuevo-vista.php` | `rolesAjax.php` | `rolesControlador.php` | `rolesModelo.php` |

### Compras

| Modulo | Vistas | AJAX | Controlador | Modelo |
|---|---|---|---|---|
| Pedido | `pedido-nuevo`, `pedido-buscar` | `pedidoAjax.php` | `pedidoControlador.php` | `pedidoModelo.php` |
| Presupuesto compra | `presupuesto-nuevo`, `presupuesto-buscar` | `presupuestoAjax.php` | `presupuestoControlador.php` | `presupuestoModelo.php` |
| Orden compra | `oc-nuevo`, `oc-buscar` | `ordencompraAjax.php` | `ordencompraControlador.php` | `ordencompraModelo.php` |
| Factura/compra | `factura-nuevo`, `factura-buscar` | `compraAjax.php` | `compraControlador.php` | `compraModelo.php` |
| Remision | `remision-nuevo`, `remision-buscar` | `remisionAjax.php` | `remisionControlador.php` | `remisionModelo.php` |
| Notas credito/debito | `notasCreDe-nuevo`, `notasCreDe-buscar` | `notasCreDeAjax.php` | `notasCreDeControlador.php` | `notasCreDeModelo.php` |
| Transferencia | `transferencia-nuevo`, `transferencia-historial`, `transferencia-recibir` | `transferenciaAjax.php` | `transferenciaControlador.php` | `transferenciaModelo.php` |
| Inventario | `inventario`, `inventario-buscar` | `inventarioAjax.php` | `inventarioControlador.php` | `inventarioModelo.php` |

### Servicios

| Modulo | Vistas | AJAX | Controlador | Modelo |
|---|---|---|---|---|
| Recepcion | `recepcionServicio-nuevo`, `recepcionServicio-buscar` | `recepcionservicioAjax.php` | `recepcionservicioControlador.php` | `recepcionservicioModelo.php` |
| Diagnostico | `diagnostico-servicio-nuevo`, `diagnostico-servicio-buscar` | `diagnosticoAjax.php` | `diagnosticoControlador.php` | `diagnosticoModelo.php` |
| Presupuesto servicio | `presupuesto-servicio-nuevo`, `presupuesto-servicio-buscar` | `presupuestoservicioAjax.php` | `presupuestoservicioControlador.php` | `presupuestoservicioModelo.php` |
| Orden trabajo | `ordenTrabajo-nuevo`, `ordenTrabajo-buscar` | `ordenTrabajoAjax.php` | `ordenTrabajoControlador.php` | `ordenTrabajoModelo.php` |
| Registro servicio | `registro-servicio-nuevo`, `registro-servicio-buscar` | `registroServicioAjax.php` | `registroServicioControlador.php` | `registroServicioModelo.php` |
| Reclamo | `reclamo-servicio-nuevo`, `reclamo-servicio-lista` | `reclamoServicioAjax.php` | `reclamoServicioControlador.php` | `reclamoServicioModelo.php` |
| Salida insumo | `registro-insumos`, `registro-insumos-buscar` | `salidaInsumoAjax.php` | `salidaInsumoControlador.php` | `salidaInsumoModelo.php` |
| Equipos | `empleado-equipo*` | `equipoAjax.php` | `equipoControlador.php` | `equipoModelo.php` |

---

## 30. Checklist para defender cambios

Cuando te pregunten "como se implemento esto", responde por capas:

1. En la vista se agrego el campo/boton/listado.
2. El formulario envia por AJAX usando `FormularioAjax`.
3. El archivo `ajax/...Ajax.php` recibe el POST y llama al controlador.
4. El controlador valida permisos, datos obligatorios, formato y duplicados.
5. El controlador llama al modelo.
6. El modelo ejecuta SQL con PDO.
7. El controlador devuelve JSON.
8. `alertas.js` interpreta el JSON y muestra la respuesta.
9. El menu se controla con `navLateral.php`.
10. Las rutas validas estan en `vistasModelo.php`.

Esa explicacion sirve para casi cualquier modulo.

---

## 31. Checklist antes de entregar un cambio

1. `php -l` en todos los archivos tocados.
2. Probar con usuario con permiso completo.
3. Probar con usuario solo `*.ver` si aplica.
4. Probar crear.
5. Probar editar.
6. Probar eliminar/desactivar.
7. Probar busqueda.
8. Probar paginacion.
9. Probar que no aparezcan botones sin permiso.
10. Verificar que no haya errores en consola del navegador.
11. Verificar que no haya warnings PHP.
12. Revisar `git diff`.

---

## 32. Que no tocar sin necesidad

Evitar tocar:

- `vendor/`: dependencias de Composer.
- `composer.lock`: salvo que actualices dependencias.
- `_legacy/`: codigo retirado.
- `tmp/`: sesiones temporales.
- `uploads/`: archivos subidos.
- `facturas/fpdf.php`: libreria.

Tocar con cuidado:

- `config/SERVER.php`: afecta conexion.
- `config/APP.php`: afecta rutas.
- `modelos/mainModel.php`: afecta todo el sistema.
- `vistas/plantilla.php`: afecta todas las pantallas.
- `vistas/js/alertas.js`: afecta todos los formularios AJAX.
- `ajax/buscadorAjax.php`: afecta todas las busquedas.
- `vistas/inc/navLateral.php`: afecta menu y acceso visual.

---

## 33. Resumen mental rapido

Si te perdes, usa esta cadena:

```text
URL -> vistasModelo -> plantilla -> vista -> FormularioAjax -> ajax -> controlador -> modelo -> base de datos -> JSON -> alertas.js
```

Y para permisos:

```text
login -> $_SESSION['permisos'] -> mainModel::tienePermiso() -> nav/vista/controlador
```

Y para referenciales unificados:

```text
*-nuevo/ no significa solo crear; tambien contiene buscador/listado.
*.ver entra y lista.
*.crear muestra formulario.
*.editar permite actualizar.
*.eliminar permite eliminar/desactivar.
```

Con esto podes explicar la arquitectura, ubicar cambios y defender por que se modifica cada archivo.
