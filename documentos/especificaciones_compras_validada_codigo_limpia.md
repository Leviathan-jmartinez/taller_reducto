# Especificaciones de caso de uso: Movimientos Gestión de Compras

Nombre de Caso de Uso
Registrar Pedido

Descripción Básica
Este caso permite registrar, consultar, imprimir y anular pedidos de compra generados por necesidad de reposición de artículos. El pedido se carga con artículos activos, muestra el stock actual de la sucursal y deja el registro disponible para generar presupuestos de compra.

Actores relacionados
Personal de Compras

Pre Condición
El usuario debe estar autenticado en el sistema.
El usuario debe tener permisos para operar pedidos de compra.
Debe existir una sucursal asociada al usuario.
Deben existir artículos activos para cargar al pedido.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema mediante logueo.
El usuario ingresa al módulo Pedido de Compra.
El sistema muestra las opciones Nuevo Pedido y Buscar Pedidos.

Nuevo
El usuario presiona Agregar artículo.
El sistema muestra el buscador de artículos.
El usuario ingresa código, descripción u otro criterio de búsqueda.
El sistema consulta artículos activos y stock de la sucursal. Tablas consultadas: articulos, stock.
El sistema muestra los artículos encontrados con su stock disponible.
El usuario selecciona un artículo.
El usuario ingresa la cantidad solicitada.
El sistema valida que el artículo exista y esté activo.
El sistema valida que la cantidad sea numérica y mayor a cero.
El sistema evita agregar el mismo artículo más de una vez al pedido.
El sistema agrega el artículo a la grilla del pedido.
El usuario puede quitar artículos de la grilla antes de guardar.

Guardar
El usuario presiona Guardar.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para registrar pedidos.
El sistema valida que exista al menos un artículo cargado.
El sistema registra la cabecera del pedido en pedido_cabecera.
El sistema registra el detalle del pedido en pedido_detalle, incluyendo cantidad solicitada y stock actual.
El sistema emite mensaje de registro correcto y limpia el formulario.

Buscar e Imprimir
El usuario ingresa a Buscar Pedidos.
El sistema permite filtrar y ordenar pedidos.
El sistema consulta los pedidos registrados. Tablas consultadas: pedido_cabecera, usuarios.
El sistema muestra número de pedido, fecha, usuario, estado y acciones.
El usuario puede imprimir el pedido.
El sistema consulta la cabecera y detalle del pedido. Tablas consultadas: pedido_cabecera, pedido_detalle, articulos, usuarios.

Anular
El usuario ingresa a Buscar Pedidos.
El usuario presiona Anular sobre un pedido.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para anular pedidos.
El sistema verifica que el pedido exista.
El sistema verifica que el pedido pertenezca a la sucursal del usuario.
El sistema verifica que el pedido no se encuentre procesado.
El sistema actualiza el pedido a estado Anulado.
El sistema registra el usuario y fecha de actualización.
El sistema emite mensaje de anulación correcta.

Flujo Alternativo:
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si no existen artículos cargados, el sistema muestra datos incompletos.
Si la cantidad no es válida, el sistema no permite agregar el artículo.
Si el artículo ya fue agregado, el sistema no permite duplicarlo.
Si el pedido no existe, el sistema muestra pedido no encontrado.
Si el pedido pertenece a otra sucursal, el sistema no permite anularlo.
Si el pedido ya fue procesado, el sistema no permite anularlo.
Si ocurre un error durante el registro o anulación, el sistema muestra error.

Post Condición
El pedido queda registrado en pedido_cabecera.
Los artículos solicitados quedan registrados en pedido_detalle.
El pedido queda disponible para generar presupuesto de compra mientras se encuentre pendiente.
Si se anula, el pedido queda en estado Anulado.

Descripción de las tablas
Nombre	Alias	Base de Datos
pedido_cabecera	pedido_cabecera	Bd_reduc
pedido_detalle	pedido_detalle	Bd_reduc
articulos	articulos	Bd_reduc
stock	stock	Bd_reduc
usuarios	usuarios	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia
Agregar
Anular


Nombre de Caso de Uso
Registrar Presupuesto de Compra

Descripción Básica
Este caso permite registrar, consultar, imprimir y anular presupuestos de compra enviados por proveedores. El presupuesto se genera desde un pedido pendiente, se asocia a un proveedor, carga los artículos solicitados, permite ingresar precios y totaliza los importes para su posterior conversión en orden de compra.

Actores relacionados
Encargado de Compras

Pre Condición
El usuario debe estar autenticado en el sistema.
El usuario debe tener permisos para operar presupuestos de compra.
Debe existir un pedido de compra pendiente.
Debe existir un proveedor seleccionado.
El pedido debe pertenecer a la sucursal del usuario.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema mediante logueo.
El usuario ingresa al módulo Presupuesto de Compra.
El sistema muestra las opciones Nuevo Presupuesto y Buscar Presupuestos.

