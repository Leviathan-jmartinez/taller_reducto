# Checklist de pruebas para validar el sistema

Este documento sirve como guia de validacion funcional despues de los cambios realizados en informes, estados, filtros, clientes, compras, servicios y boundaries de interfaz.

## 1. Acceso general y navegacion

- [ ] Iniciar sesion con un usuario valido.
- [ ] Verificar que el sistema cargue el panel principal sin errores.
- [ ] Verificar que el menu lateral muestre solo las opciones permitidas para el usuario.
- [ ] Acceder a Clientes desde el menu y confirmar que redirige a `cliente-nuevo/`.
- [ ] Confirmar que ya no se intenta abrir `cliente-lista/` como vista principal.
- [ ] Probar cerrar sesion y volver a ingresar.

## 2. Permisos

- [ ] Ingresar con usuario administrador y confirmar acceso a todos los modulos principales.
- [ ] Ingresar con usuario limitado y confirmar que no puede acceder a vistas sin permiso.
- [ ] Validar que las vistas muestren "Acceso no autorizado" cuando corresponde.
- [ ] Validar permisos de informes referenciales.
- [ ] Validar permisos de informes de movimientos.
- [ ] Validar permisos de compras, ordenes de compra, servicios, clientes, usuarios y roles.

## 3. Clientes

- [ ] Abrir `cliente-nuevo/`.
- [ ] Registrar un cliente nuevo con datos validos.
- [ ] Intentar registrar un cliente con documento repetido.
- [ ] Buscar un cliente existente.
- [ ] Actualizar datos de un cliente.
- [ ] Validar que no aparezca error por `paginador_cliente_controlador()`.
- [ ] Validar que `cliente-lista/` redirija correctamente a `cliente-nuevo/`.
- [ ] Validar que la pestana "LISTA DE CLIENTES" ya no aparezca donde fue removida.

## 4. Articulos

- [ ] Registrar un articulo de tipo producto.
- [ ] Registrar un articulo de tipo insumo.
- [ ] Validar carga de IVA.
- [ ] Validar carga de unidad de medida.
- [ ] Validar carga de categoria.
- [ ] Validar carga de marca.
- [ ] Buscar articulos por codigo.
- [ ] Buscar articulos por descripcion.
- [ ] Actualizar un articulo.
- [ ] Cambiar estado de un articulo.

## 5. Proveedores

- [ ] Registrar proveedor.
- [ ] Buscar proveedor por RUC.
- [ ] Buscar proveedor por razon social.
- [ ] Actualizar proveedor.
- [ ] Validar que proveedores carguen en filtros Select2 de informes/movimientos cuando corresponda.

## 6. Sucursales y empleados

- [ ] Registrar sucursal.
- [ ] Buscar sucursal.
- [ ] Actualizar sucursal.
- [ ] Registrar empleado asociado a sucursal.
- [ ] Buscar empleado por nombre.
- [ ] Buscar empleado por apellido.
- [ ] Buscar empleado por cedula.
- [ ] Validar filtro de sucursal en informe referencial de empleados.

## 7. Pedidos de compra

- [ ] Crear pedido de compra.
- [ ] Abrir `ModalArticulo` desde pedido.
- [ ] Buscar articulo dentro del modal.
- [ ] Seleccionar articulo.
- [ ] Agregar cantidad.
- [ ] Guardar pedido.
- [ ] Buscar pedido.
- [ ] Anular pedido.
- [ ] Validar que el informe de pedidos filtre por sucursal.

## 8. Presupuestos de compra

- [ ] Crear presupuesto desde pedido.
- [ ] Abrir `ModalBuscarPedido`.
- [ ] Buscar pedido por codigo, fecha o usuario.
- [ ] Seleccionar pedido.
- [ ] Abrir `ModalproveedorPre`.
- [ ] Buscar proveedor.
- [ ] Seleccionar proveedor.
- [ ] Guardar presupuesto.
- [ ] Buscar presupuesto.
- [ ] Abrir `modalDetallePresupuestoCompra` desde busqueda.
- [ ] Anular presupuesto.

## 9. Ordenes de compra

