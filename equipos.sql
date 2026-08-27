-- Ejecutar este script en tu base de datos MySQL (por ejemplo desde phpMyAdmin,
-- pestaña "SQL") para crear la tabla que va a guardar los equipos.

CREATE TABLE IF NOT EXISTS equipos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  tipo VARCHAR(50) NOT NULL,
  ubicacion VARCHAR(150),
  estado ENUM('ok', 'pendiente') NOT NULL DEFAULT 'ok',
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