Nuevo
El usuario presiona Buscar Pedido.
El usuario ingresa un parámetro de búsqueda.
El sistema consulta pedidos pendientes de la sucursal. Tablas consultadas: pedido_cabecera, usuarios.
El sistema muestra los pedidos encontrados.
El usuario selecciona un pedido.
El sistema carga los artículos asociados al pedido. Tablas consultadas: pedido_detalle, articulos.
El usuario presiona Buscar Proveedor.
El sistema muestra el buscador de proveedores.
El usuario busca y selecciona un proveedor. Tabla consultada: proveedores.
El sistema carga el proveedor seleccionado.
El usuario ingresa el precio de cada artículo.
El usuario selecciona fecha de vencimiento del presupuesto.
El sistema calcula subtotales y total.

Guardar
El usuario presiona Guardar.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para registrar presupuestos.
El sistema valida que exista pedido seleccionado.
El sistema valida proveedor seleccionado.
El sistema valida que existan artículos cargados.
El sistema valida precios e importes.
El sistema valida fecha de vencimiento.
El sistema registra la cabecera en presupuesto_compra.
El sistema registra el detalle en presupuesto_detalle.
El sistema actualiza el pedido como procesado para presupuesto.
El sistema emite mensaje de registro correcto y limpia el formulario.

Buscar e Imprimir
El usuario ingresa a Buscar Presupuestos.
El sistema permite filtrar y ordenar presupuestos.
El sistema consulta los presupuestos registrados. Tablas consultadas: presupuesto_compra, proveedores, usuarios.
El sistema muestra número, proveedor, fecha, vencimiento, total, usuario, estado y acciones.
El usuario puede imprimir el presupuesto.
El sistema consulta la cabecera y detalle del presupuesto. Tablas consultadas: presupuesto_compra, presupuesto_detalle, articulos, proveedores, usuarios.

Anular
El usuario ingresa a Buscar Presupuestos.
El usuario presiona Anular sobre un presupuesto.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para anular presupuestos.
El sistema verifica que el presupuesto exista.
El sistema verifica que el presupuesto pertenezca a la sucursal del usuario.
El sistema verifica que el presupuesto no se encuentre procesado.
El sistema actualiza el presupuesto a estado Anulado.
Si el presupuesto proviene de un pedido, el sistema devuelve el pedido a estado pendiente.
El sistema emite mensaje de anulación correcta.

Flujo Alternativo:
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si no se selecciona pedido, proveedor o artículos, el sistema muestra datos incompletos.
Si la fecha de vencimiento no es válida, el sistema no permite guardar.
Si los precios o importes no son válidos, el sistema no permite guardar.
Si el presupuesto no existe, el sistema muestra presupuesto no encontrado.
Si el presupuesto pertenece a otra sucursal, el sistema no permite anularlo.
Si el presupuesto ya fue procesado, el sistema no permite anularlo.
Si ocurre un error durante el registro o anulación, el sistema muestra error.

Post Condición
El presupuesto queda registrado en presupuesto_compra.
Los artículos presupuestados quedan registrados en presupuesto_detalle.
El pedido relacionado queda marcado como procesado para presupuesto.
El presupuesto queda disponible para generar orden de compra mientras se encuentre pendiente.
Si se anula, el presupuesto queda en estado Anulado y el pedido vuelve a quedar pendiente.

Descripción de las tablas
Nombre	Alias	Base de Datos
presupuesto_compra	presupuesto_compra	Bd_reduc
presupuesto_detalle	presupuesto_detalle	Bd_reduc
pedido_cabecera	pedido_cabecera	Bd_reduc
pedido_detalle	pedido_detalle	Bd_reduc
articulos	articulos	Bd_reduc
proveedores	proveedores	Bd_reduc
usuarios	usuarios	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia
Agregar
Anular


Nombre de Caso de Uso
Generar Orden de Compra

Descripción Básica
Este caso permite registrar, consultar, imprimir y anular órdenes de compra. La orden puede generarse desde un presupuesto de compra pendiente o cargarse de forma directa sin presupuesto, seleccionando proveedor y artículos. La orden queda disponible para registrar la compra y controlar cantidades pendientes.

Actores relacionados
Encargado de Compras

Pre Condición
El usuario debe estar autenticado en el sistema.
El usuario debe tener permisos para operar órdenes de compra.
Debe existir un proveedor seleccionado.
Para generar desde presupuesto, debe existir un presupuesto pendiente de la sucursal del usuario.
Para una orden directa, deben existir artículos activos.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema mediante logueo.
El usuario ingresa al módulo Orden de Compra.
El sistema muestra las opciones Nueva Orden de Compra y Buscar Órdenes de Compra.

