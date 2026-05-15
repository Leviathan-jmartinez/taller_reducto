# Especificaciones de Casos de Uso - Referenciales

## 1. Sucursales

* Nombre de Caso de Uso
Registrar, Actualizar y Eliminar Sucursales.

* Descripcion Basica
Este caso permite administrar las sucursales de la empresa. El sistema permite registrar una nueva sucursal, listar sucursales, buscar por descripcion, actualizar sus datos y eliminar o desactivar una sucursal segun su uso en el sistema.

* Actores relacionados
Administrador o Usuario autorizado.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso sucursal.crear para registrar.
El usuario debe tener permiso sucursal.editar para actualizar.
El usuario debe tener permiso sucursal.eliminar para eliminar o desactivar.
Debe existir una empresa registrada para asociar la sucursal.
Para actualizar o eliminar, debe existir una sucursal registrada.

* Flujo de eventos
Flujo Basico:
El usuario accede al sistema a traves de un logueo.
El usuario ingresa al modulo Sucursales.
El sistema muestra el formulario de registro y el listado de sucursales.

Nuevo
El sistema carga las empresas disponibles.
Tabla consultada para empresas: empresa.
El usuario selecciona una empresa.
El usuario ingresa descripcion de la sucursal.
El usuario ingresa numero de establecimiento.
El usuario ingresa direccion.
El usuario ingresa telefono.
El usuario selecciona estado Activo o Inactivo.

Guardar
El usuario presiona Guardar.
El sistema emite mensaje de confirmacion.
El usuario confirma la accion.
El sistema valida permiso sucursal.crear.
El sistema valida datos obligatorios: empresa, descripcion y estado.
El sistema registra la sucursal. Tabla insertada: sucursales.
El sistema emite mensaje de sucursal registrada correctamente.

Buscar/Listar
El usuario ingresa un criterio de busqueda por descripcion de sucursal.
El usuario presiona Buscar.
El sistema consulta las sucursales registradas.
Tablas consultadas para listar sucursales: sucursales, empresa.
El sistema muestra sucursal, empresa, numero de establecimiento y estado.
Si el usuario tiene permiso sucursal.editar, el sistema muestra la opcion Actualizar.
Si el usuario tiene permiso sucursal.eliminar, el sistema muestra la opcion Eliminar.

Actualizar
El usuario presiona Actualizar.
El sistema consulta la sucursal seleccionada. Tabla consultada: sucursales.
El sistema carga los datos en el formulario.
El usuario modifica empresa, descripcion, numero de establecimiento, direccion, telefono o estado.
El usuario presiona Guardar.
El sistema valida permiso sucursal.editar.
El sistema valida que la sucursal exista. Tabla consultada: sucursales.
El sistema actualiza los datos de la sucursal. Tabla actualizada: sucursales.
El sistema emite mensaje de sucursal actualizada correctamente.

Eliminar
El usuario presiona Eliminar.
El sistema emite mensaje de confirmacion.
El usuario confirma la accion.
El sistema valida permiso sucursal.eliminar.
El sistema valida que la sucursal exista. Tabla consultada: sucursales.
El sistema verifica si la sucursal esta asociada a usuarios. Tabla consultada: usuarios.
Si la sucursal esta asociada a usuarios, el sistema la desactiva. Tabla actualizada: sucursales.
Si la sucursal no esta asociada a usuarios, el sistema la elimina. Tabla eliminada: sucursales.
El sistema emite mensaje segun corresponda.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan empresa, descripcion o estado, el sistema muestra campos obligatorios incompletos.
Si la sucursal no existe al actualizar o eliminar, el sistema muestra error.
Si no se puede registrar, actualizar o eliminar, el sistema muestra error.

* Post Condicion
La sucursal queda registrada en sucursales.
Si se actualiza, los datos quedan modificados.
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
Registrar, Actualizar y Eliminar Articulos.

