# Especificacion de Caso de Uso: Informes del Sistema

## Generar Informes

* **Nombre de Caso de Uso**  
Generar informes del sistema.

* **Descripcion Basica**  
Este caso de uso se ocupa de la generacion de informes del sistema en formato de consulta previa o PDF. Los informes permiten visualizar datos de articulos, proveedores, clientes, empleados, compras, stock, transferencias y servicios, aplicando filtros segun el modulo.

* **Actores relacionados**  
Administrador del sistema.  
Personal autorizado de compras.  
Personal autorizado de servicios.  
Personal autorizado de inventario.  
Usuario con permisos de consulta.

* **Pre Condicion**  
El usuario debe estar autenticado en el sistema.  
El usuario debe tener permiso para visualizar el informe solicitado.  
Deben existir registros previos en el modulo correspondiente.  
Para informes filtrados, el usuario debe ingresar o seleccionar criterios validos.

* **Flujo de eventos**

**Flujo Basico:**

**Acceso al modulo de informes**  
* El usuario accede al sistema mediante logueo.
* El usuario ingresa a la interfaz del informe requerido.
* El sistema valida los permisos del usuario.
* El sistema muestra los filtros disponibles segun el informe.
* El usuario selecciona los criterios de busqueda.
* El usuario presiona el boton para consultar o generar PDF.
* El sistema consulta las tablas correspondientes.
* El sistema aplica los filtros seleccionados.
* El sistema genera el informe con los datos encontrados.
* El sistema muestra la vista previa o abre el PDF generado.

---

## Informe de Articulos

* **Descripcion del informe**  
Permite consultar articulos registrados, con o sin detalle de stock, filtrando por sucursal, categoria, proveedor, estado, codigo y disponibilidad de stock.

* **Permiso requerido**  
`articulo.ver`

* **Filtros disponibles**  
Sucursal.  
Categoria.  
Proveedor.  
Estado.  
Codigo.  
Stock.

* **Flujo especifico**  
* El usuario ingresa al informe de articulos.
* El sistema carga categorias, proveedores y sucursales.
* El usuario selecciona los filtros.
* El sistema consulta articulos y, si corresponde, su stock por sucursal.
* El sistema genera resumen de articulos activos, inactivos, con stock, sin stock o bajo minimo.
* El usuario puede imprimir el informe en PDF.

**Tablas consultadas:**  
articulos  
categorias  
marcas  
articulo_proveedor  
proveedores  
unidad_medida  
tipo_impuesto  
stock  
sucursales

---

## Informe de Proveedores

* **Descripcion del informe**  
Permite consultar proveedores registrados, filtrando por estado o busqueda por razon social/RUC.

* **Permiso requerido**  
`proveedor.ver`

* **Filtros disponibles**  
Estado.  
Busqueda por razon social o RUC.

* **Flujo especifico**  
* El usuario ingresa al informe de proveedores.
* El sistema muestra los filtros.
* El usuario selecciona estado o ingresa texto de busqueda.
* El sistema consulta los proveedores.
* El sistema genera resumen de proveedores activos e inactivos.
* El usuario puede imprimir el informe en PDF.

**Tablas consultadas:**  
proveedores

---

## Informe de Clientes

* **Descripcion del informe**  
Permite consultar clientes registrados, filtrando por estado o busqueda por nombre, apellido, documento o correo.

* **Permiso requerido**  
`cliente.ver`

* **Filtros disponibles**  
Estado.  
Busqueda por cliente, documento o email.

* **Flujo especifico**  
* El usuario ingresa al informe de clientes.
* El sistema muestra los filtros.
* El usuario ingresa los criterios.
* El sistema consulta los clientes.
* El sistema muestra ciudad, datos personales y estado.
* El sistema genera resumen de clientes activos e inactivos.
* El usuario puede imprimir el informe en PDF.

**Tablas consultadas:**  
clientes  
ciudades

---

## Informe de Empleados

* **Descripcion del informe**  
Permite consultar empleados registrados, filtrando por sucursal, cargo, estado o busqueda por nombre, apellido o cedula.

* **Permiso requerido**  
`empleado.ver`

* **Filtros disponibles**  
Sucursal.  
Cargo.  
Estado.  
Busqueda por empleado o cedula.

* **Flujo especifico**  
* El usuario ingresa al informe de empleados.
* El sistema carga sucursales y cargos.
* El usuario selecciona filtros.
* El sistema consulta empleados.
* El sistema muestra datos personales, cargo, sucursal y estado.
* El sistema genera resumen de empleados activos e inactivos.
* El usuario puede imprimir el informe en PDF.

**Tablas consultadas:**  
empleados  
cargos  
sucursales

---

## Informe de Pedidos de Compra

* **Descripcion del informe**  
Permite consultar pedidos de compra registrados, filtrando por fecha, estado y sucursal.

* **Permiso requerido**  
`compra.pedido.ver`

* **Filtros disponibles**  
Fecha desde.  
Fecha hasta.  
Estado.  
Sucursal.

