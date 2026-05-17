# Especificaciones de Casos de Uso - Referenciales

## 1. Sucursales

* Nombre de Caso de Uso
Registrar, Editar y Eliminar Sucursales.

* Descripcion Basica
Este caso permite administrar las sucursales de la empresa. El sistema permite registrar una nueva sucursal, listar sucursales, buscar por descripcion, editar sus datos y eliminar o desactivar una sucursal segun su uso en el sistema.

* Actores relacionados
Administrador o Usuario autorizado.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe contar con permiso para creacion.
El usuario debe contar con permiso para edicion.
El usuario debe contar con permiso para eliminacion.
Debe existir una empresa registrada para asociar la sucursal.
Para editar o eliminar, debe existir una sucursal registrada.

* Flujo de eventos
Flujo Basico:
El usuario accede al sistema a traves de un logueo.
El usuario ingresa al modulo Sucursales.
El sistema muestra el formulario de registro y el listado de sucursales.
El sistema carga las empresas disponibles.
Tabla consultada para empresas: empresa.

Nuevo
El usuario selecciona una empresa.
El usuario ingresa descripcion de la sucursal.
El usuario ingresa numero de establecimiento.
El usuario ingresa direccion.
El usuario ingresa telefono.
El usuario selecciona estado Activo o Inactivo.

Guardar
El usuario presiona Guardar.
El sistema valida permiso de creacion.
El sistema valida datos obligatorios: empresa, descripcion y estado.
El sistema registra la sucursal. Tabla insertada: sucursales.
El sistema emite mensaje de sucursal registrada correctamente.

Buscar/Listar
El usuario ingresa un criterio de busqueda por descripcion de sucursal.
El usuario presiona Buscar.
El sistema consulta las sucursales registradas.
Tablas consultadas para listar sucursales: sucursales, empresa.
El sistema muestra sucursal, empresa, numero de establecimiento y estado.
Si el usuario tiene permiso de edicion, el sistema muestra la opcion Editar.
Si el usuario tiene permiso de eliminacion, el sistema muestra la opcion Eliminar.

Editar
El usuario presiona Editar.
El sistema consulta la sucursal seleccionada. Tabla consultada: sucursales.
El sistema carga los datos en el formulario y marca la empresa cuyo ID coincide con la sucursal.
El usuario modifica empresa, descripcion, numero de establecimiento, direccion, telefono o estado.
El usuario presiona Guardar.
El sistema valida permiso de edicion.
El sistema valida que la sucursal exista. Tabla consultada: sucursales.
El sistema edita los datos de la sucursal. Tabla modificada: sucursales.
El sistema emite mensaje de sucursal editada correctamente.

Eliminar
El usuario presiona Eliminar.
El sistema emite mensaje de confirmacion.
El usuario confirma la accion.
El sistema valida permiso de eliminacion.
El sistema valida que la sucursal exista. Tabla consultada: sucursales.
El sistema verifica si la sucursal esta asociada a usuarios. Tabla consultada: usuarios.
Si la sucursal esta asociada a usuarios, el sistema la desactiva. Tabla modificada: sucursales.
Si la sucursal no esta asociada a usuarios, el sistema la elimina. Tabla eliminada: sucursales.
El sistema emite mensaje segun corresponda.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan empresa, descripcion o estado, el sistema muestra campos obligatorios incompletos.
Si la sucursal no existe al editar o eliminar, el sistema muestra error.
Si no se puede registrar, editar o eliminar, el sistema muestra error.

* Post Condicion
La sucursal queda registrada en sucursales.
Si se edita, los datos quedan modificados.
Si se elimina y no tiene relacion con usuarios, la sucursal se elimina.
Si tiene relacion con usuarios, la sucursal queda inactiva.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| sucursales | sucursales | Bd_reduc |
| empresa | empresa | Bd_reduc |
| usuarios | usuarios | Bd_reduc |

## 2. Articulos

* Nombre de Caso de Uso
Registrar, Editar y Eliminar Articulos.

* Descripcion Basica
Este caso permite administrar articulos del sistema, incluyendo servicios, productos e insumos. El sistema permite cargar datos comerciales, categoria, unidad de medida, IVA, marca, precio de venta, codigo y tipo de articulo.

* Actores relacionados
Administrador o Usuario autorizado.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe contar con permiso para creacion.
El usuario debe contar con permiso para edicion.
El usuario debe contar con permiso para eliminacion.
Deben existir categorias, unidades de medida, tipos de IVA y marcas disponibles.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Articulos.
El sistema muestra el formulario de registro y el listado de articulos.
El sistema carga categorias, unidades de medida, IVA y marcas.
Tablas consultadas: categorias, unidad_medida, tipo_impuesto, marcas.

Nuevo
El usuario ingresa codigo, descripcion, precio de venta, categoria, unidad de medida, IVA, marca y tipo de articulo.

