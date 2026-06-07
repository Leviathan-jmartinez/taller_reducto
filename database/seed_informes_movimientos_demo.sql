-- Datos demo para evaluar Informes Referenciales y Panel de Movimientos.
-- Ejecutar sobre una BD con datos base cargados. Usa IDs 9000+ para poder limpiar/reinsertar.
-- Charset recomendado: utf8mb4.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM registro_servicio_detalle WHERE idregistro_servicio BETWEEN 9001 AND 9010;
DELETE FROM registro_servicio WHERE idregistro_servicio BETWEEN 9001 AND 9010;
DELETE FROM orden_trabajo_detalle WHERE idorden_trabajo BETWEEN 9001 AND 9010;
DELETE FROM orden_trabajo WHERE idorden_trabajo BETWEEN 9001 AND 9010;
DELETE FROM presupuesto_detalleservicio WHERE idpresupuesto_servicio BETWEEN 9001 AND 9010;
DELETE FROM presupuesto_servicio WHERE idpresupuesto_servicio BETWEEN 9001 AND 9010;
DELETE FROM recepcion_servicio WHERE idrecepcion BETWEEN 9001 AND 9010;
DELETE FROM transferencia_stock_detalle WHERE idtransferencia BETWEEN 9001 AND 9010;
DELETE FROM transferencia_stock WHERE idtransferencia BETWEEN 9001 AND 9010;
DELETE FROM libro_compra WHERE idlibro_compra BETWEEN 9001 AND 9010;
DELETE FROM compra_detalle WHERE idcompra_cabecera BETWEEN 9001 AND 9010;
DELETE FROM compra_cabecera WHERE idcompra_cabecera BETWEEN 9001 AND 9010;
DELETE FROM orden_compra_detalle WHERE idorden_compra BETWEEN 9001 AND 9010;
DELETE FROM orden_compra WHERE idorden_compra BETWEEN 9001 AND 9010;
DELETE FROM presupuesto_detalle WHERE idpresupuesto_compra BETWEEN 9001 AND 9010;
DELETE FROM presupuesto_compra WHERE idpresupuesto_compra BETWEEN 9001 AND 9010;
DELETE FROM pedido_detalle WHERE idpedido_cabecera BETWEEN 9001 AND 9010;
DELETE FROM pedido_cabecera WHERE idpedido_cabecera BETWEEN 9001 AND 9010;
DELETE FROM movimientostock WHERE MovStockId BETWEEN 9001 AND 9020;
DELETE FROM proveedores WHERE idproveedores BETWEEN 9001 AND 9003;

SET FOREIGN_KEY_CHECKS = 1;

SET @sucursal := (SELECT id_sucursal FROM sucursales ORDER BY id_sucursal LIMIT 1);
SET @sucursal_destino := COALESCE((SELECT id_sucursal FROM sucursales WHERE id_sucursal <> @sucursal ORDER BY id_sucursal LIMIT 1), @sucursal);
SET @usuario := (SELECT id_usuario FROM usuarios ORDER BY id_usuario LIMIT 1);
SET @ciudad := (SELECT id_ciudad FROM ciudades ORDER BY id_ciudad LIMIT 1);
SET @art1 := (SELECT id_articulo FROM articulos ORDER BY id_articulo LIMIT 1);
SET @art2 := COALESCE((SELECT id_articulo FROM articulos WHERE id_articulo <> @art1 ORDER BY id_articulo LIMIT 1), @art1);
SET @cliente := (SELECT id_cliente FROM clientes ORDER BY id_cliente LIMIT 1);
SET @vehiculo := (SELECT id_vehiculo FROM vehiculos ORDER BY id_vehiculo LIMIT 1);
SET @empleado := (SELECT idempleados FROM empleados ORDER BY idempleados LIMIT 1);
SET @equipo := (SELECT id_equipo FROM equipo_trabajo ORDER BY id_equipo LIMIT 1);

START TRANSACTION;