Generar desde presupuesto
El usuario busca un presupuesto de compra pendiente.
El sistema consulta presupuestos disponibles de la sucursal. Tablas consultadas: presupuesto_compra, proveedores, usuarios.
El sistema muestra los presupuestos encontrados.
El usuario selecciona un presupuesto.
El sistema carga proveedor, fecha, vencimiento y total.
El sistema consulta el detalle del presupuesto. Tablas consultadas: presupuesto_detalle, articulos.
El sistema muestra los artículos presupuestados.
El usuario confirma las cantidades a incluir en la orden.
El sistema valida que las cantidades no superen lo presupuestado.

Orden directa
El usuario selecciona la opción de orden sin presupuesto.
El usuario busca y selecciona un proveedor. Tabla consultada: proveedores.
El usuario busca artículos activos. Tablas consultadas: articulos, articulo_proveedor.
El sistema muestra los artículos encontrados con precio de compra asociado cuando exista.
El usuario agrega artículos e ingresa cantidades y precios.
El sistema calcula subtotales y total.

Guardar
El usuario presiona Guardar o Generar Orden.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para registrar órdenes de compra.
El sistema valida proveedor seleccionado.
El sistema valida que exista al menos un artículo.
El sistema valida cantidades, precios e importes.
Si proviene de presupuesto, el sistema valida que el presupuesto exista, pertenezca a la sucursal y esté pendiente.
El sistema registra la cabecera en orden_compra.
El sistema registra el detalle en orden_compra_detalle, incluyendo cantidad pendiente.
Si proviene de presupuesto, el sistema actualiza el presupuesto como procesado para orden de compra.
El sistema emite mensaje de registro correcto.

Buscar e Imprimir
El usuario ingresa a Buscar Órdenes de Compra.
El sistema permite filtrar y ordenar órdenes de compra.
El sistema consulta las órdenes registradas. Tablas consultadas: orden_compra, proveedores, usuarios.
El sistema muestra número, proveedor, fecha, total, usuario, estado y acciones.
El usuario puede imprimir la orden.
El sistema consulta la cabecera y detalle de la orden. Tablas consultadas: orden_compra, orden_compra_detalle, articulos, proveedores, usuarios.

Anular
El usuario ingresa a Buscar Órdenes de Compra.
El usuario presiona Anular sobre una orden.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para anular órdenes de compra.
El sistema verifica que la orden exista.
El sistema verifica que la orden pertenezca a la sucursal del usuario.
El sistema verifica que la orden no se encuentre procesada por compra.
El sistema actualiza la orden a estado Anulado.
El sistema emite mensaje de anulación correcta.

Flujo Alternativo:
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si no se selecciona proveedor o artículos, el sistema muestra datos incompletos.
Si la cantidad o precio no es válido, el sistema no permite guardar.
Si el presupuesto seleccionado no existe o no está pendiente, el sistema no permite generar la orden.
Si la cantidad ingresada supera la cantidad presupuestada, el sistema no permite guardar.
Si la orden no existe, el sistema muestra orden no encontrada.
Si la orden pertenece a otra sucursal, el sistema no permite anularla.
Si la orden ya fue procesada por compra, el sistema no permite anularla.
Si ocurre un error durante el registro o anulación, el sistema muestra error.

Post Condición
La orden queda registrada en orden_compra.
Los artículos quedan registrados en orden_compra_detalle.
Las cantidades pendientes quedan disponibles para registrar compras.
Si proviene de presupuesto, el presupuesto queda marcado como procesado.
Si se anula, la orden queda en estado Anulado.

Descripción de las tablas
Nombre	Alias	Base de Datos
orden_compra	orden_compra	Bd_reduc
orden_compra_detalle	orden_compra_detalle	Bd_reduc
presupuesto_compra	presupuesto_compra	Bd_reduc
presupuesto_detalle	presupuesto_detalle	Bd_reduc
proveedores	proveedores	Bd_reduc
articulos	articulos	Bd_reduc
articulo_proveedor	articulo_proveedor	Bd_reduc
usuarios	usuarios	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia
Agregar
Anular


Nombre de Caso de Uso
Registrar Compras

Descripción Básica
Este caso permite registrar, consultar, imprimir y anular facturas de compra recibidas de proveedores. La compra puede cargarse desde una orden de compra pendiente o de forma directa. Al guardar, el sistema registra cabecera y detalle, actualiza stock, genera movimientos de stock, registra libro de compras, genera cuentas a pagar cuando corresponde y actualiza la orden de compra si aplica.

Actores relacionados
Personal de Compras

Pre Condición
El usuario debe estar autenticado en el sistema.
El usuario debe tener permisos para operar compras.
Debe existir proveedor seleccionado o una orden de compra seleccionada.
Debe existir al menos un artículo en el detalle.
Si la compra se realiza desde orden de compra, la orden debe estar pendiente y pertenecer a la sucursal del usuario.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema mediante logueo.
El usuario ingresa al módulo Compras.
El sistema muestra las opciones Nueva Compra y Buscar Compras.

