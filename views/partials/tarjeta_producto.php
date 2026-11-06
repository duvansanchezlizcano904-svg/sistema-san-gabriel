<?php
/**
 * Partial: Tarjeta de Producto
 * =====================================================================
 * Variables requeridas (definidas antes de hacer require de este partial):
 *
 *   $q    (array)  — Fila de la tabla `productos`
 *   $modo (string) — 'home' | 'publico'
 *
 * Variables de sesión disponibles globalmente:
 *   $estaAutenticado, $esAdmin
 * =====================================================================
 */

// ── Datos del producto ──────────────────────────────────────────────
$_p_disponible   = (int)($q['cantidad'] ?? 0) > 0;
$_p_categoria    = htmlspecialchars($q['categoria'] ?? $q['tipo'] ?? '');
$_p_tipoLeche    = htmlspecialchars($q['tipo_leche'] ?? 'Vaca');
$_p_intensidad   = $q['intensidad_sabor'] ?? 'medio';
$_p_origen       = htmlspecialchars($q['origen'] ?? '');
$_p_presentacion = htmlspecialchars($q['peso_presentacion'] ?? $q['unidad_medida'] ?? '');
$_p_precio       = '$ ' . number_format((float)($q['precio_venta'] ?? 0), 0, ',', '.');
$_p_id           = (int)($q['id'] ?? 0);
$_p_nombre       = htmlspecialchars($q['nombre'] ?? '');
$_p_codigo       = htmlspecialchars($q['codigo'] ?? '');
$_p_desc         = htmlspecialchars($q['descripcion'] ?? 'Queso artesanal elaborado con leche fresca de la región.');

// ── Imagen ─────────────────────────────────────────────────────────
// Prioridad: columna `imagen` (o `imagen_url`) en BD → fallback por categoría → placeholder
$_p_imgGuardada = trim($q['imagen'] ?? $q['imagen_url'] ?? '');
$_p_imgUrl      = '';
if ($_p_imgGuardada !== '') {
    if (filter_var($_p_imgGuardada, FILTER_VALIDATE_URL) || str_starts_with($_p_imgGuardada, 'http')) {
        $_p_imgUrl = $_p_imgGuardada;
    } else {
        $__base = realpath(__DIR__ . '/../../');
        if ($__base && file_exists($__base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $_p_imgGuardada))) {
            $_p_imgUrl = $_p_imgGuardada;
        }
    }
}
if ($_p_imgUrl === '') {
    $__catMap = [
        'Quesos Frescos'     => 'public/img/productos/queso_fresco.jpg',
        'Quesos Madurados'   => 'public/img/productos/parmesano.jpg',
        'Quesos Artesanales' => 'public/img/productos/queso_artesanal.jpg',
        'Quesos Cremosos'    => 'public/img/productos/brie.jpg',
        'Quesos de Cabra'    => 'public/img/productos/queso_cabra.jpg',
        'Quesos Azules'      => 'public/img/productos/queso_azul.jpg',
    ];
    $__cat = $q['categoria'] ?? $q['tipo'] ?? '';
    $_p_imgUrl = $__catMap[$__cat] ?? 'public/img/productos/queso_fresco.jpg';
}

// ── Color de intensidad ────────────────────────────────────────────
$_p_colorIntensidad = [
    'suave'      => 'success',
    'medio'      => 'warning',
    'fuerte'     => 'orange',
    'muy fuerte' => 'danger',
];
$_p_color = $_p_colorIntensidad[$_p_intensidad] ?? 'secondary';

