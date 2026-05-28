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
* El sistema valida que el articulo exista y este activo.
* El sistema valida que la cantidad sea mayor a 0.
* El sistema valida que el articulo no se encuentre duplicado en la lista.
* El sistema agrega el articulo a la lista temporal del pedido.

**Guardar**

* El usuario presiona Guardar.
* El sistema valida que existan articulos cargados.
* El sistema registra la cabecera en `pedido_cabecera` con estado pendiente.
* El sistema registra los articulos en `pedido_detalle`.
* El sistema emite mensaje de confirmacion de registro.

**Anular**

* El usuario accede al Listado de Pedidos.
* El sistema muestra los pedidos de la sucursal del usuario.
* El usuario selecciona un pedido y presiona Anular.
* El sistema verifica que el pedido exista.
* El sistema verifica que pertenezca a la sucursal del usuario.
* El sistema verifica que el pedido no se encuentre procesado.
* El sistema valida el permiso `compra.pedido.anular`.
* El sistema actualiza el pedido a estado anulado.
* El sistema registra usuario y fecha de actualizacion.
* El sistema muestra mensaje de confirmacion.

* **Flujo Alternativo**  
El sistema no permite articulos duplicados.  
El sistema no permite cantidades invalidas.  
El sistema no permite guardar sin articulos.  
El sistema no permite anular pedidos procesados.  
El sistema no permite anular sin permiso.

* **Post Condicion**  
El pedido queda registrado en cabecera y detalle.  
El pedido puede quedar pendiente, procesado o anulado.  
El sistema emite mensajes de confirmacion o advertencia segun corresponda.

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
* El sistema guarda los datos del proveedor en sesion.

**Cargar desde pedido**

* El usuario busca un pedido por numero.
* El sistema consulta `pedido_cabecera` y `pedido_detalle` con estado pendiente.
* El usuario selecciona el pedido.
* El sistema carga los articulos solicitados.

**Agregar articulo**

* El usuario busca articulos por codigo o descripcion.
* El sistema consulta `articulos` activos.
* El usuario ingresa cantidad y precio.
* El sistema valida cantidad, precio y duplicidad.
* El sistema calcula subtotal.
* El sistema recalcula total de unidades y total general en pantalla.
* Si el usuario modifica precios en la grilla, el sistema actualiza los datos temporales mediante Ajax.
* El sistema actualiza la grilla temporal.

**Guardar**

* El usuario presiona Guardar.
* El sistema valida proveedor y articulos cargados.
* El sistema registra la cabecera en `presupuesto_compra`.
* El sistema registra el detalle en `presupuesto_detalle`.
* Si proviene de un pedido, el sistema actualiza el pedido a procesado.
* El sistema emite mensaje de presupuesto registrado.

**Anular**

* El usuario accede al Listado de Presupuestos.
* El sistema muestra presupuestos de la sucursal.
* El usuario selecciona un presupuesto y presiona Anular.
* El sistema verifica existencia, sucursal y estado.
* El sistema no permite anular presupuestos procesados.
* El sistema valida el permiso `compra.presupuesto.anular`.
* El sistema actualiza el estado a anulado y registra usuario/fecha.

* **Flujo Alternativo**  
El sistema no permite guardar sin proveedor.  
El sistema no permite guardar sin articulos.  
El sistema no permite articulos duplicados.  
El sistema no permite cantidades o precios invalidos.  
El sistema no permite anular presupuestos procesados.

* **Post Condicion**  
El presupuesto queda registrado con proveedor, articulos y total.  
Si fue generado desde pedido, el pedido queda procesado.  
El sistema emite confirmacion de registro o anulacion.

---

## Movimiento "Gestion de Ordenes de Compra"

* **Nombre de Caso de Uso**  
Registrar Orden de Compra

* **Descripcion Basica**  
Permite generar una orden de compra para un proveedor, con fecha de entrega y detalle de articulos. Puede generarse desde un presupuesto aprobado o de forma manual.

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
* El sistema consulta `presupuesto_compra` y muestra presupuestos activos.
* El usuario selecciona un presupuesto.
* El sistema carga proveedor, articulos, cantidades, precios y fecha de vencimiento.

**Agregar datos**

