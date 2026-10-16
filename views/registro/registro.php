<?php
/**
 * Vista de Registro de Clientes
 * Quesos San Gabriel — Página y Formulario de Registro
 */
require_once __DIR__ . '/../../config/helpers.php';
$flash = get_flash();

$errorRegistro = $errorRegistro ?? null;
$datosRegistro = $datosRegistro ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quesos San Gabriel — Registro de Cliente</title>
    <meta name="description" content="Crea tu cuenta de cliente en Quesos San Gabriel para comprar y disfrutar de los mejores quesos artesanales.">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Estilos de Login y Registro -->
    <link href="public/css/login.css" rel="stylesheet">
</head>
<body>

<div class="hero-page">

    <!-- Imagen del queso de fondo -->
    <div class="hero-bg">
        <img src="public/img/queso-fondo.jpg"
             alt=""
             onerror="this.src='https://images.unsplash.com/photo-1452195100486-9cc805987862?w=900&q=80'">
    </div>

    <!-- Gradiente oscuro para legibilidad -->
    <div class="hero-overlay"></div>

    <!-- Topbar -->
    <div class="hero-topbar">
        <a href="login.php" class="hero-brand">
            <div class="hero-brand-icon">
                🧀
            </div>
            <div>
                <div style="line-height:1.1">Quesos San Gabriel</div>
                <div style="font-size:.63rem; color:rgba(255,255,255,.45); font-weight:500;">Quesería Artesanal</div>
            </div>
        </a>
        <a href="login.php" class="btn-hero-register" style="padding:0.5rem 1.4rem; font-size:0.88rem;">
            <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
        </a>
    </div>

    <!-- Contenido central con tarjeta de formulario de registro -->
    <div class="hero-center" style="max-width: 520px; margin: 0 auto; width: 100%; padding: 1rem 1.5rem;">
        <div class="modal-content" style="border-radius: 20px; box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45); overflow: hidden; border: none;">

            <!-- Header de la tarjeta -->
            <div class="modal-header modal-header-registro" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); padding: 1.5rem 2rem 1.25rem;">
                <div class="modal-logo" style="margin-bottom: 0.5rem;">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h4 class="modal-title" style="color: #fff; font-weight: 800; font-size: 1.35rem; margin: 0;">Crear Cuenta de Cliente</h4>
                <p class="modal-subtitle" style="margin: 0.2rem 0 0; color: rgba(255,255,255,0.7); font-size: 0.85rem;">
                    Completa tus datos para registrarte como cliente
                </p>
            </div>

            <!-- Cuerpo del formulario -->
            <div class="modal-body" style="padding: 1.75rem 2rem; background: #ffffff;">

                <?php if ($flash): ?>
                    <div class="alert alert-<?= htmlspecialchars($flash['tipo']) ?> alert-dismissible fade show py-2 mb-3" role="alert">
                        <small><?= $flash['mensaje'] ?></small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errorRegistro)): ?>
                    <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <small><?= htmlspecialchars($errorRegistro) ?></small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="registro.php" method="POST" autocomplete="off">
                    <input type="hidden" name="_action" value="registro">

                    <!-- Nombre completo -->
                    <div class="mb-3">
                        <label for="reg_nombre" class="form-label">Nombre completo</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0 ps-1"
                                   id="reg_nombre"
                                   name="reg_nombre"
                                   placeholder="Ej: Juan Pérez"
                                   required
                                   value="<?= htmlspecialchars($datosRegistro['nombre'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Correo electrónico -->
                    <div class="mb-3">
                        <label for="reg_email" class="form-label">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email"
                                   class="form-control border-start-0 ps-1"
                                   id="reg_email"
                                   name="reg_email"
                                   placeholder="correo@ejemplo.com"
                                   required
                                   value="<?= htmlspecialchars($datosRegistro['email'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="mb-3">
                        <label for="reg_telefono" class="form-label">Teléfono</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="tel"
                                   class="form-control border-start-0 ps-1"
                                   id="reg_telefono"
                                   name="reg_telefono"
                                   placeholder="310 000 0000"
                                   required
                                   value="<?= htmlspecialchars($datosRegistro['telefono'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Contraseña -->
                        <div class="col-md-6 mb-3">
                            <label for="reg_password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password"
                                       class="form-control border-start-0 ps-1"
                                       id="reg_password"
                                       name="reg_password"
                                       placeholder="Mínimo 6 caract."
                                       required
                                       minlength="6">
                            </div>
                        </div>

                        <!-- Confirmar contraseña -->
                        <div class="col-md-6 mb-3">
                            <label for="reg_password2" class="form-label">Confirmar</label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password"
                                       class="form-control border-start-0 ps-1"
                                       id="reg_password2"
                                       name="reg_password2"
                                       placeholder="Repite clave"
                                       required
                                       minlength="6">
                            </div>
                        </div>
                    </div>

                    <div class="password-match-msg mb-3" id="msg-password-match"></div>

                    <!-- Botón submit registro -->
                    <button type="submit" class="btn-modal-submit" id="btn-submit-registro" style="padding: 0.8rem;">
                        <i class="bi bi-person-check-fill me-1"></i> Registrarse como Cliente
                    </button>
                </form>

                <p class="modal-switch-text mt-3 mb-1">
                    ¿Ya tienes una cuenta?
                    <a href="login.php" id="link-volver-login">Inicia sesión aquí</a>
                </p>

                <p class="modal-footer-text mb-0 mt-3">
                    &copy; <?= date('Y') ?> San Gabriel — Todos los derechos reservados.
                </p>

            </div><!-- /.modal-body -->

        </div>
    </div>

    <!-- Bottombar -->
    <div class="hero-bottom" style="padding: 0.75rem 3rem 1.25rem;">
        <div class="hero-stat-pill">
            <i class="bi bi-shield-lock-fill"></i>
            <span>Tus datos y contraseña están <strong>100% seguros</strong></span>
        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var pass1 = document.getElementById('reg_password');
    var pass2 = document.getElementById('reg_password2');
    var msg   = document.getElementById('msg-password-match');

    function checkPasswords() {
        if (!pass2.value) { msg.textContent = ''; msg.className = 'password-match-msg mb-3'; return; }
        if (pass1.value === pass2.value) {
            msg.textContent = '✓ Las contraseñas coinciden';
            msg.className = 'password-match-msg match-ok mb-3';
        } else {
            msg.textContent = '✗ Las contraseñas no coinciden';
            msg.className = 'password-match-msg match-error mb-3';
        }
    }

    pass1.addEventListener('input', checkPasswords);
    pass2.addEventListener('input', checkPasswords);
});
</script>

</body>
</html>