* Descripcion Basica
Este caso permite administrar articulos del sistema, incluyendo servicios, productos e insumos. El sistema permite cargar datos comerciales, categoria, proveedor, unidad de medida, IVA, marca, precios, codigo y tipo de articulo.

* Actores relacionados
Administrador o Usuario autorizado.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso articulo.crear para registrar.
El usuario debe tener permiso articulo.editar para actualizar.
El usuario debe tener permiso articulo.eliminar para eliminar o desactivar.
Deben existir categoria, unidad de medida, IVA y marca disponibles.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Articulos.
El sistema muestra el formulario de registro y el listado de articulos.

Nuevo
El sistema carga categorias, proveedores, unidades de medida, IVA y marcas.
Tablas consultadas: categoria, proveedores, unidad_medida, iva, marcas.
El usuario ingresa codigo, descripcion, precio de venta, precio de compra si corresponde, categoria, proveedor, unidad de medida, IVA, marca y tipo de articulo.

Guardar
El usuario presiona Guardar.
El sistema valida permiso articulo.crear.
El sistema valida campos obligatorios y formatos.
El sistema valida que el codigo no este duplicado. Tabla consultada: articulos.
El sistema registra el articulo. Tabla insertada: articulos.
El sistema emite mensaje de articulo registrado correctamente.

Buscar/Listar
El usuario ingresa busqueda por codigo, descripcion o identificador.
El sistema consulta los articulos.
Tabla consultada para listar articulos: articulos.
El sistema muestra codigo, descripcion y detalle de precios.
Si el usuario tiene permiso articulo.editar, muestra Actualizar.
Si el usuario tiene permiso articulo.eliminar, muestra Eliminar.

Actualizar
El usuario presiona Actualizar.
El sistema consulta el articulo seleccionado y sus datos relacionados.
Tablas consultadas: articulos, proveedores.
El sistema carga los datos en el formulario.
El usuario modifica los datos.
El sistema valida permiso articulo.editar.
El sistema valida existencia del articulo. Tabla consultada: articulos.
El sistema valida formatos, selects y duplicado de codigo.
El sistema actualiza el articulo. Tabla actualizada: articulos.
El sistema emite mensaje de articulo actualizado correctamente.

Eliminar
El usuario presiona Eliminar.
El sistema emite mensaje de confirmacion.
El usuario confirma la accion.
El sistema valida permiso articulo.eliminar.
El sistema valida que el articulo exista. Tabla consultada: articulos.
Si el articulo tiene movimientos asociados, el sistema lo desactiva.
Si no tiene movimientos asociados, el sistema lo elimina.
Tabla actualizada o eliminada: articulos.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan campos obligatorios, el sistema muestra error.
Si el codigo ya existe, el sistema no permite registrar o actualizar.
Si los formatos no son validos, el sistema muestra error.
Si el articulo no existe, el sistema muestra error.

* Post Condicion
El articulo queda registrado en articulos.
Si se actualiza, sus datos quedan modificados.
Si se elimina con movimientos, queda inactivo.
Si se elimina sin movimientos, se elimina del sistema.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| articulos | articulos | Bd_reduc |
| categoria | categoria | Bd_reduc |
| proveedores | proveedores | Bd_reduc |
| unidad_medida | unidad_medida | Bd_reduc |
| iva | iva | Bd_reduc |
| marcas | marcas | Bd_reduc |

## 3. Proveedores

* Nombre de Caso de Uso
Registrar, Actualizar y Eliminar Proveedores.

* Descripcion Basica
Este caso permite administrar proveedores. El sistema permite registrar razon social, RUC, telefono, direccion, correo, ciudad y estado.

* Actores relacionados
Administrador o Usuario autorizado.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso proveedor.crear para registrar.
El usuario debe tener permiso proveedor.editar para actualizar.
El usuario debe tener permiso proveedor.eliminar para eliminar o desactivar.
Debe existir ciudad registrada.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Proveedores.
El sistema carga ciudades disponibles. Tabla consultada: ciudades.

