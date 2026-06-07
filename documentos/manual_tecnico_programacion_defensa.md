# Manual tecnico de programacion y defensa del proyecto

Este documento explica como esta programado el sistema, por que se tomaron ciertas decisiones y donde modificar si durante la defensa solicitan cambios. Esta pensado como guia tecnica para responder preguntas del tutor y para ubicar rapidamente los archivos importantes.

## 1. Vision general

El proyecto es una aplicacion PHP tradicional con estructura cercana a MVC:

- `vistas/`: pantallas HTML/PHP que ve el usuario.
- `ajax/`: puntos de entrada para peticiones asincronas o formularios.
- `controladores/`: validan permisos, reciben datos, aplican reglas y llaman modelos.
- `modelos/`: consultas SQL y operaciones directas sobre la base de datos.
- `config/`: constantes del sistema, conexion y URL base.
- `pdf/`: plantillas o generacion de reportes PDF.
- `documentos/`: especificaciones, manuales y respaldo tecnico.
- `database/`: dumps y scripts SQL de apoyo.

La idea principal es separar responsabilidades:

- La vista no debe decidir reglas de negocio importantes.
- El controlador decide si el usuario puede hacer la accion y valida datos.
- El modelo ejecuta la consulta o transaccion en la base de datos.
- El AJAX conecta la pantalla con el controlador.

## 2. Flujo basico de una pantalla

El flujo normal de una pantalla es:

1. El usuario entra a una URL, por ejemplo `reporte-movimientos/`.
2. `modelos/vistasModelo.php` valida si la vista esta permitida en la lista blanca.
3. Se carga el archivo de vista ubicado en `vistas/contenidos/`.
4. La vista muestra el formulario y botones.
5. Al enviar el formulario, se llama a un archivo de `ajax/`.
6. El archivo AJAX instancia el controlador correspondiente.
7. El controlador valida permisos y datos.
8. El controlador llama al modelo.
9. El modelo consulta o actualiza la base de datos.
10. La respuesta vuelve a la vista como HTML, JSON, PDF o CSV.

Ejemplo en informes:

- Vista: `vistas/contenidos/reporte-movimientos-vista.php`
- AJAX: `ajax/reportesAjax.php`
- Controlador: `controladores/reportesControlador.php`
- Modelo: `modelos/reportesModelo.php`

## 3. Enrutamiento de vistas

El archivo principal para permitir vistas es:

`modelos/vistasModelo.php`

La funcion importante es:

```php
protected static function obtenVista_modelo($vistas)
```

Dentro existe una lista blanca. Si se crea una vista nueva y no se agrega ahi, el sistema mostrara `404`.

Ejemplo:

```php
"reporte-referenciales", "reporte-movimientos"
```

Para agregar una nueva pantalla:

1. Crear `vistas/contenidos/nueva-vista.php`.
2. Agregar `"nueva"` a la lista blanca de `vistasModelo.php`.
3. Agregar opcion en el menu si corresponde, normalmente en `vistas/inc/navLateral.php`.
4. Crear AJAX/controlador/modelo si la pantalla guarda o consulta datos.

## 4. Seguridad y permisos

El sistema usa permisos por clave. La funcion central es:

`mainModel::tienePermiso('clave.permiso')`

Esta funcion esta en:

`modelos/mainModel.php`

Ejemplo:

```php
if (!mainModel::tienePermiso('reportes.movimientos_stock.ver')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
```

Los permisos se validan en dos capas:

- En la vista, para no mostrar pantallas o botones al usuario sin permiso.
- En el controlador, para evitar que alguien llame directo al AJAX.

Esto es importante para la defensa: no alcanza con ocultar botones. El controlador tambien debe bloquear la accion.

## 5. Patron AJAX

Los archivos de `ajax/` reciben datos por `POST`, validan sesion y redirigen al controlador.

Ejemplo de informes:

`ajax/reportesAjax.php`

Usa dos formas:

- `modulo`: para previsualizaciones por AJAX.
- `accion`: para operaciones concretas como PDF o CSV.

Ejemplo:

```php
if ($modulo === "movimientos_unificado") {
    echo $inst_reporte->reporte_movimientos_unificado_controlador();
    exit();
}
```

Y para exportar:

```php
case 'exportar_reporte_movimientos_csv':
    $inst_reporte->exportar_reporte_movimientos_csv_controlador();
    exit();
```

Si se agrega una accion nueva:

1. Crear metodo en el controlador.
2. Agregar `case` en AJAX.
3. En la vista, enviar `accion` o `modulo` correcto.

## 6. Modelo base `mainModel`

Archivo:

`modelos/mainModel.php`

Responsabilidades principales:

- Conexion PDO.
- Limpieza de cadenas.
- Encriptacion/desencriptacion de IDs en URLs.
- Paginacion.
- Validacion de permisos.
- Funciones reutilizables.
- Movimiento de stock centralizado.

Funciones importantes:

```php
public static function conectar()
public static function tienePermiso(string $permiso): bool
protected static function limpiar_string($cadena)
public static function registrar_movimiento_stock_modelo(PDO $conexion, array $datos)
```

## 7. Movimiento de stock centralizado

La funcion clave para stock es:

```php
mainModel::registrar_movimiento_stock_modelo($conexion, $datos)
```

Esta funcion:

1. Recibe sucursal, articulo, cantidad, signo, tipo, usuario y referencia.
2. Bloquea el stock actual con `FOR UPDATE`.
3. Calcula saldo anterior.
4. Calcula saldo actual.
5. Actualiza la tabla `stock`.
6. Inserta el movimiento en `movimientostock`.
7. Guarda el ID del movimiento en `stockultimoIdActualizacion`.

Por que se hizo asi:

- Evita que cada modulo actualice stock de una forma distinta.
- Permite que el Kardex tenga saldo confiable.
- Reduce errores donde se inserta movimiento pero no se actualiza stock, o al reves.
- Hace mas facil defender la trazabilidad.

Datos esperados:

```php
[
    "id_sucursal" => 1,
    "tipo" => "RECEPCION COMPRA",
    "id_articulo" => 10,
    "cantidad" => 5,
    "precio_venta" => 0,
    "costo" => 120000,
    "usuario" => $_SESSION['id_str'],
    "signo" => 1,
    "referencia" => "COMPRA #25"
]
```

Signos:

- `1`: entrada de stock.
- `-1`: salida de stock.

El saldo se calcula como:

```php
$saldoActual = $saldoAnterior + ($cantidad * $signo);
```

## 8. Tablas principales para stock

### `stock`

Representa el estado actual de un articulo por sucursal.

