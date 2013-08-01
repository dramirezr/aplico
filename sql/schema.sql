ALTER TABLE `solicitud` MODIFY COLUMN `estado` ENUM('P','A','C','E')  CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'P' COMMENT 'P: Pendiente;A=Aceptado;C=Cancelada;E=Entregado';
ALTER TABLE `agente` MODIFY COLUMN `estado` ENUM('A', 'P', 'C')  CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'P';
ALTER TABLE `agente` ADD COLUMN `clave` CHAR(100)  NOT NULL AFTER `codigo`;
ALTER TABLE `agente` ADD COLUMN `estado_servicio` ENUM('LIBRE', 'OCUPADO')  NOT NULL DEFAULT 'LIBRE' AFTER `estado`;