- [ ] Crear orden de compra con presupuesto.
- [ ] Filtrar presupuestos por proveedor.
- [ ] Abrir `modalDetallePresupuesto`.
- [ ] Filtrar productos dentro del detalle.
- [ ] Ingresar fecha de entrega.
- [ ] Ingresar cantidad de orden de compra.
- [ ] Generar orden de compra desde el modal.
- [ ] Crear orden de compra sin presupuesto.
- [ ] Abrir `ModalproveedorOC`.
- [ ] Buscar y seleccionar proveedor.
- [ ] Abrir `ModalArticuloOC`.
- [ ] Buscar y seleccionar articulo.
- [ ] Guardar orden de compra sin presupuesto.
- [ ] Buscar orden de compra.
- [ ] Generar PDF de orden de compra.
- [ ] Anular orden de compra.
- [ ] Validar informe de ordenes de compra con filtros fecha, estado, sucursal y proveedor.

## 10. Compras / facturas de compra

- [ ] Registrar factura con orden de compra.
- [ ] Abrir `ModalBuscarOC`.
- [ ] Buscar y seleccionar orden de compra.
- [ ] Validar carga de articulos pendientes.
- [ ] Ingresar cantidad facturada.
- [ ] Ingresar cantidad recibida.
- [ ] Confirmar calculo de diferencia.
- [ ] Guardar factura sin diferencia.
- [ ] Guardar factura con diferencia.
- [ ] Registrar factura sin orden de compra.
- [ ] Abrir `ModalproveedorCO`.
- [ ] Buscar y seleccionar proveedor.
- [ ] Abrir `ModalArticuloCO`.
- [ ] Buscar y seleccionar articulo.
- [ ] Validar calculo de IVA 5%.
- [ ] Validar calculo de IVA 10%.
- [ ] Validar total de factura.
- [ ] Buscar factura.
- [ ] Abrir `modalDetalleCompra`.
- [ ] Anular factura.
- [ ] Validar que estado `2` ya no se use como estado visible de compras.
- [ ] Validar estado `Regularizada con NC` cuando la compra queda regularizada por nota de credito.

## 11. Notas de credito y debito

- [ ] Abrir formulario de nota de credito/debito.
- [ ] Abrir `modalFactura`.
- [ ] Buscar factura.
- [ ] Seleccionar factura.
- [ ] Registrar nota de credito.
- [ ] Registrar nota de debito.
- [ ] Validar que una nota de credito por diferencia deje la compra en estado `Regularizada con NC`.
- [ ] Buscar notas registradas.
- [ ] Anular nota cuando corresponda.

## 12. Remisiones

- [ ] Crear remision.
- [ ] Abrir `ModalBuscarFactura`.
- [ ] Buscar factura.
- [ ] Seleccionar factura.
- [ ] Cargar datos de remision.
- [ ] Guardar remision.
- [ ] Buscar remision.
- [ ] Generar PDF de remision.
- [ ] Anular remision.

## 13. Inventario

- [ ] Abrir modulo Inventario.
- [ ] Abrir `modalInventario`.
- [ ] Generar inventario general.
- [ ] Generar inventario por categoria.
- [ ] Generar inventario por proveedor.
- [ ] Generar inventario por articulo.
- [ ] Abrir `ModalBuscarINV`.
- [ ] Buscar inventario.
- [ ] Cargar inventario.
- [ ] Modificar cantidades fisicas.
- [ ] Guardar inventario.
- [ ] Ajustar stock cuando el estado lo permita.
- [ ] Buscar inventario desde vista de busqueda.
- [ ] Abrir `modalDetalleInventario`.

## 14. Solicitud de servicios

- [ ] Crear solicitud de servicio normal.
- [ ] Buscar cliente usando el campo de busqueda/autocomplete.
- [ ] Abrir `modalNuevoClienteRecepcion`.
- [ ] Registrar cliente rapido.
- [ ] Buscar vehiculo usando el campo de busqueda/autocomplete.
- [ ] Abrir `modalNuevoVehiculoRecepcion`.
- [ ] Registrar vehiculo rapido.
- [ ] Abrir `modalVehiculo`.
- [ ] Buscar vehiculo por placa o modelo.
- [ ] Marcar servicio proveniente de reclamo.
- [ ] Abrir `modalReclamo`.
- [ ] Buscar y seleccionar reclamo.
- [ ] Cargar tipo de servicio.
- [ ] Cargar area del problema.
- [ ] Cargar observacion del cliente.
- [ ] Adjuntar fotos del vehiculo.
- [ ] Guardar solicitud.
- [ ] Buscar solicitud.
- [ ] Abrir `modalFotosRecepcion`.
- [ ] Anular solicitud.

