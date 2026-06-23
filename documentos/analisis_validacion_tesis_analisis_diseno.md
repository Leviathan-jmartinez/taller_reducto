# Analisis de validacion del documento TesisAnalisisYDiseno

Fecha de revision: 22/06/2026  
Documento revisado: `documentos/TesisAnalisisYDiseno.docx` (archivo original: TesisAnalisisYDiseno, con n con tilde en el nombre)

## 1. Alcance de la revision

Se reviso el contenido textual extraido del documento Word y se contrasto con el comportamiento actual del sistema observado en el codigo fuente y en los ajustes recientes realizados sobre los modulos de compras, servicios, ordenes de trabajo, diagnostico, reclamos, notas de credito/debito y referenciales.

El objetivo de este informe es identificar:

- puntos documentados correctamente;
- inconsistencias contra el sistema actual;
- textos que conviene ajustar para defensa;
- errores de forma u ortografia;
- riesgos de interpretacion ante evaluadores.

## 2. Resultado general

El documento esta bastante completo y cubre la mayor parte del sistema:

- referenciales de compras;
- referenciales de servicios;
- seguridad, usuarios, roles y permisos;
- movimientos de compras;
- movimientos de servicios;
- informes referenciales;
- informes de movimientos;
- diagramas principales;
- DER, despliegue y estructura modular.

La estructura general es defendible. El documento no parece incompleto a nivel macro. Sin embargo, hay algunos puntos que conviene corregir porque el sistema cambio recientemente o porque ciertas frases pueden abrir preguntas innecesarias en defensa.

## 3. Datos tecnicos detectados del documento

Del archivo Word se detecto:

- 4237 lineas de texto utiles extraidas.
- 4771 parrafos internos.
- 39 tablas.
- 195 imagenes embebidas.
- 63 saltos de pagina.
- 219 entradas internas dentro del archivo DOCX.

Esto confirma que el documento es amplio y no es solo un resumen superficial. Contiene texto, tablas y diagramas suficientes para representar el sistema.

## 4. Hallazgos importantes

### 4.1. Notas de Credito/Debito de Compra: validacion desactualizada

En el documento se indica:

> Si es Nota de Credito, el sistema valida que el total de notas de credito activas asociadas a la factura no supere el total de la factura.

Esta frase quedo desactualizada.

El sistema actual ya considera tambien las Notas de Debito asociadas a la factura. La logica correcta es:

```text
saldo disponible para NC = total factura + total ND activas - total NC activas
```

Recomendacion de reemplazo:

```text
Si es Nota de Credito, el sistema calcula el saldo disponible de la factura considerando el total de la compra, las Notas de Debito activas asociadas y las Notas de Credito activas ya registradas. La nueva Nota de Credito no puede superar dicho saldo disponible. Tablas consultadas: compra_cabecera, nota_compra y nota_compra_detalle.
```

Tambien conviene ajustar la parte de "Anular factura completa":

```text
Si el alcance es Anular factura completa, el sistema valida que la Nota de Credito cubra exactamente el saldo disponible de la factura, considerando aumentos por Nota de Debito y descuentos por Notas de Credito anteriores.
```

Nivel de importancia: alto.

## 5. Orden de Trabajo por Reclamo

La seccion "Completar OT por Reclamo" esta bien encaminada y ya contiene la defensa correcta:

```text
El sistema registra en la OT el detalle operativo confirmado de trabajos y repuestos para atender el reclamo, sin modificar el diagnostico original.
```

Esta frase es buena porque evita decir que se modifica el diagnostico. La defensa conceptual correcta es:

- el diagnostico queda como antecedente tecnico;
- la OT registra la ejecucion operativa;
- los trabajos y repuestos pueden confirmarse o ajustarse en la OT por disponibilidad de stock o equivalencia operativa;
- el diagnostico original no se altera.

### 5.1. Recomendacion menor

Agregar antes del mensaje final:

```text
El diagnostico asociado permanece como antecedente tecnico original; la OT registra la ejecucion operativa confirmada.
```

Esto refuerza la defensa ante la pregunta: "por que se pueden ajustar repuestos si ya existe diagnostico?".

Nivel de importancia: medio.

