<?php
/**
 * Quesos San Gabriel — Página de Inicio
 * Vista principal: muestra los productos sin requerir autenticación.
 */
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
                        <a href="index.php?c=carrito" class="home-nav-cart-btn" id="nav-cart-btn" style="text-decoration:none;">
                            <i class="bi bi-cart3"></i>
                            <span>Carrito</span>
                            <span class="badge bg-warning text-dark ms-1" id="nav-cart-count"><?= (int)$carritoTotal ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="home-nav-link text-danger" id="nav-cerrar">
                            <i class="bi bi-box-arrow-right me-1"></i>Salir
                        </a>
                    </li>
                <?php elseif ($estaAutenticado && $esAdmin): ?>
                    <!-- Administrador -->
                    <li>
                        <a href="index.php?c=dashboard" class="home-nav-btn" style="background:#2563eb; text-decoration:none;" id="nav-admin">
                            <i class="bi bi-speedometer2 me-1"></i>Panel Admin
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="home-nav-link" id="nav-cerrar">Cerrar Sesión</a>
                    </li>
                <?php else: ?>
                    <!-- Visitante no autenticado -->
                    <li>
                        <a href="index.php?c=carrito" class="home-nav-cart-btn" id="nav-cart-btn-guest" style="text-decoration:none;">
                            <i class="bi bi-cart3"></i>
                            <span>Carrito</span>
                            <span class="badge bg-warning text-dark ms-1" id="nav-cart-count"><?= (int)$carritoTotal ?></span>
                        </a>
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
            
            <a href="index.php?c=carrito" id="mob-carrito">
                <i class="bi bi-cart3 me-2"></i>Carrito (<span id="mob-cart-count"><?= $carritoTotal ?></span>)
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
     SECCIÓN CATÁLOGO SHOWCASE PROFESIONAL
     ============================================= -->