## 15. Diagnostico de servicio

- [ ] Crear diagnostico desde solicitud.
- [ ] Cargar recepcion/solicitud.
- [ ] Agregar detalle de diagnostico.
- [ ] Indicar si requiere repuesto.
- [ ] Indicar servicio sugerido.
- [ ] Guardar diagnostico.
- [ ] Buscar diagnostico.
- [ ] Abrir `modalDetalleDiagnostico`.
- [ ] Generar orden de trabajo por reclamo cuando corresponda.
- [ ] Anular diagnostico.

## 16. Presupuesto de servicio

- [ ] Crear presupuesto con diagnostico.
- [ ] Abrir `modalDiagnostico`.
- [ ] Buscar y seleccionar diagnostico.
- [ ] Verificar carga de cliente, vehiculo y detalle del diagnostico.
- [ ] Crear presupuesto preliminar.
- [ ] Abrir `modalClientePresupuesto`.
- [ ] Buscar y seleccionar cliente.
- [ ] Abrir `modalVehiculoPresupuesto`.
- [ ] Buscar y seleccionar vehiculo.
- [ ] Buscar servicio o articulo.
- [ ] Agregar detalle.
- [ ] Validar promociones.
- [ ] Validar descuentos.
- [ ] Validar subtotal y total final.
- [ ] Guardar presupuesto.
- [ ] Buscar presupuesto.
- [ ] Aprobar presupuesto.
- [ ] Anular presupuesto.

## 17. Ordenes de trabajo

- [ ] Crear orden de trabajo desde presupuesto aprobado.
- [ ] Abrir `modalPresupuesto`.
- [ ] Buscar presupuesto aprobado.
- [ ] Seleccionar presupuesto.
- [ ] Verificar carga de cliente, vehiculo y trabajos.
- [ ] Guardar orden de trabajo.
- [ ] Buscar orden de trabajo.
- [ ] Generar PDF.
- [ ] Asignar tecnico si aplica.
- [ ] Anular orden de trabajo.

## 18. Registro de servicios

- [ ] Crear registro de servicio desde orden de trabajo.
- [ ] Cargar orden de trabajo.
- [ ] Registrar trabajos realizados.
- [ ] Registrar insumos utilizados.
- [ ] Guardar registro.
- [ ] Buscar registro.
- [ ] Abrir `modalDetalleRegistroServicio`.
- [ ] Anular registro.

## 19. Reclamos

- [ ] Registrar reclamo desde un servicio finalizado.
- [ ] Validar datos de cliente y vehiculo.
- [ ] Indicar motivo.
- [ ] Indicar prioridad.
- [ ] Indicar si solicita garantia.
- [ ] Guardar reclamo.
- [ ] Buscar reclamo.
- [ ] Usar reclamo como origen de una nueva solicitud de servicio.
- [ ] Anular reclamo.

## 20. Registro de insumos

- [ ] Crear salida de insumos.
- [ ] Buscar empleado.
- [ ] Buscar insumo.
- [ ] Agregar insumo.
- [ ] Guardar salida.
- [ ] Buscar salida.
- [ ] Anular salida.

## 21. Usuarios, roles y permisos

- [ ] Registrar usuario.
- [ ] Buscar usuario.
- [ ] Actualizar usuario.
- [ ] Abrir `modalRolesUsuario`.
- [ ] Asignar roles a usuario.
- [ ] Abrir `modalSucursalUsuario`.
- [ ] Asignar sucursal a usuario.
- [ ] Bloquear usuario por intentos fallidos.
- [ ] Desbloquear o reactivar usuario si corresponde.
- [ ] Registrar rol.
- [ ] Actualizar rol.
- [ ] Asignar permisos al rol.
- [ ] Validar que los permisos asignados impacten en el menu y accesos.

## 22. Informes referenciales

