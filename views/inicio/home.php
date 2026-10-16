<?php
/**
 * Quesos San Gabriel — Página de Inicio
 * Vista principal: muestra los productos sin requerir autenticación.
 */

// Mapa de imágenes Unsplash por tipo de queso
$imagenesPorTipo = [
    'Queso Fresco'        => 'https://images.unsplash.com/photo-1618164436241-4473940d1f5c?w=600&q=80',
    'Queso Duro / Salado' => 'https://images.unsplash.com/photo-1631379578550-7038263db699?w=600&q=80',
    'Queso Semigraso'     => 'https://images.unsplash.com/photo-1552767059-ce182ead6c1b?w=600&q=80',
    'Queso Hilado'        => 'https://images.unsplash.com/photo-1571748982800-fa51082c2224?w=600&q=80',
    'Cuajada'             => 'https://images.unsplash.com/photo-1559561853-08451507cbe7?w=600&q=80',
    'Queso Maduro'        => 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a318?w=600&q=80',
];
$imgDefault = 'https://images.unsplash.com/photo-1452195100486-9cc805987862?w=600&q=80';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quesos San Gabriel — Nuestros Productos</title>
    <meta name="description" content="Quesos San Gabriel: quesería artesanal colombiana. Queso campesino, doble crema, costeño, quesillo, cuajada y mozzarella. Frescos y madurados, directo del campo a tu mesa.">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <!-- Estilos públicos -->
    <link href="public/css/publico.css" rel="stylesheet">
    <!-- Estilos de la página de inicio -->
    <link href="public/css/home.css" rel="stylesheet">
    <!-- Estilos de modales de Login y Registro -->
    <link href="public/css/login.css" rel="stylesheet">
</head>
<body class="home-body">

<!-- =============================================
     NAVBAR
     ============================================= -->
<nav class="home-navbar" id="homeNavbar">
    <div class="home-container">
        <div class="home-nav-inner">

            <!-- Logo -->
            <a href="index.php" class="home-brand" id="home-logo-link">
                <span class="home-brand-emoji">🧀</span>
                <div>
                    <div class="home-brand-name">Quesos San Gabriel</div>
                    <div class="home-brand-sub">Quesería Artesanal</div>
                </div>
            </a>

            <!-- Links escritorio -->
            <ul class="home-nav-links" id="home-nav-links">
                <li><a href="index.php" class="home-nav-link active" id="nav-inicio">Inicio</a></li>
                <li><a href="#productos" class="home-nav-link" id="nav-productos">Productos</a></li>
                <li><a href="#nosotros" class="home-nav-link" id="nav-nosotros">Nosotros</a></li>

                <?php if ($estaAutenticado && !$esAdmin): ?>
                    <!-- Cliente autenticado -->
                    <li>
                        <span class="home-nav-user">
                            <i class="bi bi-person-circle text-info me-1"></i>
                            Hola, <?= htmlspecialchars(explode(' ', $usuarioNombre)[0] ?: 'Cliente') ?>
                        </span>
                    </li>
                    <li>
                        <button type="button" class="home-nav-cart-btn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCarrito" id="nav-cart-btn">
                            <i class="bi bi-cart3"></i>
                            <span>Carrito</span>
                            <span class="badge bg-warning text-dark ms-1" id="nav-cart-count"><?= (int)$carritoTotal ?></span>
                        </button>
                    </li>
                    <li>
                        <a href="logout.php" class="home-nav-link text-danger" id="nav-cerrar">
                            <i class="bi bi-box-arrow-right me-1"></i>Salir
                        </a>
                    </li>
                <?php elseif ($estaAutenticado && $esAdmin): ?>
                    <!-- Administrador -->
                    <li>
                        <a href="index.php?c=dashboard" class="home-nav-btn" style="background:#2563eb;" id="nav-admin">
                            <i class="bi bi-speedometer2 me-1"></i>Panel Admin
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="home-nav-link" id="nav-cerrar">Cerrar Sesión</a>
                    </li>
                <?php else: ?>
                    <!-- Visitante no autenticado -->
                    <li>
                        <button type="button" class="home-nav-cart-btn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCarrito" id="nav-cart-btn-guest">
                            <i class="bi bi-cart3"></i>
                            <span>Carrito</span>
                            <?php if ($carritoTotal > 0): ?>
                                <span class="badge bg-warning text-dark ms-1"><?= (int)$carritoTotal ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="home-nav-btn" data-bs-toggle="modal" data-bs-target="#modalLogin" id="nav-login">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
                        </button>
                    </li>
                    <li>
                        <button type="button" class="home-nav-btn home-nav-btn-registro" data-bs-toggle="modal" data-bs-target="#modalRegistro" id="nav-registro">
                            <i class="bi bi-person-plus me-1"></i>Registrarse
                        </button>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Botón hamburguesa móvil -->
            <button class="home-hamburger" id="homeHamburger" aria-label="Menú" type="button">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <!-- Menú móvil desplegable -->
    <div class="home-mobile-nav" id="homeMobileNav">
        <div class="home-container home-mobile-links">
            <a href="index.php" id="mob-inicio"><i class="bi bi-house me-2"></i>Inicio</a>
            <a href="#productos" id="mob-productos"><i class="bi bi-grid me-2"></i>Productos</a>
            <a href="#nosotros" id="mob-nosotros"><i class="bi bi-info-circle me-2"></i>Nosotros</a>
            
            <a href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCarrito" id="mob-carrito">
                <i class="bi bi-cart3 me-2"></i>Carrito (<?= $carritoTotal ?>)
            </a>

            <hr class="home-mobile-hr">
            <?php if ($estaAutenticado): ?>
                <?php if ($esAdmin): ?>
                    <a href="index.php?c=dashboard" id="mob-admin"><i class="bi bi-speedometer2 me-2"></i>Panel Admin</a>
                <?php else: ?>
                    <span class="text-white small px-2 py-1"><i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($usuarioNombre) ?></span>
                <?php endif; ?>
                <a href="logout.php" id="mob-salir" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a>
            <?php else: ?>
                <button type="button" class="home-mobile-login border-0 w-100 text-start" data-bs-toggle="modal" data-bs-target="#modalLogin" id="mob-login"><i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión</button>
                <button type="button" class="home-mobile-login w-100 text-start" style="background:transparent; border:1px solid #fff;" data-bs-toggle="modal" data-bs-target="#modalRegistro" id="mob-reg"><i class="bi bi-person-plus me-2"></i>Registrarse</button>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Mensajes Flash -->
