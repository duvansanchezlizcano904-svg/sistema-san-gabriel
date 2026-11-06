<?php
/**
 * Vista para Registrar Producto
 */
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Registrar Producto Lácteo</h2>
                <p class="text-muted mb-0">Ingresa la información detallada para añadir el producto al inventario.</p>
            </div>
            <a href="index.php?c=productos" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Volver al Catálogo
            </a>
        </div>

        <div class="card-lacteo p-4">
            <form action="index.php?c=productos&a=guardar" method="POST" autocomplete="off"
                  enctype="multipart/form-data">
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
                               placeholder="Ej: LAC-011" required>
                        <div class="form-text">Código único de identificación.</div>
                    </div>

                    <div class="col-12 col-md-8">
                        <label for="nombre" class="form-label fw-semibold">Nombre del Producto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                               placeholder="Ej: Queso Campesino con Sal" required>
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
                               placeholder="Selecciona o escribe el tipo de queso" required>
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
                            <option value="Vaca">Vaca</option>
                            <option value="Cabra">Cabra</option>
                            <option value="Oveja">Oveja</option>
                            <option value="Mixta">Mixta</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="intensidad_sabor" class="form-label fw-semibold">Intensidad de Sabor</label>
                        <select class="form-select" id="intensidad_sabor" name="intensidad_sabor">
                            <option value="suave">Suave</option>
                            <option value="medio" selected>Medio</option>
                            <option value="fuerte">Fuerte</option>
                            <option value="muy fuerte">Muy Fuerte</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="origen" class="form-label fw-semibold">Origen / Región</label>
                        <input type="text" class="form-control" id="origen" name="origen"
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
                               placeholder="Ej: 500 g, 1 Kg, 250 g…">
                        <div class="form-text">Lo que el cliente verá como presentación.</div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="tiempo_maduracion" class="form-label fw-semibold">Tiempo de Maduración</label>
                        <input type="text" class="form-control" id="tiempo_maduracion" name="tiempo_maduracion"
                               placeholder="Ej: Fresco, 30 días, 6 meses…">
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="unidad_medida" class="form-label fw-semibold">Unidad de Medida <span class="text-danger">*</span></label>
                        <select class="form-select" id="unidad_medida" name="unidad_medida" required>
                            <option value="Kilogramo">Kilogramo (Kg)</option>
                            <option value="Litro">Litro (Lt)</option>
                            <option value="Libra (500g)">Libra (500g)</option>
                            <option value="Unidad">Unidad</option>
                            <option value="Bloque">Bloque</option>
                            <option value="Vaso">Vaso</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="precio_compra" class="form-label fw-semibold">Precio de Compra / Costo ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" class="form-control"
                                   id="precio_compra" name="precio_compra" placeholder="0.00" required>
                        </div>
                        <div class="form-text">Costo de producción o compra por unidad.</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="precio_venta" class="form-label fw-semibold">Precio de Venta ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" class="form-control"
                                   id="precio_venta" name="precio_venta" placeholder="0.00" required>
                        </div>
                        <div class="form-text">Precio de venta al público en general.</div>
                    </div>

                    <!-- ── Inventario ── -->
                    <div class="col-12 mt-2">
                        <h6 class="fw-bold text-muted text-uppercase small border-bottom pb-2 mb-1">
                            <i class="bi bi-archive me-1"></i>Inventario y Estado
                        </h6>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="cantidad" class="form-label fw-semibold">Cantidad Inicial (Stock) <span class="text-danger">*</span></label>
                        <input type="number" min="0" class="form-control" id="cantidad" name="cantidad" value="0" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="stock_minimo" class="form-label fw-semibold">Stock Mínimo de Alerta <span class="text-danger">*</span></label>
                        <input type="number" min="0" class="form-control" id="stock_minimo" name="stock_minimo" value="5" required>
                        <div class="form-text">Disparará una alerta al llegar a este nivel.</div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="estado" class="form-label fw-semibold">Estado del Producto</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="activo" selected>Activo (Disponible para venta)</option>
                            <option value="inactivo">Inactivo (Ocultar)</option>
                        </select>
                    </div>

                    <!-- ── Imagen ── -->
                    <div class="col-12 mt-2">
                        <h6 class="fw-bold text-muted text-uppercase small border-bottom pb-2 mb-1">
                            <i class="bi bi-image me-1"></i>Imagen del Producto
                        </h6>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="imagen" class="form-label fw-semibold">Foto del Queso</label>
                        <input type="file" class="form-control" id="imagen" name="imagen"
                               accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Formatos: JPG, PNG, WEBP · Tamaño máximo: 2 MB.
                            Si no subes imagen se usará un placeholder genérico.
                        </div>
                    </div>

                    <div class="col-12 col-md-6 d-flex align-items-end">
                        <div id="preview-wrap" class="d-none">
                            <img id="img-preview" src="" alt="Vista previa"
                                 class="img-thumbnail" style="max-height:120px;">
                        </div>
                    </div>

                    <!-- ── Descripción ── -->
                    <div class="col-12">
                        <label for="descripcion" class="form-label fw-semibold">Descripción / Notas Adicionales</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                  placeholder="Detalles de elaboración, presentación o almacenamiento..."></textarea>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                        <a href="index.php?c=productos" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <i class="bi bi-save"></i> Guardar Producto
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
/* Preview de imagen antes de enviar */
document.getElementById('imagen').addEventListener('change', function () {
    const file = this.files[0];
    const wrap = document.getElementById('preview-wrap');
    const prev = document.getElementById('img-preview');
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            prev.src = e.target.result;
            wrap.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    } else {
        wrap.classList.add('d-none');
        prev.src = '';
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
