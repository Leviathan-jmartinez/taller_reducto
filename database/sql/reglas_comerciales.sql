CREATE TABLE IF NOT EXISTS regla_comercial (
    id_regla INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    id_sucursal INT UNSIGNED NULL,
    prioridad INT NOT NULL DEFAULT 0,
    modo_competencia VARCHAR(30) NOT NULL DEFAULT 'COMPITE_MISMO_ALCANCE',
    estado TINYINT(1) NOT NULL DEFAULT 1,
    id_usuario_crea INT UNSIGNED NOT NULL,
    id_usuario_modifica INT UNSIGNED NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME NULL,
    PRIMARY KEY (id_regla),
    KEY idx_regla_comercial_filtros (estado, fecha_inicio, fecha_fin, id_sucursal),
    KEY fk_regla_comercial_sucursal_idx (id_sucursal),
    KEY fk_regla_comercial_usuario_crea_idx (id_usuario_crea),
    KEY fk_regla_comercial_usuario_modifica_idx (id_usuario_modifica),
    CONSTRAINT fk_regla_comercial_sucursal
        FOREIGN KEY (id_sucursal) REFERENCES sucursales(id_sucursal),
    CONSTRAINT fk_regla_comercial_usuario_crea
        FOREIGN KEY (id_usuario_crea) REFERENCES usuarios(id_usuario),
    CONSTRAINT fk_regla_comercial_usuario_modifica
        FOREIGN KEY (id_usuario_modifica) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS regla_comercial_condicion (
    id_condicion INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_regla INT UNSIGNED NOT NULL,
    tipo_condicion VARCHAR(30) NOT NULL,
    operador VARCHAR(20) NOT NULL DEFAULT '=',
    valor_ref INT UNSIGNED NULL,
    valor_texto VARCHAR(120) NULL,
    PRIMARY KEY (id_condicion),
    KEY idx_regla_condicion_regla (id_regla),
    KEY idx_regla_condicion_tipo (tipo_condicion, valor_ref),
    CONSTRAINT fk_regla_condicion_regla
        FOREIGN KEY (id_regla) REFERENCES regla_comercial(id_regla)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS regla_comercial_descuento (
    id_regla_descuento INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_regla INT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(20) NOT NULL,
    valor DECIMAL(12,2) NOT NULL,
    cantidad_requerida DECIMAL(12,2) NULL,
    cantidad_cobrada DECIMAL(12,2) NULL,
    aplica_a VARCHAR(20) NOT NULL DEFAULT 'TOTAL',
    alcance_tipo VARCHAR(30) NULL,
    alcance_ref INT UNSIGNED NULL,
    PRIMARY KEY (id_regla_descuento),
    KEY idx_regla_descuento_regla (id_regla),
    CONSTRAINT fk_regla_descuento_regla
        FOREIGN KEY (id_regla) REFERENCES regla_comercial(id_regla)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO permisos (clave, descripcion)
SELECT 'servicio.regla_comercial.ver', 'Ver reglas comerciales'
WHERE NOT EXISTS (SELECT 1 FROM permisos WHERE clave = 'servicio.regla_comercial.ver');

INSERT INTO permisos (clave, descripcion)
SELECT 'servicio.regla_comercial.crear', 'Crear reglas comerciales'
WHERE NOT EXISTS (SELECT 1 FROM permisos WHERE clave = 'servicio.regla_comercial.crear');

INSERT INTO permisos (clave, descripcion)
SELECT 'servicio.regla_comercial.editar', 'Editar reglas comerciales'
WHERE NOT EXISTS (SELECT 1 FROM permisos WHERE clave = 'servicio.regla_comercial.editar');
