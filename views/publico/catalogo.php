<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quesos San Gabriel — Quesería Artesanal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="public/css/publico.css" rel="stylesheet">
</head>
<body>

<!-- =============================================
     NAVBAR
     ============================================= -->
<nav class="pub-navbar" id="pubNavbar">
    <div class="pub-container">
        <div class="pub-nav-inner">

            <!-- Logo -->
            <a href="publico.php" class="pub-brand">
                <span class="pub-brand-emoji">🧀</span>
                <div>
                    <div class="pub-brand-name">Quesos San Gabriel</div>
                    <div class="pub-brand-sub">Quesería Artesanal</div>
                </div>
            </a>

            <!-- Links escritorio -->
            <ul class="pub-nav-links">
                <li><a href="publico.php" class="pub-nav-link active">Inicio</a></li>
                <li><a href="#productos" class="pub-nav-link">Productos</a></li>
                <li><a href="#nosotros" class="pub-nav-link">Nosotros</a></li>
                <li><a href="#contacto" class="pub-nav-link">Contacto</a></li>
            </ul>

            <!-- Acciones escritorio -->
            <div class="pub-nav-actions">
                <?php if ($carritoTotal > 0): ?>
                <a href="index.php?c=carrito" class="pub-icon-btn" title="Mi carrito">
                    <i class="bi bi-cart3"></i>
                    <span class="pub-cart-badge"><?= $carritoTotal ?></span>
                </a>
                <?php endif; ?>

                <?php if ($estaAutenticado): ?>
                    <?php if ($esAdmin): ?>
                        <a href="index.php?c=dashboard" class="pub-btn-admin">
                            <i class="bi bi-speedometer2 me-1"></i>Panel Admin
                        </a>
                    <?php else: ?>
                        <a href="index.php?c=carrito" class="pub-btn-cart-nav">
                            <i class="bi bi-cart3 me-1"></i>Mi Carrito
                            <?php if ($carritoTotal > 0): ?>
                                <span class="pub-cart-pill"><?= $carritoTotal ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="logout.php" class="pub-btn-outline-nav">
                            <i class="bi bi-box-arrow-right me-1"></i>Salir
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="pub-btn-login-nav">
                        <i class="bi bi-person me-1"></i>Iniciar Sesión
                    </a>
                <?php endif; ?>

                <!-- Hamburguesa móvil -->
                <button class="pub-hamburger" id="pubHamburger" aria-label="Menú">
                    <span></span><span></span><span></span>
                </button>
            </div>

        </div><!-- /.pub-nav-inner -->
    </div><!-- /.pub-container -->

    <!-- Menú móvil -->
    <div class="pub-mobile-nav" id="pubMobileNav">
        <div class="pub-container pub-mobile-links">
            <a href="publico.php"><i class="bi bi-house me-2"></i>Inicio</a>
            <a href="#productos"><i class="bi bi-grid me-2"></i>Productos</a>
            <a href="#nosotros"><i class="bi bi-info-circle me-2"></i>Nosotros</a>
            <a href="#contacto"><i class="bi bi-telephone me-2"></i>Contacto</a>
            <hr class="pub-mobile-hr">
            <?php if ($estaAutenticado): ?>
                <?php if ($esAdmin): ?>
                    <a href="index.php?c=dashboard"><i class="bi bi-speedometer2 me-2"></i>Panel Admin</a>
                <?php else: ?>
                    <a href="index.php?c=carrito"><i class="bi bi-cart3 me-2"></i>Mi Carrito <?= $carritoTotal > 0 ? "({$carritoTotal})" : '' ?></a>
                <?php endif; ?>
                <a href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a>
            <?php else: ?>
                <a href="login.php" class="pub-mobile-login"><i class="bi bi-person me-2"></i>Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </div>
</nav>


<!-- =============================================
     HERO COMPACTO — título + descripción + CTA
     ============================================= -->
<section class="pub-hero-compact">
    <div class="pub-hero-img-wrap">
        <img src="public/img/queso-fondo.jpg" alt="Quesos artesanales"
             onerror="this.src='https://images.unsplash.com/photo-1452195100486-9cc805987862?w=1600&q=85'">
        <div class="pub-hero-overlay"></div>
    </div>
    <div class="pub-container pub-hero-body">
        <div class="pub-hero-badge">
            <i class="bi bi-award-fill me-1"></i> Quesos 100% Artesanales
        </div>
        <h1 class="pub-hero-title">
            Nuestros <span class="pub-hero-gold">Productos</span>
        </h1>
        <p class="pub-hero-desc">
            Quesos frescos y madurados elaborados con leche de la región.<br>
            Directo del campo a tu mesa.
        </p>
        <a href="#productos" class="pub-cta-btn">
            <i class="bi bi-arrow-down-circle me-2"></i>Ver todos los quesos
        </a>
    </div>