Nuevo desde orden de compra
El usuario presiona Cargar con Orden de Compra.
El usuario busca una orden pendiente por número o proveedor.
El sistema consulta órdenes pendientes de la sucursal. Tablas consultadas: orden_compra, proveedores.
El sistema muestra las órdenes disponibles.
El usuario selecciona una orden.
El sistema carga proveedor y datos de la orden.
El sistema consulta los artículos pendientes de la orden. Tablas consultadas: orden_compra_detalle, articulos, tipo_impuesto.
El sistema carga los artículos pendientes en la grilla de compra.
El sistema calcula subtotal, IVA y total.

Nuevo directo
El usuario carga o selecciona un proveedor.
El usuario agrega artículos al detalle de compra.
El sistema consulta artículos activos y datos impositivos. Tablas consultadas: articulos, tipo_impuesto.
El usuario ingresa cantidad recibida y precio unitario.
El sistema calcula subtotal, IVA y total.

Datos de factura
El usuario ingresa número de factura.
El usuario ingresa fecha de factura.
El usuario ingresa número de timbrado.
El usuario ingresa vencimiento de timbrado.
El usuario selecciona condición de compra.
Si la compra es a crédito, el usuario define datos de vencimiento o cuotas según corresponda.

Guardar
El usuario presiona Guardar.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para registrar compras.
El sistema valida proveedor, factura, timbrado, fechas, condición y detalle.
El sistema valida que no exista una factura activa duplicada para el proveedor, sucursal, número y timbrado.
El sistema valida cantidades, precios, IVA e importes.
Si proviene de orden de compra, el sistema valida la orden y las cantidades pendientes.
El sistema registra la cabecera en compra_cabecera.
El sistema registra el detalle en compra_detalle.
El sistema actualiza o crea el stock de cada artículo en la sucursal.
El sistema registra movimientos de entrada en movimientostock.
El sistema registra o actualiza la relación de artículo con proveedor cuando corresponda.
Si la compra es a crédito, el sistema registra cuentas a pagar.
El sistema registra el movimiento en libro_compra.
Si proviene de orden de compra, el sistema descuenta cantidades pendientes de la orden.
Si la orden queda sin pendientes, el sistema marca la orden como procesada.
El sistema emite mensaje de registro correcto.

Buscar e Imprimir
El usuario ingresa a Buscar Compras.
El sistema permite filtrar y ordenar compras.
El sistema consulta las compras registradas. Tablas consultadas: compra_cabecera, proveedores, usuarios.
El sistema muestra número de compra, proveedor, factura, fecha, total, condición, estado y acciones.
El usuario puede imprimir la compra.
El sistema consulta cabecera y detalle de la compra. Tablas consultadas: compra_cabecera, compra_detalle, articulos, proveedores, usuarios.

Anular
El usuario ingresa a Buscar Compras.
El usuario presiona Anular sobre una compra.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para anular compras.
El sistema verifica que la compra exista, esté activa y pertenezca a la sucursal del usuario.
El sistema actualiza la compra a estado Anulado.
El sistema consulta el detalle de la compra.
El sistema descuenta del stock las cantidades ingresadas por la compra.
El sistema registra movimientos de anulación en movimientostock.
Si la compra proviene de orden de compra, el sistema restaura las cantidades pendientes de la orden y la devuelve a estado pendiente.
El sistema anula cuentas a pagar relacionadas.
El sistema anula el registro correspondiente en libro_compra.
El sistema emite mensaje de anulación correcta.

Flujo Alternativo:
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si faltan datos obligatorios, el sistema muestra datos incompletos.
Si la factura ya existe activa para el proveedor, sucursal, número y timbrado, el sistema no permite guardar.
Si una cantidad, precio o importe no es válido, el sistema no permite guardar.
Si la orden de compra no existe, pertenece a otra sucursal o no está pendiente, el sistema no permite cargarla.
Si una cantidad recibida supera lo pendiente de la orden, el sistema no permite guardar.
Si falla la actualización de stock, el sistema revierte la operación.
Si la compra no existe o ya está anulada, el sistema no permite anularla.
Si la anulación deja stock negativo, el sistema no debe completar la operación.
Si ocurre un error durante el registro o anulación, el sistema muestra error.

Post Condición
La compra queda registrada en compra_cabecera.
Los artículos comprados quedan registrados en compra_detalle.
El stock queda incrementado por los artículos recibidos.
Los movimientos de stock quedan registrados en movimientostock.
El libro de compras queda registrado en libro_compra.
Si la compra es a crédito, quedan generadas las cuentas a pagar.
Si proviene de orden de compra, la orden queda actualizada según sus cantidades pendientes.
Si se anula, la compra queda en estado Anulado, se reversa stock, cuentas a pagar, libro de compras y orden de compra cuando corresponda.

