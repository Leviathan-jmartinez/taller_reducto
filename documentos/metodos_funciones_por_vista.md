# Metodos por vista

Formato orientado a UML. Se listan metodos genericos por vista y se agregan metodos indispensables cuando la vista tiene logica propia de interfaz, calculos, carga dinamica o acciones especiales.

## articulo-nuevo-vista

+agregar_articulo()
+listar_articulo()
+datos_articulo()
+editar_articulo()
+eliminar_articulo()
+buscar_articulo()
+listar_marca()
+listar_tipo_articulo()
+validar_codigo_articulo()

## articulo-actualizar-vista

+datos_articulo()
+editar_articulo()
+listar_marca()
+listar_tipo_articulo()
+validar_codigo_articulo()

## cargo-nuevo-vista

+agregar_cargo()
+listar_cargo()
+datos_cargo()
+editar_cargo()
+eliminar_cargo()
+buscar_cargo()
+validar_nombre_cargo()

## cargo-actualizar-vista

+datos_cargo()
+editar_cargo()
+validar_nombre_cargo()

## cliente-nuevo-vista

+agregar_cliente()
+listar_cliente()
+datos_cliente()
+editar_cliente()
+eliminar_cliente()
+buscar_cliente()
+validar_documento_cliente()
+validar_contacto_cliente()

## cliente-actualizar-vista

+datos_cliente()
+editar_cliente()
+validar_documento_cliente()
+validar_contacto_cliente()

## cliente-lista-vista

+listar_cliente()
+datos_cliente()
+editar_cliente()
+eliminar_cliente()
+paginar_cliente()

## cliente-buscar-vista

+buscar_cliente()
+listar_cliente()
+datos_cliente()
+limpiar_busqueda_cliente()

## company-vista

+datos_empresa()
+editar_empresa()
+validar_datos_empresa()

## descuento-nuevo-vista

+agregar_descuento()
+datos_descuento()
+editar_descuento()
+listar_sucursal()
+asignar_descuento_cliente()
+listar_clientes_descuento()
+validar_vigencia_descuento()
+validar_porcentaje_descuento()

## descuento-lista-vista

+listar_descuento()
+datos_descuento()
+editar_descuento()
+buscar_descuento()
+asignar_descuento_cliente()
+filtrar_descuento_sucursal()

## diagnostico-servicio-nuevo-vista

+agregar_diagnostico()
+listar_recepcion_servicio()
+datos_recepcion_servicio()
+datos_diagnostico()
+buscar_recepcion_servicio()
+seleccionar_recepcion_servicio()
+cargar_datos_vehiculo()
+validar_diagnostico()

## diagnostico-servicio-buscar-vista

+buscar_diagnostico()
+listar_diagnostico()
+datos_diagnostico()
+anular_diagnostico()
+ver_detalle_diagnostico()
+filtrar_diagnostico_estado()

## empleado-nuevo-vista

+agregar_empleado()
+listar_empleado()
+datos_empleado()
+editar_empleado()
+eliminar_empleado()
+buscar_empleado()
+listar_cargo()
+listar_sucursal()
+validar_documento_empleado()
+validar_sucursal_empleado()

## empleado-actualizar-vista

+datos_empleado()
+editar_empleado()
+listar_cargo()
+listar_sucursal()
+validar_documento_empleado()
+validar_sucursal_empleado()

## empleado-lista-vista

+listar_empleado()
+datos_empleado()
+editar_empleado()
+eliminar_empleado()
+paginar_empleado()

## empleado-buscar-vista

+buscar_empleado()
+listar_empleado()
+datos_empleado()
+limpiar_busqueda_empleado()

## empleado-equipo-vista

+agregar_equipo()
+listar_equipo()
+datos_equipo()
+editar_equipo()
+eliminar_equipo()
+listar_sucursal()
+listar_miembro_equipo()
+asignar_empleado_equipo()
+validar_equipo_sucursal()
+validar_nombre_equipo()

## empleado-equipo-actualizar-vista

+datos_equipo()
+editar_equipo()
+listar_sucursal()
+validar_equipo_sucursal()
+validar_nombre_equipo()

## empleado-equipo-asignar-vista

+datos_equipo()
+listar_empleado()
+cargar_empleados_equipo()
+asignar_empleado_equipo()
+validar_equipo_activo()
+validar_empleado_sucursal()

## empleado-equipo-miembros-vista

+datos_equipo()
+listar_miembro_equipo()
+quitar_miembro_equipo()
+validar_miembro_equipo()
+confirmar_quitar_miembro()

## factura-nuevo-vista

