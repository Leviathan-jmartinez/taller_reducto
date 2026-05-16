ALTER TABLE usuarios
    ADD COLUMN usu_intentos_fallidos INT UNSIGNED NOT NULL DEFAULT 0 AFTER usu_estado,
    ADD COLUMN usu_bloqueado TINYINT(1) NOT NULL DEFAULT 0 AFTER usu_intentos_fallidos;

