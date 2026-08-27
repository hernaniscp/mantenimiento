-- Ejecutar este script en tu base de datos MySQL (por ejemplo desde phpMyAdmin,
-- pestaña "SQL") para crear la tabla que va a guardar los mantenimientos.
-- Requiere que la tabla "equipos" ya exista (equipos.sql).

CREATE TABLE IF NOT EXISTS mantenimientos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  equipo_id INT NOT NULL,
  tipo_trabajo VARCHAR(50) NOT NULL,
  descripcion TEXT,
  estado ENUM('pendiente', 'realizado') NOT NULL DEFAULT 'pendiente',
  fecha DATE NOT NULL,
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
