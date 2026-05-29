# Casos de uso - Movimientos de Compras y Servicios

## 1. Registrar Recepcion de Compra

* Nombre de Caso de Uso
Registrar Recepcion de Compra

* Descripcion Basica
Este caso se ocupa del registro de una compra o recepcion de mercaderias. El sistema permite seleccionar una orden de compra, cargar datos de factura, recibir articulos, actualizar stock, registrar movimientos de stock, generar cuentas a pagar si corresponde y guardar el libro de compras.

* Actores relacionados
Personal de Compras

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso compra.crear.
Debe existir una sucursal asociada al usuario.
Debe existir proveedor y articulos registrados.
Si se recibe desde orden de compra, la orden debe estar activa y con cantidades pendientes.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo de compras.
El sistema valida el permiso compra.crear.
El usuario busca una orden de compra o selecciona proveedor y articulos manualmente.
El sistema carga proveedor, articulos pendientes, cantidades y precios si proviene de orden de compra (buscar_oc_controlador, cargar_oc_controlador).
El usuario ingresa nro. de factura, fecha de factura, timbrado, vencimiento de timbrado, condicion de compra e intervalo si corresponde.
El usuario confirma guardar la compra.
El sistema valida que exista detalle para guardar.
El sistema registra la cabecera de compra (guardar_compra_controlador, insertar_compra_cabecera_modelo).
El sistema registra el detalle de compra (insertar_compra_detalle_modelo).
El sistema actualiza o crea stock por articulo y sucursal (upsert_stock_modelo).
El sistema registra el movimiento de stock por ingreso de compra (agregar_movimiento_stock).
Si la compra proviene de orden de compra, el sistema descuenta cantidades pendientes y cierra la orden si queda completa (actualizar_oc_modelo).
Si la compra es a credito, el sistema genera cuentas a pagar (insertar_cuentas_a_pagar_modelo).
El sistema registra el libro de compras (insertar_libro_compra_modelo).
El sistema emite mensaje de compra registrada correctamente.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si no hay detalles, el sistema no permite guardar.
Si falla la cabecera, el detalle, el stock, la cuenta a pagar o el libro de compras, el sistema cancela la operacion.
Si la orden de compra no pertenece a la sucursal, el sistema cancela la operacion.

* Post Condicion
La compra queda registrada en compra_cabecera y compra_detalle.
El stock queda incrementado.
El movimiento de stock queda registrado.
La orden de compra queda actualizada si corresponde.
Las cuentas a pagar quedan generadas si corresponde.
El libro de compras queda registrado.

* Tablas utilizadas
Exponen datos: proveedores, orden_compra, orden_compra_detalle, articulos, stock.
Insertan o actualizan: compra_cabecera, compra_detalle, stock, movimientostock, orden_compra, orden_compra_detalle, cuentas_a_pagar, libro_compra, articulo_proveedor.

## 2. Anular Recepcion de Compra

* Nombre de Caso de Uso
Anular Recepcion de Compra

* Descripcion Basica
Este caso permite anular una recepcion de compra registrada, revertir el stock ingresado, registrar el movimiento inverso, reabrir cantidades pendientes de orden de compra y anular las cuentas a pagar y el libro de compras asociados.

* Actores relacionados
Personal de Compras

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso compra.anular.
La compra debe existir, pertenecer a la sucursal y no estar anulada.
Debe existir stock suficiente para revertir la entrada.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al listado de compras.
El sistema permite buscar compras registradas.
El usuario presiona Anular.
El sistema emite mensaje de confirmacion.
El usuario confirma.
El sistema valida que la compra exista y este activa (anular_compra_controlador).
El sistema marca la compra como anulada (anular_compra_modelo).
El sistema obtiene los detalles de la compra (datos_detalle_compra_modelo).
El sistema descuenta del stock las cantidades recibidas (descontar_stock_modelo).
El sistema registra movimiento de stock por anulacion de compra (movimiento_stock_anulacion_modelo).
Si la compra provino de orden de compra, el sistema devuelve cantidades pendientes y reabre la orden (revertir_oc_compra_modelo).
El sistema anula las cuentas a pagar asociadas (anular_cuentas_pagar_modelo).
El sistema anula el libro de compras asociado (anular_libro_compra_modelo).
El sistema emite mensaje de compra anulada correctamente.

* Flujo Alternativo
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si la compra no existe o ya esta anulada, el sistema no permite anular.
Si no hay stock suficiente para revertir, el sistema cancela la anulacion.
Si falla cualquier reversion, el sistema cancela toda la operacion.

