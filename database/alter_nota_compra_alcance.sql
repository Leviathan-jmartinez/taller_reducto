SET NAMES utf8mb4;

ALTER TABLE nota_compra
    ADD COLUMN alcance ENUM('regularizar_diferencia','anulacion_total')
    NOT NULL DEFAULT 'regularizar_diferencia'
    AFTER movimiento_stock;

UPDATE nota_compra
SET alcance = 'anulacion_total'
WHERE tipo = 'credito'
  AND descripcion LIKE '[anulacion_total]%';
