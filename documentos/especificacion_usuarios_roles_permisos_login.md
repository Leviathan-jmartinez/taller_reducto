# Especificacion de Casos de Uso: Usuarios, Roles, Permisos y Login

## Usuarios

* **Nombre de Caso de Uso**  
Registrar y administrar usuarios.

* **Descripcion Basica**  
Este caso de uso se ocupa del registro, consulta, actualizacion, eliminacion o desactivacion de usuarios del sistema. Tambien permite asignar roles y sucursal a cada usuario.

* **Actores relacionados**  
Administrador del sistema.  
Usuario autorizado.

* **Pre Condicion**  
El usuario debe estar autenticado en el sistema.  
El usuario debe tener permisos para crear, editar, eliminar, asignar roles o asignar sucursal.  
No debe existir previamente otro usuario con el mismo CI, nombre de usuario o email.

* **Flujo de eventos**

**Flujo Basico:**

**Nuevo Usuario**  
* El usuario accede al sistema mediante logueo.
* El usuario ingresa a la interfaz de Usuarios.
* El sistema valida que tenga permiso `usuarios.crear`.
* El sistema muestra el formulario de informacion personal y datos de cuenta.
* El usuario ingresa CI, nombres, apellidos, telefono, usuario, email y contrasena.
* El usuario repite la contrasena.
* El usuario presiona Guardar.
* El sistema valida campos obligatorios.
* El sistema valida el formato de CI, nombres, apellidos, telefono, usuario, email y contrasena.
* El sistema valida que las contrasenas coincidan.
* El sistema valida que no exista otro usuario con el mismo CI.
* El sistema valida que no exista otro usuario con el mismo nombre de usuario.
* El sistema valida que no exista otro usuario con el mismo email.
* El sistema encripta la contrasena.
* El sistema registra el usuario en estado activo.

**Tablas consultadas al registrar usuario:**  
usuarios

**Tablas afectadas al registrar usuario:**  
usuarios

**Buscar/Listar Usuarios**  
* El usuario ingresa a la interfaz de Usuarios.
* El sistema muestra el buscador de usuarios.
* El usuario puede buscar por CI o nombre de usuario.
* El sistema consulta los usuarios registrados, excluyendo el usuario principal y el usuario actualmente logueado.
* El sistema muestra CI, nombre, telefono, usuario, email y acciones disponibles.

**Tablas consultadas para listar usuarios:**  
usuarios

**Actualizar Usuario**  
* El usuario selecciona la opcion de actualizar.
* El sistema carga los datos del usuario seleccionado.
* El usuario modifica los datos permitidos.
* El usuario puede cambiar la contrasena si ingresa una nueva y la confirma.
* El sistema solicita usuario y clave de autorizacion.
* Si el usuario actualiza su propia cuenta, el sistema valida sus propias credenciales.
* Si actualiza una cuenta ajena, el sistema valida permiso `usuarios.editar`.
* El sistema valida formatos, duplicados y estado.
* El sistema actualiza los datos del usuario.

**Tablas consultadas y afectadas al actualizar usuario:**  
usuarios

**Eliminar o Desactivar Usuario**  
* El usuario presiona el boton eliminar.
* El sistema valida que tenga permiso `usuarios.eliminar`.
* El sistema valida que el usuario exista.
* El sistema valida que no sea el usuario principal del sistema.
* El sistema verifica si el usuario ya posee movimientos en pedidos.
* Si el usuario tiene movimientos, el sistema lo desactiva.
* Si el usuario no tiene movimientos, el sistema lo elimina.

**Tablas consultadas y afectadas al eliminar o desactivar usuario:**  
usuarios  
pedido_cabecera

**Asignar Roles a Usuario**  
* El usuario presiona la opcion de asignar roles.
* El sistema carga todos los roles disponibles.
* El sistema marca los roles que el usuario ya tiene asignados.
* El usuario selecciona o desmarca roles.
* El usuario guarda los cambios.
* El sistema elimina las asignaciones anteriores.
* El sistema registra las nuevas asignaciones de roles.

**Tablas consultadas y afectadas al asignar roles:**  
usuarios  
roles  
usuario_rol

**Asignar Sucursal a Usuario**  
* El usuario presiona la opcion de asignar sucursal.
* El sistema consulta la sucursal actual del usuario.
* El sistema lista las sucursales activas.
* El usuario selecciona una sucursal.
* El sistema actualiza la sucursal del usuario.

**Tablas consultadas y afectadas al asignar sucursal:**  
usuarios  
sucursales

* **Flujo Alternativo**  
El sistema no permite dejar vacios los campos obligatorios.  
El sistema no permite registrar CI, usuario o email duplicados.  
El sistema no permite registrar datos con formato invalido.  
El sistema no permite guardar si las contrasenas no coinciden.  
El sistema no permite eliminar el usuario principal.  
El sistema desactiva el usuario cuando ya posee movimientos en el sistema.  
El usuario puede cancelar la operacion y limpiar el formulario.