* Post Condicion
La compra queda anulada.
El stock queda revertido.
Las cuentas a pagar quedan anuladas.
El libro de compras queda anulado.
La orden de compra queda reabierta si corresponde.

* Tablas utilizadas
Exponen datos: compra_cabecera, compra_detalle, proveedores, usuarios, stock.
Insertan o actualizan: compra_cabecera, stock, movimientostock, orden_compra, orden_compra_detalle, cuentas_a_pagar, libro_compra.

## 3. Registrar Solicitud de Servicios

* Nombre de Caso de Uso
Registrar Solicitud de Servicios

* Descripcion Basica
Este caso permite registrar la recepcion o solicitud de servicio de un vehiculo. El sistema permite seleccionar o registrar rapidamente un cliente, seleccionar o registrar su vehiculo, cargar el estado del vehiculo al ingreso, indicar el tipo de servicio solicitado, adjuntar fotos y guardar la recepcion. Tambien permite generar la recepcion desde un reclamo existente.

* Actores relacionados
Personal de Recepcion

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso servicio.recepcion.crear.
Debe existir una sucursal asociada al usuario.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Nueva Recepcion.
El sistema valida el permiso servicio.recepcion.crear.
El usuario busca o registra cliente rapido (buscarClienteAutocomplete, guardar_cliente_rapido_controlador).
El usuario busca o registra vehiculo rapido (buscarVehiculoAutocomplete, guardar_vehiculo_rapido_controlador).
Si es recepcion por reclamo, el usuario activa Servicio proveniente de reclamo (activarServicioDesdeReclamo).
El sistema bloquea cliente y vehiculo (bloquearOrigenReclamo).
El usuario busca y selecciona un reclamo activo de la sucursal (buscarReclamoAjax, buscar_reclamo_recepcion_controlador, seleccionarReclamo).
El sistema carga cliente, vehiculo y detalle del reclamo (obtener_reclamo_para_recepcion_controlador, cargarRecepcionDesdeReclamo, pintarDetalleReclamo).
El usuario carga kilometraje, combustible, estado exterior, objetos, tipo de servicio, area, prioridad, accesorios, fotos y observacion.
El usuario presiona Guardar.
El sistema valida cliente, vehiculo, kilometraje y observacion (guardar_recepcion_controlador).
Si el origen es reclamo, valida reclamo seleccionado.
El sistema registra la recepcion (guardar_recepcion_modelo).
Si el origen es reclamo, el sistema cambia el reclamo a estado en proceso.
Si hay fotos, el sistema guarda las rutas en recepcion_fotos.
El sistema emite mensaje de recepcion registrada.

* Flujo Alternativo
Si falta cliente, vehiculo, kilometraje u observacion, el sistema muestra datos incompletos.
Si el reclamo no esta disponible, el sistema cancela el registro.
Si el usuario presiona Cancelar, el sistema limpia la interfaz (limpiarFormularioRecepcion).

* Post Condicion
La recepcion queda registrada en estado Recepcionado.
Si proviene de reclamo, el reclamo queda en proceso.
Las fotos quedan registradas si fueron adjuntadas.

* Tablas utilizadas
Exponen datos: clientes, vehiculos, modelo_auto, marcas, ciudades, reclamo_servicio, recepcion_servicio, recepcion_fotos, usuarios.
Insertan o actualizan: clientes, vehiculos, recepcion_servicio, recepcion_fotos, reclamo_servicio.

## 4. Anular Recepcion de Servicio

* Nombre de Caso de Uso
Anular Recepcion de Servicio

* Descripcion Basica
Este caso permite anular una recepcion de servicio activa. Si la recepcion fue generada desde un reclamo, el sistema devuelve el reclamo a estado disponible.

* Actores relacionados
Personal de Recepcion

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso servicio.recepcion.anular.
La recepcion debe existir, pertenecer a la sucursal y estar en estado Recepcionado.

* Flujo de eventos
Flujo Basico:
El usuario ingresa a Buscar Recepcion.
El sistema lista recepciones de la sucursal (listar_recepcion_controlador, listar_recepcion_modelo).
El usuario presiona Anular.
El sistema emite mensaje de confirmacion.
El usuario confirma.
El sistema valida el identificador de recepcion (anular_recepcion_controlador).
El sistema busca la recepcion activa de la sucursal (anular_recepcion_modelo).
El sistema cambia la recepcion a estado Anulado.
Si la recepcion tiene reclamo asociado, el sistema devuelve el reclamo a estado Activo.
El sistema emite mensaje de recepcion anulada.