Campos importantes:

- `id_sucursal`
- `id_articulo`
- `stockDisponible`
- `stockUltActualizacion`
- `stockUsuActualizacion`
- `stockultimoIdActualizacion`

Esta tabla responde: cuanto stock hay ahora.

### `movimientostock`

Representa el historial de movimientos.

Campos importantes:

- `MovStockId`
- `id_sucursal`
- `TipoMovStockId`
- `MovStockArticuloId`
- `MovStockCantidad`
- `MovStockFechaHora`
- `MovStockUsuario`
- `MovStockSigno`
- `MovStockReferencia`
- `MovStockSaldoAnterior`
- `MovStockSaldoActual`

Esta tabla responde: como se llego al stock actual.

SQL agregado para Kardex:

```sql
ALTER TABLE movimientostock
ADD COLUMN MovStockSaldoAnterior DECIMAL(12,4) NULL AFTER MovStockSigno,
ADD COLUMN MovStockSaldoActual DECIMAL(12,4) NULL AFTER MovStockSaldoAnterior;
```

Indice recomendado:

```sql
CREATE INDEX idx_movstock_kardex
ON movimientostock (MovStockArticuloId, id_sucursal, MovStockFechaHora, MovStockId);
```

## 9. Tipos de movimientos de stock

Los movimientos actuales se agrupan asi:

Compras:

- `RECEPCION COMPRA`: entrada.
- `ANULACION COMPRA`: salida.
- `NC_COMPRA_DEV`: salida por devolucion/nota de credito.
- `ANULA_NC_COMPRA`: entrada por anulacion de nota.

Transferencias:

- `TRANSFERENCIA_SALIDA`: salida de sucursal origen.
- `TRANSFERENCIA_ENTRADA`: entrada en sucursal destino.

Ajustes:

- `AJUSTE_INV`: entrada o salida segun diferencia.
- `ANULACION_AJUSTE_INV`: reversa del ajuste.

Servicios:

- `REG. SERVICIO`: salida por repuesto usado en servicio.
- `ANULACION REG. SERVICIO`: entrada por anulacion del registro.

Insumos:

- `SALIDA INSUMO`: salida de insumo.
- `ANUL SALIDA INSUMO`: entrada por anulacion de salida de insumo.

## 10. Kardex

El Kardex esta dentro de:

`vistas/contenidos/reporte-movimientos-vista.php`

Configuracion:

`controladores/reportesControlador.php`

Modelo:

`modelos/reportesModelo.php`

Metodo:

```php
protected static function reporte_kardex_articulo_modelo($f)
```

El Kardex muestra:

- Fecha.
- Tipo de movimiento.
- Referencia.
- Entrada.
- Salida.
- Costo.
- Saldo anterior.
- Saldo actual.
- Usuario.

Por que exige articulo y sucursal:

- El Kardex es historico de un articulo concreto.
- El saldo depende de la sucursal.
- Si se mezclan sucursales, el saldo deja de ser defendible.
- Si se mezclan articulos, ya no es Kardex sino informe general de movimientos.

Las fechas pueden ser opcionales. Si no se indica fecha, se muestra todo el historial disponible.

## 11. Filtros del Kardex

Filtros principales:

- Sucursal: obligatorio.
- Articulo por ID o codigo exacto: obligatorio.
- Fecha desde: opcional.
- Fecha hasta: opcional.
- Naturaleza: opcional.
- Tipo de movimiento: opcional.

Naturaleza:

- Todos.
- Entradas.
- Salidas.
- Ajustes.
- Compras.
- Transferencias.
- Servicios.
- Insumos.

Importante: el saldo real se calcula con todos los movimientos del articulo/sucursal. Si se filtran solo entradas o solo salidas, el sistema muestra solo esas filas, pero el saldo corresponde al saldo real que quedo luego de cada movimiento.

Este comportamiento es correcto porque un Kardex no debe mentir el saldo.

## 12. Informes referenciales

Vista:

`vistas/contenidos/reporte-referenciales-vista.php`

Controlador:

`controladores/reportesControlador.php`

Modelo:

`modelos/reportesModelo.php`

Objetivo:

Unificar reportes de datos maestros en una sola pantalla.

Incluye:

- Proveedores.
- Clientes.
- Vehiculos.
- Sucursales.
- Articulos.
- Marcas.
- Categorias.
- Usuarios.

Por que se unifico:

- Evita muchas vistas repetidas.
- Facilita mantenimiento.
- Se agregan nuevos referenciales desde configuracion.
- Mantiene filtros, PDF y CSV en una sola logica.

Para agregar un nuevo referencial:

1. Crear consulta en `reportesModelo.php`.
2. Crear resumen si corresponde.
3. Agregar configuracion en `config_referenciales()` dentro de `reportesControlador.php`.
4. Agregar permiso en la lista de la vista si corresponde.

Ejemplo de configuracion:

```php
"marcas" => [
    "titulo" => "Marcas",
    "permiso" => "reportes.articulos.ver",
    "modelo" => "reporte_marcas_modelo",
    "resumen" => "resumen_marcas_modelo",
    "orientacion" => "P",
    "columnas" => [
        ["key" => "id_marcas", "label" => "ID"],
        ["key" => "mar_descri", "label" => "Marca"]
    ]
]
```

## 13. Informes de movimientos

Vista:

`vistas/contenidos/reporte-movimientos-vista.php`

Controlador:

`controladores/reportesControlador.php`

Modelo:

`modelos/reportesModelo.php`

Objetivo:

Centralizar informes operativos:

- Pedidos.
- Presupuestos de compra.
- Ordenes de compra.
- Compras.
- Libro de compras.
- Stock.
- Transferencias.
- Movimientos de stock.
- Kardex de articulo.
- Recepcion de servicios.
- Presupuesto de servicios.
- Ordenes de trabajo.
- Registro de servicios.

Por que se unifico:

- Reduce vistas antiguas.
- Unifica filtros, paginacion, graficos, PDF y CSV.
- Permite agregar un nuevo informe solo desde configuracion y modelo.

Para agregar un nuevo informe de movimientos:

1. Crear metodo en `reportesModelo.php`.
2. Agregar entrada en `config_movimientos()` de `reportesControlador.php`.
3. Definir columnas.
4. Definir permiso.
5. Definir campo fecha, estado, importe y entidad si aplica.
6. Si necesita filtro especial, agregarlo en la vista y pasarlo por `filtros_movimientos()`.

## 14. Graficos de informes

La vista de movimientos usa Chart.js local:

```html
<script src="<?= SERVERURL ?>vistas/js/chart.js"></script>
```

