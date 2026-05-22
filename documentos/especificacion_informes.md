# Especificaciones de Casos de Uso: Informes del Sistema

## Informe de Articulos

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de articulos registrados en el sistema.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.articulos.ver`.
- El usuario ingresa al menu Informes Referenciales.
- Existen articulos registrados.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona la opcion Informes Referenciales, Referenciales de Compras y habilita el informe de articulos.

#### Generar
- El sistema valida permiso `reportes.articulos.ver`.
- El sistema carga categorias disponibles. Tabla consultada: categorias.
- El sistema carga proveedores disponibles. Tabla consultada: proveedores.
- El sistema muestra filtros de categoria, proveedor, estado y codigo.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.articulos.ver`.
- El sistema recibe los filtros.
- El sistema consulta los articulos segun los filtros seleccionados.
- Tablas consultadas: articulos, categorias, marcas, articulo_proveedor, proveedores, unidad_medida, tipo_impuesto.
- El sistema calcula resumen de total, activos e inactivos.
- El sistema carga los datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema envia los filtros al generador del reporte.
- El sistema valida nuevamente permiso `reportes.articulos.ver`.
- El sistema consulta nuevamente los datos.
- El sistema carga la plantilla PDF de articulos.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todos los articulos permitidos.
- Si no existen datos para los filtros seleccionados, el sistema muestra la grilla sin registros.
- Si no se puede generar el PDF, el sistema no emite el informe.

### Post Condicion
- El sistema muestra los articulos encontrados.
- El sistema genera el PDF cuando la operacion se ejecuta correctamente.
- La informacion consultada no se modifica.

### Tablas Involucradas
- articulos
- categorias
- marcas
- articulo_proveedor
- proveedores
- unidad_medida
- tipo_impuesto

---

## Informe de Proveedores

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de proveedores registrados.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.proveedores.ver`.
- Existen proveedores registrados.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes Referenciales, Referenciales de Compras y habilita el informe de proveedores.

#### Generar
- El sistema valida permiso `reportes.proveedores.ver`.
- El sistema muestra filtros de estado y busqueda.
- El usuario selecciona estado o ingresa texto de busqueda.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.proveedores.ver`.
- El sistema consulta proveedores segun los filtros.
- Tabla consultada: proveedores.
- El sistema calcula resumen de total, activos e inactivos.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema valida nuevamente permiso `reportes.proveedores.ver`.
- El sistema consulta nuevamente los datos.
- El sistema carga la plantilla PDF de proveedores.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todos los proveedores.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros.

### Post Condicion
- El sistema muestra los proveedores encontrados.
- El sistema genera el PDF cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- proveedores

---

## Informe de Sucursales

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de sucursales registradas.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.sucursales.ver`.
- Existen sucursales registradas.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes Referenciales, Referenciales de Compras y habilita el informe de sucursales.

#### Generar
- El sistema valida permiso `reportes.sucursales.ver`.
- El sistema muestra filtros de estado y busqueda.
- El usuario selecciona estado o ingresa texto de busqueda.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.sucursales.ver`.
- El sistema consulta sucursales segun los filtros.
- Tablas consultadas: sucursales, empresa.
- El sistema calcula resumen de total, activas e inactivas.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema valida nuevamente permiso `reportes.sucursales.ver`.
- El sistema consulta nuevamente los datos.
- El sistema carga la plantilla PDF de sucursales.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todas las sucursales.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros.

### Post Condicion
- El sistema muestra las sucursales encontradas.
- El sistema genera el PDF cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- sucursales
- empresa

---

## Informe de Clientes

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de clientes registrados.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.clientes.ver`.
- Existen clientes registrados.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes Referenciales, Referenciales de Servicios y habilita el informe de clientes.

#### Generar
- El sistema valida permiso `reportes.clientes.ver`.
- El sistema muestra filtros de estado y busqueda.
- El usuario selecciona estado o ingresa cliente, documento o correo.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.clientes.ver`.
- El sistema consulta clientes segun los filtros.
- Tablas consultadas: clientes, ciudades.
- El sistema calcula resumen de total, activos e inactivos.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema valida nuevamente permiso `reportes.clientes.ver`.
- El sistema consulta nuevamente los datos.
- El sistema carga la plantilla PDF de clientes.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todos los clientes.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros.

