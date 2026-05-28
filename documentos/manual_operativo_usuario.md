# Manual Operativo de Usuario

Sistema de Gestion Integral de Compras y Servicios

## Presentacion

Este manual describe el uso operativo del sistema, desde el inicio de sesion hasta la ejecucion de los procesos de compras, servicios, mantenimiento, seguridad e informes.

El documento esta orientado al usuario final, pero mantiene un enfoque tecnico-operativo. Por ese motivo, cada modulo se explica indicando su objetivo, ruta de acceso, acciones disponibles, datos principales, validaciones esperadas, restricciones y relacion con otros procesos.

No se documentan claves internas de permisos, nombres de tablas ni campos tecnicos de base de datos. Cuando se mencionan permisos, se describen como acciones funcionales visibles para el usuario, por ejemplo: ver, agregar, modificar, eliminar, anular, aprobar, imprimir o generar documentos.

## Indice operativo

1. Objetivo del manual
2. Inicio del sistema
3. Ventanas y acciones comunes
4. Mantenimiento
5. Seguridad
6. Procesos de compras
7. Procesos de servicios
8. Informes
9. Anulaciones y eliminaciones
10. Buenas practicas
11. Flujos recomendados
12. Soporte y ayuda

## 1. Objetivo del manual

El objetivo del manual es guiar al usuario en la operacion correcta del sistema, explicando que debe cargar, donde debe hacerlo, que validaciones puede encontrar y que impacto tiene cada accion dentro del flujo general.

Esta dirigido a usuarios operativos, administrativos, encargados de compras, personal de taller, recepcionistas, supervisores y administradores del sistema.

## 2. Inicio del sistema

### 2.1 Iniciar sesion

1. Abrir el navegador.
2. Ingresar a la direccion del sistema.
3. En la pantalla de login, completar usuario y contrasena.
4. Presionar el boton de ingreso.
5. Si los datos son correctos, el sistema muestra el Panel Principal.

### 2.2 Cerrar sesion

1. Ir a la opcion de usuario o salida del sistema.
2. Confirmar el cierre de sesion si el sistema lo solicita.
3. El sistema vuelve a la pantalla de login.

### 2.3 Accesos funcionales

El sistema muestra las opciones segun el perfil asignado al usuario. Si una opcion no aparece en el menu o una accion no esta disponible, significa que el perfil del usuario no tiene acceso funcional a esa operacion.

En este manual los accesos se describen en lenguaje operativo:

- Ver: permite ingresar a una pantalla o consultar registros.
- Agregar: permite registrar datos nuevos.
- Modificar: permite actualizar datos existentes.
- Eliminar: permite dar de baja datos referenciales, si el sistema lo permite.
- Anular: permite dejar sin efecto movimientos ya registrados.
- Aprobar: permite autorizar un documento para continuar el flujo.
- Imprimir o generar PDF: permite emitir documentos o reportes.

Los nombres internos usados por el sistema para controlar permisos no se incluyen en el manual de usuario.

## 3. Ventanas y navegacion

### 3.1 Panel Principal

El Panel Principal es la primera pantalla luego del login. Desde aqui el usuario puede acceder al menu lateral y navegar a los modulos habilitados.

### 3.2 Menu lateral

El menu lateral organiza el sistema en las siguientes secciones:

- Compras
- Servicios
- Mantenimiento
- Informes Referenciales
- Informes de Movimientos
- Ayuda

### 3.3 Acciones comunes del sistema

En varias pantallas se repiten acciones similares:

- Nuevo: abre la pantalla para registrar un dato o movimiento.
- Buscar: permite filtrar registros por fecha, estado, numero, cliente, proveedor u otros criterios.
- Lista: muestra registros existentes.
- Guardar: registra la informacion cargada.
- Actualizar: modifica un registro existente.
- Anular: deja sin efecto un movimiento sin eliminarlo fisicamente.
- Eliminar: desactiva o borra logicamente un registro, segun el modulo.
- Imprimir o PDF: genera un documento para visualizar o descargar.
- Limpiar busqueda: borra filtros aplicados.

### 3.4 Estructura tecnica de cada interfaz

Para facilitar la operacion, cada modulo del manual puede describirse con la siguiente estructura:

- Objetivo: indica para que sirve el modulo.
- Ruta: indica donde se encuentra dentro del menu.
- Accesos funcionales: indica que acciones puede realizar el usuario segun su perfil.
- Datos principales: indica los campos o datos relevantes que se cargan.
- Alta o registro: explica como crear un nuevo registro.
- Modificacion: explica como actualizar registros existentes.
- Eliminacion o anulacion: explica como dar de baja o dejar sin efecto.
- Validaciones: indica controles que puede realizar el sistema.
- Relacion con otros modulos: explica donde se utiliza la informacion.
- Errores frecuentes: indica mensajes o situaciones comunes.

## 4. Operacion de mantenimiento

Los modulos de mantenimiento sirven para cargar datos base que luego se usan en compras, servicios y reportes.

### 4.1 Sucursales

Ruta: Mantenimiento > Compras > Sucursales

Uso: registrar y administrar las sucursales de la empresa.

Operacion:

1. Ingresar a Sucursales.
2. Completar los datos requeridos.
3. Presionar Guardar.
4. Para modificar una sucursal, buscarla en la lista y seleccionar la accion de editar.
5. Para eliminar o desactivar, usar la accion correspondiente si el perfil del usuario lo permite.

### 4.2 Articulos

Ruta: Mantenimiento > Compras > Articulos

Uso: registrar productos, repuestos, insumos o servicios que se utilizan en compras, inventario, presupuestos y ordenes.

Operacion:

1. Ingresar a Articulos.
2. Completar informacion basica: codigo, descripcion, categoria, proveedor, tipo de articulo, precios y datos de stock si corresponden.
3. Presionar Guardar.
4. Para actualizar, buscar el articulo y modificar sus datos.
5. Para eliminar, usar la accion de eliminar si el articulo no tiene restricciones operativas.

Recomendacion: antes de cargar movimientos de compra o servicio, verificar que los articulos necesarios esten registrados.

### 4.3 Proveedores

Ruta: Mantenimiento > Compras > Proveedores

Uso: administrar proveedores utilizados en pedidos, presupuestos, ordenes de compra, facturas y remisiones.

Operacion:

1. Ingresar a Proveedores.
2. Completar razon social, documento, contacto y datos requeridos.
3. Presionar Guardar.
4. Para modificar, buscar el proveedor y editarlo.
5. Para eliminar, usar la accion correspondiente si el proveedor no esta bloqueado por movimientos.

### 4.4 Clientes

Ruta: Mantenimiento > Servicios > Clientes

Objetivo: registrar y administrar las personas o entidades que solicitan servicios del taller.

Accesos funcionales:

- Ver clientes registrados.
- Agregar nuevos clientes.
- Modificar datos de clientes existentes.
- Eliminar o desactivar clientes, si el sistema lo permite.
- Buscar clientes por criterios disponibles.

Pantallas relacionadas:

- Agregar cliente.
- Lista de clientes.
- Buscar cliente.
- Actualizar cliente.

Datos principales:

- Tipo de documento.
- Numero de documento.
- Digito verificador, si aplica.
- Nombre.
- Apellido.
- Telefono o celular.
- Correo electronico, si aplica.
- Direccion.
- Ciudad.
- Estado.

Alta de cliente:

1. Ingresar a Mantenimiento > Servicios > Clientes.
2. Seleccionar la opcion Agregar Cliente.
3. Completar los datos obligatorios.
4. Verificar que el documento no corresponda a un cliente ya registrado.
5. Presionar Guardar.
6. El sistema valida la informacion ingresada.
7. Si los datos son correctos, el cliente queda registrado y disponible para otros procesos.

Modificacion:

1. Ingresar a la lista o busqueda de clientes.
2. Localizar el cliente por documento, nombre, apellido u otro criterio disponible.
3. Seleccionar la accion de modificar.
4. Actualizar los datos necesarios.
5. Presionar Guardar o Actualizar.
6. El sistema valida los cambios y actualiza el registro.

Eliminacion o desactivacion:

1. Buscar el cliente.
2. Seleccionar la accion de eliminar, si esta disponible.
3. Confirmar la accion.
4. El sistema verifica si el cliente posee registros relacionados.
5. Si no existen restricciones, el cliente se elimina o queda inactivo segun la logica del sistema.

