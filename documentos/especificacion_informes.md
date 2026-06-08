# Especificaciones de Casos de Uso: Informes del Sistema

## Consideraciones Generales

El modulo de informes permite consultar informacion del sistema sin modificar los registros de origen. La implementacion actual utiliza vistas unificadas:

- Informes Referenciales: una sola vista para datos maestros.
- Informes de Movimientos: una sola vista para compras, stock y servicios.

Cada informe se documenta de forma individual para conservar claridad funcional, pero su ejecucion se realiza dentro de la vista unificada correspondiente.

### Reglas Generales

- El usuario debe iniciar sesion.
- El sistema valida permisos antes de mostrar la vista.
- El sistema valida permisos nuevamente antes de previsualizar, exportar CSV o generar PDF.
- La previsualizacion no se ejecuta automaticamente al abrir la vista.
- El usuario debe presionar Previsualizar para consultar datos.
- El usuario puede limpiar los filtros para volver a los valores iniciales y ocultar resultados previos.
- En informes de movimientos, el sistema evita busquedas de texto libre sobre el detalle completo para no degradar el rendimiento. Se utilizan filtros exactos y contextuales: articulo acepta ID o codigo, cliente acepta ID o documento y tecnico acepta ID o cedula.
- Los estados se muestran con descripcion textual.
- Los importes y cantidades se muestran con separador de miles.
- El CSV se genera con separador punto y coma. En informes referenciales se usa UTF-16LE con BOM para compatibilidad con Excel y acentos; en informes de movimientos se usa UTF-8 con BOM.
- La informacion consultada no se modifica.
- Los documentos anulados no se eliminan de los informes. Se muestran mediante su estado. Cuando la anulacion pertenece a movimientos criticos de compras o servicios, el motivo, usuario y fecha quedan registrados en `anulacion_auditoria`.

### Auditoria de Anulaciones e Informes

La tabla `anulacion_auditoria` se utiliza como respaldo de trazabilidad para anulaciones transaccionales. Los informes principales consultan las tablas operativas del movimiento y muestran el estado del documento. La auditoria central conserva la justificacion administrativa de la anulacion.

Movimientos con auditoria de anulacion:

| Informe relacionado | Tabla operativa | Tabla de auditoria |
|---|---|---|
| Pedidos de Compra | `pedido_cabecera` | `anulacion_auditoria` |
| Presupuestos de Compra | `presupuesto_compra` | `anulacion_auditoria` |
| Ordenes de Compra | `orden_compra` | `anulacion_auditoria` |
| Compras | `compra_cabecera` | `anulacion_auditoria` |
| Notas de Remision | `nota_remision` | `anulacion_auditoria` |
| Notas de Credito/Debito de Compra | `nota_compra` | `anulacion_auditoria` |
| Ajustes de Inventario | `ajuste_inventario` | `anulacion_auditoria` |
| Recepcion de Servicios | `recepcion_servicio` | `anulacion_auditoria` |
| Diagnosticos de Servicios | `diagnostico_servicio` | `anulacion_auditoria` |
| Presupuestos de Servicios | `presupuesto_servicio` | `anulacion_auditoria` |
| Ordenes de Trabajo | `orden_trabajo` | `anulacion_auditoria` |
| Registro de Servicios | `registro_servicio` | `anulacion_auditoria` |
| Salida de Insumos | `salida_insumo` | `anulacion_auditoria` |
| Reclamos de Servicios | `reclamo_servicio` | `anulacion_auditoria` |

La auditoria no modifica el calculo del informe; sirve para explicar por que un documento quedo anulado. Si se requiere auditar el motivo exacto, se consulta `anulacion_auditoria` por `tabla_afectada` e `id_registro`.

### Fuente de Datos por Accion

Las acciones Previsualizar, Generar PDF y Exportar CSV consultan las mismas tablas base del informe seleccionado. La diferencia entre ellas es solo la salida:

- Previsualizar: devuelve datos en formato JSON y los muestra en pantalla.
- Generar PDF: toma los mismos datos filtrados y los presenta en un documento PDF.
- Exportar CSV: toma los mismos datos filtrados y los entrega en archivo CSV para planilla electronica.

Por lo tanto, las tablas consultadas no cambian entre Previsualizar, PDF y CSV. Si el usuario aplica filtros, esos filtros se aplican en las tres acciones.

#### Informes Referenciales

| Informe | Tablas consultadas |
|---|---|
| Articulos | `articulos`, `categorias`, `marcas`, `articulo_proveedor`, `proveedores`, `unidad_medida`, `tipo_impuesto` |
| Proveedores | `proveedores`, `ciudades` |
| Clientes | `clientes`, `ciudades` |
| Vehiculos | `vehiculos`, `clientes`, `modelo_auto`, `marcas` |
| Sucursales | `sucursales`, `empresa` |
| Marcas | `marcas` |
| Categorias | `categorias` |
| Usuarios | `usuarios`, `sucursales` |

#### Informes de Movimientos

| Informe | Tablas consultadas |
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

### Flujo Detallado de Generacion por Informe

Esta seccion describe el comportamiento real de la interfaz unificada. En todos los casos la accion Previsualizar, Generar PDF y Exportar CSV utiliza los mismos filtros seleccionados por el usuario. La diferencia es el formato de salida.

Los informes de movimientos generales se ordenan de forma descendente por fecha e identificador, mostrando primero los registros mas recientes. El Kardex de Articulo es la excepcion: se ordena de forma ascendente por fecha e identificador para conservar la lectura cronologica del saldo.

