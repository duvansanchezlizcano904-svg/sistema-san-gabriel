<?php
/**
 * Vista de Registro de Ventas
 */
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Registrar Venta de Lácteos</h2>
        <p class="text-muted mb-0">Genera una nueva nota de venta con descuento automático de inventario.</p>
    </div>
    <div>
        <a href="index.php?c=historial" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-clock-history"></i> Ver Historial de Ventas
        </a>
    </div>
</div>

<form action="index.php?c=ventas&a=guardar" method="POST" id="formVenta" autocomplete="off">
    <div class="row g-4">
        <!-- Columna Izquierda: Información de la Venta y Selector de Productos -->
        <div class="col-12 col-lg-5">
            <!-- Datos Generales -->
            <div class="card-lacteo p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <i class="bi bi-receipt text-primary me-2"></i> Datos de la Venta
                </h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small">Número de Venta (Consecutivo)</label>
                    <input type="text" class="form-control bg-light font-monospace fw-bold text-primary" 
                           value="<?= htmlspecialchars($siguienteNumeroVenta) ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="fecha" class="form-label fw-semibold text-secondary small">Fecha y Hora</label>
                    <input type="datetime-local" class="form-control" id="fecha" name="fecha" 
                           value="<?= date('Y-m-d\TH:i') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="cliente" class="form-label fw-semibold text-secondary small">Nombre del Cliente / Destino</label>
                    <input type="text" class="form-control" id="cliente" name="cliente" 
                           placeholder="Ej: Cliente Mostrador, Panadería, Supermercado" value="Cliente Mostrador">
                </div>

                <div class="mb-0">
                    <label for="notas" class="form-label fw-semibold text-secondary small">Notas u Observaciones</label>
                    <textarea class="form-control" id="notas" name="notas" rows="2" placeholder="Método de pago, entrega..."></textarea>
                </div>
            </div>

            <!-- Panel para Agregar Productos -->
            <div class="card-lacteo p-4">
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                    <i class="bi bi-plus-square text-primary me-2"></i> Agregar Producto al Carrito
                </h5>

                <div class="mb-3">
                    <label for="selectProductoVenta" class="form-label fw-semibold text-secondary small">Seleccionar Variedad de Queso</label>
                    <select class="form-select" id="selectProductoVenta">
                        <option value="">-- Elige un queso con stock disponible --</option>
                        <?php foreach ($productosDisponibles as $prod): ?>
                            <option value="<?= $prod['id'] ?>" 
                                    data-nombre="<?= htmlspecialchars($prod['nombre']) ?>"
                                    data-precio="<?= $prod['precio_venta'] ?>"
                                    data-stock="<?= $prod['cantidad'] ?>"
                                    data-unidad="<?= htmlspecialchars($prod['unidad_medida']) ?>">
                                <?= htmlspecialchars($prod['nombre']) ?> (Disponibles: <?= $prod['cantidad'] ?> <?= htmlspecialchars($prod['unidad_medida']) ?>) - $ <?= number_format($prod['precio_venta'], 2, ',', '.') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text small" id="infoStockDisponible">
                        Selecciona un producto para ver su disponibilidad.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="inputCantidadVenta" class="form-label fw-semibold text-secondary small">Cantidad a Vender</label>
                    <input type="number" class="form-control" id="inputCantidadVenta" min="1" value="1">
                    <div class="form-text small text-danger">No se permite vender más de lo disponible.</div>
                </div>

                <button type="button" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" id="btnAgregarProducto">
                    <i class="bi bi-cart-plus"></i> Añadir a la Venta
                </button>
            </div>
        </div>

        <!-- Columna Derecha: Detalle de Ítems y Total de la Venta -->
        <div class="col-12 col-lg-7">
            <div class="card-lacteo p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-cart3 text-primary me-2"></i> Productos en la Venta
                        </h5>
                        <span class="badge bg-light text-muted border">Cálculo Automático</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-lacteo align-middle" id="tablaItemsVenta">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Unidad</th>
                                    <th style="width: 130px;">Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                    <th class="text-center">Quitar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="filaSinProductos">
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-cart-x fs-1 d-block mb-2 text-muted"></i>
                                        Selecciona productos a la izquierda y presiona "Añadir a la Venta".
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Resumen de Totales y Botón de Finalizar -->
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fs-5 text-muted fw-semibold">Total a Pagar:</span>
                        <span class="fs-2 fw-bold text-primary" id="totalVentaSpan">$ 0,00</span>
                    </div>

                    <div class="alert alert-info py-2 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Al completar la venta, el sistema descontará automáticamente las existencias de la bodega.
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg fw-bold d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-check-circle-fill"></i> Completar y Registrar Venta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