Guardar
El usuario presiona Guardar.
El sistema valida permiso de creacion.
El sistema valida campos obligatorios y formatos.
El sistema valida que el codigo no este duplicado. Tabla consultada: articulos.
El sistema registra el articulo. Tabla insertada: articulos.
El sistema emite mensaje de articulo registrado correctamente.

Buscar/Listar
El usuario ingresa busqueda por codigo, descripcion o identificador.
El sistema consulta los articulos.
Tabla consultada para listar articulos: articulos.
El sistema muestra codigo, nombre y precio de venta.
Si el usuario tiene permiso de edicion, muestra Editar.
Si el usuario tiene permiso de eliminacion, muestra Eliminar.

Editar
El usuario ingresa busqueda por codigo, descripcion o identificador.
El sistema consulta los articulos.
Tabla consultada para listar articulos: articulos.
El sistema muestra codigo, descripcion y precio de venta.
Si el usuario tiene permiso de edicion, muestra Editar.
Si el usuario tiene permiso de eliminacion, muestra Eliminar.
El usuario selecciona registro y presiona Editar.
El sistema consulta el articulo seleccionado. Tabla consultada: articulos.
El sistema carga los datos en el formulario y marca las opciones referenciales cuyo ID coincide con el articulo.
El usuario modifica codigo, descripcion, precio de venta, categoria, unidad de medida, IVA, marca, tipo o estado.
El usuario presiona Guardar.
El sistema valida permiso de edicion.
El sistema valida existencia del articulo. Tabla consultada: articulos.
El sistema valida formatos, campos de seleccion, estado y duplicado de codigo.
El sistema edita el articulo. Tabla modificada: articulos.
El sistema emite mensaje de articulo editado correctamente.

Eliminar
El usuario presiona Eliminar.
El sistema emite mensaje de confirmacion.
El usuario confirma la accion.
El sistema valida permiso de eliminacion.
El sistema valida que el articulo exista. Tabla consultada: articulos.
El sistema verifica si el articulo tiene movimientos asociados. Tabla consultada: movimientostock.
Si el articulo tiene movimientos asociados, el sistema lo desactiva.
Si no tiene movimientos asociados, el sistema lo elimina.
Tabla modificada o eliminada: articulos.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan campos obligatorios, el sistema muestra error.
Si el codigo ya existe, el sistema no permite registrar o editar.
Si los formatos no son validos, el sistema muestra error.
Si el articulo no existe, el sistema muestra error.

* Post Condicion
El articulo queda registrado en articulos.
Si se edita, sus datos quedan modificados.
Si se elimina con movimientos, queda inactivo.
Si se elimina sin movimientos, se elimina del sistema.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| articulos | articulos | Bd_reduc |
| categorias | categorias | Bd_reduc |
| unidad_medida | unidad_medida | Bd_reduc |
| tipo_impuesto | tipo_impuesto | Bd_reduc |
| marcas | marcas | Bd_reduc |
| movimientostock | movimientostock | Bd_reduc |

## 3. Proveedores

* Nombre de Caso de Uso
Registrar, Editar y Eliminar Proveedores.

* Descripcion Basica
Este caso permite administrar proveedores. El sistema permite registrar razon social, RUC, telefono, direccion, correo, ciudad y estado.

* Actores relacionados
Administrador o Usuario autorizado.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe contar con permiso para creacion.
El usuario debe contar con permiso para edicion.
El usuario debe contar con permiso para eliminacion.
Debe existir ciudad registrada.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Proveedores.
El sistema carga ciudades disponibles. Tabla consultada: ciudades.

Nuevo
El usuario ingresa razon social, RUC, telefono, direccion, correo, ciudad y estado.

Guardar
El sistema valida permiso de creacion.
El sistema valida campos obligatorios: razon social, RUC, ciudad y estado.
El sistema valida formato de correo si fue ingresado.
El sistema valida que el RUC no este duplicado. Tabla consultada: proveedores.
El sistema registra el proveedor. Tabla insertada: proveedores.
El sistema emite mensaje de proveedor registrado correctamente.

Buscar/Listar
El usuario busca proveedor por razon social o RUC.
El sistema consulta proveedores.
Tablas consultadas para listar proveedores: proveedores, ciudades.
El sistema muestra razon social, RUC, ciudad y estado.
Si el usuario tiene permiso de edicion, muestra Editar.
Si el usuario tiene permiso de eliminacion, muestra Eliminar.

Editar
El usuario presiona Editar.
El sistema consulta el proveedor seleccionado. Tabla consultada: proveedores.
El sistema carga los datos en el formulario y marca la ciudad cuyo ID coincide con el proveedor.
El sistema valida permiso de edicion.
El sistema valida existencia, campos obligatorios, correo, estado y duplicado de RUC.
El sistema edita el proveedor. Tabla modificada: proveedores.
El sistema emite mensaje de proveedor editado correctamente.

