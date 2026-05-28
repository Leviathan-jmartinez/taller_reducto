ALTER TABLE presupuesto_servicio
  ADD COLUMN origen ENUM('PRELIMINAR','DIAGNOSTICO') NOT NULL DEFAULT 'DIAGNOSTICO' AFTER id_diagnostico,
  ADD COLUMN convertido_desde INT UNSIGNED NULL AFTER origen,
  ADD KEY fk_presupuesto_servicio_convertido_desde_idx (convertido_desde),
  ADD CONSTRAINT fk_presupuesto_servicio_convertido_desde
    FOREIGN KEY (convertido_desde)
    REFERENCES presupuesto_servicio (idpresupuesto_servicio)
    ON DELETE SET NULL;
