<?php
/**
 * Dashboard — Quesos San Gabriel
 */
require_once __DIR__ . '/../layouts/header.php';
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
