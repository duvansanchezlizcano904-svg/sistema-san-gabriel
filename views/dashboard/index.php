<?php
/**
 * Dashboard — Quesos San Gabriel
 */
require_once __DIR__ . '/../layouts/header.php';

/**
 * Mapeo de imagen por nombre de producto / código
 */
if (!function_exists('resolverImagenProducto')) {
    function resolverImagenProducto(array $p): string {
        $mapa = [
            'brie'         => 'brie.jpg',
            'burrata'      => 'burrata.jpg',
            'cheddar'      => 'cheddar.jpg',
            'cuajada'      => 'cuajada.jpg',
            'doble crema'  => 'doble_crema.jpg',
            'doble_crema'  => 'doble_crema.jpg',
            'mozzarella'   => 'mozzarella.jpg',
            'mozarela'     => 'mozzarella.jpg',
            'parmesano'    => 'parmesano.jpg',
            'provolone'    => 'provolone.jpg',
            'quesillo'     => 'quesillo_hoja.jpg',
            'artesanal'    => 'queso_artesanal.jpg',
            'azul'         => 'queso_azul.jpg',
            'cabra'        => 'queso_cabra.jpg',
            'costeño'      => 'queso_costeno.jpg',
            'costeno'      => 'queso_costeno.jpg',
            'fresco'       => 'queso_fresco.jpg',
            'trufa'        => 'queso_trufa.jpg',
            'lac'          => 'lac-1000.png',
        ];
        if (!empty($p['imagen'])) {
            $ruta = 'public/img/productos/' . $p['imagen'];
            if (file_exists($ruta)) return $ruta;
        }
        $nombreLower = strtolower($p['nombre'] ?? '');
        foreach ($mapa as $clave => $archivo) {
            if (str_contains($nombreLower, $clave)) {
                return 'public/img/productos/' . $archivo;
            }
        }
        return 'public/img/placeholder-queso.png';
    }
}

if (!function_exists('categoriaBadge')) {
    function categoriaBadge(string $cat): string {
        $colores = [
            'fresco'   => ['#10b981','#d1fae5'],
            'madurado' => ['#d97706','#fef3c7'],
            'azul'     => ['#6366f1','#e0e7ff'],
            'suave'    => ['#0ea5e9','#e0f2fe'],
            'duro'     => ['#78716c','#f5f5f4'],
            'artesanal'=> ['#ec4899','#fce7f3'],
        ];
        $lc = strtolower($cat);
        [$color,$bg] = $colores[$lc] ?? ['#64748b','#f1f5f9'];
        return "<span class='cat-badge' style='background:{$bg};color:{$color};'>" . htmlspecialchars($cat) . "</span>";
    }
}
?>

<!-- HERO -->
<div class="dashboard-hero mb-4">
    <img class="hero-bg-img"
         src="public/img/queso-fondo.jpg" alt=""
         onerror="this.src='https://images.unsplash.com/photo-1452195100486-9cc805987862?w=1400&q=80'">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <i class="bi bi-award-fill me-1"></i> Panel de Administración
        </div>
        <h1 class="hero-title">
            Quesos <span class="highlight">San Gabriel</span>
        </h1>
        <p class="hero-subtitle">Gestión de inventario, ventas y reportes de tu quesería.</p>
        <div class="d-flex flex-wrap gap-2">
            <a href="index.php?c=ventas" class="btn btn-light d-flex align-items-center gap-2">
                <i class="bi bi-cart-plus text-primary"></i> Registrar Venta
            </a>
            <a href="index.php?c=productos&a=crear" class="btn btn-outline-light d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> Nuevo Queso
            </a>
            <a href="publico.php" class="btn btn-outline-light d-flex align-items-center gap-2" target="_blank">
                <i class="bi bi-globe"></i> Ver Tienda
            </a>
        </div>
    </div>
    <div class="hero-label-corner">
        <span class="hero-label-text">Variedades</span>
        <span class="hero-label-number"><?= number_format($statsProductos['total_productos']) ?></span>
    </div>
</div>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-hero">
            <div class="stat-card-accent accent-blue"></div>
            <div class="stat-icon primary"><i class="bi bi-box-seam"></i></div>
            <div class="stat-value"><?= number_format($statsProductos['total_productos']) ?></div>
            <div class="stat-label">Total Quesos</div>
            <div class="text-muted small mt-2">
                <span class="text-success fw-semibold">
                    <i class="bi bi-check2-circle"></i> <?= $statsProductos['disponibles'] ?>
                </span> con stock óptimo
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-hero">
            <div class="stat-card-accent accent-green"></div>
            <div class="stat-icon success"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value text-success"><?= number_format($statsProductos['disponibles']) ?></div>
            <div class="stat-label">Stock Disponible</div>
            <div class="text-muted small mt-2">Listos para venta</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-hero">
            <div class="stat-card-accent accent-yellow"></div>
            <div class="stat-icon warning"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="stat-value text-warning"><?= number_format($statsProductos['stock_bajo']) ?></div>
            <div class="stat-label">Stock Bajo</div>
            <div class="text-muted small mt-2">
                <span class="text-danger fw-semibold"><?= $statsProductos['agotados'] ?> agotados</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card-hero">
            <div class="stat-card-accent accent-blue"></div>
            <div class="stat-icon primary"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-value"><?= formatearMoneda($statsVentas['total_ingresos']) ?></div>
            <div class="stat-label">Total en Ventas</div>
            <div class="text-muted small mt-2"><?= $statsVentas['total_ventas'] ?> transacciones</div>
        </div>
    </div>
