# Especificacion de casos de uso: Movimientos del sistema

## Consideraciones tomadas desde JavaScript

Ademas de controladores y modelos, los movimientos usan scripts en `vistas/inc` para manejar la interaccion del usuario. Estos scripts agregan comportamientos de interfaz que complementan la logica del servidor:

* Busquedas asincronas por `fetch`, `FormData`, jQuery/Ajax o eventos `keyup`.
* Modales y tablas dinamicas para seleccionar proveedores, articulos, pedidos, facturas, sucursales y ajustes.
* Validaciones inmediatas antes de enviar datos al servidor.
* Confirmaciones con `Swal.fire` o `confirm` antes de agregar, guardar, aplicar ajustes, recibir transferencias o limpiar datos.
* Recalculo de totales, subtotales, diferencias y cantidades directamente en pantalla.
* Actualizacion temporal de datos de sesion cuando el usuario modifica precios o cantidades.
* Mensajes visuales de exito, advertencia o error segun la respuesta del servidor.

Estas validaciones de interfaz no reemplazan las validaciones del controlador; sirven como apoyo para evitar errores antes del envio.

## Auditoria central de anulaciones

Los movimientos transaccionales que pueden afectar trazabilidad operativa registran la anulacion en la tabla `anulacion_auditoria`. Esta tabla no reemplaza el cambio de estado del documento; complementa el proceso guardando motivo, usuario, fecha, modulo, tabla afectada y referencia del registro anulado.

El registro se realiza mediante el metodo central `mainModel::registrar_anulacion_auditoria_modelo()`. El metodo valida que la tabla `anulacion_auditoria` exista y exige que el motivo no este vacio. Si la tabla aun no existe, el sistema puede continuar con el flujo funcional, pero no persiste la auditoria central hasta ejecutar el script `database/create_anulacion_auditoria.sql` o contar con la tabla en el dump final.

Movimientos que actualmente registran auditoria de anulacion:

| Movimiento | Modulo registrado | Tabla afectada | Referencia |
|---|---|---|---|
| Pedido de compra | `pedido_compra` | `pedido_cabecera` | `PEDIDO #id` |
| Presupuesto de compra | `presupuesto_compra` | `presupuesto_compra` | `PRESUPUESTO_COMPRA #id` |
| Orden de compra | `orden_compra` | `orden_compra` | `ORDEN_COMPRA #id` |
| Compra / factura proveedor | `compra` | `compra_cabecera` | `COMPRA #id` |
| Nota de remision | `nota_remision` | `nota_remision` | `NOTA_REMISION #id` |
| Nota de credito/debito de compra | `nota_compra` | `nota_compra` | `NOTA_COMPRA #id` |
| Ajuste de inventario | `ajuste_inventario` | `ajuste_inventario` | `AJUSTE_INVENTARIO #id` |
| Recepcion de servicio | `recepcion_servicio` | `recepcion_servicio` | `RECEPCION #id` |
| Diagnostico de servicio | `diagnostico_servicio` | `diagnostico_servicio` | `DIAGNOSTICO #id` |
| Presupuesto de servicio | `presupuesto_servicio` | `presupuesto_servicio` | `PRESUPUESTO_SERVICIO #id` |
| Orden de trabajo | `orden_trabajo` | `orden_trabajo` | `OT #id` |
| Registro de servicio | `registro_servicio` | `registro_servicio` | `REGISTRO_SERVICIO #id` |
| Salida de insumos | `salida_insumo` | `salida_insumo` | `SALIDA_INSUMO #id` |
| Reclamo de servicio | `reclamo_servicio` | `reclamo_servicio` | `RECLAMO_SERVICIO #id` |

Datos guardados en `anulacion_auditoria`:

| Campo | Descripcion |
|---|---|
| `modulo` | Nombre logico del modulo que ejecuto la anulacion. |
| `tabla_afectada` | Tabla principal cuyo registro cambio de estado. |
| `id_registro` | Identificador del documento o movimiento anulado. |
| `id_sucursal` | Sucursal relacionada, cuando el movimiento la posee. |
| `estado_anterior` | Estado previo del documento antes de anular. |
| `estado_nuevo` | Estado final de anulacion, normalmente `0`. |
| `motivo` | Justificacion ingresada por el usuario en el modal de anulacion. |
| `usuario_anula` | Usuario autenticado que confirmo la anulacion. |
| `fecha_anulacion` | Fecha y hora del registro de auditoria. |
| `referencia` | Texto identificador para lectura rapida del documento. |

Defensa funcional:

> La anulacion no elimina el documento. El sistema cambia el estado del registro original y guarda una auditoria central con motivo, usuario y fecha. Si el movimiento afecta stock, adicionalmente se registra el movimiento inverso correspondiente en `movimientostock`.

## Movimiento "Gestion de Pedidos"

* **Nombre de Caso de Uso**  
Registrar Pedido

* **Descripcion Basica**  
Permite registrar un pedido interno de articulos para compras, dejando una cabecera y un detalle de productos solicitados para su posterior presupuesto u orden de compra.

* **Actores relacionados**  
Personal de Compras / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
El usuario debe tener permisos para operar pedidos.  
Debe existir conexion a la base de datos.  
Debe existir una sucursal asociada al usuario.  
Deben existir articulos activos.

* **Flujo Basico**

El usuario accede al sistema mediante login.  
El usuario ingresa al menu Nuevo Pedido.  
El sistema valida la sesion y la sucursal del usuario.

**Agregar articulo**

* El usuario presiona "Agregar articulo".
* El sistema muestra el buscador de articulos.
* El usuario ingresa codigo o descripcion.
* El sistema ejecuta la busqueda sin recargar la pagina.
* El sistema consulta la tabla `articulos` filtrando articulos activos.
* El sistema muestra los articulos encontrados.
* El usuario ingresa la cantidad y selecciona el articulo.
* El sistema valida que el articulo exista y este activo. Tabla consultada: `articulos`.
* El sistema valida que la cantidad sea mayor a 0.
* El sistema valida que el articulo no se encuentre duplicado en la lista temporal del pedido.
* El sistema agrega el articulo a la lista temporal del pedido.

**Guardar**

* El usuario presiona Guardar.
* El sistema muestra un mensaje de confirmacion de la accion.
* El usuario confirma la accion.
* El sistema valida que existan articulos cargados en el detalle temporal.
* El sistema valida la sucursal y el usuario autenticado con datos de sesion.
* El sistema registra la cabecera en `pedido_cabecera` con estado pendiente.
* El sistema registra los articulos en `pedido_detalle`.
* El sistema emite mensaje de confirmacion de registro.

**Anular**

* El usuario busca pedidos por los filtros disponibles.
* El sistema consulta los pedidos de la sucursal del usuario. Tablas consultadas: `pedido_cabecera`, `usuarios`.
* El usuario selecciona un pedido y presiona Anular.
* El sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma la anulacion.
* El sistema verifica que el pedido exista. Tabla consultada: `pedido_cabecera`.
* El sistema verifica que pertenezca a la sucursal del usuario. Tabla consultada: `pedido_cabecera`.
* El sistema verifica que el pedido no se encuentre procesado. Tabla consultada: `pedido_cabecera`.
* El sistema valida el permiso `compra.pedido.anular`.
* El sistema actualiza el pedido a estado anulado.
* El sistema registra usuario y fecha de actualizacion.
* El sistema registra auditoria en `anulacion_auditoria` con modulo `pedido_compra`, tabla `pedido_cabecera`, ID del pedido, sucursal, estado anterior, estado nuevo, motivo, usuario y referencia `PEDIDO #id`.
* El sistema muestra mensaje de confirmacion.

* **Flujo Alternativo**  
El sistema no permite articulos duplicados.  
El sistema no permite cantidades invalidas.  
El sistema no permite guardar sin articulos.  
El sistema no permite anular pedidos procesados.  
El sistema no permite anular sin permiso.
El sistema no permite anular sin motivo.

* **Post Condicion**  
El pedido queda registrado en cabecera y detalle.  
El pedido puede quedar pendiente, procesado o anulado.  
El sistema emite mensajes de confirmacion o advertencia segun corresponda.

* **Tablas interactuadas**  
`pedido_cabecera`, `pedido_detalle`, `articulos`, `usuarios`, `sucursales`, `anulacion_auditoria`

---

## Movimiento "Gestion de Presupuestos de Compra"

* **Nombre de Caso de Uso**  
Registrar Presupuesto de Compra

* **Descripcion Basica**  
Permite registrar un presupuesto de compra asociado a un proveedor, con articulos, cantidades, precios y subtotales. Puede generarse manualmente o cargarse a partir de un pedido pendiente.

* **Actores relacionados**  
Personal de Compras / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir una sucursal asociada al usuario.  
Deben existir proveedores y articulos activos.  
Si se carga desde pedido, debe existir un pedido pendiente.

* **Flujo Basico**

El usuario ingresa al menu Nuevo Presupuesto.  
El sistema valida sesion y sucursal.

**Seleccionar proveedor**

* El usuario busca un proveedor por RUC, razon social o telefono.
* El sistema consulta la tabla `proveedores`.
* El usuario selecciona un proveedor.
* El sistema valida que el proveedor exista y este disponible para operar. Tabla consultada: `proveedores`.
* El sistema guarda los datos del proveedor en sesion.

**Cargar desde pedido**

* El usuario busca un pedido por numero.
* El sistema consulta pedidos pendientes. Tablas consultadas: `pedido_cabecera`, `usuarios`.
* El sistema valida que el pedido pertenezca a la sucursal del usuario. Tabla consultada: `pedido_cabecera`.
* El usuario selecciona el pedido.
* El sistema carga los articulos solicitados. Tablas consultadas: `pedido_detalle`, `articulos`.

**Agregar articulo**

* El usuario busca articulos por codigo o descripcion.
* El sistema consulta `articulos` activos.
* El usuario ingresa cantidad y precio.
* El sistema valida que el articulo exista y este activo. Tabla consultada: `articulos`.
* El sistema valida cantidad, precio y duplicidad en el detalle temporal.
* El sistema calcula subtotal.
* El sistema recalcula total de unidades y total general en pantalla.
* Si el usuario modifica precios en la grilla, el sistema actualiza los datos temporales mediante Ajax.
* El sistema actualiza la grilla temporal.

**Guardar**

* El usuario presiona Guardar.
* El sistema muestra un mensaje de confirmacion de la accion.
* El usuario confirma la accion.
* El sistema valida proveedor. Tabla consultada: `proveedores`.
* El sistema valida articulos cargados en el detalle temporal.
* Si el presupuesto proviene de pedido, el sistema valida nuevamente que el pedido exista y siga disponible. Tablas consultadas: `pedido_cabecera`, `pedido_detalle`.
* El sistema registra la cabecera en `presupuesto_compra`.
* El sistema registra el detalle en `presupuesto_detalle`.
* Si proviene de un pedido, el sistema actualiza el pedido a procesado. Tabla afectada: `pedido_cabecera`.
* El sistema emite mensaje de presupuesto registrado.

**Ver detalle**

* El usuario busca presupuestos por los filtros disponibles.
* El sistema consulta presupuestos de la sucursal. Tablas consultadas: `presupuesto_compra`, `proveedores`, `usuarios`.
* Si el usuario presiona Ver detalle, el sistema consulta la cabecera del presupuesto. Tablas consultadas: `presupuesto_compra`, `proveedores`, `usuarios`.
* El sistema consulta los articulos del presupuesto bajo demanda. Tablas consultadas: `presupuesto_detalle`, `articulos`.
* El sistema muestra en modal proveedor, RUC, fecha, vencimiento, estado, usuario, articulos, cantidades, precios, subtotales y total.

**Anular**

