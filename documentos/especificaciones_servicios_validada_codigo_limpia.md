Nombre de Caso de Uso
Registrar Solicitud de servicios

Descripción Básica
Este caso se ocupa del registro de la recepción o solicitud de servicio de un vehículo. El sistema permite buscar y seleccionar clientes y vehículos mediante campos de autocompletado, registrar rápidamente un cliente o vehículo cuando no existe, cargar el estado del vehículo al ingreso, indicar el tipo de servicio solicitado, adjuntar fotos y guardar la recepción. También permite generar la recepción desde un reclamo existente.

El registro rápido de cliente y vehículo permite cargar únicamente los datos obligatorios para agilizar la recepción, o completar datos adicionales si el usuario los posee.

Actores relacionados
Personal de Recepción

Pre Condición
El usuario debe estar autenticado en el sistema.
El usuario debe tener permisos para acceder y operar el módulo de recepción de servicios, según la acción que realice.
Debe existir una sucursal asociada al usuario.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema mediante logueo.
El usuario ingresa al módulo Nueva Recepción.
El sistema valida que el usuario tenga permiso para registrar solicitudes de servicio.
El sistema muestra el formulario de solicitud de servicio.

Nuevo
Flujo normal.
El usuario busca un cliente escribiendo al menos cuatro caracteres del nombre, apellido o documento.
El sistema muestra los clientes activos encontrados. Tablas consultadas para buscar cliente: clientes.
El usuario selecciona el cliente.
El sistema carga el ID del cliente en el formulario.
El sistema limpia el resultado de búsqueda de cliente y reinicia la selección de vehículo.

Registrar Cliente Rápido
Si el cliente no existe, el usuario puede presionar el botón de nuevo cliente.
El usuario puede ingresar tipo de documento, documento, DV, nombre, apellido, teléfono, email, dirección, ciudad y estado civil.
Los campos obligatorios son documento y nombre.
La ciudad se busca escribiendo al menos dos caracteres y seleccionando una opción del listado.
Si no se selecciona ciudad, el sistema puede registrar el cliente usando una ciudad activa predeterminada.
El sistema valida los datos obligatorios del cliente.
El sistema valida que el usuario tenga permiso para registrar clientes.
El sistema valida que el documento no exista previamente. Tablas consultadas para nuevo cliente: clientes, ciudades.
El sistema registra el nuevo cliente.
El sistema selecciona automáticamente el cliente registrado.

Seleccionar Vehículo
El usuario busca un vehículo escribiendo al menos cuatro caracteres de la placa o modelo.
El sistema exige que primero se haya seleccionado un cliente.
El sistema busca vehículos activos asociados al cliente seleccionado. Tablas consultadas para buscar vehículo: vehiculos, modelo_auto, marcas.
El usuario selecciona el vehículo.
El sistema carga el ID del vehículo en el formulario.
El sistema limpia el resultado de búsqueda de vehículo.

Registrar Vehículo Rápido
Si el vehículo no existe, el usuario puede presionar el botón de nuevo vehículo.
El usuario puede ingresar modelo, color, placa, año, versión, transmisión, motor y tipo de vehículo.
Los campos obligatorios son modelo, color y placa.
El modelo se busca escribiendo al menos dos caracteres y seleccionando una opción del listado.
El sistema valida que el usuario tenga permiso para registrar vehículos.
El sistema valida cliente, modelo, color y placa.
El sistema valida que la placa no exista previamente. Tablas consultadas para nuevo vehículo: vehiculos, modelo_auto.
El sistema registra el vehículo en vehiculos.
El sistema selecciona automáticamente el vehículo registrado.

Flujo con reclamo.
El usuario puede presionar Servicio proveniente de reclamo.
El sistema marca la recepción como proveniente de reclamo.
El sistema muestra el buscador de reclamos.
El sistema bloquea la edición de cliente, vehículo, nuevo cliente y nuevo vehículo.
El usuario busca un reclamo por cliente, documento, placa o número de reclamo.
El sistema busca reclamos activos pertenecientes a la sucursal actual del usuario. Tablas consultadas para buscar reclamos: reclamo_servicio, clientes, vehiculos, modelo_auto, marcas.
El usuario selecciona un reclamo.
El sistema obtiene el detalle del reclamo.
El sistema muestra los datos principales del reclamo: número, fecha, tipo, garantía, cliente, vehículo, descripción y prioridad.
El sistema carga automáticamente cliente y vehículo del reclamo.
El sistema muestra el detalle del reclamo.
El usuario completa estado de ingreso del vehículo.
El usuario ingresa el kilometraje actual del vehículo.
El usuario completa datos del servicio solicitado.
El usuario selecciona accesorios entregados.
El usuario puede adjuntar imágenes del vehículo.
El usuario ingresa la observación o reclamo del cliente.

Guardar
El usuario presiona Guardar.
El sistema emite mensaje de confirmación.
El usuario confirma acción.
El sistema valida que el usuario tenga permiso para registrar solicitudes de servicio.
El sistema valida que se haya seleccionado cliente.
El sistema valida que se haya seleccionado vehículo.
El sistema valida kilometraje.
El sistema valida observación.
Si la recepción proviene de reclamo, el sistema valida que exista un reclamo seleccionado.
Si el reclamo requiere garantía, el sistema consulta el kilometraje de salida del servicio original.
Si el servicio original no tiene kilometraje de salida registrado, el sistema no permite registrar la recepción.
El sistema registra la solicitud en recepcion_servicio.
Si la recepción proviene de un reclamo, el sistema actualiza el reclamo en la tabla reclamo_servicio.
Si existen fotos adjuntas, el sistema guarda los archivos y registra sus rutas en recepcion_fotos.
El sistema emite mensaje de recepción registrada correctamente.

Anular
El usuario ingresa a Buscar Recepción.
El sistema permite filtrar por fecha desde, fecha hasta, número, cliente, documento, placa, estado, origen, tipo de servicio, prioridad y usuario.
El sistema consulta las recepciones de la sucursal actual.
El sistema muestra fecha, cliente, CI/RUC, vehículo, kilometraje, servicio, origen, prioridad, fotos, usuario y estado. Tablas consultadas para listar recepciones: recepcion_servicio, clientes, vehiculos, modelo_auto, marcas, usuarios, recepcion_fotos.
Ver Fotos
Si la recepción tiene fotos, el sistema muestra un botón con la cantidad de imágenes.
El usuario presiona el botón.
El sistema consulta las fotos de la recepción. Tablas consultadas para fotos: recepcion_fotos, recepcion_servicio.
El sistema muestra la galería de imágenes.
El usuario presiona Anular desde el listado.
El sistema emite mensaje de confirmación.
El usuario confirma acción.
El sistema valida que el usuario tenga permiso para anular recepciones de servicio.
El sistema valida el ID de la recepción.
El sistema busca la recepción activa de la sucursal.
El sistema cambia el estado de la recepción a Anulado.
Si la recepción provenía de un reclamo, el sistema devuelve el reclamo a estado activo en la tabla reclamo_servicio.
El sistema emite mensaje de recepción anulada correctamente.

Flujo Alternativo:
Si el usuario no tiene permiso para la acción solicitada, el sistema muestra Acceso no autorizado.
Si el usuario intenta registrar cliente rápido sin permiso, el sistema no permite la operación.
Si el usuario intenta registrar vehículo rápido sin permiso, el sistema no permite la operación.
Si el usuario intenta anular una recepción sin permiso, el sistema no permite la operación.
Si no selecciona cliente, el sistema no permite guardar.
Si no selecciona vehículo, el sistema no permite guardar.
Si no carga kilometraje u observación, el sistema muestra datos incompletos.
Si intenta registrar un cliente con documento existente, el sistema muestra advertencia.
Si intenta registrar un vehículo con placa existente, el sistema muestra advertencia.
Si intenta guardar un vehículo rápido sin seleccionar un modelo del listado, el sistema muestra advertencia.
Si intenta buscar o registrar un vehículo sin cliente seleccionado, el sistema solicita seleccionar un cliente.
Si la búsqueda de cliente, vehículo, ciudad o modelo no encuentra resultados, el sistema informa que no existen coincidencias.
Si la recepción viene de reclamo y no se selecciona reclamo, el sistema muestra error.
Si el reclamo requiere garantía y el servicio original no tiene kilometraje de salida, el sistema cancela la recepción.
Si el kilometraje actual supera el kilometraje de salida del servicio original más el límite permitido de garantía, el sistema no permite registrar la recepción e informa que la garantía está vencida por kilometraje.
Si el reclamo ya no está disponible, el sistema cancela la recepción.
Si la recepción no está activa, no se permite anular.
El usuario puede presionar Cancelar, y el sistema limpia el formulario.

Post Condición
La solicitud queda registrada en recepcion_servicio.
La recepción queda en estado Recepcionado.
Las fotos quedan registradas en recepcion_fotos, si fueron adjuntadas.
Si proviene de reclamo, el reclamo queda marcado como no disponible para una nueva recepción mientras el proceso continúa.
Si se anula, la recepción queda en estado Anulado y el reclamo vuelve a estar disponible.