* Flujo Alternativo
Si la recepcion no existe, no pertenece a la sucursal o no esta activa, el sistema no permite anular.
Si la recepcion proviene de reclamo y no puede devolver el reclamo a activo, el sistema cancela la anulacion.

* Post Condicion
La recepcion queda anulada.
Si existia reclamo asociado, el reclamo vuelve a estar disponible.

* Tablas utilizadas
Exponen datos: recepcion_servicio, clientes, vehiculos, modelo_auto, marcas, usuarios, recepcion_fotos.
Insertan o actualizan: recepcion_servicio, reclamo_servicio.

## 5. Registrar Diagnostico de Servicio

* Nombre de Caso de Uso
Registrar Diagnostico de Servicio

* Descripcion Basica
Este caso permite registrar el diagnostico tecnico de una recepcion de servicio, con observaciones y detalles del problema encontrado. En recepciones provenientes de reclamo permite definir si el reclamo es valido. Cuando el reclamo es valido, luego de guardar el diagnostico el sistema consulta si se desea generar la orden de trabajo en ese momento.

* Actores relacionados
Personal Tecnico o Encargado de Diagnostico

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso servicio.diagnostico.crear.
Debe existir una recepcion disponible para diagnostico.
Debe existir sucursal asociada al usuario.
Para generar OT directa desde el diagnostico, la recepcion debe provenir de un reclamo, el reclamo debe estar en proceso, el diagnostico debe marcar el reclamo como valido, debe corresponder garantia, no debe requerir cobro y no debe existir una OT activa para ese reclamo.
El usuario debe tener permiso servicio.ot.generar para crear la OT por reclamo.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Diagnostico.
El sistema busca recepciones disponibles (buscarRecepcionAjax, buscar_recepcion_inline_controlador).
El usuario selecciona una recepcion.
El sistema muestra detalle de recepcion y, si corresponde, detalle de reclamo (obtener_recepcion_detalle_controlador, obtener_reclamo_detalle_controlador).
El usuario carga fecha, equipo, observaciones y detalles del diagnostico.
Si la recepcion proviene de reclamo, el usuario indica si el reclamo es valido, si corresponde garantia y si requiere cobro.
El usuario guarda diagnostico.
El sistema valida permiso, recepcion y fecha (guardar_diagnostico_controlador).
El sistema registra cabecera del diagnostico (guardar_diagnostico_modelo).
El sistema registra detalles del diagnostico.
El sistema cambia la recepcion a estado En proceso.
El sistema emite mensaje de diagnostico registrado.
Si el diagnostico corresponde a un reclamo valido, con garantia y sin cobro, el sistema devuelve una accion posterior para generar OT (PostAccion generar_ot_reclamo).
El sistema pregunta si el usuario desea generar la orden de trabajo ahora (alertasAjax).
Si el usuario confirma, el sistema ejecuta la misma funcion utilizada desde el listado de diagnosticos (crearOTReclamo).
El sistema envia la solicitud a ordenTrabajoAjax.php con accion crear_ot_reclamo.
El sistema valida permiso, reclamo, sucursal y existencia de OT activa (crear_ot_reclamo_controlador, crear_ot_reclamo_modelo).
El sistema registra la orden de trabajo con origen RECLAMO, sin presupuesto asociado y en estado pendiente de completar.
El sistema actualiza el diagnostico del reclamo como finalizado para el flujo y mantiene el reclamo en proceso.
El sistema emite mensaje de OT generada correctamente.

* Flujo Alternativo
Si la recepcion no esta disponible, el sistema cancela el diagnostico.
Si faltan recepcion o fecha, el sistema muestra datos incompletos.
Si falla la actualizacion de recepcion, el sistema cancela la operacion.
Si la recepcion no proviene de reclamo, el sistema solo registra el diagnostico y no pregunta por OT directa.
Si el reclamo se marca como no valido, el sistema solo registra el diagnostico y no genera OT.
Si el usuario responde que no desea generar la OT ahora, el diagnostico queda registrado y la OT puede generarse luego desde el listado de diagnosticos si mantiene las condiciones para OT directa.
Si el reclamo es valido pero no corresponde garantia o requiere cobro, el sistema no genera OT directa y el diagnostico queda disponible para presupuesto.
Si el usuario no tiene permiso servicio.ot.generar, el sistema muestra acceso denegado al intentar generar la OT.
Si el reclamo ya tiene una OT activa, el sistema no permite duplicar la orden.
Si el reclamo no pertenece a la sucursal del usuario, el sistema cancela la generacion de OT.