* El usuario selecciona proveedor cuando la orden es manual.
* El usuario busca articulos por codigo o descripcion.
* El sistema consulta articulos activos y precios relacionados al proveedor cuando existan.
* El usuario ingresa cantidad y precio.
* El sistema valida cantidad, precio y duplicidad.
* El sistema calcula subtotales.
* El sistema permite filtrar productos cargados en pantalla.
* El usuario confirma la generacion de la orden cuando corresponde.

**Guardar**

* El usuario presiona Guardar.
* El sistema registra cabecera en `orden_compra`.
* El sistema registra detalle en `orden_compra_detalle`.
* El sistema registra o actualiza la relacion articulo-proveedor.
* Si proviene de presupuesto, el sistema actualiza el presupuesto a estado OC generada/procesado.
* El sistema emite mensaje de confirmacion.

**Anular**

* El usuario accede al Listado de Ordenes de Compra.
* El sistema muestra las ordenes de la sucursal.
* El usuario selecciona una orden y presiona Anular.
* El sistema verifica existencia y sucursal.
* El sistema verifica que la orden no este procesada.
* El sistema valida el permiso `compra.oc.anular`.
* El sistema actualiza el estado a anulado y registra usuario/fecha.

* **Flujo Alternativo**  
El sistema no permite guardar sin proveedor o sin articulos.  
El sistema no permite cantidades o precios invalidos.  
El sistema no permite articulos duplicados.  
El sistema no permite anular ordenes procesadas.  
El sistema no permite anular sin permiso.

* **Post Condicion**  
La orden de compra queda registrada en cabecera y detalle.  
El presupuesto relacionado puede quedar procesado.  
El sistema emite confirmacion de registro o anulacion.

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
El sistema consulta `compra_cabecera`, `proveedores` y `usuarios`, filtrando compras activas de la sucursal.  
El usuario selecciona la factura.  
El sistema carga los datos de la compra y su detalle.  
El usuario completa datos de remision: numero, transportista, documento, telefono, empresa transportista, RUC, vehiculo, chapa, fechas y motivo.  
El usuario presiona Guardar.  
El sistema registra la cabecera en `nota_remision`.  
El sistema registra el detalle en `nota_remision_detalle`.  
El sistema emite mensaje de confirmacion.

**Anular**

* El usuario accede al listado de remisiones.
* El sistema muestra remisiones activas/procesadas de la sucursal.
* El usuario selecciona una remision y presiona Anular.
* El sistema valida el permiso `compra.remision.anular`.
* El sistema actualiza la remision a estado anulado.
* El sistema registra usuario y fecha de anulacion.

* **Flujo Alternativo**  
El sistema no permite buscar sin ingresar numero de factura.  
El sistema no permite cargar facturas inexistentes o de otra sucursal.  
El sistema no permite anular sin permiso.

* **Post Condicion**  
La nota de remision queda registrada con cabecera y detalle.  
La remision puede quedar activa, procesada o anulada.

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
El sistema carga los datos del proveedor y el detalle de la factura.  
El usuario selecciona tipo de nota: credito o debito.  
El usuario ingresa numero de documento, timbrado, fecha y detalle.  
El sistema recalcula el total de cada item cuando el usuario modifica cantidad o precio.  
El sistema actualiza el detalle temporal de la nota mediante Ajax.  
El sistema valida que el tipo sea credito o debito.  
El sistema valida el movimiento de stock permitido.  
Para nota de debito, el sistema no permite movimiento de devolucion.  
Para nota de credito con devolucion, el sistema valida stock disponible por articulo.  
El sistema registra cabecera en `nota_compra`.  
El sistema registra detalle en `nota_compra_detalle`.  
El sistema impacta en `cuentas_a_pagar`.  
El sistema registra el comprobante en `libro_compra` como NC o ND.  
Si corresponde devolucion, el sistema descuenta stock y registra movimiento en `movimientostock`.  
El sistema confirma la transaccion y emite mensaje.

**Anular**

* El usuario accede al listado de notas.
* El sistema muestra notas registradas de la sucursal.
* El usuario selecciona una nota y presiona Anular.
* El sistema valida el permiso `compra.nota.anular`.
* El sistema verifica que la nota no este anulada.
* El sistema anula la cabecera de nota.
* El sistema genera el impacto inverso en cuentas a pagar.
* Si la nota fue de credito con devolucion, el sistema repone stock y registra movimiento de anulacion.
* El sistema anula el registro correspondiente en `libro_compra`.
* El sistema emite mensaje de confirmacion.

