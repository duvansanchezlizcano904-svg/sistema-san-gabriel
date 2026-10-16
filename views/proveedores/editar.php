<?php
/**
 * Vista: Editar Proveedor
 */
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Encabezado -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="index.php?c=proveedores" class="btn btn-light border">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h2 class="fw-bold text-dark mb-0">Editar Proveedor</h2>
        <p class="text-muted mb-0 small">Modifica los datos de: <strong><?= htmlspecialchars($proveedor['nombre']) ?></strong></p>
    </div>
</div>

<!-- Errores de validación -->
<?php if (!empty($errores)): ?>
    <div class="alert alert-danger mb-4">
        <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Corrige los siguientes errores:</div>
        <ul class="mb-0 ps-3">
            <?php foreach ($errores as $e): ?>
                <li><?= $e ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="index.php?c=proveedores&a=editar&id=<?= $proveedor['id'] ?>" autocomplete="off">
    <div class="row g-4">

        <!-- Columna principal -->
        <div class="col-12 col-lg-8">
            <div class="card-lacteo p-4">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-building text-primary"></i> Información del Proveedor
                </h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label for="nombre" class="form-label fw-semibold">
                            Nombre / Razón Social <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control"
                               id="nombre" name="nombre"
                               placeholder="Ej: Lácteos del Valle S.A.S"
                               value="<?= htmlspecialchars($proveedor['nombre']) ?>"
                               required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="nit" class="form-label fw-semibold">NIT</label>
                        <input type="text"
                               class="form-control"
                               id="nit" name="nit"
                               placeholder="Ej: 900.123.456-7"
                               value="<?= htmlspecialchars($proveedor['nit'] ?? '') ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="contacto" class="form-label fw-semibold">Persona de Contacto</label>
                        <input type="text"
                               class="form-control"
                               id="contacto" name="contacto"
                               placeholder="Ej: Carlos Martínez"
                               value="<?= htmlspecialchars($proveedor['contacto'] ?? '') ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0"
                                   id="telefono" name="telefono"
                                   placeholder="Ej: 310 234 5678"
                                   value="<?= htmlspecialchars($proveedor['telefono'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email"
                                   class="form-control border-start-0"
                                   id="email" name="email"
                                   placeholder="ventas@proveedor.com"
                                   value="<?= htmlspecialchars($proveedor['email'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="direccion" class="form-label fw-semibold">Dirección</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0"
                                   id="direccion" name="direccion"
                                   placeholder="Ej: Cra 12 #45-67, Bogotá"
                                   value="<?= htmlspecialchars($proveedor['direccion'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="notas" class="form-label fw-semibold">Notas adicionales</label>
                        <textarea class="form-control"
                                  id="notas" name="notas"
                                  rows="3"
                                  placeholder="Condiciones de pago, productos que suministra, etc."><?= htmlspecialchars($proveedor['notas'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna lateral -->
        <div class="col-12 col-lg-4">
            <div class="card-lacteo p-4">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-toggle-on text-primary"></i> Estado
                </h6>
                <select name="estado" class="form-select mb-3">
                    <option value="activo"   <?= $proveedor['estado'] === 'activo'   ? 'selected' : '' ?>>
                        ✅ Activo
                    </option>
                    <option value="inactivo" <?= $proveedor['estado'] === 'inactivo' ? 'selected' : '' ?>>
                        ⏸ Inactivo
                    </option>
                </select>
                <p class="text-muted small">Solo los proveedores activos aparecen disponibles en el sistema.</p>
            </div>

            <!-- Info de registro -->
            <div class="card-lacteo p-3 mt-3 bg-light">
                <small class="text-muted d-block">
                    <i class="bi bi-calendar3 me-1"></i>
                    Registrado el <?= formatearFecha($proveedor['creado_en'], false) ?>
                </small>
            </div>

            <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-save"></i> Guardar Cambios
                </button>
                <a href="index.php?c=proveedores" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </div>

    </div>
</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