</section>


<!-- =============================================
     SECCIÓN PRODUCTOS
     ============================================= -->
<section class="pub-productos" id="productos">
    <div class="pub-container">

        <!-- Encabezado de sección -->
        <div class="pub-sec-head">
            <div class="pub-sec-kicker">Catálogo</div>
            <h2 class="pub-sec-title">Nuestros Quesos</h2>
            <p class="pub-sec-sub">Frescos, hilados y madurados — tradición en cada bloque</p>
        </div>

        <!-- Buscador y filtros avanzados -->
        <div class="pub-search-bar">
            <form action="publico.php" method="GET" class="pub-search-form-advanced">
                
                <!-- Búsqueda por texto -->
                <div class="pub-search-input-wrap">
                    <i class="bi bi-search pub-search-icon"></i>
                    <input type="text" name="q" class="pub-search-input"
                           placeholder="Buscar queso por nombre o tipo..."
                           value="<?= htmlspecialchars($busqueda) ?>">
                </div>

                <!-- Filtros en grid -->
                <div class="pub-filters-grid">
                    <!-- Categoría -->
                    <div class="pub-filter-item">
                        <label class="pub-filter-label">
                            <i class="bi bi-grid-3x3-gap me-1"></i>Categoría
                        </label>
                        <select name="categoria" class="pub-filter-select">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= htmlspecialchars($c) ?>"
                                    <?= $categoria === $c ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tipo de Leche -->
                    <div class="pub-filter-item">
                        <label class="pub-filter-label">
                            <i class="bi bi-droplet me-1"></i>Tipo de Leche
                        </label>
                        <select name="tipo_leche" class="pub-filter-select">
                            <option value="">Todos los tipos</option>
                            <?php foreach ($tiposLeche as $tl): ?>
                                <option value="<?= htmlspecialchars($tl) ?>"
                                    <?= $tipoLeche === $tl ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tl) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Intensidad de Sabor -->
                    <div class="pub-filter-item">
                        <label class="pub-filter-label">
                            <i class="bi bi-fire me-1"></i>Intensidad
                        </label>
                        <select name="intensidad" class="pub-filter-select">
                            <option value="">Todas las intensidades</option>
                            <?php foreach ($intensidades as $int): ?>
                                <option value="<?= htmlspecialchars($int) ?>"
                                    <?= $intensidad === $int ? 'selected' : '' ?>>
                                    <?= ucfirst($int) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tipo Tradicional (opcional) -->
                    <div class="pub-filter-item">
                        <label class="pub-filter-label">
                            <i class="bi bi-tag me-1"></i>Tipo
                        </label>
                        <select name="tipo" class="pub-filter-select">
                            <option value="">Todos los tipos</option>
                            <?php foreach ($tipos as $t): ?>
                                <option value="<?= htmlspecialchars($t) ?>"
                                    <?= $tipoFiltro === $t ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="pub-filter-actions">
                    <button type="submit" class="pub-search-btn">
                        <i class="bi bi-funnel me-1"></i>Aplicar Filtros
                    </button>
                    <?php if (!empty($busqueda) || !empty($tipoFiltro) || !empty($categoria) || !empty($tipoLeche) || !empty($intensidad)): ?>
                        <a href="publico.php#productos" class="pub-clear-btn">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Filtros activos (badges) -->
        <?php if (!empty($busqueda) || !empty($tipoFiltro) || !empty($categoria) || !empty($tipoLeche) || !empty($intensidad)): ?>
        <div class="pub-active-filters">
            <span class="pub-active-filters-label">
                <i class="bi bi-funnel-fill me-1"></i>Filtros activos:
            </span>
            <?php if (!empty($busqueda)): ?>
                <span class="pub-filter-badge">
                    <i class="bi bi-search me-1"></i><?= htmlspecialchars($busqueda) ?>
                    <a href="publico.php?<?= http_build_query(array_filter(['tipo'=>$tipoFiltro,'categoria'=>$categoria,'tipo_leche'=>$tipoLeche,'intensidad'=>$intensidad])) ?>#productos" class="pub-filter-badge-close">×</a>
                </span>
            <?php endif; ?>
            <?php if (!empty($categoria)): ?>
                <span class="pub-filter-badge">
                    <i class="bi bi-grid-3x3-gap me-1"></i><?= htmlspecialchars($categoria) ?>
                    <a href="publico.php?<?= http_build_query(array_filter(['q'=>$busqueda,'tipo'=>$tipoFiltro,'tipo_leche'=>$tipoLeche,'intensidad'=>$intensidad])) ?>#productos" class="pub-filter-badge-close">×</a>
                </span>
            <?php endif; ?>
            <?php if (!empty($tipoLeche)): ?>
                <span class="pub-filter-badge">
                    <i class="bi bi-droplet me-1"></i>Leche de <?= htmlspecialchars($tipoLeche) ?>
                    <a href="publico.php?<?= http_build_query(array_filter(['q'=>$busqueda,'tipo'=>$tipoFiltro,'categoria'=>$categoria,'intensidad'=>$intensidad])) ?>#productos" class="pub-filter-badge-close">×</a>
                </span>
            <?php endif; ?>
            <?php if (!empty($intensidad)): ?>
                <span class="pub-filter-badge">
                    <i class="bi bi-fire me-1"></i>Sabor <?= ucfirst($intensidad) ?>
                    <a href="publico.php?<?= http_build_query(array_filter(['q'=>$busqueda,'tipo'=>$tipoFiltro,'categoria'=>$categoria,'tipo_leche'=>$tipoLeche])) ?>#productos" class="pub-filter-badge-close">×</a>
                </span>
            <?php endif; ?>
            <?php if (!empty($tipoFiltro)): ?>
                <span class="pub-filter-badge">
                    <i class="bi bi-tag me-1"></i><?= htmlspecialchars($tipoFiltro) ?>
                    <a href="publico.php?<?= http_build_query(array_filter(['q'=>$busqueda,'categoria'=>$categoria,'tipo_leche'=>$tipoLeche,'intensidad'=>$intensidad])) ?>#productos" class="pub-filter-badge-close">×</a>
                </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Grid de tarjetas -->
        <?php if (empty($quesos)): ?>
            <div class="pub-empty-state">
                <div class="pub-empty-icon">🧀</div>
                <h4>No encontramos quesos con esa búsqueda</h4>
                <p class="text-muted">Intenta con otro nombre o limpia los filtros.</p>
                <a href="publico.php#productos" class="pub-cta-btn mt-3">Ver todos</a>
            </div>
        <?php else: ?>
            <div class="pub-cards-grid">
                <?php
                // Imágenes por tipo de queso (Unsplash, sin costo, formato seguro)
                $imagenesPorTipo = [
                    'Queso Fresco'        => 'https://images.unsplash.com/photo-1618164436241-4473940d1f5c?w=600&q=80',
                    'Queso Duro / Salado' => 'https://images.unsplash.com/photo-1631379578550-7038263db699?w=600&q=80',
                    'Queso Semigraso'     => 'https://images.unsplash.com/photo-1552767059-ce182ead6c1b?w=600&q=80',
                    'Queso Hilado'        => 'https://images.unsplash.com/photo-1571748982800-fa51082c2224?w=600&q=80',
                    'Cuajada'             => 'https://images.unsplash.com/photo-1559561853-08451507cbe7?w=600&q=80',
                    'Queso Maduro'        => 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a318?w=600&q=80',
                ];
                $imgDefault = 'https://images.unsplash.com/photo-1452195100486-9cc805987862?w=600&q=80';

                foreach ($quesos as $q):
                    $disponible = (int)$q['cantidad'] > 0;
                    $imgUrl     = $imagenesPorTipo[$q['tipo']] ?? $imgDefault;
                    
                    // Información adicional de categorización
                    $categoria      = $q['categoria'] ?? $q['tipo'];
                    $tipoLeche      = $q['tipo_leche'] ?? 'Vaca';
                    $intensidad     = $q['intensidad_sabor'] ?? 'medio';
                    $presentacion   = $q['peso_presentacion'] ?? $q['unidad_medida'];
                ?>
                <div class="pub-card <?= !$disponible ? 'pub-card--agotado' : '' ?>">

                    <!-- Imagen -->
                    <div class="pub-card-img-wrap">
                        <img src="<?= $imgUrl ?>"
                             alt="<?= htmlspecialchars($q['nombre']) ?>"
                             class="pub-card-img"
                             loading="lazy">
                        <!-- Badge disponibilidad -->
                        <span class="pub-card-status <?= $disponible ? 'pub-card-status--ok' : 'pub-card-status--off' ?>">
                            <?php if ($disponible): ?>
                                <i class="bi bi-check-circle-fill me-1"></i>Disponible
                            <?php else: ?>
                                <i class="bi bi-x-circle-fill me-1"></i>Agotado
                            <?php endif; ?>
                        </span>
                        <!-- Categoría principal -->
                        <span class="pub-card-categoria"><?= htmlspecialchars($categoria) ?></span>
                    </div>

                    <!-- Cuerpo -->
                    <div class="pub-card-body">
                        <div class="pub-card-codigo"><?= htmlspecialchars($q['codigo']) ?></div>
                        <h3 class="pub-card-nombre"><?= htmlspecialchars($q['nombre']) ?></h3>
                        <p class="pub-card-desc">
                            <?= htmlspecialchars($q['descripcion'] ?? 'Queso artesanal elaborado con leche fresca de la región.') ?>
                        </p>
                        
                        <!-- Etiquetas de características -->
                        <div class="pub-card-tags">
                            <span class="pub-card-tag pub-card-tag-leche">
                                <i class="bi bi-droplet-fill me-1"></i><?= htmlspecialchars($tipoLeche) ?>
                            </span>
                            <span class="pub-card-tag pub-card-tag-intensidad pub-card-tag-intensidad-<?= $intensidad ?>">
                                <i class="bi bi-fire me-1"></i><?= ucfirst($intensidad) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="pub-card-foot">
                        <div class="pub-card-precio-wrap">
                            <span class="pub-card-unidad"><?= htmlspecialchars($presentacion) ?></span>
                            <span class="pub-card-precio"><?= formatearMoneda($q['precio_venta']) ?></span>
                        </div>

                        <div class="pub-card-actions">
                            <!-- Botón "Ver producto" siempre visible -->
                            <button type="button"
                                    class="pub-btn-ver"
                                    onclick="abrirModal(<?= htmlspecialchars(json_encode([
                                        'nombre'      => $q['nombre'],
                                        'tipo'        => $q['tipo'],
                                        'categoria'   => $categoria,
                                        'tipo_leche'  => $tipoLeche,
                                        'intensidad'  => ucfirst($intensidad),
                                        'descripcion' => $q['descripcion'] ?? 'Queso artesanal elaborado con leche fresca.',
                                        'precio'      => formatearMoneda($q['precio_venta']),
                                        'unidad'      => $presentacion,
                                        'codigo'      => $q['codigo'],
                                        'disponible'  => $disponible,
                                        'imagen'      => $imgUrl,
                                        'id'          => $q['id'],
                                        'origen'      => $q['origen'] ?? 'Nacional',
                                        'maduracion'  => $q['tiempo_maduracion'] ?? 'N/A',
                                    ])) ?>)">
                                <i class="bi bi-eye me-1"></i>Ver producto
                            </button>

                            <?php if ($disponible): ?>
                                <?php if ($estaAutenticado && !$esAdmin): ?>
                                    <form method="POST" action="index.php?c=carrito&a=agregar">
                                        <input type="hidden" name="producto_id" value="<?= $q['id'] ?>">
                                        <input type="hidden" name="cantidad" value="1">
                                        <input type="hidden" name="redirect" value="publico.php#productos">
                                        <button type="submit" class="pub-btn-agregar">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </form>
                                <?php elseif (!$estaAutenticado): ?>
                                    <a href="login.php" class="pub-btn-agregar pub-btn-agregar--lock" title="Inicia sesión para comprar">
                                        <i class="bi bi-lock"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>