+agregar_factura_compra()
+listar_orden_compra()
+datos_orden_compra()
+agregar_detalle_factura()
+quitar_detalle_factura()
+calcular_total_factura()
+validar_detalle_factura()
+actualizar_stock()
+registrar_cuenta_pagar()
+registrar_libro_compra()
+validar_timbrado_factura()

## factura-buscar-vista

+buscar_factura_compra()
+listar_factura_compra()
+datos_factura_compra()
+anular_factura_compra()
+ver_detalle_factura()
+filtrar_factura_fecha()

## inventario-vista

+agregar_inventario()
+listar_inventario()
+datos_inventario()
+ajustar_inventario()
+anular_inventario()
+listar_articulo()
+cargar_detalle_inventario()
+calcular_diferencia_inventario()
+validar_cantidad_fisica()
+confirmar_ajuste_inventario()

## inventario-buscar-vista

+buscar_inventario()
+listar_inventario()
+datos_inventario()
+ajustar_inventario()
+anular_inventario()
+ver_detalle_inventario()
+filtrar_inventario_sucursal()

## notasCreDe-nuevo-vista

+agregar_nota_credito_debito()
+listar_factura_compra()
+datos_factura_compra()
+agregar_detalle_nota()
+quitar_detalle_nota()
+calcular_total_nota()
+validar_detalle_nota()
+actualizar_cuenta_pagar()
+actualizar_libro_compra()
+actualizar_stock()

## notasCreDe-buscar-vista

+buscar_nota_credito_debito()
+listar_nota_credito_debito()
+datos_nota_credito_debito()
+anular_nota_credito_debito()
+ver_detalle_nota()
+filtrar_nota_fecha()

## oc-nuevo-vista

+agregar_orden_compra()
+listar_presupuesto_compra()
+datos_presupuesto_compra()
+agregar_detalle_orden_compra()
+quitar_detalle_orden_compra()
+calcular_total_orden_compra()
+validar_detalle_orden_compra()
+validar_presupuesto_compra()

## oc-buscar-vista

+buscar_orden_compra()
+listar_orden_compra()
+datos_orden_compra()
+anular_orden_compra()
+ver_detalle_orden_compra()
+filtrar_orden_compra_fecha()

## ordenTrabajo-nuevo-vista

+agregar_orden_trabajo()
+listar_presupuesto_servicio()
+datos_presupuesto_servicio()
+listar_trabajo()
+listar_tecnico()
+seleccionar_presupuesto_servicio()
+validar_presupuesto_aprobado()
+validar_tecnico_responsable()

## ordenTrabajo-lista-vista

+listar_orden_trabajo()
+datos_orden_trabajo()
+asignar_tecnico_orden_trabajo()
+cerrar_orden_trabajo()
+anular_orden_trabajo()
+ver_detalle_orden_trabajo()
+validar_estado_orden_trabajo()

## ordenTrabajo-buscar-vista

+buscar_orden_trabajo()
+listar_orden_trabajo()
+datos_orden_trabajo()
+ver_detalle_orden_trabajo()
+filtrar_orden_trabajo_estado()

## ordenTrabajo-asignar-vista

+datos_orden_trabajo()
+asignar_tecnico_orden_trabajo()
+listar_trabajo()
+listar_tecnico()
+validar_tecnico_responsable()
+validar_orden_trabajo_asignable()

## pedido-nuevo-vista

+agregar_pedido()
+agregar_detalle_pedido()
+quitar_detalle_pedido()
+listar_articulo()
+buscar_articulo()
+calcular_total_pedido()
+validar_detalle_pedido()
+validar_articulo_repetido()

## pedido-lista-vista

+listar_pedido()
+datos_pedido()
+anular_pedido()
+ver_detalle_pedido()
+validar_estado_pedido()

## pedido-buscar-vista

+buscar_pedido()
+listar_pedido()
+datos_pedido()
+limpiar_busqueda_pedido()
+ver_detalle_pedido()

## presupuesto-nuevo-vista

+agregar_presupuesto_compra()
+listar_pedido()
+datos_pedido()
+agregar_detalle_presupuesto()
+quitar_detalle_presupuesto()
+calcular_total_presupuesto()
+validar_detalle_presupuesto()
+seleccionar_pedido()

## presupuesto-lista-vista

+listar_presupuesto_compra()
+datos_presupuesto_compra()
+anular_presupuesto_compra()
+ver_detalle_presupuesto_compra()
+validar_estado_presupuesto_compra()

## presupuesto-buscar-vista

+buscar_presupuesto_compra()
+listar_presupuesto_compra()
+datos_presupuesto_compra()
+limpiar_busqueda_presupuesto()
+ver_detalle_presupuesto_compra()