* El usuario busca presupuestos por los filtros disponibles.
* El sistema consulta presupuestos de la sucursal. Tablas consultadas: `presupuesto_compra`, `proveedores`, `usuarios`.
* El usuario selecciona un presupuesto y presiona Anular.
* El sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma la anulacion.
* El sistema verifica existencia, sucursal y estado. Tabla consultada: `presupuesto_compra`.
* El sistema no permite anular presupuestos procesados. Tabla consultada: `presupuesto_compra`.
* El sistema valida el permiso `compra.presupuesto.anular`.
* El sistema actualiza el estado a anulado y registra usuario/fecha.
* Si el presupuesto proviene de un pedido, el sistema devuelve el pedido a estado pendiente cuando corresponde. Tabla afectada: `pedido_cabecera`.
* El sistema registra auditoria en `anulacion_auditoria` con modulo `presupuesto_compra`, tabla `presupuesto_compra`, ID del presupuesto, sucursal, estado anterior, estado nuevo, motivo, usuario y referencia `PRESUPUESTO_COMPRA #id`.

* **Flujo Alternativo**  
El sistema no permite guardar sin proveedor.  
El sistema no permite guardar sin articulos.  
El sistema no permite articulos duplicados.  
El sistema no permite cantidades o precios invalidos.  
El usuario puede usar Ver detalle para consultar los articulos del presupuesto sin modificar ni procesar el documento.  
El sistema no permite anular presupuestos procesados.
El sistema no permite anular sin motivo.

* **Post Condicion**  
El presupuesto queda registrado con proveedor, articulos y total.  
Si fue generado desde pedido, el pedido queda procesado.  
El sistema emite confirmacion de registro o anulacion.

* **Tablas interactuadas**  
`presupuesto_compra`, `presupuesto_detalle`, `pedido_cabecera`, `pedido_detalle`, `proveedores`, `articulos`, `usuarios`, `sucursales`, `anulacion_auditoria`

---

## Movimiento "Gestion de Ordenes de Compra"

* **Nombre de Caso de Uso**  
Registrar Orden de Compra

* **Descripcion Basica**  
Permite generar una orden de compra para un proveedor, con fecha de entrega, sucursal destino donde deben entregarse los articulos y detalle de productos solicitados. Puede generarse desde un presupuesto aprobado o de forma manual.

* **Actores relacionados**  
Personal de Compras / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir una sucursal asociada al usuario.  
Deben existir proveedores y articulos activos.  
Si se genera desde presupuesto, debe existir un presupuesto activo.

* **Flujo Basico**

El usuario ingresa al menu Orden de Compra.  
El sistema valida sesion, sucursal y datos requeridos.

**Generar desde presupuesto**

* El usuario busca un presupuesto por numero, proveedor o RUC.
* El sistema consulta presupuestos activos de la sucursal. Tablas consultadas: `presupuesto_compra`, `proveedores`, `usuarios`.
* El usuario selecciona un presupuesto.
* El sistema carga proveedor, articulos, cantidades, precios y fecha de vencimiento. Tablas consultadas: `presupuesto_compra`, `presupuesto_detalle`.

**Agregar datos**

* El usuario selecciona proveedor cuando la orden es manual.
* El sistema valida el proveedor seleccionado. Tabla consultada: `proveedores`.
* El usuario busca articulos por codigo o descripcion.
* El sistema consulta articulos activos y precios relacionados al proveedor cuando existan. Tablas consultadas: `articulos`, `articulo_proveedor`.
* El usuario ingresa cantidad y precio.
* El sistema valida cantidad, precio y duplicidad en el detalle temporal.
* El sistema calcula subtotales.
* El sistema permite filtrar productos cargados en pantalla.
* El usuario confirma la generacion de la orden cuando corresponde.

**Guardar**

* El usuario presiona Guardar.
* El sistema muestra un mensaje de confirmacion de la accion.
* El usuario confirma la accion.
* El sistema valida proveedor y articulos cargados. Tablas consultadas: `proveedores`, `articulos`.
* Si la orden proviene de presupuesto, el sistema valida nuevamente que el presupuesto exista, pertenezca a la sucursal y no este procesado. Tabla consultada: `presupuesto_compra`.
* El sistema registra cabecera en `orden_compra`, guardando en `id_sucursal` la sucursal destino o lugar de entrega.
* El sistema registra detalle en `orden_compra_detalle`.
* El sistema registra o actualiza la relacion articulo-proveedor. Tabla afectada: `articulo_proveedor`.
* Si proviene de presupuesto, el sistema actualiza el presupuesto a estado OC generada/procesado. Tabla afectada: `presupuesto_compra`.
* El sistema emite mensaje de confirmacion.

**Imprimir orden**

* El usuario presiona Imprimir en una orden de compra generada.
* El sistema consulta la cabecera de la orden, proveedor, usuario y sucursal destino. Tablas consultadas: `orden_compra`, `proveedores`, `usuarios`, `sucursales`.
* El sistema consulta el detalle de articulos de la orden. Tablas consultadas: `orden_compra_detalle`, `articulos`.
* El sistema carga la plantilla PDF de orden de compra.
* El sistema muestra en la impresion los datos del proveedor, fecha de entrega, sucursal destino/lugar de entrega, direccion y telefono de la sucursal.
* El sistema genera el PDF de la orden de compra.

**Anular**

* El usuario ingresa al buscador de ordenes de compra.
* El usuario busca ordenes por los filtros disponibles.
* El sistema consulta las ordenes de la sucursal. Tablas consultadas: `orden_compra`, `proveedores`, `usuarios`.
* El usuario selecciona una orden y presiona Anular.
* El sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma la anulacion.
* El sistema verifica existencia y sucursal. Tabla consultada: `orden_compra`.
* El sistema verifica que la orden no este procesada. Tabla consultada: `orden_compra`.
* El sistema valida el permiso `compra.oc.anular`.
* El sistema actualiza el estado a anulado y registra usuario/fecha.
* Si la orden proviene de un presupuesto, el sistema devuelve el presupuesto a estado anterior cuando corresponde. Tabla afectada: `presupuesto_compra`.
* El sistema registra auditoria en `anulacion_auditoria` con modulo `orden_compra`, tabla `orden_compra`, ID de la orden, sucursal, estado anterior, estado nuevo, motivo, usuario y referencia `ORDEN_COMPRA #id`.

* **Flujo Alternativo**  
El sistema no permite guardar sin proveedor o sin articulos.  
El sistema no permite cantidades o precios invalidos.  
El sistema no permite articulos duplicados.  
El sistema no permite anular ordenes procesadas.  
El sistema no permite anular sin permiso.
El sistema no permite anular sin motivo.

* **Post Condicion**  
La orden de compra queda registrada en cabecera y detalle.  
El presupuesto relacionado puede quedar procesado.  
El sistema emite confirmacion de registro o anulacion.

* **Tablas interactuadas**  
`orden_compra`, `orden_compra_detalle`, `presupuesto_compra`, `presupuesto_detalle`, `proveedores`, `articulos`, `articulo_proveedor`, `usuarios`, `sucursales`, `anulacion_auditoria`

---

## Movimiento "Gestion de Compras"

* **Nombre de Caso de Uso**  
Registrar Compra / Factura de Proveedor

* **Descripcion Basica**  
Permite registrar una factura de proveedor, asociada o no a una orden de compra, con detalle de articulos recibidos, importes, condicion de pago, libro de compras, cuentas a pagar y movimientos de stock.

* **Actores relacionados**  
Personal de Compras / Administracion / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir una sucursal asociada al usuario.  
Debe existir un proveedor activo.  
Si la compra proviene de orden de compra, la orden debe existir y estar habilitada para recepcion.

* **Flujo Basico**

El usuario ingresa al menu de Registro de Compra.  
El sistema valida sesion, sucursal y permisos.

**Registrar factura**

* El usuario selecciona proveedor u orden de compra cuando corresponde.
* Si el usuario selecciona orden de compra, el sistema consulta `orden_compra`, `orden_compra_detalle`, `proveedores`, `articulos` y `tipo_impuesto` para cargar proveedor, articulos pendientes, precios e IVA.
* Si el usuario registra compra directa, el sistema consulta `proveedores` para validar el proveedor seleccionado.
* El usuario ingresa numero de factura, timbrado, fecha, condicion y vencimiento cuando corresponde.
* El usuario confirma cantidad facturada, cantidad recibida, precios, IVA y subtotales.
* El sistema muestra un mensaje de confirmacion de la accion.
* El usuario confirma la accion.
* El sistema valida proveedor. Tabla consultada: `proveedores`.
* El sistema valida que el numero de factura no este registrado previamente para el mismo proveedor y sucursal. Tabla consultada: `compra_cabecera`.
* El sistema valida articulos, codigo, descripcion e impuesto aplicado. Tablas consultadas: `articulos`, `tipo_impuesto`.
* El sistema valida comprobante, timbrado, detalle, cantidades facturadas, cantidades recibidas, precios, IVA y totales.
* El sistema calcula importes fiscales con `cantidad_facturada`.
* El sistema registra cabecera en `compra_cabecera` con estado activo cuando no hay diferencia o estado `Con diferencia` cuando existen cantidades facturadas distintas a las recibidas. El estado `Procesado` no se utiliza para compras.
* El sistema registra detalle en `compra_detalle`, guardando `cantidad_facturada` para lo fiscal/contable y `cantidad_recibida` para lo fisico/stock.
* El sistema actualiza stock solamente por los articulos recibidos. Tabla afectada: `stock`.
* El sistema registra movimientos de entrada en `movimientostock` solamente por la cantidad recibida.
* Si la compra proviene de orden de compra, el sistema descuenta la cantidad recibida de `orden_compra_detalle.cantidad_pendiente`.
* El sistema registra el comprobante en `libro_compra` segun la cantidad facturada.
* Si la compra es a credito, el sistema registra cuentas a pagar segun el total facturado.
* El sistema emite mensaje de confirmacion.

**Ver detalle**

* El usuario ingresa al buscador de compras.
* El usuario busca compras por los filtros disponibles.
* El sistema consulta compras de la sucursal. Tablas consultadas: `compra_cabecera`, `proveedores`, `usuarios`.
* Si el usuario presiona Ver detalle, el sistema consulta cabecera de compra, proveedor y usuario. Tablas consultadas: `compra_cabecera`, `proveedores`, `usuarios`.
* El sistema consulta los articulos recibidos de la compra. Tablas consultadas: `compra_detalle`, `articulos`.
* El sistema consulta el resumen tributario registrado. Tabla consultada: `libro_compra`.
* El sistema consulta el resumen de obligaciones generadas cuando corresponde. Tabla consultada: `cuentas_a_pagar`.
* El sistema muestra en modal factura, proveedor, timbrado, condicion, estado, usuario, articulos, cantidad facturada, cantidad recibida, diferencia, precios, IVA, subtotales, libro de compras y cuentas a pagar.

**Anular**

* El usuario ingresa al buscador de compras.
* El usuario busca compras por los filtros disponibles.
* El sistema consulta compras de la sucursal. Tablas consultadas: `compra_cabecera`, `proveedores`, `usuarios`.
* El usuario selecciona una compra y presiona Anular.
* El sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma la anulacion.
* El sistema valida el permiso de anulacion de compra.
* El sistema verifica que la compra exista, pertenezca a la sucursal y no este previamente anulada. Tabla consultada: `compra_cabecera`.
* El sistema consulta el detalle de la compra para identificar articulos, cantidades y costos a revertir. Tablas consultadas: `compra_detalle`, `compra_cabecera`.
* El sistema verifica que exista stock suficiente para revertir la entrada generada por la compra. Tabla consultada: `stock`.
* El sistema registra movimientos inversos en `movimientostock` por los articulos afectados.
* El sistema anula cuentas a pagar asociadas cuando corresponde. Tablas afectadas/consultadas: `cuentas_a_pagar`, `compra_cabecera`.
* El sistema anula el registro relacionado en `libro_compra`. Tabla afectada: `libro_compra`.
* El sistema actualiza `compra_cabecera` a estado anulado.
* El sistema registra auditoria en `anulacion_auditoria` con modulo `compra`, tabla `compra_cabecera`, ID de compra, sucursal, estado anterior, estado nuevo, motivo, usuario y referencia `COMPRA #id`.
* El sistema emite mensaje de confirmacion.

* **Flujo Alternativo**  
El sistema no permite registrar sin proveedor.  
El sistema no permite registrar sin detalle.  
El sistema no permite facturar ni recibir cantidades mayores a la cantidad pendiente de la orden de compra cuando la compra proviene de una OC.  
El usuario puede usar Ver detalle para consultar articulos, IVA, libro de compras y cuentas a pagar sin modificar el documento.  
El sistema no permite anular sin permiso.  
El sistema no permite anular sin motivo.  
El sistema no permite anular si no puede revertir stock o documentos relacionados.

