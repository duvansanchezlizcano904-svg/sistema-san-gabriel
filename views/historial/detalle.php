<?php
/**
 * Vista Detalle de Venta — Quesos San Gabriel
 */
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">

        <!-- Barra de acciones -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 no-print">
            <a href="index.php?c=historial"
               class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Volver al Historial
            </a>
            <div class="d-flex gap-2 flex-wrap">
                <!-- Tiquete térmico (nueva ventana) -->
                <a href="index.php?c=historial&a=factura&id=<?= $venta['id'] ?>"
                   class="btn btn-primary d-flex align-items-center gap-2"
                   target="_blank">
                    <i class="bi bi-receipt"></i> Tiquete con QR
                </a>
                <!-- Imprimir tiquete directo -->
                <a href="index.php?c=historial&a=factura&id=<?= $venta['id'] ?>&print=1"
                   class="btn btn-outline-primary d-flex align-items-center gap-2"
                   target="_blank">
                    <i class="bi bi-printer"></i> Imprimir Tiquete
                </a>
                <!-- Imprimir esta página completa -->
                <button onclick="window.print()"
                        class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text"></i> Imprimir Recibo
                </button>
            </div>
        </div>

        <!-- Tarjeta comprobante completo -->
        <div class="card-lacteo p-4 p-md-5">

            <!-- Encabezado -->
            <div class="row align-items-center pb-4 mb-4 border-bottom">
                <div class="col-12 col-sm-7">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span style="font-size:2rem;line-height:1">🧀</span>
                        <h3 class="fw-bold text-dark mb-0">Quesos San Gabriel</h3>
                    </div>
                    <p class="text-muted small mb-0">Quesería Artesanal</p>
                    <p class="text-muted small mb-0">ventas@quesossangabriel.com</p>
                </div>
                <div class="col-12 col-sm-5 text-sm-end mt-3 mt-sm-0">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle
                                 fs-6 font-monospace px-3 py-2">
                        <?= htmlspecialchars($venta['numero_venta']) ?>
                    </span>
                    <div class="text-muted small mt-2">
                        <strong>Fecha:</strong> <?= formatearFecha($venta['fecha']) ?>
                    </div>
                </div>
            </div>

            <!-- Datos -->
            <div class="row mb-4">
                <div class="col-6">
                    <h6 class="text-secondary small fw-bold text-uppercase mb-1">Cliente</h6>
                    <p class="fw-bold text-dark mb-1"><?= htmlspecialchars($venta['cliente']) ?></p>
                    <?php if (!empty($venta['notas'])): ?>
                        <small class="text-muted">
                            <strong>Notas:</strong> <?= htmlspecialchars($venta['notas']) ?>
                        </small>
                    <?php endif; ?>
                </div>
                <div class="col-6 text-end">
                    <h6 class="text-secondary small fw-bold text-uppercase mb-1">Atendido por</h6>
                    <p class="fw-semibold text-dark mb-0">
                        <?= htmlspecialchars($venta['usuario_nombre'] ?? 'Administrador') ?>
                    </p>
                </div>
            </div>

            <!-- Tabla ítems -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:40px">#</th>
                            <th>Queso</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-center">Unidad</th>
                            <th class="text-end">Precio Unit.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $n = 1; foreach ($venta['detalles'] as $item): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $n++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        <?= htmlspecialchars($item['producto_nombre']) ?>
                                    </div>
                                    <small class="text-muted font-monospace">
                                        <?= htmlspecialchars($item['producto_codigo']) ?>
                                    </small>
                                </td>
                                <td class="text-center fw-bold"><?= $item['cantidad'] ?></td>
                                <td class="text-center text-muted small">
                                    <?= htmlspecialchars($item['unidad_medida']) ?>
                                </td>
                                <td class="text-end"><?= formatearMoneda($item['precio_unitario']) ?></td>
                                <td class="text-end fw-bold text-dark">
                                    <?= formatearMoneda($item['subtotal']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end fw-bold fs-6 pt-3">TOTAL:</td>
                            <td class="text-end fw-bold fs-5 text-primary pt-3">
                                <?= formatearMoneda($venta['total']) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Pie -->
            <div class="text-center pt-3 border-top">
                <p class="fw-semibold mb-1">¡Gracias por preferir Quesos San Gabriel!</p>
                <small class="text-muted">
                    Conserve este comprobante para cualquier consulta.
                </small>
                <div class="mt-2">
                    <a href="index.php?c=historial&a=factura&id=<?= $venta['id'] ?>"
                       class="text-primary small text-decoration-none" target="_blank">
                        <i class="bi bi-qr-code me-1"></i>
                        Ver tiquete con código QR
                    </a>
                </div>
            </div>

        </div><!-- /.card-lacteo -->
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
