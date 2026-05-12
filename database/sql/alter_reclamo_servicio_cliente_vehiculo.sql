ALTER TABLE reclamo_servicio
ADD COLUMN id_cliente INT UNSIGNED NULL AFTER id_sucursal,
ADD COLUMN id_vehiculo INT UNSIGNED NULL AFTER id_cliente;

ALTER TABLE reclamo_servicio
ADD INDEX idx_reclamo_cliente (id_cliente),
ADD INDEX idx_reclamo_vehiculo (id_vehiculo);

UPDATE reclamo_servicio rs
INNER JOIN registro_servicio rgs ON rgs.idregistro_servicio = rs.idregistro_servicio
INNER JOIN orden_trabajo ot ON ot.idorden_trabajo = rgs.idorden_trabajo
LEFT JOIN presupuesto_servicio ps ON ps.idpresupuesto_servicio = ot.idpresupuesto_servicio
LEFT JOIN diagnostico_servicio ds ON ds.id_diagnostico = ps.id_diagnostico
LEFT JOIN recepcion_servicio r_normal ON r_normal.idrecepcion = ds.idrecepcion
LEFT JOIN recepcion_servicio r_reclamo ON r_reclamo.idreclamo_servicio = ot.idreclamo_servicio
SET rs.id_cliente = COALESCE(r_normal.id_cliente, r_reclamo.id_cliente),
    rs.id_vehiculo = COALESCE(r_normal.id_vehiculo, r_reclamo.id_vehiculo)
WHERE rs.id_cliente IS NULL
   OR rs.id_vehiculo IS NULL;
