-- ================================================================
-- MIGRACIÓN: Agregar columna 'telefono' a la tabla usuarios
-- Quesos San Gabriel — gabriel_lacteos
-- Ejecuta este script UNA SOLA VEZ si ya tienes la base de datos
-- creada con el database.sql original.
-- ================================================================

USE `gabriel_lacteos`;

-- Agrega la columna solo si no existe ya
ALTER TABLE `usuarios`
  ADD COLUMN IF NOT EXISTS `telefono` VARCHAR(30) NULL AFTER `email`;
