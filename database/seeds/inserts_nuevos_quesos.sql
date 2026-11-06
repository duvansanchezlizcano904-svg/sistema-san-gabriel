-- ==============================================================
-- AMPLIACIÓN DE CATÁLOGO: Nuevas Variedades de Quesos Artesanales
-- Fecha: 2024
-- Descripción: Inserta nuevas variedades organizadas por categorías
--              Quesos Frescos, Madurados, Cremosos, Azules, de Cabra y Artesanales
-- ==============================================================

USE `gabriel_lacteos`;

-- --------------------------------------------------------
-- CATEGORÍA: Quesos Frescos
-- --------------------------------------------------------

INSERT IGNORE INTO `productos` (`codigo`, `nombre`, `tipo`, `categoria`, `descripcion`, `precio_compra`, `precio_venta`, `cantidad`, `stock_minimo`, `unidad_medida`, `tipo_leche`, `intensidad_sabor`, `tiempo_maduracion`, `peso_presentacion`, `origen`, `estado`) VALUES
('QF-001', 'Queso Fresco Paipa', 'Queso Fresco', 'Quesos Frescos', 'Queso blanco tradicional de Paipa, ideal para arepas y desayunos.', 10000.00, 14500.00, 30, 8, 'Kilogramo', 'Vaca', 'suave', 'Sin maduración', '1 kg', 'Boyacá', 'activo'),
('QF-002', 'Queso Blanco Pasteurizado', 'Queso Fresco', 'Quesos Frescos', 'Queso blanco bajo en sal, perfecto para dietas saludables.', 9500.00, 13000.00, 25, 8, 'Kilogramo', 'Vaca', 'suave', 'Sin maduración', '1 kg', 'Cundinamarca', 'activo'),
('QF-003', 'Queso Panela Artesanal', 'Queso Fresco', 'Quesos Frescos', 'Queso suave y fresco estilo panela, ideal para ensaladas.', 8500.00, 12000.00, 20, 6, 'Kilogramo', 'Vaca', 'suave', 'Sin maduración', '1 kg', 'Antioquia', 'activo'),
('QF-004', 'Queso Cottage Cremoso', 'Queso Fresco', 'Quesos Frescos', 'Queso cottage con textura granulada, alto en proteína.', 11000.00, 16000.00, 15, 5, 'Kilogramo', 'Vaca', 'suave', 'Sin maduración', '500 g', 'Valle del Cauca', 'activo'),

-- --------------------------------------------------------
-- CATEGORÍA: Quesos Madurados
-- --------------------------------------------------------

('QM-001', 'Queso Parmesano Artesanal', 'Queso Maduro', 'Quesos Madurados', 'Queso duro madurado con cristales de sal, ideal para rallar.', 18000.00, 26000.00, 12, 4, 'Kilogramo', 'Vaca', 'muy fuerte', '12-24 meses', '500 g', 'Región Andina', 'activo'),
('QM-002', 'Queso Manchego Estilo', 'Queso Maduro', 'Quesos Madurados', 'Queso semicurado con textura firme y sabor intenso.', 16000.00, 23000.00, 10, 4, 'Kilogramo', 'Oveja', 'fuerte', '3-6 meses', '1 kg', 'Nacional', 'activo'),
('QM-003', 'Queso Cheddar Añejado', 'Queso Maduro', 'Quesos Madurados', 'Queso cheddar con maduración media, color naranja natural.', 14000.00, 20000.00, 18, 5, 'Kilogramo', 'Vaca', 'medio', '3-6 meses', '1 kg', 'Valle del Cauca', 'activo'),
('QM-004', 'Queso Gouda Ahumado', 'Queso Maduro', 'Quesos Madurados', 'Queso holandés ahumado naturalmente con madera de haya.', 15500.00, 22000.00, 14, 5, 'Kilogramo', 'Vaca', 'medio', '2-4 meses', '1 kg', 'Cundinamarca', 'activo'),

-- --------------------------------------------------------
-- CATEGORÍA: Quesos Cremosos
-- --------------------------------------------------------