Descripción de las tablas
Nombre	Alias	Base de Datos
compra_cabecera	compra_cabecera	Bd_reduc
compra_detalle	compra_detalle	Bd_reduc
proveedores	proveedores	Bd_reduc
orden_compra	orden_compra	Bd_reduc
orden_compra_detalle	orden_compra_detalle	Bd_reduc
articulos	articulos	Bd_reduc
tipo_impuesto	tipo_impuesto	Bd_reduc
articulo_proveedor	articulo_proveedor	Bd_reduc
stock	stock	Bd_reduc
movimientostock	movimientostock	Bd_reduc
cuentas_a_pagar	cuentas_a_pagar	Bd_reduc
libro_compra	libro_compra	Bd_reduc
usuarios	usuarios	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia
Agregar
Anular


Nombre de Caso de Uso
Registrar Nota de Remisión de Compra

Descripción Básica
Este caso permite registrar, consultar, imprimir y anular notas de remisión asociadas a facturas de compra. La remisión toma como origen una compra activa, copia el detalle de artículos de la factura y registra datos de transporte, vehículo, fechas y motivo de traslado.

Actores relacionados
Personal de Compras

Pre Condición
El usuario debe estar autenticado en el sistema.
El usuario debe tener permisos para operar remisiones de compra.
Debe existir una compra activa en la sucursal del usuario.
La compra debe tener detalle de artículos.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema mediante logueo.
El usuario ingresa al módulo Nota de Remisión de Compra.
El sistema muestra las opciones Nueva Remisión y Buscar Remisiones.

Nuevo
El usuario presiona Buscar Factura.
El usuario ingresa número de factura o criterio de búsqueda.
El sistema consulta compras activas de la sucursal. Tabla consultada: compra_cabecera.
El sistema muestra las facturas encontradas.
El usuario selecciona una compra.
El sistema carga datos de la compra y proveedor. Tablas consultadas: compra_cabecera, proveedores.
El sistema carga el detalle de artículos de la compra. Tablas consultadas: compra_detalle, articulos.
El usuario ingresa datos de remisión: número, fecha de emisión, transportista, documento, teléfono, vehículo, fechas de envío y llegada, y motivo.
El sistema muestra los artículos a incluir en la remisión.

Guardar
El usuario presiona Guardar.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para registrar remisiones.
El sistema valida compra seleccionada.
El sistema valida número de remisión y datos obligatorios de transporte.
El sistema registra la cabecera en nota_remision con tipo recepción compra.
El sistema registra el detalle en nota_remision_detalle.
El sistema emite mensaje de registro correcto.

Buscar e Imprimir
El usuario ingresa a Buscar Remisiones.
El sistema permite filtrar y ordenar remisiones.
El sistema consulta las remisiones registradas. Tablas consultadas: nota_remision, compra_cabecera, usuarios.
El sistema muestra número, fecha, factura asociada, transportista, estado y acciones.
El usuario puede imprimir la remisión.
El sistema consulta la cabecera y detalle de la remisión. Tablas consultadas: nota_remision, nota_remision_detalle, compra_cabecera, compra_detalle, articulos, proveedores, usuarios.

Anular
El usuario ingresa a Buscar Remisiones.
El usuario presiona Anular sobre una remisión.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para anular remisiones.
El sistema verifica que la remisión exista y pertenezca a la sucursal del usuario.
El sistema actualiza la remisión a estado Anulado.
El sistema registra usuario y fecha de anulación.
El sistema emite mensaje de anulación correcta.

Flujo Alternativo:
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si no se selecciona compra, el sistema muestra datos incompletos.
Si faltan datos obligatorios de remisión, el sistema no permite guardar.
Si la compra no existe o pertenece a otra sucursal, el sistema no permite cargarla.
Si la remisión no existe, el sistema muestra remisión no encontrada.
Si ocurre un error durante el registro o anulación, el sistema muestra error.

Post Condición
La remisión queda registrada en nota_remision.
Los artículos remitidos quedan registrados en nota_remision_detalle.
La remisión queda asociada a la compra seleccionada.
Si se anula, la remisión queda en estado Anulado.

Descripción de las tablas
Nombre	Alias	Base de Datos
nota_remision	nota_remision	Bd_reduc
nota_remision_detalle	nota_remision_detalle	Bd_reduc
compra_cabecera	compra_cabecera	Bd_reduc
compra_detalle	compra_detalle	Bd_reduc
articulos	articulos	Bd_reduc
proveedores	proveedores	Bd_reduc
usuarios	usuarios	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia
Agregar
Anular


Nombre de Caso de Uso
Registrar Notas de Crédito y Débito de Compra

Descripción Básica
Este caso permite registrar, consultar, imprimir y anular notas de crédito y débito de compra asociadas a una factura de compra. La nota puede impactar cuentas a pagar, libro de compras y, en el caso de devolución de mercadería, también stock y movimientos de stock.

Actores relacionados
Personal de Compras

Pre Condición
El usuario debe estar autenticado en el sistema.
El usuario debe tener permisos para operar notas de compra.
Debe existir una compra activa en la sucursal del usuario.
La compra debe tener detalle de artículos.
Para devolución de mercadería, debe existir stock suficiente en la sucursal.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema mediante logueo.
El usuario ingresa al módulo Notas de Crédito y Débito de Compra.
El sistema muestra las opciones Nueva Nota y Buscar Notas.