Nuevo
El usuario ingresa razon social, RUC, telefono, direccion, correo, ciudad y estado.

Guardar
El sistema valida permiso proveedor.crear.
El sistema valida campos obligatorios: razon social, ciudad y estado.
El sistema valida formato de correo si fue ingresado.
El sistema valida que la razon social no este duplicada. Tabla consultada: proveedores.
El sistema registra el proveedor. Tabla insertada: proveedores.
El sistema emite mensaje de proveedor registrado correctamente.

Buscar/Listar
El usuario busca proveedor por razon social o RUC.
El sistema consulta proveedores.
Tablas consultadas para listar proveedores: proveedores, ciudades.
El sistema muestra razon social, RUC, ciudad y estado.
Si el usuario tiene permiso proveedor.editar, muestra Actualizar.
Si el usuario tiene permiso proveedor.eliminar, muestra Eliminar.

Actualizar
El usuario presiona Actualizar.
El sistema consulta el proveedor seleccionado. Tabla consultada: proveedores.
El sistema carga los datos en el formulario.
El sistema valida permiso proveedor.editar.
El sistema valida existencia, campos obligatorios, correo, estado y duplicado de razon social.
El sistema actualiza el proveedor. Tabla actualizada: proveedores.
El sistema emite mensaje de proveedor actualizado correctamente.

Eliminar
El usuario presiona Eliminar.
El sistema valida permiso proveedor.eliminar.
El sistema valida que el proveedor exista. Tabla consultada: proveedores.
Si el proveedor tiene registros relacionados, el sistema lo desactiva.
Si no tiene registros relacionados, el sistema lo elimina.
Tabla actualizada o eliminada: proveedores.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan campos obligatorios, el sistema muestra error.
Si el correo no es valido, el sistema muestra error.
Si la razon social ya existe, el sistema no permite registrar o actualizar.
Si el proveedor no existe, el sistema muestra error.

* Post Condicion
El proveedor queda registrado en proveedores.
Si se actualiza, sus datos quedan modificados.
Si se elimina con registros relacionados, queda inactivo.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| proveedores | proveedores | Bd_reduc |
| ciudades | ciudades | Bd_reduc |

## 4. Clientes

* Nombre de Caso de Uso
Registrar, Actualizar y Eliminar Clientes.

* Descripcion Basica
Este caso permite administrar clientes, registrando documento, tipo de documento, digito verificador, nombre, apellido, telefono, correo, direccion, ciudad y estado civil.

* Actores relacionados
Administrador, Recepcionista o Usuario autorizado.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso cliente.crear para registrar.
El usuario debe tener permiso cliente.editar para actualizar.
El usuario debe tener permiso cliente.eliminar para eliminar o desactivar.
Debe existir ciudad registrada.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Clientes.
El sistema carga ciudades disponibles. Tabla consultada: ciudades.

Nuevo
El usuario ingresa documento, tipo de documento, digito verificador, nombre, apellido, telefono, correo, direccion, ciudad y estado civil.

Guardar
El sistema valida permiso cliente.crear.
El sistema valida campos obligatorios: documento, nombre, direccion y ciudad.
El sistema valida documento duplicado. Tabla consultada: clientes.
El sistema registra el cliente. Tabla insertada: clientes.
El sistema emite mensaje de cliente registrado correctamente.

Buscar/Listar
El usuario busca por documento, nombre o apellido.
El sistema consulta clientes. Tabla consultada: clientes.
El sistema muestra documento, cliente, telefono y direccion.
Si el usuario tiene permiso cliente.editar, muestra Actualizar.
Si el usuario tiene permiso cliente.eliminar, muestra Eliminar.

Actualizar
El usuario presiona Actualizar.
El sistema consulta el cliente seleccionado. Tabla consultada: clientes.
El sistema carga los datos.
El sistema valida permiso cliente.editar.
El sistema valida existencia, campos obligatorios, ciudad, estado y duplicado de documento.
El sistema actualiza el cliente. Tabla actualizada: clientes.
El sistema emite mensaje de cliente actualizado correctamente.

