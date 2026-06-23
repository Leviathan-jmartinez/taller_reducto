# Defensa tecnica del analisis, diseno y programacion del sistema

Fecha: 22/06/2026  
Sistema: Gestion de compras y servicios para Lubri Reducto S.A.

Este documento esta preparado como apoyo para defensa. Esta separado en dos partes:

- defensa del analisis y diseno;
- defensa de la programacion e implementacion.

La idea principal es poder explicar el sistema con fundamento, no solo describir pantallas.

---

# Parte 1: Defensa del analisis y diseno

## 1. Enfoque general del sistema

El sistema fue planteado para cubrir dos areas operativas principales de la empresa:

- gestion de compras;
- gestion de servicios de taller.

Ambas areas comparten datos maestros, seguridad, usuarios, sucursales, articulos, clientes, proveedores e informes. Por eso el analisis se estructuro por modulos:

- referenciales;
- movimientos;
- seguridad;
- informes;
- servicios;
- compras.

Esta division permite separar datos base de procesos operativos. Los referenciales sirven como soporte; los movimientos representan operaciones del negocio; los informes permiten controlar y consultar la informacion generada.

Defensa:

```text
El sistema fue dividido por modulos para reducir complejidad, separar responsabilidades y facilitar mantenimiento. Los referenciales alimentan los movimientos, los movimientos generan impacto operativo y los informes permiten controlar la informacion registrada.
```

## 2. Justificacion del uso de UML y StarUML

StarUML se utilizo como herramienta de modelado para representar graficamente el sistema antes y durante la implementacion.

Los diagramas ayudan a explicar:

- actores que interactuan con el sistema;
- procesos principales;
- flujo de eventos;
- entidades relacionadas;
- comportamiento secuencial de algunos casos;
- estructura general del sistema.

La ventaja de StarUML es que permite mantener una vision ordenada del sistema y facilita la comunicacion con evaluadores, usuarios y desarrolladores.

Defensa:

```text
Use UML porque permite documentar el sistema desde distintas vistas: funcional, estructural y de comportamiento. StarUML fue la herramienta elegida para organizar esos diagramas y mantener consistencia entre los casos de uso, las actividades, las secuencias y el modelo de datos.
```

## 3. Casos de uso

Los casos de uso representan funcionalidades vistas desde el punto de vista del usuario y del negocio. No describen codigo, sino interacciones.

Ejemplos:

- mantener articulos;
- registrar pedido de compra;
- generar orden de compra;
- registrar compra;
- registrar diagnostico;
- generar orden de trabajo;
- registrar servicio;
- registrar reclamo;
- registrar nota de credito/debito;
- generar informes.

Cada caso de uso tiene:

- actor principal;
- precondiciones;
- flujo basico;
- flujos alternativos;
- postcondiciones;
- tablas involucradas.

Defensa:

```text
Los casos de uso se utilizaron para definir el alcance funcional del sistema. Cada caso describe una necesidad del usuario y permite validar que la implementacion responda al proceso real de la empresa.
```

Pregunta posible:

```text
Por que no hizo un solo caso de uso para todo?
```

Respuesta:

```text
Porque cada proceso tiene reglas propias, actores, validaciones y postcondiciones diferentes. Separar los casos de uso facilita entender, probar y mantener el sistema.
```

## 4. Diagramas de actividad

Los diagramas de actividad muestran el flujo de procesos, decisiones y validaciones.

Son utiles para defender:

- caminos normales;
- validaciones;
- alternativas;
- decisiones del sistema;
- pasos antes y despues de guardar.

Ejemplo en compras:

- pedido;
- presupuesto de proveedor;
- orden de compra;
- compra;
- cuenta a pagar;
- libro de compras;
- stock.

Ejemplo en servicios:

- solicitud;
- diagnostico;
- presupuesto;
- orden de trabajo;
- registro de servicio;
- reclamo.

Defensa:

```text
Los diagramas de actividad permiten mostrar el comportamiento del proceso antes de llegar al codigo. Ayudan a visualizar decisiones como permisos, estados, stock, anulaciones y validaciones.
```

## 5. Diagramas de secuencia