La interfaz de Informes de Movimientos permite seleccionar Vista Resumen o Vista Detallado. La Vista Resumen muestra una fila por documento o movimiento principal. La Vista Detallado, cuando corresponde, muestra una fila por articulo o linea interna del documento, permitiendo verificar que articulos componen cada compra, pedido, presupuesto, orden o servicio.

#### Informe Referencial: Articulos

Generar

El sistema valida permiso `reportes.articulos.ver`.
El sistema muestra la vista Informes Referenciales.
El sistema muestra el tipo Articulos si el usuario tiene permiso.
El sistema consulta categorias para el filtro de categoria. Tabla consultada: `categorias`.
El sistema muestra filtros de tipo referencial, estado, busqueda y categoria.
El usuario selecciona Articulos.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.articulos.ver`.
El sistema consulta articulos segun los filtros.
Tablas consultadas: `articulos`, `categorias`, `marcas`, `articulo_proveedor`, `proveedores`, `unidad_medida`, `tipo_impuesto`.
El sistema carga resumen y datos dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
El usuario presiona Generar PDF.
El sistema envia los filtros al generador del reporte.
El sistema valida nuevamente permiso `reportes.articulos.ver`.
El sistema consulta nuevamente articulos segun los filtros.
El sistema genera el PDF del informe.
El usuario puede presionar Exportar CSV.
El sistema valida nuevamente permiso `reportes.articulos.ver`.
El sistema consulta nuevamente articulos segun los filtros.
El sistema genera CSV con separador punto y coma.

#### Informe Referencial: Proveedores

Generar

El sistema valida permiso `reportes.proveedores.ver`.
El sistema muestra la vista Informes Referenciales.
El sistema muestra filtros de tipo referencial, estado y busqueda.
El usuario selecciona Proveedores.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.proveedores.ver`.
El sistema consulta proveedores segun los filtros.
Tablas consultadas: `proveedores`, `ciudades`.
El sistema carga resumen y datos dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe Referencial: Clientes

Generar

El sistema valida permiso `reportes.clientes.ver`.
El sistema muestra la vista Informes Referenciales.
El sistema muestra filtros de tipo referencial, estado y busqueda.
El usuario selecciona Clientes.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.clientes.ver`.
El sistema consulta clientes segun los filtros.
Tablas consultadas: `clientes`, `ciudades`.
El sistema carga resumen y datos dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe Referencial: Vehiculos

Generar

El sistema valida permiso `reportes.vehiculos.ver`.
El sistema muestra la vista Informes Referenciales.
El sistema muestra filtros de tipo referencial, estado y busqueda.
El usuario selecciona Vehiculos.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.vehiculos.ver`.
El sistema consulta vehiculos segun los filtros.
Tablas consultadas: `vehiculos`, `clientes`, `modelo_auto`, `marcas`.
El sistema carga resumen y datos dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe Referencial: Sucursales

Generar

El sistema valida permiso `reportes.sucursales.ver`.
El sistema muestra la vista Informes Referenciales.
El sistema muestra filtros de tipo referencial, estado y busqueda.
El usuario selecciona Sucursales.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.sucursales.ver`.
El sistema consulta sucursales segun los filtros.
Tablas consultadas: `sucursales`, `empresa`.
El sistema carga resumen y datos dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe Referencial: Marcas

Generar

El sistema valida permiso `reportes.articulos.ver`.
El sistema muestra la vista Informes Referenciales.
El sistema muestra filtros de tipo referencial y busqueda.
El usuario selecciona Marcas.
El usuario ingresa filtro opcional.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.articulos.ver`.
El sistema consulta marcas segun los filtros.
Tabla consultada: `marcas`.
El sistema carga datos dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta la misma tabla.

#### Informe Referencial: Categorias

Generar

El sistema valida permiso `reportes.articulos.ver`.
El sistema muestra la vista Informes Referenciales.
El sistema muestra filtros de tipo referencial y busqueda.
El usuario selecciona Categorias.
El usuario ingresa filtro opcional.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.articulos.ver`.
El sistema consulta categorias segun los filtros.
Tabla consultada: `categorias`.
El sistema carga datos dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta la misma tabla.

#### Informe Referencial: Usuarios

Generar

El sistema valida permiso `usuarios.ver`.
El sistema muestra la vista Informes Referenciales.
El sistema muestra filtros de tipo referencial, estado y busqueda.
El usuario selecciona Usuarios.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `usuarios.ver`.
El sistema consulta usuarios segun los filtros.
Tablas consultadas: `usuarios`, `sucursales`.
El sistema carga resumen y datos dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe de Movimientos: Pedidos de Compra

Generar

El sistema valida permiso `reportes.pedidos.ver`.
El sistema consulta sucursales para el filtro. Tabla consultada: `sucursales`.
El sistema consulta proveedores para filtros de informes que lo requieran. Tabla consultada: `proveedores`.
El sistema muestra filtros de vista, fecha desde, fecha hasta, estado, sucursal, articulo por ID o codigo exacto y cantidad de registros.
El usuario selecciona Pedidos de Compra.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.pedidos.ver`.
El sistema consulta pedidos de compra segun los filtros.
Tablas consultadas en Vista Resumen: `pedido_cabecera`, `pedido_detalle`, `usuarios`, `sucursales`.
Tablas consultadas en Vista Detallado: `pedido_cabecera`, `pedido_detalle`, `articulos`, `usuarios`, `sucursales`.
El sistema carga resumen, graficos y datos dentro de la grilla. En Vista Detallado, cada fila representa un articulo solicitado.
El sistema habilita los botones PDF y CSV si existen registros.
El usuario presiona Generar PDF.
El sistema envia los filtros al generador del reporte.
El sistema valida nuevamente permiso `reportes.pedidos.ver`.
El sistema consulta nuevamente pedidos de compra segun los filtros.
El sistema genera el PDF del informe.
El usuario puede presionar Exportar CSV.
El sistema consulta nuevamente pedidos de compra segun los filtros y genera el CSV.

