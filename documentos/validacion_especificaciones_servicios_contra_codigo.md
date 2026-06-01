# Validación de especificaciones de servicios contra código

Archivo base revisado: `documentos/especificaciones_servicios_verificadas_tablas_depuradas.md`

Archivo limpio generado: `documentos/especificaciones_servicios_validada_codigo_limpia.md`

Alcance de esta validación: movimientos del módulo de servicios. El texto adjunto también contiene movimientos de compras; esos no se mezclaron en el archivo limpio de servicios.

## Criterio usado

Se validó cada caso de uso contra modelos, controladores, AJAX y vistas relacionadas. Las tablas se dejaron solo cuando el flujo consulta, inserta, actualiza o elimina datos propios del movimiento. No se incluyeron tablas usadas únicamente como dato derivado de otro movimiento si no forman parte real del caso.

## Cambios aplicados en el archivo limpio

### Generar Orden de Trabajo

Se quitaron de la descripción de tablas:

- `presupuesto_promocion`
- `promociones`

Motivo: en el flujo principal de OT no se usan para generar, completar ni anular una orden. Solo aparecen en `ordenTrabajoModelo::obtener_detalle_ot()` como consulta auxiliar de detalle, y además el join no representa el presupuesto original de la OT de forma confiable.

Se mantuvo:

- `presupuesto_servicio`
- `presupuesto_detalleservicio`
- `diagnostico_servicio`
- `diagnostico_detalle`
- `recepcion_servicio`
- `reclamo_servicio`
- `registro_servicio`
- `stock`

Motivo: esas tablas sí participan en validaciones, carga de datos, copiado de detalle, completar OT por reclamo, validación de stock o anulación.

### Anular OT por reclamo

Debe quedar documentado así:

> Si la OT proviene de reclamo, el sistema no modifica el estado del reclamo y deja el diagnóstico asociado nuevamente disponible para continuar el flujo del reclamo.

Motivo: el código fue corregido para no devolver el reclamo a activo. La anulación de OT por reclamo solo anula `orden_trabajo` y actualiza `diagnostico_servicio` a disponible.

### Registrar Reclamos de Clientes

Se ajustó la frase:

Antes:

> Si no quedan reclamos activos para el registro, el registro de servicio vuelve a estado activo.

Ahora:

> Si no quedan reclamos no anulados para el registro, el registro de servicio vuelve a estado activo.

Motivo: el código cuenta reclamos con `estado != 0`, no solo activos o en proceso.

## Validación por caso de uso

### Registrar Solicitud de Servicios

La documentación está alineada con el código.

Puntos confirmados:

- Cliente rápido consulta/inserta en `clientes`.
- Ciudad se busca en `ciudades` y se usa en el registro rápido de cliente.
- Vehículo rápido consulta/inserta en `vehiculos`, `modelo_auto` y `marcas`.
- Recepción inserta en `recepcion_servicio`.
- Fotos se registran en `recepcion_fotos`.
- Si la recepción proviene de reclamo, el reclamo pasa de activo a en proceso.
- Si la recepción proviene de reclamo y el reclamo requiere garantía, valida kilometraje contra el servicio original antes de registrar.
- Si el servicio original no tiene kilometraje de salida, no registra la recepción.
- Si el kilometraje actual supera el kilometraje de salida del servicio original más 5000 km, no registra la recepción e informa garantía vencida por kilometraje.
- Si se anula una recepción de reclamo, el reclamo vuelve a activo.

Mantener en tablas:

- `recepcion_servicio`
- `recepcion_fotos`
- `clientes`
- `vehiculos`
- `modelo_auto`
- `marcas`
- `ciudades`
- `usuarios`
- `reclamo_servicio`
- `registro_servicio`

Agregar explícitamente en Guardar y Flujo Alternativo la validación de kilometraje para reclamos con garantía: servicio original con kilometraje de salida y límite por kilometraje.

### Registrar Diagnóstico de Servicio

La documentación está alineada con el código.

Puntos confirmados:

- Busca recepciones activas.
- Registra cabecera en `diagnostico_servicio`.
- Registra detalle en `diagnostico_detalle`.
- Valida artículos activos para servicios y repuestos.
- Al guardar, cambia `recepcion_servicio` a diagnosticada/en proceso.
- Si el diagnóstico viene de reclamo válido, garantía y sin cobro, habilita OT directa.
- Si se genera OT directa por reclamo, se registra `orden_trabajo` con origen reclamo y estado pendiente de completar.
- Al anular diagnóstico, se anula `diagnostico_servicio` y se libera `recepcion_servicio`; no actualiza el estado del reclamo.

Mantener las tablas actuales. `reclamo_servicio` y `orden_trabajo` corresponden porque se consultan para validar/gestionar el flujo de reclamo.

### Registrar Promociones

La documentación está alineada con el código.

Puntos confirmados:

- Crea en `promociones`.
- Asocia artículos en `promocion_producto` si fueron seleccionados.
- Permite registrar promoción sin artículos, aunque no se aplicará hasta asociarlos.
- Lista con `promociones`, `usuarios` y `sucursales`.
- Edita `promociones` y reemplaza asociaciones en `promocion_producto`.
- Cambia estado en `promociones`.

Mantener tablas:

- `promociones`
- `promocion_producto`
- `articulos`
- `sucursales`
- `usuarios`

### Registrar y Gestionar Descuentos

La documentación está alineada con el código.

Puntos confirmados:

- Crea en `descuentos`.
- Asocia clientes en `descuento_cliente` si fueron seleccionados.
- Busca clientes activos en `clientes`.
- Lista con `descuentos`, `sucursales` y `usuarios`.
- Edita `descuentos`.
- Agrega o elimina relaciones en `descuento_cliente`.
- Los descuentos aplicables se consultan por cliente desde presupuesto.

Mantener tablas:

- `descuentos`
- `descuento_cliente`
- `clientes`
- `sucursales`
- `usuarios`

### Registrar Presupuesto

La documentación está alineada con el código actual.

Puntos confirmados:

- Puede nacer desde diagnóstico o como preliminar.
- Desde diagnóstico carga cabecera y detalle técnico desde `diagnostico_servicio`, `diagnostico_detalle`, `recepcion_servicio`, `clientes`, `vehiculos`, `modelo_auto`, `marcas`, `articulos` y `stock`.
- Busca servicios/repuestos en `articulos` y valida stock en `stock`.
- Evalúa promociones con `promociones` y `promocion_producto`.
- Consulta descuentos con `descuentos` y `descuento_cliente`.
- Al guardar, conserva precios, promociones y descuentos aceptados en pantalla; no recalcula vigencia comercial al momento final si ya fue aceptado.
- Registra cabecera en `presupuesto_servicio`.
- Registra detalle en `presupuesto_detalleservicio`.
- Registra promociones aplicadas en `presupuesto_promocion`.
- Registra descuentos aplicados en `presupuesto_descuento`.
- Si proviene de diagnóstico, actualiza `diagnostico_servicio`.
- Si convierte preliminar, marca el preliminar como convertido.
- Al aprobar, actualiza `presupuesto_servicio`.
- Al anular, valida que no tenga OT activa y devuelve diagnóstico a disponible cuando corresponde.

Mantener las tablas actuales de presupuesto, incluyendo promociones y descuentos, porque aquí sí forman parte real del movimiento.

### Generar Orden de Trabajo

La documentación queda alineada con el código en el archivo limpio.

Puntos confirmados:

- Genera OT desde presupuesto aprobado.
- Valida que el presupuesto sea de diagnóstico; el preliminar no genera OT directamente.
- Copia detalle desde `presupuesto_detalleservicio` hacia `orden_trabajo_detalle`.
- Actualiza `presupuesto_servicio` a OT generada.
- Completa OT por reclamo solo si la OT está en estado pendiente de completar.
- Al completar OT por reclamo, elimina detalle anterior de la OT y registra trabajos/repuestos confirmados en `orden_trabajo_detalle`.
- Valida stock para repuestos al completar OT por reclamo, pero no descuenta stock en ese momento.
- Al anular OT de presupuesto, devuelve el presupuesto a aprobado.
- Al anular OT de reclamo, no modifica `reclamo_servicio`; solo deja `diagnostico_servicio` disponible.