Los graficos se generan en JavaScript dentro de:

`vistas/contenidos/reporte-movimientos-vista.php`

Funciones importantes:

- `renderGraficos(data)`
- `crearGrafico(id, tipo, datos, opciones)`
- `datosGrafico(datos)`

Los datos vienen del controlador:

```php
"graficos" => $this->grafico_movimientos_desde_datos($datos, $config[$tipo])
```

Los graficos actuales son:

- Movimientos por periodo.
- Distribucion por estado.
- Top relacionado.

Por que no son editables:

- Son visualizacion de datos, no formulario.
- Evita que el usuario piense que puede modificar informacion desde el grafico.
- Mantiene el informe como consulta, no como mantenimiento.

## 15. Exportacion CSV

Hay dos exportaciones importantes:

- Referenciales.
- Movimientos.

Referenciales usa UTF-16LE para Excel:

```php
header('Content-Type: text/csv; charset=UTF-16LE');
echo "\xFF\xFE";
```

Por que:

- Excel suele abrir mal CSV UTF-8 con acentos.
- UTF-16LE con BOM es mas compatible con Excel en Windows.

Separador:

```text
sep=;
```

Esto indica a Excel que use punto y coma como separador.

## 16. Exportacion PDF

Los PDF se generan desde el controlador con HTML y mPDF:

```php
$this->imprimir_mpdf_html($html, "archivo.pdf", 'L');
```

Orientaciones:

- `P`: vertical.
- `L`: horizontal.

Se limita detalle en PDF cuando hay demasiados registros, y se recomienda CSV para detalle completo. Esto evita PDF pesados y problemas de memoria.

## 17. Eliminacion e inactivacion de referenciales

En varios referenciales no se puede borrar fisicamente un registro si ya fue usado por otra tabla.

Ejemplo:

Un proveedor usado en `compra_cabecera` no puede eliminarse por clave foranea.

Solucion aplicada:

- Si no tiene relaciones, se puede eliminar.
- Si tiene relaciones, se inactiva.
- Si ya esta inactivo, se informa sin lanzar error.

Por que:

- Protege historial.
- Evita romper compras, servicios o reportes antiguos.
- Mantiene integridad referencial.

Esto es defendible como regla profesional: los datos maestros usados historicamente no deben desaparecer.

## 18. Busquedas exactas por rendimiento

En informes de movimientos, algunos filtros usan busqueda exacta por ID o codigo.

Ejemplo:

- Articulo: ID o codigo exacto.
- Cliente: ID o documento exacto.
- Tecnico: ID o cedula exacta.

Por que no usar desplegables:

- Puede haber 30.000 o 50.000 articulos.
- Un desplegable seria lento y poco usable.
- Consultas con `LIKE` amplio pueden afectar rendimiento.

Por que exacto:

- Usa indices.
- Reduce carga.
- Evita resultados ambiguos.
- Es mas defendible en informes grandes.

## 19. Como modificar un informe existente

### Cambiar columnas

Archivo:

`controladores/reportesControlador.php`

Buscar:

```php
private function config_movimientos()
```

O:

```php
private function config_referenciales()
```

Modificar la lista:

```php
"columnas" => [
    ["key" => "campo_bd", "label" => "Texto", "tipo" => "numero"]
]
```

Tipos soportados:

- `fecha`
- `estado`
- `moneda`
- `numero`

### Cambiar consulta

Archivo:

`modelos/reportesModelo.php`

Buscar el metodo del reporte.

Ejemplo:

```php
reporte_kardex_articulo_modelo($f)
```

Modificar el SELECT, JOIN, WHERE u ORDER BY.

### Cambiar filtro visual

Archivo:

`vistas/contenidos/reporte-movimientos-vista.php`

Agregar el input/select en el formulario y luego:

1. Agregarlo a `sincronizar()`.
2. Agregarlo a `limpiarFiltros()`.
3. Agregarlo al listener que reinicia pagina.
4. Recibirlo en `filtros_movimientos()`.
5. Usarlo en el modelo.

## 20. Como agregar un movimiento nuevo de stock

Si aparece un nuevo modulo que afecta stock, no se debe hacer `UPDATE stock` manual mas `INSERT movimientostock` por separado.

Se debe usar:

```php
mainModel::registrar_movimiento_stock_modelo($pdo, [
    "id_sucursal" => $idSucursal,
    "tipo" => "NUEVO_TIPO",
    "id_articulo" => $idArticulo,
    "cantidad" => $cantidad,
    "precio_venta" => 0,
    "costo" => $costo,
    "usuario" => $_SESSION['id_str'],
    "signo" => 1,
    "referencia" => "REFERENCIA #".$id
]);
```

Si es salida:

```php
"signo" => -1
```

Si es entrada:

```php
"signo" => 1
```

Si es ajuste:

```php
"signo" => $diferencia > 0 ? 1 : -1
"cantidad" => abs($diferencia)
```

## 21. Transacciones

Cuando una accion afecta varias tablas, debe ejecutarse en una transaccion:

```php
$pdo->beginTransaction();

try {
    // guardar cabecera
    // guardar detalle
    // mover stock
    // actualizar estado

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
}
```

Por que:

- Si falla una parte, se revierte todo.
- Evita cabeceras sin detalle.
- Evita stock actualizado sin movimiento.
- Evita movimientos sin documento origen.

## 22. Modulos principales y archivos

### Referenciales

Clientes:

- Vista: `vistas/contenidos/cliente-*.php`
- AJAX: `ajax/clienteAjax.php`
- Controlador: `controladores/clienteControlador.php`
- Modelo: `modelos/clienteModelo.php`

Proveedores:

- Vista: `vistas/contenidos/proveedor-*.php`
- AJAX: `ajax/proveedorAjax.php`
- Controlador: `controladores/proveedorControlador.php`
- Modelo: `modelos/proveedorModelo.php`

Articulos:

- Vista: `vistas/contenidos/articulo-*.php`
- AJAX: `ajax/articuloAjax.php`
- Controlador: `controladores/articuloControlador.php`
- Modelo: `modelos/articuloModelo.php`

Usuarios/roles:

- Vista: `vistas/contenidos/usuario-*.php`, `rol-*.php`
- AJAX: `ajax/usuarioAjax.php`, `ajax/rolesAjax.php`
- Controlador: `controladores/usuarioControlador.php`, `controladores/rolesControlador.php`
- Modelo: `modelos/usuarioModelo.php`, `modelos/rolesModelo.php`

### Compras y stock

Compras:

- Vista: `factura-*`
- AJAX: `ajax/compraAjax.php`
- Controlador: `controladores/compraControlador.php`
- Modelo: `modelos/compraModelo.php`