Nuevo
El usuario busca una factura de compra.
El sistema consulta compras activas de la sucursal. Tabla consultada: compra_cabecera.
El usuario selecciona una compra.
El sistema carga datos de la factura y proveedor. Tablas consultadas: compra_cabecera, proveedores.
El sistema carga el detalle de la factura. Tablas consultadas: compra_detalle, articulos, tipo_impuesto.
El usuario selecciona tipo de nota: crédito o débito.
El usuario ingresa número de nota, timbrado, fecha, descripción y condición de impacto.
El usuario selecciona los artículos o conceptos afectados.
El usuario ingresa cantidad, precio, IVA y subtotal según corresponda.
Si la nota es por devolución de mercadería, el usuario marca el movimiento de stock.
El sistema calcula el total de la nota.

Guardar
El usuario presiona Guardar.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para registrar notas de compra.
El sistema valida compra seleccionada.
El sistema valida tipo de nota, número, timbrado, fecha y detalle.
El sistema valida que no exista una nota activa duplicada para el proveedor, sucursal, tipo, número y timbrado.
El sistema valida cantidades, precios, IVA e importes.
Si la nota es de crédito, el sistema valida que el total acumulado de notas de crédito activas no supere el total de la factura.
Si la nota afecta stock, el sistema valida stock suficiente por artículo.
El sistema registra la cabecera en nota_compra.
El sistema registra el detalle en nota_compra_detalle.
El sistema registra el impacto en cuentas_a_pagar.
El sistema registra el movimiento en libro_compra.
Si la nota afecta stock, el sistema descuenta stock y registra movimientostock.
El sistema emite mensaje de registro correcto.

Buscar e Imprimir
El usuario ingresa a Buscar Notas.
El sistema permite filtrar y ordenar notas.
El sistema consulta las notas registradas. Tablas consultadas: nota_compra, compra_cabecera, proveedores, usuarios.
El sistema muestra número, tipo, proveedor, factura, fecha, total, estado y acciones.
El usuario puede imprimir la nota.
El sistema consulta cabecera y detalle de la nota. Tablas consultadas: nota_compra, nota_compra_detalle, compra_cabecera, compra_detalle, articulos, proveedores, usuarios.

Anular
El usuario ingresa a Buscar Notas.
El usuario presiona Anular sobre una nota.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para anular notas de compra.
El sistema verifica que la nota exista, esté activa y pertenezca a la sucursal del usuario.
El sistema actualiza la nota a estado Anulado.
El sistema registra el impacto inverso en cuentas_a_pagar.
Si la nota había afectado stock, el sistema restaura stock y registra movimiento inverso.
El sistema anula el registro relacionado en libro_compra.
El sistema emite mensaje de anulación correcta.

Flujo Alternativo:
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si no se selecciona factura, el sistema muestra datos incompletos.
Si el tipo de nota no es válido, el sistema no permite guardar.
Si la nota ya existe activa para el proveedor, sucursal, tipo, número y timbrado, el sistema no permite guardar.
Si una cantidad, precio o importe no es válido, el sistema no permite guardar.
Si la cantidad supera la cantidad comprada, el sistema no permite guardar.
Si la nota de crédito supera el total disponible de la factura, el sistema no permite guardar.
Si la devolución no tiene stock suficiente, el sistema no permite guardar.
Si la nota no existe o ya está anulada, el sistema no permite anularla.
Si ocurre un error durante el registro o anulación, el sistema muestra error.

Post Condición
La nota queda registrada en nota_compra.
El detalle queda registrado en nota_compra_detalle.
El impacto financiero queda registrado en cuentas_a_pagar.
El movimiento queda registrado en libro_compra.
Si corresponde devolución de mercadería, el stock queda actualizado y se registra movimientostock.
Si se anula, la nota queda en estado Anulado y se reversan los impactos correspondientes.

Descripción de las tablas
Nombre	Alias	Base de Datos
nota_compra	nota_compra	Bd_reduc
nota_compra_detalle	nota_compra_detalle	Bd_reduc
compra_cabecera	compra_cabecera	Bd_reduc
compra_detalle	compra_detalle	Bd_reduc
proveedores	proveedores	Bd_reduc
articulos	articulos	Bd_reduc
tipo_impuesto	tipo_impuesto	Bd_reduc
cuentas_a_pagar	cuentas_a_pagar	Bd_reduc
libro_compra	libro_compra	Bd_reduc
stock	stock	Bd_reduc
movimientostock	movimientostock	Bd_reduc
usuarios	usuarios	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia
Agregar
Anular


Nombre de Caso de Uso
Generar Transferencia entre Sucursales