* **Flujo especifico**  
* El usuario ingresa al informe de pedidos.
* El usuario selecciona rango de fechas, estado o sucursal.
* El sistema consulta los pedidos.
* El sistema muestra usuario, sucursal, estado y cantidad de items.
* El usuario genera el PDF del informe.

**Tablas consultadas:**  
pedido_cabecera  
pedido_detalle  
usuarios  
sucursales

---

## Informe de Presupuestos de Compra

* **Descripcion del informe**  
Permite consultar presupuestos de compra, filtrando por fecha, estado y sucursal.

* **Permiso requerido**  
`compra.presupuesto.ver`

* **Filtros disponibles**  
Fecha desde.  
Fecha hasta.  
Estado.  
Sucursal.

* **Flujo especifico**  
* El usuario ingresa al informe de presupuestos de compra.
* El usuario selecciona los filtros.
* El sistema consulta presupuestos, proveedor, usuarios y detalle.
* El sistema calcula cantidad de items y unidades.
* El usuario genera el PDF del informe.

**Tablas consultadas:**  
presupuesto_compra  
presupuesto_detalle  
proveedores  
usuarios  
sucursales

---

## Informe de Ordenes de Compra

* **Descripcion del informe**  
Permite consultar ordenes de compra, filtrando por fecha, estado y sucursal.

* **Permiso requerido**  
`compra.oc.ver`

* **Filtros disponibles**  
Fecha desde.  
Fecha hasta.  
Estado.  
Sucursal.

* **Flujo especifico**  
* El usuario ingresa al informe de ordenes de compra.
* El usuario selecciona los filtros.
* El sistema consulta ordenes de compra, proveedor, usuarios, sucursal y detalle.
* El sistema muestra cantidad de items y unidades.
* El usuario genera el PDF del informe.

**Tablas consultadas:**  
orden_compra  
orden_compra_detalle  
proveedores  
usuarios  
sucursales

---

## Informe de Compras

* **Descripcion del informe**  
Permite consultar facturas de compra registradas, filtrando por fecha, estado y sucursal.

* **Permiso requerido**  
`compra.factura.ver`

* **Filtros disponibles**  
Fecha desde.  
Fecha hasta.  
Estado.  
Sucursal.

* **Flujo especifico**  
* El usuario ingresa al informe de compras.
* El usuario selecciona los filtros.
* El sistema consulta compras, proveedor, usuarios, sucursal y detalle.
* El sistema muestra importes y cantidad de items.
* El usuario genera el PDF del informe.

**Tablas consultadas:**  
compra_cabecera  
compra_detalle  
proveedores  
usuarios  
sucursales

---

## Informe Libro de Compras

* **Descripcion del informe**  
Permite consultar el libro de compras registrado, filtrando por fecha, proveedor, estado y sucursal.

* **Permiso requerido**  
`compras.reporte.ver`

* **Filtros disponibles**  
Fecha desde.  
Fecha hasta.  
Proveedor.  
Estado.  
Sucursal.

* **Flujo especifico**  
* El usuario ingresa al informe Libro de Compras.
* El usuario selecciona los filtros.
* El sistema consulta los comprobantes registrados en el libro.
* El sistema muestra comprobante, proveedor, RUC, importes gravados, IVA, exenta y total.
* El usuario genera el PDF del informe.

**Tablas consultadas:**  
libro_compra  
sucursales

---

## Informe de Transferencias

* **Descripcion del informe**  
Permite consultar transferencias de stock entre sucursales, filtrando por sucursal, estado, tipo y rango de fechas.

* **Permiso requerido**  
`transferencia.ver`

* **Filtros disponibles**  
Sucursal.  
Estado.  
Tipo.  
Fecha desde.  
Fecha hasta.

* **Flujo especifico**  
* El usuario ingresa al informe de transferencias.
* El usuario selecciona sucursal, tipo de movimiento, estado o fechas.
* El sistema consulta transferencias y notas de remision asociadas.
* El sistema muestra sucursal origen, sucursal destino, estado y datos de remision.
* El sistema genera resumen de transferencias en transito, recibidas y parciales.
* El usuario puede imprimir el PDF.

**Tablas consultadas:**  
transferencia_stock  
sucursales  
nota_remision

---

## Informe de Movimientos de Stock

* **Descripcion del informe**  
Permite consultar entradas y salidas de stock registradas en el sistema, filtrando por sucursal, tipo de movimiento, signo y fechas.

* **Permiso requerido**  
`stock.movimientos.ver`

* **Filtros disponibles**  
Sucursal.  
Tipo de movimiento.  
Signo.  
Fecha desde.  
Fecha hasta.

* **Flujo especifico**  
* El usuario ingresa al informe de movimientos de stock.
* El usuario selecciona los filtros.
* El sistema consulta los movimientos de stock.
* El sistema muestra fecha, sucursal, tipo, articulo, cantidad, signo, costos, referencia y usuario.
* El sistema genera resumen de entradas y salidas.
* El usuario puede imprimir el PDF.

**Tablas consultadas:**  
movimientostock  
sucursales  
articulos  
usuarios

---

## Informe de Recepcion de Servicios

* **Descripcion del informe**  
Permite consultar recepciones de servicio registradas, filtrando por fecha, estado y sucursal.