#### Informe de Movimientos: Presupuestos de Compra

Generar

El sistema valida permiso `reportes.presupuestos_compra.ver`.
El sistema consulta sucursales para el filtro. Tabla consultada: `sucursales`.
El sistema consulta proveedores para el filtro. Tabla consultada: `proveedores`.
El sistema muestra filtros de vista, fecha desde, fecha hasta, estado, sucursal, proveedor, articulo por ID o codigo exacto y cantidad de registros.
El usuario selecciona Presupuestos de Compra.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.presupuestos_compra.ver`.
El sistema consulta presupuestos de compra segun los filtros.
Tablas consultadas en Vista Resumen: `presupuesto_compra`, `presupuesto_detalle`, `proveedores`, `usuarios`, `sucursales`.
Tablas consultadas en Vista Detallado: `presupuesto_compra`, `presupuesto_detalle`, `articulos`, `proveedores`, `sucursales`.
El sistema carga resumen, graficos y datos dentro de la grilla. En Vista Detallado, cada fila representa un articulo presupuestado con cantidad, precio y subtotal.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe de Movimientos: Ordenes de Compra

Generar

El sistema valida permiso `reportes.ordenes_compra.ver`.
El sistema consulta sucursales para el filtro. Tabla consultada: `sucursales`.
El sistema consulta proveedores para el filtro. Tabla consultada: `proveedores`.
El sistema muestra filtros de vista, fecha desde, fecha hasta, estado, sucursal, proveedor, articulo por ID o codigo exacto y cantidad de registros.
El usuario selecciona Ordenes de Compra.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.ordenes_compra.ver`.
El sistema consulta ordenes de compra segun los filtros.
Tablas consultadas en Vista Resumen: `orden_compra`, `orden_compra_detalle`, `proveedores`, `usuarios`, `sucursales`.
Tablas consultadas en Vista Detallado: `orden_compra`, `orden_compra_detalle`, `articulos`, `proveedores`, `sucursales`.
El sistema carga resumen, graficos y datos dentro de la grilla. En Vista Detallado, cada fila representa un articulo ordenado con cantidad, pendiente, precio y subtotal.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe de Movimientos: Compras

Generar

El sistema valida permiso `reportes.compras.ver`.
El sistema consulta sucursales para el filtro. Tabla consultada: `sucursales`.
El sistema consulta proveedores para el filtro. Tabla consultada: `proveedores`.
El sistema muestra filtros de vista, fecha desde, fecha hasta, estado, sucursal, proveedor, articulo por ID o codigo exacto y cantidad de registros.
El usuario selecciona Compras.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.compras.ver`.
El sistema consulta compras segun los filtros.
Tablas consultadas en Vista Resumen: `compra_cabecera`, `compra_detalle`, `proveedores`, `usuarios`, `sucursales`.
Tablas consultadas en Vista Detallado: `compra_cabecera`, `compra_detalle`, `articulos`, `proveedores`, `sucursales`.
El sistema carga resumen, graficos y datos dentro de la grilla. En Vista Detallado, cada fila representa un articulo comprado con cantidad recibida, precio y subtotal.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe de Movimientos: Libro de Compras

Generar

El sistema valida permiso `reportes.libro_compras.ver`.
El sistema consulta sucursales para el filtro. Tabla consultada: `sucursales`.
El sistema consulta proveedores para el filtro. Tabla consultada: `proveedores`.
El sistema muestra filtros de fecha desde, fecha hasta, estado, sucursal, proveedor y cantidad de registros.
El usuario selecciona Libro de Compras.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.libro_compras.ver`.
El sistema consulta libro de compras segun los filtros.
Tablas consultadas: `libro_compra`, `sucursales`.
El sistema carga resumen, graficos y datos dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe de Movimientos: Stock

Generar

El sistema valida permiso `reportes.stock.ver`.
El sistema consulta sucursales para el filtro. Tabla consultada: `sucursales`.
El sistema muestra filtros de estado, sucursal, articulo por ID o codigo exacto y cantidad de registros.
El usuario selecciona Stock.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.stock.ver`.
El sistema consulta stock segun los filtros.
Tablas consultadas: `articulos`, `stock`, `categorias`, `marcas`, `articulo_proveedor`, `proveedores`, `unidad_medida`, `sucursales`.
El sistema carga resumen, graficos y datos dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe de Movimientos: Transferencias

Generar

El sistema valida permiso `reportes.transferencias.ver`.
El sistema consulta sucursales para el filtro. Tabla consultada: `sucursales`.
El sistema muestra filtros de fecha desde, fecha hasta, estado, sucursal, articulo por ID o codigo exacto y cantidad de registros.
El usuario selecciona Transferencias.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.transferencias.ver`.
El sistema consulta transferencias segun los filtros.
Tablas consultadas: `transferencia_stock`, `transferencia_stock_detalle`, `sucursales`, `nota_remision`.
El sistema carga resumen, graficos y datos dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe de Movimientos: Movimientos de Stock

Generar