* **Flujo Alternativo**  
El sistema no permite tipos de nota invalidos.  
El sistema no permite movimiento de stock invalido.  
El sistema no permite devolucion sin stock suficiente.  
El sistema no permite anular notas ya anuladas.  
El sistema revierte la transaccion si ocurre un error.

* **Post Condicion**  
La nota queda registrada y asociada a la factura de compra.  
Las cuentas a pagar y libro de compras quedan actualizados.  
El stock se ajusta cuando la nota de credito implica devolucion.

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
El sistema consulta sucursales activas excluyendo la sucursal origen.  
El usuario busca productos.  
El sistema consulta `stock` y `articulos`, mostrando solo productos con stock disponible.  
El usuario ingresa cantidades a transferir.  
El sistema valida en pantalla cantidad invalida, stock superado y producto duplicado.
El usuario confirma el guardado de la transferencia y la emision de la remision.
El sistema valida stock suficiente en origen.  
El sistema registra cabecera en `transferencia_stock` con estado `en_transito`.  
El sistema registra detalle en `transferencia_stock_detalle`.  
El sistema descuenta stock de la sucursal origen.  
El sistema registra movimiento de salida en `movimientostock`.  
El sistema genera una `nota_remision` y su detalle para el traslado.  
El sistema actualiza la numeracion documental de la sucursal.  
El sistema emite mensaje de transferencia creada.

**Recepcionar transferencia**

* El usuario de la sucursal destino accede a transferencias por recibir.
* El sistema muestra transferencias en estado `en_transito` destinadas a su sucursal.
* El usuario carga cantidades recibidas.
* El sistema calcula en pantalla la diferencia entre cantidad enviada y cantidad recibida.
* Si existen diferencias, el sistema muestra un resumen antes de confirmar la recepcion.
* El sistema valida que la transferencia exista y este en transito.
* El sistema registra las cantidades recibidas en el detalle.
* El sistema suma stock en la sucursal destino.
* El sistema registra movimiento de entrada en `movimientostock`.
* Si se recibio todo, el sistema actualiza estado a `recibido`.
* Si existen faltantes, el sistema actualiza estado a `recibido_parcial` y genera una transferencia pendiente por el faltante.
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
* El sistema valida el permiso `inventario.anular`.
* Si el ajuste esta pendiente o modificado, el sistema solo actualiza estado a anulado.
* Si el ajuste ya fue aplicado, el sistema revierte las diferencias en stock.
* El sistema registra movimientos de anulacion de ajuste.
* El sistema actualiza el ajuste a estado anulado.
* El sistema emite mensaje de confirmacion.

* **Flujo Alternativo**  
El sistema no permite generar inventario sin tipo definido.  
El sistema no permite aplicar ajustes sin articulos.  
El sistema no permite aplicar ajustes sin diferencias reales.  
El sistema no permite aplicar ajustes que no pertenezcan a la sucursal.  
El sistema no permite aplicar ajustes en estado invalido.  
El sistema revierte el stock si se anula un ajuste ya aplicado.

* **Post Condicion**  
El inventario queda registrado en cabecera y detalle.  
El stock se actualiza al aplicar diferencias.  
Los movimientos de ajuste y anulacion quedan registrados.

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
* El sistema consulta `clientes` y muestra los datos esenciales para carga rapida.
* Si el cliente no existe, el usuario puede registrarlo desde la recepcion con datos minimos: tipo de documento, documento, nombre, apellido y telefono.
* El sistema guarda el cliente y lo deja seleccionado en la recepcion.

**Seleccionar o cargar vehiculo**

* El usuario busca los vehiculos asociados al cliente.
* El sistema consulta `vehiculos`, `modelo_auto` y `marcas`.
* Si el vehiculo no existe, el usuario puede registrarlo desde la recepcion con datos esenciales para carga rapida.
* El sistema vincula el vehiculo al cliente seleccionado.

**Registrar datos de ingreso**