### Post Condicion
- El sistema muestra los clientes encontrados.
- El sistema genera el PDF cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- clientes
- ciudades

---

## Informe de Vehiculos

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de vehiculos registrados.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.vehiculos.ver`.
- Existen vehiculos registrados.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes Referenciales, Referenciales de Servicios y habilita el informe de vehiculos.

#### Generar
- El sistema valida permiso `reportes.vehiculos.ver`.
- El sistema consulta modelos activos para el filtro. Tabla consultada: modelo_auto.
- El sistema muestra filtros de modelo, estado y busqueda.
- El usuario selecciona modelo, estado o ingresa placa, serie, color, documento o cliente.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.vehiculos.ver`.
- El sistema consulta vehiculos segun los filtros.
- Tablas consultadas: vehiculos, clientes, modelo_auto.
- El sistema calcula resumen de total, activos e inactivos.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema valida nuevamente permiso `reportes.vehiculos.ver`.
- El sistema consulta nuevamente los datos.
- El sistema carga la plantilla PDF de vehiculos.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todos los vehiculos.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros.

### Post Condicion
- El sistema muestra los vehiculos encontrados.
- El sistema genera el PDF cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- vehiculos
- clientes
- modelo_auto

---

## Informe de Empleados

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de empleados registrados.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.empleados.ver`.
- Existen empleados registrados.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes Referenciales, Referenciales de Servicios y habilita el informe de empleados.

#### Generar
- El sistema valida permiso `reportes.empleados.ver`.
- El sistema consulta sucursales para el filtro. Tabla consultada: sucursales.
- El sistema consulta cargos para el filtro. Tabla consultada: cargos.
- El sistema muestra filtros de sucursal, cargo, estado y busqueda.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.empleados.ver`.
- El sistema consulta empleados segun los filtros.
- Tablas consultadas: empleados, cargos, sucursales.
- El sistema calcula resumen de total, activos e inactivos.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema valida nuevamente permiso `reportes.empleados.ver`.
- El sistema consulta nuevamente los datos.
- El sistema carga la plantilla PDF de empleados.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todos los empleados.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros.

### Post Condicion
- El sistema muestra los empleados encontrados.
- El sistema genera el PDF cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- empleados
- cargos
- sucursales

---

## Informe de Pedidos de Compra

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de pedidos de compra.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.pedidos.ver`.
- Existen pedidos de compra registrados.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes de Movimientos, Informes de Compras y habilita el informe de pedidos.

#### Generar
- El sistema valida permiso `reportes.pedidos.ver`.
- El sistema consulta sucursales para el filtro. Tabla consultada: sucursales.
- El sistema muestra filtros de fecha desde, fecha hasta, estado (Pendiente, Procesado o Anulado) y sucursal.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.pedidos.ver`.
- El sistema consulta pedidos de compra segun los filtros.
- Tablas consultadas: pedido_cabecera, pedido_detalle, usuarios, sucursales.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema envia los filtros al generador del reporte.
- El sistema valida nuevamente permiso `reportes.pedidos.ver`.
- El sistema consulta nuevamente los pedidos de compra segun los filtros.
- El sistema carga la plantilla PDF de pedidos.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todos los pedidos permitidos.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros y el PDF indica que no existen registros.

### Post Condicion
- El sistema muestra los pedidos encontrados.
- El sistema genera el PDF de pedidos cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- pedido_cabecera
- pedido_detalle
- usuarios
- sucursales

---

