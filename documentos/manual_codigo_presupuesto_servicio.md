# Manual tecnico del codigo de Presupuesto de Servicio

Este documento explica paso a paso como funciona el codigo de presupuesto de servicio, especialmente el calculo de precios, promociones y descuentos.

La idea principal del modulo es esta: el presupuesto guarda el precio base del item y registra aparte las promociones y descuentos aplicados. Esto permite saber cuanto valia originalmente el servicio o repuesto, que beneficio se aplico y cuanto termino pagando el cliente.

## 1. Archivos principales

Los archivos que intervienen en el flujo son:

- `vistas/contenidos/presupuesto-servicio-nuevo-vista.php`
  Contiene el formulario visible del presupuesto, los inputs ocultos y la tabla de detalle.

- `vistas/inc/presupuestoServicio.php`
  Contiene la logica JavaScript de la pantalla: busqueda, seleccion de diagnostico, carga de items, promociones, descuentos, totales y envio del formulario.

- `ajax/presupuestoServicioAjax.php`
  Recibe las peticiones AJAX y llama al controlador correspondiente.

- `controladores/presupuestoservicioControlador.php`
  Valida permisos, datos basicos recibidos desde la pantalla y arma el arreglo que se envia al modelo.

- `modelos/presupuestoservicioModelo.php`
  Revalida datos importantes contra la base, recalcula totales en servidor y guarda cabecera, detalle, promociones y descuentos.

## 2. Formulario principal

El formulario esta en `vistas/contenidos/presupuesto-servicio-nuevo-vista.php`.

Campos ocultos importantes:

- `origen_presupuesto`: indica si el presupuesto viene de `DIAGNOSTICO` o es `PRELIMINAR`.
- `id_diagnostico`: se usa cuando el presupuesto nace desde diagnostico.
- `id_cliente` e `id_vehiculo`: se usan para validar cliente y vehiculo.
- `convertido_desde`: se usa cuando un preliminar se reutiliza y se convierte.
- `detalle_json`: guarda el detalle completo generado en JavaScript.
- `descuentos_json`: guarda los descuentos aplicados en JavaScript.
- `subtotal_servicios`: subtotal sin descontar promociones ni descuentos.
- `total_descuento`: suma de descuentos aplicados.
- `total_final`: total final calculado en pantalla.

La tabla de detalle muestra:

- Servicio o articulo.
- Cantidad.
- Precio unitario.
- Subtotal.
- Promocion.
- Subtotal final.

El subtotal final es visual. Sirve para que el usuario vea cuanto queda cada linea luego de promociones y descuentos. El guardado real sigue separando precio base, promociones y descuentos.

## 3. Busqueda y carga de items

La funcion `buscarServicio()` en `vistas/inc/presupuestoServicio.php` consulta articulos o servicios activos.

El controlador arma los botones para agregar cada item en `buscar_servicios_controlador()`.

Cuando el usuario agrega un item, se ejecuta:

```js
agregarServicio(id, descripcion, precio, tipo, stock)
```

Cada item se guarda temporalmente en el arreglo JavaScript `detalleServicios` con datos como:

- `id_articulo`
- `descripcion`
- `cantidad`
- `precio_base`
- `precio_final`
- `subtotal`
- `tipo`
- `stock`
- `promocion`
- `monto_promocion`

El campo mas importante es `precio_base`. Ese precio representa el precio aceptado al momento de armar el presupuesto.

## 4. Por que se guarda precio base sin descuento ni promocion

Se guarda el precio base en `presupuesto_detalleservicio.preciouni` porque el precio original de la linea debe quedar congelado.

Esto es importante por varias razones:

- Si luego cambia el precio en `articulos`, el presupuesto viejo no cambia.
- Permite auditar que precio se ofrecio al cliente.
- Permite separar precio normal, promocion y descuento.
- Permite recalcular el total del presupuesto sin depender del precio actual del articulo.
- Permite imprimir o revisar historicamente el presupuesto tal como fue aceptado.

Ejemplo:

```text
Precio unitario: 100.000
Cantidad: 2
Subtotal: 200.000
Promocion: 20.000
Descuento: 10.000
Total final de la linea: 170.000
```

En la base no se reemplaza el precio por 85.000 ni por 170.000. Se guarda el precio base y los descuentos/promociones aparte.

## 5. Promociones

Cuando se agrega un articulo, el JavaScript consulta:

```text
ajax/presupuestoServicioAjax.php -> promo_articulo_controlador()
```

Luego el modelo usa `promo_articulo_modelo()` para buscar una promocion activa y vigente asociada al articulo.

Tablas usadas:

- `promociones`
- `promocion_producto`

La promocion puede ser:

- `PORCENTAJE`
- `MONTO_FIJO`
- `PRECIO_FIJO`