Eliminar
El usuario presiona Eliminar.
El sistema valida permiso cliente.eliminar.
El sistema valida que el cliente exista. Tabla consultada: clientes.
Si el cliente tiene movimientos asociados, el sistema lo desactiva.
Si no tiene movimientos asociados, el sistema lo elimina.
Tabla actualizada o eliminada: clientes.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan datos obligatorios, el sistema muestra error.
Si el documento ya existe, el sistema no permite registrar o actualizar.
Si el cliente no existe, el sistema muestra error.

* Post Condicion
El cliente queda registrado en clientes.
Si se actualiza, sus datos quedan modificados.
Si se elimina con movimientos, queda inactivo.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| clientes | clientes | Bd_reduc |
| ciudades | ciudades | Bd_reduc |

## 5. Vehiculos

* Nombre de Caso de Uso
Registrar, Actualizar y Eliminar Vehiculos.

* Descripcion Basica
Este caso permite administrar vehiculos asociados a clientes. El sistema permite registrar cliente, modelo, color, placa, anho, numero de serie y estado.

* Actores relacionados
Administrador, Recepcionista o Usuario autorizado.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso vehiculo.crear para registrar.
El usuario debe tener permiso vehiculo.editar para actualizar.
El usuario debe tener permiso vehiculo.eliminar para eliminar o desactivar.
Deben existir cliente y modelo registrados.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Vehiculos.
El sistema carga clientes y modelos disponibles.
Tablas consultadas: clientes, modelo_auto.

Nuevo
El usuario selecciona cliente y modelo.
El usuario ingresa color, placa, anho, numero de serie y estado.

Guardar
El sistema valida permiso vehiculo.crear.
El sistema valida campos obligatorios: cliente, modelo, color y placa.
El sistema registra el vehiculo. Tabla insertada: vehiculos.
El sistema emite mensaje de vehiculo registrado correctamente.

Buscar/Listar
El usuario busca por placa o cliente.
El sistema consulta vehiculos.
Tablas consultadas para listar vehiculos: vehiculos, clientes, modelo_auto.
El sistema muestra placa, cliente, modelo, color y estado.
Si el usuario tiene permiso vehiculo.editar, muestra Actualizar.
Si el usuario tiene permiso vehiculo.eliminar, muestra Eliminar.

Actualizar
El usuario presiona Actualizar.
El sistema consulta el vehiculo seleccionado. Tabla consultada: vehiculos.
El sistema carga los datos.
El sistema valida permiso vehiculo.editar.
El sistema actualiza el vehiculo. Tabla actualizada: vehiculos.
El sistema emite mensaje de vehiculo actualizado correctamente.

Eliminar
El usuario presiona Eliminar.
El sistema valida permiso vehiculo.eliminar.
El sistema valida que el vehiculo exista. Tabla consultada: vehiculos.
Si el vehiculo tiene movimientos asociados, el sistema lo desactiva.
Si no tiene movimientos asociados, el sistema lo elimina.
Tabla actualizada o eliminada: vehiculos.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan datos obligatorios, el sistema muestra error.
Si el vehiculo no existe, el sistema muestra error.

* Post Condicion
El vehiculo queda registrado en vehiculos.
Si se actualiza, sus datos quedan modificados.
Si se elimina con movimientos, queda inactivo.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| vehiculos | vehiculos | Bd_reduc |
| clientes | clientes | Bd_reduc |
| modelo_auto | modelo_auto | Bd_reduc |

## 6. Empleados

* Nombre de Caso de Uso
Registrar, Actualizar y Eliminar Empleados.

* Descripcion Basica
Este caso permite administrar empleados, asociandolos a cargo y sucursal.