## Informe de Presupuestos de Compra

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de presupuestos de compra.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.presupuestos_compra.ver`.
- Existen presupuestos de compra registrados.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes de Movimientos, Informes de Compras y habilita el informe de presupuestos de compra.

#### Generar
- El sistema valida permiso `reportes.presupuestos_compra.ver`.
- El sistema consulta sucursales para el filtro. Tabla consultada: sucursales.
- El sistema muestra filtros de fecha desde, fecha hasta, estado (Pendiente, OC generada o Anulado) y sucursal.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.presupuestos_compra.ver`.
- El sistema consulta presupuestos de compra segun los filtros.
- Tablas consultadas: presupuesto_compra, presupuesto_detalle, proveedores, usuarios, sucursales.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema envia los filtros al generador del reporte.
- El sistema valida nuevamente permiso `reportes.presupuestos_compra.ver`.
- El sistema consulta nuevamente los presupuestos de compra segun los filtros.
- El sistema carga la plantilla PDF de presupuestos de compra.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todos los presupuestos permitidos.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros y el PDF indica que no existen registros.

### Post Condicion
- El sistema muestra los presupuestos de compra encontrados.
- El sistema genera el PDF de presupuestos de compra cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- presupuesto_compra
- presupuesto_detalle
- proveedores
- usuarios
- sucursales

---

## Informe de Ordenes de Compra

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de ordenes de compra.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.ordenes_compra.ver`.
- Existen ordenes de compra registradas.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes de Movimientos, Informes de Compras y habilita el informe de ordenes de compra.

#### Generar
- El sistema valida permiso `reportes.ordenes_compra.ver`.
- El sistema consulta sucursales para el filtro. Tabla consultada: sucursales.
- El sistema muestra filtros de fecha desde, fecha hasta, estado (Pendiente, Procesado o Anulado) y sucursal.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.ordenes_compra.ver`.
- El sistema consulta ordenes de compra segun los filtros.
- Tablas consultadas: orden_compra, orden_compra_detalle, proveedores, usuarios, sucursales.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema envia los filtros al generador del reporte.
- El sistema valida nuevamente permiso `reportes.ordenes_compra.ver`.
- El sistema consulta nuevamente las ordenes de compra segun los filtros.
- El sistema carga la plantilla PDF de ordenes de compra.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todas las ordenes permitidas.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros y el PDF indica que no existen registros.

### Post Condicion
- El sistema muestra las ordenes de compra encontradas.
- El sistema genera el PDF de ordenes de compra cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- orden_compra
- orden_compra_detalle
- proveedores
- usuarios
- sucursales

---

## Informe de Compras

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de compras registradas.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.compras.ver`.
- Existen facturas de compra registradas.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes de Movimientos, Informes de Compras y habilita el informe de compras.

#### Generar
- El sistema valida permiso `reportes.compras.ver`.
- El sistema consulta sucursales para el filtro. Tabla consultada: sucursales.
- El sistema muestra filtros de fecha desde, fecha hasta, estado (Activo, Procesado o Anulado) y sucursal.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.compras.ver`.
- El sistema consulta compras segun los filtros.
- Tablas consultadas: compra_cabecera, compra_detalle, proveedores, usuarios, sucursales.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema envia los filtros al generador del reporte.
- El sistema valida nuevamente permiso `reportes.compras.ver`.
- El sistema consulta nuevamente las compras segun los filtros.
- El sistema carga la plantilla PDF de compras.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todas las compras permitidas.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros y el PDF indica que no existen registros.

### Post Condicion
- El sistema muestra las compras encontradas.
- El sistema genera el PDF de compras cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- compra_cabecera
- compra_detalle
- proveedores
- usuarios
- sucursales

---

## Informe Libro de Compras

### Descripcion Basica
Caso de uso que describe el proceso de generar el informe del libro de compras.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.libro_compras.ver`.
- Existen registros en el libro de compras.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes de Movimientos, Informes de Compras y habilita el informe Libro de Compras.

#### Generar
- El sistema valida permiso `reportes.libro_compras.ver`.
- El sistema consulta sucursales para el filtro. Tabla consultada: sucursales.
- El sistema muestra filtros de fecha desde, fecha hasta, proveedor, estado (Activo o Anulado) y sucursal.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.libro_compras.ver`.
- El sistema consulta el libro de compras segun los filtros.
- Tablas consultadas: libro_compra, sucursales.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema envia los filtros al generador del reporte.
- El sistema valida nuevamente permiso `reportes.libro_compras.ver`.
- El sistema consulta nuevamente el libro de compras segun los filtros.
- El sistema carga la plantilla PDF de libro de compras.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todos los registros permitidos.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros y el PDF indica que no existen registros.