El sistema valida permiso `reportes.movimientos_stock.ver`.
El sistema consulta sucursales para el filtro. Tabla consultada: `sucursales`.
El sistema muestra filtros de fecha desde, fecha hasta, estado, sucursal, articulo por ID o codigo exacto, naturaleza, tipo de movimiento de stock y cantidad de registros.
El usuario selecciona Movimientos de Stock.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.movimientos_stock.ver`.
El sistema consulta movimientos de stock segun los filtros.
Tablas consultadas: `movimientostock`, `sucursales`, `articulos`, `usuarios`.
El sistema filtra por naturaleza cuando corresponde: entradas, salidas, ajustes, compras, transferencias, servicios o insumos.
El sistema carga resumen, graficos y datos dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe de Movimientos: Kardex de Articulo

Generar

El sistema valida permiso `reportes.movimientos_stock.ver`.
El sistema consulta sucursales para el filtro. Tabla consultada: `sucursales`.
El sistema muestra filtros de fecha desde, fecha hasta, sucursal, articulo por ID o codigo exacto, naturaleza, tipo de movimiento de stock y cantidad de registros.
El usuario selecciona Kardex de Articulo.
El sistema exige sucursal y articulo.
El usuario selecciona sucursal.
El usuario ingresa articulo por ID o codigo exacto.
El usuario selecciona o ingresa filtros opcionales.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.movimientos_stock.ver`.
El sistema valida que sucursal y articulo esten informados.
El sistema consulta movimientos del articulo en la sucursal.
Tablas consultadas: `movimientostock`, `sucursales`, `articulos`, `usuarios`.
El sistema obtiene saldo anterior y saldo actual desde `MovStockSaldoAnterior` y `MovStockSaldoActual` cuando existen.
Si los saldos no existen en movimientos antiguos, el sistema calcula saldo acumulado con `MovStockCantidad * MovStockSigno`.
El sistema muestra una ficha del articulo con codigo, descripcion, sucursal, periodo, saldo inicial, total de entradas, total de salidas y saldo final.
El sistema carga entradas, salidas, saldo anterior, saldo actual, referencia y usuario dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe de Movimientos: Recepcion de Servicios

Generar

El sistema valida permiso `reportes.recepcion_servicio.ver`.
El sistema consulta sucursales para el filtro. Tabla consultada: `sucursales`.
El sistema muestra filtros de fecha desde, fecha hasta, estado, sucursal, cliente por ID o documento exacto y cantidad de registros.
El usuario selecciona Recepcion de Servicios.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.recepcion_servicio.ver`.
El sistema consulta recepciones segun los filtros.
Tablas consultadas: `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `sucursales`.
El sistema carga resumen, graficos y datos dentro de la grilla.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe de Movimientos: Presupuestos de Servicios

Generar

El sistema valida permiso `reportes.presupuesto_servicio.ver`.
El sistema consulta sucursales para el filtro. Tabla consultada: `sucursales`.
El sistema muestra filtros de vista, fecha desde, fecha hasta, estado, sucursal, cliente por ID o documento exacto, articulo por ID o codigo exacto y cantidad de registros.
El usuario selecciona Presupuestos de Servicios.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.presupuesto_servicio.ver`.
El sistema consulta presupuestos de servicios segun los filtros.
Tablas consultadas en Vista Resumen: `presupuesto_servicio`, `presupuesto_detalleservicio`, `diagnostico_servicio`, `recepcion_servicio`, `usuarios`, `sucursales`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`.
Tablas consultadas en Vista Detallado: `presupuesto_servicio`, `presupuesto_detalleservicio`, `articulos`, `diagnostico_servicio`, `recepcion_servicio`, `sucursales`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`.
El sistema carga resumen, graficos y datos dentro de la grilla. En Vista Detallado, cada fila representa un repuesto o articulo presupuestado para el servicio.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe de Movimientos: Ordenes de Trabajo

Generar

El sistema valida permiso `reportes.orden_trabajo.ver`.
El sistema consulta sucursales para el filtro. Tabla consultada: `sucursales`.
El sistema muestra filtros de vista, fecha desde, fecha hasta, estado, sucursal, cliente por ID o documento exacto, articulo por ID o codigo exacto y cantidad de registros.
El usuario selecciona Ordenes de Trabajo.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.orden_trabajo.ver`.
El sistema consulta ordenes de trabajo segun los filtros.
Tablas consultadas en Vista Resumen: `orden_trabajo`, `orden_trabajo_detalle`, `presupuesto_servicio`, `diagnostico_servicio`, `recepcion_servicio`, `sucursales`, `usuarios`, `equipo_trabajo`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`.
Tablas consultadas en Vista Detallado: `orden_trabajo`, `orden_trabajo_detalle`, `articulos`, `sucursales`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`.
El sistema carga resumen, graficos y datos dentro de la grilla. En Vista Detallado, cada fila representa un articulo utilizado o previsto en la orden.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

#### Informe de Movimientos: Registro de Servicios

Generar

El sistema valida permiso `reportes.registro_servicio.ver`.
El sistema consulta sucursales para el filtro. Tabla consultada: `sucursales`.
El sistema muestra filtros de vista, fecha desde, fecha hasta, estado, sucursal, cliente por ID o documento exacto, articulo por ID o codigo exacto, tecnico por ID o cedula exacta y cantidad de registros.
El usuario selecciona Registro de Servicios.
El usuario selecciona o ingresa filtros.
El usuario presiona Previsualizar.
El sistema valida nuevamente permiso `reportes.registro_servicio.ver`.
El sistema consulta registros de servicios segun los filtros.
Tablas consultadas en Vista Resumen: `registro_servicio`, `registro_servicio_detalle`, `orden_trabajo`, `sucursales`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `equipo_trabajo`, `empleados`.
Tablas consultadas en Vista Detallado: `registro_servicio`, `registro_servicio_detalle`, `articulos`, `orden_trabajo`, `sucursales`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `empleados`.
El sistema carga resumen, graficos y datos dentro de la grilla. En Vista Detallado, cada fila representa un repuesto o insumo registrado en el servicio.
El sistema habilita los botones PDF y CSV si existen registros.
Para PDF o CSV, el sistema envia los mismos filtros, valida permiso nuevamente y consulta las mismas tablas.