* Actores relacionados
Administrador o Usuario autorizado.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso empleado.crear para registrar.
El usuario debe tener permiso empleado.editar para actualizar.
El usuario debe tener permiso empleado.eliminar para eliminar o desactivar.
Deben existir cargo y sucursal registrados.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Empleados.
El sistema carga cargos y sucursales.
Tablas consultadas: cargos, sucursales.

Nuevo
El usuario ingresa cargo, sucursal, nombre, apellido, direccion, celular, cedula y estado civil.

Guardar
El sistema valida permiso empleado.crear.
El sistema valida campos obligatorios: nombre, apellido y cedula.
El sistema valida cedula duplicada. Tabla consultada: empleados.
El sistema registra el empleado. Tabla insertada: empleados.
El sistema emite mensaje de empleado registrado correctamente.

Buscar/Listar
El usuario busca por nombre, apellido o cedula.
El sistema consulta empleados.
Tablas consultadas para listar empleados: empleados, cargos, sucursales.
El sistema muestra empleado, cargo y sucursal.
Si el usuario tiene permiso empleado.editar, muestra Actualizar.
Si el usuario tiene permiso empleado.eliminar, muestra Eliminar.

Actualizar
El usuario presiona Actualizar.
El sistema consulta el empleado seleccionado. Tabla consultada: empleados.
El sistema carga los datos.
El sistema valida permiso empleado.editar.
El sistema actualiza el empleado. Tabla actualizada: empleados.
El sistema emite mensaje de empleado actualizado correctamente.

Eliminar
El usuario presiona Eliminar.
El sistema valida permiso empleado.eliminar.
El sistema valida que el empleado exista. Tabla consultada: empleados.
Si el empleado tiene movimientos asociados, el sistema lo desactiva.
Si no tiene movimientos asociados, el sistema lo elimina.
Tabla actualizada o eliminada: empleados.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan campos obligatorios, el sistema muestra error.
Si la cedula ya existe, el sistema no permite registrar.
Si el empleado no existe, el sistema muestra error.

* Post Condicion
El empleado queda registrado en empleados.
Si se actualiza, sus datos quedan modificados.
Si se elimina con movimientos, queda inactivo.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| empleados | empleados | Bd_reduc |
| cargos | cargos | Bd_reduc |
| sucursales | sucursales | Bd_reduc |

## 7. Equipos de Trabajo

* Nombre de Caso de Uso
Registrar, Actualizar, Eliminar y Asignar Empleados a Equipos de Trabajo.

* Descripcion Basica
Este caso permite administrar equipos de trabajo por sucursal. El sistema permite crear equipos, actualizar sus datos, eliminar equipos de forma logica, ver miembros y asignar o quitar empleados.

* Actores relacionados
Administrador o Encargado de personal.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso empleado.crear para crear equipos.
El usuario debe tener permiso empleado.editar para actualizar equipos, asignar empleados o quitar miembros.
El usuario debe tener permiso empleado.eliminar para eliminar equipos.
Deben existir sucursales y empleados activos.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Equipos de Trabajo.
El sistema muestra las opciones Equipos y Asignar Empleados.

Nuevo
El sistema carga sucursales activas. Tabla consultada: sucursales.
El usuario selecciona sucursal.
El usuario ingresa nombre del equipo y descripcion.

Guardar
El sistema valida permiso empleado.crear.
El sistema valida sucursal y nombre.
El sistema registra el equipo. Tabla insertada: equipo_trabajo.
El sistema emite mensaje de equipo creado correctamente.

Buscar/Listar
El sistema consulta equipos activos.
Tablas consultadas para listar equipos: equipo_trabajo, sucursales.
El sistema muestra equipo, sucursal y estado.
Si el usuario tiene permiso empleado.editar, muestra Actualizar.
Si el usuario tiene permiso empleado.eliminar, muestra Eliminar.
El sistema permite ver miembros del equipo.