## presupuesto-servicio-nuevo-vista

+agregar_presupuesto_servicio()
+listar_diagnostico()
+datos_diagnostico()
+agregar_trabajo_presupuesto()
+quitar_trabajo_presupuesto()
+agregar_repuesto_presupuesto()
+quitar_repuesto_presupuesto()
+calcular_presupuesto_servicio()
+validar_detalle_presupuesto_servicio()
+seleccionar_diagnostico()

## presupuesto-servicio-lista-vista

+listar_presupuesto_servicio()
+datos_presupuesto_servicio()
+aprobar_presupuesto_servicio()
+anular_presupuesto_servicio()
+ver_detalle_presupuesto_servicio()
+validar_estado_presupuesto_servicio()

## presupuesto-servicio-buscar-vista

+buscar_presupuesto_servicio()
+listar_presupuesto_servicio()
+datos_presupuesto_servicio()
+ver_detalle_presupuesto_servicio()
+filtrar_presupuesto_servicio_estado()

## promocion-nuevo-vista

+agregar_promocion()
+datos_promocion()
+editar_promocion()
+listar_sucursal()
+validar_vigencia_promocion()
+validar_porcentaje_promocion()

## promocion-lista-vista

+listar_promocion()
+datos_promocion()
+editar_promocion()
+buscar_promocion()
+filtrar_promocion_sucursal()
+filtrar_promocion_vigencia()

## proveedor-nuevo-vista

+agregar_proveedor()
+listar_proveedor()
+datos_proveedor()
+editar_proveedor()
+eliminar_proveedor()
+buscar_proveedor()
+validar_ruc_proveedor()
+validar_contacto_proveedor()

## proveedor-actualizar-vista

+datos_proveedor()
+editar_proveedor()
+validar_ruc_proveedor()
+validar_contacto_proveedor()

## recepcionServicio-nuevo-vista

+agregar_recepcion_servicio()
+datos_cliente()
+agregar_cliente()
+datos_vehiculo()
+agregar_vehiculo()
+datos_reclamo_servicio()
+buscar_cliente()
+buscar_vehiculo()
+seleccionar_reclamo_servicio()
+cargar_fotos_recepcion()
+validar_datos_recepcion()

## recepcionServicio-buscar-vista

+buscar_recepcion_servicio()
+listar_recepcion_servicio()
+datos_recepcion_servicio()
+anular_recepcion_servicio()
+ver_detalle_recepcion()
+ver_fotos_recepcion()
+filtrar_recepcion_estado()

## reclamo-servicio-nuevo-vista

+agregar_reclamo_servicio()
+datos_cliente()
+datos_vehiculo()
+buscar_cliente()
+buscar_vehiculo()
+validar_datos_reclamo()

## reclamo-servicio-lista-vista

+listar_reclamo_servicio()
+buscar_reclamo_servicio()
+datos_reclamo_servicio()
+cerrar_reclamo_servicio()
+anular_reclamo_servicio()
+ver_detalle_reclamo()
+filtrar_reclamo_estado()

## registro-servicio-nuevo-vista

+agregar_registro_servicio()
+listar_orden_trabajo()
+datos_orden_trabajo()
+actualizar_stock()
+seleccionar_orden_trabajo()
+validar_orden_trabajo_cerrable()
+validar_repuestos_utilizados()

## registro-servicio-lista-vista

+listar_registro_servicio()
+datos_registro_servicio()
+anular_registro_servicio()
+ver_detalle_registro_servicio()
+validar_estado_registro_servicio()

## registro-servicio-buscar-vista

+buscar_registro_servicio()
+listar_registro_servicio()
+datos_registro_servicio()
+ver_detalle_registro_servicio()
+filtrar_registro_servicio_fecha()

## regla-comercial-nuevo-vista

+agregar_regla_comercial()
+datos_regla_comercial()
+editar_regla_comercial()
+listar_sucursal()
+validar_vigencia_regla()
+validar_prioridad_regla()

## regla-comercial-lista-vista

+listar_regla_comercial()
+datos_regla_comercial()
+editar_regla_comercial()
+buscar_regla_comercial()
+filtrar_regla_sucursal()
+filtrar_regla_vigencia()

## remision-nuevo-vista

+agregar_remision()
+listar_factura_compra()
+datos_factura_compra()
+seleccionar_factura_compra()
+validar_datos_remision()
+validar_fecha_remision()

## remision-buscar-vista

+buscar_remision()
+listar_remision()
+datos_remision()
+anular_remision()
+ver_detalle_remision()
+filtrar_remision_fecha()

## rol-nuevo-vista