Eliminar
El usuario presiona Eliminar.
El sistema valida permiso de eliminacion.
El sistema valida que el proveedor exista. Tabla consultada: proveedores.
El sistema verifica si el proveedor tiene articulos asociados. Tabla consultada: articulo_proveedor.
Si el proveedor tiene articulos asociados, el sistema lo desactiva.
Si no tiene articulos asociados, el sistema lo elimina.
Tabla modificada o eliminada: proveedores.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan campos obligatorios, el sistema muestra error.
Si el correo no es valido, el sistema muestra error.
Si el RUC ya existe, el sistema no permite registrar o editar.
Si el proveedor no existe, el sistema muestra error.

* Post Condicion
El proveedor queda registrado en proveedores.
Si se edita, sus datos quedan modificados.
Si se elimina con articulos asociados, queda inactivo.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| proveedores | proveedores | Bd_reduc |
| ciudades | ciudades | Bd_reduc |
| articulo_proveedor | articulo_proveedor | Bd_reduc |

## 4. Clientes

* Nombre de Caso de Uso
Registrar, Editar y Eliminar Clientes.

* Descripcion Basica
Este caso permite administrar clientes, registrando documento, tipo de documento, digito verificador, nombre, apellido, telefono, correo, direccion, ciudad y estado civil.

* Actores relacionados
Administrador, Recepcionista o Usuario autorizado.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe contar con permiso para creacion.
El usuario debe contar con permiso para edicion.
El usuario debe contar con permiso para eliminacion.
Debe existir ciudad registrada.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Clientes.
El sistema carga ciudades disponibles. Tabla consultada: ciudades.

Nuevo
El usuario ingresa documento, tipo de documento, digito verificador, nombre, apellido, telefono, correo, direccion, ciudad y estado civil.

Guardar
El sistema valida permiso de creacion.
El sistema valida campos obligatorios: documento, nombre, direccion y ciudad.
El sistema valida documento duplicado. Tabla consultada: clientes.
El sistema registra el cliente. Tabla insertada: clientes.
El sistema emite mensaje de cliente registrado correctamente.

Buscar/Listar
El usuario busca por documento, nombre o apellido.
El sistema consulta clientes. Tabla consultada: clientes.
El sistema muestra documento, cliente, telefono y direccion.
Si el usuario tiene permiso de edicion, muestra Editar.
Si el usuario tiene permiso de eliminacion, muestra Eliminar.

Editar
El usuario presiona Editar.
El sistema consulta el cliente seleccionado. Tabla consultada: clientes.
El sistema carga los datos y marca la ciudad cuyo ID coincide con el cliente.
El sistema valida permiso de edicion.
El sistema valida existencia, campos obligatorios, ciudad, estado y duplicado de documento.
El sistema edita el cliente. Tabla modificada: clientes.
El sistema emite mensaje de cliente editado correctamente.

Eliminar
El usuario presiona Eliminar.
El sistema valida permiso de eliminacion.
El sistema valida que el cliente exista. Tabla consultada: clientes.
El sistema verifica si el cliente tiene vehiculos asociados. Tabla consultada: vehiculos.
Si el cliente tiene vehiculos asociados, el sistema lo desactiva.
Si no tiene vehiculos asociados, el sistema lo elimina.
Tabla modificada o eliminada: clientes.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan datos obligatorios, el sistema muestra error.
Si el documento ya existe, el sistema no permite registrar o editar.
Si el cliente no existe, el sistema muestra error.

* Post Condicion
El cliente queda registrado en clientes.
Si se edita, sus datos quedan modificados.
Si se elimina con vehiculos asociados, queda inactivo.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| clientes | clientes | Bd_reduc |
| ciudades | ciudades | Bd_reduc |
| vehiculos | vehiculos | Bd_reduc |

## 5. Vehiculos

* Nombre de Caso de Uso
Registrar, Editar y Eliminar Vehiculos.

* Descripcion Basica
Este caso permite administrar vehiculos asociados a clientes. El sistema permite registrar cliente, modelo, color, placa, anho, numero de serie y estado.

* Actores relacionados
Administrador, Recepcionista o Usuario autorizado.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe contar con permiso para creacion.
El usuario debe contar con permiso para edicion.
El usuario debe contar con permiso para eliminacion.
Deben existir cliente y modelo registrados.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Vehiculos.
El sistema carga modelos activos para el selector. Tabla consultada: modelo_auto.
El sistema prepara la busqueda de clientes por documento, nombre o apellido. Tabla consultada: clientes.

Nuevo
El usuario busca el cliente por documento, nombre o apellido.
El sistema muestra clientes activos que coinciden con la busqueda. Tabla consultada: clientes.
El usuario selecciona el cliente.
El usuario selecciona el modelo desde la lista cargada. Tabla consultada: modelo_auto.
El usuario ingresa color, placa, anho, numero de serie y estado.
Si el usuario presiona Cancelar, el sistema limpia los campos del formulario sin consultar tablas.

