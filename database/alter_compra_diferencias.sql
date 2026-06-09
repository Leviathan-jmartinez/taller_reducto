SET NAMES utf8mb4;

ALTER TABLE compra_detalle
    ADD COLUMN cantidad_facturada DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER precio_unitario;

UPDATE compra_detalle
SET cantidad_facturada = cantidad_recibida
WHERE cantidad_facturada = 0;

-- Estados de compra_cabecera usados por el sistema:
-- 0 = Anulada
-- 1 = Activa / registrada sin diferencia
-- 2 = Procesada
-- 3 = Con diferencia entre cantidad facturada y cantidad recibida
-- 4 = Regularizada por nota de credito
