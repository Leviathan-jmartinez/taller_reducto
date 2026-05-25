# Manual general de cambios posibles para defensa

## Objetivo

Este documento sirve como guia de practica para realizar cambios en vivo durante la defensa del proyecto.

La idea es tener claros los puntos mas comunes que pueden pedir:

- Cambios en el menu lateral.
- Cambios de permisos.
- Cambios en login y sesion.
- Cambios de validaciones.
- Cambios en vistas, controladores y modelos.
- Ubicacion de archivos importantes.
- Riesgos de cambios demasiado grandes o fuera del estilo del proyecto.

## Archivos importantes del proyecto

| Archivo | Uso |
|---|---|
| `vistas/plantilla.php` | Carga la plantilla general y valida sesion |
| `vistas/inc/navLateral.php` | Genera el menu lateral |
| `vistas/css/style.css` | Estilos generales del sistema |
| `vistas/js/main.js` | Comportamiento del menu lateral y otros elementos |
| `controladores/loginControlador.php` | Controla inicio, cierre y cambio obligatorio de clave |
| `modelos/loginModelo.php` | Consultas del login, roles y permisos |
| `modelos/mainModel.php` | Funciones comunes: conexion, limpieza, permisos |
| `modelos/vistasModelo.php` | Lista blanca de vistas permitidas |
| `ajax/loginAjax.php` | Peticiones AJAX relacionadas al login |

## Cambios posibles en el menu lateral

### Agregar una opcion nueva

Se modifica el arreglo `$menuLateral` en `vistas/inc/navLateral.php`.

Ejemplo:

```php
[
    'titulo' => 'Nueva Opcion',
    'icono' => 'fas fa-file',
    'href' => 'nueva-opcion/',
    'vistas' => ['nueva-opcion', 'nueva-opcion-buscar'],
    'permiso' => 'modulo.nueva_opcion.ver'
]
```

Puntos a explicar:

- `titulo` es el texto visible.
- `icono` es la clase de Font Awesome.
- `href` es la ruta.
- `vistas` indica que pantallas activan esa opcion.
- `permiso` define que usuarios pueden verla.

### Cambiar el texto de una opcion

Buscar el campo:

```php
'titulo' => 'Pedidos'
```

y modificarlo:

```php
'titulo' => 'Pedidos de Compra'
```

### Cambiar un icono

Buscar:

```php
'icono' => 'fas fa-file-alt'
```

y reemplazar por otro icono disponible:

```php
'icono' => 'fas fa-clipboard-list'
```

### Cambiar el permiso de una opcion

Buscar:

```php
'permiso' => 'compra.pedido.ver'
```

y cambiar por el permiso requerido:

```php
'permiso' => 'compra.pedido.crear'
```

### Hacer que una pantalla marque activo el menu

Agregar la vista al arreglo `vistas`.

Ejemplo:

```php
'vistas' => ['pedido-nuevo', 'pedido-lista', 'pedido-buscar', 'pedido-detalle']
```

Si el usuario entra a `pedido-detalle`, el item `Pedidos` queda activo.

### Ocultar un grupo vacio

No hay que programar nada adicional.

La funcion `nav_item_visible()` ya evita mostrar grupos sin opciones visibles.

## Funciones del menu lateral

### `nav_tiene_permiso($permisos)`

Valida si el usuario tiene permiso para ver una opcion.

Usa:

```php
mainModel::tienePermiso($permiso)
```

Puede recibir un permiso o una lista de permisos.

### `nav_item_visible($item)`

Indica si un item debe mostrarse.

Si el item tiene permiso y el usuario no lo tiene, no se muestra.

Si el item tiene subitems, solo se muestra si al menos uno de los subitems es visible.

### `nav_item_activo($item, $vistaActual)`

Indica si una opcion corresponde a la vista actual.

Sirve para agregar la clase `active` y abrir submenus.

### `nav_render_items($items, $vistaActual)`

Genera el HTML del menu.

Dibuja:

- Items normales.
- Submenus.
- Submenus anidados.
- Clases activas.
- Clases de submenu abierto.

## Cambios posibles en permisos

### Ver donde se cargan los permisos

Archivo:

`controladores/loginControlador.php`

Linea logica:

```php
$_SESSION['permisos'] = loginModelo::obtener_permisos_usuario($row['id_usuario']);
```

