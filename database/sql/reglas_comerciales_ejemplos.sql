-- Ejemplos de reglas comerciales para probar la generacion.
-- Ajustar estos IDs segun los datos existentes en la base.
SET @id_usuario = 1;
SET @id_sucursal = 1;
SET @id_articulo = 32;
SET @id_articulo_precio_fijo = 33;
SET @id_servicio_gratis = 40;
SET @id_cliente = 15;
SET @id_categoria = 1;

-- 1. Promocion N x M: 2x1 por articulo.
INSERT INTO regla_comercial
(nombre, descripcion, fecha_inicio, fecha_fin, id_sucursal, prioridad, modo_competencia, estado, id_usuario_crea)
VALUES
('2x1 por desagote', 'Lleva 2 unidades del articulo seleccionado y paga 1.', '2026-05-01', '2026-12-31', NULL, 100, 'COMPITE_MISMO_ALCANCE', 1, @id_usuario);

SET @regla_2x1 = LAST_INSERT_ID();

INSERT INTO regla_comercial_condicion
(id_regla, tipo_condicion, operador, valor_ref, valor_texto)
VALUES
(@regla_2x1, 'ARTICULO', '=', @id_articulo, NULL),
(@regla_2x1, 'CANTIDAD_ITEMS', '>=', NULL, '2');

INSERT INTO regla_comercial_descuento
(id_regla, nombre, tipo, valor, cantidad_requerida, cantidad_cobrada, aplica_a, alcance_tipo, alcance_ref)
VALUES
(@regla_2x1, 'Lleva 2 paga 1', 'NXM', 0, 2, 1, 'ARTICULO', 'ARTICULO', @id_articulo);

-- 2. Promocion por porcentaje sobre articulo.
INSERT INTO regla_comercial
(nombre, descripcion, fecha_inicio, fecha_fin, id_sucursal, prioridad, modo_competencia, estado, id_usuario_crea)
VALUES
('15% en articulo seleccionado', 'Descuento porcentual sobre un articulo puntual.', '2026-05-01', '2026-12-31', NULL, 80, 'COMPITE_MISMO_ALCANCE', 1, @id_usuario);

SET @regla_porcentaje_articulo = LAST_INSERT_ID();

INSERT INTO regla_comercial_condicion
(id_regla, tipo_condicion, operador, valor_ref, valor_texto)
VALUES
(@regla_porcentaje_articulo, 'ARTICULO', '=', @id_articulo, NULL);

INSERT INTO regla_comercial_descuento
(id_regla, nombre, tipo, valor, cantidad_requerida, cantidad_cobrada, aplica_a, alcance_tipo, alcance_ref)
VALUES
(@regla_porcentaje_articulo, '15% sobre articulo', 'PORCENTAJE', 15, NULL, NULL, 'ARTICULO', 'ARTICULO', @id_articulo);

-- 3. Promocion por monto fijo sobre total de operacion.
INSERT INTO regla_comercial
(nombre, descripcion, fecha_inicio, fecha_fin, id_sucursal, prioridad, modo_competencia, estado, id_usuario_crea)
VALUES
('Gs. 50000 desde Gs. 500000', 'Descuento fijo cuando la operacion supera el minimo.', '2026-05-01', '2026-12-31', NULL, 60, 'COMPITE_MISMO_ALCANCE', 1, @id_usuario);

SET @regla_monto_total = LAST_INSERT_ID();

INSERT INTO regla_comercial_condicion
(id_regla, tipo_condicion, operador, valor_ref, valor_texto)
VALUES
(@regla_monto_total, 'TOTAL_OPERACION', '>=', NULL, '500000');

INSERT INTO regla_comercial_descuento
(id_regla, nombre, tipo, valor, cantidad_requerida, cantidad_cobrada, aplica_a, alcance_tipo, alcance_ref)
VALUES
(@regla_monto_total, 'Gs. 50000 sobre total', 'MONTO_FIJO', 50000, NULL, NULL, 'TOTAL', 'TOTAL', NULL);

-- 4. Promocion por precio fijo.
INSERT INTO regla_comercial
(nombre, descripcion, fecha_inicio, fecha_fin, id_sucursal, prioridad, modo_competencia, estado, id_usuario_crea)
VALUES
('Precio fijo articulo seleccionado', 'Articulo seleccionado queda a un precio especial.', '2026-05-01', '2026-12-31', NULL, 90, 'COMPITE_MISMO_ALCANCE', 1, @id_usuario);

SET @regla_precio_fijo = LAST_INSERT_ID();

INSERT INTO regla_comercial_condicion
(id_regla, tipo_condicion, operador, valor_ref, valor_texto)
VALUES
(@regla_precio_fijo, 'ARTICULO', '=', @id_articulo_precio_fijo, NULL);

INSERT INTO regla_comercial_descuento
(id_regla, nombre, tipo, valor, cantidad_requerida, cantidad_cobrada, aplica_a, alcance_tipo, alcance_ref)
VALUES
(@regla_precio_fijo, 'Precio fijo Gs. 120000', 'PRECIO_FIJO', 120000, NULL, NULL, 'ARTICULO', 'ARTICULO', @id_articulo_precio_fijo);

-- 5. Promocion por cliente.
INSERT INTO regla_comercial
(nombre, descripcion, fecha_inicio, fecha_fin, id_sucursal, prioridad, modo_competencia, estado, id_usuario_crea)
VALUES
('Cliente preferencial 10%', 'Descuento especial para un cliente especifico.', '2026-05-01', '2026-12-31', NULL, 70, 'NO_COMPITE', 1, @id_usuario);