Descripción Básica
Este caso permite registrar, consultar y recibir transferencias de artículos entre sucursales. Al generar la transferencia, el sistema descuenta stock de la sucursal origen, registra movimientos de salida y genera una nota de remisión de transferencia. Al recibir, el sistema ingresa stock en la sucursal destino, registra movimientos de entrada y actualiza el estado de la transferencia.

Actores relacionados
Personal de Compras
Encargado de Depósito

Pre Condición
El usuario debe estar autenticado en el sistema.
El usuario debe tener permisos para operar transferencias.
Debe existir una sucursal origen asociada al usuario.
Debe existir una sucursal destino activa y distinta a la sucursal origen.
Deben existir artículos activos con stock disponible en la sucursal origen.
Debe existir configuración de documento y timbrado para emitir la remisión de transferencia.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema mediante logueo.
El usuario ingresa al módulo Transferencia entre Sucursales.
El sistema muestra las opciones Nueva Transferencia, Buscar Transferencias y Recibir Transferencias.

Nueva transferencia
El usuario selecciona una sucursal destino.
El sistema consulta sucursales disponibles. Tabla consultada: sucursales.
El usuario busca artículos con stock en la sucursal origen.
El sistema consulta artículos activos y stock disponible. Tablas consultadas: articulos, stock.
El usuario agrega artículos e indica cantidad a transferir.
El sistema valida que la cantidad sea mayor a cero.
El sistema valida stock suficiente en la sucursal origen.
El usuario confirma la transferencia.
El sistema valida permisos para generar transferencias.
El sistema obtiene numeración de remisión. Tablas consultadas: sucursal_documento, timbrado.
El sistema registra la cabecera en transferencia_stock.
El sistema registra el detalle en transferencia_stock_detalle.
El sistema descuenta stock de la sucursal origen.
El sistema registra movimientos de salida en movimientostock.
El sistema registra la nota de remisión de transferencia en nota_remision.
El sistema registra el detalle de remisión en nota_remision_detalle.
El sistema actualiza la numeración del documento.
El sistema emite mensaje de registro correcto.

Buscar
El usuario ingresa a Buscar Transferencias.
El sistema permite filtrar transferencias.
El sistema consulta transferencias registradas. Tablas consultadas: transferencia_stock, sucursales, nota_remision.
El sistema muestra sucursal origen, destino, fecha, estado, remisión y acciones.

Recibir transferencia
El usuario ingresa a Recibir Transferencias.
El sistema muestra transferencias en tránsito para la sucursal del usuario.
El usuario selecciona una transferencia.
El sistema consulta cabecera y detalle. Tablas consultadas: transferencia_stock, transferencia_stock_detalle, articulos, sucursales.
El sistema muestra artículos enviados y cantidades pendientes de recepción.
El usuario ingresa cantidades recibidas.
El sistema valida que las cantidades recibidas no superen lo enviado pendiente.
El usuario confirma recepción.
El sistema valida permisos para recibir transferencias.
El sistema registra las cantidades recibidas en transferencia_stock_detalle.
El sistema actualiza o crea stock en la sucursal destino.
El sistema registra movimientos de entrada en movimientostock.
El sistema actualiza la transferencia como recibida o recibida parcialmente.
Si existen faltantes, el sistema genera una nueva transferencia por la diferencia pendiente.
El sistema emite mensaje de recepción correcta.

Flujo Alternativo:
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si no se selecciona sucursal destino, el sistema muestra datos incompletos.
Si la sucursal destino es igual a la sucursal origen, el sistema no permite guardar.
Si no existen artículos cargados, el sistema no permite guardar.
Si no hay stock suficiente en origen, el sistema no permite transferir.
Si no existe configuración de documento o timbrado, el sistema no permite generar la remisión.
Si la transferencia no existe o no está en tránsito, el sistema no permite recibirla.
Si la cantidad recibida supera la cantidad pendiente, el sistema no permite guardar.
Si ocurre un error durante generación o recepción, el sistema revierte la operación.

Post Condición
La transferencia queda registrada en transferencia_stock.
El detalle queda registrado en transferencia_stock_detalle.
El stock de la sucursal origen queda descontado al generar la transferencia.
La remisión de transferencia queda registrada en nota_remision y nota_remision_detalle.
Al recibir, el stock de la sucursal destino queda incrementado.
Los movimientos de salida y entrada quedan registrados en movimientostock.
La transferencia queda en tránsito, recibida o recibida parcialmente según corresponda.

Descripción de las tablas
Nombre	Alias	Base de Datos
transferencia_stock	transferencia_stock	Bd_reduc
transferencia_stock_detalle	transferencia_stock_detalle	Bd_reduc
articulos	articulos	Bd_reduc
stock	stock	Bd_reduc
movimientostock	movimientostock	Bd_reduc
sucursales	sucursales	Bd_reduc
nota_remision	nota_remision	Bd_reduc
nota_remision_detalle	nota_remision_detalle	Bd_reduc
sucursal_documento	sucursal_documento	Bd_reduc
timbrado	timbrado	Bd_reduc
articulo_proveedor	articulo_proveedor	Bd_reduc
usuarios	usuarios	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia
Agregar
Recibir