* El usuario completa kilometraje, nivel de combustible, estado exterior, objetos dentro del vehiculo, tipo de servicio, area del problema, prioridad, accesorios y observacion.
* El sistema registra la cabecera en `recepcion_servicio` con `id_cliente`, `id_vehiculo`, usuario, sucursal, fecha de ingreso y estado activo.
* Si la recepcion viene desde un reclamo, el sistema marca el origen como reclamo y guarda el identificador del reclamo.
* El sistema emite mensaje de confirmacion.

**Anular**

* El usuario accede al listado de recepciones.
* El sistema muestra recepciones de la sucursal del usuario.
* El usuario selecciona una recepcion y presiona Anular.
* El sistema verifica existencia, sucursal y estado.
* El sistema no permite anular si ya existe un diagnostico activo relacionado.
* El sistema actualiza la recepcion a estado anulado.

* **Flujo Alternativo**  
El sistema no permite guardar sin cliente.  
El sistema no permite guardar sin vehiculo.  
El sistema no permite vehiculos sin cliente asociado.  
El sistema no permite anular recepciones ya procesadas.  
El sistema revierte la transaccion si falla el alta rapida de cliente o vehiculo.

* **Post Condicion**  
La recepcion queda registrada y disponible para diagnostico.  
El cliente y vehiculo quedan identificados directamente en la recepcion.  
La informacion inicial de kilometraje, combustible y estado del vehiculo queda como dato propio del ingreso al taller, no como dato maestro del vehiculo.

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
* El usuario selecciona la recepcion.
* El sistema carga datos de cliente, vehiculo, recepcion y observacion del ingreso.

**Registrar diagnostico**

* El usuario selecciona el equipo de trabajo.
* El usuario carga observaciones generales y detalles tecnicos.
* En cada detalle se indica sistema revisado, problema, gravedad, solucion propuesta, si requiere repuesto y si requiere mano de obra.
* La fecha y el estado del diagnostico son controlados por el sistema; no son campos seleccionables por el usuario.
* El sistema registra la cabecera en `diagnostico_servicio` con fecha actual, estado activo, usuario, recepcion, equipo y sucursal.
* El sistema registra los detalles en `diagnostico_detalle`.
* El sistema actualiza la recepcion para indicar que ya fue diagnosticada.

**Anular**

* El usuario accede al listado de diagnosticos.
* El sistema muestra diagnosticos de la sucursal.
* El usuario selecciona un diagnostico y presiona Anular.
* El sistema verifica que no exista un presupuesto activo relacionado.
* Si el diagnostico pertenece a un reclamo, el sistema valida tambien que no exista una orden de trabajo activa por reclamo.
* El sistema actualiza el diagnostico a estado anulado y libera la recepcion cuando corresponde.

* **Flujo Alternativo**  
El sistema no permite diagnosticar recepciones anuladas.  
El sistema no permite guardar sin equipo de trabajo.  
El sistema no permite guardar sin al menos un detalle tecnico valido.  
El sistema no permite anular diagnosticos que ya tengan presupuesto u orden de trabajo activa.