Guardar
El sistema valida permiso de creacion.
El sistema valida campos obligatorios: cliente, modelo, color y placa.
El sistema valida estado, cliente existente, modelo existente y placa no duplicada.
El sistema registra el vehiculo. Tabla insertada: vehiculos.
El sistema emite mensaje de vehiculo registrado correctamente.

Buscar/Listar
El usuario busca por placa o cliente.
El sistema consulta vehiculos y une los datos del cliente y modelo.
Tablas consultadas: vehiculos, clientes, modelo_auto.
El sistema muestra placa, cliente, modelo, color y estado.
Si el usuario tiene permiso de edicion, muestra Editar.
Si el usuario tiene permiso de eliminacion, muestra Eliminar.

Editar
El usuario presiona Editar.
El sistema consulta el vehiculo seleccionado con su cliente asociado. Tablas consultadas: vehiculos, clientes.
El sistema carga modelos activos para el selector. Tabla consultada: modelo_auto.
El sistema carga los datos en el formulario y marca el cliente y modelo cuyos IDs coinciden con el vehiculo.
El usuario modifica cliente, modelo, color, placa, anho, numero de serie o estado.
El sistema valida permiso de edicion.
El sistema valida existencia del vehiculo, campos obligatorios, estado, cliente existente, modelo existente y placa no duplicada.
El sistema edita el vehiculo. Tabla modificada: vehiculos.
El sistema emite mensaje de vehiculo editado correctamente.

Eliminar
El usuario presiona Eliminar.
El sistema valida permiso de eliminacion.
El sistema valida que el vehiculo exista. Tabla consultada: vehiculos.
El sistema verifica si el vehiculo tiene recepciones de servicio asociadas. Tabla consultada: recepcion_servicio.
Si el vehiculo tiene recepciones asociadas, el sistema lo desactiva.
Si no tiene recepciones asociadas, el sistema lo elimina.
Tabla modificada o eliminada: vehiculos.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan datos obligatorios, el sistema muestra error.
Si el cliente o modelo no es valido, el sistema muestra error.
Si la placa ya existe, el sistema muestra error.
Si el vehiculo no existe, el sistema muestra error.

* Post Condicion
El vehiculo queda registrado en vehiculos.
Si se edita, sus datos quedan modificados.
Si se elimina con recepciones de servicio asociadas, queda inactivo.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| vehiculos | vehiculos | Bd_reduc |
| clientes | clientes | Bd_reduc |
| modelo_auto | modelo_auto | Bd_reduc |
| recepcion_servicio | recepcion_servicio | Bd_reduc |

## 6. Empleados

* Nombre de Caso de Uso
Registrar, Editar y Eliminar Empleados.

* Descripcion Basica
Este caso permite administrar empleados, asociandolos a cargo y sucursal.

* Actores relacionados
Administrador o Usuario autorizado.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe contar con permiso para creacion.
El usuario debe contar con permiso para edicion.
El usuario debe contar con permiso para eliminacion.
Deben existir cargo y sucursal registrados.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Empleados.
El sistema carga cargos activos para el selector. Tabla consultada: cargos.
El sistema carga sucursales activas para el selector. Tabla consultada: sucursales.

Nuevo
El usuario selecciona cargo desde la lista cargada. Tabla consultada: cargos.
El usuario selecciona sucursal desde la lista cargada. Tabla consultada: sucursales.
El usuario ingresa nombre, apellido, direccion, celular, cedula y estado civil.
Si el usuario presiona Cancelar, el sistema limpia los campos del formulario sin consultar tablas.

Guardar
El sistema valida permiso de creacion.
El sistema valida campos obligatorios: cargo, sucursal, nombre, apellido y cedula.
El sistema valida cargo y sucursal existentes.
El sistema valida cedula duplicada. Tabla consultada: empleados.
El sistema registra el empleado. Tabla insertada: empleados.
El sistema emite mensaje de empleado registrado correctamente.

Buscar/Listar
El usuario busca por nombre, apellido o cedula.
El sistema consulta empleados y une los datos de cargo y sucursal.
Tablas consultadas: empleados, cargos, sucursales.
El sistema muestra empleado, cargo y sucursal.
Si el usuario tiene permiso de edicion, muestra Editar.
Si el usuario tiene permiso de eliminacion, muestra Eliminar.

Editar
El usuario presiona Editar.
El sistema consulta el empleado seleccionado. Tabla consultada: empleados.
El sistema carga cargos activos y sucursales activas para los selectores. Tablas consultadas: cargos, sucursales.
El sistema carga los datos en el formulario y marca cargo y sucursal cuyos IDs coinciden con el empleado.
El usuario modifica cargo, sucursal, nombre, apellido, direccion, celular, cedula, estado civil o estado.
El sistema valida permiso de edicion.
El sistema valida existencia del empleado, campos obligatorios, estado, cargo existente, sucursal existente y cedula no duplicada.
El sistema edita el empleado. Tabla modificada: empleados.
El sistema emite mensaje de empleado editado correctamente.