* **Post Condicion**  
La compra queda registrada con cabecera, detalle, libro de compras, cuentas a pagar y movimientos de stock cuando corresponde.  
Si se anula, el documento queda en estado anulado, se conservan sus datos historicos y queda registrada la auditoria.

* **Tablas interactuadas**  
`compra_cabecera`, `compra_detalle`, `orden_compra`, `orden_compra_detalle`, `proveedores`, `stock`, `movimientostock`, `libro_compra`, `cuentas_a_pagar`, `usuarios`, `sucursales`, `anulacion_auditoria`

---

## Movimiento "Gestion de Nota de Remision"

* **Nombre de Caso de Uso**  
Registrar Nota de Remision

* **Descripcion Basica**  
Permite registrar una nota de remision asociada a una factura de compra, incluyendo datos del transporte, vehiculo, fechas de envio/llegada, motivo y detalle de articulos.

* **Actores relacionados**  
Personal de Compras / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir una sucursal asociada al usuario.  
Debe existir una compra/factura activa en la sucursal.

* **Flujo Basico**

El usuario ingresa al menu Nueva Remision.  
El usuario busca una factura de compra por numero.  
El sistema realiza la busqueda de factura mediante Ajax.  
El sistema consulta `compra_cabecera` y `proveedores`, filtrando compras activas de la sucursal.  
El usuario selecciona la factura.  
El sistema carga los datos de la compra y su detalle. Tablas consultadas: `compra_cabecera`, `compra_detalle`, `proveedores`, `articulos`.
El sistema muestra el detalle de articulos en modo solo lectura, tomando cantidad y costo desde la factura cargada.
El usuario completa datos de remision: numero, transportista, documento, telefono, empresa transportista, RUC, vehiculo, chapa, fechas y motivo.  
El usuario presiona Guardar.  
El sistema muestra un mensaje de confirmacion de la accion.
El usuario confirma la accion.
El sistema valida que la compra exista, este activa y pertenezca a la sucursal. Tabla consultada: `compra_cabecera`.
El sistema valida que la compra tenga detalle para remitir. Tabla consultada: `compra_detalle`.
El sistema arma el detalle de la remision desde la factura cargada en sesion; no toma cantidades ni costos editados desde el formulario.
El sistema valida usuario y sucursal con datos de sesion.
El sistema registra la cabecera en `nota_remision`.  
El sistema registra el detalle en `nota_remision_detalle`.  
El sistema emite mensaje de confirmacion.

**Anular**

* El usuario ingresa al buscador de remisiones.
* El usuario busca remisiones por los filtros disponibles.
* El sistema consulta remisiones activas/procesadas de la sucursal. Tablas consultadas: `nota_remision`, `compra_cabecera`, `usuarios`.
* El usuario selecciona una remision y presiona Anular.
* El sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma.
* El sistema valida el permiso `compra.remision.anular`.
* El sistema verifica que la remision exista, pertenezca a la sucursal y no este anulada. Tabla consultada: `nota_remision`.
* El sistema actualiza la remision a estado anulado.
* El sistema registra usuario y fecha de anulacion.
* El sistema registra la anulacion en `anulacion_auditoria` con modulo `nota_remision`, tabla afectada `nota_remision`, estado anterior, estado nuevo, motivo, usuario, fecha y referencia.

* **Flujo Alternativo**  
El sistema no permite buscar sin ingresar numero de factura.  
El sistema no permite cargar facturas inexistentes o de otra sucursal.  
El sistema no permite modificar manualmente cantidades ni costos del detalle cargado desde la factura.
El sistema no permite anular sin permiso.
El sistema no permite anular sin motivo cuando la tabla de auditoria se encuentra disponible.

* **Post Condicion**  
La nota de remision queda registrada con cabecera y detalle.  
La remision puede quedar activa, procesada o anulada.

* **Tablas interactuadas**  
`nota_remision`, `nota_remision_detalle`, `compra_cabecera`, `compra_detalle`, `proveedores`, `usuarios`, `sucursales`, `anulacion_auditoria`

---

## Movimiento "Notas de Credito y Debito de Compra"

* **Nombre de Caso de Uso**  
Registrar Nota de Credito / Nota de Debito de Compra

* **Descripcion Basica**  
Permite registrar notas de credito o debito asociadas a facturas de compra. La nota impacta en cuentas a pagar, libro de compras y, cuando corresponde, stock por devolucion.

* **Actores relacionados**  
Personal de Compras / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir una factura de compra activa.  
Debe existir una sucursal asociada al usuario.  
Para devolucion de mercaderia debe existir stock suficiente.

* **Flujo Basico**

El usuario ingresa al modulo de Notas de Compra.  
El usuario abre el modal de factura, busca y selecciona una factura de compra.  
El sistema carga los datos del proveedor y el detalle de la factura. Tablas consultadas: `compra_cabecera`, `compra_detalle`, `proveedores`, `articulos`, `tipo_impuesto`.
Si la compra se encuentra en estado `Con diferencia`, el sistema precarga en el detalle de la nota la diferencia entre cantidad facturada y cantidad recibida.
El usuario selecciona tipo de nota: credito o debito.  
El usuario ingresa numero de documento, timbrado, fecha y detalle.  
El sistema recalcula el total de cada item cuando el usuario modifica cantidad o precio.  
El sistema actualiza el detalle temporal de la nota mediante Ajax.  
El sistema muestra un mensaje de confirmacion de la accion.
El usuario confirma la accion.
El sistema valida que el tipo sea credito o debito.  
El sistema valida el movimiento de stock permitido.  
Para nota de debito, el sistema no permite movimiento de devolucion.  
Para nota de credito con devolucion, el sistema valida stock disponible por articulo. Tabla consultada: `stock`.
Para nota de credito sobre una compra con diferencia, el sistema exige movimiento de stock `Sin movimiento`, valida que la nota no supere la diferencia pendiente y exige que cubra exactamente la diferencia para regularizarla.
El sistema valida que la factura exista, pertenezca a la sucursal y este habilitada para recibir la nota. Tabla consultada: `compra_cabecera`.
El sistema valida los articulos de la nota contra el detalle de la compra. Tabla consultada: `compra_detalle`.
El sistema registra cabecera en `nota_compra`.  
El sistema registra detalle en `nota_compra_detalle`.  
El sistema impacta en `cuentas_a_pagar`.  
El sistema registra el comprobante en `libro_compra` como NC o ND.  
Si corresponde devolucion, el sistema descuenta stock y registra movimiento en `movimientostock`.  
Si la nota de credito regulariza completamente una compra con diferencia, el sistema actualiza `compra_cabecera` a estado `Regularizada con NC`.
El sistema confirma la transaccion y emite mensaje.

**Anular**

* El usuario ingresa al buscador de notas de compra.
* El usuario busca notas por los filtros disponibles.
* El sistema consulta notas registradas de la sucursal. Tablas consultadas: `nota_compra`, `compra_cabecera`, `proveedores`, `usuarios`.
* El usuario selecciona una nota y presiona Anular.
* El sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma.
* El sistema valida el permiso `compra.nota.anular`.
* El sistema verifica que la nota exista, pertenezca a la sucursal y no este anulada. Tabla consultada: `nota_compra`.
* El sistema anula la cabecera de nota.
* El sistema genera el impacto inverso en cuentas a pagar. Tabla afectada: `cuentas_a_pagar`.
* Si la nota anulada era una nota de credito sin movimiento de stock que regularizaba una compra con diferencia, el sistema devuelve la compra a estado `Con diferencia`.
* Si la nota fue de credito con devolucion, el sistema consulta el detalle de nota y repone stock. Tablas consultadas/afectadas: `nota_compra_detalle`, `stock`.
* Si corresponde stock, el sistema registra movimiento de anulacion. Tabla afectada: `movimientostock`.
* El sistema anula el registro correspondiente en `libro_compra`. Tabla afectada: `libro_compra`.
* El sistema registra la anulacion en `anulacion_auditoria` con modulo `nota_compra`, tabla afectada `nota_compra`, estado anterior, estado nuevo, motivo, usuario, fecha y referencia.
* El sistema emite mensaje de confirmacion.

* **Flujo Alternativo**  
El sistema no permite tipos de nota invalidos.  
El sistema no permite movimiento de stock invalido.  
El sistema no permite regularizar diferencias con nota de debito ni con nota de credito que mueva stock.  
El sistema no permite que una nota de credito de regularizacion supere la diferencia pendiente ni que quede por debajo de la diferencia requerida.  
El sistema no permite devolucion sin stock suficiente.  
El sistema no permite anular notas ya anuladas.  
El sistema no permite anular sin motivo cuando la tabla de auditoria se encuentra disponible.  
El sistema revierte la transaccion si ocurre un error.

* **Post Condicion**  
La nota queda registrada y asociada a la factura de compra.  
Las cuentas a pagar y libro de compras quedan actualizados.  
El stock se ajusta cuando la nota de credito implica devolucion.
La compra con diferencia queda regularizada cuando la NC sin stock cubre exactamente la diferencia fiscal/fisica.

* **Tablas interactuadas**  
`nota_compra`, `nota_compra_detalle`, `compra_cabecera`, `compra_detalle`, `proveedores`, `cuentas_a_pagar`, `libro_compra`, `stock`, `movimientostock`, `articulos`, `usuarios`, `sucursales`, `anulacion_auditoria`

---

## Movimiento "Transferencias de Stock"

* **Nombre de Caso de Uso**  
Registrar Transferencia entre Sucursales

* **Descripcion Basica**  
Permite enviar articulos desde una sucursal origen hacia una sucursal destino, descontando stock en origen, generando movimientos de stock y permitiendo la recepcion total o parcial en destino.

* **Actores relacionados**  
Usuario de sucursal origen / Usuario de sucursal destino

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir sucursal origen asociada al usuario.  
Debe existir sucursal destino activa y distinta a la origen.  
Deben existir articulos con stock disponible en la sucursal origen.

* **Flujo Basico**

El usuario ingresa al menu Nueva Transferencia.  
El usuario busca la sucursal destino.  
El sistema consulta sucursales activas excluyendo la sucursal origen. Tabla consultada: `sucursales`.
El usuario busca productos.  
El sistema consulta `stock` y `articulos`, mostrando solo productos con stock disponible.  
El usuario ingresa cantidades a transferir.  
El sistema valida en pantalla cantidad invalida, stock superado y producto duplicado.
El sistema muestra un mensaje de confirmacion de guardado y emision de remision.
El usuario confirma la accion.
El sistema obtiene costos de los articulos para la remision y movimientos. Tablas consultadas: `articulos`, `articulo_proveedor`.
El sistema valida stock suficiente en origen. Tabla consultada: `stock`.
El sistema registra cabecera en `transferencia_stock` con estado `en_transito`.  
El sistema registra detalle en `transferencia_stock_detalle`.  
El sistema descuenta stock de la sucursal origen. Tabla afectada: `stock`.
El sistema registra movimiento de salida en `movimientostock`.  
El sistema genera una `nota_remision` y su detalle para el traslado.  
El sistema actualiza la numeracion documental de la sucursal. Tablas consultadas/afectadas: `sucursal_documento`, `timbrado`.
El sistema emite mensaje de transferencia creada.

**Recepcionar transferencia**

* El usuario de la sucursal destino accede a transferencias por recibir.
* El sistema muestra transferencias en estado `en_transito` destinadas a su sucursal. Tabla consultada: `transferencia_stock`.
* El usuario carga cantidades recibidas.
* El sistema calcula en pantalla la diferencia entre cantidad enviada y cantidad recibida.
* Si existen diferencias, el sistema muestra un resumen antes de confirmar la recepcion.
* El sistema valida que la transferencia exista y este en transito. Tabla consultada: `transferencia_stock`.
* El sistema registra las cantidades recibidas en el detalle. Tabla afectada: `transferencia_stock_detalle`.
* El sistema suma stock en la sucursal destino. Tabla afectada: `stock`.
* El sistema registra movimiento de entrada en `movimientostock`.
* Si se recibio todo, el sistema actualiza estado a `recibido`. Tabla afectada: `transferencia_stock`.
* Si existen faltantes, el sistema actualiza estado a `recibido_parcial` y genera una transferencia pendiente por el faltante. Tablas afectadas: `transferencia_stock`, `transferencia_stock_detalle`.
* El sistema emite mensaje de recepcion.