<section class="home-productos" id="productos">
    <div class="home-container">

        <div class="catalogo-section">
            <!-- Encabezado del catálogo -->
            <div class="catalogo-header">
                <div class="catalogo-header-left">
                    <div class="catalogo-eyebrow">
                        <i class="bi bi-grid-3x3-gap-fill me-2"></i>Inventario Completo
                    </div>
                    <h2 class="catalogo-title">
                        Catálogo de <span class="catalogo-title-quesos">Quesos</span> <span class="catalogo-title-artesanales">Artesanales</span>
                    </h2>
                    <p class="catalogo-subtitle">Colección completa de variedades disponibles en Quesos San Gabriel</p>
                </div>
            </div>

            <!-- Filtros rápidos por categoría -->
            <?php
            $categoriasLista = [];
            foreach ($quesos as $cp) {
                $c = trim($cp['categoria'] ?? '');
                if ($c && !in_array($c, $categoriasLista)) {
                    $categoriasLista[] = $c;
                }
            }
            sort($categoriasLista);
            ?>
            <div class="catalogo-filtros" id="catalogo-filtros">
                <button type="button" class="filtro-btn active" data-cat="all">
                    <i class="bi bi-grid-fill me-2"></i>Todos (<?= count($quesos) ?>)
                </button>
                <?php foreach ($categoriasLista as $cat): ?>
                    <?php
                    $cnt = count(array_filter($quesos, fn($p) => trim($p['categoria'] ?? '') === $cat));
                    ?>
                    <button type="button" class="filtro-btn" data-cat="<?= htmlspecialchars($cat) ?>">
                        <?= htmlspecialchars(ucfirst($cat)) ?> (<?= $cnt ?>)
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Grid de tarjetas de quesos -->
            <div class="catalogo-grid" id="catalogo-grid">
                <?php if (empty($quesos)): ?>
                    <div class="catalogo-empty text-center py-5 text-white">
                        <i class="bi bi-emoji-frown fs-1 text-warning d-block mb-3"></i>
                        <p class="fs-5">No hay productos activos en el catálogo.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($quesos as $prod):
                        $imgSrc      = resolverImagenProducto($prod);
                        $cat         = trim($prod['categoria'] ?? 'Sin categoría');
                        $cantidad    = (int)($prod['cantidad'] ?? 0);
                        $minStock    = (int)($prod['stock_minimo'] ?? 0);
                        $agotado     = $cantidad === 0;
                        $stockBajo   = !$agotado && $cantidad <= $minStock;
                        $precio      = isset($prod['precio_venta']) ? formatearMoneda($prod['precio_venta']) : '—';
                        $unidad      = $prod['unidad_medida'] ?? 'Unidad';
                        $presentacion = $prod['peso_presentacion'] ?? '';
                        $tipo        = strtoupper(trim($prod['tipo'] ?? 'QUESO'));
                        $intensidad  = trim($prod['intensidad_sabor'] ?? '');
                        $origen      = strtoupper(trim($prod['origen'] ?? ''));
                        $subtitulo   = $tipo . ($origen ? " · {$origen}" : '');
                        $descripcion = substr($prod['descripcion'] ?? '', 0, 95);
                        if (strlen($prod['descripcion'] ?? '') > 95) $descripcion .= '…';
                        $codigo      = $prod['codigo'] ?? '';
                        $nombre      = $prod['nombre'] ?? '';
                        $id          = (int)($prod['id'] ?? 0);
                        $esPlaceholder = str_contains($imgSrc, 'placeholder-queso') || str_contains($imgSrc, 'lac-1000');
                    ?>
                    <div class="queso-card" data-cat="<?= htmlspecialchars($cat) ?>" id="card-<?= htmlspecialchars($codigo) ?>">
                        <!-- Imagen con nombre flotante y badges -->
                        <div class="queso-card-img-wrap">
                            <img
                                src="<?= htmlspecialchars($imgSrc) ?>"
                                alt="<?= htmlspecialchars($nombre) ?>"
                                class="queso-card-img <?= $esPlaceholder ? 'queso-card-img--contain' : '' ?>"
                                loading="lazy"
                                onerror="this.src='public/img/placeholder-queso.png'"
                            >
                            <!-- Badges superiores -->
                            <div class="queso-badges-top">
                                <span class="cat-badge"><?= htmlspecialchars(strtoupper($cat)) ?></span>
                                <?php if ($agotado): ?>
                                    <span class="stock-badge agotado"><i class="bi bi-x-circle-fill"></i>Agotado</span>
                                <?php elseif ($stockBajo): ?>
                                    <span class="stock-badge bajo"><i class="bi bi-exclamation-triangle-fill"></i>Bajo</span>
                                <?php else: ?>
                                    <span class="stock-badge ok"><i class="bi bi-check-circle-fill"></i>Stock OK</span>
                                <?php endif; ?>
                            </div>

                            <!-- Nombre flotante sobre imagen -->
                            <div class="queso-img-label">
                                <div class="queso-img-nombre"><?= htmlspecialchars($nombre) ?></div>
                                <div class="queso-img-tipo"><?= htmlspecialchars($subtitulo) ?></div>
                            </div>

                            <!-- Overlay hover con acciones -->
                            <div class="queso-card-overlay">
                                <?php if ($esAdmin): ?>
                                    <a href="index.php?c=productos&a=editar&id=<?= $id ?>" class="overlay-btn">
                                        <i class="bi bi-pencil-fill"></i>Editar
                                    </a>
                                    <a href="index.php?c=ventas" class="overlay-btn overlay-btn-gold">
                                        <i class="bi bi-cart-plus-fill"></i>Vender
                                    </a>
                                <?php else: ?>
                                    <?php if (!$agotado && $id > 0): ?>
                                        <button type="button" class="overlay-btn"
                                                onclick="agregarDirecto(<?= $id ?>, '<?= htmlspecialchars(addslashes($nombre)) ?>')">
                                            <i class="bi bi-cart-plus-fill"></i>Agregar
                                        </button>
                                        <button type="button" class="overlay-btn overlay-btn-gold"
                                            onclick="abrirModalHome(<?= htmlspecialchars(json_encode([
                                                'id'          => $id,
                                                'nombre'      => $nombre,
                                                'imagen'      => $imgSrc,
                                                'categoria'   => $cat,
                                                'tipo'        => $tipo,
                                                'codigo'      => $codigo,
                                                'descripcion' => $prod['descripcion'] ?? '',
                                                'tipo_leche'  => $prod['tipo_leche'] ?? 'Vaca',
                                                'intensidad'  => $intensidad,
                                                'maduracion'  => $prod['tiempo_maduracion'] ?? '',
                                                'origen'      => $prod['origen'] ?? '',
                                                'unidad'      => $unidad,
                                                'precio'      => $precio,
                                                'disponible'  => !$agotado,
                                            ]), ENT_QUOTES, 'UTF-8') ?>)">
                                            <i class="bi bi-eye-fill"></i>Detalle
                                        </button>
                                    <?php else: ?>
                                        <span class="overlay-btn overlay-btn-disabled">
                                            <i class="bi bi-slash-circle"></i>Agotado
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Info inferior -->
                        <div class="queso-card-body">
                            <?php if ($descripcion): ?>
                                <p class="queso-card-desc"><?= htmlspecialchars($descripcion) ?></p>
                            <?php endif; ?>
                            <div class="queso-card-detalles">
                                <?php if ($intensidad): ?>
                                <div class="detalle-item">
                                    <i class="bi <?= (strtolower($intensidad) === 'muy fuerte' || strtolower($intensidad) === 'fuerte') ? 'bi-fire text-danger' : 'bi-thermometer-half text-warning' ?>"></i>
                                    <span><?= htmlspecialchars(ucfirst($intensidad)) ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="detalle-item">
                                    <i class="bi bi-archive text-primary"></i>
                                    <span><?= number_format($cantidad) ?> <?= htmlspecialchars($presentacion ?: $unidad) ?></span>
                                </div>
                            </div>
                            <div class="queso-card-footer">
                                <div class="queso-precio">
                                    <span class="precio-label">Precio venta</span>
                                    <span class="precio-valor"><?= $precio ?></span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!$esAdmin && !$agotado && $id > 0): ?>
                                    <button type="button" class="btn-card-quick-cart"
                                            onclick="agregarDirecto(<?= $id ?>, '<?= htmlspecialchars(addslashes($nombre)) ?>')"
                                            title="Agregar al carrito">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div><!-- /catalogo-grid -->
        </div><!-- /catalogo-section -->

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
<a href="index.php?c=carrito" class="home-floating-cart <?= $carritoTotal > 0 ? '' : 'd-none' ?>" id="floatingCartBtn" title="Ver Carrito de Compras">
    <i class="bi bi-cart3"></i>
    <span class="floating-cart-badge" id="floating-cart-count"><?= $carritoTotal ?></span>