* **Post Condicion**  
El sistema emite un mensaje cuando el usuario se registra correctamente.  
El sistema emite un mensaje cuando el usuario se actualiza correctamente.  
El sistema emite un mensaje cuando el usuario se elimina o desactiva.  
El usuario queda disponible para iniciar sesion si se encuentra activo.  
El usuario queda asociado a los roles y sucursal seleccionados.

* **Tablas involucradas en el modulo**  
usuarios  
roles  
usuario_rol  
sucursales  
pedido_cabecera

---

## Roles

* **Nombre de Caso de Uso**  
Registrar y administrar roles.

* **Descripcion Basica**  
Este caso de uso se ocupa del registro, consulta, actualizacion y eliminacion de roles. Los roles agrupan permisos que luego pueden asignarse a usuarios.

* **Actores relacionados**  
Administrador del sistema.  
Usuario autorizado.

* **Pre Condicion**  
El usuario debe estar autenticado en el sistema.  
El usuario debe tener permisos para ver, crear, editar o eliminar roles.  
No debe existir previamente otro rol con el mismo nombre.

* **Flujo de eventos**

**Flujo Basico:**

**Nuevo Rol**  
* El usuario ingresa a la interfaz de Roles.
* El sistema valida que tenga permiso `roles.ver`.
* El sistema muestra el formulario para registrar rol.
* El usuario ingresa nombre y descripcion.
* El usuario presiona Guardar.
* El sistema valida que el nombre no este vacio.
* El sistema valida que no exista otro rol con el mismo nombre.
* El sistema registra el rol en estado activo.

**Tablas consultadas al registrar rol:**  
roles

**Tablas afectadas al registrar rol:**  
roles

**Buscar/Listar Roles**  
* El usuario ingresa a la interfaz de Roles.
* El sistema muestra el buscador de roles.
* El usuario puede buscar por nombre o descripcion.
* El sistema consulta los roles registrados.
* El sistema muestra nombre, descripcion, estado y acciones disponibles.

**Tablas consultadas para listar roles:**  
roles

**Actualizar Rol**  
* El usuario selecciona la opcion actualizar.
* El sistema carga los datos del rol seleccionado.
* El usuario modifica nombre, descripcion o estado.
* El usuario presiona Actualizar.
* El sistema valida que el nombre no este vacio.
* El sistema valida el estado seleccionado.
* El sistema actualiza los datos del rol.

**Tablas consultadas y afectadas al actualizar rol:**  
roles

**Eliminar Rol**  
* El usuario presiona la opcion eliminar.
* El sistema valida que tenga permiso `roles.eliminar`.
* El sistema elimina el rol seleccionado.

**Tablas afectadas al eliminar rol:**  
roles

* **Flujo Alternativo**  
El sistema no permite registrar un rol sin nombre.  
El sistema no permite registrar roles duplicados.  
El sistema ajusta el estado a activo si recibe un estado invalido.  
El usuario puede cancelar la actualizacion y volver al listado.

* **Post Condicion**  
El sistema emite un mensaje cuando el rol se registra correctamente.  
El sistema emite un mensaje cuando el rol se actualiza correctamente.  
El sistema emite un mensaje cuando el rol se elimina correctamente.  
El rol queda disponible para asignar permisos y asociar a usuarios.

* **Tablas involucradas en el modulo**  
roles

---

## Permisos

* **Nombre de Caso de Uso**  
Asignar permisos a roles.

* **Descripcion Basica**  
Este caso de uso se ocupa de la asignacion de permisos a los roles del sistema. Los permisos definidos para un rol determinan las acciones y pantallas disponibles para los usuarios que tengan dicho rol.

* **Actores relacionados**  
Administrador del sistema.  
Usuario autorizado.

* **Pre Condicion**  
El usuario debe estar autenticado en el sistema.  
El usuario debe tener permiso para administrar permisos por roles.  
Debe existir al menos un rol registrado.  
Deben existir permisos registrados en el sistema.

* **Flujo de eventos**

**Flujo Basico:**

**Asignar Permisos**  
* El usuario ingresa a la interfaz de Permisos.
* El sistema valida que tenga permiso `usuarios.permisos_por_roles`.
* El sistema carga los roles activos.
* El usuario selecciona un rol.
* El sistema valida que tenga permiso `roles.editar` para cargar y modificar permisos.
* El sistema consulta todos los permisos del sistema.
* El sistema marca los permisos que el rol ya posee.
* El sistema agrupa los permisos por modulo segun la clave del permiso.
* El usuario selecciona o desmarca permisos.
* El usuario puede marcar permisos por modulo.
* El usuario presiona Guardar permisos.
* El sistema valida el rol seleccionado.
* El sistema elimina los permisos anteriores del rol.
* El sistema registra los permisos seleccionados.

