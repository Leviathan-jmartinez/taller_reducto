CREATE TABLE IF NOT EXISTS reclamo_servicio_detalle (
    idreclamo_detalle INT UNSIGNED NOT NULL AUTO_INCREMENT,
    idreclamo_servicio INT UNSIGNED NOT NULL,
    id_registro_servicio_detalle INT NOT NULL,
    motivo TEXT NOT NULL,
    requiere_garantia TINYINT(1) DEFAULT 0,
    estado TINYINT(1) DEFAULT 1,
    PRIMARY KEY (idreclamo_detalle),
    KEY idx_reclamo_servicio (idreclamo_servicio),
    KEY idx_registro_servicio_detalle (id_registro_servicio_detalle),
    CONSTRAINT fk_reclamo_detalle_reclamo
        FOREIGN KEY (idreclamo_servicio)
        REFERENCES reclamo_servicio (idreclamo_servicio),
    CONSTRAINT fk_reclamo_detalle_registro_detalle
        FOREIGN KEY (id_registro_servicio_detalle)
        REFERENCES registro_servicio_detalle (id_registro_servicio_detalle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
