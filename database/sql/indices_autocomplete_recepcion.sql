-- Indices recomendados para el autocomplete de recepcion de servicio.
-- Ejecutar una sola vez sobre la base actual, revisando primero si ya existen.

CREATE INDEX idx_clientes_doc_estado
    ON clientes (doc_number, estado_cliente);

CREATE INDEX idx_clientes_nombre_apellido_estado
    ON clientes (nombre_cliente, apellido_cliente, estado_cliente);

CREATE INDEX idx_clientes_apellido_nombre_estado
    ON clientes (apellido_cliente, nombre_cliente, estado_cliente);

CREATE INDEX idx_vehiculos_cliente_placa_estado
    ON vehiculos (id_cliente, placa, estado);

CREATE INDEX idx_modelo_auto_descri_estado
    ON modelo_auto (mod_descri, estado);
