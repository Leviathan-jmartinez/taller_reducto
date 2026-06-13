# Boundaries de interfaces del sistema

Orden sugerido para el diagrama de clases: primero se coloca la vista principal `gui_...` y debajo sus modales o ventanas auxiliares `modal_...`, respetando la dependencia visual del usuario.

## Panel principal

```text
«boundary»
gui_panelPrincipal
--------------------------------
+metricasCompras
+metricasServicios
+accionesPermitidas
+informesPermitidos
--------------------------------
+cargarMetricas()
+mostrarAccesosRapidos()
+navegarModulo()
```

## Compras

### Pedidos de compra

```text
«boundary»
gui_pedidosCompra
--------------------------------
+fecha_pedido
+sucursal
+busqueda_articulo
+tablaArticulos
+detallePedido
+cantidad
+btn_agregarArticulo
+btn_guardar
+btn_cancelar
+busqueda_pedido
+fecha_desde
+fecha_hasta
+estado
+tablaPedidos
+paginadorPedidos
+btn_anular
--------------------------------
+buscarArticulo()
+agregarArticulo()
+quitarArticulo()
+registrarPedido()
+buscarPedidos()
+listarPedidos()
+anularPedido()
```

```text
«boundary»
ModalArticulo
--------------------------------
+label Codigo, descripcion
+input_articulo
+tabla_articulos
+btn_buscar
+btn_cerrar
--------------------------------
+buscarArticulo()
+seleccionarArticulo()
+cerrarModal()
```

### Presupuestos de compra

```text
«boundary»
gui_presupuestosCompra
--------------------------------
+fecha_presupuesto
+proveedor
+sucursal
+busqueda_articulo
+tablaArticulos
+detallePresupuesto
+cantidad
+precio
+btn_agregarArticulo
+btn_guardar
+btn_cancelar
+busqueda_presupuesto
+fecha_desde
+fecha_hasta
+estado
+tablaPresupuestos
+paginadorPresupuestos
+btn_anular
--------------------------------
+buscarProveedor()
+buscarArticulo()
+agregarArticulo()
+quitarArticulo()
+registrarPresupuesto()
+buscarPresupuestos()
+listarPresupuestos()
+anularPresupuesto()
```

```text
«boundary»
ModalproveedorPre
--------------------------------
+label Buscar proveedor
+input_proveedor
+tabla_proveedorPre
+btn_buscar
+btn_cerrar
--------------------------------
+buscarProveedor()
+seleccionarProveedor()
+cerrarModal()
```

```text
«boundary»
ModalBuscarPedido
--------------------------------
+label Buscar pedido
+input_pedido
+tabla_pedidosPre
+btn_buscar
+btn_cerrar
--------------------------------
+buscarPedido()
+seleccionarPedido()
+cerrarModal()
```

```text
«boundary»
modalDetallePresupuestoCompra
--------------------------------
+contenidoDetallePresupuestoCompra
+btn_cerrar
--------------------------------
+cargarDetallePresupuestoCompra()
+cerrarModal()
```

### Ordenes de compra

```text
«boundary»
gui_ordenesCompra
--------------------------------
+presupuesto_origen
+proveedor
+fecha
+fecha_entrega
+sucursal
+tipo_ordencompra
+busqueda_presupuesto
+tablaPresupuestos
+detalleOrdenCompra
+cantidad
+precio_unitario
+fecha_entrega_presupuesto
+filtroProductos
+btn_agregarArticulo
+btn_agregarProveedor
+btn_ocSinPresupuesto
+btn_guardar
+btn_cancelar
+busqueda_oc
+fecha_desde
+fecha_hasta
+estado
+tablaOrdenesCompra
+paginadorOrdenesCompra
+btn_pdf
+btn_anular
--------------------------------
+filtrarPresupuestos()
+abrirDetallePresupuesto()
+generarOrdenConPresupuesto()
+buscarProveedorOC()
+seleccionarProveedorOC()
+buscarArticuloOC()
+agregarArticuloOC()
+eliminarArticuloOC()
+registrarOrdenCompra()
+buscarOrdenesCompra()
+listarOrdenesCompra()
+imprimirOrdenCompra()
+anularOrdenCompra()
```

