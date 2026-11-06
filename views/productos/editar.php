<?php
/**
 * Vista para Editar Producto
 */
require_once __DIR__ . '/../layouts/header.php';

// Imagen actual del producto
$imgActual = trim($producto['imagen'] ?? '');
$imgPreviewSrc = ($imgActual !== '' && file_exists(__DIR__ . '/../../' . $imgActual))
    ? $imgActual
    : '';
?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
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
            <form action="index.php?c=productos&a=actualizar" method="POST" autocomplete="off"
                  enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $producto['id'] ?>">

                <div class="row g-3">

                    <!-- ── Identificación ── -->
                    <div class="col-12">
                        <h6 class="fw-bold text-muted text-uppercase small border-bottom pb-2 mb-1">
                            <i class="bi bi-upc-scan me-1"></i>Identificación
                        </h6>
                    </div>

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

                    <!-- ── Clasificación ── -->
                    <div class="col-12 mt-2">
                        <h6 class="fw-bold text-muted text-uppercase small border-bottom pb-2 mb-1">
                            <i class="bi bi-tags me-1"></i>Clasificación
                        </h6>
                    </div>

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
                            <option value="Queso Premium">
                        </datalist>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="categoria" class="form-label fw-semibold">Categoría</label>
                        <input type="text" class="form-control" list="listaCategorias" id="categoria" name="categoria"
                               value="<?= htmlspecialchars($producto['categoria'] ?? '') ?>"
                               placeholder="Ej: Queso Fresco, Hilado, Maduro…">
                        <datalist id="listaCategorias">
                            <option value="Queso Fresco">
                            <option value="Queso Hilado">
                            <option value="Queso Maduro">
                            <option value="Cuajada">
                            <option value="Queso Semigraso">
                        </datalist>
                        <div class="form-text">Si se omite, se mostrará el Tipo.</div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="tipo_leche" class="form-label fw-semibold">Tipo de Leche</label>
                        <select class="form-select" id="tipo_leche" name="tipo_leche">
                            <option value="">— Seleccionar —</option>
                            <?php
                            $lechesOpts = ['Vaca', 'Cabra', 'Oveja', 'Mixta'];
                            foreach ($lechesOpts as $lOpt):
                            ?>
                                <option value="<?= htmlspecialchars($lOpt) ?>"
                                    <?= ($producto['tipo_leche'] ?? '') === $lOpt ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($lOpt) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="intensidad_sabor" class="form-label fw-semibold">Intensidad de Sabor</label>
                        <select class="form-select" id="intensidad_sabor" name="intensidad_sabor">
                            <?php
                            $intensidades = ['suave' => 'Suave', 'medio' => 'Medio', 'fuerte' => 'Fuerte', 'muy fuerte' => 'Muy Fuerte'];
                            foreach ($intensidades as $iVal => $iLabel):
                            ?>
                                <option value="<?= $iVal ?>"
                                    <?= ($producto['intensidad_sabor'] ?? 'medio') === $iVal ? 'selected' : '' ?>>
                                    <?= $iLabel ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="origen" class="form-label fw-semibold">Origen / Región</label>
                        <input type="text" class="form-control" id="origen" name="origen"
                               value="<?= htmlspecialchars($producto['origen'] ?? '') ?>"
                               placeholder="Ej: Boyacá, Huila, Nacional…">
                    </div>

                    <!-- ── Presentación ── -->
                    <div class="col-12 mt-2">
                        <h6 class="fw-bold text-muted text-uppercase small border-bottom pb-2 mb-1">
                            <i class="bi bi-box me-1"></i>Presentación y Precios
                        </h6>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="peso_presentacion" class="form-label fw-semibold">Peso / Presentación</label>
                        <input type="text" class="form-control" id="peso_presentacion" name="peso_presentacion"
                               value="<?= htmlspecialchars($producto['peso_presentacion'] ?? '') ?>"
                               placeholder="Ej: 500 g, 1 Kg…">
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="tiempo_maduracion" class="form-label fw-semibold">Tiempo de Maduración</label>
                        <input type="text" class="form-control" id="tiempo_maduracion" name="tiempo_maduracion"
                               value="<?= htmlspecialchars($producto['tiempo_maduracion'] ?? '') ?>"
                               placeholder="Ej: Fresco, 30 días…">
                    </div>

                    <div class="col-12 col-md-4">
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

                    <div class="col-12 col-md-6">
                        <label for="precio_compra" class="form-label fw-semibold">Precio de Compra / Costo ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" class="form-control"
                                   id="precio_compra" name="precio_compra"
                                   value="<?= $producto['precio_compra'] ?>" required>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="precio_venta" class="form-label fw-semibold">Precio de Venta ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" class="form-control"
                                   id="precio_venta" name="precio_venta"
                                   value="<?= $producto['precio_venta'] ?>" required>
                        </div>
                    </div>

                    <!-- ── Inventario ── -->
                    <div class="col-12 mt-2">
                        <h6 class="fw-bold text-muted text-uppercase small border-bottom pb-2 mb-1">
                            <i class="bi bi-archive me-1"></i>Inventario y Estado
                        </h6>
                    </div>

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
                            <option value="activo"   <?= ($producto['estado'] === 'activo')   ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= ($producto['estado'] === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>

                    <!-- ── Imagen ── -->
                    <div class="col-12 mt-2">
                        <h6 class="fw-bold text-muted text-uppercase small border-bottom pb-2 mb-1">
                            <i class="bi bi-image me-1"></i>Imagen del Producto
                        </h6>
                    </div>

                    <div class="col-12 col-md-7">
                        <label for="imagen" class="form-label fw-semibold">Subir Nueva Imagen</label>
                        <input type="file" class="form-control" id="imagen" name="imagen"
                               accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Formatos: JPG, PNG, WEBP · Tamaño máximo: 2 MB.
                            Si no subes imagen nueva se conserva la actual.
                        </div>
                    </div>

                    <div class="col-12 col-md-5 d-flex align-items-end gap-3">
                        <?php if ($imgPreviewSrc !== ''): ?>
                        <div>
                            <div class="small text-muted mb-1">Imagen actual:</div>
                            <img id="img-preview"
                                 src="<?= htmlspecialchars($imgPreviewSrc) ?>"
                                 alt="Imagen actual de <?= htmlspecialchars($producto['nombre']) ?>"
                                 class="img-thumbnail" style="max-height:120px;">
                        </div>
                        <?php else: ?>
                        <div id="preview-wrap" class="d-none">
                            <img id="img-preview" src="" alt="Vista previa"
                                 class="img-thumbnail" style="max-height:120px;">
                        </div>
                        <span class="text-muted small fst-italic">Sin imagen guardada</span>
                        <?php endif; ?>
                    </div>

                    <!-- ── Descripción ── -->
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

<script>
/* Preview de imagen al seleccionar archivo nuevo */
document.getElementById('imagen').addEventListener('change', function () {
    const file = this.files[0];
    const prev = document.getElementById('img-preview');
    const wrap = document.getElementById('preview-wrap');
    if (file && prev) {
        const reader = new FileReader();
        reader.onload = e => {
            prev.src = e.target.result;
            if (wrap) wrap.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