Descripción de las tablas
Nombre	Alias	Base de Datos
recepcion_servicio	recepcion_servicio	Bd_reduc
recepcion_fotos	recepcion_fotos	Bd_reduc
clientes	clientes	Bd_reduc
vehiculos	vehiculos	Bd_reduc
modelo_auto	modelo_auto	Bd_reduc
marcas	marcas	Bd_reduc
ciudades	ciudades	Bd_reduc
usuarios	usuarios	Bd_reduc
reclamo_servicio	reclamo_servicio	Bd_reduc
registro_servicio	registro_servicio	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia

Agregar

Anular

Nombre de Caso de Uso
Registrar Diagnóstico de Servicio.

Descripción Básica
Este caso se ocupa del registro del diagnóstico técnico de una recepción de servicio. El sistema permite buscar una recepción disponible, visualizar sus datos, seleccionar un equipo de trabajo, cargar observaciones y registrar uno o más detalles del diagnóstico. Los detalles pueden indicar trabajos o servicios, repuestos, cantidades, origen del repuesto, problema y gravedad. Si la recepción proviene de un reclamo, el sistema permite indicar si el reclamo es válido, si corresponde garantía y si requiere cobro.

Actores relacionados
Encargado de dto. de Servicios.

Pre Condición
El usuario debe estar autenticado en el sistema.
El usuario debe tener permisos para operar el módulo de diagnóstico de servicio.
Debe existir una recepción de servicio disponible para diagnóstico.
Debe existir un equipo de trabajo activo en la sucursal.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema mediante logueo.
El usuario ingresa al módulo Diagnóstico de Servicio.
El sistema muestra las opciones Nuevo Diagnóstico y Buscar Diagnósticos.

Nuevo
El usuario busca una recepción escribiendo cliente, documento, placa, marca o modelo.
El sistema busca recepciones disponibles para diagnóstico. Tablas consultadas para buscar recepción: recepcion_servicio, clientes, vehiculos, modelo_auto, marcas, reclamo_servicio.
El sistema muestra las recepciones encontradas.
El usuario selecciona una recepción.
El sistema consulta el detalle de la recepción seleccionada.
El sistema muestra los datos de recepción, cliente y vehículo.
Si la recepción proviene de un reclamo, el sistema muestra una alerta y habilita el bloque de resultado del reclamo.
El sistema consulta el detalle del reclamo. Tablas consultadas: reclamo_servicio, reclamo_servicio_detalle, registro_servicio_detalle, articulos.
El usuario indica si el reclamo es válido, si corresponde garantía y si requiere cobro.
El sistema carga los equipos de trabajo activos de la sucursal. Tabla consultada para equipos de trabajo: equipo_trabajo.
El usuario selecciona el equipo de trabajo.
El usuario ingresa una observación general.
El usuario agrega uno o más detalles del diagnóstico.
Por cada detalle, el usuario puede seleccionar trabajo o servicio, repuesto, cantidad, origen del repuesto, problema y gravedad.
El usuario puede eliminar detalles antes de guardar.

Guardar
El usuario presiona Guardar.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos de usuario.
El sistema valida que exista una recepción seleccionada.
El sistema valida que exista equipo de trabajo seleccionado.
El sistema valida que exista sucursal asociada.
El sistema valida que exista al menos un detalle del diagnóstico.
El sistema valida que la recepción siga disponible para diagnóstico.
El sistema registra la cabecera del diagnóstico en diagnostico_servicio.
El sistema registra los detalles técnicos en diagnostico_detalle.
El sistema actualiza la recepción en recepcion_servicio, dejándola en estado En proceso.
El sistema emite mensaje de diagnóstico registrado correctamente.
Si la recepción proviene de reclamo, el sistema evalúa el resultado del reclamo cargado en el diagnóstico.
Si el reclamo fue marcado como válido, corresponde garantía, no requiere cobro, pertenece a la sucursal del usuario, está dentro de las condiciones de garantía y no tiene una OT activa, el sistema devuelve una acción posterior para generar una orden de trabajo por reclamo.
El sistema muestra una alerta consultando si el usuario desea generar la orden de trabajo ahora.
Si el usuario confirma, el sistema valida que tenga permisos necesarios.
El sistema valida que el reclamo exista, pertenezca a la sucursal del usuario, esté en proceso y no tenga una OT activa.
El sistema registra la orden de trabajo con origen reclamo, sin presupuesto asociado y en estado pendiente de completar.
El sistema actualiza el diagnóstico asociado al reclamo como finalizado para el flujo.

Anular
El usuario ingresa a Buscar Diagnósticos.
El sistema permite filtrar por búsqueda general, número de diagnóstico, número de recepción, fecha inicio, fecha fin, cliente, placa, estado y origen.
El sistema consulta los diagnósticos de la sucursal actual.
Tablas consultadas para listar diagnósticos: diagnostico_servicio, recepcion_servicio, clientes, vehiculos, modelo_auto, marcas, usuarios, equipo_trabajo, orden_trabajo.
El sistema muestra los diagnósticos encontrados.
El usuario puede presionar el botón de detalle.
El sistema consulta la cabecera del diagnóstico. Tablas consultadas para cabecera del detalle: diagnostico_servicio, recepcion_servicio, clientes, vehiculos, modelo_auto, marcas, equipo_trabajo, usuarios.
El sistema consulta los detalles técnicos del diagnóstico. Tabla consultada para detalle técnico: diagnostico_detalle.
El sistema muestra datos de recepción, cliente, vehículo, equipo, observaciones y detalles técnicos cargados.
El usuario presiona Anular.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos de usuario.
El sistema valida el ID del diagnóstico.
El sistema verifica que el diagnóstico exista.
El sistema verifica que el diagnóstico no esté anulado.
El sistema verifica que el diagnóstico no tenga presupuesto de servicio activo asociado.
El sistema verifica si el diagnóstico pertenece a una recepción proveniente de reclamo.
Si tiene reclamo asociado, el sistema valida que no exista una OT activa para ese reclamo. Tablas consultadas para anular: diagnostico_servicio, recepcion_servicio, presupuesto_servicio, orden_trabajo.
Si las validaciones son correctas, el sistema actualiza el estado en diagnostico_servicio, dejando el diagnóstico anulado.
El sistema actualiza la recepción asociada en recepcion_servicio, dejándola disponible nuevamente.
El sistema emite mensaje de anulación correcta.

Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si la búsqueda de recepción tiene menos de tres caracteres, el sistema solicita ingresar más datos.
Si no se encuentra recepción, el sistema muestra mensaje informativo.
Si la recepción ya no está disponible, el sistema no permite guardar el diagnóstico.
Si no se selecciona recepción, el sistema muestra datos incompletos.
Si no se selecciona equipo de trabajo, el sistema muestra error.
Si no existe sucursal asociada, el sistema muestra error.
Si no se agrega al menos un detalle, el sistema no permite guardar.
Si falla el registro de la cabecera o de los detalles, el sistema cancela la operación.
Si falla la actualización de la recepción, el sistema cancela la operación.
Si la recepción no proviene de reclamo, el sistema registra el diagnóstico y no consulta por OT directa.
Si el reclamo se marca como no válido, el sistema registra el diagnóstico y no genera OT.
Si el reclamo es válido pero no corresponde garantía o requiere cobro, el sistema no genera OT directa y el diagnóstico queda disponible para presupuesto.
Si el usuario responde que no desea generar la OT ahora, el diagnóstico queda registrado.
Si el usuario no tiene permiso para generar órdenes de trabajo, el sistema muestra acceso denegado al intentar generar la OT.
Si el reclamo no existe, no está en proceso o no pertenece a la sucursal del usuario, el sistema cancela la generación de OT.
Si el reclamo ya tiene una OT activa, el sistema no permite duplicar la orden.
Si el diagnóstico ya tiene presupuesto activo, el sistema no permite anularlo.
Si el diagnóstico ya está anulado, el sistema muestra error.
Si el diagnóstico tiene una OT activa generada por reclamo, el sistema no permite anularlo sin anular primero la OT asociada.
El usuario puede presionar Cancelar, y el sistema limpia el formulario.

Post Condición
El diagnóstico queda registrado en diagnostico_servicio.
Los detalles quedan registrados en diagnostico_detalle.
La recepción asociada pasa a estado En proceso.
El diagnóstico queda disponible para presupuesto de servicio u orden de trabajo, según corresponda.
Si corresponde OT directa por reclamo y el usuario confirma la generación, queda registrada una orden de trabajo con origen reclamo.
Si el reclamo no es válido, no corresponde garantía, requiere cobro o el usuario no confirma, no se crea OT directa desde el diagnóstico.
Si se anula, el diagnóstico queda en estado Anulado y la recepción vuelve a estado Recepcionado.