#### Modal detalle de presupuesto

```text
«boundary»
modalDetallePresupuesto
--------------------------------
+label Orden de compra
+filtroProductos
+fecha_entrega_presupuesto
+tablaDetallePresupuesto
+cantidad_oc
+btn_cerrar
+btnGuardarOC
--------------------------------
+filtrarProductos()
+cargarDetallePresupuesto()
+generarOrdenCompra()
+cerrarModal()
```

#### Modal proveedor orden de compra

```text
«boundary»
ModalproveedorOC
--------------------------------
+label RUC, razon social
+input_proveedor
+tabla_proveedorOC
+btn_buscar
+btn_cerrar
--------------------------------
+buscarProveedorOC()
+seleccionarProveedorOC()
+cerrarModal()
```

#### Modal articulo orden de compra

```text
«boundary»
ModalArticuloOC
--------------------------------
+label Codigo, descripcion
+input_articulo
+tabla_articulosOC
+btn_buscar
+btn_cerrar
--------------------------------
+buscarArticuloOC()
+seleccionarArticuloOC()
+cerrarModal()
```

### Ingreso de facturas

```text
«boundary»
gui_ingresoFacturasCompra
--------------------------------
+orden_compra
+proveedor
+nro_factura
+fecha_factura
+timbrado
+vencimiento_timbrado
+condicion
+sucursal
+detalleFactura
+cantidad_facturada
+cantidad_recibida
+precio_unitario
+total_compra
+btn_guardar
+btn_cancelar
+busqueda_factura
+fecha_desde
+fecha_hasta
+estado
+tablaFacturasCompra
+paginadorFacturasCompra
+btn_verDetalle
+btn_anular
--------------------------------
+buscarOrdenCompra()
+seleccionarOrdenCompra()
+calcularDiferencias()
+registrarFactura()
+buscarFacturas()
+listarFacturas()
+verDetalleFactura()
+anularFactura()
```

```text
«boundary»
ModalBuscarOC
--------------------------------
+label Buscar orden de compra
+input_oc
+tabla_OC
+btn_buscar
+btn_cerrar
--------------------------------
+buscarOrdenCompra()
+seleccionarOrdenCompra()
+cerrarModal()
```

```text
«boundary»
ModalproveedorCO
--------------------------------
+label RUC, razon social
+input_proveedor
+tabla_proveedorCO
+btn_buscar
+btn_cerrar
--------------------------------
+buscarProveedorCO()
+seleccionarProveedorCO()
+cerrarModal()
```

```text
«boundary»
ModalArticuloCO
--------------------------------
+label Codigo, descripcion
+input_articulo
+tabla_articuloCO
+btn_buscar
+btn_cerrar
--------------------------------
+buscarArticuloCO()
+seleccionarArticuloCO()
+cerrarModal()
```

```text
«boundary»
modalDetalleCompra
--------------------------------
+contenidoDetalleCompra
+btn_cerrar
--------------------------------
+cargarDetalleCompra()
+cerrarModal()
```

### Remisiones

```text
«boundary»
gui_remisiones
--------------------------------
+compra
+nro_remision
+fecha_emision
+fecha_envio
+fecha_llegada
+transportista
+vehiculo_transporte
+detalleRemision
+btn_guardar
+btn_cancelar
+busqueda_remision
+fecha_desde
+fecha_hasta
+estado
+tablaRemisiones
+paginadorRemisiones
+btn_pdf
+btn_anular
--------------------------------
+buscarCompra()
+seleccionarCompra()
+registrarRemision()
+buscarRemisiones()
+listarRemisiones()
+imprimirRemision()
+anularRemision()
```

```text
«boundary»
ModalBuscarFactura
--------------------------------
+label Buscar factura
+input_factura
+tabla_facturas
+btn_buscar
+btn_cerrar
--------------------------------
+buscarFactura()
+seleccionarFactura()
+cerrarModal()
```

### Notas de credito y debito