## 6. Anulacion de OT por Reclamo

El documento dice:

```text
Si la OT proviene de reclamo, el sistema no modifica el estado del reclamo y deja el diagnostico asociado nuevamente disponible para continuar el flujo del reclamo.
```

Pero el codigo actual realiza dos acciones:

- devuelve el diagnostico asociado a estado disponible/pendiente;
- devuelve el reclamo desde estado OT generada a estado diagnostico generado.

En codigo se observa:

```text
diagnostico_servicio.estado = 1
reclamo_servicio.estado = 3
```

Recomendacion de reemplazo:

```text
Si la OT proviene de reclamo, el sistema devuelve el reclamo al estado de diagnostico generado y deja el diagnostico asociado nuevamente disponible para continuar el flujo del reclamo. Tablas afectadas: reclamo_servicio y diagnostico_servicio.
```

Nivel de importancia: medio.

## 7. Estado "OT generada" en diagnostico

El sistema actual maneja el estado "OT generada" del diagnostico como estado 4.

Se verifico que:

- al generar OT desde presupuesto de servicio con diagnostico, el diagnostico pasa a estado 4;
- al generar OT directa por reclamo, el diagnostico tambien debe pasar a estado 4;
- la pantalla de diagnosticos contempla el filtro "OT generada".

Recomendacion:

En las especificaciones donde se hable del estado de diagnostico, conviene aclarar:

```text
Cuando el diagnostico ya origino una OT, el diagnostico pasa a estado OT generada, manteniendo su informacion original como antecedente tecnico.
```

Nivel de importancia: medio.

## 8. Cargos en Empleados

El documento menciona `cargos` dentro de empleados e informes:

- empleados con cargos;
- filtros por cargo;
- tabla cargos;
- relacion empleados, cargos y sucursales.

Esto debe decidirse con cuidado porque previamente se definio que "Cargos" no se defenderia como referencial principal y se moveria a legacy.

Hay dos caminos defendibles:

### Opcion A: mantenerlo como tabla auxiliar interna

No se presenta "Mantener Cargos" como caso de uso independiente, pero se acepta que empleados usa una tabla auxiliar `cargos` para clasificacion interna.

Frase recomendada:

```text
El cargo del empleado se utiliza como dato auxiliar interno para clasificacion y filtrado, no como referencial principal defendido por el sistema.
```

### Opcion B: eliminar menciones visibles a cargos

Si no se quiere defender cargos de ninguna manera, habria que quitarlo o reemplazarlo en las especificaciones de empleados e informes.

Riesgo:

El codigo actual todavia consulta la tabla `cargos` en empleados y reportes, por lo que eliminar toda mencion puede generar contradiccion si revisan pantalla o base de datos.

Recomendacion:

Usar la opcion A. Es mas segura: no se defiende como modulo, pero se reconoce como dato auxiliar.

Nivel de importancia: medio.

## 9. Referenciales y eliminacion por estado

Durante los ajustes del sistema se definio que el estado inactivo no debe impedir eliminar un registro. La regla correcta es:

```text
Un referencial puede eliminarse si no esta referenciado en la tabla validada correspondiente. El estado activo/inactivo no debe ser impedimento para eliminar.
```

Para articulos, se valido especialmente:

```text
El articulo no puede eliminarse si tiene movimientos en movimientostock. Si no tiene movimientos, puede eliminarse aunque este inactivo.
```

Recomendacion:

Revisar todas las especificaciones de eliminar referenciales para que no indiquen que "si esta inactivo no se puede eliminar".

Nivel de importancia: medio.

## 10. Stock en OT por Reclamo

La documentacion actual de "Completar OT por Reclamo" ya quedo alineada con la validacion de stock:

- precarga repuestos sugeridos con stock disponible;
- permite ajustar repuestos operativos por disponibilidad de stock;
- muestra productos activos con stock disponible;
- valida stock al acumular repuestos;
- valida stock nuevamente antes de guardar.

Esto es defendible.

Frase clave para defensa:

```text
La OT toma como base el diagnostico, pero el detalle operativo puede ajustarse por disponibilidad de stock o equivalencia operativa, sin modificar el diagnostico original.
```

Nivel de importancia: correcto, mantener.