</a>

<!-- Toast Flotante de Confirmación de Carrito -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
    <div id="homeCartToast" class="toast align-items-center text-bg-dark border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" id="homeToastMessage">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <span>Producto agregado al carrito.</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
    </div>
</div>

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

/* ── Barra de desplazamiento y navegación horizontal (home) ── */
const scrollContainer = document.getElementById('home-cards-scroll-container');
const btnScrollLeft   = document.getElementById('btn-scroll-left');
const btnScrollRight  = document.getElementById('btn-scroll-right');
const btnToggleGrid   = document.getElementById('btn-toggle-grid');
const iconToggleGrid  = document.getElementById('icon-toggle-grid');
const textToggleGrid  = document.getElementById('text-toggle-grid');
const progressIndicator = document.getElementById('home-scroll-progress-indicator');
const visibleCounter  = document.getElementById('visible-products-count');

function actualizarScrollStatus() {
    if (!scrollContainer) return;
    const maxScroll = scrollContainer.scrollWidth - scrollContainer.clientWidth;
    const current = scrollContainer.scrollLeft;

    if (btnScrollLeft) {
        btnScrollLeft.disabled = current <= 5;
    }
    if (btnScrollRight) {
        btnScrollRight.disabled = maxScroll <= 0 || current >= maxScroll - 5;
    }
    if (progressIndicator) {
        if (maxScroll <= 0) {
            progressIndicator.style.width = '100%';
        } else {
            const pct = Math.min(100, Math.max(12, ((current + scrollContainer.clientWidth) / scrollContainer.scrollWidth) * 100));
            progressIndicator.style.width = pct + '%';
        }
    }
}