```text
«boundary»
gui_notasCreditoDebito
--------------------------------
+factura_compra
+tipo_nota
+nro_documento
+fecha
+motivo
+detalleNota
+cantidad
+precio
+total
+btn_guardar
+btn_cancelar
+busqueda_nota
+fecha_desde
+fecha_hasta
+estado
+tablaNotas
+paginadorNotas
+btn_anular
--------------------------------
+buscarFactura()
+seleccionarFactura()
+registrarNota()
+buscarNotas()
+listarNotas()
+anularNota()
```

```text
«boundary»
modalFactura
--------------------------------
+label Buscar factura
+input_factura
+tabla_facturas
+btn_buscar
+btn_cerrar
--------------------------------
+buscarFactura()
+seleccionarFactura()
+cerrarModal()
```

### Transferencias

```text
«boundary»
gui_transferencias
--------------------------------
+sucursal_destino
+observacion
+transportista
+ruc_transportista
+chofer
+ci_chofer
+celular_chofer
+chapa_vehiculo
+marca_vehiculo
+modelo_vehiculo
+fecha_envio
+fecha_llegada
+busqueda_producto
+detalleProductos
+btn_agregarProducto
+btn_guardar
+btn_cancelar
+filtro_estado
+filtro_fecha
+filtro_id
+tablaTransferencias
+btn_recibir
+btn_verRemision
+btn_anular
--------------------------------
+buscarSucursalDestino()
+buscarProducto()
+agregarProducto()
+crearTransferencia()
+listarTransferencias()
+recibirTransferencia()
+anularTransferencia()
+imprimirRemision()
```

### Inventarios

```text
«boundary»
gui_inventarios
--------------------------------
+tipo_inventario
+descripcion
+fecha
+sucursal
+busqueda_articulo
+detalleInventario
+cantidad_fisica
+cantidad_teorica
+diferencia
+btn_agregarArticulo
+btn_guardar
+btn_cancelar
+filtro_fecha_desde
+filtro_fecha_hasta
+filtro_estado
+tablaInventarios
+paginadorInventarios
+btn_verDetalle
+btn_anular
--------------------------------
+buscarArticulo()
+agregarArticulo()
+registrarInventario()
+buscarInventarios()
+listarInventarios()
+verDetalleInventario()
+anularInventario()
```

```text
«boundary»
modalInventario
--------------------------------
+tipo_inventario
+tipo_articulo
+subtipo_categoria
+subtipo_proveedor
+subtipo_producto
+fecha_creacion
+observacion
+btn_guardar
+btn_cancelar
--------------------------------
+cargarOpcionesInventario()
+generarInventario()
+cerrarModal()
```

```text
«boundary»
ModalBuscarINV
--------------------------------
+label Codigo de inventario, observacion
+input_inv
+tabla_INV
+btn_buscar
+btn_cerrar
--------------------------------
+buscarInventario()
+seleccionarInventario()
+cerrarModal()
```

```text
«boundary»
modalDetalleInventario
--------------------------------
+datosInventario
+detalleInventario
+btn_cerrar
--------------------------------
+cargarDetalleInventario()
+cerrarModal()
```

## Servicios

### Solicitud de servicios

```text
«boundary»
gui_solicitudServicios
--------------------------------
+cliente
+vehiculo
+kilometraje
+nivel_combustible
+estado_exterior
+objetos_vehiculo
+tipo_servicio
+area_problema
+prioridad
+accesorios
+observacion
+fotos
+origen_reclamo
+btn_guardar
+btn_cancelar
+busqueda_recepcion
+fecha_desde
+fecha_hasta
+estado
+origen
+tablaSolicitudesServicio
+paginadorSolicitudesServicio
+btn_verFotos
+btn_anular
--------------------------------
+buscarCliente()
+buscarVehiculo()
+buscarReclamo()
+cargarDatosReclamo()
+registrarSolicitud()
+buscarSolicitudes()
+listarSolicitudes()
+verFotos()
+anularSolicitud()
```

