-- ==============================================================
-- MIGRACIÓN: Sistema de Categorización Avanzada para Quesos
-- Fecha: 2024
-- Descripción: Agrega campos de categorización avanzada para
--              mejorar la experiencia de compra en el e-commerce
-- ==============================================================

USE `gabriel_lacteos`;

-- --------------------------------------------------------
-- 1. Agregar nuevos campos de categorización
-- --------------------------------------------------------

ALTER TABLE `productos`
    ADD COLUMN `categoria` VARCHAR(60) NULL DEFAULT NULL COMMENT 'Categoría principal del queso' AFTER `tipo`,
    ADD COLUMN `tipo_leche` VARCHAR(60) NULL DEFAULT NULL COMMENT 'Tipo de leche: Vaca, Cabra, Oveja, Mezcla' AFTER `categoria`,
    ADD COLUMN `intensidad_sabor` ENUM('suave', 'medio', 'fuerte', 'muy fuerte') NULL DEFAULT 'medio' COMMENT 'Intensidad del sabor' AFTER `tipo_leche`,
    ADD COLUMN `tiempo_maduracion` VARCHAR(60) NULL DEFAULT NULL COMMENT 'Tiempo de maduración del queso' AFTER `intensidad_sabor`,
    ADD COLUMN `peso_presentacion` VARCHAR(60) NULL DEFAULT NULL COMMENT 'Peso o presentación específica' AFTER `tiempo_maduracion`,
    ADD COLUMN `origen` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Región o lugar de origen' AFTER `peso_presentacion`,
    ADD COLUMN `imagen_url` VARCHAR(255) NULL DEFAULT NULL COMMENT 'URL de imagen del producto' AFTER `origen`;

-- --------------------------------------------------------
-- 2. Crear índices para mejorar búsquedas y filtros
-- --------------------------------------------------------

ALTER TABLE `productos`
    ADD INDEX `idx_categoria` (`categoria`),
    ADD INDEX `idx_tipo_leche` (`tipo_leche`),
    ADD INDEX `idx_intensidad_sabor` (`intensidad_sabor`);

-- --------------------------------------------------------
-- 3. Actualizar productos existentes con las nuevas categorías
-- --------------------------------------------------------

UPDATE `productos` SET 
    categoria = 'Quesos Frescos',
    tipo_leche = 'Vaca',
    intensidad_sabor = 'suave',
    tiempo_maduracion = 'Sin maduración',
    peso_presentacion = '1 kg',
    origen = 'Región Andina'
WHERE codigo = 'LAC-001';

UPDATE `productos` SET 
    categoria = 'Quesos Madurados',
    tipo_leche = 'Vaca',
    intensidad_sabor = 'fuerte',
    tiempo_maduracion = '2-3 meses',
    peso_presentacion = '1 kg',
    origen = 'Costa Atlántica'
WHERE codigo = 'LAC-002';

UPDATE `productos` SET 
    categoria = 'Quesos Frescos',
    tipo_leche = 'Vaca',
    intensidad_sabor = 'suave',
    tiempo_maduracion = 'Sin maduración',
    peso_presentacion = '1 kg',
    origen = 'Región Andina'
WHERE codigo = 'LAC-003';

UPDATE `productos` SET 
    categoria = 'Quesos Cremosos',
    tipo_leche = 'Vaca',
    intensidad_sabor = 'medio',
    tiempo_maduracion = '1-2 semanas',
    peso_presentacion = '1 kg',
    origen = 'Valle del Cauca'
WHERE codigo = 'LAC-004';

UPDATE `productos` SET 
    categoria = 'Quesos Cremosos',
    tipo_leche = 'Vaca',
    intensidad_sabor = 'suave',
    tiempo_maduracion = '1-2 semanas',
    peso_presentacion = '1 kg',
    origen = 'Región Andina'
WHERE codigo = 'LAC-005';

UPDATE `productos` SET 
    categoria = 'Quesos Artesanales',
    tipo_leche = 'Vaca',
    intensidad_sabor = 'medio',
    tiempo_maduracion = '1 semana',
    peso_presentacion = '500 g',
    origen = 'Región Tolimense'
WHERE codigo = 'LAC-006';

UPDATE `productos` SET 
    categoria = 'Quesos Artesanales',
    tipo_leche = 'Vaca',
    intensidad_sabor = 'medio',
    tiempo_maduracion = '1-2 semanas',
    peso_presentacion = '500 g',
    origen = 'Llanos Orientales'
WHERE codigo = 'LAC-007';

UPDATE `productos` SET 
    categoria = 'Quesos Artesanales',
    tipo_leche = 'Vaca',
    intensidad_sabor = 'medio',
    tiempo_maduracion = '1 semana',
    peso_presentacion = '400-500 g',
    origen = 'Región Andina'
WHERE codigo = 'LAC-008';

UPDATE `productos` SET 
    categoria = 'Quesos Frescos',
    tipo_leche = 'Vaca',
    intensidad_sabor = 'suave',
    tiempo_maduracion = 'Sin maduración',
    peso_presentacion = '1 kg',
    origen = 'Región Andina'
WHERE codigo = 'LAC-009';

UPDATE `productos` SET 
    categoria = 'Quesos Madurados',
    tipo_leche = 'Vaca',
    intensidad_sabor = 'fuerte',
    tiempo_maduracion = '3-6 meses',
    peso_presentacion = '1 kg',
    origen = 'Italia / Nacional'
WHERE codigo = 'LAC-010';

-- Mensaje de confirmación
SELECT 'Migración de categorización completada exitosamente' AS resultado;