### Ver donde se consultan los permisos

Archivo:

`modelos/loginModelo.php`

Metodo:

```php
obtener_permisos_usuario($idUsuario)
```

Este metodo consulta:

- `usuario_rol`
- `rol_permiso`
- `permisos`

### Ver donde se valida un permiso

Archivo:

`modelos/mainModel.php`

Metodo:

```php
tienePermiso(string $permiso)
```

Este metodo busca el permiso dentro de:

```php
$_SESSION['permisos']
```

## Cambios posibles en login

### Cambiar validacion de campos vacios

Archivo:

`controladores/loginControlador.php`

Buscar:

```php
if ($usuario == "" || $clave == "")
```

Sirve para evitar iniciar sesion con campos incompletos.

### Cambiar validacion de formato

Archivo:

`controladores/loginControlador.php`

Se usan validaciones con:

```php
mainModel::verificarDatos(...)
```

Para defensa, conviene explicar que estas reglas evitan caracteres no permitidos y datos mal formados.

### Cambiar comportamiento por cuenta inactiva

Buscar:

```php
if ($row['usu_estado'] != 1)
```

Esta condicion impide iniciar sesion a usuarios inactivos.

### Cambiar comportamiento por cuenta bloqueada

Buscar:

```php
if (isset($row['usu_bloqueado']) && $row['usu_bloqueado'] == 1)
```

Esta condicion impide iniciar sesion a usuarios bloqueados.

### Ver intentos fallidos

Archivo:

`modelos/loginModelo.php`

Metodo:

```php
registrar_intento_fallido_modelo($idUsuario)
```

Hace tres cosas:

- Incrementa `usu_intentos_fallidos`.
- Bloquea si supera el limite.
- Devuelve el estado actual del usuario.

### Reiniciar intentos fallidos

Metodo:

```php
reiniciar_intentos_login_modelo($idUsuario)
```

Se ejecuta cuando el usuario ingresa correctamente.

## Cambios posibles en sucursal y empresa

### Donde se guarda la sucursal

Archivo:

`controladores/loginControlador.php`

```php
$_SESSION['nick_sucursal'] = $row['sucursalid'];
```

Esto guarda el identificador de la sucursal asignada al usuario.

### Donde se obtiene la empresa

Archivo:

`modelos/loginModelo.php`

Consulta:

```sql
FROM usuarios u
LEFT JOIN sucursales s ON s.id_sucursal = u.sucursalid
LEFT JOIN empresa e ON e.id_empresa = s.id_empresa
```

Esto evita tomar una empresa cualquiera con `LIMIT 1`.

### Donde se guarda el nombre de empresa

Archivo:

`controladores/loginControlador.php`

```php
$_SESSION['empresa_nombre'] = !empty($row['empresa_razon_social'])
    ? $row['empresa_razon_social']
    : 'Empresa';
```

## Cambios posibles en vistas

### Agregar una nueva vista al sistema

1. Crear el archivo en:

`vistas/contenidos/nueva-vista.php`

2. Agregar el nombre a la lista blanca en:

`modelos/vistasModelo.php`

3. Agregar la opcion al menu lateral si corresponde.

4. Agregar permisos si la vista debe estar restringida.

## Cambios posibles en controladores

### Agregar una validacion

Ejemplo generico:

```php
if (empty($_POST['campo'])) {
    echo json_encode([
        "Alerta" => "simple",
        "Titulo" => "Campo requerido",
        "Texto" => "Debe completar el campo",
        "Tipo" => "error"
    ]);
    exit();
}
```

### Validar permisos en una accion

Ejemplo:

```php
if (!mainModel::tienePermiso('modulo.accion')) {
    echo json_encode([
        "Alerta" => "simple",
        "Titulo" => "Permiso denegado",
        "Texto" => "No posee permisos para realizar esta accion",
        "Tipo" => "error"
    ]);
    exit();
}
```

## Cambios posibles en estilos

Archivo:

`vistas/css/style.css`

Cambios comunes:

- Color de fondo del menu.
- Color de opcion activa.
- Espaciado de links.
- Estilo de la tarjeta del usuario.
- Hover del menu.
- Bordes y sombras.

Variables agregadas para el menu:

```css
--nav-bg
--nav-panel
--nav-text
--nav-muted
--nav-hover
--nav-active
```

