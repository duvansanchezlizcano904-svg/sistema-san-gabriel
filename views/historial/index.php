<?php
/**
 * Vista de Historial de Ventas
 */
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Historial de Ventas</h2>
        <p class="text-muted mb-0">Consulta todas las ventas registradas, aplica filtros y revisa comprobantes.</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-printer"></i> Imprimir Listado
        </button>
        <a href="index.php?c=ventas" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-cart-plus"></i> Nueva Venta
        </a>
    </div>
</div>

<!-- Filtros de Búsqueda -->
<div class="card-lacteo mb-4 p-3 no-print">
    <form action="index.php" method="GET" class="row g-2 align-items-end">
        <input type="hidden" name="c" value="historial">

        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label small fw-semibold text-secondary mb-1">Desde Fecha:</label>
            <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label small fw-semibold text-secondary mb-1">Hasta Fecha:</label>
            <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
        </div>

        <div class="col-12 col-md-4">
            <label class="form-label small fw-semibold text-secondary mb-1">Filtrar por Producto:</label>
            <select name="producto_id" class="form-select">
                <option value="">-- Todos los productos --</option>
                <?php foreach ($productos as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= (isset($_GET['producto_id']) && $_GET['producto_id'] == $p['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre']) ?> (<?= htmlspecialchars($p['codigo']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-filter"></i> Filtrar
            </button>
            <?php if (!empty($_GET['fecha_inicio']) || !empty($_GET['fecha_fin']) || !empty($_GET['producto_id'])): ?>
                <a href="index.php?c=historial" class="btn btn-outline-secondary" title="Limpiar Filtros">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Encabezado para impresión -->
<div class="print-header text-center mb-4">
    <h2 class="fw-bold mb-1">San Gabriel - Historial de Ventas</h2>
    <p class="text-muted mb-0">Reporte generado el <?= date('d/m/Y h:i A') ?></p>
</div>

<!-- Resumen del Filtro -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">Mostrando <strong><?= count($ventas) ?></strong> ventas registradas</span>
    <span class="badge bg-primary fs-6 px-3 py-2">
        Total en este período: <?= formatearMoneda($totalFiltrado) ?>
    </span>
</div>

<!-- Tabla de Historial -->
<div class="card-lacteo">
    <div class="table-responsive">
        <table class="table table-lacteo align-middle">
            <thead>
                <tr>
                    <th>N° Venta</th>
                    <th>Fecha y Hora</th>
                    <th>Cliente</th>
                    <th class="text-center">Ítems</th>
                    <th class="text-center">Unidades</th>
                    <th>Registrado Por</th>
                    <th class="text-end">Total</th>
                    <th class="text-center no-print">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ventas)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt-cutoff fs-2 d-block mb-2"></i>
                            No se encontraron ventas con los filtros seleccionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ventas as $v): ?>
                        <tr>
                            <td>
                                <span class="fw-bold text-primary font-monospace"><?= htmlspecialchars($v['numero_venta']) ?></span>
                            </td>
                            <td><?= formatearFecha($v['fecha']) ?></td>
                            <td>
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($v['cliente']) ?></div>
                                <?php if (!empty($v['notas'])): ?>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">
                                        <?= htmlspecialchars($v['notas']) ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border"><?= $v['total_items'] ?></span>
                            </td>
                            <td class="text-center fw-semibold">
                                <?= $v['unidades_vendidas'] ?>
                            </td>
                            <td>
                                <small class="text-muted"><?= htmlspecialchars($v['usuario_nombre'] ?? 'Administrador') ?></small>
                            </td>
                            <td class="text-end fw-bold text-dark fs-6">
                                <?= formatearMoneda($v['total']) ?>
                            </td>
                            <td class="text-center no-print">
                                <a href="index.php?c=historial&a=detalle&id=<?= $v['id'] ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" title="Ver Comprobante">
                                    <i class="bi bi-eye"></i> Detalle
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