En pantalla se calcula `monto_promocion` por unidad. Para mostrar el total promocionado de la linea se multiplica:

```text
monto_promocion * cantidad
```

Al guardar, la promocion se registra en `presupuesto_promocion` con:

- presupuesto
- detalle del presupuesto
- promocion
- articulo
- cantidad
- monto unitario
- monto aplicado

Esto permite saber exactamente que promocion afecto a que linea.

## 6. Descuentos

Los descuentos se cargan por cliente desde:

```text
descuentos_cliente_controlador()
descuentos_cliente_modelo()
```

Tablas usadas:

- `descuentos`
- `descuento_cliente`

El descuento tiene:

- `tipo`: `PORCENTAJE` o `MONTO_FIJO`.
- `valor`: porcentaje o monto.
- `aplica_a`: `TOTAL`, `PRODUCTO` o `SERVICIO`.

Regla de alcance:

- `TOTAL`: aplica sobre todo el presupuesto despues de promociones.
- `PRODUCTO`: aplica solo sobre lineas cuyo articulo sea tipo producto.
- `SERVICIO`: aplica solo sobre lineas cuyo articulo sea tipo servicio.

El descuento se guarda en `presupuesto_descuento` con:

- `id_presupuesto`
- `id_descuento`
- `id_usuario`
- `tipo`
- `valor`
- `aplica_a`
- `base_aplicada`
- `monto_aplicado`
- `motivo`
- `fecha`

## 7. Diferencia entre promocion y descuento

La promocion esta asociada al articulo. Por eso se registra por linea en `presupuesto_promocion`.

El descuento esta asociado al cliente y al presupuesto. Puede aplicar al total, a productos o a servicios. Por eso se registra en `presupuesto_descuento`.

Resumen:

```text
promocion = beneficio ligado al articulo
descuento = beneficio ligado al cliente/presupuesto
```

## 8. Calculo en pantalla

La funcion principal es:

```js
recalcularTotales()
```

Primero recalcula el subtotal:

```text
subtotal de linea = precio_base * cantidad
subtotal general = suma de subtotales de linea
```

Luego calcula promociones:

```text
promocion de linea = monto_promocion unitario * cantidad
total promociones = suma de promociones de linea
```

Luego calcula la base de descuentos:

```text
base descuentos = subtotal general - total promociones
```

Despues aplica cada descuento segun su alcance:

```text
TOTAL    -> usa todas las lineas
PRODUCTO -> usa solo productos
SERVICIO -> usa solo servicios
```

Finalmente:

```text
total final = subtotal general - total promociones - total descuentos
```

## 9. Subtotal final visual por linea

La columna `Subtotal Final` es para lectura del usuario.

Como el descuento puede ser total o por tipo de item, el sistema lo reparte visualmente entre las lineas alcanzadas usando proporcion.

La funcion que hace esto es:

```js
distribuirDescuentoEnLineas(alcance, baseAlcance, montoDescuento)
```

Ejemplo:

```text
Linea A base luego de promocion: 100.000
Linea B base luego de promocion: 300.000
Descuento total: 40.000
```

El reparto visual queda:

```text
Linea A recibe 10.000 de descuento
Linea B recibe 30.000 de descuento
```

Esto no crea una tabla nueva. Solo ayuda a visualizar el neto de cada linea.

## 10. Envio al servidor

Antes de enviar el formulario, se ejecuta:

```js
prepararEnvioPresupuesto()
```

Esta funcion convierte los arreglos JavaScript en JSON:

```js
detalle_json = JSON.stringify(detalleServicios)
descuentos_json = JSON.stringify(descuentosAplicados)
```

Tambien se envian los totales ocultos:

- subtotal
- descuento
- total final

## 11. Controlador

El metodo `guardar_presupuesto_controlador()` recibe el formulario.

Sus responsabilidades principales son:

- Verificar permiso `servicio.presupuesto.crear`.
- Leer `detalle_json`.
- Leer `descuentos_json`.
- Validar fecha de vencimiento.
- Validar origen del presupuesto.
- Validar que exista diagnostico o cliente/vehiculo segun corresponda.
- Validar que exista al menos un detalle.
- Armar el arreglo `$datos`.
- Llamar a `guardar_presupuesto_modelo($datos)`.

El controlador no confia ciegamente en la vista, pero tampoco hace todo el calculo. La revalidacion fuerte queda en el modelo.

## 12. Modelo y transaccion

El metodo principal es:

```php
guardar_presupuesto_modelo($d)
```

Este metodo trabaja dentro de una transaccion. Eso significa que si algo falla, se revierte todo.

El modelo valida:

- Si el presupuesto viene de diagnostico, que el diagnostico exista, este activo y sea de la sucursal.
- Si el diagnostico viene de reclamo, que no tenga OT activa asociada.
- Si es preliminar, que cliente y vehiculo esten activos y relacionados.
- Si convierte un preliminar, que no este vencido y pertenezca al mismo cliente, vehiculo y sucursal.
- Que cada articulo exista y este activo.
- Stock disponible para productos.
- Promociones enviadas.
- Descuentos enviados.
- Totales enviados contra totales recalculados.

## 13. Guardado en tablas

El guardado se divide asi:

### Cabecera

Tabla:

```text
presupuesto_servicio
```

Guarda datos generales:

- usuario
- sucursal
- cliente
- vehiculo
- fecha
- vencimiento
- subtotal
- total descuento
- total final
- diagnostico
- origen
- preliminar convertido

### Detalle

Tabla:

```text
presupuesto_detalleservicio
```

Guarda cada linea con:

- articulo
- presupuesto
- cantidad
- precio unitario base
- subtotal

Importante: aca no se guarda el precio con promocion o descuento. Se guarda el precio unitario base del presupuesto.

### Promociones

Tabla:

```text
presupuesto_promocion
```

Guarda la promocion aplicada a cada detalle.

### Descuentos

Tabla:

```text
presupuesto_descuento
```

Guarda los descuentos aplicados al presupuesto, incluyendo alcance y base aplicada.

## 14. Comparacion de totales

El servidor recalcula los totales y los compara con los que llegaron desde la pantalla.

Esto evita que se guarde un presupuesto con datos manipulados desde el navegador.

La regla es:

```text
si la diferencia entre pantalla y servidor es mayor a 1, se cancela
```

Se usa tolerancia de 1 por redondeos.

## 15. Por que no se guarda solo el total final

No se guarda solo el total final porque se perderia trazabilidad.

Si solo se guarda total final, despues no se puede responder:

- Que precio tenia cada articulo.
- Que promocion se aplico.
- Que descuento se aplico.
- Sobre que base se calculo el descuento.
- Si el descuento fue por producto, servicio o total.
- Quien aplico el descuento.
- Cuanto se desconto por promocion y cuanto por descuento.

Por eso el diseno separa:

```text
precio base -> presupuesto_detalleservicio
promocion   -> presupuesto_promocion
descuento   -> presupuesto_descuento
total       -> presupuesto_servicio
```

## 16. Ejemplo completo

Detalle:

```text
Servicio alineacion: 100.000 x 1 = 100.000
Repuesto pastilla:   200.000 x 1 = 200.000
Subtotal: 300.000
```

Promocion:

```text
Pastilla tiene promocion de 20.000
Total promociones: 20.000
```

Base despues de promocion:

```text
300.000 - 20.000 = 280.000
```

Descuento:

```text
Descuento 10% aplica a TOTAL
Base aplicada: 280.000
Monto aplicado: 28.000
```

Total final:

```text
300.000 - 20.000 - 28.000 = 252.000
```

En la base queda:

```text
presupuesto_detalleservicio:
- alineacion, precio 100.000, subtotal 100.000
- pastilla, precio 200.000, subtotal 200.000

presupuesto_promocion:
- pastilla, promocion 20.000

presupuesto_descuento:
- descuento 10%, aplica TOTAL, base 280.000, monto 28.000

presupuesto_servicio:
- subtotal 300.000
- total descuento 28.000
- total final 252.000
```

## 17. Como defender la decision tecnica

La respuesta corta para defender el diseno es:

```text
Guardamos el precio base y registramos promociones/descuentos por separado para mantener trazabilidad, auditoria e historico. Si el precio del articulo cambia despues, el presupuesto no cambia. Ademas, podemos saber exactamente que beneficio se aplico, sobre que base se calculo y cuanto afecto al total.
```

La respuesta mas completa:

```text
El detalle guarda el precio unitario aceptado al momento del presupuesto. Las promociones se registran aparte porque pertenecen a articulos especificos. Los descuentos se registran aparte porque pertenecen al presupuesto y pueden aplicar al total, a productos o a servicios. La cabecera guarda los totales finales para consulta rapida. El servidor recalcula todo antes de guardar para evitar inconsistencias con los calculos de pantalla.
```

## 18. Resumen del flujo

```text
1. Usuario selecciona diagnostico o preliminar.
2. Sistema carga cliente, vehiculo y detalle.
3. Usuario agrega o quita items.
4. Sistema calcula promociones por articulo.
5. Sistema carga descuentos disponibles del cliente.
6. Usuario selecciona descuentos.
7. Pantalla calcula subtotal, promociones, descuentos y total.
8. Pantalla envia detalle_json, descuentos_json y totales.
9. Controlador valida datos generales.
10. Modelo revalida contra base de datos.
11. Modelo recalcula totales.
12. Modelo guarda cabecera, detalle, promociones y descuentos.
13. Si viene de diagnostico, marca el diagnostico como presupuestado.
14. Sistema muestra mensaje de presupuesto registrado.
```