Descripción de las tablas
Nombre	Alias	Base de Datos
diagnostico_servicio	diagnostico_servicio	Bd_reduc
diagnostico_detalle	diagnostico_detalle	Bd_reduc
recepcion_servicio	recepcion_servicio	Bd_reduc
clientes	clientes	Bd_reduc
vehiculos	vehiculos	Bd_reduc
modelo_auto	modelo_auto	Bd_reduc
marcas	marcas	Bd_reduc
usuarios	usuarios	Bd_reduc
equipo_trabajo	equipo_trabajo	Bd_reduc
reclamo_servicio	reclamo_servicio	Bd_reduc
reclamo_servicio_detalle	reclamo_servicio_detalle	Bd_reduc
registro_servicio	registro_servicio	Bd_reduc
registro_servicio_detalle	registro_servicio_detalle	Bd_reduc
orden_trabajo	orden_trabajo	Bd_reduc
presupuesto_servicio	presupuesto_servicio	Bd_reduc
articulos	articulos	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clase.

Nombre de Caso de Uso
Registrar Promociones.

Descripción Básica
Permite registrar, listar, buscar, editar y cambiar el estado de promociones aplicables a artículos. Las promociones pueden ser por porcentaje, monto fijo o precio fijo, tienen rango de vigencia, pueden aplicar a todas las sucursales o a una sucursal específica, y pueden asociarse a uno o más artículos.

Las promociones activas y vigentes son consideradas al armar presupuestos de servicio cuando el artículo presupuestado está asociado a la promoción.

Actores relacionados
Encargado de dto. de Servicios

Pre Condición
El usuario debe tener sesión activa.
El usuario debe tener permisos para operar promociones.
Deben existir artículos activos para asociarlos a una promoción.
Para seleccionar sucursal, deben existir sucursales activas.

Flujo de eventos
Flujo Básico:
El usuario ingresa al menú de Promociones desde el módulo de servicios.
El sistema muestra las opciones Nueva Promoción y Lista de Promociones.
El usuario puede registrar una nueva promoción, consultar promociones existentes, editar una promoción o cambiar su estado.

Nuevo
El sistema muestra el formulario con los campos nombre, tipo, valor, descripción, fecha inicio, fecha fin, sucursal, estado y artículos asociados.
El usuario completa los campos requeridos.
El usuario selecciona una sucursal o deja la promoción disponible para todas.
El sistema carga las sucursales activas. Tabla consultada para sucursales: sucursales.
El usuario busca artículos por código o descripción.
El sistema consulta artículos activos. Tabla consultada para buscar artículos: articulos.
El sistema muestra los artículos encontrados.
El usuario agrega uno o más artículos a la promoción.
El usuario puede quitar artículos antes de guardar.

Guardar
El usuario presiona Guardar.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para crear promociones.
El sistema valida que existan nombre, tipo, valor, fecha inicio y fecha fin.
El sistema valida que el tipo de promoción sea válido.
El sistema valida que el valor sea mayor a cero.
Si el tipo es porcentaje, el sistema valida que no supere el cien por ciento.
El sistema valida que la fecha de inicio no sea mayor a la fecha fin.
El sistema registra la cabecera de la promoción en promociones.
Si fueron seleccionados, el sistema registra los artículos asociados en promocion_producto.
El sistema guarda la promoción en estado Activa.
El sistema emite mensaje de registro correcto.

Editar
El usuario ingresa a Lista de Promociones.
El sistema permite filtrar por texto, estado, sucursal y vigencia.
El sistema consulta las promociones registradas. Tablas consultadas para listar promociones: promociones, usuarios, sucursales.
El sistema muestra nombre, tipo, valor, vigencia, sucursal, usuario creador y estado.
El usuario presiona Editar.
El sistema valida permiso de edición.
El sistema carga los datos de la promoción y sus artículos asociados. Tablas consultadas: promociones, promocion_producto, articulos.
El sistema muestra los datos registrados.
El usuario modifica los datos permitidos.
El usuario puede agregar o quitar artículos asociados.
El usuario guarda los cambios.
El sistema valida permisos para editar promociones.
El sistema valida los mismos datos requeridos que en el registro.
El sistema actualiza la promoción. Tabla afectada: promociones.
El sistema elimina la relación anterior de artículos asociados y registra la relación vigente. Tabla afectada: promocion_producto.
El sistema emite mensaje de actualización correcta.

Cambiar Estado
El usuario ingresa a Lista de Promociones.
El sistema muestra el estado actual de cada promoción.
El usuario presiona Activar o Desactivar.
El sistema actualiza el estado de la promoción. Tabla afectada: promociones.
El sistema emite mensaje de operación correcta.

Flujo Alternativo:
Si el usuario no tiene permiso para acceder, crear o editar promociones, el sistema muestra acceso no autorizado.
Si faltan datos obligatorios al registrar, el sistema muestra datos incompletos.
Si el tipo de promoción no es válido, el sistema muestra datos inválidos.
Si el valor es menor o igual a cero, el sistema no permite guardar.
Si la promoción es por porcentaje y el valor supera 100, el sistema no permite guardar.
Si la fecha de inicio es mayor a la fecha fin, el sistema no permite guardar.
Si no se seleccionan artículos, la promoción se registra, pero no se aplicará a productos hasta asociarlos.
Si no se encuentran artículos en la búsqueda, el sistema muestra mensaje informativo.
Si la promoción no existe al abrir la edición, el sistema muestra promoción no encontrada.
Si falla el registro o actualización, el sistema muestra error.

Post Condición
La promoción queda registrada en promociones.
Los artículos asociados quedan registrados en promocion_producto cuando fueron seleccionados.
La promoción queda activa al registrarse.
Si se edita, quedan actualizados los datos de la promoción y sus artículos asociados.
Si se cambia el estado, la promoción queda activa o inactiva según corresponda.
Las promociones activas, vigentes y asociadas a artículos quedan disponibles para aplicarse en presupuestos de servicio.

Descripción de las tablas
Nombre	Alias	Base de Datos
promociones	promociones	Bd_reduc
promocion_producto	promocion_producto	Bd_reduc
articulos	articulos	Bd_reduc
sucursales	sucursales	Bd_reduc
usuarios	usuarios	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia

Agregar

Anular

Nombre de Caso de Uso
Registrar y Gestionar Descuentos.

Descripción Básica
Este caso permite registrar, listar, buscar y editar descuentos aplicables a presupuestos de servicio. Los descuentos pueden ser por porcentaje o por monto fijo, pueden tener rango de vigencia, estado activo o inactivo, alcance de aplicación, sucursal específica o todas las sucursales, y pueden asociarse a clientes.

Los descuentos activos, vigentes, reutilizables y asociados al cliente quedan disponibles para aplicarse en presupuestos de servicio.

Actores relacionados
Encargado de dto. de Servicios

Pre Condición
El usuario debe tener sesión activa.
El usuario debe tener permisos para operar descuentos.
Deben existir clientes activos si el descuento será asignado a clientes específicos.
Para seleccionar sucursal, deben existir sucursales activas.

Flujo de eventos
Flujo Básico:
El usuario ingresa al módulo de Descuentos.
El sistema muestra las opciones Nuevo Descuento y Lista de Descuentos.
El usuario puede registrar, consultar o editar descuentos.

Nuevo
El sistema muestra el formulario con los campos nombre, descripción, tipo, valor, aplica a, fechas de vigencia, sucursal y clientes asociados cuando corresponda.
El sistema carga las sucursales activas. Tabla consultada para sucursales: sucursales.
El usuario completa los campos requeridos.
El usuario puede seleccionar una sucursal o dejar el descuento disponible para todas.
El usuario define si el descuento queda activo o inactivo.
El usuario selecciona el alcance del descuento: total, producto o categoría.
Si el descuento será aplicado a clientes, el usuario busca clientes por documento, nombre o apellido.
El sistema consulta clientes activos. Tabla consultada para buscar clientes: clientes.
El sistema muestra los clientes encontrados.
El usuario agrega uno o más clientes al descuento.
El usuario puede quitar clientes antes de guardar.

Guardar
El usuario presiona Guardar.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para crear descuentos.
El sistema valida nombre, tipo y valor.
El sistema valida que el tipo sea porcentaje o monto fijo.
El sistema valida que el valor sea mayor a cero.
Si el tipo es porcentaje, el sistema valida que no supere el cien por ciento.
El sistema valida que la fecha de inicio no sea mayor a la fecha fin.
Si el alcance no es válido, el sistema lo toma como descuento aplicado al total.
El sistema registra el descuento en descuentos.
Si fueron seleccionados, el sistema registra los clientes asociados en descuento_cliente.
El sistema emite mensaje de registro correcto.

