-- ================================================================
-- MIGRACIÓN: Agregar columnas para recuperación de contraseña (HU-30)
-- Quesos San Gabriel — gabriel_lacteos
--
-- EJECUTAR UNA SOLA VEZ.
-- No reejecutar si las columnas ya existen: MySQL 8 no soporta
-- ADD COLUMN IF NOT EXISTS y producirá error "Duplicate column name".
-- ================================================================

USE `gabriel_lacteos`;

ALTER TABLE `usuarios`
  ADD COLUMN `token_recuperacion` VARCHAR(64) NULL DEFAULT NULL AFTER `rol`,
  ADD COLUMN `token_expiracion` DATETIME NULL DEFAULT NULL AFTER `token_recuperacion`;