Validaciones operativas:

- Campos obligatorios sin completar.
- Documento duplicado.
- Formato incorrecto de documento, telefono o correo.
- Longitud invalida de nombres o apellidos.
- Restricciones por movimientos asociados.

Restricciones habituales:

El sistema puede impedir la eliminacion si el cliente esta relacionado con:

- Vehiculos.
- Recepciones de servicio.
- Diagnosticos.
- Presupuestos de trabajo.
- Ordenes de trabajo.
- Registros de servicio.
- Reclamos.

Relacion con otros modulos:

El cliente se utiliza en:

- Vehiculos.
- Recepcion de servicio.
- Diagnostico.
- Presupuesto de trabajo.
- Orden de trabajo.
- Registro de servicio.
- Reclamos.
- Informes de clientes y servicios.

Nota: en Recepcion de Servicio tambien existe carga rapida de clientes para agilizar la atencion.

Recomendaciones:

- Buscar al cliente antes de crearlo para evitar duplicados.
- Registrar correctamente el documento y telefono.
- No eliminar clientes con historial operativo.
- Mantener actualizado el telefono para contacto del taller.
- Usar la carga rapida solo cuando se necesite iniciar una recepcion con rapidez.

Errores frecuentes:

- Cliente ya registrado: el documento ingresado ya existe.
- Datos incompletos: falta completar un campo obligatorio.
- Accion no disponible: el perfil del usuario no tiene acceso funcional para esa operacion.
- No se puede eliminar: el cliente posee movimientos o registros relacionados.

### 4.5 Vehiculos

Ruta: Mantenimiento > Servicios > Vehiculos

Uso: registrar vehiculos asociados a clientes y utilizados en recepcion, diagnostico y servicios.

Operacion:

1. Ingresar a Vehiculos.
2. Seleccionar cliente y modelo.
3. Completar placa, color, ano, version, transmision, motor y tipo de vehiculo.
4. Presionar Guardar.
5. Para modificar, buscar el vehiculo y editar sus datos.

Nota: datos como kilometraje y combustible se cargan en la recepcion del servicio, no en la ficha principal del vehiculo.

### 4.6 Empleados

Ruta: Mantenimiento > Servicios > Empleados

Uso: registrar empleados, tecnicos y personal operativo.

Operacion:

1. Ingresar a Empleados.
2. Completar datos personales, cargo, sucursal y estado.
3. Presionar Guardar.
4. Para actualizar, buscar el empleado y editar.
5. Para eliminar o desactivar, usar la accion correspondiente.

### 4.7 Equipos de trabajo

Ruta: Mantenimiento > Servicios > Equipos

Uso: crear equipos de trabajo y asignar empleados a cada equipo.

Operacion:

1. Ingresar a Equipos.
2. Crear un equipo indicando nombre y sucursal.
3. Guardar.
4. Ingresar a miembros o asignacion.
5. Agregar empleados al equipo.
6. Guardar los cambios.

Uso operativo: los equipos se seleccionan en la Orden de Trabajo.

## 5. Seguridad

### 5.1 Usuarios

Ruta: Mantenimiento > Seguridad > Usuarios

Uso: administrar cuentas de acceso al sistema.

Operacion:

1. Ingresar a Usuarios.
2. Completar datos personales y datos de la cuenta.
3. Asignar rol y sucursal si corresponde.
4. Presionar Guardar.
5. Para modificar, buscar el usuario y actualizar sus datos.
6. Para cambiar estado o accesos funcionales, usar las opciones disponibles segun el rol.

### 5.2 Roles y accesos funcionales

Ruta: Mantenimiento > Seguridad > Roles y Permisos

Uso: definir perfiles de acceso.

Operacion:

1. Crear un rol con nombre y descripcion.
2. Ingresar a la asignacion de accesos.
3. Marcar las acciones funcionales que corresponden al rol.
4. Guardar.

Recomendacion: crear roles por funcion, por ejemplo Administrador, Compras, Recepcion, Taller, Supervisor o Reportes.

## 6. Proceso operativo de compras

El flujo recomendado de compras es:

1. Pedido de compra.
2. Presupuesto de compra.
3. Orden de compra.
4. Factura de compra.
5. Remision si corresponde.
6. Inventario o movimientos de stock.
7. Informes.

### 6.1 Pedidos de compra

Ruta: Compras > Pedidos

Uso: solicitar articulos para reposicion o compra.

Operacion:

1. Ingresar a Pedidos.
2. Buscar y agregar articulos.
3. Indicar cantidad solicitada.
4. Revisar el detalle.
5. Guardar el pedido.
6. Desde la busqueda o lista se puede imprimir el pedido o anularlo si corresponde.

Estados habituales:

- Pendiente: pedido creado y aun no procesado.
- Procesado: pedido utilizado en un proceso posterior.
- Anulado: pedido cancelado.

### 6.2 Presupuestos de compra

Ruta: Compras > Presupuestos

Uso: registrar cotizaciones o presupuestos recibidos de proveedores.

Operacion:

1. Ingresar a Presupuestos.
2. Seleccionar proveedor o pedido de referencia si aplica.
3. Agregar articulos y cantidades.
4. Cargar precios y totales.
5. Guardar.
6. Buscar el presupuesto para revisarlo, anularlo o generar una orden de compra segun las acciones habilitadas para el usuario.

### 6.3 Ordenes de compra

Ruta: Compras > Ordenes de Compra

Uso: formalizar la compra autorizada a un proveedor.

Operacion:

1. Ingresar a Ordenes de Compra.
2. Crear una orden desde presupuesto, pedido o carga directa segun la pantalla habilitada.
3. Seleccionar proveedor, fecha de entrega y articulos.
4. Guardar.
5. Imprimir la orden si corresponde.
6. Anular solo si la orden aun no fue procesada.

### 6.4 Ingreso de facturas

Ruta: Compras > Ingreso de Facturas

Uso: registrar facturas de compra recibidas.

Operacion:

1. Ingresar a Ingreso de Facturas.
2. Seleccionar proveedor.
3. Cargar datos de factura: numero, fecha, timbrado, condicion y totales.
4. Agregar articulos o vincular documentos previos si corresponde.
5. Guardar.
6. Buscar la factura para consultar o anular si esta permitido.

### 6.5 Remisiones

Ruta: Compras > Remisiones

Uso: registrar documentos de traslado o remision.

Operacion:

1. Ingresar a Remisiones.
2. Completar datos de la remision.
3. Cargar datos del transportista y vehiculo.
4. Agregar productos.
5. Guardar.
6. Buscar remisiones para consultar o imprimir.

### 6.6 Notas de credito y debito

Ruta: Compras > Notas de Credito y Debito

Uso: registrar ajustes sobre facturas.

Operacion:

1. Ingresar a Notas de Credito y Debito.
2. Buscar la factura relacionada.
3. Seleccionar tipo de nota.
4. Cargar motivo, importes y detalle.
5. Guardar.
6. Consultar o anular desde la busqueda si corresponde.

### 6.7 Transferencias

Ruta: Compras > Transferencias

Uso: mover productos entre sucursales.

Operacion para crear:

1. Ingresar a Transferencias.
2. Seleccionar sucursal origen y destino.
3. Buscar productos.
4. Indicar cantidades.
5. Revisar detalle.
6. Guardar la transferencia.

Operacion para recibir:

1. Ingresar a la opcion de recibir transferencia.
2. Seleccionar la transferencia pendiente.
3. Revisar productos y cantidades.
4. Confirmar recepcion.

### 6.8 Inventario

Ruta: Compras > Inventarios

Uso: registrar controles y ajustes de stock.

Operacion:

1. Ingresar a Inventarios.
2. Seleccionar sucursal y criterios.
3. Cargar articulos y cantidades contadas.
4. Guardar el inventario.
5. Buscar inventarios anteriores para consultar o anular si corresponde.

## 7. Proceso operativo de servicios

El flujo recomendado del taller es:

1. Recepcion de servicio.
2. Diagnostico.
3. Presupuesto de trabajo.
4. Aprobacion del presupuesto.
5. Orden de trabajo.
6. Registro de servicio.
7. Reclamo si aplica.
8. Informes.

### 7.1 Recepcion de servicio

Ruta: Servicios > Solicitud de Servicios