<?php if (!empty($flash)): ?>
<div class="home-container mt-3">
    <div class="alert alert-<?= htmlspecialchars($flash['tipo']) ?> alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi <?= $flash['tipo'] === 'success' ? 'bi-check-circle-fill' : 'bi-info-circle-fill' ?>"></i>
            <div><?= $flash['mensaje'] ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
</div>
<?php endif; ?>


<!-- =============================================
     HERO
     ============================================= -->
<section class="home-hero" id="home-hero">
    <div class="home-hero-bg">
        <img src="public/img/queso-fondo.jpg"
             alt="Quesos artesanales San Gabriel"
             class="home-hero-img"
             onerror="this.src='https://images.unsplash.com/photo-1452195100486-9cc805987862?w=1600&q=85'">
        <div class="home-hero-overlay"></div>
    </div>
    <div class="home-container home-hero-body">
        <div class="home-hero-badge">
            <i class="bi bi-award-fill me-1"></i> Quesos 100% Artesanales · Colombia
        </div>
        <h1 class="home-hero-title">
            La mejor <span class="home-hero-accent">quesería artesanal</span><br>del campo a tu mesa
        </h1>
        <p class="home-hero-desc">
            Elaboramos quesos frescos y madurados con leche pura de la región,<br>
            usando técnicas tradicionales transmitidas de generación en generación.
        </p>
        <div class="home-hero-ctas">
            <a href="#productos" class="home-cta-primary" id="cta-ver-productos">
                <i class="bi bi-grid me-2"></i>Ver Nuestros Productos
            </a>
            <?php if (!$estaAutenticado): ?>
            <button type="button" class="home-cta-secondary border-0" data-bs-toggle="modal" data-bs-target="#modalLogin" id="cta-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </button>
            <?php endif; ?>
        </div>
    </div>
    <!-- Scroll indicator -->
    <div class="home-scroll-indicator">
        <i class="bi bi-chevron-double-down"></i>
    </div>
</section>


<!-- =============================================
     BANDA DE CONFIANZA
     ============================================= -->
