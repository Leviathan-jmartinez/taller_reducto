SET @db := DATABASE();

SET @sql := (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE diagnostico_detalle ADD COLUMN id_articulo_servicio INT UNSIGNED DEFAULT NULL AFTER id_diagnostico',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'diagnostico_detalle'
      AND COLUMN_NAME = 'id_articulo_servicio'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE diagnostico_detalle ADD COLUMN id_articulo_repuesto INT UNSIGNED DEFAULT NULL AFTER id_articulo_servicio',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'diagnostico_detalle'
      AND COLUMN_NAME = 'id_articulo_repuesto'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE diagnostico_detalle ADD COLUMN cantidad_repuesto DECIMAL(12,2) DEFAULT 1 AFTER id_articulo_repuesto',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'diagnostico_detalle'
      AND COLUMN_NAME = 'cantidad_repuesto'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(COUNT(*) = 0,
        "ALTER TABLE diagnostico_detalle ADD COLUMN repuesto_origen VARCHAR(20) DEFAULT 'TALLER' AFTER cantidad_repuesto",
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'diagnostico_detalle'
      AND COLUMN_NAME = 'repuesto_origen'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