Uso: registrar el ingreso del vehiculo al taller y la solicitud del cliente.

Operacion:

1. Ingresar a Solicitud de Servicios.
2. Seleccionar cliente y vehiculo.
3. Si el cliente o vehiculo no existe, usar la carga rapida disponible en la pantalla.
4. Completar datos de recepcion: motivo, descripcion del cliente, kilometraje, combustible, accesorios y observaciones.
5. Guardar.
6. Buscar recepciones para consultar, continuar el proceso o anular si corresponde.

Recomendacion: registrar de forma clara el problema indicado por el cliente, porque esa informacion se utiliza luego en diagnostico.

### 7.2 Diagnostico

Ruta: Servicios > Diagnostico

Uso: registrar la revision tecnica del vehiculo.

Operacion:

1. Ingresar a Diagnostico.
2. Seleccionar una recepcion pendiente.
3. Revisar los datos del cliente, vehiculo y solicitud.
4. Completar diagnostico general.
5. Cargar el checklist tecnico por sistema o area revisada.
6. Indicar hallazgo, gravedad, solucion recomendada y si requiere repuesto o mano de obra.
7. Guardar.

Despues de guardar:

- Si el diagnostico requiere cobro, se genera presupuesto.
- Si corresponde a reclamo valido en garantia, puede habilitar flujo de reclamo.

### 7.3 Presupuesto de trabajo

Ruta: Servicios > Presupuesto de Trabajo

Uso: preparar el presupuesto para el cliente a partir del diagnostico o de una carga preliminar.

Operacion:

1. Ingresar a Presupuesto de Trabajo.
2. Seleccionar diagnostico o cliente/vehiculo segun el origen.
3. Agregar servicios, repuestos o productos.
4. Revisar cantidades, precios, promociones o descuentos.
5. Guardar el presupuesto.
6. Desde la busqueda, imprimir PDF, aprobar, anular o generar la orden de trabajo segun las acciones habilitadas para el usuario.

Estados habituales:

- Pendiente: presupuesto creado.
- Aprobado: autorizado por el cliente.
- OT generada: ya posee orden de trabajo.
- Facturado: proceso comercial cerrado.
- Anulado: sin efecto.

### 7.4 Orden de trabajo

Ruta: Servicios > Ordenes de Trabajo

Uso: convertir un presupuesto aprobado en una orden operativa para el taller.

Operacion:

1. Ingresar a Ordenes de Trabajo.
2. Buscar un presupuesto aprobado.
3. Seleccionarlo.
4. Revisar cliente, vehiculo, presupuesto y trabajos/repuestos autorizados.
5. Asignar equipo encargado.
6. Asignar tecnico responsable.
7. Agregar instrucciones internas si corresponde.
8. Generar Orden de Trabajo.
9. Desde la busqueda se puede imprimir la OT o anularla si aun no fue registrada.

Nota: la orden de trabajo no es un presupuesto; es una instruccion operativa para ejecucion del taller.

### 7.5 Registro de servicio

Ruta: Servicios > Registro de Servicios

Uso: registrar la ejecucion final del trabajo realizado.

Operacion:

1. Ingresar a Registro de Servicios.
2. Seleccionar una orden de trabajo activa.
3. Verificar datos de la orden, cliente, vehiculo y detalle.
4. Registrar insumos utilizados si corresponde.
5. Confirmar la ejecucion del servicio.
6. Guardar.
7. Buscar registros para consultar, imprimir o revisar historial.

### 7.6 Reclamos

Ruta: Servicios > Reclamos

Uso: registrar reclamos asociados a servicios realizados.

Operacion:

1. Ingresar a Reclamos.
2. Seleccionar un servicio realizado.
3. Cargar tipo de reclamo, prioridad y descripcion.
4. Guardar.
5. El reclamo debe ser evaluado mediante diagnostico.
6. Segun el resultado, puede generar una orden de trabajo por garantia o requerir presupuesto.

### 7.7 Promociones

Ruta: Servicios > Promociones

Uso: crear promociones aplicables a productos o servicios.

Operacion:

1. Ingresar a Promociones.
2. Cargar nombre, vigencia, condiciones y articulos asociados.
3. Guardar.
4. Desde la lista se puede modificar o desactivar segun las acciones habilitadas para el usuario.