### Post Condicion
- El sistema muestra los registros del libro de compras encontrados.
- El sistema genera el PDF del libro de compras cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- libro_compra
- sucursales

---

## Informe de Stock

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de stock de articulos por filtros.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.stock.ver`.
- Existen articulos y registros de stock.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes de Movimientos, Informes de Compras y habilita el informe de stock.

#### Generar
- El sistema valida permiso `reportes.stock.ver`.
- El sistema consulta sucursales, categorias y proveedores para los filtros.
- Tablas consultadas: sucursales, categorias, proveedores.
- El sistema muestra filtros de sucursal, categoria, proveedor, estado, codigo y stock.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.stock.ver`.
- El sistema consulta articulos y stock segun los filtros.
- Tablas consultadas: articulos, categorias, marcas, articulo_proveedor, proveedores, unidad_medida, tipo_impuesto, stock, sucursales.
- El sistema calcula resumen de total, activos, inactivos, con stock, sin stock y bajo minimo.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema valida nuevamente permiso `reportes.stock.ver`.
- El sistema consulta nuevamente los datos.
- El sistema carga la plantilla PDF de stock.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todo el stock permitido.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros.

### Post Condicion
- El sistema muestra el stock encontrado.
- El sistema genera el PDF cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- articulos
- categorias
- marcas
- articulo_proveedor
- proveedores
- unidad_medida
- tipo_impuesto
- stock
- sucursales

---

## Informe de Movimientos de Stock

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de entradas y salidas de stock.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.movimientos_stock.ver`.
- Existen movimientos de stock registrados.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes de Movimientos, Informes de Compras y habilita el informe de movimientos de stock.

#### Generar
- El sistema valida permiso `reportes.movimientos_stock.ver`.
- El sistema consulta sucursales para el filtro. Tabla consultada: sucursales.
- El sistema muestra filtros de sucursal, tipo de movimiento, signo, fecha desde y fecha hasta.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.movimientos_stock.ver`.
- El sistema consulta movimientos de stock segun los filtros.
- Tablas consultadas: movimientostock, sucursales, articulos, usuarios.
- El sistema calcula resumen de total, entradas y salidas.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema valida nuevamente permiso `reportes.movimientos_stock.ver`.
- El sistema consulta nuevamente los datos.
- El sistema carga la plantilla PDF de movimientos de stock.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todos los movimientos permitidos.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros.

### Post Condicion
- El sistema muestra los movimientos encontrados.
- El sistema genera el PDF cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- movimientostock
- sucursales
- articulos
- usuarios

---

## Informe de Transferencias

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de transferencias entre sucursales.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.transferencias.ver`.
- Existen transferencias registradas.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes de Movimientos, Informes de Compras y habilita el informe de transferencias.

#### Generar
- El sistema valida permiso `reportes.transferencias.ver`.
- El sistema consulta sucursales para el filtro. Tabla consultada: sucursales.
- El sistema muestra filtros de sucursal, estado, tipo, fecha desde y fecha hasta.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.transferencias.ver`.
- El sistema consulta transferencias segun los filtros.
- Tablas consultadas: transferencia_stock, sucursales, nota_remision.
- El sistema calcula resumen de transferencias en transito, recibidas y parciales.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema valida nuevamente permiso `reportes.transferencias.ver`.
- El sistema consulta nuevamente los datos.
- El sistema carga la plantilla PDF de transferencias.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todas las transferencias permitidas.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros.

### Post Condicion
- El sistema muestra las transferencias encontradas.
- El sistema genera el PDF cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- transferencia_stock
- sucursales
- nota_remision

---