---

## Informe de Articulos

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de articulos registrados. Se ejecuta desde la vista Informes Referenciales mediante el tipo Articulos.

### Actores Relacionados

- Encargado de Compras
- Encargado de Stock
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.articulos.ver`.
- Existen articulos registrados.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes Referenciales.
2. El sistema muestra la vista referencial unificada.
3. El usuario selecciona el tipo Articulos.
4. El sistema habilita filtros de busqueda, estado y categoria.
5. El usuario ingresa filtros opcionales.
6. El usuario presiona Previsualizar.
7. El sistema valida permiso `reportes.articulos.ver`.
8. El sistema consulta los articulos segun los filtros.
9. El sistema muestra resumen y grilla de resultados.
10. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen registros para los filtros, el sistema muestra la tabla sin resultados.
- Si no se ingresan filtros, el sistema consulta todos los articulos permitidos.

### Post Condicion

- El sistema muestra los articulos encontrados.
- El sistema genera PDF o CSV si corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `articulos`
- `categorias`
- `marcas`
- `articulo_proveedor`
- `proveedores`
- `unidad_medida`
- `tipo_impuesto`

---

## Informe de Proveedores

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de proveedores registrados. Se ejecuta desde la vista Informes Referenciales mediante el tipo Proveedores.

### Actores Relacionados

- Encargado de Compras
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.proveedores.ver`.
- Existen proveedores registrados.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes Referenciales.
2. El usuario selecciona el tipo Proveedores.
3. El sistema habilita filtros de busqueda y estado.
4. El usuario ingresa filtros opcionales.
5. El usuario presiona Previsualizar.
6. El sistema valida permiso `reportes.proveedores.ver`.
7. El sistema consulta proveedores segun los filtros.
8. El sistema muestra resumen y grilla de resultados.
9. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen registros, el sistema muestra la tabla sin resultados.

### Post Condicion

- El sistema muestra los proveedores encontrados.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `proveedores`
- `ciudades`

---

## Informe de Clientes

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de clientes registrados. Se ejecuta desde la vista Informes Referenciales mediante el tipo Clientes.

### Actores Relacionados

- Personal de Recepcion
- Encargado de Servicios
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.clientes.ver`.
- Existen clientes registrados.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes Referenciales.
2. El usuario selecciona el tipo Clientes.
3. El sistema habilita filtros de busqueda y estado.
4. El usuario ingresa filtros opcionales.
5. El usuario presiona Previsualizar.
6. El sistema valida permiso `reportes.clientes.ver`.
7. El sistema consulta clientes segun los filtros.
8. El sistema muestra resumen y grilla de resultados.
9. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen registros, el sistema muestra la tabla sin resultados.

### Post Condicion

- El sistema muestra los clientes encontrados.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `clientes`
- `ciudades`

---

## Informe de Vehiculos

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de vehiculos registrados. Se ejecuta desde la vista Informes Referenciales mediante el tipo Vehiculos.

### Actores Relacionados

- Personal de Recepcion
- Encargado de Servicios
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.vehiculos.ver`.
- Existen vehiculos registrados.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes Referenciales.
2. El usuario selecciona el tipo Vehiculos.
3. El sistema habilita filtros de busqueda y estado.
4. El usuario ingresa filtros opcionales.
5. El usuario presiona Previsualizar.
6. El sistema valida permiso `reportes.vehiculos.ver`.
7. El sistema consulta vehiculos segun los filtros.
8. El sistema muestra resumen y grilla de resultados.
9. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen registros, el sistema muestra la tabla sin resultados.

### Post Condicion

- El sistema muestra los vehiculos encontrados.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `vehiculos`
- `clientes`
- `modelo_auto`
- `marcas`

---

## Informe de Sucursales

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de sucursales registradas. Se ejecuta desde la vista Informes Referenciales mediante el tipo Sucursales.

### Actores Relacionados