### 7.8 Descuentos

Ruta: Servicios > Descuentos

Uso: administrar descuentos que pueden aplicarse en presupuestos de servicio.

Operacion:

1. Ingresar a Descuentos.
2. Definir tipo, valor, vigencia y condiciones.
3. Guardar.
4. Consultar o editar desde la lista.

### 7.9 Reglas comerciales

Ruta: Servicios > Reglas Comerciales

Uso: configurar condiciones comerciales para aplicar descuentos o beneficios.

Operacion:

1. Ingresar a Reglas Comerciales.
2. Cargar datos de la regla.
3. Definir condiciones.
4. Asociar descuentos aplicables.
5. Guardar.

## 8. Informes

Los informes permiten consultar informacion y exportarla a PDF.

Operacion general:

1. Ingresar al informe deseado.
2. Completar filtros disponibles.
3. Presionar Previsualizar o Buscar.
4. Revisar la tabla en pantalla.
5. Presionar Generar PDF si se requiere el documento.

### 8.1 Informes referenciales

Compras:

- Articulos.
- Proveedores.
- Sucursales.

Servicios:

- Clientes.
- Vehiculos.
- Empleados.

### 8.2 Informes de movimientos de compras

- Informe de Pedidos.
- Informe de Presupuestos.
- Informe de Ordenes de Compra.
- Informe de Compras.
- Informe Libro de Compras.
- Informe de Stock.
- Movimientos de Stock.

### 8.3 Informes de movimientos de servicios

- Informe de Recepcion de Servicios.
- Informe de Presupuestos de Servicio.
- Informe de Ordenes de Trabajo.
- Informe de Registro de Servicios.

## 9. Anulaciones y eliminaciones

### 9.1 Diferencia entre eliminar y anular

Eliminar se utiliza normalmente en datos referenciales o mantenimientos. En muchos casos el sistema realiza una baja logica para mantener historial.

Anular se utiliza en movimientos. Un movimiento anulado queda registrado pero sin efecto operativo.

### 9.2 Recomendaciones

- Anular solo cuando el movimiento fue cargado por error o ya no corresponde.
- No anular documentos que ya fueron utilizados por procesos posteriores.
- Verificar que el usuario tenga acceso funcional antes de solicitar anulaciones.
- Consultar con un supervisor si el documento ya afecta stock, presupuesto, orden de trabajo o registro de servicio.

## 10. Buenas practicas de uso

- Cargar datos completos y claros.
- Evitar duplicar clientes, vehiculos, proveedores o articulos.
- Usar busquedas antes de crear nuevos registros.
- Revisar los datos antes de guardar.
- Usar observaciones para aclarar situaciones especiales.
- Imprimir o descargar PDF solo cuando el documento este revisado.
- Mantener actualizados los roles y accesos funcionales de usuarios.
- Cerrar sesion al terminar.

## 11. Flujo rapido recomendado

### 11.1 Compra de productos

1. Registrar proveedor si no existe.
2. Registrar articulos si no existen.
3. Crear pedido de compra.
4. Registrar presupuesto de compra.
5. Generar orden de compra.
6. Registrar factura.
7. Actualizar stock mediante el proceso correspondiente.
8. Consultar informes.

### 11.2 Servicio de taller

1. Registrar cliente y vehiculo o usar carga rapida en recepcion.
2. Crear recepcion de servicio.
3. Registrar diagnostico.
4. Crear presupuesto de trabajo.
5. Aprobar presupuesto.
6. Generar orden de trabajo.
7. Registrar servicio ejecutado.
8. Consultar informes o gestionar reclamo si corresponde.

## 12. Soporte y ayuda

Si el usuario no puede acceder a una pantalla, debe verificar:

- Si inicio sesion correctamente.
- Si su usuario esta activo.
- Si tiene el rol adecuado.
- Si cuenta con acceso funcional para la accion.
- Si los datos obligatorios fueron completados.

Para errores del sistema, se recomienda informar:

- Usuario que realizo la operacion.
- Fecha y hora aproximada.
- Pantalla utilizada.
- Accion realizada.
- Mensaje de error mostrado.
- Numero de documento o registro relacionado, si existe.