* Post Condicion
El diagnostico queda registrado.
La recepcion queda en estado En proceso.
Si el usuario confirma la generacion de OT y las validaciones son correctas, queda registrada una orden de trabajo por reclamo.
Si el usuario no confirma, el reclamo no es valido, no corresponde garantia o requiere cobro, no se crea orden de trabajo directa desde el diagnostico.

* Tablas utilizadas
Exponen datos: recepcion_servicio, clientes, vehiculos, modelo_auto, marcas, reclamo_servicio, equipo_trabajo, orden_trabajo.
Insertan o actualizan: diagnostico_servicio, diagnostico_detalle, recepcion_servicio, orden_trabajo, reclamo_servicio.

## 6. Anular Diagnostico de Servicio

* Nombre de Caso de Uso
Anular Diagnostico de Servicio

* Descripcion Basica
Este caso permite anular un diagnostico registrado desde la busqueda o listado de diagnosticos, siempre que no tenga presupuesto asociado. Al anularlo, se libera la recepcion para volver a diagnosticar.

* Actores relacionados
Personal Tecnico o Encargado de Diagnostico

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso servicio.diagnostico.anular.
El diagnostico debe existir.
El diagnostico no debe tener presupuesto activo asociado.
El diagnostico no debe estar ya anulado.

* Flujo de eventos
Flujo Basico:
El usuario ingresa a Buscar Diagnosticos.
El sistema permite filtrar por rango de fecha, cliente, placa, nro. de diagnostico, nro. de recepcion, estado, origen o busqueda general.
El sistema lista los diagnosticos de la sucursal del usuario (paginador_diagnostico_controlador, listar_diagnosticos_modelo).
El usuario presiona Anular.
El sistema emite mensaje de confirmacion.
El usuario confirma.
El sistema valida permiso e identificador (anular_diagnostico_controlador).
El sistema verifica que el diagnostico no tenga presupuesto.
El sistema anula el diagnostico (anular_diagnostico_modelo).
El sistema libera la recepcion volviendola a estado Recepcionado.
El sistema emite mensaje de diagnostico anulado.

* Flujo Alternativo
Si el diagnostico tiene presupuesto, el sistema no permite anular.
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.
Si el diagnostico no existe, el sistema muestra diagnostico no encontrado.
Si el diagnostico ya esta anulado, el sistema informa que ya se encuentra anulado.
Si el diagnostico tiene una OT activa generada por reclamo, no deberia anularse sin anular primero la OT asociada.

* Post Condicion
El diagnostico queda anulado.
La recepcion queda disponible para diagnostico.
El diagnostico ya no puede utilizarse para generar presupuesto ni OT.

* Tablas utilizadas
Exponen datos: diagnostico_servicio, recepcion_servicio, presupuesto_servicio, orden_trabajo, clientes, vehiculos, modelo_auto, marcas, usuarios.
Insertan o actualizan: diagnostico_servicio, recepcion_servicio.

## 7. Registrar Presupuesto de Servicio

* Nombre de Caso de Uso
Registrar Presupuesto de Servicio

* Descripcion Basica
Este caso permite generar un presupuesto a partir de un diagnostico. El sistema permite agregar servicios o productos, validar stock, aplicar promociones y descuentos, calcular totales y guardar el presupuesto.

* Actores relacionados
Personal de Presupuesto o Recepcion

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso servicio.presupuesto.crear.
Debe existir un diagnostico valido.
El diagnostico debe pertenecer a la sucursal del usuario.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Presupuesto de Servicio.
El sistema busca y carga diagnostico (buscar_diagnostico_controlador, datos_diagnostico_controlador).
El usuario agrega articulos o servicios (buscar_servicios_controlador).
El sistema valida articulos activos.
Si el articulo es producto, el sistema valida stock de la sucursal.
El sistema aplica promociones vigentes si corresponde.
El sistema obtiene descuentos disponibles para el cliente.
El usuario confirma guardar presupuesto.
El sistema valida permiso, diagnostico y detalle (guardar_presupuesto_controlador).
El sistema recalcula precios, promociones, descuentos y totales (guardar_presupuesto_modelo).
El sistema registra cabecera del presupuesto.
El sistema registra detalle del presupuesto.
El sistema registra promociones y descuentos aplicados.
El sistema cambia el diagnostico a estado presupuestado.
El sistema emite mensaje de presupuesto registrado.