Eliminar
El usuario presiona Eliminar.
El sistema valida permiso de eliminacion.
El sistema valida que el empleado exista. Tabla consultada: empleados.
El sistema verifica si el empleado tiene ordenes de trabajo asociadas como tecnico responsable. Tabla consultada: orden_trabajo.
Si el empleado tiene ordenes de trabajo asociadas, el sistema lo desactiva.
Si no tiene ordenes de trabajo asociadas, el sistema lo elimina.
Tabla modificada o eliminada: empleados.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan campos obligatorios, el sistema muestra error.
Si el cargo o sucursal no es valido, el sistema muestra error.
Si la cedula ya existe, el sistema no permite registrar.
Si el empleado no existe, el sistema muestra error.

* Post Condicion
El empleado queda registrado en empleados.
Si se edita, sus datos quedan modificados.
Si se elimina con ordenes de trabajo asociadas, queda inactivo.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| empleados | empleados | Bd_reduc |
| cargos | cargos | Bd_reduc |
| sucursales | sucursales | Bd_reduc |
| orden_trabajo | orden_trabajo | Bd_reduc |

## 7. Equipos de Trabajo

* Nombre de Caso de Uso
Registrar, Editar, Eliminar y Asignar Empleados a Equipos de Trabajo.

* Descripcion Basica
Este caso permite administrar equipos de trabajo por sucursal. El sistema permite crear equipos, editar sus datos, eliminar o inactivar equipos, ver miembros y asignar o quitar empleados.

* Actores relacionados
Administrador o Encargado de personal.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe contar con permiso equipo.crear para creacion de equipos.
El usuario debe contar con permiso equipo.editar para edicion, listado, miembros y asignacion de equipos.
El usuario debe contar con permiso equipo.eliminar para eliminacion de equipos.
Deben existir sucursales y empleados activos.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Equipos de Trabajo.
El sistema muestra la opcion Equipos si el usuario tiene permiso equipo.crear o equipo.editar.
El sistema muestra la opcion Asignar Empleados si el usuario tiene permiso equipo.editar.
El sistema carga sucursales activas para el selector. Tabla consultada: sucursales.
Si el usuario tiene permiso equipo.editar, el sistema consulta equipos activos para el listado. Tablas consultadas: equipo_trabajo, sucursales.

Nuevo
El usuario selecciona sucursal desde la lista cargada. Tabla consultada: sucursales.
El usuario ingresa nombre del equipo y descripcion.
Si el usuario presiona Cancelar, el sistema limpia los campos del formulario sin consultar tablas.

Guardar
El sistema valida permiso equipo.crear.
El sistema valida sucursal y nombre.
El sistema valida sucursal existente y que no exista otro equipo activo con el mismo nombre en la sucursal.
El sistema registra el equipo. Tabla insertada: equipo_trabajo.
El sistema emite mensaje de equipo creado correctamente.

Buscar/Listar
El sistema valida permiso equipo.editar.
El sistema consulta equipos activos y une la descripcion de la sucursal.
Tablas consultadas: equipo_trabajo, sucursales.
El sistema muestra equipo, sucursal y estado.
Si el usuario tiene permiso equipo.editar, muestra Editar y permite ver miembros del equipo.
Si el usuario tiene permiso equipo.eliminar, muestra Eliminar.

Editar
El usuario presiona Editar.
El sistema abre la vista de edicion con el ID del equipo.
El sistema consulta el equipo seleccionado. Tabla consultada: equipo_trabajo.
El sistema carga sucursales activas para el selector. Tabla consultada: sucursales.
El sistema carga sucursal, nombre y descripcion, y marca la sucursal cuyo ID coincide con el equipo.
El usuario modifica sucursal, nombre o descripcion.
El sistema valida permiso equipo.editar.
El sistema valida que el equipo exista. Tabla consultada: equipo_trabajo.
El sistema valida sucursal existente y que no exista otro equipo activo con el mismo nombre en la sucursal.
El sistema edita el equipo. Tabla modificada: equipo_trabajo.
El sistema emite mensaje de equipo editado correctamente.


Asignar Empleados
El usuario ingresa a Asignar Empleados.
El sistema valida permiso equipo.editar.
El sistema consulta equipos activos con su sucursal. Tablas consultadas: equipo_trabajo, sucursales.
El sistema muestra el selector de equipo, el buscador de empleados, la seccion de empleados disponibles y la seccion de miembros actuales.
El sistema mantiene deshabilitado el buscador y el boton Asignar Seleccionados hasta que el usuario seleccione un equipo.

