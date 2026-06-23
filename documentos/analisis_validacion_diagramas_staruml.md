# Analisis de validacion de diagramas StarUML

Fecha de revision: 22/06/2026  
Archivo revisado: `documentos/Diagramas_2026 - 2 modulos.mdj`  
Documento contrastado: `documentos/TesisAnalisisYDiseno.docx`

## 1. Alcance de la revision

Se reviso el archivo `.mdj` actualizado de StarUML y se comparo contra el alcance documentado en el Word de analisis y diseno.

La revision se enfoco en:

- cantidad y tipos de diagramas;
- paquetes vigentes y paquetes legacy;
- diagramas que sobran respecto al alcance defendido;
- diagramas posiblemente duplicados;
- diagramas vacios o casi vacios;
- diferencias contra el documento Word;
- recomendaciones sobre imagenes que conviene reemplazar en el Word.

Nota: esta revision analiza la estructura interna del `.mdj`. No reemplaza una revision visual final en StarUML para verificar alineacion, solapamientos o legibilidad de las imagenes exportadas.

## 2. Resumen del archivo StarUML

El archivo contiene dos bloques principales:

- `old`: paquete legacy.
- `Diagramas`: paquete actual.

Conteo total detectado:

```text
Total de diagramas: 293
```

Distribucion general:

```text
UMLActivityDiagram:     9
UMLClassDiagram:      107
UMLDeploymentDiagram:   1
UMLProfileDiagram:      3
UMLSequenceDiagram:   149
UMLUseCaseDiagram:     24
```

Distribucion del paquete actual `Diagramas`:

```text
UMLUseCaseDiagram:     15
UMLActivityDiagram:     3
UMLSequenceDiagram:   111
UMLClassDiagram:       55
UMLDeploymentDiagram:   1
```

Distribucion del paquete `old`:

```text
UMLUseCaseDiagram:      9
UMLActivityDiagram:     6
UMLSequenceDiagram:    38
UMLClassDiagram:       52
UMLProfileDiagram:      3
```

## 3. Resultado general

El archivo actualizado contiene los diagramas necesarios para defender el sistema de compras y servicios, pero todavia conserva diagramas que no pertenecen al alcance final defendido o que conviene mover a legacy/no exportar.

La estructura principal es util y defendible, pero antes de exportar imagenes finales al Word conviene limpiar o aislar:

- diagramas de ventas;
- diagramas de facturacion;
- diagramas de caja/cobros;
- cargos como referencial independiente;
- duplicados `_3`;
- nombres con codificacion rota;
- diagramas vacios o casi vacios.

## 4. Diagramas que coinciden con el alcance del Word

El Word contiene y el `.mdj` cubre, de forma general, estos bloques:

- Casos de uso de negocio.
- Mantener referenciales.
- Referenciales de compras.
- Referenciales de servicios.
- Referenciales de seguridad.
- Gestion de compras.
- Gestion de servicios.
- Informes referenciales.
- Informes de movimientos de compras.
- Informes de movimientos de servicios.
- Diagrama de actividades de compras.
- Diagrama de actividades de servicios.
- Diagramas de secuencia para compras.
- Diagramas de secuencia para servicios.
- Diagramas de secuencia para referenciales.
- Diagramas de clases de movimientos.
- Diagramas de clases de referenciales.
- Diagrama de despliegue.

Esto es positivo: no se detecta una ausencia estructural grande.

## 5. Observaciones importantes

### 5.1. El paquete `old` no debe exportarse al Word final

El `.mdj` conserva un paquete `old` con 108 diagramas aproximadamente.

Recomendacion:

```text
No usar imagenes exportadas desde el paquete old en el documento final.
```

Defensa:

```text
El paquete old se conserva como respaldo historico del modelado, pero los diagramas defendidos corresponden al paquete Diagramas.
```

### 5.2. Hay diagramas de Facturacion y Ventas en el paquete actual

En el paquete actual aparecen diagramas como:

- `Caso de Uso Referenciales Facturacion`
- `Caso de Uso Gestion de Facturacion`
- `Aventas`
- `Gestionar Ventas`
- `Agregar Venta`
- `Anular Venta`
- `Agregar Cobro`
- `Anular Cobro`
- `Apertura de Caja`
- `Cierre de Caja`
- `Agregar arqueo de Caja`
- `Anular Arqueo de Caja`
- `Gestionar cobranzas por forma de Cobro`
- `Gestionar notas de credito y debito` de facturacion/ventas

