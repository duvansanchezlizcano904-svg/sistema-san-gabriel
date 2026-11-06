-- ============================================================
-- MIGRACIÓN: Agregar columna `imagen` a la tabla `productos`
-- Quesos San Gabriel — Sistema de Inventario
-- Fecha: 2026-10-18
--
-- EJECUTAR UNA SOLA VEZ.
-- No reejecutar si la columna ya existe: MySQL 8 no soporta
-- ADD COLUMN IF NOT EXISTS ni CREATE INDEX IF NOT EXISTS.
-- Si necesitas verificar, ejecuta: DESCRIBE productos;
--
-- INSTRUCCIONES DE EJECUCIÓN:
--   1. Abre phpMyAdmin o tu cliente MySQL favorito.
--   2. Selecciona la base de datos del proyecto.
--   3. Ejecuta este archivo completo (Import → seleccionar archivo).
--   ¡No hay rollback automático! Crea un respaldo antes.
-- ============================================================

-- Agrega la columna `imagen` después de `descripcion`.
-- VARCHAR(255) es suficiente para rutas relativas tipo
--   "public/img/productos/QC-001.jpg"
-- NULL por defecto: productos sin imagen usan el placeholder local.

ALTER TABLE `productos`
    ADD COLUMN `imagen` VARCHAR(255) NULL DEFAULT NULL
    AFTER `descripcion`;

-- Índice para acelerar consultas que filtren/busquen por imagen
-- (útil si se desea listar productos sin imagen desde el panel admin)
CREATE INDEX `idx_productos_imagen`
    ON `productos` (`imagen`);

-- Verificación rápida: muestra la nueva estructura
-- DESCRIBE `productos`;