<!-- =============================================
     MODAL "VER PRODUCTO"
     ============================================= -->
<div class="pub-modal-overlay" id="pubModalOverlay" onclick="cerrarModal()">
    <div class="pub-modal" id="pubModal" onclick="event.stopPropagation()">
        <button class="pub-modal-close" onclick="cerrarModal()">
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="pub-modal-img-wrap">
            <img src="" alt="" id="pubModalImg" class="pub-modal-img">
            <span class="pub-modal-tipo" id="pubModalCategoria"></span>
        </div>
        <div class="pub-modal-body">
            <div class="pub-modal-codigo" id="pubModalCodigo"></div>
            <h2 class="pub-modal-nombre" id="pubModalNombre"></h2>
            <p class="pub-modal-desc" id="pubModalDesc"></p>
            
            <!-- Características detalladas -->
            <div class="pub-modal-caracteristicas">
                <div class="pub-modal-caract-item">
                    <i class="bi bi-droplet-fill text-primary"></i>
                    <span class="pub-modal-caract-label">Tipo de Leche:</span>
                    <span class="pub-modal-caract-value" id="pubModalTipoLeche"></span>
                </div>
                <div class="pub-modal-caract-item">
                    <i class="bi bi-fire text-danger"></i>
                    <span class="pub-modal-caract-label">Intensidad:</span>
                    <span class="pub-modal-caract-value" id="pubModalIntensidad"></span>
                </div>
                <div class="pub-modal-caract-item">
                    <i class="bi bi-clock-history text-warning"></i>
                    <span class="pub-modal-caract-label">Maduración:</span>
                    <span class="pub-modal-caract-value" id="pubModalMaduracion"></span>
                </div>
                <div class="pub-modal-caract-item">
                    <i class="bi bi-geo-alt-fill text-success"></i>
                    <span class="pub-modal-caract-label">Origen:</span>
                    <span class="pub-modal-caract-value" id="pubModalOrigen"></span>
                </div>
            </div>
            
            <div class="pub-modal-meta">
                <div>
                    <div class="pub-modal-meta-label">Presentación</div>
                    <div class="pub-modal-meta-val" id="pubModalUnidad"></div>
                </div>
                <div>
                    <div class="pub-modal-meta-label">Precio</div>
                    <div class="pub-modal-precio" id="pubModalPrecio"></div>
                </div>
                <div>
                    <div class="pub-modal-meta-label">Estado</div>
                    <div id="pubModalEstado"></div>
                </div>
            </div>
            <div id="pubModalAccion" class="pub-modal-accion"></div>
        </div>
    </div>