// ── Clases CSS según modo ─────────────────────────────────────────
if ($modo === 'home') {
    // ── TARJETA HOME ────────────────────────────────────────────────
?>
<article class="home-card <?= !$_p_disponible ? 'home-card--agotado' : '' ?>"
         id="card-<?= $_p_codigo ?>"
         data-categoria="<?= $_p_categoria ?>"
         data-tipo="<?= htmlspecialchars($q['tipo'] ?? '') ?>">

    <!-- Imagen -->
    <div class="home-card-img-wrap">
        <img src="<?= htmlspecialchars($_p_imgUrl) ?>"
             alt="<?= $_p_nombre ?>"
             class="home-card-img"
             loading="lazy"
             onerror="this.src='public/img/placeholder-queso.png'">
        <span class="home-card-status <?= $_p_disponible ? 'home-card-status--ok' : 'home-card-status--off' ?>">
            <?php if ($_p_disponible): ?>
                <i class="bi bi-check-circle-fill me-1"></i>Disponible
            <?php else: ?>
                <i class="bi bi-x-circle-fill me-1"></i>Agotado
            <?php endif; ?>
        </span>
        <span class="home-card-tipo"><?= $_p_categoria ?></span>
        <?php if ($_p_disponible && $_p_id > 0): ?>
        <button type="button" class="home-card-quick-cart-btn"
                onclick="agregarDirecto(<?= $_p_id ?>, '<?= htmlspecialchars(addslashes($_p_nombre)) ?>')"
                title="Agregar al carrito">
            <i class="bi bi-cart-plus-fill"></i>
        </button>
        <?php endif; ?>
    </div>

    <!-- Cuerpo -->
    <div class="home-card-body">
        <div class="home-card-codigo"><?= $_p_codigo ?></div>
        <h3 class="home-card-nombre"><?= $_p_nombre ?></h3>
        <p class="home-card-desc"><?= $_p_desc ?></p>

        <!-- Etiquetas leche / intensidad / origen -->
        <div class="home-card-tags">
            <span class="home-card-tag home-card-tag-leche">
                <i class="bi bi-droplet-fill me-1"></i><?= $_p_tipoLeche ?>
            </span>
            <span class="home-card-tag home-card-tag-intensidad home-card-tag-intensidad-<?= htmlspecialchars($_p_intensidad) ?>">
                <i class="bi bi-fire me-1"></i><?= ucfirst($_p_intensidad) ?>
            </span>
            <?php if ($_p_origen !== ''): ?>
            <span class="home-card-tag home-card-tag-origen">
                <i class="bi bi-geo-alt me-1"></i><?= $_p_origen ?>
            </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="home-card-foot">
        <form method="POST" action="index.php?c=carrito&a=agregar" class="home-buy-form form-agregar-carrito w-100" data-nombre="<?= $_p_nombre ?>">
            <input type="hidden" name="producto_id" value="<?= $_p_id ?>">
            <input type="hidden" name="redirect"    value="index.php?open_cart=1#productos">

            <!-- Fila superior: Precio y Selector de Cantidad -->
            <div class="home-card-foot-row">
                <div class="home-card-precio-wrap">
                    <span class="home-card-unidad"><?= $_p_presentacion ?></span>
                    <span class="home-card-precio"><?= $_p_precio ?></span>
                </div>

                <?php if ($_p_disponible && $_p_id > 0): ?>
                <?php $__maxStock = max(1, (int)($q['cantidad'] ?? 99)); ?>
                <div class="home-qty-picker" title="Selecciona la cantidad">
                    <button type="button" class="home-qty-btn" onclick="cambiarCantidad(this, -1)">−</button>
                    <input type="number" name="cantidad" class="home-qty-input" value="1" min="1" max="<?= $__maxStock ?>" readonly>
                    <button type="button" class="home-qty-btn" onclick="cambiarCantidad(this, 1, <?= $__maxStock ?>)">+</button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Fila inferior: Botón Agregar al Carrito SIEMPRE VISIBLE -->
            <div class="home-card-btn-row">
                <?php if ($_p_id === 0): ?>
                    <button type="button" class="home-btn-comprar w-100" disabled title="Producto de ejemplo">
                        <i class="bi bi-hourglass-split me-1"></i> Próximamente
                    </button>
                <?php elseif ($_p_disponible): ?>
                    <button type="submit" class="home-btn-comprar w-100"
                            title="Agregar al carrito"
                            id="agregar-<?= $_p_codigo ?>">
                        <i class="bi bi-cart-plus me-1"></i> Agregar al carrito
                    </button>
                <?php else: ?>
                    <button type="button" class="home-btn-comprar home-btn-comprar--disabled w-100" disabled title="Producto Agotado">
                        <i class="bi bi-slash-circle me-1"></i> Agotado
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>

</article>

<?php } else {
    // ── TARJETA PUBLICO ─────────────────────────────────────────────
    // Preparar datos para el modal (JSON seguro)
    $__modalData = htmlspecialchars(json_encode([
        'nombre'      => $q['nombre']      ?? '',
        'tipo'        => $q['tipo']        ?? '',
        'categoria'   => $q['categoria']   ?? $q['tipo'] ?? '',
        'tipo_leche'  => $q['tipo_leche']  ?? 'Vaca',
        'intensidad'  => ucfirst($q['intensidad_sabor'] ?? 'medio'),
        'descripcion' => $q['descripcion'] ?? 'Queso artesanal elaborado con leche fresca.',
        'precio'      => $_p_precio,
        'unidad'      => $q['peso_presentacion'] ?? $q['unidad_medida'] ?? '',
        'codigo'      => $q['codigo']      ?? '',
        'disponible'  => $_p_disponible,
        'imagen'      => $_p_imgUrl,
        'id'          => $_p_id,
        'origen'      => $q['origen']          ?? 'Nacional',
        'maduracion'  => $q['tiempo_maduracion'] ?? 'N/A',
    ], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');

    // URL redirect conservando filtros activos
    $__filtrosPermitidos = ['q' => '', 'tipo' => '', 'categoria' => '', 'tipo_leche' => '', 'intensidad' => ''];
    $__filtrosActivos    = array_intersect_key($_GET ?? [], $__filtrosPermitidos);
    $__queryString       = !empty($__filtrosActivos) ? '?' . http_build_query($__filtrosActivos) : '';
    $__urlRedirect       = htmlspecialchars('publico.php' . $__queryString . '#productos');
?>
<div class="pub-card <?= !$_p_disponible ? 'pub-card--agotado' : '' ?>"
     data-categoria="<?= $_p_categoria ?>"
     data-tipo="<?= htmlspecialchars($q['tipo'] ?? '') ?>">

    <!-- ── Imagen con overlays ── -->
    <div class="pub-card-img-wrap">
        <img src="<?= htmlspecialchars($_p_imgUrl) ?>"
             alt="<?= $_p_nombre ?>"
             class="pub-card-img"
             loading="lazy"
             onerror="this.src='public/img/placeholder-queso.png'">

        <!-- Badge estado (arriba izquierda) -->
        <span class="pub-card-status <?= $_p_disponible ? 'pub-card-status--ok' : 'pub-card-status--off' ?>">
            <?php if ($_p_disponible): ?>
                <i class="bi bi-check-circle-fill"></i>Disponible
            <?php else: ?>
                <i class="bi bi-x-circle-fill"></i>Agotado
            <?php endif; ?>
        </span>

        <!-- Categoría badge (arriba derecha) -->
        <span class="pub-card-categoria"><?= $_p_categoria ?></span>

        <!-- Nombre flotante sobre imagen -->
        <div class="pub-card-nombre-img">
            <h3><?= $_p_nombre ?></h3>
            <span><?= ucfirst($_p_intensidad) ?> · <?= $_p_tipoLeche ?></span>
        </div>

        <!-- Overlay de acción central al hover -->
        <div class="pub-card-overlay">
            <button type="button"
                    class="pub-btn-ver"
                    onclick="abrirModal(<?= $__modalData ?>)">
                <i class="bi bi-eye-fill"></i>Ver detalles
            </button>
        </div>
    </div>

    <!-- ── Cuerpo ── -->
    <div class="pub-card-body">
        <div class="pub-card-codigo"><?= $_p_codigo ?></div>
        <p class="pub-card-desc"><?= $_p_desc ?></p>
        <div class="pub-card-tags">
            <span class="pub-card-tag pub-card-tag-leche">
                <i class="bi bi-droplet-fill"></i><?= $_p_tipoLeche ?>
            </span>
            <span class="pub-card-tag pub-card-tag-intensidad pub-card-tag-intensidad-<?= htmlspecialchars($_p_intensidad) ?>">
                <i class="bi bi-fire"></i><?= ucfirst($_p_intensidad) ?>
            </span>
            <?php if ($_p_origen !== ''): ?>
            <span class="pub-card-tag pub-card-tag-origen">
                <i class="bi bi-geo-alt"></i><?= $_p_origen ?>
            </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Footer ── -->
    <div class="pub-card-foot">
        <div class="pub-card-precio-wrap">
            <span class="pub-card-unidad"><?= $_p_presentacion ?></span>
            <span class="pub-card-precio"><?= $_p_precio ?></span>
        </div>

        <div class="pub-card-actions">
            <button type="button"
                    class="pub-btn-ver"
                    onclick="abrirModal(<?= $__modalData ?>)">
                <i class="bi bi-eye"></i>Ver
            </button>

            <?php if ($_p_id === 0): ?>
                <button class="pub-btn-agregar" disabled>
                    <i class="bi bi-hourglass-split"></i>Pronto
                </button>
            <?php elseif ($_p_disponible): ?>
                <form method="POST" action="index.php?c=carrito&a=agregar"
                      class="form-agregar-carrito d-inline"
                      data-nombre="<?= $_p_nombre ?>">
                    <input type="hidden" name="producto_id" value="<?= $_p_id ?>">
                    <input type="hidden" name="cantidad"    value="1">
                    <input type="hidden" name="redirect"    value="<?= $__urlRedirect ?>">
                    <button type="submit" class="pub-btn-agregar"
                            title="Agregar al carrito">
                        <i class="bi bi-cart-plus"></i>Agregar
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php } ?>