('QC-001', 'Queso Crema Natural', 'Queso Cremoso', 'Quesos Cremosos', 'Queso crema suave sin aditivos, perfecto para untar.', 12000.00, 17500.00, 22, 6, 'Kilogramo', 'Vaca', 'suave', 'Sin maduración', '500 g', 'Bogotá', 'activo'),
('QC-002', 'Queso Brie Francés', 'Queso Cremoso', 'Quesos Cremosos', 'Queso de corteza blanca con interior cremoso y suave.', 20000.00, 28000.00, 8, 3, 'Kilogramo', 'Vaca', 'medio', '4-6 semanas', '250 g', 'Importado / Nacional', 'activo'),
('QC-003', 'Queso Camembert Artesanal', 'Queso Cremoso', 'Quesos Cremosos', 'Queso francés cremoso con notas a champiñón.', 19000.00, 27000.00, 6, 3, 'Kilogramo', 'Vaca', 'medio', '3-5 semanas', '250 g', 'Nacional', 'activo'),
('QC-004', 'Queso Mascarpone Premium', 'Queso Cremoso', 'Quesos Cremosos', 'Queso italiano extra cremoso, ideal para postres y tiramisú.', 16000.00, 24000.00, 10, 4, 'Kilogramo', 'Vaca', 'suave', 'Sin maduración', '500 g', 'Valle del Cauca', 'activo'),

-- --------------------------------------------------------
-- CATEGORÍA: Quesos Azules
-- --------------------------------------------------------

('QA-001', 'Queso Azul Roquefort Estilo', 'Queso Azul', 'Quesos Azules', 'Queso azul intenso con vetas de penicilium, sabor potente.', 22000.00, 32000.00, 5, 2, 'Kilogramo', 'Oveja', 'muy fuerte', '3-5 meses', '300 g', 'Nacional', 'activo'),
('QA-002', 'Queso Gorgonzola Dulce', 'Queso Azul', 'Quesos Azules', 'Queso italiano azul con textura cremosa y sabor suave.', 20000.00, 29000.00, 7, 3, 'Kilogramo', 'Vaca', 'fuerte', '2-3 meses', '500 g', 'Nacional', 'activo'),
('QA-003', 'Queso Azul Danés', 'Queso Azul', 'Quesos Azules', 'Queso azul cremoso estilo danés, menos intenso que el roquefort.', 18000.00, 26500.00, 8, 3, 'Kilogramo', 'Vaca', 'medio', '2-4 meses', '500 g', 'Nacional', 'activo'),

-- --------------------------------------------------------
-- CATEGORÍA: Quesos de Cabra
-- --------------------------------------------------------

('QG-001', 'Queso de Cabra Fresco', 'Queso de Cabra', 'Quesos de Cabra', 'Queso fresco de cabra con textura suave y sabor delicado.', 14000.00, 21000.00, 12, 4, 'Kilogramo', 'Cabra', 'medio', 'Sin maduración', '500 g', 'Santander', 'activo'),
('QG-002', 'Queso de Cabra Madurado', 'Queso de Cabra', 'Quesos de Cabra', 'Queso de cabra semicurado con corteza natural.', 17000.00, 25000.00, 8, 3, 'Kilogramo', 'Cabra', 'fuerte', '2-4 meses', '500 g', 'Boyacá', 'activo'),
('QG-003', 'Chèvre en Aceite con Hierbas', 'Queso de Cabra', 'Quesos de Cabra', 'Bolitas de queso de cabra marinadas en aceite de oliva y hierbas.', 16000.00, 24000.00, 10, 4, 'Frasco 400g', 'Cabra', 'medio', '1 semana', '400 g', 'Cundinamarca', 'activo'),
('QG-004', 'Queso de Cabra con Ceniza', 'Queso de Cabra', 'Quesos de Cabra', 'Queso fresco de cabra cubierto con ceniza vegetal comestible.', 15000.00, 22500.00, 9, 3, 'Unidad 250g', 'Cabra', 'medio', '1-2 semanas', '250 g', 'Valle del Cauca', 'activo'),

-- --------------------------------------------------------
-- CATEGORÍA: Quesos Artesanales Especiales
-- --------------------------------------------------------