**Tablas consultadas para cargar roles:**  
roles

**Tablas consultadas para cargar permisos del rol:**  
permisos  
rol_permiso

**Tablas afectadas al guardar permisos:**  
rol_permiso

**Uso de permisos en el sistema**  
* Al iniciar sesion, el sistema consulta los roles del usuario.
* El sistema consulta los permisos asociados a dichos roles.
* El sistema almacena los permisos en la sesion.
* Cada modulo valida el permiso requerido antes de mostrar la pantalla o ejecutar una accion.

**Tablas consultadas para permisos en sesion:**  
usuario_rol  
rol_permiso  
permisos  
roles

* **Flujo Alternativo**  
El sistema no permite cargar permisos si no se selecciona un rol.  
El sistema no permite modificar permisos si el usuario no posee autorizacion.  
El sistema puede guardar un rol sin permisos seleccionados, eliminando las asignaciones previas.  
El usuario puede cambiar de rol antes de guardar.

* **Post Condicion**  
El sistema emite un mensaje cuando los permisos se actualizan correctamente.  
Los usuarios con el rol modificado tendran acceso segun los permisos asignados en su proximo uso de sesion.  
Las pantallas y acciones del sistema quedan controladas por las claves de permiso almacenadas en sesion.

* **Tablas involucradas en el modulo**  
roles  
permisos  
rol_permiso  
usuario_rol

---

## Login

* **Nombre de Caso de Uso**  
Iniciar y cerrar sesion.

* **Descripcion Basica**  
Este caso de uso se ocupa del acceso del usuario al sistema mediante usuario y contrasena. Al iniciar sesion, el sistema carga los datos del usuario, su sucursal, roles y permisos. Tambien permite cerrar la sesion de forma segura.

* **Actores relacionados**  
Usuario del sistema.  
Administrador del sistema.

* **Pre Condicion**  
El usuario debe estar registrado en el sistema.  
El usuario debe estar activo.  
El usuario debe ingresar un nombre de usuario y contrasena validos.

* **Flujo de eventos**

**Flujo Basico:**

**Iniciar Sesion**  
* El usuario accede a la pantalla de login.
* El sistema muestra los campos usuario y contrasena.
* El usuario ingresa sus credenciales.
* El usuario presiona Iniciar sesion.
* El sistema valida que los campos no esten vacios.
* El sistema valida el formato del usuario.
* El sistema valida el formato de la contrasena.
* El sistema encripta la contrasena ingresada.
* El sistema consulta el usuario activo con el usuario y contrasena ingresados.
* Si las credenciales son correctas, el sistema inicia la sesion.
* El sistema guarda en sesion el id, nombre, apellido, usuario y sucursal.
* El sistema consulta los roles asociados al usuario.
* El sistema consulta los permisos asociados a los roles del usuario.
* El sistema guarda roles y permisos en sesion.
* El sistema consulta el nombre de la empresa.
* El sistema genera un token de sesion.
* El sistema redirecciona al usuario a la pantalla principal.

**Tablas consultadas al iniciar sesion:**  
usuarios  
usuario_rol  
roles  
rol_permiso  
permisos  
empresa

**Cerrar Sesion**  
* El usuario solicita cerrar sesion.
* El sistema recibe el token y el usuario.
* El sistema desencripta y valida los datos recibidos.
* El sistema compara el token recibido con el token almacenado en sesion.
* El sistema compara el usuario recibido con el usuario almacenado en sesion.
* Si los datos coinciden, el sistema destruye la sesion.
* El sistema redirecciona al login.

**Tablas consultadas al cerrar sesion:**  
No consulta tablas.

**Forzar Cierre de Sesion**  
* Si el usuario no tiene una sesion valida o accede indebidamente, el sistema limpia la sesion.
* El sistema destruye la sesion.
* El sistema redirecciona al login.

**Tablas consultadas al forzar cierre:**  
No consulta tablas.

* **Flujo Alternativo**  
El sistema no permite iniciar sesion con campos vacios.  
El sistema no permite iniciar sesion con formato invalido de usuario o contrasena.  
El sistema no permite iniciar sesion si el usuario no existe.  
El sistema no permite iniciar sesion si la contrasena no coincide.  
El sistema no permite iniciar sesion si el usuario esta inactivo.  
El sistema muestra un mensaje si no puede cerrar la sesion por token o usuario invalido.

* **Post Condicion**  
El sistema permite el acceso al usuario autenticado.  
El sistema mantiene en sesion los datos del usuario, sucursal, roles y permisos.  
El sistema restringe pantallas y acciones segun los permisos cargados.  
Al cerrar sesion, el sistema elimina los datos de sesion y redirige al login.

* **Tablas involucradas en el modulo**  
usuarios  
usuario_rol  
roles  
rol_permiso  
permisos  
empresa
