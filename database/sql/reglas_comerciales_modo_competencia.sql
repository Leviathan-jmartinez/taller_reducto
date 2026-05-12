ALTER TABLE regla_comercial
    ADD COLUMN modo_competencia VARCHAR(30) NOT NULL DEFAULT 'COMPITE_MISMO_ALCANCE' AFTER prioridad;

UPDATE regla_comercial
SET modo_competencia = CASE
    WHEN politica_acumulacion = 'ACUMULABLE' THEN 'NO_COMPITE'
    WHEN politica_acumulacion = 'EXCLUSIVA' THEN 'EXCLUSIVA'
    WHEN politica_acumulacion = 'NO_ACUMULAR_MISMO_ALCANCE' THEN 'COMPITE_MISMO_ALCANCE'
    ELSE 'COMPITE_MISMO_ALCANCE'
END;

ALTER TABLE regla_comercial
    DROP COLUMN politica_acumulacion;