Estos diagramas no forman parte del alcance central defendido en el Word actual, que esta enfocado en compras y servicios.

Recomendacion:

```text
Mover estos diagramas a legacy/no defendido, o mantenerlos en StarUML pero no exportarlos al Word.
```

Nivel de importancia: alto.

Motivo:

Si aparecen imagenes de facturacion/ventas en la tesis, el evaluador puede preguntar por un modulo que no se esta defendiendo completamente.

### 5.3. Cargos sigue apareciendo como diagrama propio

En el paquete actual aparecen:

- secuencia `agregar cargo`;
- secuencia `editar cargo`;
- secuencia `eliminar cargo`;
- clase `Cargos`.

Como se definio que cargos no se va a defender como referencial principal, conviene no exportar esos diagramas como parte de la tesis final.

Recomendacion:

```text
Mantener cargos solo como tabla auxiliar interna de empleados, no como caso de uso ni diagrama principal defendido.
```

Nivel de importancia: medio.

### 5.4. Diagramas de informes duplicados con sufijo `_3`

Se detectaron:

- `Caso de uso informe movimientos compras`
- `Caso de uso informe movimientos compras_3`
- `Caso de uso informe movimientos servicios`
- `Caso de uso informe movimientos servicios_3`

Recomendacion:

```text
Verificar cual es la version final. Eliminar, mover a legacy o no exportar los diagramas con sufijo _3 si son copias de trabajo.
```

Nivel de importancia: medio.

### 5.5. Diagramas con nombres genericos duplicados

Se detectaron nombres repetidos como:

- `Agregar`
- `Anular`
- `Agregar Presupuesto`
- `Anular Presupuesto`
- `Agregar ND NC`
- `Anular ND NC`
- varios informes con el mismo nombre en secuencia y clase.

Algunos duplicados son normales porque existen diagramas de secuencia y de clases con el mismo nombre. Pero otros nombres son demasiado genericos.

Recomendacion:

```text
Renombrar diagramas genericos indicando modulo o proceso, por ejemplo:
Agregar Pedido de Compra
Anular Pedido de Compra
Agregar Presupuesto de Compra
Anular Presupuesto de Compra
Agregar Nota Credito/Debito Compra
Anular Nota Credito/Debito Compra
```

Nivel de importancia: bajo/medio.

### 5.6. Diagramas casi vacios

Se detectaron dos diagramas actuales con muy poco contenido:

- `timbrado`
- `medios_pago`

Ambos aparecen como diagramas de clases con un solo elemento visual.

Recomendacion:

```text
Si no se defienden como referenciales, no exportarlos. Si se van a defender, completar el contenido o moverlos a legacy.
```

Nivel de importancia: bajo/medio.

### 5.7. Nombres con codificacion rota

Se detectan nombres con mojibake, por ejemplo:

- `Agregar Nota de Remision` aparece con caracteres corruptos en la o acentuada.
- `Anular Nota de Remision` aparece con caracteres corruptos en la o acentuada.
- `Gestionar notas de credito y debito` aparece con caracteres corruptos en las palabras acentuadas.

Recomendacion:

```text
Corregir los nombres en StarUML antes de exportar imagenes. Usar ASCII si se quiere evitar problemas:
Agregar Nota de Remision
Anular Nota de Remision
Gestionar notas de credito y debito
```

Nivel de importancia: bajo, pero visible.

### 5.8. Typos en nombres de diagramas de clase

Se detectan nombres como:

- `registar_notaCreditoDebito`
- `regitrar_ajuste_inventario`

Recomendacion:

```text
Corregir a:
registrar_notaCreditoDebito
registrar_ajuste_inventario
```

Nivel de importancia: bajo.

## 6. Diagramas de secuencia criticos a revisar visualmente

Por los cambios recientes del sistema, conviene reabrir y revisar visualmente en StarUML estos diagramas:

### 6.1. Agregar ND NC

Debe mostrar o al menos no contradecir:

```text
saldo disponible = total compra + ND activas - NC activas
```

Tambien debe contemplar:

- Nota de Credito;
- Nota de Debito;
- Anular factura completa;
- Regularizar diferencia;
- devolucion fisica;
- validacion de stock cuando corresponde.

Recomendacion:

```text
Si la imagen del Word fue exportada antes de este ajuste, reemplazarla.
```

Revision puntual del diagrama actual:

```text
Estado: parcialmente correcto.
```