El usuario selecciona un equipo.
El sistema consulta que el equipo exista y este activo. Tabla consultada: equipo_trabajo.
El sistema consulta empleados activos de la sucursal del equipo. Tabla consultada: empleados.
El sistema consulta relaciones activas de empleados con equipos para identificar miembros actuales y empleados con otros equipos. Tablas consultadas: equipo_empleado, equipo_trabajo.
El sistema separa los empleados en Disponibles y Miembros actuales.
El sistema muestra la cantidad de empleados disponibles y miembros actuales.
El sistema habilita el buscador de empleados.

El usuario puede filtrar empleados por nombre, apellido, cedula o equipo asignado.
El sistema filtra en pantalla los empleados disponibles y miembros actuales sin consultar nuevamente la base de datos.

El usuario selecciona uno o mas empleados disponibles.
El sistema actualiza el contador de empleados seleccionados.
El sistema habilita el boton Asignar Seleccionados cuando existe al menos un empleado seleccionado.

El usuario presiona Asignar Seleccionados.
El sistema valida permiso equipo.editar.
El sistema valida que se haya seleccionado un equipo y al menos un empleado.
El sistema valida que el equipo exista y este activo. Tabla consultada: equipo_trabajo.
El sistema valida que los empleados seleccionados esten activos y pertenezcan a la sucursal del equipo. Tabla consultada: empleados.
El sistema registra o reactiva la asignacion del empleado al equipo.
Tabla insertada o modificada: equipo_empleado.
El sistema emite mensaje de empleados asignados correctamente.

Quitar Miembro
Desde el listado de equipos, el usuario presiona la opcion Ver miembros de un equipo.
El sistema abre la vista Miembros del Equipo con el identificador del equipo seleccionado.
El sistema valida permiso equipo.editar.
El sistema obtiene el ID del equipo desde la ruta.
El sistema consulta los miembros activos del equipo y sus datos de empleado.
Tablas consultadas: equipo_empleado, empleados.
El sistema muestra empleado, rol, estado y la accion Quitar miembro.

El usuario presiona Quitar miembro sobre un empleado.
El sistema envia el ID del equipo y el ID del empleado.
El sistema valida permiso equipo.editar.
El sistema valida que el equipo y el empleado hayan sido recibidos correctamente.
El sistema valida que el empleado pertenezca activamente al equipo.
Tabla consultada: equipo_empleado.
El sistema desactiva la relacion del empleado con el equipo.
Tabla modificada: equipo_empleado.
El sistema emite mensaje de empleado quitado del equipo.

Eliminar
El usuario presiona Eliminar.
El sistema valida permiso equipo.eliminar.
El sistema valida ID del equipo y existencia del equipo.
El sistema consulta si el equipo tiene registros en equipo_empleado. Tabla consultada: equipo_empleado.
Si el equipo tiene registros en equipo_empleado, el sistema desactiva el equipo. Tabla modificada: equipo_trabajo.
Si el equipo no tiene registros en equipo_empleado, el sistema elimina el equipo. Tabla eliminada: equipo_trabajo.
El sistema emite mensaje de equipo inactivado o eliminado correctamente segun corresponda.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan sucursal o nombre, el sistema muestra error.
Si la sucursal no es valida, el sistema muestra error.
Si ya existe un equipo activo con el mismo nombre en la sucursal, el sistema muestra error.
Si el equipo no existe o esta inactivo, el sistema no permite editar.
Si no se selecciona equipo o empleados, el sistema muestra error.
Si un empleado seleccionado no corresponde a la sucursal del equipo, el sistema muestra error.

* Post Condicion
El equipo queda registrado en equipo_trabajo.
Si se edita, sus datos quedan modificados.
Si se elimina con registros en equipo_empleado, queda inactivo.
Si se elimina sin registros en equipo_empleado, se elimina del sistema.
Las asignaciones de empleados quedan registradas en equipo_empleado.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| equipo_trabajo | equipo_trabajo | Bd_reduc |
| equipo_empleado | equipo_empleado | Bd_reduc |
| empleados | empleados | Bd_reduc |
| sucursales | sucursales | Bd_reduc |

## 8. Usuarios

* Nombre de Caso de Uso
Registrar, Editar, Eliminar y Asignar Roles o Sucursal a Usuarios.

* Descripcion Basica
Este caso permite administrar usuarios del sistema. El sistema permite crear usuarios, editar sus datos, eliminar o desactivar usuarios, asignar roles y asignar sucursal.

* Actores relacionados
Administrador del sistema.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe contar con permiso usuarios.crear para registrar usuarios.
El usuario debe contar con permiso usuarios.ver para consultar y listar usuarios.
El usuario debe contar con permiso usuarios.editar para editar usuarios y ver datos de seguridad de cuenta.
El usuario puede actualizar su propia cuenta sin permiso usuarios.editar, pero solo con los campos permitidos para cuenta propia.
El usuario debe contar con permiso usuarios.eliminar para eliminar o desactivar usuarios.
El usuario debe contar con permiso usuarios.asignarrol para asignar roles.
El usuario debe contar con permiso usuarios.asignarlocal para asignar sucursal.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Usuarios.
El sistema muestra opciones Nuevo Usuario, Lista de Usuarios, Buscar Usuario, Asignar Rol, Asignar Sucursal y Permisos por Rol segun permisos.
En la vista Nuevo Usuario, el sistema muestra el formulario de registro, buscador, listado de usuarios y acciones segun permisos.
En la edicion de cuenta propia, el sistema muestra solo el formulario de actualizacion y oculta buscador, listado y acciones administrativas.
El sistema consulta usuarios para el listado cuando corresponde. Tabla consultada: usuarios.

