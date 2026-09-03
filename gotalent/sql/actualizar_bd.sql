-- Go Talent: script para correr UNA VEZ en phpMyAdmin sobre la base
-- conakwwi_mantenimiento, además de los dumps participantes.sql y
-- jurado.sql que ya tenés.

-- Tabla donde queda registrado el voto de cada jurado a cada participante.
-- La clave única (id_jurado, id_participante) es lo que impide que un
-- mismo jurado vote dos veces al mismo participante.
CREATE TABLE IF NOT EXISTS `votos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_jurado` int(11) NOT NULL,
  `id_participante` int(11) NOT NULL,
  `puntaje` tinyint(4) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_voto_por_jurado` (`id_jurado`, `id_participante`),
  KEY `id_participante` (`id_participante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