Inventario:

- Vista: `inventario`, `inventario-buscar`
- Controlador: `controladores/inventarioControlador.php`
- Modelo: `modelos/inventarioModelo.php`

Transferencias:

- Vista: `transferencia-*`
- AJAX: `ajax/transferenciaAjax.php`
- Controlador: `controladores/transferenciaControlador.php`
- Modelo: `modelos/transferenciaModelo.php`

Notas:

- Vista: `notasCreDe-*`
- AJAX: `ajax/notasCreDeAjax.php`
- Controlador: `controladores/notasCreDeControlador.php`
- Modelo: `modelos/notasCreDeModelo.php`

### Servicios

Recepcion:

- Vista: `recepcionServicio-*`
- AJAX: `ajax/recepcionservicioAjax.php`
- Controlador: `controladores/recepcionservicioControlador.php`
- Modelo: `modelos/recepcionservicioModelo.php`

Diagnostico:

- Vista: `diagnostico-servicio-*`
- AJAX: `ajax/diagnosticoServicioAjax.php`
- Controlador: `controladores/diagnosticoServicioControlador.php`
- Modelo: `modelos/diagnosticoServicioModelo.php`

Presupuesto de servicio:

- Vista: `presupuesto-servicio-*`
- AJAX: `ajax/presupuestoServicioAjax.php`
- Controlador: `controladores/presupuestoservicioControlador.php`
- Modelo: `modelos/presupuestoservicioModelo.php`

Orden de trabajo:

- Vista: `ordenTrabajo-*`
- AJAX: `ajax/ordenTrabajoAjax.php`
- Controlador: `controladores/ordenTrabajoControlador.php`
- Modelo: `modelos/ordenTrabajoModelo.php`

Registro de servicio:

- Vista: `registro-servicio-*`
- AJAX: `ajax/registroServicioAjax.php`
- Controlador: `controladores/registroServicioControlador.php`
- Modelo: `modelos/registroServicioModelo.php`

## 23. Preguntas comunes de defensa

### Por que se usa MVC

Porque separa presentacion, reglas y base de datos. Esto facilita mantenimiento, pruebas y cambios.

### Por que se usa AJAX

Porque permite enviar formularios y consultar datos sin recargar toda la pantalla. Tambien permite respuestas JSON para vistas dinamicas.

### Por que no se borran registros usados

Porque romperia el historial. Si un proveedor tiene compras, debe quedar para auditoria. Por eso se inactiva.

### Por que Kardex exige articulo y sucursal

Porque el saldo de stock solo tiene sentido para un articulo especifico en una sucursal especifica.

### Por que guardar saldo anterior y actual

Porque permite trazabilidad inmediata y evita recalcular todo el historial cada vez. Tambien facilita auditoria.

### Por que se mantiene `stock` y `movimientostock`

`stock` responde cuanto hay ahora. `movimientostock` responde como se llego a ese saldo.

### Por que los informes unificados son mejores

Porque evitan duplicacion de vistas y centralizan filtros, columnas, PDF y CSV.

### Por que no usar desplegable de articulos

Porque puede haber demasiados articulos. Es mejor buscar por ID o codigo exacto para rendimiento.

### Por que CSV usa UTF-16LE

Porque Excel en Windows abre mejor acentos y caracteres especiales con BOM UTF-16LE.

## 24. Checklist antes de entregar

Antes de defender o hacer cambios:

1. Verificar que no haya errores de sintaxis:

```powershell
php -l archivo.php
```

2. Probar permisos con usuario normal y administrador.
3. Probar guardar, editar, anular e inactivar.
4. Probar informes con filtros vacios y con filtros especificos.
5. Probar CSV con acentos.
6. Probar PDF con varios registros.
7. Probar Kardex con:
   - solo articulo y sucursal
   - rango de fechas
   - filtro entrada
   - filtro salida
   - filtro ajuste
8. Confirmar que stock actual coincide con ultimo saldo del Kardex.

## 25. Regla de oro para cambios futuros

Si el cambio afecta stock:

- No actualizar `stock` manualmente.
- Usar `registrar_movimiento_stock_modelo()`.
- Hacerlo dentro de una transaccion si forma parte de una operacion mayor.

Si el cambio es un informe:

- Agregar configuracion en el controlador.
- Agregar consulta en el modelo.
- Evitar `LIKE` amplio sobre tablas grandes.
- Preferir filtros exactos por ID/codigo.

Si el cambio es un permiso:

- Validar en vista.
- Validar en controlador.
- Agregar permiso al rol correspondiente.

Si el cambio es una vista nueva:

- Crear archivo en `vistas/contenidos`.
- Agregar a `vistasModelo.php`.
- Agregar al menu.
- Crear AJAX/controlador/modelo si necesita datos.

## 26. Archivos mas importantes para estudiar

Para defender la programacion, estudiar primero:

1. `modelos/mainModel.php`
2. `modelos/vistasModelo.php`
3. `ajax/reportesAjax.php`
4. `controladores/reportesControlador.php`
5. `modelos/reportesModelo.php`
6. `vistas/contenidos/reporte-movimientos-vista.php`
7. `vistas/contenidos/reporte-referenciales-vista.php`
8. `controladores/compraControlador.php`
9. `modelos/compraModelo.php`
10. `modelos/registroServicioModelo.php`
11. `modelos/transferenciaModelo.php`
12. `controladores/inventarioControlador.php`

Con esos archivos se entiende el nucleo del sistema: rutas, permisos, informes, stock y trazabilidad.

## 27. Como leer el proyecto si se debe explicar desde cero

Para entender el desarrollo completo conviene leerlo por capas, no por carpetas al azar.

Orden recomendado:

1. `config/APP.php` y `config/SERVER.php`: URL base y datos de conexion.
2. `index.php`: punto inicial de carga.
3. `controladores/vistasControlador.php`: decide que vista mostrar.
4. `modelos/vistasModelo.php`: lista blanca de vistas permitidas.
5. `vistas/inc/`: layout, menu lateral, scripts comunes.
6. `modelos/mainModel.php`: funciones base compartidas.
7. Un modulo simple, por ejemplo cargos o clientes.
8. Un modulo complejo, por ejemplo compras o servicios.
9. Informes y Kardex.

Esta forma de lectura ayuda a defender la arquitectura porque muestra primero la estructura comun y despues los casos particulares.

## 28. Ciclo completo de una operacion CRUD

CRUD significa crear, consultar, actualizar y eliminar/inactivar.

En este proyecto el ciclo general es:

### Crear

