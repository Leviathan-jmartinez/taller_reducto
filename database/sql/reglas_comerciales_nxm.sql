ALTER TABLE regla_comercial_descuento
    ADD COLUMN cantidad_requerida DECIMAL(12,2) NULL AFTER valor,
    ADD COLUMN cantidad_cobrada DECIMAL(12,2) NULL AFTER cantidad_requerida;
