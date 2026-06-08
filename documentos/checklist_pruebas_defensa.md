# Checklist de pruebas para defensa

Este documento sirve para validar el sistema antes de la defensa. La idea es probar lo importante sin hacer cambios grandes: acceso, permisos, compras, servicios, stock, informes, PDF/CSV y anulaciones.

## 1. Preparacion

| Paso | Accion | Resultado esperado | OK | Observacion |
|---|---|---|---|---|
| 1 | Levantar Apache y MySQL desde XAMPP | Ambos servicios quedan activos |  |  |
| 2 | Abrir el sistema en navegador | Carga la pantalla de login |  |  |
| 3 | Verificar que la base de datos usada sea la correcta | El sistema apunta a la BD final de prueba |  |  |
| 4 | Ejecutar `database/create_anulacion_auditoria.sql` si aun no se ejecuto | Existe la tabla `anulacion_auditoria` |  |  |
| 5 | Iniciar sesion con usuario administrador | Accede al panel principal |  |  |
| 6 | Cerrar sesion e ingresar con usuario limitado | El menu muestra solo permisos asignados |  |  |

Consulta util:

```sql
SHOW TABLES LIKE 'anulacion_auditoria';
```

## 2. Login, permisos y menu

| Paso | Accion | Resultado esperado | OK | Observacion |
|---|---|---|---|---|
| 1 | Ingresar con usuario administrador | Muestra modulos de compras, servicios, mantenimiento e informes |  |  |
| 2 | Ingresar con usuario solo lectura | Muestra opciones de consulta/lista, no pantallas de alta como entrada principal |  |  |
| 3 | Probar permiso `servicio.ver` sin subpermisos | No muestra servicios si no hay ningun subitem permitido |  |  |
| 4 | Agregar `servicio.descuento.ver` | Muestra Descuentos y entra a `descuento-lista/` |  |  |
| 5 | Probar `servicio.insumo.ver` | Muestra Registro de Insumos y entra a busqueda/listado |  |  |
| 6 | Intentar entrar por URL directa a una vista sin permiso | El sistema bloquea o redirige |  |  |

Punto para defensa:

> El menu oculta opciones por permiso, pero la seguridad real tambien se valida en controladores y vistas.

## 3. Compras

| Paso | Accion | Resultado esperado | OK | Observacion |
|---|---|---|---|---|
| 1 | Crear pedido de compra | Pedido queda pendiente |  |  |
| 2 | Generar presupuesto de compra desde pedido o manual | Presupuesto queda registrado con proveedor y detalle |  |  |
| 3 | Generar orden de compra desde presupuesto | Orden queda pendiente/procesada segun flujo |  |  |
| 4 | Registrar compra/factura desde orden | Compra queda registrada y actualiza stock |  |  |
| 5 | Revisar libro de compras | Comprobante aparece con importes fiscales |  |  |
| 6 | Crear nota de credito/debito si aplica | Nota queda registrada con referencia a compra |  |  |
| 7 | Registrar remision si aplica | Remision queda disponible para busqueda |  |  |
| 8 | Registrar transferencia entre sucursales | Transferencia aparece en historial |  |  |
| 9 | Recibir transferencia | Stock destino se actualiza |  |  |
| 10 | Crear ajuste de inventario | Ajuste genera movimiento de stock con costo |  |  |

Consultas utiles:

```sql
SELECT * FROM compra_cabecera ORDER BY idcompra_cabecera DESC LIMIT 5;
```

```sql
SELECT * FROM libro_compra ORDER BY idlibro_compra DESC LIMIT 5;
```

```sql
SELECT MovStockId, TipoMovStockId, MovStockArticuloId, MovStockCantidad, MovStockCosto, MovStockSigno, MovStockReferencia
FROM movimientostock
ORDER BY MovStockId DESC
LIMIT 10;
```

## 4. Servicios

| Paso | Accion | Resultado esperado | OK | Observacion |
|---|---|---|---|---|
| 1 | Crear recepcion de servicio | Recepcion queda registrada con cliente y vehiculo |  |  |
| 2 | Agregar diagnostico | Diagnostico queda asociado a la recepcion |  |  |
| 3 | Agregar repuestos sugeridos en diagnostico | Se registran como necesidad tecnica, sin descontar stock |  |  |
| 4 | Crear presupuesto de servicio | Valida precios, descuentos/promociones y disponibilidad |  |  |
| 5 | Aprobar presupuesto de servicio | Queda disponible para generar orden de trabajo |  |  |
| 6 | Generar orden de trabajo | OT queda registrada con tecnico/equipo |  |  |
| 7 | Registrar servicio finalizado | Registro queda generado y descuenta repuestos si corresponde |  |  |
| 8 | Registrar salida de insumos | Insumo sale de stock y queda trazable |  |  |
| 9 | Crear reclamo desde servicio | Reclamo queda asociado al registro |  |  |
| 10 | Consultar historial del cliente/vehiculo | Se visualiza trazabilidad del servicio |  |  |