Los diagramas de secuencia ayudan a explicar la interaccion entre usuario, interfaz, controlador, modelo y base de datos.

En este sistema son especialmente utiles para procesos con varias validaciones:

- registrar compra;
- anular compra;
- registrar nota de credito/debito;
- generar OT;
- completar OT por reclamo;
- registrar servicio;
- anular movimientos.

Defensa:

```text
El diagrama de secuencia muestra el orden de comunicacion entre componentes. En el sistema se utiliza una estructura donde la vista envia datos, el controlador valida y coordina, el modelo ejecuta operaciones de base de datos y la respuesta vuelve a la interfaz.
```

## 6. DER y modelo de datos

El DER representa la estructura de datos del sistema y sus relaciones.

El sistema usa entidades principales como:

- articulos;
- proveedores;
- clientes;
- vehiculos;
- empleados;
- usuarios;
- roles;
- permisos;
- sucursales;
- stock;
- pedido_compra;
- presupuesto_compra;
- orden_compra;
- compra_cabecera;
- compra_detalle;
- nota_compra;
- cuentas_a_pagar;
- libro_compra;
- recepcion_servicio;
- diagnostico_servicio;
- presupuesto_servicio;
- orden_trabajo;
- registro_servicio;
- reclamo_servicio;
- anulacion_auditoria.

Defensa:

```text
El DER permite representar como se almacenan los datos y como se relacionan entre si. Fue clave para evitar duplicidad innecesaria y para asegurar trazabilidad entre los procesos.
```

## 7. Normalizacion de base de datos

La base de datos fue pensada siguiendo criterios de normalizacion.

## 7.1. Primera forma normal

Cada campo almacena un solo valor y las tablas tienen identificadores.

Ejemplo:

- un articulo tiene su codigo, descripcion, tipo, impuesto y estado;
- el detalle de una compra no se guarda como texto largo, sino como filas en `compra_detalle`.

Defensa:

```text
La primera forma normal se cumple al evitar campos repetidos o listas dentro de una misma columna. Los detalles se almacenan en tablas hijas.
```

## 7.2. Segunda forma normal

Los datos dependen de la clave principal de su tabla.

Ejemplo:

- los datos de proveedor estan en `proveedores`;
- los datos de compra estan en `compra_cabecera`;
- los articulos comprados estan en `compra_detalle`.

No se repite toda la informacion del proveedor dentro de cada detalle.

Defensa:

```text
La segunda forma normal se aplica separando cabeceras y detalles, y evitando que datos de una entidad dependan parcialmente de otra.
```

## 7.3. Tercera forma normal

Se evita dependencia transitiva.

Ejemplo:

- el vehiculo se relaciona con modelo;
- el modelo se relaciona con marca;
- no se guarda la marca como texto repetido en cada vehiculo si ya existe una tabla relacionada.

Defensa:

```text
La tercera forma normal se aplica separando datos que pertenecen a entidades distintas. Esto reduce redundancia y evita inconsistencias.
```

## 8. Cabecera y detalle

Muchos procesos usan estructura cabecera-detalle:

- pedido y pedido_detalle;
- presupuesto y presupuesto_detalle;
- orden de compra y orden_compra_detalle;
- compra_cabecera y compra_detalle;
- nota_compra y nota_compra_detalle;
- presupuesto_servicio y presupuesto_detalleservicio;
- orden_trabajo y orden_trabajo_detalle;
- registro_servicio y registro_servicio_detalle.

Defensa:

```text
La estructura cabecera-detalle permite registrar datos generales una sola vez en la cabecera y multiples items relacionados en el detalle. Es una practica correcta para documentos comerciales y operativos.
```

## 9. Estados

El sistema usa estados para controlar el ciclo de vida de los registros.

Ejemplos:

- activo;
- inactivo;
- pendiente;
- aprobado;
- anulado;
- OT generada;
- pendiente de completar;
- finalizado.

Los estados evitan eliminar historico importante y permiten controlar que acciones estan disponibles.

Defensa:

```text
Los estados permiten controlar el flujo de cada proceso y evitar operaciones fuera de secuencia. Por ejemplo, no se puede generar una OT si el presupuesto no esta aprobado, y no se puede anular un documento si ya tiene movimientos dependientes no reversibles.
```

