<?php
/**
 * Vista Catálogo — Quesos San Gabriel
 * Para clientes autenticados y administradores.
 */
require_once __DIR__ . '/../layouts/header.php';
$carrito      = $_SESSION['carrito'] ?? [];
$carritoItems = array_sum(array_column($carrito, 'cantidad'));
?>

<!-- Encabezado -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-shop text-primary me-2"></i> Catálogo de Quesos
        </h2>
        <p class="text-muted mb-0">Explora nuestras variedades artesanales — frescos, hilados y madurados.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <?php if (!is_admin() && $carritoItems > 0): ?>
            <a href="index.php?c=carrito" class="btn btn-warning fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-cart3"></i> Mi Carrito
                <span class="badge bg-dark"><?= $carritoItems ?></span>
            </a>
        <?php endif; ?>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6">
            <i class="bi bi-tag-fill me-1"></i> Precios de Venta
        </span>
    </div>
</div>

<!-- Filtros avanzados -->
<div class="card-lacteo mb-4 p-3">
    <form action="index.php" method="GET" class="row g-3 align-items-end">
        <input type="hidden" name="c" value="catalogo">
        
        <!-- Búsqueda por texto -->
        <div class="col-12 col-md-4">
            <label class="form-label fw-semibold small text-muted text-uppercase">
                <i class="bi bi-search me-1"></i>Buscar
            </label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control"
                       placeholder="Nombre o descripción..."
                       value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
        </div>

        <!-- Categoría -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label fw-semibold small text-muted text-uppercase">
                <i class="bi bi-grid-3x3-gap me-1"></i>Categoría
            </label>
            <select name="categoria" class="form-select">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"
                        <?= (isset($_GET['categoria']) && $_GET['categoria'] === $cat) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Tipo de Leche -->
        <div class="col-12 col-sm-6 col-md-2">
            <label class="form-label fw-semibold small text-muted text-uppercase">
                <i class="bi bi-droplet me-1"></i>Leche
            </label>
            <select name="tipo_leche" class="form-select">
                <option value="">Todas</option>
                <?php foreach ($tiposLeche as $tl): ?>
                    <option value="<?= htmlspecialchars($tl) ?>"
                        <?= (isset($_GET['tipo_leche']) && $_GET['tipo_leche'] === $tl) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tl) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Intensidad -->
        <div class="col-12 col-sm-6 col-md-2">
            <label class="form-label fw-semibold small text-muted text-uppercase">
                <i class="bi bi-fire me-1"></i>Intensidad
            </label>
            <select name="intensidad" class="form-select">
                <option value="">Todas</option>
                <?php foreach ($intensidades as $int): ?>
                    <option value="<?= htmlspecialchars($int) ?>"
                        <?= (isset($_GET['intensidad']) && $_GET['intensidad'] === $int) ? 'selected' : '' ?>>
                        <?= ucfirst($int) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Tipo tradicional (opcional, más específico) -->
        <div class="col-12 col-sm-6 col-md-3 d-none d-lg-block">
            <label class="form-label fw-semibold small text-muted text-uppercase">
                <i class="bi bi-tag me-1"></i>Tipo
            </label>
            <select name="tipo" class="form-select">
                <option value="">Todos los tipos</option>
                <?php foreach ($tiposDisponibles as $t): ?>
                    <option value="<?= htmlspecialchars($t) ?>"
                        <?= (isset($_GET['tipo']) && $_GET['tipo'] === $t) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Botones -->
        <div class="col-12 col-md-auto ms-auto d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-funnel me-1"></i> Filtrar
            </button>
            <?php if (!empty($_GET['q']) || !empty($_GET['tipo']) || !empty($_GET['categoria']) || !empty($_GET['tipo_leche']) || !empty($_GET['intensidad'])): ?>
                <a href="index.php?c=catalogo" class="btn btn-outline-secondary" title="Limpiar filtros">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Filtros activos (badges) -->
<?php 
$filtrosActivos = array_filter([
    'q' => $_GET['q'] ?? '',
    'categoria' => $_GET['categoria'] ?? '',
    'tipo_leche' => $_GET['tipo_leche'] ?? '',
    'intensidad' => $_GET['intensidad'] ?? '',
    'tipo' => $_GET['tipo'] ?? '',
]);
if (!empty($filtrosActivos)): 
?>
<div class="mb-3 d-flex flex-wrap align-items-center gap-2">
    <span class="text-muted small fw-semibold">
        <i class="bi bi-funnel-fill me-1"></i>Filtros activos:
    </span>
    <?php foreach ($filtrosActivos as $key => $val): 
        $labels = [
            'q' => 'Búsqueda',
            'categoria' => 'Categoría',
            'tipo_leche' => 'Leche',
            'intensidad' => 'Intensidad',
            'tipo' => 'Tipo'
        ];
        $label = $labels[$key] ?? $key;
        $otrosFiltros = array_diff_key($filtrosActivos, [$key => '']);
        $urlSinFiltro = 'index.php?c=catalogo' . (!empty($otrosFiltros) ? '&' . http_build_query($otrosFiltros) : '');
    ?>
        <span class="badge bg-primary-subtle text-primary border border-primary d-inline-flex align-items-center gap-1">
            <span><?= htmlspecialchars($label) ?>: <strong><?= htmlspecialchars($val) ?></strong></span>
            <a href="<?= $urlSinFiltro ?>" class="text-primary text-decoration-none" style="font-size:1.1rem">×</a>
        </span>
    <?php endforeach; ?>
    <a href="index.php?c=catalogo" class="badge bg-danger-subtle text-danger border border-danger text-decoration-none">
        <i class="bi bi-x-circle me-1"></i>Limpiar todos
    </a>