Punto para defensa:

> Diagnostico es tecnico. El cobro o compromiso de stock se formaliza en presupuesto, orden o registro de servicio, segun el caso.

## 5. Anulaciones y auditoria

| Paso | Accion | Resultado esperado | OK | Observacion |
|---|---|---|---|---|
| 1 | Anular pedido | Pide motivo obligatorio |  |  |
| 2 | Anular presupuesto de compra | Cambia estado y guarda auditoria |  |  |
| 3 | Anular orden de compra | Cambia estado y guarda auditoria |  |  |
| 4 | Anular compra | Cambia estado, guarda auditoria y revierte stock si aplica |  |  |
| 5 | Anular recepcion de servicio | Cambia estado y guarda auditoria |  |  |
| 6 | Anular presupuesto de servicio | Cambia estado y guarda auditoria |  |  |
| 7 | Anular orden de trabajo | Cambia estado y guarda auditoria |  |  |
| 8 | Anular registro de servicio | Cambia estado, guarda auditoria y revierte stock si aplica |  |  |
| 9 | Intentar anular sin motivo | El sistema no permite continuar |  |  |
| 10 | Intentar anular un registro ya anulado | El sistema informa que no corresponde o no duplica el proceso |  |  |

Consulta util:

```sql
SELECT *
FROM anulacion_auditoria
ORDER BY fecha_anulacion DESC
LIMIT 20;
```

Validar:

- `modulo`
- `tabla_afectada`
- `id_registro`
- `id_sucursal`
- `estado_anterior`
- `estado_nuevo`
- `motivo`
- `usuario_anula`
- `fecha_anulacion`
- `referencia`

## 6. Stock, movimientos y kardex

| Paso | Accion | Resultado esperado | OK | Observacion |
|---|---|---|---|---|
| 1 | Registrar compra con articulo | Genera entrada en `movimientostock` |  |  |
| 2 | Registrar servicio con repuesto | Genera salida en `movimientostock` |  |  |
| 3 | Registrar ajuste positivo | Genera entrada o ajuste positivo |  |  |
| 4 | Registrar ajuste negativo | Genera salida o ajuste negativo |  |  |
| 5 | Anular compra | Genera movimiento inverso |  |  |
| 6 | Anular registro de servicio | Genera movimiento inverso |  |  |
| 7 | Consultar informe de movimientos de stock | Muestra naturaleza Entrada/Salida/Ajuste |  |  |
| 8 | Verificar precios en movimientos de stock | Muestra costo unitario, precio venta unitario e importe costo |  |  |
| 9 | Consultar kardex por articulo y sucursal | Muestra saldo inicial, entradas, salidas y saldo final |  |  |
| 10 | Filtrar kardex por naturaleza | Mantiene lectura cronologica y datos coherentes |  |  |

Consultas utiles:

```sql
SELECT MovStockId, TipoMovStockId, MovStockArticuloId, MovStockCantidad, MovStockCosto, MovStockPrecioVenta, MovStockSigno, MovStockReferencia
FROM movimientostock
ORDER BY MovStockFechaHora DESC, MovStockId DESC
LIMIT 20;
```

```sql
SELECT *
FROM stock
ORDER BY id_sucursal, id_articulo
LIMIT 20;
```

Punto para defensa:

> El kardex se ordena cronologicamente porque muestra la evolucion del saldo. Los informes generales se ordenan descendente para ver primero lo mas reciente.

## 7. Informes referenciales

| Paso | Accion | Resultado esperado | OK | Observacion |
|---|---|---|---|---|
| 1 | Abrir Informes Referenciales | No carga datos hasta previsualizar |  |  |
| 2 | Probar Proveedores | Tabla, PDF y CSV funcionan |  |  |
| 3 | Probar Clientes | Tabla, PDF y CSV funcionan |  |  |
| 4 | Probar Vehiculos | Tabla, PDF y CSV funcionan |  |  |
| 5 | Probar Sucursales | Tabla, PDF y CSV funcionan |  |  |
| 6 | Probar Articulos | Tabla, PDF y CSV funcionan |  |  |
| 7 | Probar Marcas | Tabla, PDF y CSV funcionan |  |  |
| 8 | Probar Categorias | Tabla, PDF y CSV funcionan |  |  |
| 9 | Probar Usuarios | Tabla, PDF y CSV funcionan |  |  |
| 10 | Validar CSV con acentos | Excel muestra acentos correctamente |  |  |

## 8. Informes de movimientos