## 11. Correcciones de forma y ortografia

Se detectaron algunos detalles de redaccion:

### 11.1. "Solcitudes"

Aparece:

```text
Informe de Solcitudes de Servicios
```

Debe decir:

```text
Informe de Solicitudes de Servicios
```

Nivel de importancia: bajo, pero visible en indice.

### 11.2. Titulos con palabras pegadas

En los titulos internos se detectan textos como:

```text
Modulo deCompras
Modulo deServicios
Referencialescompras
Referencialesservicios
Informesreferenciales
```

Recomendacion:

Revisar estilos/titulos en Word para que queden:

```text
Modulo de Compras
Modulo de Servicios
Referenciales compras
Referenciales servicios
Informes referenciales
```

Nivel de importancia: bajo.

### 11.3. Acentos en titulos

Hay titulos sin acento:

```text
Informe de Articulos
Informe de Vehiculos
```

Recomendacion:

Usar:

```text
Informe de Articulos
Informe de Vehiculos
```

Si el documento usa ASCII por decision tecnica, puede dejarse sin acentos. Si es documento academico final, conviene corregir:

```text
Informe de Articulos
Informe de Vehiculos
```

Nivel de importancia: bajo.

## 12. Puntos bien documentados

Se consideran correctos o defendibles:

- alcance general del sistema;
- division en compras, servicios, referenciales, seguridad e informes;
- existencia de permisos por accion;
- uso de sucursal como restriccion operativa;
- trazabilidad de anulaciones mediante auditoria;
- movimientos de stock registrados en `movimientostock`;
- presupuesto de compra con validacion de costos;
- orden de compra con y sin presupuesto;
- compra, cuenta a pagar y libro de compras;
- reclamos con garantia y diagnostico;
- OT por reclamo como ejecucion operativa;
- informes referenciales y de movimientos.

## 13. Riesgos principales para defensa

### Riesgo 1: decir que la OT modifica el diagnostico

No usar esa frase.

Defensa correcta:

```text
El diagnostico no se modifica. La OT toma el diagnostico como base y registra el detalle operativo confirmado.
```

### Riesgo 2: Nota de Credito sin considerar Nota de Debito

Corregir la especificacion para explicar saldo disponible real:

```text
Factura + ND activas - NC activas.
```

### Riesgo 3: Cargos

No presentarlo como modulo referencial defendido.

Defensa correcta:

```text
Cargos funciona como tabla auxiliar interna para clasificar empleados y filtrar informes.
```

### Riesgo 4: anulacion de OT por reclamo

Evitar decir que no modifica nada. El sistema si vuelve estados para permitir continuar el flujo.

Defensa correcta:

```text
La anulacion revierte el flujo del reclamo a diagnostico generado y deja el diagnostico disponible para una nueva OT.
```

## 14. Checklist de correccion recomendado

Antes de entregar o defender, corregir:

- [ ] Cambiar "Solcitudes" por "Solicitudes".
- [ ] Ajustar Notas de Credito para incluir Notas de Debito en el saldo disponible.
- [ ] Ajustar "Anular factura completa" para indicar saldo disponible real.
- [ ] Agregar frase de antecedente tecnico original en OT por Reclamo.
- [ ] Ajustar anulacion de OT por Reclamo: reclamo vuelve a diagnostico generado y diagnostico queda disponible.
- [ ] Decidir tratamiento de cargos como tabla auxiliar, no como referencial defendido.
- [ ] Revisar titulos pegados en el indice y en los encabezados.
- [ ] Revisar especificaciones de eliminar referenciales para que el estado inactivo no sea impedimento de eliminacion.

## 15. Conclusion

El documento `TesisAnalisisYDiseno.docx` esta avanzado y cubre practicamente todo el sistema. No se detecta una falta estructural grave.

Las correcciones mas importantes son de alineacion fina contra el sistema actual:

- saldo de Notas de Credito considerando Notas de Debito;
- estados de diagnostico/reclamo al generar o anular OT;
- tratamiento defendible de OT por reclamo sin modificar diagnostico;
- tratamiento de cargos como dato auxiliar;
- correcciones de forma visibles.

Con esos ajustes, el documento queda mucho mas consistente para defensa.
