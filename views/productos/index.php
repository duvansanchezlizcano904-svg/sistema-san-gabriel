<?php
/**
 * Vista de Listado de Productos
 */
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Catálogo de Quesos</h2>
        <p class="text-muted mb-0">Administra las diferentes variedades de queso, precios y existencias.</p>
    </div>
    <div>
        <a href="index.php?c=productos&a=crear" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Nuevo Queso
        </a>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="card-lacteo mb-4 p-3">
    <form action="index.php" method="GET" class="row g-2 align-items-center">
        <input type="hidden" name="c" value="productos">
        
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Buscar por código, nombre o descripción..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <select name="tipo" class="form-select">
                <option value="">-- Todos los Tipos --</option>
                <?php foreach ($tiposDisponibles as $t): ?>
                    <option value="<?= htmlspecialchars($t) ?>" <?= (isset($_GET['tipo']) && $_GET['tipo'] === $t) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 col-sm-6 col-md-2">
            <select name="estado" class="form-select">
                <option value="">-- Estado --</option>
                <option value="activo" <?= (isset($_GET['estado']) && $_GET['estado'] === 'activo') ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= (isset($_GET['estado']) && $_GET['estado'] === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-secondary w-100">
                <i class="bi bi-funnel"></i> Filtrar
            </button>
            <?php if (!empty($_GET['q']) || !empty($_GET['tipo']) || !empty($_GET['estado'])): ?>
                <a href="index.php?c=productos" class="btn btn-outline-secondary" title="Limpiar Filtros">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabla de Productos -->
<div class="card-lacteo">
    <div class="table-responsive">
        <table class="table table-lacteo align-middle">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th class="text-end">Precio Compra</th>
                    <th class="text-end">Precio Venta</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($productos)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-search fs-2 d-block mb-2"></i>
                            No se encontraron productos lácteos con los criterios de búsqueda.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($productos as $p): 
                        $estadoStock = calcularEstadoStock((int)$p['cantidad'], (int)$p['stock_minimo']);
                    ?>
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($p['codigo']) ?></span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($p['nombre']) ?></div>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">
                                    <?= htmlspecialchars($p['descripcion'] ?? 'Sin descripción') ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                    <?= htmlspecialchars($p['tipo']) ?>
                                </span>
                            </td>
                            <td class="text-end text-muted">
                                <?= formatearMoneda($p['precio_compra']) ?>
                            </td>
                            <td class="text-end fw-semibold text-primary">
                                <?= formatearMoneda($p['precio_venta']) ?>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold"><?= $p['cantidad'] ?></span> 
                                <small class="text-muted"><?= htmlspecialchars($p['unidad_medida']) ?></small>
                                <div class="mt-1">
                                    <span class="<?= $estadoStock['clase'] ?>" style="font-size: 0.7rem;"><?= $estadoStock['nombre'] ?></span>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if ($p['estado'] === 'activo'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="index.php?c=productos&a=editar&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <form action="index.php?c=productos&a=eliminar" method="POST" class="d-inline form-eliminar-producto" data-nombre="<?= htmlspecialchars($p['nombre']) ?>">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