</div>


<!-- =============================================
     NOSOTROS
     ============================================= -->
<section class="pub-nosotros" id="nosotros">
    <div class="pub-container">
        <div class="row align-items-center g-5">
            <div class="col-12 col-md-6">
                <div class="pub-nosotros-kicker">Sobre Nosotros</div>
                <h2 class="pub-nosotros-title">Tradición quesera<br>desde el campo</h2>
                <p class="pub-nosotros-text">
                    En <strong>Quesos San Gabriel</strong> elaboramos quesos artesanales con leche fresca
                    de la región, combinando técnicas tradicionales con calidad moderna.
                    Cada bloque es el resultado de años de experiencia y amor por el oficio.
                </p>
                <div class="pub-features">
                    <div class="pub-feature-item">
                        <i class="bi bi-patch-check-fill"></i>
                        <span>100% leche natural</span>
                    </div>
                    <div class="pub-feature-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Producción local</span>
                    </div>
                    <div class="pub-feature-item">
                        <i class="bi bi-heart-fill"></i>
                        <span>Elaboración artesanal</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="pub-nosotros-img-frame">
                    <img src="public/img/queso-fondo.jpg"
                         alt="Quesos San Gabriel"
                         onerror="this.src='https://images.unsplash.com/photo-1559561853-08451507cbe7?w=700&q=80'">
                </div>
            </div>
        </div>
    </div>
