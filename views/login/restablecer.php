<?php
/**
 * Vista de Restablecer Contraseña (HU-30)
 * Quesos San Gabriel
 */
require_once __DIR__ . '/../../config/helpers.php';
$flash = get_flash();
$error = $error ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quesos San Gabriel — Restablecer Contraseña</title>
    <meta name="description" content="Restablece tu contraseña de forma segura en Quesos San Gabriel.">

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

    <!-- Contenido central -->
    <div class="hero-center" style="max-width: 480px; margin: 0 auto; width: 100%; padding: 1rem 1.5rem;">
        <div class="modal-content" style="border-radius: 20px; box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45); overflow: hidden; border: none;">

            <!-- Header de la tarjeta -->
            <div class="modal-header" style="background: linear-gradient(135deg, #0f172a, #1e293b); padding: 1.5rem 2rem 1.25rem;">
                <div class="modal-logo" style="margin-bottom: 0.5rem;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h4 class="modal-title" style="color: #fff; font-weight: 800; font-size: 1.35rem; margin: 0;">Nueva Contraseña</h4>
                <p class="modal-subtitle" style="margin: 0.2rem 0 0; color: rgba(255,255,255,0.7); font-size: 0.85rem;">
                    Hola, <strong><?= htmlspecialchars($usuario['nombre'] ?? 'Usuario') ?></strong>. Ingresa tu nueva contraseña.
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

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <small><?= htmlspecialchars($error) ?></small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="login.php?a=restablecer" method="POST" autocomplete="off">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="mb-3">
                        <label for="password" class="form-label">Nueva contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password"
                                   class="form-control border-start-0 ps-1"
                                   id="password"
                                   name="password"
                                   placeholder="Mínimo 6 caracteres"
                                   required
                                   minlength="6">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password2" class="form-label">Confirmar nueva contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="password"
                                   class="form-control border-start-0 ps-1"
                                   id="password2"
                                   name="password2"
                                   placeholder="Repite tu nueva contraseña"
                                   required
                                   minlength="6">
                        </div>
                        <div class="password-match-msg" id="msg-password-match"></div>
                    </div>

                    <button type="submit" class="btn-modal-submit" id="btn-submit-restablecer">
                        <i class="bi bi-check2-circle"></i> Guardar Nueva Contraseña
                    </button>
                </form>

                <p class="modal-switch-text mt-3">
                    <a href="login.php">
                        <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
                    </a>
                </p>

                <p class="modal-footer-text">
                    &copy; <?= date('Y') ?> San Gabriel — Todos los derechos reservados.
                </p>

            </div><!-- /.modal-body -->

        </div><!-- /.modal-content -->
    </div><!-- /.hero-center -->

</div><!-- /.hero-page -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var pass1 = document.getElementById('password');
    var pass2 = document.getElementById('password2');
    var msg   = document.getElementById('msg-password-match');

    function checkPasswords() {
        if (!pass2.value) { msg.textContent = ''; msg.className = 'password-match-msg'; return; }
        if (pass1.value === pass2.value) {
            msg.textContent = '✓ Las contraseñas coinciden';
            msg.className = 'password-match-msg match-ok';
        } else {
            msg.textContent = '✗ Las contraseñas no coinciden';
            msg.className = 'password-match-msg match-error';
        }
    }

    pass1.addEventListener('input', checkPasswords);
    pass2.addEventListener('input', checkPasswords);
});
</script>

</body>
</html>