</div>
<?php endif; ?>

<!-- Grid de tarjetas -->
<?php if (empty($quesos)): ?>
    <div class="card-lacteo p-5 text-center text-muted">
        <div style="font-size:3rem" class="mb-3">🧀</div>
        <h5 class="fw-bold text-dark">No se encontraron variedades</h5>
        <p class="mb-0">Ajusta los filtros de búsqueda.</p>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($quesos as $q):
            $disponible  = (int)$q['cantidad'] > 0;
            $enCarrito   = isset($carrito[(string)$q['id']]);
            
            // Información de categorización
            $categoria      = $q['categoria'] ?? $q['tipo'];
            $tipoLeche      = $q['tipo_leche'] ?? 'Vaca';
            $intensidad     = $q['intensidad_sabor'] ?? 'medio';
            $presentacion   = $q['peso_presentacion'] ?? $q['unidad_medida'];
        ?>
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card-lacteo h-100 d-flex flex-column p-3 position-relative">

                    <!-- Badge estado -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-light text-secondary border font-monospace small">
                            <?= htmlspecialchars($q['codigo']) ?>
                        </span>
                        <?php if ($disponible): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-check-circle me-1"></i>Disponible
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                <i class="bi bi-x-circle me-1"></i>Agotado
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Icono -->
                    <div class="text-center py-3 rounded-3 mb-3"
                         style="background:linear-gradient(135deg,#fef3c7 0%,#e0f2fe 100%)">
                        <div style="font-size:2.75rem">🧀</div>
                        <span class="badge bg-white text-primary border shadow-sm mt-1">
                            <?= htmlspecialchars($categoria) ?>
                        </span>
                    </div>

                    <!-- Info -->
                    <div class="flex-grow-1">
                        <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($q['nombre']) ?></h5>
                        <p class="text-muted small mb-2" style="min-height:40px">
                            <?= htmlspecialchars($q['descripcion'] ?? 'Elaborado artesanalmente con leche fresca.') ?>
                        </p>
                        
                        <!-- Tags de características -->
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <span class="badge bg-info-subtle text-info border border-info-subtle small">
                                <i class="bi bi-droplet-fill me-1"></i><?= htmlspecialchars($tipoLeche) ?>
                            </span>
                            <?php 
                            $colorIntensidad = [
                                'suave' => 'success',
                                'medio' => 'warning', 
                                'fuerte' => 'orange',
                                'muy fuerte' => 'danger'
                            ];
                            $color = $colorIntensidad[$intensidad] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $color ?>-subtle text-<?= $color ?> border border-<?= $color ?>-subtle small">
                                <i class="bi bi-fire me-1"></i><?= ucfirst($intensidad) ?>
                            </span>
                            <?php if (!empty($q['origen'])): ?>
                            <span class="badge bg-secondary-subtle text-secondary border small">
                                <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($q['origen']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Footer tarjeta -->
                    <div class="border-top pt-3 mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <small class="text-muted d-block" style="font-size:.72rem">Presentación:</small>
                                <span class="fw-semibold text-secondary small"><?= htmlspecialchars($presentacion) ?></span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block" style="font-size:.72rem">Precio:</small>
                                <span class="fw-bold fs-5 text-primary"><?= formatearMoneda($q['precio_venta']) ?></span>
                            </div>
                        </div>

                        <!-- Botón de acción según rol -->
                        <?php if (is_admin()): ?>
                            <div class="d-flex gap-2">
                                <a href="index.php?c=productos&a=editar&id=<?= $q['id'] ?>"
                                   class="btn btn-sm btn-outline-primary flex-grow-1">
                                    <i class="bi bi-pencil me-1"></i>Editar
                                </a>
                                <a href="index.php?c=ventas"
                                   class="btn btn-sm btn-primary flex-grow-1">
                                    <i class="bi bi-cart-plus me-1"></i>Vender
                                </a>
                            </div>
                        <?php elseif ($disponible): ?>
                            <form method="POST" action="index.php?c=carrito&a=agregar">
                                <input type="hidden" name="producto_id" value="<?= $q['id'] ?>">
                                <input type="hidden" name="redirect" value="index.php?c=catalogo">
                                
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <label class="text-muted small mb-0 flex-shrink-0">Cantidad:</label>
                                    <input type="number" name="cantidad"
                                           class="form-control form-control-sm text-center"
                                           value="1" min="1" max="<?= $q['cantidad'] ?>"
                                           style="width:70px">
                                </div>
                                <button type="submit"
                                        class="btn btn-sm w-100 <?= $enCarrito ? 'btn-success' : 'btn-primary' ?> d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-cart-plus"></i>
                                    <?= $enCarrito ? '+ Agregar más' : 'Agregar al carrito' ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-sm btn-secondary w-100" disabled>Sin stock</button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