## Cambios que pueden pedir en defensa

### Cambio 1: agregar una opcion al menu

Demuestra que entendes `$menuLateral`.

### Cambio 2: cambiar un permiso

Demuestra que entendes permisos por rol.

### Cambio 3: hacer que una vista quede activa

Demuestra que entendes `vista`, `vistas` y `$vistaActual`.

### Cambio 4: ocultar una opcion

Demuestra que entendes `nav_tiene_permiso()` y `nav_item_visible()`.

### Cambio 5: modificar el color activo

Demuestra que sabes ubicar estilos en CSS.

### Cambio 6: validar un campo en un controlador

Demuestra que entendes flujo de entrada, validacion y respuesta.

### Cambio 7: explicar por que un usuario no ve un modulo

Respuesta esperada:

El modulo no se renderiza porque el usuario no tiene el permiso requerido cargado en `$_SESSION['permisos']`.

## Como defender el cambio del menu lateral

Explicacion corta:

El menu lateral estaba repetido directamente en HTML/PHP. Se reorganizo en un arreglo para representar mejor la jerarquia de modulos, submodulos y opciones. Luego una funcion recorre esa estructura, valida permisos y genera el HTML final.

Explicacion tecnica:

El arreglo `$menuLateral` define el menu. Cada item puede tener titulo, icono, ruta, permisos, vista o vistas relacionadas. La funcion `nav_render_items()` recorre el arreglo y renderiza los enlaces. Antes de renderizar valida si el usuario tiene permisos con `nav_item_visible()`. Para marcar la opcion seleccionada utiliza `nav_item_activo()`, comparando la vista actual con `vista` o `vistas`.

## Revision de naturalidad del proyecto

### Cambios que se ven normales y defendibles

- Corregir la consulta de empresa para que dependa de la sucursal del usuario.
- Quitar el uso de token si el sistema ya valida sesion por usuario e ID.
- Mejorar el menu lateral para reducir duplicacion.
- Agregar marcado de opcion activa.
- Mejorar estilos del menu lateral.
- Documentar los cambios.

### Cambios que pueden llamar la atencion

- El cambio del menu lateral es grande.
- Pasar de HTML/PHP repetido a un arreglo dinamico puede sentirse diferente al estilo original.
- Los backups y archivos de sesion no deberian entregarse como parte final.
- Un manual demasiado perfecto puede parecer generado si no se adapta a tu forma de explicar.

### Recomendaciones para entrega

- No entregar archivos temporales de `tmp/sess_*`.
- No entregar backups si no son requeridos.
- Si se entregan backups, explicar que fueron creados antes de refactorizar.
- Leer y entender cada funcion del menu.
- Poder agregar una opcion nueva sin ayuda.
- Poder explicar por que se usa `vistas` para marcar activo un menu.
- Poder explicar que los permisos siguen dependiendo de la sesion.

### Archivos temporales detectados que conviene limpiar antes de entregar

Estos archivos son sesiones temporales y no forman parte del codigo fuente:

`tmp/sess_*`

Tambien existen backups de trabajo:

`vistas/inc/navLateral.bk.php`  
`vistas/inc/navLateral.activo-bk.php`  
`vistas/css/style.bk.css`

Para una entrega final limpia, conviene quitarlos del paquete o no versionarlos.

## Frases utiles para la defensa

> Centralice la estructura del menu para evitar repetir bloques HTML y facilitar mantenimiento.

> El menu no decide permisos por si solo; utiliza los permisos ya cargados en la sesion del usuario.

> Si un usuario no ve una opcion, es porque no tiene el permiso requerido o porque el grupo no tiene subopciones visibles.

> La opcion activa se determina comparando la vista actual con las vistas configuradas en cada item.

> La empresa se obtiene desde la sucursal asignada al usuario para evitar tomar una empresa incorrecta.

## Checklist antes de defender

- Puedo explicar el login.
- Puedo explicar donde se cargan permisos.
- Puedo explicar donde se valida permiso.
- Puedo agregar una opcion al menu.
- Puedo cambiar un permiso del menu.
- Puedo hacer que una vista quede activa.
- Puedo explicar el filtrado por sucursal.
- Puedo explicar la relacion usuario, sucursal y empresa.
- Puedo ubicar una vista en `vistasModelo.php`.
- Puedo validar sintaxis con `php -l`.