## 10. Trazabilidad

La trazabilidad es una de las ideas mas importantes del sistema.

Ejemplos:

- una orden de compra puede venir de un presupuesto;
- una compra puede venir de una orden;
- una nota de credito se asocia a una compra;
- una OT puede venir de un presupuesto de servicio;
- una OT por reclamo se asocia al reclamo;
- un diagnostico puede originar presupuesto u OT;
- las anulaciones quedan registradas.

Defensa:

```text
El sistema busca que cada documento importante tenga relacion con su origen. Esto permite consultar de donde viene una operacion, que impacto genero y que documentos dependen de ella.
```

## 11. Auditoria de anulaciones

Las anulaciones no se tratan como simples eliminaciones. El sistema registra motivo, usuario, fecha, modulo y tabla afectada.

Defensa:

```text
La auditoria de anulaciones permite mantener responsabilidad y trazabilidad. En procesos administrativos no conviene borrar sin dejar evidencia, porque se pierde control sobre quien anulo, cuando y por que.
```

## 12. Seguridad: usuarios, roles y permisos

El sistema utiliza:

- usuarios;
- roles;
- permisos por accion.

Esto permite que un usuario pueda tener acceso a una parte del sistema sin necesariamente poder modificar, eliminar o anular.

Ejemplo:

- `cliente.ver`;
- `cliente.agregar`;
- `cliente.editar`;
- `cliente.eliminar`;
- `servicio.ot.asignar_tecnico`.

Defensa:

```text
La seguridad se maneja mediante roles y permisos para aplicar control granular. Esto evita que todos los usuarios tengan acceso total y permite adaptar el sistema a responsabilidades reales dentro de la empresa.
```

## 13. Justificacion de modulos de compras

El modulo de compras cubre el flujo administrativo desde la necesidad hasta el impacto contable/operativo:

- pedido;
- presupuesto de proveedor;
- orden de compra;
- compra;
- cuenta a pagar;
- libro de compras;
- stock;
- notas de credito/debito.

Defensa:

```text
El flujo de compras fue modelado por etapas porque cada documento tiene una finalidad distinta: el pedido expresa necesidad, el presupuesto compara condiciones, la orden formaliza la solicitud, la compra registra la factura y los movimientos posteriores impactan stock, cuentas y libro de compras.
```

## 14. Justificacion de notas de credito/debito

Las notas de credito y debito permiten ajustar una factura de compra.

- Nota de credito: disminuye la deuda o anula total/parcialmente.
- Nota de debito: aumenta la deuda.
- Devolucion fisica: puede afectar stock cuando corresponde.

Defensa:

```text
Las notas se modelan separadas de la compra para mantener el documento original intacto y registrar ajustes posteriores con trazabilidad. El saldo real se calcula considerando factura, notas de debito y notas de credito activas.
```

## 15. Justificacion de modulos de servicios

El modulo de servicios cubre el flujo de taller:

- solicitud de servicio;
- diagnostico;
- presupuesto;
- orden de trabajo;
- registro de servicio;
- reclamo.

Defensa:

```text
El modulo de servicios se modelo por etapas porque en un taller primero se recibe la solicitud, luego se diagnostica, se presupuesta si corresponde, se genera la OT para ejecutar y finalmente se registra el servicio realizado.
```

## 16. Diagnostico y OT

El diagnostico representa la evaluacion tecnica. La OT representa la ejecucion operativa.

Defensa:

```text
El diagnostico no se modifica al completar una OT. La OT toma el diagnostico como base y registra el detalle operativo confirmado de trabajos y repuestos. Esto permite mantener el antecedente tecnico original y registrar lo que efectivamente se ejecutara.
```

## 17. OT por reclamo

La OT por reclamo se utiliza cuando existe un reclamo valido, en garantia y sin cobro.

El sistema permite completar la OT tomando como base el diagnostico del reclamo, pero validando stock y detalle operativo.

Defensa:

```text
En una OT por reclamo, el diagnostico queda como antecedente tecnico. La OT permite confirmar la ejecucion operativa, incluyendo ajustes de repuestos por disponibilidad de stock o equivalencia, sin alterar el diagnostico original.
```