if (scrollContainer) {
    scrollContainer.addEventListener('scroll', actualizarScrollStatus, { passive: true });
    window.addEventListener('resize', actualizarScrollStatus);
    setTimeout(actualizarScrollStatus, 150);

    // Botones de desplazamiento
    if (btnScrollLeft) {
        btnScrollLeft.addEventListener('click', () => {
            const scrollAmount = Math.max(280, scrollContainer.clientWidth * 0.75);
            scrollContainer.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });
    }
    if (btnScrollRight) {
        btnScrollRight.addEventListener('click', () => {
            const scrollAmount = Math.max(280, scrollContainer.clientWidth * 0.75);
            scrollContainer.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });
    }

    // Arrastre con el mouse
    let isMouseDown = false;
    let startX, startScrollLeft;

    scrollContainer.addEventListener('mousedown', (e) => {
        if (e.target.closest('button, a, input, form')) return;
        isMouseDown = true;
        scrollContainer.style.cursor = 'grabbing';
        scrollContainer.style.userSelect = 'none';
        startX = e.pageX - scrollContainer.offsetLeft;
        startScrollLeft = scrollContainer.scrollLeft;
    });

    window.addEventListener('mouseup', () => {
        if (isMouseDown) {
            isMouseDown = false;
            if (scrollContainer) {
                scrollContainer.style.cursor = '';
                scrollContainer.style.removeProperty('user-select');
            }
        }
    });

    scrollContainer.addEventListener('mousemove', (e) => {
        if (!isMouseDown) return;
        e.preventDefault();
        const x = e.pageX - scrollContainer.offsetLeft;
        const walk = (x - startX) * 1.5;
        scrollContainer.scrollLeft = startScrollLeft - walk;
    });

    // Alternar entre modo desplazamiento y cuadrícula completa
    if (btnToggleGrid) {
        btnToggleGrid.addEventListener('click', () => {
            const isGrid = scrollContainer.classList.toggle('is-grid-mode');
            if (isGrid) {
                iconToggleGrid.className = 'bi bi-arrows-expand me-1';
                textToggleGrid.textContent = 'Ver con desplazamiento';
                if (btnScrollLeft) btnScrollLeft.style.display = 'none';
                if (btnScrollRight) btnScrollRight.style.display = 'none';
                if (progressIndicator) progressIndicator.parentElement.style.display = 'none';
            } else {
                iconToggleGrid.className = 'bi bi-grid-3x3-gap-fill me-1';
                textToggleGrid.textContent = 'Ver en cuadrícula';
                if (btnScrollLeft) btnScrollLeft.style.display = '';
                if (btnScrollRight) btnScrollRight.style.display = '';
                if (progressIndicator) progressIndicator.parentElement.style.display = '';
                actualizarScrollStatus();
            }
        });
    }
}

/* ── Filtrado interactivo por pestañas de categorías (Showcase) ── */
(function() {
    const filtros = document.querySelectorAll('#catalogo-filtros .filtro-btn');
    const cards   = document.querySelectorAll('#catalogo-grid .queso-card');

    filtros.forEach(btn => {
        btn.addEventListener('click', function() {
            filtros.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const cat = (this.dataset.cat || '').trim().toLowerCase();
            cards.forEach(card => {
                const cardCat = (card.dataset.cat || '').trim().toLowerCase();
                const show = cat === 'all' || cat === 'todos' || cardCat === cat;
                card.style.animation = 'none';
                card.style.display   = show ? '' : 'none';
                if (show) {
                    requestAnimationFrame(() => {
                        card.style.animation = '';
                        card.classList.add('card-entering');
                        setTimeout(() => card.classList.remove('card-entering'), 400);
                    });
                }
            });
        });
    });
})();

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

/* ── Interceptar 'Agregar al Carrito' para feedback instantáneo y actualización de contador ── */
document.querySelectorAll('.form-agregar-carrito').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const prodNombre = this.getAttribute('data-nombre') || 'Producto';
        const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Agregando...';
        }

        const formData = new FormData(this);
        formData.append('ajax', '1');

        fetch('index.php?c=carrito&a=agregar', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }

            if (data.success) {
                // Actualizar contadores del carrito
                const navCount = document.getElementById('nav-cart-count');
                if (navCount) navCount.textContent = data.total_items;

                const mobCount = document.getElementById('mob-cart-count');
                if (mobCount) mobCount.textContent = data.total_items;

                const floatBtn = document.getElementById('floatingCartBtn');
                const floatCount = document.getElementById('floating-cart-count');
                if (floatBtn && floatCount) {
                    floatCount.textContent = data.total_items;
                    floatBtn.classList.remove('d-none');
                }

                mostrarToastHome('✓ Producto agregado al carrito (' + prodNombre + ')', true);
            } else {
                mostrarToastHome(data.mensaje || 'No se pudo agregar el producto.', false);
            }
        })
        .catch(err => {
            console.error('Error al agregar al carrito:', err);
            // Fallback en caso de error
            form.submit();
        });
    });
});

