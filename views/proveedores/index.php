<?php
/**
 * Vista: Listado de Proveedores
 */
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Encabezado -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Proveedores</h2>
        <p class="text-muted mb-0">Gestiona los proveedores de insumos y materias primas.</p>
    </div>
    <a href="index.php?c=proveedores&a=crear" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i> Nuevo Proveedor
    </a>
</div>

<!-- Tarjetas de estadísticas -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="bi bi-truck"></i></div>
            <div class="stat-value"><?= $stats['total'] ?></div>
            <div class="stat-label">Total Proveedores</div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="stat-card">
            <div class="stat-icon success"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value text-success"><?= $stats['activos'] ?></div>
            <div class="stat-label">Activos</div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="stat-card">
            <div class="stat-icon warning"><i class="bi bi-pause-circle"></i></div>
            <div class="stat-value text-warning"><?= $stats['inactivos'] ?></div>
            <div class="stat-label">Inactivos</div>
        </div>
    </div>
</div>

<!-- Filtros de búsqueda -->
<div class="card-lacteo p-3 mb-4">
    <form method="GET" action="index.php" class="row g-2 align-items-end">
        <input type="hidden" name="c" value="proveedores">
        <div class="col-12 col-md-6">
            <label class="form-label small fw-semibold text-muted">Buscar</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text"
                       name="busqueda"
                       class="form-control border-start-0"
                       placeholder="Nombre, contacto, NIT, teléfono..."
                       value="<?= htmlspecialchars($busqueda) ?>">
            </div>
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label small fw-semibold text-muted">Estado</label>
            <select name="estado" class="form-select">
                <option value="">Todos</option>
                <option value="activo"   <?= $estado === 'activo'   ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= $estado === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="bi bi-funnel"></i> Filtrar
            </button>
            <a href="index.php?c=proveedores" class="btn btn-outline-secondary" title="Limpiar filtros">
                <i class="bi bi-x-circle"></i>
            </a>
        </div>
    </form>
</div>

<!-- Tabla de proveedores -->
<div class="card-lacteo">
    <div class="card-header-clean">
        <h5 class="card-title-clean">
            <i class="bi bi-truck text-primary me-2"></i> Lista de Proveedores
        </h5>
        <span class="badge bg-light text-dark border">
            <?= count($proveedores) ?> resultado(s)
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-lacteo align-middle">
            <thead>
                <tr>
                    <th>Proveedor</th>
                    <th>Contacto</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>NIT</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($proveedores)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-truck fs-3 d-block mb-2"></i>
                            No se encontraron proveedores.
                            <?php if (!empty($busqueda) || !empty($estado)): ?>
                                <a href="index.php?c=proveedores" class="d-block mt-1 small">Limpiar filtros</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($proveedores as $p): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($p['nombre']) ?></div>
                                <?php if (!empty($p['direccion'])): ?>
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($p['direccion']) ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($p['contacto'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($p['telefono'])): ?>
                                    <a href="tel:<?= htmlspecialchars($p['telefono']) ?>" class="text-dark text-decoration-none">
                                        <i class="bi bi-telephone me-1 text-muted"></i><?= htmlspecialchars($p['telefono']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($p['email'])): ?>
                                    <a href="mailto:<?= htmlspecialchars($p['email']) ?>" class="text-primary text-decoration-none small">
                                        <?= htmlspecialchars($p['email']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code class="text-muted small"><?= htmlspecialchars($p['nit'] ?? '-') ?></code>
                            </td>
                            <td class="text-center">
                                <?php if ($p['estado'] === 'activo'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle me-1"></i>Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                        <i class="bi bi-pause-circle me-1"></i>Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="index.php?c=proveedores&a=editar&id=<?= $p['id'] ?>"
                                       class="btn btn-sm btn-light border text-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="index.php?c=proveedores&a=eliminar"
                                          onsubmit="return confirm('¿Eliminar el proveedor «<?= htmlspecialchars($p['nombre']) ?>»? Esta acción no se puede deshacer.')">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Eliminar">
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