* **Flujo Alternativo**  
El sistema no permite transferir a la misma sucursal.  
El sistema no permite productos sin stock disponible.  
El sistema no permite cantidades mayores al stock.  
El sistema no permite recibir transferencias que no esten en transito.  
El sistema registra recepcion parcial cuando las cantidades recibidas son menores a las enviadas.

* **Post Condicion**  
El stock origen queda descontado al enviar.  
El stock destino queda incrementado al recibir.  
La transferencia queda en estado en transito, recibido o recibido parcial.  
Los movimientos de stock quedan registrados.

* **Tablas interactuadas**  
`transferencia_stock`, `transferencia_stock_detalle`, `stock`, `movimientostock`, `nota_remision`, `nota_remision_detalle`, `articulos`, `sucursales`, `usuarios`, `sucursal_documento`

---

## Movimiento "Inventario y Ajuste de Stock"

* **Nombre de Caso de Uso**  
Generar y Aplicar Ajuste de Inventario

* **Descripcion Basica**  
Permite generar un inventario de articulos, registrar cantidades fisicas, calcular diferencias y aplicar ajustes al stock de la sucursal.

* **Actores relacionados**  
Encargado de Inventario / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir una sucursal asociada al usuario.  
Deben existir articulos activos.  
Para aplicar ajuste, el inventario debe estar modificado y pertenecer a la sucursal activa.

* **Flujo Basico**

El usuario ingresa al modulo Inventario.  
El usuario selecciona tipo de inventario y descripcion.  
El sistema muestra opciones adicionales segun el tipo de inventario seleccionado, como categoria, proveedor o producto.  
El sistema genera cabecera en `ajuste_inventario` con estado pendiente.  
El sistema carga articulos segun el tipo seleccionado.  
El sistema registra detalle en `ajuste_inventario_detalle` con cantidad teorica, cantidad fisica inicial y costo.

**Modificar cantidades**

* El usuario busca un ajuste de inventario.
* El sistema consulta `ajuste_inventario` y `ajuste_inventario_detalle`.
* El usuario carga cantidades fisicas reales.
* El sistema recalcula automaticamente la diferencia en pantalla.
* El sistema guarda los cambios temporales con espera breve para evitar multiples envios consecutivos.
* El sistema valida que el ajuste pertenezca a la sucursal activa.
* El sistema calcula diferencias entre cantidad fisica y teorica.
* El sistema actualiza el detalle.
* El sistema cambia el estado del ajuste a modificado.

**Aplicar ajuste**

* El usuario presiona Aplicar Ajuste.
* El sistema solicita confirmacion antes de impactar el stock.
* El sistema verifica que exista un ajuste seleccionado.
* El sistema verifica que el ajuste pertenezca a la sucursal.
* El sistema verifica que el estado sea valido para aplicar.
* El sistema recorre los detalles con diferencias.
* El sistema actualiza `stock` sumando o restando la diferencia.
* Si no existe stock para un articulo, el sistema crea el registro cuando corresponde.
* El sistema registra movimientos en `movimientostock` con referencia al ajuste.
* El sistema actualiza `ajuste_inventario` a estado ajustado.
* El sistema emite mensaje de ajuste aplicado.

**Anular**

* El usuario accede al listado de inventarios.
* El sistema muestra los ajustes de la sucursal.
* El usuario selecciona un ajuste y presiona Anular.
* El sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma.
* El sistema valida el permiso `inventario.anular`.
* Si el ajuste esta pendiente o modificado, el sistema solo actualiza estado a anulado.
* Si el ajuste ya fue aplicado, el sistema revierte las diferencias en stock.
* El sistema registra movimientos de anulacion de ajuste.
* El sistema actualiza el ajuste a estado anulado.
* El sistema registra la anulacion en `anulacion_auditoria` con modulo `ajuste_inventario`, tabla afectada `ajuste_inventario`, estado anterior, estado nuevo, motivo, usuario, fecha y referencia.
* El sistema emite mensaje de confirmacion.

* **Flujo Alternativo**  
El sistema no permite generar inventario sin tipo definido.  
El sistema no permite aplicar ajustes sin articulos.  
El sistema no permite aplicar ajustes sin diferencias reales.  
El sistema no permite aplicar ajustes que no pertenezcan a la sucursal.  
El sistema no permite aplicar ajustes en estado invalido.  
El sistema no permite anular sin motivo cuando la tabla de auditoria se encuentra disponible.  
El sistema revierte el stock si se anula un ajuste ya aplicado.

* **Post Condicion**  
El inventario queda registrado en cabecera y detalle.  
El stock se actualiza al aplicar diferencias.  
Los movimientos de ajuste y anulacion quedan registrados.

* **Tablas interactuadas**  
`ajuste_inventario`, `ajuste_inventario_detalle`, `stock`, `movimientostock`, `articulos`, `categorias`, `proveedores`, `sucursales`, `usuarios`, `anulacion_auditoria`

---

## Movimiento "Gestion de Recepcion de Servicio"

* **Nombre de Caso de Uso**  
Registrar Recepcion de Servicio

* **Descripcion Basica**  
Permite ingresar al taller un vehiculo asociado a un cliente, registrar los datos iniciales de recepcion y dejar habilitado el flujo posterior de diagnostico, presupuesto, orden de trabajo y registro de servicio. La recepcion es el punto de entrada operativo cuando el vehiculo ya se encuentra en el taller.

* **Actores relacionados**  
Recepcionista / Asesor de servicio / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir una sucursal asociada al usuario.  
Debe existir un cliente y un vehiculo activo, o deben cargarse rapidamente desde la misma recepcion.  
Si la recepcion proviene de un reclamo, debe existir un reclamo de servicio activo.

* **Flujo Basico**

El usuario ingresa al menu Nueva Recepcion de Servicio.  
El sistema valida sesion y sucursal.

**Seleccionar o cargar cliente**

* El usuario busca un cliente por documento, nombre, apellido o telefono.
* El sistema consulta `clientes` y muestra documento, nombre, apellido y telefono para seleccionar rapidamente.
* Para alta rapida o autocompletado de ciudad, el sistema consulta `ciudades`.
* Si el cliente no existe, el usuario puede registrarlo desde la recepcion con datos minimos: tipo de documento, documento, nombre, apellido y telefono.
* El sistema valida que el documento del cliente no este duplicado. Tabla consultada: `clientes`.
* El sistema registra el cliente en `clientes` y lo deja seleccionado en la recepcion.

**Seleccionar o cargar vehiculo**

* El usuario busca los vehiculos asociados al cliente.
* El sistema consulta `vehiculos`, `modelo_auto` y `marcas` para exponer placa, modelo, marca, color, anho y propietario.
* Si el vehiculo no existe, el usuario puede registrarlo desde la recepcion con datos esenciales para carga rapida.
* Para registrar vehiculo, el sistema consulta `modelo_auto` para validar el modelo seleccionado.
* El sistema valida que la placa no este duplicada. Tabla consultada: `vehiculos`.
* El sistema registra el vehiculo en `vehiculos` y lo vincula al cliente seleccionado.

**Recepcion desde reclamo**

* Si el origen es reclamo, el usuario busca reclamos pendientes por numero, cliente, documento, telefono, placa o tipo de reclamo.
* El sistema consulta `reclamo_servicio`, `clientes`, `vehiculos` y `modelo_auto` para mostrar reclamo, fecha, cliente, documento, telefono, placa, modelo y tipo.
* Al seleccionar el reclamo, el sistema consulta `reclamo_servicio`, `clientes`, `vehiculos`, `modelo_auto` y `marcas` para cargar los datos completos del cliente y vehiculo.
* El sistema valida que el reclamo este activo y disponible para recepcion. Tabla consultada: `reclamo_servicio`.

**Registrar datos de ingreso**

* El usuario completa kilometraje, nivel de combustible, estado exterior, objetos dentro del vehiculo, tipo de servicio, area del problema, prioridad, accesorios y observacion.
* El sistema registra la cabecera en `recepcion_servicio` con `id_cliente`, `id_vehiculo`, usuario, sucursal, fecha de ingreso y estado activo.
* Si se adjuntan fotos, el sistema registra las rutas en `recepcion_fotos`.
* Si la recepcion viene desde un reclamo, el sistema valida garantia y origen consultando `reclamo_servicio` y `registro_servicio`.
* Si la recepcion viene desde un reclamo, el sistema marca el origen como reclamo, guarda `idreclamo_servicio` y actualiza el estado del reclamo. Tabla afectada: `reclamo_servicio`.
* El sistema emite mensaje de confirmacion.

**Buscar recepciones**

* El usuario busca recepciones por numero, fecha, cliente, documento, placa, estado, origen, usuario, tipo de servicio o prioridad.
* El sistema consulta `recepcion_servicio` como tabla principal.
* El sistema une `clientes`, `vehiculos`, `modelo_auto`, `marcas` y `usuarios` para mostrar cliente, documento, telefono, vehiculo, marca, modelo, placa, usuario receptor, estado, origen, prioridad y fecha.
* El sistema consulta `recepcion_fotos` para indicar si la recepcion posee fotos asociadas.

**Anular**

* El usuario accede al listado de recepciones.
* El sistema muestra recepciones de la sucursal del usuario. Tablas consultadas: `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `recepcion_fotos`.
* El usuario selecciona una recepcion y presiona Anular.
* El sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma la anulacion.
* El sistema valida el permiso de anulacion de recepcion.
* El sistema verifica existencia, sucursal, estado y reclamo relacionado cuando exista. Tabla consultada: `recepcion_servicio`.
* El sistema actualiza la recepcion a estado anulado.
* Si la recepcion proviene de reclamo, el sistema reabre el reclamo relacionado. Tabla afectada: `reclamo_servicio`.
* El sistema registra auditoria en `anulacion_auditoria` con modulo `recepcion_servicio`, tabla `recepcion_servicio`, ID de recepcion, sucursal, estado anterior, estado nuevo, motivo, usuario y referencia `RECEPCION #id`.

* **Flujo Alternativo**  
El sistema no permite guardar sin cliente.  
El sistema no permite guardar sin vehiculo.  
El sistema no permite vehiculos sin cliente asociado.  
El sistema no permite anular recepciones ya procesadas.  
El sistema no permite anular sin motivo.  
El sistema revierte la transaccion si falla el alta rapida de cliente o vehiculo.

* **Post Condicion**  
La recepcion queda registrada y disponible para diagnostico.  
El cliente y vehiculo quedan identificados directamente en la recepcion.  
La informacion inicial de kilometraje, combustible y estado del vehiculo queda como dato propio del ingreso al taller, no como dato maestro del vehiculo.

* **Tablas interactuadas**  
`recepcion_servicio`, `recepcion_fotos`, `clientes`, `ciudades`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `reclamo_servicio`, `registro_servicio`, `anulacion_auditoria`

---

## Movimiento "Gestion de Diagnostico de Servicio"

* **Nombre de Caso de Uso**  
Registrar Diagnostico de Servicio

* **Descripcion Basica**  
Permite evaluar una recepcion de servicio y documentar los problemas detectados por sistema del vehiculo, gravedad, solucion propuesta y si requiere repuestos o mano de obra. El diagnostico funciona como base tecnica para generar un presupuesto formal.

* **Actores relacionados**  
Tecnico / Jefe de taller / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir una recepcion activa en la sucursal.  
Debe existir un equipo de trabajo activo.  
La recepcion no debe tener un diagnostico activo duplicado.

* **Flujo Basico**

El usuario ingresa al menu Nuevo Diagnostico.  
El sistema muestra recepciones pendientes de diagnostico.

**Seleccionar recepcion**

