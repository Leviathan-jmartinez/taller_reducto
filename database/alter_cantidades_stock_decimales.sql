ALTER TABLE pedido_detalle
    MODIFY cantidad DECIMAL(12,4) DEFAULT NULL,
    MODIFY stock_actual DECIMAL(12,4) DEFAULT NULL;

ALTER TABLE orden_compra_detalle
    MODIFY cantidad DECIMAL(12,4) DEFAULT NULL,
    MODIFY cantidad_pendiente DECIMAL(12,4) DEFAULT NULL;

ALTER TABLE presupuesto_detalle
    MODIFY cantidad DECIMAL(12,4) DEFAULT NULL;

ALTER TABLE diagnostico_detalle
    MODIFY cantidad_repuesto DECIMAL(12,4) DEFAULT '1.0000';

ALTER TABLE presupuesto_detalleservicio
    MODIFY cantidad DECIMAL(12,4) NOT NULL;

ALTER TABLE orden_trabajo_detalle
    MODIFY cantidad DECIMAL(12,4) NOT NULL;

ALTER TABLE registro_servicio_detalle
    MODIFY cantidad DECIMAL(12,4) NOT NULL DEFAULT '0.0000';

ALTER TABLE compra_detalle
    MODIFY cantidad_facturada DECIMAL(12,4) NOT NULL DEFAULT 0,
    MODIFY cantidad_recibida DECIMAL(12,4) NOT NULL;

ALTER TABLE salida_insumo_detalle
    MODIFY cantidad DECIMAL(12,4) NOT NULL;

ALTER TABLE transferencia_stock_detalle
    MODIFY cantidad DECIMAL(12,4) DEFAULT NULL,
    MODIFY cantidad_recibida DECIMAL(12,4) DEFAULT NULL;