<section class="home-trust">
    <div class="home-container">
        <div class="home-trust-grid">
            <div class="home-trust-item">
                <div class="home-trust-icon"><i class="bi bi-patch-check-fill"></i></div>
                <div>
                    <div class="home-trust-title">100% Leche Natural</div>
                    <div class="home-trust-sub">Sin conservantes artificiales</div>
                </div>
            </div>
            <div class="home-trust-item">
                <div class="home-trust-icon"><i class="bi bi-geo-alt-fill"></i></div>
                <div>
                    <div class="home-trust-title">Producción Local</div>
                    <div class="home-trust-sub">Directo del campo colombiano</div>
                </div>
            </div>
            <div class="home-trust-item">
                <div class="home-trust-icon"><i class="bi bi-heart-fill"></i></div>
                <div>
                    <div class="home-trust-title">Elaboración Artesanal</div>
                    <div class="home-trust-sub">Tradición en cada bloque</div>
                </div>
            </div>
            <div class="home-trust-item">
                <div class="home-trust-icon"><i class="bi bi-truck"></i></div>
                <div>
                    <div class="home-trust-title">Entrega Fresca</div>
                    <div class="home-trust-sub">Productos del día</div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- =============================================
     SECCIÓN PRODUCTOS
     ============================================= -->
<section class="home-productos" id="productos">
    <div class="home-container">

        <!-- Encabezado -->
        <div class="home-sec-head">
            <div class="home-sec-kicker">Catálogo</div>
            <h2 class="home-sec-title">Nuestros Productos</h2>
            <p class="home-sec-sub">
                Quesos frescos, hilados y madurados — tradición y calidad en cada presentación
            </p>
        </div>

        <!-- Grid de tarjetas -->
        <div class="home-cards-grid" id="home-cards-grid">
            <?php foreach ($quesos as $q):
                $disponible = (int)($q['cantidad'] ?? 1) > 0;
                $imgUrl     = $imagenesPorTipo[$q['tipo']] ?? $imgDefault;
                $precio     = '$ ' . number_format((float)$q['precio_venta'], 0, ',', '.');
            ?>
            <article class="home-card <?= !$disponible ? 'home-card--agotado' : '' ?>" id="card-<?= htmlspecialchars($q['codigo']) ?>">

                <!-- Imagen -->
                <div class="home-card-img-wrap">
                    <img src="<?= $imgUrl ?>"
                         alt="<?= htmlspecialchars($q['nombre']) ?>"
                         class="home-card-img"
                         loading="lazy">
                    <span class="home-card-status <?= $disponible ? 'home-card-status--ok' : 'home-card-status--off' ?>">
                        <?php if ($disponible): ?>
                            <i class="bi bi-check-circle-fill me-1"></i>Disponible
                        <?php else: ?>
                            <i class="bi bi-x-circle-fill me-1"></i>Agotado
                        <?php endif; ?>
                    </span>
                    <span class="home-card-tipo"><?= htmlspecialchars($q['tipo']) ?></span>
                </div>

                <!-- Cuerpo -->
                <div class="home-card-body">
                    <div class="home-card-codigo"><?= htmlspecialchars($q['codigo']) ?></div>
                    <h3 class="home-card-nombre"><?= htmlspecialchars($q['nombre']) ?></h3>
                    <p class="home-card-desc">
                        <?= htmlspecialchars($q['descripcion'] ?? 'Queso artesanal elaborado con leche fresca de la región.') ?>
                    </p>
                </div>

                <!-- Footer -->
                <div class="home-card-foot">
                    <div class="home-card-precio-wrap">
                        <span class="home-card-unidad"><?= htmlspecialchars($q['unidad_medida']) ?></span>
                        <span class="home-card-precio"><?= $precio ?></span>
                    </div>
                    <div class="home-card-actions">
                        <?php if ($disponible): 
                            $maxStock = max(1, (int)($q['cantidad'] ?? 99));
                        ?>
                            <form method="POST" action="index.php?c=carrito&a=agregar" class="home-buy-form">
                                <input type="hidden" name="producto_id" value="<?= (int)$q['id'] ?>">
                                <input type="hidden" name="redirect" value="index.php?open_cart=1#productos">
                                
                                <div class="home-qty-picker" title="Selecciona la cantidad deseada">
                                    <button type="button" class="home-qty-btn" onclick="cambiarCantidad(this, -1)">-</button>
                                    <input type="number" name="cantidad" class="home-qty-input" value="1" min="1" max="<?= $maxStock ?>" readonly>
                                    <button type="button" class="home-qty-btn" onclick="cambiarCantidad(this, 1, <?= $maxStock ?>)">+</button>
                                </div>

                                <button type="submit" class="home-btn-comprar" title="Añadir cantidad al carrito"
                                        id="agregar-<?= htmlspecialchars($q['codigo']) ?>">
                                    <i class="bi bi-cart-plus me-1"></i> Comprar
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="home-btn-agregar home-btn-agregar--lock" disabled title="Producto Agotado">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

            </article>
            <?php endforeach; ?>
        </div>

        <!-- CTA ver catálogo completo -->
        <div class="home-sec-cta">
            <a href="publico.php" class="home-cta-catalog" id="cta-catalogo-completo">
                <i class="bi bi-grid-3x3-gap me-2"></i>Ver catálogo completo
            </a>
        </div>

    </div>