</div>

<!-- TABLAS -->
<div class="row g-4">

    <!-- Últimas ventas -->
    <div class="col-12 col-lg-8">
        <div class="card-lacteo">
            <div class="card-header-clean">
                <h5 class="card-title-clean">
                    <i class="bi bi-clock-history text-primary me-2"></i> Últimas Ventas
                </h5>
                <a href="index.php?c=historial" class="btn btn-sm btn-outline-secondary">Ver Todo</a>
            </div>
            <div class="table-responsive">
                <table class="table table-lacteo align-middle">
                    <thead>
                        <tr>
                            <th>N° Venta</th><th>Fecha</th><th>Cliente</th>
                            <th class="text-end">Total</th><th class="text-center">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ultimasVentas)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                    No hay ventas registradas aún.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ultimasVentas as $v): ?>
                                <tr>
                                    <td><span class="fw-bold text-primary"><?= htmlspecialchars($v['numero_venta']) ?></span></td>
                                    <td><?= formatearFecha($v['fecha']) ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($v['cliente']) ?></div>
                                        <small class="text-muted"><?= $v['cantidad_lineas'] ?> producto(s)</small>
                                    </td>
                                    <td class="text-end fw-bold"><?= formatearMoneda($v['total']) ?></td>
                                    <td class="text-center">
                                        <a href="index.php?c=historial&a=detalle&id=<?= $v['id'] ?>"
                                           class="btn btn-sm btn-light border text-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Panel derecho -->
    <div class="col-12 col-lg-4 d-flex flex-column gap-4">

        <!-- Alertas de stock -->
        <div class="card-lacteo">
            <div class="card-header-clean">
                <h5 class="card-title-clean">
                    <i class="bi bi-bell text-warning me-2"></i> Alerta de Stock
                </h5>
                <a href="index.php?c=inventario&estado_stock=bajo" class="btn btn-sm btn-outline-secondary">Revisar</a>
            </div>
            <div class="p-3">
                <?php if (empty($productosCriticos)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-2"></i>
                        <span>¡Todos los quesos tienen stock suficiente!</span>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($productosCriticos, 0, 5) as $pc):
                            $estado = calcularEstadoStock((int)$pc['cantidad'], (int)$pc['stock_minimo']);
                        ?>
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($pc['nombre']) ?></div>
                                    <small class="text-muted">Mín: <?= $pc['stock_minimo'] ?> <?= $pc['unidad_medida'] ?></small>
                                </div>
                                <span class="<?= $estado['clase'] ?>"><?= $pc['cantidad'] ?> <?= $pc['unidad_medida'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Atajos -->
        <div class="card-lacteo p-3">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-warning"></i> Atajos Rápidos
            </h6>
            <div class="d-grid gap-2">
                <a href="index.php?c=inventario" class="btn btn-white border text-start d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-clipboard-data text-primary me-2"></i> Ver Inventario</span>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="index.php?c=proveedores" class="btn btn-white border text-start d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-truck text-info me-2"></i> Proveedores</span>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="index.php?c=reportes" class="btn btn-white border text-start d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-printer text-secondary me-2"></i> Imprimir Reporte</span>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="publico.php" class="btn btn-white border text-start d-flex align-items-center justify-content-between" target="_blank">
                    <span><i class="bi bi-globe text-success me-2"></i> Ver Tienda Pública</span>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>

    </div>
</div>


<!-- ══════════════════════════════════════════════════════
     CATÁLOGO DE QUESOS — SHOWCASE PROFESIONAL
═══════════════════════════════════════════════════════════ -->
<div class="catalogo-section mt-5">

    <!-- Encabezado del catálogo -->
    <div class="catalogo-header">
        <div class="catalogo-header-left">
            <div class="catalogo-eyebrow">
                <i class="bi bi-grid-3x3-gap-fill me-2"></i>Inventario Completo
            </div>
            <h2 class="catalogo-title">Catálogo de <span class="catalogo-title-quesos">Quesos</span> <span class="catalogo-title-artesanales">Artesanales</span></h2>
            <p class="catalogo-subtitle">Colección completa de variedades disponibles en Quesos San Gabriel</p>
        </div>
    </div>

    <!-- Filtros rápidos por categoría -->
    <?php
    $categorias = [];
    foreach ($catalogoProductos as $cp) {
        $cat = trim($cp['categoria'] ?? '');
        if ($cat && !in_array($cat, $categorias)) $categorias[] = $cat;
    }
    sort($categorias);
    ?>
    <div class="catalogo-filtros" id="catalogo-filtros">
        <button class="filtro-btn active" data-cat="all">
            <i class="bi bi-grid me-1"></i>Todos (<?= count($catalogoProductos) ?>)
        </button>
        <?php foreach ($categorias as $cat): ?>
            <?php $cnt = count(array_filter($catalogoProductos, fn($p) => trim($p['categoria'] ?? '') === $cat)); ?>
            <button class="filtro-btn" data-cat="<?= htmlspecialchars($cat) ?>">
                <?= htmlspecialchars(ucfirst($cat)) ?> (<?= $cnt ?>)
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Grid de tarjetas de quesos -->
    <div class="catalogo-grid" id="catalogo-grid">
        <?php if (empty($catalogoProductos)): ?>
            <div class="catalogo-empty">
                <i class="bi bi-emoji-frown"></i>
                <p>No hay productos activos en el catálogo.</p>
                <a href="index.php?c=productos&a=crear" class="btn btn-primary mt-2">Agregar primer queso</a>
            </div>
        <?php else: ?>
            <?php foreach ($catalogoProductos as $prod):
                $imgSrc   = resolverImagenProducto($prod);
                $cat      = trim($prod['categoria'] ?? 'Sin categoría');
                $cantidad = (int)($prod['cantidad'] ?? 0);
                $minStock = (int)($prod['stock_minimo'] ?? 0);
                $agotado  = $cantidad === 0;
                $stockBajo = !$agotado && $cantidad <= $minStock;
                $precio   = isset($prod['precio_venta']) ? formatearMoneda($prod['precio_venta']) : '—';
                $unidad   = $prod['unidad_medida'] ?? 'u';
                $tipo     = ucfirst($prod['tipo'] ?? '');
                $intensidad = $prod['intensidad_sabor'] ?? '';
                $origen   = $prod['origen'] ?? '';
                $descripcion = substr($prod['descripcion'] ?? '', 0, 90);
                if (strlen($prod['descripcion'] ?? '') > 90) $descripcion .= '…';
                $esPlaceholder = str_contains($imgSrc, 'placeholder-queso') || str_contains($imgSrc, 'lac-1000');
            ?>
            <div class="queso-card" data-cat="<?= htmlspecialchars($cat) ?>">
                <!-- Imagen con nombre flotante -->
                <div class="queso-card-img-wrap">
                    <img
                        src="<?= htmlspecialchars($imgSrc) ?>"
                        alt="<?= htmlspecialchars($prod['nombre']) ?>"
                        class="queso-card-img <?= $esPlaceholder ? 'queso-card-img--contain' : '' ?>"
                        loading="lazy"
                        onerror="this.src='public/img/placeholder-queso.png'"
                    >
                    <!-- Badges superiores -->
                    <div class="queso-badges-top">
                        <?php echo categoriaBadge($cat); ?>
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
                        <div class="queso-img-nombre"><?= htmlspecialchars($prod['nombre']) ?></div>
                        <div class="queso-img-tipo"><?= htmlspecialchars($tipo) ?><?= $origen ? " · {$origen}" : '' ?></div>
                    </div>
                    <!-- Overlay hover con acciones -->
                    <div class="queso-card-overlay">
                        <a href="index.php?c=productos&a=editar&id=<?= $prod['id'] ?>" class="overlay-btn">
                            <i class="bi bi-pencil-fill"></i>Editar
                        </a>
                        <a href="index.php?c=ventas" class="overlay-btn overlay-btn-gold">
                            <i class="bi bi-cart-plus-fill"></i>Vender
                        </a>
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
                            <i class="bi bi-thermometer-half text-warning"></i>
                            <span><?= htmlspecialchars(ucfirst($intensidad)) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="detalle-item">
                            <i class="bi bi-archive text-primary"></i>
                            <span><?= number_format($cantidad) ?> <?= htmlspecialchars($unidad) ?></span>
                        </div>
                    </div>
                    <div class="queso-card-footer">
                        <div class="queso-precio">
                            <span class="precio-label">Precio venta</span>
                            <span class="precio-valor"><?= $precio ?></span>
                        </div>
                        <span class="queso-codigo"><?= htmlspecialchars($prod['codigo'] ?? '') ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div><!-- /catalogo-grid -->
</div><!-- /catalogo-section -->

<!-- Script del filtro de catálogo -->
<script>
(function() {
    const filtros = document.querySelectorAll('#catalogo-filtros .filtro-btn');
    const cards   = document.querySelectorAll('#catalogo-grid .queso-card');

    filtros.forEach(btn => {
        btn.addEventListener('click', () => {
            filtros.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const cat = btn.dataset.cat;
            cards.forEach(card => {
                const show = cat === 'all' || card.dataset.cat === cat;
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
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