* El usuario busca una recepcion por cliente, vehiculo, chapa o numero.
* El sistema consulta `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto` y `marcas`.
* La busqueda muestra numero de recepcion, cliente, documento, placa, marca, modelo, tipo de servicio, prioridad, observacion y si proviene de reclamo.
* El usuario selecciona la recepcion.
* El sistema vuelve a consultar `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto` y `marcas` para cargar datos de cliente, vehiculo, kilometraje, combustible, tipo de servicio, prioridad, observacion del ingreso y `idreclamo_servicio` cuando corresponda.

**Registrar diagnostico**

* El usuario selecciona el equipo de trabajo.
* El sistema consulta `equipo_trabajo` para cargar los equipos disponibles de la sucursal.
* El usuario carga observaciones generales y detalles tecnicos.
* En cada detalle se indica servicio/trabajo, problema, gravedad, repuesto requerido, cantidad y origen del repuesto.
* Para seleccionar servicio o repuesto, el sistema consulta `articulos`, filtrando por tipo segun corresponda.
* La fecha y el estado del diagnostico son controlados por el sistema; no son campos seleccionables por el usuario.
* El sistema valida que la recepcion este disponible para diagnostico. Tabla consultada: `recepcion_servicio`.
* El sistema valida los articulos seleccionados en el detalle. Tabla consultada: `articulos`.
* El sistema registra la cabecera en `diagnostico_servicio` con fecha actual, estado activo, usuario, recepcion, equipo y sucursal.
* El sistema registra los detalles en `diagnostico_detalle`.
* El sistema actualiza la recepcion para indicar que ya fue diagnosticada.
* Si el diagnostico corresponde a un reclamo, el sistema puede validar garantia por kilometraje consultando `reclamo_servicio`, `registro_servicio`, `orden_trabajo`, `presupuesto_servicio`, `diagnostico_servicio` y `recepcion_servicio`.

**Buscar diagnosticos y ver detalle**

* El usuario busca diagnosticos por fechas, cliente, placa, numero de diagnostico, numero de recepcion, estado, origen o texto general.
* El sistema consulta `diagnostico_servicio` como tabla principal.
* El sistema une `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios` y `equipo_trabajo` para mostrar recepcion, cliente, vehiculo, equipo, usuario, origen, estado y observaciones.
* Al ver detalle, el sistema consulta `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `equipo_trabajo` y `articulos`.
* Si el diagnostico esta asociado a reclamo, el sistema consulta `reclamo_servicio`, `reclamo_servicio_detalle`, `registro_servicio_detalle` y `articulos` para exponer el detalle reclamado.

**Anular**

* El usuario accede al listado de diagnosticos.
* El sistema muestra diagnosticos de la sucursal. Tablas consultadas: `diagnostico_servicio`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `equipo_trabajo`.
* El usuario selecciona un diagnostico y presiona Anular.
* El sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma.
* El sistema valida el permiso `servicio.diagnostico.anular`.
* El sistema verifica existencia, estado y sucursal. Tabla consultada: `diagnostico_servicio`.
* El sistema verifica que no exista un presupuesto activo relacionado. Tabla consultada: `presupuesto_servicio`.
* Si el diagnostico pertenece a un reclamo, el sistema valida tambien que no exista una orden de trabajo activa por reclamo. Tablas consultadas: `recepcion_servicio`, `orden_trabajo`.
* El sistema actualiza el diagnostico a estado anulado y libera la recepcion cuando corresponde. Tablas afectadas: `diagnostico_servicio`, `recepcion_servicio`.
* El sistema registra la anulacion en `anulacion_auditoria` con modulo `diagnostico_servicio`, tabla afectada `diagnostico_servicio`, estado anterior, estado nuevo, motivo, usuario, fecha y referencia.

* **Flujo Alternativo**  
El sistema no permite diagnosticar recepciones anuladas.  
El sistema no permite guardar sin equipo de trabajo.  
El sistema no permite guardar sin al menos un detalle tecnico valido.  
El sistema no permite anular diagnosticos que ya tengan presupuesto u orden de trabajo activa.
El sistema no permite anular sin motivo cuando la tabla de auditoria se encuentra disponible.

* **Post Condicion**  
El diagnostico queda registrado con cabecera y detalle tecnico.  
La recepcion queda vinculada al diagnostico.  
El diagnostico queda disponible para generar presupuesto de servicio.

* **Tablas interactuadas**  
`diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `reclamo_servicio`, `reclamo_servicio_detalle`, `registro_servicio`, `registro_servicio_detalle`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `equipo_trabajo`, `usuarios`, `presupuesto_servicio`, `orden_trabajo`, `anulacion_auditoria`

---

## Movimiento "Gestion de Presupuesto de Servicio"

* **Nombre de Caso de Uso**  
Registrar Presupuesto de Servicio

* **Descripcion Basica**  
Permite generar una propuesta economica de trabajos y repuestos para un cliente y vehiculo. El presupuesto puede originarse desde un diagnostico o como presupuesto preliminar antes de la recepcion formal. Desde los cambios recientes, el presupuesto guarda directamente `id_cliente` e `id_vehiculo` para independizar el documento de consultas lejanas.

* **Actores relacionados**  
Asesor de servicio / Administracion / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir una sucursal asociada al usuario.  
Debe existir cliente y vehiculo.  
Si el origen es diagnostico, debe existir un diagnostico activo.  
Si el origen es preliminar, el presupuesto puede cargarse directamente con cliente, vehiculo y articulos.

* **Flujo Basico**

El usuario ingresa al menu Nuevo Presupuesto de Servicio.  
El sistema valida sesion y sucursal.

**Presupuesto desde diagnostico**

* El usuario busca un diagnostico disponible.
* El sistema consulta `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto` y `marcas`.
* El sistema consulta `orden_trabajo` para excluir diagnosticos que ya tengan una OT activa relacionada.
* El usuario selecciona el diagnostico.
* El sistema copia al presupuesto el cliente y vehiculo de la recepcion.
* El sistema muestra los detalles tecnicos como referencia para cargar trabajos y repuestos.
* El sistema consulta `diagnostico_detalle`, `articulos` y `stock` para mostrar servicios/repuestos sugeridos y disponibilidad cuando corresponda.

**Presupuesto preliminar**

* El usuario selecciona cliente y vehiculo sin requerir diagnostico previo.
* El sistema valida que el vehiculo exista y pertenezca al cliente seleccionado. Tablas consultadas: `vehiculos`, `clientes`.
* El sistema marca el presupuesto con origen `PRELIMINAR`.
* El presupuesto preliminar permite entregar una estimacion inicial al cliente.
* Si el presupuesto preliminar avanza a una revision real del taller, debe vincularse a una recepcion y diagnostico formal antes de continuar el proceso.

**Agregar trabajos y repuestos**

* El usuario busca articulos o servicios por codigo o descripcion.
* El sistema consulta `articulos` y `stock`, mostrando descripcion, codigo, tipo, precio y existencia disponible en sucursal.
* El sistema consulta `promociones` y `promocion_producto` para beneficios vigentes por articulo.
* El sistema consulta `descuentos` y `descuento_cliente` para beneficios comerciales aplicables al cliente.
* El usuario ingresa cantidad y precio unitario.
* El sistema valida duplicidad, cantidad y precio.
* El sistema valida stock disponible para productos/repuestos. Tabla consultada: `stock`.
* El sistema calcula subtotal, descuentos, promociones y total final en pantalla.
* Si existen descuentos o promociones aplicables, el sistema registra la relacion en las tablas correspondientes.
* El sistema registra la cabecera en `presupuesto_servicio` con `id_cliente`, `id_vehiculo`, origen, sucursal, usuario, fecha, vencimiento, subtotal, descuento y total final.
* El sistema registra el detalle en `presupuesto_detalleservicio`.
* Si corresponde, el sistema registra promociones en `presupuesto_promocion` y descuentos en `presupuesto_descuento`.

**Buscar, ver detalle e imprimir**

* El usuario busca presupuestos por fechas, cliente, placa, numero, estado u origen.
* El sistema consulta `presupuesto_servicio` como tabla principal.
* El sistema une `clientes`, `vehiculos`, `modelo_auto` y `usuarios` para mostrar cliente, vehiculo, fecha, total, estado, origen y usuario.
* Al ver detalle o generar PDF, el sistema consulta `presupuesto_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `presupuesto_detalleservicio` y `articulos`.
* Si existen beneficios aplicados, el sistema consulta `presupuesto_promocion`, `promociones`, `presupuesto_descuento` y `descuentos`.

**Aprobar y anular**

* El usuario puede aprobar el presupuesto cuando el cliente acepta la propuesta.
* El sistema actualiza el estado del presupuesto aprobado. Tabla afectada: `presupuesto_servicio`.
* El usuario puede anular presupuestos que no tengan una orden de trabajo activa relacionada.
* Al presionar Anular, el sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma la anulacion.
* El sistema valida el permiso de anulacion de presupuesto de servicio.
* El sistema verifica existencia, sucursal y estado. Tabla consultada: `presupuesto_servicio`.
* El sistema verifica que no exista orden de trabajo activa relacionada. Tabla consultada: `orden_trabajo`.
* El sistema actualiza `presupuesto_servicio` a estado anulado.
* Si el presupuesto proviene de diagnostico, el sistema puede liberar el diagnostico relacionado. Tabla afectada: `diagnostico_servicio`.
* Si el presupuesto proviene de otro presupuesto preliminar o conversion relacionada, el sistema actualiza la relacion operativa cuando corresponde. Tabla afectada: `presupuesto_servicio`.
* El sistema registra auditoria en `anulacion_auditoria` con modulo `presupuesto_servicio`, tabla `presupuesto_servicio`, ID del presupuesto, sucursal, estado anterior, estado nuevo, motivo, usuario y referencia `PRESUPUESTO_SERVICIO #id`.

* **Flujo Alternativo**  
El sistema no permite guardar sin cliente ni vehiculo.  
El sistema no permite guardar sin detalle.  
El sistema no permite usar un presupuesto preliminar como orden operativa del taller.  
El sistema no permite anular presupuestos ya procesados.
El sistema no permite anular sin motivo.

* **Post Condicion**  
El presupuesto queda registrado con identidad propia de cliente y vehiculo.  
El detalle economico queda disponible para PDF, aprobacion y consulta comercial.  
El presupuesto puede quedar preliminar, activo, aprobado o anulado segun el flujo.