Actualizar
El usuario presiona Actualizar.
El sistema abre la vista de actualizacion con el ID del equipo.
El sistema consulta el equipo seleccionado. Tabla consultada: equipo_trabajo.
El sistema carga sucursal, nombre y descripcion.
El usuario modifica los datos.
El sistema valida permiso empleado.editar.
El sistema valida que el equipo exista y este activo. Tabla consultada: equipo_trabajo.
El sistema actualiza el equipo. Tabla actualizada: equipo_trabajo.
El sistema emite mensaje de equipo actualizado correctamente.

Asignar Empleados
El usuario ingresa a Asignar Empleados.
El sistema consulta equipos activos. Tabla consultada: equipo_trabajo.
El sistema consulta empleados activos de la sucursal. Tabla consultada: empleados.
El usuario selecciona equipo y empleados.
El sistema valida permiso empleado.editar.
El sistema registra o reactiva la asignacion del empleado al equipo.
Tabla insertada o actualizada: equipo_empleado.
El sistema emite mensaje de empleados asignados correctamente.

Quitar Miembro
El usuario ingresa a miembros del equipo.
El sistema consulta miembros activos.
Tablas consultadas: equipo_empleado, empleados.
El usuario presiona quitar miembro.
El sistema valida permiso empleado.editar.
El sistema desactiva la relacion del empleado con el equipo.
Tabla actualizada: equipo_empleado.

Eliminar
El usuario presiona Eliminar.
El sistema valida permiso empleado.eliminar.
El sistema valida ID del equipo.
El sistema desactiva el equipo. Tabla actualizada: equipo_trabajo.
El sistema emite mensaje de equipo eliminado correctamente.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan sucursal o nombre, el sistema muestra error.
Si el equipo no existe o esta inactivo, el sistema no permite actualizar.
Si no se selecciona equipo o empleados, el sistema muestra error.

* Post Condicion
El equipo queda registrado en equipo_trabajo.
Si se actualiza, sus datos quedan modificados.
Si se elimina, queda inactivo.
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
Registrar, Actualizar, Eliminar y Asignar Roles o Sucursal a Usuarios.

* Descripcion Basica
Este caso permite administrar usuarios del sistema. El sistema permite crear usuarios, actualizar sus datos, eliminar o desactivar usuarios, asignar roles y asignar sucursal.

* Actores relacionados
Administrador del sistema.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso usuarios.crear para registrar.
El usuario debe tener permiso usuarios.editar para actualizar.
El usuario debe tener permiso usuarios.eliminar para eliminar.
El usuario debe tener permiso usuarios.asignarrol para asignar roles.
El usuario debe tener permiso usuarios.asignarlocal para asignar sucursal.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Usuarios.
El sistema muestra opciones Nuevo Usuario, Lista de Usuarios, Buscar Usuario, Asignar Rol y Asignar Sucursal segun permisos.

Nuevo
El usuario ingresa CI, nombres, apellidos, telefono, usuario, email y contrasena.

Guardar
El sistema valida permiso usuarios.crear.
El sistema valida campos obligatorios, formatos, duplicado de CI, usuario y email.
Tabla consultada: usuarios.
El sistema registra el usuario. Tabla insertada: usuarios.
El sistema emite mensaje de usuario registrado correctamente.

Buscar/Listar
El sistema consulta usuarios. Tabla consultada: usuarios.
El sistema muestra usuarios y acciones segun permisos.

Actualizar
El usuario presiona Actualizar.
El sistema consulta el usuario seleccionado. Tabla consultada: usuarios.
El sistema carga los datos.
El usuario modifica datos y confirma con credenciales administrativas.
El sistema valida permiso usuarios.editar cuando corresponde.
El sistema valida existencia, formatos, duplicados y credenciales.
El sistema actualiza el usuario. Tabla actualizada: usuarios.

Asignar Roles
El usuario selecciona un usuario.
El sistema valida permiso usuarios.asignarrol.
El sistema consulta roles del usuario. Tablas consultadas: roles, usuario_rol.
El usuario marca o desmarca roles.
El sistema guarda roles del usuario. Tabla actualizada: usuario_rol.