Editar
El usuario ingresa a Lista de Descuentos.
El sistema permite filtrar por texto, estado, sucursal y vigencia.
El sistema consulta los descuentos registrados. Tablas consultadas para listar descuentos: descuentos, sucursales, usuarios.
El sistema muestra nombre, tipo, valor, aplica a, vigencia, sucursal, usuario creador, usuario modificador y estado.
El usuario presiona Editar.
El sistema valida permisos para editar descuentos o para asignar clientes, según la operación.
El sistema carga los datos del descuento y los clientes asociados.
El sistema consulta los datos del descuento. Tabla consultada para datos del descuento: descuentos.
El sistema consulta los clientes asignados. Tablas consultadas para clientes asignados: descuento_cliente, clientes.
El sistema muestra los datos registrados.
El usuario modifica nombre, descripción, tipo, valor, alcance, vigencia, sucursal, estado o clientes asignados.
El usuario puede agregar nuevos clientes al descuento.
El usuario puede eliminar clientes ya asociados al descuento.
Si elimina un cliente asociado, el sistema valida los datos recibidos y elimina la relación en descuento_cliente.
El usuario guarda los cambios.
El sistema valida permisos para editar descuentos.
El sistema valida tipo, valor, alcance y vigencia.
El sistema actualiza el descuento. Tabla afectada: descuentos.
Si se agregan clientes, el sistema registra las nuevas relaciones en descuento_cliente.
El sistema emite mensaje de actualización correcta.

Flujo Alternativo:
Si el usuario no tiene permiso para acceder, crear, editar o asignar clientes a descuentos, el sistema muestra acceso no autorizado.
Si faltan datos obligatorios al registrar, el sistema muestra datos incompletos.
Si el tipo de descuento no es válido, el sistema muestra datos inválidos.
Si el valor es menor o igual a cero, el sistema no permite guardar.
Si el descuento es por porcentaje y el valor supera 100, el sistema no permite guardar.
Si la fecha de inicio es mayor a la fecha fin, el sistema no permite guardar.
Si el valor de aplica a no es válido, el sistema lo toma como descuento aplicado al total.
Si no se seleccionan clientes, el descuento se registra o actualiza, pero no quedará asociado a clientes.
Si no se encuentran clientes en la búsqueda, el sistema muestra mensaje informativo.
Si el descuento no existe al abrir la edición, el sistema muestra descuento no encontrado.
Si los datos para eliminar un cliente asociado no son válidos, el sistema muestra error.
Si falla el registro o actualización, el sistema muestra error.

Post Condición
El descuento queda registrado en descuentos.
Si se edita, quedan actualizados los datos del descuento.
El estado del descuento queda activo o inactivo según el valor seleccionado en el formulario.
Los clientes asociados quedan registrados en descuento_cliente cuando fueron seleccionados.
Los clientes quitados del descuento dejan de estar asociados en descuento_cliente.
Los descuentos activos, vigentes, reutilizables y asociados al cliente quedan disponibles para aplicarse en presupuestos de servicio.

Descripción de las tablas
Nombre	Alias	Base de Datos
descuentos	descuentos	Bd_reduc
descuento_cliente	descuento_cliente	Bd_reduc
clientes	clientes	Bd_reduc
sucursales	sucursales	Bd_reduc
usuarios	usuarios	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia

Agregar

Anular

Nombre de Caso de Uso
Registrar Presupuesto

Descripción Básica
Este caso permite registrar, listar, aprobar, anular e imprimir presupuestos de servicio. El presupuesto puede generarse desde un diagnóstico disponible o como presupuesto preliminar para un cliente y vehículo. El sistema permite agregar servicios y repuestos, aplicar promociones vigentes, aplicar descuentos disponibles para el cliente y calcular subtotal, descuentos y total final.

Actores relacionados
Encargado de dto. de Servicios

Pre Condición
El usuario debe estar autenticado.
El usuario debe tener permisos para operar presupuestos de servicio.
Debe existir un diagnóstico disponible para presupuesto, o un cliente y vehículo seleccionados para presupuesto preliminar.
Deben existir artículos o servicios activos para agregar al detalle.
Si se agregan repuestos, debe existir stock disponible en la sucursal.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema mediante logueo.
El usuario ingresa al módulo Presupuesto de Servicio.
El sistema muestra las opciones Nuevo Presupuesto y Buscar Presupuestos.

Nuevo desde diagnóstico
El usuario busca un diagnóstico por cliente, documento, placa, marca, modelo o número.
El sistema busca diagnósticos disponibles para presupuesto. Tablas consultadas: diagnostico_servicio, diagnostico_detalle, recepcion_servicio, clientes, vehiculos, modelo_auto, marcas.
El usuario selecciona un diagnóstico.
El sistema carga los datos del diagnóstico, cliente, vehículo, kilometraje, observación y sucursal.
El sistema consulta el detalle técnico del diagnóstico. Tablas consultadas para detalle técnico: diagnostico_detalle, articulos, stock.
El sistema muestra el detalle técnico del diagnóstico con servicio, origen del repuesto, repuesto, cantidad, gravedad y problema.
El sistema arma el detalle inicial del presupuesto a partir del diagnóstico seleccionado.
Para el detalle inicial, el sistema incluye los servicios registrados en el diagnóstico.
Para el detalle inicial, el sistema incluye los repuestos del diagnóstico únicamente cuando el origen del repuesto corresponde al taller y el artículo es de tipo producto.
Si un mismo servicio o repuesto aparece más de una vez en el diagnóstico, el sistema acumula la cantidad en el detalle presupuestable.
El sistema toma el precio mostrado al usuario al momento de armar el presupuesto y consulta el stock disponible de los repuestos en la sucursal.
El sistema identifica si el presupuesto proviene de diagnóstico.
El sistema carga automáticamente los ítems presupuestables generados desde el diagnóstico.
El sistema evalúa promociones vigentes por cada artículo cargado desde el diagnóstico y conserva la promoción aplicada en el detalle del presupuesto. Tablas consultadas: promociones, promocion_producto.
El sistema consulta descuentos disponibles para el cliente asociado al diagnóstico. Tablas consultadas: descuentos, descuento_cliente.
El sistema puede buscar presupuestos preliminares existentes para el mismo cliente y vehículo. Tabla consultada: presupuesto_servicio.
El usuario puede reutilizar un presupuesto preliminar existente para revisión y conversión.
El usuario ingresa fecha de vencimiento.
El usuario puede agregar servicios o repuestos adicionales al detalle.
El sistema busca artículos o servicios activos. Tablas consultadas: articulos, stock, promociones, promocion_producto.
El sistema muestra stock disponible para productos.
El usuario agrega uno o más ítems al detalle.
El sistema evalúa promociones vigentes sobre los artículos agregados manualmente y conserva la promoción aplicada.
El usuario puede aplicar un descuento permitido.
El sistema calcula subtotal, promociones, descuentos y total final.

Nuevo preliminar
El usuario selecciona cliente y vehículo.
El sistema valida que el vehículo pertenezca al cliente seleccionado.
El sistema consulta descuentos disponibles para el cliente. Tablas consultadas: descuentos, descuento_cliente.
El sistema puede buscar presupuestos preliminares existentes para el cliente y vehículo. Tabla consultada: presupuesto_servicio.
El usuario puede reutilizar un presupuesto preliminar existente.
Si el usuario reutiliza un preliminar existente, el sistema carga su detalle. Tablas consultadas: presupuesto_servicio, presupuesto_detalleservicio, articulos, stock.
El usuario agrega servicios o repuestos al detalle.
El sistema busca artículos o servicios activos y muestra stock para productos. Tablas consultadas: articulos, stock.
El sistema evalúa promociones vigentes sobre los artículos agregados y conserva la promoción aplicada. Tablas consultadas: promociones, promocion_producto.
El sistema calcula los importes del presupuesto.

Guardar
El usuario presiona Guardar.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para registrar presupuestos.
El sistema valida fecha de vencimiento.
El sistema valida que la fecha de vencimiento tenga formato válido y no sea anterior a la fecha actual.
Si el presupuesto proviene de diagnóstico, el sistema valida que exista diagnóstico seleccionado.
Si el presupuesto es preliminar, el sistema valida cliente y vehículo.
Si el presupuesto proviene de diagnóstico, el sistema valida que el diagnóstico siga activo y disponible para presupuesto.
Si el presupuesto es preliminar, el sistema valida que el cliente y vehículo estén activos y relacionados.
El sistema valida que la sucursal del presupuesto corresponda a la sucursal del usuario.
Si se convierte un preliminar desde un diagnóstico, el sistema valida que el preliminar pertenezca al mismo cliente, vehículo y sucursal, y que esté disponible para conversión.
El sistema valida que exista al menos un detalle.
El sistema valida importes y cantidades.
El sistema valida que cada artículo del detalle exista y esté activo.
El sistema valida stock disponible para productos cuando corresponda.
El sistema conserva precios, promociones y descuentos aceptados por el usuario al armar el presupuesto.
El sistema recalcula subtotal, promociones, descuentos y total final en el servidor usando los importes aceptados en pantalla.
El sistema compara los totales calculados desde el detalle enviado con los totales enviados desde la pantalla.
El sistema registra la cabecera en presupuesto_servicio.
El sistema registra el detalle en presupuesto_detalleservicio.
El sistema registra promociones aplicadas en presupuesto_promocion cuando corresponda.
El sistema registra descuentos aplicados en presupuesto_descuento cuando corresponda.
Si proviene de diagnóstico, el sistema actualiza el diagnóstico como presupuestado.
Si proviene de diagnóstico y se convirtió desde un preliminar, el sistema marca el preliminar convertido.
El sistema emite mensaje de presupuesto guardado correctamente.