* Flujo Alternativo
Si no hay stock suficiente, el sistema cancela el presupuesto.
Si los totales cambiaron entre pantalla y servidor, el sistema solicita verificar.
Si el diagnostico no pertenece a la sucursal, el sistema cancela la operacion.

* Post Condicion
El presupuesto queda registrado en estado Pendiente.
El diagnostico queda asociado a presupuesto.

* Tablas utilizadas
Exponen datos: diagnostico_servicio, recepcion_servicio, clientes, vehiculos, articulos, stock, promociones, promocion_producto, descuentos, descuento_cliente.
Insertan o actualizan: presupuesto_servicio, presupuesto_detalleservicio, presupuesto_promocion, presupuesto_descuento, diagnostico_servicio.

## 8. Aprobar Presupuesto de Servicio

* Nombre de Caso de Uso
Aprobar Presupuesto de Servicio

* Descripcion Basica
Este caso permite aprobar un presupuesto para habilitar la generacion de orden de trabajo.

* Actores relacionados
Personal de Presupuesto o Encargado

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso servicio.presupuesto.aprobar.
El presupuesto debe existir y estar pendiente.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al listado de presupuestos.
El usuario presiona Aprobar.
El sistema emite confirmacion.
El usuario confirma.
El sistema valida permiso e identificador (aprobar_presupuesto_controlador).
El sistema cambia el presupuesto a estado Aprobado (aprobar_presupuesto_modelo).
El sistema emite mensaje de presupuesto aprobado.

* Flujo Alternativo
Si el presupuesto no esta pendiente, no se aprueba.
Si el usuario no tiene permiso, el sistema muestra acceso no autorizado.

* Post Condicion
El presupuesto queda aprobado y habilitado para generar OT.

* Tablas utilizadas
Exponen datos: presupuesto_servicio, diagnostico_servicio, recepcion_servicio, clientes, vehiculos, modelo_auto, usuarios.
Insertan o actualizan: presupuesto_servicio.

## 9. Anular Presupuesto de Servicio

* Nombre de Caso de Uso
Anular Presupuesto de Servicio

* Descripcion Basica
Este caso permite anular un presupuesto siempre que no tenga una orden de trabajo activa asociada. Al anularlo, el diagnostico vuelve al estado anterior.

* Actores relacionados
Personal de Presupuesto o Encargado

* Pre Condicion
El usuario debe estar autenticado.
El usuario debe tener permiso servicio.presupuesto.anular.
El presupuesto debe existir.
El presupuesto no debe tener OT activa.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al listado de presupuestos.
El usuario presiona Anular.
El sistema emite mensaje de confirmacion.
El usuario confirma.
El sistema valida permiso e identificador (anular_presupuesto_controlador).
El sistema verifica que no exista OT activa.
El sistema anula el presupuesto (anular_presupuesto_modelo).
El sistema devuelve el diagnostico a estado diagnosticado.
El sistema emite mensaje de presupuesto anulado.

* Flujo Alternativo
Si el presupuesto tiene OT activa, el sistema no permite anular.

* Post Condicion
El presupuesto queda anulado.
El diagnostico queda disponible para nuevo presupuesto.

* Tablas utilizadas
Exponen datos: presupuesto_servicio, orden_trabajo, diagnostico_servicio.
Insertan o actualizan: presupuesto_servicio, diagnostico_servicio.

## 10. Generar Orden de Trabajo

* Nombre de Caso de Uso
Generar Orden de Trabajo

* Descripcion Basica
Este caso permite generar una orden de trabajo desde un presupuesto aprobado o desde un reclamo en proceso.

* Actores relacionados
Encargado de Servicios

* Pre Condicion
El usuario debe estar autenticado.
Debe tener permiso para crear OT normal o crear OT por reclamo.
Para OT normal, el presupuesto debe estar aprobado.
Para OT por reclamo, el reclamo debe estar en proceso y no debe tener OT activa.