</section>


<!-- =============================================
     CONTACTO
     ============================================= -->
<section class="pub-contacto" id="contacto">
    <div class="pub-container">
        <div class="pub-sec-head pub-sec-head--light">
            <div class="pub-sec-kicker pub-sec-kicker--light">Comunícate</div>
            <h2 class="pub-sec-title pub-sec-title--light">Contáctanos</h2>
            <p class="pub-sec-sub pub-sec-sub--light">Estamos para atenderte con gusto</p>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-12 col-sm-6 col-md-4">
                <div class="pub-contact-card">
                    <div class="pub-contact-icon-wrap">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <div class="pub-contact-label">Teléfono</div>
                    <div class="pub-contact-val">+57 310 000 0000</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="pub-contact-card">
                    <div class="pub-contact-icon-wrap">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div class="pub-contact-label">Correo</div>
                    <div class="pub-contact-val">ventas@quesossangabriel.com</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="pub-contact-card">
                    <div class="pub-contact-icon-wrap">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div class="pub-contact-label">Dirección</div>
                    <div class="pub-contact-val">Vía principal, Colombia</div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- =============================================
     FOOTER
     ============================================= -->
<footer class="pub-footer">
    <div class="pub-container pub-footer-inner">
        <div class="pub-footer-brand-wrap">
            <span class="pub-footer-emoji">🧀</span>
            <div>
                <div class="pub-footer-name">Quesos San Gabriel</div>
                <div class="pub-footer-tagline">Quesería Artesanal</div>
            </div>
        </div>
        <div class="pub-footer-links">
            <a href="publico.php">Inicio</a>
            <a href="#productos">Productos</a>
            <a href="#nosotros">Nosotros</a>
            <a href="#contacto">Contacto</a>
        </div>
        <div class="pub-footer-copy">
            &copy; <?= date('Y') ?> Quesos San Gabriel
        </div>
    </div>