Tablas quitadas en OT:

- `presupuesto_promocion`
- `promociones`

### Registrar Servicios y Registrar Insumos Utilizados

La documentación está alineada con el código.

Puntos confirmados:

- Busca OT activa y sin registro activo previo.
- Registra cabecera en `registro_servicio`.
- Copia detalle de OT a `registro_servicio_detalle`.
- Descuenta stock para productos/insumos del detalle.
- Registra movimientos en `movimientostock`.
- Cierra `orden_trabajo`.
- Finaliza `recepcion_servicio`.
- Si corresponde a reclamo, cierra `reclamo_servicio`.
- Al anular, revierte stock, anula `registro_servicio`, reactiva `orden_trabajo`, reabre `recepcion_servicio` y reabre `reclamo_servicio` si estaba cerrado.

Mantener tablas actuales.

### Registrar Reclamos de Clientes

La documentación fue ajustada en el archivo limpio.

Puntos confirmados:

- Busca registros activos en `registro_servicio`.
- Usa `registro_servicio_detalle` y `articulos` para validar detalles reclamados.
- Registra cabecera en `reclamo_servicio`.
- Registra detalle en `reclamo_servicio_detalle`.
- Cambia `registro_servicio` a estado con reclamo.
- Solo permite anular reclamos activos sin proceso iniciado.
- No permite anular si ya existe recepción no anulada.
- Al anular, si no quedan reclamos no anulados para el mismo registro, devuelve `registro_servicio` a activo.

Corrección aplicada en limpio:

- Cambiar "reclamos activos" por "reclamos no anulados" en la post condición y en el flujo de anulación.

### Registrar Salida de Insumos

La documentación está alineada con el código.

Puntos confirmados:

- Busca empleado activo por sucursal en `empleados`.
- Busca insumos activos con stock en `articulos` y `stock`.
- La pantalla exige al menos dos caracteres para buscar empleado e insumo.
- Registra cabecera en `salida_insumo`.
- Registra detalle en `salida_insumo_detalle`.
- Registra salida en `movimientostock`.
- Descuenta stock en `stock`.
- Lista salidas con `salida_insumo`, `empleados` y `usuarios`.
- Anula salida activa, registra movimiento inverso en `movimientostock`, devuelve stock y anula `salida_insumo`.

Mantener tablas:

- `salida_insumo`
- `salida_insumo_detalle`
- `articulos`
- `stock`
- `movimientostock`
- `empleados`
- `usuarios`

## Observaciones técnicas para posible limpieza de código

Estas observaciones no requieren cambio documental inmediato, pero conviene tenerlas presentes.

### Orden de Trabajo

En el listado de OT, `presupuesto_servicio` se une principalmente para mostrar el número de presupuesto. Como `orden_trabajo` ya tiene `idpresupuesto_servicio`, ese join podría simplificarse si no se usa otro dato del presupuesto.

En creación de OT por reclamo, el código aún usa `COALESCE(rs.id_cliente, r.id_cliente)` y `COALESCE(rs.id_vehiculo, r.id_vehiculo)`. Si todos los reclamos ya tienen `id_cliente` e `id_vehiculo`, se puede quitar el fallback contra `recepcion_servicio`. Si existen datos históricos incompletos, conviene dejarlo.

### Orden de Trabajo - detalle

`ordenTrabajoModelo::obtener_detalle_ot()` todavía consulta `presupuesto_promocion` y `promociones`, pero esa consulta no sostiene el flujo principal de OT. Si se usa para impresión o vista auxiliar, conviene revisar el join porque relaciona `pp.idpresupuesto_servicio` con `d.idorden_trabajo`, que no representa necesariamente el presupuesto original.
