<?php
/**
 * Vista de Pedido Confirmado — Quesos San Gabriel
 */
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
<div class="col-12 col-md-8 col-lg-6">

    <!-- Encabezado éxito -->
    <div class="text-center mb-4">
        <div style="font-size:4rem;line-height:1" class="mb-3">🎉</div>
        <h2 class="fw-bold text-dark mb-2">¡Pedido confirmado!</h2>
        <p class="text-muted mb-0">
            Tu pedido ha sido registrado. El equipo de Quesos San Gabriel
            lo procesará a la brevedad.
        </p>
    </div>

    <?php if ($pedido): ?>

    <!-- Resumen del pedido -->
    <div class="card-lacteo p-4 mb-3">

        <!-- Cabecera -->
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <div class="text-muted small mb-1">Número de pedido</div>
                <div class="fw-bold text-primary font-monospace fs-5">
                    <?= htmlspecialchars($pedido['numero_pedido']) ?>
                </div>
            </div>
            <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                <i class="bi bi-clock me-1"></i> Pendiente
            </span>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="text-muted small">Fecha</div>
                <div class="fw-semibold small"><?= formatearFecha($pedido['fecha']) ?></div>
            </div>
            <div class="col-6 text-end">
                <div class="text-muted small">Cliente</div>
                <div class="fw-semibold small">
                    <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Cliente') ?>
                </div>
            </div>
        </div>

        <hr class="my-2">

        <!-- Detalles -->
        <?php foreach ($pedido['detalles'] as $d): ?>
        <div class="d-flex justify-content-between align-items-center py-1">
            <div>
                <div class="fw-semibold small"><?= htmlspecialchars($d['producto_nombre']) ?></div>
                <div class="text-muted" style="font-size:.75rem">
                    <?= $d['cantidad'] ?> × <?= formatearMoneda($d['precio_unitario']) ?>
                </div>
            </div>
            <div class="fw-bold small"><?= formatearMoneda($d['subtotal']) ?></div>
        </div>
        <?php endforeach; ?>

        <hr class="my-2">

        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold fs-6">Total</span>
            <span class="fw-bold fs-4 text-primary"><?= formatearMoneda($pedido['total']) ?></span>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="d-grid gap-2 mb-3">
        <!-- Botón principal: ver tiquete -->
        <a href="index.php?c=carrito&a=factura&id=<?= $pedido['id'] ?>"
           class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2"
           target="_blank">
            <i class="bi bi-receipt"></i>
            Ver e Imprimir Tiquete
        </a>
        <!-- Imprimir directo -->
        <a href="index.php?c=carrito&a=factura&id=<?= $pedido['id'] ?>&print=1"
           class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2"
           target="_blank">
            <i class="bi bi-printer"></i>
            Imprimir directamente
        </a>
    </div>

    <!-- Info tiquete -->
    <div class="alert alert-info d-flex align-items-start gap-2 py-2 mb-4">
        <i class="bi bi-qr-code fs-5 flex-shrink-0 mt-1"></i>
        <div class="small">
            <strong>Tu tiquete incluye un código QR</strong> que permite verificar
            la autenticidad del pedido. Guárdalo o imprímelo.
        </div>
    </div>

    <?php endif; ?>

    <!-- Navegación -->
    <div class="d-flex gap-2 flex-wrap justify-content-center">
        <a href="index.php" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-house-door"></i> Volver a la página principal
        </a>
        <a href="index.php?c=carrito" class="btn btn-light border d-flex align-items-center gap-2">
            <i class="bi bi-cart3"></i> Ver carrito
        </a>
    </div>

</div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
