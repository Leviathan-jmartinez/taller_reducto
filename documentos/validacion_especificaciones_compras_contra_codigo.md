# Validación de especificaciones de compras contra código

Archivo base revisado: `pasted-text.txt`.
Archivo limpio generado: `documentos/especificaciones_compras_validada_codigo_limpia.md`.

## Criterio usado para tablas

Se incluyeron tablas que el movimiento consulta, inserta o actualiza directamente en controladores/modelos.
No se agregaron tablas solo por relación conceptual, ni por valores que vienen desde sesión.

## Registrar Pedido

Cambios recomendados sobre el texto original:
Agregar que el pedido no actualiza stock; solo consulta `stock` para mostrar el stock actual.
Agregar el flujo Buscar e Imprimir, porque el código consulta cabecera y detalle para listado/PDF.
En Anular, mantener que no se puede anular si el pedido está procesado.
En Post Condición, cambiar mensajes genéricos por efectos reales: cabecera en `pedido_cabecera`, detalle en `pedido_detalle`, disponible para presupuesto mientras esté pendiente.

Tablas correctas:
`pedido_cabecera`, `pedido_detalle`, `articulos`, `stock`, `usuarios`.

Código validado:
`controladores/pedidoControlador.php`, `modelos/pedidoModelo.php`, `ajax/pedidoAjax.php`.

## Registrar Presupuesto de Compra

Cambios recomendados sobre el texto original:
Corregir el menú inicial: corresponde a Presupuesto de Compra, no Nuevo Pedido.
Agregar que se genera desde un pedido pendiente de la sucursal.
Agregar que al guardar se actualiza `pedido_cabecera` como procesado.
Agregar que al anular se devuelve el pedido a pendiente cuando corresponde.
En flujo alternativo, reemplazar textos genéricos por validaciones reales: pedido, proveedor, artículos, fecha, precios/importes y presupuesto procesado.

Tablas correctas:
`presupuesto_compra`, `presupuesto_detalle`, `pedido_cabecera`, `pedido_detalle`, `articulos`, `proveedores`, `usuarios`.

Código validado:
`controladores/presupuestoControlador.php`, `modelos/presupuestoModelo.php`, `ajax/presupuestoAjax.php`, `vistas/inc/presupuestoCompra.php`.

## Generar Orden de Compra

Cambios recomendados sobre el texto original:
Agregar este caso si no está separado, porque el código maneja orden de compra como movimiento propio.
Documentar dos entradas: desde presupuesto y directa sin presupuesto.
Agregar que desde presupuesto se validan cantidades contra el detalle presupuestado.
Agregar que el detalle de OC registra `cantidad_pendiente`, usada luego por compras.
Agregar que al generar desde presupuesto se actualiza `presupuesto_compra`.
En anulación, indicar que no se permite anular una OC ya procesada por compra.

Tablas correctas:
`orden_compra`, `orden_compra_detalle`, `presupuesto_compra`, `presupuesto_detalle`, `proveedores`, `articulos`, `articulo_proveedor`, `usuarios`.

Código validado:
`controladores/ordencompraControlador.php`, `modelos/ordencompraModelo.php`, `ajax/ordencompraAjax.php`, `vistas/inc/ordencompra.php`.

## Registrar Compras

Cambios recomendados sobre el texto original:
Mantener que puede cargarse desde OC, pero agregar que también puede cargarse directa.
Agregar validación de factura duplicada por proveedor, sucursal, número y timbrado.
Agregar `tipo_impuesto`, porque se consulta para el detalle e IVA.
Agregar `articulo_proveedor`, porque se registra o actualiza la relación artículo-proveedor.
Agregar `cuentas_a_pagar` solo cuando la compra es a crédito.
Agregar `libro_compra`, porque siempre se registra el comprobante de compra.
Agregar que al anular se revierte stock, movimientos, cuentas, libro y cantidades pendientes de OC cuando corresponda.

Tablas correctas:
`compra_cabecera`, `compra_detalle`, `proveedores`, `orden_compra`, `orden_compra_detalle`, `articulos`, `tipo_impuesto`, `articulo_proveedor`, `stock`, `movimientostock`, `cuentas_a_pagar`, `libro_compra`, `usuarios`.