* **Tablas interactuadas**  
`presupuesto_servicio`, `presupuesto_detalleservicio`, `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `stock`, `descuentos`, `descuento_cliente`, `promociones`, `promocion_producto`, `presupuesto_descuento`, `presupuesto_promocion`, `orden_trabajo`, `usuarios`, `anulacion_auditoria`

---

## Movimiento "Gestion de Promociones"

* **Nombre de Caso de Uso**  
Registrar y Gestionar Promocion

* **Descripcion Basica**  
Permite registrar promociones aplicables a articulos o servicios. La promocion define tipo de beneficio, valor, vigencia, sucursal y articulos alcanzados, para luego aplicarse en presupuestos de servicio.

* **Actores relacionados**  
Administracion / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Deben existir articulos activos para asociar a la promocion.

* **Flujo Basico**

* El usuario carga nombre, tipo, valor, vigencia, sucursal, descripcion y estado.
* El sistema permite buscar articulos activos por codigo o descripcion.
* El sistema registra o actualiza la cabecera en `promociones`.
* El sistema registra, reemplaza o elimina articulos asociados en `promocion_producto`.
* El listado expone usuario creador/modificador y sucursal cuando corresponde.

* **Post Condicion**  
La promocion queda disponible para presupuestos de servicio si esta activa, vigente y asociada al articulo.

* **Tablas interactuadas**  
`promociones`, `promocion_producto`, `articulos`, `usuarios`, `sucursales`

---

## Movimiento "Gestion de Descuentos"

* **Nombre de Caso de Uso**  
Registrar y Gestionar Descuento

* **Descripcion Basica**  
Permite registrar descuentos comerciales y asociarlos a clientes especificos, para aplicarlos en presupuestos de servicio.

* **Actores relacionados**  
Administracion / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Deben existir clientes activos para asignar descuentos.

* **Flujo Basico**

* El usuario carga nombre, tipo, valor, descripcion, aplicacion, vigencia, sucursal y estado.
* El sistema permite buscar clientes por documento, nombre o apellido.
* El sistema registra o actualiza la cabecera en `descuentos`.
* El sistema registra o elimina clientes asociados en `descuento_cliente`.
* El listado expone usuario creador/modificador y sucursal cuando corresponde.

* **Post Condicion**  
El descuento queda disponible para presupuestos de servicio si esta activo, vigente y asociado al cliente.

* **Tablas interactuadas**  
`descuentos`, `descuento_cliente`, `clientes`, `usuarios`, `sucursales`

---

## Movimiento "Gestion de Orden de Trabajo"

* **Nombre de Caso de Uso**  
Generar y Gestionar Orden de Trabajo

* **Descripcion Basica**  
Permite generar una orden operativa para el taller. El flujo normal genera la OT desde un presupuesto de servicio aprobado y originado en diagnostico. Tambien existe el flujo por reclamo, donde la OT se genera desde un reclamo valido en garantia y sin cobro. La orden de trabajo muestra cliente, vehiculo, origen, equipo, tecnico responsable, trabajos autorizados y repuestos/productos a utilizar.

* **Actores relacionados**  
Jefe de taller / Tecnico / Asesor de servicio / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir una sucursal asociada al usuario.  
Para OT normal, debe existir un presupuesto aprobado, de origen diagnostico y sin OT activa relacionada.  
Para OT por reclamo, debe existir un reclamo en proceso con recepcion y diagnostico activo.  
Para OT por reclamo, el diagnostico debe indicar reclamo valido, garantia aplicable y sin cobro.  
Deben existir equipo de trabajo y, opcionalmente, tecnico responsable segun el flujo.

* **Flujo Basico**

El usuario ingresa al menu de Orden de Trabajo o al buscador/detalle de diagnosticos y reclamos.  
El sistema valida sesion y sucursal.

**Generar desde presupuesto aprobado**

* El usuario busca un presupuesto aprobado por cliente, vehiculo, chapa o modelo.
* El sistema usa `presupuesto_servicio` como fuente principal de cliente, vehiculo, sucursal, estado y origen.
* El sistema consulta `clientes`, `vehiculos` y `modelo_auto` para exponer y buscar datos visibles del cliente y vehiculo.
* El sistema consulta `diagnostico_servicio` y `recepcion_servicio` como trazabilidad del origen diagnostico.
* El sistema consulta `orden_trabajo` para validar que el presupuesto no tenga una OT activa relacionada.
* El sistema muestra solo presupuestos aprobados, de origen diagnostico y sin OT activa relacionada.
* El usuario selecciona el presupuesto.
* El sistema carga cliente, vehiculo, fecha y detalle autorizado desde `presupuesto_detalleservicio` y `articulos`.
* El usuario selecciona equipo, tecnico responsable y observacion operativa.
* El sistema consulta `equipo_trabajo` para listar equipos disponibles.
* Al seleccionar equipo, el sistema consulta `equipo_empleado` y `empleados` para listar tecnicos del equipo.
* El sistema registra la cabecera en `orden_trabajo` y copia el detalle del presupuesto a `orden_trabajo_detalle`.
* El sistema actualiza `presupuesto_servicio` a procesado.

**Generar desde diagnostico de reclamo en garantia**

* El usuario selecciona un diagnostico asociado a un reclamo.
* El sistema consulta `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `reclamo_servicio`, `registro_servicio`, `orden_trabajo`, `clientes`, `vehiculos` y `articulos`.
* El sistema valida que el diagnostico pertenezca a un reclamo activo.
* El sistema valida que el diagnostico tenga reclamo valido, garantia aplicable y que no requiera cobro.
* El sistema valida garantia por kilometraje consultando `reclamo_servicio`, `registro_servicio`, `orden_trabajo`, `presupuesto_servicio`, `diagnostico_servicio` y `recepcion_servicio`.
* El sistema valida que no exista una OT activa para el mismo reclamo.
* El sistema registra la cabecera en `orden_trabajo` con origen `RECLAMO`, `id_cliente`, `id_vehiculo`, usuario, sucursal y estado operativo.
* El sistema deja `idpresupuesto_servicio` sin valor porque esta OT no nace de presupuesto.
* El sistema actualiza el reclamo y el diagnostico para reflejar que ya se genero la orden.
* El usuario puede completar o ajustar trabajos y repuestos autorizados para la ejecucion cuando corresponda.
* Cuando se agregan repuestos/productos, el sistema valida `articulos` y `stock` antes de guardar el detalle.

**Asignar y cerrar**

* El usuario puede asignar o modificar equipo de trabajo y tecnico responsable.
* El sistema permite consultar el detalle operativo de la orden. Tablas consultadas: `orden_trabajo`, `orden_trabajo_detalle`, `articulos`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `presupuesto_servicio`, `diagnostico_servicio`, `recepcion_servicio`, `reclamo_servicio`, `equipo_trabajo`, `equipo_empleado`, `empleados`.
* Al finalizar la ejecucion, la orden queda disponible para registro de servicio.
* El sistema no permite anular una OT que ya tenga registro de servicio activo.

**Buscar ordenes**

* El usuario busca ordenes por fechas, cliente, placa, numero, estado u origen.
* El sistema consulta `orden_trabajo` como tabla principal.
* El sistema une `presupuesto_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios` y `equipo_trabajo` para mostrar presupuesto/reclamo, cliente, vehiculo, fecha, equipo, tecnico, usuario, total, estado y origen.
* Para totales operativos, el sistema consulta `orden_trabajo_detalle`.

**Anular**

* El usuario ingresa al buscador de ordenes de trabajo.
* El sistema muestra ordenes segun sucursal, estado y permisos. Tablas consultadas: `orden_trabajo`, `presupuesto_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `equipo_trabajo`.
* El usuario selecciona una orden y presiona Anular.
* El sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma la anulacion.
* El sistema valida el permiso de anulacion de orden de trabajo.
* El sistema verifica existencia, estado y que no exista registro de servicio activo relacionado. Tablas consultadas: `orden_trabajo`, `registro_servicio`.
* El sistema actualiza `orden_trabajo` a estado anulado.
* Si la OT proviene de presupuesto, el sistema libera o actualiza el presupuesto relacionado cuando corresponde. Tabla afectada: `presupuesto_servicio`.
* Si la OT proviene de diagnostico, el sistema puede reabrir el diagnostico y la recepcion relacionada. Tablas afectadas: `diagnostico_servicio`, `recepcion_servicio`.
* Si la OT proviene de reclamo, el sistema reabre los movimientos relacionados cuando corresponde. Tablas afectadas: `reclamo_servicio`, `recepcion_servicio`, `diagnostico_servicio`.
* El sistema registra auditoria en `anulacion_auditoria` con modulo `orden_trabajo`, tabla `orden_trabajo`, ID de OT, estado anterior, estado nuevo, motivo, usuario y referencia `OT #id`.

* **Flujo Alternativo**  
El sistema no permite generar OT sin cliente ni vehiculo.  
El sistema no permite generar OT normal desde presupuestos no aprobados o preliminares sin diagnostico.  
El sistema no permite duplicar una OT activa para el mismo presupuesto.  
El sistema no permite generar OT por reclamo desde diagnosticos que no correspondan a reclamo.  
El sistema no permite generar OT si el reclamo requiere cobro.  
El sistema no permite generar OT si el reclamo no aplica garantia.  
El sistema no permite duplicar una OT activa para el mismo reclamo.  
El sistema no permite anular OT con registro de servicio activo.
El sistema no permite anular sin motivo.

* **Post Condicion**  
La orden queda registrada con cliente, vehiculo y origen propios.  
Los trabajos y repuestos quedan copiados desde presupuesto o cargados manualmente para reclamo, segun el origen.  
La orden queda disponible para asignacion, seguimiento y registro de servicio.

* **Tablas interactuadas**  
`orden_trabajo`, `orden_trabajo_detalle`, `presupuesto_servicio`, `presupuesto_detalleservicio`, `presupuesto_promocion`, `promociones`, `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `reclamo_servicio`, `registro_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `stock`, `equipo_trabajo`, `equipo_empleado`, `empleados`, `usuarios`, `anulacion_auditoria`

---

## Movimiento "Gestion de Registro de Servicio"

* **Nombre de Caso de Uso**  
Registrar Servicio Ejecutado

* **Descripcion Basica**  
Permite cerrar operativamente una orden de trabajo registrando la fecha de ejecucion, observaciones y detalle final de trabajos/repuestos utilizados. El registro guarda directamente cliente y vehiculo para que el historial del servicio no dependa de reconstruir toda la cadena desde presupuesto, diagnostico o recepcion.

* **Actores relacionados**  
Jefe de taller / Tecnico / Administracion / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir una orden de trabajo activa en la sucursal.  
La orden no debe tener un registro de servicio activo duplicado.  
Debe existir cliente y vehiculo en la orden.

* **Flujo Basico**

El usuario ingresa al menu Nuevo Registro de Servicio.  
El sistema muestra ordenes de trabajo disponibles para registrar.

**Seleccionar OT**

* El usuario busca una orden por numero, cliente, vehiculo o chapa.
* El sistema consulta `orden_trabajo`, `clientes`, `vehiculos`, `modelo_auto`, `equipo_trabajo` y `empleados` para mostrar orden, cliente, vehiculo, equipo, tecnico, fecha y estado.
* El sistema consulta `orden_trabajo_detalle` y `articulos` para mostrar trabajos/repuestos autorizados.
* El usuario selecciona la orden.
* El sistema carga el detalle operativo autorizado.

**Registrar ejecucion**

* El usuario indica fecha de ejecucion y observaciones finales.
* El sistema copia `id_cliente` e `id_vehiculo` desde la orden de trabajo.
* El sistema valida que no exista un registro activo duplicado para la OT. Tabla consultada: `registro_servicio`.
* El sistema consulta `orden_trabajo` para validar estado, sucursal, origen, cliente y vehiculo.
* El sistema consulta `orden_trabajo_detalle` y `articulos` para copiar el detalle final.
* El sistema registra la cabecera en `registro_servicio`.
* El sistema registra el detalle final en `registro_servicio_detalle`, separando el origen cuando corresponde.
* Para articulos de tipo producto/insumo, el sistema valida y descuenta stock. Tabla afectada/consultada: `stock`.
* El sistema registra movimientos de salida en `movimientostock`.
* El sistema actualiza la orden de trabajo como finalizada.
* Si la orden proviene de reclamo, el sistema actualiza tambien el estado del reclamo y de los movimientos relacionados cuando corresponda.

**Buscar y ver detalle**

* El usuario busca registros por fechas, cliente, vehiculo, placa o numero.
* El sistema consulta `registro_servicio` como tabla principal.
* El sistema une `clientes`, `vehiculos`, `modelo_auto` y `usuarios` para mostrar cliente, vehiculo, usuario, fecha, estado, kilometraje y OT relacionada.
* Al ver detalle, el sistema consulta `registro_servicio`, `registro_servicio_detalle`, `orden_trabajo`, `clientes`, `vehiculos`, `modelo_auto`, `usuarios` y `articulos`.

**Anular**

* El usuario accede al listado de registros de servicio.
* El sistema muestra registros de la sucursal. Tablas consultadas: `registro_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `usuarios`.
* El usuario selecciona un registro y presiona Anular.
* El sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma la anulacion.
* El sistema valida el permiso de anulacion de registro de servicio.
* El sistema verifica existencia, sucursal, estado y OT relacionada. Tabla consultada: `registro_servicio`.
* El sistema consulta `registro_servicio_detalle` y `articulos` para identificar articulos con stock a revertir.
* El sistema actualiza el registro a anulado.
* El sistema revierte movimientos de stock y registra movimiento inverso. Tablas afectadas: `stock`, `movimientostock`.
* El sistema reabre la orden de trabajo y reabre recepcion/reclamo cuando corresponde. Tablas afectadas: `orden_trabajo`, `recepcion_servicio`, `reclamo_servicio`.
* El sistema registra auditoria en `anulacion_auditoria` con modulo `registro_servicio`, tabla `registro_servicio`, ID del registro, sucursal, estado anterior, estado nuevo, motivo, usuario y referencia `REGISTRO_SERVICIO #id`.