1. La vista muestra formulario.
2. El formulario apunta a un archivo AJAX.
3. El AJAX carga el controlador.
4. El controlador valida permiso `modulo.crear`.
5. El controlador limpia datos con `mainModel::limpiar_string`.
6. El controlador valida campos obligatorios.
7. El modelo ejecuta `INSERT`.
8. El controlador responde JSON con alerta.

### Consultar/Listar

1. La vista llama al paginador del controlador o a un buscador.
2. El controlador valida permiso `modulo.ver`.
3. El modelo ejecuta `SELECT`.
4. El controlador arma tabla HTML o respuesta JSON.

### Actualizar

1. La vista recibe un ID cifrado en URL.
2. El controlador descifra el ID.
3. El sistema busca el registro actual.
4. El usuario modifica datos.
5. El controlador valida permiso `modulo.editar`.
6. El modelo ejecuta `UPDATE`.

### Eliminar/Inactivar

1. El usuario presiona eliminar.
2. El controlador valida permiso `modulo.eliminar`.
3. El modelo intenta eliminar o inactivar.
4. Si hay relaciones por clave foranea, se inactiva para proteger historial.

Esto se aplica en referenciales como clientes, proveedores, articulos, cargos, empleados, sucursales y vehiculos.

## 29. Login, sesiones y permisos

El login crea variables de sesion que despues usa el sistema para permisos, usuario y sucursal.

Variables importantes que aparecen en el codigo:

- `$_SESSION['id_str']`: ID del usuario conectado.
- `$_SESSION['nombre_str']`: nombre del usuario.
- `$_SESSION['apellido_str']`: apellido del usuario.
- `$_SESSION['nick_sucursal']`: sucursal activa.
- `$_SESSION['permisos']`: lista de permisos cargados.

El metodo:

```php
mainModel::cargarPermisosSesion($idUsuario)
```

consulta los permisos del rol del usuario y los guarda en sesion.

El metodo:

```php
mainModel::tienePermiso($permiso)
```

verifica si la clave existe dentro de `$_SESSION['permisos']`.

### Por que se usan permisos por clave

Porque es mas flexible que validar solo por tipo de usuario. Un rol puede tener permisos concretos como:

- `cliente.ver`
- `cliente.crear`
- `cliente.editar`
- `cliente.eliminar`
- `reportes.movimientos_stock.ver`

Esto permite asignar capacidades especificas sin cambiar codigo.

## 30. Navegacion y menu lateral

El menu lateral esta en:

`vistas/inc/navLateral.php`

Su responsabilidad es mostrar accesos segun permisos. Si el usuario no tiene permiso, el menu no deberia mostrar la opcion.

Regla importante:

Ocultar una opcion del menu mejora la experiencia, pero no reemplaza la validacion del controlador.

Si se agrega una nueva pantalla:

1. Agregar la ruta en `modelos/vistasModelo.php`.
2. Crear la vista en `vistas/contenidos`.
3. Agregar opcion en `navLateral.php`.
4. Proteger la vista con `mainModel::tienePermiso`.
5. Proteger el controlador/AJAX.

## 31. Modulo de usuarios y roles

Este modulo administra quienes pueden entrar y que acciones pueden realizar.

Archivos comunes:

- Vistas: `vistas/contenidos/usuario-nuevo-vista.php`, `usuario-lista-vista.php`, `rol-nuevo-vista.php`, `rol-permisos-vista.php`
- AJAX: `ajax/usuarioAjax.php`, `ajax/rolesAjax.php`
- Controladores: `controladores/usuarioControlador.php`, `controladores/rolesControlador.php`
- Modelos: `modelos/usuarioModelo.php`, `modelos/rolesModelo.php`

Tablas habituales:

- `usuarios`
- `roles`
- `permisos`
- `rol_permiso`
- `sucursales`

Operaciones importantes:

- Crear usuario.
- Editar datos de usuario.
- Cambiar estado.
- Asignar rol.
- Asignar sucursal.
- Crear roles.
- Asignar permisos a roles.

Defensa:

El modulo de roles evita hardcodear perfiles fijos. En vez de decir "administrador puede todo", se consulta la tabla de permisos y se decide dinamicamente.

## 32. Referenciales

Los referenciales son datos maestros. No representan una operacion comercial por si mismos, pero son usados por compras, stock, servicios e informes.

Principales referenciales:

- Clientes.
- Proveedores.
- Vehiculos.
- Articulos.
- Marcas.
- Categorias.
- Sucursales.
- Empleados.
- Cargos.
- Equipos de trabajo.
- Usuarios.

### Clientes

Archivos:

- `vistas/contenidos/cliente-*.php`
- `ajax/clienteAjax.php`
- `controladores/clienteControlador.php`
- `modelos/clienteModelo.php`

Usos:

- Recepcion de servicios.
- Vehiculos.
- Presupuestos de servicio.
- Ordenes de trabajo.
- Registro de servicios.
- Informes.

Regla:

Si el cliente ya fue usado en movimientos, no conviene borrarlo fisicamente. Se inactiva.

### Proveedores

Archivos:

- `vistas/contenidos/proveedor-*.php`
- `ajax/proveedorAjax.php`
- `controladores/proveedorControlador.php`
- `modelos/proveedorModelo.php`

Usos:

- Presupuestos de compra.
- Ordenes de compra.
- Compras.
- Libro de compras.
- Notas de credito/debito.
- Informes.

Regla:

Si un proveedor tiene compras relacionadas, la base de datos impide borrarlo por clave foranea. La solucion correcta es inactivar.

### Articulos

Archivos:

- `vistas/contenidos/articulo-*.php`
- `ajax/articuloAjax.php`
- `controladores/articuloControlador.php`
- `modelos/articuloModelo.php`

Usos:

- Pedido de compra.
- Presupuesto de compra.
- Orden de compra.
- Compra.
- Stock.
- Transferencia.
- Servicio.
- Kardex.

Regla:

Un articulo usado historicamente no debe borrarse porque romperia stock, compras o servicios. Se inactiva.

### Vehiculos

Archivos:

- `vistas/contenidos/vehiculo-*.php`
- `ajax/vehiculoAjax.php`
- `controladores/vehiculoControlador.php`
- `modelos/vehiculoModelo.php`

Usos:

- Recepcion de servicios.
- Diagnostico.
- Orden de trabajo.
- Registro de servicio.
- Reclamos.

Regla:

El vehiculo queda vinculado al cliente y al historial del taller.

## 33. Compras: flujo general

El modulo de compras no es una sola pantalla; es un circuito.

Flujo normal:

1. Pedido de compra.
2. Presupuesto de compra.
3. Orden de compra.
4. Recepcion/compra.
5. Libro de compras.
6. Cuentas a pagar si corresponde.
7. Movimiento de stock.

### Pedido de compra

Objetivo:

Registrar necesidad de articulos.

Tablas frecuentes:

- `pedido_cabecera`
- `pedido_detalle`
- `usuarios`
- `sucursales`
- `articulos`

### Presupuesto de compra

Objetivo:

Registrar propuesta de proveedor con cantidades, precios y total.

Tablas frecuentes:

- `presupuesto_compra`
- `presupuesto_detalle`
- `proveedores`
- `usuarios`
- `sucursales`
- `articulos`

### Orden de compra

Objetivo:

Formalizar la compra aprobada al proveedor.

Tablas frecuentes:

- `orden_compra`
- `orden_compra_detalle`
- `presupuesto_compra`
- `proveedores`
- `usuarios`
- `sucursales`

### Compra / factura

Archivos principales:

- Vista: `factura-nuevo`, `factura-lista`, `factura-buscar`
- AJAX: `ajax/compraAjax.php`
- Controlador: `controladores/compraControlador.php`
- Modelo: `modelos/compraModelo.php`

Tablas frecuentes:

- `compra_cabecera`
- `compra_detalle`
- `proveedores`
- `usuarios`
- `sucursales`
- `libro_compra`
- `cuentas_a_pagar`
- `stock`
- `movimientostock`

Cuando se registra una compra:

1. Se guarda cabecera.
2. Se guarda detalle.
3. Se registra relacion articulo-proveedor.
4. Se registra movimiento `RECEPCION COMPRA`.
5. Se actualiza stock por medio de `registrar_movimiento_stock_modelo`.
6. Se actualiza orden de compra si corresponde.
7. Se genera libro de compras.
8. Se generan cuentas a pagar si corresponde.

Cuando se anula una compra:

1. Se marca cabecera como anulada.
2. Se revisan detalles.
3. Se registra movimiento `ANULACION COMPRA`.
4. Se descuenta stock.
5. Se revierte pendiente de orden de compra.
6. Se anula libro de compras y cuentas asociadas.

Defensa:

La anulacion no borra la compra. Cambia estado y registra movimientos inversos para conservar auditoria.

## 34. Libro de compras y notas

### Libro de compras

Representa el registro fiscal/contable de comprobantes de compra.

Tablas:

- `libro_compra`
- `compra_cabecera`
- `proveedores`
- `sucursales`

Se genera desde la compra y tambien puede relacionarse con notas.

### Notas de credito/debito

Archivos:

- Vista: `notasCreDe-nuevo`, `notasCreDe-buscar`
- AJAX: `ajax/notasCreDeAjax.php`
- Controlador: `controladores/notasCreDeControlador.php`
- Modelo: `modelos/notasCreDeModelo.php`

Si una nota de credito implica devolucion de mercaderia:

1. Se valida stock disponible.
2. Se registra movimiento `NC_COMPRA_DEV`.
3. Se descuenta stock.

Si se anula la nota:

1. Se registra movimiento `ANULA_NC_COMPRA`.
2. Se devuelve stock.
3. Se marca documento como anulado.

## 35. Inventario y ajustes

Archivos:

- Vista: `inventario`, `inventario-buscar`
- Controlador: `controladores/inventarioControlador.php`
- Modelo: `modelos/inventarioModelo.php`

Objetivo:

Comparar stock teorico con stock fisico y registrar diferencias.

Tablas frecuentes:

- `ajuste_inventario`
- `ajuste_inventario_detalle`
- `stock`
- `movimientostock`
- `articulos`
- `sucursales`
- `usuarios`

Regla:

Si la diferencia es positiva, entra stock. Si es negativa, sale stock.

Ejemplo:

```php
$signo = $cantidad > 0 ? 1 : -1;
$cantidadMovimiento = abs($cantidad);
```

Tipos:

- `AJUSTE_INV`
- `ANULACION_AJUSTE_INV`

Defensa:

El ajuste no modifica directamente sin trazabilidad. Cada diferencia genera movimiento para que el Kardex explique por que cambio el stock.

## 36. Transferencias de stock

Archivos:

- Vista: `transferencia-nuevo`, `transferencia-historial`, `transferencia-recibir`
- AJAX: `ajax/transferenciaAjax.php`
- Controlador: `controladores/transferenciaControlador.php`
- Modelo: `modelos/transferenciaModelo.php`

Objetivo:

Mover stock de una sucursal origen a una sucursal destino.

Tablas frecuentes:

- `transferencia_stock`
- `transferencia_stock_detalle`
- `stock`
- `movimientostock`
- `sucursales`
- `nota_remision`

Flujo:

1. Se crea cabecera de transferencia.
2. Se valida stock en sucursal origen.
3. Se guarda detalle.
4. Se registra `TRANSFERENCIA_SALIDA` en origen.
5. Al recibir, se registra `TRANSFERENCIA_ENTRADA` en destino.
6. Se actualiza estado: en transito, recibido o recibido parcial.

Defensa:

La transferencia tiene dos movimientos porque son dos hechos de stock distintos: salida del origen y entrada al destino.

## 37. Servicios: flujo general

El modulo de servicios tambien es un circuito:

1. Recepcion del vehiculo.
2. Diagnostico.
3. Presupuesto de servicio.
4. Orden de trabajo.
5. Registro de servicio.
6. Reclamo si corresponde.

### Recepcion de servicio

Archivos:

- Vista: `recepcionServicio-nuevo`, `recepcionServicio-buscar`
- AJAX: `ajax/recepcionservicioAjax.php`
- Controlador: `controladores/recepcionservicioControlador.php`
- Modelo: `modelos/recepcionservicioModelo.php`

Tablas frecuentes:

- `recepcion_servicio`
- `clientes`
- `vehiculos`
- `usuarios`
- `sucursales`

Objetivo:

Registrar ingreso del vehiculo, kilometraje, combustible, prioridad, problema y observaciones.

### Diagnostico

Archivos:

- Vista: `diagnostico-servicio-nuevo`, `diagnostico-servicio-buscar`
- AJAX: `ajax/diagnosticoServicioAjax.php`
- Controlador: `controladores/diagnosticoServicioControlador.php`
- Modelo: `modelos/diagnosticoServicioModelo.php`

Objetivo:

Registrar revision tecnica y determinar trabajos/repuestos necesarios.

### Presupuesto de servicio

Archivos:

- Vista: `presupuesto-servicio-nuevo`, `presupuesto-servicio-lista`, `presupuesto-servicio-buscar`
- AJAX: `ajax/presupuestoServicioAjax.php`
- Controlador: `controladores/presupuestoservicioControlador.php`
- Modelo: `modelos/presupuestoservicioModelo.php`

Tablas frecuentes:

- `presupuesto_servicio`
- `presupuesto_detalleservicio`
- `diagnostico_servicio`
- `clientes`
- `vehiculos`
- `articulos`
- `sucursales`
- `usuarios`

Objetivo:

Calcular y registrar costo estimado de repuestos/servicios.

### Orden de trabajo

Archivos:

- Vista: `ordenTrabajo-lista`, `ordenTrabajo-nuevo`, `ordenTrabajo-asignar`, `ordenTrabajo-buscar`
- AJAX: `ajax/ordenTrabajoAjax.php`
- Controlador: `controladores/ordenTrabajoControlador.php`
- Modelo: `modelos/ordenTrabajoModelo.php`

Tablas frecuentes:

- `orden_trabajo`
- `orden_trabajo_detalle`
- `presupuesto_servicio`
- `equipo_trabajo`
- `empleados`
- `clientes`
- `vehiculos`
- `sucursales`

Objetivo:

Formalizar el trabajo que se realizara sobre el vehiculo.

### Registro de servicio

Archivos:

- Vista: `registro-servicio-nuevo`, `registro-servicio-lista`, `registro-servicio-buscar`
- AJAX: `ajax/registroServicioAjax.php`
- Controlador: `controladores/registroServicioControlador.php`
- Modelo: `modelos/registroServicioModelo.php`

Tablas frecuentes:

- `registro_servicio`
- `registro_servicio_detalle`
- `orden_trabajo`
- `orden_trabajo_detalle`
- `stock`
- `movimientostock`
- `clientes`
- `vehiculos`
- `usuarios`
- `sucursales`

Cuando se registra un servicio:

1. Se valida orden de trabajo activa.
2. Se crea cabecera de registro.
3. Se copia detalle desde orden de trabajo.
4. Se descuentan productos del stock.
5. Se registra movimiento `REG. SERVICIO`.
6. Se cierra la orden de trabajo.
7. Se actualiza recepcion/reclamo si corresponde.

Cuando se anula:

1. Se valida estado.
2. Se registra movimiento inverso `ANULACION REG. SERVICIO`.
3. Se devuelve stock.
4. Se reabre la orden de trabajo.

## 38. Reclamos de servicio

Objetivo:

Registrar una inconformidad o retorno posterior a un servicio.

Tablas frecuentes:

- `reclamo_servicio`
- `recepcion_servicio`
- `registro_servicio`
- `clientes`
- `vehiculos`

Defensa:

El reclamo no borra ni reemplaza el servicio original. Lo referencia para mantener trazabilidad del problema y su resolucion.

## 39. Informes: arquitectura tecnica detallada

Los informes unificados se programaron con configuracion para evitar una vista por cada informe.

Vista:

- `reporte-referenciales-vista.php`
- `reporte-movimientos-vista.php`

Controlador:

- `config_referenciales()`
- `config_movimientos()`
- `reporte_referenciales_controlador()`
- `reporte_movimientos_unificado_controlador()`
- `imprimir_reporte_*`
- `exportar_reporte_*`

Modelo:

- Metodos `reporte_*_modelo`.

### Como funciona la previsualizacion

1. La vista envia `modulo=referenciales` o `modulo=movimientos_unificado`.
2. `ajax/reportesAjax.php` llama al controlador.
3. El controlador identifica el tipo de informe.
4. Busca la configuracion del tipo.
5. Valida permiso.
6. Prepara filtros.
7. Llama al modelo.
8. Calcula resumen y graficos.
9. Devuelve JSON.
10. La vista renderiza tabla, tarjetas y graficos.

### Como funciona PDF

1. La vista sincroniza filtros a un formulario oculto.
2. Envia `accion=imprimir_reporte_movimientos_unificado` o equivalente.
3. El controlador vuelve a consultar los mismos datos.
4. Arma HTML.
5. Genera PDF con mPDF.

### Como funciona CSV

1. La vista sincroniza filtros a formulario oculto.
2. Envia `accion=exportar_reporte_movimientos_csv` o equivalente.
3. El controlador vuelve a consultar los mismos datos.
4. Escribe encabezados.
5. Escribe filas.
6. El navegador descarga el archivo.

Defensa:

Previsualizar, PDF y CSV usan la misma fuente de datos. Cambia la salida, no la consulta base.

## 40. Tablas consultadas por informes

### Referenciales

| Informe | Tablas |
|---|---|
| Articulos | `articulos`, `categorias`, `marcas`, `articulo_proveedor`, `proveedores`, `unidad_medida`, `tipo_impuesto` |
| Proveedores | `proveedores`, `ciudades` |
| Clientes | `clientes`, `ciudades` |
| Vehiculos | `vehiculos`, `clientes`, `modelo_auto`, `marcas` |
| Sucursales | `sucursales`, `empresa` |
| Marcas | `marcas` |
| Categorias | `categorias` |
| Usuarios | `usuarios`, `sucursales` |

### Movimientos