```text
«boundary»
modalNuevoClienteRecepcion
--------------------------------
+tipo_documento_reg
+cliente_doc_reg
+cliente_dv_reg
+cliente_nombre_reg
+cliente_apellido_reg
+cliente_telefono_reg
+cliente_email_reg
+cliente_direccion_reg
+ciudad_reg
+cliente_estadoC_reg
+btn_guardarCliente
+btn_cancelar
--------------------------------
+registrarCliente()
+cerrarModal()
```

```text
«boundary»
modalNuevoVehiculoRecepcion
--------------------------------
+modelo_reg
+color_reg
+placa_reg
+anho_reg
+version_reg
+transmision_reg
+motor_reg
+tipo_vehiculo_reg
+btn_guardarVehiculo
+btn_cancelar
--------------------------------
+registrarVehiculo()
+cerrarModal()
```

```text
«boundary»
modalVehiculo
--------------------------------
+label Buscar vehiculo
+buscar_vehiculo
+tabla_vehiculos
--------------------------------
+buscarVehiculoAjax()
+seleccionarVehiculo()
+cerrarModal()
```

```text
«boundary»
modalReclamo
--------------------------------
+label Buscar reclamo
+buscar_reclamo
+tabla_reclamos_modal
--------------------------------
+buscarReclamoAjax()
+seleccionarReclamo()
+cerrarModal()
```

```text
«boundary»
modalFotosRecepcion
--------------------------------
+galeriaFotos
+btn_cerrar
--------------------------------
+cargarFotos()
+cerrarModal()
```

### Diagnostico

```text
«boundary»
gui_diagnosticoServicio
--------------------------------
+recepcion
+detalleRecepcion
+fecha_diagnostico
+equipo_trabajo
+observaciones
+problema_detectado
+requiere_repuesto
+servicio_sugerido
+reclamo_valido
+corresponde_garantia
+requiere_cobro
+btn_guardar
+btn_cancelar
+busqueda_diagnostico
+fecha_desde
+fecha_hasta
+cliente
+placa
+nro_diagnostico
+nro_recepcion
+estado
+origen
+tablaDiagnosticos
+paginadorDiagnosticos
+btn_verDetalle
+btn_generarOTReclamo
+btn_anular
--------------------------------
+buscarRecepcion()
+seleccionarRecepcion()
+agregarDetalleDiagnostico()
+registrarDiagnostico()
+buscarDiagnosticos()
+listarDiagnosticos()
+verDetalleDiagnostico()
+generarOTReclamo()
+anularDiagnostico()
```

```text
«boundary»
modalDetalleDiagnostico
--------------------------------
+contenidoDetalleDiagnostico
+btn_cerrar
--------------------------------
+cargarDetalleDiagnostico()
+cerrarModal()
```

### Promociones

```text
«boundary»
gui_promociones
--------------------------------
+nombre
+descripcion
+tipo
+valor
+fecha_inicio
+fecha_fin
+estado
+productosAplicables
+btn_guardar
+btn_cancelar
+tablaPromociones
+btn_actualizar
+btn_eliminar
--------------------------------
+registrarPromocion()
+listarPromociones()
+actualizarPromocion()
+eliminarPromocion()
+asignarProductos()
```

### Descuentos

```text
«boundary»
gui_descuentos
--------------------------------
+nombre
+descripcion
+tipo
+valor
+alcance
+estado
+clientesAsignados
+btn_guardar
+btn_cancelar
+tablaDescuentos
+btn_actualizar
+btn_eliminar
--------------------------------
+registrarDescuento()
+listarDescuentos()
+actualizarDescuento()
+eliminarDescuento()
+asignarClientes()
```

### Reglas comerciales

```text
«boundary»
gui_reglasComerciales
--------------------------------
+nombre
+descripcion
+tipo_regla
+condiciones
+acciones
+estado
+btn_guardar
+btn_cancelar
+tablaReglasComerciales
+btn_actualizar
+btn_eliminar
--------------------------------
+registrarRegla()
+listarReglas()
+actualizarRegla()
+eliminarRegla()
```

### Presupuesto de trabajo