## 18. Stock

El stock se maneja por articulo y sucursal.

Esto permite que una sucursal tenga disponibilidad distinta a otra.

Defensa:

```text
El stock se controla por sucursal porque la disponibilidad fisica depende del lugar donde se encuentra el producto. Por eso las validaciones de stock siempre consideran articulo y sucursal.
```

## 19. Movimientos de stock

El sistema no solo cambia numeros en la tabla stock. Tambien registra movimientos.

Esto permite saber:

- que articulo se movio;
- cantidad;
- tipo de movimiento;
- origen;
- fecha;
- usuario.

Defensa:

```text
La tabla de movimientos de stock permite trazabilidad del inventario. No basta con saber el stock actual; tambien es necesario saber por que cambio.
```

## 20. Informes

Los informes permiten transformar los datos registrados en informacion de control.

Se separan en:

- informes referenciales;
- informes de compras;
- informes de servicios;
- informes de movimientos.

Defensa:

```text
Los informes se incluyeron para que la empresa pueda consultar informacion operativa sin acceder directamente a la base de datos. Permiten control, seguimiento y toma de decisiones.
```

---

# Parte 2: Defensa de la programacion

## 21. Arquitectura del codigo

El sistema esta desarrollado en PHP con una estructura separada en:

- vistas;
- controladores;
- modelos;
- ajax;
- configuracion;
- reportes/PDF;
- documentos.

Defensa:

```text
La estructura separa responsabilidades. Las vistas muestran la interfaz, los controladores reciben y validan solicitudes, los modelos interactuan con la base de datos y los archivos AJAX permiten operaciones asincronas sin recargar toda la pantalla.
```

## 22. Patron utilizado

El sistema sigue una organizacion similar a MVC:

- Modelo: consultas y transacciones con la base de datos.
- Vista: formularios, listados y pantallas.
- Controlador: reglas de validacion y coordinacion.

Defensa:

```text
Se utiliza una organizacion tipo MVC para evitar mezclar interfaz, logica de negocio y acceso a datos en un mismo archivo. Esto facilita mantenimiento y ubicacion de errores.
```

## 23. Uso de AJAX

AJAX se usa para:

- buscar articulos;
- agregar items;
- consultar facturas;
- guardar formularios;
- anular documentos;
- cargar datos dinamicos;
- evitar recargas innecesarias.

Defensa:

```text
AJAX mejora la experiencia del usuario porque permite validar, buscar y guardar informacion sin recargar completamente la pagina. Es util en formularios con detalles dinamicos, como compras, notas, presupuestos y OT.
```

## 24. Validaciones en frontend y backend

El sistema valida en dos niveles:

- frontend: ayuda inmediata al usuario;
- backend: seguridad real antes de guardar.

Ejemplo:

- la pantalla puede advertir que no hay stock;
- el backend vuelve a validar stock antes de confirmar.

Defensa:

```text
La validacion del frontend mejora la usabilidad, pero la validacion importante esta en el backend, porque el usuario podria manipular la interfaz o enviar datos manualmente.
```

## 25. Transacciones

Los procesos importantes se ejecutan dentro de transacciones.

Ejemplos:

- registrar compra;
- registrar nota de credito/debito;
- anular documentos;
- completar OT;
- registrar servicio.

Defensa:

```text
Las transacciones aseguran integridad. Si una parte del proceso falla, se revierte todo para evitar datos incompletos, como una cabecera sin detalle o un stock actualizado sin documento asociado.
```

## 26. Consultas preparadas

El sistema utiliza consultas preparadas con parametros en los modelos.

Defensa:

```text
Las consultas preparadas reducen el riesgo de inyeccion SQL y permiten separar la estructura de la consulta de los valores enviados por el usuario.
```

## 27. Sanitizacion de entradas

Los datos recibidos pasan por funciones de limpieza y validacion.

Ejemplos:

- limpiar texto;
- validar numeros;
- validar fechas;
- validar permisos;
- validar existencia de registros.

Defensa:

```text
La sanitizacion evita procesar datos inesperados y reduce errores o riesgos de seguridad. Se complementa con validaciones de formato y existencia en base de datos.
```