* Flujo de eventos
Flujo Basico:
El usuario selecciona un presupuesto aprobado o un reclamo procesable.
El sistema valida que no exista OT activa para ese origen.
Para presupuesto, el sistema crea la OT copiando los trabajos del presupuesto (generar_ot_controlador2, crear_ot_modelo2).
Para presupuesto, el sistema toma cliente y vehiculo directamente desde `presupuesto_servicio` y los guarda en `orden_trabajo`.
Para reclamo, el sistema crea la OT con origen RECLAMO (crear_ot_reclamo_controlador, crear_ot_reclamo_modelo).
Para reclamo por garantia, el sistema conserva el uso de `recepcion_servicio` y `diagnostico_servicio` para validar garantia, validez del reclamo y condiciones sin cobro.
El sistema registra detalle de OT cuando corresponde.
El sistema cambia el presupuesto a estado con OT o mantiene el reclamo en proceso.
El sistema emite mensaje de OT generada.

* Flujo Alternativo
Si el presupuesto no esta aprobado, no se genera OT.
Si el reclamo no esta en proceso o ya tiene OT activa, no se genera OT.
Si la sucursal no coincide, el sistema cancela la operacion.

* Post Condicion
La OT queda registrada.
El presupuesto queda marcado con OT si corresponde.
El reclamo mantiene continuidad hacia registro de servicio si corresponde.

* Tablas utilizadas
Exponen datos: presupuesto_servicio, presupuesto_detalleservicio, reclamo_servicio, clientes, vehiculos. Diagnostico y recepcion se consultan solo para trazabilidad tecnica, kilometraje o validacion de reclamo por garantia.
Insertan o actualizan: orden_trabajo, orden_trabajo_detalle, presupuesto_servicio, reclamo_servicio, diagnostico_servicio.

## 11. Asignar o Completar Orden de Trabajo

* Nombre de Caso de Uso
Asignar o Completar Orden de Trabajo

* Descripcion Basica
Este caso permite asignar tecnico o equipo a una OT, y en OT provenientes de reclamo permite completar trabajos y repuestos antes del registro del servicio.

* Actores relacionados
Encargado de Servicios

* Pre Condicion
El usuario debe estar autenticado.
Debe tener permiso de asignacion o registro segun corresponda.
La OT debe existir y pertenecer a la sucursal.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al listado o asignacion de OT.
El sistema carga tecnicos/equipos disponibles (cargar_tecnicos_equipo_controlador, listar_equipos_controlador).
El usuario asigna tecnico/equipo (asignar_equipo_controlador, asignar_equipo_modelo).
Si la OT es por reclamo y esta pendiente, el usuario completa trabajos o repuestos (completar_ot_controlador, completar_ot_modelo).
El sistema valida sucursal, origen y estado.
El sistema valida stock de repuestos.
El sistema registra o reemplaza el detalle de la OT.
El sistema emite mensaje de OT actualizada.

* Flujo Alternativo
Si la OT no pertenece a la sucursal, el sistema cancela la operacion.
Si no hay stock suficiente, no permite completar la OT.
Si la OT no es de reclamo, no aplica completar por reclamo.

* Post Condicion
La OT queda asignada o completada para registro del servicio.

* Tablas utilizadas
Exponen datos: orden_trabajo, equipo_trabajo, equipo_empleado, empleados, articulos, stock.
Insertan o actualizan: orden_trabajo, orden_trabajo_detalle.

## 12. Anular Orden de Trabajo

* Nombre de Caso de Uso
Anular Orden de Trabajo

* Descripcion Basica
Este caso permite anular una OT siempre que no tenga servicio registrado. Si proviene de presupuesto, el presupuesto vuelve a aprobado. Si proviene de reclamo, el reclamo vuelve a activo y el diagnostico se libera.

* Actores relacionados
Encargado de Servicios

* Pre Condicion
El usuario debe estar autenticado.
Debe tener permiso servicio.ot.anular.
La OT debe existir y estar en estado anulable.
La OT no debe tener registro de servicio activo.

* Flujo de eventos
Flujo Basico:
El usuario presiona Anular OT.
El sistema emite mensaje de confirmacion.
El usuario confirma.
El sistema valida permiso, OT y estado (anular_ot_controlador).
El sistema verifica que no exista registro de servicio activo.
El sistema anula la OT (anular_ot_modelo).
Si la OT viene de presupuesto, el sistema devuelve el presupuesto a aprobado.
Si la OT viene de reclamo, el sistema devuelve el reclamo a activo y libera el diagnostico asociado.
El sistema emite mensaje de OT anulada.

* Flujo Alternativo
Si la OT tiene servicio registrado, el sistema no permite anular.
Si la OT no esta en estado anulable, el sistema cancela la operacion.