```text
«boundary»
gui_presupuestoTrabajo
--------------------------------
+origen
+diagnostico
+cliente
+vehiculo
+fecha
+fecha_vencimiento
+detallePresupuesto
+articulo_servicio
+cantidad
+precio
+descuentos
+promociones
+subtotal
+total_descuento
+total_final
+btn_guardar
+btn_cancelar
+busqueda_presupuesto
+fecha_desde
+fecha_hasta
+estado
+tablaPresupuestosServicio
+paginadorPresupuestosServicio
+btn_aprobar
+btn_anular
--------------------------------
+buscarDiagnostico()
+cargarDiagnostico()
+buscarArticuloServicio()
+agregarDetalle()
+aplicarDescuento()
+calcularTotales()
+registrarPresupuesto()
+buscarPresupuestos()
+listarPresupuestos()
+aprobarPresupuesto()
+anularPresupuesto()
```

```text
«boundary»
modalDiagnostico
--------------------------------
+label Buscar diagnostico
+buscar_diagnostico
+tabla_diagnostico
--------------------------------
+buscarDiagnostico()
+seleccionarDiagnostico()
+cerrarModal()
```

```text
«boundary»
modalClientePresupuesto
--------------------------------
+label Buscar cliente
+buscar_cliente
+tabla_clientes
--------------------------------
+buscarClientePresupuesto()
+seleccionarCliente()
+cerrarModal()
```

```text
«boundary»
modalVehiculoPresupuesto
--------------------------------
+label Buscar vehiculo
+buscar_vehiculo
+tabla_vehiculos
--------------------------------
+buscarVehiculoPresupuesto()
+seleccionarVehiculo()
+cerrarModal()
```

### Ordenes de trabajo

```text
«boundary»
gui_ordenesTrabajo
--------------------------------
+presupuesto
+reclamo
+cliente
+vehiculo
+tecnico_responsable
+observacion
+detalleTrabajos
+btn_guardar
+btn_cancelar
+busqueda_ot
+fecha_desde
+fecha_hasta
+estado
+tablaOrdenesTrabajo
+paginadorOrdenesTrabajo
+btn_asignarTecnico
+btn_pdf
+btn_anular
--------------------------------
+buscarPresupuesto()
+buscarReclamo()
+generarOrdenTrabajo()
+buscarOrdenesTrabajo()
+listarOrdenesTrabajo()
+asignarTecnico()
+imprimirOrdenTrabajo()
+anularOrdenTrabajo()
```

```text
«boundary»
modalPresupuesto
--------------------------------
+label Buscar presupuesto
+buscar_presupuesto
+tabla_presupuesto
--------------------------------
+buscarPresupuesto()
+seleccionarPresupuesto()
+cerrarModal()
```

### Registro de servicios

```text
«boundary»
gui_registroServicios
--------------------------------
+orden_trabajo
+cliente
+vehiculo
+fecha_servicio
+trabajos_realizados
+insumos_utilizados
+observacion
+garantia
+btn_guardar
+btn_cancelar
+busqueda_registro
+fecha_desde
+fecha_hasta
+estado
+tablaRegistrosServicio
+paginadorRegistrosServicio
+btn_verDetalle
+btn_anular
--------------------------------
+buscarOrdenTrabajo()
+cargarOrdenTrabajo()
+agregarInsumo()
+registrarServicio()
+buscarRegistros()
+listarRegistros()
+verDetalleRegistro()
+anularRegistro()
```

```text
«boundary»
modalDetalleRegistroServicio
--------------------------------
+contenidoDetalleRegistroServicio
+btn_cerrar
--------------------------------
+cargarDetalleRegistro()
+cerrarModal()
```

### Reclamos

```text
«boundary»
gui_reclamosServicio
--------------------------------
+registro_servicio
+cliente
+vehiculo
+fecha_reclamo
+motivo
+prioridad
+solicita_garantia
+detalleReclamo
+btn_guardar
+btn_cancelar
+busqueda_reclamo
+estado
+tablaReclamos
+btn_anular
--------------------------------
+buscarRegistroServicio()
+cargarServicioOrigen()
+registrarReclamo()
+listarReclamos()
+anularReclamo()
```

### Registro de insumos

