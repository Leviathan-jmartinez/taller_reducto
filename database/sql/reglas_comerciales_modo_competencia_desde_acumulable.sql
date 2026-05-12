ALTER TABLE regla_comercial
    ADD COLUMN modo_competencia VARCHAR(30) NOT NULL DEFAULT 'COMPITE_MISMO_ALCANCE' AFTER prioridad;

UPDATE regla_comercial
SET modo_competencia = CASE
    WHEN acumulable = 1 THEN 'NO_COMPITE'
    ELSE 'COMPITE_MISMO_ALCANCE'
END;

ALTER TABLE regla_comercial
    DROP COLUMN acumulable;
