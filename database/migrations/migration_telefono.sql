-- ================================================================
-- MIGRACIÓN: Agregar columna 'telefono' a la tabla usuarios
-- Quesos San Gabriel — gabriel_lacteos
--
-- EJECUTAR UNA SOLA VEZ.
-- No reejecutar si la columna ya existe: MySQL 8 no soporta
-- ADD COLUMN IF NOT EXISTS y producirá error "Duplicate column name".
-- Si necesitas verificar, ejecuta: DESCRIBE usuarios;
-- ================================================================

USE `gabriel_lacteos`;

ALTER TABLE `usuarios`
  ADD COLUMN `telefono` VARCHAR(30) NULL AFTER `email`;