El diagrama contiene el flujo general de alta de Nota de Credito/Debito de compra, incluyendo:

- busqueda de factura;
- carga de detalle;
- seleccion de tipo de nota;
- alcance Regularizar diferencia / Anular factura completa;
- movimiento de stock;
- validaciones de cantidad y precio;
- duplicidad de documento;
- stock para devolucion fisica;
- impacto en cuentas a pagar;
- actualizacion de compra;
- mensaje final.

Pero debe corregirse el mensaje:

```text
valida el total de NC activas por factura
```

por:

```text
calcula el saldo disponible de la factura considerando total de compra + Notas de Debito activas - Notas de Credito activas.
```

Tambien conviene ampliar el mensaje:

```text
Si alcance es Anular factura completa, valida que la NC cubra exactamente el saldo disponible de la factura.
```

por:

```text
Si alcance es Anular factura completa, valida que la NC cubra exactamente el saldo disponible de la factura, considerando aumentos por Nota de Debito y descuentos por Notas de Credito anteriores.
```

Ademas corregir textos visibles con problemas de codificacion y typos:

```text
la palabra devolucion aparece con caracteres corruptos -> devolucion fisica
la palabra Credito aparece con caracteres corruptos -> Credito
insertda detalle -> inserta detalle
agregra datos -> agrega datos
```

### 6.2. Anular ND NC

Debe contemplar reversa de impactos:

- nota anulada;
- libro de compras;
- cuentas a pagar;
- stock si hubo devolucion fisica;
- auditoria de anulacion.

Revision puntual del diagrama actual:

```text
Estado: correcto en flujo general, con ajustes menores de texto.
```

El diagrama contempla:

- busqueda de notas;
- validacion de permiso;
- motivo de anulacion;
- verificacion de existencia de la nota;
- verificacion de sucursal y estado;
- anulacion de la nota;
- movimiento inverso en cuentas a pagar;
- restauracion de compra si corresponde;
- reversion de stock si fue Nota de Credito con devolucion fisica;
- movimiento `ANULA_NC_COMPRA`;
- registro de auditoria;
- mensaje final.

Conviene ajustar o aclarar el mensaje:

```text
actualiza estado
```

por:

```text
anula el registro asociado en libro_compra.
```

Tambien corregir codificacion visible:

```text
la palabra boton aparece con caracteres corruptos -> boton
la palabra anulacion aparece con caracteres corruptos -> anulacion
la frase este vacio aparece con caracteres corruptos -> este vacio
la palabra regularizacion aparece con caracteres corruptos -> regularizacion
la palabra segun aparece con caracteres corruptos -> segun
la frase devolucion fisica aparece con caracteres corruptos -> devolucion fisica
la palabra reversion aparece con caracteres corruptos -> reversion
```

### 6.3. Agregar Orden de Trabajo - Reclamo

Este diagrama parece representar el proceso que en el Word se describe como "Completar OT por Reclamo".

Recomendacion:

```text
Renombrar el diagrama a Completar OT por Reclamo, o aclarar en el Word que el diagrama de secuencia corresponde a la etapa de completar OT por reclamo.
```

Debe incluir:

- validacion de permiso;
- OT origen reclamo;
- estado pendiente de completar;
- diagnostico asociado;
- precarga de trabajos/repuestos;
- stock disponible;
- equipo y tecnico;
- validacion final;
- registro de detalle operativo sin modificar diagnostico original.

### 6.4. Anular Orden de Trabajo

Debe reflejar:

- si proviene de presupuesto, presupuesto vuelve a aprobado;
- si proviene de reclamo, reclamo vuelve a diagnostico generado;
- diagnostico queda disponible;
- auditoria de anulacion.

### 6.5. Agregar servicios / Registrar Servicios

Debe reflejar que el descuento de stock ocurre al registrar el servicio, no al completar la OT por reclamo.

### 6.6. Agregar diagnostico / Anular diagnostico

Debe contemplar estados actuales:

- pendiente;
- presupuesto generado/aprobado si aplica;
- OT generada;
- anulado.

## 7. Diagramas de clases a revisar

### 7.1. Nota Credito/Debito

El diagrama `registar_notaCreditoDebito` debe reflejar:

- compra_cabecera;
- compra_detalle;
- nota_compra;
- nota_compra_detalle;
- cuentas_a_pagar;
- libro_compra;
- stock;
- movimientostock;
- anulacion_auditoria, si se modela anulacion.