function agregarDirecto(productoId, prodNombre) {
    const formData = new FormData();
    formData.append('ajax', '1');
    formData.append('producto_id', productoId);
    formData.append('cantidad', '1');

    fetch('index.php?c=carrito&a=agregar', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const navCount = document.getElementById('nav-cart-count');
            if (navCount) navCount.textContent = data.total_items;

            const mobCount = document.getElementById('mob-cart-count');
            if (mobCount) mobCount.textContent = data.total_items;

            const floatBtn = document.getElementById('floatingCartBtn');
            const floatCount = document.getElementById('floating-cart-count');
            if (floatBtn && floatCount) {
                floatCount.textContent = data.total_items;
                floatBtn.classList.remove('d-none');
            }

            mostrarToastHome('✓ Producto agregado al carrito (' + prodNombre + ')', true);
        } else {
            mostrarToastHome(data.mensaje || 'No se pudo agregar el producto.', false);
        }
    })
    .catch(err => {
        console.error(err);
        window.location.href = 'index.php?c=carrito';
    });
}

function mostrarToastHome(mensaje, esExito = true) {
    const toastEl = document.getElementById('homeCartToast');
    const toastMsg = document.getElementById('homeToastMessage');
    if (!toastEl || !toastMsg) return;

    toastMsg.innerHTML = `<i class="bi ${esExito ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning'} fs-5"></i> <span>${mensaje}</span>`;
    const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
    toast.show();
}

/* ── Botón Cargar más productos (estilo tabla) ── */
(function() {
    const loadMoreBtn = document.getElementById('home-load-more-btn');
    const loadMoreWrapper = document.getElementById('home-load-more-wrapper');
    const spinner = document.getElementById('home-load-more-spinner');
    const tbody = document.getElementById('home-products-tbody');
    
    if (!loadMoreBtn || !tbody) return;
    
    let currentPage = 1;
    let isLoading = false;
    let productosCargados = <?= count($quesos) ?>;
    let totalProductos = <?= $totalProductos ?? 0 ?>;
    
    async function cargarMasProductos() {
        if (isLoading) return;
        
        isLoading = true;
        loadMoreBtn.style.display = 'none';
        spinner.style.display = 'flex';
        
        try {
            const params = new URLSearchParams({
                c: 'publico',
                a: 'cargarMasProductos',
                pagina: currentPage + 1,
                por_pagina: 20
            });
            
            const response = await fetch('index.php?' + params.toString());
            const data = await response.json();
            
            if (data.success && data.html) {
                // Agregar las nuevas filas a la tabla
                tbody.insertAdjacentHTML('beforeend', data.html);
                
                // Actualizar contadores
                currentPage = data.pagina;
                productosCargados += data.mostrados;
                
                // Actualizar el contador de productos
                const infoEl = document.querySelector('.home-products-info span strong');
                if (infoEl) {
                    infoEl.textContent = productosCargados;
                }
                
                // Ocultar botón si no hay más productos
                if (!data.hayMas && loadMoreWrapper) {
                    loadMoreWrapper.innerHTML = `
                        <div class="home-no-more-products">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span>¡Has visto todos los ${totalProductos} productos!</span>
                        </div>
                    `;
                }
            }
        } catch (error) {
            console.error('Error al cargar más productos:', error);
            loadMoreBtn.style.display = 'inline-flex';
        } finally {
            spinner.style.display = 'none';
            isLoading = false;
        }
    }
    
    loadMoreBtn.addEventListener('click', cargarMasProductos);
    
    // También cargar más cuando se hace scroll hacia abajo
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        if (scrollTimeout) clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            const scrollPosition = window.innerHeight + window.scrollY;
            const pageHeight = document.documentElement.scrollHeight;
            
            if (pageHeight - scrollPosition < 400 && !isLoading && loadMoreBtn && loadMoreBtn.style.display !== 'none') {
                cargarMasProductos();
            }
        }, 100);
    });
})();
</script>

<!-- =============================================
     MODAL "VER PRODUCTO" — HOME
     ============================================= -->