Asignar Sucursal
El usuario selecciona un usuario.
El sistema valida permiso usuarios.asignarlocal.
El sistema consulta sucursales. Tabla consultada: sucursales.
El usuario selecciona sucursal.
El sistema actualiza la sucursal del usuario. Tabla actualizada: usuarios.

Eliminar
El usuario presiona Eliminar.
El sistema valida permiso usuarios.eliminar.
El sistema valida que el usuario exista. Tabla consultada: usuarios.
El sistema elimina o desactiva el usuario segun corresponda. Tabla actualizada o eliminada: usuarios.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si faltan campos obligatorios, el sistema muestra error.
Si CI, usuario o email ya existen, el sistema no permite guardar.
Si las credenciales administrativas no son validas, el sistema cancela la actualizacion.

* Post Condicion
El usuario queda registrado o actualizado en usuarios.
Los roles quedan asociados en usuario_rol.
La sucursal queda asociada al usuario.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| usuarios | usuarios | Bd_reduc |
| roles | roles | Bd_reduc |
| usuario_rol | usuario_rol | Bd_reduc |
| sucursales | sucursales | Bd_reduc |

## 9. Roles y Permisos

* Nombre de Caso de Uso
Registrar, Actualizar, Eliminar Roles y Asignar Permisos por Rol.

* Descripcion Basica
Este caso permite administrar roles del sistema y sus permisos asociados. El sistema permite crear roles, actualizarlos, eliminarlos y definir permisos por rol.

* Actores relacionados
Administrador del sistema.

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso roles.ver para acceder al listado.
El usuario debe tener permiso roles.editar para registrar, actualizar, eliminar y modificar permisos.
El usuario debe tener permiso usuarios.permisos_por_roles para ingresar a la pantalla de permisos por rol.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Roles.
El sistema muestra el formulario de roles y el listado.

Nuevo
El usuario ingresa nombre y descripcion del rol.

Guardar
El sistema valida permiso roles.editar.
El sistema valida nombre obligatorio.
El sistema valida que el rol no este duplicado. Tabla consultada: roles.
El sistema registra el rol. Tabla insertada: roles.

Buscar/Listar
El usuario busca rol por nombre o descripcion.
El sistema consulta roles. Tabla consultada: roles.
El sistema muestra nombre, descripcion y estado.
Si el usuario tiene permiso roles.editar, muestra Actualizar.

Actualizar
El usuario presiona Actualizar.
El sistema consulta el rol seleccionado. Tabla consultada: roles.
El usuario modifica nombre, descripcion o estado.
El sistema valida permiso roles.editar.
El sistema actualiza el rol. Tabla actualizada: roles.

Eliminar
El usuario presiona Eliminar.
El sistema valida permiso roles.editar.
El sistema elimina o desactiva el rol. Tabla actualizada o eliminada: roles.

Permisos por Rol
El usuario ingresa a Permisos por Rol.
El sistema valida permiso usuarios.permisos_por_roles.
El usuario selecciona un rol.
El sistema consulta permisos asociados al rol.
Tablas consultadas: permisos, rol_permiso.
El usuario marca o desmarca permisos.
El sistema valida permiso roles.editar.
El sistema guarda permisos del rol. Tabla actualizada: rol_permiso.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si no se ingresa nombre del rol, el sistema muestra error.
Si el rol ya existe, el sistema no permite registrar.
Si el rol no es valido, el sistema muestra error.

* Post Condicion
El rol queda registrado o actualizado en roles.
Los permisos del rol quedan actualizados en rol_permiso.

* Descripcion de las tablas
| Nombre | Alias | Base de Datos |
|---|---|---|
| roles | roles | Bd_reduc |
| permisos | permisos | Bd_reduc |
| rol_permiso | rol_permiso | Bd_reduc |