## 28. Control de permisos

Antes de ejecutar acciones sensibles, el sistema valida permisos.

Ejemplos:

- agregar;
- editar;
- eliminar;
- anular;
- imprimir;
- completar OT.

Defensa:

```text
El control de permisos se realiza en backend para que no dependa solo de ocultar botones en pantalla. Aunque un boton no se vea, el servidor igual valida si el usuario puede realizar la accion.
```

## 29. Estados en programacion

Los estados controlan acciones disponibles.

Ejemplos:

- no anular una compra ya anulada;
- no generar OT si el presupuesto no esta aprobado;
- no completar OT si no esta pendiente;
- no registrar NC sobre saldo insuficiente;
- no eliminar referenciales con movimientos.

Defensa:

```text
Los estados funcionan como controles del flujo. Evitan que el usuario ejecute acciones fuera del orden logico del negocio.
```

## 30. Eliminacion logica y fisica

En varios referenciales, el sistema puede inactivar o eliminar segun corresponda.

Regla defendible:

```text
Si un registro tiene movimientos o referencias, no se elimina fisicamente para no romper trazabilidad. Si no tiene dependencias, puede eliminarse.
```

Para articulos:

```text
El articulo no se elimina si tiene movimientos en movimientostock. Si no tiene movimientos, puede eliminarse aunque este inactivo.
```

Defensa:

```text
La eliminacion depende de la trazabilidad, no solo del estado. El estado inactivo no debe impedir eliminar si el registro no esta referenciado.
```

## 31. Stock en programacion

El stock se valida en operaciones donde puede haber impacto o compromiso operativo.

Ejemplos:

- orden de trabajo por reclamo;
- nota de credito con devolucion fisica;
- registro de servicio;
- inventario;
- compra;
- transferencia;
- ajuste.

Defensa:

```text
El sistema valida stock antes de permitir operaciones que requieren disponibilidad fisica. La validacion se hace por articulo y sucursal.
```

## 32. Nota de credito y nota de debito

La logica actual considera:

```text
saldo disponible = total compra + notas de debito activas - notas de credito activas
```

Defensa:

```text
La Nota de Debito aumenta la obligacion con el proveedor, por lo tanto debe formar parte del saldo disponible para una Nota de Credito. Si no se considera, el sistema podria impedir una anulacion total correcta o calcular mal el saldo.
```

Ejemplo:

```text
Compra: 100.000
Nota de Debito: 10.000
Nota de Credito previa: 5.000
Saldo real: 105.000
```

Si se quiere anular el saldo total restante, la nueva NC debe cubrir 105.000.

## 33. Orden de trabajo por reclamo

La OT por reclamo tiene una regla especial:

- reclamo valido;
- garantia vigente;
- sin cobro;
- diagnostico asociado;
- pendiente de completar.

Defensa:

```text
Se separa la generacion de la OT y su completado porque en un reclamo la OT puede requerir asignar equipo, tecnico y confirmar el detalle operativo antes de pasar a ejecucion.
```

## 34. Por que se pueden ajustar repuestos en la OT

Esta es una pregunta probable.

Respuesta recomendada:

```text
No se modifica el diagnostico. El diagnostico queda como antecedente tecnico. La OT registra la ejecucion operativa. Si un repuesto diagnosticado no tiene stock, se puede seleccionar un repuesto equivalente o ajustar la cantidad operativa, siempre validando disponibilidad y manteniendo la trazabilidad con el diagnostico original.
```

## 35. PDF de Orden de Trabajo

El PDF sirve como documento de soporte para el taller y el cliente.

Incluye:

- datos de OT;
- cliente;
- vehiculo;
- trabajos;
- repuestos;
- diagnostico;
- condiciones de autorizacion.

Defensa:

```text
El PDF formaliza la informacion de la OT para impresion o respaldo. Resume el trabajo autorizado y mantiene informacion relevante del diagnostico, cliente y vehiculo.
```

## 36. Informes y filtros

Los informes usan filtros para consultar datos:

- fechas;
- estado;
- sucursal;
- cliente;
- proveedor;
- articulo;
- usuario;
- tipo de movimiento.

