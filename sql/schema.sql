ALTER TABLE `solicitud` MODIFY COLUMN `estado` ENUM('P','A','C','E')  CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'P' COMMENT 'P: Pendiente;A=Aceptado;C=Cancelada;E=Entregado';
ALTER TABLE `agente` MODIFY COLUMN `estado` ENUM('A', 'P', 'C')  CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'P';
ALTER TABLE `agente` ADD COLUMN `clave` CHAR(100)  NOT NULL AFTER `codigo`;
ALTER TABLE `agente` ADD COLUMN `estado_servicio` ENUM('LIBRE', 'OCUPADO')  NOT NULL DEFAULT 'LIBRE' AFTER `estado`;


CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` char(250) DEFAULT NULL,
  `codigo` char(20) DEFAULT NULL,
  `estado` enum('ADMIN','CALL') NOT NULL DEFAULT 'ADMIN',
  `clave` char(100) NOT NULL,
  `telefono` char(100) DEFAULT NULL,
  `direccion` char(250) DEFAULT NULL,
  `ciudad` char(100) DEFAULT NULL,
  `departamento` char(100) DEFAULT NULL,
  `pais` char(100) DEFAULT NULL,
  `latitud` float NOT NULL DEFAULT '0',
  `longitud` float NOT NULL DEFAULT '0',
  `fecha_localizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;