## Informe de Recepcion de Servicios

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de recepciones de servicio.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.recepcion_servicio.ver`.
- Existen recepciones de servicio registradas.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes de Movimientos, Informes de Servicios y habilita el informe de recepcion de servicios.

#### Generar
- El sistema valida permiso `reportes.recepcion_servicio.ver`.
- El sistema consulta sucursales para el filtro. Tabla consultada: sucursales.
- El sistema muestra filtros de fecha desde, fecha hasta, estado (Recepcionado, En proceso, Finalizado o Anulado) y sucursal.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.recepcion_servicio.ver`.
- El sistema consulta recepciones de servicio segun los filtros.
- Tablas consultadas: recepcion_servicio, clientes, vehiculos, modelo_auto, marcas, usuarios, sucursales.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema envia los filtros al generador del reporte.
- El sistema valida nuevamente permiso `reportes.recepcion_servicio.ver`.
- El sistema consulta nuevamente las recepciones de servicio segun los filtros.
- El sistema carga la plantilla PDF de recepcion de servicios.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todas las recepciones permitidas.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros y el PDF indica que no existen registros.

### Post Condicion
- El sistema muestra las recepciones de servicio encontradas.
- El sistema genera el PDF de recepciones de servicio cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- recepcion_servicio
- clientes
- vehiculos
- modelo_auto
- marcas
- usuarios
- sucursales

---

## Informe de Presupuesto de Servicios

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de presupuestos de servicio.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.presupuesto_servicio.ver`.
- Existen presupuestos de servicio registrados.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes de Movimientos, Informes de Servicios y habilita el informe de presupuestos de servicio.

#### Generar
- El sistema valida permiso `reportes.presupuesto_servicio.ver`.
- El sistema consulta sucursales para el filtro. Tabla consultada: sucursales.
- El sistema muestra filtros de fecha desde, fecha hasta, estado (Pendiente, Aprobado, OT generada, Facturado o Anulado) y sucursal.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.presupuesto_servicio.ver`.
- El sistema consulta presupuestos de servicio segun los filtros.
- Tablas consultadas: presupuesto_servicio, presupuesto_detalleservicio, diagnostico_servicio, recepcion_servicio, usuarios, sucursales, clientes, vehiculos, modelo_auto, marcas.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema envia los filtros al generador del reporte.
- El sistema valida nuevamente permiso `reportes.presupuesto_servicio.ver`.
- El sistema consulta nuevamente los presupuestos de servicio segun los filtros.
- El sistema carga la plantilla PDF de presupuestos de servicio.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todos los presupuestos permitidos.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros y el PDF indica que no existen registros.

### Post Condicion
- El sistema muestra los presupuestos de servicio encontrados.
- El sistema genera el PDF de presupuestos de servicio cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- presupuesto_servicio
- presupuesto_detalleservicio
- diagnostico_servicio
- recepcion_servicio
- usuarios
- sucursales
- clientes
- vehiculos
- modelo_auto
- marcas

---

## Informe de Ordenes de Trabajo

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de ordenes de trabajo.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.orden_trabajo.ver`.
- Existen ordenes de trabajo registradas.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes de Movimientos, Informes de Servicios y habilita el informe de ordenes de trabajo.

#### Generar
- El sistema valida permiso `reportes.orden_trabajo.ver`.
- El sistema consulta sucursales para el filtro. Tabla consultada: sucursales.
- El sistema muestra filtros de fecha desde, fecha hasta, estado (Activa, Servicio registrado, Pendiente completar o Anulada) y sucursal.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.orden_trabajo.ver`.
- El sistema consulta ordenes de trabajo segun los filtros.
- Tablas consultadas: orden_trabajo, orden_trabajo_detalle, presupuesto_servicio, diagnostico_servicio, recepcion_servicio, sucursales, usuarios, equipo_trabajo, clientes, vehiculos, modelo_auto, marcas.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema envia los filtros al generador del reporte.
- El sistema valida nuevamente permiso `reportes.orden_trabajo.ver`.
- El sistema consulta nuevamente las ordenes de trabajo segun los filtros.
- El sistema carga la plantilla PDF de ordenes de trabajo.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todas las ordenes permitidas.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros y el PDF indica que no existen registros.