Código validado:
`controladores/compraControlador.php`, `modelos/compraModelo.php`, `ajax/compraAjax.php`, `vistas/inc/compra.php`.

## Registrar Nota de Remisión de Compra

Cambios recomendados sobre el texto original:
Especificar que la remisión parte de una compra activa.
Indicar que no actualiza stock; solo registra `nota_remision` y `nota_remision_detalle`.
Mantener `compra_cabecera` y `compra_detalle` porque se consultan para cargar factura y artículos.
Agregar que el tipo de remisión registrado es recepción compra.

Tablas correctas:
`nota_remision`, `nota_remision_detalle`, `compra_cabecera`, `compra_detalle`, `articulos`, `proveedores`, `usuarios`.

Código validado:
`controladores/remisionControlador.php`, `modelos/remisionModelo.php`, `ajax/remisionAjax.php`, `vistas/inc/remisiones.php`.

## Registrar Notas de Crédito y Débito de Compra

Cambios recomendados sobre el texto original:
Documentar crédito y débito en el mismo movimiento, porque el código usa `nota_compra`.
Agregar validación de nota duplicada por proveedor, sucursal, tipo, número y timbrado.
Agregar que la nota de crédito no puede superar el total disponible de la factura.
Agregar que stock solo se toca cuando la nota tiene movimiento de devolución.
Agregar que al anular se registra impacto inverso en cuentas a pagar, se revierte stock si correspondía y se anula libro de compra.

Tablas correctas:
`nota_compra`, `nota_compra_detalle`, `compra_cabecera`, `compra_detalle`, `proveedores`, `articulos`, `tipo_impuesto`, `cuentas_a_pagar`, `libro_compra`, `stock`, `movimientostock`, `usuarios`.

Código validado:
`controladores/notasCreDeControlador.php`, `modelos/notasCreDeModelo.php`, `ajax/notasCreDeAjax.php`, `vistas/inc/notasCreDe.php`.

## Generar Transferencia entre Sucursales

Cambios recomendados sobre el texto original:
Separar claramente generación y recepción.
Agregar que al generar se descuenta stock de sucursal origen.
Agregar que se genera nota de remisión de transferencia.
Agregar `sucursal_documento` y `timbrado`, porque el código los usa para la numeración de remisión.
Agregar que al recibir se incrementa stock en destino.
Agregar que si hay recepción parcial, el sistema puede generar una nueva transferencia por faltantes.

Tablas correctas:
`transferencia_stock`, `transferencia_stock_detalle`, `articulos`, `stock`, `movimientostock`, `sucursales`, `nota_remision`, `nota_remision_detalle`, `sucursal_documento`, `timbrado`, `articulo_proveedor`, `usuarios`.

Código validado:
`controladores/transferenciaControlador.php`, `modelos/transferenciaModelo.php`, `ajax/transferenciaAjax.php`, `vistas/inc/transferenciaJS.php`, `vistas/inc/transferenciaRecibirJS.php`.

## Registrar Ajuste de Inventario

Cambios recomendados sobre el texto original:
Documentar las etapas reales: crear inventario, cargar conteo físico, aplicar ajuste y anular.
Agregar que el inventario inicial registra stock teórico y cantidad física inicial.
Agregar que aplicar ajuste actualiza `stock` y registra `movimientostock`.
Agregar que anular un ajuste aplicado revierte diferencias en stock.
No incluir sucursales como tabla principal si solo se usa la sucursal de sesión; en el código revisado el movimiento trabaja con `sucursal_id` y sesión.

Tablas correctas:
`ajuste_inventario`, `ajuste_inventario_detalle`, `articulos`, `stock`, `movimientostock`, `articulo_proveedor`, `categorias`, `proveedores`, `usuarios`.

Código validado:
`controladores/inventarioControlador.php`, `modelos/inventarioModelo.php`, `ajax/inventarioAjax.php`, `vistas/inc/inventario.php`.