Nuevo
El usuario ingresa CI, nombres, apellidos, telefono, nombre de usuario, email, contrasena y confirmacion de contrasena.
En esta carga no se consultan tablas referenciales; la validacion contra usuarios se realiza al guardar.
Si el usuario presiona Cancelar, el sistema limpia los campos del formulario sin consultar tablas.

Guardar
El sistema valida permiso usuarios.crear.
El sistema valida campos obligatorios: nombres, apellidos, nombre de usuario, contrasena y confirmacion de contrasena.
El sistema valida formato de CI, nombres, apellidos, telefono, usuario y contrasena.
El sistema valida que las contrasenas coincidan.
El sistema valida duplicado de CI, usuario y email.
Tabla consultada: usuarios.
El sistema registra el usuario. Tabla insertada: usuarios.
El sistema emite mensaje de usuario registrado correctamente.

Buscar/Listar
El sistema valida permiso usuarios.ver para mostrar listado de usuarios.
El usuario puede buscar por CI o nombre de usuario.
El sistema consulta usuarios segun la busqueda. Tabla consultada: usuarios.
El sistema no muestra el usuario principal ni el usuario de la sesion actual en el listado.
El sistema muestra CI, nombre, telefono, usuario, email y estado.
Si el usuario tiene permiso usuarios.editar, el sistema muestra intentos fallidos, bloqueo por intentos y la accion Editar.
Si el usuario tiene permiso usuarios.asignarrol, el sistema muestra la accion Asignar roles.
Si el usuario tiene permiso usuarios.asignarlocal, el sistema muestra la accion Asignar sucursal.
Si el usuario tiene permiso usuarios.eliminar, el sistema muestra la accion Eliminar.

Editar
El usuario presiona Editar.
El sistema consulta el usuario seleccionado. Tabla consultada: usuarios.
El sistema carga CI, nombres, apellidos, telefono, usuario, email, estado, intentos fallidos y bloqueo.
Si la cuenta es impropia, el usuario puede modificar CI, nombres, apellidos, telefono, usuario, email, estado y, opcionalmente, contrasena.
Si la cuenta es propia, el sistema deja CI, nombres, apellidos, usuario y estado en solo lectura; el usuario solo puede modificar telefono, email y, opcionalmente, contrasena.
Opcionalmente, el usuario ingresa nueva contrasena y confirmacion de contrasena.
El usuario ingresa nombre de usuario y contrasena administrativa para confirmar la actualizacion.
El usuario presiona Actualizar.
El sistema valida existencia del usuario. Tabla consultada: usuarios.
El sistema valida tipo de cuenta propia o impropia.
Si la cuenta es propia, el sistema valida que corresponda al usuario en sesion y conserva desde la base de datos CI, nombres, apellidos, usuario y estado.
Si la cuenta es impropia, el sistema valida permiso usuarios.editar.
El sistema valida campos obligatorios, formatos, estado, duplicados, contrasenas y credenciales administrativas.
Si los datos son correctos, el sistema actualiza el registro del usuario. Tabla modificada: usuarios.
El sistema emite mensaje de usuario actualizado correctamente.

Asignar Roles
El usuario presiona la accion Asignar roles desde el listado de usuarios.
El sistema valida permiso usuarios.asignarrol.
El sistema envia el ID del usuario seleccionado.
El sistema consulta roles disponibles y roles asignados al usuario. Tablas consultadas: roles, usuario_rol.
El sistema muestra los roles en un modal con casillas de seleccion.
El usuario marca o desmarca roles.
El usuario presiona Guardar cambios.
El sistema elimina las relaciones anteriores del usuario y registra las relaciones seleccionadas.
Tabla eliminada e insertada: usuario_rol.
El sistema emite mensaje de roles actualizados correctamente.

Asignar Sucursal
El usuario presiona la accion Asignar sucursal desde el listado de usuarios o ingresa a la vista Asignar Sucursal.
El sistema valida permiso usuarios.asignarlocal.
Desde el listado, el sistema envia el ID del usuario seleccionado y consulta su sucursal actual. Tabla consultada: usuarios.
El sistema consulta sucursales activas para el selector. Tabla consultada: sucursales.
El usuario selecciona sucursal.
El sistema edita la sucursal del usuario. Tabla modificada: usuarios.
El sistema emite mensaje de sucursal actualizada correctamente.

