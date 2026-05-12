UPDATE regla_comercial_condicion
SET tipo_condicion = 'TOTAL_OPERACION'
WHERE tipo_condicion = 'TOTAL_MINIMO';

UPDATE regla_comercial_condicion
SET tipo_condicion = 'CANTIDAD_ITEMS'
WHERE tipo_condicion = 'CANTIDAD_MINIMA';
