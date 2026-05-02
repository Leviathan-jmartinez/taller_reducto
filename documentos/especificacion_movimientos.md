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