Nombre de Caso de Uso
Registrar Ajuste de Inventario

Descripción Básica
Este caso permite crear inventarios de ajuste, cargar conteos físicos, aplicar diferencias al stock y anular ajustes. El sistema genera un inventario por tipo de artículo, categoría, proveedor o todos los artículos, registra el stock teórico, permite modificar el conteo físico y aplica diferencias mediante movimientos de stock.

Actores relacionados
Encargado de Depósito
Personal de Compras

Pre Condición
El usuario debe estar autenticado en el sistema.
El usuario debe tener permisos para operar inventarios.
Debe existir una sucursal asociada al usuario.
Deben existir artículos activos para inventariar.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema mediante logueo.
El usuario ingresa al módulo Ajuste de Inventario.
El sistema muestra las opciones Nuevo Inventario, Cargar Conteo, Aplicar Ajuste y Buscar Inventarios.

Nuevo inventario
El usuario selecciona tipo de inventario.
El usuario puede filtrar por categoría, proveedor o todos los artículos.
El sistema consulta categorías y proveedores cuando corresponde. Tablas consultadas: categorias, proveedores.
El sistema consulta artículos activos y stock teórico de la sucursal. Tablas consultadas: articulos, stock, articulo_proveedor.
El sistema registra la cabecera en ajuste_inventario.
El sistema registra el detalle en ajuste_inventario_detalle con cantidad teórica, cantidad física inicial y costo.
El sistema emite mensaje de registro correcto.

Cargar conteo físico
El usuario busca un inventario pendiente o en carga.
El sistema consulta inventarios registrados. Tablas consultadas: ajuste_inventario, usuarios.
El usuario selecciona un inventario.
El sistema carga el detalle. Tablas consultadas: ajuste_inventario_detalle, articulos.
El usuario ingresa cantidades físicas contadas.
El sistema valida cantidades numéricas y no negativas.
El sistema actualiza las cantidades físicas en ajuste_inventario_detalle.
El sistema actualiza el inventario a estado cargado.
El sistema emite mensaje de actualización correcta.

Aplicar ajuste
El usuario selecciona un inventario cargado.
El sistema consulta la cabecera y detalle del inventario.
El sistema calcula diferencias entre cantidad física y teórica.
El sistema valida que la aplicación no deje stock negativo.
El sistema actualiza o crea stock por artículo en la sucursal.
El sistema registra movimientos de ajuste en movimientostock.
El sistema actualiza el inventario a estado aplicado.
El sistema registra usuario y fecha de ajuste.
El sistema emite mensaje de aplicación correcta.

Buscar
El usuario ingresa a Buscar Inventarios.
El sistema permite filtrar y ordenar inventarios.
El sistema consulta inventarios registrados. Tablas consultadas: ajuste_inventario, usuarios.
El sistema muestra número, tipo, fecha, estado, usuario y acciones.

Anular
El usuario presiona Anular sobre un inventario.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para anular inventarios.
El sistema verifica que el inventario exista y pertenezca a la sucursal del usuario.
Si el inventario está pendiente o cargado, el sistema cambia el estado a Anulado.
Si el inventario ya fue aplicado, el sistema revierte las diferencias aplicadas sobre stock.
Si el inventario ya fue aplicado, el sistema registra movimientos inversos en movimientostock.
El sistema actualiza el inventario a estado Anulado.
El sistema emite mensaje de anulación correcta.

Flujo Alternativo:
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si no existen artículos para inventariar, el sistema no permite crear el inventario.
Si la cantidad física no es válida, el sistema no permite guardar el conteo.
Si el inventario no existe o pertenece a otra sucursal, el sistema no permite operar.
Si el inventario no está cargado, el sistema no permite aplicar ajuste.
Si la aplicación o reversa deja stock negativo, el sistema no permite completar la operación.
Si ocurre un error durante creación, carga, aplicación o anulación, el sistema muestra error.

Post Condición
El inventario queda registrado en ajuste_inventario.
El detalle queda registrado en ajuste_inventario_detalle.
El conteo físico queda actualizado cuando se carga el inventario.
Si se aplica el ajuste, el stock queda actualizado y se registran movimientos en movimientostock.
Si se anula, el inventario queda en estado Anulado y se revierten efectos de stock cuando corresponda.

Descripción de las tablas
Nombre	Alias	Base de Datos
ajuste_inventario	ajuste_inventario	Bd_reduc
ajuste_inventario_detalle	ajuste_inventario_detalle	Bd_reduc
articulos	articulos	Bd_reduc
stock	stock	Bd_reduc
movimientostock	movimientostock	Bd_reduc
articulo_proveedor	articulo_proveedor	Bd_reduc
categorias	categorias	Bd_reduc
proveedores	proveedores	Bd_reduc
usuarios	usuarios	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia
Agregar
Aplicar
Anular