-- Proveedores demo para que el Top Proveedores no quede concentrado en un solo nombre.
INSERT INTO proveedores (idproveedores, id_ciudad, razon_social, ruc, telefono, correo, direccion, estado) VALUES
(9001, @ciudad, 'Repuestos Central S.A.', '80012345-6', '021555100', 'ventas@repuestoscentral.com.py', 'Avda. Artigas 1540', 1),
(9002, @ciudad, 'Autopartes del Sur S.R.L.', '80045678-1', '021555220', 'contacto@autopartesdelsur.com.py', 'Ruta Acceso Sur Km 12', 1),
(9003, @ciudad, 'Lubricantes Asuncion S.A.', '80078912-3', '021555330', 'pedidos@lubricantesasuncion.com.py', 'Avda. Eusebio Ayala 2990', 1);

SET @proveedor1 := 9001;
SET @proveedor2 := 9002;
SET @proveedor3 := 9003;

-- Pedidos de compra: variedad por estado y periodo.
INSERT INTO pedido_cabecera (idpedido_cabecera, id_sucursal, id_usuario, fecha, estado, updated, updatedby) VALUES
(9001, @sucursal, @usuario, '2026-01-08 09:10:00', 1, NULL, NULL),
(9002, @sucursal, @usuario, '2026-02-12 10:30:00', 2, '2026-02-13 08:00:00', @usuario),
(9003, @sucursal, @usuario, '2026-03-20 14:05:00', 0, '2026-03-21 11:00:00', @usuario),
(9004, @sucursal, @usuario, '2026-04-18 16:22:00', 2, '2026-04-19 09:40:00', @usuario);

INSERT INTO pedido_detalle (idpedido_cabecera, id_articulo, cantidad, stock_actual) VALUES
(9001, @art1, 6, 12), (9001, @art2, 3, 7),
(9002, @art1, 10, 5), (9002, @art2, 4, 9),
(9003, @art1, 2, 11), (9004, @art2, 12, 2);

-- Presupuestos de compra.
INSERT INTO presupuesto_compra (idpresupuesto_compra, id_sucursal, idproveedores, id_usuario, fecha, estado, fecha_venc, updatedby, updated, total, idPedido) VALUES
(9001, @sucursal, @proveedor1, @usuario, '2026-01-09', 1, '2026-01-25', NULL, NULL, 1250000.00, 9001),
(9002, @sucursal, @proveedor2, @usuario, '2026-02-13', 2, '2026-02-28', @usuario, '2026-02-14 09:00:00', 2340000.00, 9002),
(9003, @sucursal, @proveedor3, @usuario, '2026-03-22', 0, '2026-04-05', @usuario, '2026-03-23 10:00:00', 450000.00, 9003),
(9004, @sucursal, @proveedor1, @usuario, '2026-04-20', 2, '2026-05-05', @usuario, '2026-04-21 08:30:00', 3120000.00, 9004);

INSERT INTO presupuesto_detalle (idpresupuesto_compra, id_articulo, cantidad, precio, subtotal) VALUES
(9001, @art1, 5.00, 180000.00, 900000.00), (9001, @art2, 2.00, 175000.00, 350000.00),
(9002, @art1, 8.00, 220000.00, 1760000.00), (9002, @art2, 4.00, 145000.00, 580000.00),
(9003, @art2, 3.00, 150000.00, 450000.00),
(9004, @art1, 12.00, 190000.00, 2280000.00), (9004, @art2, 6.00, 140000.00, 840000.00);

-- Ordenes de compra.
INSERT INTO orden_compra (idorden_compra, id_sucursal, presupuestoid, idproveedores, id_usuario, fecha, estado, fecha_entrega, updatedby, updated) VALUES
(9001, @sucursal, 9001, @proveedor1, @usuario, '2026-01-10', 1, '2026-01-18', NULL, NULL),
(9002, @sucursal, 9002, @proveedor2, @usuario, '2026-02-14', 2, '2026-02-20', @usuario, '2026-02-20 09:30:00'),
(9003, @sucursal, 9003, @proveedor3, @usuario, '2026-03-24', 0, '2026-03-30', @usuario, '2026-03-25 15:10:00'),
(9004, @sucursal, 9004, @proveedor1, @usuario, '2026-04-22', 2, '2026-04-28', @usuario, '2026-04-28 10:00:00');

INSERT INTO orden_compra_detalle (idorden_compra, id_articulo, cantidad, precio_unitario, cantidad_pendiente) VALUES
(9001, @art1, 5, 180000, 5), (9001, @art2, 2, 175000, 2),
(9002, @art1, 8, 220000, 0), (9002, @art2, 4, 145000, 0),
(9003, @art2, 3, 150000, 3),
(9004, @art1, 12, 190000, 0), (9004, @art2, 6, 140000, 0);

