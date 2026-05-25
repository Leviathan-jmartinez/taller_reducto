# Manual tecnico: menu lateral dinamico

## Archivo principal

El menu lateral del sistema se encuentra en:

`vistas/inc/navLateral.php`

Los estilos visuales asociados se encuentran en:

`vistas/css/style.css`

Tambien se dejaron respaldos de trabajo:

`vistas/inc/navLateral.bk.php`  
`vistas/inc/navLateral.activo-bk.php`  
`vistas/css/style.bk.css`

## Objetivo del cambio

El menu lateral fue reorganizado para que las opciones se generen desde una estructura de datos centralizada llamada `$menuLateral`.

Antes, cada opcion del menu estaba escrita directamente en HTML/PHP. Ahora, el sistema recorre un arreglo y genera automaticamente:

- Opciones principales.
- Submenus.
- Submenus anidados.
- Validacion de permisos.
- Marcado de opcion activa.
- Apertura automatica del submenu correspondiente.

Esto facilita agregar, quitar o modificar opciones sin repetir tanto codigo HTML.

## Vista actual

Al inicio del archivo se obtiene la vista actual:

```php
if (isset($pagina[0]) && $pagina[0] !== '') {
    $vistaActual = (string)$pagina[0];
} elseif (isset($_GET['vista'])) {
    $vistaActual = explode('/', trim((string)$_GET['vista'], '/'))[0] ?? 'home';
} else {
    $vistaActual = 'home';
}
```

Esta variable sirve para saber en que pantalla esta el usuario.

Ejemplos:

- `pedido-nuevo`
- `pedido-buscar`
- `ordenTrabajo-nuevo`
- `reporte-compras`

Con ese dato el sistema puede marcar la opcion correspondiente como activa.

## Datos del usuario

El menu tambien prepara los datos visibles del usuario:

```php
$usuarioNav = trim(($_SESSION['nombre_str'] ?? '') . ' ' . ($_SESSION['apellido_str'] ?? ''));
$empresaNav = $_SESSION['empresa_nombre'] ?? '';
```

Tambien genera iniciales para mostrar en la tarjeta del usuario:

```php
$inicialesNav = '';
```

Por ejemplo:

- Juan Perez -> JP
- Maria Lopez -> ML

Si no encuentra nombre, usa `US`.

## Funcion `nav_tiene_permiso`

```php
function nav_tiene_permiso($permisos)
```

Verifica si el usuario posee al menos uno de los permisos requeridos.

Puede recibir:

- Un permiso como texto.
- Una lista de permisos.
- Un valor vacio, en cuyo caso permite mostrar la opcion.

Ejemplo con un permiso:

```php
'permiso' => 'compra.ver'
```

Ejemplo con varios permisos:

```php
'permiso' => ['equipo.crear', 'equipo.editar']
```

Internamente utiliza:

```php
mainModel::tienePermiso($permiso)
```

## Funcion `nav_item_visible`

```php
function nav_item_visible($item)
```

Determina si una opcion del menu debe mostrarse.

Reglas:

- Si la opcion tiene permiso y el usuario no lo posee, no se muestra.
- Si la opcion tiene subopciones, solo se muestra si al menos una subopcion es visible.
- Si no tiene restriccion, se muestra.

Esto evita que aparezcan grupos vacios.

Ejemplo:

Si el usuario no tiene permisos de reportes, el grupo de informes no se muestra.

## Funcion `nav_item_activo`

```php
function nav_item_activo($item, $vistaActual)
```

Determina si una opcion corresponde a la pantalla actual.

Puede comparar contra:

```php
'vista' => 'home'
```

o contra varias vistas relacionadas:

```php
'vistas' => ['pedido-nuevo', 'pedido-lista', 'pedido-buscar']
```

Esto permite que el mismo item quede activo en diferentes pantallas del mismo modulo.

Ejemplo:

Si el usuario entra a `pedido-buscar`, el menu marca como activo el item `Pedidos`.

## Funcion `nav_render_items`

```php
function nav_render_items($items, $vistaActual)
```

Es la funcion que dibuja el menu en HTML.

Recorre cada elemento de `$menuLateral` y:

- Valida si debe mostrarse.
- Verifica si esta activo.
- Si tiene subitems, genera un submenu.
- Si no tiene subitems, genera un enlace normal.
- Agrega la clase `active` cuando corresponde.
- Agrega la clase `show-nav-lateral-submenu` cuando un submenu debe abrirse.

Ejemplo de salida para un enlace activo:

```html
<a href="..." class="active">Pedidos</a>
```

Ejemplo de salida para un submenu abierto:

```html
<ul class="show-nav-lateral-submenu">
```