Aprobar
El usuario ingresa a Buscar Presupuestos.
El sistema permite filtrar y ordenar presupuestos.
El sistema muestra fecha, cliente, vehículo, total, estado y acciones.
El usuario presiona Aprobar.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para aprobar presupuestos.
El sistema valida que el presupuesto pertenezca a la sucursal del usuario.
El sistema valida que el presupuesto esté en estado pendiente.
El sistema actualiza el presupuesto como aprobado.
El sistema emite mensaje de aprobación correcta.

Anular
El usuario ingresa a Buscar Presupuestos.
El usuario presiona Anular sobre un presupuesto pendiente o aprobado.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para anular presupuestos.
El sistema valida el ID del presupuesto.
El sistema valida que el presupuesto pueda anularse.
El sistema valida que no tenga una orden de trabajo activa asociada.
El sistema anula el presupuesto.
Si el presupuesto provenía de diagnóstico, el sistema devuelve el diagnóstico a estado disponible para presupuesto cuando corresponda.
El sistema emite mensaje de anulación correcta.

Flujo Alternativo:
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si no se selecciona diagnóstico para presupuesto desde diagnóstico, el sistema muestra datos incompletos.
Si no se selecciona cliente o vehículo para presupuesto preliminar, el sistema muestra datos incompletos.
Si no se carga fecha de vencimiento, el sistema no permite guardar.
Si la fecha de vencimiento tiene formato inválido, el sistema no permite guardar.
Si la fecha de vencimiento es anterior a la fecha actual, el sistema no permite guardar.
Si no se agrega detalle, el sistema no permite guardar.
Si un artículo del detalle no existe o no está activo, el sistema cancela la operación.
Si un producto no tiene stock, el sistema no permite agregarlo o guardarlo según corresponda.
Si la cantidad de un producto supera el stock disponible, el sistema no permite guardar.
Si el diagnóstico no está disponible, el sistema no permite generar presupuesto.
Si el cliente y vehículo de un presupuesto preliminar no son válidos, el sistema no permite guardar.
Si el presupuesto preliminar seleccionado para conversión no está disponible, el sistema no permite convertirlo.
Si los importes enviados no coinciden con el detalle del presupuesto, el sistema solicita verificar nuevamente antes de guardar.
Si el presupuesto pertenece a otra sucursal, el sistema no permite aprobarlo.
Si el presupuesto ya tiene orden de trabajo activa, el sistema no permite anularlo.
El usuario puede presionar Cancelar, y el sistema limpia el formulario.

Post Condición
El presupuesto queda registrado en presupuesto_servicio.
El detalle queda registrado en presupuesto_detalleservicio.
Las promociones aplicadas quedan registradas en presupuesto_promocion cuando corresponda.
Los descuentos aplicados quedan registrados en presupuesto_descuento cuando corresponda.
Si proviene de diagnóstico, el diagnóstico queda marcado como presupuestado.
Si se convirtió desde un preliminar, el presupuesto preliminar queda marcado como convertido.
Si se aprueba, el presupuesto queda disponible para generar orden de trabajo.
Si se anula, el presupuesto queda en estado Anulado.
Si se anula y provenía de diagnóstico, el diagnóstico vuelve a estar disponible para presupuesto.

Descripción de las tablas
Nombre	Alias	Base de Datos
presupuesto_servicio	presupuesto_servicio	Bd_reduc
presupuesto_detalleservicio	presupuesto_detalleservicio	Bd_reduc
presupuesto_promocion	presupuesto_promocion	Bd_reduc
presupuesto_descuento	presupuesto_descuento	Bd_reduc
diagnostico_servicio	diagnostico_servicio	Bd_reduc
diagnostico_detalle	diagnostico_detalle	Bd_reduc
recepcion_servicio	recepcion_servicio	Bd_reduc
clientes	clientes	Bd_reduc
vehiculos	vehiculos	Bd_reduc
modelo_auto	modelo_auto	Bd_reduc
marcas	marcas	Bd_reduc
articulos	articulos	Bd_reduc
stock	stock	Bd_reduc
promociones	promociones	Bd_reduc
promocion_producto	promocion_producto	Bd_reduc
descuentos	descuentos	Bd_reduc
descuento_cliente	descuento_cliente	Bd_reduc
usuarios	usuarios	Bd_reduc
orden_trabajo	orden_trabajo	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia

Agregar

Anular

Nombre de Caso de Uso
Generar Orden de Trabajo.

Descripción Básica
Este caso permite generar una orden de trabajo a partir de un presupuesto aprobado o completar una orden de trabajo generada por reclamo. El sistema permite buscar presupuestos aprobados, seleccionar equipo de trabajo y técnico responsable, copiar el detalle del presupuesto a la orden de trabajo, completar trabajos y repuestos en órdenes provenientes de reclamo, y anular órdenes cuando corresponda.

Actores relacionados
Encargado de dto. de Servicios.

Pre Condición
El usuario debe estar autenticado.
El usuario debe tener permisos para operar órdenes de trabajo.
Debe existir un presupuesto aprobado sin orden de trabajo activa, o una orden de trabajo por reclamo pendiente de completar.
Debe existir equipo de trabajo activo.
Debe existir técnico activo asociado al equipo seleccionado.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema mediante logueo.
El usuario ingresa al módulo Orden de Trabajo.
El sistema muestra las opciones Nueva Orden de Trabajo y Buscar Órdenes de Trabajo.

Generar OT desde presupuesto
El usuario busca un presupuesto aprobado por cliente, vehículo o número.
El sistema busca presupuestos aprobados, vigentes, de la sucursal del usuario y sin OT activa. Tablas consultadas: presupuesto_servicio, clientes, vehiculos, modelo_auto, marcas, orden_trabajo.
El sistema muestra los presupuestos encontrados.
El usuario selecciona un presupuesto.
El sistema muestra datos del presupuesto, cliente, vehículo, fecha, subtotal, descuento y total.
El sistema consulta el detalle del presupuesto. Tablas consultadas para detalle de presupuesto: presupuesto_detalleservicio, articulos.
El sistema muestra los servicios o artículos presupuestados.
El usuario selecciona equipo de trabajo.
El sistema carga técnicos activos pertenecientes al equipo. Tablas consultadas para técnicos: equipo_empleado, empleados.
El usuario selecciona técnico responsable.
El usuario puede ingresar observación.

Guardar
El usuario presiona Generar OT.
El sistema emite mensaje de confirmación.
El usuario confirma acción.
El sistema valida permisos para generar OT.
El sistema valida que exista presupuesto seleccionado.
El sistema valida que exista equipo y técnico responsable.
El sistema valida que el presupuesto esté aprobado.
El sistema valida que el presupuesto pertenezca a la sucursal del usuario.
El sistema valida que el presupuesto no tenga una OT activa.
El sistema valida que el presupuesto provenga de un diagnóstico; si es preliminar, debe haberse convertido a presupuesto con diagnóstico antes de generar la OT.
El sistema registra la cabecera en orden_trabajo.
El sistema copia el detalle del presupuesto a orden_trabajo_detalle.
El sistema actualiza el presupuesto a estado OT generada.
El sistema emite mensaje de OT generada correctamente.