| Paso | Accion | Resultado esperado | OK | Observacion |
|---|---|---|---|---|
| 1 | Abrir Informes de Movimientos | Carga la vista sin demora excesiva |  |  |
| 2 | Cambiar tipo de informe | Solo muestra filtros correspondientes |  |  |
| 3 | Probar Pedidos | Resumen, detalle, PDF y CSV funcionan |  |  |
| 4 | Probar Presupuestos de compra | Resumen, detalle, PDF y CSV funcionan |  |  |
| 5 | Probar Ordenes de compra | Resumen, detalle, PDF y CSV funcionan |  |  |
| 6 | Probar Compras | Resumen, detalle, PDF y CSV funcionan |  |  |
| 7 | Probar Libro de compras | Muestra campos fiscales y totales tributarios |  |  |
| 8 | Probar Stock | Muestra stock por articulo/sucursal |  |  |
| 9 | Probar Transferencias | Muestra origen, destino y estado |  |  |
| 10 | Probar Movimientos de stock | Muestra naturaleza, costos e importes |  |  |
| 11 | Probar Kardex | Obliga articulo y sucursal; calcula saldos |  |  |
| 12 | Probar Recepcion servicio | Previsualiza, PDF y CSV funcionan |  |  |
| 13 | Probar Presupuesto servicio | Resumen, detalle, PDF y CSV funcionan |  |  |
| 14 | Probar Orden trabajo | Resumen, detalle, PDF y CSV funcionan |  |  |
| 15 | Probar Registro servicio | Resumen, detalle, PDF y CSV funcionan |  |  |

Validar visualmente:

- Botones alineados despues de previsualizar.
- Graficos legibles al 100% de zoom.
- Tablas sin columnas montadas.
- PDF con formato formal.
- CSV abre en Excel con acentos correctos.

## 9. PDF y CSV

| Paso | Accion | Resultado esperado | OK | Observacion |
|---|---|---|---|---|
| 1 | Generar PDF referencial | Usa encabezado formal, tabla limpia y pie con paginas |  |  |
| 2 | Generar PDF movimientos | Usa formato formal y orientacion correcta |  |  |
| 3 | Generar PDF libro compras | Muestra columnas fiscales legibles |  |  |
| 4 | Generar CSV referencial | Acentos correctos en Excel |  |  |
| 5 | Generar CSV movimientos | Respeta filtros aplicados |  |  |
| 6 | Exportar sin resultados | No rompe; muestra o exporta vacio correctamente |  |  |

## 10. Casos limite

| Paso | Accion | Resultado esperado | OK | Observacion |
|---|---|---|---|---|
| 1 | Buscar cliente inexistente en informe | No rompe; muestra sin registros |  |  |
| 2 | Buscar articulo inexistente en informe | No rompe; muestra sin registros |  |  |
| 3 | Kardex con articulo sin movimientos | No rompe; muestra sin registros o saldo cero |  |  |
| 4 | Cambiar informe despues de filtrar | Limpia u oculta filtros que no corresponden |  |  |
| 5 | Limpiar filtros | Restablece campos y oculta resultados anteriores |  |  |
| 6 | Intentar eliminar referencial usado | No rompe; inactiva o informa restriccion |  |  |
| 7 | Eliminar de nuevo un registro inactivo | No muestra warning PHP ni `null` |  |  |
| 8 | Navegar con usuario sin permiso | No muestra accesos no autorizados |  |  |

## 11. Evidencia recomendada para defensa

Preparar capturas o ejemplos de:

- Menu con permisos.
- Informe referencial exportado.
- Informe de movimientos en vista resumen.
- Informe de movimientos en vista detallada.
- Libro de compras con columnas fiscales.
- Kardex con saldo inicial/final.
- Anulacion con motivo.
- Registro en `anulacion_auditoria`.
- Movimiento inverso en `movimientostock`.
- PDF con formato formal.

## 12. Frases utiles

**Sobre informes generales y detalle**

> Los informes generales dan una vision ejecutiva del documento. La vista detallada permite auditar las lineas internas, como articulos, cantidades, precios y subtotales.

**Sobre kardex**

> El kardex muestra la evolucion cronologica del stock de un articulo por sucursal, incluyendo entradas, salidas, saldo inicial y saldo final.

**Sobre anulaciones**

> El sistema no elimina movimientos criticos. Cambia el estado, solicita motivo y registra auditoria con usuario, fecha, modulo y documento afectado.

**Sobre diagnostico**

> El diagnostico es tecnico. No descuenta stock ni cobra automaticamente. La decision comercial se formaliza en presupuesto, orden o registro de servicio.

**Sobre seguridad por permisos**

> La interfaz oculta opciones no permitidas, pero la validacion importante tambien se realiza en backend antes de ejecutar acciones.

## 13. Criterio final de aprobacion

Se considera listo para defensa cuando:

- El login funciona con usuario administrador y usuario limitado.
- El menu respeta permisos y no manda a pantallas incorrectas para permisos de solo lectura.
- Compras y servicios completan sus flujos principales.
- Stock y kardex son coherentes.
- Anulaciones piden motivo y guardan auditoria.
- Informes previsualizan, filtran, exportan PDF y CSV.
- PDF sale con formato formal.
- CSV abre en Excel con acentos correctos.
- No aparecen errores PHP, warnings, respuestas `null` ni errores de JavaScript visibles.