* **Flujo Alternativo**  
El sistema no permite registrar sin orden de trabajo.  
El sistema no permite duplicar registros activos para la misma OT.  
El sistema no permite guardar sin fecha de ejecucion.  
El sistema no permite anular registros inexistentes o ya anulados.
El sistema no permite anular sin motivo.

* **Post Condicion**  
El servicio queda registrado como historial ejecutado del cliente y vehiculo.  
La orden queda finalizada o reabierta segun el flujo.  
El registro queda disponible para reclamos, garantias e informes.

* **Tablas interactuadas**  
`registro_servicio`, `registro_servicio_detalle`, `orden_trabajo`, `orden_trabajo_detalle`, `presupuesto_servicio`, `diagnostico_servicio`, `recepcion_servicio`, `reclamo_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `stock`, `movimientostock`, `equipo_trabajo`, `empleados`, `usuarios`, `anulacion_auditoria`

---

## Movimiento "Gestion de Salida de Insumos"

* **Nombre de Caso de Uso**  
Registrar Salida de Insumos

* **Descripcion Basica**  
Permite registrar la salida de articulos clasificados como insumo, descontando stock de la sucursal y dejando trazabilidad en movimientos de stock. Se usa para consumos internos del taller o utilizacion de insumos no asociados directamente a un registro de servicio.

* **Actores relacionados**  
Encargado de taller / Administracion / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir una sucursal asociada al usuario.  
Deben existir insumos activos con stock disponible.  
Debe existir empleado responsable cuando el flujo lo requiera.

* **Flujo Basico**

El usuario ingresa al menu Registro de Insumos.  
El sistema valida sesion, sucursal y permiso `servicio.insumo.crear`.

**Registrar salida**

* El usuario busca insumos por codigo o descripcion.
* El sistema consulta `articulos` y `stock`, filtrando articulos de tipo `insumo` activos y con existencia en la sucursal.
* El usuario selecciona insumo, cantidad y empleado responsable cuando corresponde.
* El sistema valida cantidad mayor a cero, stock disponible y que el insumo no este duplicado en el detalle.
* El usuario carga observacion si corresponde.
* El sistema registra cabecera en `salida_insumo`.
* El sistema registra detalle en `salida_insumo_detalle`.
* El sistema descuenta stock de cada insumo.
* El sistema registra movimiento de salida en `movimientostock`.
* El sistema emite mensaje de confirmacion.

**Anular**

* El usuario accede al listado de salidas de insumos.
* El sistema muestra salidas de la sucursal segun filtros.
* El usuario selecciona una salida activa y presiona Anular.
* El sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma la anulacion.
* El sistema valida permiso `servicio.insumo.anular`.
* El sistema verifica que la salida exista, pertenezca a la sucursal y este activa.
* El sistema obtiene el detalle de insumos.
* El sistema registra movimientos inversos de entrada en `movimientostock` con tipo `ANUL SALIDA INSUMO`.
* El sistema actualiza `salida_insumo` a estado anulado.
* El sistema registra auditoria en `anulacion_auditoria` con modulo `salida_insumo`, tabla `salida_insumo`, ID de salida, sucursal, estado anterior, estado nuevo, motivo, usuario y referencia `SALIDA_INSUMO #id`.
* El sistema emite mensaje de confirmacion.

* **Flujo Alternativo**  
El sistema no permite registrar sin insumos.  
El sistema no permite cantidades invalidas.  
El sistema no permite descontar mas stock del disponible.  
El sistema no permite anular salidas de otra sucursal.  
El sistema no permite anular salidas ya anuladas.  
El sistema no permite anular sin motivo.

* **Post Condicion**  
La salida queda registrada con cabecera y detalle.  
El stock se descuenta al registrar y se repone al anular.  
Los movimientos de stock y la auditoria de anulacion quedan disponibles para trazabilidad.

* **Tablas interactuadas**  
`salida_insumo`, `salida_insumo_detalle`, `articulos`, `stock`, `movimientostock`, `empleados`, `usuarios`, `sucursales`, `anulacion_auditoria`

---

## Movimiento "Gestion de Reclamos de Servicio"

* **Nombre de Caso de Uso**  
Registrar Reclamo de Servicio

* **Descripcion Basica**  
Permite registrar un reclamo sobre un servicio ya ejecutado, validar si corresponde garantia y derivar el caso a recepcion y diagnostico. Segun el resultado tecnico, el flujo posterior puede continuar con presupuesto si requiere cobro u orden de trabajo si corresponde garantia sin cobro.

* **Actores relacionados**  
Asesor de servicio / Jefe de taller / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir un registro de servicio activo.  
Debe existir cliente y vehiculo relacionados al servicio ejecutado.

* **Flujo Basico**

El usuario ingresa al menu Reclamos de Servicio.  
El usuario busca un registro de servicio por numero de registro, cliente, vehiculo o chapa.

**Registrar reclamo**

* El sistema consulta `registro_servicio`, `clientes`, `vehiculos` y `modelo_auto` para buscar servicios ejecutados por numero, cliente, documento, telefono, placa o modelo.
* El usuario selecciona el servicio ejecutado.
* El sistema carga el servicio consultando `registro_servicio`, `clientes`, `vehiculos` y `modelo_auto`.
* El sistema carga el detalle reclamable consultando `registro_servicio_detalle` y `articulos`.
* El sistema consulta `reclamo_servicio_detalle` y `reclamo_servicio` para no permitir reclamar un detalle que ya tenga reclamo activo.
* El usuario carga descripcion, tipo de reclamo, origen, prioridad y si requiere garantia.
* El sistema valida que el registro pertenezca a la sucursal. Tabla consultada: `registro_servicio`.
* El sistema valida que no exista un reclamo activo del mismo tipo para el registro. Tabla consultada: `reclamo_servicio`.
* El sistema valida que los detalles correspondan al tipo de reclamo: servicio para reclamo de servicio, producto para reclamo de repuesto. Tablas consultadas: `registro_servicio_detalle`, `articulos`.
* El sistema registra el reclamo en `reclamo_servicio` con cliente, vehiculo, sucursal, usuario y estado activo.
* El sistema registra los items reclamados en `reclamo_servicio_detalle` cuando corresponde.
* El sistema actualiza `registro_servicio` para indicar que tiene reclamo activo.

**Derivar reclamo**

* Si el reclamo requiere revision fisica, el sistema permite generar una recepcion de servicio con origen reclamo.
* Para buscar reclamos desde recepcion, el sistema consulta `reclamo_servicio`, `clientes`, `vehiculos` y `modelo_auto`.
* Para seleccionar reclamo desde recepcion, el sistema consulta `reclamo_servicio`, `clientes`, `vehiculos`, `modelo_auto` y `marcas`.
* El diagnostico del reclamo indica si corresponde garantia, si el reclamo es valido y si requiere cobro.
* Si corresponde garantia sin cobro, el flujo puede continuar en Orden de Trabajo desde el diagnostico del reclamo.
* Si requiere cobro, el caso no habilita OT directa por garantia y debe tratarse como proceso comercial mediante presupuesto de servicio.

**Buscar reclamos**

* El usuario busca reclamos por numero, cliente, documento, telefono, placa, fecha o estado.
* El sistema consulta `reclamo_servicio` como tabla principal.
* El sistema une `clientes`, `vehiculos` y `modelo_auto` para mostrar reclamo, fecha, cliente, documento, telefono, placa, modelo, tipo, estado y registro de servicio origen.

**Cerrar o anular**

* El usuario puede cerrar el reclamo con observacion de cierre.
* El usuario puede anular reclamos que no tengan movimientos activos bloqueantes.
* Al presionar Anular, el sistema muestra un modal solicitando motivo de anulacion.
* El usuario ingresa el motivo y confirma.
* El sistema valida el permiso `servicio.reclamo.anular`.
* El sistema verifica que el reclamo exista, pertenezca a la sucursal y se encuentre activo. Tabla consultada: `reclamo_servicio`.
* El sistema verifica que no tenga recepcion generada. Tabla consultada: `recepcion_servicio`.
* El sistema actualiza estado, fecha de cierre y relaciones operativas. Tabla afectada: `reclamo_servicio`.
* Si ya no quedan reclamos activos para el registro, el sistema actualiza el registro origen. Tablas consultadas/afectadas: `reclamo_servicio`, `registro_servicio`.
* El sistema registra la anulacion en `anulacion_auditoria` con modulo `reclamo_servicio`, tabla afectada `reclamo_servicio`, estado anterior, estado nuevo, motivo, usuario, fecha y referencia.

* **Flujo Alternativo**  
El sistema no permite reclamos sin registro de servicio.  
El sistema no permite generar movimientos duplicados para el mismo reclamo activo.  
El sistema no permite cerrar sin observacion cuando el proceso lo requiere.  
El sistema no permite anular reclamos con movimientos derivados activos que impidan la reversa.
El sistema no permite anular sin motivo cuando la tabla de auditoria se encuentra disponible.

* **Post Condicion**  
El reclamo queda vinculado al servicio ejecutado.  
El sistema conserva cliente y vehiculo directos desde el registro de servicio para consulta rapida.  
El reclamo puede quedar pendiente, derivado, cerrado o anulado segun el proceso.

* **Tablas interactuadas**  
`reclamo_servicio`, `reclamo_servicio_detalle`, `registro_servicio`, `registro_servicio_detalle`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `usuarios`, `anulacion_auditoria`

---

## Criterio actual de independencia en servicios

Los movimientos de servicios fueron ajustados para evitar depender siempre de consultas lejanas entre recepcion, diagnostico, presupuesto, orden y registro. El criterio actual es:

* `recepcion_servicio` sigue siendo el ingreso fisico del vehiculo al taller.
* `diagnostico_servicio` depende de una recepcion porque documenta la revision tecnica de ese ingreso.
* `presupuesto_servicio` guarda `id_cliente` e `id_vehiculo` propios. Si nace desde diagnostico, copia esos datos desde la recepcion. Si nace como preliminar, los toma directamente del cliente y vehiculo seleccionados.
* `orden_trabajo` guarda `id_cliente` e `id_vehiculo` propios. Puede nacer desde presupuesto aprobado de origen diagnostico o desde reclamo en garantia; en ambos casos copia esos datos al documento operativo.
* `registro_servicio` guarda `id_cliente` e `id_vehiculo` propios. Al generarse desde una OT, copia esos datos desde la orden.
* `reclamo_servicio` guarda cliente y vehiculo para no depender solamente del registro original al consultar historiales o generar movimientos derivados.
* La garantia del servicio inicia en `registro_servicio.fecha_ejecucion` y vence por el primer limite alcanzado: tres meses calendario o 2.000 km desde el kilometraje de la recepcion que dio origen al servicio. Estos limites se calculan con los datos existentes; no se guardan campos adicionales de vencimiento o kilometraje limite en `registro_servicio`.
* Cuando se registra un reclamo, la interfaz valida inicialmente la garantia por fecha usando el registro reclamado. Si luego se genera recepcion por reclamo, el kilometraje cargado en esa recepcion permite volver a validar la garantia por kilometraje antes de habilitar la OT directa por garantia.
* En consultas, listados, impresiones e informes se deben usar primero los campos directos `id_cliente` e `id_vehiculo` de la tabla principal del movimiento. No se debe reconstruir cliente/vehiculo por joins hacia recepcion, diagnostico u otro documento anterior cuando la tabla actual ya los guarda.
* Los joins contra `recepcion_servicio` y `diagnostico_servicio` se mantienen solamente cuando el dato requerido pertenece a esos documentos: kilometraje, numero de recepcion, estado de recepcion, diagnostico tecnico, garantia, validez del reclamo o reapertura/cierre operativo.
* En reclamos por garantia, la recepcion sigue siendo parte del flujo obligatorio cuando se genera revision fisica y diagnostico. La OT por reclamo toma cliente/vehiculo del reclamo/recepcion segun corresponda, pero conserva esos datos en `orden_trabajo` una vez creada.
* En recepcion, diagnostico, presupuesto, orden de trabajo, registro y reclamos, la sucursal se maneja principalmente como `id_sucursal` tomado de la sesion para registrar, filtrar y validar pertenencia. La tabla `sucursales` solo debe listarse cuando el flujo consulta o expone datos propios de la sucursal, como ocurre en promociones, descuentos e informes.