<div class="pub-modal-overlay" id="homeModalOverlay" onclick="cerrarModalHome()">
    <div class="pub-modal" id="homeModal" onclick="event.stopPropagation()">
        <button class="pub-modal-close" onclick="cerrarModalHome()">
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="pub-modal-img-wrap">
            <img src="" alt="" id="homeModalImg" class="pub-modal-img">
            <span class="pub-modal-tipo" id="homeModalCategoria"></span>
        </div>
        <div class="pub-modal-body">
            <h2 class="pub-modal-nombre" id="homeModalNombre"></h2>
            <p class="pub-modal-desc" id="homeModalDesc"></p>

            <div class="pub-modal-caracteristicas">
                <div class="pub-modal-caract-item">
                    <i class="bi bi-droplet-fill text-primary"></i>
                    <span class="pub-modal-caract-label">Tipo de Leche:</span>
                    <span class="pub-modal-caract-value" id="homeModalTipoLeche"></span>
                </div>
                <div class="pub-modal-caract-item">
                    <i class="bi bi-fire text-danger"></i>
                    <span class="pub-modal-caract-label">Intensidad:</span>
                    <span class="pub-modal-caract-value" id="homeModalIntensidad"></span>
                </div>
                <div class="pub-modal-caract-item">
                    <i class="bi bi-clock-history text-warning"></i>
                    <span class="pub-modal-caract-label">Maduración:</span>
                    <span class="pub-modal-caract-value" id="homeModalMaduracion"></span>
                </div>
                <div class="pub-modal-caract-item">
                    <i class="bi bi-geo-alt-fill text-success"></i>
                    <span class="pub-modal-caract-label">Origen:</span>
                    <span class="pub-modal-caract-value" id="homeModalOrigen"></span>
                </div>
            </div>

            <div class="pub-modal-meta">
                <div>
                    <div class="pub-modal-meta-label">Presentación</div>
                    <div class="pub-modal-meta-val" id="homeModalUnidad"></div>
                </div>
                <div>
                    <div class="pub-modal-meta-label">Precio</div>
                    <div class="pub-modal-precio" id="homeModalPrecio"></div>
                </div>
                <div>
                    <div class="pub-modal-meta-label">Estado</div>
                    <div id="homeModalEstado"></div>
                </div>
            </div>
            <div id="homeModalAccion" class="pub-modal-accion"></div>
        </div>
    </div>
</div>

<script>
/* ── Modal Detalle — Home ── */
function abrirModalHome(p) {
    document.getElementById('homeModalImg').src        = p.imagen;
    document.getElementById('homeModalImg').alt        = p.nombre;
    document.getElementById('homeModalCategoria').textContent = p.categoria || p.tipo;
    document.getElementById('homeModalNombre').textContent    = p.nombre;
    document.getElementById('homeModalDesc').textContent      = p.descripcion;
    document.getElementById('homeModalUnidad').textContent    = p.unidad;
    document.getElementById('homeModalPrecio').textContent    = p.precio;
    document.getElementById('homeModalTipoLeche').textContent  = p.tipo_leche  || 'Vaca';
    document.getElementById('homeModalIntensidad').textContent = p.intensidad  || 'Medio';
    document.getElementById('homeModalMaduracion').textContent = p.maduracion  || 'N/A';
    document.getElementById('homeModalOrigen').textContent     = p.origen      || 'Nacional';

    const estadoEl = document.getElementById('homeModalEstado');
    estadoEl.innerHTML = p.disponible
        ? '<span class="pub-modal-ok"><i class="bi bi-check-circle-fill me-1"></i>Disponible</span>'
        : '<span class="pub-modal-off"><i class="bi bi-x-circle-fill me-1"></i>Agotado</span>';

    const accionEl = document.getElementById('homeModalAccion');
    const esAdmin  = <?= ($esAdmin ?? false) ? 'true' : 'false' ?>;

    if (p.disponible && !esAdmin) {
        accionEl.innerHTML = `
            <form method="POST" action="index.php?c=carrito&a=agregar">
                <input type="hidden" name="producto_id" value="${p.id}">
                <input type="hidden" name="cantidad"    value="1">
                <input type="hidden" name="redirect"    value="index.php">
                <button type="submit" class="pub-modal-btn-agregar">
                    <i class="bi bi-cart-plus me-2"></i>Agregar al carrito
                </button>
            </form>`;
    } else if (!p.disponible) {
        accionEl.innerHTML = '<span class="pub-modal-sin-stock">Sin stock disponible</span>';
    } else {
        accionEl.innerHTML = '';
    }

    document.getElementById('homeModalOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function cerrarModalHome() {
    document.getElementById('homeModalOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') cerrarModalHome();
});
</script>
</body>
</html>