- [ ] Abrir Informes Referenciales.
- [ ] Validar que se muestran solo tipos permitidos para el usuario.
- [ ] Seleccionar Articulos.
- [ ] Validar filtros de articulos: estado, categoria, busqueda y cantidad.
- [ ] Confirmar que Articulos no muestre filtro sucursal.
- [ ] Seleccionar Proveedores.
- [ ] Validar filtros de proveedores.
- [ ] Seleccionar Sucursales.
- [ ] Validar filtros de sucursales.
- [ ] Seleccionar Clientes.
- [ ] Validar filtros de clientes.
- [ ] Seleccionar Vehiculos.
- [ ] Validar filtros de vehiculos.
- [ ] Seleccionar Empleados.
- [ ] Validar filtros de empleados: estado, sucursal, busqueda y cantidad.
- [ ] Probar Select2 en filtros de categoria y sucursal.
- [ ] Presionar Previsualizar.
- [ ] Validar resumen del informe.
- [ ] Validar grilla de resultados.
- [ ] Generar PDF si existen registros.
- [ ] Exportar CSV si existen registros.
- [ ] Validar comportamiento sin registros.

## 23. Informes de movimientos

- [ ] Abrir Informes de Movimientos.
- [ ] Validar que se muestran solo informes permitidos para el usuario.
- [ ] Seleccionar Pedidos.
- [ ] Validar que Pedidos use filtro sucursal.
- [ ] Seleccionar Ordenes de Compra.
- [ ] Validar filtros fecha, estado, sucursal y proveedor.
- [ ] Seleccionar Compras.
- [ ] Validar filtros fecha, estado, sucursal y proveedor.
- [ ] Confirmar que Compras no muestre estado `2` como estado visible.
- [ ] Confirmar que Compras muestre `Regularizada con NC`.
- [ ] Seleccionar Solicitudes de Servicios.
- [ ] Validar filtros fecha, estado, sucursal, busqueda y cantidad.
- [ ] Confirmar estados `Recepcionado`, `En proceso`, `Finalizado` y `Anulado` segun corresponda.
- [ ] Seleccionar Presupuestos.
- [ ] Validar filtros fecha, estado, sucursal y busqueda.
- [ ] Seleccionar Registros de Servicios.
- [ ] Validar filtros vista, fecha, estado, sucursal, articulo, tecnico y cantidad.
- [ ] Probar Select2 en sucursal, proveedor y tecnico.
- [ ] Presionar Previsualizar en cada informe.
- [ ] Validar resumen y graficos cuando correspondan.
- [ ] Generar PDF.
- [ ] Exportar CSV.
- [ ] Validar comportamiento sin registros.

## 24. PDF y CSV

- [ ] Generar PDF de informe referencial con registros.
- [ ] Generar CSV de informe referencial con registros.
- [ ] Generar PDF de informe de movimientos con registros.
- [ ] Generar CSV de informe de movimientos con registros.
- [ ] Validar que PDF y CSV respeten los mismos filtros de la previsualizacion.
- [ ] Validar que PDF indique sin registros cuando no existan datos.
- [ ] Validar que CSV no genere datos ajenos al filtro.
- [ ] Validar PDF de compras con estado `Regularizada con NC`.
- [ ] Validar PDF de recepcion/solicitud de servicios con placa, tipo, area y solicitud.

## 25. Estados

- [ ] Validar estados de compras.
- [ ] Validar que estado `2` de compras no quede disponible como filtro visible.
- [ ] Validar compra regularizada por NC.
- [ ] Validar estados de recepcion/solicitud de servicios.
- [ ] Validar estados de ordenes de compra.
- [ ] Validar estados de pedidos.
- [ ] Validar estados de presupuestos.
- [ ] Validar estados de registros de servicios.
- [ ] Validar estados en informes y vistas de busqueda.

## 26. Modales y boundaries

- [ ] Validar que `ModalArticulo` abre en pedidos.
- [ ] Validar que `ModalproveedorPre` y `ModalBuscarPedido` abren en presupuestos de compra.
- [ ] Validar que `modalDetallePresupuesto`, `ModalproveedorOC` y `ModalArticuloOC` abren en ordenes de compra.
- [ ] Validar que `ModalBuscarOC`, `ModalproveedorCO` y `ModalArticuloCO` abren en facturas.
- [ ] Validar que `ModalBuscarFactura` abre en remisiones.
- [ ] Validar que `modalFactura` abre en notas de credito/debito.
- [ ] Validar que `modalInventario` y `ModalBuscarINV` abren en inventario.
- [ ] Validar que `modalNuevoClienteRecepcion`, `modalNuevoVehiculoRecepcion`, `modalVehiculo` y `modalReclamo` abren en solicitud de servicios.
- [ ] Validar que `modalFotosRecepcion` abre desde busqueda de solicitudes.
- [ ] Validar que `modalDetalleDiagnostico` abre desde busqueda de diagnosticos.
- [ ] Validar que `modalDiagnostico`, `modalClientePresupuesto` y `modalVehiculoPresupuesto` abren en presupuesto de servicios.
- [ ] Validar que `modalPresupuesto` abre en ordenes de trabajo.
- [ ] Validar que `modalDetalleRegistroServicio` abre desde busqueda de registros de servicio.
- [ ] Validar que `modalRolesUsuario` y `modalSucursalUsuario` abren desde usuarios.

