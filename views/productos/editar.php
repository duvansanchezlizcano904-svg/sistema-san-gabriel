<?php
/**
 * Vista para Editar Producto
 */
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-9">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Editar Producto: <?= htmlspecialchars($producto['nombre']) ?></h2>
                <p class="text-muted mb-0">Modifica los detalles, precios o cantidades del producto lácteo.</p>
            </div>
            <a href="index.php?c=productos" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Volver al Catálogo
            </a>
        </div>

        <div class="card-lacteo p-4">
            <form action="index.php?c=productos&a=actualizar" method="POST" autocomplete="off">
                <input type="hidden" name="id" value="<?= $producto['id'] ?>">

                <div class="row g-3">
                    <!-- Código y Nombre -->
                    <div class="col-12 col-md-4">
                        <label for="codigo" class="form-label fw-semibold">Código del Producto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" id="codigo" name="codigo" 
                               value="<?= htmlspecialchars($producto['codigo']) ?>" required>
                    </div>

                    <div class="col-12 col-md-8">
                        <label for="nombre" class="form-label fw-semibold">Nombre del Producto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                    </div>

                    <!-- Tipo y Unidad de Medida -->
                    <div class="col-12 col-md-6">
                        <label for="tipo" class="form-label fw-semibold">Tipo de Queso <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" list="listaTipos" id="tipo" name="tipo" 
                               value="<?= htmlspecialchars($producto['tipo']) ?>" required>
                        <datalist id="listaTipos">
                            <option value="Queso Fresco">
                            <option value="Queso Duro / Salado">
                            <option value="Queso Hilado">
                            <option value="Queso Semigraso">
                            <option value="Cuajada">
                            <option value="Quesillo">
                            <option value="Queso Maduro">
                            <option value="Queso Ahumado">
                        </datalist>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="unidad_medida" class="form-label fw-semibold">Unidad de Medida <span class="text-danger">*</span></label>
                        <select class="form-select" id="unidad_medida" name="unidad_medida" required>
                            <?php 
                            $unidades = ['Kilogramo', 'Litro', 'Libra (500g)', 'Unidad', 'Bloque', 'Vaso'];
                            foreach ($unidades as $u):
                            ?>
                                <option value="<?= $u ?>" <?= ($producto['unidad_medida'] === $u) ? 'selected' : '' ?>><?= $u ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Precios -->
                    <div class="col-12 col-md-6">
                        <label for="precio_compra" class="form-label fw-semibold">Precio de Compra / Costo ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" class="form-control" id="precio_compra" name="precio_compra" 
                                   value="<?= $producto['precio_compra'] ?>" required>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="precio_venta" class="form-label fw-semibold">Precio de Venta ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="precio_venta" name="precio_venta" 
                                   value="<?= $producto['precio_venta'] ?>" required>
                        </div>
                    </div>

                    <!-- Cantidades y Stock -->
                    <div class="col-12 col-md-4">
                        <label for="cantidad" class="form-label fw-semibold">Cantidad Disponible (Stock) <span class="text-danger">*</span></label>
                        <input type="number" min="0" class="form-control" id="cantidad" name="cantidad" 
                               value="<?= $producto['cantidad'] ?>" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="stock_minimo" class="form-label fw-semibold">Stock Mínimo de Alerta <span class="text-danger">*</span></label>
                        <input type="number" min="0" class="form-control" id="stock_minimo" name="stock_minimo" 
                               value="<?= $producto['stock_minimo'] ?>" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="estado" class="form-label fw-semibold">Estado del Producto</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="activo" <?= ($producto['estado'] === 'activo') ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= ($producto['estado'] === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>

                    <!-- Descripción -->
                    <div class="col-12">
                        <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                        <a href="index.php?c=productos" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <i class="bi bi-check2"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