Eliminar
El usuario presiona Eliminar.
El sistema emite mensaje de confirmacion.
El usuario confirma la accion.
El sistema valida que no se intente eliminar el usuario principal del sistema.
El sistema valida permiso usuarios.eliminar.
El sistema valida que el usuario exista. Tabla consultada: usuarios.
El sistema intenta eliminar el usuario.
Si la base de datos no permite eliminarlo porque el usuario esta relacionado con otros registros, el sistema lo desactiva.
Si la base de datos permite eliminarlo, el sistema lo elimina.
Tabla modificada o eliminada: usuarios.
El sistema emite mensaje de usuario desactivado o eliminado correctamente segun corresponda.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan campos obligatorios, el sistema muestra error.
Si CI, usuario o email ya existen, el sistema no permite guardar.
Si el tipo de cuenta no es valido, el sistema muestra error.
Si la cuenta fue marcada como propia pero no corresponde al usuario en sesion, el sistema muestra error.
Si el estado no es valido, el sistema muestra error.
Si las contrasenas no coinciden o no cumplen formato, el sistema muestra error.
Si las credenciales administrativas no son validas, el sistema cancela la edicion.
Si se intenta eliminar el usuario principal, el sistema muestra error.
Si no se pueden guardar roles o sucursal, el sistema muestra error.

* Post Condicion
El usuario queda registrado o editado en usuarios.
Los roles quedan asociados en usuario_rol.
La sucursal queda asociada al usuario.
Si la eliminacion es bloqueada por relaciones, el usuario queda inactivo.
Si no existen relaciones que bloqueen la eliminacion, el usuario se elimina del sistema.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| usuarios | usuarios | Bd_reduc |
| roles | roles | Bd_reduc |
| usuario_rol | usuario_rol | Bd_reduc |
| sucursales | sucursales | Bd_reduc |

## 9. Roles y Permisos

* Nombre de Caso de Uso
Registrar, Editar, Eliminar Roles y Asignar Permisos por Rol.

* Descripcion Basica
Este caso permite administrar roles del sistema y sus permisos asociados. El sistema permite crear roles, editarlos, eliminarlos y definir permisos por rol.

* Actores relacionados
Administrador del sistema.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe contar con permiso para visualizacion.
El usuario debe contar con permiso para creacion, edicion, eliminacion y modificacion de permisos.
El usuario debe contar con permiso para eliminacion.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Roles.
El sistema muestra el formulario de roles y consulta el listado. Tabla consultada: roles.

Nuevo
El usuario ingresa nombre y descripcion del rol.
En esta carga no se consultan tablas referenciales; la validacion contra roles se realiza al guardar.
Si el usuario presiona Cancelar, el sistema limpia los campos del formulario sin consultar tablas.

Guardar
El sistema valida permiso de edicion.
El sistema valida nombre obligatorio.
El sistema valida que el rol no este duplicado. Tabla consultada: roles.
El sistema registra el rol. Tabla insertada: roles.

Buscar/Listar
El usuario busca rol por nombre o descripcion.
El sistema consulta roles segun la busqueda. Tabla consultada: roles.
El sistema muestra nombre, descripcion y estado.
Si el usuario tiene permiso de edicion, muestra Editar.
Si el usuario tiene permiso de eliminacion, muestra Eliminar.

Editar
El usuario presiona Editar.
El sistema consulta el rol seleccionado. Tabla consultada: roles.
El sistema carga nombre, descripcion y estado en el formulario.
El usuario modifica nombre, descripcion o estado.
El sistema valida permiso de edicion.
El sistema valida existencia del rol, nombre obligatorio, estado valido y nombre no duplicado.
El sistema edita el rol. Tabla modificada: roles.

Eliminar
El usuario presiona Eliminar.
El sistema valida permiso de edicion.
El sistema valida que el rol exista. Tabla consultada: roles.
El sistema elimina el rol. Tabla eliminada: roles.

Permisos por Rol
El usuario ingresa a Permisos por Rol.
El sistema valida permiso de edicion.
El sistema consulta roles disponibles para seleccionar. Tabla consultada: roles.
El usuario selecciona un rol.
El sistema consulta permisos disponibles y permisos asociados al rol.
Tablas consultadas: permisos, rol_permiso.
El usuario marca o desmarca permisos.
El sistema valida permiso de edicion.
El sistema guarda permisos del rol. Tabla modificada: rol_permiso.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si no se ingresa nombre del rol, el sistema muestra error.
Si el rol ya existe, el sistema no permite guardar.
Si el rol no es valido, el sistema muestra error.

* Post Condicion
El rol queda registrado, editado o eliminado en roles.
Los permisos del rol quedan modificados en rol_permiso.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| roles | roles | Bd_reduc |
| permisos | permisos | Bd_reduc |
| rol_permiso | rol_permiso | Bd_reduc |
