CREATE TABLE `odalyscs_edgar`.`pre_subastas` ( `id` INT NOT NULL COMMENT 'Clave primaria' , `usuario_id` INT NULL COMMENT 'Usuario que realiza la acción.' , `puja_maxima` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Ìndica si hará una puja maxima en la presubasta.' , `puja_telefonica` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Indica si hará una puja telefónica' , `asistir_subasta` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Indica si asitirar a una subasta en vivo.' , `imagen_s_id` INT NULL COMMENT 'Clave foránea a la imágen pertenenciente al usuario actual' , `no_hacer_nada` BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Indica si no hará nada el usuario.' ) ENGINE = InnoDB COMMENT = 'Control de la presubasta.';

ALTER TABLE `pre_subastas` ADD `subasta_id` INT NOT NULL COMMENT 'Clave foránea a la subasta la cuál pertenece este registro.';

ALTER TABLE `pre_subastas` ADD `monto` FLOAT NULL DEFAULT NULL COMMENT 'Monto para el caso que sea por puja maxima la selección para la presubasta.' ;

ALTER TABLE `pre_subastas` ADD PRIMARY KEY(`id`);

ALTER TABLE `pre_subastas` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Clave primaria';

ALTER TABLE `pre_subastas` ADD UNIQUE( `usuario_id`, `imagen_s_id`, `subasta_id`);