* **Permiso requerido**  
`servicio.recepcion.ver`

* **Filtros disponibles**  
Fecha desde.  
Fecha hasta.  
Estado.  
Sucursal.

* **Flujo especifico**  
* El usuario ingresa al informe de recepcion de servicios.
* El usuario selecciona los filtros.
* El sistema consulta recepciones de servicio.
* El sistema muestra cliente, vehiculo, kilometraje, usuario, sucursal y estado.
* El usuario genera el PDF del informe.

**Tablas consultadas:**  
recepcion_servicio  
clientes  
vehiculos  
modelo_auto  
marcas  
usuarios  
sucursales

---

## Informe de Presupuesto de Servicios

* **Descripcion del informe**  
Permite consultar presupuestos de servicio generados, filtrando por fecha, estado y sucursal.

* **Permiso requerido**  
`servicio.presupuesto.ver`

* **Filtros disponibles**  
Fecha desde.  
Fecha hasta.  
Estado.  
Sucursal.

* **Flujo especifico**  
* El usuario ingresa al informe de presupuesto de servicios.
* El usuario selecciona los filtros.
* El sistema consulta presupuestos de servicio, diagnostico y recepcion asociada.
* El sistema muestra cliente, vehiculo, usuario, sucursal, importes y cantidad de items.
* El usuario genera el PDF del informe.

**Tablas consultadas:**  
presupuesto_servicio  
presupuesto_detalleservicio  
diagnostico_servicio  
recepcion_servicio  
usuarios  
sucursales  
clientes  
vehiculos  
modelo_auto  
marcas

---

## Informe de Ordenes de Trabajo

* **Descripcion del informe**  
Permite consultar ordenes de trabajo generadas, filtrando por fecha, estado y sucursal.

* **Permiso requerido**  
`servicio.ot.ver`

* **Filtros disponibles**  
Fecha desde.  
Fecha hasta.  
Estado.  
Sucursal.

* **Flujo especifico**  
* El usuario ingresa al informe de ordenes de trabajo.
* El usuario selecciona los filtros.
* El sistema consulta ordenes de trabajo y sus datos relacionados.
* El sistema muestra presupuesto, recepcion, cliente, vehiculo, equipo, usuario, sucursal y cantidad de items.
* El usuario genera el PDF del informe.

**Tablas consultadas:**  
orden_trabajo  
orden_trabajo_detalle  
presupuesto_servicio  
diagnostico_servicio  
recepcion_servicio  
sucursales  
usuarios  
equipo_trabajo  
clientes  
vehiculos  
modelo_auto  
marcas

---

## Informe de Registro de Servicios

* **Descripcion del informe**  
Permite consultar servicios registrados, filtrando por fecha, estado, tecnico encargado y sucursal.

* **Permiso requerido**  
`servicio.registro.ver`

* **Filtros disponibles**  
Fecha desde.  
Fecha hasta.  
Estado.  
Tecnico encargado.  
Sucursal.

* **Flujo especifico**  
* El usuario ingresa al informe de registro de servicios.
* El usuario selecciona filtros.
* El sistema consulta registros de servicio.
* El sistema muestra orden de trabajo, usuario que registra, equipo, tecnico, cliente, vehiculo, sucursal, observacion, cantidad de items y total.
* El usuario genera el PDF del informe.

**Tablas consultadas:**  
registro_servicio  
registro_servicio_detalle  
orden_trabajo  
presupuesto_servicio  
diagnostico_servicio  
recepcion_servicio  
sucursales  
clientes  
vehiculos  
modelo_auto  
marcas  
usuarios  
equipo_trabajo  
empleados

---

* **Flujo Alternativo**  
El sistema no permite generar un informe si el usuario no posee el permiso correspondiente.  
El sistema redirecciona al inicio cuando el usuario intenta generar un PDF sin permiso.  
El sistema puede generar el informe sin filtros cuando los campos se dejan vacios.  
El sistema muestra el informe vacio si no existen datos para los criterios seleccionados.  
El usuario puede modificar filtros y volver a generar el informe.  

* **Post Condicion**  
El sistema genera la consulta previa o el PDF del informe solicitado.  
El informe muestra la empresa y el usuario que genera el documento cuando corresponde.  
Los datos del sistema no son modificados por la generacion del informe.  
El usuario obtiene informacion filtrada para control, auditoria o toma de decisiones.

* **Tablas involucradas en el modulo de informes**  
articulos  
categorias  
marcas  
articulo_proveedor  
proveedores  
unidad_medida  
tipo_impuesto  
stock  
sucursales  
clientes  
ciudades  
empleados  
cargos  
pedido_cabecera  
pedido_detalle  
presupuesto_compra  
presupuesto_detalle  
orden_compra  
orden_compra_detalle  
compra_cabecera  
compra_detalle  
libro_compra  
transferencia_stock  
nota_remision  
movimientostock  
usuarios  
recepcion_servicio  
vehiculos  
modelo_auto  
presupuesto_servicio  
presupuesto_detalleservicio  
diagnostico_servicio  
orden_trabajo  
orden_trabajo_detalle  
equipo_trabajo  
registro_servicio  
registro_servicio_detalle