Ademas debe contemplar que la Nota de Debito afecta saldo y la Nota de Credito reduce saldo.

### 7.2. Orden de Trabajo

El diagrama `generar orden de trabajo` debe contemplar:

- orden_trabajo;
- orden_trabajo_detalle;
- presupuesto_servicio;
- diagnostico_servicio;
- reclamo_servicio;
- equipo_trabajo;
- empleados/tecnicos;
- articulos.

### 7.3. Registro de Servicio

Debe contemplar:

- registro_servicio;
- orden_trabajo;
- orden_trabajo_detalle;
- stock;
- movimientostock;
- articulos.

### 7.4. Cargos

Si se mantiene como diagrama, puede abrir preguntas. Mejor no exportarlo como referencial principal.

## 8. Diagramas de actividad

En el paquete actual aparecen:

- `Acompra`
- `Aservicios`
- `Aventas`

Recomendacion:

```text
Exportar al Word solo Acompra y Aservicios.
Mover Aventas a legacy o no exportarlo si ventas/facturacion no se defiende.
```

Nivel de importancia: alto si Aventas aparece en el Word final.

### 8.1. Revision puntual de Acompra

```text
Estado: sirve como diagrama macro, pero requiere mejoras para quedar alineado con el sistema actual.
```

El diagrama actual incluye:

- verificacion de stock;
- pedido;
- presupuesto;
- orden de compra;
- factura de compra;
- recepcion correcta o con diferencia;
- solicitud/registro de Nota de Credito.

Puntos a mejorar:

- Agregar o aclarar que existen dos caminos para orden de compra:
  - con presupuesto;
  - sin presupuesto.
- Agregar Nota de Debito junto con Nota de Credito, no solo Nota de Credito.
- En la rama de Nota de Credito/Debito, aclarar que el saldo disponible de NC considera:

```text
total compra + Notas de Debito activas - Notas de Credito activas
```

- Si se mantiene el flujo de recepcion con diferencia, aclarar:
  - Regularizar diferencia;
  - Anular factura completa.
- Agregar, si se defiende como parte del modulo de compras:
  - Nota de Remision de Compra;
  - Transferencia entre sucursales;
  - Ajuste de stock.
- Corregir textos:
  - `recibe y veriifica` -> `recibe y verifica`.
  - palabras con codificacion corrupta como `Credito` o `notificacion`.

Recomendacion:

```text
Mantener Acompra como diagrama general, pero actualizarlo para que no parezca que compras termina solo en factura y Nota de Credito. Debe reflejar tambien NC/ND, remision, transferencia y ajuste si esos casos estan en el Word.
```

### 8.2. Revision puntual de Aservicios

```text
Estado: sirve como diagrama macro, pero requiere mejoras importantes para reflejar reclamos y OT por reclamo.
```

El diagrama actual incluye:

- solicitud de servicio;
- cliente nuevo/habitual;
- diagnostico;
- presupuesto;
- promociones/descuentos;
- orden de trabajo;
- realizacion del servicio;
- registro de servicio finalizado;
- notificacion al cliente;
- mencion de stock suficiente/insuficiente.

Puntos a mejorar:

- Agregar rama de reclamo de cliente.
- Agregar diagnostico de reclamo con condiciones:
  - reclamo valido;
  - garantia;
  - sin cobro.
- Agregar generacion de OT por reclamo.
- Agregar completar OT por reclamo:
  - asignar equipo;
  - asignar tecnico;
  - confirmar detalle operativo;
  - validar stock de repuestos;
  - mantener diagnostico original sin modificar.
- Aclarar que el descuento de stock ocurre al registrar el servicio, no al completar la OT por reclamo.
- Agregar estado "OT generada" para diagnostico cuando corresponda.
- Corregir textos:
  - `Personal de Rececpion` -> `Personal de Recepcion`.
  - `serivicio` -> `servicio`.
  - palabras con codificacion corrupta como `notificacion`.

Recomendacion:

```text
Aservicios debe actualizarse antes de exportar imagen final, porque actualmente representa bien el flujo normal de servicio, pero no muestra con suficiente claridad el flujo de reclamo, garantia y OT por reclamo que se esta defendiendo.
```

### 8.3. Frase defendible para los diagramas de actividad

Si los diagramas se mantienen como vista macro, se puede defender asi:

```text
Los diagramas de actividad representan el flujo general del negocio. Los detalles finos de validacion, como saldo de NC/ND, stock por sucursal o estados especificos, se documentan con mayor detalle en las especificaciones de casos de uso y diagramas de secuencia.
```

Pero si se van a usar como imagen principal del Word, conviene actualizarlos para que no contradigan los casos de uso detallados.

## 9. Casos de uso

Los casos de uso principales de compras, servicios, seguridad e informes estan cubiertos.

Observacion:

Hay casos de uso de Facturacion en el paquete actual. Si no se defienden, no deben aparecer como imagen en el Word.

Recomendacion:

```text
El Word final debe mostrar solamente los casos de uso relacionados al alcance: compras, servicios, referenciales, seguridad e informes.
```

## 10. Imagenes del Word que conviene reemplazar

El Word fue modificado a las 18:33 y el `.mdj` fue modificado a las 18:45. Por lo tanto, hay posibilidad de que algunas imagenes del Word hayan quedado anteriores a los ultimos cambios de StarUML.

Se recomienda reexportar y reemplazar en el Word, como minimo:

- Diagrama de secuencia `Agregar ND NC`.
- Diagrama de clase `registar_notaCreditoDebito` luego de corregir nombre.
- Diagrama de secuencia `Agregar Orden de Trabajo - Reclamo` o `Completar OT por Reclamo`.
- Diagrama de clase `generar orden de trabajo`.
- Diagrama de secuencia `Anular Orden de Trabajo`.
- Diagrama de actividad `Aservicios`, si se modifico el flujo de OT/reclamo.
- Diagrama de actividad `Acompra`, si se modifico el flujo de NC/ND.
- Casos de uso de Gestion de Compras y Gestion de Servicios, si se agregaron o renombraron casos.

No reemplazar ni exportar:

- `Aventas`;
- casos de uso de Facturacion;
- secuencias de ventas;
- clases de ventas/caja/cobros;
- diagramas de cargos como referencial principal;
- diagramas del paquete `old`.

## 11. Diferencias contra el documento Word

El Word actual cubre los casos principales de:

- referenciales;
- compras;
- servicios;
- informes;
- seguridad.

El `.mdj` cubre esos puntos, pero tiene alcance adicional activo:

- ventas;
- facturacion;
- caja;
- cobros;
- cargos como referencial;
- timbrado;
- medios de pago;
- categorias/departamentos/ciudades.

Recomendacion:

```text
Si esos modulos no se defienden, deben quedar fuera de las imagenes finales del Word o moverse a un paquete legacy.
```

## 12. Concordancia entre tablas del analisis y clases del StarUML

Se cruzaron las tablas mencionadas en el Word con las clases de dominio/tablas detectadas en el paquete actual `Diagramas`.

Resultado general:

```text
La mayoria de las tablas involucradas en el analisis tienen clase equivalente en StarUML.
```

Tablas del Word con clase equivalente detectada:

```text
ajuste_inventario
ajuste_inventario_detalle
anulacion_auditoria
articulo_proveedor
compra_cabecera
compra_detalle
cuentas_a_pagar
descuento_cliente
diagnostico_detalle
diagnostico_servicio
equipo_empleado
equipo_trabajo
libro_compra
modelo_auto
nota_compra
nota_compra_detalle
nota_remision
nota_remision_detalle
orden_compra
orden_compra_detalle
orden_trabajo
orden_trabajo_detalle
pedido_cabecera
pedido_detalle
presupuesto_compra
presupuesto_descuento
presupuesto_detalle
presupuesto_detalleservicio
presupuesto_promocion
presupuesto_servicio
promocion_producto
recepcion_fotos
recepcion_servicio
reclamo_servicio
reclamo_servicio_detalle
registro_servicio
registro_servicio_detalle
rol_permiso
salida_insumo
salida_insumo_detalle
sucursal_documento
tipo_impuesto
transferencia_stock
transferencia_stock_detalle
unidad_medida
usuario_rol
```

### 12.1. Tablas mencionadas en el Word sin clase exacta

Se detectaron dos diferencias:

```text
reclamo_servicioy
salida_insumos
```

Analisis:

- `reclamo_servicioy` parece un error de escritura. Deberia ser `reclamo_servicio`.
- `salida_insumos` no coincide con el modelo de clases actual. La clase existente es `salida_insumo`, y el detalle es `salida_insumo_detalle`.

Recomendacion:

```text
Corregir en el Word cualquier aparicion de reclamo_servicioy por reclamo_servicio.
Corregir salida_insumos por salida_insumo, salvo que se decida renombrar tambien la clase y la tabla real.
```