-- Compras y libro de compras.
INSERT INTO compra_cabecera (idcompra_cabecera, id_sucursal, idproveedores, id_usuario, fecha_creacion, nro_factura, fecha_factura, nro_timbrado, vencimiento_timbrado, estado, total_compra, condicion, compra_intervalo, idOcompra, updated, updatedby) VALUES
(9001, @sucursal, @proveedor2, @usuario, '2026-02-20 09:30:00', '001-001-009001', '2026-02-20', 12345678, '2026-12-31', 2, 2340000, 'contado', '0', 9002, NULL, NULL),
(9002, @sucursal, @proveedor1, @usuario, '2026-04-28 10:00:00', '001-001-009002', '2026-04-28', 12345678, '2026-12-31', 2, 3120000, 'credito', '30', 9004, NULL, NULL),
(9003, @sucursal, @proveedor3, @usuario, '2026-05-16 11:20:00', '001-001-009003', '2026-05-16', 12345678, '2026-12-31', 1, 980000, 'contado', '0', NULL, NULL, NULL),
(9004, @sucursal, @proveedor2, @usuario, '2026-06-03 08:45:00', '001-001-009004', '2026-06-03', 12345678, '2026-12-31', 0, 620000, 'contado', '0', NULL, '2026-06-04 09:00:00', @usuario);

INSERT INTO compra_detalle (idcompra_cabecera, id_articulo, precio_unitario, cantidad_recibida, subtotal, tipo_iva, ivaPro) VALUES
(9001, @art1, 220000.00, 8, 1760000.00, '2', 160000.00), (9001, @art2, 145000.00, 4, 580000.00, '2', 52727.27),
(9002, @art1, 190000.00, 12, 2280000.00, '2', 207272.73), (9002, @art2, 140000.00, 6, 840000.00, '2', 76363.64),
(9003, @art1, 196000.00, 5, 980000.00, '2', 89090.91),
(9004, @art2, 155000.00, 4, 620000.00, '2', 56363.64);

INSERT INTO libro_compra (idlibro_compra, id_sucursal, idcompra_cabecera, fecha, tipo_comprobante, serie, nro_comprobante, idproveedores, proveedor_nombre, proveedor_ruc, exenta, gravada_5, iva_5, gravada_10, iva_10, total, estado, fecha_registro)
SELECT 9001, @sucursal, 9001, '2026-02-20', 'factura', '001-001', '001-001-009001', p.idproveedores, p.razon_social, p.ruc, 0, 0, 0, 2127272.73, 212727.27, 2340000.00, 1, '2026-02-20 09:30:00' FROM proveedores p WHERE p.idproveedores = @proveedor2
UNION ALL
SELECT 9002, @sucursal, 9002, '2026-04-28', 'factura', '001-001', '001-001-009002', p.idproveedores, p.razon_social, p.ruc, 0, 0, 0, 2836363.64, 283636.36, 3120000.00, 1, '2026-04-28 10:00:00' FROM proveedores p WHERE p.idproveedores = @proveedor1
UNION ALL
SELECT 9003, @sucursal, 9003, '2026-05-16', 'factura', '001-001', '001-001-009003', p.idproveedores, p.razon_social, p.ruc, 0, 0, 0, 890909.09, 89090.91, 980000.00, 1, '2026-05-16 11:20:00' FROM proveedores p WHERE p.idproveedores = @proveedor3
UNION ALL
SELECT 9004, @sucursal, 9004, '2026-06-03', 'factura', '001-001', '001-001-009004', p.idproveedores, p.razon_social, p.ruc, 0, 0, 0, 563636.36, 56363.64, 620000.00, 0, '2026-06-03 08:45:00' FROM proveedores p WHERE p.idproveedores = @proveedor2;

-- Transferencias.
INSERT INTO transferencia_stock (idtransferencia, sucursal_origen, sucursal_destino, fecha, estado, observacion, usuario_envia, usuario_recibe, idtransferencia_origen, fecha_actualizacion) VALUES
(9001, @sucursal, @sucursal_destino, '2026-02-25 14:00:00', 'en_transito', 'Reposicion preventiva de filtros', @usuario, NULL, NULL, NULL),
(9002, @sucursal, @sucursal_destino, '2026-03-12 09:20:00', 'recibido', 'Transferencia completa para mostrador', @usuario, @usuario, NULL, '2026-03-13 08:10:00'),
(9003, @sucursal, @sucursal_destino, '2026-04-05 15:40:00', 'recibido_parcial', 'Recepcion parcial por diferencia de conteo', @usuario, @usuario, NULL, '2026-04-06 10:00:00');