- Administrador
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.sucursales.ver`.
- Existen sucursales registradas.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes Referenciales.
2. El usuario selecciona el tipo Sucursales.
3. El sistema habilita filtros de busqueda y estado.
4. El usuario ingresa filtros opcionales.
5. El usuario presiona Previsualizar.
6. El sistema valida permiso `reportes.sucursales.ver`.
7. El sistema consulta sucursales segun los filtros.
8. El sistema muestra resumen y grilla de resultados.
9. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen registros, el sistema muestra la tabla sin resultados.

### Post Condicion

- El sistema muestra las sucursales encontradas.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `sucursales`
- `empresa`

---

## Informe de Marcas

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de marcas registradas. Se ejecuta desde la vista Informes Referenciales mediante el tipo Marcas.

### Actores Relacionados

- Encargado de Compras
- Encargado de Stock
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.articulos.ver`.
- Existen marcas registradas.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes Referenciales.
2. El usuario selecciona el tipo Marcas.
3. El sistema habilita filtro de busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.articulos.ver`.
6. El sistema consulta marcas registradas.
7. El sistema muestra grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen registros, el sistema muestra la tabla sin resultados.

### Post Condicion

- El sistema muestra las marcas encontradas.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `marcas`

---

## Informe de Categorias

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de categorias registradas. Se ejecuta desde la vista Informes Referenciales mediante el tipo Categorias.

### Actores Relacionados

- Encargado de Compras
- Encargado de Stock
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.articulos.ver`.
- Existen categorias registradas.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes Referenciales.
2. El usuario selecciona el tipo Categorias.
3. El sistema habilita filtro de busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.articulos.ver`.
6. El sistema consulta categorias registradas.
7. El sistema muestra grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen registros, el sistema muestra la tabla sin resultados.

### Post Condicion

- El sistema muestra las categorias encontradas.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `categorias`

---

## Informe de Usuarios

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de usuarios registrados. Se ejecuta desde la vista Informes Referenciales mediante el tipo Usuarios.

### Actores Relacionados

- Administrador
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `usuarios.ver`.
- Existen usuarios registrados.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes Referenciales.
2. El usuario selecciona el tipo Usuarios.
3. El sistema habilita filtros de busqueda y estado.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `usuarios.ver`.
6. El sistema consulta usuarios registrados.
7. El sistema muestra grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen registros, el sistema muestra la tabla sin resultados.

### Post Condicion

- El sistema muestra los usuarios encontrados.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `usuarios`
- `sucursales`

---

## Informe de Pedidos de Compra

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de pedidos de compra. Se ejecuta desde la vista Informes de Movimientos mediante el tipo Pedidos de Compra.

### Actores Relacionados

- Encargado de Compras
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.pedidos.ver`.
- Existen pedidos de compra registrados.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes de Movimientos.
2. El usuario selecciona Pedidos de Compra.
3. El sistema habilita filtros de fecha, sucursal, estado y busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.pedidos.ver`.
6. El sistema consulta pedidos segun los filtros.
7. El sistema muestra resumen, graficos y grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen datos, el sistema muestra resumen en cero y tabla sin registros.

### Post Condicion

- El sistema muestra los pedidos encontrados.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `pedido_cabecera`
- `pedido_detalle`
- `usuarios`
- `sucursales`

---

## Informe de Presupuestos de Compra

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de presupuestos de compra. Se ejecuta desde la vista Informes de Movimientos mediante el tipo Presupuestos de Compra.

### Actores Relacionados

- Encargado de Compras
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.presupuestos_compra.ver`.
- Existen presupuestos de compra registrados.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes de Movimientos.
2. El usuario selecciona Presupuestos de Compra.
3. El sistema habilita filtros de fecha, sucursal, estado y busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.presupuestos_compra.ver`.
6. El sistema consulta presupuestos segun los filtros.
7. El sistema muestra resumen, graficos y grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen datos, el sistema muestra resumen en cero y tabla sin registros.

### Post Condicion

- El sistema muestra los presupuestos encontrados.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `presupuesto_compra`
- `presupuesto_detalle`
- `proveedores`
- `sucursales`
- `usuarios`

---

## Informe de Ordenes de Compra

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de ordenes de compra. Se ejecuta desde la vista Informes de Movimientos mediante el tipo Ordenes de Compra.

### Actores Relacionados

- Encargado de Compras
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.ordenes_compra.ver`.
- Existen ordenes de compra registradas.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes de Movimientos.
2. El usuario selecciona Ordenes de Compra.
3. El sistema habilita filtros de fecha, sucursal, estado y busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.ordenes_compra.ver`.
6. El sistema consulta ordenes segun los filtros.
7. El sistema muestra resumen, graficos y grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen datos, el sistema muestra resumen en cero y tabla sin registros.

### Post Condicion

- El sistema muestra las ordenes encontradas.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `orden_compra`
- `orden_compra_detalle`
- `presupuesto_compra`
- `proveedores`
- `sucursales`
- `usuarios`

---

## Informe de Compras

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de compras registradas. Se ejecuta desde la vista Informes de Movimientos mediante el tipo Compras.

### Actores Relacionados

- Encargado de Compras
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.compras.ver`.
- Existen compras registradas.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes de Movimientos.
2. El usuario selecciona Compras.
3. El sistema habilita filtros de fecha, sucursal, estado y busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.compras.ver`.
6. El sistema consulta compras segun los filtros.
7. El sistema muestra resumen, graficos y grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen datos, el sistema muestra resumen en cero y tabla sin registros.

### Post Condicion

- El sistema muestra las compras encontradas.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `compra_cabecera`
- `compra_detalle`
- `orden_compra`
- `proveedores`
- `sucursales`
- `usuarios`

---

## Informe Libro de Compras

### Descripcion Basica

Caso de uso que describe el proceso de generar informes del libro de compras. Se ejecuta desde la vista Informes de Movimientos mediante el tipo Libro de Compras.

### Actores Relacionados

- Encargado de Compras
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.libro_compras.ver`.
- Existen registros en libro de compras.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes de Movimientos.
2. El usuario selecciona Libro de Compras.
3. El sistema habilita filtros de fecha, sucursal, proveedor, estado y busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.libro_compras.ver`.
6. El sistema consulta registros del libro segun los filtros.
7. El sistema muestra resumen, graficos y grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen datos, el sistema muestra resumen en cero y tabla sin registros.

### Post Condicion

- El sistema muestra los registros encontrados.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `libro_compra`
- `proveedores`
- `sucursales`

---

## Informe de Stock

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de stock de articulos. Se ejecuta desde la vista Informes de Movimientos mediante el tipo Stock.

### Actores Relacionados

- Encargado de Stock
- Encargado de Compras
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.stock.ver`.
- Existen articulos con stock registrado.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes de Movimientos.
2. El usuario selecciona Stock.
3. El sistema habilita filtros de sucursal, estado y busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.stock.ver`.
6. El sistema consulta stock segun los filtros.
7. El sistema muestra resumen, graficos y grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen datos, el sistema muestra resumen en cero y tabla sin registros.

### Post Condicion

- El sistema muestra el stock encontrado.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `articulos`
- `categorias`
- `marcas`
- `stock`
- `sucursales`

---

## Informe de Transferencias

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de transferencias entre sucursales. Se ejecuta desde la vista Informes de Movimientos mediante el tipo Transferencias.

### Actores Relacionados

- Encargado de Stock
- Encargado de Compras
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.transferencias.ver`.
- Existen transferencias registradas.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes de Movimientos.
2. El usuario selecciona Transferencias.
3. El sistema habilita filtros de fecha, sucursal, estado y busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.transferencias.ver`.
6. El sistema consulta transferencias segun los filtros.
7. El sistema muestra resumen, graficos y grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen datos, el sistema muestra resumen en cero y tabla sin registros.

### Post Condicion

- El sistema muestra las transferencias encontradas.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `transferencia_stock`
- `transferencia_stock_detalle`
- `sucursales`
- `nota_remision`

---

## Informe de Movimientos de Stock

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de entradas y salidas de stock. Se ejecuta desde la vista Informes de Movimientos mediante el tipo Movimientos de Stock.

### Actores Relacionados

- Encargado de Stock
- Encargado de Compras
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.movimientos_stock.ver`.
- Existen movimientos de stock registrados.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes de Movimientos.
2. El usuario selecciona Movimientos de Stock.
3. El sistema habilita filtros de fecha, sucursal y busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.movimientos_stock.ver`.
6. El sistema consulta movimientos segun los filtros.
7. El sistema muestra resumen, graficos y grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen datos, el sistema muestra resumen en cero y tabla sin registros.

### Post Condicion

- El sistema muestra los movimientos encontrados.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `movimientostock`
- `articulos`
- `sucursales`
- `usuarios`

---

## Informe Kardex de Articulo

### Descripcion Basica

Caso de uso que describe el proceso de generar el Kardex de un articulo. Se ejecuta desde la vista Informes de Movimientos mediante el tipo Kardex de Articulo.

El Kardex muestra el historial cronologico de movimientos de un articulo en una sucursal, indicando entradas, salidas, saldo anterior y saldo actual.

### Actores Relacionados

- Encargado de Stock
- Encargado de Compras
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.movimientos_stock.ver`.
- Existen movimientos de stock registrados para el articulo seleccionado.
- El usuario debe indicar sucursal.
- El usuario debe indicar articulo por ID o codigo exacto.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes de Movimientos.
2. El usuario selecciona Kardex de Articulo.
3. El sistema habilita filtros de fecha, sucursal, articulo, naturaleza y tipo de movimiento.
4. El usuario ingresa sucursal y articulo.
5. El usuario ingresa filtros opcionales.
6. El usuario presiona Previsualizar.
7. El sistema valida permiso `reportes.movimientos_stock.ver`.
8. El sistema valida que exista sucursal y articulo.
9. El sistema consulta `movimientostock` segun articulo, sucursal y fechas.
10. El sistema calcula o lee saldo anterior y saldo actual.
11. El sistema muestra ficha del articulo, resumen, graficos y grilla de resultados.
12. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se indica sucursal, el sistema solicita seleccionar sucursal.
- Si no se indica articulo, el sistema solicita ingresar ID o codigo exacto.
- Si el articulo no existe, el sistema muestra tabla sin resultados.
- Si no existen movimientos, el sistema muestra resumen en cero y tabla sin registros.
- Si se filtra por naturaleza, el sistema muestra solo los movimientos coincidentes.