* Post Condicion
La OT queda anulada.
El presupuesto o reclamo vuelve al estado que permite reprocesar.

* Tablas utilizadas
Exponen datos: orden_trabajo, registro_servicio.
Insertan o actualizan: orden_trabajo, presupuesto_servicio, reclamo_servicio, diagnostico_servicio.

## 13. Registrar Servicio Realizado

* Nombre de Caso de Uso
Registrar Servicio Realizado

* Descripcion Basica
Este caso permite registrar la ejecucion final de una OT, copiar los trabajos realizados, agregar insumos consumidos, descontar stock, cerrar la OT y finalizar la recepcion. Si corresponde a reclamo, cierra el reclamo como resuelto.

* Actores relacionados
Personal Tecnico o Encargado de Servicios

* Pre Condicion
El usuario debe estar autenticado.
Debe tener permiso servicio.registro.crear.
La OT debe existir, estar activa y pertenecer a la sucursal.
La OT no debe tener servicio registrado previamente.
Debe existir stock suficiente para productos e insumos.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Registro de Servicio.
El sistema busca OT disponibles (buscar_ot_para_registro_controlador).
El usuario selecciona una OT.
El sistema carga datos y detalle de OT (cargar_ot_para_registro_controlador).
El usuario agrega insumos si corresponde (buscar_insumo_controlador).
El usuario carga fecha y observacion.
El usuario confirma registrar servicio.
El sistema valida permiso, OT, sucursal y registro previo (registrar_servicio_controlador, registrar_servicio_modelo).
El sistema identifica cliente y vehiculo desde `orden_trabajo` y los copia a `registro_servicio`.
El sistema registra cabecera de servicio.
El sistema deja disponible la garantia para calculo posterior desde la fecha de ejecucion: vencimiento a tres meses y limite de kilometraje a 2.000 km sobre el kilometraje de la recepcion que dio origen al servicio.
El sistema copia detalle de la OT a registro_servicio_detalle.
El sistema agrega insumos consumidos.
El sistema descuenta stock y registra movimientos de stock (aplicar_stock_registro_servicio).
El sistema cierra la OT.
El sistema finaliza la recepcion.
Si corresponde a reclamo, el sistema marca el reclamo como resuelto.
El sistema emite mensaje de servicio registrado.

* Flujo Alternativo
Si la OT no existe, no esta activa o no pertenece a la sucursal, el sistema cancela.
Si ya existe servicio registrado, el sistema no permite duplicar.
Si no hay stock suficiente, el sistema cancela el registro.

* Post Condicion
El servicio queda registrado.
La OT queda cerrada.
La recepcion queda finalizada.
El stock queda descontado.
El reclamo queda resuelto si corresponde.

* Tablas utilizadas
Exponen datos: orden_trabajo, orden_trabajo_detalle, articulos, stock, clientes, vehiculos, modelo_auto. Recepcion se consulta solo para cierre operativo o reclamo asociado.
Insertan o actualizan: registro_servicio, registro_servicio_detalle, stock, movimientostock, orden_trabajo, recepcion_servicio, reclamo_servicio.

## 14. Anular Registro de Servicio

* Nombre de Caso de Uso
Anular Registro de Servicio

* Descripcion Basica
Este caso permite anular un servicio registrado, devolver stock de insumos/productos, reabrir la OT y reabrir la recepcion. Si el servicio cerraba un reclamo, el reclamo vuelve a estado en proceso.

* Actores relacionados
Encargado de Servicios

* Pre Condicion
El usuario debe estar autenticado.
Debe tener permiso servicio.registro.anular.
El registro debe existir, estar activo y pertenecer a la sucursal.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al listado de registros de servicio.
El usuario presiona Anular.
El sistema emite mensaje de confirmacion.
El usuario confirma.
El sistema valida permiso, registro y sucursal (anular_registro_servicio_controlador).
El sistema revierte stock y registra movimiento inverso (revertir_stock_registro_servicio).
El sistema anula el registro.
El sistema reabre la OT.
El sistema reabre la recepcion.
Si habia reclamo resuelto, el sistema vuelve el reclamo a estado en proceso.
El sistema emite mensaje de registro anulado.

* Flujo Alternativo
Si el registro no existe o no esta activo, no se permite anular.
Si no pertenece a la sucursal, el sistema cancela.
Si no puede revertir stock, se cancela la anulacion.

