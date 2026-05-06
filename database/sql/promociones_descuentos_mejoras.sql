ALTER TABLE descuentos
    MODIFY id_usuario_modifica INT UNSIGNED NULL,
    MODIFY fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE descuentos
    ADD COLUMN aplica_a VARCHAR(20) NOT NULL DEFAULT 'TOTAL' AFTER valor,
    ADD COLUMN fecha_inicio DATE NULL AFTER aplica_a,
    ADD COLUMN fecha_fin DATE NULL AFTER fecha_inicio,
    ADD COLUMN id_sucursal INT UNSIGNED NULL AFTER id_usuario_crea;

ALTER TABLE promociones
    ADD COLUMN id_sucursal INT UNSIGNED NULL AFTER fecha_fin;

ALTER TABLE descuentos
    ADD CONSTRAINT fk_descuentos_sucursal
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id_sucursal);

ALTER TABLE promociones
    ADD CONSTRAINT fk_promociones_sucursal
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id_sucursal);

CREATE INDEX idx_descuentos_filtros
    ON descuentos (estado, fecha_inicio, fecha_fin, id_sucursal);

CREATE INDEX idx_promociones_filtros
    ON promociones (estado, fecha_inicio, fecha_fin, id_sucursal);