Este enfoque conserva la trazabilidad completa, pero cada documento principal mantiene los datos minimos necesarios para mostrarse, imprimirse e informarse sin reconstruir toda la cadena.

---

## Tablas involucradas en interfaces de servicios

Las siguientes tablas son las que exponen datos visibles o seleccionables en las interfaces de servicios. No se listan tablas internas de permisos o configuracion salvo que alimenten datos operativos visibles.

| Interfaz / Proceso | Tablas principales | Datos visibles en interfaz |
| --- | --- | --- |
| Recepcion de servicio | `recepcion_servicio`, `recepcion_fotos`, `clientes`, `ciudades`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `reclamo_servicio`, `registro_servicio`, `anulacion_auditoria` | Cliente, vehiculo, chapa/modelo/marca, kilometraje, combustible, estado exterior, objetos, accesorios, fotos, prioridad, tipo de servicio, usuario, origen reclamo, garantia del reclamo, motivo de anulacion y estado |
| Alta rapida de cliente en recepcion | `clientes`, `ciudades` | Tipo/documento, nombre, apellido, telefono y datos minimos de contacto |
| Alta rapida de vehiculo en recepcion | `vehiculos`, `modelo_auto`, `marcas`, `clientes` | Cliente asociado, modelo, marca, chapa, color y datos esenciales del vehiculo |
| Diagnostico de servicio | `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `reclamo_servicio`, `reclamo_servicio_detalle`, `registro_servicio`, `registro_servicio_detalle`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `equipo_trabajo`, `usuarios`, `presupuesto_servicio`, `orden_trabajo`, `anulacion_auditoria` | Recepcion, cliente, vehiculo, equipo, observaciones, servicio/repuesto revisado, problema, gravedad, origen de repuesto, garantia de reclamo, bloqueos por presupuesto/OT, motivo de anulacion y estado |
| Presupuesto de servicio | `presupuesto_servicio`, `presupuesto_detalleservicio`, `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `stock`, `descuentos`, `descuento_cliente`, `promociones`, `promocion_producto`, `presupuesto_descuento`, `presupuesto_promocion`, `orden_trabajo`, `usuarios`, `anulacion_auditoria` | Origen, cliente y vehiculo tomados de `presupuesto_servicio`, marca/modelo, diagnostico/recepcion cuando correspondan, articulos/servicios, stock referencial, cantidades, precios, subtotales, descuentos, promociones, total final, vencimiento, bloqueo por OT, motivo de anulacion y estado |
| Descuentos y promociones de servicio | `descuentos`, `descuento_cliente`, `promociones`, `promocion_producto`, `presupuesto_descuento`, `presupuesto_promocion` | Beneficios aplicables, productos incluidos, montos aplicados y total descontado |
| Promociones de servicio | `promociones`, `promocion_producto`, `articulos`, `usuarios`, `sucursales` | Promocion, tipo, valor, vigencia, sucursal, articulos asociados, usuario y estado |
| Descuentos de servicio | `descuentos`, `descuento_cliente`, `clientes`, `usuarios`, `sucursales` | Descuento, tipo, valor, vigencia, sucursal, clientes asociados, usuario y estado |
| Orden de trabajo | `orden_trabajo`, `orden_trabajo_detalle`, `presupuesto_servicio`, `presupuesto_detalleservicio`, `presupuesto_promocion`, `promociones`, `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `reclamo_servicio`, `registro_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `stock`, `equipo_trabajo`, `equipo_empleado`, `empleados`, `usuarios`, `anulacion_auditoria` | Numero de OT, origen, presupuesto/reclamo, cliente y vehiculo tomados de `orden_trabajo`, marca/modelo, equipo, tecnico, diagnostico/recepcion solo para datos tecnicos o garantia, trabajos/repuestos autorizados, stock en reclamo, cantidades, observacion operativa, motivo de anulacion y estado |
| Registro de servicio | `registro_servicio`, `registro_servicio_detalle`, `orden_trabajo`, `orden_trabajo_detalle`, `presupuesto_servicio`, `diagnostico_servicio`, `recepcion_servicio`, `reclamo_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `stock`, `movimientostock`, `equipo_trabajo`, `empleados`, `usuarios`, `anulacion_auditoria` | OT, cliente y vehiculo tomados de `registro_servicio`, marca/modelo, fecha de ejecucion, garantia calculada por fecha/kilometraje cuando corresponde, trabajos/repuestos ejecutados, stock/movimientos generados o revertidos, observacion, tecnico/equipo, motivo de anulacion y estado |
| Salida de insumos | `salida_insumo`, `salida_insumo_detalle`, `articulos`, `stock`, `movimientostock`, `empleados`, `usuarios`, `sucursales`, `anulacion_auditoria` | Numero de salida, fecha, empleado responsable, usuario, observacion, insumos, cantidades, stock descontado o repuesto por anulacion, motivo de anulacion y estado |
| Reclamos de servicio | `reclamo_servicio`, `reclamo_servicio_detalle`, `registro_servicio`, `registro_servicio_detalle`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `usuarios`, `anulacion_auditoria` | Servicio reclamado, cliente y vehiculo tomados de `reclamo_servicio`, detalle ejecutado/reclamado, descripcion, tipo, prioridad, garantia solicitada, evaluacion por fecha/kilometraje, recepcion derivada, cierre, motivo de anulacion y estado |

---

## Tablas involucradas por movimiento de servicios

Esta lista resume las tablas que participan directamente en cada movimiento, incluyendo cabecera, detalle y tablas de apoyo que forman parte del flujo operativo.

| Movimiento | Tablas involucradas |
| --- | --- |
| Recepcion de servicio | `recepcion_servicio`, `recepcion_fotos`, `clientes`, `ciudades`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `reclamo_servicio`, `registro_servicio`, `anulacion_auditoria` |
| Diagnostico de servicio | `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `reclamo_servicio`, `reclamo_servicio_detalle`, `registro_servicio`, `registro_servicio_detalle`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `equipo_trabajo`, `usuarios`, `presupuesto_servicio`, `orden_trabajo`, `anulacion_auditoria` |
| Presupuesto de servicio | `presupuesto_servicio`, `presupuesto_detalleservicio`, `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `stock`, `descuentos`, `descuento_cliente`, `promociones`, `promocion_producto`, `presupuesto_descuento`, `presupuesto_promocion`, `orden_trabajo`, `usuarios`, `anulacion_auditoria` |
| Promocion de servicio | `promociones`, `promocion_producto`, `articulos`, `usuarios`, `sucursales` |
| Descuento de servicio | `descuentos`, `descuento_cliente`, `clientes`, `usuarios`, `sucursales` |
| Orden de trabajo | `orden_trabajo`, `orden_trabajo_detalle`, `presupuesto_servicio`, `presupuesto_detalleservicio`, `presupuesto_promocion`, `promociones`, `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `reclamo_servicio`, `registro_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `stock`, `equipo_trabajo`, `equipo_empleado`, `empleados`, `usuarios`, `anulacion_auditoria` |
| Registro de servicio | `registro_servicio`, `registro_servicio_detalle`, `orden_trabajo`, `orden_trabajo_detalle`, `presupuesto_servicio`, `diagnostico_servicio`, `recepcion_servicio`, `reclamo_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `stock`, `movimientostock`, `equipo_trabajo`, `empleados`, `usuarios`, `anulacion_auditoria` |
| Salida de insumos | `salida_insumo`, `salida_insumo_detalle`, `articulos`, `stock`, `movimientostock`, `empleados`, `usuarios`, `sucursales`, `anulacion_auditoria` |
| Reclamo de servicio | `reclamo_servicio`, `reclamo_servicio_detalle`, `registro_servicio`, `registro_servicio_detalle`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos`, `usuarios`, `anulacion_auditoria` |

---

## Tablas involucradas en informes

Los informes toman datos de tablas transaccionales y referenciales para exponer listados filtrables y exportables. En los informes de servicios, los movimientos principales deben priorizar los campos directos de cliente y vehiculo cuando existan, y recurrir a recepcion/diagnostico como apoyo de trazabilidad o compatibilidad con movimientos anteriores.

| Informe | Tablas principales | Datos expuestos |
| --- | --- | --- |
| Articulos | `articulos`, `categorias`, `marcas`, `unidad_medida`, `tipo_impuesto`, `articulo_proveedor`, `proveedores` | Codigo, descripcion, categoria, marca, unidad, impuesto, proveedor y estado |
| Stock | `stock`, `articulos`, `categorias`, `marcas`, `unidad_medida`, `sucursales`, `articulo_proveedor`, `proveedores` | Producto, sucursal, existencia, minimo, proveedor y clasificacion |
| Movimientos de stock | `movimientostock`, `articulos`, `sucursales`, `usuarios` | Fecha, tipo de movimiento, articulo, cantidad, sucursal, referencia y usuario |
| Proveedores | `proveedores` | RUC, razon social, telefono, correo, direccion y estado |
| Sucursales | `sucursales`, `empresa` | Sucursal, empresa, direccion, telefono y estado |
| Clientes | `clientes`, `ciudades` | Documento, nombre, apellido, telefono, ciudad, direccion y estado |
| Vehiculos | `vehiculos`, `clientes`, `modelo_auto`, `marcas` | Cliente, marca, modelo, chapa, color, anho, version, transmision, motor y tipo |
| Empleados | `empleados`, `cargos`, `sucursales` | Empleado, cargo, sucursal, documento, telefono y estado |
| Pedidos de compra | `pedido_cabecera`, `pedido_detalle`, `articulos`, `usuarios`, `sucursales` | Numero, fecha, usuario, sucursal, cantidad de items, cantidades y estado |
| Presupuestos de compra | `presupuesto_compra`, `presupuesto_detalle`, `proveedores`, `usuarios`, `sucursales` | Numero, proveedor, fecha, vencimiento, total, usuario y estado |
| Ordenes de compra | `orden_compra`, `orden_compra_detalle`, `proveedores`, `usuarios`, `sucursales` | Numero, proveedor, fecha, entrega, total, usuario, sucursal y estado |
| Compras | `compra_cabecera`, `compra_detalle`, `proveedores`, `usuarios`, `sucursales` | Factura, proveedor, fecha, condicion, total, usuario, sucursal y estado |
| Libro de compras | `libro_compra`, `sucursales` | Periodo, documento, timbrado, importes gravados/exentos, impuesto y total |
| Transferencias | `transferencia_stock`, `transferencia_stock_detalle`, `sucursales`, `nota_remision` | Origen, destino, fecha, estado, remision relacionada, items enviados y recibidos |
| Recepcion de servicio | `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `sucursales` | Fecha ingreso, cliente, vehiculo, chapa, tipo de servicio, prioridad, sucursal, usuario y estado |
| Presupuesto de servicio | `presupuesto_servicio`, `presupuesto_detalleservicio`, `diagnostico_servicio`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `sucursales` | Numero, origen, cliente/vehiculo desde `presupuesto_servicio`, diagnostico/recepcion cuando exista, subtotal, descuento, total final, fecha, vencimiento y estado |
| Orden de trabajo | `orden_trabajo`, `orden_trabajo_detalle`, `presupuesto_servicio`, `diagnostico_servicio`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `equipo_trabajo`, `usuarios`, `sucursales` | Numero de OT, cliente/vehiculo desde `orden_trabajo`, equipo, diagnostico/recepcion cuando exista, cantidad de items, fecha inicio, fecha fin y estado |
| Registro de servicio | `registro_servicio`, `registro_servicio_detalle`, `orden_trabajo`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `equipo_trabajo`, `empleados`, `usuarios`, `sucursales` | Servicio ejecutado, OT, cliente/vehiculo desde `registro_servicio`, tecnico/equipo, fecha ejecucion, items ejecutados, total referencial y estado |

**Observacion tecnica**  
La especificacion funcional refleja dos flujos activos de OT: generacion normal desde presupuesto aprobado de origen diagnostico y generacion por reclamo en garantia sin cobro. El presupuesto preliminar no genera OT hasta vincularse a un diagnostico formal y aprobarse.