</section>


<!-- =============================================
     NOSOTROS
     ============================================= -->
<section class="home-nosotros" id="nosotros">
    <div class="home-container">
        <div class="home-nosotros-grid">
            <div class="home-nosotros-text">
                <div class="home-sec-kicker home-sec-kicker--blue">Sobre Nosotros</div>
                <h2 class="home-nosotros-title">Tradición quesera<br>desde el campo colombiano</h2>
                <p class="home-nosotros-desc">
                    En <strong>Quesos San Gabriel</strong> elaboramos quesos artesanales con leche fresca
                    de la región, combinando técnicas tradicionales con calidad moderna.
                    Cada bloque es el resultado de años de experiencia y amor por el oficio.
                </p>
                <div class="home-features">
                    <div class="home-feature-item">
                        <i class="bi bi-patch-check-fill"></i>
                        <span>Leche 100% natural sin aditivos</span>
                    </div>
                    <div class="home-feature-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Producción local, sabor auténtico</span>
                    </div>
                    <div class="home-feature-item">
                        <i class="bi bi-heart-fill"></i>
                        <span>Elaboración artesanal con amor</span>
                    </div>
                    <div class="home-feature-item">
                        <i class="bi bi-award-fill"></i>
                        <span>Calidad garantizada en cada lote</span>
                    </div>
                </div>
            </div>
            <div class="home-nosotros-img-wrap">
                <div class="home-nosotros-img-frame">
                    <img src="public/img/queso-fondo.jpg"
                         alt="Elaboración artesanal de quesos San Gabriel"
                         onerror="this.src='https://images.unsplash.com/photo-1559561853-08451507cbe7?w=700&q=80'">
                </div>
            </div>
        </div>
    </div>
</section>


<!-- =============================================
     FOOTER
     ============================================= -->
<footer class="home-footer">
    <div class="home-container home-footer-inner">
        <div class="home-footer-brand">
            <span class="home-footer-emoji">🧀</span>
            <div>
                <div class="home-footer-name">Quesos San Gabriel</div>
                <div class="home-footer-tagline">Quesería Artesanal · Colombia</div>
            </div>
        </div>
        <div class="home-footer-links">
            <a href="index.php" id="footer-inicio">Inicio</a>
            <a href="#productos" id="footer-productos">Productos</a>
            <a href="publico.php" id="footer-catalogo">Catálogo</a>
            <a href="login.php" id="footer-login">Iniciar Sesión</a>
        </div>
        <div class="home-footer-copy">
            &copy; <?= date('Y') ?> Quesos San Gabriel — Todos los derechos reservados.
        </div>
    </div>
</footer>


<!-- =============================================
     OFFCANVAS: CARRITO DE COMPRAS Y FACTURACIÓN
     ============================================= -->