## Estructura del arreglo `$menuLateral`

El menu completo se define en:

```php
$menuLateral = [
    ...
];
```

Cada opcion puede tener estos campos:

| Campo | Funcion |
|---|---|
| `titulo` | Texto que se muestra en el menu |
| `icono` | Clase del icono Font Awesome |
| `href` | Ruta a la que apunta la opcion |
| `vista` | Vista unica que activa el item |
| `vistas` | Lista de vistas que activan el item |
| `permiso` | Permiso o permisos requeridos |
| `items` | Subopciones del menu |
| `download` | Indica si el enlace descarga un archivo |

## Ejemplo de item simple

```php
[
    'titulo' => 'Panel Principal',
    'icono' => 'fab fa-dashcube',
    'href' => 'home/',
    'vista' => 'home'
]
```

Este item:

- Muestra el texto `Panel Principal`.
- Usa el icono `fa-dashcube`.
- Redirecciona a `home/`.
- Se marca activo cuando la vista actual es `home`.

## Ejemplo de item con permiso

```php
[
    'titulo' => 'Pedidos',
    'icono' => 'fas fa-file-alt',
    'href' => 'pedido-nuevo/',
    'vistas' => ['pedido-nuevo', 'pedido-lista', 'pedido-buscar'],
    'permiso' => 'compra.pedido.ver'
]
```

Este item solo aparece si el usuario tiene el permiso:

`compra.pedido.ver`

Queda activo si la vista actual es:

- `pedido-nuevo`
- `pedido-lista`
- `pedido-buscar`

## Ejemplo de submenu

```php
[
    'titulo' => 'Compras',
    'icono' => 'fas fa-shopping-cart',
    'permiso' => 'compra.ver',
    'items' => [
        ...
    ]
]
```

Este elemento no redirecciona directamente. Funciona como grupo y contiene otras opciones.

Si una de sus opciones internas esta activa, el submenu se abre automaticamente.

## Como agregar una nueva opcion

Para agregar una opcion nueva dentro de Compras:

1. Buscar el grupo `Compras`.
2. Agregar un nuevo arreglo dentro de `items`.

Ejemplo:

```php
[
    'titulo' => 'Nueva Opcion',
    'icono' => 'fas fa-file',
    'href' => 'nueva-opcion/',
    'vistas' => ['nueva-opcion', 'nueva-opcion-buscar'],
    'permiso' => 'compra.nueva_opcion.ver'
]
```

Luego se debe verificar que:

- La vista exista en `modelos/vistasModelo.php`.
- El permiso exista en la tabla `permisos`.
- El rol del usuario tenga asignado ese permiso.

## Como agregar un nuevo submenu

Ejemplo:

```php
[
    'titulo' => 'Nuevo Grupo',
    'icono' => 'fas fa-folder',
    'permiso' => 'modulo.ver',
    'items' => [
        [
            'titulo' => 'Opcion Interna',
            'icono' => 'fas fa-file',
            'href' => 'opcion-interna/',
            'vista' => 'opcion-interna',
            'permiso' => 'modulo.opcion.ver'
        ]
    ]
]
```

El submenu se mostrara solo si el usuario tiene permisos para ver al menos una opcion interna.

## Estilos aplicados

Los estilos se encuentran en `vistas/css/style.css`.

Las clases principales son:

| Clase | Funcion |
|---|---|
| `.nav-lateral` | Contenedor general del menu lateral |
| `.nav-lateral-content` | Capa interna del menu |
| `.nav-user-card` | Tarjeta del usuario |
| `.nav-user-initials` | Iniciales del usuario |
| `.nav-user-name` | Nombre del usuario |
| `.nav-user-company` | Empresa del usuario |
| `.nav-lateral-menu` | Contenedor de opciones del menu |
| `.active` | Opcion seleccionada |
| `.show-nav-lateral-submenu` | Submenu abierto |

## Relacion con JavaScript

El archivo `vistas/js/main.js` ya tenia el comportamiento para abrir y cerrar submenus.

El nuevo menu conserva las clases:

```html
nav-btn-submenu
show-nav-lateral-submenu
fa-chevron-down
fa-rotate-180
```

Por eso el JavaScript existente sigue funcionando sin cambios.

## Resumen del funcionamiento

1. El sistema identifica la vista actual.
2. Carga los datos del usuario desde la sesion.
3. Recorre el arreglo `$menuLateral`.
4. Verifica permisos por cada item.
5. Oculta opciones sin permiso.
6. Marca como activa la opcion correspondiente.
7. Abre automaticamente el submenu que contiene la vista actual.
8. Renderiza el HTML final del menu lateral.