('QAR-001', 'Queso Cuatro Quesos Mix', 'Queso Mezcla', 'Quesos Artesanales', 'Mezcla de mozzarella, parmesano, provolone y queso azul.', 17000.00, 24500.00, 15, 5, 'Kilogramo', 'Vaca', 'fuerte', 'Mezcla', '500 g', 'Nacional', 'activo'),
('QAR-002', 'Queso Ahumado Artesanal', 'Queso Ahumado', 'Quesos Artesanales', 'Queso semicurado ahumado con leña de roble.', 14000.00, 20500.00, 11, 4, 'Kilogramo', 'Vaca', 'medio', '2-3 meses', '1 kg', 'Región Andina', 'activo'),
('QAR-003', 'Queso Trenzado Llanero', 'Queso Hilado', 'Quesos Artesanales', 'Queso fresco hilado trenzado manualmente, típico llanero.', 8500.00, 13000.00, 20, 6, 'Trenza 400g', 'Vaca', 'medio', '1 semana', '400 g', 'Llanos Orientales', 'activo'),
('QAR-004', 'Queso con Trufa Negra', 'Queso Premium', 'Quesos Artesanales', 'Queso semicurado con láminas de trufa negra, producto gourmet.', 28000.00, 42000.00, 4, 2, 'Kilogramo', 'Vaca', 'muy fuerte', '3-6 meses', '300 g', 'Nacional Premium', 'activo'),
('QAR-005', 'Queso Burrata Fresca', 'Queso Italiano', 'Quesos Cremosos', 'Queso italiano con corazón cremoso de stracciatella.', 18000.00, 27000.00, 8, 3, 'Unidad 250g', 'Vaca', 'suave', 'Sin maduración', '250 g', 'Valle del Cauca', 'activo'),

-- --------------------------------------------------------
-- AMPLIACIÓN ADICIONAL DE CATÁLOGO (18 Nuevas Variedades)
-- --------------------------------------------------------

-- Quesos Artesanales
('QAR-006', 'Queso Paipa D.O. Reserva Especial', 'Queso Semimaduro', 'Quesos Artesanales', 'Auténtico queso madurado de Boyacá con Denominación de Origen protegida, aroma rústico y notas a mantequilla madura.', 17000.00, 25500.00, 16, 5, '500 g', 'Vaca', 'fuerte', '3-6 meses', '500 g', 'Boyacá', 'activo'),
('QAR-007', 'Queso al Vino Tinto Andino', 'Queso Gourmet', 'Quesos Artesanales', 'Queso semicurado sumergido en reducción de vino tinto durante su afinado. Corteza morada profunda y bouquet afrutado.', 22000.00, 33000.00, 12, 4, '450 g', 'Vaca', 'medio', '2 meses', '450 g', 'Cundinamarca', 'activo'),
('QAR-008', 'Queso con Finas Hierbas & Ajo Negro', 'Queso Especiado', 'Quesos Artesanales', 'Pasta semicurada con incrustaciones de romero, tomillo silvestre y un delicado toque dulce de ajo negro confitado.', 16000.00, 24000.00, 18, 5, '400 g', 'Vaca', 'medio', '1 mes', '400 g', 'Valle del Cauca', 'activo'),
('QAR-009', 'Queso Provolone Hilado al Horno', 'Queso Hilado', 'Quesos Artesanales', 'Rodaja gruesa de queso hilado con pimentón dulce y orégano seco, listo para asar a la parrilla o al horno.', 13500.00, 20000.00, 25, 6, '500 g', 'Vaca', 'medio', '2 meses', '500 g', 'Antioquia', 'activo'),

-- Quesos Frescos
('QF-005', 'Queso Sabana Tradicional', 'Queso Fresco', 'Quesos Frescos', 'Queso campesino prensado de consistencia firme y punto de sal ideal para tajadas y chocolate caliente.', 9500.00, 14000.00, 30, 8, 'Kilogramo', 'Vaca', 'suave', 'Sin maduración', '1 kg', 'Cundinamarca', 'activo'),
('QF-006', 'Bocconcini de Mozzarella en Suero', 'Queso Fresco', 'Quesos Frescos', 'Esferas frescas de pasta hilada en suero lácteo natural, frescura y jugosidad inigualable para ensaladas gourmet.', 11000.00, 16500.00, 20, 6, 'Pote 350g', 'Vaca', 'suave', 'Sin maduración', '350 g', 'Cundinamarca', 'activo'),
('QF-007', 'Queso Molido Santandereano', 'Queso Fresco', 'Quesos Frescos', 'Queso fresco salado finamente desgranado en molino artesanal, base predilecta para arepas de maíz pelado.', 8000.00, 12000.00, 22, 6, '500 g', 'Vaca', 'medio', 'Sin maduración', '500 g', 'Santander', 'activo'),