INSERT INTO transferencia_stock_detalle (idtransferencia, id_articulo, cantidad, cantidad_recibida) VALUES
(9001, @art1, 4.00, 0.00), (9001, @art2, 2.00, 0.00),
(9002, @art1, 6.00, 6.00), (9002, @art2, 5.00, 5.00),
(9003, @art1, 8.00, 5.00), (9003, @art2, 3.00, 3.00);

-- Movimientos de stock.
INSERT INTO movimientostock (MovStockId, id_sucursal, TipoMovStockId, MovStockArticuloId, MovStockCantidad, MovStockPrecioVenta, MovStockCosto, MovStockFechaHora, MovStockNroTicket, MovStockPOS, MovStockUsuario, MovStockSigno, MovStockReferencia) VALUES
(9001, @sucursal, 'RECEPCION COMPRA', @art1, 8.0000, 0.00, 220000.00, '2026-02-20 09:30:00', '001-001-009001', NULL, @usuario, 1, 'COMPRA #9001'),
(9002, @sucursal, 'RECEPCION COMPRA', @art2, 4.0000, 0.00, 145000.00, '2026-02-20 09:30:00', '001-001-009001', NULL, @usuario, 1, 'COMPRA #9001'),
(9003, @sucursal, 'TRANSFERENCIA ENVIO', @art1, 6.0000, 0.00, 0.00, '2026-03-12 09:20:00', NULL, NULL, @usuario, -1, 'TRANSF #9002'),
(9004, @sucursal, 'REG. SERVICIO', @art2, 2.0000, 165000.00, 0.00, '2026-05-22 17:10:00', NULL, NULL, @usuario, -1, 'REG_SERV #9002');

-- Servicios: recepcion, presupuesto, OT y registro.
INSERT INTO recepcion_servicio (idrecepcion, id_usuario, id_vehiculo, id_cliente, fecha_ingreso, fecha_salida, kilometraje, nivel_combustible, estado_exterior, objetos_vehiculo, tipo_servicio, area_problema, prioridad, accesorios, observacion, estado, fecha_creacion, fecha_actualizacion, id_sucursal, origen, idreclamo_servicio) VALUES
(9001, @usuario, @vehiculo, @cliente, '2026-02-03 08:15:00', NULL, '82000', '1/2', 'sin_danos', '', 'mantenimiento', 'motor', 'normal', 'llave,rueda_auxilio', 'Mantenimiento preventivo de 80.000 km', 1, '2026-02-03 08:15:00', NULL, @sucursal, 'NORMAL', NULL),
(9002, @usuario, @vehiculo, @cliente, '2026-03-10 10:40:00', '2026-03-12 17:00:00', '83500', '3/4', 'rayones_leves', '', 'reparacion', 'frenos', 'alta', 'llave', 'Ruido al frenar y vibracion', 3, '2026-03-10 10:40:00', '2026-03-12 17:00:00', @sucursal, 'NORMAL', NULL),
(9003, @usuario, @vehiculo, @cliente, '2026-04-14 09:30:00', NULL, '84850', '1/4', 'sin_danos', '', 'diagnostico', 'suspension', 'normal', 'llave', 'Revision por golpe en tren delantero', 2, '2026-04-14 09:30:00', '2026-04-14 11:00:00', @sucursal, 'NORMAL', NULL);

INSERT INTO presupuesto_servicio (idpresupuesto_servicio, id_diagnostico, id_usuario, id_sucursal, id_cliente, id_vehiculo, fecha, estado, fecha_venc, subtotal, total_descuento, total_final) VALUES
(9001, NULL, @usuario, @sucursal, @cliente, @vehiculo, '2026-02-04', 1, '2026-02-15', 580000.00, 0.00, 580000.00),
(9002, NULL, @usuario, @sucursal, @cliente, @vehiculo, '2026-03-11', 4, '2026-03-25', 760000.00, 50000.00, 710000.00),
(9003, NULL, @usuario, @sucursal, @cliente, @vehiculo, '2026-04-15', 0, '2026-04-30', 420000.00, 0.00, 420000.00);