</footer>


<!-- =============================================
     SCRIPTS
     ============================================= -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ── Navbar scroll shadow ── */
window.addEventListener('scroll', function () {
    document.getElementById('pubNavbar').classList.toggle('pub-navbar--scrolled', window.scrollY > 40);
});

/* ── Menú hamburguesa móvil ── */
const hamburger   = document.getElementById('pubHamburger');
const mobileNav   = document.getElementById('pubMobileNav');
if (hamburger && mobileNav) {
    hamburger.addEventListener('click', function () {
        this.classList.toggle('open');
        mobileNav.classList.toggle('open');
    });
    // Cerrar al hacer clic en un enlace
    mobileNav.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', () => {
            hamburger.classList.remove('open');
            mobileNav.classList.remove('open');
        });
    });
}

/* ── Modal "Ver producto" ── */
function abrirModal(p) {
    document.getElementById('pubModalImg').src     = p.imagen;
    document.getElementById('pubModalImg').alt     = p.nombre;
    document.getElementById('pubModalCategoria').textContent  = p.categoria || p.tipo;
    document.getElementById('pubModalCodigo').textContent = p.codigo;
    document.getElementById('pubModalNombre').textContent = p.nombre;
    document.getElementById('pubModalDesc').textContent   = p.descripcion;
    document.getElementById('pubModalUnidad').textContent = p.unidad;
    document.getElementById('pubModalPrecio').textContent = p.precio;
    
    // Características adicionales
    document.getElementById('pubModalTipoLeche').textContent = p.tipo_leche || 'Vaca';
    document.getElementById('pubModalIntensidad').textContent = p.intensidad || 'Medio';
    document.getElementById('pubModalMaduracion').textContent = p.maduracion || 'N/A';
    document.getElementById('pubModalOrigen').textContent = p.origen || 'Nacional';

    const estadoEl = document.getElementById('pubModalEstado');
    if (p.disponible) {
        estadoEl.innerHTML = '<span class="pub-modal-ok"><i class="bi bi-check-circle-fill me-1"></i>Disponible</span>';
    } else {
        estadoEl.innerHTML = '<span class="pub-modal-off"><i class="bi bi-x-circle-fill me-1"></i>Agotado</span>';
    }

    const accionEl = document.getElementById('pubModalAccion');
    const esAuth   = <?= $estaAutenticado ? 'true' : 'false' ?>;
    const esAdmin  = <?= $esAdmin ? 'true' : 'false' ?>;

    if (p.disponible && !esAdmin) {
        if (esAuth) {
            accionEl.innerHTML = `
                <form method="POST" action="index.php?c=carrito&a=agregar">
                    <input type="hidden" name="producto_id" value="${p.id}">
                    <input type="hidden" name="cantidad" value="1">
                    <input type="hidden" name="redirect" value="publico.php#productos">
                    <button type="submit" class="pub-modal-btn-agregar">
                        <i class="bi bi-cart-plus me-2"></i>Agregar al carrito
                    </button>
                </form>`;
        } else {
            accionEl.innerHTML = `
                <a href="login.php" class="pub-modal-btn-login">
                    <i class="bi bi-person me-2"></i>Inicia sesión para comprar
                </a>`;
        }
    } else if (!p.disponible) {
        accionEl.innerHTML = `<span class="pub-modal-sin-stock">Sin stock disponible</span>`;
    } else {
        accionEl.innerHTML = '';
    }

    document.getElementById('pubModalOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function cerrarModal() {
    document.getElementById('pubModalOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModal(); });
</script>
</body>
</html>