+agregar_rol()
+listar_rol()
+datos_rol()
+editar_rol()
+eliminar_rol()
+buscar_rol()
+validar_nombre_rol()
+validar_estado_rol()

## rol-actualizar-vista

+datos_rol()
+editar_rol()
+validar_nombre_rol()
+validar_estado_rol()

## rol-permisos-vista

+listar_rol()
+permisos_por_rol()
+guardar_permisos_rol()
+cargar_permisos_rol()
+activar_eventos_permisos()
+marcar_permisos_modulo()
+sincronizar_modulo_permisos()
+validar_rol_seleccionado()

## sucursal-nuevo-vista

+agregar_sucursal()
+listar_sucursal()
+datos_sucursal()
+editar_sucursal()
+eliminar_sucursal()
+buscar_sucursal()
+validar_descripcion_sucursal()
+validar_estado_sucursal()

## sucursal-actualizar-vista

+datos_sucursal()
+editar_sucursal()
+validar_descripcion_sucursal()
+validar_estado_sucursal()

## transferencia-nuevo-vista

+agregar_transferencia()
+agregar_detalle_transferencia()
+quitar_detalle_transferencia()
+listar_sucursal()
+listar_articulo()
+validar_stock_transferencia()
+validar_sucursal_destino()
+calcular_total_transferencia()

## transferencia-historial-vista

+listar_transferencia()
+buscar_transferencia()
+datos_transferencia()
+anular_transferencia()
+ver_detalle_transferencia()
+filtrar_transferencia_estado()

## transferencia-recibir-vista

+listar_transferencia()
+datos_transferencia()
+recibir_transferencia()
+actualizar_stock()
+ver_detalle_transferencia()
+validar_transferencia_pendiente()

## usuario-nuevo-vista

+agregar_usuario()
+listar_usuario()
+datos_usuario()
+editar_usuario()
+eliminar_usuario()
+buscar_usuario()
+roles_por_usuario()
+guardar_roles_usuario()
+sucursal_por_usuario()
+asignar_sucursal_usuario()
+abrir_modal_roles_usuario()
+abrir_modal_sucursal_usuario()
+cargar_roles_usuario()
+cargar_sucursal_usuario()
+validar_credenciales_administrativas()

## usuario-actualizar-vista

+datos_usuario()
+editar_usuario()
+validar_credenciales_administrativas()
+validar_estado_usuario()

## vehiculo-nuevo-vista

+agregar_vehiculo()
+listar_vehiculo()
+datos_vehiculo()
+editar_vehiculo()
+eliminar_vehiculo()
+buscar_vehiculo()
+validar_chapa_vehiculo()
+validar_cliente_vehiculo()

## vehiculo-actualizar-vista

+datos_vehiculo()
+editar_vehiculo()
+validar_chapa_vehiculo()
+validar_cliente_vehiculo()

## reporte-articulos-vista

+generar_reporte_articulo()
+filtrar_reporte_articulo()

## reporte-clientes-vista

+generar_reporte_cliente()
+filtrar_reporte_cliente()

## reporte-compras-vista

+generar_reporte_compra()
+filtrar_reporte_compra()

## reporte-empleados-vista

+generar_reporte_empleado()
+filtrar_reporte_empleado()

## reporte-LibroCompras-vista

+generar_reporte_libro_compra()
+filtrar_reporte_libro_compra()

## reporte-movimientostock-vista

+generar_reporte_movimiento_stock()
+filtrar_reporte_movimiento_stock()

## reporte-ordenes-compra-vista

+generar_reporte_orden_compra()
+filtrar_reporte_orden_compra()

## reporte-orden-trabajo-vista

+generar_reporte_orden_trabajo()
+filtrar_reporte_orden_trabajo()

## reporte-pedidos-vista

+generar_reporte_pedido()
+filtrar_reporte_pedido()

## reporte-presupuestos-vista

+generar_reporte_presupuesto_compra()
+filtrar_reporte_presupuesto_compra()

## reporte-presupuesto-servicio-vista

+generar_reporte_presupuesto_servicio()
+filtrar_reporte_presupuesto_servicio()

## reporte-proveedores-vista

+generar_reporte_proveedor()
+filtrar_reporte_proveedor()

## reporte-recepcion-servicio-vista

+generar_reporte_recepcion_servicio()
+filtrar_reporte_recepcion_servicio()

## reporte-registro-servicio-vista

+generar_reporte_registro_servicio()
+filtrar_reporte_registro_servicio()

## reporte-stock-vista

+generar_reporte_stock()
+filtrar_reporte_stock()

## reporte-transferencias-vista

+generar_reporte_transferencia()
+filtrar_reporte_transferencia()
