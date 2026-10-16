<?php
/**
 * Vista de Inventario
 */
require_once __DIR__ . '/../layouts/header.php';
$filtroEstadoActual = $_GET['estado_stock'] ?? '';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Control de Inventario</h2>
        <p class="text-muted mb-0">Monitoreo en tiempo real de existencias y niveles mínimos de alerta.</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-printer"></i> Imprimir Inventario
        </button>
        <a href="index.php?c=productos&a=crear" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Nuevo Producto
        </a>
    </div>
</div>

<!-- Tarjetas Resumen de Inventario -->
<div class="row g-3 mb-4 no-print">
    <div class="col-6 col-lg-3">
        <a href="index.php?c=inventario" class="text-decoration-none">
            <div class="stat-card border-<?= empty($filtroEstadoActual) ? 'primary border-2 shadow-sm' : 'border' ?>">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="stat-label">Total en Catálogo</span>
                    <i class="bi bi-boxes text-primary fs-4"></i>
                </div>
                <div class="stat-value text-dark"><?= $stats['total_productos'] ?></div>
                <small class="text-muted">Productos registrados</small>
            </div>
        </a>
    </div>

    <div class="col-6 col-lg-3">
        <a href="index.php?c=inventario&estado_stock=disponible" class="text-decoration-none">
            <div class="stat-card border-<?= $filtroEstadoActual === 'disponible' ? 'success border-2 shadow-sm' : 'border' ?>">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="stat-label text-success">Disponibles</span>
                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                </div>
                <div class="stat-value text-success"><?= $stats['disponibles'] ?></div>
                <small class="text-muted">Con stock suficiente</small>
            </div>
        </a>
    </div>

    <div class="col-6 col-lg-3">
        <a href="index.php?c=inventario&estado_stock=bajo" class="text-decoration-none">
            <div class="stat-card border-<?= $filtroEstadoActual === 'bajo' ? 'warning border-2 shadow-sm' : 'border' ?>">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="stat-label text-warning">Stock Bajo</span>
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
                </div>
                <div class="stat-value text-warning"><?= $stats['stock_bajo'] ?></div>
                <small class="text-muted">Requieren reposición</small>
            </div>
        </a>
    </div>

    <div class="col-6 col-lg-3">
        <a href="index.php?c=inventario&estado_stock=agotado" class="text-decoration-none">
            <div class="stat-card border-<?= $filtroEstadoActual === 'agotado' ? 'danger border-2 shadow-sm' : 'border' ?>">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="stat-label text-danger">Agotados</span>
                    <i class="bi bi-x-octagon-fill text-danger fs-4"></i>
                </div>
                <div class="stat-value text-danger"><?= $stats['agotados'] ?></div>
                <small class="text-muted">Sin existencia física</small>
            </div>
        </a>
    </div>
</div>

<!-- Encabezado para impresión -->
<div class="print-header text-center mb-4">
    <h2 class="fw-bold mb-1">San Gabriel - Reporte de Inventario</h2>
    <p class="text-muted mb-0">Fecha de generación: <?= date('d/m/Y h:i A') ?></p>
</div>

<!-- Tabla de Inventario requerida -->
<div class="card-lacteo">
    <div class="card-header-clean no-print">
        <h5 class="card-title-clean">Existencias Actuales de Variedades de Queso</h5>
        <div class="d-flex gap-2">
            <form action="index.php" method="GET" class="d-flex gap-2">
                <input type="hidden" name="c" value="inventario">
                <?php if (!empty($filtroEstadoActual)): ?>
                    <input type="hidden" name="estado_stock" value="<?= htmlspecialchars($filtroEstadoActual) ?>">
                <?php endif; ?>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Buscar producto..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-lacteo align-middle">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">Unidad</th>
                    <th class="text-center">Stock Mínimo</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center no-print">Alerta</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($productos)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-box2 fs-3 d-block mb-2"></i>
                            No se encontraron productos en el inventario con los criterios seleccionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($productos as $p): 
                        $estado = calcularEstadoStock((int)$p['cantidad'], (int)$p['stock_minimo']);
                    ?>
                        <tr class="<?= $estado['codigo'] === 'agotado' ? 'table-danger' : ($estado['codigo'] === 'bajo' ? 'table-warning' : '') ?>">
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($p['nombre']) ?></div>
                                <small class="text-muted font-monospace"><?= htmlspecialchars($p['codigo']) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border"><?= htmlspecialchars($p['tipo']) ?></span>
                            </td>
                            <td class="text-center fw-bold fs-6">
                                <?= $p['cantidad'] ?>
                            </td>
                            <td class="text-center text-muted">
                                <?= htmlspecialchars($p['unidad_medida']) ?>
                            </td>
                            <td class="text-center text-muted fw-semibold">
                                <?= $p['stock_minimo'] ?>
                            </td>
                            <td class="text-center">
                                <span class="<?= $estado['clase'] ?> px-3 py-2">
                                    <?= $estado['nombre'] ?>
                                </span>
                            </td>
                            <td class="text-center no-print">
                                <?php if ($estado['codigo'] === 'agotado'): ?>
                                    <span class="text-danger fw-bold small d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-exclamation-octagon-fill"></i> ¡URGENTE: Agotado!
                                    </span>
                                <?php elseif ($estado['codigo'] === 'bajo'): ?>
                                    <span class="text-warning-emphasis fw-bold small d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-exclamation-triangle-fill text-warning"></i> Stock Bajo
                                    </span>
                                <?php else: ?>
                                    <span class="text-success small d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-check2-circle"></i> Stock Óptimo
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