Completar OT por Reclamo
El usuario realiza la búsqueda de OT.
El sistema permite filtrar por fecha inicial, fecha final y estado.
El sistema consulta las órdenes de trabajo registradas. Tablas consultadas para listar OT: orden_trabajo, presupuesto_servicio, diagnostico_servicio, recepcion_servicio, clientes, vehiculos, modelo_auto, usuarios.
El sistema muestra cliente, vehículo, fecha, presupuesto u origen, usuario creador y estado.
Si la OT proviene de reclamo y está pendiente de completar, el sistema muestra la opción Completar OT.
El usuario presiona Completar OT.
El sistema valida que el usuario tenga permiso para completar OT por reclamo.
El sistema consulta la OT seleccionada.
El sistema valida que la OT exista, provenga de reclamo y se encuentre en estado Pendiente de completar.
El sistema consulta los datos necesarios para completar la OT. Tablas consultadas para completar OT: orden_trabajo, reclamo_servicio, recepcion_servicio, clientes, vehiculos, modelo_auto, diagnostico_servicio, equipo_trabajo, equipo_empleado, empleados.
El sistema muestra número de OT, fecha, estado pendiente, origen reclamo y acceso para volver al listado.
El sistema muestra información del vehículo: cliente, vehículo y kilometraje.
El sistema muestra el diagnóstico asociado al reclamo: número de diagnóstico, fecha, garantía, reclamo válido, requiere cobro y observación.
El sistema consulta y muestra el detalle técnico del diagnóstico. Tablas consultadas para detalle técnico: diagnostico_detalle, articulos.
El sistema precarga como trabajos los servicios cargados en el detalle del diagnóstico.
El sistema precarga como repuestos los artículos de repuesto cargados en el detalle del diagnóstico, con su cantidad.
El sistema carga los equipos de trabajo activos de la sucursal. Tabla consultada para equipos: equipo_trabajo.
El usuario selecciona un equipo de trabajo.
El sistema carga los técnicos activos pertenecientes al equipo seleccionado. Tablas consultadas para técnicos del equipo: equipo_empleado, empleados.
El usuario puede conservar, quitar o agregar trabajos precargados.
El usuario busca trabajos o servicios. Tabla consultada: articulos.
El sistema muestra servicios activos encontrados.
El usuario agrega uno o más trabajos realizados.
El sistema evita agregar el mismo servicio más de una vez desde la pantalla.
El usuario puede conservar, quitar o agregar repuestos precargados.
El usuario busca repuestos. Tablas consultadas: articulos, stock.
El sistema muestra productos activos con stock disponible en la sucursal.
El usuario agrega uno o más repuestos e indica cantidad.
Si el repuesto ya existe en el detalle, el sistema acumula la cantidad.
El usuario presiona Guardar.
El sistema valida permisos de usuario.
El sistema valida datos obligatorios: OT, equipo y técnico.
El sistema valida que el detalle de trabajos y repuestos tenga formato válido.
El sistema valida que exista al menos un trabajo o repuesto en el detalle confirmado.
El sistema valida existencia de la OT, sucursal, origen reclamo y estado pendiente de completar.
Si existen repuestos, el sistema consulta stock por artículo y sucursal.
El sistema valida stock suficiente por cada repuesto.
El sistema elimina el detalle anterior de la OT.
El sistema registra los repuestos confirmados en orden_trabajo_detalle con cantidad, precio unitario cero y subtotal cero.
El sistema registra los trabajos confirmados en orden_trabajo_detalle con cantidad uno, precio unitario cero y subtotal cero.
El sistema actualiza orden_trabajo con equipo, técnico, observación y estado Activa.
El sistema emite mensaje de OT completada correctamente y redirige al listado de OT.

Anular
El usuario realiza la búsqueda de OT.
El sistema permite filtrar por fecha inicial, fecha final y estado.
El sistema consulta las órdenes de trabajo registradas.
Si la OT está activa o pendiente de completar, el sistema muestra la opción Anular.
El usuario presiona Anular.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos de usuario.
El sistema verifica que la OT exista, pertenezca a la sucursal del usuario y se encuentre en estado permitido para anular. Tabla consultada: orden_trabajo.
El sistema valida que la OT no tenga servicio registrado activo. Tabla consultada: registro_servicio.
Si las validaciones son correctas, el sistema anula la OT. Tabla actualizada: orden_trabajo.
Si la OT proviene de presupuesto, el sistema devuelve el presupuesto a estado Aprobado. Tabla actualizada: presupuesto_servicio.
Si la OT proviene de reclamo, el sistema no modifica el estado del reclamo y deja el diagnóstico asociado nuevamente disponible para continuar el flujo del reclamo. Tabla actualizada: diagnostico_servicio.
El sistema emite mensaje de OT anulada correctamente.

Flujo Alternativo:
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si no se selecciona presupuesto, el sistema muestra datos incompletos.
Si no se selecciona equipo de trabajo, el sistema muestra datos incompletos.
Si no se selecciona técnico responsable, el sistema muestra datos incompletos.
Si el presupuesto no existe o no está aprobado, el sistema no permite generar la OT.
Si el presupuesto pertenece a otra sucursal, el sistema cancela la operación.
Si el presupuesto ya tiene una OT activa, el sistema no permite duplicar la OT.
Si el presupuesto es preliminar y no fue convertido a presupuesto con diagnóstico, el sistema no permite generar la OT.
Si la OT por reclamo no existe, el sistema muestra error.
Si la OT pertenece a otra sucursal, el sistema no permite completarla.
Si la OT no proviene de reclamo, el sistema no permite completarla desde esta pantalla.
Si la OT por reclamo no está pendiente de completar, el sistema no permite completarla.
Si el detalle de trabajos o repuestos no tiene formato válido, el sistema no permite guardar.
Si no se agrega al menos un trabajo o repuesto, el sistema no permite completar la OT.
Si se intenta agregar un servicio duplicado, el sistema muestra advertencia.
Si se intenta agregar un repuesto sin seleccionarlo o con cantidad inválida, el sistema muestra advertencia.
Si no existe stock suficiente para un repuesto, el sistema no permite completar la OT.
Si la OT tiene servicio registrado activo, el sistema no permite anularla.

Post Condición
Se genera una nueva orden de trabajo en el sistema.
El detalle de la OT es copiado desde presupuesto_detalleservicio cuando proviene de presupuesto.
El estado del presupuesto pasa a OT generada.
Si se completa una OT por reclamo, el detalle confirmado queda registrado en orden_trabajo_detalle.
Si se completa una OT por reclamo, queda activa para registro de servicio.
Al completar una OT por reclamo no se descuenta stock; el stock se valida en esta etapa y se descuenta posteriormente al registrar el servicio.
Si se anula la OT, queda en estado Anulada; si proviene de presupuesto, el presupuesto vuelve a aprobado, y si proviene de reclamo, se mantiene el estado del reclamo y el diagnóstico queda disponible para continuar.

Descripción de las tablas
Nombre	Alias	Base de Datos
orden_trabajo	orden_trabajo	Bd_reduc
orden_trabajo_detalle	orden_trabajo_detalle	Bd_reduc
usuarios	usuarios	Bd_reduc
presupuesto_servicio	presupuesto_servicio	Bd_reduc
presupuesto_detalleservicio	presupuesto_detalleservicio	Bd_reduc
recepcion_servicio	recepcion_servicio	Bd_reduc
diagnostico_servicio	diagnostico_servicio	Bd_reduc
diagnostico_detalle	diagnostico_detalle	Bd_reduc
articulos	articulos	Bd_reduc
vehiculos	vehiculos	Bd_reduc
clientes	clientes	Bd_reduc
marcas	marcas	Bd_reduc
empleados	empleados	Bd_reduc
equipo_trabajo	equipo_trabajo	Bd_reduc
equipo_empleado	equipo_empleado	Bd_reduc
stock	stock	Bd_reduc
registro_servicio	registro_servicio	Bd_reduc
modelo_auto	modelo_auto	Bd_reduc
reclamo_servicio	reclamo_servicio	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia

Agregar

Anular

Nombre de Caso de Uso
Registrar Servicios y Registrar insumos utilizados.

Descripción Básica
Este caso permite registrar la ejecución de una orden de trabajo activa. El sistema permite buscar una OT disponible, visualizar sus datos y detalle, agregar insumos utilizados, registrar fecha de ejecución, kilometraje de salida y observación. Cuando se agregan productos o insumos, el sistema descuenta stock y registra el movimiento correspondiente.

Actores relacionados
Personal de Recepción

Pre Condición
El usuario debe estar autenticado.
El usuario debe tener permisos necesarios para el módulo.
Debe existir una OT activa.
La OT debe pertenecer a la sucursal del usuario.
La OT debe tener detalle registrado.
No debe existir un registro de servicio activo previo para la misma OT.
Si se agregan insumos, debe existir stock disponible.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema a través de un logueo.
El usuario ingresa al módulo Registro de Servicio.
El sistema muestra las opciones Registro de Servicio y Buscar Registro de Servicio.

Nuevo
El usuario busca una orden de trabajo por cliente, vehículo o número de OT.
El sistema busca OT activas, de la sucursal del usuario, con detalle registrado y sin servicio activo previo.
Tablas consultadas para buscar OT: orden_trabajo, presupuesto_servicio, diagnostico_servicio, recepcion_servicio, clientes, vehiculos, modelo_auto, registro_servicio, orden_trabajo_detalle.
El sistema muestra las OT encontradas.
El usuario selecciona una OT.
El sistema consulta los datos de la OT seleccionada.
Tablas consultadas para cargar OT: orden_trabajo, presupuesto_servicio, diagnostico_servicio, recepcion_servicio, clientes, vehiculos, modelo_auto.
El sistema muestra número de OT, cliente y vehículo.
El sistema consulta el detalle de la OT.
Tablas consultadas para detalle de OT: orden_trabajo_detalle, articulos.
El sistema muestra artículo o servicio, cantidad, precio y subtotal.
El usuario puede buscar insumos utilizados.
El sistema consulta insumos activos con stock.
Tablas consultadas para insumos: articulos, stock.
El sistema muestra los insumos encontrados.
El usuario agrega uno o más insumos y su cantidad correspondiente.
El usuario puede quitar insumos antes de guardar.
El usuario ingresa fecha de ejecución, kilometraje de salida y observación si corresponde.