INSERT INTO presupuesto_detalleservicio (id_detalle_presupuesto, idpresupuesto_servicio, id_articulo, id_diagnostico_detalle, cantidad, preciouni, subtotal) VALUES
(9001, 9001, @art1, NULL, 1.00, 220000.00, 220000.00),
(9002, 9001, @art2, NULL, 2.00, 180000.00, 360000.00),
(9003, 9002, @art1, NULL, 2.00, 220000.00, 440000.00),
(9004, 9002, @art2, NULL, 2.00, 160000.00, 320000.00),
(9005, 9003, @art2, NULL, 3.00, 140000.00, 420000.00);

INSERT INTO orden_trabajo (idorden_trabajo, idtrabajos, tecnico_responsable, idpresupuesto_servicio, id_usuario, id_cliente, id_vehiculo, id_sucursal, fecha_inicio, fecha_fin, estado, observacion, updated_at, updated_by, created_at, origen, idreclamo_servicio) VALUES
(9001, @equipo, @empleado, 9001, @usuario, @cliente, @vehiculo, @sucursal, '2026-02-05 08:00:00', NULL, 1, 'Pendiente de asignacion final de repuestos', '2026-02-05 08:00:00', @usuario, '2026-02-05 08:00:00', 'NORMAL', NULL),
(9002, @equipo, @empleado, 9002, @usuario, @cliente, @vehiculo, @sucursal, '2026-03-12 08:00:00', '2026-03-12 17:00:00', 2, 'Trabajo terminado y entregado', '2026-03-12 17:00:00', @usuario, '2026-03-12 08:00:00', 'NORMAL', NULL),
(9003, @equipo, @empleado, 9003, @usuario, @cliente, @vehiculo, @sucursal, '2026-04-16 13:30:00', NULL, 0, 'Orden anulada por presupuesto rechazado', '2026-04-16 15:00:00', @usuario, '2026-04-16 13:30:00', 'NORMAL', NULL);

INSERT INTO orden_trabajo_detalle (id_detalle_ot, cantidad, precio_unitario, subtotal, idorden_trabajo, id_articulo) VALUES
(9001, 1, 220000.00, 220000.00, 9001, @art1),
(9002, 2, 180000.00, 360000.00, 9001, @art2),
(9003, 2, 220000.00, 440000.00, 9002, @art1),
(9004, 2, 160000.00, 320000.00, 9002, @art2),
(9005, 3, 140000.00, 420000.00, 9003, @art2);

INSERT INTO registro_servicio (idregistro_servicio, idorden_trabajo, id_vehiculo, id_cliente, id_sucursal, fecha_servicio, fecha_registro, kilometraje_salida, usuario_registra, estado, observacion) VALUES
(9001, 9002, @vehiculo, @cliente, @sucursal, '2026-03-12', '2026-03-12 17:10:00', 83600, @usuario, 1, 'Servicio registrado y pendiente de facturacion'),
(9002, 9002, @vehiculo, @cliente, @sucursal, '2026-05-22', '2026-05-22 17:10:00', 85600, @usuario, 2, 'Servicio facturado correctamente'),
(9003, 9002, @vehiculo, @cliente, @sucursal, '2026-06-01', '2026-06-01 12:00:00', 86100, @usuario, 3, 'Cliente reporta reclamo por ruido residual');

INSERT INTO registro_servicio_detalle (id_registro_servicio_detalle, cantidad, precio_unitario, subtotal, origen, idregistro_servicio, id_articulo) VALUES
(9001, 2.00, 220000.00, 440000.00, 'OT', 9001, @art1),
(9002, 2.00, 160000.00, 320000.00, 'OT', 9001, @art2),
(9003, 1.00, 220000.00, 220000.00, 'OT', 9002, @art1),
(9004, 2.00, 165000.00, 330000.00, 'OT', 9002, @art2),
(9005, 1.00, 150000.00, 150000.00, 'OT', 9003, @art2);

COMMIT;

-- Luego de importar, probar:
-- 1) Informes de Movimientos -> Previsualizar por cada modulo.
-- 2) Filtros por fecha entre 2026-01-01 y 2026-06-30.
-- 3) CSV/PDF para validar acentos y totales.