### Post Condicion

- El sistema muestra el Kardex del articulo seleccionado.
- La informacion consultada no se modifica.
- El saldo mostrado corresponde a la trazabilidad de movimientos registrados.

### Tablas Involucradas

- `movimientostock`
- `articulos`
- `sucursales`
- `usuarios`

### Reglas de Calculo

- El saldo anterior se obtiene desde `MovStockSaldoAnterior` cuando existe.
- Si el movimiento no tiene saldos guardados, el sistema calcula el saldo acumulando `MovStockCantidad * MovStockSigno`.
- Las entradas se identifican con `MovStockSigno = 1`.
- Las salidas se identifican con `MovStockSigno = -1`.
- El saldo actual se obtiene desde `MovStockSaldoActual` cuando existe.
- Si se filtra solo entrada, salida, ajuste u otra naturaleza, el filtro afecta las filas mostradas, pero el saldo representa la trazabilidad real del movimiento.
- El Kardex se ordena de forma ascendente por fecha e identificador para que el saldo pueda leerse de inicio a fin.

---

## Informe de Recepcion de Servicios

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de recepciones de servicio. Se ejecuta desde la vista Informes de Movimientos mediante el tipo Recepcion de Servicios.

### Actores Relacionados

- Personal de Recepcion
- Encargado de Servicios
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.recepcion_servicio.ver`.
- Existen recepciones de servicio registradas.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes de Movimientos.
2. El usuario selecciona Recepcion de Servicios.
3. El sistema habilita filtros de fecha, sucursal, estado y busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.recepcion_servicio.ver`.
6. El sistema consulta recepciones segun los filtros.
7. El sistema muestra resumen, graficos y grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen datos, el sistema muestra resumen en cero y tabla sin registros.