Guardar
El usuario presiona Registrar Servicio.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para registrar.
El sistema valida datos obligatorios.
El sistema valida que la OT esté activa y que pertenezca a la sucursal del usuario. Tabla consultada: orden_trabajo.
El sistema valida que no exista un registro de servicio activo previo para la OT. Tabla consultada: registro_servicio.
El sistema valida que la OT tenga detalle.
Si se agregan insumos, el sistema valida stock suficiente.
El sistema registra la cabecera del servicio en registro_servicio.
El sistema registra el detalle de la OT en registro_servicio_detalle con origen OT.
Si se agregaron insumos, el sistema registra los insumos en registro_servicio_detalle con origen INSUMO.
El sistema descuenta stock e inserta movimiento de productos e insumos utilizados. Tablas actualizadas: stock, movimientostock.
El sistema actualiza la OT a estado Servicio registrado. Tabla actualizada: orden_trabajo.
El sistema registra fecha de finalización de la OT.
El sistema actualiza la recepción a estado Finalizado. Tabla actualizada: recepcion_servicio.
Si la recepción corresponde a un reclamo, el sistema actualiza el reclamo como cerrado por servicio registrado. Tabla actualizada: reclamo_servicio.
El sistema emite mensaje de servicio registrado correctamente.

Anular
El usuario ingresa a Buscar Registro de Servicio.
El usuario ingresa filtros de búsqueda por fecha inicial, fecha final o estado.
El usuario presiona Buscar.
El sistema consulta los registros de servicio.
Tablas consultadas para buscar/listar registros: registro_servicio, orden_trabajo, presupuesto_servicio, diagnostico_servicio, recepcion_servicio, clientes, vehiculos, modelo_auto, usuarios.
El sistema muestra número de registro, OT, cliente, vehículo, fecha de ejecución, usuario registrador y estado.
Si el registro está activo y el usuario tiene permiso de anulación, el sistema muestra la opción Anular.
El usuario presiona Anular.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permiso de anulación.
El sistema valida el registro seleccionado.
El sistema verifica que el registro exista, esté activo y pertenezca a la sucursal del usuario.
Tablas consultadas para validar anulación: registro_servicio, orden_trabajo.
El sistema revierte el stock utilizado en los artículos tipo producto e insumo.
Tablas consultadas para revertir stock: registro_servicio_detalle, articulos.
Tablas actualizadas: stock, movimientostock.
El sistema anula el registro de servicio. Tabla actualizada: registro_servicio.
El sistema reactiva la orden de trabajo asociada. Tabla actualizada: orden_trabajo.
El sistema reabre la recepción asociada al servicio. Tabla actualizada: recepcion_servicio.
Si el servicio estaba asociado a un reclamo cerrado, el sistema reabre el reclamo. Tabla actualizada: reclamo_servicio.
El sistema emite mensaje de registro anulado correctamente.
El sistema recarga el listado de registros.

Flujo Alternativo:
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si no se selecciona OT, el sistema muestra datos incompletos.
Si la OT no existe, el sistema muestra que la orden de trabajo no existe.
Si la OT no está activa, el sistema no permite registrar el servicio.
Si la OT pertenece a otra sucursal, el sistema cancela la operación.
Si la OT ya tiene un registro de servicio activo, el sistema no permite duplicar el registro.
Si la OT no tiene detalle, no aparece disponible para registro.
Si se agrega un insumo sin stock suficiente, el sistema no permite registrar o revierte la operación.
Si el registro de servicio no existe al anular, el sistema muestra error.
Si el registro no está activo, el sistema no permite anularlo.
Si ocurre un error durante el registro o anulación, el sistema revierte la transacción.

Post Condición
El registro de servicio queda registrado en registro_servicio.
El detalle queda registrado en registro_servicio_detalle.
La OT queda en estado Servicio registrado.
La recepción asociada queda cerrada o finalizada.
Si corresponde a reclamo, el reclamo queda cerrado por servicio registrado.
El stock de productos e insumos utilizados queda descontado.
Se registra movimiento de stock por salida.
Si se anula el registro, se revierte el stock, se anula el registro, se reactiva la OT y se reabre la recepción asociada.

Descripción de las tablas
Nombre	Alias	Base de Datos
registro_servicio	registro_servicio	Bd_reduc
registro_servicio_detalle	registro_servicio_detalle	Bd_reduc
orden_trabajo	orden_trabajo	Bd_reduc
orden_trabajo_detalle	orden_trabajo_detalle	Bd_reduc
presupuesto_servicio	presupuesto_servicio	Bd_reduc
diagnostico_servicio	diagnostico_servicio	Bd_reduc
recepcion_servicio	recepcion_servicio	Bd_reduc
clientes	clientes	Bd_reduc
vehiculos	vehiculos	Bd_reduc
modelo_auto	modelo_auto	Bd_reduc
usuarios	usuarios	Bd_reduc
articulos	articulos	Bd_reduc
stock	stock	Bd_reduc
movimientostock	movimientostock	Bd_reduc
reclamo_servicio	reclamo_servicio	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia

Agregar

Anular

Nombre de Caso de Uso
Registrar Reclamos de Clientes.

Descripción Básica
Este caso se ocupa de registrar los reclamos de los clientes posteriores a un servicio realizado. El sistema permite buscar un registro de servicio activo, visualizar los trabajos realizados, seleccionar el tipo de reclamo, indicar si requiere garantía y registrar el reclamo evitando duplicados activos o en proceso para el mismo tipo de reclamo y servicio.

Actores relacionados
Personal de Recepción

Pre Condición
El usuario debe estar autenticado.
El usuario debe tener permisos necesarios para el módulo.
Debe existir un registro de servicio activo.
No debe existir otro reclamo activo o en proceso del mismo tipo para el mismo registro de servicio.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema a través de un logueo.
El usuario ingresa al módulo Reclamos de Servicio.
El sistema muestra las opciones Nuevo y Listado de Reclamos.

Nuevo
El usuario busca un servicio realizado por cliente, vehículo, número de registro u OT.
El sistema busca registros de servicio activos de la sucursal del usuario.
Tablas consultadas para buscar servicio realizado: registro_servicio, registro_servicio_detalle, orden_trabajo, articulos, presupuesto_servicio, diagnostico_servicio, recepcion_servicio, clientes, vehiculos, modelo_auto.
El sistema muestra los registros encontrados.
El usuario selecciona un registro de servicio.
El sistema carga número de registro, cliente, vehículo, garantía y trabajos realizados.
El usuario ingresa descripción del reclamo.
El usuario selecciona tipo de reclamo, prioridad y si requiere garantía.
Según el tipo de reclamo, el usuario selecciona los detalles reclamados.
El sistema valida que un reclamo de servicio incluya servicios y que un reclamo de repuesto incluya productos.

Guardar
El usuario presiona Registrar reclamo.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para registrar.
El sistema valida datos obligatorios.
El sistema valida que exista el registro de servicio.
El sistema valida que el registro de servicio pertenezca a la sucursal del usuario. Tabla consultada: registro_servicio.
El sistema valida que no exista otro reclamo activo o en proceso del mismo tipo para el mismo registro. Tabla consultada: reclamo_servicio.
El sistema valida que los detalles seleccionados pertenezcan al registro de servicio.
El sistema valida que los detalles seleccionados no tengan reclamo activo.
Si requiere garantía, el sistema valida vigencia por fecha y kilometraje cuando corresponda.
El sistema identifica cliente y vehículo del servicio reclamado.
Tablas consultadas: registro_servicio, orden_trabajo, presupuesto_servicio, diagnostico_servicio, recepcion_servicio.
El sistema registra el reclamo. Tabla insertada: reclamo_servicio.
El sistema registra el detalle del reclamo. Tabla insertada: reclamo_servicio_detalle.
El sistema actualiza el registro de servicio como con reclamo. Tabla actualizada: registro_servicio.
El sistema emite mensaje de reclamo registrado correctamente.