<div class="offcanvas offcanvas-end offcanvas-carrito" tabindex="-1" id="offcanvasCarrito" aria-labelledby="offcanvasCarritoLabel">
    <div class="offcanvas-header bg-dark text-white border-bottom border-secondary">
        <h5 class="offcanvas-title d-flex align-items-center gap-2 fw-bold" id="offcanvasCarritoLabel">
            <span>🧀</span> Mi Carrito & Facturación
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column p-0">
        <?php if (empty($carrito)): ?>
            <div class="text-center p-5 my-auto">
                <div style="font-size: 3.5rem;">🛒</div>
                <h5 class="fw-bold text-dark mt-3 mb-2">Tu carrito está vacío</h5>
                <p class="text-muted small mb-4">Selecciona cualquiera de nuestros quesos artesanales y agrégalo aquí para facturar tu pedido.</p>
                <a href="#productos" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="offcanvas">
                    <i class="bi bi-grid me-1"></i> Ver Quesos
                </a>
            </div>
        <?php else: ?>
            <!-- Lista de productos en carrito -->
            <div class="p-3 flex-grow-1 overflow-auto">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <span class="fw-bold text-dark small text-uppercase">Productos en Carrito (<?= $carritoTotal ?>)</span>
                    <form method="POST" action="index.php?c=carrito&a=vaciar" onsubmit="return confirm('¿Deseas vaciar todo el carrito?')">
                        <input type="hidden" name="redirect" value="index.php?open_cart=1#productos">
                        <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none p-0">
                            <i class="bi bi-trash"></i> Vaciar
                        </button>
                    </form>
                </div>

                <div class="carrito-items-list">
                    <?php foreach ($carrito as $id => $item): ?>
                    <div class="carrito-item-card d-flex align-items-center gap-2 p-2 mb-2 rounded bg-light border">
                        <div class="carrito-item-icon bg-white p-2 rounded shadow-sm fs-4 text-center" style="width:44px;height:44px;">🧀</div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-bold text-dark text-truncate small"><?= htmlspecialchars($item['nombre']) ?></div>
                            <div class="text-muted" style="font-size:0.75rem;">
                                <?= htmlspecialchars($item['unidad_medida'] ?? '') ?> · $ <?= number_format($item['precio_unitario'], 0, ',', '.') ?>
                            </div>
                            <div class="d-flex align-items-center gap-1 mt-1">
                                <!-- Controles cantidad -->
                                <form method="POST" action="index.php?c=carrito&a=actualizar" class="d-inline">
                                    <input type="hidden" name="redirect" value="index.php?open_cart=1#productos">
                                    <input type="hidden" name="producto_id" value="<?= $id ?>">
                                    <input type="hidden" name="cantidad" value="<?= max(1, $item['cantidad'] - 1) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-1" <?= $item['cantidad'] <= 1 ? 'disabled' : '' ?>>-</button>
                                </form>
                                <span class="fw-bold small px-2"><?= $item['cantidad'] ?></span>
                                <form method="POST" action="index.php?c=carrito&a=actualizar" class="d-inline">
                                    <input type="hidden" name="redirect" value="index.php?open_cart=1#productos">
                                    <input type="hidden" name="producto_id" value="<?= $id ?>">
                                    <input type="hidden" name="cantidad" value="<?= min((int)($item['stock_real'] ?? 999), $item['cantidad'] + 1) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-1">+</button>
                                </form>
                            </div>
                        </div>
                        <div class="text-end ps-1">
                            <div class="fw-bold text-primary small">$ <?= number_format($item['subtotal'], 0, ',', '.') ?></div>
                            <a href="index.php?c=carrito&a=quitar&id=<?= $id ?>&redirect=<?= urlencode('index.php?open_cart=1#productos') ?>" class="text-danger small" title="Eliminar"><i class="bi bi-trash"></i></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Panel de Facturación y Confirmación -->
            <div class="p-3 bg-white border-top shadow-lg">
                <div class="d-flex justify-content-between mb-1 small text-muted">
                    <span>Subtotal:</span>
                    <span>$ <?= number_format($carritoSubtotal, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2 small text-muted">
                    <span>Envío:</span>
                    <span class="text-success fw-bold">Gratis</span>
                </div>
                <div class="d-flex justify-content-between mb-3 pt-2 border-top">
                    <span class="fw-bold fs-5 text-dark">Total a Pagar:</span>
                    <span class="fw-bold fs-5 text-primary">$ <?= number_format($carritoSubtotal, 0, ',', '.') ?></span>
                </div>

                <?php if ($estaAutenticado && !$esAdmin): ?>
                    <!-- Formulario de Facturación para Cliente -->
                    <form method="POST" action="index.php?c=carrito&a=confirmar">
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted mb-1">Cliente a Facturar:</label>
                            <div class="p-2 bg-light rounded small border">
                                <i class="bi bi-person-fill text-primary me-1"></i>
                                <strong><?= htmlspecialchars($usuarioNombre) ?></strong>
                                <span class="text-muted d-block" style="font-size:0.75rem"><?= htmlspecialchars($usuarioEmail) ?></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notas_factura" class="form-label small fw-bold text-muted mb-1">Dirección de entrega / Notas:</label>
                            <textarea name="notas" id="notas_factura" class="form-control form-control-sm" rows="2" placeholder="Ej: Calle 15 # 4-30, apto 201..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow" id="btn-facturar-pedido">
                            <i class="bi bi-receipt-cutoff fs-5"></i>
                            <span>Confirmar Compra y Generar Factura</span>
                        </button>
                    </form>
                <?php elseif ($estaAutenticado && $esAdmin): ?>
                    <div class="alert alert-warning small mb-0">
                        <i class="bi bi-info-circle me-1"></i> Estás conectado como Administrador. Para emitir compras como cliente ingresa con cuenta de cliente.
                    </div>
                <?php else: ?>
                    <!-- Cliente no autenticado -->
                    <div class="p-3 bg-light rounded text-center border">
                        <div class="small fw-semibold text-dark mb-2">Para facturar y confirmar tu pedido:</div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-dismiss="offcanvas" data-bs-toggle="modal" data-bs-target="#modalLogin">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" data-bs-dismiss="offcanvas" data-bs-toggle="modal" data-bs-target="#modalRegistro">
                                <i class="bi bi-person-plus me-1"></i> Registrarse como Cliente
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Botón Flotante de Carrito -->
<?php if ($carritoTotal > 0): ?>
<button class="home-floating-cart" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCarrito" id="floatingCartBtn" title="Ver Carrito y Facturación">
    <i class="bi bi-cart3"></i>
    <span class="floating-cart-badge"><?= $carritoTotal ?></span>
</button>
<?php endif; ?>

<!-- ============================================================
     MODAL DE LOGIN (SEGUNDA IMAGEN DIRECTA)
     ============================================================ -->
<div class="modal fade modal-login" id="modalLogin" tabindex="-1" aria-labelledby="modalLoginLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                <div class="modal-logo">
                    <i class="bi bi-droplet-fill"></i>
                </div>
                <h5 class="modal-title" id="modalLoginLabel">Bienvenido</h5>
                <p class="modal-subtitle">Ingresa tus credenciales para continuar</p>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">
                <form action="login.php" method="POST" autocomplete="off">
                    <input type="hidden" name="_action" value="login">

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email"
                                   class="form-control border-start-0 ps-1"
                                   id="email"
                                   name="email"
                                   placeholder="correo@ejemplo.com"
                                   required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password"
                                   class="form-control border-start-0 ps-1"
                                   id="password"
                                   name="password"
                                   placeholder="••••••••"
                                   required>
                        </div>
                    </div>

                    <button type="submit" class="btn-modal-submit" id="btn-submit-login">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                    </button>
                </form>

                <p class="modal-switch-text">
                    ¿No tienes cuenta?
                    <a href="#" id="link-ir-registro">Regístrate aquí</a>
                </p>

                <p class="modal-footer-text">
                    &copy; <?= date('Y') ?> San Gabriel — Todos los derechos reservados.
                </p>
            </div>

        </div>
    </div>
</div>

<!-- ============================================================
     MODAL DE REGISTRO
     ============================================================ -->
<div class="modal fade modal-login modal-registro" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header modal-header-registro">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                <div class="modal-logo">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h5 class="modal-title" id="modalRegistroLabel">Crear Cuenta</h5>
                <p class="modal-subtitle">Regístrate como cliente de Quesos San Gabriel</p>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">
                <form action="registro.php" method="POST" autocomplete="off">
                    <input type="hidden" name="_action" value="registro">

                    <div class="mb-3">
                        <label for="reg_nombre" class="form-label">Nombre completo</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0 ps-1"
                                   id="reg_nombre"
                                   name="reg_nombre"
                                   placeholder="Tu nombre completo"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reg_email" class="form-label">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email"
                                   class="form-control border-start-0 ps-1"
                                   id="reg_email"
                                   name="reg_email"
                                   placeholder="correo@ejemplo.com"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reg_telefono" class="form-label">Teléfono</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="tel"
                                   class="form-control border-start-0 ps-1"
                                   id="reg_telefono"
                                   name="reg_telefono"
                                   placeholder="310 000 0000"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reg_password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password"
                                   class="form-control border-start-0 ps-1"
                                   id="reg_password"
                                   name="reg_password"
                                   placeholder="Mínimo 6 caracteres"
                                   required
                                   minlength="6">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="reg_password2" class="form-label">Confirmar contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="password"
                                   class="form-control border-start-0 ps-1"
                                   id="reg_password2"
                                   name="reg_password2"
                                   placeholder="Repite tu contraseña"
                                   required
                                   minlength="6">
                        </div>
                        <div class="password-match-msg" id="msg-password-match"></div>
                    </div>

                    <button type="submit" class="btn-modal-submit btn-modal-submit-registro" id="btn-submit-registro">
                        <i class="bi bi-person-check"></i> Crear Cuenta
                    </button>
                </form>

                <p class="modal-switch-text">
                    ¿Ya tienes cuenta?
                    <a href="#" id="link-ir-login">Inicia sesión aquí</a>
                </p>

                <p class="modal-footer-text">
                    &copy; <?= date('Y') ?> San Gabriel — Todos los derechos reservados.
                </p>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ── Navbar scroll shadow ── */
window.addEventListener('scroll', function () {
    const nav = document.getElementById('homeNavbar');
    if (nav) nav.classList.toggle('home-navbar--scrolled', window.scrollY > 40);
});

/* ── Menú hamburguesa móvil ── */
const hamburger = document.getElementById('homeHamburger');
const mobileNav = document.getElementById('homeMobileNav');
if (hamburger && mobileNav) {
    hamburger.addEventListener('click', function () {
        this.classList.toggle('open');
        mobileNav.classList.toggle('open');
    });
    mobileNav.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', () => {
            hamburger.classList.remove('open');
            mobileNav.classList.remove('open');
        });
    });
}