### Post Condicion

- El sistema muestra las recepciones encontradas.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `recepcion_servicio`
- `clientes`
- `vehiculos`
- `usuarios`
- `sucursales`

---

## Informe de Presupuesto de Servicios

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de presupuestos de servicio. Se ejecuta desde la vista Informes de Movimientos mediante el tipo Presupuestos de Servicios.

### Actores Relacionados

- Encargado de Servicios
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.presupuesto_servicio.ver`.
- Existen presupuestos de servicio registrados.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes de Movimientos.
2. El usuario selecciona Presupuestos de Servicios.
3. El sistema habilita filtros de fecha, sucursal, estado y busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.presupuesto_servicio.ver`.
6. El sistema consulta presupuestos segun los filtros.
7. El sistema muestra resumen, graficos y grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen datos, el sistema muestra resumen en cero y tabla sin registros.

### Post Condicion

- El sistema muestra los presupuestos encontrados.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `presupuesto_servicio`
- `presupuesto_detalleservicio`
- `clientes`
- `vehiculos`
- `sucursales`
- `usuarios`

---

## Informe de Ordenes de Trabajo

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de ordenes de trabajo. Se ejecuta desde la vista Informes de Movimientos mediante el tipo Ordenes de Trabajo.

### Actores Relacionados

- Encargado de Servicios
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.orden_trabajo.ver`.
- Existen ordenes de trabajo registradas.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes de Movimientos.
2. El usuario selecciona Ordenes de Trabajo.
3. El sistema habilita filtros de fecha, sucursal, estado y busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.orden_trabajo.ver`.
6. El sistema consulta ordenes segun los filtros.
7. El sistema muestra resumen, graficos y grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen datos, el sistema muestra resumen en cero y tabla sin registros.

### Post Condicion

- El sistema muestra las ordenes encontradas.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `orden_trabajo`
- `orden_trabajo_detalle`
- `presupuesto_servicio`
- `clientes`
- `vehiculos`
- `equipo_trabajo`
- `empleados`
- `sucursales`
- `usuarios`

---

## Informe de Registro de Servicios

### Descripcion Basica

Caso de uso que describe el proceso de generar informes de servicios registrados. Se ejecuta desde la vista Informes de Movimientos mediante el tipo Registro de Servicios.

### Actores Relacionados

- Encargado de Servicios
- Gerente general

### Pre Condicion

- Conexion a base de datos.
- El usuario accede al sistema mediante login.
- El usuario tiene permiso `reportes.registro_servicio.ver`.
- Existen registros de servicio.

### Flujo de Eventos

#### Flujo Basico

1. El usuario ingresa al menu Informes de Movimientos.
2. El usuario selecciona Registro de Servicios.
3. El sistema habilita filtros de fecha, sucursal, tecnico, estado y busqueda.
4. El usuario presiona Previsualizar.
5. El sistema valida permiso `reportes.registro_servicio.ver`.
6. El sistema consulta registros segun los filtros.
7. El sistema muestra resumen, graficos y grilla de resultados.
8. El usuario puede generar PDF o exportar CSV.

### Flujo Alternativo

- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no existen datos, el sistema muestra resumen en cero y tabla sin registros.

### Post Condicion

- El sistema muestra los registros encontrados.
- La informacion consultada no se modifica.

### Tablas Involucradas

- `registro_servicio`
- `registro_servicio_detalle`
- `orden_trabajo`
- `clientes`
- `vehiculos`
- `empleados`
- `sucursales`
- `usuarios`

---

## Reglas de Graficos Estadisticos

### Descripcion

Los informes de movimientos muestran graficos estadisticos para apoyar el analisis visual de los datos consultados.

La representacion visual se realiza con la libreria JavaScript Chart.js sobre elementos `canvas`, manteniendo la consulta y preparacion de datos en el backend del sistema. Los graficos se presentan en modo estatico para evitar modificaciones visuales por parte del usuario durante la consulta.

### Graficos Disponibles

- Movimientos por periodo mediante grafico de linea.
- Distribucion por estado mediante grafico de dona.
- Top relacionado mediante grafico de barras horizontales.

### Top Relacionado

El Top relacionado muestra la entidad mas representativa segun el informe seleccionado. En compras se orienta a proveedores; en servicios puede orientarse a clientes, tecnicos o vehiculos; en stock se orienta a articulos o tipos de movimiento.

Los valores del grafico se muestran con separador de miles.

---

## Reglas de Exportacion CSV

### Descripcion

El sistema permite exportar los informes a CSV para su apertura en planillas electronicas.

### Reglas

- El archivo se genera desde el servidor.
- En Informes Referenciales se usa codificacion UTF-16LE con BOM para evitar problemas de acentos al abrir en Excel.
- En Informes de Movimientos se usa codificacion UTF-8 con BOM.
- Se usa separador punto y coma.
- Los encabezados corresponden a las columnas visibles del informe.
- Los estados se exportan como texto descriptivo.
- Los importes y cantidades se exportan con formato legible.

---

## Reglas de Exportacion PDF

### Descripcion

El sistema permite generar PDF con los resultados del informe seleccionado.

### Reglas

- El sistema valida permisos antes de generar el PDF.
- El PDF respeta el tipo de informe y los filtros enviados.
- El documento incluye titulo, columnas y registros filtrados.
- Si no existen registros, el PDF informa que no hay datos.