Anular
El usuario ingresa filtros de búsqueda por texto o estado en la Búsqueda de Reclamos.
El usuario presiona Buscar.
El sistema consulta los reclamos de la sucursal.
Tablas consultadas para buscar/listar reclamos: reclamo_servicio, registro_servicio, orden_trabajo, recepcion_servicio, clientes, vehiculos, modelo_auto.
El sistema muestra número de reclamo, cliente, vehículo, fecha, descripción, tipo, prioridad y estado.
Si el reclamo está activo y el usuario tiene permiso de anulación, el sistema muestra la opción Anular.
El usuario presiona Anular.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permiso de anulación.
El sistema valida el reclamo seleccionado.
El sistema verifica que el reclamo exista, esté activo y pertenezca a la sucursal del usuario. Tabla consultada: reclamo_servicio.
El sistema verifica que el reclamo no tenga recepción generada. Tabla consultada: recepcion_servicio.
El sistema anula el reclamo. Tabla actualizada: reclamo_servicio.
El sistema verifica si quedan reclamos no anulados para el mismo registro de servicio. Tabla consultada: reclamo_servicio.
Si no quedan reclamos no anulados, el sistema devuelve el registro de servicio a estado activo. Tabla actualizada: registro_servicio.
El sistema emite mensaje de reclamo anulado correctamente.
El sistema recarga el listado de reclamos.

Flujo Alternativo:
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si no se selecciona registro de servicio, el sistema muestra datos incompletos.
Si no se ingresa descripción del reclamo, el sistema muestra datos incompletos.
Si el registro de servicio no existe, el sistema no permite registrar el reclamo.
Si ya existe un reclamo activo o en proceso del mismo tipo para el mismo registro, el sistema no permite duplicar el reclamo.
Si no se puede identificar cliente o vehículo del servicio reclamado, el sistema cancela la operación.
Si el tipo de reclamo no es válido, el sistema no permite registrar.
Si un detalle seleccionado no pertenece al registro, el sistema cancela la operación.
Si un detalle seleccionado ya tiene reclamo activo, el sistema no permite duplicar el reclamo.
Si el reclamo no existe al anular, el sistema muestra error.
Si el reclamo ya está anulado, el sistema informa que ya se encuentra anulado.
Si el reclamo no está activo, el sistema no permite anularlo.
Si el reclamo tiene recepción generada, el sistema no permite anularlo.
Si ocurre un error durante el registro o anulación, el sistema revierte la transacción.
El usuario puede presionar Cancelar, y el sistema limpia el formulario.

Post Condición
El reclamo queda registrado en reclamo_servicio.
El detalle del reclamo queda registrado en reclamo_servicio_detalle.
El reclamo queda en estado Activo.
El registro de servicio queda marcado como con reclamo.
El reclamo queda disponible para generar una recepción por reclamo.
Si se anula, el reclamo queda en estado Anulado.
Si no quedan reclamos no anulados para el registro, el registro de servicio vuelve a estado activo.

Descripción de las tablas
Nombre	Alias	Base de Datos
reclamo_servicio	reclamo_servicio	Bd_reduc
reclamo_servicio_detalle	reclamo_servicio_detalle	Bd_reduc
registro_servicio	registro_servicio	Bd_reduc
registro_servicio_detalle	registro_servicio_detalle	Bd_reduc
orden_trabajo	orden_trabajo	Bd_reduc
articulos	articulos	Bd_reduc
presupuesto_servicio	presupuesto_servicio	Bd_reduc
diagnostico_servicio	diagnostico_servicio	Bd_reduc
recepcion_servicio	recepcion_servicio	Bd_reduc
clientes	clientes	Bd_reduc
vehiculos	vehiculos	Bd_reduc
modelo_auto	modelo_auto	Bd_reduc
marcas	marcas	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia

Agregar

Anular

Nombre de Caso de Uso
Registrar Salida de Insumos.

Descripción Básica
Este caso permite registrar la salida de insumos o consumibles operativos del taller. El sistema permite seleccionar un empleado responsable, buscar insumos activos con stock disponible, agregar uno o más insumos al detalle, indicar la cantidad de salida, registrar una observación y descontar el stock correspondiente. También permite consultar y anular salidas registradas, devolviendo el stock al inventario.

Actores relacionados
Personal de Recepción
Encargado de dto. de Servicios

Pre Condición
El usuario debe estar autenticado.
El usuario debe tener permisos para registrar salidas de insumos.
Debe existir una sucursal asociada al usuario.
Debe existir un empleado activo en la sucursal.
Deben existir insumos activos con stock disponible en la sucursal.

Flujo de eventos
Flujo Básico:
El usuario accede al sistema mediante logueo.
El usuario ingresa al módulo Salida de Insumos.
El sistema muestra las opciones Nuevo y Buscar.

Nuevo
El sistema muestra el formulario de salida de insumos.
El usuario busca un empleado responsable escribiendo nombre, apellido o número de documento.
El sistema busca empleados activos pertenecientes a la sucursal del usuario. Tablas consultadas para buscar empleado: empleados.
El usuario selecciona el empleado responsable.
El sistema carga el ID del empleado en el formulario.
El usuario ingresa una observación, si corresponde.
El usuario busca un insumo escribiendo al menos dos caracteres de la descripción.
El sistema busca artículos activos de tipo insumo con stock en la sucursal del usuario. Tablas consultadas para buscar insumo: articulos, stock.
El sistema muestra insumo, stock disponible y opción para agregar.
El usuario agrega uno o más insumos al detalle.
El usuario ingresa la cantidad de salida por cada insumo.
El sistema valida en pantalla que la cantidad no supere el stock disponible.
El usuario puede quitar insumos del detalle antes de guardar.

Guardar
El usuario presiona Registrar salida.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para registrar salida de insumos.
El sistema valida que exista una sesión válida y una sucursal asociada.
El sistema valida que se haya seleccionado empleado responsable.
El sistema valida que exista al menos un insumo en el detalle.
El sistema valida que cada insumo sea válido, esté activo y sea de tipo insumo.
El sistema valida que cada cantidad sea mayor a cero.
El sistema valida stock suficiente por insumo en la sucursal.
El sistema registra la cabecera en salida_insumo.
El sistema registra el detalle en salida_insumo_detalle.
El sistema registra un movimiento de stock por cada insumo. Tabla actualizada: movimientostock.
El sistema descuenta el stock de cada insumo. Tabla actualizada: stock.
El sistema emite mensaje de salida registrada correctamente.

Buscar
El usuario ingresa a Buscar.
El sistema permite filtrar por fecha inicial, fecha final, número de salida, empleado y estado.
El usuario presiona Buscar.
El sistema consulta las salidas de insumos de la sucursal actual.
Tablas consultadas para listar salidas: salida_insumo, empleados, usuarios.
El sistema muestra número, fecha, empleado, usuario registrador, observación, estado y acciones.

Anular
El usuario presiona Anular desde el listado.
El sistema emite mensaje de confirmación.
El usuario confirma la acción.
El sistema valida permisos para anular salidas de insumos.
El sistema valida que exista una sesión válida y una sucursal asociada.
El sistema valida el ID de la salida.
El sistema verifica que la salida exista.
El sistema verifica que la salida esté activa.
El sistema verifica que la salida pertenezca a la sucursal del usuario.
El sistema verifica que la salida tenga detalle válido.
El sistema consulta el detalle de la salida. Tablas consultadas: salida_insumo_detalle, articulos.
El sistema registra un movimiento inverso de stock por cada insumo. Tabla actualizada: movimientostock.
El sistema devuelve el stock de cada insumo. Tabla actualizada: stock.
El sistema anula la cabecera de la salida. Tabla actualizada: salida_insumo.
El sistema emite mensaje de salida anulada correctamente.

Flujo Alternativo:
Si el usuario no tiene permiso, el sistema muestra acceso denegado.
Si la sesión no es válida, el sistema cancela la operación.
Si no se selecciona empleado responsable, el sistema muestra error.
Si no se agrega al menos un insumo, el sistema no permite guardar.
Si un insumo no existe, no está activo o no es de tipo insumo, el sistema cancela la operación.
Si una cantidad es inválida, el sistema cancela la operación.
Si no existe stock para un insumo, el sistema cancela la operación.
Si el stock disponible es menor a la cantidad solicitada, el sistema no permite registrar la salida.
Si la salida no existe al anular, el sistema muestra error.
Si la salida ya está anulada, el sistema no permite anularla nuevamente.
Si la salida pertenece a otra sucursal, el sistema no permite anularla.
Si ocurre un error durante el registro o la anulación, el sistema revierte la transacción.
El usuario puede presionar Cancelar, y el sistema limpia el formulario.

Post Condición
La salida queda registrada en salida_insumo.
El detalle queda registrado en salida_insumo_detalle.
El stock de los insumos queda descontado.
Se registra movimiento de stock por salida de insumo.
Si se anula, la salida queda en estado Anulado.
Si se anula, el stock de los insumos queda devuelto.
Si se anula, se registra movimiento inverso de stock.

Descripción de las tablas
Nombre	Alias	Base de Datos
salida_insumo	salida_insumo	Bd_reduc
salida_insumo_detalle	salida_insumo_detalle	Bd_reduc
articulos	articulos	Bd_reduc
stock	stock	Bd_reduc
movimientostock	movimientostock	Bd_reduc
empleados	empleados	Bd_reduc
usuarios	usuarios	Bd_reduc

Interfaz Gráfica de Usuario

Diagrama de clases

Diagrama de secuencia

Agregar

Anular