/* ── Animación de entrada para las tarjetas (Intersection Observer) ── */
const cards = document.querySelectorAll('.home-card');
if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                entry.target.style.transitionDelay = (i * 0.06) + 's';
                entry.target.classList.add('home-card--visible');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    cards.forEach(c => io.observe(c));
}

/* ── Control de cantidad (+ / -) en tarjetas ── */
function cambiarCantidad(btn, delta, maxStock) {
    const picker = btn.closest('.home-qty-picker');
    if (!picker) return;
    const input = picker.querySelector('.home-qty-input');
    if (!input) return;

    let actual = parseInt(input.value) || 1;
    let nuevo = actual + delta;
    if (nuevo < 1) nuevo = 1;
    if (maxStock && nuevo > maxStock) nuevo = maxStock;
    input.value = nuevo;
}

/* ── Alternar entre modalLogin y modalRegistro sin recargar ── */
function switchModal(closeId, openId) {
    var closingEl = document.getElementById(closeId);
    var openingEl = document.getElementById(openId);
    var closingModal = bootstrap.Modal.getInstance(closingEl);
    if (closingModal) {
        closingEl.addEventListener('hidden.bs.modal', function handler() {
            closingEl.removeEventListener('hidden.bs.modal', handler);
            new bootstrap.Modal(openingEl).show();
        });
        closingModal.hide();
    } else {
        new bootstrap.Modal(openingEl).show();
    }
}