```text
«boundary»
gui_registroInsumos
--------------------------------
+empleado
+fecha
+observacion
+busqueda_insumo
+detalleInsumos
+cantidad
+btn_agregarInsumo
+btn_guardar
+btn_cancelar
+fecha_desde
+fecha_hasta
+estado
+tablaSalidasInsumos
+paginadorSalidasInsumos
+btn_anular
--------------------------------
+buscarEmpleado()
+buscarInsumo()
+agregarInsumo()
+registrarSalidaInsumos()
+buscarSalidas()
+listarSalidas()
+anularSalida()
```

## Mantenimiento

### Sucursales

```text
«boundary»
gui_sucursales
--------------------------------
+empresa
+descripcion
+nro_establecimiento
+direccion
+telefono
+estado
+btn_guardar
+btn_cancelar
+busqueda_sucursal
+btn_buscar
+btn_limpiarBusqueda
+tablaSucursales
+paginadorSucursales
+btn_actualizar
+btn_eliminar
--------------------------------
+listarEmpresas()
+registrarSucursal()
+actualizarSucursal()
+buscarSucursal()
+listarSucursales()
+eliminarSucursal()
```

### Articulos

```text
«boundary»
gui_articulos
--------------------------------
+label Codigo
+articulo_codigo

+label Descripcion
+articulo_nombre

+label Precio Venta
+articulo_precio_venta

+label IVA
+tipo_iva

+label Unidad de medida
+unidad_medida

+label Categoria
+categoria

+label Marca
+marca

+label Tipo de articulo
+tipo_articulo

+label Estado
+estado_articulo

+btn_guardar
+btn_cancelar

+label Buscar articulo
+busqueda_articulo
+btn_buscar
+btn_limpiarBusqueda

+tablaArticulos
+paginadorArticulos
+btn_actualizar
+btn_eliminar
--------------------------------
+listarIVA()
+listarUnidadesMedida()
+listarCategorias()
+listarMarcas()
+cargarArticulo()
+registrarArticulo()
+actualizarArticulo()
+buscarArticulo()
+listarArticulos()
+eliminarArticulo()
+actualizarRequeridosArticulo()
```

### Proveedores

```text
«boundary»
gui_proveedores
--------------------------------
+razon_social
+ruc
+telefono
+direccion
+correo
+ciudad
+estado
+btn_guardar
+btn_cancelar
+busqueda_proveedor
+btn_buscar
+btn_limpiarBusqueda
+tablaProveedores
+paginadorProveedores
+btn_actualizar
+btn_eliminar
--------------------------------
+listarCiudades()
+registrarProveedor()
+actualizarProveedor()
+buscarProveedor()
+listarProveedores()
+eliminarProveedor()
```

### Clientes

```text
«boundary»
gui_clientes
--------------------------------
+tipo_documento
+documento
+digito_verificador
+nombre
+apellido
+telefono
+email
+direccion
+ciudad
+estado_civil
+estado
+btn_guardar
+btn_cancelar
+busqueda_cliente
+btn_buscar
+btn_limpiarBusqueda
+tablaClientes
+paginadorClientes
+btn_actualizar
+btn_eliminar
--------------------------------
+listarCiudades()
+registrarCliente()
+actualizarCliente()
+buscarCliente()
+listarClientes()
+eliminarCliente()
```

### Vehiculos

```text
«boundary»
gui_vehiculos
--------------------------------
+cliente
+modelo
+color
+placa
+anho
+version
+transmision
+motor
+tipo_vehiculo
+estado
+btn_guardar
+btn_cancelar
+busqueda_vehiculo
+btn_buscar
+btn_limpiarBusqueda
+tablaVehiculos
+paginadorVehiculos
+btn_actualizar
+btn_eliminar
--------------------------------
+listarModelos()
+buscarCliente()
+registrarVehiculo()
+actualizarVehiculo()
+buscarVehiculo()
+listarVehiculos()
+eliminarVehiculo()
```

### Empleados

