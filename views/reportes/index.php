<?php
/**
 * Vista de Reportes y Estadísticas
 */
require_once __DIR__ . '/../layouts/header.php';
$tipoActivo = $_GET['tipo'] ?? 'general';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Reportes Ejecutivos</h2>
        <p class="text-muted mb-0">Consolidado de inventarios disponibles, existencias críticas y ventas del período.</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-printer"></i> Imprimir Reporte
        </button>
    </div>
</div>

<!-- Encabezado para impresión -->
<div class="print-header text-center mb-4">
    <h2 class="fw-bold mb-1">San Gabriel - Reporte Oficial de Inventario y Ventas</h2>
    <p class="text-muted mb-0">Fecha de emisión: <?= date('d/m/Y h:i A') ?> | Generado por: <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Administrador') ?></p>
</div>

<!-- Filtro de Fechas para Ventas -->
<div class="card-lacteo p-3 mb-4 no-print">
    <form action="index.php" method="GET" class="row g-2 align-items-end">
        <input type="hidden" name="c" value="reportes">
        <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipoActivo) ?>">

        <div class="col-12 col-sm-4 col-md-3">
            <label class="form-label small fw-semibold text-secondary mb-1">Fecha Desde:</label>
            <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($fechaInicio) ?>">
        </div>

        <div class="col-12 col-sm-4 col-md-3">
            <label class="form-label small fw-semibold text-secondary mb-1">Fecha Hasta:</label>
            <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($fechaFin) ?>">
        </div>

        <div class="col-12 col-sm-4 col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-funnel"></i> Actualizar
            </button>
        </div>
    </form>
</div>

<!-- Tarjetas Resumen Financiero y Operativo -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-label text-success">Disponibles</div>
            <div class="stat-value text-success"><?= count($productosDisponibles) ?></div>
            <small class="text-muted">Productos con stock normal</small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-label text-warning">Stock Bajo</div>
            <div class="stat-value text-warning"><?= count($productosStockBajo) ?></div>
            <small class="text-muted">Requieren compra/producción</small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-label text-danger">Agotados</div>
            <div class="stat-value text-danger"><?= count($productosAgotados) ?></div>
            <small class="text-muted">Sin unidades disponibles</small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-label text-primary">Total Vendido Período</div>
            <div class="stat-value text-primary fs-4"><?= formatearMoneda($totalVentasPeriodo) ?></div>
            <small class="text-muted"><?= count($ventasPeriodo) ?> ventas realizadas</small>
        </div>
    </div>
</div>

<!-- Pestañas para navegación de reportes -->
<ul class="nav nav-tabs mb-4 no-print" role="tablist">
    <li class="nav-item">
        <a class="nav-link <?= $tipoActivo === 'general' ? 'active fw-bold' : '' ?>" href="index.php?c=reportes&tipo=general&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>">
            <i class="bi bi-grid me-1"></i> Resumen General
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipoActivo === 'disponibles' ? 'active fw-bold' : '' ?>" href="index.php?c=reportes&tipo=disponibles&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>">
            <i class="bi bi-check-circle me-1"></i> Disponibles (<?= count($productosDisponibles) ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipoActivo === 'stock_bajo' ? 'active fw-bold text-warning' : '' ?>" href="index.php?c=reportes&tipo=stock_bajo&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>">
            <i class="bi bi-exclamation-triangle me-1"></i> Stock Bajo (<?= count($productosStockBajo) ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipoActivo === 'agotados' ? 'active fw-bold text-danger' : '' ?>" href="index.php?c=reportes&tipo=agotados&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>">
            <i class="bi bi-x-octagon me-1"></i> Agotados (<?= count($productosAgotados) ?>)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipoActivo === 'ventas' ? 'active fw-bold' : '' ?>" href="index.php?c=reportes&tipo=ventas&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>">
            <i class="bi bi-cash-stack me-1"></i> Ventas Realizadas (<?= count($ventasPeriodo) ?>)
        </a>
    </li>
</ul>

<!-- Contenido del Reporte según selección -->
<?php if ($tipoActivo === 'general' || $tipoActivo === 'ventas'): ?>
    <!-- Tabla de Ventas Realizadas -->
    <div class="card-lacteo mb-4">
        <div class="card-header-clean">
            <h5 class="card-title-clean">
                <i class="bi bi-receipt text-primary me-2"></i> Reporte de Ventas Realizadas (<?= formatearFecha($fechaInicio, false) ?> al <?= formatearFecha($fechaFin, false) ?>)
            </h5>
            <span class="badge bg-primary fs-6">Total: <?= formatearMoneda($totalVentasPeriodo) ?></span>
        </div>
        <div class="table-responsive">
            <table class="table table-lacteo align-middle">
                <thead>
                    <tr>
                        <th>N° Venta</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th class="text-center">Ítems</th>
                        <th class="text-center">Unidades</th>
                        <th class="text-end">Total Venta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ventasPeriodo)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No se registraron ventas en este período.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ventasPeriodo as $v): ?>
                            <tr>
                                <td class="fw-bold font-monospace text-primary"><?= htmlspecialchars($v['numero_venta']) ?></td>
                                <td><?= formatearFecha($v['fecha']) ?></td>
                                <td><?= htmlspecialchars($v['cliente']) ?></td>
                                <td class="text-center"><?= $v['total_items'] ?></td>
                                <td class="text-center fw-semibold"><?= $v['unidades_vendidas'] ?></td>
                                <td class="text-end fw-bold text-dark"><?= formatearMoneda($v['total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if ($tipoActivo === 'general' || in_array($tipoActivo, ['disponibles', 'stock_bajo', 'agotados'])): 
    // Seleccionar el conjunto de productos a mostrar
    $listaMostrar = match($tipoActivo) {
        'disponibles' => $productosDisponibles,
        'stock_bajo'  => $productosStockBajo,
        'agotados'    => $productosAgotados,
        default       => $todosProductos
    };
?>
    <!-- Tabla de Productos según Estado -->
    <div class="card-lacteo">
        <div class="card-header-clean">
            <h5 class="card-title-clean">
                <i class="bi bi-box-seam text-primary me-2"></i> Estado de Inventario de Productos Lácteos
            </h5>
            <span class="text-muted small">Total: <?= count($listaMostrar) ?> producto(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-lacteo align-middle">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th class="text-center">Cantidad Actual</th>
                        <th class="text-center">Stock Mínimo</th>
                        <th class="text-center">Estado Calculado</th>
                        <th class="text-end">Precio Venta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaMostrar)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No hay productos en esta categoría.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaMostrar as $p): 
                            $estado = calcularEstadoStock((int)$p['cantidad'], (int)$p['stock_minimo']);
                        ?>
                            <tr>
                                <td class="font-monospace fw-semibold"><?= htmlspecialchars($p['codigo']) ?></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($p['nombre']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['tipo']) ?></span></td>
                                <td class="text-center fw-bold fs-6"><?= $p['cantidad'] ?> <?= htmlspecialchars($p['unidad_medida']) ?></td>
                                <td class="text-center text-muted"><?= $p['stock_minimo'] ?> <?= htmlspecialchars($p['unidad_medida']) ?></td>
                                <td class="text-center"><span class="<?= $estado['clase'] ?>"><?= $estado['nombre'] ?></span></td>
                                <td class="text-end fw-semibold text-primary"><?= formatearMoneda($p['precio_venta']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