Defensa:

```text
Los filtros permiten que el usuario consulte informacion especifica sin modificar datos. Los informes se separan de los movimientos para mantener claridad entre registrar operaciones y analizarlas.
```

## 37. Manejo de errores

El sistema responde con mensajes al usuario:

- errores de validacion;
- errores de permisos;
- errores de stock;
- errores de estado;
- confirmaciones exitosas.

Defensa:

```text
Los mensajes permiten guiar al usuario y evitar operaciones incorrectas. Ademas, al validar antes de guardar, el sistema protege la integridad de los datos.
```

## 38. Sesiones

La sesion permite identificar:

- usuario logueado;
- sucursal;
- permisos;
- datos temporales de algunos formularios.

Defensa:

```text
La sesion se utiliza para mantener el contexto del usuario autenticado. Esto permite aplicar restricciones por sucursal y permisos durante las operaciones.
```

## 39. Por que no todo se borra

En sistemas administrativos, borrar datos historicos puede ser peligroso.

Defensa:

```text
No se eliminan documentos con impacto operativo porque forman parte del historial. Por eso se usan estados de anulacion y auditoria. La eliminacion se reserva para registros sin movimientos o sin referencias.
```

## 40. Posibles preguntas y respuestas rapidas

### Por que separaste cabecera y detalle?

```text
Porque un documento tiene datos generales y multiples items. Separarlos evita repetir informacion y respeta normalizacion.
```

### Por que usaste estados?

```text
Para controlar el ciclo de vida de cada proceso y evitar acciones fuera de secuencia.
```

### Por que validar tambien en backend si ya valido en pantalla?

```text
Porque la validacion de pantalla es solo ayuda visual. La seguridad real debe estar en el servidor.
```

### Por que la Nota de Credito considera Nota de Debito?

```text
Porque la Nota de Debito aumenta la deuda. El saldo real de la factura no es solo el total original, sino total original mas debitos menos creditos.
```

### Por que la OT puede ajustar repuestos?

```text
Porque la OT registra ejecucion operativa. El diagnostico no se modifica; se mantiene como antecedente tecnico.
```

### Por que no se elimina un articulo con movimientos?

```text
Porque se perderia trazabilidad de stock y documentos historicos.
```

### Por que usar roles y permisos?

```text
Para que cada usuario tenga acceso solo a las acciones que corresponden a su responsabilidad.
```

### Por que usar transacciones?

```text
Para que los procesos complejos se guarden completos o no se guarden. Evita inconsistencias.
```

### Por que usar StarUML?

```text
Porque permite representar el sistema antes de programar y facilita explicar procesos, actores, secuencias y estructura.
```

### Por que el DER es importante?

```text
Porque muestra como se organizan y relacionan los datos. Es la base para construir una base de datos consistente.
```

## 41. Defensa final corta

```text
El sistema fue analizado separando procesos de compras, servicios, referenciales, seguridad e informes. Se modelo con UML para representar actores, flujos, secuencias y estructura de datos. La base de datos fue organizada con criterios de normalizacion, separando cabeceras, detalles y entidades relacionadas para evitar duplicidad y mantener trazabilidad.

En la programacion se aplico una estructura tipo MVC, con vistas, controladores, modelos y AJAX. Las operaciones importantes validan permisos, estados, datos obligatorios, stock y relaciones antes de guardar. Los procesos criticos se ejecutan con transacciones para preservar integridad. Ademas, las anulaciones y movimientos se registran con trazabilidad, evitando perdida de informacion historica.
```

## 42. Frases clave para usar en defensa

- El diagnostico no se modifica; queda como antecedente tecnico.
- La OT registra la ejecucion operativa confirmada.
- El stock se valida por articulo y sucursal.
- La trazabilidad es mas importante que borrar fisicamente documentos.
- La Nota de Credito considera el saldo real: compra mas debitos menos creditos.
- Los estados controlan el ciclo de vida del proceso.
- Los permisos se validan en backend, no solo ocultando botones.
- Las transacciones evitan datos incompletos.
- El DER ayuda a mantener relaciones claras y reducir redundancia.
- StarUML se uso para documentar visualmente el analisis y diseno.

