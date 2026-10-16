<?php
/**
 * Vista del Carrito de Compras — Quesos San Gabriel
 */
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="publico.php" class="btn btn-light border">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h2 class="fw-bold text-dark mb-0">
            <i class="bi bi-cart3 text-primary me-2"></i> Mi Carrito
        </h2>
        <p class="text-muted mb-0 small">Revisa tu selección antes de confirmar el pedido.</p>
    </div>
</div>

<?php if (empty($carrito)): ?>
    <!-- Carrito vacío -->
    <div class="card-lacteo p-5 text-center">
        <div style="font-size:4rem" class="mb-3">🛒</div>
        <h4 class="fw-bold text-dark mb-2">Tu carrito está vacío</h4>
        <p class="text-muted mb-4">Aún no has agregado ningún queso a tu carrito.</p>
        <a href="publico.php" class="btn btn-primary px-4">
            <i class="bi bi-grid me-2"></i> Ver catálogo de quesos
        </a>
    </div>

<?php else: ?>
    <div class="row g-4">

        <!-- Tabla de productos -->
        <div class="col-12 col-lg-8">
            <div class="card-lacteo">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">
                        <i class="bi bi-list-check text-primary me-2"></i>
                        Productos seleccionados
                    </h5>
                    <form method="POST" action="index.php?c=carrito&a=vaciar"
                          onsubmit="return confirm('¿Vaciar todo el carrito?')">
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash me-1"></i> Vaciar carrito
                        </button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-lacteo align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center" style="width:120px">Cantidad</th>
                                <th class="text-end" style="width:110px">Precio unit.</th>
                                <th class="text-end" style="width:120px">Subtotal</th>
                                <th class="text-center" style="width:80px">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($carrito as $id => $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="carrito-emoji">🧀</div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold text-dark mb-1">
                                                <?= htmlspecialchars($item['nombre']) ?>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <?php if (!empty($item['categoria'])): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">
                                                    <i class="bi bi-grid-3x3-gap me-1"></i><?= htmlspecialchars($item['categoria']) ?>
                                                </span>
                                                <?php endif; ?>
                                                <?php if (!empty($item['tipo_leche'])): ?>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle small">
                                                    <i class="bi bi-droplet-fill me-1"></i>Leche de <?= htmlspecialchars($item['tipo_leche']) ?>
                                                </span>
                                                <?php endif; ?>
                                                <span class="badge bg-light text-secondary border small">
                                                    <?= htmlspecialchars($item['peso_presentacion'] ?? $item['unidad_medida']) ?>
                                                </span>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                <i class="bi bi-box-seam me-1"></i>Stock disponible: <strong><?= $item['stock_real'] ?></strong>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="index.php?c=carrito&a=actualizar"
                                          class="carrito-qty-form">
                                        <input type="hidden" name="producto_id" value="<?= $id ?>">
                                        <div class="carrito-qty-control">
                                            <button type="button" class="carrito-qty-btn carrito-qty-btn-minus"
                                                    onclick="cambiarCantidad(this, -1, <?= $item['stock_real'] ?>)"
                                                    <?= $item['cantidad'] <= 1 ? 'disabled' : '' ?>>
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="number"
                                                   name="cantidad"
                                                   value="<?= $item['cantidad'] ?>"
                                                   min="1"
                                                   max="<?= $item['stock_real'] ?>"
                                                   class="carrito-qty-input"
                                                   onchange="this.form.submit()"
                                                   readonly>
                                            <button type="button" class="carrito-qty-btn carrito-qty-btn-plus"
                                                    onclick="cambiarCantidad(this, 1, <?= $item['stock_real'] ?>)"
                                                    <?= $item['cantidad'] >= $item['stock_real'] ? 'disabled' : '' ?>>
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                                <td class="text-end text-muted">
                                    <?= formatearMoneda($item['precio_unitario']) ?>
                                </td>
                                <td class="text-end fw-bold text-primary fs-5">
                                    <?= formatearMoneda($item['subtotal']) ?>
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="index.php?c=carrito&a=quitar"
                                          onsubmit="return confirm('¿Quitar <?= htmlspecialchars($item['nombre']) ?> del carrito?')">
                                        <input type="hidden" name="producto_id" value="<?= $id ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Quitar del carrito">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold text-dark">TOTAL:</td>
                                <td class="text-end fw-bold text-success fs-4"><?= formatearMoneda($total) ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-3">
                <a href="publico.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Seguir comprando
                </a>
            </div>
        </div>

        <!-- Resumen y confirmar -->
        <div class="col-12 col-lg-4">
            <div class="card-lacteo p-4">
                <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                    <i class="bi bi-receipt text-primary"></i> Resumen del pedido
                </h5>

                <!-- Desglose -->
                <div class="d-flex flex-column gap-2 mb-4">
                    <?php foreach ($carrito as $item): ?>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">
                            <?= htmlspecialchars($item['nombre']) ?>
                            <span class="badge bg-light text-dark border ms-1">×<?= $item['cantidad'] ?></span>
                        </span>
                        <span class="fw-semibold"><?= formatearMoneda($item['subtotal']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <hr>

                <!-- Total -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fs-5 fw-bold text-dark">Total</span>
                    <span class="fs-3 fw-bold text-primary"><?= formatearMoneda($total) ?></span>
                </div>

                <!-- Formulario de confirmación -->
                <form method="POST" action="index.php?c=carrito&a=confirmar">
                    <div class="mb-3">
                        <label for="notas" class="form-label fw-semibold small text-muted text-uppercase">
                            Notas del pedido (opcional)
                        </label>
                        <textarea class="form-control" id="notas" name="notas" rows="2"
                                  placeholder="Ej: entregar en la mañana, sin sal extra..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg fw-bold d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-check-circle-fill"></i> Confirmar pedido
                        </button>
                    </div>
                </form>

                <div class="mt-3 p-3 rounded-3 bg-light">
                    <small class="text-muted d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle-fill text-primary mt-1 flex-shrink-0"></i>
                        Al confirmar, tu pedido quedará registrado y el administrador lo procesará. El stock se descontará automáticamente.
                    </small>
                </div>
            </div>
        </div>

    </div>
<?php endif; ?>

<style>
.carrito-emoji {
    width: 40px; height: 40px;
    background: linear-gradient(135deg,#fef3c7,#fde68a);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0;
}
.carrito-qty-input { width: 70px; }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