```text
«boundary»
gui_empleados
--------------------------------
+cedula
+nombre
+apellido
+celular
+direccion
+cargo
+sucursal
+estado
+btn_guardar
+btn_cancelar
+busqueda_empleado
+btn_buscar
+btn_limpiarBusqueda
+tablaEmpleados
+paginadorEmpleados
+btn_actualizar
+btn_eliminar
--------------------------------
+listarCargos()
+listarSucursales()
+registrarEmpleado()
+actualizarEmpleado()
+buscarEmpleado()
+listarEmpleados()
+eliminarEmpleado()
```

### Equipos de trabajo

```text
«boundary»
gui_equiposTrabajo
--------------------------------
+nombre_equipo
+descripcion
+estado
+empleadosDisponibles
+miembrosEquipo
+btn_guardar
+btn_cancelar
+tablaEquipos
+btn_asignarMiembros
+btn_actualizar
+btn_eliminar
--------------------------------
+registrarEquipo()
+actualizarEquipo()
+listarEquipos()
+listarEmpleados()
+asignarMiembros()
+quitarMiembro()
+eliminarEquipo()
```

### Usuarios

```text
«boundary»
gui_usuarios
--------------------------------
+cedula
+nombre
+apellido
+telefono
+usuario
+email
+estado
+clave
+confirmar_clave
+usuario_admin
+clave_admin
+btn_guardar
+btn_cancelar
+busqueda_usuario
+btn_buscar
+btn_limpiarBusqueda
+tablaUsuarios
+paginadorUsuarios
+btn_roles
+btn_sucursal
+btn_actualizar
+btn_eliminar
--------------------------------
+registrarUsuario()
+actualizarUsuario()
+buscarUsuario()
+listarUsuarios()
+eliminarUsuario()
+asignarRoles()
+asignarSucursal()
```

```text
«boundary»
modalRolesUsuario
--------------------------------
+input_id_usuario
+contenedor_roles_usuario
+btn_guardar
+btn_cerrar
--------------------------------
+cargarRolesUsuario()
+guardarRolesUsuario()
+cerrarModal()
```

```text
«boundary»
modalSucursalUsuario
--------------------------------
+input_id_usuario_sucursal
+contenedor_sucursal_usuario
+btn_guardar
+btn_cerrar
--------------------------------
+cargarSucursalUsuario()
+asignarSucursal()
+cerrarModal()
```

### Roles y permisos

```text
«boundary»
gui_rolesPermisos
--------------------------------
+nombre_rol
+descripcion
+estado
+btn_guardar
+btn_cancelar
+busqueda_rol
+tablaRoles
+paginadorRoles
+rol
+contenedorPermisos
+btn_guardarPermisos
+btn_actualizar
+btn_eliminar
--------------------------------
+registrarRol()
+actualizarRol()
+buscarRol()
+listarRoles()
+eliminarRol()
+cargarPermisosRol()
+guardarPermisosRol()
```

## Informes

### Informes referenciales

```text
«boundary»
gui_informesReferenciales
--------------------------------
+tipo_referencial
+estado
+categoria
+sucursal
+buscar
+por_pagina
+btn_previsualizar
+btn_limpiar
+btn_generarPDF
+btn_exportarCSV
+resumenReferenciales
+tablaReferenciales
+paginadorReferenciales
--------------------------------
+listarTiposReferenciales()
+listarCategorias()
+listarSucursales()
+configurarFiltros()
+previsualizarInforme()
+generarPDF()
+exportarCSV()
```

### Informes de movimientos

```text
«boundary»
gui_informesMovimientos
--------------------------------
+tipo_movimiento
+vista_movimiento
+fecha_desde
+fecha_hasta
+estado
+sucursal
+proveedor
+cliente
+articulo
+tecnico
+naturaleza_stock
+tipo_movimiento_stock
+por_pagina
+btn_previsualizar
+btn_limpiar
+btn_generarPDF
+btn_exportarCSV
+resumenMovimientos
+panelGraficosMovimientos
+tablaMovimientos
+paginadorMovimientos
--------------------------------
+listarTiposMovimientos()
+listarSucursales()
+listarProveedores()
+listarEmpleados()
+configurarFiltros()
+previsualizarInforme()
+generarPDF()
+exportarCSV()
```
