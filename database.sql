-- ==============================================================
-- BASE DE DATOS: Gabriel Lácteos - Sistema de Gestión de Inventario
-- ==============================================================

CREATE DATABASE IF NOT EXISTS `gabriel_lacteos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gabriel_lacteos`;

-- --------------------------------------------------------
-- 1. TABLA: usuarios
-- --------------------------------------------------------
DROP TABLE IF EXISTS `detalle_ventas`;
DROP TABLE IF EXISTS `ventas`;
DROP TABLE IF EXISTS `productos`;
DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `usuario` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NULL,
  `telefono` VARCHAR(30) NULL,
  `rol` ENUM('administrador', 'cliente') NOT NULL DEFAULT 'cliente',
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuarios del sistema:
-- 1. Administrador (acceso total a inventario, ventas, reportes y configuración)
-- Usuario: admin / Clave: admin123
INSERT INTO `usuarios` (`nombre`, `usuario`, `password`, `email`, `rol`) VALUES
('Administrador', 'admin', '$2y$10$s6eoVKwqTTqk21I5QnE1D.rx33tICZC2aoBR4PTfrZmBGDLly1iO2', 'admin@gabriellacteos.com', 'administrador');

-- 2. Cliente (solo puede consultar el catálogo de quesos y disponibilidad)
-- Usuario: cliente / Clave: cliente123
INSERT INTO `usuarios` (`nombre`, `usuario`, `password`, `email`, `rol`) VALUES
('Cliente General', 'cliente', '$2y$10$Y3Nd7O1QEu/.c53Y8yPm2e7TXgemQLPFzI0NDgtAk9j3AFzdhT8z6', 'cliente@gabriellacteos.com', 'cliente');

-- --------------------------------------------------------
-- 2. TABLA: productos
-- --------------------------------------------------------
CREATE TABLE `productos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(30) UNIQUE NOT NULL,
  `nombre` VARCHAR(120) NOT NULL,
  `tipo` VARCHAR(60) NOT NULL,
  `descripcion` TEXT NULL,
  `precio_compra` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `precio_venta` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cantidad` INT NOT NULL DEFAULT 0,
  `stock_minimo` INT NOT NULL DEFAULT 5,
  `unidad_medida` VARCHAR(30) NOT NULL DEFAULT 'Kilogramo',
  `estado` ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_tipo` (`tipo`),
  INDEX `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserción de productos lácteos iniciales de prueba
INSERT INTO `productos` (`codigo`, `nombre`, `tipo`, `descripcion`, `precio_compra`, `precio_venta`, `cantidad`, `stock_minimo`, `unidad_medida`, `estado`) VALUES
('LAC-001', 'Queso Campesino Tradicional', 'Queso Fresco', 'Queso campesino fresco y bajo en sal elaborado con leche pasteurizada.', 11000.00, 15000.00, 24, 8, 'Kilogramo', 'activo'),
('LAC-002', 'Queso Costeño Especial', 'Queso Duro / Salado', 'Queso costeño con el punto ideal de sal para arepas y frituras.', 12500.00, 17000.00, 15, 6, 'Kilogramo', 'activo'),
('LAC-003', 'Cuajada Fresca Campesina', 'Cuajada', 'Cuajada suave y fresca, ideal para acompañar con melao o dulce.', 8000.00, 11500.00, 4, 6, 'Kilogramo', 'activo'),
('LAC-004', 'Queso Mozzarella Tajado', 'Queso Hilado', 'Queso mozzarella con óptimo fundido para pizzas y sanduches.', 15000.00, 21000.00, 18, 5, 'Kilogramo', 'activo'),
('LAC-005', 'Queso Doble Crema Bloque', 'Queso Semigraso', 'Queso suave de hilado fino y textura cremosa.', 13000.00, 18500.00, 3, 5, 'Kilogramo', 'activo'),
('LAC-006', 'Quesillo de Hoja Tradicional', 'Queso Hilado', 'Quesillo envuelto en hoja de plátano con sabor artesanal único.', 4500.00, 7000.00, 0, 5, 'Unidad', 'activo'),
('LAC-007', 'Queso Siete Cueros Tradicional', 'Queso Hilado', 'Queso hilado en capas con textura suave y elástica típica llanera.', 7000.00, 10500.00, 12, 4, 'Libra (500g)', 'activo'),
('LAC-008', 'Queso Pera Artesanal', 'Queso Hilado', 'Queso fresco ácido hilado con forma de pera, textura semidura.', 6000.00, 9000.00, 2, 4, 'Bloque', 'activo'),
('LAC-009', 'Queso Ricotta / Requesón', 'Queso Fresco', 'Queso blanco granulado obtenido del suero lácteo, suave y ligero.', 5000.00, 8000.00, 20, 6, 'Kilogramo', 'activo'),
('LAC-010', 'Queso Provolone Ahumado', 'Queso Maduro', 'Queso madurado de pasta hilada con aroma y notas ahumadas naturales.', 5000.00, 8000.00, 10, 6, 'Kilogramo', 'activo');