Nivel de importancia: alto, porque son nombres de tabla.

### 12.2. Clases de dominio sin mencion clara como tabla involucrada

El paquete actual contiene algunas clases de dominio que no aparecen claramente como tablas involucradas en el Word:

```text
apercier_cajas
arqueos
asignar_empleados
cajas
cheque_detalle
cobro_cheque
cobro_detalle
cobro_efectivo
cobro_tarjeta
cobros
colores
cuentas_cobrar
departamentos
detalle_fac
forma_cobro
libro_venta
medios_pago
recaudacion_deposito
tarjeta_detalle
ver_miembros
```

Analisis:

- Varias pertenecen a facturacion, ventas, cobros o caja.
- Algunas pertenecen a referenciales no defendidos o auxiliares.
- `asignar_empleados` y `ver_miembros` parecen mas cercanas a procesos/vistas que a tablas principales.

Recomendacion:

```text
Si no se defienden ventas, facturacion, caja o cobros, no exportar esas clases al Word final.
Si alguna clase representa una tabla real usada en servicios o seguridad, agregarla al analisis solo si aparece en un flujo defendido.
```

### 12.3. Concordancia por modulo

Compras:

```text
pedido_cabecera, pedido_detalle, presupuesto_compra, presupuesto_detalle,
orden_compra, orden_compra_detalle, compra_cabecera, compra_detalle,
nota_compra, nota_compra_detalle, nota_remision, nota_remision_detalle,
cuentas_a_pagar, libro_compra, stock, movimientostock,
transferencia_stock, transferencia_stock_detalle,
ajuste_inventario, ajuste_inventario_detalle
```

Estado: consistente en general.

Servicios:

```text
recepcion_servicio, recepcion_fotos, diagnostico_servicio,
diagnostico_detalle, presupuesto_servicio, presupuesto_detalleservicio,
presupuesto_descuento, presupuesto_promocion, promocion_producto,
orden_trabajo, orden_trabajo_detalle, registro_servicio,
registro_servicio_detalle, reclamo_servicio, reclamo_servicio_detalle,
salida_insumo, salida_insumo_detalle
```

Estado: consistente, con la salvedad de corregir `salida_insumos` si aparece en el Word.

Seguridad:

```text
usuarios, roles, permisos, usuario_rol, rol_permiso
```

Estado: consistente.

Referenciales:

```text
articulos, proveedores, sucursales, clientes, vehiculos, empleados,
modelo_auto, marcas, equipo_trabajo, equipo_empleado, cargos
```

Estado: consistente, pero `cargos` debe quedar como tabla auxiliar interna, no como referencial principal defendido.

## 13. Checklist antes de exportar imagenes finales

- [ ] Confirmar que las imagenes exportadas provienen del paquete `Diagramas`, no de `old`.
- [ ] No exportar diagramas de ventas/facturacion/caja/cobros si no se defienden.
- [ ] No exportar Cargos como caso de uso o diagrama principal.
- [ ] Corregir nombres con codificacion rota.
- [ ] Corregir typos: `registar`, `regitrar`.
- [ ] Corregir `reclamo_servicioy` por `reclamo_servicio` en el Word.
- [ ] Corregir `salida_insumos` por `salida_insumo` si aparece como tabla.
- [ ] Revisar y decidir que hacer con diagramas `_3`.
- [ ] Reexportar `Agregar ND NC`.
- [ ] Reexportar `Agregar Orden de Trabajo - Reclamo` o renombrar a `Completar OT por Reclamo`.
- [ ] Reexportar `Anular Orden de Trabajo`.
- [ ] Reexportar diagramas de clase afectados por NC/ND y OT.
- [ ] Verificar visualmente que los diagramas de secuencia no tengan textos solapados.
- [ ] Verificar que el Word no incluya imagenes de diagramas que ya no se defienden.

## 14. Conclusion

El `.mdj` actualizado contiene material suficiente para defender el analisis y diseno del sistema, pero no esta completamente limpio para exportacion final.

La recomendacion principal es:

```text
Usar solo el paquete Diagramas, excluir ventas/facturacion/caja/cobros, no defender cargos como referencial principal, corregir nombres visibles y reexportar las imagenes de NC/ND y OT por reclamo.
```

Con esos ajustes, los diagramas quedan alineados con el documento y con la programacion actual del sistema.