const linkIrReg = document.getElementById('link-ir-registro');
if (linkIrReg) {
    linkIrReg.addEventListener('click', function(e) {
        e.preventDefault();
        switchModal('modalLogin', 'modalRegistro');
    });
}

const linkIrLog = document.getElementById('link-ir-login');
if (linkIrLog) {
    linkIrLog.addEventListener('click', function(e) {
        e.preventDefault();
        switchModal('modalRegistro', 'modalLogin');
    });
}

/* ── Validación de contraseñas en registro ── */
var pass1 = document.getElementById('reg_password');
var pass2 = document.getElementById('reg_password2');
var msg   = document.getElementById('msg-password-match');

if (pass1 && pass2 && msg) {
    function checkPasswords() {
        if (!pass2.value) { msg.textContent = ''; msg.className = 'password-match-msg'; return; }
        if (pass1.value === pass2.value) {
            msg.textContent = '✓ Las contraseñas coinciden';
            msg.className = 'password-match-msg match-ok';
        } else {
            msg.textContent = '✗ Las contraseñas no coinciden';
            msg.className = 'password-match-msg match-error';
        }
    }
    pass1.addEventListener('input', checkPasswords);
    pass2.addEventListener('input', checkPasswords);
}

/* ── Auto-abrir offcanvas de Carrito si se añade producto (open_cart=1) ── */
if (window.location.search.includes('open_cart=1')) {
    const offEl = document.getElementById('offcanvasCarrito');
    if (offEl && window.bootstrap) {
        new bootstrap.Offcanvas(offEl).show();
    }
}
</script>
</body>
</html>