-- --------------------------------------------------------
-- 3. TABLA: ventas
-- --------------------------------------------------------
CREATE TABLE `ventas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `numero_venta` VARCHAR(30) UNIQUE NOT NULL,
  `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cliente` VARCHAR(100) NULL DEFAULT 'Cliente Mostrador',
  `notas` TEXT NULL,
  `usuario_id` INT NULL,
  INDEX `idx_fecha` (`fecha`),
  CONSTRAINT `fk_ventas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. TABLA: detalle_ventas
-- --------------------------------------------------------
CREATE TABLE `detalle_ventas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `venta_id` INT NOT NULL,
  `producto_id` INT NOT NULL,
  `cantidad` INT NOT NULL,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  CONSTRAINT `fk_detalle_venta` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Ventas de prueba iniciales
-- --------------------------------------------------------
INSERT INTO `ventas` (`id`, `numero_venta`, `fecha`, `total`, `cliente`, `notas`, `usuario_id`) VALUES
(1, 'VNT-00001', DATE_SUB(NOW(), INTERVAL 2 DAY), 45000.00, 'Supermercado Central', 'Pago en efectivo', 1),
(2, 'VNT-00002', DATE_SUB(NOW(), INTERVAL 1 DAY), 35500.00, 'Panadería La Espiga', 'Entrega a domicilio', 1),
(3, 'VNT-00003', NOW(), 21000.00, 'Cliente Mostrador', 'Venta directa en mostrador', 1);

INSERT INTO `detalle_ventas` (`venta_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 2, 15000.00, 30000.00),
(1, 7, 1, 10500.00, 10500.00),
(1, 9, 1, 4500.00, 4500.00),
(2, 2, 1, 17000.00, 17000.00),
(2, 5, 1, 18500.00, 18500.00),
(3, 4, 1, 21000.00, 21000.00);

-- --------------------------------------------------------
-- 5. TABLA: proveedores
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre`    VARCHAR(120) NOT NULL,
  `contacto`  VARCHAR(100) NULL,
  `telefono`  VARCHAR(30)  NULL,
  `email`     VARCHAR(100) NULL,
  `direccion` VARCHAR(200) NULL,
  `nit`       VARCHAR(30)  NULL,
  `notas`     TEXT         NULL,
  `estado`    ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Proveedores de ejemplo
INSERT INTO `proveedores` (`nombre`, `contacto`, `telefono`, `email`, `direccion`, `nit`, `notas`, `estado`) VALUES
('Lácteos del Valle S.A.S', 'Carlos Martínez', '310 234 5678', 'ventas@lacteosvalle.com', 'Cra 12 #45-67, Bogotá', '900.123.456-7', 'Proveedor principal de leche fresca', 'activo'),
('Distribuidora Quesera Andina', 'María Torres', '315 876 5432', 'info@queseraandina.com', 'Cl 8 #23-11, Medellín', '800.987.654-3', 'Proveedor de cuajos y fermentos', 'activo'),
('Cooperativa Lechera Nariño', 'José Benavides', '311 456 7890', 'coop@lecheranarino.com', 'Km 5 Vía Pasto-Ipiales', '901.456.789-0', 'Leche pasteurizada a granel', 'activo');

-- --------------------------------------------------------
-- 6. TABLA: pedidos (compras realizadas por clientes)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id`           INT AUTO_INCREMENT PRIMARY KEY,
  `numero_pedido` VARCHAR(30) UNIQUE NOT NULL,
  `fecha`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cliente_id`   INT NULL,
  `notas`        TEXT NULL,
  `estado`       ENUM('pendiente','confirmado','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  INDEX `idx_fecha`   (`fecha`),
  INDEX `idx_estado`  (`estado`),
  CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. TABLA: detalle_pedidos
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `detalle_pedidos` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `pedido_id`      INT NOT NULL,
  `producto_id`    INT NOT NULL,
  `cantidad`       INT NOT NULL,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  `subtotal`       DECIMAL(10,2) NOT NULL,
  CONSTRAINT `fk_detalle_pedido`   FOREIGN KEY (`pedido_id`)   REFERENCES `pedidos`(`id`)   ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_ped_prod` FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