SET @regla_cliente = LAST_INSERT_ID();

INSERT INTO regla_comercial_condicion
(id_regla, tipo_condicion, operador, valor_ref, valor_texto)
VALUES
(@regla_cliente, 'CLIENTE', '=', @id_cliente, NULL);

INSERT INTO regla_comercial_descuento
(id_regla, nombre, tipo, valor, cantidad_requerida, cantidad_cobrada, aplica_a, alcance_tipo, alcance_ref)
VALUES
(@regla_cliente, '10% cliente preferencial', 'PORCENTAJE', 10, NULL, NULL, 'TOTAL', 'TOTAL', NULL);

-- 6. Promocion por categoria.
INSERT INTO regla_comercial
(nombre, descripcion, fecha_inicio, fecha_fin, id_sucursal, prioridad, modo_competencia, estado, id_usuario_crea)
VALUES
('20% por categoria', 'Descuento para articulos de una categoria.', '2026-05-01', '2026-12-31', NULL, 75, 'COMPITE_MISMO_ALCANCE', 1, @id_usuario);

SET @regla_categoria = LAST_INSERT_ID();

INSERT INTO regla_comercial_condicion
(id_regla, tipo_condicion, operador, valor_ref, valor_texto)
VALUES
(@regla_categoria, 'CATEGORIA', '=', @id_categoria, NULL);

INSERT INTO regla_comercial_descuento
(id_regla, nombre, tipo, valor, cantidad_requerida, cantidad_cobrada, aplica_a, alcance_tipo, alcance_ref)
VALUES
(@regla_categoria, '20% categoria', 'PORCENTAJE', 20, NULL, NULL, 'CATEGORIA', 'CATEGORIA', @id_categoria);

-- 7. Promocion por cantidad de items.
INSERT INTO regla_comercial
(nombre, descripcion, fecha_inicio, fecha_fin, id_sucursal, prioridad, modo_competencia, estado, id_usuario_crea)
VALUES
('5% por 4 items o mas', 'Descuento sobre total cuando la operacion tiene 4 items o mas.', '2026-05-01', '2026-12-31', NULL, 40, 'NO_COMPITE', 1, @id_usuario);

SET @regla_cantidad = LAST_INSERT_ID();

INSERT INTO regla_comercial_condicion
(id_regla, tipo_condicion, operador, valor_ref, valor_texto)
VALUES
(@regla_cantidad, 'CANTIDAD_ITEMS', '>=', NULL, '4');

INSERT INTO regla_comercial_descuento
(id_regla, nombre, tipo, valor, cantidad_requerida, cantidad_cobrada, aplica_a, alcance_tipo, alcance_ref)
VALUES
(@regla_cantidad, '5% por cantidad', 'PORCENTAJE', 5, NULL, NULL, 'TOTAL', 'TOTAL', NULL);

-- 8. Promocion por sucursal.
INSERT INTO regla_comercial
(nombre, descripcion, fecha_inicio, fecha_fin, id_sucursal, prioridad, modo_competencia, estado, id_usuario_crea)
VALUES
('Promo sucursal seleccionada', 'Descuento fijo disponible solo para una sucursal.', '2026-05-01', '2026-12-31', @id_sucursal, 30, 'NO_COMPITE', 1, @id_usuario);

SET @regla_sucursal = LAST_INSERT_ID();

INSERT INTO regla_comercial_condicion
(id_regla, tipo_condicion, operador, valor_ref, valor_texto)
VALUES
(@regla_sucursal, 'SUCURSAL', '=', @id_sucursal, NULL);

INSERT INTO regla_comercial_descuento
(id_regla, nombre, tipo, valor, cantidad_requerida, cantidad_cobrada, aplica_a, alcance_tipo, alcance_ref)
VALUES
(@regla_sucursal, 'Gs. 30000 por sucursal', 'MONTO_FIJO', 30000, NULL, NULL, 'TOTAL', 'TOTAL', NULL);

-- 9. Promocion con servicio gratis comprando producto.
INSERT INTO regla_comercial
(nombre, descripcion, fecha_inicio, fecha_fin, id_sucursal, prioridad, modo_competencia, estado, id_usuario_crea)
VALUES
('Servicio gratis comprando producto', 'Comprando 2 unidades del articulo seleccionado, el servicio indicado queda gratis.', '2026-05-01', '2026-12-31', NULL, 95, 'COMPITE_MISMO_ALCANCE', 1, @id_usuario);

SET @regla_servicio_gratis = LAST_INSERT_ID();

INSERT INTO regla_comercial_condicion
(id_regla, tipo_condicion, operador, valor_ref, valor_texto)
VALUES
(@regla_servicio_gratis, 'ARTICULO', '=', @id_articulo, NULL),
(@regla_servicio_gratis, 'CANTIDAD_ITEMS', '>=', NULL, '2');

INSERT INTO regla_comercial_descuento
(id_regla, nombre, tipo, valor, cantidad_requerida, cantidad_cobrada, aplica_a, alcance_tipo, alcance_ref)
VALUES
(@regla_servicio_gratis, 'Servicio gratis', 'GRATIS', 0, NULL, NULL, 'ARTICULO', 'ARTICULO', @id_servicio_gratis);