-- Quesos Madurados
('QM-005', 'Queso Pecorino Andino Madurado', 'Queso Maduro', 'Quesos Madurados', 'Elaborado con leche de oveja de pastos altos, curado con sal marina. Notas punzantes y cristalización perfecta.', 21000.00, 31000.00, 10, 3, '400 g', 'Oveja', 'muy fuerte', '8 meses', '400 g', 'Nariño', 'activo'),
('QM-006', 'Queso Gruyère de Montaña', 'Queso Maduro', 'Quesos Madurados', 'Queso madurado de pasta cocida con notas a avellana y caldo vegetal. Excepcional elasticidad al fundir.', 19500.00, 29000.00, 14, 4, '500 g', 'Vaca', 'fuerte', '6 meses', '500 g', 'Boyacá', 'activo'),
('QM-007', 'Queso Emmental con Ojos Naturales', 'Queso Maduro', 'Quesos Madurados', 'Gran clásico con grandes ojos de fermentación propiónica, dulzor lácteo sutil y textura flexible.', 16500.00, 24500.00, 15, 4, '500 g', 'Vaca', 'medio', '4 meses', '500 g', 'Región Andina', 'activo'),
('QM-008', 'Queso Cheddar Curado 12 Meses', 'Queso Maduro', 'Quesos Madurados', 'Cheddar artesanal envejecido en cueva con característico tono anaranjado natural y regusto profundo y salino.', 18000.00, 27000.00, 11, 4, '450 g', 'Vaca', 'fuerte', '12 meses', '450 g', 'Valle del Cauca', 'activo'),

-- Quesos Cremosos
('QC-005', 'Stracciatella Fresca de Búfala', 'Queso Cremoso', 'Quesos Cremosos', 'El corazón tierno de la burrata: hebras de cuajada hilada fresca bañadas en crema de leche espesa y dulce.', 15500.00, 23500.00, 13, 4, 'Pote 300g', 'Búfala', 'suave', 'Sin maduración', '300 g', 'Magdalena Medio', 'activo'),
('QC-006', 'Queso Feta Artesanal en Salmuera', 'Queso Cremoso', 'Quesos Cremosos', 'Bloque blanco desmenuzable madurado en salmuera marina, ligeramente ácido y salado, ideal para ensaladas.', 14000.00, 21000.00, 16, 5, 'Frasco 400g', 'Cabra', 'medio', '2 meses', '400 g', 'Santander', 'activo'),
('QC-007', 'Queso Crema con Albahaca & Nuez', 'Queso Cremoso', 'Quesos Cremosos', 'Untable artesanal sedoso aromatizado con albahaca fresca del huerto y trocitos de nuez pecana tostada.', 9000.00, 14500.00, 20, 6, 'Tarrina 250g', 'Vaca', 'suave', 'Sin maduración', '250 g', 'Bogotá', 'activo'),

-- Quesos Azules
('QA-004', 'Queso Gorgonzola Piccante', 'Queso Azul', 'Quesos Azules', 'Versión madurada de pasta marmolada con penetrantes vetas de penicilium, consistencia firme y picor vivaz.', 22000.00, 32500.00, 8, 3, '350 g', 'Vaca', 'muy fuerte', '4 meses', '350 g', 'Nacional', 'activo'),
('QA-005', 'Queso Stilton Reserva Azul', 'Queso Azul', 'Quesos Azules', 'El rey de los quesos azules: textura escamosa y untuosa con corteza enmohecida natural y notas terrosas.', 24000.00, 35000.00, 6, 2, '300 g', 'Vaca', 'fuerte', '5 meses', '300 g', 'Nacional Premium', 'activo'),

-- Quesos de Cabra
('QG-005', 'Pirámide de Cabra con Ceniza & Tomillo', 'Queso de Cabra', 'Quesos de Cabra', 'Pirámide truncada estilo Valençay con fina capa de ceniza vegetal y aroma a tomillo silvestre.', 17500.00, 26000.00, 10, 3, 'Unidad 250g', 'Cabra', 'medio', '3 semanas', '250 g', 'Boyacá', 'activo'),
('QG-006', 'Queso Gouda de Cabra Semicurado', 'Queso de Cabra', 'Quesos de Cabra', 'Queso de pasta prensada y corteza encerada con la suavidad del gouda y el distintivo sabor caprino.', 19000.00, 28500.00, 12, 4, '450 g', 'Cabra', 'medio', '3 meses', '450 g', 'Santander', 'activo');

-- Mensaje de confirmación
SELECT COUNT(*) as 'Nuevos productos insertados' FROM productos WHERE codigo LIKE 'Q%';
