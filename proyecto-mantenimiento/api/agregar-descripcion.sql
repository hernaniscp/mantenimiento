-- Ejecutar en tu base de datos (HeidiSQL o phpMyAdmin) para agregar el campo
-- "descripción" a la tabla equipos que ya existe. Solo hace falta correrlo UNA vez.

ALTER TABLE equipos
  ADD COLUMN descripcion TEXT NULL AFTER nombre;