## 27. Pruebas tecnicas minimas

- [ ] Ejecutar `php -l` sobre controladores modificados.
- [ ] Ejecutar `php -l` sobre vistas modificadas.
- [ ] Ejecutar `php -l` sobre modelos modificados.
- [ ] Ejecutar `php -l` sobre archivos PDF modificados.
- [ ] Revisar consola del navegador en las vistas con modales.
- [ ] Revisar consola del navegador en informes referenciales.
- [ ] Revisar consola del navegador en informes de movimientos.
- [ ] Validar que no existan errores AJAX.
- [ ] Validar que las respuestas AJAX devuelvan datos esperados.
- [ ] Validar que no haya errores SQL en logs o pantalla.

## 28. Validacion visual de vistas

- [ ] Validar que las vistas internas usen fondo gris claro y no la imagen del taller.
- [ ] Validar que `home/` conserve la imagen de fondo del taller.
- [ ] Validar que los paneles principales tengan fondo claro, borde, sombra suave y padding uniforme.
- [ ] Validar que las pestanas superiores se vean como botones consistentes en las vistas modificadas.
- [ ] Validar que encabezados y pestanas no queden dentro de formularios cuando sean navegacion de modulo.
- [ ] Validar `notasCreDe-nuevo/` y `notasCreDe-buscar/` con encabezado/pestanas fuera del formulario.
- [ ] Validar `transferencia-historial/` para confirmar que texto, filtros y tabla no se mezclen con el fondo.
- [ ] Validar `inventario-buscar/`, `diagnostico-servicio-buscar/`, `diagnostico-servicio-nuevo/` y `descuento-nuevo/` con los botones superiores uniformes.
- [ ] Validar que tablas, fieldsets, filtros y botones de accion mantengan separacion visual suficiente.
- [ ] Validar que en resolucion movil las pestanas no se encimen y ocupen el ancho disponible.

## 29. Datos recomendados para prueba

- [ ] Al menos 2 sucursales activas.
- [ ] Al menos 2 proveedores activos.
- [ ] Al menos 2 empleados activos en distintas sucursales.
- [ ] Al menos 3 articulos: producto, insumo y servicio.
- [ ] Al menos 2 clientes.
- [ ] Al menos 2 vehiculos.
- [ ] Al menos 1 pedido de compra.
- [ ] Al menos 1 presupuesto de compra.
- [ ] Al menos 1 orden de compra con presupuesto.
- [ ] Al menos 1 orden de compra sin presupuesto.
- [ ] Al menos 1 compra sin diferencia.
- [ ] Al menos 1 compra con diferencia.
- [ ] Al menos 1 nota de credito para regularizar diferencia.
- [ ] Al menos 1 solicitud de servicio normal.
- [ ] Al menos 1 solicitud de servicio desde reclamo.
- [ ] Al menos 1 diagnostico.
- [ ] Al menos 1 presupuesto de servicio.
- [ ] Al menos 1 orden de trabajo.
- [ ] Al menos 1 registro de servicio.
- [ ] Al menos 1 reclamo.

## 30. Criterios de aceptacion general

- [ ] Las vistas cargan sin errores fatales.
- [ ] Los modales abren y cierran correctamente.
- [ ] Los filtros muestran solo campos correspondientes al informe seleccionado.
- [ ] Los Select2 cargan datos cuando corresponde.
- [ ] Las grillas respetan los filtros aplicados.
- [ ] PDF y CSV respetan los mismos filtros de la vista previa.
- [ ] Los estados visibles coinciden con los estados definidos funcionalmente.
- [ ] No se pierden datos existentes.
- [ ] No se modifican registros al consultar informes.
- [ ] Los permisos restringen correctamente el acceso.