* Post Condicion
El registro queda anulado.
El stock queda devuelto.
La OT queda activa nuevamente.
La recepcion queda en proceso.
El reclamo vuelve a proceso si corresponde.

* Tablas utilizadas
Exponen datos: registro_servicio, registro_servicio_detalle, articulos, stock. Orden y recepcion se consultan solo para reabrir el flujo operativo.
Insertan o actualizan: registro_servicio, stock, movimientostock, orden_trabajo, recepcion_servicio, reclamo_servicio.

## 15. Registrar Reclamo de Servicio

* Nombre de Caso de Uso
Registrar Reclamo de Servicio

* Descripcion Basica
Este caso permite registrar un reclamo sobre un servicio realizado. El reclamo queda asociado al registro de servicio, cliente y vehiculo, y permite luego generar una recepcion por reclamo.

* Actores relacionados
Personal de Recepcion o Servicios

* Pre Condicion
El usuario debe estar autenticado.
Debe tener permiso servicio.reclamo.crear.
Debe existir un registro de servicio activo.
No debe existir otro reclamo activo o en proceso para el mismo registro.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al modulo Reclamos.
El usuario busca servicio realizado (buscar_registro_controlador, buscar_registro_modelo).
El sistema muestra datos de servicio, cliente, vehiculo y trabajos.
El usuario selecciona el registro.
El usuario carga descripcion, tipo, origen, prioridad y garantia.
El usuario confirma registrar reclamo.
El sistema valida permiso y datos obligatorios (registrar_reclamo_controlador).
El sistema valida duplicado.
El sistema identifica cliente y vehiculo desde `registro_servicio`, valida su consistencia y los copia a `reclamo_servicio`.
Si se solicita garantia, el sistema valida inicialmente la fecha de ejecucion del registro reclamado contra el plazo de tres meses. La validacion por kilometraje se vuelve a realizar cuando se cargue la recepcion del reclamo con el kilometraje actual.
El sistema registra el reclamo (registrar_reclamo_modelo).
El sistema marca el registro de servicio como con reclamo.
El sistema emite mensaje de reclamo registrado.

* Flujo Alternativo
Si faltan registro o descripcion, el sistema muestra datos incompletos.
Si ya existe reclamo activo, el sistema no permite duplicar.
Si no se puede identificar cliente o vehiculo, el sistema cancela.
Si la garantia esta vencida por fecha, el sistema no permite registrar el reclamo como garantia. El reclamo puede continuar sin garantia si el usuario cambia esa opcion.

* Post Condicion
El reclamo queda registrado en estado Activo.
El registro de servicio queda marcado como con reclamo.

* Tablas utilizadas
Exponen datos: registro_servicio, registro_servicio_detalle, articulos, clientes, vehiculos, modelo_auto. Recepcion se consulta solo cuando se necesita calcular el kilometraje de origen para la garantia.
Insertan o actualizan: reclamo_servicio, registro_servicio.

## 16. Anular Reclamo de Servicio

* Nombre de Caso de Uso
Anular Reclamo de Servicio

* Descripcion Basica
Este caso permite anular un reclamo activo siempre que no tenga recepcion generada ni proceso iniciado.

* Actores relacionados
Personal de Recepcion o Servicios

* Pre Condicion
El usuario debe estar autenticado.
Debe tener permiso servicio.reclamo.anular.
El reclamo debe existir y estar activo.
El reclamo no debe tener recepcion generada.

* Flujo de eventos
Flujo Basico:
El usuario ingresa al listado de reclamos.
El usuario presiona Anular.
El sistema emite mensaje de confirmacion.
El usuario confirma.
El sistema valida permiso, reclamo y estado (anular_reclamo_controlador).
El sistema verifica que no exista recepcion generada.
El sistema anula el reclamo (anular_reclamo_modelo).
El sistema verifica si quedan reclamos activos del mismo registro.
Si no quedan reclamos activos, el sistema devuelve el registro de servicio a estado normal.
El sistema emite mensaje de reclamo anulado.

* Flujo Alternativo
Si el reclamo no esta activo, no se permite anular.
Si el reclamo tiene recepcion generada, no se permite anular.

* Post Condicion
El reclamo queda anulado.
El registro de servicio se actualiza si no quedan reclamos activos.

* Tablas utilizadas
Exponen datos: reclamo_servicio, recepcion_servicio, registro_servicio.
Insertan o actualizan: reclamo_servicio, registro_servicio.