* **Post Condicion**  
El diagnostico queda registrado con cabecera y detalle tecnico.  
La recepcion queda vinculada al diagnostico.  
El diagnostico queda disponible para generar presupuesto de servicio.

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
* El sistema consulta `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `clientes` y `vehiculos`.
* El usuario selecciona el diagnostico.
* El sistema copia al presupuesto el cliente y vehiculo de la recepcion.
* El sistema muestra los detalles tecnicos como referencia para cargar trabajos y repuestos.

**Presupuesto preliminar**

* El usuario selecciona cliente y vehiculo sin requerir diagnostico previo.
* El sistema marca el presupuesto con origen `PRELIMINAR`.
* El presupuesto preliminar permite entregar una estimacion inicial al cliente.
* Para generar una orden de trabajo normal, el sistema exige convertir o relacionar el presupuesto a un diagnostico formal y aprobarlo.

**Agregar trabajos y repuestos**

* El usuario busca articulos o servicios por codigo o descripcion.
* El sistema consulta `articulos`, precios, stock referencial y datos comerciales disponibles.
* El usuario ingresa cantidad y precio unitario.
* El sistema valida duplicidad, cantidad y precio.
* El sistema calcula subtotal, descuentos, promociones y total final en pantalla.
* Si existen descuentos, promociones o reglas comerciales aplicables, el sistema registra la relacion en las tablas correspondientes.
* El sistema registra la cabecera en `presupuesto_servicio` con `id_cliente`, `id_vehiculo`, origen, sucursal, usuario, fecha, vencimiento, subtotal, descuento y total final.
* El sistema registra el detalle en `presupuesto_detalleservicio`.

**Aprobar, anular y generar OT**

* El usuario puede aprobar el presupuesto cuando el cliente acepta la propuesta.
* El sistema actualiza el estado del presupuesto aprobado.
* El usuario puede generar una orden de trabajo desde un presupuesto aprobado.
* El sistema valida que no exista una orden activa duplicada para el presupuesto.
* El sistema copia cliente, vehiculo y detalle del presupuesto a la orden de trabajo.
* Al generar la orden, el presupuesto queda procesado.
* El usuario puede anular presupuestos que no esten procesados por una orden activa.

* **Flujo Alternativo**  
El sistema no permite guardar sin cliente ni vehiculo.  
El sistema no permite guardar sin detalle.  
El sistema no permite generar OT desde un presupuesto preliminar sin diagnostico formal.  
El sistema no permite generar dos ordenes activas para el mismo presupuesto.  
El sistema no permite anular presupuestos ya procesados.

* **Post Condicion**  
El presupuesto queda registrado con identidad propia de cliente y vehiculo.  
El detalle economico queda disponible para PDF, aprobacion y generacion de orden de trabajo.  
El presupuesto puede quedar preliminar, activo, aprobado, procesado o anulado segun el flujo.

---

## Movimiento "Gestion de Orden de Trabajo"

* **Nombre de Caso de Uso**  
Generar y Gestionar Orden de Trabajo

* **Descripcion Basica**  
Permite convertir un presupuesto aprobado o un reclamo valido en una orden operativa para el taller. En el flujo normal, la OT no se genera directamente desde el diagnostico; se genera desde un `presupuesto_servicio` aprobado, y ese presupuesto debe estar asociado a un diagnostico formal. La orden de trabajo debe mostrar lo necesario para ejecutar el servicio: cliente, vehiculo, origen, equipo, tecnico responsable, trabajos autorizados y repuestos/productos a utilizar. No debe funcionar como una copia visual del presupuesto ni centrar la vista en totales comerciales.

* **Actores relacionados**  
Jefe de taller / Tecnico / Asesor de servicio / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir una sucursal asociada al usuario.  
Para origen normal, debe existir un presupuesto aprobado, de origen diagnostico y apto para generar OT.  
Para origen reclamo, debe existir un reclamo activo y validado.  
Deben existir equipo de trabajo y, opcionalmente, tecnico responsable.

* **Flujo Basico**

El usuario ingresa al menu Nueva Orden de Trabajo.  
El sistema valida sesion y sucursal.

**Generar desde presupuesto**

* El usuario busca un presupuesto aprobado.
* El sistema consulta `presupuesto_servicio`, `presupuesto_detalleservicio`, `clientes`, `vehiculos`, `articulos`, `diagnostico_servicio` y `recepcion_servicio` cuando corresponde.
* El usuario selecciona el presupuesto.
* El sistema carga datos de cliente, vehiculo y origen.
* El sistema muestra trabajos y repuestos autorizados, sin usar la vista como resumen comercial del presupuesto.
* El usuario selecciona equipo de trabajo, tecnico responsable y observaciones operativas.
* El sistema registra la cabecera en `orden_trabajo` con `id_cliente`, `id_vehiculo`, presupuesto, usuario, sucursal, equipo, tecnico y estado activo.
* El sistema copia los articulos del presupuesto en `orden_trabajo_detalle`.
* El sistema actualiza el presupuesto como procesado.

**Generar desde reclamo**

* El usuario selecciona un reclamo activo que requiere atencion del taller.
* El sistema consulta `reclamo_servicio`, `registro_servicio`, `clientes`, `vehiculos`, recepcion y diagnostico asociado cuando exista.
* El sistema registra la orden con origen `RECLAMO`, cliente y vehiculo directos.
* Si el reclamo no requiere cobro por garantia, la orden puede generarse sin presupuesto comercial.
* El usuario puede completar o ajustar trabajos y repuestos autorizados para la ejecucion.

**Asignar y cerrar**

* El usuario puede asignar o modificar equipo de trabajo y tecnico responsable.
* El sistema permite consultar el detalle operativo de la orden.
* Al finalizar la ejecucion, la orden queda disponible para registro de servicio.
* El sistema no permite anular una OT que ya tenga registro de servicio activo.

* **Flujo Alternativo**  
El sistema no permite generar OT sin cliente ni vehiculo.  
El sistema no permite generar OT normal desde presupuesto no aprobado.  
El sistema no permite generar OT directa desde diagnostico sin presupuesto aprobado.  
El sistema no permite generar OT normal desde presupuesto preliminar sin diagnostico.  
El sistema no permite duplicar una OT activa para el mismo presupuesto o reclamo.  
El sistema no permite anular OT con registro de servicio activo.

* **Post Condicion**  
La orden queda registrada con cliente, vehiculo y origen propios.  
Los trabajos y repuestos quedan copiados o cargados en el detalle operativo.  
La orden queda disponible para asignacion, seguimiento y registro de servicio.

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
* El sistema consulta `orden_trabajo`, `orden_trabajo_detalle`, `clientes`, `vehiculos`, `articulos`, equipo y tecnico.
* El usuario selecciona la orden.
* El sistema carga el detalle operativo autorizado.

**Registrar ejecucion**

* El usuario indica fecha de ejecucion y observaciones finales.
* El sistema copia `id_cliente` e `id_vehiculo` desde la orden de trabajo.
* El sistema registra la cabecera en `registro_servicio`.
* El sistema registra el detalle final en `registro_servicio_detalle`, separando el origen cuando corresponde.
* El sistema actualiza la orden de trabajo como finalizada.
* Si la orden proviene de reclamo, el sistema actualiza tambien el estado del reclamo y de los movimientos relacionados cuando corresponda.

**Anular**

* El usuario accede al listado de registros de servicio.
* El usuario selecciona un registro y presiona Anular.
* El sistema verifica existencia, sucursal y estado.
* El sistema actualiza el registro a anulado.
* El sistema reabre o ajusta la orden de trabajo relacionada cuando corresponda.

* **Flujo Alternativo**  
El sistema no permite registrar sin orden de trabajo.  
El sistema no permite duplicar registros activos para la misma OT.  
El sistema no permite guardar sin fecha de ejecucion.  
El sistema no permite anular registros inexistentes o ya anulados.

* **Post Condicion**  
El servicio queda registrado como historial ejecutado del cliente y vehiculo.  
La orden queda finalizada o actualizada segun el flujo.  
El registro queda disponible para reclamos, garantias e informes.

---

## Movimiento "Gestion de Reclamos de Servicio"

* **Nombre de Caso de Uso**  
Registrar Reclamo de Servicio

* **Descripcion Basica**  
Permite registrar un reclamo sobre un servicio ya ejecutado, validar si corresponde garantia y derivar el caso a recepcion, diagnostico, presupuesto u orden de trabajo segun corresponda. El reclamo tambien guarda cliente y vehiculo para facilitar busqueda, historial e informes.

* **Actores relacionados**  
Asesor de servicio / Jefe de taller / Usuario autorizado

* **Pre Condicion**  
El usuario debe estar autenticado.  
Debe existir un registro de servicio activo.  
Debe existir cliente y vehiculo relacionados al servicio ejecutado.

* **Flujo Basico**

El usuario ingresa al menu Reclamos de Servicio.  
El usuario busca un registro de servicio por cliente, vehiculo, chapa u orden.

**Registrar reclamo**

* El sistema consulta `registro_servicio`, `orden_trabajo`, `clientes`, `vehiculos` y detalle del servicio.
* El usuario selecciona el servicio ejecutado.
* El usuario carga descripcion, tipo de reclamo, origen, prioridad y si requiere garantia.
* El sistema registra el reclamo en `reclamo_servicio` con cliente, vehiculo, sucursal, usuario y estado activo.

**Derivar reclamo**

* Si el reclamo requiere revision fisica, el sistema permite generar una recepcion de servicio con origen reclamo.
* El diagnostico del reclamo indica si corresponde garantia, si el reclamo es valido y si requiere cobro.
* Si corresponde garantia sin cobro, el sistema puede generar una orden de trabajo directa desde el reclamo.
* Si requiere cobro, el flujo debe pasar por presupuesto de servicio antes de generar una orden normal.

**Cerrar o anular**

* El usuario puede cerrar el reclamo con observacion de cierre.
* El usuario puede anular reclamos que no tengan movimientos activos bloqueantes.
* El sistema actualiza estado, fecha de cierre y relaciones operativas.

* **Flujo Alternativo**  
El sistema no permite reclamos sin registro de servicio.  
El sistema no permite generar movimientos duplicados para el mismo reclamo activo.  
El sistema no permite cerrar sin observacion cuando el proceso lo requiere.  
El sistema no permite anular reclamos con orden o registro activo que impida la reversa.

* **Post Condicion**  
El reclamo queda vinculado al servicio ejecutado.  
El sistema conserva cliente y vehiculo directos para consulta rapida.  
El reclamo puede quedar pendiente, derivado, cerrado o anulado segun el proceso.

---

## Criterio actual de independencia en servicios

Los movimientos de servicios fueron ajustados para evitar depender siempre de consultas lejanas entre recepcion, diagnostico, presupuesto, orden y registro. El criterio actual es:

* `recepcion_servicio` sigue siendo el ingreso fisico del vehiculo al taller.
* `diagnostico_servicio` depende de una recepcion porque documenta la revision tecnica de ese ingreso.
* `presupuesto_servicio` guarda `id_cliente` e `id_vehiculo` propios. Si nace desde diagnostico, copia esos datos desde la recepcion. Si nace como preliminar, los toma directamente del cliente y vehiculo seleccionados.
* `orden_trabajo` guarda `id_cliente` e `id_vehiculo` propios. Si nace desde presupuesto, copia esos datos desde el presupuesto. Si nace desde reclamo, los toma del reclamo o del movimiento relacionado.
* `registro_servicio` guarda `id_cliente` e `id_vehiculo` propios. Al generarse desde una OT, copia esos datos desde la orden.
* `reclamo_servicio` guarda cliente y vehiculo para no depender solamente del registro original al consultar historiales o generar movimientos derivados.

Este enfoque conserva la trazabilidad completa, pero cada documento principal mantiene los datos minimos necesarios para mostrarse, imprimirse e informarse sin reconstruir toda la cadena.

---

## Tablas involucradas en interfaces de servicios

Las siguientes tablas son las que exponen datos visibles o seleccionables en las interfaces de servicios. No se listan tablas internas de permisos o configuracion salvo que alimenten datos operativos visibles.

| Interfaz / Proceso | Tablas principales | Datos visibles en interfaz |
| --- | --- | --- |
| Recepcion de servicio | `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `sucursales` | Cliente, vehiculo, chapa/modelo/marca, kilometraje, combustible, estado exterior, objetos, accesorios, prioridad, tipo de servicio, sucursal, usuario y estado |
| Alta rapida de cliente en recepcion | `clientes`, `ciudades` | Tipo/documento, nombre, apellido, telefono y datos minimos de contacto |
| Alta rapida de vehiculo en recepcion | `vehiculos`, `modelo_auto`, `marcas`, `clientes` | Cliente asociado, modelo, marca, chapa, color y datos esenciales del vehiculo |
| Diagnostico de servicio | `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `equipo_trabajo`, `usuarios` | Recepcion, cliente, vehiculo, equipo, observaciones, sistema revisado, problema, gravedad, solucion propuesta, requiere repuesto y requiere mano de obra |
| Presupuesto de servicio | `presupuesto_servicio`, `presupuesto_detalleservicio`, `diagnostico_servicio`, `diagnostico_detalle`, `clientes`, `vehiculos`, `articulos`, `sucursales`, `usuarios` | Origen, cliente, vehiculo, diagnostico, articulos/servicios, cantidades, precios, subtotales, descuentos, promociones, total final, vencimiento y estado |
| Descuentos y promociones de servicio | `descuentos`, `descuento_cliente`, `promociones`, `promocion_producto`, `presupuesto_descuento`, `presupuesto_promocion`, `regla_comercial`, `regla_comercial_condicion`, `regla_comercial_descuento` | Beneficios aplicables, reglas, condiciones, productos incluidos, montos aplicados y total descontado |
| Orden de trabajo | `orden_trabajo`, `orden_trabajo_detalle`, `presupuesto_servicio`, `presupuesto_detalleservicio`, `reclamo_servicio`, `clientes`, `vehiculos`, `articulos`, `equipo_trabajo`, `equipo_empleado`, `empleados`, `usuarios`, `sucursales` | Numero de OT, origen, cliente, vehiculo, equipo, tecnico, trabajos/repuestos autorizados, cantidades, observacion operativa y estado |
| Registro de servicio | `registro_servicio`, `registro_servicio_detalle`, `orden_trabajo`, `orden_trabajo_detalle`, `clientes`, `vehiculos`, `articulos`, `equipo_trabajo`, `empleados`, `usuarios`, `sucursales` | OT, cliente, vehiculo, fecha de ejecucion, trabajos/repuestos ejecutados, observacion, tecnico/equipo y estado |
| Reclamos de servicio | `reclamo_servicio`, `registro_servicio`, `registro_servicio_detalle`, `orden_trabajo`, `recepcion_servicio`, `diagnostico_servicio`, `clientes`, `vehiculos`, `usuarios`, `sucursales` | Servicio reclamado, cliente, vehiculo, descripcion, tipo, prioridad, garantia, recepcion/diagnostico/OT asociada, cierre y estado |

---

## Tablas involucradas por movimiento de servicios

Esta lista resume las tablas que participan directamente en cada movimiento, incluyendo cabecera, detalle y tablas de apoyo que forman parte del flujo operativo.

| Movimiento | Tablas involucradas |
| --- | --- |
| Recepcion de servicio | `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `sucursales` |
| Diagnostico de servicio | `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `equipo_trabajo`, `usuarios`, `sucursales` |
| Presupuesto de servicio | `presupuesto_servicio`, `presupuesto_detalleservicio`, `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `clientes`, `vehiculos`, `articulos`, `descuentos`, `descuento_cliente`, `promociones`, `promocion_producto`, `presupuesto_descuento`, `presupuesto_promocion`, `regla_comercial`, `regla_comercial_condicion`, `regla_comercial_descuento`, `usuarios`, `sucursales` |
| Orden de trabajo normal | `orden_trabajo`, `orden_trabajo_detalle`, `presupuesto_servicio`, `presupuesto_detalleservicio`, `diagnostico_servicio`, `recepcion_servicio`, `clientes`, `vehiculos`, `articulos`, `equipo_trabajo`, `equipo_empleado`, `empleados`, `usuarios`, `sucursales` |
| Orden de trabajo por reclamo | `orden_trabajo`, `orden_trabajo_detalle`, `reclamo_servicio`, `registro_servicio`, `recepcion_servicio`, `diagnostico_servicio`, `clientes`, `vehiculos`, `articulos`, `usuarios`, `sucursales` |
| Registro de servicio | `registro_servicio`, `registro_servicio_detalle`, `orden_trabajo`, `orden_trabajo_detalle`, `clientes`, `vehiculos`, `articulos`, `equipo_trabajo`, `empleados`, `usuarios`, `sucursales` |
| Reclamo de servicio | `reclamo_servicio`, `registro_servicio`, `registro_servicio_detalle`, `orden_trabajo`, `recepcion_servicio`, `diagnostico_servicio`, `clientes`, `vehiculos`, `usuarios`, `sucursales` |

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
| Presupuesto de servicio | `presupuesto_servicio`, `presupuesto_detalleservicio`, `diagnostico_servicio`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `usuarios`, `sucursales` | Numero, origen, cliente, vehiculo, diagnostico/recepcion cuando exista, subtotal, descuento, total final, fecha, vencimiento y estado |
| Orden de trabajo | `orden_trabajo`, `orden_trabajo_detalle`, `presupuesto_servicio`, `diagnostico_servicio`, `recepcion_servicio`, `reclamo_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `equipo_trabajo`, `usuarios`, `sucursales` | Numero de OT, origen, cliente, vehiculo, equipo, tecnico, trabajos/repuestos, fecha inicio, fecha fin y estado |
| Registro de servicio | `registro_servicio`, `registro_servicio_detalle`, `orden_trabajo`, `presupuesto_servicio`, `diagnostico_servicio`, `recepcion_servicio`, `reclamo_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `equipo_trabajo`, `empleados`, `usuarios`, `sucursales` | Servicio ejecutado, OT, cliente, vehiculo, tecnico/equipo, fecha ejecucion, items ejecutados, total referencial y estado |

---