### Post Condicion
- El sistema muestra las ordenes de trabajo encontradas.
- El sistema genera el PDF de ordenes de trabajo cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- orden_trabajo
- orden_trabajo_detalle
- presupuesto_servicio
- diagnostico_servicio
- recepcion_servicio
- sucursales
- usuarios
- equipo_trabajo
- clientes
- vehiculos
- modelo_auto
- marcas

---

## Informe de Registro de Servicios

### Descripcion Basica
Caso de uso que describe el proceso de generar informes de servicios registrados.

### Actores Relacionados
- Encargado de Compras
- Personal de Recepcion
- Encargado de dto. de Servicios
- Gerente general

### Pre Condicion
- Conexion a base de datos.
- El usuario accede al sistema mediante logeo.
- El usuario tiene permiso `reportes.registro_servicio.ver`.
- Existen registros de servicio.

### Flujo de Eventos

#### Flujo Basico
Este caso de uso se inicia cuando el usuario selecciona Informes de Movimientos, Informes de Servicios y habilita el informe de registro de servicios.

#### Generar
- El sistema valida permiso `reportes.registro_servicio.ver`.
- El sistema consulta sucursales para el filtro. Tabla consultada: sucursales.
- El sistema consulta empleados activos para el filtro. Tabla consultada: empleados.
- El sistema muestra filtros de fecha desde, fecha hasta, estado (Registrado, Facturado, Con Reclamo o Anulado), tecnico encargado y sucursal.
- El usuario selecciona o ingresa filtros.
- El usuario presiona Previsualizar.
- El sistema valida nuevamente permiso `reportes.registro_servicio.ver`.
- El sistema consulta registros de servicio segun los filtros.
- Tablas consultadas: registro_servicio, registro_servicio_detalle, orden_trabajo, presupuesto_servicio, diagnostico_servicio, recepcion_servicio, sucursales, clientes, vehiculos, modelo_auto, marcas, usuarios, equipo_trabajo, empleados.
- La sucursal del informe se obtiene desde `registro_servicio.id_sucursal`.
- Cliente y vehiculo se obtienen desde la recepcion asociada a la orden de trabajo. Si la orden proviene de un presupuesto, se consulta la recepcion vinculada al diagnostico; si la orden proviene de un reclamo, se consulta la recepcion vinculada al reclamo.
- El sistema diferencia los articulos utilizados por origen del detalle: `OT` para repuestos e `INSUMO` para insumos.
- El sistema calcula cantidad de repuestos, cantidad de insumos e importe total del servicio.
- El sistema carga datos dentro de la grilla.
- El sistema habilita el boton Generar PDF.
- El usuario presiona Generar PDF.
- El sistema envia los filtros al generador del reporte.
- El sistema valida nuevamente permiso `reportes.registro_servicio.ver`.
- El sistema consulta nuevamente los registros de servicio segun los filtros.
- El sistema vuelve a calcular el resumen de servicios, repuestos e insumos para el PDF.
- El sistema carga la plantilla PDF de registro de servicios.
- El sistema genera el PDF del informe.

#### Volver
- El usuario selecciona otra opcion del menu o vuelve a la pantalla anterior.
- El sistema redirecciona a la vista seleccionada.

#### Salir
- El usuario cierra sesion o sale del modulo.
- El sistema finaliza la sesion o vuelve al menu principal.

### Flujo Alternativo
- Si el usuario no tiene permiso, el sistema muestra acceso denegado.
- Si no se ingresan filtros, el sistema consulta todos los registros permitidos.
- Si no existen datos para mostrar, el sistema muestra la grilla sin registros y el PDF indica que no existen registros.

### Post Condicion
- El sistema muestra los registros de servicio encontrados.
- El sistema genera el PDF de registros de servicio cuando corresponde.
- La informacion consultada no se modifica.

### Tablas Involucradas
- registro_servicio
- registro_servicio_detalle
- orden_trabajo
- presupuesto_servicio
- diagnostico_servicio
- recepcion_servicio
- sucursales
- clientes
- vehiculos
- modelo_auto
- marcas
- usuarios
- equipo_trabajo
- empleados