| Informe | Tablas |
|---|---|
| Pedidos de Compra | `pedido_cabecera`, `pedido_detalle`, `usuarios`, `sucursales` |
| Presupuestos de Compra | `presupuesto_compra`, `presupuesto_detalle`, `proveedores`, `usuarios`, `sucursales` |
| Ordenes de Compra | `orden_compra`, `orden_compra_detalle`, `proveedores`, `usuarios`, `sucursales` |
| Compras | `compra_cabecera`, `compra_detalle`, `proveedores`, `usuarios`, `sucursales` |
| Libro de Compras | `libro_compra`, `sucursales` |
| Stock | `articulos`, `stock`, `categorias`, `marcas`, `articulo_proveedor`, `proveedores`, `unidad_medida`, `sucursales` |
| Transferencias | `transferencia_stock`, `transferencia_stock_detalle`, `sucursales`, `nota_remision` |
| Movimientos de Stock | `movimientostock`, `sucursales`, `articulos`, `usuarios` |
| Kardex de Articulo | `movimientostock`, `sucursales`, `articulos`, `usuarios` |
| Recepcion de Servicios | `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `sucursales` |
| Presupuestos de Servicios | `presupuesto_servicio`, `presupuesto_detalleservicio`, `diagnostico_servicio`, `recepcion_servicio`, `usuarios`, `sucursales`, `clientes`, `vehiculos`, `modelo_auto`, `marcas` |
| Ordenes de Trabajo | `orden_trabajo`, `orden_trabajo_detalle`, `presupuesto_servicio`, `diagnostico_servicio`, `recepcion_servicio`, `sucursales`, `usuarios`, `equipo_trabajo`, `clientes`, `vehiculos`, `modelo_auto`, `marcas` |
| Registro de Servicios | `registro_servicio`, `registro_servicio_detalle`, `orden_trabajo`, `sucursales`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `equipo_trabajo`, `empleados` |

## 41. Manejo de estados

Muchos modulos usan estados numericos o textos. El informe transforma esos valores en etiquetas legibles.

Ejemplos:

- `0`: anulado o inactivo.
- `1`: activo, pendiente o registrado segun modulo.
- `2`: procesado, aprobado o facturado segun modulo.
- `3`: finalizado, reclamo o pendiente completar segun modulo.

En transferencias se usan textos:

- `en_transito`
- `recibido`
- `recibido_parcial`
- `anulado`

Por eso cada informe define su mapa de estados en el controlador.

Defensa:

No se puede interpretar un estado solo por el numero sin saber el modulo. Por eso se usa configuracion especifica por informe.

## 42. Validaciones importantes

Validaciones repetidas:

- Sesion activa.
- Permiso de acceso.
- Campos obligatorios.
- IDs existentes.
- Sucursal correcta.
- Estado valido para la accion.
- Stock suficiente para salidas.
- No duplicar operaciones ya registradas.

Ejemplos:

- No registrar servicio si la orden no esta activa.
- No anular compra si no tiene permiso.
- No transferir si origen no tiene stock suficiente.
- No borrar proveedor usado en compra.
- No generar Kardex sin articulo y sucursal.

## 43. Rendimiento y decisiones tecnicas

### Por que evitar texto libre en informes grandes

Porque un `LIKE '%texto%'` en tablas grandes puede recorrer muchos registros y afectar rendimiento.

Por eso en informes se prefiere:

- ID exacto.
- Codigo exacto.
- Documento exacto.
- Cedula exacta.
- Rangos de fecha.
- Estados.
- Sucursal.

### Por que no usar desplegable de articulos

Porque puede haber miles de articulos. Un select con 30.000 o 50.000 opciones es lento y dificil de usar.

### Por que paginar

Porque cargar todos los registros en pantalla puede hacer lenta la vista y el navegador.

### Por que limitar PDF

Porque PDF con miles de filas puede consumir mucha memoria. Para detalle masivo se usa CSV.

## 44. Como responder si piden un cambio en defensa

### Si piden agregar una columna a un informe

1. Revisar si el dato ya viene en el SELECT del modelo.
2. Si no viene, agregarlo al SELECT.
3. Agregar columna en `config_movimientos()` o `config_referenciales()`.
4. Validar PDF y CSV.

### Si piden agregar un filtro

1. Agregar input en la vista.
2. Enviar el valor por AJAX.
3. Recibirlo en `filtros_movimientos()` o `filtros_referenciales()`.
4. Aplicarlo en el modelo.
5. Agregarlo en formularios ocultos PDF/CSV.

### Si piden cambiar un estado

1. Revisar donde se guarda el estado.
2. Revisar el mapa de etiquetas en el controlador.
3. Cambiar la regla en el modulo origen si afecta negocio.
4. Cambiar la etiqueta si solo afecta presentacion.

### Si piden que un informe tenga detalle

Hay dos caminos:

- Detalle por documento: abrir filas o exportar detalle asociado.
- Kardex: cuando el detalle es de stock historico por articulo.

No todo informe general debe convertirse en detalle infinito. Se puede justificar separando resumen general y detalle especifico.

## 45. Posibles errores y como diagnosticarlos

### Error de permiso

Sintoma:

- Acceso no autorizado.
- Boton no aparece.
- AJAX devuelve error.

Revisar:

- Permiso en vista.
- Permiso en controlador.
- Permiso asignado al rol.
- Sesion cargada.

### Error 404 en vista

Revisar:

- Que la vista exista en `vistas/contenidos`.
- Que este agregada en `modelos/vistasModelo.php`.

### Error de clave foranea al eliminar

Significa que el registro esta siendo usado por otra tabla.

Solucion:

- Inactivar en vez de borrar.

### CSV con acentos incorrectos

Revisar:

- Cabecera `Content-Type`.
- BOM.
- Codificacion usada.
- Separador `sep=;`.

### Kardex sin resultados

Revisar:

- Articulo correcto por ID o codigo exacto.
- Sucursal correcta.
- Fechas.
- Naturaleza o tipo demasiado restrictivo.
- Existencia de movimientos en `movimientostock`.

### Stock no coincide

Revisar:

- Ultimos movimientos en `movimientostock`.
- `stockDisponible`.
- Movimientos manuales antiguos sin saldo.
- Anulaciones.
- Sucursal.

## 46. Consultas utiles para defensa

Ver stock actual:

```sql
SELECT s.id_sucursal, s.id_articulo, s.stockDisponible
FROM stock s
WHERE s.id_articulo = 1
  AND s.id_sucursal = 1;
```

Ver movimientos de un articulo:

```sql
SELECT MovStockFechaHora, TipoMovStockId, MovStockCantidad,
       MovStockSigno, MovStockSaldoAnterior, MovStockSaldoActual,
       MovStockReferencia
FROM movimientostock
WHERE MovStockArticuloId = 1
  AND id_sucursal = 1
ORDER BY MovStockFechaHora, MovStockId;
```

Calcular saldo por movimientos:

```sql
SELECT COALESCE(SUM(MovStockCantidad * MovStockSigno), 0) AS saldo
FROM movimientostock
WHERE MovStockArticuloId = 1
  AND id_sucursal = 1;
```

Ver permisos de un usuario:

```sql
SELECT p.clave
FROM permisos p
INNER JOIN rol_permiso rp ON rp.id_permiso = p.id_permiso
INNER JOIN usuarios u ON u.id_rol = rp.id_rol
WHERE u.id_usuario = 1;
```

## 47. Que partes defender como decisiones profesionales

Decisiones fuertes del proyecto:

- Validacion de permisos en vista y controlador.
- Lista blanca de vistas.
- Soft delete/inactivacion para datos usados.
- Informes unificados para evitar duplicacion.
- Filtros exactos para tablas grandes.
- Kardex separado del informe general.
- Movimiento de stock centralizado.
- Uso de transacciones en operaciones compuestas.
- CSV compatible con Excel.
- PDF limitado para evitar problemas de rendimiento.

Estas decisiones se pueden defender como mejoras de seguridad, integridad, rendimiento y mantenibilidad.

